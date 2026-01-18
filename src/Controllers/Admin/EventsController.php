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
        $data['slug'] = Slug::generate($data['name'], 'events');

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
            $data['slug'] = Slug::generate($data['name'], 'events', (int) $id);
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
        $description = $this->getPost('description');
        $venueName = Sanitizer::string($this->getPost('venue_name'));
        $venueAddress = Sanitizer::string($this->getPost('venue_address'));
        $venueCity = Sanitizer::string($this->getPost('venue_city'));
        $venueCoordinates = Sanitizer::string($this->getPost('venue_coordinates'));
        $eventDate = $this->getPost('event_date');
        $eventEndDate = $this->getPost('event_end_date');
        $totalCapacity = Sanitizer::int($this->getPost('total_capacity'));
        $status = $this->getPost('status', 'draft');
        $featuredImage = Sanitizer::url($this->getPost('featured_image'));
        $registrationOpen = Sanitizer::bool($this->getPost('registration_open'));
        $matchingEnabled = Sanitizer::bool($this->getPost('matching_enabled'));
        $meetingsEnabled = Sanitizer::bool($this->getPost('meetings_enabled'));

        if (empty($name)) {
            $errors[] = 'El nombre es obligatorio.';
        }

        if ($totalCapacity < 1) {
            $errors[] = 'El aforo debe ser mayor que 0.';
        }

        if (!in_array($status, array_keys(Event::getStatusOptions()))) {
            $errors[] = 'Estado no válido.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'name' => $name,
            'description' => $description ?: null,
            'venue_name' => $venueName ?: null,
            'venue_address' => $venueAddress ?: null,
            'venue_city' => $venueCity ?: null,
            'venue_coordinates' => $venueCoordinates ?: null,
            'event_date' => $eventDate ?: null,
            'event_end_date' => $eventEndDate ?: null,
            'total_capacity' => $totalCapacity,
            'status' => $status,
            'featured_image' => $featuredImage ?: null,
            'registration_open' => $registrationOpen ? 1 : 0,
            'matching_enabled' => $matchingEnabled ? 1 : 0,
            'meetings_enabled' => $meetingsEnabled ? 1 : 0,
        ];
    }
}
