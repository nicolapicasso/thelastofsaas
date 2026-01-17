<?php
/**
 * Video Block Template
 * Displays embedded video (YouTube, Vimeo)
 * Omniwallet CMS
 */

$title = $content['title'] ?? '';
$subtitle = $content['subtitle'] ?? '';
$videoUrl = $content['video_url'] ?? '';
$videoThumbnail = $content['video_thumbnail'] ?? '';
$description = $content['description'] ?? '';
$layout = $settings['layout'] ?? 'full'; // full, left, right

// Display style: styled (default) or minimal (no decoration)
$displayStyle = $settings['display_style'] ?? 'styled';
$videoWidth = $settings['video_width'] ?? '100%';

// Lightbox option
$lightboxEnabled = $settings['lightbox_enabled'] ?? false;

// Overlap settings
$overlapEnabled = $settings['overlap_enabled'] ?? false;
$overlapAmount = $settings['overlap_amount'] ?? '100px';
if ($overlapAmount === 'custom' && !empty($settings['overlap_custom'])) {
    $overlapAmount = $settings['overlap_custom'];
}
$overlapShadow = $settings['overlap_shadow'] ?? true;
$background = $settings['background'] ?? '';

// Build overlap classes and styles
$overlapClass = $overlapEnabled ? 'has-overlap' : '';
$noShadowClass = (!$overlapShadow && $overlapEnabled) ? 'no-shadow' : '';
$minimalClass = $displayStyle === 'minimal' ? 'style-minimal' : '';
$lightboxClass = $lightboxEnabled ? 'has-lightbox' : '';

$customStyles = [];
if ($overlapEnabled) {
    $customStyles[] = "--overlap-amount: {$overlapAmount}";
}
if ($displayStyle === 'minimal' && $videoWidth !== '100%') {
    $customStyles[] = "--video-width: {$videoWidth}";
}

$inlineCustomStyles = !empty($customStyles) ? implode('; ', $customStyles) . ';' : '';

// Parse video URL to get embed URL
if (!function_exists('getVideoEmbedUrl')) {
    function getVideoEmbedUrl(string $url): ?array {
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
                'thumbnail' => null // Vimeo requires API for thumbnail
            ];
        }
        return null;
    }
}

$videoData = $videoUrl ? getVideoEmbedUrl($videoUrl) : null;
$thumbnail = $videoThumbnail ?: ($videoData['thumbnail'] ?? '');
?>

<?php if ($videoData): ?>
<section class="block block-video layout-<?= $layout ?> <?= $overlapClass ?> <?= $noShadowClass ?> <?= $minimalClass ?> <?= $lightboxClass ?> <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $renderer->getBlockStyles($settings) ?>; <?= $inlineCustomStyles ?>">
    <div class="<?= $displayStyle === 'minimal' ? 'container-fluid' : 'container' ?>">
        <?php if (!empty($title)): ?>
            <div class="section-header">
                <h2><?= htmlspecialchars($title) ?></h2>
                <?php if (!empty($subtitle)): ?>
                    <p><?= htmlspecialchars($subtitle) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="video-wrapper <?= $layout !== 'full' ? 'video-with-text' : '' ?>">
            <?php if ($layout !== 'full' && !empty($description)): ?>
                <div class="video-text">
                    <div class="video-description">
                        <?= nl2br(htmlspecialchars($description)) ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="video-container">
                <div class="video-player" data-video-id="<?= htmlspecialchars($videoData['id']) ?>" data-video-type="<?= $videoData['type'] ?>" data-lightbox="<?= $lightboxEnabled ? 'true' : 'false' ?>" data-embed-url="<?= htmlspecialchars($videoData['embed']) ?>">
                    <?php if ($thumbnail): ?>
                        <div class="video-thumbnail">
                            <img src="<?= htmlspecialchars($thumbnail) ?>" alt="<?= htmlspecialchars($title) ?>">
                            <button class="video-play-btn" aria-label="Reproducir video">
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="video-iframe-container" style="display: <?= $thumbnail ? 'none' : 'block' ?>;">
                        <iframe
                            <?php if (!$thumbnail): ?>src="<?= htmlspecialchars($videoData['embed']) ?>"<?php endif; ?>
                            data-src="<?= htmlspecialchars($videoData['embed']) ?>"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.block-video {
    padding: var(--spacing-3xl) 0;
}

/* Overlap effect */
.block-video.has-overlap {
    margin-top: calc(-1 * var(--overlap-amount, 100px));
    padding-top: 0;
    background: transparent !important;
    position: relative;
    z-index: 10;
}

.block-video.has-overlap .video-player {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.block-video.has-overlap.no-shadow .video-player {
    box-shadow: none;
}

/* Transparent background option */
.block-video.bg-transparent {
    background: transparent !important;
}

.block-video .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
}

.block-video .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
}

.block-video .section-header p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
}

.block-video .video-container {
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
}

.block-video.layout-full .video-container {
    max-width: 1000px;
}

.block-video .video-player {
    position: relative;
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    background: #000;
    width: 100%;
    /* Aspect ratio with fallback */
    aspect-ratio: 16/9;
}

/* Fallback for browsers without aspect-ratio support */
@supports not (aspect-ratio: 16/9) {
    .block-video .video-player {
        padding-bottom: 56.25%;
        height: 0;
    }
}

.block-video .video-thumbnail {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 2;
}

.block-video .video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.block-video .video-play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--color-primary);
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    transition: all var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 3;
}

.block-video .video-play-btn:hover {
    background: var(--color-primary-dark);
    transform: translate(-50%, -50%) scale(1.1);
}

.block-video .video-play-btn i {
    margin-left: 4px; /* Visual centering for play icon */
}

.block-video .video-iframe-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.block-video .video-iframe-container iframe {
    width: 100%;
    height: 100%;
    border: none;
}

/* When video is playing (thumbnail hidden), show iframe container */
.block-video .video-thumbnail.is-hidden {
    display: none !important;
}

.block-video .video-iframe-container.is-playing {
    display: block !important;
    z-index: 5;
}

/* Layout with text */
.block-video .video-with-text {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-2xl);
    align-items: center;
}

.block-video.layout-right .video-with-text {
    direction: rtl;
}

.block-video.layout-right .video-with-text > * {
    direction: ltr;
}

.block-video .video-text {
    padding: var(--spacing-lg);
}

.block-video .video-description {
    font-size: var(--font-size-lg);
    line-height: 1.8;
    color: var(--color-gray-700);
}

.block-video .video-with-text .video-container {
    max-width: 100%;
}

@media (max-width: 768px) {
    .block-video .video-with-text {
        grid-template-columns: 1fr;
    }

    .block-video .video-play-btn {
        width: 60px;
        height: 60px;
        font-size: 20px;
    }
}

/* ================================
   MINIMAL STYLE - No decoration
   ================================ */
.block-video.style-minimal {
    padding: 0;
    margin: 0;
}

.block-video.style-minimal .container-fluid {
    padding: 0;
    max-width: 100%;
}

.block-video.style-minimal .video-container {
    max-width: var(--video-width, 100%);
    width: var(--video-width, 100%);
    margin: 0;
    display: inline-block;
    vertical-align: top;
}

.block-video.style-minimal .video-player {
    border-radius: 0;
    box-shadow: none;
}

.block-video.style-minimal .video-play-btn {
    width: 60px;
    height: 60px;
    font-size: 20px;
}

.block-video.style-minimal .section-header {
    display: none;
}

/* ================================
   VIDEO LIGHTBOX
   ================================ */
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
    border-radius: var(--radius-lg);
}

.video-lightbox-iframe iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

/* Lightbox mode - keep thumbnail visible */
.block-video.has-lightbox .video-thumbnail {
    cursor: pointer;
}

.block-video.has-lightbox .video-iframe-container {
    display: none !important;
}

@media (max-width: 768px) {
    .video-lightbox-close {
        top: -40px;
        width: 36px;
        height: 36px;
        font-size: 16px;
    }
}
</style>

<script>
(function() {
    // Create lightbox overlay once
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

        // Close handlers
        overlay.querySelector('.video-lightbox-close').addEventListener('click', closeLightbox);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeLightbox();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });
    }

    function openLightbox(embedUrl) {
        const overlay = document.getElementById('videoLightboxOverlay');
        const iframe = overlay.querySelector('iframe');
        const separator = embedUrl.includes('?') ? '&' : '?';
        iframe.src = embedUrl + separator + 'autoplay=1';
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const overlay = document.getElementById('videoLightboxOverlay');
        const iframe = overlay.querySelector('iframe');
        iframe.src = '';
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    // Handle video thumbnail clicks
    document.querySelectorAll('.block-video .video-thumbnail').forEach(function(thumbnail) {
        thumbnail.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const player = this.closest('.video-player');
            if (!player) return;

            const useLightbox = player.dataset.lightbox === 'true';
            const embedUrl = player.dataset.embedUrl;

            if (useLightbox && embedUrl) {
                // Open in lightbox
                openLightbox(embedUrl);
            } else {
                // Play inline
                const iframeContainer = player.querySelector('.video-iframe-container');
                const iframe = iframeContainer ? iframeContainer.querySelector('iframe') : null;

                if (iframe) {
                    const inlineEmbedUrl = iframe.dataset.src;
                    if (inlineEmbedUrl) {
                        const separator = inlineEmbedUrl.includes('?') ? '&' : '?';
                        iframe.src = inlineEmbedUrl + separator + 'autoplay=1';
                    }
                }

                // Use classes for more reliable CSS control
                this.classList.add('is-hidden');
                if (iframeContainer) {
                    iframeContainer.classList.add('is-playing');
                }
            }
        });
    });
})();
</script>
<?php else: ?>
<section class="block block-video <?= $renderer->getBlockClasses($block, $settings) ?>">
    <div class="container">
        <div class="empty-state">
            <i class="fas fa-video"></i>
            <p>No se ha configurado ningún video</p>
        </div>
    </div>
</section>
<?php endif; ?>
