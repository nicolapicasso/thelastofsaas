<?php
/**
 * Category Detail Template
 * We're Sinapsis CMS
 */
?>

<article class="category-detail">
    <!-- Hero Section -->
    <section class="category-hero">
        <div class="container">
            <?php if (!empty($category['featured_image'])): ?>
                <div class="hero-background">
                    <img src="<?= htmlspecialchars($category['featured_image']) ?>" alt="">
                </div>
            <?php endif; ?>
            <div class="hero-content">
                <?php if (!empty($category['icon_image'])): ?>
                    <div class="hero-icon-image">
                        <img src="<?= htmlspecialchars($category['icon_image']) ?>" alt="<?= htmlspecialchars($category['name']) ?>">
                    </div>
                <?php elseif (!empty($category['icon'])): ?>
                    <div class="hero-icon">
                        <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                    </div>
                <?php endif; ?>
                <h1><?= htmlspecialchars($category['name']) ?></h1>
                <?php if (!empty($category['short_description'])): ?>
                    <p class="hero-subtitle"><?= htmlspecialchars($category['short_description']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="container">
            <nav class="breadcrumb">
                <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                <span>/</span>
                <a href="<?= _url('/categorias') ?>"><?= __('categories') ?></a>
                <span>/</span>
                <span><?= htmlspecialchars($category['name']) ?></span>
            </nav>
        </div>
    </div>

    <!-- Description -->
    <?php if (!empty($category['description'])): ?>
    <section class="section">
        <div class="container">
            <div class="category-description">
                <?= $category['description'] ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Services in Category -->
    <?php if (!empty($content['services'])): ?>
    <section class="section bg-light">
        <div class="container">
            <div class="section-header">
                <h2><?= __('services') ?></h2>
                <p><?= __('services_in_category') ?></p>
            </div>
            <div class="services-grid">
                <?php foreach ($content['services'] as $service): ?>
                    <a href="<?= _url('/servicios/' . $service['slug']) ?>" class="service-card">
                        <div class="service-icon">
                            <i class="<?= htmlspecialchars($service['icon'] ?? 'fas fa-cog') ?>"></i>
                        </div>
                        <h3><?= htmlspecialchars($service['title']) ?></h3>
                        <?php if (!empty($service['short_description'])): ?>
                            <?php
                            $desc = strip_tags($service['short_description']);
                            $desc = mb_strlen($desc) > 150 ? mb_substr($desc, 0, 150) . '...' : $desc;
                            ?>
                            <p><?= htmlspecialchars($desc) ?></p>
                        <?php endif; ?>
                        <span class="card-link"><?= __('read_more') ?> <i class="fas fa-arrow-right"></i></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Tools in Category -->
    <?php if (!empty($content['tools'])): ?>
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2><?= __('tools') ?></h2>
                <p><?= __('tools_in_category') ?></p>
            </div>
            <div class="tools-grid">
                <?php foreach ($content['tools'] as $tool): ?>
                    <a href="<?= _url('/herramientas/' . $tool['slug']) ?>" class="tool-card">
                        <div class="tool-logo">
                            <?php if (!empty($tool['logo'])): ?>
                                <img src="<?= htmlspecialchars($tool['logo']) ?>" alt="<?= htmlspecialchars($tool['title']) ?>">
                            <?php else: ?>
                                <i class="fas fa-puzzle-piece"></i>
                            <?php endif; ?>
                        </div>
                        <h4><?= htmlspecialchars($tool['title']) ?></h4>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Success Cases in Category -->
    <?php if (!empty($content['cases'])): ?>
    <section class="section bg-light">
        <div class="container">
            <div class="section-header">
                <h2><?= __('success_cases') ?></h2>
                <p><?= __('cases_in_category') ?></p>
            </div>
            <div class="cases-grid">
                <?php foreach ($content['cases'] as $case): ?>
                    <article class="case-card">
                        <a href="<?= _url('/casos-de-exito/' . $case['slug']) ?>">
                            <div class="case-image">
                                <?php if (!empty($case['featured_image'])): ?>
                                    <img src="<?= htmlspecialchars($case['featured_image']) ?>" alt="">
                                <?php endif; ?>
                                <?php if (!empty($case['client_name'])): ?>
                                    <span class="case-client"><?= htmlspecialchars($case['client_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="case-content">
                                <h3><?= htmlspecialchars($case['title']) ?></h3>
                                <span class="card-link"><?= __('view_case') ?> <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Blog Posts in Category -->
    <?php if (!empty($content['posts'])): ?>
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2><?= __('blog_posts') ?></h2>
                <p><?= __('posts_in_category') ?></p>
            </div>
            <div class="posts-grid">
                <?php foreach ($content['posts'] as $post): ?>
                    <article class="post-card">
                        <a href="<?= _url('/blog/' . $post['slug']) ?>">
                            <?php if (!empty($post['thumbnail'])): ?>
                                <div class="post-image">
                                    <img src="<?= htmlspecialchars($post['thumbnail']) ?>" alt="">
                                </div>
                            <?php endif; ?>
                            <div class="post-content">
                                <h3><?= htmlspecialchars($post['title']) ?></h3>
                                <?php if (!empty($post['excerpt'])): ?>
                                    <p><?= htmlspecialchars(substr(strip_tags($post['excerpt']), 0, 120)) ?>...</p>
                                <?php endif; ?>
                                <?php if (!empty($post['published_at'])): ?>
                                    <span class="post-date">
                                        <i class="far fa-calendar-alt"></i>
                                        <?= date('d M Y', strtotime($post['published_at'])) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</article>

<style>
.category-hero {
    position: relative;
    min-height: 350px;
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-3xl);
    background: linear-gradient(135deg, #264752 0%, #1a3a44 100%);
    color: white;
    overflow: hidden;
}

.category-hero .hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
}

.category-hero .hero-background img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.3;
}

.category-hero .hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 1100px;
    margin: 0 auto;
}

.category-hero h1 {
    color: white;
    margin-bottom: var(--spacing-md);
    font-size: var(--font-size-4xl);
}

.hero-icon-image {
    max-width: 200px;
    max-height: 200px;
    margin: 0 auto var(--spacing-lg);
    background: #fff;
}

.hero-icon-image img {
    max-width: 100%;
    max-height: 200px;
    object-fit: contain;
    filter: drop-shadow(0 4px 20px rgba(0, 0, 0, 0.3));
}

.hero-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto var(--spacing-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.15);
    border-radius: var(--radius-xl);
    font-size: 36px;
    color: white;
    backdrop-filter: blur(10px);
}

.hero-subtitle {
    font-size: var(--font-size-xl);
    opacity: 0.9;
}

.category-description {
    max-width: 1100px;
    margin: 0 auto;
    font-size: var(--font-size-lg);
    line-height: 1.8;
    color: var(--color-gray-700);
}

.section-header {
    text-align: center;
    margin-bottom: var(--spacing-2xl);
}

.section-header h2 {
    margin-bottom: var(--spacing-sm);
}

.section-header p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
}

/* Services Grid */
.services-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-xl);
}

.service-card {
    background: white;
    padding: var(--spacing-xl);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
    text-align: center;
}

.service-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.service-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto var(--spacing-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary-light);
    border-radius: var(--radius-lg);
    font-size: 28px;
    color: var(--color-primary);
}

.service-card h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-sm);
}

.service-card p {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-md);
}

/* Tools Grid */
.tools-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: var(--spacing-lg);
}

.tool-card {
    background: white;
    padding: var(--spacing-lg);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    text-align: center;
    transition: all var(--transition);
}

.tool-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.tool-logo {
    width: 60px;
    height: 60px;
    margin: 0 auto var(--spacing-sm);
    display: flex;
    align-items: center;
    justify-content: center;
}

.tool-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.tool-logo i {
    font-size: 32px;
    color: var(--color-gray-400);
}

.tool-card h4 {
    font-size: var(--font-size-sm);
    margin: 0;
}

/* Cases Grid */
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
    position: relative;
    height: 200px;
    background: var(--color-gray-100);
}

.case-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.case-client {
    position: absolute;
    bottom: var(--spacing-sm);
    left: var(--spacing-sm);
    padding: var(--spacing-xs) var(--spacing-sm);
    background: rgba(0,0,0,0.7);
    color: white;
    font-size: var(--font-size-xs);
    border-radius: var(--radius-sm);
}

.case-content {
    padding: var(--spacing-lg);
}

.case-content h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-sm);
}

.case-content p {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-md);
}

.card-link {
    color: var(--color-primary);
    font-weight: 600;
    font-size: var(--font-size-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

/* Posts Grid */
.posts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-xl);
}

.post-card {
    background: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.post-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.post-card a {
    display: block;
    color: inherit;
}

.post-image {
    height: 180px;
    background: var(--color-gray-100);
    overflow: hidden;
}

.post-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition);
}

.post-card:hover .post-image img {
    transform: scale(1.05);
}

.post-content {
    padding: var(--spacing-lg);
}

.post-content h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-sm);
    line-height: 1.4;
}

.post-content p {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-sm);
    line-height: 1.6;
}

.post-date {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: var(--font-size-xs);
    color: var(--color-gray-500);
}

@media (max-width: 1024px) {
    .services-grid { grid-template-columns: repeat(2, 1fr); }
    .tools-grid { grid-template-columns: repeat(4, 1fr); }
    .cases-grid { grid-template-columns: repeat(2, 1fr); }
    .posts-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .category-hero h1 { font-size: var(--font-size-2xl); }
    .services-grid { grid-template-columns: 1fr; }
    .tools-grid { grid-template-columns: repeat(3, 1fr); }
    .cases-grid { grid-template-columns: 1fr; }
    .posts-grid { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
    .tools-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
