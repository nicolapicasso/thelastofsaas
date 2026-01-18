<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * MeetingAssignment Model
 * TLOS - The Last of SaaS
 */
class MeetingAssignment extends Model
{
    protected string $table = 'meeting_assignments';

    protected array $fillable = [
        'slot_id',
        'event_id',
        'sponsor_id',
        'company_id',
        'status',
        'notes',
        'feedback_sponsor',
        'feedback_company',
        'rating_sponsor',
        'rating_company',
        'assigned_by',
        'completed_at',
    ];

    /**
     * Get assignment with full details
     */
    public function getWithDetails(int $assignmentId): ?array
    {
        $sql = "SELECT ma.*,
                       ms.slot_time, ms.room_number, ms.room_name,
                       mb.name as block_name, mb.event_date, mb.slot_duration, mb.location,
                       s.name as sponsor_name, s.logo_url as sponsor_logo, s.contact_email as sponsor_email,
                       c.name as company_name, c.logo_url as company_logo, c.contact_email as company_email
                FROM meeting_assignments ma
                INNER JOIN meeting_slots ms ON ma.slot_id = ms.id
                INNER JOIN meeting_blocks mb ON ms.block_id = mb.id
                INNER JOIN sponsors s ON ma.sponsor_id = s.id
                INNER JOIN companies c ON ma.company_id = c.id
                WHERE ma.id = ?";

        return $this->db->fetch($sql, [$assignmentId]);
    }

    /**
     * Get assignments for an event
     */
    public function getByEvent(int $eventId): array
    {
        $sql = "SELECT ma.*,
                       ms.slot_time, ms.room_number, ms.room_name,
                       mb.name as block_name, mb.event_date, mb.slot_duration,
                       s.name as sponsor_name, c.name as company_name
                FROM meeting_assignments ma
                INNER JOIN meeting_slots ms ON ma.slot_id = ms.id
                INNER JOIN meeting_blocks mb ON ms.block_id = mb.id
                INNER JOIN sponsors s ON ma.sponsor_id = s.id
                INNER JOIN companies c ON ma.company_id = c.id
                WHERE ma.event_id = ?
                ORDER BY mb.event_date ASC, ms.slot_time ASC, ms.room_number ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Get assignments for a sponsor in an event
     */
    public function getBySponsor(int $sponsorId, int $eventId): array
    {
        $sql = "SELECT ma.*,
                       ms.slot_time, ms.room_number, ms.room_name,
                       mb.name as block_name, mb.event_date, mb.slot_duration,
                       c.name as company_name, c.logo_url as company_logo
                FROM meeting_assignments ma
                INNER JOIN meeting_slots ms ON ma.slot_id = ms.id
                INNER JOIN meeting_blocks mb ON ms.block_id = mb.id
                INNER JOIN companies c ON ma.company_id = c.id
                WHERE ma.sponsor_id = ? AND ma.event_id = ? AND ma.status != 'cancelled'
                ORDER BY mb.event_date ASC, ms.slot_time ASC";

        return $this->db->fetchAll($sql, [$sponsorId, $eventId]);
    }

    /**
     * Get assignments for a company in an event
     */
    public function getByCompany(int $companyId, int $eventId): array
    {
        $sql = "SELECT ma.*,
                       ms.slot_time, ms.room_number, ms.room_name,
                       mb.name as block_name, mb.event_date, mb.slot_duration,
                       s.name as sponsor_name, s.logo_url as sponsor_logo
                FROM meeting_assignments ma
                INNER JOIN meeting_slots ms ON ma.slot_id = ms.id
                INNER JOIN meeting_blocks mb ON ms.block_id = mb.id
                INNER JOIN sponsors s ON ma.sponsor_id = s.id
                WHERE ma.company_id = ? AND ma.event_id = ? AND ma.status != 'cancelled'
                ORDER BY mb.event_date ASC, ms.slot_time ASC";

        return $this->db->fetchAll($sql, [$companyId, $eventId]);
    }

    /**
     * Assign a meeting
     */
    public function assign(int $slotId, int $eventId, int $sponsorId, int $companyId, string $assignedBy = 'admin', ?string $notes = null): ?int
    {
        $slotModel = new MeetingSlot();

        // Check slot is available
        if (!$slotModel->isAvailable($slotId)) {
            return null;
        }

        // Check if this pair already has a meeting
        if ($this->hasExistingMeeting($eventId, $sponsorId, $companyId)) {
            return null;
        }

        $id = $this->create([
            'slot_id' => $slotId,
            'event_id' => $eventId,
            'sponsor_id' => $sponsorId,
            'company_id' => $companyId,
            'status' => 'confirmed',
            'assigned_by' => $assignedBy,
            'notes' => $notes,
        ]);

        return $id > 0 ? $id : null;
    }

    /**
     * Cancel assignment
     */
    public function cancel(int $assignmentId): bool
    {
        return $this->update($assignmentId, ['status' => 'cancelled']);
    }

    /**
     * Mark as completed
     */
    public function complete(int $assignmentId): bool
    {
        return $this->update($assignmentId, [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Mark as no-show
     */
    public function markNoShow(int $assignmentId): bool
    {
        return $this->update($assignmentId, ['status' => 'no_show']);
    }

    /**
     * Check if sponsor-company pair already has a meeting in event
     */
    public function hasExistingMeeting(int $eventId, int $sponsorId, int $companyId): bool
    {
        $sql = "SELECT COUNT(*) FROM meeting_assignments
                WHERE event_id = ? AND sponsor_id = ? AND company_id = ? AND status NOT IN ('cancelled')";

        return (int) $this->db->fetchColumn($sql, [$eventId, $sponsorId, $companyId]) > 0;
    }

    /**
     * Get unassigned matches (mutual matches without meetings)
     */
    public function getUnassignedMatches(int $eventId): array
    {
        $sql = "SELECT
                    s.id as sponsor_id, s.name as sponsor_name, s.logo_url as sponsor_logo,
                    c.id as company_id, c.name as company_name, c.logo_url as company_logo,
                    ss.selected_at as sponsor_selected_at,
                    cs.selected_at as company_selected_at
                FROM sponsor_selections ss
                INNER JOIN company_selections cs
                    ON ss.sponsor_id = cs.sponsor_id
                    AND ss.company_id = cs.company_id
                    AND ss.event_id = cs.event_id
                INNER JOIN sponsors s ON ss.sponsor_id = s.id
                INNER JOIN companies c ON ss.company_id = c.id
                LEFT JOIN meeting_assignments ma
                    ON ma.sponsor_id = ss.sponsor_id
                    AND ma.company_id = ss.company_id
                    AND ma.event_id = ss.event_id
                    AND ma.status NOT IN ('cancelled')
                WHERE ss.event_id = ? AND ma.id IS NULL
                ORDER BY s.name ASC, c.name ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Get all matches (mutual selections)
     */
    public function getAllMatches(int $eventId): array
    {
        $sql = "SELECT
                    s.id as sponsor_id, s.name as sponsor_name,
                    c.id as company_id, c.name as company_name,
                    CASE WHEN ma.id IS NOT NULL THEN 1 ELSE 0 END as has_meeting,
                    ma.id as meeting_id
                FROM sponsor_selections ss
                INNER JOIN company_selections cs
                    ON ss.sponsor_id = cs.sponsor_id
                    AND ss.company_id = cs.company_id
                    AND ss.event_id = cs.event_id
                INNER JOIN sponsors s ON ss.sponsor_id = s.id
                INNER JOIN companies c ON ss.company_id = c.id
                LEFT JOIN meeting_assignments ma
                    ON ma.sponsor_id = ss.sponsor_id
                    AND ma.company_id = ss.company_id
                    AND ma.event_id = ss.event_id
                    AND ma.status NOT IN ('cancelled')
                WHERE ss.event_id = ?
                ORDER BY s.name ASC, c.name ASC";

        return $this->db->fetchAll($sql, [$eventId]);
    }

    /**
     * Add feedback from sponsor
     */
    public function addSponsorFeedback(int $assignmentId, string $feedback, ?int $rating = null): bool
    {
        $data = ['feedback_sponsor' => $feedback];
        if ($rating !== null) {
            $data['rating_sponsor'] = $rating;
        }
        return $this->update($assignmentId, $data);
    }

    /**
     * Add feedback from company
     */
    public function addCompanyFeedback(int $assignmentId, string $feedback, ?int $rating = null): bool
    {
        $data = ['feedback_company' => $feedback];
        if ($rating !== null) {
            $data['rating_company'] = $rating;
        }
        return $this->update($assignmentId, $data);
    }

    /**
     * Get event stats
     */
    public function getEventStats(int $eventId): array
    {
        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) as no_show
                FROM meeting_assignments WHERE event_id = ?";

        return $this->db->fetch($sql, [$eventId]) ?: [];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'no_show' => 'No asistió',
        ];
    }
}
