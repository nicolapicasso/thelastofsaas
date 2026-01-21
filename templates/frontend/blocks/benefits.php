<?php
/**
 * Benefits Block Template (Características Detalladas)
 * Displays detailed features/characteristics with multiple display modes
 * Items are defined in-place in the block content
 * We're Sinapsis CMS
 */

$columns = (int)($settings['columns'] ?? 2);
$displayMode = $settings['display_mode'] ?? 'cards';
$cardStyle = $settings['card_style'] ?? 'bordered';
$hoverEffect = $settings['hover_effect'] ?? 'lift';
$iconPosition = $settings['icon_position'] ?? 'top';
$iconStyle = $settings['icon_style'] ?? 'gradient';
$showIcon = $settings['show_icon'] ?? true;
$showDescription = $settings['show_description'] ?? true;
$items = $content['items'] ?? [];
$animationAttrs = $renderer->getAnimationAttributes($settings);
$hasStagger = !empty($settings['animation_stagger']) && !empty($settings['animation']);

// Background color handling
$background = $settings['background'] ?? '';
$textColorMode = $settings['text_color_mode'] ?? 'auto';

$customStyles = [];
if ($background === 'custom' && !empty($settings['background_color'])) {
    $customStyles[] = "--block-bg-color: {$settings['background_color']}";
}
if (!empty($settings['title_color'])) {
    $customStyles[] = "--block-title-color: {$settings['title_color']}";
}
if (!empty($settings['subtitle_color'])) {
    $customStyles[] = "--block-subtitle-color: {$settings['subtitle_color']}";
}

// Text color classes
$textClass = '';
if ($textColorMode === 'light' || ($textColorMode === 'auto' && in_array($background, ['dark', 'primary']))) {
    $textClass = 'text-light';
}

$inlineStyles = !empty($customStyles) ? implode('; ', $customStyles) . ';' : '';

$blockId = 'benefits-' . uniqid();
?>

<section class="block block-benefits section <?= $renderer->getBlockClasses($block, $settings) ?> <?= $textClass ?>"
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
            <?php if ($displayMode === 'accordion'): ?>
                <!-- Accordion Mode -->
                <div class="benefits-accordion" <?= $hasStagger ? 'data-animate-stagger="' . htmlspecialchars($settings['animation']) . '"' : '' ?>>
                    <?php foreach ($items as $index => $item): ?>
                        <div class="accordion-item <?= $index === 0 ? 'active' : '' ?>"
                             <?= $hasStagger ? 'data-animate="' . htmlspecialchars($settings['animation']) . '"' : '' ?>>
                            <button class="accordion-header" type="button">
                                <?php if ($showIcon && !empty($item['icon'])): ?>
                                    <span class="accordion-icon icon-style-<?= $iconStyle ?>"><i class="<?= htmlspecialchars($item['icon']) ?>"></i></span>
                                <?php endif; ?>
                                <span class="accordion-title"><?= htmlspecialchars($item['title'] ?? '') ?></span>
                                <span class="accordion-arrow"><i class="fas fa-chevron-down"></i></span>
                            </button>
                            <div class="accordion-content">
                                <?php if ($showDescription && !empty($item['description'])): ?>
                                    <div class="accordion-text"><?= $item['description'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($displayMode === 'list'): ?>
                <!-- List Mode -->
                <div class="benefits-list columns-<?= $columns ?>" <?= $hasStagger ? 'data-animate-stagger="' . htmlspecialchars($settings['animation']) . '"' : '' ?>>
                    <?php foreach ($items as $item): ?>
                        <div class="benefit-list-item hover-<?= $hoverEffect ?>"
                             <?= $hasStagger ? 'data-animate="' . htmlspecialchars($settings['animation']) . '"' : '' ?>>
                            <?php if ($showIcon && !empty($item['icon'])): ?>
                                <div class="benefit-icon icon-style-<?= $iconStyle ?>">
                                    <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                                </div>
                            <?php endif; ?>
                            <div class="benefit-content">
                                <?php if (!empty($item['title'])): ?>
                                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                                <?php endif; ?>
                                <?php if ($showDescription && !empty($item['description'])): ?>
                                    <p><?= $item['description'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Cards Mode (default) -->
                <div class="benefits-grid columns-<?= $columns ?> icon-<?= $iconPosition ?>" <?= $hasStagger ? 'data-animate-stagger="' . htmlspecialchars($settings['animation']) . '"' : '' ?>>
                    <?php foreach ($items as $item): ?>
                        <div class="benefit-card style-<?= $cardStyle ?> hover-<?= $hoverEffect ?>"
                             <?= $hasStagger ? 'data-animate="' . htmlspecialchars($settings['animation']) . '"' : '' ?>>
                            <?php if ($showIcon && !empty($item['icon'])): ?>
                                <div class="benefit-icon icon-style-<?= $iconStyle ?>">
                                    <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($item['image'])): ?>
                                <div class="benefit-image">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title'] ?? '') ?>">
                                </div>
                            <?php endif; ?>
                            <div class="benefit-content">
                                <?php if (!empty($item['title'])): ?>
                                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                                <?php endif; ?>
                                <?php if ($showDescription && !empty($item['description'])): ?>
                                    <p><?= $item['description'] ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['link'])): ?>
                                    <a href="<?= htmlspecialchars($item['link']) ?>" class="benefit-link">
                                        <?= htmlspecialchars($item['link_text'] ?? 'Saber más') ?> <i class="fas fa-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
.block-benefits {
    padding: var(--spacing-3xl) 0;
}

/* Custom background support */
.block-benefits.bg-custom {
    background-color: var(--block-bg-color, #ffffff);
}

/* Text light mode for dark backgrounds */
.block-benefits.text-light .section-header h2,
.block-benefits.text-light .section-header p,
.block-benefits.text-light .benefit-card h3,
.block-benefits.text-light .benefit-list-item h3 {
    color: white;
}

.block-benefits.text-light .benefit-card p,
.block-benefits.text-light .benefit-list-item p {
    color: rgba(255, 255, 255, 0.8);
}

.block-benefits .section-header {
    text-align: center;
    max-width: 900px;
    margin: 0 auto var(--spacing-2xl);
}

.block-benefits .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
    color: var(--block-title-color, inherit);
}

.block-benefits .section-header p {
    color: var(--block-subtitle-color, var(--color-gray-600));
    font-size: var(--font-size-lg);
}

/* =========================
   Cards Mode
   ========================= */
.benefits-grid {
    display: grid;
    gap: var(--spacing-xl);
}

.benefits-grid.columns-1 { grid-template-columns: 1fr; }
.benefits-grid.columns-2 { grid-template-columns: repeat(2, 1fr); }
.benefits-grid.columns-3 { grid-template-columns: repeat(3, 1fr); }

/* Icon Position */
.benefits-grid.icon-left .benefit-card {
    display: flex;
    gap: var(--spacing-lg);
    text-align: left;
}

.benefits-grid.icon-left .benefit-icon {
    flex-shrink: 0;
}

.benefits-grid.icon-inline .benefit-card {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: var(--spacing-md);
}

.benefits-grid.icon-inline .benefit-icon {
    flex-shrink: 0;
}

.benefits-grid.icon-inline .benefit-content {
    flex: 1;
}

.benefits-grid.icon-inline .benefit-content h3 {
    margin-top: 0;
}

/* Benefit Card */
.benefit-card {
    padding: var(--spacing-xl);
    border-radius: var(--radius-lg);
    transition: all 0.3s ease;
}

.benefit-card.style-bordered {
    background: white;
    border: 1px solid var(--color-gray-200);
}

.benefit-card.style-shadow {
    background: white;
    box-shadow: var(--shadow-md);
}

.benefit-card.style-minimal {
    background: transparent;
    padding: var(--spacing-lg);
}

.benefit-card.style-filled {
    background: var(--color-gray-50);
}

/* Hover Effects */
.benefit-card.hover-lift:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-lg);
}

.benefit-card.hover-glow:hover {
    box-shadow: 0 0 30px rgba(var(--color-primary-rgb, 33, 90, 107), 0.2);
}

.benefit-card.hover-border:hover {
    border-color: var(--color-primary);
}

.benefit-icon {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-primary-light, #e8f4f6) 0%, var(--color-secondary-light, #fef5e0) 100%);
    border-radius: var(--radius-lg);
    font-size: 28px;
    color: var(--color-primary);
    margin-bottom: var(--spacing-md);
}

/* Icon Style: Light (black on white) */
.benefit-icon.icon-style-light {
    background: #ffffff;
    color: #1a1a1a;
    border: 1px solid var(--color-gray-200, #e5e7eb);
}

/* Icon Style: Dark (white on black) */
.benefit-icon.icon-style-dark {
    background: #1a1a1a;
    color: #ffffff;
}

.benefits-grid.icon-left .benefit-icon,
.benefits-grid.icon-inline .benefit-icon {
    margin-bottom: 0;
}

.benefit-image {
    width: 100%;
    height: 160px;
    border-radius: var(--radius-md);
    overflow: hidden;
    margin-bottom: var(--spacing-md);
}

.benefit-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.benefit-card h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-sm);
    color: var(--color-gray-900);
}

.benefit-card p {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    line-height: 1.6;
    margin-bottom: var(--spacing-md);
}

.benefit-link {
    color: var(--color-primary);
    font-weight: 500;
    font-size: var(--font-size-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.benefit-link i {
    transition: transform 0.2s ease;
}

.benefit-link:hover i {
    transform: translateX(4px);
}

/* =========================
   List Mode
   ========================= */
.benefits-list {
    display: grid;
    gap: var(--spacing-lg);
}

.benefits-list.columns-1 { grid-template-columns: 1fr; }
.benefits-list.columns-2 { grid-template-columns: repeat(2, 1fr); }
.benefits-list.columns-3 { grid-template-columns: repeat(3, 1fr); }

.benefit-list-item {
    display: flex;
    gap: var(--spacing-md);
    padding: var(--spacing-lg);
    border-radius: var(--radius-md);
    background: white;
    border: 1px solid var(--color-gray-100);
    transition: all 0.3s ease;
}

.benefit-list-item .benefit-icon {
    width: 48px;
    height: 48px;
    font-size: 20px;
    flex-shrink: 0;
    margin-bottom: 0;
}

.benefit-list-item h3 {
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-xs);
    color: var(--color-gray-900);
}

.benefit-list-item p {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    line-height: 1.5;
    margin: 0;
}

.benefit-list-item.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.benefit-list-item.hover-glow:hover {
    box-shadow: 0 0 20px rgba(var(--color-primary-rgb, 33, 90, 107), 0.15);
}

.benefit-list-item.hover-border:hover {
    border-color: var(--color-primary);
}

/* =========================
   Accordion Mode
   ========================= */
.benefits-accordion {
    max-width: 800px;
    margin: 0 auto;
}

.block-benefits .accordion-item {
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    overflow: hidden;
    background: white;
}

.block-benefits .accordion-header {
    width: 100%;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-lg);
    background: transparent;
    border: none;
    cursor: pointer;
    text-align: left;
    transition: background 0.2s ease;
}

.block-benefits .accordion-header:hover {
    background: var(--color-gray-50);
}

.block-benefits .accordion-icon {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary-light, #e8f4f6);
    border-radius: var(--radius-md);
    color: var(--color-primary);
    font-size: 18px;
    flex-shrink: 0;
}

/* Accordion Icon Style: Light (black on white) */
.block-benefits .accordion-icon.icon-style-light {
    background: #ffffff;
    color: #1a1a1a;
    border: 1px solid var(--color-gray-200, #e5e7eb);
}

/* Accordion Icon Style: Dark (white on black) */
.block-benefits .accordion-icon.icon-style-dark {
    background: #1a1a1a;
    color: #ffffff;
}

.block-benefits .accordion-title {
    flex: 1;
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--color-gray-900);
}

.block-benefits .accordion-arrow {
    color: var(--color-gray-400);
    transition: transform 0.3s ease;
}

.block-benefits .accordion-item.active .accordion-arrow {
    transform: rotate(180deg);
}

.block-benefits .accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
}

.block-benefits .accordion-item.active .accordion-content {
    max-height: 500px;
}

.block-benefits .accordion-text {
    padding: 0 var(--spacing-lg) var(--spacing-lg) calc(44px + var(--spacing-lg) + var(--spacing-md));
    color: var(--color-gray-600);
    line-height: 1.7;
}

/* =========================
   Footer & Empty State
   ========================= */
.block-benefits .section-footer {
    text-align: center;
    margin-top: var(--spacing-2xl);
}

.block-benefits .empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-500);
}

/* =========================
   Responsive
   ========================= */
@media (max-width: 1024px) {
    .benefits-grid.columns-3 {
        grid-template-columns: repeat(2, 1fr);
    }

    .benefits-list.columns-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .benefits-grid.columns-3,
    .benefits-grid.columns-2 {
        grid-template-columns: 1fr;
    }

    .benefits-list.columns-3,
    .benefits-list.columns-2 {
        grid-template-columns: 1fr;
    }

    .benefits-grid.icon-left .benefit-card {
        flex-direction: column;
    }

    .benefit-icon {
        width: 56px;
        height: 56px;
        font-size: 24px;
    }

    .block-benefits .accordion-text {
        padding-left: var(--spacing-lg);
    }
}
</style>

<script>
(function() {
    const block = document.getElementById('<?= $blockId ?>');
    if (!block) return;

    // Accordion functionality
    const accordionItems = block.querySelectorAll('.accordion-item');
    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        if (!header) return;

        header.addEventListener('click', () => {
            // Close other items (optional - remove for multi-open)
            accordionItems.forEach(other => {
                if (other !== item) {
                    other.classList.remove('active');
                }
            });

            // Toggle current item
            item.classList.toggle('active');
        });
    });
})();
</script>
