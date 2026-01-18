<?php
/**
 * Event Detail Template
 * TLOS - The Last of SaaS
 */
$startDate = new DateTime($event['start_date']);
$endDate = $event['end_date'] ? new DateTime($event['end_date']) : null;
?>

<section class="event-hero" <?php if ($event['featured_image']): ?>style="background-image: url('<?= htmlspecialchars($event['featured_image']) ?>');"<?php endif; ?>>
    <div class="event-hero__overlay"></div>
    <div class="container">
        <div class="event-hero__content">
            <div class="event-hero__date">
                <div class="event-hero__date-inner">
                    <span class="day"><?= $startDate->format('d') ?></span>
                    <span class="month"><?= strtoupper($startDate->format('M')) ?></span>
                    <span class="year"><?= $startDate->format('Y') ?></span>
                </div>
            </div>
            <div class="event-hero__info">
                <h1><?= htmlspecialchars($event['name']) ?></h1>
                <?php if ($event['short_description']): ?>
                    <p class="lead"><?= htmlspecialchars($event['short_description']) ?></p>
                <?php endif; ?>
                <div class="event-hero__meta">
                    <?php if ($event['location']): ?>
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></span>
                    <?php endif; ?>
                    <?php if ($event['start_time']): ?>
                        <span><i class="fas fa-clock"></i> <?= substr($event['start_time'], 0, 5) ?></span>
                    <?php endif; ?>
                </div>
                <a href="/eventos/<?= $event['slug'] ?>/registro" class="btn btn-primary btn-lg">
                    <i class="fas fa-ticket-alt"></i> CONSEGUIR ENTRADA
                </a>
            </div>
        </div>
    </div>
</section>

<section class="event-content">
    <div class="container">
        <div class="event-layout">
            <!-- Main Content -->
            <div class="event-main">
                <!-- Description -->
                <?php if ($event['description']): ?>
                    <div class="event-section">
                        <h2>SOBRE EL EVENTO</h2>
                        <div class="prose">
                            <?= $event['description'] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Features -->
                <?php if (!empty($features)): ?>
                    <div class="event-section">
                        <h2>QUE INCLUYE</h2>
                        <ul class="features-list">
                            <?php foreach ($features as $feature): ?>
                                <li>
                                    <i class="fas fa-check"></i>
                                    <span><?= htmlspecialchars($feature['feature']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Sponsors -->
                <?php if (!empty($sponsorsByLevel)): ?>
                    <div class="event-section">
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
                                        <div class="sponsor-card">
                                            <?php if ($sponsor['logo_url']): ?>
                                                <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="<?= htmlspecialchars($sponsor['name']) ?>">
                                            <?php else: ?>
                                                <span class="sponsor-name"><?= htmlspecialchars($sponsor['name']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="event-sidebar">
                <!-- Ticket Card -->
                <div class="sidebar-card">
                    <h3>ENTRADAS</h3>
                    <?php if (empty($ticketTypes)): ?>
                        <p class="text-muted">Las entradas estaran disponibles proximamente</p>
                    <?php else: ?>
                        <?php foreach ($ticketTypes as $type): ?>
                            <div class="ticket-type">
                                <div class="ticket-type__info">
                                    <strong><?= htmlspecialchars($type['name']) ?></strong>
                                    <span class="price">
                                        <?php if ($type['price'] > 0): ?>
                                            <?= number_format($type['price'], 2) ?> EUR
                                        <?php else: ?>
                                            GRATIS
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <?php if ($type['description']): ?>
                                    <p class="ticket-type__desc"><?= htmlspecialchars($type['description']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <a href="/eventos/<?= $event['slug'] ?>/registro" class="btn btn-primary btn-block">
                            REGISTRARSE
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Event Info Card -->
                <div class="sidebar-card">
                    <h3>DETALLES</h3>
                    <ul class="info-list">
                        <li>
                            <i class="fas fa-calendar"></i>
                            <div>
                                <strong>Fecha</strong>
                                <span><?= $startDate->format('d/m/Y') ?><?php if ($endDate && $endDate != $startDate): ?> - <?= $endDate->format('d/m/Y') ?><?php endif; ?></span>
                            </div>
                        </li>
                        <?php if ($event['start_time']): ?>
                        <li>
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Hora</strong>
                                <span><?= substr($event['start_time'], 0, 5) ?><?php if ($event['end_time']): ?> - <?= substr($event['end_time'], 0, 5) ?><?php endif; ?></span>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if ($event['location']): ?>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Lugar</strong>
                                <span><?= htmlspecialchars($event['location']) ?></span>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if ($event['address']): ?>
                        <li>
                            <i class="fas fa-directions"></i>
                            <div>
                                <strong>Direccion</strong>
                                <span><?= htmlspecialchars($event['address']) ?></span>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if ($event['max_attendees']): ?>
                        <li>
                            <i class="fas fa-users"></i>
                            <div>
                                <strong>Aforo</strong>
                                <span><?= $event['max_attendees'] ?> personas</span>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Share Card -->
                <div class="sidebar-card">
                    <h3>COMPARTIR</h3>
                    <div class="share-buttons">
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(($_ENV['APP_URL'] ?? '') . '/eventos/' . $event['slug']) ?>&text=<?= urlencode($event['name']) ?>" target="_blank" class="share-btn">
                            <i class="fab fa-x-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(($_ENV['APP_URL'] ?? '') . '/eventos/' . $event['slug']) ?>&title=<?= urlencode($event['name']) ?>" target="_blank" class="share-btn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://wa.me/?text=<?= urlencode($event['name'] . ' ' . ($_ENV['APP_URL'] ?? '') . '/eventos/' . $event['slug']) ?>" target="_blank" class="share-btn">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
