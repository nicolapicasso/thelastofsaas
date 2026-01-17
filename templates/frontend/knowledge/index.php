<?php
/**
 * Knowledge Base Index Template
 * Omniwallet CMS
 */
?>

<!-- Knowledge Header -->
<section class="knowledge-header">
    <div class="container">
        <h1><?= __('knowledge_base') ?></h1>
        <p><?= __('help_subtitle') ?></p>

        <!-- Search -->
        <div class="knowledge-search">
            <form action="<?= _url('/ayuda/buscar') ?>" method="GET" id="knowledge-search-form">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text"
                           name="q"
                           id="knowledge-search-input"
                           placeholder="<?= __('search_help') ?>"
                           autocomplete="off">
                    <button type="submit" class="btn btn-primary"><?= __('search') ?></button>
                </div>
                <div id="search-suggestions" class="search-suggestions"></div>
            </form>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <nav class="breadcrumb">
                <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                <span>/</span>
                <span><?= __('help') ?></span>
            </nav>
        </div>
    </div>
</div>

<!-- Main Content (full width - no sidebar) -->
<section class="section knowledge-content">
    <div class="container">
        <!-- Categories Grid -->
        <?php if (!empty($categories)): ?>
            <div class="content-section">
                <h2 class="section-subtitle"><?= __('browse_categories') ?></h2>
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                        <a href="<?= _url('/ayuda/categoria/' . htmlspecialchars($category['slug'])) ?>" class="category-card">
                            <div class="category-icon">
                                <?php if (!empty($category['icon'])): ?>
                                    <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                                <?php else: ?>
                                    <i class="fas fa-folder"></i>
                                <?php endif; ?>
                            </div>
                            <h3><?= htmlspecialchars($category['name']) ?></h3>
                            <span class="article-count"><?= $category['article_count'] ?> <?= __('articles') ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="knowledge-two-columns">
            <!-- Popular Articles -->
            <?php if (!empty($popularArticles)): ?>
                <div class="content-section">
                    <h2 class="section-subtitle"><?= __('popular_articles') ?></h2>
                    <div class="articles-list">
                        <?php foreach ($popularArticles as $article): ?>
                            <a href="<?= _url('/ayuda/' . htmlspecialchars($article['slug'])) ?>" class="article-item">
                                <div class="article-item-content">
                                    <h4><?= htmlspecialchars($article['title']) ?></h4>
                                    <?php if (!empty($article['excerpt'])): ?>
                                        <p><?= htmlspecialchars(substr($article['excerpt'], 0, 120)) ?>...</p>
                                    <?php endif; ?>
                                    <span class="article-category-tag">
                                        <?php if (!empty($article['category_icon'])): ?>
                                            <i class="<?= htmlspecialchars($article['category_icon']) ?>"></i>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($article['category_name'] ?? 'General') ?>
                                    </span>
                                </div>
                                <div class="article-item-meta">
                                    <span><i class="far fa-eye"></i> <?= number_format($article['view_count'] ?? $article['views'] ?? 0) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recent Articles -->
            <?php if (!empty($recentArticles)): ?>
                <div class="content-section">
                    <h2 class="section-subtitle"><?= __('recent_articles') ?></h2>
                    <div class="articles-list">
                        <?php foreach ($recentArticles as $article): ?>
                            <a href="<?= _url('/ayuda/' . htmlspecialchars($article['slug'])) ?>" class="article-item compact">
                                <i class="far fa-file-alt"></i>
                                <div class="article-item-content">
                                    <h4><?= htmlspecialchars($article['title']) ?></h4>
                                    <span class="article-category-tag small">
                                        <?php if (!empty($article['category_icon'])): ?>
                                            <i class="<?= htmlspecialchars($article['category_icon']) ?>"></i>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($article['category_name'] ?? 'General') ?>
                                    </span>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Support CTA -->
        <div class="support-cta-banner">
            <i class="fas fa-headset"></i>
            <div>
                <h4><?= __('need_help') ?></h4>
                <p><?= __('team_here_to_help') ?></p>
            </div>
            <a href="<?= _url('/contacto') ?>" class="btn btn-primary"><?= __('contact') ?></a>
        </div>
    </div>
</section>

<style>
/* Knowledge Header */
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
    margin-bottom: var(--spacing-xl);
    max-width: 950px;
    margin-left: auto;
    margin-right: auto;
}

/* Search */
.knowledge-search {
    max-width: 600px;
    margin: 0 auto;
    position: relative;
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

.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--color-white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    margin-top: var(--spacing-xs);
    display: none;
    max-height: 300px;
    overflow-y: auto;
    z-index: 100;
}

.search-suggestions.active {
    display: block;
}

.search-suggestion-item {
    display: block;
    padding: var(--spacing-md);
    border-bottom: 1px solid var(--color-gray-100);
    color: var(--color-dark);
    text-decoration: none;
}

.search-suggestion-item:hover {
    background-color: var(--color-gray-50);
}

.search-suggestion-item:last-child {
    border-bottom: none;
}

/* Knowledge Layout */
.knowledge-content {
    background-color: var(--color-gray-50);
}

.knowledge-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: var(--spacing-xl);
    align-items: start;
}

.knowledge-main {
    min-width: 0;
}

/* Content Section */
.content-section {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    margin-bottom: var(--spacing-lg);
}

.section-subtitle {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-lg);
    color: var(--color-dark);
}

/* Categories Grid - 4 columns for full width */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-md);
}

/* Two columns layout for popular/recent */
.knowledge-two-columns {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-xl);
    margin-bottom: var(--spacing-xl);
}

/* Support CTA Banner */
.support-cta-banner {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    padding: var(--spacing-xl);
    background: linear-gradient(135deg, var(--color-primary-lighter) 0%, var(--color-white) 100%);
    border-radius: var(--radius-xl);
    border: 1px solid var(--color-gray-200);
}

.support-cta-banner > i {
    font-size: 48px;
    color: var(--color-primary);
}

.support-cta-banner > div {
    flex: 1;
}

.support-cta-banner h4 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-xs);
}

.support-cta-banner p {
    color: var(--color-gray-600);
    margin: 0;
}

.category-card {
    display: block;
    padding: var(--spacing-lg);
    background: var(--color-gray-50);
    border-radius: var(--radius-lg);
    text-align: center;
    transition: all var(--transition);
    text-decoration: none;
}

.category-card:hover {
    background: var(--color-primary-lighter);
    transform: translateY(-2px);
}

.category-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-primary) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--spacing-sm);
}

.category-icon i {
    font-size: 20px;
    color: var(--color-white);
}

.category-card h3 {
    font-size: var(--font-size-base);
    color: var(--color-dark);
    margin-bottom: var(--spacing-xs);
}

.category-card .article-count {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
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

/* Category Tag */
.article-category-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: var(--font-size-xs);
    color: var(--color-white);
    background: var(--color-primary);
    padding: 4px 10px;
    border-radius: var(--radius-full);
    font-weight: 500;
    margin-top: var(--spacing-xs);
}

.article-category-tag i {
    font-size: 10px;
}

.article-category-tag.small {
    font-size: 10px;
    padding: 2px 8px;
    margin-top: 4px;
}

.article-category {
    font-size: var(--font-size-xs);
    color: var(--color-primary);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.article-item h4 {
    font-size: var(--font-size-base);
    color: var(--color-dark);
    margin: var(--spacing-xs) 0;
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

.article-item.compact {
    align-items: center;
    padding: var(--spacing-sm) var(--spacing-md);
}

.article-item.compact > i:first-child {
    color: var(--color-gray-400);
    font-size: 18px;
}

.article-item.compact > i:last-child {
    color: var(--color-gray-300);
}

.article-item.compact h4 {
    margin: 0;
}

.article-item.compact .article-category {
    display: block;
    margin-top: 2px;
}

/* Sidebar */
.knowledge-sidebar {
    position: sticky;
    top: calc(var(--header-height) + var(--spacing-lg));
    max-height: calc(100vh - var(--header-height) - var(--spacing-2xl));
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--color-gray-300) transparent;
}

.knowledge-sidebar::-webkit-scrollbar {
    width: 6px;
}

.knowledge-sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.knowledge-sidebar::-webkit-scrollbar-thumb {
    background-color: var(--color-gray-300);
    border-radius: 3px;
}

.knowledge-sidebar::-webkit-scrollbar-thumb:hover {
    background-color: var(--color-gray-400);
}

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

.sidebar-nav {
    /* Scroll is handled by the parent sticky container */
}

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

.sidebar-category-toggle .toggle-icon {
    font-size: 12px;
    transition: transform var(--transition);
}

.sidebar-category.collapsed .sidebar-category-toggle .toggle-icon {
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

.sidebar-articles a.active {
    color: var(--color-primary);
    font-weight: 600;
}

.sidebar-empty {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    text-align: center;
    padding: var(--spacing-lg);
}

/* Sidebar CTA */
.sidebar-cta {
    text-align: center;
    background: linear-gradient(135deg, var(--color-primary-lighter) 0%, var(--color-white) 100%);
}

.sidebar-cta i {
    font-size: 32px;
    color: var(--color-primary);
    margin-bottom: var(--spacing-sm);
}

.sidebar-cta h4 {
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-xs);
}

.sidebar-cta p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-md);
}

/* Responsive */
@media (max-width: 1024px) {
    .categories-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .knowledge-two-columns {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .categories-grid {
        grid-template-columns: repeat(2, 1fr);
    }

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

    .support-cta-banner {
        flex-direction: column;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .categories-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Toggle sidebar category
function toggleSidebarCategory(button) {
    const category = button.closest('.sidebar-category');
    category.classList.toggle('collapsed');
}

// Live search suggestions
const searchInput = document.getElementById('knowledge-search-input');
const suggestionsBox = document.getElementById('search-suggestions');
let searchTimeout;

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            suggestionsBox.classList.remove('active');
            suggestionsBox.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const langPrefix = '<?= $currentLang !== "es" ? "/" . $currentLang : "" ?>';
                const response = await fetch(`${langPrefix}/ayuda/buscar?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.results && data.results.length > 0) {
                    suggestionsBox.innerHTML = data.results.slice(0, 5).map(article =>
                        `<a href="${langPrefix}/ayuda/${article.slug}" class="search-suggestion-item">
                            <strong>${article.title}</strong>
                            <small style="display: block; color: var(--color-gray-500);">
                                ${article.category_name || 'General'}
                            </small>
                        </a>`
                    ).join('');
                    suggestionsBox.classList.add('active');
                } else {
                    suggestionsBox.innerHTML = '<div class="search-suggestion-item"><?= __("no_results_found") ?></div>';
                    suggestionsBox.classList.add('active');
                }
            } catch (e) {
                console.error('Search error:', e);
            }
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.remove('active');
        }
    });
}
</script>
