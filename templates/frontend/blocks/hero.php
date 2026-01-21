<?php
/**
 * Hero Block Template
 * Omniwallet CMS
 */
$slides = $content['slides'] ?? [];
$showSlider = count($slides) > 1;
$textAlignment = $settings['text_alignment'] ?? 'left';
$heroHeight = $settings['height'] ?? '600px';
$overlayOpacity = $settings['overlay_opacity'] ?? 0.6;
$parallaxEnabled = $settings['parallax_enabled'] ?? true;
$heroId = 'hero-' . ($block['id'] ?? uniqid());

/**
 * Parse video URL and return embed data
 * @param string $url YouTube or Vimeo URL
 * @return array|null ['type' => 'youtube'|'vimeo', 'id' => 'videoId', 'embed' => 'embedUrl']
 */
if (!function_exists('parseVideoUrl')) {
    function parseVideoUrl(string $url): ?array {
        if (empty($url)) return null;

        // YouTube patterns
        $youtubePatterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/',
        ];

        foreach ($youtubePatterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return [
                    'type' => 'youtube',
                    'id' => $matches[1],
                    'embed' => 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $matches[1] . '&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1&enablejsapi=1'
                ];
            }
        }

        // Vimeo patterns
        $vimeoPatterns = [
            '/vimeo\.com\/(\d+)/',
            '/player\.vimeo\.com\/video\/(\d+)/',
        ];

        foreach ($vimeoPatterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return [
                    'type' => 'vimeo',
                    'id' => $matches[1],
                    'embed' => 'https://player.vimeo.com/video/' . $matches[1] . '?autoplay=1&muted=1&loop=1&background=1&quality=1080p'
                ];
            }
        }

        return null;
    }
}

// Calculate margins
$marginTop = $settings['margin_top'] ?? 'auto';
if ($marginTop === 'auto') {
    $marginTopValue = 'var(--header-height)';
} elseif ($marginTop === 'custom' && !empty($settings['margin_top_custom'])) {
    $marginTopValue = $settings['margin_top_custom'];
} elseif ($marginTop === '0') {
    $marginTopValue = '0';
} else {
    $marginTopValue = $marginTop;
}

$marginBottom = $settings['margin_bottom'] ?? '0';
if ($marginBottom === 'custom' && !empty($settings['margin_bottom_custom'])) {
    $marginBottomValue = $settings['margin_bottom_custom'];
} else {
    $marginBottomValue = $marginBottom;
}

$parallaxClass = $parallaxEnabled ? 'parallax-enabled' : '';
?>

<section class="block block-hero hero-align-<?= $textAlignment ?> <?= $parallaxClass ?> <?= $renderer->getBlockClasses($block, $settings) ?>" id="<?= $heroId ?>" style="<?= $renderer->getBlockStyles($settings) ?>; --hero-height: <?= $heroHeight ?>; --overlay-opacity: <?= $overlayOpacity ?>; --hero-margin-top: <?= $marginTopValue ?>; --hero-margin-bottom: <?= $marginBottomValue ?>;" <?= $parallaxEnabled ? 'data-parallax="true"' : '' ?>>
    <?php if ($showSlider): ?>
        <div class="hero-slider" data-autoplay="<?= $settings['autoplay'] ?? 'true' ?>" data-interval="<?= $settings['interval'] ?? 5000 ?>">
            <?php foreach ($slides as $i => $slide):
                $titleColor = $slide['title_color'] ?? '#ffffff';
                $subtitleColor = $slide['subtitle_color'] ?? 'rgba(255,255,255,0.9)';
                $videoData = parseVideoUrl($slide['background_video'] ?? '');
                $hasVideo = $videoData !== null;
                $bgStyle = !empty($slide['background_image']) ? "background-image: url('{$slide['background_image']}'); --parallax-bg: url('{$slide['background_image']}')" : '';
            ?>
                <div class="hero-slide <?= $i === 0 ? 'active' : '' ?> <?= $hasVideo ? 'has-video' : '' ?>"
                     style="<?= $bgStyle ?>"
                     data-bg="<?= htmlspecialchars($slide['background_image'] ?? '') ?>">
                    <?php if ($hasVideo): ?>
                        <div class="hero-video-container">
                            <iframe
                                src="<?= htmlspecialchars($videoData['embed']) ?>"
                                frameborder="0"
                                allow="autoplay; fullscreen; picture-in-picture"
                                allowfullscreen
                                title="Background video"
                                loading="lazy"
                            ></iframe>
                        </div>
                    <?php endif; ?>
                    <div class="container">
                        <div class="hero-content">
                            <?php if (!empty($slide['title'])): ?>
                                <h1 style="color: <?= htmlspecialchars($titleColor) ?>"><?= htmlspecialchars($slide['title']) ?></h1>
                            <?php endif; ?>
                            <?php if (!empty($slide['subtitle'])): ?>
                                <p class="hero-subtitle" style="color: <?= htmlspecialchars($subtitleColor) ?>"><?= htmlspecialchars($slide['subtitle']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($slide['cta_text']) && !empty($slide['cta_url'])): ?>
                                <div class="hero-actions">
                                    <a href="<?= htmlspecialchars($slide['cta_url']) ?>" class="btn btn-primary btn-lg">
                                        <?= htmlspecialchars($slide['cta_text']) ?>
                                    </a>
                                    <?php if (!empty($slide['cta_secondary_text']) && !empty($slide['cta_secondary_url'])): ?>
                                        <a href="<?= htmlspecialchars($slide['cta_secondary_url']) ?>" class="btn btn-outline btn-lg">
                                            <?= htmlspecialchars($slide['cta_secondary_text']) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($slide['image']) && $textAlignment !== 'center'): ?>
                            <div class="hero-image">
                                <img src="<?= htmlspecialchars($slide['image']) ?>" alt="<?= htmlspecialchars($slide['title'] ?? '') ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (count($slides) > 1): ?>
                <div class="slider-nav">
                    <button class="slider-prev" aria-label="<?= __('previous') ?>"><i class="fas fa-chevron-left"></i></button>
                    <div class="slider-dots">
                        <?php foreach ($slides as $i => $slide): ?>
                            <button class="slider-dot <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <button class="slider-next" aria-label="<?= __('next') ?>"><i class="fas fa-chevron-right"></i></button>
                </div>
            <?php endif; ?>
        </div>
    <?php elseif (!empty($slides[0])): ?>
        <?php
        $slide = $slides[0];
        $titleColor = $slide['title_color'] ?? '#ffffff';
        $subtitleColor = $slide['subtitle_color'] ?? 'rgba(255,255,255,0.9)';
        $videoData = parseVideoUrl($slide['background_video'] ?? '');
        $hasVideo = $videoData !== null;
        $bgStyle = !empty($slide['background_image']) ? "background-image: url('{$slide['background_image']}'); --parallax-bg: url('{$slide['background_image']}')" : '';
        ?>
        <div class="hero-single <?= $hasVideo ? 'has-video' : '' ?>" style="<?= $bgStyle ?>" data-bg="<?= htmlspecialchars($slide['background_image'] ?? '') ?>">
            <?php if ($hasVideo): ?>
                <div class="hero-video-container">
                    <iframe
                        src="<?= htmlspecialchars($videoData['embed']) ?>"
                        frameborder="0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen
                        title="Background video"
                        loading="lazy"
                    ></iframe>
                </div>
            <?php endif; ?>
            <div class="container">
                <div class="hero-content">
                    <?php if (!empty($slide['title'])): ?>
                        <h1 style="color: <?= htmlspecialchars($titleColor) ?>"><?= htmlspecialchars($slide['title']) ?></h1>
                    <?php endif; ?>
                    <?php if (!empty($slide['subtitle'])): ?>
                        <p class="hero-subtitle" style="color: <?= htmlspecialchars($subtitleColor) ?>"><?= htmlspecialchars($slide['subtitle']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($slide['cta_text']) && !empty($slide['cta_url'])): ?>
                        <div class="hero-actions">
                            <a href="<?= htmlspecialchars($slide['cta_url']) ?>" class="btn btn-primary btn-lg">
                                <?= htmlspecialchars($slide['cta_text']) ?>
                            </a>
                            <?php if (!empty($slide['cta_secondary_text']) && !empty($slide['cta_secondary_url'])): ?>
                                <a href="<?= htmlspecialchars($slide['cta_secondary_url']) ?>" class="btn btn-outline btn-lg">
                                    <?= htmlspecialchars($slide['cta_secondary_text']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($slide['image']) && $textAlignment !== 'center'): ?>
                    <div class="hero-image">
                        <img src="<?= htmlspecialchars($slide['image']) ?>" alt="<?= htmlspecialchars($slide['title'] ?? '') ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<style>
.block-hero {
    padding-top: var(--hero-margin-top, var(--header-height));
    margin-bottom: var(--hero-margin-bottom, 0);
    overflow: hidden;
}

.hero-slide,
.hero-single {
    min-height: var(--hero-height, 600px);
    display: flex;
    align-items: center;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
}

/* Video Background */
.hero-video-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: -2;
    pointer-events: none;
}

.hero-video-container iframe {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100vw;
    height: 100vh;
    min-width: 177.78vh; /* 16:9 aspect ratio */
    min-height: 56.25vw; /* 16:9 aspect ratio */
    transform: translate(-50%, -50%);
    pointer-events: none;
}

/* When video is present, hide the parallax background */
.hero-slide.has-video::after,
.hero-single.has-video::after {
    display: none;
}

.hero-slide.has-video,
.hero-single.has-video {
    background-image: none !important;
}

/* Parallax effect using pseudo-element for better mobile support */
.block-hero.parallax-enabled .hero-slide,
.block-hero.parallax-enabled .hero-single {
    background-image: none !important; /* Remove inline background */
}

.block-hero.parallax-enabled .hero-slide::after,
.block-hero.parallax-enabled .hero-single::after {
    content: '';
    position: absolute;
    top: -30%;
    left: 0;
    right: 0;
    bottom: -30%;
    background-image: var(--parallax-bg);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: -1;
    will-change: transform;
    transform: translateY(var(--parallax-translate, 0));
}

/* Disable parallax animations for users who prefer reduced motion */
@media (prefers-reduced-motion: reduce) {
    .block-hero.parallax-enabled .hero-slide::after,
    .block-hero.parallax-enabled .hero-single::after {
        top: 0;
        bottom: 0;
        will-change: auto;
    }
}

.hero-slide::before,
.hero-single::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, var(--overlay-opacity, 0.6));
    z-index: 0;
}

/* Extend overlay for parallax mode to cover the moving background */
.block-hero.parallax-enabled .hero-slide::before,
.block-hero.parallax-enabled .hero-single::before {
    top: -30%;
    bottom: -30%;
    left: 0;
    right: 0;
}

.hero-slide .container,
.hero-single .container {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-2xl);
    align-items: center;
}

/* Center alignment */
.hero-align-center .hero-slide .container,
.hero-align-center .hero-single .container {
    grid-template-columns: 1fr;
    text-align: center;
    max-width: 1250px;
}

.hero-align-center .hero-content {
    max-width: 1250px;
    margin: 0 auto;
}

.hero-align-center .hero-actions {
    justify-content: center;
}

/* Right alignment */
.hero-align-right .hero-slide .container,
.hero-align-right .hero-single .container {
    direction: rtl;
}

.hero-align-right .hero-slide .container > *,
.hero-align-right .hero-single .container > * {
    direction: ltr;
}

.hero-align-right .hero-content {
    text-align: right;
}

.hero-align-right .hero-actions {
    justify-content: flex-end;
}

.hero-content {
    color: var(--color-white);
}

.hero-content h1 {
    font-size: var(--font-size-5xl);
    color: var(--color-white);
    margin-bottom: var(--spacing-lg);
    line-height: 1.1;
}

.hero-subtitle {
    font-size: var(--font-size-xl);
    opacity: 0.9;
    margin-bottom: var(--spacing-xl);
}

.hero-actions {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.hero-image img {
    width: 100%;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
}

/* Slider */
.hero-slider {
    position: relative;
}

.hero-slide {
    display: none;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.hero-slide.active {
    display: flex;
    opacity: 1;
}

/* Slider Navigation */
.slider-nav {
    position: absolute;
    bottom: var(--spacing-xl);
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    z-index: 10;
}

.slider-prev,
.slider-next {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.5);
    background: transparent;
    color: white;
    cursor: pointer;
    transition: all var(--transition);
}

.slider-prev:hover,
.slider-next:hover {
    background: var(--color-white);
    color: var(--color-primary);
}

.slider-dots {
    display: flex;
    gap: var(--spacing-sm);
}

.slider-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    background: transparent;
    cursor: pointer;
    transition: all var(--transition);
}

.slider-dot.active {
    background: white;
}

@media (max-width: 768px) {
    .hero-slide .container,
    .hero-single .container {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .hero-content h1 {
        font-size: var(--font-size-3xl);
    }

    .hero-actions {
        justify-content: center;
    }

    .hero-image {
        order: -1;
        max-width: 80%;
        margin: 0 auto;
    }
}
</style>

<script>
(function() {
    // Parallax effect using CSS transform for better performance
    document.querySelectorAll('.block-hero[data-parallax="true"]').forEach(function(hero) {
        const slides = hero.querySelectorAll('.hero-slide, .hero-single');

        function updateParallax() {
            const rect = hero.getBoundingClientRect();
            const windowHeight = window.innerHeight;

            // Only apply parallax when hero is visible
            if (rect.bottom > 0 && rect.top < windowHeight) {
                // Calculate how far through the viewport the element is
                const scrollProgress = (windowHeight - rect.top) / (windowHeight + rect.height);
                // Translate the background (pseudo-element) based on scroll
                // Range: -25% to +25% of the element height for more noticeable effect
                const translateY = (scrollProgress - 0.5) * 50;

                slides.forEach(function(slide) {
                    // Apply transform to the ::after pseudo-element via CSS variable
                    slide.style.setProperty('--parallax-translate', translateY + '%');
                });
            }
        }

        // Check for reduced motion preference
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!prefersReducedMotion) {
            window.addEventListener('scroll', updateParallax, { passive: true });
            window.addEventListener('resize', updateParallax, { passive: true });
            updateParallax(); // Initial position
        }
    });

    // Slider functionality
    document.querySelectorAll('.hero-slider').forEach(function(slider) {
        const slides = slider.querySelectorAll('.hero-slide');
        const dots = slider.querySelectorAll('.slider-dot');
        const prevBtn = slider.querySelector('.slider-prev');
        const nextBtn = slider.querySelector('.slider-next');

        if (slides.length <= 1) return;

        let currentIndex = 0;
        let autoplayInterval = null;
        const autoplay = slider.dataset.autoplay !== 'false';
        const interval = parseInt(slider.dataset.interval) || 5000;

        function showSlide(index) {
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;

            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });

            currentIndex = index;
        }

        function nextSlide() {
            showSlide(currentIndex + 1);
        }

        function prevSlide() {
            showSlide(currentIndex - 1);
        }

        function startAutoplay() {
            if (autoplay && !autoplayInterval) {
                autoplayInterval = setInterval(nextSlide, interval);
            }
        }

        function stopAutoplay() {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
                autoplayInterval = null;
            }
        }

        // Event listeners
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                stopAutoplay();
                prevSlide();
                startAutoplay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                stopAutoplay();
                nextSlide();
                startAutoplay();
            });
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                stopAutoplay();
                showSlide(index);
                startAutoplay();
            });
        });

        // Pause on hover
        slider.addEventListener('mouseenter', stopAutoplay);
        slider.addEventListener('mouseleave', startAutoplay);

        // Keyboard navigation
        slider.setAttribute('tabindex', '0');
        slider.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                stopAutoplay();
                prevSlide();
                startAutoplay();
            } else if (e.key === 'ArrowRight') {
                stopAutoplay();
                nextSlide();
                startAutoplay();
            }
        });

        // Start autoplay
        startAutoplay();
    });
})();
</script>
