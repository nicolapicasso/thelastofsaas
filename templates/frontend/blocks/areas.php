<?php
/**
 * Areas Block Template
 * Interactive columns with multiple hover effects
 * We're Sinapsis CMS
 */

$items = $content['items'] ?? [];
$effect = $settings['effect'] ?? 'reveal-up';
$columns = (int)($settings['columns'] ?? 4);
$height = $settings['height'] ?? '500px';
$gap = $settings['gap'] ?? '0';
$textColor = $settings['text_color'] ?? '#ffffff';
$textPosition = $settings['text_position'] ?? 'center';
$showSubtitle = $settings['show_subtitle'] ?? true;
$showDescription = $settings['show_description'] ?? false;
$overlayOpacity = $settings['overlay_opacity'] ?? 0;
$borderRadius = $settings['border_radius'] ?? '0';
$fullWidth = $settings['full_width'] ?? true;

// Generate unique ID for this block
$blockId = 'areas-' . uniqid();
?>

<section class="block block-areas effect-<?= htmlspecialchars($effect) ?>" id="<?= $blockId ?>">
    <?php if (!empty($content['title']) || !empty($content['subtitle'])): ?>
        <div class="container">
            <div class="section-header">
                <?php if (!empty($content['title'])): ?>
                    <h2><?= htmlspecialchars($content['title']) ?></h2>
                <?php endif; ?>
                <?php if (!empty($content['subtitle'])): ?>
                    <p><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="areas-wrapper <?= $fullWidth ? 'full-width' : 'container' ?>">
        <div class="areas-grid columns-<?= $columns ?>">
            <?php foreach ($items as $index => $item): ?>
                <?php
                $bgColor = $item['background_color'] ?? '#1A1A1A';
                $image = $item['image'] ?? '';
                $url = trim($item['url'] ?? '');
                $title = $item['title'] ?? '';
                $subtitle = $item['subtitle'] ?? '';
                $description = $item['description'] ?? '';
                $hasLink = !empty($url) && $url !== '#';

                // Determine text color based on background brightness
                $itemTextColor = $item['text_color'] ?? $textColor;

                // Use <a> if has link, otherwise <div>
                $tag = $hasLink ? 'a' : 'div';
                $linkAttr = $hasLink ? 'href="' . htmlspecialchars($url) . '"' : '';
                ?>
                <<?= $tag ?> <?= $linkAttr ?> class="area-item text-<?= $textPosition ?> <?= $hasLink ? 'has-link' : 'no-link' ?>" data-index="<?= $index ?>">
                    <!-- Background Image Layer -->
                    <?php if (!empty($image)): ?>
                        <div class="area-bg-image" style="background-image: url('<?= htmlspecialchars($image) ?>');"></div>
                    <?php endif; ?>

                    <!-- Color Overlay Layer -->
                    <div class="area-color-layer" style="background-color: <?= htmlspecialchars($bgColor) ?>;"></div>

                    <!-- Dark Overlay (optional) -->
                    <?php if ($overlayOpacity > 0): ?>
                        <div class="area-overlay" style="opacity: <?= $overlayOpacity ?>;"></div>
                    <?php endif; ?>

                    <!-- Content -->
                    <div class="area-content" style="color: <?= htmlspecialchars($itemTextColor) ?>;">
                        <h3 class="area-title"><?= htmlspecialchars($title) ?></h3>
                        <?php if ($showSubtitle && !empty($subtitle)): ?>
                            <p class="area-subtitle"><?= htmlspecialchars($subtitle) ?></p>
                        <?php endif; ?>
                        <?php if ($showDescription && !empty($description)): ?>
                            <p class="area-description"><?= htmlspecialchars($description) ?></p>
                        <?php endif; ?>
                        <?php if ($hasLink): ?>
                            <span class="area-arrow"><i class="fas fa-arrow-right"></i></span>
                        <?php endif; ?>
                    </div>
                </<?= $tag ?>>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
/* ========================================
   Areas Block Base Styles
   ======================================== */
#<?= $blockId ?> {
    --areas-height: <?= htmlspecialchars($height) ?>;
    --areas-gap: <?= htmlspecialchars($gap) ?>;
    --areas-radius: <?= htmlspecialchars($borderRadius) ?>;
    --areas-text-color: <?= htmlspecialchars($textColor) ?>;
}

#<?= $blockId ?> .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
    padding: 0 var(--spacing-md);
}

#<?= $blockId ?> .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
}

#<?= $blockId ?> .section-header p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
}

#<?= $blockId ?> .areas-wrapper.full-width {
    width: 100%;
    max-width: none;
}

#<?= $blockId ?> .areas-grid {
    display: grid;
    gap: var(--areas-gap);
    width: 100%;
}

#<?= $blockId ?> .areas-grid.columns-2 { grid-template-columns: repeat(2, 1fr); }
#<?= $blockId ?> .areas-grid.columns-3 { grid-template-columns: repeat(3, 1fr); }
#<?= $blockId ?> .areas-grid.columns-4 { grid-template-columns: repeat(4, 1fr); }
#<?= $blockId ?> .areas-grid.columns-5 { grid-template-columns: repeat(5, 1fr); }
#<?= $blockId ?> .areas-grid.columns-6 { grid-template-columns: repeat(6, 1fr); }

#<?= $blockId ?> .area-item {
    position: relative;
    height: var(--areas-height);
    overflow: hidden;
    display: flex;
    text-decoration: none;
    color: inherit;
    border-radius: var(--areas-radius);
}

#<?= $blockId ?> .area-item.text-top { align-items: flex-start; padding-top: var(--spacing-2xl); }
#<?= $blockId ?> .area-item.text-top .area-content { text-align: left; }
#<?= $blockId ?> .area-item.text-center { align-items: center; }
#<?= $blockId ?> .area-item.text-center .area-content { text-align: center; }
#<?= $blockId ?> .area-item.text-bottom { align-items: flex-end; padding-bottom: var(--spacing-2xl); }
#<?= $blockId ?> .area-item.text-bottom .area-content { text-align: left; }

#<?= $blockId ?> .area-bg-image {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    z-index: 1;
}

#<?= $blockId ?> .area-color-layer {
    position: absolute;
    inset: 0;
    z-index: 2;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

#<?= $blockId ?> .area-overlay {
    position: absolute;
    inset: 0;
    background: #000;
    z-index: 3;
    pointer-events: none;
}

#<?= $blockId ?> .area-content {
    position: relative;
    z-index: 10;
    padding: var(--spacing-xl);
    width: 100%;
    transition: transform 0.3s ease;
}

#<?= $blockId ?> .area-title {
    font-size: clamp(1.25rem, 2vw, 1.75rem);
    font-weight: 700;
    margin-bottom: var(--spacing-sm);
    color: inherit;
}

#<?= $blockId ?> .area-subtitle {
    font-size: var(--font-size-base);
    opacity: 0.9;
    margin-bottom: var(--spacing-sm);
}

#<?= $blockId ?> .area-description {
    font-size: var(--font-size-sm);
    opacity: 0.85;
    line-height: 1.6;
    margin-top: var(--spacing-xs);
}

/* Center description when text is centered */
#<?= $blockId ?> .area-item.text-center .area-description {
    margin-left: auto;
    margin-right: auto;
}

/* Items without links keep hover effects but no pointer cursor */
#<?= $blockId ?> .area-item.no-link {
    cursor: default;
}

#<?= $blockId ?> .area-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 2px solid currentColor;
    border-radius: 50%;
    margin-top: var(--spacing-md);
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
}

#<?= $blockId ?> .area-item:hover .area-arrow {
    opacity: 1;
    transform: translateX(0);
}

/* ========================================
   Effect: Reveal (Up/Down/Left/Right)
   ======================================== */
#<?= $blockId ?>.effect-reveal-up .area-item:hover .area-color-layer {
    transform: translateY(-100%);
}

#<?= $blockId ?>.effect-reveal-down .area-item:hover .area-color-layer {
    transform: translateY(100%);
}

#<?= $blockId ?>.effect-reveal-left .area-item:hover .area-color-layer {
    transform: translateX(-100%);
}

#<?= $blockId ?>.effect-reveal-right .area-item:hover .area-color-layer {
    transform: translateX(100%);
}

/* ========================================
   Effect: Zoom
   ======================================== */
#<?= $blockId ?>.effect-zoom .area-bg-image {
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

#<?= $blockId ?>.effect-zoom .area-color-layer {
    opacity: 1;
    transition: opacity 0.4s ease;
}

#<?= $blockId ?>.effect-zoom .area-item:hover .area-bg-image {
    transform: scale(1.15);
}

#<?= $blockId ?>.effect-zoom .area-item:hover .area-color-layer {
    opacity: 0;
}

/* ========================================
   Effect: Flip
   ======================================== */
#<?= $blockId ?>.effect-flip .area-item {
    perspective: 1000px;
}

#<?= $blockId ?>.effect-flip .area-color-layer,
#<?= $blockId ?>.effect-flip .area-bg-image {
    backface-visibility: hidden;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

#<?= $blockId ?>.effect-flip .area-bg-image {
    transform: rotateY(180deg);
}

#<?= $blockId ?>.effect-flip .area-item:hover .area-color-layer {
    transform: rotateY(-180deg);
}

#<?= $blockId ?>.effect-flip .area-item:hover .area-bg-image {
    transform: rotateY(0deg);
}

/* ========================================
   Effect: Tilt (3D)
   ======================================== */
#<?= $blockId ?>.effect-tilt .area-item {
    transform-style: preserve-3d;
    transition: transform 0.3s ease;
}

#<?= $blockId ?>.effect-tilt .area-content {
    transform: translateZ(30px);
}

#<?= $blockId ?>.effect-tilt .area-color-layer {
    transition: opacity 0.4s ease;
}

#<?= $blockId ?>.effect-tilt .area-item:hover {
    transform: rotateX(5deg) rotateY(5deg) scale(1.02);
}

#<?= $blockId ?>.effect-tilt .area-item:hover .area-color-layer {
    opacity: 0.3;
}

/* ========================================
   Effect: Gradient Shift
   ======================================== */
#<?= $blockId ?>.effect-gradient .area-color-layer {
    background: linear-gradient(135deg, var(--bg-color, #1A1A1A) 0%, transparent 100%) !important;
    opacity: 1;
    transition: opacity 0.4s ease, background 0.4s ease;
}

#<?= $blockId ?>.effect-gradient .area-item:hover .area-color-layer {
    opacity: 0.2;
    background: linear-gradient(45deg, var(--bg-color, #1A1A1A) 0%, transparent 100%) !important;
}

/* ========================================
   Effect: Split (Diagonal)
   ======================================== */
#<?= $blockId ?>.effect-split .area-color-layer {
    clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
    transition: clip-path 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

#<?= $blockId ?>.effect-split .area-item:hover .area-color-layer {
    clip-path: polygon(0 0, 0 0, 0 100%, 0 100%);
}

/* ========================================
   Effect: Curtain (Two Panels)
   ======================================== */
#<?= $blockId ?>.effect-curtain .area-color-layer {
    clip-path: inset(0 0 0 0);
    transition: clip-path 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

#<?= $blockId ?>.effect-curtain .area-item:hover .area-color-layer {
    clip-path: inset(0 50% 0 50%);
}

/* ========================================
   Effect: Circle Expand
   ======================================== */
#<?= $blockId ?>.effect-circle .area-color-layer {
    clip-path: circle(100% at 50% 50%);
    transition: clip-path 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

#<?= $blockId ?>.effect-circle .area-item:hover .area-color-layer {
    clip-path: circle(0% at 50% 50%);
}

/* ========================================
   Responsive
   ======================================== */
@media (max-width: 1024px) {
    #<?= $blockId ?> .areas-grid.columns-5,
    #<?= $blockId ?> .areas-grid.columns-6 {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    #<?= $blockId ?> {
        --areas-height: 350px;
    }

    #<?= $blockId ?> .areas-grid.columns-3,
    #<?= $blockId ?> .areas-grid.columns-4,
    #<?= $blockId ?> .areas-grid.columns-5,
    #<?= $blockId ?> .areas-grid.columns-6 {
        grid-template-columns: repeat(2, 1fr);
    }

    #<?= $blockId ?> .area-content {
        padding: var(--spacing-md);
    }

    #<?= $blockId ?> .area-title {
        font-size: 1.125rem;
    }
}

@media (max-width: 480px) {
    #<?= $blockId ?> {
        --areas-height: 280px;
    }

    #<?= $blockId ?> .areas-grid {
        grid-template-columns: 1fr;
    }
}
</style>
