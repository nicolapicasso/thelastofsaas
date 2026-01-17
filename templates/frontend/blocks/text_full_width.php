<?php
/**
 * Text Full Width Block Template
 * Full width text content without image
 * Omniwallet CMS
 */

$alignment = $settings['text_alignment'] ?? 'center';
$maxWidth = $settings['max_width'] ?? '800px';

// Calculate spacing - using !== '' instead of !empty() because '0' is a valid value
$marginTop = $settings['margin_top'] ?? '';
if ($marginTop === 'custom' && isset($settings['margin_top_custom']) && $settings['margin_top_custom'] !== '') {
    $marginTopValue = $settings['margin_top_custom'];
} elseif ($marginTop !== '' && $marginTop !== 'custom') {
    $marginTopValue = $marginTop;
} else {
    $marginTopValue = '';
}

$marginBottom = $settings['margin_bottom'] ?? '';
if ($marginBottom === 'custom' && isset($settings['margin_bottom_custom']) && $settings['margin_bottom_custom'] !== '') {
    $marginBottomValue = $settings['margin_bottom_custom'];
} elseif ($marginBottom !== '' && $marginBottom !== 'custom') {
    $marginBottomValue = $marginBottom;
} else {
    $marginBottomValue = '';
}

$paddingTop = $settings['padding_top'] ?? '';
if ($paddingTop === 'custom' && isset($settings['padding_top_custom']) && $settings['padding_top_custom'] !== '') {
    $paddingTopValue = $settings['padding_top_custom'];
} elseif ($paddingTop !== '' && $paddingTop !== 'custom') {
    $paddingTopValue = $paddingTop;
} else {
    $paddingTopValue = '';
}

$paddingBottom = $settings['padding_bottom'] ?? '';
if ($paddingBottom === 'custom' && isset($settings['padding_bottom_custom']) && $settings['padding_bottom_custom'] !== '') {
    $paddingBottomValue = $settings['padding_bottom_custom'];
} elseif ($paddingBottom !== '' && $paddingBottom !== 'custom') {
    $paddingBottomValue = $paddingBottom;
} else {
    $paddingBottomValue = '';
}

$paddingHorizontal = $settings['padding_horizontal'] ?? '';
if ($paddingHorizontal === 'custom' && isset($settings['padding_horizontal_custom']) && $settings['padding_horizontal_custom'] !== '') {
    $paddingHorizontalValue = $settings['padding_horizontal_custom'];
} elseif ($paddingHorizontal !== '' && $paddingHorizontal !== 'custom') {
    $paddingHorizontalValue = $paddingHorizontal;
} else {
    $paddingHorizontalValue = '';
}

// Build custom styles - using !== '' to allow '0' values
$customStyles = [];
if ($marginTopValue !== '') {
    $customStyles[] = "--block-margin-top: {$marginTopValue}";
}
if ($marginBottomValue !== '') {
    $customStyles[] = "--block-margin-bottom: {$marginBottomValue}";
}
if ($paddingTopValue !== '') {
    $customStyles[] = "--block-padding-top: {$paddingTopValue}";
}
if ($paddingBottomValue !== '') {
    $customStyles[] = "--block-padding-bottom: {$paddingBottomValue}";
}
if ($paddingHorizontalValue !== '') {
    $customStyles[] = "--block-padding-horizontal: {$paddingHorizontalValue}";
}
$customStyles[] = "--block-max-width: {$maxWidth}";

$inlineStyles = !empty($customStyles) ? implode('; ', $customStyles) . ';' : '';
?>

<section class="block block-text-full section <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $renderer->getBlockStyles($settings) ?>; <?= $inlineStyles ?>">
    <div class="container">
        <div class="text-full-content align-<?= $alignment ?>">
            <?php if (!empty($content['badge'])): ?>
                <span class="badge"><?= htmlspecialchars($content['badge']) ?></span>
            <?php endif; ?>

            <?php if (!empty($content['title'])): ?>
                <h2><?= htmlspecialchars($content['title']) ?></h2>
            <?php endif; ?>

            <?php if (!empty($content['subtitle'])): ?>
                <p class="subtitle"><?= htmlspecialchars($content['subtitle']) ?></p>
            <?php endif; ?>

            <?php if (!empty($content['text'])): ?>
                <div class="text-body">
                    <?= $content['text'] ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($content['cta_text']) && !empty($content['cta_url'])): ?>
                <div class="text-actions">
                    <a href="<?= htmlspecialchars($content['cta_url']) ?>" class="btn btn-primary">
                        <?= htmlspecialchars($content['cta_text']) ?>
                    </a>
                    <?php if (!empty($content['cta_secondary_text']) && !empty($content['cta_secondary_url'])): ?>
                        <a href="<?= htmlspecialchars($content['cta_secondary_url']) ?>" class="btn btn-outline">
                            <?= htmlspecialchars($content['cta_secondary_text']) ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.block-text-full {
    padding-top: var(--block-padding-top, var(--spacing-3xl));
    padding-bottom: var(--block-padding-bottom, var(--spacing-3xl));
    padding-left: var(--block-padding-horizontal, 0);
    padding-right: var(--block-padding-horizontal, 0);
    margin-top: var(--block-margin-top, 0);
    margin-bottom: var(--block-margin-bottom, 0);
}

.block-text-full .text-full-content {
    max-width: var(--block-max-width, 800px);
}

.block-text-full .text-full-content.align-center {
    margin: 0 auto;
    text-align: center;
}

.block-text-full .text-full-content.align-left {
    margin: 0;
    text-align: left;
}

.block-text-full .text-full-content.align-right {
    margin-left: auto;
    text-align: right;
}

.block-text-full .badge {
    display: inline-block;
    background-color: var(--color-primary-light);
    color: var(--color-primary-dark);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    font-weight: 600;
    margin-bottom: var(--spacing-md);
}

.block-text-full h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-md);
    color: inherit;
}

.block-text-full .subtitle {
    font-size: var(--font-size-xl);
    opacity: 0.8;
    margin-bottom: var(--spacing-lg);
    color: inherit;
}

.block-text-full .text-body {
    line-height: 1.8;
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-xl);
    color: inherit;
}

.block-text-full .text-body p {
    margin-bottom: var(--spacing-md);
    color: inherit;
}

.block-text-full .text-actions {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.block-text-full .text-full-content.align-center .text-actions {
    justify-content: center;
}

.block-text-full .text-full-content.align-right .text-actions {
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .block-text-full h2 {
        font-size: var(--font-size-2xl);
    }

    .block-text-full .text-body {
        font-size: var(--font-size-base);
    }
}
</style>
