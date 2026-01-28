<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Event;
use App\Models\Sponsor;
use App\Models\Company;
use App\Models\SponsorContact;
use App\Models\CompanyContact;
use App\Helpers\Sanitizer;
use App\Services\OmniwalletService;
use App\Services\EmailService;

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
    private Company $companyModel;
    private SponsorContact $sponsorContactModel;
    private CompanyContact $companyContactModel;

    public function __construct()
    {
        parent::__construct();
        $this->ticketModel = new Ticket();
        $this->ticketTypeModel = new TicketType();
        $this->eventModel = new Event();
        $this->sponsorModel = new Sponsor();
        $this->companyModel = new Company();
        $this->sponsorContactModel = new SponsorContact();
        $this->companyContactModel = new CompanyContact();
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
            'csrf_token' => $this->generateCsrf(),
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
        $sponsorId = $ticket['sponsor_id'] ?? $ticket['invited_by_sponsor_id'] ?? null;
        $sponsor = $sponsorId ? $this->sponsorModel->find($sponsorId) : null;

        // Get assigned company/sponsor for this ticket
        $assignedCompany = null;
        $assignedSponsor = null;
        if (!empty($ticket['assigned_company_id'])) {
            $assignedCompany = $this->companyModel->find($ticket['assigned_company_id']);
        }
        if (!empty($ticket['assigned_sponsor_id'])) {
            $assignedSponsor = $this->sponsorModel->find($ticket['assigned_sponsor_id']);
        }

        // Get all sponsors and companies for the event (for assignment dropdown)
        $eventSponsors = $this->sponsorModel->getAllByEvent($ticket['event_id']);
        $eventCompanies = $this->companyModel->getByEvent($ticket['event_id']);

        // Also get all active sponsors and companies (not just those in event)
        $allSponsors = $this->sponsorModel->getAllActive();
        $allCompanies = $this->companyModel->getActive();

        $this->renderAdmin('tickets/show', [
            'title' => 'Detalle de Entrada',
            'ticket' => $ticket,
            'event' => $event,
            'ticketType' => $ticketType,
            'sponsor' => $sponsor,
            'assignedCompany' => $assignedCompany,
            'assignedSponsor' => $assignedSponsor,
            'eventSponsors' => $eventSponsors,
            'eventCompanies' => $eventCompanies,
            'allSponsors' => $allSponsors,
            'allCompanies' => $allCompanies,
            'statusOptions' => Ticket::getStatusOptions(),
            'csrf_token' => $this->generateCsrf(),
            'flash' => $_SESSION['flash'] ?? null,
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

            // Omniwallet integration - award points for check-in
            $this->processOmniwalletCheckin($ticket);

            $this->jsonSuccess(['message' => 'Check-in realizado correctamente.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al hacer check-in: ' . $e->getMessage());
        }
    }

    /**
     * Process Omniwallet integration for check-in
     */
    private function processOmniwalletCheckin(array $ticket): void
    {
        try {
            $omniwallet = new OmniwalletService();

            if (!$omniwallet->isEnabled()) {
                return;
            }

            $omniwallet->processCheckin($ticket);
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            error_log('Omniwallet check-in error: ' . $e->getMessage());
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
     * Approve a pending ticket
     */
    public function approve(string $id): void
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

        if ($ticket['status'] !== 'pending') {
            $this->jsonError('Solo se pueden aprobar tickets pendientes.');
            return;
        }

        try {
            $this->ticketModel->update((int) $id, ['status' => 'confirmed']);
            $this->jsonSuccess(['message' => 'Ticket aprobado correctamente.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al aprobar: ' . $e->getMessage());
        }
    }

    /**
     * Bulk action on multiple tickets
     */
    public function bulkAction(): void
    {
        $this->requireAuth();

        // Get JSON body
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') === false) {
            $this->jsonError('Content-Type debe ser application/json');
            return;
        }

        $json = json_decode(file_get_contents('php://input'), true);
        if (!$json) {
            $this->jsonError('JSON inválido');
            return;
        }

        $ids = $json['ids'] ?? [];
        $action = $json['action'] ?? '';
        $value = $json['value'] ?? null;

        if (empty($ids) || !is_array($ids)) {
            $this->jsonError('No se han seleccionado tickets');
            return;
        }

        if (empty($action)) {
            $this->jsonError('Acción no especificada');
            return;
        }

        $validActions = ['check-in', 'approve', 'status', 'delete'];
        if (!in_array($action, $validActions)) {
            $this->jsonError('Acción no válida');
            return;
        }

        $processed = 0;
        $errors = [];

        foreach ($ids as $id) {
            $id = (int) $id;
            $ticket = $this->ticketModel->find($id);

            if (!$ticket) {
                $errors[] = "Ticket #$id no encontrado";
                continue;
            }

            try {
                switch ($action) {
                    case 'check-in':
                        if ($ticket['status'] === 'confirmed') {
                            $this->ticketModel->checkIn($id);
                            $processed++;
                        } else {
                            $errors[] = "Ticket #$id no está confirmado";
                        }
                        break;

                    case 'approve':
                        if ($ticket['status'] === 'pending') {
                            $this->ticketModel->update($id, ['status' => 'confirmed']);
                            $processed++;
                        } else {
                            $errors[] = "Ticket #$id no está pendiente";
                        }
                        break;

                    case 'status':
                        if (!$value) {
                            $errors[] = "Estado no especificado";
                            break;
                        }
                        $validStatuses = array_keys(Ticket::getStatusOptions());
                        if (!in_array($value, $validStatuses)) {
                            $errors[] = "Estado '$value' no válido";
                            break;
                        }
                        $this->ticketModel->update($id, ['status' => $value]);
                        $processed++;
                        break;

                    case 'delete':
                        $this->ticketModel->delete($id);
                        // Decrement sold count
                        if (!empty($ticket['ticket_type_id'])) {
                            $this->ticketTypeModel->decrementSold($ticket['ticket_type_id']);
                        }
                        $processed++;
                        break;
                }
            } catch (\Exception $e) {
                $errors[] = "Error en ticket #$id: " . $e->getMessage();
            }
        }

        if ($processed > 0) {
            $message = "$processed ticket(s) procesado(s) correctamente";
            if (!empty($errors)) {
                $message .= ". Errores: " . implode(', ', $errors);
            }
            // Get updated pending count for badge
            $pendingCount = $this->ticketModel->count(['status' => 'pending']);
            $this->jsonSuccess([
                'message' => $message,
                'processed' => $processed,
                'errors' => $errors,
                'pendingCount' => $pendingCount
            ]);
        } else {
            $this->jsonError('No se pudo procesar ningún ticket. ' . implode(', ', $errors));
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
        $maxTickets = $this->getPost('max_tickets');
        $saleStart = $this->getPost('sale_start');
        $saleEnd = $this->getPost('sale_end');
        $active = $this->getPost('active') ? 1 : 0;
        $requiresApproval = $this->getPost('requires_approval') ? 1 : 0;

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
                'max_tickets' => $maxTickets ?: 100,
                'tickets_sold' => 0,
                'sale_start' => $saleStart ?: null,
                'sale_end' => $saleEnd ?: null,
                'active' => $active,
                'requires_approval' => $requiresApproval,
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
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/tickets/types');
            return;
        }

        $ticketType = $this->ticketTypeModel->find((int) $id);

        if (!$ticketType) {
            $this->flash('error', 'Tipo de entrada no encontrado.');
            $this->redirect('/admin/tickets/types');
            return;
        }

        $eventId = $ticketType['event_id'];
        $data = [
            'name' => Sanitizer::string($this->getPost('name')),
            'description' => $this->getPost('description'),
            'price' => (float) $this->getPost('price', 0),
            'max_tickets' => $this->getPost('max_tickets') ?: 100,
            'sale_start' => $this->getPost('sale_start') ?: null,
            'sale_end' => $this->getPost('sale_end') ?: null,
            'active' => $this->getPost('active') ? 1 : 0,
            'requires_approval' => $this->getPost('requires_approval') ? 1 : 0,
        ];

        try {
            $this->ticketTypeModel->update((int) $id, $data);
            $this->flash('success', 'Tipo de entrada actualizado.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error: ' . $e->getMessage());
        }

        $this->redirect('/admin/tickets/types?event_id=' . $eventId);
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
     * Serve scanner PWA manifest
     */
    public function scannerManifest(): void
    {
        header('Content-Type: application/manifest+json');
        echo file_get_contents(PUBLIC_PATH . '/scanner-manifest.json');
        exit;
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
     * Download ticket as image/PDF
     */
    public function download(string $id): void
    {
        $this->requireAuth();

        $ticket = $this->ticketModel->find((int) $id);

        if (!$ticket) {
            $this->flash('error', 'Entrada no encontrada.');
            $this->redirect('/admin/tickets');
            return;
        }

        $event = $this->eventModel->find($ticket['event_id']);
        $code = $ticket['code'] ?? $ticket['ticket_code'];

        // Redirect to public ticket page which has download functionality
        $this->redirect("/eventos/{$event['slug']}/ticket/{$code}");
    }

    /**
     * Validate ticket code and perform check-in (AJAX for scanner)
     */
    public function validateCode(): void
    {
        $this->requireAuth();

        // Get JSON body if content type is JSON
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $rawInput = file_get_contents('php://input');

        if (strpos($contentType, 'application/json') !== false) {
            $json = json_decode($rawInput, true);
            if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
                $this->jsonError('Error al parsear JSON: ' . json_last_error_msg());
                return;
            }
            // Handle code as string or int
            $rawCode = $json['code'] ?? '';
            $code = trim((string) $rawCode);
            $eventId = (int) ($json['event_id'] ?? 0);
        } else {
            $rawCode = $this->getPost('code');
            $code = trim((string) ($rawCode ?? ''));
            $eventId = (int) $this->getPost('event_id');
        }

        if (empty($code)) {
            $this->jsonError('Código no proporcionado.');
            return;
        }

        $ticket = $this->ticketModel->findByCode($code);

        if (!$ticket) {
            $this->jsonError('Entrada no encontrada para código: ' . $code);
            return;
        }

        // Helper to get attendee name from ticket
        $getAttendeeName = function($t) {
            if (!empty($t['attendee_name'])) {
                return $t['attendee_name'];
            }
            $firstName = $t['attendee_first_name'] ?? '';
            $lastName = $t['attendee_last_name'] ?? '';
            return trim($firstName . ' ' . $lastName) ?: 'Sin nombre';
        };

        // Check if ticket is for the selected event
        if ($eventId && $ticket['event_id'] != $eventId) {
            $event = $this->eventModel->find($ticket['event_id']);
            $this->jsonError('Este ticket es para otro evento: ' . ($event['name'] ?? 'Desconocido'), [
                'valid' => false,
                'ticket' => [
                    'name' => $getAttendeeName($ticket),
                    'email' => $ticket['attendee_email'],
                ]
            ]);
            return;
        }

        $event = $this->eventModel->find($ticket['event_id']);
        $ticketData = [
            'id' => $ticket['id'],
            'code' => $ticket['ticket_code'] ?? $ticket['code'],
            'name' => $getAttendeeName($ticket),
            'email' => $ticket['attendee_email'],
            'company' => $ticket['attendee_company_name'] ?? $ticket['attendee_company'] ?? '',
            'status' => $ticket['status'],
            'event_name' => $event['name'] ?? 'N/A',
        ];

        // Check ticket status and perform check-in
        $status = $ticket['status'];

        if ($status === 'used' || $status === 'checked_in') {
            $checkedAt = $ticket['used_at'] ?? $ticket['checked_in_at'] ?? '';
            $this->json([
                'success' => false,
                'message' => 'Ya ha hecho check-in' . ($checkedAt ? ' a las ' . date('H:i', strtotime($checkedAt)) : ''),
                'already_checked_in' => true,
                'ticket' => $ticketData,
            ]);
            return;
        }

        if ($status === 'cancelled') {
            $this->json([
                'success' => false,
                'message' => 'Ticket cancelado',
                'ticket' => $ticketData,
            ]);
            return;
        }

        if ($status === 'pending') {
            $this->json([
                'success' => false,
                'message' => 'Ticket pendiente de aprobación',
                'ticket' => $ticketData,
            ]);
            return;
        }

        if ($status !== 'confirmed') {
            $this->json([
                'success' => false,
                'message' => 'Estado del ticket: ' . $status,
                'ticket' => $ticketData,
            ]);
            return;
        }

        // Perform check-in
        try {
            $this->ticketModel->checkIn((int) $ticket['id']);
            $ticketData['status'] = 'used';

            $this->jsonSuccess([
                'message' => '✓ Check-in realizado correctamente',
                'ticket' => $ticketData,
            ]);
        } catch (\Exception $e) {
            $this->jsonError('Error al hacer check-in: ' . $e->getMessage(), [
                'ticket' => $ticketData,
            ]);
        }
    }

    /**
     * Assign ticket attendee to a Company or Sponsor (SaaS)
     */
    public function assignToEntity(string $id): void
    {
        $this->requireAuth();

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') === false) {
            $this->jsonError('Content-Type debe ser application/json');
            return;
        }

        $json = json_decode(file_get_contents('php://input'), true);
        if (!$json) {
            $this->jsonError('JSON inválido');
            return;
        }

        $ticket = $this->ticketModel->find((int) $id);
        if (!$ticket) {
            $this->jsonError('Ticket no encontrado.');
            return;
        }

        $entityType = $json['entity_type'] ?? ''; // 'company' or 'sponsor'
        $entityId = (int) ($json['entity_id'] ?? 0);
        $sendWelcomeEmail = (bool) ($json['send_welcome_email'] ?? true);

        if (!in_array($entityType, ['company', 'sponsor'])) {
            $this->jsonError('Tipo de entidad no válido. Debe ser "company" o "sponsor".');
            return;
        }

        if (!$entityId) {
            $this->jsonError('ID de entidad no proporcionado.');
            return;
        }

        try {
            $entity = null;
            $updateData = [];

            if ($entityType === 'company') {
                $entity = $this->companyModel->find($entityId);
                if (!$entity) {
                    $this->jsonError('Empresa no encontrada.');
                    return;
                }
                $updateData = [
                    'assigned_company_id' => $entityId,
                    'assigned_sponsor_id' => null,
                    'assigned_at' => date('Y-m-d H:i:s'),
                ];

                // Add contact to company if not exists (using email as pivot)
                $this->addContactToCompany($entity, $ticket);

                // Register company in event if not registered
                $this->companyModel->registerForEvent($entityId, $ticket['event_id']);

            } else {
                $entity = $this->sponsorModel->find($entityId);
                if (!$entity) {
                    $this->jsonError('SaaS/Sponsor no encontrado.');
                    return;
                }
                $updateData = [
                    'assigned_sponsor_id' => $entityId,
                    'assigned_company_id' => null,
                    'assigned_at' => date('Y-m-d H:i:s'),
                ];

                // Add contact to sponsor if not exists
                $this->addContactToSponsor($entity, $ticket);

                // Register sponsor in event if not registered
                if (!$this->sponsorModel->participatesInEvent($entityId, $ticket['event_id'])) {
                    $this->registerSponsorInEvent($entityId, $ticket['event_id']);
                }
            }

            // Update ticket with assignment
            $this->ticketModel->update((int) $id, $updateData);

            // Send welcome email with portal access
            if ($sendWelcomeEmail) {
                $event = $this->eventModel->find($ticket['event_id']);
                $emailService = new EmailService();
                $emailService->sendPortalWelcomeEmail($entityType, $entity, $ticket, $event);
            }

            $this->jsonSuccess([
                'message' => 'Usuario asignado correctamente a ' . htmlspecialchars($entity['name']),
                'entity_type' => $entityType,
                'entity_name' => $entity['name'],
            ]);

        } catch (\Exception $e) {
            $this->jsonError('Error al asignar: ' . $e->getMessage());
        }
    }

    /**
     * Create a new Company from ticket data and assign
     */
    public function createCompanyFromTicket(string $id): void
    {
        $this->requireAuth();

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') === false) {
            $this->jsonError('Content-Type debe ser application/json');
            return;
        }

        $json = json_decode(file_get_contents('php://input'), true);

        $ticket = $this->ticketModel->find((int) $id);
        if (!$ticket) {
            $this->jsonError('Ticket no encontrado.');
            return;
        }

        try {
            // Create company with ticket data
            $companyName = $json['name'] ?? $ticket['attendee_company_name'] ?? $ticket['attendee_company'] ?? '';
            if (empty($companyName)) {
                $this->jsonError('El nombre de la empresa es obligatorio.');
                return;
            }

            $slug = Sanitizer::slug($companyName);
            $code = Company::generateUniqueCode();

            $contactName = $ticket['attendee_first_name'] . ' ' . $ticket['attendee_last_name'];
            if (empty(trim($contactName))) {
                $contactName = $ticket['attendee_name'] ?? '';
            }

            $companyId = $this->companyModel->create([
                'name' => $companyName,
                'slug' => $slug,
                'code' => $code,
                'contact_name' => trim($contactName),
                'contact_email' => $ticket['attendee_email'],
                'contact_phone' => $ticket['attendee_phone'] ?? null,
                'contact_position' => $ticket['attendee_job_title'] ?? $ticket['attendee_position'] ?? null,
                'sector' => $json['sector'] ?? null,
                'employees' => $json['employees'] ?? $ticket['attendee_company_size'] ?? null,
                'website' => $json['website'] ?? null,
                'active' => 1,
            ]);

            // Register company in event
            $this->companyModel->registerForEvent($companyId, $ticket['event_id']);

            // Assign ticket to company
            $this->ticketModel->update((int) $id, [
                'assigned_company_id' => $companyId,
                'assigned_sponsor_id' => null,
                'assigned_at' => date('Y-m-d H:i:s'),
            ]);

            // Send welcome email
            $company = $this->companyModel->find($companyId);
            $event = $this->eventModel->find($ticket['event_id']);
            $emailService = new EmailService();
            $emailService->sendPortalWelcomeEmail('company', $company, $ticket, $event);

            $this->jsonSuccess([
                'message' => 'Empresa "' . htmlspecialchars($companyName) . '" creada y asignada correctamente.',
                'company_id' => $companyId,
                'company_code' => $code,
            ]);

        } catch (\Exception $e) {
            $this->jsonError('Error al crear empresa: ' . $e->getMessage());
        }
    }

    /**
     * Create a new Sponsor (SaaS) from ticket data and assign
     */
    public function createSponsorFromTicket(string $id): void
    {
        $this->requireAuth();

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') === false) {
            $this->jsonError('Content-Type debe ser application/json');
            return;
        }

        $json = json_decode(file_get_contents('php://input'), true);

        $ticket = $this->ticketModel->find((int) $id);
        if (!$ticket) {
            $this->jsonError('Ticket no encontrado.');
            return;
        }

        try {
            // Create sponsor with ticket data
            $sponsorName = $json['name'] ?? $ticket['attendee_company_name'] ?? $ticket['attendee_company'] ?? '';
            if (empty($sponsorName)) {
                $this->jsonError('El nombre del SaaS es obligatorio.');
                return;
            }

            $slug = Sanitizer::slug($sponsorName);
            $code = Sponsor::generateUniqueCode();

            $contactName = $ticket['attendee_first_name'] . ' ' . $ticket['attendee_last_name'];
            if (empty(trim($contactName))) {
                $contactName = $ticket['attendee_name'] ?? '';
            }

            $sponsorId = $this->sponsorModel->create([
                'name' => $sponsorName,
                'slug' => $slug,
                'code' => $code,
                'tagline' => $json['tagline'] ?? null,
                'description' => $json['description'] ?? null,
                'website' => $json['website'] ?? null,
                'contact_name' => trim($contactName),
                'contact_email' => $ticket['attendee_email'],
                'contact_phone' => $ticket['attendee_phone'] ?? null,
                'active' => 1,
                'is_hidden' => 0,
            ]);

            // Register sponsor in event
            $this->registerSponsorInEvent($sponsorId, $ticket['event_id'], $json['level'] ?? 'bronze');

            // Assign ticket to sponsor
            $this->ticketModel->update((int) $id, [
                'assigned_sponsor_id' => $sponsorId,
                'assigned_company_id' => null,
                'assigned_at' => date('Y-m-d H:i:s'),
            ]);

            // Send welcome email
            $sponsor = $this->sponsorModel->find($sponsorId);
            $event = $this->eventModel->find($ticket['event_id']);
            $emailService = new EmailService();
            $emailService->sendPortalWelcomeEmail('sponsor', $sponsor, $ticket, $event);

            $this->jsonSuccess([
                'message' => 'SaaS "' . htmlspecialchars($sponsorName) . '" creado y asignado correctamente.',
                'sponsor_id' => $sponsorId,
                'sponsor_code' => $code,
            ]);

        } catch (\Exception $e) {
            $this->jsonError('Error al crear SaaS: ' . $e->getMessage());
        }
    }

    /**
     * Remove assignment from ticket
     */
    public function removeAssignment(string $id): void
    {
        $this->requireAuth();

        $ticket = $this->ticketModel->find((int) $id);
        if (!$ticket) {
            $this->jsonError('Ticket no encontrado.');
            return;
        }

        try {
            $this->ticketModel->update((int) $id, [
                'assigned_company_id' => null,
                'assigned_sponsor_id' => null,
                'assigned_at' => null,
            ]);

            $this->jsonSuccess(['message' => 'Asignación eliminada correctamente.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al eliminar asignación: ' . $e->getMessage());
        }
    }

    /**
     * Search sponsors for assignment (AJAX)
     */
    public function searchSponsors(): void
    {
        $this->requireAuth();

        $query = $this->getQuery('q', '');
        $eventId = (int) $this->getQuery('event_id', 0);

        if (strlen($query) < 2) {
            $this->json(['results' => []]);
            return;
        }

        $result = $this->sponsorModel->searchByName($query, 1, 20, ['active' => 1]);

        $sponsors = array_map(function($s) {
            return [
                'id' => $s['id'],
                'name' => $s['name'],
                'contact_email' => $s['contact_email'],
                'logo_url' => $s['logo_url'] ?? null,
            ];
        }, $result['data']);

        $this->json(['results' => $sponsors]);
    }

    /**
     * Search companies for assignment (AJAX)
     */
    public function searchCompanies(): void
    {
        $this->requireAuth();

        $query = $this->getQuery('q', '');
        $eventId = (int) $this->getQuery('event_id', 0);

        if (strlen($query) < 2) {
            $this->json(['results' => []]);
            return;
        }

        $result = $this->companyModel->searchByName($query, 1, 20, ['active' => 1]);

        $companies = array_map(function($c) {
            return [
                'id' => $c['id'],
                'name' => $c['name'],
                'contact_email' => $c['contact_email'],
                'logo_url' => $c['logo_url'] ?? null,
            ];
        }, $result['data']);

        $this->json(['results' => $companies]);
    }

    /**
     * Resend confirmation email
     */
    public function resendEmail(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $ticket = $this->ticketModel->find((int) $id);
        if (!$ticket) {
            $this->jsonError('Ticket no encontrado.');
            return;
        }

        try {
            $event = $this->eventModel->find($ticket['event_id']);
            $emailService = new EmailService();
            $emailService->sendTicketConfirmation($ticket, $event);

            $this->jsonSuccess(['message' => 'Email reenviado correctamente.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al enviar email: ' . $e->getMessage());
        }
    }

    /**
     * Add contact to company if not exists (by email)
     */
    private function addContactToCompany(array $company, array $ticket): void
    {
        $ticketEmail = strtolower(trim($ticket['attendee_email']));

        // Check if email already exists in company_contacts table
        $existingContacts = $this->companyContactModel->getByCompany((int) $company['id']);
        foreach ($existingContacts as $contact) {
            if (strtolower(trim($contact['email'])) === $ticketEmail) {
                return; // Already exists in contacts table
            }
        }

        // Create contact record in company_contacts table
        $contactName = trim($ticket['attendee_name'] ?? '');
        $this->companyContactModel->create([
            'company_id' => (int) $company['id'],
            'name' => $contactName ?: 'Sin nombre',
            'email' => $ticketEmail,
            'position' => trim($ticket['attendee_position'] ?? ''),
            'phone' => trim($ticket['attendee_phone'] ?? ''),
            'is_primary' => empty($existingContacts) ? 1 : 0, // Primary if first contact
            'notes' => 'Añadido desde ticket: ' . ($ticket['code'] ?? ''),
        ]);

        // Also update legacy contact_email field for backward compatibility
        $emails = $this->companyModel->getEmailsArray($company);
        $emailExists = false;
        foreach ($emails as $email) {
            if (strtolower(trim($email)) === $ticketEmail) {
                $emailExists = true;
                break;
            }
        }
        if (!$emailExists) {
            $newEmails = $company['contact_email'] ? $company['contact_email'] . ', ' . $ticketEmail : $ticketEmail;
            $this->companyModel->update($company['id'], ['contact_email' => $newEmails]);
        }
    }

    /**
     * Add contact to sponsor if not exists (by email)
     */
    private function addContactToSponsor(array $sponsor, array $ticket): void
    {
        $ticketEmail = strtolower(trim($ticket['attendee_email']));

        // Check if email already exists in sponsor_contacts table
        $existingContacts = $this->sponsorContactModel->getBySponsor((int) $sponsor['id']);
        foreach ($existingContacts as $contact) {
            if (strtolower(trim($contact['email'])) === $ticketEmail) {
                return; // Already exists in contacts table
            }
        }

        // Create contact record in sponsor_contacts table
        $contactName = trim($ticket['attendee_name'] ?? '');
        $this->sponsorContactModel->create([
            'sponsor_id' => (int) $sponsor['id'],
            'name' => $contactName ?: 'Sin nombre',
            'email' => $ticketEmail,
            'position' => trim($ticket['attendee_position'] ?? ''),
            'phone' => trim($ticket['attendee_phone'] ?? ''),
            'is_primary' => empty($existingContacts) ? 1 : 0, // Primary if first contact
            'notes' => 'Añadido desde ticket: ' . ($ticket['code'] ?? ''),
        ]);

        // Also update legacy contact_email field for backward compatibility
        $emails = $this->sponsorModel->getEmailsArray($sponsor);
        $emailExists = false;
        foreach ($emails as $email) {
            if (strtolower(trim($email)) === $ticketEmail) {
                $emailExists = true;
                break;
            }
        }
        if (!$emailExists) {
            $newEmails = $sponsor['contact_email'] ? $sponsor['contact_email'] . ', ' . $ticketEmail : $ticketEmail;
            $this->sponsorModel->update($sponsor['id'], ['contact_email' => $newEmails]);
        }
    }

    /**
     * Register sponsor in event
     */
    private function registerSponsorInEvent(int $sponsorId, int $eventId, string $level = 'bronze'): void
    {
        $db = \App\Core\Database::getInstance();
        $sql = "INSERT IGNORE INTO event_sponsors (event_id, sponsor_id, level, display_order)
                VALUES (?, ?, ?, (SELECT COALESCE(MAX(display_order), 0) + 1 FROM event_sponsors es2 WHERE es2.event_id = ?))";
        $db->query($sql, [$eventId, $sponsorId, $level, $eventId]);
    }
}
