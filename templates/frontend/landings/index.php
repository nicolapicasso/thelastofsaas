<?php
/**
 * Landings Index Template (by theme)
 * Omniwallet CMS
 */
?>

<section class="page-hero">
    <div class="container">
        <div class="hero-with-icon">
            <?php if (!empty($theme['icon'])): ?>
                <div class="hero-icon">
                    <i class="<?= htmlspecialchars($theme['icon']) ?>"></i>
                </div>
            <?php endif; ?>
            <div>
                <h1><?= htmlspecialchars($theme['title']) ?></h1>
                <?php if (!empty($theme['subtitle'])): ?>
                    <p class="hero-subtitle"><?= htmlspecialchars($theme['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!empty($theme['description'])): ?>
            <p class="hero-description"><?= htmlspecialchars($theme['description']) ?></p>
        <?php endif; ?>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <nav class="breadcrumb">
                <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                <span>/</span>
                <a href="<?= _url('/lp/') ?>"><?= __('landing_resources') ?></a>
                <span>/</span>
                <span><?= htmlspecialchars($theme['title']) ?></span>
            </nav>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (empty($landings)): ?>
            <div class="empty-message">
                <p><?= __('no_content_available') ?></p>
            </div>
        <?php else: ?>
            <div class="landings-grid">
                <?php foreach ($landings as $landing): ?>
                    <a href="<?= _url('/lp/' . htmlspecialchars($theme['slug']) . '/' . htmlspecialchars($landing['slug'])) ?>" class="landing-card">
                        <?php if (!empty($landing['image'])): ?>
                            <div class="landing-image">
                                <img src="<?= htmlspecialchars($landing['image']) ?>" alt="<?= htmlspecialchars($landing['title']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="landing-content">
                            <div class="landing-icon">
                                <?php if (!empty($landing['icon'])): ?>
                                    <i class="<?= htmlspecialchars($landing['icon']) ?>"></i>
                                <?php else: ?>
                                    <i class="fas fa-file-alt"></i>
                                <?php endif; ?>
                            </div>
                            <h2><?= htmlspecialchars($landing['title']) ?></h2>
                            <?php if (!empty($landing['subtitle'])): ?>
                                <p class="landing-subtitle"><?= htmlspecialchars($landing['subtitle']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($landing['description'])): ?>
                                <p class="landing-description"><?= htmlspecialchars($landing['description']) ?></p>
                            <?php endif; ?>
                            <span class="landing-cta"><?= __('view_more') ?> <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.breadcrumb {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-lg);
}

.breadcrumb a {
    color: var(--color-primary-light);
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb .separator {
    color: var(--color-gray-400);
}

.hero-with-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: var(--spacing-lg);
}

.hero-icon {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-lg);
}

.hero-icon i {
    font-size: 36px;
    color: white;
}

.hero-subtitle {
    font-size: var(--font-size-xl);
    opacity: 0.9;
    margin-top: var(--spacing-sm);
}

.hero-description {
    width: 100%;
    margin-top: var(--spacing-lg);
    opacity: 0.85;
    text-align: center;
}

.landings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--spacing-xl);
}

.landing-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    text-decoration: none;
    color: inherit;
    transition: all var(--transition);
    display: flex;
    flex-direction: column;
}

.landing-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.landing-image {
    height: 180px;
    overflow: hidden;
}

.landing-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition);
}

.landing-card:hover .landing-image img {
    transform: scale(1.05);
}

.landing-content {
    padding: var(--spacing-xl);
    flex: 1;
    display: flex;
    flex-direction: column;
}

.landing-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-md);
}

.landing-icon i {
    font-size: 20px;
    color: white;
}

.landing-content h2 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-sm);
}

.landing-subtitle {
    color: var(--color-primary);
    font-weight: 500;
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-sm);
}

.landing-description {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    line-height: 1.6;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.landing-cta {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    color: var(--color-primary);
    font-weight: 600;
    font-size: var(--font-size-sm);
    margin-top: var(--spacing-md);
}

.landing-cta i {
    transition: transform var(--transition);
}

.landing-card:hover .landing-cta i {
    transform: translateX(5px);
}

.empty-message {
    text-align: center;
    padding: var(--spacing-3xl);
    color: var(--color-gray-500);
}

@media (max-width: 768px) {
    .landings-grid {
        grid-template-columns: 1fr;
    }
}
</style>
