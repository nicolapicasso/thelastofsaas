<?php
/**
 * Blog Index Template
 * Omniwallet CMS
 */
?>

<!-- Blog Header -->
<section class="blog-header">
    <div class="container">
        <h1><?= $currentCategory ? htmlspecialchars($currentCategory['name']) : 'Blog' ?></h1>
        <p>Noticias, guías y artículos sobre fintech y pagos digitales</p>
    </div>
</section>

<!-- Blog Content -->
<section class="section">
    <div class="container">
        <div class="blog-layout">
            <!-- Main Content -->
            <div class="blog-main">
                <!-- Featured Posts -->
                <?php if (empty($currentCategory) && !empty($featuredPosts)): ?>
                    <div class="featured-posts">
                        <?php foreach ($featuredPosts as $i => $post): ?>
                            <article class="post-card <?= $i === 0 ? 'featured' : '' ?>">
                                <?php if ($post['thumbnail'] || $post['hero_image']): ?>
                                    <a href="<?= _url('/blog/' . htmlspecialchars($post['slug'])) ?>" class="post-image">
                                        <img src="<?= htmlspecialchars($post['hero_image'] ?? $post['thumbnail']) ?>"
                                             alt="<?= htmlspecialchars($post['title']) ?>">
                                    </a>
                                <?php endif; ?>
                                <div class="post-content">
                                    <?php if ($post['category_name'] ?? false): ?>
                                        <span class="post-category"><?= htmlspecialchars($post['category_name']) ?></span>
                                    <?php endif; ?>
                                    <h2>
                                        <a href="<?= _url('/blog/' . htmlspecialchars($post['slug'])) ?>">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </h2>
                                    <?php if ($post['excerpt']): ?>
                                        <p><?= htmlspecialchars($post['excerpt']) ?></p>
                                    <?php endif; ?>
                                    <div class="post-meta">
                                        <span><i class="far fa-calendar"></i> <?= date('d M Y', strtotime($post['published_at'] ?? $post['created_at'])) ?></span>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!empty($posts)): ?>
                        <h2 class="section-title">Últimos artículos</h2>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Posts Grid -->
                <?php if (!empty($posts)): ?>
                    <div class="posts-grid">
                        <?php foreach ($posts as $post): ?>
                            <article class="post-card">
                                <?php if ($post['thumbnail']): ?>
                                    <a href="<?= _url('/blog/' . htmlspecialchars($post['slug'])) ?>" class="post-image">
                                        <img src="<?= htmlspecialchars($post['thumbnail']) ?>"
                                             alt="<?= htmlspecialchars($post['title']) ?>">
                                    </a>
                                <?php endif; ?>
                                <div class="post-content">
                                    <?php if ($post['category_name'] ?? false): ?>
                                        <span class="post-category"><?= htmlspecialchars($post['category_name']) ?></span>
                                    <?php endif; ?>
                                    <h3>
                                        <a href="<?= _url('/blog/' . htmlspecialchars($post['slug'])) ?>">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </h3>
                                    <?php if ($post['excerpt']): ?>
                                        <p><?= htmlspecialchars(substr($post['excerpt'], 0, 120)) ?>...</p>
                                    <?php endif; ?>
                                    <div class="post-meta">
                                        <span><i class="far fa-calendar"></i> <?= date('d M Y', strtotime($post['published_at'] ?? $post['created_at'])) ?></span>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <div class="pagination">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <a href="?page=<?= $pagination['current_page'] - 1 ?><?= $currentCategory ? '&categoria=' . $currentCategory['slug'] : '' ?>" class="btn btn-outline btn-sm">
                                    <i class="fas fa-chevron-left"></i> Anterior
                                </a>
                            <?php endif; ?>

                            <span class="pagination-info">
                                Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?>
                            </span>

                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <a href="?page=<?= $pagination['current_page'] + 1 ?><?= $currentCategory ? '&categoria=' . $currentCategory['slug'] : '' ?>" class="btn btn-outline btn-sm">
                                    Siguiente <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-newspaper"></i>
                        <h3>No hay artículos</h3>
                        <p>Vuelve pronto para ver nuevos contenidos</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="blog-sidebar">
                <!-- Categories -->
                <div class="sidebar-widget">
                    <h4>Categorías</h4>
                    <ul class="category-list">
                        <li>
                            <a href="<?= _url('/blog') ?>" class="<?= !$currentCategory ? 'active' : '' ?>">
                                Todos los artículos
                            </a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="<?= _url('/blog') ?>?categoria=<?= htmlspecialchars($category['slug']) ?>"
                                   class="<?= $currentCategory && $currentCategory['id'] === $category['id'] ? 'active' : '' ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="sidebar-widget newsletter-widget">
                    <h4>Newsletter</h4>
                    <p>Recibe las últimas novedades en tu email</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="tu@email.com" required>
                        <button type="submit" class="btn btn-primary">Suscribirse</button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</section>

<style>
/* Blog Header */
.blog-header {
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-xl);
    background-color: var(--color-gray-50);
    text-align: center;
}

.blog-header h1 {
    margin-bottom: var(--spacing-sm);
}

.blog-header p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
}

/* Blog Layout */
.blog-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: var(--spacing-2xl);
}

.section-title {
    font-size: var(--font-size-2xl);
    margin: var(--spacing-2xl) 0 var(--spacing-lg);
}

/* Featured Posts */
.featured-posts {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--spacing-lg);
}

.featured-posts .post-card.featured {
    grid-row: span 2;
}

.featured-posts .post-card.featured .post-image {
    aspect-ratio: 4/3;
}

.featured-posts .post-card.featured h2 {
    font-size: var(--font-size-2xl);
}

/* Post Cards */
.posts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-lg);
}

.post-card {
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.post-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.post-image {
    display: block;
    aspect-ratio: 16/9;
    overflow: hidden;
}

.post-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-slow);
}

.post-card:hover .post-image img {
    transform: scale(1.05);
}

.post-content {
    padding: var(--spacing-lg);
}

.post-category {
    display: inline-block;
    font-size: var(--font-size-sm);
    color: var(--color-primary);
    font-weight: 600;
    margin-bottom: var(--spacing-sm);
}

.post-content h2,
.post-content h3 {
    margin-bottom: var(--spacing-sm);
    line-height: 1.3;
}

.post-content h2 a,
.post-content h3 a {
    color: var(--color-dark);
}

.post-content h2 a:hover,
.post-content h3 a:hover {
    color: var(--color-primary);
}

.post-content p {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-md);
}

.post-meta {
    display: flex;
    gap: var(--spacing-md);
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

.post-meta i {
    margin-right: var(--spacing-xs);
}

/* Sidebar */
.sidebar-widget {
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
    box-shadow: var(--shadow-sm);
}

.sidebar-widget h4 {
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-md);
    padding-bottom: var(--spacing-sm);
    border-bottom: 2px solid var(--color-primary);
}

.category-list {
    list-style: none;
}

.category-list li {
    margin-bottom: var(--spacing-xs);
}

.category-list a {
    display: block;
    padding: var(--spacing-sm);
    color: var(--color-gray-700);
    border-radius: var(--radius-md);
    transition: all var(--transition);
}

.category-list a:hover,
.category-list a.active {
    background-color: var(--color-gray-50);
    color: var(--color-primary);
}

.newsletter-widget {
    background-color: var(--color-primary);
    color: var(--color-white);
}

.newsletter-widget h4 {
    color: var(--color-white);
    border-bottom-color: rgba(255,255,255,0.3);
}

.newsletter-widget p {
    color: rgba(255,255,255,0.9);
    font-size: var(--font-size-sm);
}

.newsletter-form {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.newsletter-form input {
    padding: var(--spacing-sm) var(--spacing-md);
    border: none;
    border-radius: var(--radius-md);
    font-size: var(--font-size-base);
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: var(--spacing-md);
    margin-top: var(--spacing-2xl);
}

.pagination-info {
    color: var(--color-gray-500);
}

/* Empty State */
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
    .blog-layout {
        grid-template-columns: 1fr;
    }

    .blog-sidebar {
        order: -1;
    }
}

@media (max-width: 768px) {
    .featured-posts,
    .posts-grid {
        grid-template-columns: 1fr;
    }

    .featured-posts .post-card.featured {
        grid-row: span 1;
    }
}
</style>
