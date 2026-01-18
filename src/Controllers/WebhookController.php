<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ticket;
use App\Models\Event;
use App\Services\StripeService;
use App\Services\EmailService;

/**
 * Webhook Controller
 * TLOS - The Last of SaaS
 *
 * Handles external webhooks (Stripe, etc.)
 */
class WebhookController extends Controller
{
    /**
     * Handle Stripe webhook events
     */
    public function stripe(): void
    {
        // Get raw payload
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        if (!$payload || !$signature) {
            http_response_code(400);
            echo 'Invalid request';
            exit;
        }

        try {
            $stripeService = new StripeService();

            // Verify signature
            if (!$stripeService->verifyWebhookSignature($payload, $signature)) {
                http_response_code(400);
                echo 'Invalid signature';
                exit;
            }

            // Parse event
            $event = $stripeService->parseWebhookEvent($payload);

            if (!$event || !isset($event->type)) {
                http_response_code(400);
                echo 'Invalid event';
                exit;
            }

            // Handle event
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutCompleted($event->data->object);
                    break;

                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->handleRefund($event->data->object);
                    break;

                default:
                    // Log unhandled event types
                    error_log('Unhandled Stripe webhook event: ' . $event->type);
            }

            http_response_code(200);
            echo 'OK';

        } catch (\Exception $e) {
            error_log('Stripe webhook error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Error: ' . $e->getMessage();
        }

        exit;
    }

    /**
     * Handle checkout.session.completed event
     */
    private function handleCheckoutCompleted(object $session): void
    {
        $ticketId = $session->metadata->ticket_id ?? null;

        if (!$ticketId) {
            error_log('Checkout completed but no ticket_id in metadata');
            return;
        }

        $ticketModel = new Ticket();
        $ticket = $ticketModel->find($ticketId);

        if (!$ticket) {
            error_log('Ticket not found: ' . $ticketId);
            return;
        }

        // Update ticket status
        $ticketModel->update($ticketId, [
            'status' => 'confirmed',
            'stripe_payment_id' => $session->payment_intent ?? null
        ]);

        // Send confirmation email
        $eventModel = new Event();
        $event = $eventModel->find($ticket['event_id']);

        if ($event) {
            $ticket = $ticketModel->find($ticketId); // Refresh ticket data
            $emailService = new EmailService();
            $emailService->sendTicketConfirmation($ticket, $event);
        }

        error_log('Checkout completed for ticket: ' . $ticketId);
    }

    /**
     * Handle payment_intent.succeeded event
     */
    private function handlePaymentSucceeded(object $paymentIntent): void
    {
        // This is backup for checkout.session.completed
        // Usually the checkout session event is sufficient

        error_log('Payment succeeded: ' . $paymentIntent->id);
    }

    /**
     * Handle payment_intent.payment_failed event
     */
    private function handlePaymentFailed(object $paymentIntent): void
    {
        error_log('Payment failed: ' . $paymentIntent->id);

        // Could send notification to admin or update ticket status
    }

    /**
     * Handle charge.refunded event
     */
    private function handleRefund(object $charge): void
    {
        $paymentIntentId = $charge->payment_intent ?? null;

        if (!$paymentIntentId) {
            return;
        }

        $ticketModel = new Ticket();
        $ticket = $ticketModel->findByPaymentId($paymentIntentId);

        if ($ticket) {
            $ticketModel->update($ticket['id'], [
                'status' => 'refunded'
            ]);

            error_log('Ticket refunded: ' . $ticket['id']);
        }
    }
}
