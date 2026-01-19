<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Company Model
 * TLOS - The Last of SaaS
 */
class Company extends Model
{
    protected string $table = 'companies';

    protected array $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'logo_url',
        'sector',
        'employees',
        'contact_name',
        'contact_position',
        'contact_email',
        'contact_phone',
        'code',
        'active',
        'notes',
    ];

    /**
     * Get company by unique code
     */
    public function findByCode(string $code): ?array
    {
        return $this->findBy('code', $code);
    }

    /**
     * Get company by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * Get active companies
     */
    public function getActive(): array
    {
        return $this->where(['active' => 1], ['name' => 'ASC']);
    }

    /**
     * Search companies by name with pagination
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
     * Get companies for an event
     */
    public function getByEvent(int $eventId): array
    {
        $sql = "SELECT c.*
                FROM companies c
                INNER JOIN event_companies ec ON c.id = ec.company_id
                WHERE ec.event_id = ? AND c.active = 1
                ORDER BY c.name ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Get events for a company (published or active only)
     */
    public function getEvents(int $companyId): array
    {
        $sql = "SELECT e.*
                FROM events e
                INNER JOIN event_companies ec ON e.id = ec.event_id
                WHERE ec.company_id = ? AND e.status IN ('published', 'active')
                ORDER BY e.start_date DESC";

        return $this->db->fetchAll($sql, [$companyId]);
    }

    /**
     * Check if company participates in an event
     */
    public function participatesInEvent(int $companyId, int $eventId): bool
    {
        $sql = "SELECT COUNT(*) FROM event_companies WHERE company_id = ? AND event_id = ?";
        return (int) $this->db->fetchColumn($sql, [$companyId, $eventId]) > 0;
    }

    /**
     * Register company for an event
     */
    public function registerForEvent(int $companyId, int $eventId): bool
    {
        $sql = "INSERT IGNORE INTO event_companies (event_id, company_id) VALUES (?, ?)";
        $this->db->query($sql, [$eventId, $companyId]);
        return true;
    }

    /**
     * Get sponsors selected by company for an event
     */
    public function getSelectedSponsors(int $companyId, int $eventId): array
    {
        $sql = "SELECT s.*, cs.selected_at, cs.priority, es.level
                FROM sponsors s
                INNER JOIN company_selections cs ON s.id = cs.sponsor_id
                LEFT JOIN event_sponsors es ON s.id = es.sponsor_id AND es.event_id = cs.event_id
                WHERE cs.company_id = ? AND cs.event_id = ? AND s.active = 1
                ORDER BY cs.priority DESC, cs.selected_at ASC";

        return $this->db->fetchAll($sql, [$companyId, $eventId]);
    }

    /**
     * Get sponsors that selected this company for an event
     */
    public function getInterestedSponsors(int $companyId, int $eventId): array
    {
        $sql = "SELECT s.*, ss.selected_at, es.level
                FROM sponsors s
                INNER JOIN sponsor_selections ss ON s.id = ss.sponsor_id
                LEFT JOIN event_sponsors es ON s.id = es.sponsor_id AND es.event_id = ss.event_id
                WHERE ss.company_id = ? AND ss.event_id = ? AND s.active = 1
                ORDER BY FIELD(es.level, 'platinum', 'gold', 'silver', 'bronze'), ss.selected_at ASC";

        return $this->db->fetchAll($sql, [$companyId, $eventId]);
    }

    /**
     * Get mutual matches (sponsors that company selected AND that selected company)
     */
    public function getMutualMatches(int $companyId, int $eventId): array
    {
        $sql = "SELECT s.*,
                       cs.selected_at as company_selected_at,
                       ss.selected_at as sponsor_selected_at,
                       es.level
                FROM sponsors s
                INNER JOIN company_selections cs ON s.id = cs.sponsor_id
                INNER JOIN sponsor_selections ss ON s.id = ss.sponsor_id AND ss.company_id = cs.company_id AND ss.event_id = cs.event_id
                LEFT JOIN event_sponsors es ON s.id = es.sponsor_id AND es.event_id = cs.event_id
                WHERE cs.company_id = ? AND cs.event_id = ? AND s.active = 1
                ORDER BY FIELD(es.level, 'platinum', 'gold', 'silver', 'bronze')";

        return $this->db->fetchAll($sql, [$companyId, $eventId]);
    }

    /**
     * Select a sponsor
     */
    public function selectSponsor(int $companyId, int $sponsorId, int $eventId, int $priority = 0): bool
    {
        $sql = "INSERT INTO company_selections (event_id, company_id, sponsor_id, priority)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE priority = VALUES(priority)";

        $this->db->query($sql, [$eventId, $companyId, $sponsorId, $priority]);
        return true;
    }

    /**
     * Deselect a sponsor
     */
    public function deselectSponsor(int $companyId, int $sponsorId, int $eventId): bool
    {
        $sql = "DELETE FROM company_selections WHERE company_id = ? AND sponsor_id = ? AND event_id = ?";
        $this->db->query($sql, [$companyId, $sponsorId, $eventId]);
        return true;
    }

    /**
     * Alias for deselectSponsor
     */
    public function unselectSponsor(int $companyId, int $sponsorId, int $eventId): bool
    {
        return $this->deselectSponsor($companyId, $sponsorId, $eventId);
    }

    /**
     * Get selections (alias for getSelectedSponsors with sponsor_id included)
     */
    public function getSelections(int $companyId, int $eventId): array
    {
        $sql = "SELECT s.*, cs.selected_at, cs.priority, s.id as sponsor_id, es.level
                FROM sponsors s
                INNER JOIN company_selections cs ON s.id = cs.sponsor_id
                LEFT JOIN event_sponsors es ON s.id = es.sponsor_id AND es.event_id = cs.event_id
                WHERE cs.company_id = ? AND cs.event_id = ? AND s.active = 1
                ORDER BY cs.priority DESC, cs.selected_at ASC";

        return $this->db->fetchAll($sql, [$companyId, $eventId]);
    }

    /**
     * Check if company has selected a sponsor
     */
    public function hasSelected(int $companyId, int $sponsorId, int $eventId): bool
    {
        $sql = "SELECT COUNT(*) FROM company_selections WHERE company_id = ? AND sponsor_id = ? AND event_id = ?";
        return (int) $this->db->fetchColumn($sql, [$companyId, $sponsorId, $eventId]) > 0;
    }

    /**
     * Check if it's a mutual match
     */
    public function isMutualMatch(int $companyId, int $sponsorId, int $eventId): bool
    {
        $sql = "SELECT COUNT(*) FROM company_selections cs
                INNER JOIN sponsor_selections ss
                ON cs.sponsor_id = ss.sponsor_id AND cs.company_id = ss.company_id AND cs.event_id = ss.event_id
                WHERE cs.company_id = ? AND cs.sponsor_id = ? AND cs.event_id = ?";
        return (int) $this->db->fetchColumn($sql, [$companyId, $sponsorId, $eventId]) > 0;
    }

    /**
     * Get scheduled meetings for company
     */
    public function getScheduledMeetings(int $companyId, int $eventId): array
    {
        return $this->getMeetings($companyId, $eventId);
    }

    /**
     * Get message from a specific sponsor
     */
    public function getMessageFromSponsor(int $companyId, int $sponsorId, int $eventId): ?array
    {
        $sql = "SELECT * FROM sponsor_messages WHERE company_id = ? AND sponsor_id = ? AND event_id = ?";
        return $this->db->fetch($sql, [$companyId, $sponsorId, $eventId]) ?: null;
    }

    /**
     * Get meetings for company in an event
     */
    public function getMeetings(int $companyId, int $eventId): array
    {
        $sql = "SELECT ma.*, ms.slot_time, ms.room_number, ms.room_name,
                       mb.name as block_name, mb.event_date, mb.slot_duration,
                       s.name as sponsor_name, s.logo_url as sponsor_logo
                FROM meeting_assignments ma
                INNER JOIN meeting_slots ms ON ma.slot_id = ms.id
                INNER JOIN meeting_blocks mb ON ms.block_id = mb.id
                INNER JOIN sponsors s ON ma.sponsor_id = s.id
                WHERE ma.company_id = ? AND ma.event_id = ?
                ORDER BY mb.event_date ASC, ms.slot_time ASC";

        return $this->db->fetchAll($sql, [$companyId, $eventId]);
    }

    /**
     * Count simultaneous meetings at a specific time
     */
    public function countMeetingsAtTime(int $companyId, int $blockId, string $time): int
    {
        $sql = "SELECT COUNT(*)
                FROM meeting_assignments ma
                INNER JOIN meeting_slots ms ON ma.slot_id = ms.id
                WHERE ma.company_id = ? AND ms.block_id = ? AND ms.slot_time = ?
                AND ma.status NOT IN ('cancelled')";

        return (int) $this->db->fetchColumn($sql, [$companyId, $blockId, $time]);
    }

    /**
     * Get max simultaneous meetings (based on number of contact emails)
     */
    public function getMaxSimultaneousMeetings(array $company): int
    {
        if (empty($company['contact_email'])) {
            return 1;
        }

        $emails = array_filter(array_map('trim', explode(',', $company['contact_email'])));
        return max(1, count($emails));
    }

    /**
     * Get SaaS tools used by company
     */
    public function getSaasUsage(int $companyId): array
    {
        $sql = "SELECT s.*
                FROM sponsors s
                INNER JOIN company_saas_usage csu ON s.id = csu.sponsor_id
                WHERE csu.company_id = ?
                ORDER BY s.name ASC";

        return $this->db->fetchAll($sql, [$companyId]);
    }

    /**
     * Add SaaS usage
     */
    public function addSaasUsage(int $companyId, int $sponsorId): bool
    {
        $sql = "INSERT IGNORE INTO company_saas_usage (company_id, sponsor_id) VALUES (?, ?)";
        $this->db->query($sql, [$companyId, $sponsorId]);
        return true;
    }

    /**
     * Remove SaaS usage
     */
    public function removeSaasUsage(int $companyId, int $sponsorId): bool
    {
        $sql = "DELETE FROM company_saas_usage WHERE company_id = ? AND sponsor_id = ?";
        $this->db->query($sql, [$companyId, $sponsorId]);
        return true;
    }

    /**
     * Get received messages
     */
    public function getReceivedMessages(int $companyId, int $eventId): array
    {
        try {
            $sql = "SELECT sm.*, s.name as sponsor_name, s.logo_url as sponsor_logo
                    FROM sponsor_messages sm
                    INNER JOIN sponsors s ON sm.sponsor_id = s.id
                    WHERE sm.company_id = ? AND sm.event_id = ?
                    ORDER BY sm.created_at DESC";

            return $this->db->fetchAll($sql, [$companyId, $eventId]);
        } catch (\PDOException $e) {
            // Table might not exist yet
            return [];
        }
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
    public function getEmailsArray(array $company): array
    {
        if (empty($company['contact_email'])) {
            return [];
        }

        return array_map('trim', explode(',', $company['contact_email']));
    }

    /**
     * Get company size options
     */
    public static function getSizeOptions(): array
    {
        return [
            '1-10' => '1-10 empleados',
            '11-50' => '11-50 empleados',
            '51-200' => '51-200 empleados',
            '201-500' => '201-500 empleados',
            '500+' => '500+ empleados',
        ];
    }
}
