<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Event Model
 * TLOS - The Last of SaaS
 */
class Event extends Model
{
    protected string $table = 'events';

    protected array $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'featured_image',
        'location',
        'address',
        'city',
        'coordinates',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'max_attendees',
        'status',
        'registration_open',
        'matching_enabled',
        'meetings_enabled',
        'is_featured',
        'meta_title',
        'meta_description',
    ];

    /**
     * Get event by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * Get published events
     */
    public function getPublished(): array
    {
        return $this->where(
            ['status' => 'published'],
            ['start_date' => 'ASC']
        );
    }

    /**
     * Get active events
     */
    public function getActive(): array
    {
        return $this->where(
            ['status' => 'active'],
            ['start_date' => 'ASC']
        );
    }

    /**
     * Get upcoming events (published or active, future dates)
     */
    public function getUpcoming(): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE status IN ('published', 'active')
                AND start_date >= CURDATE()
                ORDER BY start_date ASC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Get event sponsors with their level
     */
    public function getSponsors(int $eventId): array
    {
        $sql = "SELECT s.*, es.level, es.display_order, es.max_free_tickets
                FROM sponsors s
                INNER JOIN event_sponsors es ON s.id = es.sponsor_id
                WHERE es.event_id = ?
                ORDER BY FIELD(es.level, 'platinum', 'gold', 'silver', 'bronze'), es.display_order ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Get event sponsors by level
     */
    public function getSponsorsByLevel(int $eventId, string $level): array
    {
        $sql = "SELECT s.*, es.level, es.display_order
                FROM sponsors s
                INNER JOIN event_sponsors es ON s.id = es.sponsor_id
                WHERE es.event_id = ? AND es.level = ?
                ORDER BY es.display_order ASC";

        return $this->db->fetchAll($sql, [$eventId, $level]);
    }

    /**
     * Associate sponsor with event
     */
    public function addSponsor(int $eventId, int $sponsorId, string $level = 'bronze', int $order = 0): bool
    {
        $sql = "INSERT INTO event_sponsors (event_id, sponsor_id, level, display_order)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE level = VALUES(level), display_order = VALUES(display_order)";

        $this->db->query($sql, [$eventId, $sponsorId, $level, $order]);
        return true;
    }

    /**
     * Remove sponsor from event
     */
    public function removeSponsor(int $eventId, int $sponsorId): bool
    {
        $sql = "DELETE FROM event_sponsors WHERE event_id = ? AND sponsor_id = ?";
        $this->db->query($sql, [$eventId, $sponsorId]);
        return true;
    }

    /**
     * Update sponsor level in event
     */
    public function updateSponsorLevel(int $eventId, int $sponsorId, string $level): bool
    {
        $sql = "UPDATE event_sponsors SET level = ? WHERE event_id = ? AND sponsor_id = ?";
        $this->db->query($sql, [$level, $eventId, $sponsorId]);
        return true;
    }

    /**
     * Get event companies
     */
    public function getCompanies(int $eventId): array
    {
        $sql = "SELECT c.*
                FROM companies c
                INNER JOIN event_companies ec ON c.id = ec.company_id
                WHERE ec.event_id = ?
                ORDER BY c.name ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Associate company with event
     */
    public function addCompany(int $eventId, int $companyId): bool
    {
        $sql = "INSERT IGNORE INTO event_companies (event_id, company_id) VALUES (?, ?)";
        $this->db->query($sql, [$eventId, $companyId]);
        return true;
    }

    /**
     * Remove company from event
     */
    public function removeCompany(int $eventId, int $companyId): bool
    {
        $sql = "DELETE FROM event_companies WHERE event_id = ? AND company_id = ?";
        $this->db->query($sql, [$eventId, $companyId]);
        return true;
    }

    /**
     * Get event features
     */
    public function getFeatures(int $eventId): array
    {
        $sql = "SELECT * FROM event_features WHERE event_id = ? ORDER BY display_order ASC";
        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Add feature to event
     */
    public function addFeature(int $eventId, array $data): int
    {
        return $this->db->insert('event_features', [
            'event_id' => $eventId,
            'feature' => $data['feature'],
            'icon' => $data['icon'] ?? null,
            'display_order' => $data['display_order'] ?? 0,
        ]);
    }

    /**
     * Get event statistics
     */
    public function getStats(int $eventId): array
    {
        $event = $this->find($eventId);
        if (!$event) {
            return [];
        }

        // Count tickets
        $ticketsSql = "SELECT
            COUNT(*) as total_tickets,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_tickets,
            SUM(CASE WHEN status = 'used' THEN 1 ELSE 0 END) as checked_in,
            SUM(price) as total_revenue
            FROM tickets WHERE event_id = ?";
        $ticketStats = $this->db->fetch($ticketsSql, [$eventId]);

        // Count sponsors
        $sponsorsSql = "SELECT COUNT(*) FROM event_sponsors WHERE event_id = ?";
        $sponsorsCount = (int) $this->db->fetchColumn($sponsorsSql, [$eventId]);

        // Count companies registered
        $companiesSql = "SELECT COUNT(*) FROM event_companies WHERE event_id = ?";
        $companiesCount = (int) $this->db->fetchColumn($companiesSql, [$eventId]);

        // Count matches
        $matchesSql = "SELECT COUNT(*) FROM sponsor_selections ss
                       INNER JOIN company_selections cs
                       ON ss.sponsor_id = cs.sponsor_id AND ss.company_id = cs.company_id AND ss.event_id = cs.event_id
                       WHERE ss.event_id = ?";
        $matchesCount = (int) $this->db->fetchColumn($matchesSql, [$eventId]);

        // Count meetings
        $meetingsSql = "SELECT COUNT(*) FROM meeting_assignments WHERE event_id = ?";
        $meetingsCount = (int) $this->db->fetchColumn($meetingsSql, [$eventId]);

        return [
            'max_attendees' => (int) $event['max_attendees'],
            'tickets_sold' => (int) ($ticketStats['total_tickets'] ?? 0),
            'tickets_confirmed' => (int) ($ticketStats['confirmed_tickets'] ?? 0),
            'checked_in' => (int) ($ticketStats['checked_in'] ?? 0),
            'available_capacity' => (int) $event['max_attendees'] - (int) ($ticketStats['confirmed_tickets'] ?? 0),
            'total_revenue' => (float) ($ticketStats['total_revenue'] ?? 0),
            'sponsors_count' => $sponsorsCount,
            'companies_count' => $companiesCount,
            'matches_count' => $matchesCount,
            'meetings_count' => $meetingsCount,
        ];
    }

    /**
     * Check if event has available capacity
     */
    public function hasCapacity(int $eventId): bool
    {
        $event = $this->find($eventId);
        if (!$event) {
            return false;
        }

        $confirmedSql = "SELECT COUNT(*) FROM tickets WHERE event_id = ? AND status IN ('confirmed', 'used')";
        $confirmed = (int) $this->db->fetchColumn($confirmedSql, [$eventId]);

        return $confirmed < (int) $event['max_attendees'];
    }

    /**
     * Get all status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'draft' => 'Borrador',
            'published' => 'Publicado',
            'active' => 'Activo',
            'finished' => 'Finalizado',
            'cancelled' => 'Cancelado',
        ];
    }
}
