<?php
/**
 * Services Index Template
 * We're Sinapsis CMS
 */
?>

<!-- Services Header -->
<section class="page-hero">
    <div class="container">
        <h1><?= __('services_title') ?></h1>
        <p><?= __('services_subtitle') ?></p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
            <span>/</span>
            <span><?= __('services') ?></span>
        </nav>
    </div>
</div>

<!-- Category Filter -->
<?php if (!empty($categories)): ?>
<section class="category-filter">
    <div class="container">
        <div class="filter-tabs">
            <a href="<?= _url('/servicios') ?>" class="filter-tab <?= empty($currentCategory) ? 'active' : '' ?>">
                <?= __('all') ?>
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= _url('/servicios?categoria=' . $cat['slug']) ?>"
                   class="filter-tab <?= ($currentCategory['id'] ?? '') == $cat['id'] ? 'active' : '' ?>">
                    <?php if (!empty($cat['icon'])): ?>
                        <i class="<?= htmlspecialchars($cat['icon']) ?>"></i>
                    <?php endif; ?>
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Services -->
<?php if (!empty($featured) && empty($currentCategory)): ?>
<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <h2><?= __('featured_services') ?></h2>
        </div>
        <div class="featured-grid">
            <?php foreach ($featured as $service): ?>
                <a href="<?= _url('/servicios/' . $service['slug']) ?>" class="featured-card">
                    <div class="featured-icon">
                        <?php if (!empty($service['icon'])): ?>
                            <i class="<?= htmlspecialchars($service['icon']) ?>"></i>
                        <?php elseif (!empty($service['image'])): ?>
                            <img src="<?= htmlspecialchars($service['image']) ?>" alt="">
                        <?php else: ?>
                            <i class="fas fa-cog"></i>
                        <?php endif; ?>
                    </div>
                    <h3><?= htmlspecialchars($service['title']) ?></h3>
                    <?php if (!empty($service['short_description'])): ?>
                        <p><?= htmlspecialchars($service['short_description']) ?></p>
                    <?php endif; ?>
                    <span class="card-link"><?= __('learn_more') ?> <i class="fas fa-arrow-right"></i></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- All Services -->
<section class="section">
    <div class="container">
        <?php if (!empty($currentCategory)): ?>
            <div class="section-header">
                <h2><?= htmlspecialchars($currentCategory['name']) ?></h2>
                <?php if (!empty($currentCategory['description'])): ?>
                    <p><?= strip_tags($currentCategory['description']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($services)): ?>
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                    <article class="service-card">
                        <div class="service-icon">
                            <?php if (!empty($service['icon'])): ?>
                                <i class="<?= htmlspecialchars($service['icon']) ?>"></i>
                            <?php elseif (!empty($service['image'])): ?>
                                <img src="<?= htmlspecialchars($service['image']) ?>" alt="">
                            <?php else: ?>
                                <i class="fas fa-cog"></i>
                            <?php endif; ?>
                        </div>
                        <div class="service-content">
                            <h3><?= htmlspecialchars($service['title']) ?></h3>
                            <?php if (!empty($service['short_description'])): ?>
                                <p><?= htmlspecialchars($service['short_description']) ?></p>
                            <?php endif; ?>
                            <a href="<?= _url('/servicios/' . $service['slug']) ?>" class="service-link">
                                <?= __('view_service') ?> <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-cogs"></i>
                <h3><?= __('no_services_found') ?></h3>
                <p><?= __('check_back_later') ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section section bg-primary">
    <div class="container">
        <div class="cta-content">
            <h2><?= __('need_custom_solution') ?></h2>
            <p><?= __('contact_us_services') ?></p>
            <a href="<?= _url('/contacto') ?>" class="btn btn-white btn-lg"><?= __('contact_us') ?></a>
        </div>
    </div>
</section>

<style>
.page-hero {
    min-height: 300px;
    padding-top: var(--header-height);
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
}

.page-hero h1 {
    color: white;
    margin-bottom: var(--spacing-sm);
}

.page-hero p {
    opacity: 0.9;
    font-size: var(--font-size-lg);
    max-width: 600px;
    margin: 0 auto;
}

.category-filter {
    background: white;
    border-bottom: 1px solid var(--color-gray-200);
    padding: var(--spacing-md) 0;
    position: sticky;
    top: var(--header-height);
    z-index: 90;
}

.filter-tabs {
    display: flex;
    gap: var(--spacing-sm);
    overflow-x: auto;
    padding-bottom: var(--spacing-xs);
}

.filter-tab {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-full);
    background: var(--color-gray-100);
    color: var(--color-gray-700);
    font-weight: 500;
    white-space: nowrap;
    transition: all var(--transition);
}

.filter-tab:hover,
.filter-tab.active {
    background: var(--color-primary);
    color: white;
}

.featured-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-xl);
}

.featured-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    text-align: center;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.featured-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.featured-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto var(--spacing-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    border-radius: var(--radius-xl);
    color: white;
    font-size: 32px;
}

.featured-icon img {
    max-width: 50px;
    max-height: 50px;
}

.featured-card h3 {
    margin-bottom: var(--spacing-sm);
}

.featured-card p {
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-md);
}

.card-link {
    color: var(--color-primary);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-xl);
}

.service-card {
    display: flex;
    gap: var(--spacing-lg);
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.service-card:hover {
    box-shadow: var(--shadow-md);
}

.service-icon {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary-light);
    border-radius: var(--radius-lg);
    color: var(--color-primary);
    font-size: 24px;
}

.service-icon img {
    max-width: 40px;
    max-height: 40px;
}

.service-content h3 {
    margin-bottom: var(--spacing-xs);
}

.service-content p {
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-sm);
}

.service-link {
    color: var(--color-primary);
    font-weight: 600;
    font-size: var(--font-size-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.cta-section {
    text-align: center;
}

.cta-content {
    max-width: 600px;
    margin: 0 auto;
}

.cta-content h2 {
    color: white;
    margin-bottom: var(--spacing-sm);
}

.cta-content p {
    opacity: 0.9;
    margin-bottom: var(--spacing-xl);
}

.empty-state {
    text-align: center;
    padding: var(--spacing-3xl);
    color: var(--color-gray-500);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: var(--spacing-md);
    color: var(--color-gray-300);
}

@media (max-width: 1024px) {
    .featured-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .featured-grid,
    .services-grid {
        grid-template-columns: 1fr;
    }

    .service-card {
        flex-direction: column;
        text-align: center;
    }

    .service-icon {
        margin: 0 auto;
    }
}
</style>
