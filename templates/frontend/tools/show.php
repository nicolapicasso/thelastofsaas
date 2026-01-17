<?php
/**
 * Tool Detail Template
 * We're Sinapsis CMS
 */
?>

<article class="tool-detail">
    <!-- Hero Section -->
    <section class="tool-hero">
        <div class="container">
            <div class="tool-hero-content">
                <div class="tool-hero-logo">
                    <?php if (!empty($tool['logo'])): ?>
                        <img src="<?= htmlspecialchars($tool['logo']) ?>" alt="<?= htmlspecialchars($tool['title']) ?>">
                    <?php else: ?>
                        <i class="fas fa-puzzle-piece"></i>
                    <?php endif; ?>
                </div>
                <div class="tool-hero-info">
                    <?php if (!empty($tool['category_name'])): ?>
                        <span class="tool-category"><?= htmlspecialchars($tool['category_name']) ?></span>
                    <?php endif; ?>
                    <h1><?= htmlspecialchars($tool['title']) ?></h1>
                    <?php if (!empty($tool['subtitle'])): ?>
                        <p class="tool-tagline"><?= htmlspecialchars($tool['subtitle']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="container">
            <nav class="breadcrumb">
                <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                <span>/</span>
                <a href="<?= _url('/herramientas') ?>"><?= __('tools') ?></a>
                <span>/</span>
                <span><?= htmlspecialchars($tool['title']) ?></span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="tool-layout">
                <div class="tool-main">
                    <?php if (!empty($tool['description'])): ?>
                        <div class="tool-description">
                            <?= $tool['description'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="tool-sidebar">
                    <!-- Info Card -->
                    <div class="sidebar-card">
                        <h4><?= __('tool_info') ?></h4>
                        <dl class="info-list">
                            <?php if (!empty($tool['category_name'])): ?>
                                <dt><?= __('category') ?></dt>
                                <dd><?= htmlspecialchars($tool['category_name']) ?></dd>
                            <?php endif; ?>
                        </dl>
                        <?php if (!empty($tool['platform_url'])): ?>
                            <a href="<?= htmlspecialchars($tool['platform_url']) ?>" target="_blank" rel="noopener" class="btn btn-outline btn-block">
                                <i class="fas fa-external-link-alt"></i> <?= __('official_website') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Success Cases -->
    <?php if (!empty($successCases)): ?>
    <section class="section bg-light">
        <div class="container">
            <div class="section-header">
                <h2><?= __('projects_with_tool') ?></h2>
                <p><?= __('see_tool_in_action') ?></p>
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
        </div>
    </section>
    <?php endif; ?>

    <!-- Related Tools -->
    <?php if (!empty($relatedTools)): ?>
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2><?= __('related_tools') ?></h2>
            </div>
            <div class="related-grid">
                <?php foreach ($relatedTools as $related): ?>
                    <a href="<?= _url('/herramientas/' . $related['slug']) ?>" class="related-card">
                        <div class="related-logo">
                            <?php if (!empty($related['logo'])): ?>
                                <img src="<?= htmlspecialchars($related['logo']) ?>" alt="">
                            <?php else: ?>
                                <i class="fas fa-puzzle-piece"></i>
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
.tool-hero {
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-3xl);
    background: linear-gradient(135deg, #264752 0%, #1a3a44 100%);
    color: white;
}

.tool-hero-content {
    display: flex;
    align-items: center;
    gap: var(--spacing-xl);
}

.tool-hero-logo {
    flex-shrink: 0;
    width: 120px;
    height: 120px;
    background: white;
    border-radius: var(--radius-xl);
    padding: var(--spacing-md);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-lg);
}

.tool-hero-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.tool-hero-logo i {
    font-size: 48px;
    color: var(--color-gray-400);
}

.tool-category {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-sm);
    background: rgba(255,255,255,0.2);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-sm);
}

.tool-hero-info h1 {
    color: white;
    margin-bottom: var(--spacing-sm);
}

.tool-tagline {
    font-size: var(--font-size-lg);
    opacity: 0.9;
}

.tool-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: var(--spacing-2xl);
}

.tool-description {
    font-size: var(--font-size-lg);
    line-height: 1.8;
    color: var(--color-gray-700);
}

.tool-sidebar {
    position: sticky;
    top: calc(var(--header-height) + var(--spacing-lg));
    height: fit-content;
}

.sidebar-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-md);
}

.sidebar-card h4 {
    margin-bottom: var(--spacing-md);
    padding-bottom: var(--spacing-sm);
    border-bottom: 2px solid var(--color-primary);
}

.info-list {
    display: grid;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-lg);
}

.info-list dt {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

.info-list dd {
    font-weight: 500;
}

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

.related-logo {
    width: 60px;
    height: 60px;
    margin: 0 auto var(--spacing-md);
    display: flex;
    align-items: center;
    justify-content: center;
}

.related-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.related-logo i {
    font-size: 30px;
    color: var(--color-gray-400);
}

.related-card h4 {
    font-size: var(--font-size-sm);
    margin: 0;
}

@media (max-width: 1024px) {
    .tool-layout {
        grid-template-columns: 1fr;
    }

    .cases-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .tool-hero-content {
        flex-direction: column;
        text-align: center;
    }

    .cases-grid,
    .related-grid {
        grid-template-columns: 1fr;
    }
}
</style>
