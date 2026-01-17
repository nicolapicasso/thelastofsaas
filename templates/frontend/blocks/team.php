<?php
/**
 * Team Block Template
 * Displays team members with multiple display modes and layouts
 *
 * Display Modes:
 * - minimalista: Photo only, hover reveals info overlay
 * - basica: Photo with name/role below
 * - sinapsis: Dark bg, role above name, hover shows secondary image/GIF
 *
 * Layout Modes:
 * - grid: Configurable columns and items
 * - carousel: Auto-play slideshow
 *
 * We're Sinapsis CMS
 */

use App\Models\TeamMember;

$teamModel = new TeamMember();

// Get settings
$displayMode = $settings['display_mode'] ?? 'basica';
$layoutMode = $settings['layout_mode'] ?? 'grid';
$columns = (int)($settings['columns'] ?? 4);
$selectionMode = $settings['selection_mode'] ?? 'all';
$selectedMembers = $settings['selected_members'] ?? [];
$limit = (int)($settings['limit'] ?? 12);

// Carousel settings
$autoplay = $settings['autoplay'] ?? true;
$autoplaySpeed = $settings['autoplay_speed'] ?? 'normal';
$visibleItems = (int)($settings['visible_items'] ?? 4);

// Handle selected_members as JSON string
if (is_string($selectedMembers)) {
    $selectedMembers = json_decode($selectedMembers, true) ?? [];
}

// Get members based on selection mode
$members = [];
if ($selectionMode === 'manual' && !empty($selectedMembers)) {
    $members = $teamModel->getByIds($selectedMembers);
} else {
    $members = $teamModel->getAll($limit);
}

// Speed mapping
$speedMap = [
    'slow' => 5000,
    'normal' => 3000,
    'fast' => 2000,
];
$autoplayMs = $speedMap[$autoplaySpeed] ?? 3000;

// Build custom styles
$customStyles = "--team-columns: {$columns}; --team-visible: {$visibleItems}; ";
if (!empty($settings['background_color'])) {
    $customStyles .= "background-color: {$settings['background_color']}; ";
}

$blockId = 'team-' . uniqid();
?>

<section class="block block-team mode-<?= $displayMode ?> layout-<?= $layoutMode ?> section <?= $renderer->getBlockClasses($block, $settings) ?>"
         id="<?= $blockId ?>"
         style="<?= $customStyles ?><?= $renderer->getBlockStyles($settings) ?>">
    <div class="<?= $layoutMode === 'carousel' || $displayMode === 'sinapsis' ? 'container-fluid' : 'container' ?>">
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

        <?php if (!empty($members)): ?>
            <?php if ($layoutMode === 'carousel'): ?>
                <!-- Carousel Mode -->
                <div class="team-carousel-wrapper">
                    <div class="team-carousel" data-autoplay="<?= $autoplay ? 'true' : 'false' ?>" data-speed="<?= $autoplayMs ?>">
                        <div class="team-carousel-track">
                            <?php foreach ($members as $member): ?>
                                <?php include __DIR__ . '/_team_member_card.php'; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button class="carousel-btn carousel-prev" aria-label="Anterior">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-btn carousel-next" aria-label="Siguiente">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            <?php else: ?>
                <!-- Grid Mode -->
                <div class="team-grid">
                    <?php foreach ($members as $member): ?>
                        <?php include __DIR__ . '/_team_member_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>No hay miembros del equipo disponibles</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* ================================
   TEAM BLOCK - BASE
   ================================ */
#<?= $blockId ?> .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
}

#<?= $blockId ?> .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
}

#<?= $blockId ?> .section-header p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
}

/* ================================
   GRID LAYOUT
   ================================ */
#<?= $blockId ?> .team-grid {
    display: grid;
    grid-template-columns: repeat(var(--team-columns, 4), 1fr);
    gap: var(--spacing-lg);
}

/* ================================
   TEAM MEMBER CARD - BASE
   ================================ */
#<?= $blockId ?> .team-member-card {
    display: block;
    text-decoration: none;
    color: inherit;
    overflow: hidden;
    transition: all 0.3s ease;
}

#<?= $blockId ?> .member-photo {
    position: relative;
    width: 100%;
    padding-top: 120%;
    overflow: hidden;
    background-color: var(--color-gray-100);
    border-radius: var(--radius-xl);
}

#<?= $blockId ?> .member-photo img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s ease;
}

#<?= $blockId ?> .member-photo .photo-hover {
    opacity: 0;
}

#<?= $blockId ?> .team-member-card:hover .photo-main {
    opacity: 0;
}

#<?= $blockId ?> .team-member-card:hover .photo-hover {
    opacity: 1;
}

#<?= $blockId ?> .photo-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-200);
}

#<?= $blockId ?> .photo-placeholder i {
    font-size: 48px;
    color: var(--color-gray-400);
}

#<?= $blockId ?> .member-info {
    padding: var(--spacing-md) 0;
    text-align: center;
}

#<?= $blockId ?> .member-info h3 {
    font-size: var(--font-size-base);
    font-weight: 600;
    margin-bottom: var(--spacing-xs);
    color: var(--color-dark);
}

#<?= $blockId ?> .member-role {
    font-size: var(--font-size-xs);
    color: var(--color-gray-500);
    margin: 0;
}

#<?= $blockId ?> .member-bio {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin-top: var(--spacing-sm);
    line-height: 1.5;
}

/* ================================
   MODE: MINIMALISTA
   ================================ */
#<?= $blockId ?>.mode-minimalista .member-info {
    display: none;
}

#<?= $blockId ?>.mode-minimalista .member-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: var(--spacing-lg);
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
    color: white;
    opacity: 0;
    transition: opacity 0.3s ease;
}

#<?= $blockId ?>.mode-minimalista .team-member-card:hover .member-overlay {
    opacity: 1;
}

#<?= $blockId ?>.mode-minimalista .member-overlay h3 {
    font-size: var(--font-size-md);
    margin-bottom: var(--spacing-xs);
    color: white;
}

#<?= $blockId ?>.mode-minimalista .member-overlay .member-role {
    color: rgba(255,255,255,0.8);
}

/* ================================
   MODE: SINAPSIS (Dark theme)
   ================================ */
#<?= $blockId ?>.mode-sinapsis {
    background-color: var(--color-dark, #1A1A1A);
    padding: var(--spacing-3xl) 0;
}

#<?= $blockId ?>.mode-sinapsis .section-header h2,
#<?= $blockId ?>.mode-sinapsis .section-header p {
    color: white;
}

#<?= $blockId ?>.mode-sinapsis .team-grid {
    gap: var(--spacing-xl);
}

#<?= $blockId ?>.mode-sinapsis .team-member-card {
    text-align: left;
}

#<?= $blockId ?>.mode-sinapsis .member-photo {
    border-radius: var(--radius-xl);
}

#<?= $blockId ?>.mode-sinapsis .member-info {
    padding: var(--spacing-lg) var(--spacing-sm);
    text-align: left;
}

#<?= $blockId ?>.mode-sinapsis .member-role {
    font-size: var(--font-size-xs);
    color: var(--color-gray-400);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: var(--spacing-xs);
}

#<?= $blockId ?>.mode-sinapsis .member-info h3 {
    color: white;
    font-size: var(--font-size-base);
    font-weight: 600;
}

#<?= $blockId ?>.mode-sinapsis .member-info {
    display: flex;
    flex-direction: column;
}

/* ================================
   MODE: BASICA
   ================================ */
#<?= $blockId ?>.mode-basica .team-member-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

#<?= $blockId ?>.mode-basica .team-member-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
}

#<?= $blockId ?>.mode-basica .member-photo {
    border-radius: 0;
    padding-top: 100%;
}

#<?= $blockId ?>.mode-basica .member-info {
    padding: var(--spacing-lg);
}

/* ================================
   MODE: DETALLADA
   ================================ */
#<?= $blockId ?>.mode-detallada .team-member-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    text-align: left;
}

#<?= $blockId ?>.mode-detallada .member-photo {
    border-radius: 0;
    padding-top: 100%;
}

#<?= $blockId ?>.mode-detallada .member-info {
    padding: var(--spacing-lg);
    text-align: left;
}

/* ================================
   CAROUSEL LAYOUT
   ================================ */
#<?= $blockId ?> .team-carousel-wrapper {
    position: relative;
    overflow: hidden;
    padding: 0 60px;
}

#<?= $blockId ?> .team-carousel {
    overflow: hidden;
}

#<?= $blockId ?> .team-carousel-track {
    display: flex;
    gap: var(--spacing-lg);
    transition: transform 0.5s ease;
}

#<?= $blockId ?> .team-carousel-track .team-member-card {
    flex: 0 0 calc((100% - (var(--team-visible, 4) - 1) * 24px) / var(--team-visible, 4));
    min-width: 0;
}

#<?= $blockId ?> .carousel-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 50%;
    background: var(--color-white);
    box-shadow: var(--shadow-lg);
    cursor: pointer;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

#<?= $blockId ?> .carousel-btn:hover {
    background: var(--color-primary);
    color: white;
}

#<?= $blockId ?> .carousel-prev { left: 0; }
#<?= $blockId ?> .carousel-next { right: 0; }

/* ================================
   RESPONSIVE
   ================================ */
@media (max-width: 1200px) {
    #<?= $blockId ?> .team-grid {
        grid-template-columns: repeat(min(var(--team-columns, 4), 4), 1fr);
    }
    #<?= $blockId ?> { --team-visible: 3; }
    #<?= $blockId ?> .team-carousel-track .team-member-card {
        flex: 0 0 calc((100% - 2 * 24px) / 3);
    }
}

@media (max-width: 1024px) {
    #<?= $blockId ?> .team-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    #<?= $blockId ?> .team-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-md);
    }
    #<?= $blockId ?> { --team-visible: 2; }
    #<?= $blockId ?> .team-carousel-track .team-member-card {
        flex: 0 0 calc((100% - 16px) / 2);
    }
    #<?= $blockId ?> .team-carousel-track {
        gap: 16px;
    }
    #<?= $blockId ?> .team-carousel-wrapper {
        padding: 0 44px;
    }

    #<?= $blockId ?>.mode-minimalista .member-overlay {
        opacity: 1;
    }

    #<?= $blockId ?> .carousel-btn {
        width: 36px;
        height: 36px;
    }
}

@media (max-width: 540px) {
    #<?= $blockId ?> { --team-visible: 1; }
    #<?= $blockId ?> .team-carousel-track .team-member-card {
        flex: 0 0 100%;
    }
    #<?= $blockId ?> .team-carousel-track {
        gap: 16px;
    }
    #<?= $blockId ?> .team-carousel-wrapper {
        padding: 0 44px;
    }
    #<?= $blockId ?> .team-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<?php if ($layoutMode === 'carousel'): ?>
<script>
(function() {
    const block = document.getElementById('<?= $blockId ?>');
    const carousel = block.querySelector('.team-carousel');
    const track = block.querySelector('.team-carousel-track');
    const cards = track.querySelectorAll('.team-member-card');
    const prevBtn = block.querySelector('.carousel-prev');
    const nextBtn = block.querySelector('.carousel-next');

    if (!cards.length) return;

    let currentIndex = 0;
    const autoplay = carousel.dataset.autoplay === 'true';
    const speed = parseInt(carousel.dataset.speed) || 3000;
    let autoplayInterval;

    function getVisibleItems() {
        const style = getComputedStyle(block);
        return parseInt(style.getPropertyValue('--team-visible')) || 4;
    }

    function updateCarousel() {
        const visibleItems = getVisibleItems();
        const maxIndex = Math.max(0, cards.length - visibleItems);
        currentIndex = Math.min(currentIndex, maxIndex);

        // Get actual computed width of a card
        const cardStyle = getComputedStyle(cards[0]);
        const cardWidth = cards[0].offsetWidth;
        const gapString = getComputedStyle(track).gap;
        const gap = parseInt(gapString) || 16;
        const offset = currentIndex * (cardWidth + gap);

        track.style.transform = `translateX(-${offset}px)`;
    }

    function next() {
        const visibleItems = getVisibleItems();
        const maxIndex = Math.max(0, cards.length - visibleItems);
        currentIndex = currentIndex >= maxIndex ? 0 : currentIndex + 1;
        updateCarousel();
    }

    function prev() {
        const visibleItems = getVisibleItems();
        const maxIndex = Math.max(0, cards.length - visibleItems);
        currentIndex = currentIndex <= 0 ? maxIndex : currentIndex - 1;
        updateCarousel();
    }

    function startAutoplay() {
        if (autoplay) {
            autoplayInterval = setInterval(next, speed);
        }
    }

    function stopAutoplay() {
        clearInterval(autoplayInterval);
    }

    prevBtn.addEventListener('click', () => { stopAutoplay(); prev(); startAutoplay(); });
    nextBtn.addEventListener('click', () => { stopAutoplay(); next(); startAutoplay(); });
    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);

    // Debounced resize handler
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateCarousel, 100);
    });

    // Initial update after a small delay to ensure styles are applied
    setTimeout(updateCarousel, 50);
    startAutoplay();
})();
</script>
<?php endif; ?>
