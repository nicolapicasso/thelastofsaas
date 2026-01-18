<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\TicketType;

/**
 * Frontend Events Controller
 * TLOS - The Last of SaaS
 *
 * Public event listing and detail pages
 */
class EventsController extends Controller
{
    private Event $eventModel;
    private TicketType $ticketTypeModel;

    public function __construct()
    {
        parent::__construct();
        $this->eventModel = new Event();
        $this->ticketTypeModel = new TicketType();
    }

    /**
     * List upcoming events
     */
    public function index(): void
    {
        $events = $this->eventModel->getUpcoming(12);

        $this->render('events/index', [
            'events' => $events,
            'meta_title' => 'Próximos Eventos - The Last of SaaS',
            'meta_description' => 'Descubre los próximos eventos de networking B2B y SaaS'
        ]);
    }

    /**
     * Show event detail
     */
    public function show(string $slug): void
    {
        $event = $this->eventModel->findBySlug($slug);

        if (!$event || $event['status'] !== 'published') {
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

        $this->render('events/show', [
            'event' => $event,
            'sponsorsByLevel' => $sponsorsByLevel,
            'features' => $features,
            'ticketTypes' => $ticketTypes,
            'stats' => $stats,
            'meta_title' => $event['meta_title'] ?: $event['name'] . ' - The Last of SaaS',
            'meta_description' => $event['meta_description'] ?: $event['short_description'],
            'meta_image' => $event['featured_image']
        ]);
    }

    /**
     * Event agenda/schedule
     */
    public function agenda(string $slug): void
    {
        $event = $this->eventModel->findBySlug($slug);

        if (!$event || $event['status'] !== 'published') {
            $this->notFound();
            return;
        }

        $this->render('events/agenda', [
            'event' => $event,
            'meta_title' => 'Agenda - ' . $event['name']
        ]);
    }

    /**
     * Event sponsors page
     */
    public function sponsors(string $slug): void
    {
        $event = $this->eventModel->findBySlug($slug);

        if (!$event || $event['status'] !== 'published') {
            $this->notFound();
            return;
        }

        $sponsors = $this->eventModel->getSponsors($event['id']);
        $sponsorsByLevel = [];
        foreach ($sponsors as $sponsor) {
            $sponsorsByLevel[$sponsor['level']][] = $sponsor;
        }

        $this->render('events/sponsors', [
            'event' => $event,
            'sponsorsByLevel' => $sponsorsByLevel,
            'meta_title' => 'Sponsors - ' . $event['name']
        ]);
    }
}
