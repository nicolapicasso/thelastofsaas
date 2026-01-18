<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Ticket Model
 * TLOS - The Last of SaaS
 */
class Ticket extends Model
{
    protected string $table = 'tickets';

    protected array $fillable = [
        'ticket_type_id',
        'event_id',
        'attendee_first_name',
        'attendee_last_name',
        'attendee_email',
        'attendee_phone',
        'attendee_job_title',
        'attendee_company_name',
        'attendee_company_website',
        'attendee_company_size',
        'invited_by_type',
        'invited_by_sponsor_id',
        'status',
        'payment_status',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'amount_paid',
        'ticket_code',
        'confirmation_code',
        'registration_ip',
        'user_agent',
        'notes',
        'event_date',
        'confirmed_at',
        'checked_in_at',
    ];

    /**
     * Get ticket by code
     */
    public function findByCode(string $code): ?array
    {
        return $this->findBy('ticket_code', $code);
    }

    /**
     * Get ticket by confirmation code
     */
    public function findByConfirmationCode(string $code): ?array
    {
        return $this->findBy('confirmation_code', $code);
    }

    /**
     * Get tickets for an event
     */
    public function getByEvent(int $eventId): array
    {
        $sql = "SELECT t.*, tt.name as ticket_type_name, s.name as sponsor_name
                FROM tickets t
                INNER JOIN ticket_types tt ON t.ticket_type_id = tt.id
                LEFT JOIN sponsors s ON t.invited_by_sponsor_id = s.id
                WHERE t.event_id = ?
                ORDER BY t.created_at DESC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Get tickets by email
     */
    public function getByEmail(string $email, ?int $eventId = null): array
    {
        if ($eventId) {
            return $this->where([
                'attendee_email' => $email,
                'event_id' => $eventId,
            ], ['created_at' => 'DESC']);
        }

        return $this->where(['attendee_email' => $email], ['created_at' => 'DESC']);
    }

    /**
     * Get tickets invited by sponsor
     */
    public function getBySponsor(int $sponsorId, ?int $eventId = null): array
    {
        $conditions = [
            'invited_by_sponsor_id' => $sponsorId,
            'invited_by_type' => 'sponsor',
        ];

        if ($eventId) {
            $conditions['event_id'] = $eventId;
        }

        return $this->where($conditions, ['created_at' => 'DESC']);
    }

    /**
     * Create a new ticket
     */
    public function createTicket(array $data): int
    {
        // Generate codes
        $data['ticket_code'] = $this->generateTicketCode();
        $data['confirmation_code'] = $this->generateConfirmationCode();

        // Set default status for free tickets
        if (empty($data['amount_paid']) || $data['amount_paid'] == 0) {
            $data['payment_status'] = 'free';
            $data['status'] = 'confirmed';
            $data['confirmed_at'] = date('Y-m-d H:i:s');
        }

        return $this->create($data);
    }

    /**
     * Confirm ticket (after payment)
     */
    public function confirm(int $ticketId): bool
    {
        return $this->update($ticketId, [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'confirmed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Check in ticket
     */
    public function checkIn(int $ticketId): bool
    {
        return $this->update($ticketId, [
            'status' => 'checked_in',
            'checked_in_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Cancel ticket
     */
    public function cancel(int $ticketId): bool
    {
        return $this->update($ticketId, [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Refund ticket
     */
    public function refund(int $ticketId): bool
    {
        return $this->update($ticketId, [
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);
    }

    /**
     * Generate unique ticket code (for QR)
     */
    public function generateTicketCode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = '';
            for ($i = 0; $i < 10; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while ($this->findByCode($code));

        return $code;
    }

    /**
     * Generate confirmation code
     */
    public function generateConfirmationCode(): string
    {
        return strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }

    /**
     * Get full attendee name
     */
    public function getAttendeeName(array $ticket): string
    {
        return trim($ticket['attendee_first_name'] . ' ' . $ticket['attendee_last_name']);
    }

    /**
     * Check if ticket is valid for check-in
     */
    public function isValidForCheckIn(array $ticket): bool
    {
        return $ticket['status'] === 'confirmed' && $ticket['event_date'] === date('Y-m-d');
    }

    /**
     * Get stats for event
     */
    public function getEventStats(int $eventId): array
    {
        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'checked_in' THEN 1 ELSE 0 END) as checked_in,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN payment_status = 'paid' THEN amount_paid ELSE 0 END) as total_revenue
                FROM tickets WHERE event_id = ?";

        return $this->db->fetch($sql, [$eventId]) ?: [];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmado',
            'cancelled' => 'Cancelado',
            'checked_in' => 'Check-in realizado',
        ];
    }

    /**
     * Get payment status options
     */
    public static function getPaymentStatusOptions(): array
    {
        return [
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'refunded' => 'Reembolsado',
            'free' => 'Gratuito',
        ];
    }
}
