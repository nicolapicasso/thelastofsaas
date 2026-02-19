<?php

namespace App\Controllers;

use App\Controllers\Frontend\BaseController;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Activity;
use App\Models\TeamMember;
use App\Models\Sponsor;
use App\Models\Company;
use App\Models\Room;

/**
 * Frontend Events Controller
 * TLOS - The Last of SaaS
 *
 * Public event listing and detail pages
 */
class EventsController extends BaseController
{
    private Event $eventModel;
    private TicketType $ticketTypeModel;
    private Activity $activityModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
        $this->ticketTypeModel = new TicketType();
        $this->activityModel = new Activity();
    }

    /**
     * List upcoming events
     */
    public function index(): void
    {
        $events = $this->eventModel->getUpcoming(12);

        // Translate events
        $this->translator->translateEntities('event', $events);

        $this->view->setLayout('layouts/event');
        $this->render('events/index', $this->getEventData([
            'events' => $events,
            'meta_title' => $this->translator->text('upcoming_events') . ($this->getSiteName() ? ' - ' . $this->getSiteName() : ''),
            'meta_description' => $this->translator->text('upcoming_events_subtitle')
        ]));
    }

    /**
     * Get common event page data (navigation, settings, etc.)
     */
    /**
     * Get site name from settings
     */
    private function getSiteName(): string
    {
        return $this->getSetting('site_name', '');
    }

    private function getEventData(array $data = []): array
    {
        return array_merge([
            'mainNav' => $this->getMainNavigation(),
            'logoHeader' => $this->getSetting('logo_header', '/assets/images/logo.svg'),
            'headerButtons' => $this->getHeaderButtons(),
            'sidebarMenu' => $this->getSidebarMenu(),
            'footerNav' => $this->getFooterNavigation(),
            'footerTagline' => $this->getFooterTagline(),
            'footerCopyright' => $this->getFooterCopyright(),
            'socialLinks' => $this->getSocialLinks(),
            'currentLang' => $this->currentLang,
        ], $data);
    }

    /**
     * Show event detail
     */
    public function show(string $slug): void
    {
        $event = $this->eventModel->findBySlug($slug);

        // Allow published, active, and finished events to be visible
        if (!$event || !in_array($event['status'], ['published', 'active', 'finished'])) {
            $this->notFound();
            return;
        }

        // Translate event
        $this->translator->translateEntity('event', $event);

        // Get event sponsors by level (exclude hidden sponsors)
        $sponsors = $this->eventModel->getSponsors($event['id'], false);
        $this->translator->translateEntities('sponsor', $sponsors);
        $sponsorsByLevel = [];
        foreach ($sponsors as $sponsor) {
            $sponsorsByLevel[$sponsor['level']][] = $sponsor;
        }

        // Get event features
        $features = $this->eventModel->getFeatures($event['id']);

        // Get available ticket types
        $ticketTypes = $this->ticketTypeModel->getAvailableForEvent($event['id']);
        $this->translator->translateEntities('ticket_type', $ticketTypes);

        // Get event statistics
        $stats = $this->eventModel->getStats($event['id']);

        // Get event companies
        $companies = $this->eventModel->getCompanies($event['id']);
        $this->translator->translateEntities('company', $companies);

        // Get event activities/agenda
        $activities = $this->activityModel->getByEvent($event['id']);
        $this->translator->translateEntities('activity', $activities);

        // Sort activities chronologically by date and start_time
        usort($activities, function($a, $b) {
            $dateA = $a['activity_date'] ?? '9999-99-99';
            $dateB = $b['activity_date'] ?? '9999-99-99';
            if ($dateA !== $dateB) {
                return strcmp($dateA, $dateB);
            }
            $timeA = $a['start_time'] ?? '99:99:99';
            $timeB = $b['start_time'] ?? '99:99:99';
            return strcmp($timeA, $timeB);
        });

        // Group activities by date
        $activitiesByDate = [];
        foreach ($activities as $activity) {
            $date = $activity['activity_date'] ?? $event['start_date'];
            $activitiesByDate[$date][] = $activity;
        }

        // Sort dates chronologically
        ksort($activitiesByDate);

        // Get speakers (team members with activities in this event)
        $speakerIds = array_unique(array_filter(array_column($activities, 'speaker_id')));
        $speakers = [];
        if (!empty($speakerIds)) {
            $teamMemberModel = new TeamMember();
            foreach ($speakerIds as $speakerId) {
                $speaker = $teamMemberModel->find((int)$speakerId);
                if ($speaker && $speaker['active']) {
                    $speakers[] = $speaker;
                }
            }
            $this->translator->translateEntities('team_member', $speakers);
        }

        // Get event rooms with images
        $roomModel = new Room();
        $eventRooms = $roomModel->getWithImagesByEvent($event['id']);

        // Get content activities (charlas and talleres)
        $contentActivities = $this->activityModel->getByEventAndTypes($event['id'], ['charla', 'taller']);
        $this->translator->translateEntities('activity', $contentActivities);

        $this->view->setLayout('layouts/event');
        $this->render('events/show', $this->getEventData([
            'event' => $event,
            'sponsorsByLevel' => $sponsorsByLevel,
            'eventRooms' => $eventRooms,
            'features' => $features,
            'ticketTypes' => $ticketTypes,
            'stats' => $stats,
            'companies' => $companies,
            'activities' => $activities,
            'activitiesByDate' => $activitiesByDate,
            'speakers' => $speakers,
            'contentActivities' => $contentActivities,
            'meta_title' => $event['meta_title'] ?: $event['name'] . '' . ($this->getSiteName() ? ' - ' . $this->getSiteName() : ''),
            'meta_description' => $event['meta_description'] ?: $event['short_description'],
            'meta_image' => $event['featured_image']
        ]));
    }

    /**
     * Event agenda/schedule
     */
    public function agenda(string $slug): void
    {
        $event = $this->eventModel->findBySlug($slug);

        // Allow published, active, and finished events
        if (!$event || !in_array($event['status'], ['published', 'active', 'finished'])) {
            $this->notFound();
            return;
        }

        // Translate event
        $this->translator->translateEntity('event', $event);

        $this->view->setLayout('layouts/event');
        $this->render('events/agenda', $this->getEventData([
            'event' => $event,
            'meta_title' => 'Agenda - ' . $event['name']
        ]));
    }

    /**
     * Event sponsors page
     */
    public function sponsors(string $slug): void
    {
        $event = $this->eventModel->findBySlug($slug);

        // Allow published, active, and finished events
        if (!$event || !in_array($event['status'], ['published', 'active', 'finished'])) {
            $this->notFound();
            return;
        }

        // Translate event
        $this->translator->translateEntity('event', $event);

        $sponsors = $this->eventModel->getSponsors($event['id'], false);
        $this->translator->translateEntities('sponsor', $sponsors);
        $sponsorsByLevel = [];
        foreach ($sponsors as $sponsor) {
            $sponsorsByLevel[$sponsor['level']][] = $sponsor;
        }

        $this->view->setLayout('layouts/event');
        $this->render('events/sponsors', $this->getEventData([
            'event' => $event,
            'sponsorsByLevel' => $sponsorsByLevel,
            'meta_title' => 'Sponsors - ' . $event['name']
        ]));
    }

    /**
     * Show sponsor public page
     */
    public function sponsorPage(string $slug): void
    {
        $sponsorModel = new Sponsor();
        $sponsor = $sponsorModel->findBySlug($slug);

        if (!$sponsor) {
            $this->notFound();
            return;
        }

        // Translate sponsor
        $this->translator->translateEntity('sponsor', $sponsor);

        // Get events this sponsor participates in
        $events = $sponsorModel->getEvents($sponsor['id']);
        $this->translator->translateEntities('event', $events);

        // Get activities associated with this sponsor
        $sponsorActivities = $this->activityModel->getBySponsor($sponsor['id']);
        $this->translator->translateEntities('activity', $sponsorActivities);

        $this->view->setLayout('layouts/event');
        $this->render('sponsors/show', $this->getEventData([
            'sponsor' => $sponsor,
            'events' => $events,
            'sponsorActivities' => $sponsorActivities,
            'meta_title' => $sponsor['name'] . '' . ($this->getSiteName() ? ' - ' . $this->getSiteName() : ''),
            'meta_description' => $sponsor['description'] ?? ''
        ]));
    }

    /**
     * Show company public page
     */
    public function companyPage(string $slug): void
    {
        $companyModel = new Company();
        $company = $companyModel->findBySlug($slug);

        if (!$company) {
            $this->notFound();
            return;
        }

        // Translate company
        $this->translator->translateEntity('company', $company);

        // Get events this company participates in
        $events = $companyModel->getEvents($company['id']);
        $this->translator->translateEntities('event', $events);

        $this->view->setLayout('layouts/event');
        $this->render('companies/show', $this->getEventData([
            'company' => $company,
            'events' => $events,
            'meta_title' => $company['name'] . '' . ($this->getSiteName() ? ' - ' . $this->getSiteName() : ''),
            'meta_description' => $company['description'] ?? ''
        ]));
    }
}
