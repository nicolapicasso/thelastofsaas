<?php
/**
 * Posts Block Template
 * Displays recent blog posts
 * Omniwallet CMS
 */

use App\Models\Post;

// Get posts from database
$postModel = new Post();
$limit = $settings['limit'] ?? 3;
$categoryId = $content['category_id'] ?? null;

if ($categoryId) {
    $posts = $postModel->getByCategory($categoryId, $limit);
} else {
    $posts = $postModel->getRecent($limit);
}

$columns = $settings['columns'] ?? 3;
?>

<section class="block block-posts section <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $renderer->getBlockStyles($settings) ?>">
    <div class="container">
        <?php if (!empty($content['title'])): ?>
            <div class="section-header">
                <h2><?= htmlspecialchars($content['title']) ?></h2>
                <?php if (!empty($content['subtitle'])): ?>
                    <p><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($posts)): ?>
            <div class="posts-grid columns-<?= $columns ?>">
                <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <?php if ($post['thumbnail']): ?>
                            <a href="<?= _url('/blog/' . htmlspecialchars($post['slug'])) ?>" class="post-image">
                                <img src="<?= htmlspecialchars($post['thumbnail']) ?>"
                                     alt="<?= htmlspecialchars($post['title']) ?>"
                                     loading="lazy">
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
                                <span>
                                    <i class="far fa-calendar"></i>
                                    <?= date('d M Y', strtotime($post['published_at'] ?? $post['created_at'])) ?>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($content['show_more']) && !empty($content['more_url'])): ?>
                <div class="section-footer">
                    <a href="<?= htmlspecialchars($content['more_url']) ?>" class="btn btn-outline">
                        <?= htmlspecialchars($content['more_text'] ?? 'Ver todos los artículos') ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>No hay artículos disponibles</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.block-posts .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
}

.block-posts .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
}

.block-posts .section-header p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
}

.block-posts .posts-grid {
    display: grid;
    gap: var(--spacing-xl);
}

.block-posts .posts-grid.columns-2 {
    grid-template-columns: repeat(2, 1fr);
}

.block-posts .posts-grid.columns-3 {
    grid-template-columns: repeat(3, 1fr);
}

.block-posts .posts-grid.columns-4 {
    grid-template-columns: repeat(4, 1fr);
}

.block-posts .post-card {
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.block-posts .post-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-4px);
}

.block-posts .post-image {
    display: block;
    aspect-ratio: 16/10;
    overflow: hidden;
}

.block-posts .post-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-slow);
}

.block-posts .post-card:hover .post-image img {
    transform: scale(1.05);
}

.block-posts .post-content {
    padding: var(--spacing-lg);
}

.block-posts .post-category {
    display: inline-block;
    font-size: var(--font-size-sm);
    color: var(--color-primary);
    font-weight: 600;
    margin-bottom: var(--spacing-sm);
}

.block-posts .post-content h3 {
    font-size: var(--font-size-lg);
    line-height: 1.4;
    margin-bottom: var(--spacing-sm);
}

.block-posts .post-content h3 a {
    color: var(--color-dark);
}

.block-posts .post-content h3 a:hover {
    color: var(--color-primary);
}

.block-posts .post-content p {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-md);
}

.block-posts .post-meta {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

.block-posts .post-meta i {
    margin-right: var(--spacing-xs);
}

.block-posts .section-footer {
    text-align: center;
    margin-top: var(--spacing-2xl);
}

.block-posts .section-footer .btn i {
    margin-left: var(--spacing-sm);
}

.block-posts .empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-500);
}

@media (max-width: 1024px) {
    .block-posts .posts-grid.columns-4,
    .block-posts .posts-grid.columns-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .block-posts .posts-grid.columns-4,
    .block-posts .posts-grid.columns-3,
    .block-posts .posts-grid.columns-2 {
        grid-template-columns: 1fr;
    }
}
</style>
