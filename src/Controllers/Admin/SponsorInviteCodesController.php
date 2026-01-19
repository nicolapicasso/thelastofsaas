<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\SponsorInviteCode;
use App\Models\Sponsor;
use App\Models\Event;
use App\Models\TicketType;
use App\Helpers\Sanitizer;

/**
 * Sponsor Invite Codes Controller
 * TLOS - The Last of SaaS
 */
class SponsorInviteCodesController extends Controller
{
    private SponsorInviteCode $codeModel;
    private Sponsor $sponsorModel;
    private Event $eventModel;
    private TicketType $ticketTypeModel;

    public function __construct()
    {
        parent::__construct();
        $this->codeModel = new SponsorInviteCode();
        $this->sponsorModel = new Sponsor();
        $this->eventModel = new Event();
        $this->ticketTypeModel = new TicketType();
    }

    /**
     * List all invite codes
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) ($this->getQuery('page', 1));
        $eventId = $this->getQuery('event_id');
        $sponsorId = $this->getQuery('sponsor_id');
        $active = $this->getQuery('active');

        $conditions = [];
        if ($eventId) {
            $conditions['event_id'] = (int) $eventId;
        }
        if ($sponsorId) {
            $conditions['sponsor_id'] = (int) $sponsorId;
        }
        if ($active !== null && $active !== '') {
            $conditions['active'] = (int) $active;
        }

        $result = $this->codeModel->paginate($page, 20, $conditions, ['created_at' => 'DESC']);

        // Get events and sponsors for filters
        $events = $this->eventModel->all(['start_date' => 'DESC']);
        $sponsors = $this->sponsorModel->all(['name' => 'ASC']);

        // Enrich data with sponsor and event names
        $codes = [];
        foreach ($result['data'] as $code) {
            $code['sponsor'] = $this->sponsorModel->find($code['sponsor_id']);
            $code['event'] = $this->eventModel->find($code['event_id']);
            if ($code['ticket_type_id']) {
                $code['ticket_type'] = $this->ticketTypeModel->find($code['ticket_type_id']);
            }
            $code['stats'] = $this->codeModel->getUsageStats($code['id']);
            $codes[] = $code;
        }

        $this->renderAdmin('sponsor-invite-codes/index', [
            'title' => 'Codigos de Invitacion',
            'codes' => $codes,
            'pagination' => $result['pagination'],
            'events' => $events,
            'sponsors' => $sponsors,
            'currentEventId' => $eventId,
            'currentSponsorId' => $sponsorId,
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

        $events = $this->eventModel->all(['start_date' => 'DESC']);
        $sponsors = $this->sponsorModel->where(['active' => 1], ['name' => 'ASC']);

        // Get ticket types for each event
        $ticketTypesByEvent = [];
        foreach ($events as $event) {
            $ticketTypesByEvent[$event['id']] = $this->ticketTypeModel->getByEvent((int) $event['id']);
        }

        $this->renderAdmin('sponsor-invite-codes/form', [
            'title' => 'Nuevo Codigo de Invitacion',
            'code' => null,
            'events' => $events,
            'sponsors' => $sponsors,
            'ticketTypesByEvent' => $ticketTypesByEvent,
            'discountTypes' => SponsorInviteCode::getDiscountTypes(),
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new invite code
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/sponsor-invite-codes/create');
        }

        $data = $this->validateCodeData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/sponsor-invite-codes/create');
        }

        // Generate unique code if not provided
        if (empty($data['code'])) {
            $sponsor = $this->sponsorModel->find($data['sponsor_id']);
            $prefix = strtoupper(substr($sponsor['name'] ?? 'CODE', 0, 3)) . '-';
            $data['code'] = $this->codeModel->generateUniqueCode($data['event_id'], $prefix);
        }

        try {
            $codeId = $this->codeModel->create($data);
            $this->flash('success', 'Codigo de invitacion creado: ' . $data['code']);
            $this->redirect('/admin/sponsor-invite-codes/' . $codeId . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el codigo: ' . $e->getMessage());
            $this->redirect('/admin/sponsor-invite-codes/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $code = $this->codeModel->find((int) $id);

        if (!$code) {
            $this->flash('error', 'Codigo no encontrado.');
            $this->redirect('/admin/sponsor-invite-codes');
        }

        $events = $this->eventModel->all(['start_date' => 'DESC']);
        $sponsors = $this->sponsorModel->where(['active' => 1], ['name' => 'ASC']);

        // Get ticket types for each event
        $ticketTypesByEvent = [];
        foreach ($events as $event) {
            $ticketTypesByEvent[$event['id']] = $this->ticketTypeModel->getByEvent((int) $event['id']);
        }

        // Get usage stats and tickets
        $stats = $this->codeModel->getUsageStats((int) $id);
        $tickets = $this->codeModel->getTickets((int) $id);

        $this->renderAdmin('sponsor-invite-codes/form', [
            'title' => 'Editar Codigo: ' . $code['code'],
            'code' => $code,
            'events' => $events,
            'sponsors' => $sponsors,
            'ticketTypesByEvent' => $ticketTypesByEvent,
            'discountTypes' => SponsorInviteCode::getDiscountTypes(),
            'stats' => $stats,
            'tickets' => $tickets,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update invite code
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/sponsor-invite-codes/' . $id . '/edit');
        }

        $code = $this->codeModel->find((int) $id);

        if (!$code) {
            $this->flash('error', 'Codigo no encontrado.');
            $this->redirect('/admin/sponsor-invite-codes');
        }

        $data = $this->validateCodeData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/sponsor-invite-codes/' . $id . '/edit');
        }

        // Keep original code if not changed
        if (empty($data['code'])) {
            $data['code'] = $code['code'];
        }

        try {
            $this->codeModel->update((int) $id, $data);
            $this->flash('success', 'Codigo actualizado correctamente.');
            $this->redirect('/admin/sponsor-invite-codes/' . $id . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar el codigo: ' . $e->getMessage());
            $this->redirect('/admin/sponsor-invite-codes/' . $id . '/edit');
        }
    }

    /**
     * Delete invite code
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/sponsor-invite-codes');
        }

        $code = $this->codeModel->find((int) $id);

        if (!$code) {
            $this->flash('error', 'Codigo no encontrado.');
            $this->redirect('/admin/sponsor-invite-codes');
        }

        // Check if code has been used
        $stats = $this->codeModel->getUsageStats((int) $id);
        if ($stats['total_tickets'] > 0) {
            $this->flash('error', 'No se puede eliminar un codigo que ya ha sido usado. Desactivelo en su lugar.');
            $this->redirect('/admin/sponsor-invite-codes');
            return;
        }

        try {
            $this->codeModel->delete((int) $id);
            $this->flash('success', 'Codigo eliminado correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar el codigo: ' . $e->getMessage());
        }

        $this->redirect('/admin/sponsor-invite-codes');
    }

    /**
     * Generate codes in bulk
     */
    public function bulkCreate(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $events = $this->eventModel->all(['start_date' => 'DESC']);
            $sponsors = $this->sponsorModel->where(['active' => 1], ['name' => 'ASC']);

            $this->renderAdmin('sponsor-invite-codes/bulk', [
                'title' => 'Crear Codigos en Lote',
                'events' => $events,
                'sponsors' => $sponsors,
                'csrf_token' => $this->generateCsrf(),
            ]);
            return;
        }

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesion expirada.');
            $this->redirect('/admin/sponsor-invite-codes/bulk');
        }

        $eventId = (int) $this->getPost('event_id');
        $sponsorId = (int) $this->getPost('sponsor_id');
        $quantity = (int) $this->getPost('quantity', 1);
        $maxUses = $this->getPost('max_uses') !== '' ? (int) $this->getPost('max_uses') : null;
        $prefix = Sanitizer::string($this->getPost('prefix'));

        if (!$eventId || !$sponsorId) {
            $this->flash('error', 'Debe seleccionar un evento y un sponsor.');
            $this->redirect('/admin/sponsor-invite-codes/bulk');
        }

        if ($quantity < 1 || $quantity > 100) {
            $this->flash('error', 'La cantidad debe estar entre 1 y 100.');
            $this->redirect('/admin/sponsor-invite-codes/bulk');
        }

        $sponsor = $this->sponsorModel->find($sponsorId);
        $codePrefix = $prefix ?: strtoupper(substr($sponsor['name'] ?? 'CODE', 0, 3)) . '-';

        $created = 0;
        $errors = [];

        for ($i = 0; $i < $quantity; $i++) {
            try {
                $code = $this->codeModel->generateUniqueCode($eventId, $codePrefix);
                $this->codeModel->create([
                    'event_id' => $eventId,
                    'sponsor_id' => $sponsorId,
                    'code' => $code,
                    'max_uses' => $maxUses,
                    'active' => 1,
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($created > 0) {
            $this->flash('success', "Se crearon {$created} codigos correctamente.");
        }
        if (!empty($errors)) {
            $this->flash('error', 'Algunos codigos no se pudieron crear: ' . implode(', ', array_slice($errors, 0, 3)));
        }

        $this->redirect('/admin/sponsor-invite-codes?sponsor_id=' . $sponsorId . '&event_id=' . $eventId);
    }

    /**
     * Toggle code active status (AJAX)
     */
    public function toggleActive(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesion expirada.');
            return;
        }

        $code = $this->codeModel->find((int) $id);

        if (!$code) {
            $this->jsonError('Codigo no encontrado.');
            return;
        }

        try {
            $newStatus = $code['active'] ? 0 : 1;
            $this->codeModel->update((int) $id, ['active' => $newStatus]);
            $this->jsonSuccess([
                'active' => $newStatus,
                'message' => $newStatus ? 'Codigo activado.' : 'Codigo desactivado.',
            ]);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Export codes to CSV
     */
    public function export(): void
    {
        $this->requireAuth();

        $eventId = $this->getQuery('event_id');
        $sponsorId = $this->getQuery('sponsor_id');

        $conditions = [];
        if ($eventId) {
            $conditions['event_id'] = (int) $eventId;
        }
        if ($sponsorId) {
            $conditions['sponsor_id'] = (int) $sponsorId;
        }

        $codes = $this->codeModel->where($conditions, ['created_at' => 'DESC']);

        $filename = 'invite_codes_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel

        // Headers
        fputcsv($output, [
            'Codigo',
            'Sponsor',
            'Evento',
            'Descripcion',
            'Max Usos',
            'Veces Usado',
            'Tipo Descuento',
            'Cantidad Descuento',
            'Valido Desde',
            'Valido Hasta',
            'Activo',
            'Creado'
        ], ';');

        foreach ($codes as $code) {
            $sponsor = $this->sponsorModel->find($code['sponsor_id']);
            $event = $this->eventModel->find($code['event_id']);

            fputcsv($output, [
                $code['code'],
                $sponsor['name'] ?? '',
                $event['name'] ?? '',
                $code['description'] ?? '',
                $code['max_uses'] ?? 'Ilimitado',
                $code['times_used'],
                SponsorInviteCode::DISCOUNT_TYPES[$code['discount_type']] ?? '',
                $code['discount_amount'],
                $code['valid_from'] ?? '',
                $code['valid_until'] ?? '',
                $code['active'] ? 'Si' : 'No',
                $code['created_at'],
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Validate code form data
     */
    private function validateCodeData(): array
    {
        $errors = [];

        $eventId = (int) $this->getPost('event_id');
        $sponsorId = (int) $this->getPost('sponsor_id');
        $code = strtoupper(Sanitizer::string($this->getPost('code')));
        $description = Sanitizer::string($this->getPost('description'));
        $maxUses = $this->getPost('max_uses') !== '' ? (int) $this->getPost('max_uses') : null;
        $ticketTypeId = $this->getPost('ticket_type_id') !== '' ? (int) $this->getPost('ticket_type_id') : null;
        $discountType = $this->getPost('discount_type', 'none');
        $discountAmount = (float) $this->getPost('discount_amount', 0);
        $validFrom = $this->getPost('valid_from') ?: null;
        $validUntil = $this->getPost('valid_until') ?: null;
        $active = Sanitizer::bool($this->getPost('active'));

        if (!$eventId) {
            $errors[] = 'Debe seleccionar un evento.';
        }

        if (!$sponsorId) {
            $errors[] = 'Debe seleccionar un sponsor.';
        }

        if (!in_array($discountType, array_keys(SponsorInviteCode::DISCOUNT_TYPES))) {
            $errors[] = 'Tipo de descuento no valido.';
        }

        if ($discountType !== 'none' && $discountAmount <= 0) {
            $errors[] = 'La cantidad de descuento debe ser mayor que 0.';
        }

        if ($discountType === 'percentage' && $discountAmount > 100) {
            $errors[] = 'El porcentaje de descuento no puede ser mayor a 100.';
        }

        if ($validFrom && $validUntil && $validFrom > $validUntil) {
            $errors[] = 'La fecha de inicio debe ser anterior a la fecha de fin.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'event_id' => $eventId,
            'sponsor_id' => $sponsorId,
            'code' => $code,
            'description' => $description ?: null,
            'max_uses' => $maxUses,
            'ticket_type_id' => $ticketTypeId,
            'discount_type' => $discountType,
            'discount_amount' => $discountAmount,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
            'active' => $active ? 1 : 0,
        ];
    }
}
