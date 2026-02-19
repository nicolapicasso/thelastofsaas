<?php
/**
 * Tickets Block Template
 * TLOS - The Last of SaaS
 */

use App\Models\Event;
use App\Models\Sponsor;
use App\Models\TicketType;
use App\Helpers\TranslationHelper;

$t = TranslationHelper::getInstance();

$eventModel = new Event();
$sponsorModel = new Sponsor();
$ticketTypeModel = new TicketType();

// Get settings
$eventId = $settings['event_id'] ?? '';
$sponsorId = $settings['sponsor_id'] ?? '';
$ticketTypeId = $settings['ticket_type_id'] ?? '';
$displayMode = $settings['display_mode'] ?? 'card';
$showPrice = $settings['show_price'] ?? true;
$showRemaining = $settings['show_remaining'] ?? true;
$showDescription = $settings['show_description'] ?? true;
$customPrice = $settings['custom_price'] ?? '';
$customLimit = $settings['custom_limit'] ?? '';

if (!$eventId) {
    return;
}

// Get event
$event = $eventModel->find((int)$eventId);
if (!$event || !in_array($event['status'], ['published', 'active'])) {
    return;
}

// Get sponsor if specified
$sponsor = null;
if ($sponsorId) {
    $sponsor = $sponsorModel->find((int)$sponsorId);
}

// Get ticket type if specified, otherwise get default
$ticketType = null;
if ($ticketTypeId) {
    $ticketType = $ticketTypeModel->find((int)$ticketTypeId);
} else {
    // Get first available ticket type for the event
    $availableTypes = $ticketTypeModel->getAvailableForEvent((int)$eventId);
    if (!empty($availableTypes)) {
        $ticketType = $availableTypes[0];
    }
}

// Calculate remaining tickets
$remaining = null;
if ($ticketType && isset($ticketType['quantity_available']) && $ticketType['quantity_available'] > 0) {
    $sold = $ticketType['quantity_sold'] ?? 0;
    $remaining = $ticketType['quantity_available'] - $sold;
}

// Build registration URL
$registerUrl = '/eventos/' . ($event['slug'] ?? $eventId) . '/registro';
if ($sponsor && !empty($sponsor['code'])) {
    $registerUrl .= '?code=' . urlencode($sponsor['code']);
}

// Build custom styles
$colorStyles = [];
if (!empty($settings['title_color'])) {
    $colorStyles[] = "--block-title-color: {$settings['title_color']}";
}
$customStyles = !empty($colorStyles) ? implode('; ', $colorStyles) . '; ' : '';

$blockId = 'tickets-' . uniqid();
?>

<section class="block block-tickets section <?= $renderer->getBlockClasses($block, $settings) ?? '' ?>"
         id="<?= $blockId ?>"
         style="<?= $customStyles ?>">
    <div class="container">
        <?php if (!empty($content['title']) || !empty($content['subtitle'])): ?>
            <div class="section-header text-center" data-aos="fade-up">
                <?php if (!empty($content['title'])): ?>
                    <h2 class="section-title"><?= htmlspecialchars($content['title']) ?></h2>
                <?php endif; ?>
                <?php if (!empty($content['subtitle'])): ?>
                    <p class="section-subtitle"><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="ticket-container ticket-<?= $displayMode ?>" data-aos="fade-up" data-aos-delay="100">
            <div class="ticket-card">
                <?php if ($sponsor && !empty($sponsor['logo_url'])): ?>
                    <div class="ticket-sponsor">
                        <span class="sponsor-label"><?= $t->text('invitation_from') ?></span>
                        <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="<?= htmlspecialchars($sponsor['name']) ?>" class="sponsor-logo">
                    </div>
                <?php endif; ?>

                <div class="ticket-event">
                    <?php if (!empty($event['featured_image'])): ?>
                        <div class="event-image">
                            <img src="<?= htmlspecialchars($event['featured_image']) ?>" alt="">
                        </div>
                    <?php endif; ?>
                    <div class="event-info">
                        <h3 class="event-name"><?= htmlspecialchars($event['name']) ?></h3>
                        <?php if (!empty($event['start_date'])): ?>
                            <div class="event-date">
                                <i class="fas fa-calendar"></i>
                                <span><?= date('d F Y', strtotime($event['start_date'])) ?></span>
                                <?php if (!empty($event['start_time'])): ?>
                                    <span>- <?= date('H:i', strtotime($event['start_time'])) ?>h</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($event['venue_name'])): ?>
                            <div class="event-venue">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($event['venue_name']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($ticketType): ?>
                    <div class="ticket-info">
                        <?php if (!empty($ticketType['name'])): ?>
                            <div class="ticket-type"><?= htmlspecialchars($ticketType['name']) ?></div>
                        <?php endif; ?>

                        <?php if ($showDescription && !empty($ticketType['description'])): ?>
                            <p class="ticket-description"><?= htmlspecialchars($ticketType['description']) ?></p>
                        <?php endif; ?>

                        <div class="ticket-meta">
                            <?php if ($showPrice): ?>
                                <div class="ticket-price">
                                    <?php if ($customPrice): ?>
                                        <span class="price"><?= htmlspecialchars($customPrice) ?></span>
                                    <?php elseif (isset($ticketType['price']) && $ticketType['price'] > 0): ?>
                                        <span class="price"><?= number_format($ticketType['price'], 2) ?> EUR</span>
                                    <?php else: ?>
                                        <span class="price free"><?= $t->text('free') ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($showRemaining): ?>
                                <div class="ticket-availability">
                                    <?php if ($customLimit): ?>
                                        <span class="availability"><?= htmlspecialchars($customLimit) ?></span>
                                    <?php elseif ($remaining !== null): ?>
                                        <?php if ($remaining > 0): ?>
                                            <span class="availability available">
                                                <i class="fas fa-check-circle"></i>
                                                <?= $remaining ?> <?= $remaining === 1 ? $t->text('seat_available') : $t->text('seats_available') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="availability sold-out">
                                                <i class="fas fa-times-circle"></i>
                                                <?= $t->text('sold_out') ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="availability available">
                                            <i class="fas fa-check-circle"></i>
                                            <?= $t->text('seats_available_generic') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="ticket-action">
                    <a href="<?= $registerUrl ?>" class="btn btn-primary btn-lg btn-block">
                        <?= htmlspecialchars($content['cta_text'] ?? $t->text('buy_ticket')) ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
#<?= $blockId ?> .ticket-container {
    max-width: 500px;
    margin: 0 auto;
}

#<?= $blockId ?> .ticket-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

#<?= $blockId ?> .ticket-sponsor {
    padding: 1rem 1.5rem;
    background: var(--color-gray-50);
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid var(--color-gray-100);
}

#<?= $blockId ?> .sponsor-label {
    font-size: 0.8rem;
    color: var(--color-gray-500);
}

#<?= $blockId ?> .sponsor-logo {
    height: 30px;
    width: auto;
    max-width: 120px;
    object-fit: contain;
}

#<?= $blockId ?> .ticket-event {
    padding: 1.5rem;
}

#<?= $blockId ?> .event-image {
    margin-bottom: 1rem;
    border-radius: 8px;
    overflow: hidden;
}

#<?= $blockId ?> .event-image img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

#<?= $blockId ?> .event-name {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0 0 0.75rem 0;
}

#<?= $blockId ?> .event-date,
#<?= $blockId ?> .event-venue {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--color-gray-600);
    margin-bottom: 0.5rem;
}

#<?= $blockId ?> .event-date i,
#<?= $blockId ?> .event-venue i {
    color: var(--color-primary);
    width: 16px;
}

#<?= $blockId ?> .ticket-info {
    padding: 0 1.5rem 1.5rem;
    border-top: 1px dashed var(--color-gray-200);
    margin-top: 1rem;
    padding-top: 1rem;
}

#<?= $blockId ?> .ticket-type {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

#<?= $blockId ?> .ticket-description {
    font-size: 0.9rem;
    color: var(--color-gray-600);
    margin: 0 0 1rem 0;
}

#<?= $blockId ?> .ticket-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

#<?= $blockId ?> .ticket-price .price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-primary);
}

#<?= $blockId ?> .ticket-price .price.free {
    color: var(--color-success);
}

#<?= $blockId ?> .ticket-availability .availability {
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

#<?= $blockId ?> .ticket-availability .available {
    color: var(--color-success);
}

#<?= $blockId ?> .ticket-availability .sold-out {
    color: var(--color-error);
}

#<?= $blockId ?> .ticket-action {
    padding: 1.5rem;
    background: var(--color-gray-50);
}

#<?= $blockId ?> .ticket-action .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 1rem 2rem;
    font-size: 1.1rem;
}
</style>
