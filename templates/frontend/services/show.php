<?php
/**
 * Service Detail Template
 * We're Sinapsis CMS
 */
$hasImage = !empty($service['image']);
?>

<article class="service-detail">
    <!-- Hero Section with Background Image -->
    <section class="service-hero" <?php if ($hasImage): ?>style="background-image: url('<?= htmlspecialchars($service['image']) ?>');"<?php endif; ?>>
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="service-hero-content">
                <?php if (!empty($service['category_name'])): ?>
                    <span class="service-category"><?= htmlspecialchars($service['category_name']) ?></span>
                <?php endif; ?>
                <h1><?= htmlspecialchars($service['title']) ?></h1>
                <?php if (!empty($service['short_description'])): ?>
                    <p class="service-tagline"><?= htmlspecialchars($service['short_description']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="container">
            <nav class="breadcrumb">
                <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                <span>/</span>
                <a href="<?= _url('/servicios') ?>"><?= __('services') ?></a>
                <span>/</span>
                <span><?= htmlspecialchars($service['title']) ?></span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <?php if (!empty($service['full_description'])): ?>
    <section class="section">
        <div class="container">
            <div class="service-content">
                <div class="service-description">
                    <?= $service['full_description'] ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Dynamic Blocks -->
    <?php if (!empty($renderedBlocks)): ?>
        <?= $renderedBlocks ?>
    <?php endif; ?>

    <!-- LLM Q&A Block -->
    <?php
    if (!empty($service['enable_llm_qa']) && !empty($service['llm_qa_content'])) {
        $llmQaItems = json_decode($service['llm_qa_content'], true) ?: [];
        $llmQaEntityType = 'service';
        $llmQaEntityId = $service['id'];
        include TEMPLATES_PATH . '/frontend/partials/llm-qa-section.php';
    }
    ?>

    <!-- Success Cases -->
    <?php if (!empty($successCases)): ?>
    <section class="section bg-light">
        <div class="container">
            <div class="section-header">
                <h2><?= __('success_cases') ?></h2>
                <p><?= __('discover_how_we_help_clients') ?></p>
            </div>
            <div class="cases-grid">
                <?php foreach ($successCases as $case): ?>
                    <a href="<?= _url('/casos-de-exito/' . $case['slug']) ?>" class="case-card">
                        <?php if (!empty($case['featured_image'])): ?>
                            <div class="case-image">
                                <img src="<?= htmlspecialchars($case['featured_image']) ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <div class="case-content">
                            <h3><?= htmlspecialchars($case['title']) ?></h3>
                            <?php if (!empty($case['client_name'])): ?>
                                <span class="case-client"><?= htmlspecialchars($case['client_name']) ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="section-footer">
                <a href="<?= _url('/casos-de-exito') ?>" class="btn btn-outline" title="<?= __('view_all_success_cases') ?>">
                    <?= __('view_all') ?> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Related Services -->
    <?php if (!empty($relatedServices)): ?>
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2><?= __('related_services') ?></h2>
            </div>
            <div class="related-grid">
                <?php foreach ($relatedServices as $related): ?>
                    <a href="<?= _url('/servicios/' . $related['slug']) ?>" class="related-card">
                        <div class="related-icon">
                            <?php if (!empty($related['icon_class'])): ?>
                                <i class="<?= htmlspecialchars($related['icon_class']) ?>"></i>
                            <?php else: ?>
                                <i class="fas fa-cog"></i>
                            <?php endif; ?>
                        </div>
                        <h4><?= htmlspecialchars($related['title']) ?></h4>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</article>

<style>
.service-hero {
    position: relative;
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-3xl);
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    background-size: cover;
    background-position: center;
    color: white;
    min-height: 350px;
    display: flex;
    align-items: center;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(33, 90, 107, 0.85) 0%, rgba(33, 90, 107, 0.75) 100%);
    z-index: 1;
}

.service-hero .container {
    position: relative;
    z-index: 2;
}

.service-hero-content {
    max-width: 1100px;
    margin: 0 auto;
    text-align: center;
}

.service-category {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-md);
    background: rgba(255,255,255,0.2);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-md);
    backdrop-filter: blur(4px);
}

.service-hero-content h1 {
    color: white;
    margin-bottom: var(--spacing-md);
    font-size: var(--font-size-4xl);
    line-height: 1.2;
    text-align: left;
}

.service-tagline {
    font-size: var(--font-size-xl);
    opacity: 0.95;
    line-height: 1.6;
    text-align: left;
    width: 100%;
}

.service-content {
    max-width: 1100px;
    margin: 0 auto;
}

.service-description {
    font-size: var(--font-size-lg);
    line-height: 1.8;
    color: var(--color-gray-700);
}

.service-description h2,
.service-description h3 {
    margin-top: var(--spacing-xl);
    margin-bottom: var(--spacing-md);
}

.service-description ul,
.service-description ol {
    margin-bottom: var(--spacing-lg);
    padding-left: var(--spacing-xl);
}

.service-description li {
    margin-bottom: var(--spacing-sm);
}

.service-description img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-lg);
    margin: var(--spacing-lg) 0;
}

/* Cases Grid */
.cases-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-xl);
}

.case-card {
    background: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.case-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.case-image {
    height: 180px;
    overflow: hidden;
}

.case-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.case-content {
    padding: var(--spacing-lg);
}

.case-content h3 {
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-xs);
}

.case-client {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

.section-footer {
    text-align: center;
    margin-top: var(--spacing-xl);
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-lg);
}

.related-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    text-align: center;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.related-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.related-icon {
    width: 50px;
    height: 50px;
    margin: 0 auto var(--spacing-md);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary-light);
    border-radius: var(--radius-lg);
    color: var(--color-primary);
    font-size: 20px;
}

.related-card h4 {
    font-size: var(--font-size-sm);
    margin: 0;
}

@media (max-width: 1024px) {
    .cases-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .service-hero-content {
        text-align: center;
    }

    .service-hero-content h1 {
        font-size: var(--font-size-2xl);
    }

    .service-tagline {
        font-size: var(--font-size-lg);
    }

    .cases-grid,
    .related-grid {
        grid-template-columns: 1fr;
    }
}
</style>
