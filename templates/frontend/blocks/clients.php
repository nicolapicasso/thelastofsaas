<?php
/**
 * Clients Block Template
 * Displays client logos in grid or carousel with grayscale hover effect
 * We're Sinapsis CMS
 */

use App\Models\Client;

$clientModel = new Client();

// Get settings
$displayMode = $settings['display_mode'] ?? 'grid';
$columns = (int)($settings['columns'] ?? 6);
$visibleItems = (int)($settings['visible_items'] ?? 5);
$selectionMode = $settings['selection_mode'] ?? 'all';
$industryFilter = $settings['industry_filter'] ?? '';
$selectedClients = $settings['selected_clients'] ?? [];
$limit = (int)($settings['limit'] ?? 12);
$logoHeight = (int)($settings['logo_height'] ?? 60);
$showLink = $settings['show_link'] ?? true;
$includeDrafts = !empty($settings['include_drafts']);
$autoplay = !empty($settings['autoplay']);
$autoplaySpeed = $settings['autoplay_speed'] ?? 'normal';

// Handle selected_clients as JSON string
if (is_string($selectedClients)) {
    $selectedClients = json_decode($selectedClients, true) ?? [];
}

// Get clients based on selection mode
$clients = [];
switch ($selectionMode) {
    case 'industry':
        if ($industryFilter) {
            $clients = $clientModel->getByIndustry($industryFilter, $limit, $includeDrafts);
        }
        break;

    case 'manual':
        if (!empty($selectedClients)) {
            foreach ($selectedClients as $clientId) {
                $client = $clientModel->find((int)$clientId);
                if ($client && !empty($client['logo'])) {
                    if (!empty($client['is_active']) || $includeDrafts) {
                        $clients[] = $client;
                    }
                }
            }
        }
        break;

    default: // 'all'
        $clients = $clientModel->getWithLogos($limit, $includeDrafts);
        break;
}

// Filter only clients with logos
$clients = array_filter($clients, fn($client) => !empty($client['logo']));

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

$blockId = 'clients-' . uniqid();
?>

<section class="block block-clients section <?= $renderer->getBlockClasses($block, $settings) ?>"
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

        <?php if (!empty($clients)): ?>
            <?php if ($displayMode === 'carousel'): ?>
                <!-- Carousel Mode -->
                <?php if ($autoplay): ?>
                    <!-- Autoplay Infinite Carousel -->
                    <div class="clients-carousel-autoplay" data-speed="<?= htmlspecialchars($autoplaySpeed) ?>">
                        <div class="clients-carousel-track-autoplay">
                            <?php
                            for ($repeat = 0; $repeat < 2; $repeat++):
                                foreach ($clients as $client):
                                    $canLink = $showLink && !empty($client['is_active']);
                            ?>
                                <?php if ($canLink): ?>
                                    <a href="<?= _url('/clientes/' . htmlspecialchars($client['slug'])) ?>"
                                       class="client-logo"
                                       title="<?= htmlspecialchars($client['name']) ?>">
                                        <img src="<?= htmlspecialchars($client['logo']) ?>"
                                             alt="<?= htmlspecialchars($client['name']) ?>"
                                             loading="lazy">
                                    </a>
                                <?php else: ?>
                                    <div class="client-logo" title="<?= htmlspecialchars($client['name']) ?>">
                                        <img src="<?= htmlspecialchars($client['logo']) ?>"
                                             alt="<?= htmlspecialchars($client['name']) ?>"
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
                    <div class="clients-carousel-wrapper">
                        <button class="carousel-nav carousel-prev" aria-label="Anterior">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <div class="clients-carousel">
                            <div class="clients-carousel-track">
                                <?php foreach ($clients as $client): ?>
                                    <?php $canLink = $showLink && !empty($client['is_active']); ?>
                                    <?php if ($canLink): ?>
                                        <a href="<?= _url('/clientes/' . htmlspecialchars($client['slug'])) ?>"
                                           class="client-logo"
                                           title="<?= htmlspecialchars($client['name']) ?>">
                                            <img src="<?= htmlspecialchars($client['logo']) ?>"
                                                 alt="<?= htmlspecialchars($client['name']) ?>"
                                                 loading="lazy">
                                        </a>
                                    <?php else: ?>
                                        <div class="client-logo" title="<?= htmlspecialchars($client['name']) ?>">
                                            <img src="<?= htmlspecialchars($client['logo']) ?>"
                                                 alt="<?= htmlspecialchars($client['name']) ?>"
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
                <div class="clients-grid">
                    <?php foreach ($clients as $client): ?>
                        <?php $canLink = $showLink && !empty($client['is_active']); ?>
                        <?php if ($canLink): ?>
                            <a href="<?= _url('/clientes/' . htmlspecialchars($client['slug'])) ?>"
                               class="client-logo"
                               title="<?= htmlspecialchars($client['name']) ?>">
                                <img src="<?= htmlspecialchars($client['logo']) ?>"
                                     alt="<?= htmlspecialchars($client['name']) ?>"
                                     loading="lazy">
                            </a>
                        <?php else: ?>
                            <div class="client-logo" title="<?= htmlspecialchars($client['name']) ?>">
                                <img src="<?= htmlspecialchars($client['logo']) ?>"
                                     alt="<?= htmlspecialchars($client['name']) ?>"
                                     loading="lazy">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($content['show_more']) && !empty($content['more_url'])): ?>
                <div class="section-footer">
                    <a href="<?= htmlspecialchars($content['more_url']) ?>" class="btn btn-outline">
                        <?= htmlspecialchars($content['more_text'] ?? __('view_all_clients')) ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <p><?= __('no_clients_available') ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Block Clients - Section Header */
.block-clients .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
}

.block-clients .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
    color: var(--block-title-color, inherit);
}

.block-clients .section-header p {
    color: var(--block-subtitle-color, var(--color-gray-600));
    font-size: var(--font-size-lg);
}

/* Client Logo Base Styles */
.client-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-md);
    background-color: transparent;
    border-radius: var(--radius-lg);
    transition: all 0.3s ease;
}

.client-logo img {
    height: var(--logo-height, 60px);
    width: auto;
    max-width: 100%;
    object-fit: contain;
    filter: grayscale(100%);
    opacity: 0.6;
    transition: all 0.3s ease;
}

.client-logo:hover img {
    filter: grayscale(0%);
    opacity: 1;
    transform: scale(1.05);
}

a.client-logo:hover {
    background-color: var(--color-gray-50);
}

/* Grid Layout */
.clients-grid {
    display: grid;
    grid-template-columns: repeat(var(--grid-columns, 6), 1fr);
    gap: var(--spacing-lg);
    align-items: center;
}

/* Carousel Layout */
.clients-carousel-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.clients-carousel {
    flex: 1;
    overflow: hidden;
}

.clients-carousel-track {
    display: flex;
    flex-wrap: nowrap;
    gap: var(--spacing-lg, 24px);
    transition: transform 0.5s ease;
}

/* Autoplay Carousel */
.clients-carousel-autoplay {
    overflow: hidden;
    position: relative;
    mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
}

.clients-carousel-track-autoplay {
    display: flex;
    flex-wrap: nowrap;
    gap: var(--spacing-xl, 32px);
    width: max-content;
    animation: scroll-logos 25s linear infinite;
}

.clients-carousel-autoplay[data-speed="slow"] .clients-carousel-track-autoplay {
    animation-duration: 40s;
}

.clients-carousel-autoplay[data-speed="fast"] .clients-carousel-track-autoplay {
    animation-duration: 15s;
}

.clients-carousel-autoplay:hover .clients-carousel-track-autoplay {
    animation-play-state: paused;
}

.clients-carousel-track-autoplay .client-logo {
    flex-shrink: 0;
    padding: var(--spacing-md) var(--spacing-lg);
}

@keyframes scroll-logos {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

.clients-carousel .client-logo {
    flex: 0 0 auto;
    width: calc((100% - (var(--carousel-items, 5) - 1) * var(--spacing-lg, 24px)) / var(--carousel-items, 5));
    min-width: 120px;
}

.carousel-nav {
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

.carousel-nav:hover {
    background: var(--color-primary);
    color: var(--color-white);
    border-color: var(--color-primary);
}

.carousel-nav:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.carousel-nav:disabled:hover {
    background: var(--color-white);
    color: var(--color-gray-600);
    border-color: var(--color-gray-200);
}

/* Section footer */
.block-clients .section-footer {
    text-align: center;
    margin-top: var(--spacing-2xl);
}

.block-clients .section-footer .btn i {
    margin-left: var(--spacing-sm);
}

/* Empty State */
.block-clients .empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-500);
}

/* Responsive */
@media (max-width: 1200px) {
    .clients-grid {
        grid-template-columns: repeat(min(var(--grid-columns, 6), 5), 1fr);
    }
}

@media (max-width: 1024px) {
    .clients-grid {
        grid-template-columns: repeat(min(var(--grid-columns, 6), 4), 1fr);
    }

    .clients-carousel .client-logo {
        flex: 0 0 auto;
        width: calc((100% - 3 * var(--spacing-md, 16px)) / 4);
        min-width: 100px;
    }
}

@media (max-width: 768px) {
    .clients-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: var(--spacing-md);
    }

    .client-logo {
        padding: var(--spacing-sm);
    }

    .client-logo img {
        height: calc(var(--logo-height, 60px) * 0.8);
    }

    .clients-carousel-wrapper {
        flex-direction: column;
        gap: var(--spacing-lg);
    }

    .clients-carousel .client-logo {
        flex: 0 0 auto;
        width: calc((100% - 2 * var(--spacing-md, 16px)) / 3);
        min-width: 90px;
        scroll-snap-align: start;
    }

    .carousel-nav {
        display: none;
    }

    .clients-carousel {
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .clients-carousel::-webkit-scrollbar {
        display: none;
    }

    .clients-carousel-track-autoplay {
        gap: var(--spacing-lg);
    }

    .clients-carousel-track-autoplay .client-logo {
        padding: var(--spacing-sm) var(--spacing-md);
    }
}

@media (max-width: 480px) {
    .block-clients .container {
        padding-left: 0;
        padding-right: 0;
    }

    .block-clients .section-header {
        padding-left: var(--spacing-md);
        padding-right: var(--spacing-md);
    }

    .clients-grid {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        gap: var(--spacing-sm);
        padding: 0 var(--spacing-md);
        margin: 0;
    }

    .clients-grid::-webkit-scrollbar {
        display: none;
    }

    .clients-grid .client-logo {
        flex: 0 0 calc(50% - var(--spacing-sm) / 2);
        min-width: calc(50% - var(--spacing-sm) / 2);
        scroll-snap-align: start;
    }

    .clients-carousel .client-logo {
        flex: 0 0 auto;
        width: calc(50% - var(--spacing-sm, 8px) / 2);
        min-width: 80px;
    }

    .client-logo img {
        height: calc(var(--logo-height, 60px) * 0.7);
    }
}
</style>

<script>
(function() {
    const block = document.getElementById('<?= $blockId ?>');
    if (!block) return;

    const carousel = block.querySelector('.clients-carousel');
    const track = block.querySelector('.clients-carousel-track');
    const prevBtn = block.querySelector('.carousel-prev');
    const nextBtn = block.querySelector('.carousel-next');

    if (!carousel || !track || !prevBtn || !nextBtn) return;

    const items = track.querySelectorAll('.client-logo');
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
