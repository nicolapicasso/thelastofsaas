<?php
/**
 * Pricing Block Template (Planes y Precios)
 * Displays pricing plans in a grid layout
 * Omniwallet CMS / TLOS
 */

$columns = (int)($settings['columns'] ?? 3);
$cardStyle = $settings['card_style'] ?? 'bordered';
$showPriceSuffix = $settings['show_price_suffix'] ?? true;
$showDescription = $settings['show_description'] ?? true;
$showIcon = $settings['show_icon'] ?? false;
$equalHeight = $settings['equal_height'] ?? true;
$plans = $content['plans'] ?? [];
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

$blockId = 'pricing-' . uniqid();
?>

<section class="block block-pricing section <?= $renderer->getBlockClasses($block, $settings) ?> <?= $textClass ?>"
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

        <?php if (!empty($plans)): ?>
            <div class="pricing-grid columns-<?= $columns ?> <?= $equalHeight ? 'equal-height' : '' ?>"
                 <?= $hasStagger ? 'data-animate-stagger="' . htmlspecialchars($settings['animation']) . '"' : '' ?>>
                <?php foreach ($plans as $plan): ?>
                    <?php
                    $cardClasses = ['pricing-card', 'style-' . $cardStyle];
                    if (!empty($plan['highlighted'])) $cardClasses[] = 'highlighted';
                    if (!empty($plan['is_enterprise'])) $cardClasses[] = 'enterprise';
                    ?>
                    <div class="<?= implode(' ', $cardClasses) ?>"
                         <?= $hasStagger ? 'data-animate="' . htmlspecialchars($settings['animation']) . '"' : '' ?>>

                        <?php if (!empty($plan['highlighted'])): ?>
                            <div class="pricing-badge">Mas popular</div>
                        <?php endif; ?>

                        <div class="pricing-header">
                            <?php if ($showIcon && !empty($plan['icon'])): ?>
                                <div class="pricing-icon">
                                    <i class="<?= htmlspecialchars($plan['icon']) ?>"></i>
                                </div>
                            <?php endif; ?>

                            <h3 class="pricing-name"><?= htmlspecialchars($plan['name'] ?? '') ?></h3>

                            <?php if ($showDescription && !empty($plan['description'])): ?>
                                <p class="pricing-description"><?= htmlspecialchars($plan['description']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="pricing-price">
                            <span class="price-amount"><?= htmlspecialchars($plan['price'] ?? '0') ?></span>
                            <?php if ($showPriceSuffix && !empty($plan['price_suffix'])): ?>
                                <span class="price-suffix"><?= htmlspecialchars($plan['price_suffix']) ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($plan['features'])): ?>
                            <ul class="pricing-features">
                                <?php foreach ($plan['features'] as $feature): ?>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        <span><?= htmlspecialchars($feature) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($plan['cta_text']) && !empty($plan['cta_url'])): ?>
                            <div class="pricing-footer">
                                <a href="<?= htmlspecialchars($plan['cta_url']) ?>" class="btn btn-pricing">
                                    <?= htmlspecialchars($plan['cta_text']) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No hay planes configurados</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.block-pricing {
    padding: var(--spacing-3xl) 0;
}

/* Custom background support */
.block-pricing.bg-custom {
    background-color: var(--block-bg-color, #ffffff);
}

/* Text light mode for dark backgrounds */
.block-pricing.text-light .section-header h2,
.block-pricing.text-light .section-header p {
    color: white;
}

.block-pricing .section-header {
    text-align: center;
    max-width: 900px;
    margin: 0 auto var(--spacing-2xl);
}

.block-pricing .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
    color: var(--block-title-color, inherit);
}

.block-pricing .section-header p {
    color: var(--block-subtitle-color, var(--color-gray-600));
    font-size: var(--font-size-lg);
}

/* =========================
   Pricing Grid
   ========================= */
.pricing-grid {
    display: grid;
    gap: var(--spacing-xl);
    align-items: start;
}

.pricing-grid.columns-2 { grid-template-columns: repeat(2, 1fr); }
.pricing-grid.columns-3 { grid-template-columns: repeat(3, 1fr); }
.pricing-grid.columns-4 { grid-template-columns: repeat(4, 1fr); }

.pricing-grid.equal-height {
    align-items: stretch;
}

.pricing-grid.equal-height .pricing-card {
    display: flex;
    flex-direction: column;
}

.pricing-grid.equal-height .pricing-features {
    flex: 1;
}

/* =========================
   Pricing Card
   ========================= */
.pricing-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    position: relative;
    transition: all 0.3s ease;
}

.pricing-card.style-bordered {
    border: 1px solid var(--color-gray-200);
}

.pricing-card.style-shadow {
    box-shadow: var(--shadow-md);
}

.pricing-card.style-minimal {
    background: var(--color-gray-50);
    border: none;
}

/* Highlighted Card */
.pricing-card.highlighted {
    border-color: var(--color-primary);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    transform: scale(1.02);
    z-index: 1;
}

.pricing-card.highlighted.style-bordered {
    border-width: 2px;
}

/* Enterprise Card (Dark) */
.pricing-card.enterprise {
    background: #1a1a1a;
    color: white;
    border-color: #333;
}

.pricing-card.enterprise .pricing-name,
.pricing-card.enterprise .pricing-description,
.pricing-card.enterprise .price-amount,
.pricing-card.enterprise .price-suffix,
.pricing-card.enterprise .pricing-features li {
    color: white;
}

.pricing-card.enterprise .pricing-features li i {
    color: var(--color-success, #10B981);
}

/* Badge */
.pricing-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--color-primary);
    color: white;
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-full);
    font-size: var(--font-size-xs);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}

/* Header */
.pricing-header {
    text-align: left;
    margin-bottom: var(--spacing-lg);
}

.pricing-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary-light, #e8f4f6);
    border-radius: var(--radius-lg);
    font-size: 24px;
    color: var(--color-primary);
    margin-bottom: var(--spacing-md);
}

.pricing-card.enterprise .pricing-icon {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.pricing-name {
    font-size: var(--font-size-xl);
    font-weight: 700;
    color: var(--color-gray-900);
    margin: 0 0 var(--spacing-xs);
}

.pricing-description {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin: 0;
}

/* Price */
.pricing-price {
    margin-bottom: var(--spacing-lg);
    display: flex;
    align-items: baseline;
    gap: var(--spacing-xs);
}

.price-amount {
    font-size: clamp(2rem, 5vw, 3rem);
    font-weight: 800;
    color: var(--color-primary, #1a365d);
    line-height: 1;
}

.price-suffix {
    font-size: var(--font-size-base);
    color: var(--color-gray-500);
    font-weight: 400;
}

/* Features List */
.pricing-features {
    list-style: none;
    padding: 0;
    margin: 0 0 var(--spacing-xl);
}

.pricing-features li {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) 0;
    font-size: var(--font-size-sm);
    color: var(--color-gray-700);
    line-height: 1.5;
}

.pricing-features li i {
    color: var(--color-primary);
    font-size: 14px;
    margin-top: 3px;
    flex-shrink: 0;
}

/* Footer / CTA */
.pricing-footer {
    margin-top: auto;
}

.btn-pricing {
    display: block;
    width: 100%;
    padding: var(--spacing-md) var(--spacing-lg);
    background: #1a1a1a;
    color: white;
    text-align: center;
    font-weight: 600;
    font-size: var(--font-size-sm);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-radius: var(--radius-md);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-pricing:hover {
    background: #333;
    transform: translateY(-2px);
}

.pricing-card.enterprise .btn-pricing {
    background: white;
    color: #1a1a1a;
}

.pricing-card.enterprise .btn-pricing:hover {
    background: var(--color-gray-100);
}

.pricing-card.highlighted .btn-pricing {
    background: var(--color-primary);
}

.pricing-card.highlighted .btn-pricing:hover {
    background: var(--color-primary-dark, #0a2540);
}

/* Hover Effect */
.pricing-card:not(.highlighted):hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

/* =========================
   Empty State
   ========================= */
.block-pricing .empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-500);
}

/* =========================
   Responsive
   ========================= */
@media (max-width: 1024px) {
    .pricing-grid.columns-4 {
        grid-template-columns: repeat(2, 1fr);
    }

    .pricing-grid.columns-3 {
        grid-template-columns: repeat(2, 1fr);
    }

    .pricing-card.highlighted {
        transform: none;
    }
}

@media (max-width: 768px) {
    .pricing-grid.columns-4,
    .pricing-grid.columns-3,
    .pricing-grid.columns-2 {
        grid-template-columns: 1fr;
    }

    .pricing-card {
        padding: var(--spacing-lg);
    }

    .price-amount {
        font-size: 2.5rem;
    }
}
</style>
