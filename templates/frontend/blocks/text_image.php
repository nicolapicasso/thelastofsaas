<?php
/**
 * Text with Image Block Template
 * Omniwallet CMS
 */

$layout = $settings['layout'] ?? 'image-right';
$isImageLeft = $layout === 'image-left';
?>

<section class="block block-text-image section <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $renderer->getBlockStyles($settings) ?>">
    <div class="container">
        <div class="text-image-grid <?= $isImageLeft ? 'image-left' : 'image-right' ?>">
            <!-- Text Content -->
            <div class="text-content">
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

                <?php if (!empty($content['features'])): ?>
                    <ul class="feature-list">
                        <?php foreach ($content['features'] as $feature): ?>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span><?= htmlspecialchars($feature) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
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

            <!-- Image Content -->
            <div class="image-content">
                <?php if (!empty($content['image'])): ?>
                    <div class="image-wrapper">
                        <img src="<?= htmlspecialchars($content['image']) ?>"
                             alt="<?= htmlspecialchars($content['image_alt'] ?? $content['title'] ?? '') ?>"
                             loading="lazy">
                    </div>
                <?php endif; ?>

                <?php if (!empty($content['video_url'])): ?>
                    <div class="video-wrapper">
                        <a href="<?= htmlspecialchars($content['video_url']) ?>" class="play-button" data-video>
                            <i class="fas fa-play"></i>
                        </a>
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

.block-text-image .text-image-grid.image-left .text-content {
    order: 2;
}

.block-text-image .text-image-grid.image-left .image-content {
    order: 1;
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
}

.block-text-image .subtitle {
    font-size: var(--font-size-lg);
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-lg);
}

.block-text-image .text-body {
    color: var(--color-gray-700);
    margin-bottom: var(--spacing-lg);
}

.block-text-image .text-body p {
    margin-bottom: var(--spacing-md);
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

.block-text-image .image-wrapper {
    position: relative;
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.block-text-image .image-wrapper img {
    width: 100%;
    height: auto;
    display: block;
}

.block-text-image .video-wrapper {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.block-text-image .play-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background-color: var(--color-white);
    border-radius: 50%;
    color: var(--color-primary);
    font-size: 24px;
    box-shadow: var(--shadow-lg);
    transition: all var(--transition);
}

.block-text-image .play-button:hover {
    transform: scale(1.1);
    background-color: var(--color-primary);
    color: var(--color-white);
}

.block-text-image .image-content {
    position: relative;
}

@media (max-width: 768px) {
    .block-text-image .text-image-grid {
        grid-template-columns: 1fr;
    }

    .block-text-image .text-image-grid.image-left .text-content,
    .block-text-image .text-image-grid.image-left .image-content {
        order: unset;
    }

    .block-text-image h2 {
        font-size: var(--font-size-2xl);
    }
}
</style>
