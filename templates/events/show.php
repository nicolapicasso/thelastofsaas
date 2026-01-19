<?php
/**
 * Event Detail Template
 * TLOS - The Last of SaaS
 * Redesigned with black/white balance and full-width layout
 */
$startDate = new DateTime($event['start_date']);
$endDate = $event['end_date'] ? new DateTime($event['end_date']) : null;
?>

<!-- SECTION A: Hero with Title (Black on White) -->
<section class="event-hero-minimal">
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
            </div>
        </div>
    </div>
</section>

<!-- SECTION B: Short Description (White on Black) -->
<?php if ($event['short_description']): ?>
<section class="event-intro">
    <div class="container-wide">
        <p class="intro-text"><?= htmlspecialchars($event['short_description']) ?></p>
    </div>
</section>
<?php endif; ?>

<!-- SECTION C: Get Ticket CTA (Black on White) -->
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
                            DESDE GRATIS
                        <?php elseif ($minPrice == $maxPrice): ?>
                            <?= number_format($minPrice, 0) ?>€
                        <?php else: ?>
                            DESDE <?= number_format($minPrice, 0) ?>€
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
                <span class="cta-label">PLAZAS LIMITADAS</span>
            </div>
            <a href="/eventos/<?= $event['slug'] ?>/registro" class="btn btn-dark btn-lg">
                <i class="fas fa-ticket-alt"></i> CONSEGUIR ENTRADA
            </a>
        </div>
    </div>
</section>

<!-- SECTION D: Long Description (White on Black) -->
<?php if ($event['description']): ?>
<section class="event-description">
    <div class="container-wide">
        <h2>SOBRE EL EVENTO</h2>
        <div class="description-content">
            <?= $event['description'] ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- SECTION E: Participating Companies (Black on White) -->
<?php if (!empty($companies)): ?>
<section class="event-companies">
    <div class="container-wide">
        <h2>NOS ACOMPAÑARAN</h2>
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

<!-- SECTION F: Agenda (White on Black) -->
<?php if (!empty($activitiesByDate)): ?>
<section class="event-agenda">
    <div class="container-wide">
        <h2>AGENDA</h2>
        <?php foreach ($activitiesByDate as $date => $dateActivities): ?>
            <div class="agenda-day">
                <h3 class="agenda-date">
                    <i class="fas fa-calendar-day"></i>
                    <?= date('d M Y', strtotime($date)) ?>
                </h3>
                <div class="agenda-timeline">
                    <?php foreach ($dateActivities as $activity): ?>
                        <div class="agenda-item <?= $activity['is_featured'] ? 'featured' : '' ?>">
                            <div class="agenda-time">
                                <span class="time-start"><?= substr($activity['start_time'] ?? '00:00', 0, 5) ?></span>
                                <?php if (!empty($activity['end_time'])): ?>
                                    <span class="time-end"><?= substr($activity['end_time'], 0, 5) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="agenda-content">
                                <span class="agenda-type" style="<?= !empty($activity['category_color']) ? 'background-color:' . $activity['category_color'] : '' ?>">
                                    <?= htmlspecialchars($activity['category_name'] ?? $activity['activity_type'] ?? 'Actividad') ?>
                                </span>
                                <h4><?= htmlspecialchars($activity['title'] ?? '') ?></h4>
                                <?php if (!empty($activity['description'])): ?>
                                    <p><?= htmlspecialchars($activity['description']) ?></p>
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
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- SECTION G: Speakers (Black on White) -->
<?php if (!empty($speakers)): ?>
<section class="event-speakers">
    <div class="container-wide">
        <h2>SPEAKERS</h2>
        <div class="speakers-grid">
            <?php foreach ($speakers as $speaker): ?>
                <a href="/speakers/<?= $speaker['slug'] ?? $speaker['id'] ?>" class="speaker-card">
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
                            <span class="speaker-company"><?= htmlspecialchars($speaker['company']) ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- SECTION H: Sponsors by Level (Black on White) -->
<?php if (!empty($sponsorsByLevel)): ?>
<section class="event-sponsors-section">
    <div class="container-wide">
        <h2>SPONSORS</h2>
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
        <h2>DETALLES DEL EVENTO</h2>
        <div class="details-grid">
            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="detail-content">
                    <span class="detail-label">FECHA</span>
                    <span class="detail-value"><?= $startDate->format('d/m/Y') ?><?php if ($endDate && $endDate != $startDate): ?> - <?= $endDate->format('d/m/Y') ?><?php endif; ?></span>
                </div>
            </div>
            <?php if ($event['start_time']): ?>
            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="detail-content">
                    <span class="detail-label">HORA</span>
                    <span class="detail-value"><?= substr($event['start_time'], 0, 5) ?><?php if ($event['end_time']): ?> - <?= substr($event['end_time'], 0, 5) ?><?php endif; ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($event['location']): ?>
            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="detail-content">
                    <span class="detail-label">LUGAR</span>
                    <span class="detail-value"><?= htmlspecialchars($event['location']) ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($event['address']): ?>
            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-directions"></i>
                </div>
                <div class="detail-content">
                    <span class="detail-label">DIRECCION</span>
                    <span class="detail-value"><?= htmlspecialchars($event['address']) ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($event['max_attendees']): ?>
            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="detail-content">
                    <span class="detail-label">AFORO</span>
                    <span class="detail-value"><?= $event['max_attendees'] ?> personas</span>
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
            <h2>CONSIGUE TU ENTRADA</h2>
            <p>No te pierdas este evento. Las plazas son limitadas.</p>
            <a href="/eventos/<?= $event['slug'] ?>/registro" class="btn btn-primary btn-lg">
                <i class="fas fa-ticket-alt"></i> REGISTRARSE AHORA
            </a>
        </div>
    </div>
</section>
