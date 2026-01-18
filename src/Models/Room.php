<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Room Model
 * TLOS - The Last of SaaS
 *
 * Rooms/Salas for activities and meetings
 */
class Room extends Model
{
    protected string $table = 'rooms';

    protected array $fillable = [
        'name',
        'slug',
        'description',
        'capacity',
        'location',
        'floor',
        'equipment',
        'image_url',
        'color',
        'active',
        'sort_order'
    ];

    /**
     * Get active rooms
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE active = 1 ORDER BY sort_order ASC, name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get rooms for select dropdown
     */
    public function getForSelect(): array
    {
        $rooms = $this->getActive();
        $result = [];
        foreach ($rooms as $room) {
            $result[$room['id']] = $room['name'] . ($room['capacity'] ? " (Cap: {$room['capacity']})" : '');
        }
        return $result;
    }

    /**
     * Find room by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ? LIMIT 1";
        return $this->db->fetch($sql, [$slug]) ?: null;
    }

    /**
     * Check if room is available at given time
     */
    public function isAvailable(int $roomId, int $eventId, string $date, string $startTime, string $endTime, ?int $excludeActivityId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM activities
                WHERE room_id = ? AND event_id = ? AND activity_date = ?
                AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))";

        $params = [$roomId, $eventId, $date, $endTime, $startTime, $endTime, $startTime, $startTime, $endTime];

        if ($excludeActivityId) {
            $sql .= " AND id != ?";
            $params[] = $excludeActivityId;
        }

        return (int) $this->db->fetchColumn($sql, $params) === 0;
    }

    /**
     * Get activities for room on a specific date
     */
    public function getActivities(int $roomId, ?string $date = null): array
    {
        $sql = "SELECT a.*, e.name as event_name, t.name as speaker_name
                FROM activities a
                LEFT JOIN events e ON a.event_id = e.id
                LEFT JOIN team_members t ON a.speaker_id = t.id
                WHERE a.room_id = ?";

        $params = [$roomId];

        if ($date) {
            $sql .= " AND a.activity_date = ?";
            $params[] = $date;
        }

        $sql .= " ORDER BY a.activity_date ASC, a.start_time ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get color options for room calendar display
     */
    public static function getColorOptions(): array
    {
        return [
            '#3B82F6' => 'Azul',
            '#10B981' => 'Verde',
            '#F59E0B' => 'Naranja',
            '#EF4444' => 'Rojo',
            '#8B5CF6' => 'Morado',
            '#EC4899' => 'Rosa',
            '#06B6D4' => 'Cian',
            '#6B7280' => 'Gris',
        ];
    }
}
