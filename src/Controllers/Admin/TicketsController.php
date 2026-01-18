<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Event;
use App\Models\Sponsor;
use App\Helpers\Sanitizer;

/**
 * Tickets Controller
 * TLOS - The Last of SaaS
 */
class TicketsController extends Controller
{
    private Ticket $ticketModel;
    private TicketType $ticketTypeModel;
    private Event $eventModel;
    private Sponsor $sponsorModel;

    public function __construct()
    {
        parent::__construct();
        $this->ticketModel = new Ticket();
        $this->ticketTypeModel = new TicketType();
        $this->eventModel = new Event();
        $this->sponsorModel = new Sponsor();
    }

    /**
     * List all tickets for an event
     */
    public function index(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');
        $status = $this->getQuery('status');
        $page = (int) ($this->getQuery('page', 1));

        $events = $this->eventModel->all(['start_date' => 'DESC']);

        if (!$eventId && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $conditions = ['event_id' => $eventId];
        if ($status) {
            $conditions['status'] = $status;
        }

        $result = $this->ticketModel->paginate($page, 30, $conditions, ['created_at' => 'DESC']);
        $tickets = $eventId ? $this->ticketModel->getByEvent($eventId) : [];
        $stats = $eventId ? $this->ticketModel->getEventStats($eventId) : [];

        $this->renderAdmin('tickets/index', [
            'title' => 'Entradas',
            'tickets' => $tickets,
            'pagination' => $result['pagination'],
            'events' => $events,
            'currentEventId' => $eventId,
            'currentStatus' => $status,
            'stats' => $stats,
            'statusOptions' => Ticket::getStatusOptions(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Show ticket details
     */
    public function show(string $id): void
    {
        $this->requireAuth();

        $ticket = $this->ticketModel->find((int) $id);

        if (!$ticket) {
            $this->flash('error', 'Entrada no encontrada.');
            $this->redirect('/admin/tickets');
        }

        $event = $this->eventModel->find($ticket['event_id']);
        $ticketType = $this->ticketTypeModel->find($ticket['ticket_type_id']);
        $sponsor = $ticket['invited_by_sponsor_id']
            ? $this->sponsorModel->find($ticket['invited_by_sponsor_id'])
            : null;

        $this->renderAdmin('tickets/show', [
            'title' => 'Detalle de Entrada',
            'ticket' => $ticket,
            'event' => $event,
            'ticketType' => $ticketType,
            'sponsor' => $sponsor,
            'statusOptions' => Ticket::getStatusOptions(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Check in a ticket
     */
    public function checkIn(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $ticket = $this->ticketModel->find((int) $id);

        if (!$ticket) {
            $this->jsonError('Entrada no encontrada.');
            return;
        }

        if ($ticket['status'] !== 'confirmed') {
            $this->jsonError('La entrada no está confirmada.');
            return;
        }

        try {
            $this->ticketModel->checkIn((int) $id);
            $this->jsonSuccess(['message' => 'Check-in realizado correctamente.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al hacer check-in: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a ticket
     */
    public function cancel(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $ticket = $this->ticketModel->find((int) $id);

        if (!$ticket) {
            $this->jsonError('Entrada no encontrada.');
            return;
        }

        try {
            $this->ticketModel->cancel((int) $id);

            // Decrement sold count
            $this->ticketTypeModel->decrementSold($ticket['ticket_type_id']);

            $this->jsonSuccess(['message' => 'Entrada cancelada.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al cancelar: ' . $e->getMessage());
        }
    }

    /**
     * Export tickets to CSV
     */
    public function export(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');

        if (!$eventId) {
            $this->flash('error', 'Evento no especificado.');
            $this->redirect('/admin/tickets');
        }

        $event = $this->eventModel->find($eventId);
        $tickets = $this->ticketModel->getByEvent($eventId);

        $filename = 'entradas_' . ($event['slug'] ?? 'evento') . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

        // Headers
        fputcsv($output, [
            'Código',
            'Nombre',
            'Apellidos',
            'Email',
            'Teléfono',
            'Cargo',
            'Empresa',
            'Tamaño',
            'Tipo Entrada',
            'Estado',
            'Pago',
            'Importe',
            'Invitado por',
            'Fecha Registro',
            'Check-in',
        ], ';');

        foreach ($tickets as $ticket) {
            fputcsv($output, [
                $ticket['ticket_code'],
                $ticket['attendee_first_name'],
                $ticket['attendee_last_name'],
                $ticket['attendee_email'],
                $ticket['attendee_phone'],
                $ticket['attendee_job_title'],
                $ticket['attendee_company_name'],
                $ticket['attendee_company_size'],
                $ticket['ticket_type_name'],
                $ticket['status'],
                $ticket['payment_status'],
                $ticket['amount_paid'],
                $ticket['sponsor_name'] ?? 'Organización',
                $ticket['created_at'],
                $ticket['checked_in_at'],
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Ticket types management
     */
    public function types(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');
        $events = $this->eventModel->all(['start_date' => 'DESC']);

        if (!$eventId && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $ticketTypes = $eventId ? $this->ticketTypeModel->getByEvent($eventId) : [];

        $this->renderAdmin('tickets/types', [
            'title' => 'Tipos de Entrada',
            'ticketTypes' => $ticketTypes,
            'events' => $events,
            'currentEventId' => $eventId,
            'statusOptions' => TicketType::getStatusOptions(),
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Create ticket type
     */
    public function createType(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/tickets/types');
        }

        $eventId = (int) $this->getPost('event_id');
        $name = Sanitizer::string($this->getPost('name'));
        $description = $this->getPost('description');
        $price = (float) $this->getPost('price', 0);
        $quantityAvailable = $this->getPost('quantity_available');
        $saleStartDate = $this->getPost('sale_start_date');
        $saleEndDate = $this->getPost('sale_end_date');

        if (!$eventId || empty($name)) {
            $this->flash('error', 'Evento y nombre son obligatorios.');
            $this->redirect('/admin/tickets/types?event_id=' . $eventId);
        }

        try {
            $this->ticketTypeModel->create([
                'event_id' => $eventId,
                'name' => $name,
                'description' => $description ?: null,
                'price' => $price,
                'is_free' => $price == 0 ? 1 : 0,
                'quantity_available' => $quantityAvailable ?: null,
                'sale_start_date' => $saleStartDate ?: null,
                'sale_end_date' => $saleEndDate ?: null,
                'status' => 'active',
            ]);

            $this->flash('success', 'Tipo de entrada creado.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error: ' . $e->getMessage());
        }

        $this->redirect('/admin/tickets/types?event_id=' . $eventId);
    }

    /**
     * Update ticket type
     */
    public function updateType(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $ticketType = $this->ticketTypeModel->find((int) $id);

        if (!$ticketType) {
            $this->jsonError('Tipo de entrada no encontrado.');
            return;
        }

        $data = [
            'name' => Sanitizer::string($this->getPost('name')),
            'description' => $this->getPost('description'),
            'price' => (float) $this->getPost('price', 0),
            'quantity_available' => $this->getPost('quantity_available') ?: null,
            'sale_start_date' => $this->getPost('sale_start_date') ?: null,
            'sale_end_date' => $this->getPost('sale_end_date') ?: null,
            'status' => $this->getPost('status', 'active'),
        ];

        $data['is_free'] = $data['price'] == 0 ? 1 : 0;

        try {
            $this->ticketTypeModel->update((int) $id, $data);
            $this->jsonSuccess(['message' => 'Tipo de entrada actualizado.']);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete ticket type
     */
    public function deleteType(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $ticketType = $this->ticketTypeModel->find((int) $id);

        if (!$ticketType) {
            $this->jsonError('Tipo de entrada no encontrado.');
            return;
        }

        // Check if has tickets
        $ticketsCount = $this->ticketModel->count(['ticket_type_id' => (int) $id]);
        if ($ticketsCount > 0) {
            $this->jsonError('No se puede eliminar: tiene entradas asociadas.');
            return;
        }

        try {
            $this->ticketTypeModel->delete((int) $id);
            $this->jsonSuccess(['message' => 'Tipo de entrada eliminado.']);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * QR Scanner page
     */
    public function scanner(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');
        $events = $this->eventModel->getActive();

        if (!$eventId && !empty($events)) {
            $eventId = $events[0]['id'];
        }

        $this->renderAdmin('tickets/scanner', [
            'title' => 'Scanner Check-in',
            'events' => $events,
            'currentEventId' => $eventId,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Validate ticket code (AJAX for scanner)
     */
    public function validateCode(): void
    {
        $this->requireAuth();

        $code = Sanitizer::string($this->getPost('code'));

        if (empty($code)) {
            $this->jsonError('Código no proporcionado.');
            return;
        }

        $ticket = $this->ticketModel->findByCode($code);

        if (!$ticket) {
            $this->jsonError('Entrada no encontrada.', ['valid' => false]);
            return;
        }

        $event = $this->eventModel->find($ticket['event_id']);

        $response = [
            'valid' => true,
            'ticket' => [
                'id' => $ticket['id'],
                'code' => $ticket['ticket_code'],
                'name' => $this->ticketModel->getAttendeeName($ticket),
                'email' => $ticket['attendee_email'],
                'company' => $ticket['attendee_company_name'],
                'status' => $ticket['status'],
                'event_name' => $event['name'] ?? 'N/A',
            ],
            'can_checkin' => $ticket['status'] === 'confirmed',
            'already_checked_in' => $ticket['status'] === 'checked_in',
        ];

        $this->jsonSuccess($response);
    }
}
