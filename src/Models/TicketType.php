<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * TicketType Model
 * TLOS - The Last of SaaS
 */
class TicketType extends Model
{
    protected string $table = 'ticket_types';

    protected array $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'max_tickets',
        'tickets_sold',
        'sale_start',
        'sale_end',
        'active',
        'requires_approval',
    ];

    /**
     * Get ticket types for an event
     */
    public function getByEvent(int $eventId): array
    {
        return $this->where(['event_id' => $eventId], ['name' => 'ASC']);
    }

    /**
     * Get active ticket types for an event
     */
    public function getActiveByEvent(int $eventId): array
    {
        $sql = "SELECT tt.*
                FROM ticket_types tt
                WHERE tt.event_id = ? AND tt.active = 1
                AND (tt.sale_start IS NULL OR tt.sale_start <= NOW())
                AND (tt.sale_end IS NULL OR tt.sale_end >= NOW())
                ORDER BY tt.price ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Get available ticket types for an event (for public purchase)
     */
    public function getAvailableForEvent(int $eventId): array
    {
        $sql = "SELECT tt.*,
                       COALESCE(tt.max_tickets, 0) as total_quantity,
                       COALESCE(tt.tickets_sold, 0) as sold_quantity
                FROM ticket_types tt
                WHERE tt.event_id = ? AND tt.active = 1
                AND (tt.sale_start IS NULL OR tt.sale_start <= NOW())
                AND (tt.sale_end IS NULL OR tt.sale_end >= NOW())
                AND (tt.max_tickets IS NULL OR tt.max_tickets = 0 OR tt.tickets_sold < tt.max_tickets)
                ORDER BY tt.price ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Check if ticket type has availability
     */
    public function hasAvailability(int $ticketTypeId): bool
    {
        $ticketType = $this->find($ticketTypeId);
        if (!$ticketType) {
            return false;
        }

        if (!$ticketType['active']) {
            return false;
        }

        // Unlimited
        if ($ticketType['max_tickets'] === null || $ticketType['max_tickets'] == 0) {
            return true;
        }

        return ($ticketType['tickets_sold'] ?? 0) < $ticketType['max_tickets'];
    }

    /**
     * Increment sold count
     */
    public function incrementSold(int $ticketTypeId, int $amount = 1): bool
    {
        $sql = "UPDATE ticket_types SET tickets_sold = tickets_sold + ? WHERE id = ?";
        $this->db->query($sql, [$amount, $ticketTypeId]);
        $result = true;

        // Check if sold out
        $ticketType = $this->find($ticketTypeId);
        if ($ticketType && $ticketType['max_tickets'] !== null && $ticketType['max_tickets'] > 0) {
            if (($ticketType['tickets_sold'] ?? 0) >= $ticketType['max_tickets']) {
                $this->update($ticketTypeId, ['active' => 0]);
            }
        }

        return $result;
    }

    /**
     * Decrement sold count (for refunds)
     */
    public function decrementSold(int $ticketTypeId, int $amount = 1): bool
    {
        $sql = "UPDATE ticket_types SET tickets_sold = GREATEST(0, tickets_sold - ?) WHERE id = ?";
        $this->db->query($sql, [$amount, $ticketTypeId]);
        $result = true;

        // Reactivate if was sold out
        $ticketType = $this->find($ticketTypeId);
        if ($ticketType && !$ticketType['active']) {
            if ($ticketType['max_tickets'] === null || $ticketType['max_tickets'] == 0 || ($ticketType['tickets_sold'] ?? 0) < $ticketType['max_tickets']) {
                $this->update($ticketTypeId, ['active' => 1]);
            }
        }

        return $result;
    }

    /**
     * Get available quantity
     */
    public function getAvailableQuantity(int $ticketTypeId): ?int
    {
        $ticketType = $this->find($ticketTypeId);
        if (!$ticketType) {
            return null;
        }

        if ($ticketType['max_tickets'] === null || $ticketType['max_tickets'] == 0) {
            return null; // Unlimited
        }

        return max(0, $ticketType['max_tickets'] - ($ticketType['tickets_sold'] ?? 0));
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            1 => 'Activo',
            0 => 'Inactivo',
        ];
    }
}
