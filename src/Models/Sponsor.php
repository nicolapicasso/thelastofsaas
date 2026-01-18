<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Sponsor Model
 * TLOS - The Last of SaaS
 */
class Sponsor extends Model
{
    protected string $table = 'sponsors';

    protected array $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'website',
        'logo_url',
        'contact_name',
        'contact_email',
        'contact_phone',
        'code',
        'active',
        'max_simultaneous_meetings',
        'linkedin_url',
        'twitter_url',
    ];

    /**
     * Get sponsor by unique code
     */
    public function findByCode(string $code): ?array
    {
        return $this->findBy('code', $code);
    }

    /**
     * Get sponsor by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * Get active sponsors
     */
    public function getActive(): array
    {
        return $this->where(['active' => 1], ['name' => 'ASC']);
    }

    /**
     * Get sponsors for an event
     */
    public function getByEvent(int $eventId): array
    {
        $sql = "SELECT s.*, es.level, es.display_order
                FROM sponsors s
                INNER JOIN event_sponsors es ON s.id = es.sponsor_id
                WHERE es.event_id = ? AND s.active = 1
                ORDER BY FIELD(es.level, 'platinum', 'gold', 'silver', 'bronze'), es.display_order ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Get events for a sponsor
     */
    public function getEvents(int $sponsorId): array
    {
        $sql = "SELECT e.*, es.level
                FROM events e
                INNER JOIN event_sponsors es ON e.id = es.event_id
                WHERE es.sponsor_id = ?
                ORDER BY e.start_date DESC";

        return $this->db->fetchAll($sql, [$sponsorId]);
    }

    /**
     * Get companies selected by sponsor for an event
     */
    public function getSelectedCompanies(int $sponsorId, int $eventId): array
    {
        $sql = "SELECT c.*, ss.selected_at, ss.priority
                FROM companies c
                INNER JOIN sponsor_selections ss ON c.id = ss.company_id
                WHERE ss.sponsor_id = ? AND ss.event_id = ? AND c.active = 1
                ORDER BY ss.priority DESC, ss.selected_at ASC";

        return $this->db->fetchAll($sql, [$sponsorId, $eventId]);
    }

    /**
     * Get companies that selected this sponsor for an event
     */
    public function getInterestedCompanies(int $sponsorId, int $eventId): array
    {
        $sql = "SELECT c.*, cs.selected_at
                FROM companies c
                INNER JOIN company_selections cs ON c.id = cs.company_id
                WHERE cs.sponsor_id = ? AND cs.event_id = ? AND c.active = 1
                ORDER BY cs.selected_at ASC";

        return $this->db->fetchAll($sql, [$sponsorId, $eventId]);
    }

    /**
     * Get mutual matches (companies that sponsor selected AND that selected sponsor)
     */
    public function getMutualMatches(int $sponsorId, int $eventId): array
    {
        $sql = "SELECT c.*,
                       ss.selected_at as sponsor_selected_at,
                       cs.selected_at as company_selected_at
                FROM companies c
                INNER JOIN sponsor_selections ss ON c.id = ss.company_id
                INNER JOIN company_selections cs ON c.id = cs.company_id AND cs.sponsor_id = ss.sponsor_id AND cs.event_id = ss.event_id
                WHERE ss.sponsor_id = ? AND ss.event_id = ? AND c.active = 1
                ORDER BY ss.selected_at ASC";

        return $this->db->fetchAll($sql, [$sponsorId, $eventId]);
    }

    /**
     * Select a company
     */
    public function selectCompany(int $sponsorId, int $companyId, int $eventId, int $priority = 0): bool
    {
        $sql = "INSERT INTO sponsor_selections (event_id, sponsor_id, company_id, priority)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE priority = VALUES(priority)";

        return $this->db->execute($sql, [$eventId, $sponsorId, $companyId, $priority]);
    }

    /**
     * Deselect a company
     */
    public function deselectCompany(int $sponsorId, int $companyId, int $eventId): bool
    {
        $sql = "DELETE FROM sponsor_selections WHERE sponsor_id = ? AND company_id = ? AND event_id = ?";
        return $this->db->execute($sql, [$sponsorId, $companyId, $eventId]);
    }

    /**
     * Get meetings for sponsor in an event
     */
    public function getMeetings(int $sponsorId, int $eventId): array
    {
        $sql = "SELECT ma.*, ms.slot_time, ms.room_number, ms.room_name,
                       mb.name as block_name, mb.event_date, mb.meeting_duration,
                       c.name as company_name, c.logo_url as company_logo
                FROM meeting_assignments ma
                INNER JOIN meeting_slots ms ON ma.slot_id = ms.id
                INNER JOIN meeting_blocks mb ON ms.block_id = mb.id
                INNER JOIN companies c ON ma.company_id = c.id
                WHERE ma.sponsor_id = ? AND ma.event_id = ?
                ORDER BY mb.event_date ASC, ms.slot_time ASC";

        return $this->db->fetchAll($sql, [$sponsorId, $eventId]);
    }

    /**
     * Count simultaneous meetings at a specific time
     */
    public function countMeetingsAtTime(int $sponsorId, int $blockId, string $time): int
    {
        $sql = "SELECT COUNT(*)
                FROM meeting_assignments ma
                INNER JOIN meeting_slots ms ON ma.slot_id = ms.id
                WHERE ma.sponsor_id = ? AND ms.block_id = ? AND ms.slot_time = ?
                AND ma.status NOT IN ('cancelled')";

        return (int) $this->db->fetchColumn($sql, [$sponsorId, $blockId, $time]);
    }

    /**
     * Send message to company
     */
    public function sendMessage(int $sponsorId, int $companyId, int $eventId, string $message): bool
    {
        $sql = "INSERT INTO sponsor_messages (event_id, sponsor_id, company_id, message)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE message = VALUES(message), sent_at = CURRENT_TIMESTAMP";

        return $this->db->execute($sql, [$eventId, $sponsorId, $companyId, $message]);
    }

    /**
     * Get sent messages
     */
    public function getSentMessages(int $sponsorId, int $eventId): array
    {
        $sql = "SELECT sm.*, c.name as company_name
                FROM sponsor_messages sm
                INNER JOIN companies c ON sm.company_id = c.id
                WHERE sm.sponsor_id = ? AND sm.event_id = ?
                ORDER BY sm.sent_at DESC";

        return $this->db->fetchAll($sql, [$sponsorId, $eventId]);
    }

    /**
     * Generate unique code
     */
    public static function generateUniqueCode(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Get contact emails as array
     */
    public function getEmailsArray(array $sponsor): array
    {
        if (empty($sponsor['contact_email'])) {
            return [];
        }

        return array_map('trim', explode(',', $sponsor['contact_email']));
    }

    /**
     * Get priority level options
     */
    public static function getLevelOptions(): array
    {
        return [
            'platinum' => 'Platinum',
            'gold' => 'Gold',
            'silver' => 'Silver',
            'bronze' => 'Bronze',
        ];
    }
}
