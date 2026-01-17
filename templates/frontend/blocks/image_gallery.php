<?php
/**
 * Image Gallery Block Template
 * Displays images in grid or carousel with lightbox
 * We're Sinapsis CMS
 */

$title = $content['title'] ?? '';
$subtitle = $content['subtitle'] ?? '';
$images = $content['images'] ?? [];

// Ensure images is an array
if (is_string($images)) {
    $images = json_decode($images, true) ?? [];
}

// Layout settings
$layoutMode = $settings['layout_mode'] ?? 'grid';
$columns = (int)($settings['columns'] ?? 4);
$visibleItems = (int)($settings['visible_items'] ?? 4);
$aspectRatio = $settings['aspect_ratio'] ?? '1:1';
$gap = $settings['gap'] ?? 'md';

// Carousel settings
$autoplay = $settings['autoplay'] ?? true;
$autoplaySpeed = $settings['autoplay_speed'] ?? 'normal';

// Speed mapping
$speedMap = [
    'slow' => 5000,
    'normal' => 3000,
    'fast' => 2000,
];
$autoplayMs = $speedMap[$autoplaySpeed] ?? 3000;

// Aspect ratio mapping
$ratioMap = [
    '1:1' => '100%',
    '4:3' => '75%',
    '16:9' => '56.25%',
    '3:2' => '66.67%',
    'original' => 'auto',
];
$paddingBottom = $ratioMap[$aspectRatio] ?? '100%';

$blockId = 'image-gallery-' . uniqid();
?>

<?php if (!empty($images)): ?>
<section class="block block-image-gallery layout-<?= $layoutMode ?> <?= $renderer->getBlockClasses($block, $settings) ?>"
         id="<?= $blockId ?>"
         style="<?= $renderer->getBlockStyles($settings) ?>">
    <div class="container">
        <?php if (!empty($title) || !empty($subtitle)): ?>
            <div class="section-header">
                <?php if (!empty($title)): ?>
                    <h2><?= htmlspecialchars($title) ?></h2>
                <?php endif; ?>
                <?php if (!empty($subtitle)): ?>
                    <p><?= htmlspecialchars($subtitle) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($layoutMode === 'carousel'): ?>
            <!-- Carousel Mode -->
            <div class="gallery-carousel-wrapper">
                <div class="gallery-carousel" data-autoplay="<?= $autoplay ? 'true' : 'false' ?>" data-speed="<?= $autoplayMs ?>">
                    <div class="gallery-carousel-track">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="gallery-item" data-index="<?= $index ?>">
                                <div class="gallery-image <?= $aspectRatio === 'original' ? 'ratio-original' : '' ?>" style="<?= $aspectRatio !== 'original' ? "padding-bottom: {$paddingBottom};" : '' ?>">
                                    <img src="<?= htmlspecialchars($image['url'] ?? $image) ?>"
                                         alt="<?= htmlspecialchars($image['alt'] ?? 'Imagen ' . ($index + 1)) ?>"
                                         loading="lazy">
                                </div>
                            </div>
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
            <div class="gallery-grid">
                <?php foreach ($images as $index => $image): ?>
                    <div class="gallery-item" data-index="<?= $index ?>">
                        <div class="gallery-image <?= $aspectRatio === 'original' ? 'ratio-original' : '' ?>" style="<?= $aspectRatio !== 'original' ? "padding-bottom: {$paddingBottom};" : '' ?>">
                            <img src="<?= htmlspecialchars($image['url'] ?? $image) ?>"
                                 alt="<?= htmlspecialchars($image['alt'] ?? 'Imagen ' . ($index + 1)) ?>"
                                 loading="lazy">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Image Lightbox -->
<div class="image-lightbox-overlay" id="<?= $blockId ?>-lightbox">
    <button class="lightbox-close" aria-label="Cerrar">
        <i class="fas fa-times"></i>
    </button>
    <button class="lightbox-prev" aria-label="Anterior">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="lightbox-next" aria-label="Siguiente">
        <i class="fas fa-chevron-right"></i>
    </button>
    <div class="lightbox-content">
        <img src="" alt="">
    </div>
    <div class="lightbox-counter">
        <span class="current">1</span> / <span class="total"><?= count($images) ?></span>
    </div>
</div>

<style>
/* ================================
   IMAGE GALLERY - BASE
   ================================ */
#<?= $blockId ?> {
    --gallery-columns: <?= $columns ?>;
    --gallery-visible: <?= $visibleItems ?>;
    --gallery-gap: var(--spacing-<?= $gap ?>);
}

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
#<?= $blockId ?> .gallery-grid {
    display: grid;
    grid-template-columns: repeat(var(--gallery-columns), 1fr);
    gap: var(--gallery-gap);
}

/* ================================
   GALLERY ITEM
   ================================ */
#<?= $blockId ?> .gallery-item {
    cursor: pointer;
    overflow: hidden;
    border-radius: var(--radius-lg);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

#<?= $blockId ?> .gallery-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

#<?= $blockId ?> .gallery-image {
    position: relative;
    overflow: hidden;
    background: var(--color-gray-100);
}

#<?= $blockId ?> .gallery-image:not(.ratio-original) {
    height: 0;
}

#<?= $blockId ?> .gallery-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

#<?= $blockId ?> .gallery-image.ratio-original {
    height: auto;
}

#<?= $blockId ?> .gallery-image.ratio-original img {
    position: relative;
    height: auto;
}

#<?= $blockId ?> .gallery-item:hover .gallery-image img {
    transform: scale(1.05);
}

/* ================================
   CAROUSEL LAYOUT
   ================================ */
#<?= $blockId ?> .gallery-carousel-wrapper {
    position: relative;
    padding: 0 60px;
}

#<?= $blockId ?> .gallery-carousel {
    overflow: hidden;
}

#<?= $blockId ?> .gallery-carousel-track {
    display: flex;
    gap: var(--gallery-gap);
    transition: transform 0.5s ease;
}

#<?= $blockId ?> .gallery-carousel-track .gallery-item {
    flex: 0 0 calc((100% - (var(--gallery-visible) - 1) * var(--gallery-gap)) / var(--gallery-visible));
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
   LIGHTBOX
   ================================ */
#<?= $blockId ?>-lightbox {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.95);
    z-index: 99999;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

#<?= $blockId ?>-lightbox.is-open {
    opacity: 1;
    visibility: visible;
}

#<?= $blockId ?>-lightbox .lightbox-content {
    max-width: 90%;
    max-height: 85vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

#<?= $blockId ?>-lightbox .lightbox-content img {
    max-width: 100%;
    max-height: 85vh;
    object-fit: contain;
    border-radius: var(--radius-lg);
}

#<?= $blockId ?>-lightbox .lightbox-close {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 44px;
    height: 44px;
    background: transparent;
    border: 2px solid rgba(255,255,255,0.5);
    color: white;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.2s ease;
    z-index: 10;
}

#<?= $blockId ?>-lightbox .lightbox-close:hover {
    background: white;
    color: black;
    border-color: white;
}

#<?= $blockId ?>-lightbox .lightbox-prev,
#<?= $blockId ?>-lightbox .lightbox-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.1);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: all 0.2s ease;
    z-index: 10;
}

#<?= $blockId ?>-lightbox .lightbox-prev { left: 20px; }
#<?= $blockId ?>-lightbox .lightbox-next { right: 20px; }

#<?= $blockId ?>-lightbox .lightbox-prev:hover,
#<?= $blockId ?>-lightbox .lightbox-next:hover {
    background: white;
    color: black;
    border-color: white;
}

#<?= $blockId ?>-lightbox .lightbox-counter {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-size: var(--font-size-sm);
    background: rgba(0,0,0,0.5);
    padding: 8px 16px;
    border-radius: var(--radius-full);
}

/* ================================
   RESPONSIVE
   ================================ */
@media (max-width: 1200px) {
    #<?= $blockId ?> { --gallery-visible: 3; }
    #<?= $blockId ?> .gallery-grid {
        grid-template-columns: repeat(min(var(--gallery-columns), 4), 1fr);
    }
}

@media (max-width: 1024px) {
    #<?= $blockId ?> .gallery-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    #<?= $blockId ?> { --gallery-visible: 2; }
    #<?= $blockId ?> .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    #<?= $blockId ?> .gallery-carousel-wrapper {
        padding: 0 44px;
    }
    #<?= $blockId ?> .carousel-btn {
        width: 36px;
        height: 36px;
    }
    #<?= $blockId ?>-lightbox .lightbox-prev,
    #<?= $blockId ?>-lightbox .lightbox-next {
        width: 40px;
        height: 40px;
    }
}

@media (max-width: 540px) {
    #<?= $blockId ?> { --gallery-visible: 1; }
    #<?= $blockId ?>-lightbox .lightbox-prev { left: 10px; }
    #<?= $blockId ?>-lightbox .lightbox-next { right: 10px; }
}
</style>

<script>
(function() {
    const block = document.getElementById('<?= $blockId ?>');
    const lightbox = document.getElementById('<?= $blockId ?>-lightbox');
    const lightboxImg = lightbox.querySelector('.lightbox-content img');
    const lightboxCounter = lightbox.querySelector('.lightbox-counter .current');
    const items = block.querySelectorAll('.gallery-item');
    const images = <?= json_encode(array_map(function($img) { return $img['url'] ?? $img; }, $images)) ?>;

    let currentIndex = 0;

    // Open lightbox
    items.forEach(function(item) {
        item.addEventListener('click', function() {
            currentIndex = parseInt(this.dataset.index);
            showImage(currentIndex);
            lightbox.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        });
    });

    // Close lightbox
    lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('is-open')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') showPrev();
        if (e.key === 'ArrowRight') showNext();
    });

    // Navigation
    lightbox.querySelector('.lightbox-prev').addEventListener('click', showPrev);
    lightbox.querySelector('.lightbox-next').addEventListener('click', showNext);

    function showImage(index) {
        lightboxImg.src = images[index];
        lightboxCounter.textContent = index + 1;
    }

    function showPrev() {
        currentIndex = currentIndex <= 0 ? images.length - 1 : currentIndex - 1;
        showImage(currentIndex);
    }

    function showNext() {
        currentIndex = currentIndex >= images.length - 1 ? 0 : currentIndex + 1;
        showImage(currentIndex);
    }

    function closeLightbox() {
        lightbox.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    // Carousel functionality
    <?php if ($layoutMode === 'carousel'): ?>
    const carousel = block.querySelector('.gallery-carousel');
    const track = block.querySelector('.gallery-carousel-track');
    const cards = track.querySelectorAll('.gallery-item');
    const prevBtn = block.querySelector('.carousel-prev');
    const nextBtn = block.querySelector('.carousel-next');

    let carouselIndex = 0;
    const autoplay = carousel.dataset.autoplay === 'true';
    const speed = parseInt(carousel.dataset.speed) || 3000;
    let autoplayInterval;

    function getVisibleItems() {
        const style = getComputedStyle(block);
        return parseInt(style.getPropertyValue('--gallery-visible')) || 4;
    }

    function updateCarousel() {
        const visibleItems = getVisibleItems();
        const maxIndex = Math.max(0, cards.length - visibleItems);
        carouselIndex = Math.min(carouselIndex, maxIndex);

        const cardWidth = cards[0].offsetWidth;
        const gap = parseInt(getComputedStyle(track).gap) || 16;
        const offset = carouselIndex * (cardWidth + gap);

        track.style.transform = 'translateX(-' + offset + 'px)';
    }

    function carouselNext() {
        const visibleItems = getVisibleItems();
        const maxIndex = Math.max(0, cards.length - visibleItems);
        carouselIndex = carouselIndex >= maxIndex ? 0 : carouselIndex + 1;
        updateCarousel();
    }

    function carouselPrev() {
        const visibleItems = getVisibleItems();
        const maxIndex = Math.max(0, cards.length - visibleItems);
        carouselIndex = carouselIndex <= 0 ? maxIndex : carouselIndex - 1;
        updateCarousel();
    }

    function startAutoplay() {
        if (autoplay) {
            autoplayInterval = setInterval(carouselNext, speed);
        }
    }

    function stopAutoplay() {
        clearInterval(autoplayInterval);
    }

    prevBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        stopAutoplay();
        carouselPrev();
        startAutoplay();
    });
    nextBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        stopAutoplay();
        carouselNext();
        startAutoplay();
    });
    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);

    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateCarousel, 100);
    });

    setTimeout(updateCarousel, 50);
    startAutoplay();
    <?php endif; ?>
})();
</script>
<?php else: ?>
<section class="block block-image-gallery <?= $renderer->getBlockClasses($block, $settings) ?>">
    <div class="container">
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <p>No hay imágenes en la galería</p>
        </div>
    </div>
</section>
<?php endif; ?>
