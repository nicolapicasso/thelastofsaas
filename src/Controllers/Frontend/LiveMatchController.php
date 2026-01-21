<?php

declare(strict_types=1);

namespace App\Controllers\Frontend;

use App\Core\Controller;
use App\Models\Event;
use App\Models\Sponsor;
use App\Models\Company;
use App\Models\MeetingBlock;
use App\Models\MeetingSlot;
use App\Models\MeetingAssignment;
use App\Models\TlosSetting;

/**
 * Live Match Controller
 * TLOS - The Last of SaaS
 *
 * Handles live event matching via QR scanning
 */
class LiveMatchController extends Controller
{
    private Event $eventModel;
    private Sponsor $sponsorModel;
    private Company $companyModel;
    private MeetingBlock $blockModel;
    private MeetingSlot $slotModel;
    private MeetingAssignment $assignmentModel;
    private TlosSetting $settingsModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
        $this->sponsorModel = new Sponsor();
        $this->companyModel = new Company();
        $this->blockModel = new MeetingBlock();
        $this->slotModel = new MeetingSlot();
        $this->assignmentModel = new MeetingAssignment();
        $this->settingsModel = new TlosSetting();
    }

    /**
     * Main scanner page
     */
    public function index(string $slug): void
    {
        $event = $this->eventModel->findBy('slug', $slug);

        if (!$event || !$event['active']) {
            $this->render('errors/404', ['message' => 'Evento no encontrado']);
            return;
        }

        $this->render('live-match/index', [
            'event' => $event,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Serve PWA manifest for live match scanner
     */
    public function manifest(string $slug): void
    {
        $event = $this->eventModel->findBy('slug', $slug);
        $eventName = $event ? $event['name'] : 'TLOS';

        header('Content-Type: application/manifest+json');
        echo json_encode([
            'name' => 'Match Scanner - ' . $eventName,
            'short_name' => 'Match',
            'description' => 'Escaner de matching en vivo para ' . $eventName,
            'start_url' => '/eventos/' . $slug . '/match',
            'scope' => '/eventos/' . $slug . '/match',
            'display' => 'standalone',
            'orientation' => 'portrait',
            'background_color' => '#1A1A1A',
            'theme_color' => '#215A6B',
            'icons' => [
                [
                    'src' => '/assets/images/scanner-icon-192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => '/assets/images/scanner-icon-512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
            'categories' => ['business', 'utilities'],
            'lang' => 'es',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * API: Identify sponsor by QR code
     */
    public function identifySponsor(string $slug): void
    {
        $event = $this->eventModel->findBy('slug', $slug);

        if (!$event) {
            $this->jsonError('Evento no encontrado.', 404);
            return;
        }

        $code = trim($this->getPost('code', ''));

        if (empty($code)) {
            $this->jsonError('Codigo QR vacio.');
            return;
        }

        // Find sponsor by code
        $sponsor = $this->sponsorModel->findByCode($code);

        if (!$sponsor) {
            $this->jsonError('Codigo no reconocido. Asegurate de escanear un QR de SaaS valido.');
            return;
        }

        // Check if sponsor is active and part of this event
        if (!$sponsor['active']) {
            $this->jsonError('Este SaaS no esta activo.');
            return;
        }

        // Check sponsor is registered for this event
        $eventSponsors = $this->sponsorModel->getByEvent($event['id']);
        $isInEvent = false;
        $sponsorTier = 'bronze';

        foreach ($eventSponsors as $es) {
            if ($es['id'] == $sponsor['id']) {
                $isInEvent = true;
                $sponsorTier = $es['priority_level'] ?? 'bronze';
                break;
            }
        }

        if (!$isInEvent) {
            $this->jsonError('Este SaaS no esta registrado en este evento.');
            return;
        }

        // Get meeting stats for this sponsor in this event
        $meetings = $this->sponsorModel->getMeetings($sponsor['id'], $event['id']);
        $meetingCount = count($meetings);

        $this->jsonSuccess([
            'sponsor' => [
                'id' => $sponsor['id'],
                'code' => $sponsor['code'],
                'name' => $sponsor['name'],
                'logo_url' => $sponsor['logo_url'] ?? null,
                'tier' => $sponsorTier,
            ],
            'stats' => [
                'meetings_scheduled' => $meetingCount,
            ],
        ]);
    }

    /**
     * API: Scan company and create match
     */
    public function scanCompany(string $slug): void
    {
        $event = $this->eventModel->findBy('slug', $slug);

        if (!$event) {
            $this->jsonError('Evento no encontrado.', 404);
            return;
        }

        $sponsorCode = trim($this->getPost('sponsor_code', ''));
        $companyCode = trim($this->getPost('company_code', ''));

        if (empty($sponsorCode) || empty($companyCode)) {
            $this->jsonError('Faltan datos requeridos.');
            return;
        }

        // Validate sponsor
        $sponsor = $this->sponsorModel->findByCode($sponsorCode);
        if (!$sponsor) {
            $this->jsonError('SaaS no valido. Vuelve a identificarte.');
            return;
        }

        // Find company by code
        $company = $this->companyModel->findByCode($companyCode);

        if (!$company) {
            $this->jsonError('Codigo no reconocido. Asegurate de escanear un QR de empresa valido.');
            return;
        }

        if (!$company['active']) {
            $this->jsonError('Esta empresa no esta activa.');
            return;
        }

        // Check company is registered for this event
        $eventCompanies = $this->companyModel->getByEvent($event['id']);
        $isInEvent = false;

        foreach ($eventCompanies as $ec) {
            if ($ec['id'] == $company['id']) {
                $isInEvent = true;
                break;
            }
        }

        if (!$isInEvent) {
            $this->jsonError('Esta empresa no esta registrada en este evento.');
            return;
        }

        // Check if they already have a meeting
        if ($this->assignmentModel->hasExistingMeeting($event['id'], $sponsor['id'], $company['id'])) {
            $this->jsonError('Ya tienes una reunion programada con esta empresa.');
            return;
        }

        // Create mutual selection if not exists (auto-match)
        $this->sponsorModel->selectCompany($sponsor['id'], $company['id'], $event['id']);
        $this->companyModel->selectSponsor($company['id'], $sponsor['id'], $event['id']);

        // Get available slots for this match
        $availableSlots = $this->slotModel->getAvailableForMatch(
            $event['id'],
            $sponsor['id'],
            $company['id']
        );

        // Group slots by block
        $groupedSlots = [];
        foreach ($availableSlots as $slot) {
            $blockKey = $slot['block_id'];
            if (!isset($groupedSlots[$blockKey])) {
                $groupedSlots[$blockKey] = [
                    'block_id' => $slot['block_id'],
                    'block_name' => $slot['block_name'],
                    'event_date' => $slot['event_date'],
                    'location' => $slot['location'] ?? '',
                    'slots' => [],
                ];
            }
            $groupedSlots[$blockKey]['slots'][] = [
                'id' => $slot['id'],
                'time' => substr($slot['slot_time'], 0, 5),
                'room' => $slot['room_name'] ?? 'Mesa ' . $slot['room_number'],
                'duration' => $slot['slot_duration'] ?? 15,
            ];
        }

        $this->jsonSuccess([
            'match' => [
                'sponsor_id' => $sponsor['id'],
                'sponsor_name' => $sponsor['name'],
                'company_id' => $company['id'],
                'company_name' => $company['name'],
                'company_logo' => $company['logo_url'] ?? null,
            ],
            'available_slots' => array_values($groupedSlots),
            'total_available' => count($availableSlots),
        ]);
    }

    /**
     * API: Select a slot for the match
     */
    public function selectSlot(string $slug): void
    {
        $event = $this->eventModel->findBy('slug', $slug);

        if (!$event) {
            $this->jsonError('Evento no encontrado.', 404);
            return;
        }

        $sponsorCode = trim($this->getPost('sponsor_code', ''));
        $companyId = (int) $this->getPost('company_id');
        $slotId = (int) $this->getPost('slot_id');

        if (empty($sponsorCode) || !$companyId || !$slotId) {
            $this->jsonError('Faltan datos requeridos.');
            return;
        }

        // Validate sponsor
        $sponsor = $this->sponsorModel->findByCode($sponsorCode);
        if (!$sponsor) {
            $this->jsonError('SaaS no valido. Vuelve a identificarte.');
            return;
        }

        // Validate company
        $company = $this->companyModel->find($companyId);
        if (!$company) {
            $this->jsonError('Empresa no valida.');
            return;
        }

        // Check slot is still available
        if (!$this->slotModel->isAvailable($slotId)) {
            $this->jsonError('Este slot ya no esta disponible. Por favor, selecciona otro.');
            return;
        }

        // Double-check they don't already have a meeting
        if ($this->assignmentModel->hasExistingMeeting($event['id'], $sponsor['id'], $company['id'])) {
            $this->jsonError('Ya tienes una reunion programada con esta empresa.');
            return;
        }

        // Create the assignment
        try {
            $assignmentId = $this->assignmentModel->assign(
                $slotId,
                $event['id'],
                $sponsor['id'],
                $company['id'],
                'live_matching', // Mark as live matching
                'Reunion agendada via matching en vivo'
            );

            if (!$assignmentId) {
                $this->jsonError('No se pudo agendar la reunion. El slot puede no estar disponible.');
                return;
            }

            // Get slot details for confirmation
            $slot = $this->slotModel->find($slotId);
            $block = $this->blockModel->find($slot['block_id']);

            $this->jsonSuccess([
                'assignment_id' => $assignmentId,
                'message' => 'Reunion agendada correctamente!',
                'meeting' => [
                    'date' => $block['event_date'],
                    'time' => substr($slot['slot_time'], 0, 5),
                    'room' => $slot['room_name'] ?? 'Mesa ' . $slot['room_number'],
                    'duration' => $block['slot_duration'] ?? 15,
                    'location' => $block['location'] ?? '',
                    'sponsor_name' => $sponsor['name'],
                    'company_name' => $company['name'],
                ],
            ]);
        } catch (\Exception $e) {
            $this->jsonError('Error al agendar: ' . $e->getMessage());
        }
    }

    /**
     * API: Get sponsor's scheduled meetings
     */
    public function getMeetings(string $slug): void
    {
        $event = $this->eventModel->findBy('slug', $slug);

        if (!$event) {
            $this->jsonError('Evento no encontrado.', 404);
            return;
        }

        $sponsorCode = trim($this->getQuery('sponsor_code', ''));

        if (empty($sponsorCode)) {
            $this->jsonError('Falta codigo de sponsor.');
            return;
        }

        $sponsor = $this->sponsorModel->findByCode($sponsorCode);
        if (!$sponsor) {
            $this->jsonError('SaaS no valido.');
            return;
        }

        $meetings = $this->sponsorModel->getMeetings($sponsor['id'], $event['id']);

        // Format meetings for display
        $formattedMeetings = array_map(function ($m) {
            return [
                'id' => $m['id'],
                'company_name' => $m['company_name'],
                'date' => $m['event_date'],
                'time' => substr($m['slot_time'], 0, 5),
                'room' => $m['room_name'] ?? 'Mesa ' . $m['room_number'],
                'status' => $m['status'],
                'assigned_by' => $m['assigned_by'] ?? 'admin',
            ];
        }, $meetings);

        $this->jsonSuccess([
            'meetings' => $formattedMeetings,
            'count' => count($meetings),
        ]);
    }
}
