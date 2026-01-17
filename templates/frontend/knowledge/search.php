<?php
/**
 * Knowledge Base Search Results Template
 * Omniwallet CMS
 */
?>

<!-- Search Header -->
<section class="knowledge-header">
    <div class="container">
        <h1>Buscar en Base de Conocimiento</h1>

        <!-- Search -->
        <div class="knowledge-search">
            <form action="/ayuda/buscar" method="GET" id="knowledge-search-form">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text"
                           name="q"
                           id="knowledge-search-input"
                           placeholder="Buscar artículos..."
                           value="<?= htmlspecialchars($query) ?>"
                           autocomplete="off">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Main Content with Sidebar -->
<section class="section knowledge-content">
    <div class="container">
        <div class="knowledge-layout">
            <!-- Main Area -->
            <div class="knowledge-main">
                <div class="content-section">
                    <?php if (!empty($query)): ?>
                        <div class="search-results-header">
                            <h2>
                                <?php if (count($results) > 0): ?>
                                    <?= count($results) ?> resultado<?= count($results) !== 1 ? 's' : '' ?> para "<?= htmlspecialchars($query) ?>"
                                <?php else: ?>
                                    No se encontraron resultados para "<?= htmlspecialchars($query) ?>"
                                <?php endif; ?>
                            </h2>
                        </div>

                        <?php if (!empty($results)): ?>
                            <div class="articles-list">
                                <?php foreach ($results as $article): ?>
                                    <a href="/ayuda/<?= htmlspecialchars($article['slug']) ?>" class="article-item">
                                        <div class="article-item-content">
                                            <span class="article-category"><?= htmlspecialchars($article['category_name'] ?? 'General') ?></span>
                                            <h4><?= htmlspecialchars($article['title']) ?></h4>
                                            <?php if (!empty($article['excerpt'])): ?>
                                                <p><?= htmlspecialchars(substr($article['excerpt'], 0, 150)) ?>...</p>
                                            <?php endif; ?>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-results">
                                <i class="fas fa-search"></i>
                                <h3>No encontramos resultados</h3>
                                <p>Intenta con otros términos de búsqueda o explora las categorías disponibles.</p>
                                <a href="/ayuda" class="btn btn-primary">Ver todas las categorías</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="search-prompt">
                            <i class="fas fa-search"></i>
                            <h3>Busca en nuestra base de conocimiento</h3>
                            <p>Escribe lo que estás buscando en el campo de arriba.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="knowledge-sidebar">
                <div class="sidebar-card">
                    <h3>Índice de contenido</h3>
                    <nav class="sidebar-nav">
                        <?php if (!empty($groupedArticles)): ?>
                            <?php foreach ($groupedArticles as $categoryId => $group): ?>
                                <div class="sidebar-category collapsed">
                                    <button class="sidebar-category-toggle" onclick="toggleSidebarCategory(this)">
                                        <span><?= htmlspecialchars($group['name']) ?></span>
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <ul class="sidebar-articles">
                                        <?php foreach ($group['articles'] as $article): ?>
                                            <li>
                                                <a href="/ayuda/<?= htmlspecialchars($article['slug']) ?>">
                                                    <?= htmlspecialchars($article['title']) ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="sidebar-empty">No hay artículos disponibles.</p>
                        <?php endif; ?>
                    </nav>
                </div>

                <!-- Support CTA -->
                <div class="sidebar-card sidebar-cta">
                    <i class="fas fa-headset"></i>
                    <h4>¿Necesitas ayuda?</h4>
                    <p>Nuestro equipo está aquí para ayudarte</p>
                    <a href="/contacto" class="btn btn-outline btn-sm">Contactar</a>
                </div>
            </aside>
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
    font-size: var(--font-size-3xl);
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

/* Content */
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

.content-section {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
}

/* Search Results Header */
.search-results-header {
    margin-bottom: var(--spacing-lg);
    padding-bottom: var(--spacing-md);
    border-bottom: 1px solid var(--color-gray-200);
}

.search-results-header h2 {
    font-size: var(--font-size-lg);
    color: var(--color-dark);
}

/* Articles List */
.articles-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.article-item {
    display: flex;
    align-items: center;
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

.article-item > i:last-child {
    color: var(--color-gray-300);
}

/* No Results / Search Prompt */
.no-results,
.search-prompt {
    text-align: center;
    padding: var(--spacing-2xl);
}

.no-results i,
.search-prompt i {
    font-size: 48px;
    color: var(--color-gray-300);
    margin-bottom: var(--spacing-md);
}

.no-results h3,
.search-prompt h3 {
    color: var(--color-dark);
    margin-bottom: var(--spacing-sm);
}

.no-results p,
.search-prompt p {
    color: var(--color-gray-500);
    margin-bottom: var(--spacing-lg);
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
    .knowledge-layout {
        grid-template-columns: 1fr;
    }

    .knowledge-sidebar {
        position: static;
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
