<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Event;
use App\Models\Sponsor;
use App\Models\TicketType;
use App\Helpers\Sanitizer;
use App\Helpers\Slug;

/**
 * Events Controller
 * TLOS - The Last of SaaS
 */
class EventsController extends Controller
{
    private Event $eventModel;
    private Sponsor $sponsorModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
        $this->sponsorModel = new Sponsor();
    }

    /**
     * List all events
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) ($this->getQuery('page', 1));
        $status = $this->getQuery('status');

        $conditions = [];
        if ($status) {
            $conditions['status'] = $status;
        }

        $result = $this->eventModel->paginate($page, 20, $conditions, ['start_date' => 'DESC']);

        $this->renderAdmin('events/index', [
            'title' => 'Eventos',
            'events' => $result['data'],
            'pagination' => $result['pagination'],
            'currentStatus' => $status,
            'statusOptions' => Event::getStatusOptions(),
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

        $this->renderAdmin('events/form', [
            'title' => 'Nuevo Evento',
            'event' => null,
            'statusOptions' => Event::getStatusOptions(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new event
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/events/create');
        }

        $data = $this->validateEventData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/events/create');
        }

        // Generate slug
        $data['slug'] = Slug::unique($data['name'], 'events');

        try {
            $eventId = $this->eventModel->create($data);
            $this->flash('success', 'Evento creado correctamente.');
            $this->redirect('/admin/events/' . $eventId . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el evento: ' . $e->getMessage());
            $this->redirect('/admin/events/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $event = $this->eventModel->find((int) $id);

        if (!$event) {
            $this->flash('error', 'Evento no encontrado.');
            $this->redirect('/admin/events');
        }

        $sponsors = $this->eventModel->getSponsors((int) $id);
        $features = $this->eventModel->getFeatures((int) $id);
        $allSponsors = $this->sponsorModel->getActive();
        $stats = $this->eventModel->getStats((int) $id);

        $this->renderAdmin('events/form', [
            'title' => 'Editar Evento',
            'event' => $event,
            'sponsors' => $sponsors,
            'features' => $features,
            'allSponsors' => $allSponsors,
            'stats' => $stats,
            'statusOptions' => Event::getStatusOptions(),
            'levelOptions' => Sponsor::getLevelOptions(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update event
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/events/' . $id . '/edit');
        }

        $event = $this->eventModel->find((int) $id);

        if (!$event) {
            $this->flash('error', 'Evento no encontrado.');
            $this->redirect('/admin/events');
        }

        $data = $this->validateEventData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/events/' . $id . '/edit');
        }

        // Update slug if name changed
        if ($data['name'] !== $event['name']) {
            $data['slug'] = Slug::unique($data['name'], 'events', 'slug', (int) $id);
        }

        try {
            $this->eventModel->update((int) $id, $data);
            $this->flash('success', 'Evento actualizado correctamente.');
            $this->redirect('/admin/events/' . $id . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar el evento: ' . $e->getMessage());
            $this->redirect('/admin/events/' . $id . '/edit');
        }
    }

    /**
     * Delete event
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/events');
        }

        $event = $this->eventModel->find((int) $id);

        if (!$event) {
            $this->flash('error', 'Evento no encontrado.');
            $this->redirect('/admin/events');
        }

        try {
            $this->eventModel->delete((int) $id);
            $this->flash('success', 'Evento eliminado correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar el evento: ' . $e->getMessage());
        }

        $this->redirect('/admin/events');
    }

    /**
     * Add sponsor to event
     */
    public function addSponsor(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $event = $this->eventModel->find((int) $id);
        if (!$event) {
            $this->jsonError('Evento no encontrado.');
            return;
        }

        $sponsorId = (int) $this->getPost('sponsor_id');
        $level = $this->getPost('level', 'bronze');

        if (!$sponsorId) {
            $this->jsonError('Sponsor no especificado.');
            return;
        }

        try {
            $this->eventModel->addSponsor((int) $id, $sponsorId, $level);

            // Create free ticket type for sponsor
            $sponsor = $this->sponsorModel->find($sponsorId);
            if ($sponsor) {
                $ticketTypeModel = new TicketType();
                $existing = $ticketTypeModel->getBySponsor((int) $id, $sponsorId);
                if (!$existing) {
                    $ticketTypeModel->createForSponsor((int) $id, $sponsorId, $sponsor['name']);
                }
            }

            $this->jsonSuccess(['message' => 'Sponsor añadido correctamente.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al añadir sponsor: ' . $e->getMessage());
        }
    }

    /**
     * Remove sponsor from event
     */
    public function removeSponsor(string $id, string $sponsorId): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        try {
            $this->eventModel->removeSponsor((int) $id, (int) $sponsorId);
            $this->jsonSuccess(['message' => 'Sponsor eliminado del evento.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al eliminar sponsor: ' . $e->getMessage());
        }
    }

    /**
     * Update sponsor level
     */
    public function updateSponsorLevel(string $id, string $sponsorId): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $level = $this->getPost('level', 'bronze');

        try {
            $this->eventModel->updateSponsorLevel((int) $id, (int) $sponsorId, $level);
            $this->jsonSuccess(['message' => 'Nivel actualizado.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al actualizar nivel: ' . $e->getMessage());
        }
    }

    /**
     * Add feature to event
     */
    public function addFeature(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $title = Sanitizer::string($this->getPost('title'));
        $description = Sanitizer::string($this->getPost('description'));
        $icon = Sanitizer::string($this->getPost('icon'));

        if (empty($title)) {
            $this->jsonError('El título es obligatorio.');
            return;
        }

        try {
            $featureId = $this->eventModel->addFeature((int) $id, [
                'title' => $title,
                'description' => $description,
                'icon' => $icon,
            ]);
            $this->jsonSuccess(['id' => $featureId, 'message' => 'Característica añadida.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al añadir característica: ' . $e->getMessage());
        }
    }

    /**
     * Get event stats (AJAX)
     */
    public function stats(string $id): void
    {
        $this->requireAuth();

        $stats = $this->eventModel->getStats((int) $id);
        $this->jsonSuccess($stats);
    }

    /**
     * Validate event form data
     */
    private function validateEventData(): array
    {
        $errors = [];

        $name = Sanitizer::string($this->getPost('name'));
        $shortDescription = Sanitizer::string($this->getPost('short_description'));
        $description = $this->getPost('description');
        $location = Sanitizer::string($this->getPost('location'));
        $address = Sanitizer::string($this->getPost('address'));
        $city = Sanitizer::string($this->getPost('city'));
        $coordinates = Sanitizer::string($this->getPost('coordinates'));
        $startDate = $this->getPost('start_date');
        $endDate = $this->getPost('end_date');
        $startTime = $this->getPost('start_time');
        $endTime = $this->getPost('end_time');
        $maxAttendees = Sanitizer::int($this->getPost('max_attendees', 100));
        $status = $this->getPost('status', 'draft');
        $featuredImage = Sanitizer::url($this->getPost('featured_image'));
        $registrationOpen = Sanitizer::bool($this->getPost('registration_open'));
        $matchingEnabled = Sanitizer::bool($this->getPost('matching_enabled'));
        $meetingsEnabled = Sanitizer::bool($this->getPost('meetings_enabled'));

        if (empty($name)) {
            $errors[] = 'El nombre es obligatorio.';
        }

        if (empty($startDate)) {
            $errors[] = 'La fecha de inicio es obligatoria.';
        }

        if ($maxAttendees < 1) {
            $maxAttendees = 100;
        }

        if (!in_array($status, array_keys(Event::getStatusOptions()))) {
            $errors[] = 'Estado no válido.';
        }

        // Handle featured image file upload
        if (isset($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleImageUpload($_FILES['featured_image_file']);
            if (isset($uploadResult['error'])) {
                $errors[] = $uploadResult['error'];
            } else {
                $featuredImage = $uploadResult['url'];
            }
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'name' => $name,
            'short_description' => $shortDescription ?: null,
            'description' => $description ?: null,
            'location' => $location ?: null,
            'address' => $address ?: null,
            'city' => $city ?: null,
            'coordinates' => $coordinates ?: null,
            'start_date' => $startDate,
            'end_date' => $endDate ?: null,
            'start_time' => $startTime ?: null,
            'end_time' => $endTime ?: null,
            'max_attendees' => $maxAttendees,
            'status' => $status,
            'featured_image' => $featuredImage ?: null,
            'registration_open' => $registrationOpen ? 1 : 0,
            'matching_enabled' => $matchingEnabled ? 1 : 0,
            'meetings_enabled' => $meetingsEnabled ? 1 : 0,
        ];
    }

    /**
     * Handle image file upload
     */
    private function handleImageUpload(array $file): array
    {
        $allowedTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Validate file type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['error' => 'Tipo de archivo no permitido. Use PNG, JPG, GIF o WebP.'];
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return ['error' => 'El archivo es demasiado grande. Maximo 2MB.'];
        }

        // Create upload directory if it doesn't exist
        $uploadDir = dirname(__DIR__, 3) . '/public/uploads/events';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = match($mimeType) {
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg'
        };
        $filename = 'event_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['error' => 'Error al guardar el archivo.'];
        }

        // Return the URL
        return ['url' => '/uploads/events/' . $filename];
    }
}
