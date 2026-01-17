<?php
/**
 * Client Detail Template
 * We're Sinapsis CMS
 */
?>

<article class="client-detail">
    <!-- Hero Section -->
    <section class="client-hero">
        <div class="container">
            <div class="client-hero-content">
                <?php if (!empty($client['logo'])): ?>
                    <div class="client-hero-logo">
                        <img src="<?= htmlspecialchars($client['logo']) ?>" alt="<?= htmlspecialchars($client['name']) ?>">
                    </div>
                <?php endif; ?>
                <div class="client-hero-info">
                    <?php if (!empty($client['industry'])): ?>
                        <span class="client-industry-badge"><?= htmlspecialchars($client['industry']) ?></span>
                    <?php endif; ?>
                    <h1><?= htmlspecialchars($client['name']) ?></h1>
                    <?php if (!empty($client['location'])): ?>
                        <p class="client-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($client['location']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="container">
            <nav class="breadcrumb">
                <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                <span>/</span>
                <a href="<?= _url('/clientes') ?>"><?= __('clients') ?></a>
                <span>/</span>
                <span><?= htmlspecialchars($client['name']) ?></span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="client-layout">
                <div class="client-main">
                    <?php if (!empty($client['description'])): ?>
                        <div class="client-description">
                            <?= $client['description'] ?>
                        </div>
                    <?php endif; ?>

                    <!-- Success Cases -->
                    <?php if (!empty($successCases)): ?>
                        <div class="client-cases">
                            <h2><?= __('projects_with_client') ?></h2>
                            <div class="cases-list">
                                <?php foreach ($successCases as $case): ?>
                                    <a href="<?= _url('/casos-de-exito/' . $case['slug']) ?>" class="case-item">
                                        <?php if (!empty($case['featured_image'])): ?>
                                            <div class="case-image">
                                                <img src="<?= htmlspecialchars($case['featured_image']) ?>" alt="">
                                            </div>
                                        <?php endif; ?>
                                        <div class="case-info">
                                            <h3><?= htmlspecialchars($case['title']) ?></h3>
                                            <?php if (!empty($case['challenge'])): ?>
                                                <p><?= htmlspecialchars(substr(strip_tags($case['challenge']), 0, 150)) ?>...</p>
                                            <?php endif; ?>
                                            <span class="case-link"><?= __('view_case') ?> <i class="fas fa-arrow-right"></i></span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="client-sidebar">
                    <!-- Info Card -->
                    <div class="sidebar-card">
                        <h4><?= __('client_info') ?></h4>
                        <dl class="info-list">
                            <?php if (!empty($client['industry'])): ?>
                                <dt><?= __('industry') ?></dt>
                                <dd><?= htmlspecialchars($client['industry']) ?></dd>
                            <?php endif; ?>
                            <?php if (!empty($client['location'])): ?>
                                <dt><?= __('location') ?></dt>
                                <dd><?= htmlspecialchars($client['location']) ?></dd>
                            <?php endif; ?>
                        </dl>
                        <?php if (!empty($client['website'])): ?>
                            <a href="<?= htmlspecialchars($client['website']) ?>" target="_blank" rel="noopener" class="btn btn-outline btn-block">
                                <i class="fas fa-external-link-alt"></i> <?= __('visit_website') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Related Clients -->
    <?php if (!empty($relatedClients)): ?>
    <section class="section bg-light">
        <div class="container">
            <div class="section-header">
                <h2><?= __('more_clients_industry') ?></h2>
            </div>
            <div class="related-grid">
                <?php foreach ($relatedClients as $related): ?>
                    <a href="<?= _url('/clientes/' . $related['slug']) ?>" class="related-card">
                        <div class="related-logo">
                            <?php if (!empty($related['logo'])): ?>
                                <img src="<?= htmlspecialchars($related['logo']) ?>" alt="">
                            <?php else: ?>
                                <i class="fas fa-building"></i>
                            <?php endif; ?>
                        </div>
                        <h4><?= htmlspecialchars($related['name']) ?></h4>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</article>

<style>
.client-hero {
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-3xl);
    background: linear-gradient(135deg, #264752 0%, #1a3a44 100%);
    color: white;
}

.client-hero-content {
    display: flex;
    align-items: center;
    gap: var(--spacing-xl);
}

.client-hero-logo {
    flex-shrink: 0;
    width: 140px;
    height: 140px;
    background: white;
    border-radius: var(--radius-xl);
    padding: var(--spacing-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-lg);
}

.client-hero-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.client-industry-badge {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-sm);
    background: rgba(255,255,255,0.2);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-sm);
}

.client-hero-info h1 { color: white; margin-bottom: var(--spacing-sm); }
.client-location { opacity: 0.9; display: flex; align-items: center; gap: var(--spacing-sm); }

.client-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: var(--spacing-2xl);
}

.client-description {
    font-size: var(--font-size-lg);
    line-height: 1.8;
    color: var(--color-gray-700);
    margin-bottom: var(--spacing-2xl);
}

.client-cases h2 { margin-bottom: var(--spacing-lg); }

.cases-list { display: flex; flex-direction: column; gap: var(--spacing-lg); }

.case-item {
    display: flex;
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
    background: var(--color-gray-50);
    border-radius: var(--radius-lg);
    transition: all var(--transition);
}

.case-item:hover { background: var(--color-gray-100); }

.case-image {
    flex-shrink: 0;
    width: 200px;
    height: 130px;
    border-radius: var(--radius-md);
    overflow: hidden;
}

.case-image img { width: 100%; height: 100%; object-fit: cover; }

.case-info h3 { margin-bottom: var(--spacing-xs); }
.case-info p { color: var(--color-gray-600); font-size: var(--font-size-sm); margin-bottom: var(--spacing-sm); }
.case-link { color: var(--color-primary); font-weight: 600; font-size: var(--font-size-sm); display: inline-flex; align-items: center; gap: var(--spacing-xs); }

.client-sidebar { position: sticky; top: calc(var(--header-height) + var(--spacing-lg)); height: fit-content; }

.sidebar-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-md);
}

.sidebar-card h4 { margin-bottom: var(--spacing-md); padding-bottom: var(--spacing-sm); border-bottom: 2px solid var(--color-primary); }

.info-list { display: grid; gap: var(--spacing-sm); margin-bottom: var(--spacing-lg); }
.info-list dt { font-size: var(--font-size-sm); color: var(--color-gray-500); }
.info-list dd { font-weight: 500; }

.related-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-lg);
}

.related-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    text-align: center;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.related-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

.related-logo {
    width: 80px;
    height: 80px;
    margin: 0 auto var(--spacing-md);
    display: flex;
    align-items: center;
    justify-content: center;
}

.related-logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
.related-logo i { font-size: 32px; color: var(--color-gray-400); }
.related-card h4 { font-size: var(--font-size-sm); margin: 0; }

@media (max-width: 1024px) {
    .client-layout { grid-template-columns: 1fr; }
    .related-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .client-hero-content { flex-direction: column; text-align: center; }
    .case-item { flex-direction: column; }
    .case-image { width: 100%; height: 180px; }
    .related-grid { grid-template-columns: 1fr; }
}
</style>
