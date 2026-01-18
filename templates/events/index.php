<?php
/**
 * Events List Template
 * TLOS - The Last of SaaS
 */
?>

<section class="events-hero">
    <div class="container">
        <h1>Próximos Eventos</h1>
        <p class="lead">Descubre los mejores eventos de networking B2B y SaaS</p>
    </div>
</section>

<section class="events-list">
    <div class="container">
        <?php if (empty($events)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-alt"></i>
                <h2>No hay eventos programados</h2>
                <p>Vuelve pronto para descubrir nuevos eventos</p>
            </div>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <article class="event-card">
                        <?php if ($event['featured_image']): ?>
                            <div class="event-card__image">
                                <img src="<?= htmlspecialchars($event['featured_image']) ?>" alt="<?= htmlspecialchars($event['name']) ?>">
                                <?php if ($event['is_featured']): ?>
                                    <span class="badge badge-featured">Destacado</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="event-card__content">
                            <div class="event-card__date">
                                <span class="day"><?= date('d', strtotime($event['start_date'])) ?></span>
                                <span class="month"><?= strtoupper(date('M', strtotime($event['start_date']))) ?></span>
                            </div>

                            <h2 class="event-card__title">
                                <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>"><?= htmlspecialchars($event['name']) ?></a>
                            </h2>

                            <?php if ($event['short_description']): ?>
                                <p class="event-card__excerpt"><?= htmlspecialchars($event['short_description']) ?></p>
                            <?php endif; ?>

                            <div class="event-card__meta">
                                <?php if ($event['location']): ?>
                                    <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></span>
                                <?php endif; ?>
                                <?php if ($event['max_attendees']): ?>
                                    <span><i class="fas fa-users"></i> <?= $event['max_attendees'] ?> plazas</span>
                                <?php endif; ?>
                            </div>

                            <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>" class="btn btn-primary">Ver evento</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.events-hero {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
}
.events-hero h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}
.events-hero .lead {
    font-size: 1.25rem;
    opacity: 0.9;
}

.events-list {
    padding: 4rem 0;
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
}

.event-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}
.event-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.event-card__image {
    position: relative;
    height: 200px;
    overflow: hidden;
}
.event-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.event-card__image .badge-featured {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: var(--warning-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.event-card__content {
    padding: 1.5rem;
    position: relative;
}

.event-card__date {
    position: absolute;
    top: -2rem;
    left: 1.5rem;
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
}
.event-card__date .day {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
}
.event-card__date .month {
    display: block;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.event-card__title {
    font-size: 1.25rem;
    margin: 1rem 0;
    padding-top: 1rem;
}
.event-card__title a {
    color: inherit;
    text-decoration: none;
}
.event-card__title a:hover {
    color: var(--primary-color);
}

.event-card__excerpt {
    color: var(--text-secondary);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.event-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 1.5rem;
}
.event-card__meta i {
    margin-right: 0.25rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}
.empty-state i {
    font-size: 4rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}
.empty-state h2 {
    margin-bottom: 0.5rem;
}
.empty-state p {
    color: var(--text-secondary);
}

@media (max-width: 768px) {
    .events-grid {
        grid-template-columns: 1fr;
    }
}
</style>
