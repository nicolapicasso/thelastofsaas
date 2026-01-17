<?php
/**
 * Success Cases Index Template
 * We're Sinapsis CMS
 */
?>

<!-- Cases Header -->
<section class="cases-header">
    <div class="container">
        <h1><?= __('cases_title') ?></h1>
        <p><?= __('cases_subtitle') ?></p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <nav class="breadcrumb">
                <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                <span>/</span>
                <span><?= __('success_cases') ?></span>
            </nav>
        </div>
    </div>
</div>

<!-- Filter by Industry and Search -->
<section class="cases-filter">
    <div class="container">
        <form class="filter-form" method="GET" action="<?= _url('/casos-de-exito') ?>">
            <div class="filter-search">
                <i class="fas fa-search"></i>
                <input type="text"
                       name="buscar"
                       placeholder="<?= __('search_cases') ?>"
                       value="<?= htmlspecialchars($searchQuery ?? '') ?>">
                <?php if (!empty($searchQuery)): ?>
                    <a href="<?= _url('/casos-de-exito') ?><?= !empty($currentIndustry) ? '?industria=' . urlencode($currentIndustry) : '' ?>" class="filter-clear" title="<?= __('clear') ?>">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>

            <div class="filter-select">
                <select name="industria" onchange="this.form.submit()">
                    <option value=""><?= __('all_industries') ?> (<?= $totalCases ?? 0 ?>)</option>
                    <?php foreach ($industries as $ind): ?>
                        <option value="<?= htmlspecialchars($ind['industry']) ?>"
                                <?= ($currentIndustry ?? '') === $ind['industry'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ind['industry']) ?> (<?= $ind['case_count'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-chevron-down"></i>
            </div>

            <div class="filter-select">
                <select name="cliente" onchange="this.form.submit()">
                    <option value="">Todos los clientes</option>
                    <?php foreach ($clients ?? [] as $client): ?>
                        <option value="<?= (int)$client['id'] ?>"
                                <?= ($currentClient['id'] ?? 0) == $client['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client['name']) ?> (<?= $client['case_count'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-chevron-down"></i>
            </div>

            <button type="submit" class="filter-submit">
                <i class="fas fa-search"></i>
                <span><?= __('search') ?></span>
            </button>
        </form>

        <?php if (!empty($searchQuery) || !empty($currentIndustry) || !empty($currentClient)): ?>
            <div class="filter-active">
                <span class="filter-results">
                    <?= count($cases) ?> <?= __('results') ?>
                    <?php if (!empty($searchQuery)): ?>
                        <?= __('for') ?> "<?= htmlspecialchars($searchQuery) ?>"
                    <?php endif; ?>
                    <?php if (!empty($currentIndustry)): ?>
                        <?= __('in') ?> <?= htmlspecialchars($currentIndustry) ?>
                    <?php endif; ?>
                    <?php if (!empty($currentClient)): ?>
                        - <?= htmlspecialchars($currentClient['name']) ?>
                    <?php endif; ?>
                </span>
                <a href="<?= _url('/casos-de-exito') ?>" class="filter-reset">
                    <i class="fas fa-times"></i> <?= __('clear_filters') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Cases Grid -->
<section class="section">
    <div class="container">
        <?php if (!empty($cases)): ?>
            <div class="cases-grid">
                <?php foreach ($cases as $index => $case): ?>
                    <?php $metrics = $case['metrics_array'] ?? json_decode($case['metrics'] ?? '[]', true) ?: []; ?>
                    <article class="case-card">
                        <?php if (!empty($case['client_logo'])): ?>
                            <div class="case-logo">
                                <img src="<?= htmlspecialchars($case['client_logo']) ?>"
                                     alt="<?= htmlspecialchars($case['client_name'] ?? $case['title'] ?? '') ?>">
                            </div>
                        <?php endif; ?>

                        <div class="case-content">
                            <?php if (!empty($case['client_industry'])): ?>
                                <span class="case-industry"><?= htmlspecialchars($case['client_industry']) ?></span>
                            <?php endif; ?>

                            <h3><?= htmlspecialchars($case['title'] ?? '') ?></h3>

                            <?php if (!empty($case['challenge'])): ?>
                                <p class="case-challenge"><?= htmlspecialchars(strip_tags(substr($case['challenge'], 0, 150))) ?>...</p>
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

                            <?php if (!empty($case['testimonial'])): ?>
                                <blockquote class="case-testimonial">
                                    <p>"<?= htmlspecialchars(substr($case['testimonial'], 0, 200)) ?>"</p>
                                    <?php if (!empty($case['testimonial_author'])): ?>
                                        <footer>
                                            <strong><?= htmlspecialchars($case['testimonial_author']) ?></strong>
                                            <?php if (!empty($case['testimonial_role'])): ?>
                                                <span><?= htmlspecialchars($case['testimonial_role']) ?></span>
                                            <?php endif; ?>
                                        </footer>
                                    <?php endif; ?>
                                </blockquote>
                            <?php endif; ?>

                            <a href="<?= _url('/casos-de-exito/' . htmlspecialchars($case['slug'])) ?>" class="case-link">
                                <?= __('view_full_case') ?> <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if (!empty($pagination) && ($pagination['total_pages'] ?? 0) > 1): ?>
                <div class="pagination">
                    <?php if (($pagination['current_page'] ?? 1) > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?><?= !empty($currentIndustry) ? '&industria=' . urlencode($currentIndustry) : '' ?>" class="btn btn-outline btn-sm">
                            <i class="fas fa-chevron-left"></i> <?= __('previous') ?>
                        </a>
                    <?php endif; ?>

                    <span class="pagination-info">
                        <?= __('page') ?> <?= $pagination['current_page'] ?? 1 ?> <?= __('of') ?> <?= $pagination['total_pages'] ?? 1 ?>
                    </span>

                    <?php if (($pagination['current_page'] ?? 1) < ($pagination['total_pages'] ?? 1)): ?>
                        <a href="?page=<?= ($pagination['current_page'] ?? 1) + 1 ?><?= !empty($currentIndustry) ? '&industria=' . urlencode($currentIndustry) : '' ?>" class="btn btn-outline btn-sm">
                            <?= __('next') ?> <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-briefcase"></i>
                <h3><?= __('no_cases_found') ?></h3>
                <p><?= __('more_cases_coming_soon') ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cases-cta section bg-primary">
    <div class="container">
        <div class="cta-content">
            <h2><?= __('cta_next_success_story') ?></h2>
            <p><?= __('cta_join_companies') ?></p>
            <a href="<?= _url('/contacto') ?>" class="btn btn-white btn-lg"><?= __('contact_now') ?></a>
        </div>
    </div>
</section>

<style>
/* Cases Header */
.cases-header {
    min-height: 300px;
    padding-top: var(--header-height);
    background-color: #264752;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.cases-header .container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.cases-header h1 {
    margin-bottom: var(--spacing-sm);
    color: #ffffff;
}

.cases-header p {
    color: rgba(255, 255, 255, 0.85);
    font-size: var(--font-size-lg);
    max-width: 600px;
    margin: 0 auto;
}

/* Filter */
.cases-filter {
    background-color: var(--color-white);
    border-bottom: 1px solid var(--color-gray-200);
    padding: var(--spacing-lg) 0;
    position: sticky;
    top: var(--header-height);
    z-index: 90;
}

.filter-form {
    display: flex;
    gap: var(--spacing-md);
    align-items: stretch;
}

.filter-search {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}

.filter-search i.fa-search {
    position: absolute;
    left: var(--spacing-md);
    color: var(--color-gray-400);
    pointer-events: none;
}

.filter-search input {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md) var(--spacing-sm) calc(var(--spacing-md) * 2 + 16px);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    font-size: var(--font-size-base);
    transition: all var(--transition);
    background-color: var(--color-gray-50);
}

.filter-search input:focus {
    outline: none;
    border-color: var(--color-primary);
    background-color: var(--color-white);
    box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.1);
}

.filter-search input::placeholder {
    color: var(--color-gray-400);
}

.filter-clear {
    position: absolute;
    right: var(--spacing-sm);
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: var(--color-gray-400);
    transition: all var(--transition);
}

.filter-clear:hover {
    background-color: var(--color-gray-200);
    color: var(--color-gray-600);
}

.filter-select {
    position: relative;
    min-width: 240px;
}

.filter-select select {
    width: 100%;
    padding: var(--spacing-sm) calc(var(--spacing-md) + 20px) var(--spacing-sm) var(--spacing-md);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    font-size: var(--font-size-base);
    background-color: var(--color-gray-50);
    cursor: pointer;
    appearance: none;
    transition: all var(--transition);
}

.filter-select select:focus {
    outline: none;
    border-color: var(--color-primary);
    background-color: var(--color-white);
}

.filter-select i {
    position: absolute;
    right: var(--spacing-md);
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-gray-400);
    pointer-events: none;
    font-size: 12px;
}

.filter-submit {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-sm) var(--spacing-lg);
    background-color: var(--color-primary);
    color: var(--color-white);
    border: none;
    border-radius: var(--radius-lg);
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition);
}

.filter-submit:hover {
    background-color: var(--color-primary-dark);
}

.filter-active {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: var(--spacing-md);
    padding-top: var(--spacing-md);
    border-top: 1px solid var(--color-gray-100);
}

.filter-results {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
}

.filter-reset {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--color-gray-500);
    font-size: var(--font-size-sm);
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-md);
    transition: all var(--transition);
}

.filter-reset:hover {
    background-color: var(--color-gray-100);
    color: var(--color-gray-700);
}

/* Cases Grid - Extended Card Style */
.cases-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-xl);
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
    display: flex;
    justify-content: center;
    align-items: center;
    width: 300px;
    height: 100px;
    max-width: 100%;
    margin: 0 auto var(--spacing-lg) auto;
}

.case-logo img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
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

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: var(--spacing-md);
    margin-top: var(--spacing-2xl);
}

.pagination-info {
    color: var(--color-gray-500);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: var(--spacing-3xl);
    color: var(--color-gray-500);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: var(--spacing-md);
    color: var(--color-gray-300);
}

/* CTA Section */
.cases-cta {
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    color: var(--color-white);
}

.cases-cta .cta-content {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.cases-cta h2 {
    color: var(--color-white);
    margin-bottom: var(--spacing-sm);
}

.cases-cta p {
    opacity: 0.9;
    margin-bottom: var(--spacing-xl);
}

@media (max-width: 1024px) {
    .cases-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
        gap: var(--spacing-sm);
    }

    .filter-select {
        min-width: 100%;
    }

    .filter-submit {
        justify-content: center;
    }

    .filter-active {
        flex-direction: column;
        gap: var(--spacing-sm);
        text-align: center;
    }

    .cases-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-lg);
    }

    .case-card {
        padding: var(--spacing-xl);
    }

    .case-logo {
        width: 250px;
        height: 80px;
    }

    .case-content h3 {
        font-size: var(--font-size-lg);
        margin-bottom: var(--spacing-sm);
    }

    .case-challenge {
        font-size: var(--font-size-base);
    }

    .case-metrics {
        flex-wrap: wrap;
        gap: var(--spacing-md);
    }

    .metric {
        flex: 0 0 calc(50% - var(--spacing-sm));
    }

    .case-testimonial {
        padding: var(--spacing-md);
    }

    .case-testimonial p {
        font-size: var(--font-size-sm);
    }
}
</style>
