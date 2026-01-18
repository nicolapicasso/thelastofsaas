<?php

namespace App\Services;

use App\Models\TlosSetting;

/**
 * Stripe Payment Service
 * TLOS - The Last of SaaS
 *
 * Handles Stripe payment integration for ticket purchases
 */
class StripeService
{
    private string $secretKey;
    private string $publicKey;
    private string $currency;
    private string $webhookSecret;

    public function __construct()
    {
        $settings = new TlosSetting();

        // Try settings first, then env
        $this->secretKey = $settings->get('stripe_secret_key') ?: ($_ENV['STRIPE_SECRET_KEY'] ?? '');
        $this->publicKey = $settings->get('stripe_public_key') ?: ($_ENV['STRIPE_PUBLIC_KEY'] ?? '');
        $this->currency = $settings->get('currency', 'eur');
        $this->webhookSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '';

        if (!$this->secretKey) {
            throw new \Exception('Stripe secret key not configured');
        }
    }

    /**
     * Get public key for frontend
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Create a checkout session for ticket purchase
     */
    public function createCheckoutSession(array $params): string
    {
        $response = $this->request('POST', '/v1/checkout/sessions', [
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'success_url' => $params['success_url'],
            'cancel_url' => $params['cancel_url'],
            'customer_email' => $params['email'],
            'metadata' => [
                'ticket_id' => $params['ticket_id']
            ],
            'line_items' => [[
                'price_data' => [
                    'currency' => $this->currency,
                    'unit_amount' => (int)($params['price'] * 100), // Convert to cents
                    'product_data' => [
                        'name' => $params['event_name'] . ' - ' . $params['ticket_type'],
                        'description' => 'Entrada para ' . $params['event_name']
                    ]
                ],
                'quantity' => 1
            ]]
        ]);

        return $response['url'];
    }

    /**
     * Retrieve checkout session
     */
    public function retrieveCheckoutSession(string $sessionId): object
    {
        $response = $this->request('GET', "/v1/checkout/sessions/{$sessionId}");
        return (object)$response;
    }

    /**
     * Retrieve payment intent
     */
    public function retrievePaymentIntent(string $paymentIntentId): object
    {
        $response = $this->request('GET', "/v1/payment_intents/{$paymentIntentId}");
        return (object)$response;
    }

    /**
     * Create refund for a payment
     */
    public function createRefund(string $paymentIntentId, ?int $amount = null): object
    {
        $params = ['payment_intent' => $paymentIntentId];

        if ($amount !== null) {
            $params['amount'] = $amount;
        }

        $response = $this->request('POST', '/v1/refunds', $params);
        return (object)$response;
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        if (!$this->webhookSecret) {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signature) as $part) {
            [$key, $value] = explode('=', $part, 2);
            $parts[$key] = $value;
        }

        if (!isset($parts['t']) || !isset($parts['v1'])) {
            return false;
        }

        $timestamp = $parts['t'];
        $expectedSignature = $parts['v1'];

        // Compute expected signature
        $signedPayload = $timestamp . '.' . $payload;
        $computedSignature = hash_hmac('sha256', $signedPayload, $this->webhookSecret);

        return hash_equals($computedSignature, $expectedSignature);
    }

    /**
     * Parse webhook event
     */
    public function parseWebhookEvent(string $payload): object
    {
        return json_decode($payload);
    }

    /**
     * Make API request to Stripe
     */
    private function request(string $method, string $endpoint, array $params = []): array
    {
        $url = 'https://api.stripe.com' . $endpoint;

        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/x-www-form-urlencoded'
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->flattenParams($params)));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('Stripe API error: ' . $error);
        }

        $data = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorMessage = $data['error']['message'] ?? 'Unknown error';
            throw new \Exception('Stripe API error: ' . $errorMessage);
        }

        return $data;
    }

    /**
     * Flatten nested parameters for Stripe API
     */
    private function flattenParams(array $params, string $prefix = ''): array
    {
        $result = [];

        foreach ($params as $key => $value) {
            $newKey = $prefix ? "{$prefix}[{$key}]" : $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenParams($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
