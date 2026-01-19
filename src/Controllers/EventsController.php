<?php

namespace App\Controllers;

use App\Controllers\Frontend\BaseController;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Activity;
use App\Models\TeamMember;
use App\Models\Sponsor;

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

        $this->view->setLayout('layouts/event');
        $this->render('events/index', $this->getEventData([
            'events' => $events,
            'meta_title' => 'Próximos Eventos - The Last of SaaS',
            'meta_description' => 'Descubre los próximos eventos de networking B2B y SaaS'
        ]));
    }

    /**
     * Get common event page data (navigation, settings, etc.)
     */
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

        if (!$event || !in_array($event['status'], ['published', 'active'])) {
            $this->notFound();
            return;
        }

        // Get event sponsors by level
        $sponsors = $this->eventModel->getSponsors($event['id']);
        $sponsorsByLevel = [];
        foreach ($sponsors as $sponsor) {
            $sponsorsByLevel[$sponsor['level']][] = $sponsor;
        }

        // Get event features
        $features = $this->eventModel->getFeatures($event['id']);

        // Get available ticket types
        $ticketTypes = $this->ticketTypeModel->getAvailableForEvent($event['id']);

        // Get event statistics
        $stats = $this->eventModel->getStats($event['id']);

        // Get event companies
        $companies = $this->eventModel->getCompanies($event['id']);

        // Get event activities/agenda
        $activities = $this->activityModel->getByEvent($event['id']);

        // Group activities by date
        $activitiesByDate = [];
        foreach ($activities as $activity) {
            $date = $activity['activity_date'] ?? $event['start_date'];
            $activitiesByDate[$date][] = $activity;
        }

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
        }

        $this->view->setLayout('layouts/event');
        $this->render('events/show', $this->getEventData([
            'event' => $event,
            'sponsorsByLevel' => $sponsorsByLevel,
            'features' => $features,
            'ticketTypes' => $ticketTypes,
            'stats' => $stats,
            'companies' => $companies,
            'activities' => $activities,
            'activitiesByDate' => $activitiesByDate,
            'speakers' => $speakers,
            'meta_title' => $event['meta_title'] ?: $event['name'] . ' - The Last of SaaS',
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

        if (!$event || !in_array($event['status'], ['published', 'active'])) {
            $this->notFound();
            return;
        }

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

        if (!$event || !in_array($event['status'], ['published', 'active'])) {
            $this->notFound();
            return;
        }

        $sponsors = $this->eventModel->getSponsors($event['id']);
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

        // Get events this sponsor participates in
        $events = $sponsorModel->getEvents($sponsor['id']);

        $this->view->setLayout('layouts/event');
        $this->render('sponsors/show', $this->getEventData([
            'sponsor' => $sponsor,
            'events' => $events,
            'meta_title' => $sponsor['name'] . ' - The Last of SaaS',
            'meta_description' => $sponsor['description'] ?? ''
        ]));
    }
}
