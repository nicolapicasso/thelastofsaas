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
        'is_free',
        'sponsor_id',
        'quantity_available',
        'quantity_sold',
        'sale_start_date',
        'sale_end_date',
        'status',
        'requires_approval',
        'max_per_purchase',
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
        $sql = "SELECT tt.*, s.name as sponsor_name, s.logo_url as sponsor_logo
                FROM ticket_types tt
                LEFT JOIN sponsors s ON tt.sponsor_id = s.id
                WHERE tt.event_id = ? AND tt.status = 'active'
                AND (tt.sale_start_date IS NULL OR tt.sale_start_date <= NOW())
                AND (tt.sale_end_date IS NULL OR tt.sale_end_date >= NOW())
                ORDER BY tt.price ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Get ticket type for a sponsor in an event
     */
    public function getBySponsor(int $eventId, int $sponsorId): ?array
    {
        $sql = "SELECT * FROM ticket_types WHERE event_id = ? AND sponsor_id = ? LIMIT 1";
        return $this->db->fetch($sql, [$eventId, $sponsorId]);
    }

    /**
     * Create free ticket type for sponsor
     */
    public function createForSponsor(int $eventId, int $sponsorId, string $sponsorName): int
    {
        return $this->create([
            'event_id' => $eventId,
            'name' => "Entrada {$sponsorName}",
            'description' => "Entrada gratuita invitación de {$sponsorName}",
            'price' => 0.00,
            'is_free' => 1,
            'sponsor_id' => $sponsorId,
            'status' => 'active',
        ]);
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

        if ($ticketType['status'] !== 'active') {
            return false;
        }

        // Unlimited
        if ($ticketType['quantity_available'] === null) {
            return true;
        }

        return $ticketType['quantity_sold'] < $ticketType['quantity_available'];
    }

    /**
     * Increment sold count
     */
    public function incrementSold(int $ticketTypeId, int $amount = 1): bool
    {
        $sql = "UPDATE ticket_types SET quantity_sold = quantity_sold + ? WHERE id = ?";
        $this->db->query($sql, [$amount, $ticketTypeId]);
        $result = true;

        // Check if sold out
        $ticketType = $this->find($ticketTypeId);
        if ($ticketType && $ticketType['quantity_available'] !== null) {
            if ($ticketType['quantity_sold'] >= $ticketType['quantity_available']) {
                $this->update($ticketTypeId, ['status' => 'sold_out']);
            }
        }

        return $result;
    }

    /**
     * Decrement sold count (for refunds)
     */
    public function decrementSold(int $ticketTypeId, int $amount = 1): bool
    {
        $sql = "UPDATE ticket_types SET quantity_sold = GREATEST(0, quantity_sold - ?) WHERE id = ?";
        $this->db->query($sql, [$amount, $ticketTypeId]);
        $result = true;

        // Reactivate if was sold out
        $ticketType = $this->find($ticketTypeId);
        if ($ticketType && $ticketType['status'] === 'sold_out') {
            if ($ticketType['quantity_available'] === null || $ticketType['quantity_sold'] < $ticketType['quantity_available']) {
                $this->update($ticketTypeId, ['status' => 'active']);
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

        if ($ticketType['quantity_available'] === null) {
            return null; // Unlimited
        }

        return max(0, $ticketType['quantity_available'] - $ticketType['quantity_sold']);
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'sold_out' => 'Agotado',
        ];
    }
}
