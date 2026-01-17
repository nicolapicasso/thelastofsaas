<?php
/**
 * Tools Block Template
 * Displays tool logos in grid or carousel with grayscale hover effect
 * We're Sinapsis CMS
 */

use App\Models\Tool;

$toolModel = new Tool();

// Get settings
$displayMode = $settings['display_mode'] ?? 'grid';
$columns = (int)($settings['columns'] ?? 4);
$visibleItems = (int)($settings['visible_items'] ?? 5);
$selectionMode = $settings['selection_mode'] ?? 'all';
$categoryFilter = $settings['category_filter'] ?? '';
$selectedTools = $settings['selected_tools'] ?? [];
$limit = (int)($settings['limit'] ?? 12);
$logoHeight = (int)($settings['logo_height'] ?? 60);
$grayscale = $settings['grayscale'] ?? true;
$showName = $settings['show_name'] ?? false;
$autoplay = !empty($settings['autoplay']);
$autoplaySpeed = $settings['autoplay_speed'] ?? 'normal';

// Handle selected_tools as JSON string
if (is_string($selectedTools)) {
    $selectedTools = json_decode($selectedTools, true) ?? [];
}

// Get tools based on selection mode
$tools = [];
switch ($selectionMode) {
    case 'category':
        if ($categoryFilter) {
            $tools = $toolModel->getByCategory((int)$categoryFilter, $limit);
        } else {
            $tools = $toolModel->getPublished($limit);
        }
        break;

    case 'manual':
        if (!empty($selectedTools)) {
            foreach ($selectedTools as $toolId) {
                $tool = $toolModel->find((int)$toolId);
                if ($tool && !empty($tool['is_active']) && !empty($tool['logo'])) {
                    $tools[] = $tool;
                }
            }
        }
        break;

    default: // 'all'
        $tools = $toolModel->getPublished($limit);
        break;
}

// Filter only tools with logos
$tools = array_filter($tools, fn($tool) => !empty($tool['logo']));

// Build custom color styles
$colorStyles = [];
if (!empty($settings['title_color'])) {
    $colorStyles[] = "--block-title-color: {$settings['title_color']}";
}
if (!empty($settings['subtitle_color'])) {
    $colorStyles[] = "--block-subtitle-color: {$settings['subtitle_color']}";
}
$customStyles = !empty($colorStyles) ? implode('; ', $colorStyles) . '; ' : '';

// Add logo height as CSS variable
$customStyles .= "--logo-height: {$logoHeight}px; ";
$customStyles .= "--grid-columns: {$columns}; ";
$customStyles .= "--carousel-items: {$visibleItems}; ";

$blockId = 'tools-' . uniqid();
$grayscaleClass = $grayscale ? 'grayscale' : '';
?>

<section class="block block-tools section <?= $renderer->getBlockClasses($block, $settings) ?>"
         id="<?= $blockId ?>"
         style="<?= $customStyles ?><?= $renderer->getBlockStyles($settings) ?>">
    <div class="container">
        <?php if (!empty($content['title'])): ?>
            <div class="section-header">
                <h2><?= htmlspecialchars($content['title']) ?></h2>
                <?php if (!empty($content['subtitle'])): ?>
                    <p><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($tools)): ?>
            <?php if ($displayMode === 'carousel'): ?>
                <!-- Carousel Mode -->
                <?php if ($autoplay): ?>
                    <!-- Autoplay Infinite Carousel -->
                    <div class="tools-carousel-autoplay <?= $grayscaleClass ?>" data-speed="<?= htmlspecialchars($autoplaySpeed) ?>">
                        <div class="tools-carousel-track-autoplay">
                            <?php
                            for ($repeat = 0; $repeat < 2; $repeat++):
                                foreach ($tools as $tool):
                            ?>
                                <a href="<?= _url('/herramientas/' . htmlspecialchars($tool['slug'] ?? '')) ?>"
                                   class="tool-logo <?= $showName ? 'with-name' : '' ?>"
                                   title="<?= htmlspecialchars($tool['name'] ?? $tool['title'] ?? '') ?>">
                                    <img src="<?= htmlspecialchars($tool['logo']) ?>"
                                         alt="<?= htmlspecialchars($tool['name'] ?? $tool['title'] ?? '') ?>"
                                         loading="lazy">
                                    <?php if ($showName): ?>
                                        <span class="tool-name"><?= htmlspecialchars($tool['name'] ?? $tool['title'] ?? '') ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php
                                endforeach;
                            endfor;
                            ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Manual Carousel -->
                    <div class="tools-carousel-wrapper <?= $grayscaleClass ?>">
                        <button class="carousel-nav carousel-prev" aria-label="Anterior">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <div class="tools-carousel">
                            <div class="tools-carousel-track">
                                <?php foreach ($tools as $tool): ?>
                                    <a href="<?= _url('/herramientas/' . htmlspecialchars($tool['slug'] ?? '')) ?>"
                                       class="tool-logo <?= $showName ? 'with-name' : '' ?>"
                                       title="<?= htmlspecialchars($tool['name'] ?? $tool['title'] ?? '') ?>">
                                        <img src="<?= htmlspecialchars($tool['logo']) ?>"
                                             alt="<?= htmlspecialchars($tool['name'] ?? $tool['title'] ?? '') ?>"
                                             loading="lazy">
                                        <?php if ($showName): ?>
                                            <span class="tool-name"><?= htmlspecialchars($tool['name'] ?? $tool['title'] ?? '') ?></span>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button class="carousel-nav carousel-next" aria-label="Siguiente">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Grid Mode -->
                <div class="tools-grid <?= $grayscaleClass ?>">
                    <?php foreach ($tools as $tool): ?>
                        <a href="<?= _url('/herramientas/' . htmlspecialchars($tool['slug'] ?? '')) ?>"
                           class="tool-logo <?= $showName ? 'with-name' : '' ?>"
                           title="<?= htmlspecialchars($tool['name'] ?? $tool['title'] ?? '') ?>">
                            <img src="<?= htmlspecialchars($tool['logo']) ?>"
                                 alt="<?= htmlspecialchars($tool['name'] ?? $tool['title'] ?? '') ?>"
                                 loading="lazy">
                            <?php if ($showName): ?>
                                <span class="tool-name"><?= htmlspecialchars($tool['name'] ?? $tool['title'] ?? '') ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($content['show_more']) && !empty($content['more_url'])): ?>
                <div class="section-footer">
                    <a href="<?= htmlspecialchars($content['more_url']) ?>" class="btn btn-outline">
                        <?= htmlspecialchars($content['more_text'] ?? __('view_all_tools')) ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <p><?= __('no_tools_available') ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Block Tools - Section Header */
.block-tools .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
}

.block-tools .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
    color: var(--block-title-color, inherit);
}

.block-tools .section-header p {
    color: var(--block-subtitle-color, var(--color-gray-600));
    font-size: var(--font-size-lg);
}

/* Tool Logo Base Styles */
.tool-logo {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-md);
    background-color: transparent;
    border-radius: var(--radius-lg);
    transition: all 0.3s ease;
    text-decoration: none;
}

.tool-logo img {
    height: var(--logo-height, 60px);
    width: auto;
    max-width: 100%;
    object-fit: contain;
    transition: all 0.3s ease;
}

/* Grayscale effect */
.grayscale .tool-logo img {
    filter: grayscale(100%);
    opacity: 0.6;
}

.grayscale .tool-logo:hover img {
    filter: grayscale(0%);
    opacity: 1;
    transform: scale(1.05);
}

.tool-logo:hover {
    background-color: var(--color-gray-50);
}

/* Tool name */
.tool-name {
    margin-top: var(--spacing-sm);
    font-size: var(--font-size-xs);
    color: var(--color-gray-600);
    text-align: center;
    font-weight: 500;
    transition: color 0.3s ease;
}

.tool-logo:hover .tool-name {
    color: var(--color-primary);
}

/* Grid Layout */
.tools-grid {
    display: grid;
    grid-template-columns: repeat(var(--grid-columns, 4), 1fr);
    gap: var(--spacing-lg);
    align-items: center;
}

/* Carousel Layout */
.tools-carousel-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.tools-carousel {
    flex: 1;
    overflow: hidden;
}

.tools-carousel-track {
    display: flex;
    gap: var(--spacing-lg);
    transition: transform 0.5s ease;
}

/* Autoplay Carousel */
.tools-carousel-autoplay {
    overflow: hidden;
    position: relative;
    mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
}

.tools-carousel-track-autoplay {
    display: flex;
    gap: var(--spacing-xl);
    width: max-content;
    animation: scroll-tool-logos 25s linear infinite;
}

.tools-carousel-autoplay[data-speed="slow"] .tools-carousel-track-autoplay {
    animation-duration: 40s;
}

.tools-carousel-autoplay[data-speed="fast"] .tools-carousel-track-autoplay {
    animation-duration: 15s;
}

.tools-carousel-autoplay:hover .tools-carousel-track-autoplay {
    animation-play-state: paused;
}

.tools-carousel-track-autoplay .tool-logo {
    flex-shrink: 0;
    padding: var(--spacing-md) var(--spacing-lg);
}

@keyframes scroll-tool-logos {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

.tools-carousel .tool-logo {
    flex: 0 0 calc(100% / var(--carousel-items, 5) - var(--spacing-lg) * (var(--carousel-items, 5) - 1) / var(--carousel-items, 5));
    min-width: calc(100% / var(--carousel-items, 5) - var(--spacing-lg) * (var(--carousel-items, 5) - 1) / var(--carousel-items, 5));
}

.block-tools .carousel-nav {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid var(--color-gray-200);
    background: var(--color-white);
    color: var(--color-gray-600);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-sm);
}

.block-tools .carousel-nav:hover {
    background: var(--color-primary);
    color: var(--color-white);
    border-color: var(--color-primary);
}

.block-tools .carousel-nav:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.block-tools .carousel-nav:disabled:hover {
    background: var(--color-white);
    color: var(--color-gray-600);
    border-color: var(--color-gray-200);
}

/* Section footer */
.block-tools .section-footer {
    text-align: center;
    margin-top: var(--spacing-2xl);
}

.block-tools .section-footer .btn i {
    margin-left: var(--spacing-sm);
}

/* Empty State */
.block-tools .empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-500);
}

/* Responsive */
@media (max-width: 1200px) {
    .tools-grid {
        grid-template-columns: repeat(min(var(--grid-columns, 4), 5), 1fr);
    }
}

@media (max-width: 1024px) {
    .tools-grid {
        grid-template-columns: repeat(min(var(--grid-columns, 4), 4), 1fr);
    }

    .tools-carousel .tool-logo {
        flex: 0 0 calc(100% / 4 - var(--spacing-md) * 3 / 4);
        min-width: calc(100% / 4 - var(--spacing-md) * 3 / 4);
    }
}

@media (max-width: 768px) {
    .tools-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: var(--spacing-md);
    }

    .tool-logo {
        padding: var(--spacing-sm);
    }

    .tool-logo img {
        height: calc(var(--logo-height, 60px) * 0.8);
    }

    .tools-carousel-wrapper {
        flex-direction: column;
        gap: var(--spacing-lg);
    }

    .tools-carousel .tool-logo {
        flex: 0 0 calc(100% / 3 - var(--spacing-md) * 2 / 3);
        min-width: calc(100% / 3 - var(--spacing-md) * 2 / 3);
    }

    .block-tools .carousel-nav {
        display: none;
    }

    .tools-carousel {
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .tools-carousel::-webkit-scrollbar {
        display: none;
    }

    .tools-carousel .tool-logo {
        scroll-snap-align: start;
    }

    .tools-carousel-track-autoplay {
        gap: var(--spacing-lg);
    }

    .tools-carousel-track-autoplay .tool-logo {
        padding: var(--spacing-sm) var(--spacing-md);
    }
}

@media (max-width: 480px) {
    .block-tools .container {
        padding-left: 0;
        padding-right: 0;
    }

    .block-tools .section-header {
        padding-left: var(--spacing-md);
        padding-right: var(--spacing-md);
    }

    .tools-grid {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        gap: var(--spacing-sm);
        padding: 0 var(--spacing-md);
        margin: 0;
    }

    .tools-grid::-webkit-scrollbar {
        display: none;
    }

    .tools-grid .tool-logo {
        flex: 0 0 calc(50% - var(--spacing-sm) / 2);
        min-width: calc(50% - var(--spacing-sm) / 2);
        scroll-snap-align: start;
    }

    .tools-carousel .tool-logo {
        flex: 0 0 calc(50% - var(--spacing-sm) / 2);
        min-width: calc(50% - var(--spacing-sm) / 2);
    }

    .tool-logo img {
        height: calc(var(--logo-height, 60px) * 0.7);
    }

    .tool-name {
        font-size: 10px;
    }
}
</style>

<script>
(function() {
    const block = document.getElementById('<?= $blockId ?>');
    if (!block) return;

    const carousel = block.querySelector('.tools-carousel');
    const track = block.querySelector('.tools-carousel-track');
    const prevBtn = block.querySelector('.carousel-prev');
    const nextBtn = block.querySelector('.carousel-next');

    if (!carousel || !track || !prevBtn || !nextBtn) return;

    const items = track.querySelectorAll('.tool-logo');
    if (items.length === 0) return;

    let currentIndex = 0;
    const visibleItems = <?= $visibleItems ?>;
    const totalItems = items.length;
    const maxIndex = Math.max(0, totalItems - visibleItems);

    function getItemWidth() {
        const item = items[0];
        const gap = parseInt(window.getComputedStyle(track).gap) || 0;
        return item.offsetWidth + gap;
    }

    function updateCarousel() {
        const itemWidth = getItemWidth();
        const offset = currentIndex * itemWidth;
        track.style.transform = `translateX(-${offset}px)`;

        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex >= maxIndex;
    }

    prevBtn.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });

    nextBtn.addEventListener('click', function() {
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateCarousel();
        }
    });

    updateCarousel();

    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            currentIndex = Math.min(currentIndex, maxIndex);
            updateCarousel();
        }, 100);
    });
})();
</script>
