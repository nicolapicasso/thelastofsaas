<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Room;
use App\Helpers\Slug;

/**
 * Rooms Controller
 * TLOS - The Last of SaaS
 *
 * Manage rooms/salas for activities and meetings
 */
class RoomsController extends Controller
{
    private Room $roomModel;

    public function __construct()
    {
        parent::__construct();
        $this->roomModel = new Room();
    }

    /**
     * List all rooms
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) ($this->getQuery('page', 1));
        $active = $this->getQuery('active');

        $conditions = [];
        if ($active !== null && $active !== '') {
            $conditions['active'] = (int) $active;
        }

        $result = $this->roomModel->paginate($page, 20, $conditions, ['sort_order' => 'ASC', 'name' => 'ASC']);

        $this->renderAdmin('rooms/index', [
            'title' => 'Salas',
            'rooms' => $result['data'],
            'pagination' => $result['pagination'],
            'currentActive' => $active,
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

        $this->renderAdmin('rooms/form', [
            'title' => 'Nueva Sala',
            'room' => null,
            'colorOptions' => Room::getColorOptions(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new room
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/rooms/create');
            return;
        }

        $name = trim($this->getPost('name', ''));

        if (!$name) {
            $this->flash('error', 'El nombre es obligatorio.');
            $this->redirect('/admin/rooms/create');
            return;
        }

        $data = [
            'name' => $name,
            'slug' => Slug::unique($name, 'rooms'),
            'description' => trim($this->getPost('description', '')),
            'capacity' => (int) $this->getPost('capacity', 0) ?: null,
            'location' => trim($this->getPost('location', '')),
            'floor' => trim($this->getPost('floor', '')),
            'equipment' => trim($this->getPost('equipment', '')),
            'image_url' => trim($this->getPost('image_url', '')),
            'color' => $this->getPost('color', '#3B82F6'),
            'active' => (int) $this->getPost('active', 1),
            'sort_order' => (int) $this->getPost('sort_order', 0),
        ];

        $id = $this->roomModel->create($data);

        if ($id) {
            $this->flash('success', 'Sala creada correctamente.');
            $this->redirect('/admin/rooms');
        } else {
            $this->flash('error', 'Error al crear la sala.');
            $this->redirect('/admin/rooms/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $room = $this->roomModel->find((int) $id);

        if (!$room) {
            $this->flash('error', 'Sala no encontrada.');
            $this->redirect('/admin/rooms');
            return;
        }

        $this->renderAdmin('rooms/form', [
            'title' => 'Editar Sala',
            'room' => $room,
            'colorOptions' => Room::getColorOptions(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update room
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/rooms/' . $id . '/edit');
            return;
        }

        $room = $this->roomModel->find((int) $id);

        if (!$room) {
            $this->flash('error', 'Sala no encontrada.');
            $this->redirect('/admin/rooms');
            return;
        }

        $name = trim($this->getPost('name', ''));

        if (!$name) {
            $this->flash('error', 'El nombre es obligatorio.');
            $this->redirect('/admin/rooms/' . $id . '/edit');
            return;
        }

        $data = [
            'name' => $name,
            'description' => trim($this->getPost('description', '')),
            'capacity' => (int) $this->getPost('capacity', 0) ?: null,
            'location' => trim($this->getPost('location', '')),
            'floor' => trim($this->getPost('floor', '')),
            'equipment' => trim($this->getPost('equipment', '')),
            'image_url' => trim($this->getPost('image_url', '')),
            'color' => $this->getPost('color', '#3B82F6'),
            'active' => (int) $this->getPost('active', 1),
            'sort_order' => (int) $this->getPost('sort_order', 0),
        ];

        // Update slug only if name changed
        if ($name !== $room['name']) {
            $data['slug'] = Slug::unique($name, 'rooms');
        }

        $this->roomModel->update((int) $id, $data);

        $this->flash('success', 'Sala actualizada correctamente.');
        $this->redirect('/admin/rooms');
    }

    /**
     * Delete room
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/rooms');
            return;
        }

        $room = $this->roomModel->find((int) $id);

        if (!$room) {
            $this->flash('error', 'Sala no encontrada.');
            $this->redirect('/admin/rooms');
            return;
        }

        // Check if room has activities
        $activities = $this->roomModel->getActivities((int) $id);
        if (!empty($activities)) {
            $this->flash('error', 'No se puede eliminar la sala porque tiene actividades asignadas.');
            $this->redirect('/admin/rooms');
            return;
        }

        $this->roomModel->delete((int) $id);

        $this->flash('success', 'Sala eliminada correctamente.');
        $this->redirect('/admin/rooms');
    }

    /**
     * Reorder rooms
     */
    public function reorder(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada', 403);
            return;
        }

        $order = $this->getPost('order', []);

        if (!is_array($order)) {
            $this->jsonError('Datos inválidos', 400);
            return;
        }

        foreach ($order as $position => $id) {
            $this->roomModel->update((int) $id, ['sort_order' => $position]);
        }

        $this->json(['success' => true]);
    }
}
