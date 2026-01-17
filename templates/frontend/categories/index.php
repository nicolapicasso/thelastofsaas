<?php
/**
 * Categories Directory Template
 * We're Sinapsis CMS
 */
?>

<!-- Categories Header -->
<section class="page-hero">
    <div class="container">
        <h1><?= __('categories') ?></h1>
        <p><?= __('categories_subtitle') ?></p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
            <span>/</span>
            <span><?= __('categories') ?></span>
        </nav>
    </div>
</div>

<!-- Categories Grid -->
<section class="section">
    <div class="container">
        <?php if (!empty($categories)): ?>
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                    <a href="<?= _url('/categorias/' . $category['slug']) ?>" class="category-card">
                        <div class="category-icon-wrapper">
                            <?php if (!empty($category['icon_image'])): ?>
                                <img src="<?= htmlspecialchars($category['icon_image']) ?>" alt="<?= htmlspecialchars($category['name']) ?>" class="category-icon-img">
                            <?php elseif (!empty($category['icon'])): ?>
                                <i class="<?= htmlspecialchars($category['icon']) ?>" style="<?= !empty($category['color']) ? 'color: ' . htmlspecialchars($category['color']) : '' ?>"></i>
                            <?php else: ?>
                                <i class="fas fa-folder"></i>
                            <?php endif; ?>
                        </div>
                        <div class="category-content">
                            <h3><?= htmlspecialchars($category['name']) ?></h3>
                            <?php if (!empty($category['short_description'])): ?>
                                <p><?= htmlspecialchars($category['short_description']) ?></p>
                            <?php endif; ?>
                            <div class="category-stats">
                                <?php
                                $totalItems = ($category['services_count'] ?? 0) +
                                              ($category['tools_count'] ?? 0) +
                                              ($category['cases_count'] ?? 0);
                                ?>
                                <span><?= $totalItems ?> <?= __('items') ?></span>
                            </div>
                        </div>
                        <span class="category-arrow"><i class="fas fa-arrow-right"></i></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3><?= __('no_categories') ?></h3>
                <p><?= __('no_categories_text') ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

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

.page-hero h1 { color: white; margin-bottom: var(--spacing-sm); }
.page-hero p { opacity: 0.9; font-size: var(--font-size-lg); max-width: 600px; margin: 0 auto; }

.categories-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-xl);
}

.category-card {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
    border: 1px solid var(--color-gray-100);
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    border-color: var(--color-primary);
}

.category-icon-wrapper {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-icon-wrapper i {
    font-size: 48px;
    color: var(--color-primary);
    transition: transform var(--transition);
}

.category-card:hover .category-icon-wrapper i {
    transform: scale(1.1);
}

.category-icon-wrapper .category-icon-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform var(--transition);
}

.category-card:hover .category-icon-img {
    transform: scale(1.1);
}

.category-content {
    flex: 1;
}

.category-content h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-xs);
    color: var(--color-gray-900);
}

.category-content p {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-sm);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.category-stats {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

.category-arrow {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-100);
    border-radius: var(--radius-full);
    color: var(--color-gray-500);
    transition: all var(--transition);
}

.category-card:hover .category-arrow {
    background: var(--color-primary);
    color: white;
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

@media (max-width: 768px) {
    .categories-grid {
        grid-template-columns: 1fr;
    }

    .category-card {
        flex-direction: column;
        text-align: center;
    }

    .category-arrow {
        display: none;
    }
}
</style>
