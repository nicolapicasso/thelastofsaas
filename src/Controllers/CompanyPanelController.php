<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Sponsor;
use App\Models\Company;
use App\Models\TlosSetting;
use App\Services\EmailService;

/**
 * Company Panel Controller
 * TLOS - The Last of SaaS
 *
 * Panel for companies to view and select sponsors
 */
class CompanyPanelController extends Controller
{
    private Event $eventModel;
    private Sponsor $sponsorModel;
    private Company $companyModel;
    private TlosSetting $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
        $this->sponsorModel = new Sponsor();
        $this->companyModel = new Company();
        $this->settingModel = new TlosSetting();
    }

    /**
     * Company login page
     */
    public function login(): void
    {
        // Check if already logged in
        if ($this->getCompanyFromSession()) {
            $this->redirect('/empresa/panel');
            return;
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido';
            } else {
                $code = strtoupper(trim($_POST['code'] ?? ''));

                if (!$code) {
                    $error = 'Introduce tu código de acceso';
                } else {
                    $company = $this->companyModel->findByCode($code);

                    if (!$company || !$company['active']) {
                        $error = 'Código de acceso no válido';
                    } else {
                        // Store company in session
                        $_SESSION['company_id'] = $company['id'];
                        $_SESSION['company_code'] = $company['code'];

                        $this->redirect('/empresa/panel');
                        return;
                    }
                }
            }
        }

        $this->render('company-panel/login', [
            'error' => $error,
            'meta_title' => 'Acceso Empresas - The Last of SaaS'
        ]);
    }

    /**
     * Company logout
     */
    public function logout(): void
    {
        unset($_SESSION['company_id'], $_SESSION['company_code']);
        $this->redirect('/empresa/login');
    }

    /**
     * Main company panel
     */
    public function panel(): void
    {
        $company = $this->requireCompanyAuth();
        if (!$company) return;

        // Get events where this company participates
        $events = $this->companyModel->getEvents($company['id']);

        // Get current/upcoming event
        $currentEvent = null;
        foreach ($events as $event) {
            if ($event['status'] === 'published') {
                $currentEvent = $event;
                break;
            }
        }

        // Get company's selections and matches for current event
        $selections = [];
        $matches = [];
        $stats = [];

        if ($currentEvent) {
            $selections = $this->companyModel->getSelections($company['id'], $currentEvent['id']);
            $matches = $this->companyModel->getMutualMatches($company['id'], $currentEvent['id']);

            // Get settings
            $maxSelections = (int)$this->settingModel->get('max_company_selections', 5);
            $stats = [
                'total_selections' => count($selections),
                'max_selections' => $maxSelections,
                'total_matches' => count($matches),
                'remaining_selections' => max(0, $maxSelections - count($selections))
            ];
        }

        // Get messages received from sponsors
        $messages = [];
        if ($currentEvent) {
            $messages = $this->companyModel->getReceivedMessages($company['id'], $currentEvent['id']);
        }

        $this->render('company-panel/panel', [
            'company' => $company,
            'events' => $events,
            'currentEvent' => $currentEvent,
            'selections' => $selections,
            'matches' => $matches,
            'messages' => $messages,
            'stats' => $stats,
            'meta_title' => 'Panel Empresa - ' . $company['name']
        ]);
    }

    /**
     * View sponsors list for selection
     */
    public function sponsors(string $eventId): void
    {
        $company = $this->requireCompanyAuth();
        if (!$company) return;

        $event = $this->eventModel->find($eventId);
        if (!$event || $event['status'] !== 'published') {
            $this->notFound();
            return;
        }

        // Verify company participates in this event
        if (!$this->companyModel->participatesInEvent($company['id'], $event['id'])) {
            $this->redirect('/empresa/panel');
            return;
        }

        // Get all sponsors for this event
        $sponsors = $this->eventModel->getSponsors($event['id']);

        // Get company's current selections
        $selections = $this->companyModel->getSelections($company['id'], $event['id']);
        $selectedIds = array_column($selections, 'sponsor_id');

        // Get mutual matches
        $matches = $this->companyModel->getMutualMatches($company['id'], $event['id']);
        $matchedIds = array_column($matches, 'sponsor_id');

        // Get sponsors who selected this company (showing interest)
        $interestedSponsors = $this->companyModel->getInterestedSponsors($company['id'], $event['id']);
        $interestedIds = array_column($interestedSponsors, 'sponsor_id');

        // Get settings
        $maxSelections = (int)$this->settingModel->get('max_company_selections', 5);

        // Filter options
        $filter = $_GET['filter'] ?? 'all';
        $level = $_GET['level'] ?? '';
        $search = $_GET['search'] ?? '';

        // Get unique levels for filter
        $levels = array_unique(array_column($sponsors, 'level'));

        // Apply filters
        $filteredSponsors = array_filter($sponsors, function($sponsor) use ($filter, $level, $search, $selectedIds, $matchedIds, $interestedIds) {
            // Text search
            if ($search) {
                $searchLower = strtolower($search);
                $matchesSearch = str_contains(strtolower($sponsor['name']), $searchLower) ||
                                str_contains(strtolower($sponsor['tagline'] ?? ''), $searchLower) ||
                                str_contains(strtolower($sponsor['description'] ?? ''), $searchLower);
                if (!$matchesSearch) return false;
            }

            // Level filter
            if ($level && $sponsor['level'] !== $level) {
                return false;
            }

            // Status filter
            switch ($filter) {
                case 'selected':
                    return in_array($sponsor['id'], $selectedIds);
                case 'matched':
                    return in_array($sponsor['id'], $matchedIds);
                case 'interested':
                    return in_array($sponsor['id'], $interestedIds);
                case 'available':
                    return !in_array($sponsor['id'], $selectedIds);
                default:
                    return true;
            }
        });

        $this->render('company-panel/sponsors', [
            'company' => $company,
            'event' => $event,
            'sponsors' => $filteredSponsors,
            'selectedIds' => $selectedIds,
            'matchedIds' => $matchedIds,
            'interestedIds' => $interestedIds,
            'maxSelections' => $maxSelections,
            'currentSelections' => count($selections),
            'levels' => $levels,
            'currentFilter' => $filter,
            'currentLevel' => $level,
            'currentSearch' => $search,
            'meta_title' => 'Sponsors - ' . $event['name']
        ]);
    }

    /**
     * View single sponsor detail
     */
    public function sponsorDetail(string $eventId, string $sponsorId): void
    {
        $company = $this->requireCompanyAuth();
        if (!$company) return;

        $event = $this->eventModel->find($eventId);
        $sponsor = $this->sponsorModel->find($sponsorId);

        if (!$event || !$sponsor) {
            $this->notFound();
            return;
        }

        // Check if already selected
        $isSelected = $this->companyModel->hasSelected($company['id'], $sponsor['id'], $event['id']);

        // Check if mutual match
        $isMatch = $this->companyModel->isMutualMatch($company['id'], $sponsor['id'], $event['id']);

        // Check if sponsor is interested in this company
        $isInterested = $this->sponsorModel->hasSelected($sponsor['id'], $company['id'], $event['id']);

        // Get message from sponsor if any
        $sponsorMessage = $this->companyModel->getMessageFromSponsor($company['id'], $sponsor['id'], $event['id']);

        $this->render('company-panel/sponsor-detail', [
            'company' => $company,
            'event' => $event,
            'sponsor' => $sponsor,
            'isSelected' => $isSelected,
            'isMatch' => $isMatch,
            'isInterested' => $isInterested,
            'sponsorMessage' => $sponsorMessage,
            'meta_title' => $sponsor['name'] . ' - Detalle'
        ]);
    }

    /**
     * Select a sponsor (AJAX)
     */
    public function selectSponsor(): void
    {
        $company = $this->requireCompanyAuth();
        if (!$company) {
            $this->jsonError('No autorizado', 401);
            return;
        }

        if (!$this->validateCsrf()) {
            $this->jsonError('Token de seguridad inválido', 403);
            return;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        $sponsorId = (int)($_POST['sponsor_id'] ?? 0);

        if (!$eventId || !$sponsorId) {
            $this->jsonError('Datos incompletos', 400);
            return;
        }

        // Verify event and sponsor exist
        $event = $this->eventModel->find($eventId);
        $sponsor = $this->sponsorModel->find($sponsorId);

        if (!$event || !$sponsor) {
            $this->jsonError('Evento o sponsor no encontrados', 404);
            return;
        }

        // Check if company participates in event
        if (!$this->companyModel->participatesInEvent($company['id'], $event['id'])) {
            $this->jsonError('No participas en este evento', 403);
            return;
        }

        // Check selection limit
        $maxSelections = (int)$this->settingModel->get('max_company_selections', 5);
        $currentSelections = count($this->companyModel->getSelections($company['id'], $event['id']));

        if ($currentSelections >= $maxSelections) {
            $this->jsonError("Has alcanzado el límite de {$maxSelections} selecciones", 400);
            return;
        }

        // Check if already selected
        if ($this->companyModel->hasSelected($company['id'], $sponsor['id'], $event['id'])) {
            $this->jsonError('Ya has seleccionado este sponsor', 400);
            return;
        }

        // Create selection
        $priority = $currentSelections + 1;
        $result = $this->companyModel->selectSponsor($company['id'], $sponsor['id'], $event['id'], $priority);

        if (!$result) {
            $this->jsonError('Error al guardar la selección', 500);
            return;
        }

        // Check if it's a mutual match
        $isMatch = $this->sponsorModel->hasSelected($sponsor['id'], $company['id'], $event['id']);

        // Send notification emails
        if ($this->settingModel->get('notify_sponsors', true)) {
            try {
                $emailService = new EmailService();
                $emailService->sendSelectionNotification('sponsor', $sponsor, $company, $event);

                if ($isMatch && $this->settingModel->get('auto_match_notification', true)) {
                    $emailService->sendMatchNotification($sponsor, $company, $event);
                }
            } catch (\Exception $e) {
                error_log('Email notification error: ' . $e->getMessage());
            }
        }

        $this->json([
            'success' => true,
            'is_match' => $isMatch,
            'message' => $isMatch ? '¡Match mutuo!' : 'Sponsor seleccionado'
        ]);
    }

    /**
     * Remove sponsor selection (AJAX)
     */
    public function unselectSponsor(): void
    {
        $company = $this->requireCompanyAuth();
        if (!$company) {
            $this->jsonError('No autorizado', 401);
            return;
        }

        if (!$this->validateCsrf()) {
            $this->jsonError('Token de seguridad inválido', 403);
            return;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        $sponsorId = (int)($_POST['sponsor_id'] ?? 0);

        if (!$eventId || !$sponsorId) {
            $this->jsonError('Datos incompletos', 400);
            return;
        }

        $result = $this->companyModel->unselectSponsor($company['id'], $sponsorId, $eventId);

        $this->json([
            'success' => $result,
            'message' => $result ? 'Selección eliminada' : 'Error al eliminar'
        ]);
    }

    /**
     * View matches and scheduled meetings
     */
    public function matches(string $eventId): void
    {
        $company = $this->requireCompanyAuth();
        if (!$company) return;

        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->notFound();
            return;
        }

        // Get mutual matches
        $matches = $this->companyModel->getMutualMatches($company['id'], $event['id']);

        // Get scheduled meetings
        $meetings = $this->companyModel->getScheduledMeetings($company['id'], $event['id']);

        $this->render('company-panel/matches', [
            'company' => $company,
            'event' => $event,
            'matches' => $matches,
            'meetings' => $meetings,
            'meta_title' => 'Tus Matches - ' . $event['name']
        ]);
    }

    /**
     * Update company profile
     */
    public function updateProfile(): void
    {
        $company = $this->requireCompanyAuth();
        if (!$company) return;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/empresa/panel');
            return;
        }

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Token de seguridad inválido');
            $this->redirect('/empresa/panel');
            return;
        }

        $data = [
            'contact_name' => trim($_POST['contact_name'] ?? ''),
            'contact_email' => trim($_POST['contact_email'] ?? ''),
            'contact_phone' => trim($_POST['contact_phone'] ?? ''),
            'sector' => trim($_POST['sector'] ?? ''),
            'employees' => trim($_POST['employees'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];

        // Validate email
        if ($data['contact_email'] && !filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Email inválido');
            $this->redirect('/empresa/panel');
            return;
        }

        $this->companyModel->update($company['id'], $data);

        $this->flash('success', 'Perfil actualizado');
        $this->redirect('/empresa/panel');
    }

    /**
     * Get company from session
     */
    private function getCompanyFromSession(): ?array
    {
        $companyId = $_SESSION['company_id'] ?? null;
        if (!$companyId) return null;

        return $this->companyModel->find($companyId);
    }

    /**
     * Require company authentication
     */
    private function requireCompanyAuth(): ?array
    {
        $company = $this->getCompanyFromSession();

        if (!$company) {
            if ($this->isAjax()) {
                $this->jsonError('No autorizado', 401);
                return null;
            }
            $this->redirect('/empresa/login');
            return null;
        }

        if (!$company['active']) {
            unset($_SESSION['company_id'], $_SESSION['company_code']);
            if ($this->isAjax()) {
                $this->jsonError('Cuenta desactivada', 403);
                return null;
            }
            $this->redirect('/empresa/login');
            return null;
        }

        return $company;
    }

    /**
     * Check if request is AJAX
     */
    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
