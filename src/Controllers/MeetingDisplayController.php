<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\MeetingBlock;
use App\Models\MeetingAssignment;

/**
 * Meeting Display Controller
 * Public pages for displaying meeting schedules on screens
 * TLOS - The Last of SaaS
 */
class MeetingDisplayController extends Controller
{
    private Event $eventModel;
    private MeetingBlock $blockModel;
    private MeetingAssignment $assignmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
        $this->blockModel = new MeetingBlock();
        $this->assignmentModel = new MeetingAssignment();
    }

    /**
     * Visual room display with timer (for large screens)
     * Shows tables with sponsor/company logos, and manages rounds with countdown
     */
    public function roomDisplay(string $slug, int $blockId): void
    {
        $event = $this->eventModel->findBySlug($slug);
        if (!$event) {
            $this->notFound('Evento no encontrado');
            return;
        }

        $block = $this->blockModel->find($blockId);
        if (!$block || $block['event_id'] != $event['id']) {
            $this->notFound('Bloque de reuniones no encontrado');
            return;
        }

        // Get all scheduled meetings for this block with full details
        $meetings = $this->getMeetingsForBlock($blockId);

        // Group by time slot (round)
        $rounds = $this->groupMeetingsByTime($meetings, $block);

        $this->render('meetings/room-display', [
            'event' => $event,
            'block' => $block,
            'rounds' => $rounds,
            'totalRooms' => (int) $block['total_rooms'],
            'slotDuration' => (int) $block['slot_duration'],
            'pageTitle' => 'Reuniones - ' . $block['name'],
        ]);
    }

    /**
     * Table format schedule (like the image - shows all meetings in a table grid)
     */
    public function schedule(string $slug, int $blockId): void
    {
        $event = $this->eventModel->findBySlug($slug);
        if (!$event) {
            $this->notFound('Evento no encontrado');
            return;
        }

        $block = $this->blockModel->find($blockId);
        if (!$block || $block['event_id'] != $event['id']) {
            $this->notFound('Bloque de reuniones no encontrado');
            return;
        }

        // Get all scheduled meetings for this block
        $meetings = $this->getMeetingsForBlock($blockId);

        // Group by time for table display
        $schedule = $this->groupMeetingsByTime($meetings, $block);

        // Get unique times for table headers
        $times = array_keys($schedule);

        $this->render('meetings/schedule', [
            'event' => $event,
            'block' => $block,
            'schedule' => $schedule,
            'times' => $times,
            'totalRooms' => (int) $block['total_rooms'],
            'pageTitle' => 'Horario - ' . $block['name'],
        ]);
    }

    /**
     * Block selector page - list all blocks for an event
     */
    public function index(string $slug): void
    {
        $event = $this->eventModel->findBySlug($slug);
        if (!$event) {
            $this->notFound('Evento no encontrado');
            return;
        }

        $blocks = $this->blockModel->getActiveByEvent($event['id']);

        // Add stats for each block
        foreach ($blocks as &$block) {
            $block['stats'] = $this->blockModel->getStats($block['id']);
        }

        $this->render('meetings/index', [
            'event' => $event,
            'blocks' => $blocks,
            'pageTitle' => 'Reuniones - ' . $event['name'],
        ]);
    }

    /**
     * Get all meetings for a block with sponsor and company details
     */
    private function getMeetingsForBlock(int $blockId): array
    {
        $sql = "SELECT
                    ms.id as slot_id,
                    ms.slot_time,
                    ms.room_number,
                    ms.room_name,
                    ma.id as assignment_id,
                    ma.status,
                    s.id as sponsor_id,
                    s.name as sponsor_name,
                    s.logo_url as sponsor_logo,
                    c.id as company_id,
                    c.name as company_name,
                    c.logo_url as company_logo
                FROM meeting_slots ms
                LEFT JOIN meeting_assignments ma ON ms.id = ma.slot_id AND ma.status != 'cancelled'
                LEFT JOIN sponsors s ON ma.sponsor_id = s.id
                LEFT JOIN companies c ON ma.company_id = c.id
                WHERE ms.block_id = ?
                ORDER BY ms.slot_time ASC, ms.room_number ASC";

        return $this->db->fetchAll($sql, [$blockId]);
    }

    /**
     * Group meetings by time slot
     */
    private function groupMeetingsByTime(array $meetings, array $block): array
    {
        $grouped = [];
        $totalRooms = (int) $block['total_rooms'];

        foreach ($meetings as $meeting) {
            $time = substr($meeting['slot_time'], 0, 5); // HH:MM format

            if (!isset($grouped[$time])) {
                // Initialize all rooms for this time slot
                $grouped[$time] = [];
                for ($i = 1; $i <= $totalRooms; $i++) {
                    $grouped[$time][$i] = null;
                }
            }

            $roomNum = (int) $meeting['room_number'];
            if ($meeting['assignment_id']) {
                $grouped[$time][$roomNum] = [
                    'sponsor_name' => $meeting['sponsor_name'],
                    'sponsor_logo' => $meeting['sponsor_logo'],
                    'company_name' => $meeting['company_name'],
                    'company_logo' => $meeting['company_logo'],
                    'status' => $meeting['status'],
                ];
            }
        }

        return $grouped;
    }
}
