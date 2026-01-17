<?php
/**
 * Landing Themes Index Template
 * Omniwallet CMS
 */
?>

<section class="page-hero">
    <div class="container">
        <h1><?= __('landing_resources_title') ?></h1>
        <p><?= __('landing_resources_subtitle') ?></p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <nav class="breadcrumb">
                <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                <span>/</span>
                <span><?= __('landing_resources') ?></span>
            </nav>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (empty($themes)): ?>
            <div class="empty-message">
                <p><?= __('no_content_available') ?></p>
            </div>
        <?php else: ?>
            <div class="themes-grid">
                <?php foreach ($themes as $theme): ?>
                    <a href="<?= _url('/lp/' . htmlspecialchars($theme['slug']) . '/') ?>" class="theme-card">
                        <div class="theme-icon">
                            <?php if (!empty($theme['icon'])): ?>
                                <i class="<?= htmlspecialchars($theme['icon']) ?>"></i>
                            <?php elseif (!empty($theme['image'])): ?>
                                <img src="<?= htmlspecialchars($theme['image']) ?>" alt="">
                            <?php else: ?>
                                <i class="fas fa-layer-group"></i>
                            <?php endif; ?>
                        </div>
                        <div class="theme-content">
                            <h2><?= htmlspecialchars($theme['title']) ?></h2>
                            <?php if (!empty($theme['subtitle'])): ?>
                                <p class="theme-subtitle"><?= htmlspecialchars($theme['subtitle']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($theme['description'])): ?>
                                <p class="theme-description"><?= htmlspecialchars($theme['description']) ?></p>
                            <?php endif; ?>
                            <span class="theme-count"><?= $theme['landing_count'] ?? 0 ?> <?= __('resources') ?></span>
                        </div>
                        <i class="fas fa-chevron-right theme-arrow"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.themes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: var(--spacing-xl);
}

.theme-card {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    padding: var(--spacing-xl);
    background: var(--color-white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    text-decoration: none;
    color: inherit;
    transition: all var(--transition);
}

.theme-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.theme-icon {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    border-radius: var(--radius-lg);
    flex-shrink: 0;
}

.theme-icon i {
    font-size: 32px;
    color: white;
}

.theme-icon img {
    max-width: 50px;
    max-height: 50px;
    object-fit: contain;
}

.theme-content {
    flex: 1;
}

.theme-content h2 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-xs);
}

.theme-subtitle {
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-sm);
}

.theme-description {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    margin-bottom: var(--spacing-sm);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.theme-count {
    font-size: var(--font-size-sm);
    color: var(--color-primary);
    font-weight: 500;
}

.theme-arrow {
    color: var(--color-gray-400);
    transition: transform var(--transition);
}

.theme-card:hover .theme-arrow {
    transform: translateX(5px);
    color: var(--color-primary);
}

.empty-message {
    text-align: center;
    padding: var(--spacing-3xl);
    color: var(--color-gray-500);
}

@media (max-width: 768px) {
    .themes-grid {
        grid-template-columns: 1fr;
    }

    .theme-card {
        padding: var(--spacing-lg);
    }

    .theme-icon {
        width: 60px;
        height: 60px;
    }

    .theme-icon i {
        font-size: 24px;
    }
}
</style>
