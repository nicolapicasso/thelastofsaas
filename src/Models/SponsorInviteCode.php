<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\UrlHelper;

/**
 * SponsorInviteCode Model
 * TLOS - The Last of SaaS
 */
class SponsorInviteCode extends Model
{
    protected string $table = 'sponsor_invite_codes';

    protected array $fillable = [
        'event_id',
        'sponsor_id',
        'code',
        'description',
        'max_uses',
        'times_used',
        'ticket_type_id',
        'discount_type',
        'discount_amount',
        'valid_from',
        'valid_until',
        'active',
    ];

    /**
     * Discount types
     */
    public const DISCOUNT_TYPES = [
        'none' => 'Sin descuento',
        'percentage' => 'Porcentaje',
        'fixed' => 'Cantidad fija',
    ];

    /**
     * Find code by code string and event
     */
    public function findByCode(string $code, int $eventId): ?array
    {
        $sql = "SELECT ic.*, s.name as sponsor_name, s.logo_url as sponsor_logo,
                       tt.name as ticket_type_name
                FROM sponsor_invite_codes ic
                INNER JOIN sponsors s ON ic.sponsor_id = s.id
                LEFT JOIN ticket_types tt ON ic.ticket_type_id = tt.id
                WHERE ic.code = ? AND ic.event_id = ?
                LIMIT 1";

        return $this->db->fetch($sql, [$code, $eventId]);
    }

    /**
     * Get codes by sponsor
     */
    public function getBySponsor(int $sponsorId, ?int $eventId = null): array
    {
        $sql = "SELECT ic.*, e.name as event_name, tt.name as ticket_type_name
                FROM sponsor_invite_codes ic
                INNER JOIN events e ON ic.event_id = e.id
                LEFT JOIN ticket_types tt ON ic.ticket_type_id = tt.id
                WHERE ic.sponsor_id = ?";

        $params = [$sponsorId];

        if ($eventId !== null) {
            $sql .= " AND ic.event_id = ?";
            $params[] = $eventId;
        }

        $sql .= " ORDER BY ic.created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get codes by event
     */
    public function getByEvent(int $eventId): array
    {
        $sql = "SELECT ic.*, s.name as sponsor_name, s.logo_url as sponsor_logo,
                       tt.name as ticket_type_name
                FROM sponsor_invite_codes ic
                INNER JOIN sponsors s ON ic.sponsor_id = s.id
                LEFT JOIN ticket_types tt ON ic.ticket_type_id = tt.id
                WHERE ic.event_id = ?
                ORDER BY s.name ASC, ic.created_at DESC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Validate if code can be used
     */
    public function isValid(array $code): array
    {
        $errors = [];

        // Check if active
        if (!$code['active']) {
            $errors[] = 'El codigo esta desactivado.';
        }

        // Check max uses
        if ($code['max_uses'] !== null && $code['times_used'] >= $code['max_uses']) {
            $errors[] = 'El codigo ha alcanzado el maximo de usos permitidos.';
        }

        // Check date validity
        $now = date('Y-m-d H:i:s');
        if ($code['valid_from'] && $now < $code['valid_from']) {
            $errors[] = 'El codigo aun no es valido.';
        }
        if ($code['valid_until'] && $now > $code['valid_until']) {
            $errors[] = 'El codigo ha expirado.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Use the code (increment times_used)
     */
    public function useCode(int $codeId): bool
    {
        $sql = "UPDATE sponsor_invite_codes SET times_used = times_used + 1 WHERE id = ?";
        $stmt = $this->db->query($sql, [$codeId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(array $code, float $originalPrice): float
    {
        if ($code['discount_type'] === 'none' || $code['discount_amount'] <= 0) {
            return 0;
        }

        if ($code['discount_type'] === 'percentage') {
            return $originalPrice * ($code['discount_amount'] / 100);
        }

        // Fixed amount
        return min($code['discount_amount'], $originalPrice);
    }

    /**
     * Generate unique code
     */
    public function generateCode(string $prefix = ''): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = $prefix;

        for ($i = 0; $i < 8; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $code;
    }

    /**
     * Generate unique code for sponsor
     */
    public function generateUniqueCode(int $eventId, string $prefix = ''): string
    {
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = $this->generateCode($prefix);
            if (!$this->findByCode($code, $eventId)) {
                return $code;
            }
        }

        // Fallback with timestamp
        return $prefix . strtoupper(substr(md5((string) time()), 0, 8));
    }

    /**
     * Get usage statistics for a code
     */
    public function getUsageStats(int $codeId): array
    {
        $sql = "SELECT
                    COUNT(*) as total_tickets,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'used' THEN 1 ELSE 0 END) as checked_in,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM tickets
                WHERE invite_code_id = ?";

        return $this->db->fetch($sql, [$codeId]) ?: [
            'total_tickets' => 0,
            'confirmed' => 0,
            'checked_in' => 0,
            'pending' => 0,
            'cancelled' => 0,
        ];
    }

    /**
     * Get tickets created with this code
     */
    public function getTickets(int $codeId): array
    {
        $sql = "SELECT t.*, tt.name as ticket_type_name
                FROM tickets t
                LEFT JOIN ticket_types tt ON t.ticket_type_id = tt.id
                WHERE t.invite_code_id = ?
                ORDER BY t.created_at DESC";

        return $this->db->fetchAll($sql, [$codeId]);
    }

    /**
     * Get statistics by sponsor for an event
     */
    public function getSponsorStats(int $sponsorId, int $eventId): array
    {
        $sql = "SELECT
                    COUNT(DISTINCT ic.id) as total_codes,
                    SUM(ic.times_used) as total_uses,
                    (SELECT COUNT(*) FROM tickets t
                     INNER JOIN sponsor_invite_codes ic2 ON t.invite_code_id = ic2.id
                     WHERE ic2.sponsor_id = ? AND t.event_id = ? AND t.status IN ('confirmed', 'used')) as confirmed_tickets
                FROM sponsor_invite_codes ic
                WHERE ic.sponsor_id = ? AND ic.event_id = ?";

        return $this->db->fetch($sql, [$sponsorId, $eventId, $sponsorId, $eventId]) ?: [
            'total_codes' => 0,
            'total_uses' => 0,
            'confirmed_tickets' => 0,
        ];
    }

    /**
     * Get discount type options
     */
    public static function getDiscountTypes(): array
    {
        return self::DISCOUNT_TYPES;
    }

    /**
     * Generate full registration URL for a sponsor invite code
     *
     * @param array $code Code data with 'code' key
     * @param array $event Event data with 'slug' key
     * @return string Full registration URL
     */
    public static function generateRegistrationUrl(array $code, array $event): string
    {
        return UrlHelper::sponsorCodeUrl($event, $code);
    }
}
