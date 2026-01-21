<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * MeetingSlot Model
 * TLOS - The Last of SaaS
 */
class MeetingSlot extends Model
{
    protected string $table = 'meeting_slots';

    protected array $fillable = [
        'block_id',
        'slot_time',
        'room_number',
        'room_name',
        'is_available',
    ];

    /**
     * Get slot with block info
     */
    public function getWithBlock(int $slotId): ?array
    {
        $sql = "SELECT ms.*, mb.name as block_name, mb.event_id, mb.event_date, mb.slot_duration
                FROM meeting_slots ms
                INNER JOIN meeting_blocks mb ON ms.block_id = mb.id
                WHERE ms.id = ?";

        return $this->db->fetch($sql, [$slotId]);
    }

    /**
     * Check if slot is available
     */
    public function isAvailable(int $slotId): bool
    {
        $sql = "SELECT ms.is_available,
                       CASE WHEN ma.id IS NOT NULL THEN 1 ELSE 0 END as is_assigned
                FROM meeting_slots ms
                LEFT JOIN meeting_assignments ma ON ms.id = ma.slot_id AND ma.status != 'cancelled'
                WHERE ms.id = ?";

        $result = $this->db->fetch($sql, [$slotId]);

        return $result && $result['is_available'] && !$result['is_assigned'];
    }

    /**
     * Get available slots for a match (considering both parties' availability)
     */
    public function getAvailableForMatch(int $eventId, int $sponsorId, int $companyId): array
    {
        $sponsorModel = new Sponsor();
        $companyModel = new Company();

        $sponsor = $sponsorModel->find($sponsorId);
        $company = $companyModel->find($companyId);

        if (!$sponsor || !$company) {
            return [];
        }

        $sponsorMaxSimultaneous = (int) ($sponsor['max_simultaneous_meetings'] ?? 1);
        $companyMaxSimultaneous = $companyModel->getMaxSimultaneousMeetings($company);

        $sql = "SELECT ms.*, mb.name as block_name, mb.event_date, mb.slot_duration, mb.location
                FROM meeting_slots ms
                INNER JOIN meeting_blocks mb ON ms.block_id = mb.id
                LEFT JOIN meeting_assignments ma ON ms.id = ma.slot_id AND ma.status != 'cancelled'
                WHERE mb.event_id = ? AND mb.active = 1 AND ms.is_available = 1 AND ma.id IS NULL
                ORDER BY mb.event_date ASC, ms.slot_time ASC, ms.room_number ASC";

        $allSlots = $this->db->fetchAll($sql, [$eventId]);

        // Filter by availability of both parties
        $availableSlots = [];
        foreach ($allSlots as $slot) {
            $sponsorMeetings = $sponsorModel->countMeetingsAtTime($sponsorId, (int) $slot['block_id'], $slot['slot_time']);
            $companyMeetings = $companyModel->countMeetingsAtTime($companyId, (int) $slot['block_id'], $slot['slot_time']);

            if ($sponsorMeetings < $sponsorMaxSimultaneous && $companyMeetings < $companyMaxSimultaneous) {
                $availableSlots[] = $slot;
            }
        }

        return $availableSlots;
    }

    /**
     * Get slots grouped by time
     */
    public function getGroupedByTime(int $blockId): array
    {
        $sql = "SELECT ms.*,
                       CASE WHEN ma.id IS NOT NULL THEN 1 ELSE 0 END as is_assigned,
                       s.name as sponsor_name, c.name as company_name
                FROM meeting_slots ms
                LEFT JOIN meeting_assignments ma ON ms.id = ma.slot_id AND ma.status != 'cancelled'
                LEFT JOIN sponsors s ON ma.sponsor_id = s.id
                LEFT JOIN companies c ON ma.company_id = c.id
                WHERE ms.block_id = ?
                ORDER BY ms.slot_time ASC, ms.room_number ASC";

        $slots = $this->db->fetchAll($sql, [$blockId]);

        // Group by time
        $grouped = [];
        foreach ($slots as $slot) {
            $time = $slot['slot_time'];
            if (!isset($grouped[$time])) {
                $grouped[$time] = [];
            }
            $grouped[$time][] = $slot;
        }

        return $grouped;
    }

    /**
     * Mark slot as unavailable
     */
    public function markUnavailable(int $slotId): bool
    {
        return $this->update($slotId, ['is_available' => 0]);
    }

    /**
     * Mark slot as available
     */
    public function markAvailable(int $slotId): bool
    {
        return $this->update($slotId, ['is_available' => 1]);
    }
}
