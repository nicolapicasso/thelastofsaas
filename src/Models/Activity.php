<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * Activity Model
 * TLOS - The Last of SaaS
 *
 * Activities for events: talks, breaks, networking, etc.
 */
class Activity extends Model
{
    protected string $table = 'activities';

    protected array $fillable = [
        'event_id',
        'room_id',
        'speaker_id',
        'category_id',
        'title',
        'slug',
        'description',
        'activity_type',
        'activity_date',
        'start_time',
        'end_time',
        'image_url',
        'video_url',
        'max_attendees',
        'requires_registration',
        'is_featured',
        'sort_order',
        'active'
    ];

    /**
     * Activity types available
     */
    public static function getActivityTypes(): array
    {
        return [
            'charla' => 'Charla / Ponencia',
            'mesa_redonda' => 'Mesa Redonda',
            'taller' => 'Taller / Workshop',
            'networking' => 'Networking',
            'comida' => 'Comida / Almuerzo',
            'cafe' => 'Cafe / Descanso',
            'bienvenida' => 'Bienvenida',
            'cierre' => 'Cierre / Clausura',
            'registro' => 'Registro',
            'otro' => 'Otro',
        ];
    }

    /**
     * Get activities for admin with relationships
     */
    public function getAllForAdmin(?int $eventId = null, ?string $date = null): array
    {
        $sql = "SELECT a.*,
                       e.name as event_name,
                       r.name as room_name, r.color as room_color,
                       t.name as speaker_name, t.position as speaker_position, t.photo as speaker_photo,
                       c.name as category_name, c.color as category_color
                FROM {$this->table} a
                LEFT JOIN events e ON a.event_id = e.id
                LEFT JOIN rooms r ON a.room_id = r.id
                LEFT JOIN team_members t ON a.speaker_id = t.id
                LEFT JOIN categories c ON a.category_id = c.id
                WHERE 1=1";

        $params = [];

        if ($eventId) {
            $sql .= " AND a.event_id = ?";
            $params[] = $eventId;
        }

        if ($date) {
            $sql .= " AND a.activity_date = ?";
            $params[] = $date;
        }

        $sql .= " ORDER BY a.activity_date ASC, a.start_time ASC, a.sort_order ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get activities for an event
     */
    public function getByEvent(int $eventId, bool $activeOnly = true): array
    {
        $sql = "SELECT a.*,
                       r.name as room_name, r.color as room_color, r.capacity as room_capacity,
                       t.name as speaker_name, t.position as speaker_position, t.photo as speaker_photo, t.bio as speaker_bio,
                       c.name as category_name, c.color as category_color
                FROM {$this->table} a
                LEFT JOIN rooms r ON a.room_id = r.id
                LEFT JOIN team_members t ON a.speaker_id = t.id
                LEFT JOIN categories c ON a.category_id = c.id
                WHERE a.event_id = ?";

        if ($activeOnly) {
            $sql .= " AND a.active = 1";
        }

        $sql .= " ORDER BY a.activity_date ASC, a.start_time ASC, a.sort_order ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Get activities grouped by date (for agenda display)
     */
    public function getByEventGroupedByDate(int $eventId): array
    {
        $activities = $this->getByEvent($eventId);
        $grouped = [];

        foreach ($activities as $activity) {
            $date = $activity['activity_date'];
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $activity;
        }

        return $grouped;
    }

    /**
     * Get activities grouped by room (for parallel tracks)
     */
    public function getByEventGroupedByRoom(int $eventId, string $date): array
    {
        $sql = "SELECT a.*,
                       r.name as room_name, r.color as room_color,
                       t.name as speaker_name, t.position as speaker_position, t.photo as speaker_photo
                FROM {$this->table} a
                LEFT JOIN rooms r ON a.room_id = r.id
                LEFT JOIN team_members t ON a.speaker_id = t.id
                WHERE a.event_id = ? AND a.activity_date = ? AND a.active = 1
                ORDER BY a.room_id ASC, a.start_time ASC";

        $activities = $this->db->fetchAll($sql, [$eventId, $date]);
        $grouped = [];

        foreach ($activities as $activity) {
            $roomId = $activity['room_id'] ?? 0;
            $roomName = $activity['room_name'] ?? 'Sin sala';
            if (!isset($grouped[$roomId])) {
                $grouped[$roomId] = [
                    'room_name' => $roomName,
                    'room_color' => $activity['room_color'] ?? '#6B7280',
                    'activities' => []
                ];
            }
            $grouped[$roomId]['activities'][] = $activity;
        }

        return $grouped;
    }

    /**
     * Get featured activities for an event
     */
    public function getFeatured(int $eventId, int $limit = 5): array
    {
        $sql = "SELECT a.*,
                       r.name as room_name, r.color as room_color,
                       t.name as speaker_name, t.photo as speaker_photo
                FROM {$this->table} a
                LEFT JOIN rooms r ON a.room_id = r.id
                LEFT JOIN team_members t ON a.speaker_id = t.id
                WHERE a.event_id = ? AND a.active = 1 AND a.is_featured = 1
                ORDER BY a.activity_date ASC, a.start_time ASC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$eventId, $limit]);
    }

    /**
     * Find activity by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT a.*,
                       e.name as event_name, e.slug as event_slug,
                       r.name as room_name, r.color as room_color, r.location as room_location,
                       t.name as speaker_name, t.position as speaker_position, t.photo as speaker_photo, t.bio as speaker_bio,
                       c.name as category_name, c.color as category_color
                FROM {$this->table} a
                LEFT JOIN events e ON a.event_id = e.id
                LEFT JOIN rooms r ON a.room_id = r.id
                LEFT JOIN team_members t ON a.speaker_id = t.id
                LEFT JOIN categories c ON a.category_id = c.id
                WHERE a.slug = ?
                LIMIT 1";

        return $this->db->fetch($sql, [$slug]) ?: null;
    }

    /**
     * Get activities by speaker
     */
    public function getBySpeaker(int $speakerId, ?int $eventId = null): array
    {
        $sql = "SELECT a.*, e.name as event_name, r.name as room_name
                FROM {$this->table} a
                LEFT JOIN events e ON a.event_id = e.id
                LEFT JOIN rooms r ON a.room_id = r.id
                WHERE a.speaker_id = ? AND a.active = 1";

        $params = [$speakerId];

        if ($eventId) {
            $sql .= " AND a.event_id = ?";
            $params[] = $eventId;
        }

        $sql .= " ORDER BY a.activity_date ASC, a.start_time ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get activities by room
     */
    public function getByRoom(int $roomId, ?string $date = null): array
    {
        $sql = "SELECT a.*, e.name as event_name, t.name as speaker_name
                FROM {$this->table} a
                LEFT JOIN events e ON a.event_id = e.id
                LEFT JOIN team_members t ON a.speaker_id = t.id
                WHERE a.room_id = ? AND a.active = 1";

        $params = [$roomId];

        if ($date) {
            $sql .= " AND a.activity_date = ?";
            $params[] = $date;
        }

        $sql .= " ORDER BY a.activity_date ASC, a.start_time ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get unique dates for an event
     */
    public function getEventDates(int $eventId): array
    {
        $sql = "SELECT DISTINCT activity_date FROM {$this->table}
                WHERE event_id = ? AND active = 1
                ORDER BY activity_date ASC";

        $results = $this->db->fetchAll($sql, [$eventId]);
        return array_column($results, 'activity_date');
    }

    /**
     * Check for time conflicts in the same room
     */
    public function hasTimeConflict(int $roomId, int $eventId, string $date, string $startTime, string $endTime, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}
                WHERE room_id = ? AND event_id = ? AND activity_date = ?
                AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))";

        $params = [$roomId, $eventId, $date, $endTime, $startTime, $endTime, $startTime, $startTime, $endTime];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        return (int) $this->db->fetchColumn($sql, $params) > 0;
    }

    /**
     * Create activity with auto-generated slug
     */
    public function createWithSlug(array $data): int
    {
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Slug::unique($data['title'], $this->table);
        }
        return $this->create($data);
    }

    /**
     * Update activity with slug handling
     */
    public function updateWithSlug(int $id, array $data): bool
    {
        if (isset($data['title']) && empty($data['slug'])) {
            $data['slug'] = Slug::unique($data['title'], $this->table, 'slug', $id);
        }
        return $this->update($id, $data);
    }

    /**
     * Reorder activities
     */
    public function reorder(array $ids): void
    {
        foreach ($ids as $order => $id) {
            $this->update((int) $id, ['sort_order' => $order]);
        }
    }

    /**
     * Count activities for an event
     */
    public function countByEvent(int $eventId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE event_id = ? AND active = 1";
        return (int) $this->db->fetchColumn($sql, [$eventId]);
    }

    /**
     * Get activity duration in minutes
     */
    public function getDuration(array $activity): int
    {
        $start = strtotime($activity['start_time']);
        $end = strtotime($activity['end_time']);
        return ($end - $start) / 60;
    }

    /**
     * Format time range for display
     */
    public function formatTimeRange(array $activity): string
    {
        $start = date('H:i', strtotime($activity['start_time']));
        $end = date('H:i', strtotime($activity['end_time']));
        return "{$start} - {$end}";
    }
}
