<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * MeetingBlock Model
 * TLOS - The Last of SaaS
 */
class MeetingBlock extends Model
{
    protected string $table = 'meeting_blocks';

    protected array $fillable = [
        'event_id',
        'name',
        'event_date',
        'start_time',
        'end_time',
        'slot_duration',
        'total_rooms',
        'location',
        'active',
    ];

    /**
     * Get blocks for an event
     */
    public function getByEvent(int $eventId): array
    {
        return $this->where(
            ['event_id' => $eventId],
            ['event_date' => 'ASC', 'start_time' => 'ASC']
        );
    }

    /**
     * Get active blocks for an event
     */
    public function getActiveByEvent(int $eventId): array
    {
        return $this->where(
            ['event_id' => $eventId, 'active' => 1],
            ['event_date' => 'ASC', 'start_time' => 'ASC']
        );
    }

    /**
     * Get blocks for a specific date
     */
    public function getByDate(int $eventId, string $date): array
    {
        return $this->where(
            ['event_id' => $eventId, 'event_date' => $date, 'active' => 1],
            ['start_time' => 'ASC']
        );
    }

    /**
     * Generate slots for a block
     */
    public function generateSlots(int $blockId): int
    {
        $block = $this->find($blockId);
        if (!$block) {
            return 0;
        }

        $startTime = strtotime($block['start_time']);
        $endTime = strtotime($block['end_time']);
        $duration = (int) $block['slot_duration'] * 60; // Convert to seconds
        $rooms = (int) $block['total_rooms'];

        $slotsCreated = 0;
        $currentTime = $startTime;

        while ($currentTime + $duration <= $endTime) {
            $slotTime = date('H:i:s', $currentTime);

            for ($room = 1; $room <= $rooms; $room++) {
                $sql = "INSERT IGNORE INTO meeting_slots (block_id, slot_time, room_number, room_name)
                        VALUES (?, ?, ?, ?)";
                $this->db->query($sql, [
                    $blockId,
                    $slotTime,
                    $room,
                    "Mesa {$room}",
                ]);
                $slotsCreated++;
            }

            $currentTime += $duration;
        }

        return $slotsCreated;
    }

    /**
     * Get slots for a block
     */
    public function getSlots(int $blockId): array
    {
        $sql = "SELECT ms.*,
                       CASE WHEN ma.id IS NOT NULL THEN 1 ELSE 0 END as is_assigned,
                       ma.sponsor_id, ma.company_id, ma.status as assignment_status,
                       s.name as sponsor_name, c.name as company_name
                FROM meeting_slots ms
                LEFT JOIN meeting_assignments ma ON ms.id = ma.slot_id AND ma.status != 'cancelled'
                LEFT JOIN sponsors s ON ma.sponsor_id = s.id
                LEFT JOIN companies c ON ma.company_id = c.id
                WHERE ms.block_id = ?
                ORDER BY ms.slot_time ASC, ms.room_number ASC";

        return $this->db->fetchAll($sql, [$blockId]);
    }

    /**
     * Get available slots for a block
     */
    public function getAvailableSlots(int $blockId): array
    {
        $sql = "SELECT ms.*
                FROM meeting_slots ms
                LEFT JOIN meeting_assignments ma ON ms.id = ma.slot_id AND ma.status != 'cancelled'
                WHERE ms.block_id = ? AND ms.is_available = 1 AND ma.id IS NULL
                ORDER BY ms.slot_time ASC, ms.room_number ASC";

        return $this->db->fetchAll($sql, [$blockId]);
    }

    /**
     * Get stats for a block
     */
    public function getStats(int $blockId): array
    {
        $sql = "SELECT
                    COUNT(*) as total_slots,
                    SUM(CASE WHEN ma.id IS NOT NULL AND ma.status != 'cancelled' THEN 1 ELSE 0 END) as assigned_slots,
                    SUM(CASE WHEN ms.is_available = 1 AND ma.id IS NULL THEN 1 ELSE 0 END) as available_slots
                FROM meeting_slots ms
                LEFT JOIN meeting_assignments ma ON ms.id = ma.slot_id
                WHERE ms.block_id = ?";

        return $this->db->fetch($sql, [$blockId]) ?: [];
    }

    /**
     * Delete all slots for a block
     */
    public function deleteSlots(int $blockId): bool
    {
        // First check if there are assignments
        $sql = "SELECT COUNT(*) FROM meeting_assignments ma
                INNER JOIN meeting_slots ms ON ma.slot_id = ms.id
                WHERE ms.block_id = ? AND ma.status != 'cancelled'";
        $hasAssignments = (int) $this->db->fetchColumn($sql, [$blockId]) > 0;

        if ($hasAssignments) {
            return false;
        }

        $sql = "DELETE FROM meeting_slots WHERE block_id = ?";
        $this->db->query($sql, [$blockId]);
        return true;
    }

    /**
     * Get time slots list (unique times)
     */
    public function getTimeSlotsForBlock(int $blockId): array
    {
        $sql = "SELECT DISTINCT slot_time FROM meeting_slots WHERE block_id = ? ORDER BY slot_time ASC";
        return $this->db->fetchAll($sql, [$blockId]);
    }

    /**
     * Calculate number of slots that will be generated
     */
    public function calculateSlotsCount(array $block): int
    {
        $startTime = strtotime($block['start_time']);
        $endTime = strtotime($block['end_time']);
        $duration = (int) $block['slot_duration'] * 60;
        $rooms = (int) $block['total_rooms'];

        $timeSlots = 0;
        $currentTime = $startTime;

        while ($currentTime + $duration <= $endTime) {
            $timeSlots++;
            $currentTime += $duration;
        }

        return $timeSlots * $rooms;
    }
}
