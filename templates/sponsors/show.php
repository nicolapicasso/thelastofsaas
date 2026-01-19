<?php
/**
 * Sponsor Public Page Template
 * TLOS - The Last of SaaS
 */
?>

<!-- Hero Section -->
<section class="sponsor-hero">
    <div class="container-wide">
        <div class="sponsor-hero-content">
            <?php if (!empty($sponsor['logo_url'])): ?>
                <div class="sponsor-hero-logo">
                    <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="<?= htmlspecialchars($sponsor['name']) ?>">
                </div>
            <?php endif; ?>
            <div class="sponsor-hero-info">
                <h1><?= htmlspecialchars($sponsor['name']) ?></h1>
                <?php if (!empty($sponsor['tagline'])): ?>
                    <p class="sponsor-tagline"><?= htmlspecialchars($sponsor['tagline']) ?></p>
                <?php endif; ?>
                <div class="sponsor-links">
                    <?php if (!empty($sponsor['website'])): ?>
                        <a href="<?= htmlspecialchars($sponsor['website']) ?>" target="_blank" rel="noopener" class="btn btn-primary">
                            <i class="fas fa-globe"></i> Visitar Web
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($sponsor['linkedin_url'])): ?>
                        <a href="<?= htmlspecialchars($sponsor['linkedin_url']) ?>" target="_blank" rel="noopener" class="btn btn-outline">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($sponsor['twitter_url'])): ?>
                        <a href="<?= htmlspecialchars($sponsor['twitter_url']) ?>" target="_blank" rel="noopener" class="btn btn-outline">
                            <i class="fab fa-twitter"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Description Section -->
<?php if (!empty($sponsor['description'])): ?>
<section class="sponsor-description">
    <div class="container-wide">
        <h2>SOBRE <?= strtoupper(htmlspecialchars($sponsor['name'])) ?></h2>
        <div class="description-content">
            <?= nl2br(htmlspecialchars($sponsor['description'])) ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Events Section -->
<?php if (!empty($events)): ?>
<section class="sponsor-events">
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
                        <span class="event-level badge-<?= $event['level'] ?? 'bronze' ?>"><?= strtoupper($event['level'] ?? 'SPONSOR') ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact CTA -->
<section class="sponsor-cta">
    <div class="container-wide">
        <div class="cta-content">
            <h2>CONECTA CON <?= strtoupper(htmlspecialchars($sponsor['name'])) ?></h2>
            <p>Descubre como pueden ayudarte a impulsar tu negocio</p>
            <?php if (!empty($sponsor['website'])): ?>
                <a href="<?= htmlspecialchars($sponsor['website']) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-lg">
                    <i class="fas fa-external-link-alt"></i> VISITAR WEB
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Sponsor Page Specific Styles */
.sponsor-hero {
    background: var(--bg-light);
    color: var(--text-dark);
    padding: 140px 0 80px;
}

.sponsor-hero-content {
    display: flex;
    align-items: center;
    gap: 4rem;
}

.sponsor-hero-logo {
    flex-shrink: 0;
    background: var(--bg-light);
    border: 2px solid var(--border-dark);
    padding: 2rem;
    min-width: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sponsor-hero-logo img {
    max-width: 180px;
    max-height: 120px;
    object-fit: contain;
}

.sponsor-hero-info {
    flex: 1;
}

.sponsor-hero-info h1 {
    font-size: clamp(32px, 5vw, 56px);
    margin-bottom: 1rem;
}

.sponsor-tagline {
    font-size: 18px;
    color: var(--text-grey-dark);
    margin-bottom: 1.5rem;
}

.sponsor-links {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.sponsor-links .btn-outline {
    background: transparent;
    border: 2px solid var(--bg-dark);
    color: var(--bg-dark);
    padding: 0.75rem 1rem;
}

.sponsor-links .btn-outline:hover {
    background: var(--bg-dark);
    color: var(--text-light);
}

.sponsor-description {
    background: var(--bg-dark);
    color: var(--text-light);
    padding: 100px 0;
}

.sponsor-description h2 {
    font-size: clamp(28px, 4vw, 40px);
    margin-bottom: 3rem;
    position: relative;
    display: inline-block;
}

.sponsor-description h2::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 0;
    width: 60px;
    height: 3px;
    background: var(--text-light);
}

.sponsor-description .description-content {
    font-size: 18px;
    line-height: 1.9;
    color: var(--text-grey);
}

.sponsor-events {
    background: var(--bg-light);
    color: var(--text-dark);
    padding: 100px 0;
}

.sponsor-events h2 {
    font-size: clamp(28px, 4vw, 40px);
    margin-bottom: 0.5rem;
    position: relative;
    display: inline-block;
}

.sponsor-events h2::after {
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
    margin-bottom: 1rem;
}

.event-level {
    display: inline-block;
    font-family: var(--font-mono);
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 0.25rem 0.75rem;
}

.badge-platinum { background: #E5E4E2; color: #333; }
.badge-gold { background: #FFD700; color: #333; }
.badge-silver { background: #C0C0C0; color: #333; }
.badge-bronze { background: #CD7F32; color: #fff; }

.sponsor-cta {
    background: var(--bg-dark);
    color: var(--text-light);
    padding: 100px 0;
}

.sponsor-cta .cta-content {
    text-align: center;
}

.sponsor-cta h2 {
    font-size: clamp(32px, 5vw, 48px);
    margin-bottom: 1rem;
}

.sponsor-cta p {
    font-family: var(--font-mono);
    font-size: 14px;
    color: var(--text-grey);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 2.5rem;
}

@media (max-width: 768px) {
    .sponsor-hero {
        padding: 120px 0 60px;
    }

    .sponsor-hero-content {
        flex-direction: column;
        text-align: center;
        gap: 2rem;
    }

    .sponsor-links {
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
