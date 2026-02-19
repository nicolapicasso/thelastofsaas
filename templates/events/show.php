<?php
/**
 * Event Detail Template
 * TLOS - The Last of SaaS
 * Redesigned with black/white balance and full-width layout
 */
use App\Helpers\TranslationHelper;
$t = TranslationHelper::getInstance();

$startDate = new DateTime($event['start_date']);
$endDate = $event['end_date'] ? new DateTime($event['end_date']) : null;
$featuredImage = $event['featured_image'] ?? null;
?>

<!-- SECTION A: Hero with Featured Image and Parallax -->
<section class="event-hero-parallax" <?php if ($featuredImage): ?>style="background-image: url('<?= htmlspecialchars($featuredImage) ?>');"<?php endif; ?>>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="container-wide">
            <div class="event-hero-grid">
                <div class="event-date-block">
                    <span class="day"><?= $startDate->format('d') ?></span>
                    <span class="month"><?= strtoupper($startDate->format('M')) ?></span>
                    <span class="year"><?= $startDate->format('Y') ?></span>
                </div>
                <div class="event-title-block">
                    <h1><?= htmlspecialchars($event['name']) ?></h1>
                    <div class="event-meta-inline">
                        <?php if ($event['location']): ?>
                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></span>
                        <?php endif; ?>
                        <?php if ($event['start_time']): ?>
                            <span><i class="fas fa-clock"></i> <?= substr($event['start_time'], 0, 5) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($event['short_description']): ?>
                        <p class="intro-text"><?= htmlspecialchars($event['short_description']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION B: Get Ticket CTA (Black on White) -->
<section class="event-cta event-cta--light">
    <div class="container-wide">
        <div class="cta-content">
            <div class="cta-info">
                <?php if (!empty($ticketTypes)): ?>
                    <?php
                    $minPrice = min(array_column($ticketTypes, 'price'));
                    $maxPrice = max(array_column($ticketTypes, 'price'));
                    ?>
                    <span class="cta-price">
                        <?php if ($minPrice == 0): ?>
                            <?= $t->text('from_free') ?>
                        <?php elseif ($minPrice == $maxPrice): ?>
                            <?= number_format($minPrice, 0) ?>€
                        <?php else: ?>
                            <?= $t->text('from') ?> <?= number_format($minPrice, 0) ?>€
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
                <span class="cta-label"><?= $t->text('limited_seats') ?></span>
            </div>
            <a href="/eventos/<?= $event['slug'] ?>/registro" class="btn btn-dark btn-lg">
                <i class="fas fa-ticket-alt"></i> <?= $t->text('get_ticket') ?>
            </a>
        </div>
    </div>
</section>

<!-- SECTION C: Long Description (White on Black) -->
<?php if ($event['description']): ?>
<section class="event-description">
    <div class="container-wide">
        <h2><?= $t->text('about_event') ?></h2>
        <div class="description-content">
            <?= $event['description'] ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- SECTION D: Participating Companies (Black on White) -->
<?php if (!empty($companies)): ?>
<section class="event-companies">
    <div class="container-wide">
        <h2><?= $t->text('joining_us') ?></h2>
        <div class="participants-grid">
            <?php foreach ($companies as $company): ?>
                <a href="/empresas/<?= $company['slug'] ?? $company['id'] ?>" class="participant-card">
                    <?php if (!empty($company['logo_url'])): ?>
                        <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="<?= htmlspecialchars($company['name'] ?? '') ?>">
                    <?php else: ?>
                        <span class="participant-name"><?= htmlspecialchars($company['name'] ?? '') ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- SECTION E: Agenda with Room Images (White on Black) -->
<?php if (!empty($activitiesByDate)): ?>
<section class="event-agenda">
    <div class="container-wide">
        <div class="agenda-layout">
            <div class="agenda-main">
                <h2><?= $t->text('agenda') ?></h2>
                <div class="agenda-timeline">
                    <?php
                    $lastDate = null;
                    foreach ($activitiesByDate as $date => $dateActivities):
                        foreach ($dateActivities as $index => $activity):
                            $showDate = ($lastDate !== $date);
                            $lastDate = $date;
                    ?>
                        <?php if ($showDate): ?>
                            <h3 class="agenda-date">
                                <i class="fas fa-calendar-day"></i>
                                <?= date('d M Y', strtotime($date)) ?>
                            </h3>
                        <?php endif; ?>
                        <?php
                        $actStartTime = substr($activity['start_time'] ?? '00:00', 0, 5);
                        $actEndTime = substr($activity['end_time'] ?? '23:59', 0, 5);
                        $hasValidActivityTime = $actStartTime !== '00:00' || $actEndTime !== '23:59';
                        ?>
                        <a href="/actividades/<?= htmlspecialchars($activity['slug'] ?? $activity['id']) ?>" class="agenda-item agenda-item-link <?= !empty($activity['is_featured']) ? 'featured' : '' ?>">
                            <?php if ($hasValidActivityTime): ?>
                            <div class="agenda-time">
                                <span class="time-start"><?= $actStartTime ?></span>
                                <?php if (!empty($activity['end_time'])): ?>
                                    <span class="time-end"><?= $actEndTime ?></span>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="agenda-time agenda-time-empty">
                                <span class="time-start"><i class="fas fa-clock"></i></span>
                            </div>
                            <?php endif; ?>
                            <div class="agenda-content">
                                <?php if (!empty($activity['sponsor_logo'])): ?>
                                    <div class="agenda-sponsor-badge">
                                        <img src="<?= htmlspecialchars($activity['sponsor_logo']) ?>" alt="<?= htmlspecialchars($activity['sponsor_name'] ?? 'Sponsor') ?>">
                                    </div>
                                <?php endif; ?>
                                <span class="agenda-type" style="<?= !empty($activity['category_color']) ? 'background-color:' . $activity['category_color'] : '' ?>">
                                    <?= _e($activity['category_name'] ?? $activity['activity_type'] ?? 'Actividad') ?>
                                </span>
                                <h4><?= htmlspecialchars($activity['title'] ?? '') ?></h4>
                                <?php if (!empty($activity['description'])): ?>
                                    <?php
                                    $description = strip_tags($activity['description']);
                                    $excerpt = mb_strlen($description) > 150 ? mb_substr($description, 0, 150) . '...' : $description;
                                    ?>
                                    <p class="agenda-excerpt"><?= htmlspecialchars($excerpt) ?></p>
                                    <?php if (mb_strlen($description) > 150): ?>
                                        <span class="agenda-read-more"><?= $t->text('read_more') ?> <i class="fas fa-arrow-right"></i></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <div class="agenda-meta">
                                    <?php if (!empty($activity['speaker_name'])): ?>
                                        <span class="agenda-speaker">
                                            <?php if (!empty($activity['speaker_photo'])): ?>
                                                <img src="<?= htmlspecialchars($activity['speaker_photo']) ?>" alt="">
                                            <?php endif; ?>
                                            <?= htmlspecialchars($activity['speaker_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($activity['room_name'])): ?>
                                        <span class="agenda-room">
                                            <i class="fas fa-door-open"></i> <?= htmlspecialchars($activity['room_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php
                        endforeach;
                    endforeach;
                    ?>
                </div>
            </div>
            <?php if (!empty($eventRooms)): ?>
            <div class="agenda-sidebar">
                <h3><?= $t->text('our_spaces') ?></h3>
                <div class="rooms-gallery">
                    <?php foreach ($eventRooms as $room): ?>
                        <?php if (!empty($room['image_url'])): ?>
                        <div class="room-card">
                            <div class="room-image">
                                <img src="<?= htmlspecialchars($room['image_url']) ?>" alt="<?= htmlspecialchars($room['name']) ?>">
                            </div>
                            <div class="room-info">
                                <span class="room-name"><?= htmlspecialchars($room['name']) ?></span>
                                <?php if (!empty($room['capacity'])): ?>
                                    <span class="room-capacity"><i class="fas fa-users"></i> <?= $room['capacity'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- SECTION F: Speakers Carousel (White bg) -->
<?php if (!empty($speakers)): ?>
<section class="event-speakers">
    <div class="container-wide">
        <h2><?= $t->text('speakers') ?></h2>
        <div class="speakers-carousel-wrapper">
            <div class="speakers-carousel">
                <?php foreach ($speakers as $speaker): ?>
                    <a href="/equipo/<?= $speaker['slug'] ?? $speaker['id'] ?>" class="speaker-card">
                        <div class="speaker-photo">
                            <?php if (!empty($speaker['photo'])): ?>
                                <?php
                                // Check if there's an animated version (gif)
                                $hasAnimated = !empty($speaker['photo_animated']);
                                $staticPhoto = $speaker['photo'];
                                $animatedPhoto = $speaker['photo_animated'] ?? $speaker['photo'];
                                ?>
                                <?php if ($hasAnimated): ?>
                                    <img src="<?= htmlspecialchars($staticPhoto) ?>" alt="<?= htmlspecialchars($speaker['name'] ?? '') ?>" class="photo-static">
                                    <img src="<?= htmlspecialchars($animatedPhoto) ?>" alt="<?= htmlspecialchars($speaker['name'] ?? '') ?>" class="photo-animated">
                                <?php else: ?>
                                    <img src="<?= htmlspecialchars($staticPhoto) ?>" alt="<?= htmlspecialchars($speaker['name'] ?? '') ?>">
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="speaker-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="speaker-info">
                            <strong><?= htmlspecialchars($speaker['name'] ?? '') ?></strong>
                            <?php if (!empty($speaker['position'])): ?>
                                <span class="speaker-position"><?= htmlspecialchars($speaker['position']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($speaker['company'])): ?>
                                <span class="speaker-company"><?= $t->text('at_speaker_company') ?> <?= htmlspecialchars($speaker['company']) ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- SECTION H: Sponsors by Level (Black on White) -->
<?php if (!empty($sponsorsByLevel)): ?>
<section class="event-sponsors-section">
    <div class="container-wide">
        <h2><?= $t->text('sponsors') ?></h2>
        <?php
        $levelNames = [
            'platinum' => 'Platinum',
            'gold' => 'Gold',
            'silver' => 'Silver',
            'bronze' => 'Bronze'
        ];
        foreach ($levelNames as $levelKey => $levelName):
            if (!isset($sponsorsByLevel[$levelKey])) continue;
        ?>
            <div class="sponsors-level sponsors-level--<?= $levelKey ?>">
                <h3><?= strtoupper($levelName) ?></h3>
                <div class="sponsors-grid">
                    <?php foreach ($sponsorsByLevel[$levelKey] as $sponsor): ?>
                        <a href="/sponsors/<?= $sponsor['slug'] ?? $sponsor['id'] ?>" class="sponsor-card">
                            <?php if ($sponsor['logo_url']): ?>
                                <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="<?= htmlspecialchars($sponsor['name']) ?>">
                            <?php else: ?>
                                <span class="sponsor-name"><?= htmlspecialchars($sponsor['name']) ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- SECTION I: Event Details (White on Black) -->
<section class="event-details">
    <div class="container-wide">
        <h2><?= $t->text('event_details') ?></h2>
        <div class="details-grid">
            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="detail-content">
                    <span class="detail-label"><?= $t->text('date') ?></span>
                    <span class="detail-value"><?= $startDate->format('d/m/Y') ?><?php if ($endDate && $endDate != $startDate): ?> - <?= $endDate->format('d/m/Y') ?><?php endif; ?></span>
                </div>
            </div>
            <?php if ($event['start_time']): ?>
            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="detail-content">
                    <span class="detail-label"><?= $t->text('time') ?></span>
                    <span class="detail-value"><?= substr($event['start_time'], 0, 5) ?><?php if ($event['end_time']): ?> - <?= substr($event['end_time'], 0, 5) ?><?php endif; ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($event['location']): ?>
            <?php
                // Build Google Maps URL using address if available, otherwise location name
                $mapsQuery = $event['address'] ? $event['address'] : $event['location'];
                if ($event['city']) {
                    $mapsQuery .= ', ' . $event['city'];
                }
                $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapsQuery);
            ?>
            <a href="<?= $mapsUrl ?>" target="_blank" rel="noopener" class="detail-item detail-item--link">
                <div class="detail-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="detail-content">
                    <span class="detail-label"><?= $t->text('venue') ?></span>
                    <span class="detail-value"><?= htmlspecialchars($event['location']) ?> <i class="fas fa-external-link-alt"></i></span>
                </div>
            </a>
            <?php endif; ?>
            <?php if ($event['max_attendees']): ?>
            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="detail-content">
                    <span class="detail-label"><?= $t->text('capacity') ?></span>
                    <span class="detail-value"><?= $event['max_attendees'] ?> <?= $t->text('people') ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- SECTION J: Final CTA (White on Black) -->
<section class="event-cta event-cta--dark">
    <div class="container-wide">
        <div class="cta-final">
            <h2><?= $t->text('get_your_ticket') ?></h2>
            <p><?= $t->text('dont_miss_event') ?></p>
            <a href="/eventos/<?= $event['slug'] ?>/registro" class="btn btn-primary btn-lg">
                <i class="fas fa-ticket-alt"></i> <?= $t->text('register_now') ?>
            </a>
        </div>
    </div>
</section>
