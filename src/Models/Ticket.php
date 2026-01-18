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
        'event_id',
        'ticket_type_id',
        'sponsor_id',
        'code',
        'attendee_name',
        'attendee_email',
        'attendee_phone',
        'attendee_company',
        'attendee_position',
        'price',
        'status',
        'stripe_payment_id',
        'notes',
        'used_at',
    ];

    /**
     * Get ticket by code
     */
    public function findByCode(string $code): ?array
    {
        return $this->findBy('code', $code);
    }

    /**
     * Get tickets for event
     */
    public function getByEvent(int $eventId): array
    {
        $sql = "SELECT t.*, tt.name as ticket_type_name, s.name as sponsor_name
                FROM tickets t
                LEFT JOIN ticket_types tt ON t.ticket_type_id = tt.id
                LEFT JOIN sponsors s ON t.sponsor_id = s.id
                WHERE t.event_id = ?
                ORDER BY t.created_at DESC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Get tickets by status for event
     */
    public function getByStatus(int $eventId, string $status): array
    {
        $sql = "SELECT t.*, tt.name as ticket_type_name
                FROM tickets t
                LEFT JOIN ticket_types tt ON t.ticket_type_id = tt.id
                WHERE t.event_id = ? AND t.status = ?
                ORDER BY t.created_at DESC";

        return $this->db->fetchAll($sql, [$eventId, $status]);
    }

    /**
     * Get tickets invited by sponsor
     */
    public function getBySponsor(int $eventId, int $sponsorId): array
    {
        $sql = "SELECT t.*, tt.name as ticket_type_name
                FROM tickets t
                LEFT JOIN ticket_types tt ON t.ticket_type_id = tt.id
                WHERE t.event_id = ? AND t.sponsor_id = ?
                ORDER BY t.created_at DESC";

        return $this->db->fetchAll($sql, [$eventId, $sponsorId]);
    }

    /**
     * Generate unique ticket code
     */
    public function generateCode(): string
    {
        do {
            $code = 'TKT-' . strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
        } while ($this->findByCode($code));

        return $code;
    }

    /**
     * Mark ticket as used (check-in)
     */
    public function checkIn(int $ticketId): bool
    {
        return $this->update($ticketId, [
            'status' => 'used',
            'used_at' => date('Y-m-d H:i:s'),
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
     * Check if ticket is valid for check-in
     */
    public function isValidForCheckIn(array $ticket): bool
    {
        return $ticket['status'] === 'confirmed';
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
                    SUM(CASE WHEN status = 'used' THEN 1 ELSE 0 END) as checked_in,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(price) as total_revenue
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
            'pending_payment' => 'Pendiente de Pago',
            'confirmed' => 'Confirmado',
            'used' => 'Utilizado',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
        ];
    }
}
