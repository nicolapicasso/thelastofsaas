<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Activity;
use App\Models\Event;
use App\Models\Room;
use App\Models\TeamMember;
use App\Models\Category;
use App\Helpers\Slug;

/**
 * Activities Controller
 * TLOS - The Last of SaaS
 *
 * Manage activities for events (talks, breaks, networking, etc.)
 */
class ActivitiesController extends Controller
{
    private Activity $activityModel;
    private Event $eventModel;
    private Room $roomModel;
    private TeamMember $teamModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->activityModel = new Activity();
        $this->eventModel = new Event();
        $this->roomModel = new Room();
        $this->teamModel = new TeamMember();
        $this->categoryModel = new Category();
    }

    /**
     * List all activities
     */
    public function index(): void
    {
        $this->requireAuth();

        $eventId = $this->getQuery('event_id') ? (int) $this->getQuery('event_id') : null;
        $date = $this->getQuery('date');

        $activities = $this->activityModel->getAllForAdmin($eventId, $date);
        $events = $this->eventModel->getActive();

        // Get unique dates from activities
        $dates = [];
        if ($eventId) {
            $dates = $this->activityModel->getEventDates($eventId);
        }

        $this->renderAdmin('activities/index', [
            'title' => 'Actividades',
            'activities' => $activities,
            'events' => $events,
            'dates' => $dates,
            'currentEventId' => $eventId,
            'currentDate' => $date,
            'activityTypes' => Activity::getActivityTypes(),
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $eventId = $this->getQuery('event_id') ? (int) $this->getQuery('event_id') : null;

        $this->renderAdmin('activities/form', [
            'title' => 'Nueva Actividad',
            'activity' => null,
            'events' => $this->eventModel->getActive(),
            'rooms' => $this->roomModel->getActive(),
            'speakers' => $this->teamModel->getActive(),
            'categories' => $this->categoryModel->getActive(),
            'activityTypes' => Activity::getActivityTypes(),
            'preselectedEventId' => $eventId,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new activity
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/activities/create');
            return;
        }

        $title = trim($this->getPost('title', ''));
        $eventId = (int) $this->getPost('event_id', 0);
        $activityDate = $this->getPost('activity_date', '');
        $startTime = $this->getPost('start_time', '');
        $endTime = $this->getPost('end_time', '');

        // Validations
        if (!$title) {
            $this->flash('error', 'El titulo es obligatorio.');
            $this->redirect('/admin/activities/create');
            return;
        }

        if (!$eventId) {
            $this->flash('error', 'Debes seleccionar un evento.');
            $this->redirect('/admin/activities/create');
            return;
        }

        if (!$activityDate || !$startTime || !$endTime) {
            $this->flash('error', 'La fecha y hora son obligatorias.');
            $this->redirect('/admin/activities/create');
            return;
        }

        $roomId = $this->getPost('room_id') ? (int) $this->getPost('room_id') : null;

        // Check time conflict if room selected
        if ($roomId && $this->activityModel->hasTimeConflict($roomId, $eventId, $activityDate, $startTime, $endTime)) {
            $this->flash('error', 'Ya existe una actividad en esa sala a esa hora.');
            $this->redirect('/admin/activities/create?event_id=' . $eventId);
            return;
        }

        $data = [
            'event_id' => $eventId,
            'room_id' => $roomId,
            'speaker_id' => $this->getPost('speaker_id') ? (int) $this->getPost('speaker_id') : null,
            'category_id' => $this->getPost('category_id') ? (int) $this->getPost('category_id') : null,
            'title' => $title,
            'slug' => Slug::unique($title, 'activities'),
            'description' => trim($this->getPost('description', '')),
            'activity_type' => $this->getPost('activity_type', 'charla'),
            'activity_date' => $activityDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'image_url' => trim($this->getPost('image_url', '')),
            'video_url' => trim($this->getPost('video_url', '')),
            'max_attendees' => $this->getPost('max_attendees') ? (int) $this->getPost('max_attendees') : null,
            'requires_registration' => (int) $this->getPost('requires_registration', 0),
            'is_featured' => (int) $this->getPost('is_featured', 0),
            'sort_order' => (int) $this->getPost('sort_order', 0),
            'active' => (int) $this->getPost('active', 1),
        ];

        $id = $this->activityModel->create($data);

        if ($id) {
            $this->flash('success', 'Actividad creada correctamente.');
            $this->redirect('/admin/activities?event_id=' . $eventId);
        } else {
            $this->flash('error', 'Error al crear la actividad.');
            $this->redirect('/admin/activities/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $activity = $this->activityModel->find((int) $id);

        if (!$activity) {
            $this->flash('error', 'Actividad no encontrada.');
            $this->redirect('/admin/activities');
            return;
        }

        $this->renderAdmin('activities/form', [
            'title' => 'Editar Actividad',
            'activity' => $activity,
            'events' => $this->eventModel->getActive(),
            'rooms' => $this->roomModel->getActive(),
            'speakers' => $this->teamModel->getActive(),
            'categories' => $this->categoryModel->getActive(),
            'activityTypes' => Activity::getActivityTypes(),
            'preselectedEventId' => $activity['event_id'],
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update activity
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/activities/' . $id . '/edit');
            return;
        }

        $activity = $this->activityModel->find((int) $id);

        if (!$activity) {
            $this->flash('error', 'Actividad no encontrada.');
            $this->redirect('/admin/activities');
            return;
        }

        $title = trim($this->getPost('title', ''));
        $eventId = (int) $this->getPost('event_id', 0);
        $activityDate = $this->getPost('activity_date', '');
        $startTime = $this->getPost('start_time', '');
        $endTime = $this->getPost('end_time', '');

        // Validations
        if (!$title || !$eventId || !$activityDate || !$startTime || !$endTime) {
            $this->flash('error', 'Todos los campos obligatorios deben estar completos.');
            $this->redirect('/admin/activities/' . $id . '/edit');
            return;
        }

        $roomId = $this->getPost('room_id') ? (int) $this->getPost('room_id') : null;

        // Check time conflict if room selected
        if ($roomId && $this->activityModel->hasTimeConflict($roomId, $eventId, $activityDate, $startTime, $endTime, (int) $id)) {
            $this->flash('error', 'Ya existe una actividad en esa sala a esa hora.');
            $this->redirect('/admin/activities/' . $id . '/edit');
            return;
        }

        $data = [
            'event_id' => $eventId,
            'room_id' => $roomId,
            'speaker_id' => $this->getPost('speaker_id') ? (int) $this->getPost('speaker_id') : null,
            'category_id' => $this->getPost('category_id') ? (int) $this->getPost('category_id') : null,
            'title' => $title,
            'description' => trim($this->getPost('description', '')),
            'activity_type' => $this->getPost('activity_type', 'charla'),
            'activity_date' => $activityDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'image_url' => trim($this->getPost('image_url', '')),
            'video_url' => trim($this->getPost('video_url', '')),
            'max_attendees' => $this->getPost('max_attendees') ? (int) $this->getPost('max_attendees') : null,
            'requires_registration' => (int) $this->getPost('requires_registration', 0),
            'is_featured' => (int) $this->getPost('is_featured', 0),
            'sort_order' => (int) $this->getPost('sort_order', 0),
            'active' => (int) $this->getPost('active', 1),
        ];

        // Update slug only if title changed
        if ($title !== $activity['title']) {
            $data['slug'] = Slug::unique($title, 'activities');
        }

        $this->activityModel->update((int) $id, $data);

        $this->flash('success', 'Actividad actualizada correctamente.');
        $this->redirect('/admin/activities?event_id=' . $eventId);
    }

    /**
     * Delete activity
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/activities');
            return;
        }

        $activity = $this->activityModel->find((int) $id);

        if (!$activity) {
            $this->flash('error', 'Actividad no encontrada.');
            $this->redirect('/admin/activities');
            return;
        }

        $eventId = $activity['event_id'];
        $this->activityModel->delete((int) $id);

        $this->flash('success', 'Actividad eliminada correctamente.');
        $this->redirect('/admin/activities?event_id=' . $eventId);
    }

    /**
     * Duplicate activity
     */
    public function duplicate(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/activities');
            return;
        }

        $activity = $this->activityModel->find((int) $id);

        if (!$activity) {
            $this->flash('error', 'Actividad no encontrada.');
            $this->redirect('/admin/activities');
            return;
        }

        // Remove id and create new slug
        unset($activity['id'], $activity['created_at'], $activity['updated_at']);
        $activity['title'] = $activity['title'] . ' (copia)';
        $activity['slug'] = Slug::unique($activity['title'], 'activities');

        $newId = $this->activityModel->create($activity);

        if ($newId) {
            $this->flash('success', 'Actividad duplicada correctamente.');
            $this->redirect('/admin/activities/' . $newId . '/edit');
        } else {
            $this->flash('error', 'Error al duplicar la actividad.');
            $this->redirect('/admin/activities');
        }
    }

    /**
     * Reorder activities (AJAX)
     */
    public function reorder(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesion expirada', 403);
            return;
        }

        $order = $this->getPost('order', []);

        if (!is_array($order)) {
            $this->jsonError('Datos invalidos', 400);
            return;
        }

        $this->activityModel->reorder($order);

        $this->json(['success' => true]);
    }

    /**
     * Get activities for an event (AJAX)
     */
    public function getByEvent(string $eventId): void
    {
        $this->requireAuth();

        $activities = $this->activityModel->getByEvent((int) $eventId, false);

        $this->json([
            'success' => true,
            'activities' => $activities
        ]);
    }
}
