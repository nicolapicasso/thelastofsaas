<?php
/**
 * Custom HTML Block Template
 * Allows raw HTML/CSS/JS content
 * Omniwallet CMS
 */
?>

<section class="block block-custom-html <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $renderer->getBlockStyles($settings) ?>">
    <?php if (!empty($settings['container'])): ?>
        <div class="container">
    <?php endif; ?>

    <?= $content['html'] ?? '' ?>

    <?php if (!empty($settings['container'])): ?>
        </div>
    <?php endif; ?>
</section>
