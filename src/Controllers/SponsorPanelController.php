<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\Sponsor;
use App\Models\Company;
use App\Models\SponsorInviteCode;
use App\Models\TlosSetting;
use App\Models\Message;
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
    private SponsorInviteCode $inviteCodeModel;
    private TlosSetting $settingModel;
    private Message $messageModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
        $this->sponsorModel = new Sponsor();
        $this->companyModel = new Company();
        $this->inviteCodeModel = new SponsorInviteCode();
        $this->settingModel = new TlosSetting();
        $this->messageModel = new Message();
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

        // Check for code in URL (direct access link)
        $urlCode = strtoupper(trim($_GET['code'] ?? ''));
        if ($urlCode) {
            $sponsor = $this->sponsorModel->findByCode($urlCode);
            if ($sponsor && $sponsor['active']) {
                // Store sponsor in session
                $_SESSION['sponsor_id'] = $sponsor['id'];
                $_SESSION['sponsor_code'] = $sponsor['code'];
                $this->redirect('/sponsor/panel');
                return;
            } else {
                $error = 'Código de acceso no válido';
            }
        }

        // Handle form submission
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

        // Get current/upcoming event (first published or active one)
        $currentEvent = null;
        foreach ($events as $event) {
            if (in_array($event['status'], ['published', 'active'])) {
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
        if (!$event || !in_array($event['status'], ['published', 'active'])) {
            $this->notFound();
            return;
        }

        // Verify sponsor participates in this event
        if (!$this->sponsorModel->participatesInEvent($sponsor['id'], $event['id'])) {
            $this->redirect('/sponsor/panel');
            return;
        }

        // Get all events for this sponsor (for event selector)
        $events = $this->sponsorModel->getEvents($sponsor['id']);

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
            'events' => $events,
            'companies' => $filteredCompanies,
            'selectedIds' => $selectedIds,
            'matchedIds' => $matchedIds,
            'maxSelections' => $maxSelections,
            'currentSelections' => count($selections),
            'sectors' => $sectors,
            'currentFilter' => $filter,
            'currentSector' => $sector,
            'currentSearch' => $search,
            'csrf_token' => $this->generateCsrf(),
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

        // Get all events for event selector
        $events = $this->sponsorModel->getEvents($sponsor['id']);

        $this->render('sponsor-panel/matches', [
            'sponsor' => $sponsor,
            'event' => $event,
            'events' => $events,
            'matches' => $matches,
            'meetings' => $meetings,
            'meta_title' => 'Tus Matches - ' . $event['name']
        ]);
    }

    /**
     * View sponsor's invite codes
     */
    public function inviteCodes(string $eventId): void
    {
        $sponsor = $this->requireSponsorAuth();
        if (!$sponsor) return;

        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->notFound();
            return;
        }

        // Verify sponsor participates in this event
        if (!$this->sponsorModel->participatesInEvent($sponsor['id'], $event['id'])) {
            $this->redirect('/sponsor/panel');
            return;
        }

        // Get sponsor's invite codes for this event
        $codes = $this->inviteCodeModel->getBySponsor($sponsor['id'], (int) $eventId);

        // Get stats for each code
        foreach ($codes as &$code) {
            $code['stats'] = $this->inviteCodeModel->getUsageStats($code['id']);
        }

        // Get overall stats
        $overallStats = $this->inviteCodeModel->getSponsorStats($sponsor['id'], (int) $eventId);

        // Get all events for event selector
        $events = $this->sponsorModel->getEvents($sponsor['id']);

        $this->render('sponsor-panel/invite-codes', [
            'sponsor' => $sponsor,
            'event' => $event,
            'events' => $events,
            'codes' => $codes,
            'overallStats' => $overallStats,
            'meta_title' => 'Mis Codigos de Invitacion - ' . $event['name']
        ]);
    }

    /**
     * View tickets registered with sponsor's codes
     */
    public function invitedGuests(string $eventId): void
    {
        $sponsor = $this->requireSponsorAuth();
        if (!$sponsor) return;

        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->notFound();
            return;
        }

        // Verify sponsor participates in this event
        if (!$this->sponsorModel->participatesInEvent($sponsor['id'], $event['id'])) {
            $this->redirect('/sponsor/panel');
            return;
        }

        // Get all invite codes for this sponsor/event
        $codes = $this->inviteCodeModel->getBySponsor($sponsor['id'], (int) $eventId);

        // Get all tickets for each code
        $allTickets = [];
        foreach ($codes as $code) {
            $tickets = $this->inviteCodeModel->getTickets($code['id']);
            foreach ($tickets as $ticket) {
                $ticket['invite_code'] = $code['code'];
                $allTickets[] = $ticket;
            }
        }

        // Sort by creation date
        usort($allTickets, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        // Get all events for event selector
        $events = $this->sponsorModel->getEvents($sponsor['id']);

        $this->render('sponsor-panel/invited-guests', [
            'sponsor' => $sponsor,
            'event' => $event,
            'events' => $events,
            'tickets' => $allTickets,
            'meta_title' => 'Mis Invitados - ' . $event['name']
        ]);
    }

    /**
     * View messages inbox
     */
    public function messages(string $eventId): void
    {
        $sponsor = $this->requireSponsorAuth();
        if (!$sponsor) return;

        $event = $this->eventModel->find($eventId);
        if (!$event) {
            $this->notFound();
            return;
        }

        // Verify sponsor participates in this event
        if (!$this->sponsorModel->participatesInEvent($sponsor['id'], $event['id'])) {
            $this->redirect('/sponsor/panel');
            return;
        }

        // Check if messaging is enabled
        if (!$this->settingModel->get('allow_sponsor_messages', true)) {
            $this->redirect('/sponsor/panel');
            return;
        }

        // Get all events for this sponsor (for event selector)
        $events = $this->sponsorModel->getEvents($sponsor['id']);

        // Get inbox (conversations grouped)
        $inbox = $this->messageModel->getInbox($event['id'], 'sponsor', $sponsor['id']);

        // Get unread count
        $unreadCount = $this->messageModel->getUnreadCount($event['id'], 'sponsor', $sponsor['id']);

        $this->render('sponsor-panel/messages', [
            'sponsor' => $sponsor,
            'event' => $event,
            'events' => $events,
            'inbox' => $inbox,
            'unreadCount' => $unreadCount,
            'csrf_token' => $this->generateCsrf(),
            'meta_title' => 'Mensajes - ' . $event['name']
        ]);
    }

    /**
     * View conversation with a company
     */
    public function conversation(string $eventId, string $companyId): void
    {
        $sponsor = $this->requireSponsorAuth();
        if (!$sponsor) return;

        $event = $this->eventModel->find($eventId);
        $company = $this->companyModel->find($companyId);

        if (!$event || !$company) {
            $this->notFound();
            return;
        }

        // Verify sponsor participates in this event
        if (!$this->sponsorModel->participatesInEvent($sponsor['id'], $event['id'])) {
            $this->redirect('/sponsor/panel');
            return;
        }

        // Check if messaging is enabled
        if (!$this->settingModel->get('allow_sponsor_messages', true)) {
            $this->redirect('/sponsor/panel');
            return;
        }

        // Mark messages as read
        $this->messageModel->markConversationAsRead(
            $event['id'],
            'sponsor',
            $sponsor['id'],
            'company',
            $company['id']
        );

        // Get conversation
        $messages = $this->messageModel->getConversation(
            $event['id'],
            'sponsor',
            $sponsor['id'],
            'company',
            $company['id']
        );

        // Check if can send message
        $canSend = $this->messageModel->canSendMessage(
            $event['id'],
            'sponsor',
            $sponsor['id'],
            'company',
            $company['id']
        );

        $this->render('sponsor-panel/conversation', [
            'sponsor' => $sponsor,
            'event' => $event,
            'company' => $company,
            'messages' => $messages,
            'canSend' => $canSend,
            'csrf_token' => $this->generateCsrf(),
            'meta_title' => 'Conversación con ' . $company['name']
        ]);
    }

    /**
     * Send message to company (AJAX)
     */
    public function sendMessage(): void
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

        // Check if messaging is enabled
        if (!$this->settingModel->get('allow_sponsor_messages', true)) {
            $this->jsonError('El sistema de mensajería está desactivado', 403);
            return;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        $companyId = (int)($_POST['company_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');

        if (!$eventId || !$companyId || !$message) {
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

        // Check if can send
        $canSend = $this->messageModel->canSendMessage(
            $event['id'],
            'sponsor',
            $sponsor['id'],
            'company',
            $company['id']
        );

        if (!$canSend['can_send']) {
            $this->jsonError($canSend['message'] ?? 'No puedes enviar más mensajes a esta empresa', 400);
            return;
        }

        // Send message with contact details
        $result = $this->messageModel->sendMessage(
            $event['id'],
            'sponsor',
            $sponsor['id'],
            'company',
            $company['id'],
            $message,
            $sponsor['contact_name'] ?? null,
            $sponsor['contact_email'] ?? null,
            $sponsor['contact_phone'] ?? null
        );

        if (!$result) {
            $this->jsonError('Error al enviar el mensaje', 500);
            return;
        }

        // Send email notification
        if ($this->settingModel->get('notify_companies', true)) {
            try {
                $emailService = new EmailService();
                $emailService->sendMessageNotification('company', $company, $sponsor, $event, $message);
            } catch (\Exception $e) {
                error_log('Message notification error: ' . $e->getMessage());
            }
        }

        $this->json([
            'success' => true,
            'message' => 'Mensaje enviado correctamente'
        ]);
    }

    /**
     * Reply to a message (AJAX)
     */
    public function replyMessage(): void
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

        // Check if messaging is enabled
        if (!$this->settingModel->get('allow_sponsor_messages', true)) {
            $this->jsonError('El sistema de mensajería está desactivado', 403);
            return;
        }

        $messageId = (int)($_POST['message_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');

        if (!$messageId || !$message) {
            $this->jsonError('Datos incompletos', 400);
            return;
        }

        // Get original message
        $originalMessage = $this->messageModel->getMessageWithDetails($messageId);
        if (!$originalMessage) {
            $this->jsonError('Mensaje no encontrado', 404);
            return;
        }

        // Verify this sponsor is the recipient
        if ($originalMessage['recipient_type'] !== 'sponsor' || $originalMessage['recipient_id'] != $sponsor['id']) {
            $this->jsonError('No puedes responder a este mensaje', 403);
            return;
        }

        // Send reply
        $result = $this->messageModel->replyToMessage(
            $messageId,
            $message,
            $sponsor['contact_name'] ?? null,
            $sponsor['contact_email'] ?? null,
            $sponsor['contact_phone'] ?? null
        );

        if (!$result) {
            $this->jsonError('Error al enviar la respuesta', 500);
            return;
        }

        // Get recipient details for notification
        $event = $this->eventModel->find($originalMessage['event_id']);
        $company = $this->companyModel->find($originalMessage['sender_id']);

        if ($this->settingModel->get('notify_companies', true) && $company && $event) {
            try {
                $emailService = new EmailService();
                $emailService->sendMessageNotification('company', $company, $sponsor, $event, $message);
            } catch (\Exception $e) {
                error_log('Reply notification error: ' . $e->getMessage());
            }
        }

        $this->json([
            'success' => true,
            'message' => 'Respuesta enviada correctamente'
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
