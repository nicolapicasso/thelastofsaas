<?php
/**
 * Success Case Detail Template
 * We're Sinapsis CMS
 */

$metrics = $case['metrics_array'] ?? json_decode($case['metrics'] ?? '[]', true) ?: [];
$gallery = $case['gallery_array'] ?? json_decode($case['gallery'] ?? '[]', true) ?: [];
$galleryDisplay = $case['gallery_display'] ?? 'grid';
?>

<article class="case-detail">
    <!-- Hero Section with Image Background -->
    <section class="case-hero-section" <?php if (!empty($case['featured_image'])): ?>style="background-image: url('<?= htmlspecialchars($case['featured_image']) ?>');"<?php endif; ?>>
        <div class="case-hero-overlay"></div>
        <div class="container">
            <div class="case-hero-content">
                <?php if (!empty($case['client_logo'])): ?>
                    <div class="case-hero-logo">
                        <img src="<?= htmlspecialchars($case['client_logo']) ?>"
                             alt="<?= htmlspecialchars($case['client_name'] ?? $case['title'] ?? '') ?>">
                    </div>
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
                <a href="<?= _url('/casos-de-exito') ?>"><?= __('success_cases') ?></a>
                <span>/</span>
                <span><?= htmlspecialchars($case['title'] ?? '') ?></span>
            </nav>
        </div>
    </div>

    <!-- Key Metrics -->
    <?php if (!empty($metrics)): ?>
        <section class="case-metrics-section">
            <div class="container">
                <div class="metrics-grid">
                    <?php foreach ($metrics as $metric): ?>
                        <div class="metric-card">
                            <span class="metric-value"><?= htmlspecialchars($metric['value'] ?? '') ?></span>
                            <span class="metric-label"><?= htmlspecialchars($metric['label'] ?? '') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Case Content -->
    <div class="case-body">
        <div class="container">
            <div class="case-layout">
                <div class="case-main">
                    <!-- Case Title -->
                    <h1 class="case-company-title"><?= htmlspecialchars($case['title'] ?? '') ?></h1>
                    <?php if (!empty($case['client_name'])): ?>
                        <p class="case-client-name"><?= htmlspecialchars($case['client_name']) ?></p>
                    <?php endif; ?>

                    <!-- Challenge -->
                    <?php if (!empty($case['challenge'])): ?>
                        <section class="case-section">
                            <h2><i class="fas fa-exclamation-circle"></i> <?= __('challenge') ?></h2>
                            <div class="section-content wysiwyg-content">
                                <?= $case['challenge'] ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Solution -->
                    <?php if (!empty($case['solution'])): ?>
                        <section class="case-section">
                            <h2><i class="fas fa-lightbulb"></i> <?= __('solution') ?></h2>
                            <div class="section-content wysiwyg-content">
                                <?= $case['solution'] ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Results -->
                    <?php if (!empty($case['results'])): ?>
                        <section class="case-section">
                            <h2><i class="fas fa-chart-line"></i> <?= __('results') ?></h2>
                            <div class="section-content wysiwyg-content">
                                <?= $case['results'] ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Image Gallery -->
                    <?php if (!empty($gallery)): ?>
                        <section class="case-section case-gallery-section">
                            <h2><i class="fas fa-images"></i> <?= __('gallery') ?></h2>
                            <div class="case-gallery <?= $galleryDisplay === 'carousel' ? 'gallery-carousel' : 'gallery-grid' ?>">
                                <?php foreach ($gallery as $index => $image): ?>
                                    <?php
                                    $imageUrl = is_array($image) ? ($image['url'] ?? $image['image'] ?? '') : $image;
                                    $imageCaption = is_array($image) ? ($image['caption'] ?? '') : '';
                                    if (empty($imageUrl)) continue;
                                    ?>
                                    <div class="gallery-item" data-index="<?= $index ?>">
                                        <div class="gallery-image-wrapper">
                                            <img src="<?= htmlspecialchars($imageUrl) ?>"
                                                 alt="<?= htmlspecialchars($imageCaption ?: ($case['title'] ?? '') . ' - Imagen ' . ($index + 1)) ?>"
                                                 loading="lazy">
                                            <div class="gallery-overlay">
                                                <button type="button" class="gallery-zoom" onclick="openLightbox(<?= $index ?>)">
                                                    <i class="fas fa-expand"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php if (!empty($imageCaption)): ?>
                                            <p class="gallery-caption"><?= htmlspecialchars($imageCaption) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($galleryDisplay === 'carousel' && count($gallery) > 1): ?>
                                <div class="gallery-nav">
                                    <button type="button" class="gallery-nav-btn gallery-prev" onclick="moveCarousel(-1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <div class="gallery-dots">
                                        <?php foreach ($gallery as $index => $image): ?>
                                            <button type="button" class="gallery-dot <?= $index === 0 ? 'active' : '' ?>"
                                                    onclick="goToSlide(<?= $index ?>)"></button>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="gallery-nav-btn gallery-next" onclick="moveCarousel(1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>

                    <!-- Video -->
                    <?php if (!empty($case['video_url'])): ?>
                        <section class="case-section case-video-section">
                            <h2><i class="fas fa-play-circle"></i> <?= __('video') ?></h2>
                            <div class="video-wrapper">
                                <?php
                                $videoUrl = $case['video_url'];
                                // YouTube embed
                                if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches)):
                                ?>
                                    <iframe src="https://www.youtube.com/embed/<?= $matches[1] ?>"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                                <?php
                                // Vimeo embed
                                elseif (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $matches)):
                                ?>
                                    <iframe src="https://player.vimeo.com/video/<?= $matches[1] ?>"
                                            frameborder="0"
                                            allow="autoplay; fullscreen; picture-in-picture"
                                            allowfullscreen></iframe>
                                <?php else: ?>
                                    <video controls>
                                        <source src="<?= htmlspecialchars($videoUrl) ?>" type="video/mp4">
                                    </video>
                                <?php endif; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="case-sidebar">
                    <!-- Testimonial -->
                    <?php if (!empty($case['testimonial'])): ?>
                        <div class="testimonial-card">
                            <blockquote>
                                <p>"<?= htmlspecialchars($case['testimonial']) ?>"</p>
                            </blockquote>
                            <?php if (!empty($case['testimonial_author'])): ?>
                                <div class="testimonial-author">
                                    <strong><?= htmlspecialchars($case['testimonial_author']) ?></strong>
                                    <?php if (!empty($case['testimonial_role'])): ?>
                                        <span><?= htmlspecialchars($case['testimonial_role']) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Company Info -->
                    <?php if (!empty($case['client_name']) || !empty($case['client_industry']) || !empty($case['client_location']) || !empty($case['client_website'])): ?>
                    <div class="info-card">
                        <h4><?= __('about') ?> <?= htmlspecialchars($case['client_name'] ?? '') ?></h4>
                        <dl>
                            <?php if (!empty($case['client_industry'])): ?>
                                <dt><?= __('industry') ?></dt>
                                <dd><?= htmlspecialchars($case['client_industry']) ?></dd>
                            <?php endif; ?>
                            <?php if (!empty($case['client_location'])): ?>
                                <dt><?= __('location') ?></dt>
                                <dd><?= htmlspecialchars($case['client_location']) ?></dd>
                            <?php endif; ?>
                            <?php if (!empty($case['client_website'])): ?>
                                <dt><?= __('website') ?></dt>
                                <dd><a href="<?= htmlspecialchars($case['client_website']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars(parse_url($case['client_website'], PHP_URL_HOST)) ?></a></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                    <?php endif; ?>

                </aside>
            </div>
        </div>
    </div>

    <!-- LLM Q&A Section -->
    <?php
    if (!empty($case['enable_llm_qa']) && !empty($case['llm_qa_content'])) {
        $llmQaItems = json_decode($case['llm_qa_content'], true) ?: [];
        $llmQaEntityType = 'success_case';
        $llmQaEntityId = $case['id'];
        include TEMPLATES_PATH . '/frontend/partials/llm-qa-section.php';
    }
    ?>

    <!-- Related Cases -->
    <?php if (!empty($relatedCases)): ?>
        <section class="related-cases section">
            <div class="container">
                <h2 class="section-title"><?= __('related_cases') ?></h2>
                <div class="related-cases-grid">
                    <?php foreach ($relatedCases as $related): ?>
                        <a href="<?= _url('/casos-de-exito/' . htmlspecialchars($related['slug'])) ?>" class="related-case-card">
                            <div class="related-case-bg" <?php if (!empty($related['featured_image'])): ?>style="background-image: url('<?= htmlspecialchars($related['featured_image']) ?>');"<?php endif; ?>></div>
                            <div class="related-case-overlay"></div>
                            <div class="related-case-content">
                                <?php if (!empty($related['client_logo'])): ?>
                                    <div class="related-case-logo">
                                        <img src="<?= htmlspecialchars($related['client_logo']) ?>"
                                             alt="<?= htmlspecialchars($related['client_name'] ?? '') ?>">
                                    </div>
                                <?php endif; ?>
                                <h3><?= htmlspecialchars($related['title'] ?? '') ?></h3>
                                <?php if (empty($related['client_logo']) && !empty($related['client_name'])): ?>
                                    <span class="related-case-client"><?= htmlspecialchars($related['client_name']) ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</article>

<style>
/* Case Hero Section */
.case-hero-section {
    position: relative;
    min-height: 450px;
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-3xl);
    background-color: #1a3a44;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: flex-end;
}

.case-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        rgba(26, 58, 68, 0.3) 0%,
        rgba(26, 58, 68, 0.6) 50%,
        rgba(26, 58, 68, 0.85) 100%
    );
    z-index: 1;
}

.case-hero-section .container {
    position: relative;
    z-index: 2;
}

.case-hero-content {
    max-width: 800px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: var(--spacing-lg);
}

.case-hero-logo {
    height: 100px;
    max-width: 280px;
    filter: drop-shadow(0 4px 20px rgba(0, 0, 0, 0.4));
}

.case-hero-logo img {
    height: 100%;
    width: auto;
    max-width: 100%;
    object-fit: contain;
    display: block;
}

/* Case Body */
.case-body {
    padding: var(--spacing-2xl) 0;
}

/* Company Title */
.case-company-title {
    font-size: var(--font-size-3xl);
    font-weight: 700;
    color: var(--color-dark);
    margin-bottom: var(--spacing-sm);
    padding-bottom: var(--spacing-md);
    border-bottom: 3px solid var(--color-primary);
}

.case-client-name {
    font-size: var(--font-size-lg);
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-xl);
}

/* WYSIWYG Content */
.wysiwyg-content {
    line-height: 1.8;
}

.wysiwyg-content p {
    margin-bottom: var(--spacing-md);
}

.wysiwyg-content ul,
.wysiwyg-content ol {
    margin-bottom: var(--spacing-md);
    padding-left: var(--spacing-lg);
}

.wysiwyg-content li {
    margin-bottom: var(--spacing-xs);
}

.wysiwyg-content h3,
.wysiwyg-content h4 {
    margin-top: var(--spacing-lg);
    margin-bottom: var(--spacing-sm);
}

.wysiwyg-content strong {
    font-weight: 600;
}

.wysiwyg-content a {
    color: var(--color-primary);
    text-decoration: underline;
}

/* Key Metrics */
.case-metrics-section {
    padding: var(--spacing-2xl) 0;
    background-color: var(--color-white);
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-lg);
}

.metric-card {
    text-align: center;
    padding: var(--spacing-lg);
    background-color: var(--color-gray-50);
    border-radius: var(--radius-lg);
}

.metric-card .metric-value {
    display: block;
    font-size: var(--font-size-3xl);
    font-weight: 700;
    color: var(--color-primary);
    margin-bottom: var(--spacing-xs);
}

.metric-card .metric-label {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
}

/* Case Layout */
.case-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: var(--spacing-2xl);
}

/* Case Sections */
.case-section {
    margin-bottom: var(--spacing-2xl);
}

.case-section h2 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-lg);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.case-section h2 i {
    color: var(--color-primary);
}

.section-content {
    color: var(--color-gray-700);
    line-height: 1.8;
    font-size: var(--font-size-base);
}

/* Sidebar */
.case-sidebar {
    position: sticky;
    top: calc(var(--header-height) + var(--spacing-lg));
    height: fit-content;
}

.testimonial-card {
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    color: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    margin-bottom: var(--spacing-lg);
}

.testimonial-card blockquote p {
    font-style: italic;
    font-size: var(--font-size-base);
    line-height: 1.6;
    margin-bottom: var(--spacing-md);
}

.testimonial-author strong {
    display: block;
}

.testimonial-author span {
    font-size: var(--font-size-sm);
    opacity: 0.8;
}

.info-card {
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: var(--spacing-lg);
}

.info-card h4 {
    margin-bottom: var(--spacing-md);
    padding-bottom: var(--spacing-sm);
    border-bottom: 2px solid var(--color-primary);
}

.info-card dl {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: var(--spacing-sm) var(--spacing-md);
}

.info-card dt {
    font-weight: 600;
    color: var(--color-gray-500);
    font-size: var(--font-size-sm);
}

.info-card dd {
    color: var(--color-dark);
}

.info-card dd a {
    color: var(--color-primary);
}

/* Related Cases */
.related-cases {
    background-color: var(--color-gray-100);
}

.related-cases .section-title {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.related-cases-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-lg);
}

.related-case-card {
    position: relative;
    display: block;
    height: 280px;
    border-radius: var(--radius-xl);
    overflow: hidden;
    text-decoration: none;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
}

.related-case-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-xl);
}

.related-case-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--color-primary-dark);
    background-size: cover;
    background-position: center;
    transition: transform 0.4s ease;
}

.related-case-card:hover .related-case-bg {
    transform: scale(1.08);
}

.related-case-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 0.1) 0%,
        rgba(0, 0, 0, 0.4) 50%,
        rgba(0, 0, 0, 0.85) 100%
    );
    transition: background 0.3s ease;
}

.related-case-card:hover .related-case-overlay {
    background: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 0.05) 0%,
        rgba(0, 0, 0, 0.3) 50%,
        rgba(0, 0, 0, 0.8) 100%
    );
}

.related-case-content {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    padding: var(--spacing-xl);
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.related-case-logo {
    width: 160px;
    height: 80px;
    background: rgba(255, 255, 255, 0.95);
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-md);
    display: flex;
    align-items: center;
    justify-content: center;
}

.related-case-logo img {
    max-height: 100%;
    max-width: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
}

.related-case-content h3 {
    color: var(--color-white);
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: 0;
    line-height: 1.3;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.related-case-client {
    color: rgba(255, 255, 255, 0.9);
    font-size: var(--font-size-sm);
    font-weight: 500;
}

.case-link {
    color: var(--color-primary);
    font-weight: 600;
    font-size: var(--font-size-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.case-link:hover {
    color: var(--color-primary-dark);
}

@media (max-width: 1024px) {
    .case-layout {
        grid-template-columns: 1fr;
    }

    .case-sidebar {
        position: static;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-lg);
    }

    .testimonial-card {
        grid-column: span 2;
    }

    .metrics-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .related-cases-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .related-case-card {
        height: 240px;
    }
}

@media (max-width: 768px) {
    .case-hero-section {
        min-height: 300px;
        padding-top: calc(var(--spacing-2xl) + var(--header-height));
    }

    .case-hero-logo {
        height: 70px;
        max-width: 200px;
    }

    .case-company-title {
        font-size: var(--font-size-2xl);
    }

    .metrics-grid {
        grid-template-columns: 1fr 1fr;
    }

    .case-sidebar {
        grid-template-columns: 1fr;
    }

    .testimonial-card {
        grid-column: span 1;
    }

    .related-cases-grid {
        grid-template-columns: 1fr;
    }

    .related-case-card {
        height: 220px;
    }

    .related-case-content {
        padding: var(--spacing-lg);
    }

    .related-case-content h3 {
        font-size: var(--font-size-base);
    }
}

/* Gallery Section */
.case-gallery-section {
    margin-top: var(--spacing-xl);
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-md);
}

.gallery-carousel {
    display: flex;
    gap: var(--spacing-md);
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    padding-bottom: var(--spacing-sm);
}

.gallery-carousel .gallery-item {
    flex: 0 0 100%;
    scroll-snap-align: start;
}

.gallery-item {
    position: relative;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background-color: var(--color-gray-100);
}

.gallery-image-wrapper {
    position: relative;
    aspect-ratio: 16 / 10;
    overflow: hidden;
}

.gallery-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-item:hover .gallery-image-wrapper img {
    transform: scale(1.05);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-zoom {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--color-white);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: var(--color-primary);
    transition: all 0.2s ease;
}

.gallery-zoom:hover {
    transform: scale(1.1);
    background: var(--color-primary);
    color: var(--color-white);
}

.gallery-caption {
    padding: var(--spacing-sm) var(--spacing-md);
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    text-align: center;
    background: var(--color-white);
}

.gallery-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-md);
    margin-top: var(--spacing-lg);
}

.gallery-nav-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 2px solid var(--color-primary);
    background: transparent;
    color: var(--color-primary);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.gallery-nav-btn:hover {
    background: var(--color-primary);
    color: var(--color-white);
}

.gallery-dots {
    display: flex;
    gap: var(--spacing-xs);
}

.gallery-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid var(--color-gray-300);
    background: transparent;
    cursor: pointer;
    padding: 0;
    transition: all 0.2s ease;
}

.gallery-dot.active,
.gallery-dot:hover {
    background: var(--color-primary);
    border-color: var(--color-primary);
}

/* Video Section */
.case-video-section {
    margin-top: var(--spacing-xl);
}

.video-wrapper {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 */
    height: 0;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: var(--color-gray-900);
}

.video-wrapper iframe,
.video-wrapper video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Lightbox */
.lightbox {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.95);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-xl);
}

.lightbox.active {
    display: flex;
}

.lightbox-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
}

.lightbox-content img {
    max-width: 100%;
    max-height: 85vh;
    object-fit: contain;
    border-radius: var(--radius-md);
}

.lightbox-caption {
    text-align: center;
    color: var(--color-white);
    margin-top: var(--spacing-md);
    font-size: var(--font-size-base);
}

.lightbox-close {
    position: absolute;
    top: var(--spacing-lg);
    right: var(--spacing-lg);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: none;
    background: var(--color-white);
    color: var(--color-dark);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    transition: all 0.2s ease;
    z-index: 10;
}

.lightbox-close:hover {
    background: var(--color-gray-100);
    transform: scale(1.1);
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.9);
    color: var(--color-dark);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.2s ease;
}

.lightbox-nav:hover {
    background: var(--color-white);
    transform: translateY(-50%) scale(1.1);
}

.lightbox-prev {
    left: var(--spacing-lg);
}

.lightbox-next {
    right: var(--spacing-lg);
}

.lightbox-counter {
    position: absolute;
    bottom: var(--spacing-lg);
    left: 50%;
    transform: translateX(-50%);
    color: var(--color-white);
    font-size: var(--font-size-sm);
    background: rgba(0, 0, 0, 0.5);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-full);
}

@media (max-width: 1024px) {
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: 1fr;
    }

    .lightbox-nav {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

    .lightbox-prev {
        left: var(--spacing-sm);
    }

    .lightbox-next {
        right: var(--spacing-sm);
    }
}
</style>

<!-- Lightbox Modal -->
<?php if (!empty($gallery)): ?>
<div id="caseLightbox" class="lightbox" onclick="closeLightbox(event)">
    <button type="button" class="lightbox-close" onclick="closeLightbox(event)">
        <i class="fas fa-times"></i>
    </button>
    <button type="button" class="lightbox-nav lightbox-prev" onclick="navigateLightbox(-1, event)">
        <i class="fas fa-chevron-left"></i>
    </button>
    <div class="lightbox-content" onclick="event.stopPropagation()">
        <img id="lightboxImage" src="" alt="">
        <p id="lightboxCaption" class="lightbox-caption"></p>
    </div>
    <button type="button" class="lightbox-nav lightbox-next" onclick="navigateLightbox(1, event)">
        <i class="fas fa-chevron-right"></i>
    </button>
    <div class="lightbox-counter">
        <span id="lightboxCounter">1</span> / <?= count($gallery) ?>
    </div>
</div>

<script>
(function() {
    const galleryImages = <?= json_encode(array_values(array_map(function($img) {
        return [
            'url' => is_array($img) ? ($img['url'] ?? $img['image'] ?? '') : $img,
            'caption' => is_array($img) ? ($img['caption'] ?? '') : ''
        ];
    }, array_filter($gallery, function($img) {
        $url = is_array($img) ? ($img['url'] ?? $img['image'] ?? '') : $img;
        return !empty($url);
    })))) ?>;

    let currentIndex = 0;
    let carouselIndex = 0;

    window.openLightbox = function(index) {
        currentIndex = index;
        updateLightbox();
        document.getElementById('caseLightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function(e) {
        if (e.target.classList.contains('lightbox') || e.target.closest('.lightbox-close')) {
            document.getElementById('caseLightbox').classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    window.navigateLightbox = function(direction, e) {
        e.stopPropagation();
        currentIndex = (currentIndex + direction + galleryImages.length) % galleryImages.length;
        updateLightbox();
    };

    function updateLightbox() {
        const img = galleryImages[currentIndex];
        document.getElementById('lightboxImage').src = img.url;
        document.getElementById('lightboxCaption').textContent = img.caption || '';
        document.getElementById('lightboxCounter').textContent = currentIndex + 1;
    }

    // Carousel functions
    window.moveCarousel = function(direction) {
        const carousel = document.querySelector('.gallery-carousel');
        if (!carousel) return;

        const items = carousel.querySelectorAll('.gallery-item');
        carouselIndex = (carouselIndex + direction + items.length) % items.length;

        items[carouselIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
        updateDots();
    };

    window.goToSlide = function(index) {
        const carousel = document.querySelector('.gallery-carousel');
        if (!carousel) return;

        carouselIndex = index;
        const items = carousel.querySelectorAll('.gallery-item');
        items[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
        updateDots();
    };

    function updateDots() {
        const dots = document.querySelectorAll('.gallery-dot');
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === carouselIndex);
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('caseLightbox');
        if (!lightbox.classList.contains('active')) return;

        if (e.key === 'Escape') closeLightbox({ target: lightbox });
        if (e.key === 'ArrowLeft') navigateLightbox(-1, e);
        if (e.key === 'ArrowRight') navigateLightbox(1, e);
    });
})();
</script>
<?php endif; ?>
