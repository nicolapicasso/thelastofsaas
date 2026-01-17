<?php
/**
 * Text with Image Right Block Template
 * Text on the left, image on the right
 * Omniwallet CMS
 */
$animAttrs = $renderer->getAnimationAttributes($settings);

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

// Image display settings
$imageDisplayMode = $settings['image_display_mode'] ?? 'auto';
$imageCustomSize = $settings['image_custom_size'] ?? '';
$imageVerticalAlign = $settings['image_vertical_align'] ?? 'center';
$imageBorderRadius = $settings['image_border_radius'] ?? 'default';
$imageShadow = $settings['image_shadow'] ?? true;

// Build image wrapper classes
$imageWrapperClasses = ['image-wrapper'];
$imageWrapperClasses[] = "display-{$imageDisplayMode}";
$imageWrapperClasses[] = "valign-{$imageVerticalAlign}";
$imageWrapperClasses[] = "radius-{$imageBorderRadius}";
if (!$imageShadow) {
    $imageWrapperClasses[] = 'no-shadow';
}

// Build image inline styles
$imageStyles = [];
if (!empty($imageCustomSize) && in_array($imageDisplayMode, ['fixed_height', 'fixed_width', 'contain', 'cover'])) {
    if ($imageDisplayMode === 'fixed_height') {
        $imageStyles[] = "--image-height: {$imageCustomSize}";
    } elseif ($imageDisplayMode === 'fixed_width') {
        $imageStyles[] = "--image-width: {$imageCustomSize}";
    } else {
        $imageStyles[] = "--image-size: {$imageCustomSize}";
    }
}
$imageInlineStyles = !empty($imageStyles) ? implode('; ', $imageStyles) : '';
?>

<section class="block block-text-image section <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $customStyles ?><?= $renderer->getBlockStyles($settings) ?>">
    <div class="container">
        <div class="text-image-grid image-right">
            <!-- Text Content -->
            <div class="text-content" <?= $animAttrs ?: 'data-animate="fade-in-left"' ?>>
                <?php if (!empty($content['badge'])): ?>
                    <span class="badge"><?= htmlspecialchars($content['badge']) ?></span>
                <?php endif; ?>

                <?php if (!empty($content['title'])): ?>
                    <h2><?= htmlspecialchars($content['title']) ?></h2>
                <?php endif; ?>

                <?php if (!empty($content['subtitle'])): ?>
                    <p class="subtitle"><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>

                <?php
                // Support both 'description' and 'text' field names for compatibility
                $textContent = $content['description'] ?? $content['text'] ?? '';
                if (!empty($textContent)): ?>
                    <div class="text-body">
                        <?= $textContent ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($content['features']) && is_array($content['features'])): ?>
                    <ul class="feature-list">
                        <?php foreach ($content['features'] as $feature): ?>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span><?= htmlspecialchars(is_array($feature) ? ($feature['text'] ?? '') : $feature) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php
                // Support both field naming conventions
                $ctaText = $content['link_text'] ?? $content['cta_text'] ?? '';
                $ctaUrl = $content['link_url'] ?? $content['cta_url'] ?? '';
                if (!empty($ctaText) && !empty($ctaUrl)): ?>
                    <div class="text-actions">
                        <a href="<?= htmlspecialchars($ctaUrl) ?>" class="btn btn-primary">
                            <?= htmlspecialchars($ctaText) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Image Content -->
            <div class="image-content" <?= $animAttrs ?: 'data-animate="fade-in-right"' ?>>
                <?php if (!empty($content['image'])): ?>
                    <div class="<?= implode(' ', $imageWrapperClasses) ?>" <?= $imageInlineStyles ? "style=\"{$imageInlineStyles}\"" : '' ?>>
                        <img src="<?= htmlspecialchars($content['image']) ?>"
                             alt="<?= htmlspecialchars($content['image_alt'] ?? $content['title'] ?? '') ?>"
                             loading="lazy">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.block-text-image .text-image-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-3xl);
    align-items: center;
}

.block-text-image .badge {
    display: inline-block;
    background-color: var(--color-primary-light);
    color: var(--color-primary-dark);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    font-weight: 600;
    margin-bottom: var(--spacing-md);
}

.block-text-image h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-md);
    color: var(--block-title-color, inherit);
}

.block-text-image .subtitle {
    font-size: var(--font-size-lg);
    color: var(--block-subtitle-color, var(--color-gray-600));
    margin-bottom: var(--spacing-lg);
}

.block-text-image .text-body {
    color: var(--block-text-color, var(--color-gray-700));
    margin-bottom: var(--spacing-lg);
    line-height: 1.7;
}

.block-text-image .feature-list {
    list-style: none;
    margin-bottom: var(--spacing-xl);
}

.block-text-image .feature-list li {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-sm);
    color: var(--color-gray-700);
}

.block-text-image .feature-list i {
    color: var(--color-success);
    margin-top: 4px;
}

.block-text-image .text-actions {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

/* Image wrapper base styles */
.block-text-image .image-wrapper {
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.block-text-image .image-wrapper.no-shadow {
    box-shadow: none;
}

/* Image display modes */
.block-text-image .image-wrapper.display-auto img {
    width: 100%;
    height: auto;
    display: block;
}

.block-text-image .image-wrapper.display-original img {
    width: auto;
    height: auto;
    max-width: 100%;
    display: block;
}

.block-text-image .image-wrapper.display-fixed_height img {
    width: auto;
    height: var(--image-height, 400px);
    max-width: 100%;
    display: block;
}

.block-text-image .image-wrapper.display-fixed_width img {
    width: var(--image-width, 100%);
    height: auto;
    max-width: 100%;
    display: block;
}

.block-text-image .image-wrapper.display-contain {
    height: var(--image-size, 400px);
    display: flex;
    align-items: center;
    justify-content: center;
}

.block-text-image .image-wrapper.display-contain img {
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.block-text-image .image-wrapper.display-cover {
    height: var(--image-size, 400px);
}

.block-text-image .image-wrapper.display-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Vertical alignment */
.block-text-image .image-content {
    display: flex;
}

.block-text-image .image-content:has(.valign-top) {
    align-items: flex-start;
}

.block-text-image .image-content:has(.valign-center) {
    align-items: center;
}

.block-text-image .image-content:has(.valign-bottom) {
    align-items: flex-end;
}

/* Border radius options */
.block-text-image .image-wrapper.radius-none {
    border-radius: 0;
}

.block-text-image .image-wrapper.radius-small {
    border-radius: 8px;
}

.block-text-image .image-wrapper.radius-medium {
    border-radius: 16px;
}

.block-text-image .image-wrapper.radius-large {
    border-radius: 24px;
}

.block-text-image .image-wrapper.radius-round {
    border-radius: 50%;
}

.block-text-image .image-wrapper.radius-round img {
    aspect-ratio: 1;
    object-fit: cover;
}

@media (max-width: 768px) {
    .block-text-image .text-image-grid {
        grid-template-columns: 1fr;
    }

    .block-text-image h2 {
        font-size: var(--font-size-2xl);
    }

    /* Reset fixed sizes on mobile */
    .block-text-image .image-wrapper.display-fixed_height img,
    .block-text-image .image-wrapper.display-fixed_width img {
        width: 100%;
        height: auto;
    }

    .block-text-image .image-wrapper.display-contain,
    .block-text-image .image-wrapper.display-cover {
        height: auto;
    }
}
</style>
