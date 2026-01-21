<?php

namespace App\Services;

use App\Models\TlosSetting;

/**
 * Omniwallet Integration Service
 *
 * Handles all communication with the Omniwallet API for loyalty points
 * and customer management.
 *
 * API Documentation: https://api.omniwallet.cloud/v1
 */
class OmniwalletService
{
    private const API_BASE_URL = 'https://api.omniwallet.cloud/v1';

    private TlosSetting $settings;
    private ?string $apiToken = null;
    private ?string $account = null;
    private bool $enabled = false;

    // Point action types
    public const ACTION_SPONSOR_REGISTRATION = 'sponsor_registration';
    public const ACTION_COMPANY_REGISTRATION = 'company_registration';
    public const ACTION_TICKET_PURCHASE = 'ticket_purchase';
    public const ACTION_CHECKIN = 'checkin';
    public const ACTION_SAAS_SELECTION = 'saas_selection';
    public const ACTION_MATCH = 'match';
    public const ACTION_MEETING_SCHEDULED = 'meeting_scheduled';
    public const ACTION_LIVE_MATCH_COMPANY = 'live_match_company';
    public const ACTION_LIVE_MATCH_SPONSOR = 'live_match_sponsor';

    public function __construct()
    {
        $this->settings = new TlosSetting();
        $this->loadConfig();
    }

    /**
     * Load configuration from database
     */
    private function loadConfig(): void
    {
        $this->enabled = (bool) $this->settings->get('omniwallet_enabled', false);
        $this->apiToken = $this->settings->get('omniwallet_api_token', '');
        $this->account = $this->settings->get('omniwallet_account', '');
    }

    /**
     * Check if the integration is properly configured and enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiToken) && !empty($this->account);
    }

    /**
     * Get points value for a specific action
     */
    public function getPointsForAction(string $action): int
    {
        $key = 'omniwallet_points_' . $action;
        return (int) $this->settings->get($key, 0);
    }

    /**
     * Create or update a customer in Omniwallet
     *
     * @param string $email Customer email (unique identifier)
     * @param string $name Customer first name
     * @param string|null $lastName Customer last name
     * @param string|null $phone Customer phone
     * @return array|null Response data or null on failure
     */
    public function createOrUpdateCustomer(
        string $email,
        string $name,
        ?string $lastName = null,
        ?string $phone = null
    ): ?array {
        if (!$this->isEnabled()) {
            return null;
        }

        // First, try to get existing customer
        $existing = $this->getCustomer($email);

        if ($existing) {
            // Update existing customer
            return $this->updateCustomer($email, $name, $lastName, $phone);
        }

        // Create new customer
        $data = [
            'data' => [
                'type' => 'customers',
                'attributes' => [
                    'name' => $name,
                    'email' => $email,
                ]
            ]
        ];

        if ($lastName) {
            $data['data']['attributes']['last_name'] = $lastName;
        }
        if ($phone) {
            $data['data']['attributes']['phone'] = $phone;
        }

        return $this->request('POST', '/customers', $data);
    }

    /**
     * Get customer data from Omniwallet
     */
    public function getCustomer(string $email): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $response = $this->request('GET', '/customers/' . urlencode($email));

        // If 404 or error, return null
        if (!$response || isset($response['error'])) {
            return null;
        }

        return $response;
    }

    /**
     * Update existing customer
     */
    public function updateCustomer(
        string $email,
        string $name,
        ?string $lastName = null,
        ?string $phone = null
    ): ?array {
        if (!$this->isEnabled()) {
            return null;
        }

        $data = [
            'data' => [
                'type' => 'customers',
                'attributes' => [
                    'name' => $name,
                ]
            ]
        ];

        if ($lastName) {
            $data['data']['attributes']['last_name'] = $lastName;
        }
        if ($phone) {
            $data['data']['attributes']['phone'] = $phone;
        }

        return $this->request('PATCH', '/customers/' . urlencode($email), $data);
    }

    /**
     * Add points to a customer
     *
     * @param string $email Customer email
     * @param int $points Number of points to add
     * @param string $type Type/reason for the points (e.g., 'TLOS Event Registration')
     * @param string|null $externalId Unique identifier to prevent duplicate transactions
     * @param array $metadata Additional metadata to store with the transaction
     * @return array|null Response data or null on failure
     */
    public function addPoints(
        string $email,
        int $points,
        string $type,
        ?string $externalId = null,
        array $metadata = []
    ): ?array {
        if (!$this->isEnabled() || $points <= 0) {
            return null;
        }

        $data = [
            'points' => $points,
            'type' => $type,
        ];

        if ($externalId) {
            $data['external_id'] = $externalId;
        }

        if (!empty($metadata)) {
            $data['content'] = $metadata;
        }

        return $this->request('POST', '/customers/' . urlencode($email) . '/add-points', $data);
    }

    /**
     * Award points for a specific action
     *
     * @param string $email Customer email
     * @param string $action One of the ACTION_* constants
     * @param string|null $externalId Unique identifier for idempotency
     * @param array $metadata Additional data to store
     * @return array|null Response or null if disabled/no points configured
     */
    public function awardPointsForAction(
        string $email,
        string $action,
        ?string $externalId = null,
        array $metadata = []
    ): ?array {
        $points = $this->getPointsForAction($action);

        if ($points <= 0) {
            return null;
        }

        $typeLabels = [
            self::ACTION_SPONSOR_REGISTRATION => 'TLOS - Registro de Sponsor',
            self::ACTION_COMPANY_REGISTRATION => 'TLOS - Registro de Empresa',
            self::ACTION_TICKET_PURCHASE => 'TLOS - Compra de Entrada',
            self::ACTION_CHECKIN => 'TLOS - Check-in en Evento',
            self::ACTION_SAAS_SELECTION => 'TLOS - Selección de SaaS',
            self::ACTION_MATCH => 'TLOS - Match Realizado',
            self::ACTION_MEETING_SCHEDULED => 'TLOS - Reunión Programada',
            self::ACTION_LIVE_MATCH_COMPANY => 'TLOS - Match en Vivo (Empresa)',
            self::ACTION_LIVE_MATCH_SPONSOR => 'TLOS - Match en Vivo (Sponsor)',
        ];

        $type = $typeLabels[$action] ?? 'TLOS - ' . ucfirst(str_replace('_', ' ', $action));

        return $this->addPoints($email, $points, $type, $externalId, $metadata);
    }

    /**
     * Process registration for a sponsor - creates customers and awards points
     *
     * @param array $sponsor Sponsor data
     * @param array $contacts Array of contact data with name, email, phone
     * @return int Number of contacts processed successfully
     */
    public function processSponsorRegistration(array $sponsor, array $contacts): int
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        $processed = 0;
        foreach ($contacts as $contact) {
            if (empty($contact['email'])) {
                continue;
            }

            // Split name into first/last if possible
            $nameParts = $this->splitName($contact['name'] ?? '');

            // Create/update customer
            $this->createOrUpdateCustomer(
                $contact['email'],
                $nameParts['first'],
                $nameParts['last'],
                $contact['phone'] ?? null
            );

            // Award registration points
            $externalId = 'sponsor_reg_' . ($sponsor['id'] ?? 'new') . '_' . md5($contact['email']);
            $this->awardPointsForAction(
                $contact['email'],
                self::ACTION_SPONSOR_REGISTRATION,
                $externalId,
                [
                    'sponsor_id' => $sponsor['id'] ?? null,
                    'sponsor_name' => $sponsor['name'] ?? null,
                ]
            );

            $processed++;
        }

        return $processed;
    }

    /**
     * Process registration for a company - creates customers and awards points
     *
     * @param array $company Company data
     * @param array $contacts Array of contact data
     * @return int Number of contacts processed successfully
     */
    public function processCompanyRegistration(array $company, array $contacts): int
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        $processed = 0;
        foreach ($contacts as $contact) {
            if (empty($contact['email'])) {
                continue;
            }

            $nameParts = $this->splitName($contact['name'] ?? '');

            $this->createOrUpdateCustomer(
                $contact['email'],
                $nameParts['first'],
                $nameParts['last'],
                $contact['phone'] ?? null
            );

            $externalId = 'company_reg_' . ($company['id'] ?? 'new') . '_' . md5($contact['email']);
            $this->awardPointsForAction(
                $contact['email'],
                self::ACTION_COMPANY_REGISTRATION,
                $externalId,
                [
                    'company_id' => $company['id'] ?? null,
                    'company_name' => $company['name'] ?? null,
                ]
            );

            $processed++;
        }

        return $processed;
    }

    /**
     * Process ticket purchase - awards points to ticket holder
     */
    public function processTicketPurchase(array $ticket): ?array
    {
        if (!$this->isEnabled() || empty($ticket['email'])) {
            return null;
        }

        $nameParts = $this->splitName($ticket['name'] ?? '');

        $this->createOrUpdateCustomer(
            $ticket['email'],
            $nameParts['first'],
            $nameParts['last'],
            $ticket['phone'] ?? null
        );

        $externalId = 'ticket_' . ($ticket['id'] ?? uniqid());
        return $this->awardPointsForAction(
            $ticket['email'],
            self::ACTION_TICKET_PURCHASE,
            $externalId,
            [
                'ticket_id' => $ticket['id'] ?? null,
                'event_id' => $ticket['event_id'] ?? null,
            ]
        );
    }

    /**
     * Process check-in - awards points to attendee
     */
    public function processCheckin(array $ticket): ?array
    {
        if (!$this->isEnabled() || empty($ticket['email'])) {
            return null;
        }

        $externalId = 'checkin_' . ($ticket['id'] ?? uniqid());
        return $this->awardPointsForAction(
            $ticket['email'],
            self::ACTION_CHECKIN,
            $externalId,
            [
                'ticket_id' => $ticket['id'] ?? null,
                'event_id' => $ticket['event_id'] ?? null,
            ]
        );
    }

    /**
     * Process SaaS selection by company - awards points to all company contacts
     */
    public function processSaasSelection(array $company, array $contacts, array $sponsor): int
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        $processed = 0;
        foreach ($contacts as $contact) {
            if (empty($contact['email'])) {
                continue;
            }

            $externalId = 'saas_sel_' . $company['id'] . '_' . $sponsor['id'] . '_' . md5($contact['email']);
            $result = $this->awardPointsForAction(
                $contact['email'],
                self::ACTION_SAAS_SELECTION,
                $externalId,
                [
                    'company_id' => $company['id'],
                    'sponsor_id' => $sponsor['id'],
                    'sponsor_name' => $sponsor['name'] ?? null,
                ]
            );

            if ($result) {
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Process match created - awards points to company contacts
     */
    public function processMatch(array $company, array $contacts, array $sponsor, ?int $eventId = null): int
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        $processed = 0;
        foreach ($contacts as $contact) {
            if (empty($contact['email'])) {
                continue;
            }

            $externalId = 'match_' . $company['id'] . '_' . $sponsor['id'] . '_' . ($eventId ?? 0) . '_' . md5($contact['email']);
            $result = $this->awardPointsForAction(
                $contact['email'],
                self::ACTION_MATCH,
                $externalId,
                [
                    'company_id' => $company['id'],
                    'sponsor_id' => $sponsor['id'],
                    'event_id' => $eventId,
                ]
            );

            if ($result) {
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Process meeting scheduled - awards points to company contacts
     */
    public function processMeetingScheduled(array $company, array $contacts, array $sponsor, ?int $eventId = null): int
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        $processed = 0;
        foreach ($contacts as $contact) {
            if (empty($contact['email'])) {
                continue;
            }

            $externalId = 'meeting_' . $company['id'] . '_' . $sponsor['id'] . '_' . ($eventId ?? 0) . '_' . md5($contact['email']);
            $result = $this->awardPointsForAction(
                $contact['email'],
                self::ACTION_MEETING_SCHEDULED,
                $externalId,
                [
                    'company_id' => $company['id'],
                    'sponsor_id' => $sponsor['id'],
                    'event_id' => $eventId,
                ]
            );

            if ($result) {
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Process live match with meeting - awards points to both company and sponsor contacts
     */
    public function processLiveMatchWithMeeting(
        array $company,
        array $companyContacts,
        array $sponsor,
        array $sponsorContacts,
        ?int $eventId = null,
        ?int $meetingBlockId = null
    ): array {
        if (!$this->isEnabled()) {
            return ['company' => 0, 'sponsor' => 0];
        }

        $companyProcessed = 0;
        $sponsorProcessed = 0;

        // Award points to company contacts
        foreach ($companyContacts as $contact) {
            if (empty($contact['email'])) {
                continue;
            }

            $externalId = 'live_match_c_' . $company['id'] . '_' . $sponsor['id'] . '_' . ($meetingBlockId ?? 0) . '_' . md5($contact['email']);
            $result = $this->awardPointsForAction(
                $contact['email'],
                self::ACTION_LIVE_MATCH_COMPANY,
                $externalId,
                [
                    'company_id' => $company['id'],
                    'sponsor_id' => $sponsor['id'],
                    'event_id' => $eventId,
                    'meeting_block_id' => $meetingBlockId,
                ]
            );

            if ($result) {
                $companyProcessed++;
            }
        }

        // Award points to sponsor contacts
        foreach ($sponsorContacts as $contact) {
            if (empty($contact['email'])) {
                continue;
            }

            $externalId = 'live_match_s_' . $company['id'] . '_' . $sponsor['id'] . '_' . ($meetingBlockId ?? 0) . '_' . md5($contact['email']);
            $result = $this->awardPointsForAction(
                $contact['email'],
                self::ACTION_LIVE_MATCH_SPONSOR,
                $externalId,
                [
                    'company_id' => $company['id'],
                    'sponsor_id' => $sponsor['id'],
                    'event_id' => $eventId,
                    'meeting_block_id' => $meetingBlockId,
                ]
            );

            if ($result) {
                $sponsorProcessed++;
            }
        }

        return [
            'company' => $companyProcessed,
            'sponsor' => $sponsorProcessed,
        ];
    }

    /**
     * Make an HTTP request to the Omniwallet API
     */
    private function request(string $method, string $endpoint, ?array $data = null): ?array
    {
        $url = self::API_BASE_URL . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->apiToken,
            'X-Omniwallet-Account: ' . $this->account,
            'Accept: application/vnd.api+json',
            'Content-Type: application/vnd.api+json',
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'GET':
            default:
                // GET is the default
                break;
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->logError('cURL error: ' . $error, [
                'method' => $method,
                'endpoint' => $endpoint,
            ]);
            return null;
        }

        // Handle non-success HTTP codes
        if ($httpCode >= 400) {
            $this->logError('HTTP error ' . $httpCode, [
                'method' => $method,
                'endpoint' => $endpoint,
                'response' => $response,
            ]);

            if ($httpCode === 404) {
                return ['error' => 'not_found', 'code' => 404];
            }

            return null;
        }

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logError('JSON decode error: ' . json_last_error_msg(), [
                'response' => substr($response, 0, 500),
            ]);
            return null;
        }

        return $decoded;
    }

    /**
     * Split a full name into first and last name
     */
    private function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName), 2);

        return [
            'first' => $parts[0] ?? 'Unknown',
            'last' => $parts[1] ?? null,
        ];
    }

    /**
     * Log an error (can be extended to use a proper logging system)
     */
    private function logError(string $message, array $context = []): void
    {
        $logMessage = date('Y-m-d H:i:s') . ' [Omniwallet] ' . $message;
        if (!empty($context)) {
            $logMessage .= ' | Context: ' . json_encode($context);
        }

        error_log($logMessage);
    }

    /**
     * Test the API connection
     */
    public function testConnection(): array
    {
        if (empty($this->apiToken) || empty($this->account)) {
            return [
                'success' => false,
                'message' => 'API Token o Account no configurados',
            ];
        }

        // Try to get settings as a connection test
        $response = $this->request('GET', '/settings');

        if ($response === null) {
            return [
                'success' => false,
                'message' => 'No se pudo conectar con la API de Omniwallet',
            ];
        }

        if (isset($response['error'])) {
            return [
                'success' => false,
                'message' => 'Error de autenticación o cuenta no válida',
            ];
        }

        return [
            'success' => true,
            'message' => 'Conexión exitosa con Omniwallet',
        ];
    }
}
