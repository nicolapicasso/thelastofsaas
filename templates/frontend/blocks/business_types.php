<?php
/**
 * Business Types Block Template (Características Resumidas)
 * Shows short feature highlights with numbered/lettered lists
 * We're Sinapsis CMS
 */

$columns = (int)($settings['columns'] ?? 4);
$displayMode = $settings['display_mode'] ?? 'cards';
$cardStyle = $settings['card_style'] ?? 'minimal';
$numberStyle = $settings['number_style'] ?? 'large';
$iconStyle = $settings['icon_style'] ?? 'gradient';
$showIcon = $settings['show_icon'] ?? true;
$showDescription = $settings['show_description'] ?? true;
$hoverEffect = $settings['hover_effect'] ?? 'lift';
$items = $content['items'] ?? [];
$animationAttrs = $renderer->getAnimationAttributes($settings);
$hasStagger = !empty($settings['animation_stagger']) && !empty($settings['animation']);

// Color handling
$customStyles = [];
if (!empty($settings['background_color'])) {
    $customStyles[] = "--block-bg-color: {$settings['background_color']}";
}
if (!empty($settings['title_color'])) {
    $customStyles[] = "--block-title-color: {$settings['title_color']}";
}
if (!empty($settings['subtitle_color'])) {
    $customStyles[] = "--block-subtitle-color: {$settings['subtitle_color']}";
}
if (!empty($settings['text_color'])) {
    $customStyles[] = "--block-text-color: {$settings['text_color']}";
}

$inlineStyles = !empty($customStyles) ? implode('; ', $customStyles) . ';' : '';

$blockId = 'business-types-' . uniqid();
?>

<section class="block block-business-types section <?= $renderer->getBlockClasses($block, $settings) ?>"
         id="<?= $blockId ?>"
         style="<?= $renderer->getBlockStyles($settings) ?>; <?= $inlineStyles ?>"
         <?= !$hasStagger ? $animationAttrs : '' ?>>
    <div class="container">
        <?php if (!empty($content['title'])): ?>
            <div class="section-header" <?= $animationAttrs ?>>
                <h2><?= htmlspecialchars($content['title']) ?></h2>
                <?php if (!empty($content['subtitle'])): ?>
                    <p><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($items)): ?>
            <div class="business-types-grid columns-<?= $columns ?> mode-<?= $displayMode ?> style-<?= $cardStyle ?> number-<?= $numberStyle ?>"
                 <?= $hasStagger ? 'data-animate-stagger="' . htmlspecialchars($settings['animation']) . '"' : '' ?>>
                <?php foreach ($items as $index => $item): ?>
                    <?php
                    $number = $index + 1;
                    $letter = chr(65 + $index); // A, B, C, D...
                    ?>
                    <div class="business-type-card hover-<?= $hoverEffect ?>"
                         <?= $hasStagger ? 'data-animate="' . htmlspecialchars($settings['animation']) . '"' : '' ?>>

                        <?php if ($displayMode === 'numbered'): ?>
                            <!-- Numbered Mode -->
                            <div class="item-number"><?= $number ?></div>
                        <?php elseif ($displayMode === 'lettered'): ?>
                            <!-- Lettered Mode -->
                            <div class="item-letter"><?= $letter ?></div>
                        <?php elseif ($displayMode === 'icons_only'): ?>
                            <!-- Icons Only Mode -->
                            <?php if ($showIcon && !empty($item['icon'])): ?>
                                <div class="business-type-icon large icon-style-<?= $iconStyle ?>">
                                    <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Cards Mode (default) -->
                            <?php if ($showIcon && !empty($item['icon'])): ?>
                                <div class="business-type-icon icon-style-<?= $iconStyle ?>">
                                    <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                                </div>
                            <?php elseif (!empty($item['image'])): ?>
                                <div class="business-type-image">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title'] ?? '') ?>">
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="business-type-content">
                            <?php if (!empty($item['title'])): ?>
                                <h3><?= htmlspecialchars($item['title']) ?></h3>
                            <?php endif; ?>

                            <?php if ($showDescription && !empty($item['description'])): ?>
                                <p><?= htmlspecialchars($item['description']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($item['link'])): ?>
                                <a href="<?= htmlspecialchars($item['link']) ?>" class="business-type-link">
                                    Saber más <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No hay características configuradas</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($content['cta_text']) && !empty($content['cta_url'])): ?>
            <div class="section-footer">
                <a href="<?= htmlspecialchars($content['cta_url']) ?>" class="btn btn-primary">
                    <?= htmlspecialchars($content['cta_text']) ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.block-business-types {
    padding: var(--spacing-3xl) 0;
    background-color: var(--block-bg-color, transparent);
}

.block-business-types .section-header {
    text-align: center;
    max-width: 900px;
    margin: 0 auto var(--spacing-2xl);
}

.block-business-types .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
    color: var(--block-title-color, inherit);
}

.block-business-types .section-header p {
    color: var(--block-subtitle-color, var(--color-gray-600));
    font-size: var(--font-size-lg);
}

/* =========================
   Grid Layout
   ========================= */
.business-types-grid {
    display: grid;
    gap: var(--spacing-xl);
}

.business-types-grid.columns-3 { grid-template-columns: repeat(3, 1fr); }
.business-types-grid.columns-4 { grid-template-columns: repeat(4, 1fr); }
.business-types-grid.columns-5 { grid-template-columns: repeat(5, 1fr); }
.business-types-grid.columns-6 { grid-template-columns: repeat(6, 1fr); }

/* =========================
   Card Styles
   ========================= */
.business-type-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    text-align: center;
    transition: all 0.3s ease;
}

/* Style variations */
.business-types-grid.style-bordered .business-type-card {
    border: 1px solid var(--color-gray-200);
}

.business-types-grid.style-shadow .business-type-card {
    box-shadow: var(--shadow-md);
}

.business-types-grid.style-minimal .business-type-card {
    background: transparent;
    padding: var(--spacing-lg);
}

.business-types-grid.style-filled .business-type-card {
    background: var(--color-gray-50);
}

/* Hover Effects */
.business-type-card.hover-lift:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-lg);
}

.business-type-card.hover-glow:hover {
    box-shadow: 0 0 30px rgba(var(--color-primary-rgb, 33, 90, 107), 0.2);
}

.business-type-card.hover-border:hover {
    border-color: var(--color-primary);
}

/* =========================
   Numbered Mode
   ========================= */
.item-number {
    font-size: 4rem;
    font-weight: 800;
    line-height: 1;
    color: var(--color-gray-200);
    margin-bottom: var(--spacing-md);
    transition: color 0.3s ease;
}

.business-type-card:hover .item-number {
    color: var(--color-primary);
}

/* Number style variations */
.business-types-grid.number-small .item-number {
    font-size: 2.5rem;
}

.business-types-grid.number-circle .item-number {
    width: 56px;
    height: 56px;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--color-gray-100);
    color: var(--color-gray-600);
    margin: 0 auto var(--spacing-md);
}

.business-type-card:hover .business-types-grid.number-circle .item-number,
.business-types-grid.number-circle .business-type-card:hover .item-number {
    background: var(--color-primary);
    color: white;
}

/* =========================
   Lettered Mode
   ========================= */
.item-letter {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1;
    color: var(--color-gray-200);
    margin-bottom: var(--spacing-md);
    transition: color 0.3s ease;
}

.business-type-card:hover .item-letter {
    color: var(--color-secondary, #F9AF00);
}

/* =========================
   Icons Only Mode
   ========================= */
.business-types-grid.mode-icons_only .business-type-card {
    padding: var(--spacing-lg);
}

.business-type-icon.large {
    width: 80px;
    height: 80px;
    font-size: 36px;
}

/* =========================
   Default Icon Styles
   ========================= */
.business-type-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto var(--spacing-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-primary-light, #e8f4f6) 0%, var(--color-secondary-light, #fef5e0) 100%);
    border-radius: var(--radius-lg);
    font-size: 26px;
    color: var(--color-primary);
    transition: all 0.3s ease;
}

.business-type-card:hover .business-type-icon {
    transform: scale(1.1);
}

/* Icon Style: Light (black on white) */
.business-type-icon.icon-style-light {
    background: #ffffff;
    color: #1a1a1a;
    border: 1px solid var(--color-gray-200, #e5e7eb);
}

/* Icon Style: Dark (white on black) */
.business-type-icon.icon-style-dark {
    background: #1a1a1a;
    color: #ffffff;
}

.business-type-image {
    width: 72px;
    height: 72px;
    margin: 0 auto var(--spacing-lg);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.business-type-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* =========================
   Content Styles
   ========================= */
.business-type-card h3 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--spacing-sm);
    color: var(--color-gray-900);
}

.business-type-card p {
    color: var(--block-text-color, var(--color-gray-600));
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-md);
    line-height: 1.6;
}

.business-type-link {
    color: var(--color-primary);
    font-weight: 500;
    font-size: var(--font-size-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.business-type-link i {
    transition: transform 0.2s ease;
}

.business-type-link:hover i {
    transform: translateX(4px);
}

/* =========================
   Footer & Empty State
   ========================= */
.block-business-types .section-footer {
    text-align: center;
    margin-top: var(--spacing-2xl);
}

.block-business-types .empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-500);
}

/* =========================
   Responsive
   ========================= */
@media (max-width: 1200px) {
    .business-types-grid.columns-6 {
        grid-template-columns: repeat(4, 1fr);
    }

    .business-types-grid.columns-5 {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 1024px) {
    .business-types-grid.columns-6,
    .business-types-grid.columns-5,
    .business-types-grid.columns-4 {
        grid-template-columns: repeat(3, 1fr);
    }

    .business-types-grid.columns-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .business-types-grid.columns-6,
    .business-types-grid.columns-5,
    .business-types-grid.columns-4,
    .business-types-grid.columns-3 {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-md);
    }

    .business-type-card {
        padding: var(--spacing-lg);
    }

    .item-number {
        font-size: 3rem;
    }

    .item-letter {
        font-size: 2.5rem;
    }

    .business-type-icon {
        width: 52px;
        height: 52px;
        font-size: 22px;
        margin-bottom: var(--spacing-md);
    }

    .business-type-card h3 {
        font-size: var(--font-size-base);
    }

    .business-type-card p {
        font-size: var(--font-size-xs);
    }
}

@media (max-width: 480px) {
    .business-type-card {
        padding: var(--spacing-md);
    }

    .item-number {
        font-size: 2.5rem;
        margin-bottom: var(--spacing-sm);
    }

    .item-letter {
        font-size: 2rem;
        margin-bottom: var(--spacing-sm);
    }

    .business-type-icon {
        width: 44px;
        height: 44px;
        font-size: 18px;
        margin-bottom: var(--spacing-sm);
    }

    .business-type-card h3 {
        font-size: var(--font-size-sm);
        margin-bottom: var(--spacing-xs);
    }

    /* Hide description and link on small mobile */
    .business-type-card p,
    .business-type-link {
        display: none;
    }
}
</style>
