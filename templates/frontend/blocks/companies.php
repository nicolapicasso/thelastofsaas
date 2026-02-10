<?php
/**
 * Companies Block Template
 * TLOS - The Last of SaaS
 */

use App\Models\Company;
use App\Models\Event;

$companyModel = new Company();
$eventModel = new Event();

// Get settings
$displayMode = $settings['display_mode'] ?? 'grid';
$columns = (int)($settings['columns'] ?? 4);
$visibleItems = (int)($settings['visible_items'] ?? 5);
$selectionMode = $settings['selection_mode'] ?? 'all';
$eventId = $settings['event_id'] ?? '';
$sectorFilter = $settings['sector_filter'] ?? '';
$selectedCompanies = $settings['selected_companies'] ?? [];
$limit = (int)($settings['limit'] ?? 12);
$logoHeight = (int)($settings['logo_height'] ?? 80);
$showName = $settings['show_name'] ?? true;
$showSector = $settings['show_sector'] ?? true;
$grayscale = !empty($settings['grayscale']);
$autoplay = !empty($settings['autoplay']);
$autoplaySpeed = $settings['autoplay_speed'] ?? 'normal';

// Handle selected_companies as JSON string
if (is_string($selectedCompanies)) {
    $selectedCompanies = json_decode($selectedCompanies, true) ?? [];
}

// Get companies based on selection mode
$companies = [];
switch ($selectionMode) {
    case 'event':
        if ($eventId) {
            $companies = $eventModel->getCompanies((int)$eventId);
            if ($limit > 0) {
                $companies = array_slice($companies, 0, $limit);
            }
        }
        break;

    case 'sector':
        if ($sectorFilter) {
            $allCompanies = $companyModel->getActive();
            $companies = array_filter($allCompanies, fn($c) => ($c['sector'] ?? '') === $sectorFilter);
            if ($limit > 0) {
                $companies = array_slice($companies, 0, $limit);
            }
        }
        break;

    case 'manual':
        if (!empty($selectedCompanies)) {
            foreach ($selectedCompanies as $companyId) {
                $company = $companyModel->find((int)$companyId);
                if ($company && $company['active']) {
                    $companies[] = $company;
                }
            }
        }
        break;

    default: // 'all'
        $companies = $companyModel->getActive();
        if ($limit > 0) {
            $companies = array_slice($companies, 0, $limit);
        }
        break;
}

// Filter only companies with logos
$companies = array_filter($companies, fn($c) => !empty($c['logo_url']));

if (empty($companies)) {
    return;
}

// Build custom styles
$colorStyles = [];
if (!empty($settings['title_color'])) {
    $colorStyles[] = "--block-title-color: {$settings['title_color']}";
}
if (!empty($settings['subtitle_color'])) {
    $colorStyles[] = "--block-subtitle-color: {$settings['subtitle_color']}";
}
$customStyles = !empty($colorStyles) ? implode('; ', $colorStyles) . '; ' : '';
$customStyles .= "--logo-height: {$logoHeight}px; ";
$customStyles .= "--grid-columns: {$columns}; ";

$blockId = 'companies-' . uniqid();
?>

<section class="block block-companies section <?= $renderer->getBlockClasses($block, $settings) ?? '' ?>"
         id="<?= $blockId ?>"
         style="<?= $customStyles ?>">
    <div class="container">
        <?php if (!empty($content['title']) || !empty($content['subtitle'])): ?>
            <div class="section-header text-center" data-aos="fade-up">
                <?php if (!empty($content['title'])): ?>
                    <h2 class="section-title"><?= htmlspecialchars($content['title']) ?></h2>
                <?php endif; ?>
                <?php if (!empty($content['subtitle'])): ?>
                    <p class="section-subtitle"><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($displayMode === 'carousel'): ?>
            <!-- Carousel Mode -->
            <?php if ($autoplay): ?>
                <!-- Autoplay Infinite Carousel -->
                <div class="companies-carousel-autoplay <?= $grayscale ? 'companies-grayscale' : '' ?>" data-speed="<?= htmlspecialchars($autoplaySpeed) ?>" data-aos="fade-up" data-aos-delay="100">
                    <div class="companies-carousel-track-autoplay">
                        <?php for ($repeat = 0; $repeat < 2; $repeat++): ?>
                            <?php foreach ($companies as $company): ?>
                                <div class="company-item">
                                    <div class="company-card">
                                        <div class="company-logo">
                                            <img src="<?= htmlspecialchars($company['logo_url']) ?>"
                                                 alt="<?= htmlspecialchars($company['name']) ?>"
                                                 loading="lazy">
                                        </div>
                                        <?php if ($showName || $showSector): ?>
                                            <div class="company-info">
                                                <?php if ($showName): ?>
                                                    <span class="company-name"><?= htmlspecialchars($company['name']) ?></span>
                                                <?php endif; ?>
                                                <?php if ($showSector && !empty($company['sector'])): ?>
                                                    <span class="company-sector"><?= htmlspecialchars($company['sector']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Manual Carousel with Navigation -->
                <div class="companies-carousel-wrapper <?= $grayscale ? 'companies-grayscale' : '' ?>" data-aos="fade-up" data-aos-delay="100">
                    <button class="carousel-nav carousel-prev" aria-label="Anterior">
                        <i class="fas fa-chevron-left"></i>
                    </button>

                    <div class="companies-carousel">
                        <div class="companies-carousel-track" style="--visible-items: <?= $visibleItems ?>;">
                            <?php foreach ($companies as $company): ?>
                                <div class="company-item">
                                    <div class="company-card">
                                        <div class="company-logo">
                                            <img src="<?= htmlspecialchars($company['logo_url']) ?>"
                                                 alt="<?= htmlspecialchars($company['name']) ?>"
                                                 loading="lazy">
                                        </div>
                                        <?php if ($showName || $showSector): ?>
                                            <div class="company-info">
                                                <?php if ($showName): ?>
                                                    <span class="company-name"><?= htmlspecialchars($company['name']) ?></span>
                                                <?php endif; ?>
                                                <?php if ($showSector && !empty($company['sector'])): ?>
                                                    <span class="company-sector"><?= htmlspecialchars($company['sector']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
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
            <div class="companies-container companies-grid <?= $grayscale ? 'companies-grayscale' : '' ?>" data-aos="fade-up" data-aos-delay="100">
                <?php foreach ($companies as $index => $company): ?>
                    <div class="company-item" data-aos="fade-up" data-aos-delay="<?= 50 + ($index * 30) ?>">
                        <div class="company-card">
                            <div class="company-logo">
                                <img src="<?= htmlspecialchars($company['logo_url']) ?>"
                                     alt="<?= htmlspecialchars($company['name']) ?>"
                                     loading="lazy">
                            </div>
                            <?php if ($showName || $showSector): ?>
                                <div class="company-info">
                                    <?php if ($showName): ?>
                                        <span class="company-name"><?= htmlspecialchars($company['name']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($showSector && !empty($company['sector'])): ?>
                                        <span class="company-sector"><?= htmlspecialchars($company['sector']) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
#<?= $blockId ?> .companies-grid {
    display: grid;
    grid-template-columns: repeat(var(--grid-columns, 4), 1fr);
    gap: 2rem;
}

#<?= $blockId ?> .company-item {
    text-align: center;
}

#<?= $blockId ?> .company-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

#<?= $blockId ?> .company-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

#<?= $blockId ?> .company-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    height: var(--logo-height, 80px);
    margin-bottom: 0.75rem;
}

#<?= $blockId ?> .company-logo img {
    max-height: 100%;
    max-width: 100%;
    width: auto;
    object-fit: contain;
    transition: filter 0.3s ease;
}

#<?= $blockId ?> .companies-grayscale .company-logo img {
    filter: grayscale(100%);
    opacity: 0.7;
}

#<?= $blockId ?> .companies-grayscale .company-card:hover .company-logo img {
    filter: grayscale(0);
    opacity: 1;
}

#<?= $blockId ?> .company-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

#<?= $blockId ?> .company-name {
    font-weight: 600;
    font-size: 0.95rem;
}

#<?= $blockId ?> .company-sector {
    font-size: 0.8rem;
    color: var(--color-gray-500);
}

@media (max-width: 992px) {
    #<?= $blockId ?> .companies-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    #<?= $blockId ?> .companies-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Carousel Wrapper */
#<?= $blockId ?> .companies-carousel-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
}

#<?= $blockId ?> .companies-carousel {
    flex: 1;
    overflow: hidden;
}

#<?= $blockId ?> .companies-carousel-track {
    display: flex;
    flex-wrap: nowrap;
    gap: 1.5rem;
    transition: transform 0.5s ease;
}

#<?= $blockId ?> .companies-carousel-track .company-item {
    flex: 0 0 auto;
    width: calc((100% - (var(--visible-items, 5) - 1) * 1.5rem) / var(--visible-items, 5));
    min-width: 150px;
}

/* Autoplay Carousel */
#<?= $blockId ?> .companies-carousel-autoplay {
    overflow: hidden;
    position: relative;
    mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
}

#<?= $blockId ?> .companies-carousel-track-autoplay {
    display: flex;
    flex-wrap: nowrap;
    gap: 2rem;
    width: max-content;
    animation: companies-scroll-<?= $blockId ?> 50s linear infinite;
}

#<?= $blockId ?> .companies-carousel-autoplay[data-speed="slow"] .companies-carousel-track-autoplay {
    animation-duration: 80s;
}

#<?= $blockId ?> .companies-carousel-autoplay[data-speed="fast"] .companies-carousel-track-autoplay {
    animation-duration: 30s;
}

#<?= $blockId ?> .companies-carousel-autoplay:hover .companies-carousel-track-autoplay {
    animation-play-state: paused;
}

#<?= $blockId ?> .companies-carousel-track-autoplay .company-item {
    flex-shrink: 0;
    width: 200px;
}

@keyframes companies-scroll-<?= $blockId ?> {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* Carousel Navigation */
#<?= $blockId ?> .carousel-nav {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid var(--color-gray-200, #e5e7eb);
    background: white;
    color: var(--color-gray-600, #4b5563);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

#<?= $blockId ?> .carousel-nav:hover {
    background: var(--color-primary, #2563eb);
    color: white;
    border-color: var(--color-primary, #2563eb);
}

#<?= $blockId ?> .carousel-nav:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

#<?= $blockId ?> .carousel-nav:disabled:hover {
    background: white;
    color: var(--color-gray-600, #4b5563);
    border-color: var(--color-gray-200, #e5e7eb);
}

/* Responsive Carousel */
@media (max-width: 1024px) {
    #<?= $blockId ?> .companies-carousel-track .company-item {
        width: calc((100% - 3 * 1rem) / 4);
    }
}

@media (max-width: 768px) {
    #<?= $blockId ?> .companies-carousel-wrapper {
        flex-direction: column;
        gap: 1rem;
    }

    #<?= $blockId ?> .carousel-nav {
        display: none;
    }

    #<?= $blockId ?> .companies-carousel {
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    #<?= $blockId ?> .companies-carousel::-webkit-scrollbar {
        display: none;
    }

    #<?= $blockId ?> .companies-carousel-track .company-item {
        width: calc((100% - 2 * 1rem) / 3);
        min-width: 130px;
        scroll-snap-align: start;
    }

    #<?= $blockId ?> .companies-carousel-track-autoplay .company-item {
        width: 150px;
    }
}

@media (max-width: 480px) {
    #<?= $blockId ?> .companies-carousel-track .company-item {
        width: calc(50% - 0.5rem);
        min-width: 120px;
    }

    #<?= $blockId ?> .companies-carousel-track-autoplay .company-item {
        width: 130px;
    }
}
</style>

<script>
(function() {
    const block = document.getElementById('<?= $blockId ?>');
    if (!block) return;

    const carousel = block.querySelector('.companies-carousel');
    const track = block.querySelector('.companies-carousel-track');
    const prevBtn = block.querySelector('.carousel-prev');
    const nextBtn = block.querySelector('.carousel-next');

    if (!carousel || !track || !prevBtn || !nextBtn) return;

    const items = track.querySelectorAll('.company-item');
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
