<?php
/**
 * Team Listing Template
 * Omniwallet CMS
 */
?>

<section class="team-page">
    <!-- Hero Section -->
    <div class="team-hero">
        <div class="container">
            <h1>Nuestro Equipo</h1>
            <p class="hero-subtitle">Conoce a las personas que hacen posible Omniwallet</p>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="container">
            <div class="breadcrumb-wrapper">
                <nav class="breadcrumb">
                    <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                    <span>/</span>
                    <span><?= __('team') ?></span>
                </nav>
            </div>
        </div>
    </div>

    <!-- Team Grid -->
    <div class="team-content section">
        <div class="container">
            <?php if (!empty($members)): ?>
                <div class="team-grid">
                    <?php foreach ($members as $member): ?>
                        <a href="<?= _url('/equipo/' . htmlspecialchars($member['slug'])) ?>" class="team-card">
                            <div class="card-photo">
                                <?php if (!empty($member['photo'])): ?>
                                    <img src="<?= htmlspecialchars($member['photo']) ?>"
                                         alt="<?= htmlspecialchars($member['name']) ?>"
                                         class="photo-main">
                                    <?php if (!empty($member['photo_animated'])): ?>
                                        <img src="<?= htmlspecialchars($member['photo_animated']) ?>"
                                             alt="<?= htmlspecialchars($member['name']) ?>"
                                             class="photo-hover">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="photo-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-info">
                                <h3><?= htmlspecialchars($member['name']) ?></h3>
                                <?php if (!empty($member['position'])): ?>
                                    <p class="card-position"><?= htmlspecialchars($member['position']) ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>Proximamente</h3>
                    <p>Estamos preparando esta seccion</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Team Hero */
.team-hero {
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-2xl);
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    text-align: center;
}

.team-hero h1 {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-md);
    color: var(--color-white);
}

.hero-subtitle {
    font-size: var(--font-size-lg);
    color: rgba(255, 255, 255, 0.9);
    max-width: 600px;
    margin: 0 auto;
}

/* Breadcrumb */
.page-breadcrumb {
    padding: var(--spacing-md) 0;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.page-breadcrumb .container {
    max-width: 1200px;
}

.breadcrumb-wrapper {
    max-width: 960px;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    flex-wrap: wrap;
}

.breadcrumb a {
    color: var(--color-gray-500);
    text-decoration: none;
    transition: color var(--transition);
}

.breadcrumb a:hover {
    color: var(--color-primary);
}

.breadcrumb span:last-child {
    color: var(--color-gray-700);
    font-weight: 500;
}

/* Team Grid */
.team-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-xl);
}

.team-card {
    background-color: var(--color-white);
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
    text-align: center;
}

.team-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
}

.card-photo {
    position: relative;
    width: 100%;
    padding-top: 100%;
    overflow: hidden;
    background-color: var(--color-gray-100);
}

.card-photo img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s ease;
}

.card-photo .photo-hover {
    opacity: 0;
}

.team-card:hover .card-photo .photo-main {
    opacity: 0;
}

.team-card:hover .card-photo .photo-hover {
    opacity: 1;
}

.card-photo .photo-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-gray-200);
}

.card-photo .photo-placeholder i {
    font-size: 64px;
    color: var(--color-gray-400);
}

.card-info {
    padding: var(--spacing-lg);
}

.card-info h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-xs);
    color: var(--color-dark);
}

.card-position {
    font-size: var(--font-size-sm);
    color: var(--color-primary);
    font-weight: 500;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: var(--spacing-3xl);
    color: var(--color-gray-500);
}

.empty-state i {
    font-size: 64px;
    margin-bottom: var(--spacing-lg);
    color: var(--color-gray-300);
}

.empty-state h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-sm);
    color: var(--color-gray-600);
}

/* Responsive */
@media (max-width: 1200px) {
    .team-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .team-hero h1 {
        font-size: var(--font-size-3xl);
    }

    .team-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-md);
    }
}

@media (max-width: 480px) {
    .team-grid {
        grid-template-columns: 1fr;
        max-width: 320px;
        margin: 0 auto;
    }
}
</style>
