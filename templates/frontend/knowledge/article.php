<?php
/**
 * Knowledge Base Article Template
 * Omniwallet CMS
 */
?>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <nav class="breadcrumb">
                <a href="<?= _url('/ayuda') ?>"><i class="fas fa-home"></i> <?= __('knowledge_base') ?></a>
                <?php if (!empty($article['category_name'])): ?>
                    <span>/</span>
                    <a href="<?= _url('/ayuda/categoria/' . htmlspecialchars($article['category_slug'])) ?>">
                        <?= htmlspecialchars($article['category_name']) ?>
                    </a>
                <?php endif; ?>
                <span>/</span>
                <span><?= htmlspecialchars($article['title']) ?></span>
            </nav>
        </div>
    </div>
</div>

<!-- Main Content with Sidebar -->
<section class="section knowledge-article-section">
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
                                <div class="sidebar-category <?= ($article['category_id'] != $categoryId) ? 'collapsed' : '' ?>">
                                    <button class="sidebar-category-toggle" onclick="toggleSidebarCategory(this)">
                                        <span><?= htmlspecialchars($group['name']) ?></span>
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <ul class="sidebar-articles">
                                        <?php foreach ($group['articles'] as $sideArticle): ?>
                                            <li>
                                                <a href="<?= _url('/ayuda/' . htmlspecialchars($sideArticle['slug'])) ?>"
                                                   class="<?= $sideArticle['slug'] === $article['slug'] ? 'active' : '' ?>">
                                                    <?= htmlspecialchars($sideArticle['title']) ?>
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
                </div><!-- /.sidebar-sticky-wrapper -->
            </aside>

            <!-- Main Article -->
            <article class="knowledge-article">
                <header class="article-header">
                    <?php if (!empty($article['category_name'])): ?>
                        <span class="article-category-badge">
                            <?= htmlspecialchars($article['category_name']) ?>
                        </span>
                    <?php endif; ?>
                    <h1><?= htmlspecialchars($article['title']) ?></h1>
                    <div class="article-meta">
                        <span><i class="far fa-clock"></i> <?= __('updated') ?>: <?= date('d/m/Y', strtotime($article['updated_at'] ?? $article['created_at'])) ?></span>
                        <span><i class="far fa-eye"></i> <?= number_format($article['view_count'] ?? 0) ?> <?= __('views') ?></span>
                    </div>
                </header>

                <?php if (!empty($article['featured_image'])): ?>
                    <div class="article-featured-image">
                        <img src="<?= htmlspecialchars($article['featured_image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
                    </div>
                <?php endif; ?>

                <?php if (!empty($article['excerpt'])): ?>
                    <div class="article-excerpt">
                        <p><?= htmlspecialchars($article['excerpt']) ?></p>
                    </div>
                <?php endif; ?>

                <div class="article-content">
                    <?= $article['content'] ?>
                </div>

                <?php if (!empty($article['video_url'])): ?>
                    <div class="article-video">
                        <h3><?= __('video_tutorial') ?></h3>
                        <?php
                        $videoId = '';
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $article['video_url'], $matches)) {
                            $videoId = $matches[1];
                        }
                        ?>
                        <?php if ($videoId): ?>
                            <div class="video-embed">
                                <iframe src="https://www.youtube.com/embed/<?= $videoId ?>"
                                        frameborder="0"
                                        allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Article Actions -->
                <footer class="article-footer">
                    <div class="article-helpful">
                        <span><?= __('article_helpful') ?></span>
                        <div class="helpful-buttons">
                            <button type="button" class="btn btn-outline btn-sm helpful-btn" data-helpful="yes">
                                <i class="far fa-thumbs-up"></i> <?= __('yes') ?>
                            </button>
                            <button type="button" class="btn btn-outline btn-sm helpful-btn" data-helpful="no">
                                <i class="far fa-thumbs-down"></i> <?= __('no') ?>
                            </button>
                        </div>
                    </div>

                    <?php if (!empty($article['tags'])): ?>
                        <div class="article-tags">
                            <i class="fas fa-tags"></i>
                            <?php foreach (explode(',', $article['tags']) as $tag): ?>
                                <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </footer>
            </article>
        </div>

        <!-- LLM Q&A Section -->
        <?php
        if (!empty($article['enable_llm_qa']) && !empty($article['llm_qa_content'])) {
            $llmQaItems = json_decode($article['llm_qa_content'], true) ?: [];
            $llmQaEntityType = 'knowledge_article';
            $llmQaEntityId = $article['id'];
            include TEMPLATES_PATH . '/frontend/partials/llm-qa-section.php';
        }
        ?>

        <!-- Related Articles -->
        <?php if (!empty($relatedArticles)): ?>
            <div class="related-articles">
                <h3><?= __('related_articles') ?></h3>
                <div class="related-grid">
                    <?php foreach ($relatedArticles as $related): ?>
                        <a href="<?= _url('/ayuda/' . htmlspecialchars($related['slug'])) ?>" class="related-card">
                            <span class="related-category"><?= htmlspecialchars($related['category_name'] ?? 'General') ?></span>
                            <h4><?= htmlspecialchars($related['title']) ?></h4>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Breadcrumb */
.page-breadcrumb {
    padding: calc(var(--header-height) + var(--spacing-lg)) 0 var(--spacing-md);
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

/* Knowledge Article Section */
.knowledge-article-section {
    background-color: var(--color-gray-50);
    padding-top: var(--spacing-lg);
}

.knowledge-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: var(--spacing-xl);
    /* align-items removed - sidebar must stretch full height for sticky to work */
}

/* Sidebar Left - sticky styles moved to frontend.css */

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

/* Article */
.knowledge-article {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--spacing-2xl);
    min-width: 0;
}

.article-header {
    margin-bottom: var(--spacing-xl);
}

.article-category-badge {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-sm);
    background: var(--color-primary-lighter);
    color: var(--color-primary);
    font-size: var(--font-size-xs);
    font-weight: 600;
    border-radius: var(--radius-full);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: var(--spacing-sm);
}

.knowledge-article h1 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-md);
    color: var(--color-dark);
}

.article-meta {
    display: flex;
    gap: var(--spacing-lg);
    color: var(--color-gray-500);
    font-size: var(--font-size-sm);
}

.article-meta i {
    margin-right: var(--spacing-xs);
}

.article-featured-image {
    margin-bottom: var(--spacing-xl);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.article-featured-image img {
    width: 100%;
    height: auto;
    display: block;
}

.article-excerpt {
    padding: var(--spacing-lg);
    background: var(--color-gray-50);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-xl);
    border-left: 4px solid var(--color-primary);
}

.article-excerpt p {
    margin: 0;
    font-size: var(--font-size-lg);
    color: var(--color-gray-700);
}

.article-content {
    font-size: var(--font-size-base);
    line-height: 1.8;
    color: var(--color-gray-800);
}

.article-content h2 {
    font-size: var(--font-size-xl);
    margin-top: var(--spacing-2xl);
    margin-bottom: var(--spacing-md);
    color: var(--color-dark);
}

.article-content h3 {
    font-size: var(--font-size-lg);
    margin-top: var(--spacing-xl);
    margin-bottom: var(--spacing-sm);
    color: var(--color-dark);
}

.article-content p {
    margin-bottom: var(--spacing-md);
}

.article-content ul,
.article-content ol {
    margin-bottom: var(--spacing-md);
    padding-left: var(--spacing-xl);
}

.article-content li {
    margin-bottom: var(--spacing-xs);
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-lg);
    margin: var(--spacing-lg) 0;
}

.article-content code {
    background: var(--color-gray-100);
    padding: 2px 6px;
    border-radius: var(--radius-sm);
    font-family: monospace;
    font-size: 0.9em;
}

.article-content pre {
    background: var(--color-gray-900);
    color: var(--color-gray-100);
    padding: var(--spacing-lg);
    border-radius: var(--radius-lg);
    overflow-x: auto;
    margin: var(--spacing-lg) 0;
}

.article-content pre code {
    background: none;
    padding: 0;
    color: inherit;
}

.article-content blockquote {
    border-left: 4px solid var(--color-primary);
    padding-left: var(--spacing-lg);
    margin: var(--spacing-lg) 0;
    color: var(--color-gray-600);
    font-style: italic;
}

/* Video */
.article-video {
    margin-top: var(--spacing-2xl);
}

.article-video h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-md);
}

.video-embed {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
    border-radius: var(--radius-lg);
}

.video-embed iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Footer */
.article-footer {
    margin-top: var(--spacing-2xl);
    padding-top: var(--spacing-xl);
    border-top: 1px solid var(--color-gray-200);
}

.article-helpful {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.article-helpful span {
    color: var(--color-gray-600);
}

.helpful-buttons {
    display: flex;
    gap: var(--spacing-sm);
}

.helpful-btn.active {
    background: var(--color-primary);
    color: var(--color-white);
    border-color: var(--color-primary);
}

.article-tags {
    margin-top: var(--spacing-lg);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
}

.article-tags i {
    color: var(--color-gray-400);
}

.tag {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-sm);
    background: var(--color-gray-100);
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    border-radius: var(--radius-full);
}

/* Related Articles */
.related-articles {
    margin-top: var(--spacing-2xl);
}

.related-articles h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-lg);
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-md);
}

.related-card {
    background: var(--color-white);
    padding: var(--spacing-lg);
    border-radius: var(--radius-lg);
    text-decoration: none;
    transition: all var(--transition);
}

.related-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.related-category {
    font-size: var(--font-size-xs);
    color: var(--color-primary);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.related-card h4 {
    font-size: var(--font-size-base);
    color: var(--color-dark);
    margin-top: var(--spacing-xs);
}

/* Sidebar Styles (shared) */
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

.sidebar-category-toggle i {
    font-size: 12px;
    transition: transform var(--transition);
}

.sidebar-category.collapsed .sidebar-category-toggle i {
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
    background: var(--color-primary-lighter);
}

/* Responsive */
@media (max-width: 1024px) {
    .knowledge-layout {
        grid-template-columns: 1fr;
    }

    /* Sticky disabled via frontend.css, only set order here */
    .knowledge-sidebar-left {
        order: 2;
    }

    .knowledge-article {
        order: 1;
    }

    .related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .knowledge-article {
        padding: var(--spacing-lg);
    }

    .knowledge-article h1 {
        font-size: var(--font-size-2xl);
    }

    .article-meta {
        flex-direction: column;
        gap: var(--spacing-xs);
    }

    .related-grid {
        grid-template-columns: 1fr;
    }

    .article-helpful {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<script>
// Toggle sidebar category
function toggleSidebarCategory(button) {
    const category = button.closest('.sidebar-category');
    category.classList.toggle('collapsed');
}

// Helpful buttons
document.querySelectorAll('.helpful-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.helpful-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
