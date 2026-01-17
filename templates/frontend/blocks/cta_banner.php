<?php
/**
 * CTA Banner Block Template
 * Omniwallet CMS
 */
?>

<section class="block block-cta section <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $renderer->getBlockStyles($settings) ?>">
    <div class="container">
        <div class="cta-content">
            <?php if (!empty($content['title'])): ?>
                <h2><?= htmlspecialchars($content['title']) ?></h2>
            <?php endif; ?>

            <?php if (!empty($content['subtitle'])): ?>
                <p class="cta-subtitle"><?= htmlspecialchars($content['subtitle']) ?></p>
            <?php endif; ?>

            <?php if (!empty($content['cta_text']) && !empty($content['cta_url'])): ?>
                <div class="cta-actions">
                    <a href="<?= htmlspecialchars($content['cta_url']) ?>"
                       class="btn <?= ($settings['style'] ?? 'primary') === 'primary' ? 'btn-white' : 'btn-primary' ?> btn-lg">
                        <?= htmlspecialchars($content['cta_text']) ?>
                    </a>

                    <?php if (!empty($content['cta_secondary_text']) && !empty($content['cta_secondary_url'])): ?>
                        <a href="<?= htmlspecialchars($content['cta_secondary_url']) ?>"
                           class="btn btn-outline btn-lg"
                           style="<?= ($settings['style'] ?? 'primary') === 'primary' ? 'border-color: white; color: white;' : '' ?>">
                            <?= htmlspecialchars($content['cta_secondary_text']) ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.block-cta {
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    color: var(--color-white);
}

.block-cta .cta-content {
    text-align: center;
    max-width: 700px;
    margin: 0 auto;
}

.block-cta h2 {
    color: var(--color-white);
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-md);
}

.block-cta .cta-subtitle {
    font-size: var(--font-size-lg);
    opacity: 0.9;
    margin-bottom: var(--spacing-xl);
}

.block-cta .cta-actions {
    display: flex;
    justify-content: center;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

/* Variant: Light background */
.block-cta.bg-light {
    background: var(--color-gray-50);
    color: var(--color-gray-700);
}

.block-cta.bg-light h2 {
    color: var(--color-dark);
}

.block-cta.bg-light .cta-subtitle {
    opacity: 1;
    color: var(--color-gray-600);
}
</style>
