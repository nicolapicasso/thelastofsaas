<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\MeetingBlock;
use App\Models\MeetingSlot;
use App\Models\MeetingAssignment;
use App\Models\Event;
use App\Models\Sponsor;
use App\Models\Company;
use App\Helpers\Sanitizer;

/**
 * Meetings Controller
 * TLOS - The Last of SaaS
 */
class MeetingsController extends Controller
{
    private MeetingBlock $blockModel;
    private MeetingSlot $slotModel;
    private MeetingAssignment $assignmentModel;
    private Event $eventModel;
    private Sponsor $sponsorModel;
    private Company $companyModel;

    public function __construct()
    {
        parent::__construct();
        $this->blockModel = new MeetingBlock();
        $this->slotModel = new MeetingSlot();
        $this->assignmentModel = new MeetingAssignment();
        $this->eventModel = new Event();
        $this->sponsorModel = new Sponsor();
        $this->companyModel = new Company();
    }

    /**
     * Meeting blocks management
     */
    public function blocks(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');
        $events = $this->eventModel->all(['start_date' => 'DESC']);

        if (!$eventId && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $blocks = $eventId ? $this->blockModel->getByEvent($eventId) : [];

        // Add stats to each block
        foreach ($blocks as &$block) {
            $block['stats'] = $this->blockModel->getStats($block['id']);
        }

        $this->renderAdmin('meetings/blocks', [
            'title' => 'Bloques Horarios',
            'blocks' => $blocks,
            'events' => $events,
            'currentEventId' => $eventId,
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Create meeting block
     */
    public function createBlock(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/meetings/blocks');
        }

        $eventId = (int) $this->getPost('event_id');
        $name = Sanitizer::string($this->getPost('name'));
        $eventDate = $this->getPost('event_date');
        $startTime = $this->getPost('start_time');
        $endTime = $this->getPost('end_time');
        $slotDuration = (int) $this->getPost('slot_duration', 15);
        $totalRooms = (int) $this->getPost('total_rooms', 10);
        $location = Sanitizer::string($this->getPost('location'));

        if (!$eventId || empty($name) || empty($eventDate) || empty($startTime) || empty($endTime)) {
            $this->flash('error', 'Faltan campos obligatorios.');
            $this->redirect('/admin/meetings/blocks?event_id=' . $eventId);
        }

        try {
            $blockId = $this->blockModel->create([
                'event_id' => $eventId,
                'name' => $name,
                'event_date' => $eventDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'slot_duration' => $slotDuration,
                'total_rooms' => $totalRooms,
                'location' => $location ?: null,
                'active' => 1,
            ]);

            // Auto-generate slots
            $slotsCreated = $this->blockModel->generateSlots($blockId);

            $this->flash('success', "Bloque creado con {$slotsCreated} slots.");
        } catch (\Exception $e) {
            $this->flash('error', 'Error: ' . $e->getMessage());
        }

        $this->redirect('/admin/meetings/blocks?event_id=' . $eventId);
    }

    /**
     * Update meeting block
     */
    public function updateBlock(string $id): void
    {
        $this->requireAuth();

        $block = $this->blockModel->find((int) $id);
        $eventId = $block['event_id'] ?? 0;

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/meetings/blocks?event_id=' . $eventId);
            return;
        }

        if (!$block) {
            $this->flash('error', 'Bloque no encontrado.');
            $this->redirect('/admin/meetings/blocks');
            return;
        }

        $data = [
            'name' => Sanitizer::string($this->getPost('name')),
            'event_date' => $this->getPost('event_date'),
            'start_time' => $this->getPost('start_time'),
            'end_time' => $this->getPost('end_time'),
            'slot_duration' => (int) $this->getPost('slot_duration', 15),
            'total_rooms' => (int) $this->getPost('total_rooms', 10),
            'location' => Sanitizer::string($this->getPost('location')) ?: null,
            'active' => Sanitizer::bool($this->getPost('active')) ? 1 : 0,
        ];

        try {
            $this->blockModel->update((int) $id, $data);
            $this->flash('success', 'Bloque actualizado.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error: ' . $e->getMessage());
        }

        $this->redirect('/admin/meetings/blocks?event_id=' . $eventId);
    }

    /**
     * Delete meeting block
     */
    public function deleteBlock(string $id): void
    {
        $this->requireAuth();

        $block = $this->blockModel->find((int) $id);
        $eventId = $block['event_id'] ?? 0;

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/meetings/blocks?event_id=' . $eventId);
            return;
        }

        $stats = $this->blockModel->getStats((int) $id);

        if (($stats['assigned_slots'] ?? 0) > 0) {
            $this->flash('error', 'No se puede eliminar: tiene reuniones asignadas.');
            $this->redirect('/admin/meetings/blocks?event_id=' . $eventId);
            return;
        }

        try {
            $this->blockModel->delete((int) $id);
            $this->flash('success', 'Bloque eliminado.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error: ' . $e->getMessage());
        }

        $this->redirect('/admin/meetings/blocks?event_id=' . $eventId);
    }

    /**
     * Regenerate slots for a block
     */
    public function regenerateSlots(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $stats = $this->blockModel->getStats((int) $id);

        if (($stats['assigned_slots'] ?? 0) > 0) {
            $this->jsonError('No se puede regenerar: tiene reuniones asignadas.');
            return;
        }

        try {
            $this->blockModel->deleteSlots((int) $id);
            $slotsCreated = $this->blockModel->generateSlots((int) $id);
            $this->jsonSuccess(['message' => "Regenerados {$slotsCreated} slots."]);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Assignments list
     */
    public function assignments(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');
        $events = $this->eventModel->all(['start_date' => 'DESC']);

        if (!$eventId && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $assignments = $eventId ? $this->assignmentModel->getByEvent($eventId) : [];
        $stats = $eventId ? $this->assignmentModel->getEventStats($eventId) : [];

        $this->renderAdmin('meetings/assignments', [
            'title' => 'Reuniones Asignadas',
            'assignments' => $assignments,
            'events' => $events,
            'currentEventId' => $eventId,
            'stats' => $stats,
            'statusOptions' => MeetingAssignment::getStatusOptions(),
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Matches without meetings
     */
    public function unassigned(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');
        $events = $this->eventModel->all(['start_date' => 'DESC']);

        if (!$eventId && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $matches = $eventId ? $this->assignmentModel->getUnassignedMatches($eventId) : [];
        $blocks = $eventId ? $this->blockModel->getActiveByEvent($eventId) : [];

        $this->renderAdmin('meetings/unassigned', [
            'title' => 'Matches sin Reunión',
            'matches' => $matches,
            'blocks' => $blocks,
            'events' => $events,
            'currentEventId' => $eventId,
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Get available slots for a match (AJAX)
     */
    public function availableSlots(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');
        $sponsorId = (int) $this->getQuery('sponsor_id');
        $companyId = (int) $this->getQuery('company_id');

        if (!$eventId || !$sponsorId || !$companyId) {
            $this->jsonError('Parámetros inválidos.');
            return;
        }

        $slots = $this->slotModel->getAvailableForMatch($eventId, $sponsorId, $companyId);

        // Group by block
        $grouped = [];
        foreach ($slots as $slot) {
            $blockName = $slot['block_name'] . ' - ' . $slot['event_date'];
            if (!isset($grouped[$blockName])) {
                $grouped[$blockName] = [];
            }
            $grouped[$blockName][] = $slot;
        }

        $this->jsonSuccess(['slots' => $grouped, 'count' => count($slots)]);
    }

    /**
     * Assign meeting
     */
    public function assign(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $slotId = (int) $this->getPost('slot_id');
        $eventId = (int) $this->getPost('event_id');
        $sponsorId = (int) $this->getPost('sponsor_id');
        $companyId = (int) $this->getPost('company_id');
        $notes = $this->getPost('notes');

        if (!$slotId || !$eventId || !$sponsorId || !$companyId) {
            $this->jsonError('Parámetros inválidos.');
            return;
        }

        try {
            $assignmentId = $this->assignmentModel->assign($slotId, $eventId, $sponsorId, $companyId, 'admin', $notes);

            if (!$assignmentId) {
                $this->jsonError('No se pudo asignar la reunión. El slot puede no estar disponible.');
                return;
            }

            $this->jsonSuccess(['id' => $assignmentId, 'message' => 'Reunión asignada correctamente.']);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Cancel assignment
     */
    public function cancelAssignment(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        try {
            $this->assignmentModel->cancel((int) $id);
            $this->jsonSuccess(['message' => 'Reunión cancelada.']);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Export schedule to CSV
     */
    public function export(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');

        if (!$eventId) {
            $this->flash('error', 'Evento no especificado.');
            $this->redirect('/admin/meetings/assignments');
        }

        $event = $this->eventModel->find($eventId);
        $assignments = $this->assignmentModel->getByEvent($eventId);

        $filename = 'reuniones_' . ($event['slug'] ?? 'evento') . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

        // Headers
        fputcsv($output, [
            'Fecha',
            'Hora',
            'Duración',
            'Mesa',
            'Bloque',
            'Sponsor',
            'Empresa',
            'Estado',
            'Notas',
        ], ';');

        foreach ($assignments as $assignment) {
            fputcsv($output, [
                $assignment['event_date'],
                $assignment['slot_time'],
                ($assignment['slot_duration'] ?? 15) . ' min',
                $assignment['room_name'] ?? 'Mesa ' . $assignment['room_number'],
                $assignment['block_name'],
                $assignment['sponsor_name'],
                $assignment['company_name'],
                $assignment['status'],
                $assignment['notes'],
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Matching overview (all matches)
     */
    public function matching(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');
        $events = $this->eventModel->all(['start_date' => 'DESC']);

        if (!$eventId && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $matches = $eventId ? $this->assignmentModel->getAllMatches($eventId) : [];

        // Count stats
        $totalMatches = count($matches);
        $withMeeting = count(array_filter($matches, fn($m) => $m['has_meeting']));
        $withoutMeeting = $totalMatches - $withMeeting;

        $this->renderAdmin('meetings/matching', [
            'title' => 'Matching Overview',
            'matches' => $matches,
            'events' => $events,
            'currentEventId' => $eventId,
            'stats' => [
                'total' => $totalMatches,
                'with_meeting' => $withMeeting,
                'without_meeting' => $withoutMeeting,
            ],
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }
}
