<?php
/**
 * Video Gallery Block Template
 * Displays videos in grid or carousel with lightbox
 * We're Sinapsis CMS
 */

$title = $content['title'] ?? '';
$subtitle = $content['subtitle'] ?? '';
$videos = $content['videos'] ?? [];

// Ensure videos is an array
if (is_string($videos)) {
    $videos = json_decode($videos, true) ?? [];
}

// Parse video URLs
if (!function_exists('parseVideoUrl')) {
    function parseVideoUrl(string $url): ?array {
        // YouTube
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return [
                'type' => 'youtube',
                'id' => $matches[1],
                'embed' => "https://www.youtube.com/embed/{$matches[1]}?rel=0&modestbranding=1",
                'thumbnail' => "https://img.youtube.com/vi/{$matches[1]}/maxresdefault.jpg"
            ];
        }
        // Vimeo
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $matches)) {
            return [
                'type' => 'vimeo',
                'id' => $matches[1],
                'embed' => "https://player.vimeo.com/video/{$matches[1]}?title=0&byline=0&portrait=0",
                'thumbnail' => null
            ];
        }
        return null;
    }
}

// Process videos
$processedVideos = [];
foreach ($videos as $video) {
    $url = $video['url'] ?? '';
    if (empty($url)) continue;

    $parsed = parseVideoUrl($url);
    if ($parsed) {
        $processedVideos[] = [
            'url' => $url,
            'embed' => $parsed['embed'],
            'thumbnail' => $video['thumbnail'] ?? $parsed['thumbnail'] ?? '',
            'type' => $parsed['type'],
            'id' => $parsed['id'],
        ];
    }
}

// Layout settings
$layoutMode = $settings['layout_mode'] ?? 'grid';
$columns = (int)($settings['columns'] ?? 3);
$visibleItems = (int)($settings['visible_items'] ?? 3);
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

$blockId = 'video-gallery-' . uniqid();
?>

<?php if (!empty($processedVideos)): ?>
<section class="block block-video-gallery layout-<?= $layoutMode ?> <?= $renderer->getBlockClasses($block, $settings) ?>"
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
            <div class="vgallery-carousel-wrapper">
                <div class="vgallery-carousel" data-autoplay="<?= $autoplay ? 'true' : 'false' ?>" data-speed="<?= $autoplayMs ?>">
                    <div class="vgallery-carousel-track">
                        <?php foreach ($processedVideos as $index => $video): ?>
                            <div class="vgallery-item" data-index="<?= $index ?>" data-embed="<?= htmlspecialchars($video['embed']) ?>">
                                <div class="vgallery-thumbnail">
                                    <?php if (!empty($video['thumbnail'])): ?>
                                        <img src="<?= htmlspecialchars($video['thumbnail']) ?>" alt="Video <?= $index + 1 ?>" loading="lazy">
                                    <?php else: ?>
                                        <div class="vgallery-placeholder">
                                            <i class="fas fa-video"></i>
                                        </div>
                                    <?php endif; ?>
                                    <button class="vgallery-play-btn" aria-label="Reproducir video">
                                        <i class="fas fa-play"></i>
                                    </button>
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
            <div class="vgallery-grid">
                <?php foreach ($processedVideos as $index => $video): ?>
                    <div class="vgallery-item" data-index="<?= $index ?>" data-embed="<?= htmlspecialchars($video['embed']) ?>">
                        <div class="vgallery-thumbnail">
                            <?php if (!empty($video['thumbnail'])): ?>
                                <img src="<?= htmlspecialchars($video['thumbnail']) ?>" alt="Video <?= $index + 1 ?>" loading="lazy">
                            <?php else: ?>
                                <div class="vgallery-placeholder">
                                    <i class="fas fa-video"></i>
                                </div>
                            <?php endif; ?>
                            <button class="vgallery-play-btn" aria-label="Reproducir video">
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* ================================
   VIDEO GALLERY - BASE
   ================================ */
#<?= $blockId ?> {
    --vgallery-columns: <?= $columns ?>;
    --vgallery-visible: <?= $visibleItems ?>;
    --vgallery-gap: var(--spacing-<?= $gap ?>);
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
#<?= $blockId ?> .vgallery-grid {
    display: grid;
    grid-template-columns: repeat(var(--vgallery-columns), 1fr);
    gap: var(--vgallery-gap);
}

/* ================================
   VIDEO ITEM
   ================================ */
#<?= $blockId ?> .vgallery-item {
    cursor: pointer;
    overflow: hidden;
    border-radius: var(--radius-lg);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

#<?= $blockId ?> .vgallery-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

#<?= $blockId ?> .vgallery-thumbnail {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 */
    overflow: hidden;
    background: var(--color-gray-900);
}

#<?= $blockId ?> .vgallery-thumbnail img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

#<?= $blockId ?> .vgallery-item:hover .vgallery-thumbnail img {
    transform: scale(1.05);
}

#<?= $blockId ?> .vgallery-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-800);
}

#<?= $blockId ?> .vgallery-placeholder i {
    font-size: 48px;
    color: var(--color-gray-500);
}

#<?= $blockId ?> .vgallery-play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--color-primary);
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

#<?= $blockId ?> .vgallery-play-btn:hover {
    background: var(--color-primary-dark);
    transform: translate(-50%, -50%) scale(1.1);
}

#<?= $blockId ?> .vgallery-play-btn i {
    margin-left: 3px;
}

/* ================================
   CAROUSEL LAYOUT
   ================================ */
#<?= $blockId ?> .vgallery-carousel-wrapper {
    position: relative;
    padding: 0 60px;
}

#<?= $blockId ?> .vgallery-carousel {
    overflow: hidden;
}

#<?= $blockId ?> .vgallery-carousel-track {
    display: flex;
    gap: var(--vgallery-gap);
    transition: transform 0.5s ease;
}

#<?= $blockId ?> .vgallery-carousel-track .vgallery-item {
    flex: 0 0 calc((100% - (var(--vgallery-visible) - 1) * var(--vgallery-gap)) / var(--vgallery-visible));
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
    #<?= $blockId ?> { --vgallery-visible: 3; }
    #<?= $blockId ?> .vgallery-grid {
        grid-template-columns: repeat(min(var(--vgallery-columns), 3), 1fr);
    }
}

@media (max-width: 1024px) {
    #<?= $blockId ?> .vgallery-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    #<?= $blockId ?> { --vgallery-visible: 2; }
    #<?= $blockId ?> .vgallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    #<?= $blockId ?> .vgallery-carousel-wrapper {
        padding: 0 44px;
    }
    #<?= $blockId ?> .carousel-btn {
        width: 36px;
        height: 36px;
    }
    #<?= $blockId ?> .vgallery-play-btn {
        width: 50px;
        height: 50px;
        font-size: 18px;
    }
}

@media (max-width: 540px) {
    #<?= $blockId ?> { --vgallery-visible: 1; }
    #<?= $blockId ?> .vgallery-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
(function() {
    const block = document.getElementById('<?= $blockId ?>');
    const items = block.querySelectorAll('.vgallery-item');

    // Reuse or create video lightbox
    if (!document.getElementById('videoLightboxOverlay')) {
        const overlay = document.createElement('div');
        overlay.id = 'videoLightboxOverlay';
        overlay.className = 'video-lightbox-overlay';
        overlay.innerHTML = `
            <div class="video-lightbox-content">
                <button class="video-lightbox-close" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
                <div class="video-lightbox-iframe">
                    <iframe allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        // Add lightbox styles if not present
        if (!document.getElementById('videoLightboxStyles')) {
            const styles = document.createElement('style');
            styles.id = 'videoLightboxStyles';
            styles.textContent = `
                .video-lightbox-overlay {
                    position: fixed;
                    inset: 0;
                    background: rgba(0, 0, 0, 0.95);
                    z-index: 99999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    visibility: hidden;
                    transition: opacity 0.3s ease, visibility 0.3s ease;
                }
                .video-lightbox-overlay.is-open {
                    opacity: 1;
                    visibility: visible;
                }
                .video-lightbox-content {
                    width: 90%;
                    max-width: 1200px;
                    position: relative;
                }
                .video-lightbox-close {
                    position: absolute;
                    top: -50px;
                    right: 0;
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
                }
                .video-lightbox-close:hover {
                    background: white;
                    color: black;
                    border-color: white;
                }
                .video-lightbox-iframe {
                    position: relative;
                    padding-bottom: 56.25%;
                    height: 0;
                    overflow: hidden;
                    border-radius: 12px;
                }
                .video-lightbox-iframe iframe {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    border: none;
                }
            `;
            document.head.appendChild(styles);
        }

        // Close handlers
        overlay.querySelector('.video-lightbox-close').addEventListener('click', closeVideoLightbox);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeVideoLightbox();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeVideoLightbox();
        });
    }

    function openVideoLightbox(embedUrl) {
        const overlay = document.getElementById('videoLightboxOverlay');
        const iframe = overlay.querySelector('iframe');
        const separator = embedUrl.includes('?') ? '&' : '?';
        iframe.src = embedUrl + separator + 'autoplay=1';
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeVideoLightbox() {
        const overlay = document.getElementById('videoLightboxOverlay');
        if (!overlay) return;
        const iframe = overlay.querySelector('iframe');
        iframe.src = '';
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    // Click handlers
    items.forEach(function(item) {
        item.addEventListener('click', function() {
            const embedUrl = this.dataset.embed;
            if (embedUrl) {
                openVideoLightbox(embedUrl);
            }
        });
    });

    // Carousel functionality
    <?php if ($layoutMode === 'carousel'): ?>
    const carousel = block.querySelector('.vgallery-carousel');
    const track = block.querySelector('.vgallery-carousel-track');
    const cards = track.querySelectorAll('.vgallery-item');
    const prevBtn = block.querySelector('.carousel-prev');
    const nextBtn = block.querySelector('.carousel-next');

    let carouselIndex = 0;
    const autoplay = carousel.dataset.autoplay === 'true';
    const speed = parseInt(carousel.dataset.speed) || 3000;
    let autoplayInterval;

    function getVisibleItems() {
        const style = getComputedStyle(block);
        return parseInt(style.getPropertyValue('--vgallery-visible')) || 3;
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
<section class="block block-video-gallery <?= $renderer->getBlockClasses($block, $settings) ?>">
    <div class="container">
        <div class="empty-state">
            <i class="fas fa-video"></i>
            <p>No hay videos en la galería</p>
        </div>
    </div>
</section>
<?php endif; ?>
