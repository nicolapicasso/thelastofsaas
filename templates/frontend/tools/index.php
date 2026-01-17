<?php
/**
 * Tools Index Template
 * We're Sinapsis CMS
 */
?>

<!-- Tools Header -->
<section class="page-hero">
    <div class="container">
        <h1><?= __('tools_title') ?></h1>
        <p><?= __('tools_subtitle') ?></p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
            <span>/</span>
            <span><?= __('tools') ?></span>
        </nav>
    </div>
</div>

<!-- Category Filter -->
<?php if (!empty($categories)): ?>
<section class="category-filter">
    <div class="container">
        <div class="filter-tabs">
            <a href="<?= _url('/herramientas') ?>" class="filter-tab <?= empty($currentCategory) ? 'active' : '' ?>">
                <?= __('all') ?>
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= _url('/herramientas?categoria=' . $cat['slug']) ?>"
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

<!-- Tools by Category -->
<?php if (!empty($grouped) && empty($currentCategory)): ?>
    <?php foreach ($grouped as $categoryName => $categoryTools): ?>
    <section class="section <?= $loop ?? false ? 'bg-light' : '' ?>">
        <div class="container">
            <div class="section-header">
                <h2><?= htmlspecialchars($categoryName) ?></h2>
            </div>
            <div class="tools-grid">
                <?php foreach ($categoryTools as $tool): ?>
                    <a href="<?= _url('/herramientas/' . $tool['slug']) ?>" class="tool-card">
                        <div class="tool-logo">
                            <?php if (!empty($tool['logo'])): ?>
                                <img src="<?= htmlspecialchars($tool['logo']) ?>" alt="<?= htmlspecialchars($tool['title']) ?>">
                            <?php else: ?>
                                <i class="fas fa-puzzle-piece"></i>
                            <?php endif; ?>
                        </div>
                        <div class="tool-info">
                            <h3><?= htmlspecialchars($tool['title']) ?></h3>
                            <?php if (!empty($tool['subtitle'])): ?>
                                <p><?= htmlspecialchars($tool['subtitle']) ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endforeach; ?>
<?php else: ?>
    <!-- Flat List -->
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

            <?php if (!empty($tools)): ?>
                <div class="tools-grid">
                    <?php foreach ($tools as $tool): ?>
                        <a href="<?= _url('/herramientas/' . $tool['slug']) ?>" class="tool-card">
                            <div class="tool-logo">
                                <?php if (!empty($tool['logo'])): ?>
                                    <img src="<?= htmlspecialchars($tool['logo']) ?>" alt="<?= htmlspecialchars($tool['title']) ?>">
                                <?php else: ?>
                                    <i class="fas fa-puzzle-piece"></i>
                                <?php endif; ?>
                            </div>
                            <div class="tool-info">
                                <h3><?= htmlspecialchars($tool['title']) ?></h3>
                                <?php if (!empty($tool['subtitle'])): ?>
                                    <p><?= htmlspecialchars($tool['subtitle']) ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-tools"></i>
                    <h3><?= __('no_tools_found') ?></h3>
                    <p><?= __('check_back_later') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<style>
.page-hero {
    min-height: 300px;
    padding-top: var(--header-height);
    background: linear-gradient(135deg, #264752 0%, #1a3a44 100%);
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

.tools-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-lg);
}

.tool-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.tool-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.tool-logo {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--spacing-md);
}

.tool-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.tool-logo i {
    font-size: 40px;
    color: var(--color-gray-400);
}

.tool-info h3 {
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-xs);
}

.tool-info p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    margin: 0;
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
    .tools-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .tools-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .tools-grid {
        grid-template-columns: 1fr;
    }
}
</style>
