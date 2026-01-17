<?php
/**
 * Clients Directory Template
 * We're Sinapsis CMS
 */
?>

<!-- Clients Header -->
<section class="page-hero">
    <div class="container">
        <h1><?= __('our_clients') ?></h1>
        <p><?= __('clients_subtitle') ?></p>
        <?php if (isset($totalClients)): ?>
            <div class="hero-stats">
                <span class="stat"><strong><?= $totalClients ?></strong> <?= __('companies_trust_us') ?></span>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
            <span>/</span>
            <span><?= __('clients') ?></span>
        </nav>
    </div>
</div>

<!-- Logo Wall -->
<?php if (!empty($withLogos) && empty($searchQuery) && empty($currentIndustry)): ?>
<section class="section bg-light">
    <div class="container">
        <div class="logo-wall">
            <?php foreach ($withLogos as $client): ?>
                <a href="<?= _url('/clientes/' . $client['slug']) ?>" class="logo-item" title="<?= htmlspecialchars($client['name']) ?>">
                    <img src="<?= htmlspecialchars($client['logo']) ?>" alt="<?= htmlspecialchars($client['name']) ?>">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Filter Section -->
<section class="clients-filter">
    <div class="container">
        <form class="filter-form" method="GET" action="<?= _url('/clientes') ?>">
            <div class="filter-search">
                <i class="fas fa-search"></i>
                <input type="text" name="buscar" placeholder="<?= __('search_clients') ?>"
                       value="<?= htmlspecialchars($searchQuery ?? '') ?>">
                <?php if (!empty($searchQuery)): ?>
                    <a href="<?= _url('/clientes') ?>" class="filter-clear"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </div>

            <?php if (!empty($industries)): ?>
            <div class="filter-select">
                <select name="industria" onchange="this.form.submit()">
                    <option value=""><?= __('all_industries') ?></option>
                    <?php foreach ($industries as $ind): ?>
                        <option value="<?= htmlspecialchars($ind) ?>" <?= ($currentIndustry ?? '') === $ind ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ind) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-chevron-down"></i>
            </div>
            <?php endif; ?>

            <button type="submit" class="filter-submit">
                <i class="fas fa-search"></i>
                <span><?= __('search') ?></span>
            </button>
        </form>

        <?php if (!empty($searchQuery) || !empty($currentIndustry)): ?>
            <div class="filter-active">
                <span class="filter-results"><?= count($clients) ?> <?= __('clients_found') ?></span>
                <a href="<?= _url('/clientes') ?>" class="filter-reset">
                    <i class="fas fa-times"></i> <?= __('clear_filters') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Clients Grid -->
<section class="section">
    <div class="container">
        <?php if (!empty($clients)): ?>
            <div class="clients-grid">
                <?php foreach ($clients as $client): ?>
                    <article class="client-card">
                        <a href="<?= _url('/clientes/' . $client['slug']) ?>">
                            <div class="client-logo">
                                <?php if (!empty($client['logo'])): ?>
                                    <img src="<?= htmlspecialchars($client['logo']) ?>" alt="<?= htmlspecialchars($client['name']) ?>">
                                <?php else: ?>
                                    <div class="logo-placeholder">
                                        <i class="fas fa-building"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="client-content">
                                <h3><?= htmlspecialchars($client['name']) ?></h3>
                                <?php if (!empty($client['industry'])): ?>
                                    <span class="client-industry"><?= htmlspecialchars($client['industry']) ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-building"></i>
                <h3><?= __('no_clients_found') ?></h3>
                <p><?= __('try_different_search') ?></p>
                <a href="<?= _url('/clientes') ?>" class="btn btn-primary"><?= __('view_all_clients') ?></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section section bg-primary">
    <div class="container">
        <div class="cta-content">
            <h2><?= __('join_our_clients') ?></h2>
            <p><?= __('become_client_text') ?></p>
            <a href="<?= _url('/contacto') ?>" class="btn btn-white btn-lg"><?= __('contact_us') ?></a>
        </div>
    </div>
</section>

<style>
.page-hero {
    min-height: 300px;
    padding-top: var(--header-height);
    background: linear-gradient(135deg, #264752 0%, #1a3a44 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
}

.page-hero h1 { color: white; margin-bottom: var(--spacing-sm); }
.page-hero p { opacity: 0.9; font-size: var(--font-size-lg); max-width: 600px; margin: 0 auto var(--spacing-md); }

.hero-stats {
    display: inline-flex;
    gap: var(--spacing-xl);
    padding: var(--spacing-md) var(--spacing-xl);
    background: rgba(255,255,255,0.1);
    border-radius: var(--radius-full);
}

.hero-stats .stat strong {
    display: block;
    font-size: var(--font-size-2xl);
}

.logo-wall {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: var(--spacing-lg);
}

.logo-item {
    width: 140px;
    height: 80px;
    padding: var(--spacing-md);
    background: white;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition);
    box-shadow: var(--shadow-sm);
}

.logo-item:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-md);
}

.logo-item img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: grayscale(100%);
    opacity: 0.7;
    transition: all var(--transition);
}

.logo-item:hover img {
    filter: grayscale(0%);
    opacity: 1;
}

.clients-filter {
    background: white;
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
}

.filter-search input {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md) var(--spacing-sm) calc(var(--spacing-md) * 2 + 16px);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    background-color: var(--color-gray-50);
}

.filter-clear {
    position: absolute;
    right: var(--spacing-sm);
    color: var(--color-gray-400);
    padding: var(--spacing-xs);
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
    appearance: none;
}

.filter-select i {
    position: absolute;
    right: var(--spacing-md);
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-gray-400);
    font-size: 12px;
    pointer-events: none;
}

.filter-submit {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-sm) var(--spacing-lg);
    background-color: var(--color-primary);
    color: white;
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

.filter-results { color: var(--color-gray-600); font-size: var(--font-size-sm); }
.filter-reset { display: inline-flex; align-items: center; gap: var(--spacing-xs); color: var(--color-gray-500); font-size: var(--font-size-sm); }

.clients-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-xl);
}

.client-card {
    background: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.client-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.client-logo {
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-50);
    padding: var(--spacing-lg);
}

.client-logo img {
    max-width: 100%;
    max-height: 80px;
    object-fit: contain;
}

.client-logo .logo-placeholder {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-200);
    border-radius: var(--radius-lg);
    color: var(--color-gray-400);
    font-size: 24px;
}

.client-content {
    padding: var(--spacing-lg);
    text-align: center;
}

.client-content h3 {
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-xs);
}

.client-industry {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

.cta-section { text-align: center; }
.cta-content { max-width: 600px; margin: 0 auto; }
.cta-content h2 { color: white; margin-bottom: var(--spacing-sm); }
.cta-content p { opacity: 0.9; margin-bottom: var(--spacing-xl); }

.empty-state { text-align: center; padding: var(--spacing-3xl); color: var(--color-gray-500); }
.empty-state i { font-size: 48px; margin-bottom: var(--spacing-md); color: var(--color-gray-300); }

@media (max-width: 1024px) { .clients-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px) {
    .filter-form { flex-direction: column; }
    .clients-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) { .clients-grid { grid-template-columns: 1fr; } }
</style>
