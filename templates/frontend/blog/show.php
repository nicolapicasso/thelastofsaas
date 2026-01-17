<?php
/**
 * Blog Post Detail Template
 * Omniwallet CMS
 */
?>

<!-- Post Header -->
<article class="blog-post">
    <header class="post-header">
        <div class="container">
            <?php if ($post['category_name'] ?? false): ?>
                <a href="/blog?categoria=<?= htmlspecialchars($post['category_slug']) ?>" class="post-category">
                    <?= htmlspecialchars($post['category_name']) ?>
                </a>
            <?php endif; ?>

            <h1><?= htmlspecialchars($post['title']) ?></h1>

            <div class="post-meta">
                <span>
                    <i class="far fa-calendar"></i>
                    <?= date('d M Y', strtotime($post['published_at'] ?? $post['created_at'])) ?>
                </span>
                <?php if ($post['author_name'] ?? false): ?>
                    <span>
                        <i class="far fa-user"></i>
                        <?= htmlspecialchars($post['author_name']) ?>
                    </span>
                <?php endif; ?>
                <?php if ($post['read_time'] ?? false): ?>
                    <span>
                        <i class="far fa-clock"></i>
                        <?= $post['read_time'] ?> min de lectura
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php if ($post['hero_image']): ?>
        <div class="post-hero">
            <div class="container">
                <img src="<?= htmlspecialchars($post['hero_image']) ?>"
                     alt="<?= htmlspecialchars($post['title']) ?>">
            </div>
        </div>
    <?php endif; ?>

    <div class="post-body">
        <div class="container">
            <div class="post-layout">
                <!-- Main Content -->
                <div class="post-content">
                    <?php if ($post['excerpt']): ?>
                        <p class="post-excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>
                    <?php endif; ?>

                    <div class="post-text">
                        <?= $post['content'] ?>
                    </div>

                    <?php if (!empty($post['tags'])): ?>
                        <div class="post-tags">
                            <span>Tags:</span>
                            <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                <a href="/blog?tag=<?= urlencode(trim($tag)) ?>" class="tag">
                                    <?= htmlspecialchars(trim($tag)) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Share -->
                    <div class="post-share">
                        <span>Compartir:</span>
                        <div class="share-buttons">
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($post['title']) ?>"
                               target="_blank" rel="noopener" class="share-btn twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($currentUrl) ?>&title=<?= urlencode($post['title']) ?>"
                               target="_blank" rel="noopener" class="share-btn linkedin">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>"
                               target="_blank" rel="noopener" class="share-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <button class="share-btn copy" data-url="<?= htmlspecialchars($currentUrl) ?>">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="post-sidebar">
                    <!-- Author -->
                    <?php if ($post['author_name'] ?? false): ?>
                        <div class="sidebar-widget author-widget">
                            <?php if ($post['author_avatar'] ?? false): ?>
                                <img src="<?= htmlspecialchars($post['author_avatar']) ?>"
                                     alt="<?= htmlspecialchars($post['author_name']) ?>"
                                     class="author-avatar">
                            <?php endif; ?>
                            <h4><?= htmlspecialchars($post['author_name']) ?></h4>
                            <?php if ($post['author_bio'] ?? false): ?>
                                <p><?= htmlspecialchars($post['author_bio']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Table of Contents -->
                    <div class="sidebar-widget toc-widget" id="toc-widget">
                        <h4>Contenido</h4>
                        <nav id="table-of-contents">
                            <!-- Generated by JavaScript -->
                        </nav>
                    </div>

                    <!-- Newsletter -->
                    <div class="sidebar-widget newsletter-widget">
                        <h4>Newsletter</h4>
                        <p>Recibe las últimas novedades</p>
                        <form class="newsletter-form">
                            <input type="email" placeholder="tu@email.com" required>
                            <button type="submit" class="btn btn-primary">Suscribirse</button>
                        </form>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <!-- LLM Q&A Section -->
    <?php
    if (!empty($post['enable_llm_qa']) && !empty($post['llm_qa_content'])) {
        $llmQaItems = json_decode($post['llm_qa_content'], true) ?: [];
        $llmQaEntityType = 'post';
        $llmQaEntityId = $post['id'];
        include TEMPLATES_PATH . '/frontend/partials/llm-qa-section.php';
    }
    ?>

    <!-- Related Posts -->
    <?php if (!empty($relatedPosts)): ?>
        <section class="related-posts section">
            <div class="container">
                <h2>Artículos relacionados</h2>
                <div class="posts-grid">
                    <?php foreach ($relatedPosts as $related): ?>
                        <article class="post-card">
                            <?php if ($related['thumbnail']): ?>
                                <a href="/blog/<?= htmlspecialchars($related['slug']) ?>" class="post-image">
                                    <img src="<?= htmlspecialchars($related['thumbnail']) ?>"
                                         alt="<?= htmlspecialchars($related['title']) ?>">
                                </a>
                            <?php endif; ?>
                            <div class="post-content">
                                <h3>
                                    <a href="/blog/<?= htmlspecialchars($related['slug']) ?>">
                                        <?= htmlspecialchars($related['title']) ?>
                                    </a>
                                </h3>
                                <div class="post-meta">
                                    <span>
                                        <i class="far fa-calendar"></i>
                                        <?= date('d M Y', strtotime($related['published_at'] ?? $related['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</article>

<style>
/* Post Header */
.post-header {
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-xl);
    background-color: var(--color-gray-50);
    text-align: center;
}

.post-header .post-category {
    display: inline-block;
    background-color: var(--color-primary);
    color: var(--color-white);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    font-weight: 600;
    margin-bottom: var(--spacing-md);
}

.post-header h1 {
    font-size: var(--font-size-4xl);
    max-width: 900px;
    margin: 0 auto var(--spacing-lg);
    line-height: 1.2;
}

.post-header .post-meta {
    display: flex;
    justify-content: center;
    gap: var(--spacing-lg);
    color: var(--color-gray-500);
    font-size: var(--font-size-sm);
}

.post-header .post-meta i {
    margin-right: var(--spacing-xs);
}

/* Post Hero */
.post-hero {
    margin-top: calc(-1 * var(--spacing-xl));
    margin-bottom: var(--spacing-2xl);
}

.post-hero img {
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
    display: block;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
}

/* Post Layout */
.post-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: var(--spacing-2xl);
}

/* Post Content */
.post-content {
    max-width: 100%;
}

.post-excerpt {
    font-size: var(--font-size-xl);
    color: var(--color-gray-600);
    line-height: 1.6;
    margin-bottom: var(--spacing-xl);
    padding-bottom: var(--spacing-xl);
    border-bottom: 1px solid var(--color-gray-200);
}

.post-text {
    font-size: var(--font-size-lg);
    line-height: 1.8;
    color: var(--color-gray-700);
}

.post-text h2 {
    font-size: var(--font-size-2xl);
    margin-top: var(--spacing-2xl);
    margin-bottom: var(--spacing-md);
}

.post-text h3 {
    font-size: var(--font-size-xl);
    margin-top: var(--spacing-xl);
    margin-bottom: var(--spacing-md);
}

.post-text p {
    margin-bottom: var(--spacing-md);
}

.post-text ul,
.post-text ol {
    margin-bottom: var(--spacing-md);
    padding-left: var(--spacing-xl);
}

.post-text li {
    margin-bottom: var(--spacing-sm);
}

.post-text img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-lg);
    margin: var(--spacing-lg) 0;
}

.post-text blockquote {
    border-left: 4px solid var(--color-primary);
    padding-left: var(--spacing-lg);
    margin: var(--spacing-xl) 0;
    font-style: italic;
    color: var(--color-gray-600);
}

.post-text code {
    background-color: var(--color-gray-100);
    padding: 2px 6px;
    border-radius: var(--radius-sm);
    font-family: monospace;
}

.post-text pre {
    background-color: var(--color-gray-900);
    color: var(--color-gray-100);
    padding: var(--spacing-lg);
    border-radius: var(--radius-lg);
    overflow-x: auto;
    margin: var(--spacing-lg) 0;
}

.post-text pre code {
    background: none;
    padding: 0;
}

/* Tags */
.post-tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
    align-items: center;
    margin-top: var(--spacing-2xl);
    padding-top: var(--spacing-xl);
    border-top: 1px solid var(--color-gray-200);
}

.post-tags > span {
    color: var(--color-gray-500);
    font-weight: 600;
}

.tag {
    background-color: var(--color-gray-100);
    color: var(--color-gray-700);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    transition: all var(--transition);
}

.tag:hover {
    background-color: var(--color-primary);
    color: var(--color-white);
}

/* Share */
.post-share {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    margin-top: var(--spacing-xl);
}

.post-share > span {
    color: var(--color-gray-500);
    font-weight: 600;
}

.share-buttons {
    display: flex;
    gap: var(--spacing-sm);
}

.share-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: var(--color-white);
    transition: all var(--transition);
    border: none;
    cursor: pointer;
}

.share-btn.twitter { background-color: #1DA1F2; }
.share-btn.linkedin { background-color: #0077B5; }
.share-btn.facebook { background-color: #1877F2; }
.share-btn.copy { background-color: var(--color-gray-400); }

.share-btn:hover {
    transform: translateY(-2px);
    opacity: 0.9;
}

/* Sidebar */
.post-sidebar {
    position: sticky;
    top: calc(var(--header-height) + var(--spacing-lg));
    height: fit-content;
}

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

/* Author Widget */
.author-widget {
    text-align: center;
}

.author-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin-bottom: var(--spacing-sm);
}

.author-widget p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
}

/* TOC Widget */
#table-of-contents {
    font-size: var(--font-size-sm);
}

#table-of-contents a {
    display: block;
    padding: var(--spacing-xs) 0;
    color: var(--color-gray-600);
    border-left: 2px solid transparent;
    padding-left: var(--spacing-sm);
    transition: all var(--transition);
}

#table-of-contents a:hover,
#table-of-contents a.active {
    color: var(--color-primary);
    border-left-color: var(--color-primary);
}

#table-of-contents a.level-3 {
    padding-left: var(--spacing-lg);
}

/* Newsletter Widget */
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
}

/* Related Posts */
.related-posts {
    background-color: var(--color-gray-50);
}

.related-posts h2 {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.related-posts .posts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-lg);
}

.related-posts .post-card {
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.related-posts .post-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.related-posts .post-image {
    display: block;
    aspect-ratio: 16/9;
    overflow: hidden;
}

.related-posts .post-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-posts .post-content {
    padding: var(--spacing-lg);
}

.related-posts .post-content h3 {
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-sm);
}

.related-posts .post-content h3 a {
    color: var(--color-dark);
}

.related-posts .post-meta {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

@media (max-width: 1024px) {
    .post-layout {
        grid-template-columns: 1fr;
    }

    .post-sidebar {
        position: static;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-lg);
    }

    .toc-widget {
        display: none;
    }
}

@media (max-width: 768px) {
    .post-header h1 {
        font-size: var(--font-size-2xl);
    }

    .post-header .post-meta {
        flex-direction: column;
        gap: var(--spacing-sm);
    }

    .post-sidebar {
        grid-template-columns: 1fr;
    }

    .related-posts .posts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate Table of Contents
    const postText = document.querySelector('.post-text');
    const toc = document.getElementById('table-of-contents');
    const headings = postText.querySelectorAll('h2, h3');

    if (headings.length > 0 && toc) {
        headings.forEach((heading, index) => {
            const id = 'heading-' + index;
            heading.id = id;

            const link = document.createElement('a');
            link.href = '#' + id;
            link.textContent = heading.textContent;
            link.className = 'level-' + heading.tagName.toLowerCase().replace('h', '');
            toc.appendChild(link);
        });

        // Highlight active TOC item on scroll
        const tocLinks = toc.querySelectorAll('a');
        window.addEventListener('scroll', function() {
            let current = '';
            headings.forEach(heading => {
                const rect = heading.getBoundingClientRect();
                if (rect.top <= 100) {
                    current = heading.id;
                }
            });

            tocLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    } else if (toc) {
        document.getElementById('toc-widget').style.display = 'none';
    }

    // Copy URL button
    const copyBtn = document.querySelector('.share-btn.copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            const url = this.dataset.url;
            navigator.clipboard.writeText(url).then(() => {
                const icon = this.querySelector('i');
                icon.className = 'fas fa-check';
                setTimeout(() => {
                    icon.className = 'fas fa-link';
                }, 2000);
            });
        });
    }
});
</script>
