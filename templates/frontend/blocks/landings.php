<?php
/**
 * Landings Block Template
 * Displays landing pages in grid format
 * Omniwallet CMS
 */

use App\Models\Landing;
use App\Models\LandingTheme;
use App\Helpers\TranslationHelper;

$landingModel = new Landing();
$themeModel = new LandingTheme();
$translator = TranslationHelper::getInstance();

$selectionMode = $settings['selection_mode'] ?? 'manual';
$limit = (int)($settings['limit'] ?? 6);
$columns = (int)($settings['columns'] ?? 3);
$displayMode = $settings['display_mode'] ?? 'extended';
$openNewWindow = $settings['open_new_window'] ?? false;
$animation = $settings['animation'] ?? '';
$animationAttrs = $renderer->getAnimationAttributes($settings);
$hasStagger = !empty($settings['animation_stagger']) && !empty($animation);

$items = [];
$isThemesMode = false;

switch ($selectionMode) {
    case 'manual':
        // Get specific landings by IDs
        $selectedIds = $settings['selected_landings'] ?? [];
        if (is_string($selectedIds)) {
            $selectedIds = json_decode($selectedIds, true) ?? [];
        }
        if (!empty($selectedIds)) {
            $items = $landingModel->getByIds($selectedIds);
        }
        break;

    case 'theme':
        // Get landings from a specific theme
        $themeId = (int)($settings['selected_theme'] ?? 0);
        if ($themeId > 0) {
            $items = $landingModel->getByTheme($themeId, $limit);
        }
        break;

    case 'themes':
        // Get themes as categories
        $isThemesMode = true;
        $selectedThemeIds = $settings['selected_themes'] ?? [];
        if (is_string($selectedThemeIds)) {
            $selectedThemeIds = json_decode($selectedThemeIds, true) ?? [];
        }
        if (!empty($selectedThemeIds)) {
            $items = $themeModel->getByIds($selectedThemeIds);
        } else {
            $items = $themeModel->getActive($limit);
        }
        break;
}

// Translate items
if ($isThemesMode) {
    $translator->translateEntities('landing_theme', $items);
} else {
    $translator->translateEntities('landing', $items);
}

$targetAttr = $openNewWindow ? 'target="_blank" rel="noopener"' : '';
?>

<section class="block block-landings section <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $renderer->getBlockStyles($settings) ?>" <?= !$hasStagger ? $animationAttrs : '' ?>>
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
            <div class="landings-grid columns-<?= $columns ?> mode-<?= $displayMode ?>" <?= $hasStagger ? 'data-animate-stagger="' . htmlspecialchars($animation) . '"' : '' ?>>
                <?php if ($isThemesMode): ?>
                    <!-- Themes as categories -->
                    <?php foreach ($items as $index => $theme): ?>
                        <a href="<?= _url('/lp/' . htmlspecialchars($theme['slug'])) ?>"
                           class="landing-card theme-card"
                           <?= $targetAttr ?>
                           <?= $hasStagger ? 'data-animate="' . htmlspecialchars($animation) . '"' : '' ?>>
                            <?php if (!empty($theme['image'])): ?>
                                <div class="landing-image">
                                    <img src="<?= htmlspecialchars($theme['image']) ?>"
                                         alt="<?= htmlspecialchars($theme['title']) ?>"
                                         loading="lazy">
                                </div>
                            <?php else: ?>
                                <div class="landing-image landing-image-placeholder">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                            <?php endif; ?>
                            <div class="landing-content">
                                <h3><?= htmlspecialchars($theme['title']) ?></h3>
                                <?php if ($displayMode === 'extended' && $theme['subtitle']): ?>
                                    <p><?= htmlspecialchars($theme['subtitle']) ?></p>
                                <?php endif; ?>
                                <span class="landing-cta">
                                    <?= __('view_solutions') ?> <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Individual landings -->
                    <?php foreach ($items as $index => $landing):
                        $themeSlug = $landing['theme_slug'] ?? '';
                        $landingSlug = $landing['slug'] ?? '';
                        $url = "/lp/{$themeSlug}/{$landingSlug}";
                    ?>
                        <a href="<?= htmlspecialchars($url) ?>"
                           class="landing-card"
                           <?= $targetAttr ?>
                           <?= $hasStagger ? 'data-animate="' . htmlspecialchars($animation) . '"' : '' ?>>
                            <?php if (!empty($landing['image'])): ?>
                                <div class="landing-image">
                                    <img src="<?= htmlspecialchars($landing['image']) ?>"
                                         alt="<?= htmlspecialchars($landing['title']) ?>"
                                         loading="lazy">
                                </div>
                            <?php else: ?>
                                <div class="landing-image landing-image-placeholder">
                                    <i class="fas fa-rocket"></i>
                                </div>
                            <?php endif; ?>
                            <div class="landing-content">
                                <h3><?= htmlspecialchars($landing['title']) ?></h3>
                                <?php if ($landing['theme_title'] ?? null): ?>
                                    <span class="landing-theme"><?= htmlspecialchars($landing['theme_title']) ?></span>
                                <?php endif; ?>
                                <?php if ($displayMode === 'extended' && ($landing['meta_description'] ?? $landing['subtitle'] ?? '')): ?>
                                    <p><?= htmlspecialchars(mb_substr($landing['meta_description'] ?? $landing['subtitle'] ?? '', 0, 120)) ?>...</p>
                                <?php endif; ?>
                                <span class="landing-cta">
                                    <?= __('view_more') ?> <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($content['show_more']) && !empty($content['more_url'])): ?>
                <div class="section-footer" <?= $animationAttrs ?>>
                    <a href="<?= htmlspecialchars($content['more_url']) ?>" class="btn btn-outline" <?= $targetAttr ?>>
                        <?= htmlspecialchars($content['more_text'] ?? __('see_all')) ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <p><?= __('no_landings_available') ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.block-landings .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
}

.block-landings .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
}

.block-landings .section-header p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
}

.block-landings .landings-grid {
    display: grid;
    gap: var(--spacing-xl);
}

.block-landings .landings-grid.columns-2 {
    grid-template-columns: repeat(2, 1fr);
}

.block-landings .landings-grid.columns-3 {
    grid-template-columns: repeat(3, 1fr);
}

.block-landings .landings-grid.columns-4 {
    grid-template-columns: repeat(4, 1fr);
}

.block-landings .landing-card {
    display: flex;
    flex-direction: column;
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
    text-decoration: none;
    color: inherit;
}

.block-landings .landing-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-6px);
}

.block-landings .landing-image {
    aspect-ratio: 16/10;
    overflow: hidden;
    background-color: var(--color-gray-100);
}

.block-landings .landing-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-slow);
}

.block-landings .landing-card:hover .landing-image img {
    transform: scale(1.05);
}

.block-landings .landing-image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-primary-light), var(--color-primary));
    color: white;
    font-size: 48px;
}

.block-landings .landing-content {
    padding: var(--spacing-lg);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
    flex: 1;
}

.block-landings .landing-theme {
    font-size: var(--font-size-sm);
    color: var(--color-primary);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.block-landings .landing-content h3 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--color-dark);
    line-height: 1.4;
    margin: 0;
}

.block-landings .landing-content p {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    line-height: 1.6;
    margin: 0;
    flex: 1;
}

.block-landings .landing-cta {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--color-primary);
    margin-top: var(--spacing-sm);
    transition: gap var(--transition);
}

.block-landings .landing-card:hover .landing-cta {
    gap: var(--spacing-sm);
}

.block-landings .landing-cta i {
    font-size: 12px;
}

/* Simple mode adjustments */
.block-landings .landings-grid.mode-simple .landing-content {
    padding: var(--spacing-md);
}

.block-landings .landings-grid.mode-simple .landing-content h3 {
    font-size: var(--font-size-base);
}

.block-landings .landings-grid.mode-simple .landing-cta {
    display: none;
}

/* Theme card specific styles */
.block-landings .theme-card .landing-image-placeholder {
    background: linear-gradient(135deg, var(--color-primary-dark), var(--color-primary));
}

.block-landings .section-footer {
    text-align: center;
    margin-top: var(--spacing-2xl);
}

.block-landings .section-footer .btn i {
    margin-left: var(--spacing-sm);
}

.block-landings .empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-500);
}

/* Responsive */
@media (max-width: 1024px) {
    .block-landings .landings-grid.columns-4,
    .block-landings .landings-grid.columns-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .block-landings .landings-grid.columns-4,
    .block-landings .landings-grid.columns-3,
    .block-landings .landings-grid.columns-2 {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-md);
    }

    .block-landings .landing-content {
        padding: var(--spacing-md);
    }

    .block-landings .landing-content h3 {
        font-size: var(--font-size-sm);
    }

    /* Ocultar descripción, tema y CTA en mobile */
    .block-landings .landing-content p,
    .block-landings .landing-theme,
    .block-landings .landing-cta {
        display: none;
    }

    .block-landings .landing-image {
        aspect-ratio: 4/3;
    }

    .block-landings .landing-image-placeholder {
        font-size: 28px;
    }
}
</style>
