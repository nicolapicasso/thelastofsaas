<?php
/**
 * Company Public Page Template
 * TLOS - The Last of SaaS
 */
?>

<!-- Hero Section -->
<section class="company-hero">
    <div class="container-wide">
        <div class="company-hero-content">
            <?php if (!empty($company['logo_url'])): ?>
                <div class="company-hero-logo">
                    <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="<?= htmlspecialchars($company['name']) ?>">
                </div>
            <?php endif; ?>
            <div class="company-hero-info">
                <h1><?= htmlspecialchars($company['name']) ?></h1>
                <?php if (!empty($company['sector'])): ?>
                    <span class="company-sector"><?= htmlspecialchars($company['sector']) ?></span>
                <?php endif; ?>
                <div class="company-meta">
                    <?php if (!empty($company['employees'])): ?>
                        <span class="meta-item"><i class="fas fa-users"></i> <?= htmlspecialchars($company['employees']) ?> empleados</span>
                    <?php endif; ?>
                    <?php if (!empty($company['website'])): ?>
                        <span class="meta-item"><i class="fas fa-globe"></i> <a href="<?= htmlspecialchars($company['website']) ?>" target="_blank" rel="noopener"><?= parse_url($company['website'], PHP_URL_HOST) ?></a></span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($company['website'])): ?>
                <div class="company-links">
                    <a href="<?= htmlspecialchars($company['website']) ?>" target="_blank" rel="noopener" class="btn btn-primary">
                        <i class="fas fa-globe"></i> Visitar Web
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Description Section -->
<?php if (!empty($company['description'])): ?>
<section class="company-description">
    <div class="container-wide">
        <h2>SOBRE <?= strtoupper(htmlspecialchars($company['name'])) ?></h2>
        <div class="description-content">
            <?= nl2br(htmlspecialchars($company['description'])) ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Events Section -->
<?php if (!empty($events)): ?>
<section class="company-events">
    <div class="container-wide">
        <h2>EVENTOS</h2>
        <p class="section-subtitle">Participa con nosotros en estos eventos</p>
        <div class="events-grid">
            <?php foreach ($events as $event): ?>
                <?php $eventDate = new DateTime($event['start_date']); ?>
                <a href="/eventos/<?= $event['slug'] ?>" class="event-card">
                    <div class="event-card-date">
                        <span class="day"><?= $eventDate->format('d') ?></span>
                        <span class="month"><?= strtoupper($eventDate->format('M')) ?></span>
                    </div>
                    <div class="event-card-info">
                        <h3><?= htmlspecialchars($event['name']) ?></h3>
                        <?php if (!empty($event['location'])): ?>
                            <span class="event-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact CTA -->
<section class="company-cta">
    <div class="container-wide">
        <div class="cta-content">
            <h2>CONECTA CON <?= strtoupper(htmlspecialchars($company['name'])) ?></h2>
            <p>Descubre como pueden colaborar contigo</p>
            <?php if (!empty($company['website'])): ?>
                <a href="<?= htmlspecialchars($company['website']) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-lg">
                    <i class="fas fa-external-link-alt"></i> VISITAR WEB
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Company Page Specific Styles */
.company-hero {
    background: var(--bg-light);
    color: var(--text-dark);
    padding: 140px 0 80px;
}

.company-hero-content {
    display: flex;
    align-items: center;
    gap: 4rem;
}

.company-hero-logo {
    flex-shrink: 0;
    background: var(--bg-light);
    border: 2px solid var(--border-dark);
    padding: 2rem;
    min-width: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.company-hero-logo img {
    max-width: 180px;
    max-height: 120px;
    object-fit: contain;
}

.company-hero-info {
    flex: 1;
}

.company-hero-info h1 {
    font-size: clamp(32px, 5vw, 56px);
    margin-bottom: 0.5rem;
}

.company-sector {
    display: inline-block;
    font-family: var(--font-mono);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 0.5rem 1rem;
    background: var(--bg-dark);
    color: var(--text-light);
    margin-bottom: 1.5rem;
}

.company-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.meta-item {
    font-family: var(--font-mono);
    font-size: 13px;
    color: var(--text-grey-dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.meta-item a {
    color: var(--text-dark);
    text-decoration: none;
}

.meta-item a:hover {
    text-decoration: underline;
}

.company-links {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.company-description {
    background: var(--bg-dark);
    color: var(--text-light);
    padding: 100px 0;
}

.company-description h2 {
    font-size: clamp(28px, 4vw, 40px);
    margin-bottom: 3rem;
    position: relative;
    display: inline-block;
}

.company-description h2::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 0;
    width: 60px;
    height: 3px;
    background: var(--text-light);
}

.company-description .description-content {
    font-size: 18px;
    line-height: 1.9;
    color: var(--text-light);
}

.company-events {
    background: var(--bg-light);
    color: var(--text-dark);
    padding: 100px 0;
}

.company-events h2 {
    font-size: clamp(28px, 4vw, 40px);
    margin-bottom: 0.5rem;
    position: relative;
    display: inline-block;
}

.company-events h2::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 0;
    width: 60px;
    height: 3px;
    background: var(--text-dark);
}

.section-subtitle {
    color: var(--text-grey-dark);
    font-family: var(--font-mono);
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 3rem;
    margin-top: 1.5rem;
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
}

.event-card {
    display: flex;
    gap: 1.5rem;
    padding: 2rem;
    background: var(--bg-light);
    border: 2px solid var(--border-dark);
    text-decoration: none;
    transition: var(--transition);
}

.event-card:hover {
    border-color: var(--bg-dark);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.event-card-date {
    flex-shrink: 0;
    background: var(--bg-dark);
    color: var(--text-light);
    padding: 1rem 1.5rem;
    text-align: center;
}

.event-card-date .day {
    display: block;
    font-family: var(--font-accent);
    font-size: 36px;
    font-weight: 700;
    line-height: 1;
}

.event-card-date .month {
    display: block;
    font-family: var(--font-mono);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-top: 0.25rem;
}

.event-card-info {
    flex: 1;
}

.event-card-info h3 {
    font-size: 18px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    color: var(--text-dark);
}

.event-location {
    display: block;
    font-family: var(--font-mono);
    font-size: 12px;
    color: var(--text-grey-dark);
}

.company-cta {
    background: var(--bg-dark);
    color: var(--text-light);
    padding: 100px 0;
}

.company-cta .cta-content {
    text-align: center;
}

.company-cta h2 {
    font-size: clamp(32px, 5vw, 48px);
    margin-bottom: 1rem;
}

.company-cta p {
    font-family: var(--font-mono);
    font-size: 14px;
    color: var(--text-grey);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 2.5rem;
}

@media (max-width: 768px) {
    .company-hero {
        padding: 120px 0 60px;
    }

    .company-hero-content {
        flex-direction: column;
        text-align: center;
        gap: 2rem;
    }

    .company-meta {
        justify-content: center;
    }

    .company-links {
        justify-content: center;
    }

    .events-grid {
        grid-template-columns: 1fr;
    }

    .event-card {
        flex-direction: column;
        text-align: center;
    }

    .event-card-date {
        align-self: center;
    }
}
</style>
