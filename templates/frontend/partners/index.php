<?php
/**
 * Partners Directory Index Template
 * Omniwallet CMS
 */
?>

<!-- Partners Header -->
<section class="partners-header">
    <div class="container">
        <h1><?= __('partners_directory') ?></h1>
        <p><?= __('partners_directory_subtitle') ?></p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <nav class="breadcrumb">
                <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                <span>/</span>
                <span><?= __('partners') ?></span>
            </nav>
        </div>
    </div>
</div>

<!-- Filter Section -->
<section class="partners-filter">
    <div class="container">
        <form class="filter-form" method="GET" action="<?= _url('/partners') ?>">
            <div class="filter-search">
                <i class="fas fa-search"></i>
                <input type="text"
                       name="buscar"
                       placeholder="<?= __('search_partners') ?>"
                       value="<?= htmlspecialchars($searchQuery ?? '') ?>">
                <?php if (!empty($searchQuery)): ?>
                    <a href="<?= _url('/partners') ?>" class="filter-clear" title="<?= __('clear') ?>">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>

            <div class="filter-select">
                <select name="pais" id="countryFilter" onchange="updateCities(); this.form.submit();">
                    <option value=""><?= __('all_countries') ?></option>
                    <?php foreach ($countries as $c): ?>
                        <option value="<?= htmlspecialchars($c['country']) ?>"
                                <?= ($currentCountry ?? '') === $c['country'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['country']) ?> (<?= $c['partner_count'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-chevron-down"></i>
            </div>

            <?php if (!empty($cities)): ?>
            <div class="filter-select">
                <select name="ciudad" onchange="this.form.submit()">
                    <option value=""><?= __('all_cities') ?></option>
                    <?php foreach ($cities as $c): ?>
                        <option value="<?= htmlspecialchars($c['city']) ?>"
                                <?= ($currentCity ?? '') === $c['city'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['city']) ?> (<?= $c['partner_count'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-chevron-down"></i>
            </div>
            <?php endif; ?>

            <div class="filter-select">
                <select name="tipo" onchange="this.form.submit()">
                    <option value=""><?= __('all_types') ?></option>
                    <?php foreach ($partnerTypes as $value => $label): ?>
                        <option value="<?= $value ?>" <?= ($currentType ?? '') === $value ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
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

        <?php if (!empty($searchQuery) || !empty($currentCountry) || !empty($currentCity) || !empty($currentType)): ?>
            <div class="filter-active">
                <span class="filter-results">
                    <?= count($partners) ?> <?= __('partners_found') ?>
                    <?php if (!empty($searchQuery)): ?>
                        <?= __('for') ?> "<?= htmlspecialchars($searchQuery) ?>"
                    <?php endif; ?>
                </span>
                <a href="<?= _url('/partners') ?>" class="filter-reset">
                    <i class="fas fa-times"></i> <?= __('clear_filters') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Partners Grid -->
<section class="section">
    <div class="container">
        <?php if (!empty($partners)): ?>
            <div class="partners-grid">
                <?php foreach ($partners as $partner): ?>
                    <article class="partner-card">
                        <div class="partner-logo">
                            <?php if (!empty($partner['logo'])): ?>
                                <img src="<?= htmlspecialchars($partner['logo']) ?>"
                                     alt="<?= htmlspecialchars($partner['name']) ?>">
                            <?php else: ?>
                                <div class="logo-placeholder">
                                    <i class="fas fa-building"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="partner-content">
                            <div class="partner-badges">
                                <span class="badge badge-<?= $partner['partner_type'] === 'tech_partner' ? 'primary' : 'info' ?>">
                                    <?= $partnerTypes[$partner['partner_type']] ?? $partner['partner_type'] ?>
                                </span>
                            </div>
                            <?php if ($partner['is_certified'] && !empty($certificationSeal)): ?>
                                <div class="partner-certification-seal">
                                    <img src="<?= htmlspecialchars($certificationSeal) ?>" alt="<?= __('certified') ?>">
                                </div>
                            <?php endif; ?>

                            <h3><?= htmlspecialchars($partner['name']) ?></h3>

                            <?php if (!empty($partner['city']) || !empty($partner['country'])): ?>
                                <p class="partner-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars(trim(($partner['city'] ?? '') . ', ' . ($partner['country'] ?? ''), ', ')) ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($partner['description'])): ?>
                                <p class="partner-description"><?= htmlspecialchars(substr(strip_tags($partner['description']), 0, 120)) ?>...</p>
                            <?php endif; ?>

                            <a href="<?= _url('/partners/' . htmlspecialchars($partner['slug'])) ?>" class="partner-link">
                                <?= __('view_partner') ?> <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-handshake"></i>
                <h3><?= __('no_partners_found') ?></h3>
                <p><?= __('try_different_filters') ?></p>
                <a href="<?= _url('/partners') ?>" class="btn btn-primary"><?= __('view_all_partners') ?></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Certified Partners Section -->
<?php if (!empty($certifiedPartners) && empty($searchQuery) && empty($currentCountry)): ?>
<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <h2><?= __('certified_partners') ?></h2>
            <p><?= __('certified_partners_desc') ?></p>
        </div>
        <div class="certified-grid">
            <?php foreach ($certifiedPartners as $partner): ?>
                <a href="<?= _url('/partners/' . htmlspecialchars($partner['slug'])) ?>" class="certified-card">
                    <?php if (!empty($certificationSeal)): ?>
                        <div class="certification-seal">
                            <img src="<?= htmlspecialchars($certificationSeal) ?>" alt="Certified Partner">
                        </div>
                    <?php else: ?>
                        <div class="certification-badge">
                            <i class="fas fa-certificate"></i>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($partner['logo'])): ?>
                        <img src="<?= htmlspecialchars($partner['logo']) ?>" alt="<?= htmlspecialchars($partner['name']) ?>">
                    <?php else: ?>
                        <span class="certified-name"><?= htmlspecialchars($partner['name']) ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="partners-cta section bg-primary">
    <div class="container">
        <div class="cta-content">
            <h2><?= __('become_partner_title') ?></h2>
            <p><?= __('become_partner_desc') ?></p>
            <a href="<?= _url('/contacto') ?>" class="btn btn-white btn-lg"><?= __('contact_us') ?></a>
        </div>
    </div>
</section>

<style>
/* Partners Header */
.partners-header {
    min-height: 300px;
    padding-top: var(--header-height);
    background: linear-gradient(135deg, #264752 0%, #1a3a44 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.partners-header .container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.partners-header h1 {
    margin-bottom: var(--spacing-sm);
    color: #ffffff;
}

.partners-header p {
    color: rgba(255, 255, 255, 0.85);
    font-size: var(--font-size-lg);
    max-width: 600px;
    margin: 0 auto;
}

/* Filter */
.partners-filter {
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
    flex-wrap: wrap;
}

.filter-search {
    flex: 1;
    min-width: 200px;
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
    background-color: var(--color-gray-50);
}

.filter-search input:focus {
    outline: none;
    border-color: var(--color-primary);
    background-color: var(--color-white);
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
}

.filter-select {
    position: relative;
    min-width: 180px;
}

.filter-select select {
    width: 100%;
    padding: var(--spacing-sm) calc(var(--spacing-md) + 20px) var(--spacing-sm) var(--spacing-md);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    background-color: var(--color-gray-50);
    cursor: pointer;
    appearance: none;
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
}

/* Partners Grid */
.partners-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-xl);
}

.partner-card {
    position: relative;
    background-color: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
    display: flex;
    flex-direction: column;
}

.partner-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-4px);
}

.partner-logo {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--spacing-lg);
}

.partner-logo img {
    max-height: 70px;
    max-width: 100%;
    object-fit: contain;
}

.partner-logo .logo-placeholder {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-100);
    border-radius: var(--radius-lg);
    color: var(--color-gray-400);
    font-size: 28px;
}

.partner-badges {
    display: flex;
    gap: var(--spacing-xs);
    flex-wrap: wrap;
    margin-bottom: var(--spacing-sm);
}

.partner-badges .badge {
    font-size: var(--font-size-xs);
    padding: 4px 8px;
    border-radius: var(--radius-sm);
}

/* Certification seal on partner card */
.partner-certification-seal {
    position: absolute;
    top: var(--spacing-md);
    right: var(--spacing-md);
    width: 48px;
    height: 48px;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
}

.partner-certification-seal img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.partner-content h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-xs);
}

.partner-location {
    color: var(--color-gray-500);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-sm);
}

.partner-location i {
    margin-right: var(--spacing-xs);
}

.partner-description {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    flex: 1;
    margin-bottom: var(--spacing-md);
}

.partner-link {
    color: var(--color-primary);
    font-weight: 600;
    font-size: var(--font-size-sm);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.partner-link:hover {
    color: var(--color-primary-dark);
}

.partner-link i {
    transition: transform var(--transition);
}

.partner-link:hover i {
    transform: translateX(4px);
}

/* Certified Partners Section */
.certified-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: var(--spacing-lg);
}

.certified-card {
    background: white;
    padding: var(--spacing-lg);
    border-radius: var(--radius-lg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 120px;
    position: relative;
    transition: all var(--transition);
    box-shadow: var(--shadow-sm);
}

.certified-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.certified-card img {
    max-height: 50px;
    max-width: 100%;
    object-fit: contain;
}

.certification-seal {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 40px;
    height: 40px;
}

.certification-seal img {
    width: 100%;
    height: 100%;
}

.certification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    box-shadow: var(--shadow-sm);
}

.certified-name {
    font-weight: 600;
    text-align: center;
    color: var(--color-gray-700);
}

/* CTA Section */
.partners-cta {
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    color: var(--color-white);
}

.partners-cta .cta-content {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.partners-cta h2 {
    color: var(--color-white);
    margin-bottom: var(--spacing-sm);
}

.partners-cta p {
    opacity: 0.9;
    margin-bottom: var(--spacing-xl);
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

/* Responsive */
@media (max-width: 1200px) {
    .partners-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .certified-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
    }
    .filter-select {
        min-width: 100%;
    }
    .partners-grid {
        grid-template-columns: 1fr;
    }
    .certified-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
function updateCities() {
    const countrySelect = document.getElementById('countryFilter');
    const country = countrySelect.value;

    // The form will submit and reload with cities for the selected country
}
</script>
