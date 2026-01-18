<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\Sponsor;
use App\Models\Company;
use App\Models\TlosSetting;
use App\Services\EmailService;

/**
 * Sponsor Panel Controller
 * TLOS - The Last of SaaS
 *
 * Panel for sponsors to view and select companies
 */
class SponsorPanelController extends Controller
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
     * Sponsor login page
     */
    public function login(): void
    {
        // Check if already logged in
        if ($this->getSponsorFromSession()) {
            $this->redirect('/sponsor/panel');
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
                    $sponsor = $this->sponsorModel->findByCode($code);

                    if (!$sponsor || !$sponsor['active']) {
                        $error = 'Código de acceso no válido';
                    } else {
                        // Store sponsor in session
                        $_SESSION['sponsor_id'] = $sponsor['id'];
                        $_SESSION['sponsor_code'] = $sponsor['code'];

                        $this->redirect('/sponsor/panel');
                        return;
                    }
                }
            }
        }

        $this->render('sponsor-panel/login', [
            'error' => $error,
            'meta_title' => 'Acceso Sponsors - The Last of SaaS'
        ]);
    }

    /**
     * Sponsor logout
     */
    public function logout(): void
    {
        unset($_SESSION['sponsor_id'], $_SESSION['sponsor_code']);
        $this->redirect('/sponsor/login');
    }

    /**
     * Main sponsor panel
     */
    public function panel(): void
    {
        $sponsor = $this->requireSponsorAuth();
        if (!$sponsor) return;

        // Get events where this sponsor participates
        $events = $this->sponsorModel->getEvents($sponsor['id']);

        // Get current/upcoming event (first active one)
        $currentEvent = null;
        foreach ($events as $event) {
            if ($event['status'] === 'published') {
                $currentEvent = $event;
                break;
            }
        }

        // Get sponsor's selections and matches for current event
        $selections = [];
        $matches = [];
        $stats = [];

        if ($currentEvent) {
            $selections = $this->sponsorModel->getSelections($sponsor['id'], $currentEvent['id']);
            $matches = $this->sponsorModel->getMutualMatches($sponsor['id'], $currentEvent['id']);

            // Get settings
            $maxSelections = (int)$this->settingModel->get('max_sponsor_selections', 10);
            $stats = [
                'total_selections' => count($selections),
                'max_selections' => $maxSelections,
                'total_matches' => count($matches),
                'remaining_selections' => max(0, $maxSelections - count($selections))
            ];
        }

        $this->render('sponsor-panel/panel', [
            'sponsor' => $sponsor,
            'events' => $events,
            'currentEvent' => $currentEvent,
            'selections' => $selections,
            'matches' => $matches,
            'stats' => $stats,
            'meta_title' => 'Panel Sponsor - ' . $sponsor['name']
        ]);
    }

    /**
     * View companies list for selection
     */
    public function companies(string $eventId): void
    {
        $sponsor = $this->requireSponsorAuth();
        if (!$sponsor) return;

        $event = $this->eventModel->find($eventId);
        if (!$event || $event['status'] !== 'published') {
            $this->notFound();
            return;
        }

        // Verify sponsor participates in this event
        if (!$this->sponsorModel->participatesInEvent($sponsor['id'], $event['id'])) {
            $this->redirect('/sponsor/panel');
            return;
        }

        // Get all companies for this event
        $companies = $this->companyModel->getByEvent($event['id']);

        // Get sponsor's current selections
        $selections = $this->sponsorModel->getSelections($sponsor['id'], $event['id']);
        $selectedIds = array_column($selections, 'company_id');

        // Get mutual matches
        $matches = $this->sponsorModel->getMutualMatches($sponsor['id'], $event['id']);
        $matchedIds = array_column($matches, 'company_id');

        // Get settings
        $maxSelections = (int)$this->settingModel->get('max_sponsor_selections', 10);

        // Filter options
        $filter = $_GET['filter'] ?? 'all';
        $sector = $_GET['sector'] ?? '';
        $search = $_GET['search'] ?? '';

        // Get unique sectors for filter
        $sectors = array_unique(array_filter(array_column($companies, 'sector')));
        sort($sectors);

        // Apply filters
        $filteredCompanies = array_filter($companies, function($company) use ($filter, $sector, $search, $selectedIds, $matchedIds) {
            // Text search
            if ($search) {
                $searchLower = strtolower($search);
                $matchesSearch = str_contains(strtolower($company['name']), $searchLower) ||
                                str_contains(strtolower($company['sector'] ?? ''), $searchLower) ||
                                str_contains(strtolower($company['description'] ?? ''), $searchLower);
                if (!$matchesSearch) return false;
            }

            // Sector filter
            if ($sector && ($company['sector'] ?? '') !== $sector) {
                return false;
            }

            // Status filter
            switch ($filter) {
                case 'selected':
                    return in_array($company['id'], $selectedIds);
                case 'matched':
                    return in_array($company['id'], $matchedIds);
                case 'available':
                    return !in_array($company['id'], $selectedIds);
                default:
                    return true;
            }
        });

        $this->render('sponsor-panel/companies', [
            'sponsor' => $sponsor,
            'event' => $event,
            'companies' => $filteredCompanies,
            'selectedIds' => $selectedIds,
            'matchedIds' => $matchedIds,
            'maxSelections' => $maxSelections,
            'currentSelections' => count($selections),
            'sectors' => $sectors,
            'currentFilter' => $filter,
            'currentSector' => $sector,
            'currentSearch' => $search,
            'meta_title' => 'Empresas - ' . $event['name']
        ]);
    }

    /**
     * View single company detail
     */
    public function companyDetail(string $eventId, string $companyId): void
    {
        $sponsor = $this->requireSponsorAuth();
        if (!$sponsor) return;

        $event = $this->eventModel->find($eventId);
        $company = $this->companyModel->find($companyId);

        if (!$event || !$company) {
            $this->notFound();
            return;
        }

        // Get company's SaaS usage
        $saasUsage = $this->companyModel->getSaasUsage($company['id']);

        // Check if already selected
        $isSelected = $this->sponsorModel->hasSelected($sponsor['id'], $company['id'], $event['id']);

        // Check if mutual match
        $isMatch = $this->sponsorModel->isMutualMatch($sponsor['id'], $company['id'], $event['id']);

        $this->render('sponsor-panel/company-detail', [
            'sponsor' => $sponsor,
            'event' => $event,
            'company' => $company,
            'saasUsage' => $saasUsage,
            'isSelected' => $isSelected,
            'isMatch' => $isMatch,
            'meta_title' => $company['name'] . ' - Detalle'
        ]);
    }

    /**
     * Select a company (AJAX)
     */
    public function selectCompany(): void
    {
        $sponsor = $this->requireSponsorAuth();
        if (!$sponsor) {
            $this->jsonError('No autorizado', 401);
            return;
        }

        if (!$this->validateCsrf()) {
            $this->jsonError('Token de seguridad inválido', 403);
            return;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        $companyId = (int)($_POST['company_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');

        if (!$eventId || !$companyId) {
            $this->jsonError('Datos incompletos', 400);
            return;
        }

        // Verify event and company exist
        $event = $this->eventModel->find($eventId);
        $company = $this->companyModel->find($companyId);

        if (!$event || !$company) {
            $this->jsonError('Evento o empresa no encontrados', 404);
            return;
        }

        // Check if sponsor participates in event
        if (!$this->sponsorModel->participatesInEvent($sponsor['id'], $event['id'])) {
            $this->jsonError('No participas en este evento', 403);
            return;
        }

        // Check selection limit
        $maxSelections = (int)$this->settingModel->get('max_sponsor_selections', 10);
        $currentSelections = count($this->sponsorModel->getSelections($sponsor['id'], $event['id']));

        if ($currentSelections >= $maxSelections) {
            $this->jsonError("Has alcanzado el límite de {$maxSelections} selecciones", 400);
            return;
        }

        // Check if already selected
        if ($this->sponsorModel->hasSelected($sponsor['id'], $company['id'], $event['id'])) {
            $this->jsonError('Ya has seleccionado esta empresa', 400);
            return;
        }

        // Create selection
        $priority = $currentSelections + 1;
        $result = $this->sponsorModel->selectCompany($sponsor['id'], $company['id'], $event['id'], $priority);

        if (!$result) {
            $this->jsonError('Error al guardar la selección', 500);
            return;
        }

        // Save message if provided
        if ($message && $this->settingModel->get('allow_sponsor_messages', true)) {
            $this->sponsorModel->saveMessage($sponsor['id'], $company['id'], $event['id'], $message);
        }

        // Check if it's a mutual match
        $isMatch = $this->companyModel->hasSelected($company['id'], $sponsor['id'], $event['id']);

        // Send notification emails
        if ($this->settingModel->get('notify_companies', true)) {
            try {
                $emailService = new EmailService();
                $emailService->sendSelectionNotification('company', $company, $sponsor, $event, $message);

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
            'message' => $isMatch ? '¡Match mutuo!' : 'Empresa seleccionada'
        ]);
    }

    /**
     * Remove company selection (AJAX)
     */
    public function unselectCompany(): void
    {
        $sponsor = $this->requireSponsorAuth();
        if (!$sponsor) {
            $this->jsonError('No autorizado', 401);
            return;
        }

        if (!$this->validateCsrf()) {
            $this->jsonError('Token de seguridad inválido', 403);
            return;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        $companyId = (int)($_POST['company_id'] ?? 0);

        if (!$eventId || !$companyId) {
            $this->jsonError('Datos incompletos', 400);
            return;
        }

        $result = $this->sponsorModel->unselectCompany($sponsor['id'], $companyId, $eventId);

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
        $sponsor = $this->requireSponsorAuth();
        if (!$sponsor) return;

        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->notFound();
            return;
        }

        // Get mutual matches
        $matches = $this->sponsorModel->getMutualMatches($sponsor['id'], $event['id']);

        // Get scheduled meetings
        $meetings = $this->sponsorModel->getScheduledMeetings($sponsor['id'], $event['id']);

        $this->render('sponsor-panel/matches', [
            'sponsor' => $sponsor,
            'event' => $event,
            'matches' => $matches,
            'meetings' => $meetings,
            'meta_title' => 'Tus Matches - ' . $event['name']
        ]);
    }

    /**
     * Get sponsor from session
     */
    private function getSponsorFromSession(): ?array
    {
        $sponsorId = $_SESSION['sponsor_id'] ?? null;
        if (!$sponsorId) return null;

        return $this->sponsorModel->find($sponsorId);
    }

    /**
     * Require sponsor authentication
     */
    private function requireSponsorAuth(): ?array
    {
        $sponsor = $this->getSponsorFromSession();

        if (!$sponsor) {
            if ($this->isAjax()) {
                $this->jsonError('No autorizado', 401);
                return null;
            }
            $this->redirect('/sponsor/login');
            return null;
        }

        if (!$sponsor['active']) {
            unset($_SESSION['sponsor_id'], $_SESSION['sponsor_code']);
            if ($this->isAjax()) {
                $this->jsonError('Cuenta desactivada', 403);
                return null;
            }
            $this->redirect('/sponsor/login');
            return null;
        }

        return $sponsor;
    }
}
