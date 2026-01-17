<?php
/**
 * Services Block Template
 * We're Sinapsis CMS
 */

use App\Models\Service;

$serviceModel = new Service();

// Get settings
$displayMode = $settings['display_mode'] ?? 'cards';
$layoutMode = $settings['layout_mode'] ?? 'grid';
$columns = (int)($settings['columns'] ?? 3);
$visibleItems = (int)($settings['visible_items'] ?? 3);
$selectionMode = $settings['selection_mode'] ?? 'all';
$categoryFilter = $settings['category_filter'] ?? '';
$selectedServices = $settings['selected_services'] ?? [];
$limit = (int)($settings['limit'] ?? 6);
$cardStyle = $settings['card_style'] ?? 'shadow';
$hoverEffect = $settings['hover_effect'] ?? 'lift';
$showIcon = $settings['show_icon'] ?? true;
$showDescription = $settings['show_description'] ?? true;
$showCategory = $settings['show_category'] ?? true;
$showLink = $settings['show_link'] ?? true;
$autoplay = !empty($settings['autoplay']);
$autoplaySpeed = $settings['autoplay_speed'] ?? 'normal';

// Get services based on selection mode
if ($selectionMode === 'manual' && !empty($selectedServices)) {
    if (is_string($selectedServices)) {
        $selectedServices = json_decode($selectedServices, true) ?? [];
    }
    $services = [];
    foreach ($selectedServices as $serviceId) {
        $service = $serviceModel->find((int)$serviceId);
        if ($service && $service['is_active']) {
            // Get category info if needed
            if ($showCategory && !empty($service['category_id'])) {
                $categoryModel = new \App\Models\Category();
                $category = $categoryModel->find($service['category_id']);
                $service['category_name'] = $category['name'] ?? '';
            }
            $services[] = $service;
        }
    }
} elseif ($selectionMode === 'category' && !empty($categoryFilter)) {
    $services = $serviceModel->getByCategory((int)$categoryFilter, $limit);
} else {
    $services = $serviceModel->getActive($limit);
}

if (empty($services)) {
    return;
}

// Build custom color styles
$colorStyles = [];
if (!empty($settings['title_color'])) {
    $colorStyles[] = "--block-title-color: {$settings['title_color']}";
}
if (!empty($settings['subtitle_color'])) {
    $colorStyles[] = "--block-subtitle-color: {$settings['subtitle_color']}";
}
if (!empty($settings['text_color'])) {
    $colorStyles[] = "--block-text-color: {$settings['text_color']}";
}
$customStyles = !empty($colorStyles) ? implode('; ', $colorStyles) . '; ' : '';

// Build CSS classes
$blockClasses = [
    'block',
    'block-services',
    'section',
    'display-' . $displayMode,
    'layout-' . $layoutMode,
    'card-style-' . $cardStyle,
    'hover-' . $hoverEffect,
    $renderer->getBlockClasses($block, $settings)
];

// Unique ID for carousel
$blockId = 'services-block-' . ($block['id'] ?? uniqid());
?>

<section class="<?= implode(' ', array_filter($blockClasses)) ?>" id="<?= $blockId ?>" style="<?= $customStyles ?><?= $renderer->getBlockStyles($settings) ?>">
    <div class="container">
        <?php if (!empty($content['title']) || !empty($content['subtitle'])): ?>
            <div class="section-header">
                <?php if (!empty($content['title'])): ?>
                    <h2><?= htmlspecialchars($content['title']) ?></h2>
                <?php endif; ?>
                <?php if (!empty($content['subtitle'])): ?>
                    <p><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($layoutMode === 'carousel'): ?>
        <!-- Carousel Layout -->
        <div class="services-carousel-wrapper">
            <div class="services-carousel" data-visible="<?= $visibleItems ?>" data-autoplay="<?= $autoplay ? 'true' : 'false' ?>" data-speed="<?= $autoplaySpeed ?>">
                <div class="carousel-track">
                    <?php foreach ($services as $service): ?>
                        <?php
                        $serviceUrl = '/servicios/' . htmlspecialchars($service['slug']);
                        $iconClass = $service['icon_class'] ?? $service['icon'] ?? 'fas fa-cog';
                        ?>
                        <article class="service-block-card">
                            <?php if ($showLink): ?>
                            <a href="<?= _url($serviceUrl) ?>" class="service-card-link">
                            <?php endif; ?>

                                <?php if ($showIcon): ?>
                                <div class="service-block-icon">
                                    <i class="<?= htmlspecialchars($iconClass) ?>"></i>
                                </div>
                                <?php endif; ?>

                                <?php if ($showCategory && !empty($service['category_name'])): ?>
                                <span class="service-block-category"><?= htmlspecialchars($service['category_name']) ?></span>
                                <?php endif; ?>

                                <h3 class="service-block-title"><?= htmlspecialchars($service['title']) ?></h3>

                                <?php if ($showDescription && !empty($service['short_description'])): ?>
                                <p class="service-block-description"><?= htmlspecialchars($service['short_description']) ?></p>
                                <?php endif; ?>

                                <?php if ($showLink): ?>
                                <span class="service-block-link">Saber más <i class="fas fa-arrow-right"></i></span>
                                <?php endif; ?>

                            <?php if ($showLink): ?>
                            </a>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="carousel-nav carousel-prev" aria-label="Anterior">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-nav carousel-next" aria-label="Siguiente">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <?php else: ?>
        <!-- Grid Layout -->
        <div class="services-grid columns-<?= $columns ?>">
            <?php foreach ($services as $service): ?>
                <?php
                $serviceUrl = '/servicios/' . htmlspecialchars($service['slug']);
                $iconClass = $service['icon_class'] ?? $service['icon'] ?? 'fas fa-cog';
                ?>
                <article class="service-block-card">
                    <?php if ($showLink): ?>
                    <a href="<?= _url($serviceUrl) ?>" class="service-card-link">
                    <?php endif; ?>

                        <?php if ($showIcon): ?>
                        <div class="service-block-icon">
                            <i class="<?= htmlspecialchars($iconClass) ?>"></i>
                        </div>
                        <?php endif; ?>

                        <?php if ($showCategory && !empty($service['category_name'])): ?>
                        <span class="service-block-category"><?= htmlspecialchars($service['category_name']) ?></span>
                        <?php endif; ?>

                        <h3 class="service-block-title"><?= htmlspecialchars($service['title']) ?></h3>

                        <?php if ($showDescription && !empty($service['short_description'])): ?>
                        <p class="service-block-description"><?= htmlspecialchars($service['short_description']) ?></p>
                        <?php endif; ?>

                        <?php if ($showLink): ?>
                        <span class="service-block-link">Saber más <i class="fas fa-arrow-right"></i></span>
                        <?php endif; ?>

                    <?php if ($showLink): ?>
                    </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($content['cta_text']) && !empty($content['cta_url'])): ?>
            <div class="section-footer">
                <a href="<?= htmlspecialchars($content['cta_url']) ?>" class="btn btn-primary btn-lg">
                    <?= htmlspecialchars($content['cta_text']) ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.block-services .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
}

.block-services .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
    color: var(--block-title-color, inherit);
}

.block-services .section-header p {
    color: var(--block-subtitle-color, var(--color-gray-600));
    font-size: var(--font-size-lg);
}

.block-services .section-footer {
    text-align: center;
    margin-top: var(--spacing-2xl);
}

/* Grid Layout */
.block-services .services-grid {
    display: grid;
    gap: var(--spacing-lg);
}

.block-services .services-grid.columns-2 { grid-template-columns: repeat(2, 1fr); }
.block-services .services-grid.columns-3 { grid-template-columns: repeat(3, 1fr); }
.block-services .services-grid.columns-4 { grid-template-columns: repeat(4, 1fr); }

/* Service Card */
.block-services .service-block-card {
    position: relative;
}

.block-services .service-card-link {
    display: flex;
    flex-direction: column;
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    text-decoration: none;
    color: inherit;
    height: 100%;
    transition: all var(--transition);
}

/* Card Styles */
.block-services.card-style-shadow .service-card-link {
    box-shadow: var(--shadow-md);
}

.block-services.card-style-bordered .service-card-link {
    border: 1px solid var(--color-gray-200);
    box-shadow: none;
}

.block-services.card-style-minimal .service-card-link {
    background: transparent;
    box-shadow: none;
    padding: var(--spacing-md);
}

.block-services.card-style-filled .service-card-link {
    background: var(--color-primary-light);
    box-shadow: none;
}

/* Hover Effects */
.block-services.hover-lift .service-card-link:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

.block-services.hover-glow .service-card-link:hover {
    box-shadow: 0 0 30px rgba(33, 90, 107, 0.25);
}

.block-services.hover-border .service-card-link:hover {
    border-color: var(--color-primary);
}

.block-services.hover-none .service-card-link:hover {
    transform: none;
}

/* Card Content */
.block-services .service-block-icon {
    width: 64px;
    height: 64px;
    border-radius: var(--radius-lg);
    background: linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-primary) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: var(--spacing-md);
    transition: all var(--transition);
}

.block-services .service-card-link:hover .service-block-icon {
    transform: scale(1.05);
}

.block-services .service-block-category {
    font-size: var(--font-size-xs);
    color: var(--color-primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: var(--spacing-xs);
}

.block-services .service-block-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--spacing-sm);
    color: var(--block-title-color, var(--color-gray-900));
    transition: color var(--transition);
}

.block-services .service-card-link:hover .service-block-title {
    color: var(--color-primary);
}

.block-services .service-block-description {
    color: var(--block-text-color, var(--color-gray-600));
    font-size: var(--font-size-sm);
    line-height: 1.6;
    flex: 1;
    margin-bottom: var(--spacing-md);
}

.block-services .service-block-link {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--color-primary);
    font-weight: 600;
    font-size: var(--font-size-sm);
    margin-top: auto;
}

.block-services .service-block-link i {
    transition: transform var(--transition);
}

.block-services .service-card-link:hover .service-block-link i {
    transform: translateX(4px);
}

/* Display Modes */

/* List Mode */
.block-services.display-list .services-grid {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.block-services.display-list .service-card-link {
    flex-direction: row;
    align-items: center;
    padding: var(--spacing-lg);
}

.block-services.display-list .service-block-icon {
    margin-bottom: 0;
    margin-right: var(--spacing-lg);
    flex-shrink: 0;
}

.block-services.display-list .service-block-content {
    flex: 1;
}

.block-services.display-list .service-block-title {
    margin-bottom: var(--spacing-xs);
}

.block-services.display-list .service-block-description {
    margin-bottom: 0;
    flex: none;
}

.block-services.display-list .service-block-link {
    margin-top: 0;
    margin-left: var(--spacing-lg);
    flex-shrink: 0;
}

.block-services.display-list .service-block-category {
    display: none;
}

/* Compact Mode */
.block-services.display-compact .service-card-link {
    text-align: center;
    padding: var(--spacing-lg);
    align-items: center;
}

.block-services.display-compact .service-block-icon {
    margin: 0 auto var(--spacing-sm);
    width: 56px;
    height: 56px;
    font-size: 20px;
}

.block-services.display-compact .service-block-title {
    font-size: var(--font-size-base);
    margin-bottom: 0;
}

.block-services.display-compact .service-block-description,
.block-services.display-compact .service-block-category,
.block-services.display-compact .service-block-link {
    display: none;
}

/* Icons Only Mode */
.block-services.display-icons_only .services-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: var(--spacing-xl);
}

.block-services.display-icons_only .service-card-link {
    background: transparent;
    box-shadow: none;
    border: none;
    padding: var(--spacing-md);
    text-align: center;
    align-items: center;
    width: auto;
}

.block-services.display-icons_only .service-block-icon {
    width: 80px;
    height: 80px;
    font-size: 32px;
    margin: 0 auto var(--spacing-sm);
}

.block-services.display-icons_only .service-block-title {
    font-size: var(--font-size-sm);
    margin-bottom: 0;
}

.block-services.display-icons_only .service-block-description,
.block-services.display-icons_only .service-block-category,
.block-services.display-icons_only .service-block-link {
    display: none;
}

/* Carousel */
.block-services .services-carousel-wrapper {
    position: relative;
    padding: 0 50px;
}

.block-services .services-carousel {
    overflow: hidden;
}

.block-services .carousel-track {
    display: flex;
    gap: var(--spacing-lg);
    transition: transform 0.5s ease;
}

.block-services .carousel-track .service-block-card {
    flex: 0 0 calc((100% - var(--spacing-lg) * 2) / 3);
}

.block-services .services-carousel[data-visible="2"] .carousel-track .service-block-card {
    flex: 0 0 calc((100% - var(--spacing-lg)) / 2);
}

.block-services .services-carousel[data-visible="4"] .carousel-track .service-block-card {
    flex: 0 0 calc((100% - var(--spacing-lg) * 3) / 4);
}

.block-services .carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: white;
    border: 1px solid var(--color-gray-200);
    color: var(--color-gray-700);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition);
    z-index: 10;
    box-shadow: var(--shadow-sm);
}

.block-services .carousel-nav:hover {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.block-services .carousel-prev { left: 0; }
.block-services .carousel-next { right: 0; }

/* Responsive */
@media (max-width: 992px) {
    .block-services .services-grid.columns-4 { grid-template-columns: repeat(2, 1fr); }
    .block-services .services-grid.columns-3 { grid-template-columns: repeat(2, 1fr); }

    .block-services.display-list .service-card-link {
        flex-wrap: wrap;
    }

    .block-services.display-list .service-block-link {
        width: 100%;
        margin-left: 0;
        margin-top: var(--spacing-sm);
    }
}

@media (max-width: 768px) {
    .block-services .services-grid.columns-2,
    .block-services .services-grid.columns-3,
    .block-services .services-grid.columns-4 {
        grid-template-columns: 1fr;
    }

    .block-services .service-card-link {
        padding: var(--spacing-lg);
    }

    .block-services .service-block-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }

    .block-services .services-carousel-wrapper {
        padding: 0;
    }

    .block-services .carousel-nav {
        display: none;
    }

    .block-services .carousel-track {
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        padding-bottom: var(--spacing-sm);
    }

    .block-services .carousel-track .service-block-card {
        flex: 0 0 85%;
        scroll-snap-align: start;
    }
}
</style>

<?php if ($layoutMode === 'carousel'): ?>
<script>
(function() {
    const wrapper = document.querySelector('#<?= $blockId ?> .services-carousel-wrapper');
    if (!wrapper) return;

    const carousel = wrapper.querySelector('.services-carousel');
    const track = wrapper.querySelector('.carousel-track');
    const prevBtn = wrapper.querySelector('.carousel-prev');
    const nextBtn = wrapper.querySelector('.carousel-next');
    const cards = track.querySelectorAll('.service-block-card');

    if (cards.length === 0) return;

    const visibleItems = parseInt(carousel.dataset.visible) || 3;
    const autoplay = carousel.dataset.autoplay === 'true';
    const speed = carousel.dataset.speed || 'normal';

    let currentIndex = 0;
    const maxIndex = Math.max(0, cards.length - visibleItems);

    function updateCarousel() {
        if (cards.length === 0) return;
        const cardWidth = cards[0].offsetWidth;
        const gap = parseInt(getComputedStyle(track).gap) || 24;
        const offset = currentIndex * (cardWidth + gap);
        track.style.transform = `translateX(-${offset}px)`;
    }

    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', function() {
            currentIndex = Math.max(0, currentIndex - 1);
            updateCarousel();
        });

        nextBtn.addEventListener('click', function() {
            currentIndex = Math.min(maxIndex, currentIndex + 1);
            updateCarousel();
        });
    }

    if (autoplay && maxIndex > 0) {
        const intervals = { slow: 5000, normal: 3500, fast: 2000 };
        const interval = intervals[speed] || 3500;

        setInterval(function() {
            currentIndex = currentIndex >= maxIndex ? 0 : currentIndex + 1;
            updateCarousel();
        }, interval);
    }

    window.addEventListener('resize', updateCarousel);
})();
</script>
<?php endif; ?>
