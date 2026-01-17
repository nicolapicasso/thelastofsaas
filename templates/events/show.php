<?php
/**
 * Event Detail Template
 * TLOS - The Last of SaaS
 */
$startDate = new DateTime($event['start_date']);
$endDate = $event['end_date'] ? new DateTime($event['end_date']) : null;
?>

<section class="event-hero" <?php if ($event['featured_image']): ?>style="background-image: url('<?= htmlspecialchars($event['featured_image']) ?>');"<?php endif; ?>>
    <div class="event-hero__overlay">
        <div class="container">
            <div class="event-hero__content">
                <div class="event-hero__date">
                    <span class="day"><?= $startDate->format('d') ?></span>
                    <span class="month"><?= strtoupper($startDate->format('M')) ?></span>
                    <span class="year"><?= $startDate->format('Y') ?></span>
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
                        <i class="fas fa-ticket-alt"></i> Conseguir entrada
                    </a>
                </div>
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
                        <h2>Sobre el evento</h2>
                        <div class="prose">
                            <?= $event['description'] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Features -->
                <?php if (!empty($features)): ?>
                    <div class="event-section">
                        <h2>Qué incluye</h2>
                        <ul class="features-list">
                            <?php foreach ($features as $feature): ?>
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    <span><?= htmlspecialchars($feature['feature']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Sponsors -->
                <?php if (!empty($sponsorsByLevel)): ?>
                    <div class="event-section">
                        <h2>Sponsors</h2>
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
                                <h3><?= $levelName ?></h3>
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
                    <h3>Entradas</h3>
                    <?php if (empty($ticketTypes)): ?>
                        <p class="text-muted">Las entradas estarán disponibles próximamente</p>
                    <?php else: ?>
                        <?php foreach ($ticketTypes as $type): ?>
                            <div class="ticket-type">
                                <div class="ticket-type__info">
                                    <strong><?= htmlspecialchars($type['name']) ?></strong>
                                    <span class="price">
                                        <?php if ($type['price'] > 0): ?>
                                            <?= number_format($type['price'], 2) ?> €
                                        <?php else: ?>
                                            Gratis
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <?php if ($type['description']): ?>
                                    <p class="ticket-type__desc"><?= htmlspecialchars($type['description']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <a href="/eventos/<?= $event['slug'] ?>/registro" class="btn btn-primary btn-block">
                            Registrarse
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Event Info Card -->
                <div class="sidebar-card">
                    <h3>Detalles</h3>
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
                                <strong>Dirección</strong>
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
                    <h3>Compartir</h3>
                    <div class="share-buttons">
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(url('/eventos/' . $event['slug'])) ?>&text=<?= urlencode($event['name']) ?>" target="_blank" class="share-btn share-btn--twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(url('/eventos/' . $event['slug'])) ?>&title=<?= urlencode($event['name']) ?>" target="_blank" class="share-btn share-btn--linkedin">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="https://wa.me/?text=<?= urlencode($event['name'] . ' ' . url('/eventos/' . $event['slug'])) ?>" target="_blank" class="share-btn share-btn--whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<style>
.event-hero {
    min-height: 400px;
    background-size: cover;
    background-position: center;
    position: relative;
}
.event-hero__overlay {
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.95) 0%, rgba(124, 58, 237, 0.9) 100%);
    min-height: 400px;
    display: flex;
    align-items: center;
    color: white;
}
.event-hero__content {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
}
.event-hero__date {
    background: white;
    color: var(--primary-color);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    text-align: center;
    min-width: 100px;
}
.event-hero__date .day {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
}
.event-hero__date .month {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
}
.event-hero__date .year {
    display: block;
    font-size: 0.85rem;
    color: var(--text-secondary);
}
.event-hero__info h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}
.event-hero__info .lead {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 1.5rem;
}
.event-hero__meta {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}
.event-hero__meta i {
    margin-right: 0.5rem;
}

.event-content {
    padding: 4rem 0;
}
.event-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 3rem;
}
.event-section {
    margin-bottom: 3rem;
}
.event-section h2 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-color);
}
.prose {
    line-height: 1.8;
    color: var(--text-secondary);
}
.prose p {
    margin-bottom: 1rem;
}

.features-list {
    list-style: none;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}
.features-list li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--bg-secondary);
    border-radius: 8px;
}
.features-list i {
    color: var(--success-color);
    font-size: 1.25rem;
}

.sponsors-level {
    margin-bottom: 2rem;
}
.sponsors-level h3 {
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1rem;
    color: var(--text-secondary);
}
.sponsors-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}
.sponsor-card {
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.sponsors-level--platinum .sponsor-card {
    min-width: 180px;
    min-height: 80px;
}
.sponsors-level--gold .sponsor-card {
    min-width: 150px;
    min-height: 70px;
}
.sponsors-level--silver .sponsor-card,
.sponsors-level--bronze .sponsor-card {
    min-width: 120px;
    min-height: 60px;
}
.sponsor-card img {
    max-width: 100%;
    max-height: 50px;
    object-fit: contain;
}

.sidebar-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.sidebar-card h3 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border-color);
}

.ticket-type {
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}
.ticket-type:last-of-type {
    border-bottom: none;
    margin-bottom: 1rem;
}
.ticket-type__info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
}
.ticket-type .price {
    font-weight: 700;
    color: var(--primary-color);
}
.ticket-type__desc {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin: 0;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.info-list li {
    display: flex;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}
.info-list li:last-child {
    border-bottom: none;
}
.info-list i {
    width: 20px;
    text-align: center;
    color: var(--primary-color);
    margin-top: 0.25rem;
}
.info-list strong {
    display: block;
    font-size: 0.85rem;
    color: var(--text-secondary);
}
.info-list span {
    font-size: 0.95rem;
}

.share-buttons {
    display: flex;
    gap: 0.75rem;
}
.share-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: transform 0.2s;
}
.share-btn:hover {
    transform: scale(1.1);
}
.share-btn--twitter { background: #1DA1F2; }
.share-btn--linkedin { background: #0077B5; }
.share-btn--whatsapp { background: #25D366; }

@media (max-width: 992px) {
    .event-layout {
        grid-template-columns: 1fr;
    }
    .event-hero__content {
        flex-direction: column;
        text-align: center;
        align-items: center;
    }
    .event-hero__meta {
        justify-content: center;
    }
}
</style>
