<?php
/**
 * Knowledge Base Category Template
 * Omniwallet CMS
 */
?>

<!-- Category Header (Hero style like /ayuda) -->
<section class="knowledge-header">
    <div class="container">
        <?php if (!empty($category['icon'])): ?>
            <div class="category-icon-large">
                <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
            </div>
        <?php endif; ?>
        <h1><?= htmlspecialchars($category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?>
            <p><?= htmlspecialchars($category['description']) ?></p>
        <?php endif; ?>
        <span class="article-count-badge"><?= count($articles) ?> <?= __('articles') ?></span>

        <!-- Search -->
        <div class="knowledge-search">
            <form action="<?= _url('/ayuda/buscar') ?>" method="GET">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" placeholder="<?= __('search_articles') ?>..." autocomplete="off">
                    <button type="submit" class="btn btn-primary"><?= __('search') ?></button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Breadcrumb (below hero) -->
<div class="page-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <nav class="breadcrumb">
                <a href="<?= _url('/ayuda') ?>"><i class="fas fa-home"></i> <?= __('knowledge_base') ?></a>
                <span>/</span>
                <span><?= htmlspecialchars($category['name']) ?></span>
            </nav>
        </div>
    </div>
</div>

<!-- Main Content with Left Sidebar -->
<section class="section knowledge-content">
    <div class="container">
        <div class="knowledge-layout">
            <!-- Sidebar (Left) -->
            <aside class="knowledge-sidebar knowledge-sidebar-left">
                <div class="sidebar-sticky-wrapper">
                    <div class="sidebar-card">
                        <h3><?= __('table_of_contents') ?></h3>
                        <nav class="sidebar-nav">
                            <?php if (!empty($groupedArticles)): ?>
                                <?php foreach ($groupedArticles as $categoryId => $group): ?>
                                    <div class="sidebar-category <?= ($group['slug'] !== $category['slug']) ? 'collapsed' : '' ?>">
                                        <button class="sidebar-category-toggle" onclick="toggleSidebarCategory(this)">
                                            <span>
                                                <?php if (!empty($group['icon'])): ?>
                                                    <i class="<?= htmlspecialchars($group['icon']) ?>"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($group['name']) ?>
                                            </span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <ul class="sidebar-articles">
                                            <?php foreach ($group['articles'] as $art): ?>
                                                <li>
                                                    <a href="<?= _url('/ayuda/' . htmlspecialchars($art['slug'])) ?>">
                                                        <?= htmlspecialchars($art['title']) ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </nav>
                    </div>

                    <!-- Quick Search -->
                    <div class="sidebar-card">
                        <form action="<?= _url('/ayuda/buscar') ?>" method="GET" class="sidebar-search">
                            <input type="text" name="q" placeholder="<?= __('search') ?>..." class="sidebar-search-input">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Article List -->
            <div class="knowledge-main">
                <?php if (!empty($articles)): ?>
                    <div class="content-section">
                        <div class="articles-list">
                            <?php foreach ($articles as $article): ?>
                                <a href="<?= _url('/ayuda/' . htmlspecialchars($article['slug'])) ?>" class="article-item">
                                    <div class="article-item-content">
                                        <h4><?= htmlspecialchars($article['title']) ?></h4>
                                        <?php if (!empty($article['excerpt'])): ?>
                                            <p><?= htmlspecialchars(substr($article['excerpt'], 0, 150)) ?>...</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="article-item-meta">
                                        <span><i class="far fa-eye"></i> <?= number_format($article['view_count'] ?? 0) ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="content-section empty-state">
                        <i class="fas fa-folder-open"></i>
                        <h3><?= __('no_articles_in_category') ?></h3>
                        <p><?= __('no_articles_published_yet') ?></p>
                        <a href="<?= _url('/ayuda') ?>" class="btn btn-primary"><?= __('back_to_knowledge_base') ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
/* Knowledge Header (Hero) */
.knowledge-header {
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-2xl);
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    color: var(--color-white);
    text-align: center;
}

.knowledge-header h1 {
    color: var(--color-white);
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-sm);
}

.knowledge-header p {
    font-size: var(--font-size-lg);
    opacity: 0.9;
    margin-bottom: var(--spacing-md);
    max-width: 950px;
    margin-left: auto;
    margin-right: auto;
}

.category-icon-large {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--spacing-md);
}

.category-icon-large i {
    font-size: 36px;
    color: var(--color-white);
}

.article-count-badge {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-md);
    background: rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-xl);
}

/* Search */
.knowledge-search {
    max-width: 600px;
    margin: 0 auto;
}

.search-input-wrapper {
    display: flex;
    align-items: center;
    background-color: var(--color-white);
    border-radius: var(--radius-full);
    padding: var(--spacing-xs);
    box-shadow: var(--shadow-lg);
}

.search-input-wrapper i {
    color: var(--color-gray-400);
    padding: 0 var(--spacing-md);
}

.search-input-wrapper input {
    flex: 1;
    border: none;
    padding: var(--spacing-md) 0;
    font-size: var(--font-size-base);
    outline: none;
}

.search-input-wrapper .btn {
    border-radius: var(--radius-full);
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
    flex-wrap: wrap;
    gap: var(--spacing-xs);
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
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

/* Content Layout */
.knowledge-content {
    background-color: var(--color-gray-50);
}

.knowledge-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: var(--spacing-xl);
    align-items: start;
}

.knowledge-main {
    min-width: 0;
}

/* Sidebar - no sticky, show all content */
.knowledge-sidebar-left {
    /* No max-height, show full content */
}

.sidebar-sticky-wrapper {
    /* No sticky, no max-height */
}

.content-section {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
}

/* Articles List */
.articles-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.article-item {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-md);
    padding: var(--spacing-md);
    background: var(--color-gray-50);
    border-radius: var(--radius-lg);
    text-decoration: none;
    transition: all var(--transition);
}

.article-item:hover {
    background: var(--color-gray-100);
}

.article-item-content {
    flex: 1;
    min-width: 0;
}

.article-item h4 {
    font-size: var(--font-size-base);
    color: var(--color-dark);
    margin-bottom: var(--spacing-xs);
}

.article-item p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin: 0;
}

.article-item-meta {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    white-space: nowrap;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: var(--spacing-3xl);
}

.empty-state i {
    font-size: 48px;
    color: var(--color-gray-300);
    margin-bottom: var(--spacing-md);
}

.empty-state h3 {
    color: var(--color-dark);
    margin-bottom: var(--spacing-sm);
}

.empty-state p {
    color: var(--color-gray-500);
    margin-bottom: var(--spacing-lg);
}

/* Sidebar */
.sidebar-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-md);
}

.sidebar-card h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-md);
    padding-bottom: var(--spacing-sm);
    border-bottom: 1px solid var(--color-gray-200);
}

/* Sidebar Category Toggle */
.sidebar-category {
    margin-bottom: var(--spacing-xs);
}

.sidebar-category-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: var(--spacing-sm);
    background: var(--color-gray-50);
    border: none;
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--color-dark);
    cursor: pointer;
    transition: all var(--transition);
}

.sidebar-category-toggle:hover {
    background: var(--color-gray-100);
}

.sidebar-category-toggle > span {
    display: flex;
    align-items: center;
    gap: 8px;
}

.sidebar-category-toggle > span i {
    color: var(--color-primary);
    font-size: 14px;
}

.sidebar-category-toggle > i {
    font-size: 12px;
    transition: transform var(--transition);
}

.sidebar-category.collapsed .sidebar-category-toggle > i {
    transform: rotate(-90deg);
}

.sidebar-articles {
    list-style: none;
    padding: var(--spacing-xs) 0 var(--spacing-xs) var(--spacing-md);
    margin: 0;
}

.sidebar-category.collapsed .sidebar-articles {
    display: none;
}

.sidebar-articles li {
    margin-bottom: var(--spacing-xs);
}

.sidebar-articles a {
    display: block;
    padding: var(--spacing-xs) var(--spacing-sm);
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    text-decoration: none;
    border-radius: var(--radius-sm);
    transition: all var(--transition);
}

.sidebar-articles a:hover {
    color: var(--color-primary);
    background: var(--color-gray-50);
}

/* Sidebar Search */
.sidebar-search {
    display: flex;
    gap: var(--spacing-xs);
}

.sidebar-search-input {
    flex: 1;
    padding: var(--spacing-sm);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
}

.sidebar-search button {
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--color-primary);
    color: var(--color-white);
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
}

.sidebar-search button:hover {
    background: var(--color-primary-dark);
}

/* Responsive */
@media (max-width: 1024px) {
    .knowledge-layout {
        grid-template-columns: 1fr;
    }

    .knowledge-sidebar-left {
        order: 2;
    }

    .knowledge-main {
        order: 1;
    }
}

@media (max-width: 768px) {
    .search-input-wrapper {
        flex-wrap: wrap;
        border-radius: var(--radius-lg);
    }

    .search-input-wrapper input {
        width: 100%;
    }

    .search-input-wrapper .btn {
        width: 100%;
        margin-top: var(--spacing-sm);
        border-radius: var(--radius-md);
    }
}
</style>

<script>
function toggleSidebarCategory(button) {
    const category = button.closest('.sidebar-category');
    category.classList.toggle('collapsed');
}
</script>
