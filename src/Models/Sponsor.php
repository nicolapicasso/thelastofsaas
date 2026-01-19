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
     * Search sponsors by name with pagination
     */
    public function searchByName(string $search, int $page = 1, int $perPage = 20, array $conditions = []): array
    {
        $offset = ($page - 1) * $perPage;

        // Build WHERE clause
        $where = "WHERE name LIKE ?";
        $params = ['%' . $search . '%'];

        foreach ($conditions as $key => $value) {
            $where .= " AND {$key} = ?";
            $params[] = $value;
        }

        // Count total
        $countSql = "SELECT COUNT(*) FROM {$this->table} {$where}";
        $total = (int) $this->db->fetchColumn($countSql, $params);

        // Get data
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY name ASC LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->db->fetchAll($sql, $params);

        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ];
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
     * Get events for a sponsor (published or active only)
     */
    public function getEvents(int $sponsorId): array
    {
        $sql = "SELECT e.*, es.level
                FROM events e
                INNER JOIN event_sponsors es ON e.id = es.event_id
                WHERE es.sponsor_id = ? AND e.status IN ('published', 'active')
                ORDER BY e.start_date DESC";

        return $this->db->fetchAll($sql, [$sponsorId]);
    }

    /**
     * Check if sponsor participates in an event
     */
    public function participatesInEvent(int $sponsorId, int $eventId): bool
    {
        $sql = "SELECT COUNT(*) FROM event_sponsors WHERE sponsor_id = ? AND event_id = ?";
        return (int) $this->db->fetchColumn($sql, [$sponsorId, $eventId]) > 0;
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

        $this->db->query($sql, [$eventId, $sponsorId, $companyId, $priority]);
        return true;
    }

    /**
     * Deselect a company
     */
    public function deselectCompany(int $sponsorId, int $companyId, int $eventId): bool
    {
        $sql = "DELETE FROM sponsor_selections WHERE sponsor_id = ? AND company_id = ? AND event_id = ?";
        $this->db->query($sql, [$sponsorId, $companyId, $eventId]);
        return true;
    }

    /**
     * Alias for deselectCompany
     */
    public function unselectCompany(int $sponsorId, int $companyId, int $eventId): bool
    {
        return $this->deselectCompany($sponsorId, $companyId, $eventId);
    }

    /**
     * Get selections (alias for getSelectedCompanies with company_id included)
     */
    public function getSelections(int $sponsorId, int $eventId): array
    {
        $sql = "SELECT c.*, ss.selected_at, ss.priority, c.id as company_id
                FROM companies c
                INNER JOIN sponsor_selections ss ON c.id = ss.company_id
                WHERE ss.sponsor_id = ? AND ss.event_id = ? AND c.active = 1
                ORDER BY ss.priority DESC, ss.selected_at ASC";

        return $this->db->fetchAll($sql, [$sponsorId, $eventId]);
    }

    /**
     * Check if sponsor has selected a company
     */
    public function hasSelected(int $sponsorId, int $companyId, int $eventId): bool
    {
        $sql = "SELECT COUNT(*) FROM sponsor_selections WHERE sponsor_id = ? AND company_id = ? AND event_id = ?";
        return (int) $this->db->fetchColumn($sql, [$sponsorId, $companyId, $eventId]) > 0;
    }

    /**
     * Check if it's a mutual match
     */
    public function isMutualMatch(int $sponsorId, int $companyId, int $eventId): bool
    {
        $sql = "SELECT COUNT(*) FROM sponsor_selections ss
                INNER JOIN company_selections cs
                ON ss.sponsor_id = cs.sponsor_id AND ss.company_id = cs.company_id AND ss.event_id = cs.event_id
                WHERE ss.sponsor_id = ? AND ss.company_id = ? AND ss.event_id = ?";
        return (int) $this->db->fetchColumn($sql, [$sponsorId, $companyId, $eventId]) > 0;
    }

    /**
     * Get scheduled meetings for sponsor
     */
    public function getScheduledMeetings(int $sponsorId, int $eventId): array
    {
        return $this->getMeetings($sponsorId, $eventId);
    }

    /**
     * Save message to a company
     */
    public function saveMessage(int $sponsorId, int $companyId, int $eventId, string $message): bool
    {
        return $this->sendMessage($sponsorId, $companyId, $eventId, $message);
    }

    /**
     * Get meetings for sponsor in an event
     */
    public function getMeetings(int $sponsorId, int $eventId): array
    {
        $sql = "SELECT ma.*, ms.slot_time, ms.room_number, ms.room_name,
                       mb.name as block_name, mb.event_date, mb.slot_duration,
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

        $this->db->query($sql, [$eventId, $sponsorId, $companyId, $message]);
        return true;
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
