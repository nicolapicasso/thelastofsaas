<?php
/**
 * Partners Block Template
 * Displays partner logos in grid or carousel with grayscale hover effect
 * Omniwallet CMS
 */

use App\Models\Partner;

$partnerModel = new Partner();

// Get settings
$displayMode = $settings['display_mode'] ?? 'grid';
$columns = (int)($settings['columns'] ?? 6);
$visibleItems = (int)($settings['visible_items'] ?? 5);
$selectionMode = $settings['selection_mode'] ?? 'all';
$typeFilter = $settings['type_filter'] ?? '';
$certifiedOnly = !empty($settings['certified_only']);
$selectedPartners = $settings['selected_partners'] ?? [];
$limit = (int)($settings['limit'] ?? 12);
$logoHeight = (int)($settings['logo_height'] ?? 60);
$showLink = $settings['show_link'] ?? true;
$autoplay = !empty($settings['autoplay']);
$autoplaySpeed = $settings['autoplay_speed'] ?? 'normal';

// Handle selected_partners as JSON string
if (is_string($selectedPartners)) {
    $selectedPartners = json_decode($selectedPartners, true) ?? [];
}

// Get partners based on selection mode
$partners = [];
switch ($selectionMode) {
    case 'type':
        if ($typeFilter) {
            $partners = $partnerModel->getByType($typeFilter);
            $partners = array_filter($partners, fn($p) => !empty($p['logo']));
            $partners = array_slice($partners, 0, $limit);
        }
        break;

    case 'certified':
        $partners = $partnerModel->getCertified($limit);
        $partners = array_filter($partners, fn($p) => !empty($p['logo']));
        break;

    case 'manual':
        if (!empty($selectedPartners)) {
            // Get partners by IDs maintaining order
            foreach ($selectedPartners as $partnerId) {
                $partner = $partnerModel->find((int)$partnerId);
                if ($partner && !empty($partner['logo']) && $partner['is_active']) {
                    $partners[] = $partner;
                }
            }
        }
        break;

    default: // 'all'
        $partners = $partnerModel->getWithLogos($limit);
        break;
}

// Apply certified filter if set
if ($certifiedOnly && $selectionMode !== 'certified') {
    $partners = array_filter($partners, fn($p) => !empty($p['is_certified']));
}

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

$blockId = 'partners-' . uniqid();
?>

<section class="block block-partners section <?= $renderer->getBlockClasses($block, $settings) ?>"
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

        <?php if (!empty($partners)): ?>
            <?php if ($displayMode === 'carousel'): ?>
                <!-- Carousel Mode -->
                <?php if ($autoplay): ?>
                    <!-- Autoplay Infinite Carousel -->
                    <div class="partners-carousel-autoplay" data-speed="<?= htmlspecialchars($autoplaySpeed) ?>">
                        <div class="partners-carousel-track-autoplay">
                            <?php
                            // Render logos twice for seamless infinite loop
                            for ($repeat = 0; $repeat < 2; $repeat++):
                                foreach ($partners as $partner):
                            ?>
                                <?php if ($showLink): ?>
                                    <a href="<?= _url('/partners/' . htmlspecialchars($partner['slug'])) ?>"
                                       class="partner-logo"
                                       title="<?= htmlspecialchars($partner['name']) ?>">
                                        <img src="<?= htmlspecialchars($partner['logo']) ?>"
                                             alt="<?= htmlspecialchars($partner['name']) ?>"
                                             loading="lazy">
                                    </a>
                                <?php else: ?>
                                    <div class="partner-logo" title="<?= htmlspecialchars($partner['name']) ?>">
                                        <img src="<?= htmlspecialchars($partner['logo']) ?>"
                                             alt="<?= htmlspecialchars($partner['name']) ?>"
                                             loading="lazy">
                                    </div>
                                <?php endif; ?>
                            <?php
                                endforeach;
                            endfor;
                            ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Manual Carousel -->
                    <div class="partners-carousel-wrapper">
                        <button class="carousel-nav carousel-prev" aria-label="Anterior">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <div class="partners-carousel">
                            <div class="partners-carousel-track">
                                <?php foreach ($partners as $partner): ?>
                                    <?php if ($showLink): ?>
                                        <a href="<?= _url('/partners/' . htmlspecialchars($partner['slug'])) ?>"
                                           class="partner-logo"
                                           title="<?= htmlspecialchars($partner['name']) ?>">
                                            <img src="<?= htmlspecialchars($partner['logo']) ?>"
                                                 alt="<?= htmlspecialchars($partner['name']) ?>"
                                                 loading="lazy">
                                        </a>
                                    <?php else: ?>
                                        <div class="partner-logo" title="<?= htmlspecialchars($partner['name']) ?>">
                                            <img src="<?= htmlspecialchars($partner['logo']) ?>"
                                                 alt="<?= htmlspecialchars($partner['name']) ?>"
                                                 loading="lazy">
                                        </div>
                                    <?php endif; ?>
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
                <div class="partners-grid">
                    <?php foreach ($partners as $partner): ?>
                        <?php if ($showLink): ?>
                            <a href="<?= _url('/partners/' . htmlspecialchars($partner['slug'])) ?>"
                               class="partner-logo"
                               title="<?= htmlspecialchars($partner['name']) ?>">
                                <img src="<?= htmlspecialchars($partner['logo']) ?>"
                                     alt="<?= htmlspecialchars($partner['name']) ?>"
                                     loading="lazy">
                            </a>
                        <?php else: ?>
                            <div class="partner-logo" title="<?= htmlspecialchars($partner['name']) ?>">
                                <img src="<?= htmlspecialchars($partner['logo']) ?>"
                                     alt="<?= htmlspecialchars($partner['name']) ?>"
                                     loading="lazy">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($content['cta_text']) && !empty($content['cta_url'])): ?>
                <div class="partners-cta">
                    <a href="<?= _url($content['cta_url']) ?>" class="btn btn-outline">
                        <?= htmlspecialchars($content['cta_text']) ?>
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>No hay partners disponibles</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Block Partners - Section Header */
.block-partners .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
}

.block-partners .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
    color: var(--block-title-color, inherit);
}

.block-partners .section-header p {
    color: var(--block-subtitle-color, var(--color-gray-600));
    font-size: var(--font-size-lg);
}

/* Partner Logo Base Styles */
.partner-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-md);
    background-color: transparent;
    border-radius: var(--radius-lg);
    transition: all 0.3s ease;
}

.partner-logo img {
    height: var(--logo-height, 60px);
    width: auto;
    max-width: 100%;
    object-fit: contain;
    filter: grayscale(100%);
    opacity: 0.6;
    transition: all 0.3s ease;
}

.partner-logo:hover img {
    filter: grayscale(0%);
    opacity: 1;
    transform: scale(1.05);
}

a.partner-logo:hover {
    background-color: var(--color-gray-50);
}

/* Grid Layout */
.partners-grid {
    display: grid;
    grid-template-columns: repeat(var(--grid-columns, 6), 1fr);
    gap: var(--spacing-lg);
    align-items: center;
}

/* Carousel Layout */
.partners-carousel-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.partners-carousel {
    flex: 1;
    overflow: hidden;
}

.partners-carousel-track {
    display: flex;
    gap: var(--spacing-lg);
    transition: transform 0.5s ease;
}

/* Autoplay Carousel */
.partners-carousel-autoplay {
    overflow: hidden;
    position: relative;
    mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
}

.partners-carousel-track-autoplay {
    display: flex;
    gap: var(--spacing-xl);
    width: max-content;
    animation: scroll-partner-logos 25s linear infinite;
}

.partners-carousel-autoplay[data-speed="slow"] .partners-carousel-track-autoplay {
    animation-duration: 40s;
}

.partners-carousel-autoplay[data-speed="fast"] .partners-carousel-track-autoplay {
    animation-duration: 15s;
}

.partners-carousel-autoplay:hover .partners-carousel-track-autoplay {
    animation-play-state: paused;
}

.partners-carousel-track-autoplay .partner-logo {
    flex-shrink: 0;
    padding: var(--spacing-md) var(--spacing-lg);
}

@keyframes scroll-partner-logos {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

.partners-carousel .partner-logo {
    flex: 0 0 calc(100% / var(--carousel-items, 5) - var(--spacing-lg) * (var(--carousel-items, 5) - 1) / var(--carousel-items, 5));
    min-width: calc(100% / var(--carousel-items, 5) - var(--spacing-lg) * (var(--carousel-items, 5) - 1) / var(--carousel-items, 5));
}

.block-partners .carousel-nav {
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

.block-partners .carousel-nav:hover {
    background: var(--color-primary);
    color: var(--color-white);
    border-color: var(--color-primary);
}

.block-partners .carousel-nav:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.block-partners .carousel-nav:disabled:hover {
    background: var(--color-white);
    color: var(--color-gray-600);
    border-color: var(--color-gray-200);
}

/* CTA */
.partners-cta {
    text-align: center;
    margin-top: var(--spacing-xl);
}

/* Empty State */
.block-partners .empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-500);
}

/* Responsive */
@media (max-width: 1200px) {
    .partners-grid {
        grid-template-columns: repeat(min(var(--grid-columns, 6), 5), 1fr);
    }
}

@media (max-width: 1024px) {
    .partners-grid {
        grid-template-columns: repeat(min(var(--grid-columns, 6), 4), 1fr);
    }

    .partners-carousel .partner-logo {
        flex: 0 0 calc(100% / 4 - var(--spacing-md) * 3 / 4);
        min-width: calc(100% / 4 - var(--spacing-md) * 3 / 4);
    }
}

@media (max-width: 768px) {
    .partners-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: var(--spacing-md);
    }

    .partner-logo {
        padding: var(--spacing-sm);
    }

    .partner-logo img {
        height: calc(var(--logo-height, 60px) * 0.8);
    }

    .partners-carousel-wrapper {
        flex-direction: column;
        gap: var(--spacing-lg);
    }

    .partners-carousel .partner-logo {
        flex: 0 0 calc(100% / 3 - var(--spacing-md) * 2 / 3);
        min-width: calc(100% / 3 - var(--spacing-md) * 2 / 3);
    }

    .block-partners .carousel-nav {
        display: none;
    }

    /* Enable touch scrolling on mobile */
    .partners-carousel {
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .partners-carousel::-webkit-scrollbar {
        display: none;
    }

    .partners-carousel .partner-logo {
        scroll-snap-align: start;
    }

    /* Autoplay carousel on tablet */
    .partners-carousel-track-autoplay {
        gap: var(--spacing-lg);
    }

    .partners-carousel-track-autoplay .partner-logo {
        padding: var(--spacing-sm) var(--spacing-md);
    }
}

@media (max-width: 480px) {
    .block-partners .container {
        padding-left: 0;
        padding-right: 0;
    }

    .block-partners .section-header {
        padding-left: var(--spacing-md);
        padding-right: var(--spacing-md);
    }

    /* Convert grid to horizontal carousel with 2 visible logos */
    .partners-grid {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        gap: var(--spacing-sm);
        padding: 0 var(--spacing-md);
        margin: 0;
    }

    .partners-grid::-webkit-scrollbar {
        display: none;
    }

    .partners-grid .partner-logo {
        flex: 0 0 calc(50% - var(--spacing-sm) / 2);
        min-width: calc(50% - var(--spacing-sm) / 2);
        scroll-snap-align: start;
    }

    .partners-carousel .partner-logo {
        flex: 0 0 calc(50% - var(--spacing-sm) / 2);
        min-width: calc(50% - var(--spacing-sm) / 2);
    }

    .partner-logo img {
        height: calc(var(--logo-height, 60px) * 0.7);
    }

    .partners-cta {
        padding: 0 var(--spacing-md);
    }
}
</style>

<script>
(function() {
    const block = document.getElementById('<?= $blockId ?>');
    if (!block) return;

    const carousel = block.querySelector('.partners-carousel');
    const track = block.querySelector('.partners-carousel-track');
    const prevBtn = block.querySelector('.carousel-prev');
    const nextBtn = block.querySelector('.carousel-next');

    if (!carousel || !track || !prevBtn || !nextBtn) return;

    const items = track.querySelectorAll('.partner-logo');
    if (items.length === 0) return;

    let currentIndex = 0;
    const visibleItems = <?= $visibleItems ?>;
    const totalItems = items.length;
    const maxIndex = Math.max(0, totalItems - visibleItems);

    function getItemWidth() {
        const item = items[0];
        const style = window.getComputedStyle(item);
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

    // Initial state
    updateCarousel();

    // Handle resize
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
