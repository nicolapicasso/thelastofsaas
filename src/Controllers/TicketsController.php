<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Sponsor;
use App\Models\SponsorInviteCode;
use App\Services\StripeService;
use App\Services\EmailService;
use App\Services\QRService;

/**
 * Frontend Tickets Controller
 * TLOS - The Last of SaaS
 *
 * Ticket registration, purchase and management
 */
class TicketsController extends Controller
{
    private Event $eventModel;
    private Ticket $ticketModel;
    private TicketType $ticketTypeModel;
    private Sponsor $sponsorModel;
    private SponsorInviteCode $inviteCodeModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
        $this->ticketModel = new Ticket();
        $this->ticketTypeModel = new TicketType();
        $this->sponsorModel = new Sponsor();
        $this->inviteCodeModel = new SponsorInviteCode();
    }

    /**
     * Show ticket registration form
     */
    public function register(string $eventSlug): void
    {
        $event = $this->eventModel->findBySlug($eventSlug);

        if (!$event || !in_array($event['status'], ['published', 'active'])) {
            $this->notFound();
            return;
        }

        // Check if registration is open
        if (!$this->eventModel->isRegistrationOpen($event)) {
            $this->render('tickets/closed', [
                'event' => $event,
                'meta_title' => 'Registro Cerrado - ' . $event['name']
            ]);
            return;
        }

        // Get available ticket types
        $ticketTypes = $this->ticketTypeModel->getAvailableForEvent($event['id']);

        // Check for code in URL (can be sponsor code or invite code)
        $inputCode = $_GET['code'] ?? null;
        $sponsor = null;
        $inviteCode = null;

        if ($inputCode) {
            // First try invite code
            $inviteCode = $this->inviteCodeModel->findByCode($inputCode, $event['id']);
            if ($inviteCode) {
                $validation = $this->inviteCodeModel->isValid($inviteCode);
                if (!$validation['valid']) {
                    $inviteCode = null;
                } else {
                    $sponsor = $this->sponsorModel->find($inviteCode['sponsor_id']);
                }
            }

            // If not an invite code, try sponsor code
            if (!$inviteCode) {
                $sponsor = $this->sponsorModel->findByCode($inputCode);
                if ($sponsor) {
                    // Verify sponsor is associated with this event
                    $eventSponsors = $this->eventModel->getSponsors($event['id']);
                    $sponsorIds = array_column($eventSponsors, 'id');
                    if (!in_array($sponsor['id'], $sponsorIds)) {
                        $sponsor = null;
                    }
                }
            }
        }

        $this->render('tickets/register', [
            'event' => $event,
            'ticketTypes' => $ticketTypes,
            'sponsor' => $sponsor,
            'inviteCode' => $inviteCode,
            'inputCode' => $inputCode,
            'meta_title' => 'Registro - ' . $event['name']
        ]);
    }

    /**
     * Process ticket registration
     */
    public function store(string $eventSlug): void
    {
        $event = $this->eventModel->findBySlug($eventSlug);

        if (!$event || !in_array($event['status'], ['published', 'active'])) {
            $this->jsonError('Evento no encontrado', 404);
            return;
        }

        if (!$this->validateCsrf()) {
            $this->jsonError('Token de seguridad inválido', 403);
            return;
        }

        // Validate input
        $ticketTypeId = (int)($_POST['ticket_type_id'] ?? 0);
        $attendeeName = trim($_POST['attendee_name'] ?? '');
        $attendeeEmail = trim($_POST['attendee_email'] ?? '');
        $attendeePhone = trim($_POST['attendee_phone'] ?? '');
        $attendeeCompany = trim($_POST['attendee_company'] ?? '');
        $attendeePosition = trim($_POST['attendee_position'] ?? '');
        $sponsorCode = trim($_POST['sponsor_code'] ?? '');

        // Validation
        $errors = [];
        if (!$ticketTypeId) $errors[] = 'Selecciona un tipo de entrada';
        if (!$attendeeName) $errors[] = 'El nombre es obligatorio';
        if (!$attendeeEmail || !filter_var($attendeeEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }

        if ($errors) {
            $this->jsonError(implode(', ', $errors), 400);
            return;
        }

        // Verify ticket type
        $ticketType = $this->ticketTypeModel->find($ticketTypeId);
        if (!$ticketType || $ticketType['event_id'] != $event['id']) {
            $this->jsonError('Tipo de entrada no válido', 400);
            return;
        }

        // Check availability
        if (!$this->ticketTypeModel->hasAvailability($ticketTypeId)) {
            $this->jsonError('No quedan entradas de este tipo', 400);
            return;
        }

        // Check for duplicate registration
        if ($this->ticketModel->existsForEmail($event['id'], $attendeeEmail)) {
            $this->jsonError('Ya existe un registro con este email', 400);
            return;
        }

        // Validate code (invite code or sponsor code)
        $sponsorId = null;
        $inviteCodeId = null;
        $inviteCode = null;
        $discount = 0;

        if ($sponsorCode) {
            // First try as invite code
            $inviteCode = $this->inviteCodeModel->findByCode($sponsorCode, $event['id']);
            if ($inviteCode) {
                $validation = $this->inviteCodeModel->isValid($inviteCode);
                if ($validation['valid']) {
                    // Check if restricted to specific ticket type
                    if ($inviteCode['ticket_type_id'] && $inviteCode['ticket_type_id'] != $ticketTypeId) {
                        $this->jsonError('Este codigo no es valido para este tipo de entrada', 400);
                        return;
                    }
                    $sponsorId = $inviteCode['sponsor_id'];
                    $inviteCodeId = $inviteCode['id'];
                } else {
                    $this->jsonError(implode(' ', $validation['errors']), 400);
                    return;
                }
            } else {
                // Try as direct sponsor code
                $sponsor = $this->sponsorModel->findByCode($sponsorCode);
                if ($sponsor) {
                    $eventSponsors = $this->eventModel->getSponsors($event['id']);
                    $sponsorIds = array_column($eventSponsors, 'id');
                    if (in_array($sponsor['id'], $sponsorIds)) {
                        $sponsorId = $sponsor['id'];
                    }
                }
            }
        }

        // Determine if payment is needed and calculate price
        $price = (float)$ticketType['price'];

        // Apply discount from invite code if applicable
        if ($inviteCode && $price > 0) {
            $discount = $this->inviteCodeModel->calculateDiscount($inviteCode, $price);
            $price = max(0, $price - $discount);
        } elseif ($sponsorId && !$inviteCodeId) {
            // Direct sponsor code = free ticket
            $price = 0;
        }

        $needsPayment = $price > 0;

        if ($needsPayment) {
            // Create pending ticket and redirect to payment
            $ticketData = [
                'event_id' => $event['id'],
                'ticket_type_id' => $ticketTypeId,
                'sponsor_id' => $sponsorId,
                'invite_code_id' => $inviteCodeId,
                'attendee_name' => $attendeeName,
                'attendee_email' => $attendeeEmail,
                'attendee_phone' => $attendeePhone,
                'attendee_company' => $attendeeCompany,
                'attendee_position' => $attendeePosition,
                'price' => $price,
                'status' => 'pending_payment'
            ];

            $ticketId = $this->ticketModel->create($ticketData);

            // Update invite code usage if applicable
            if ($inviteCodeId) {
                $this->inviteCodeModel->useCode($inviteCodeId);
            }

            if (!$ticketId) {
                $this->jsonError('Error al crear el registro', 500);
                return;
            }

            // Create Stripe checkout session
            try {
                $stripeService = new StripeService();
                $checkoutUrl = $stripeService->createCheckoutSession([
                    'ticket_id' => $ticketId,
                    'event_name' => $event['name'],
                    'ticket_type' => $ticketType['name'],
                    'price' => $price,
                    'email' => $attendeeEmail,
                    'success_url' => url("/eventos/{$eventSlug}/ticket/confirmacion?session_id={CHECKOUT_SESSION_ID}"),
                    'cancel_url' => url("/eventos/{$eventSlug}/registro?cancelled=1")
                ]);

                $this->json(['success' => true, 'redirect' => $checkoutUrl]);
            } catch (\Exception $e) {
                // Delete pending ticket
                $this->ticketModel->delete($ticketId);
                $this->jsonError('Error al procesar el pago: ' . $e->getMessage(), 500);
            }
            return;
        }

        // Free ticket or sponsor ticket
        $status = $ticketType['requires_approval'] ? 'pending' : 'confirmed';

        $ticketData = [
            'event_id' => $event['id'],
            'ticket_type_id' => $ticketTypeId,
            'sponsor_id' => $sponsorId,
            'invite_code_id' => $inviteCodeId,
            'attendee_name' => $attendeeName,
            'attendee_email' => $attendeeEmail,
            'attendee_phone' => $attendeePhone,
            'attendee_company' => $attendeeCompany,
            'attendee_position' => $attendeePosition,
            'price' => 0,
            'status' => $status
        ];

        $ticketId = $this->ticketModel->create($ticketData);

        if (!$ticketId) {
            $this->jsonError('Error al crear el registro', 500);
            return;
        }

        // Update invite code usage if applicable
        if ($inviteCodeId) {
            $this->inviteCodeModel->useCode($inviteCodeId);
        }

        $ticket = $this->ticketModel->find($ticketId);

        // Send confirmation email
        if ($status === 'confirmed') {
            $this->sendConfirmationEmail($ticket, $event);
        }

        $this->json([
            'success' => true,
            'redirect' => url("/eventos/{$eventSlug}/ticket/{$ticket['code']}")
        ]);
    }

    /**
     * Payment success callback
     */
    public function paymentSuccess(string $eventSlug): void
    {
        $sessionId = $_GET['session_id'] ?? null;

        if (!$sessionId) {
            $this->redirect("/eventos/{$eventSlug}/registro?error=payment");
            return;
        }

        $event = $this->eventModel->findBySlug($eventSlug);
        if (!$event) {
            $this->notFound();
            return;
        }

        try {
            $stripeService = new StripeService();
            $session = $stripeService->retrieveCheckoutSession($sessionId);

            if ($session->payment_status !== 'paid') {
                $this->redirect("/eventos/{$eventSlug}/registro?error=payment");
                return;
            }

            // Get ticket from metadata
            $ticketId = $session->metadata->ticket_id ?? null;
            if (!$ticketId) {
                throw new \Exception('Ticket ID not found in session');
            }

            // Update ticket status
            $this->ticketModel->update($ticketId, [
                'status' => 'confirmed',
                'stripe_payment_id' => $session->payment_intent
            ]);

            $ticket = $this->ticketModel->find($ticketId);

            // Send confirmation email
            $this->sendConfirmationEmail($ticket, $event);

            $this->redirect("/eventos/{$eventSlug}/ticket/{$ticket['code']}");

        } catch (\Exception $e) {
            error_log('Payment verification error: ' . $e->getMessage());
            $this->redirect("/eventos/{$eventSlug}/registro?error=payment");
        }
    }

    /**
     * Show ticket confirmation page
     */
    public function show(string $eventSlug, string $code): void
    {
        $event = $this->eventModel->findBySlug($eventSlug);
        if (!$event) {
            $this->notFound();
            return;
        }

        $ticket = $this->ticketModel->findByCode($code);
        if (!$ticket || $ticket['event_id'] != $event['id']) {
            $this->notFound();
            return;
        }

        // Get ticket type info
        $ticketType = $this->ticketTypeModel->find($ticket['ticket_type_id']);

        // Get sponsor info if applicable
        $sponsor = null;
        if ($ticket['sponsor_id']) {
            $sponsor = $this->sponsorModel->find($ticket['sponsor_id']);
        }

        $this->render('tickets/show', [
            'event' => $event,
            'ticket' => $ticket,
            'ticketType' => $ticketType,
            'sponsor' => $sponsor,
            'meta_title' => 'Tu Entrada - ' . $event['name']
        ]);
    }

    /**
     * Download ticket as PDF
     */
    public function download(string $eventSlug, string $code): void
    {
        $event = $this->eventModel->findBySlug($eventSlug);
        if (!$event) {
            $this->notFound();
            return;
        }

        $ticket = $this->ticketModel->findByCode($code);
        if (!$ticket || $ticket['event_id'] != $event['id']) {
            $this->notFound();
            return;
        }

        // TODO: Generate PDF with QR code
        // For now, redirect to show page
        $this->redirect("/eventos/{$eventSlug}/ticket/{$code}");
    }

    /**
     * Resend ticket email
     */
    public function resendEmail(string $eventSlug, string $code): void
    {
        $event = $this->eventModel->findBySlug($eventSlug);
        $ticket = $this->ticketModel->findByCode($code);

        if (!$event || !$ticket || $ticket['event_id'] != $event['id']) {
            $this->jsonError('Ticket no encontrado', 404);
            return;
        }

        if ($ticket['status'] !== 'confirmed') {
            $this->jsonError('El ticket no está confirmado', 400);
            return;
        }

        $this->sendConfirmationEmail($ticket, $event);

        $this->json(['success' => true, 'message' => 'Email enviado']);
    }

    /**
     * Send confirmation email with ticket
     */
    private function sendConfirmationEmail(array $ticket, array $event): void
    {
        try {
            $emailService = new EmailService();
            $emailService->sendTicketConfirmation($ticket, $event);
        } catch (\Exception $e) {
            error_log('Error sending ticket email: ' . $e->getMessage());
        }
    }
}
