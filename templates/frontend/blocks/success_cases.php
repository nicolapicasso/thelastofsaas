<?php
/**
 * Success Cases Block Template
 * Displays success case testimonials/stories
 * Omniwallet CMS
 */

use App\Models\SuccessCase;
use App\Helpers\TranslationHelper;

$caseModel = new SuccessCase();
$translator = TranslationHelper::getInstance();
$limit = $settings['limit'] ?? 3;
$featuredOnly = $settings['featured_only'] ?? false;

// Get cases based on featured_only setting
if ($featuredOnly) {
    $cases = $caseModel->getFeatured($limit);
} else {
    $cases = $caseModel->getActive($limit);
}

// Translate success cases
$translator->translateEntities('success_case', $cases);

$layout = $settings['layout'] ?? 'cards'; // cards, slider, featured
$displayMode = $settings['display_mode'] ?? 'extended'; // extended, compact
$columns = $settings['columns'] ?? 3;

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
?>

<section class="block block-success-cases section <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $customStyles ?><?= $renderer->getBlockStyles($settings) ?>">
    <div class="container">
        <?php if (!empty($content['title'])): ?>
            <div class="section-header">
                <h2><?= htmlspecialchars($content['title']) ?></h2>
                <?php if (!empty($content['subtitle'])): ?>
                    <p><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($cases)): ?>
            <div class="cases-grid layout-<?= $layout ?> mode-<?= $displayMode ?> columns-<?= $columns ?>">
                <?php foreach ($cases as $index => $case): ?>
                    <?php
                    $metrics = json_decode($case['metrics'] ?? '{}', true);
                    $caseUrl = '/casos-de-exito/' . htmlspecialchars($case['slug'] ?? '');
                    $isClickable = ($displayMode === 'compact');
                    $cardTag = $isClickable ? 'a' : 'article';
                    $cardAttrs = $isClickable ? 'href="' . _url($caseUrl) . '"' : '';
                    $caseLogo = $case['logo'] ?? '';
                    $caseIndustry = $case['industry'] ?? '';
                    $caseCompanyName = $case['company_name'] ?? $case['title'] ?? '';
                    ?>
                    <<?= $cardTag ?> <?= $cardAttrs ?> class="case-card <?= $index === 0 && $layout === 'featured' ? 'featured' : '' ?>">
                        <?php if (!empty($caseLogo)): ?>
                            <div class="case-logo">
                                <img src="<?= htmlspecialchars($caseLogo) ?>"
                                     alt="<?= htmlspecialchars($caseCompanyName) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="case-content">
                            <?php if ($displayMode === 'extended'): ?>
                                <?php if (!empty($caseIndustry)): ?>
                                    <span class="case-industry"><?= htmlspecialchars($caseIndustry) ?></span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <h3><?= htmlspecialchars($caseCompanyName) ?></h3>

                            <?php if ($displayMode === 'extended'): ?>
                                <?php if (!empty($case['challenge'])): ?>
                                    <p class="case-challenge"><?= htmlspecialchars(substr(strip_tags($case['challenge']), 0, 150)) ?>...</p>
                                <?php endif; ?>

                                <?php if (!empty($metrics)): ?>
                                    <div class="case-metrics">
                                        <?php foreach (array_slice($metrics, 0, 3) as $metric): ?>
                                            <div class="metric">
                                                <span class="metric-value"><?= htmlspecialchars($metric['value'] ?? '') ?></span>
                                                <span class="metric-label"><?= htmlspecialchars($metric['label'] ?? '') ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (!empty($case['testimonial'])): ?>
                                <blockquote class="case-testimonial">
                                    <p>"<?= htmlspecialchars(substr($case['testimonial'], 0, $displayMode === 'compact' ? 150 : 200)) ?>"</p>
                                    <?php if (!empty($case['contact_name'])): ?>
                                        <footer>
                                            <strong><?= htmlspecialchars($case['contact_name']) ?></strong>
                                            <?php if (!empty($case['contact_role'])): ?>
                                                <span><?= htmlspecialchars($case['contact_role']) ?></span>
                                            <?php endif; ?>
                                        </footer>
                                    <?php endif; ?>
                                </blockquote>
                            <?php endif; ?>

                            <?php if ($displayMode === 'extended'): ?>
                                <a href="<?= _url($caseUrl) ?>" class="case-link">
                                    <?= __('view_full_case') ?> <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </<?= $cardTag ?>>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($content['show_more'])): ?>
                <div class="section-footer">
                    <a href="<?= _url(htmlspecialchars($content['more_url'] ?? '/casos-de-exito')) ?>" class="btn btn-outline">
                        <?= htmlspecialchars($content['more_text'] ?? __('see_all_cases')) ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <p><?= __('no_cases_available') ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.block-success-cases .section-header {
    text-align: center;
    max-width: 700px;
    margin: 0 auto var(--spacing-2xl);
}

.block-success-cases .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
    color: var(--block-title-color, inherit);
}

.block-success-cases .section-header p {
    color: var(--block-subtitle-color, var(--color-gray-600));
    font-size: var(--font-size-lg);
}

.cases-grid {
    display: grid;
    gap: var(--spacing-xl);
}

.cases-grid.columns-2 {
    grid-template-columns: repeat(2, 1fr);
}

.cases-grid.columns-3 {
    grid-template-columns: repeat(3, 1fr);
}

.cases-grid.columns-4 {
    grid-template-columns: repeat(4, 1fr);
}

.cases-grid.layout-featured {
    grid-template-columns: 2fr 1fr;
}

.cases-grid.layout-featured .case-card.featured {
    grid-row: span 2;
}

.case-card {
    background-color: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
    display: flex;
    flex-direction: column;
}

.case-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-4px);
}

.case-logo {
    height: 48px;
    margin-bottom: var(--spacing-lg);
}

.case-logo img {
    height: 100%;
    width: auto;
    object-fit: contain;
}

.case-industry {
    display: inline-block;
    font-size: var(--font-size-xs);
    color: var(--color-primary);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: var(--spacing-sm);
}

.case-content h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-sm);
}

.case-challenge {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-lg);
}

.case-metrics {
    display: flex;
    gap: var(--spacing-lg);
    padding: var(--spacing-md) 0;
    border-top: 1px solid var(--color-gray-100);
    border-bottom: 1px solid var(--color-gray-100);
    margin-bottom: var(--spacing-lg);
}

.metric {
    text-align: center;
    flex: 1;
}

.metric-value {
    display: block;
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--color-primary);
    line-height: 1.2;
}

.metric-label {
    font-size: var(--font-size-xs);
    color: var(--color-gray-500);
}

.case-testimonial {
    background-color: var(--color-gray-50);
    border-radius: var(--radius-lg);
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
    flex: 1;
}

.case-testimonial p {
    font-style: italic;
    color: var(--color-gray-700);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-sm);
}

.case-testimonial footer {
    font-size: var(--font-size-sm);
}

.case-testimonial footer strong {
    display: block;
    color: var(--color-dark);
}

.case-testimonial footer span {
    color: var(--color-gray-500);
}

.case-link {
    color: var(--color-primary);
    font-weight: 600;
    font-size: var(--font-size-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    margin-top: auto;
}

.case-link:hover {
    color: var(--color-primary-dark);
}

.case-link i {
    transition: transform var(--transition);
}

.case-link:hover i {
    transform: translateX(4px);
}

/* Modo compacto */
.cases-grid.mode-compact .case-card {
    padding: var(--spacing-lg);
    text-decoration: none;
    color: inherit;
}

.cases-grid.mode-compact a.case-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-6px);
}

.cases-grid.mode-compact .case-logo {
    height: 36px;
    margin-bottom: var(--spacing-md);
}

.cases-grid.mode-compact .case-content h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-sm);
}

.cases-grid.mode-compact .case-testimonial {
    margin-bottom: 0;
    padding: var(--spacing-sm);
}

.cases-grid.mode-compact .case-testimonial p {
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-xs);
}

.cases-grid.mode-compact .case-testimonial footer {
    font-size: var(--font-size-xs);
}

.block-success-cases .section-footer {
    text-align: center;
    margin-top: var(--spacing-2xl);
}

.block-success-cases .section-footer .btn i {
    margin-left: var(--spacing-sm);
}

.block-success-cases .empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-500);
}

@media (max-width: 1024px) {
    .cases-grid.columns-4 {
        grid-template-columns: repeat(3, 1fr);
    }

    .cases-grid.columns-3 {
        grid-template-columns: repeat(2, 1fr);
    }

    .cases-grid.layout-featured {
        grid-template-columns: 1fr;
    }

    .cases-grid.layout-featured .case-card.featured {
        grid-row: span 1;
    }
}

@media (max-width: 768px) {
    .block-success-cases .container {
        padding-left: 0;
        padding-right: 0;
    }

    .block-success-cases .section-header {
        padding-left: var(--spacing-md);
        padding-right: var(--spacing-md);
    }

    /* Carrusel horizontal en mobile - 1 card visible */
    .cases-grid.columns-4,
    .cases-grid.columns-3,
    .cases-grid.columns-2,
    .cases-grid {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        gap: var(--spacing-md);
        padding: 0 var(--spacing-md);
        margin: 0 calc(-1 * var(--spacing-md));
        padding-bottom: var(--spacing-md);
    }

    .cases-grid::-webkit-scrollbar {
        display: none;
    }

    .cases-grid .case-card {
        flex: 0 0 calc(100% - var(--spacing-lg));
        scroll-snap-align: center;
        min-width: calc(100% - var(--spacing-lg));
    }

    .case-card {
        padding: var(--spacing-lg);
    }

    .case-logo {
        height: 40px;
        margin-bottom: var(--spacing-md);
    }

    .case-content h3 {
        font-size: var(--font-size-base);
        margin-bottom: var(--spacing-sm);
    }

    /* Mostrar más contenido en el carrusel */
    .case-industry {
        display: inline-block;
    }

    .case-challenge {
        display: block;
        font-size: var(--font-size-sm);
    }

    .case-metrics {
        display: none;
    }

    .case-link {
        display: inline-flex;
    }

    .case-testimonial {
        padding: var(--spacing-md);
        margin-bottom: var(--spacing-md);
    }

    .case-testimonial p {
        font-size: var(--font-size-sm);
        margin-bottom: var(--spacing-sm);
    }

    .case-testimonial footer {
        font-size: var(--font-size-xs);
    }

    .block-success-cases .section-footer {
        padding-left: var(--spacing-md);
        padding-right: var(--spacing-md);
    }
}
</style>
