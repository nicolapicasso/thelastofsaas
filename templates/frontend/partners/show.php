<?php
/**
 * Partner Detail Template
 * Omniwallet CMS
 */

$typeName = $partnerTypes[$partner['partner_type']] ?? 'Partner';
?>

<article class="partner-detail">
    <!-- Hero Section -->
    <section class="partner-hero" <?php if ($partner['featured_image']): ?>style="background-image: url('<?= htmlspecialchars($partner['featured_image']) ?>');"<?php endif; ?>>
        <div class="partner-hero-overlay"></div>
        <div class="container">
            <div class="partner-hero-content">
                <?php if ($partner['logo']): ?>
                    <div class="partner-hero-logo">
                        <img src="<?= htmlspecialchars($partner['logo']) ?>"
                             alt="<?= htmlspecialchars($partner['name']) ?>">
                    </div>
                <?php endif; ?>
                <div class="partner-hero-info">
                    <div class="partner-badges">
                        <span class="partner-type-badge type-<?= htmlspecialchars($partner['partner_type']) ?>">
                            <i class="fas <?= $partner['partner_type'] === 'agency' ? 'fa-users' : 'fa-plug' ?>"></i>
                            <?= htmlspecialchars($typeName) ?>
                        </span>
                    </div>
                    <h1><?= htmlspecialchars($partner['name']) ?></h1>
                    <?php if ($partner['city'] || $partner['country']): ?>
                        <p class="partner-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars(trim(($partner['city'] ?? '') . ', ' . ($partner['country'] ?? ''), ', ')) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <?php if ($partner['is_certified'] && $certificationSeal): ?>
                    <div class="partner-certification-seal-hero">
                        <img src="<?= htmlspecialchars($certificationSeal) ?>" alt="<?= __('certified_partner') ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="container">
            <div class="breadcrumb-wrapper">
                <nav class="breadcrumb">
                    <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                    <span>/</span>
                    <a href="<?= _url('/partners') ?>"><?= __('partners') ?></a>
                    <span>/</span>
                    <span><?= htmlspecialchars($partner['name']) ?></span>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="partner-body">
        <div class="container">
            <div class="partner-layout">
                <!-- Main Column -->
                <div class="partner-main">
                    <?php if ($partner['description']): ?>
                        <section class="partner-section">
                            <h2><i class="fas fa-info-circle"></i> <?= __('about_partner') ?></h2>
                            <div class="section-content">
                                <?= $partner['description'] ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Testimonial (inline version for main content) -->
                    <?php if ($partner['testimonial']): ?>
                        <section class="partner-section partner-testimonial-section">
                            <h2><i class="fas fa-quote-left"></i> <?= __('testimonial') ?></h2>
                            <blockquote class="partner-testimonial">
                                <p>"<?= htmlspecialchars($partner['testimonial']) ?>"</p>
                                <?php if ($partner['testimonial_author']): ?>
                                    <footer>
                                        <strong><?= htmlspecialchars($partner['testimonial_author']) ?></strong>
                                        <?php if ($partner['testimonial_role']): ?>
                                            <span><?= htmlspecialchars($partner['testimonial_role']) ?></span>
                                        <?php endif; ?>
                                    </footer>
                                <?php endif; ?>
                            </blockquote>
                        </section>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="partner-sidebar">
                    <!-- Contact Card -->
                    <div class="contact-card">
                        <h4><?= __('contact') ?></h4>
                        <ul class="contact-list">
                            <?php if ($partner['website']): ?>
                                <li>
                                    <a href="<?= htmlspecialchars($partner['website']) ?>" target="_blank" rel="noopener" class="contact-link website">
                                        <i class="fas fa-globe"></i>
                                        <span><?= htmlspecialchars(parse_url($partner['website'], PHP_URL_HOST)) ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($partner['email']): ?>
                                <li>
                                    <a href="mailto:<?= htmlspecialchars($partner['email']) ?>" class="contact-link email">
                                        <i class="fas fa-envelope"></i>
                                        <span><?= htmlspecialchars($partner['email']) ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($partner['phone']): ?>
                                <li>
                                    <a href="tel:<?= htmlspecialchars(preg_replace('/[^+0-9]/', '', $partner['phone'])) ?>" class="contact-link phone">
                                        <i class="fas fa-phone"></i>
                                        <span><?= htmlspecialchars($partner['phone']) ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($partner['linkedin']): ?>
                                <li>
                                    <a href="<?= htmlspecialchars($partner['linkedin']) ?>" target="_blank" rel="noopener" class="contact-link linkedin">
                                        <i class="fab fa-linkedin"></i>
                                        <span>LinkedIn</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <?php if ($partner['website']): ?>
                            <a href="<?= htmlspecialchars($partner['website']) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-block">
                                <?= __('visit_website') ?> <i class="fas fa-external-link-alt"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Info Card -->
                    <div class="info-card">
                        <h4><?= __('details') ?></h4>
                        <dl>
                            <dt><?= __('type') ?></dt>
                            <dd><?= htmlspecialchars($typeName) ?></dd>

                            <?php if ($partner['country']): ?>
                                <dt><?= __('country') ?></dt>
                                <dd><?= htmlspecialchars($partner['country']) ?></dd>
                            <?php endif; ?>

                            <?php if ($partner['city']): ?>
                                <dt><?= __('city') ?></dt>
                                <dd><?= htmlspecialchars($partner['city']) ?></dd>
                            <?php endif; ?>

                            <?php if ($partner['is_certified']): ?>
                                <dt><?= __('certification') ?></dt>
                                <dd class="certified-status">
                                    <i class="fas fa-check-circle"></i> <?= __('certified_partner') ?>
                                </dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <!-- Related Partners -->
    <?php if (!empty($relatedPartners)): ?>
        <section class="related-partners section bg-gray">
            <div class="container">
                <h2><?= __('related_partners') ?></h2>
                <div class="partners-grid">
                    <?php foreach ($relatedPartners as $related): ?>
                        <article class="partner-card">
                            <?php if ($related['logo']): ?>
                                <div class="partner-logo">
                                    <img src="<?= htmlspecialchars($related['logo']) ?>"
                                         alt="<?= htmlspecialchars($related['name']) ?>">
                                </div>
                            <?php else: ?>
                                <div class="partner-logo partner-logo-placeholder">
                                    <i class="fas <?= $related['partner_type'] === 'agency' ? 'fa-users' : 'fa-plug' ?>"></i>
                                </div>
                            <?php endif; ?>
                            <?php if ($related['is_certified'] && !empty($certificationSeal)): ?>
                                <div class="related-certification-seal">
                                    <img src="<?= htmlspecialchars($certificationSeal) ?>" alt="<?= __('certified') ?>">
                                </div>
                            <?php endif; ?>
                            <div class="partner-content">
                                <div class="partner-meta">
                                    <span class="type-badge type-<?= htmlspecialchars($related['partner_type']) ?>">
                                        <?= htmlspecialchars($partnerTypes[$related['partner_type']] ?? 'Partner') ?>
                                    </span>
                                </div>
                                <h3><?= htmlspecialchars($related['name']) ?></h3>
                                <?php if ($related['city'] || $related['country']): ?>
                                    <p class="partner-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars(trim(($related['city'] ?? '') . ', ' . ($related['country'] ?? ''), ', ')) ?>
                                    </p>
                                <?php endif; ?>
                                <a href="<?= _url('/partners/' . htmlspecialchars($related['slug'])) ?>" class="partner-link">
                                    <?= __('view_partner') ?> <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="partner-cta section">
        <div class="container">
            <div class="cta-box">
                <h2><?= __('become_partner_title') ?></h2>
                <p><?= __('become_partner_text') ?></p>
                <a href="<?= _url('/contacto') ?>" class="btn btn-primary btn-lg">
                    <?= __('contact_us') ?> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
</article>

<style>
/* Partner Hero Section */
.partner-hero {
    position: relative;
    min-height: 400px;
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-3xl);
    background-color: #1a3a44;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: flex-end;
}

.partner-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        rgba(26, 58, 68, 0.4) 0%,
        rgba(26, 58, 68, 0.7) 50%,
        rgba(26, 58, 68, 0.9) 100%
    );
    z-index: 1;
}

.partner-hero .container {
    position: relative;
    z-index: 2;
}

.partner-hero-content {
    display: flex;
    align-items: flex-end;
    gap: var(--spacing-xl);
}

.partner-hero-logo {
    flex-shrink: 0;
    width: 140px;
    height: 140px;
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--spacing-md);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-lg);
}

.partner-hero-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.partner-hero-info {
    flex: 1;
    color: var(--color-white);
}

.partner-badges {
    display: flex;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-sm);
}

.partner-type-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    font-weight: 600;
}

.partner-type-badge.type-agency {
    background-color: rgba(99, 102, 241, 0.9);
    color: white;
}

.partner-type-badge.type-tech_partner {
    background-color: rgba(16, 185, 129, 0.9);
    color: white;
}

/* Certification Seal - Large version in hero */
.partner-certification-seal-hero {
    flex-shrink: 0;
    width: 120px;
    height: 120px;
    filter: drop-shadow(0 4px 12px rgba(0,0,0,0.25));
}

.partner-certification-seal-hero img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.partner-hero-info h1 {
    font-size: var(--font-size-3xl);
    font-weight: 700;
    margin-bottom: var(--spacing-sm);
    color: white;
}

.partner-hero-info .partner-location {
    font-size: var(--font-size-lg);
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

/* Partner Body */
.partner-body {
    padding: var(--spacing-2xl) 0;
}

.partner-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: var(--spacing-2xl);
}

/* Partner Sections */
.partner-section {
    margin-bottom: var(--spacing-2xl);
}

.partner-section h2 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-lg);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.partner-section h2 i {
    color: var(--color-primary);
}

.section-content {
    color: var(--color-gray-700);
    line-height: 1.8;
    font-size: var(--font-size-base);
}

/* Testimonial */
.partner-testimonial {
    background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-gray-100) 100%);
    border-left: 4px solid var(--color-primary);
    padding: var(--spacing-xl);
    border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
}

.partner-testimonial p {
    font-size: var(--font-size-lg);
    font-style: italic;
    color: var(--color-gray-700);
    line-height: 1.7;
    margin-bottom: var(--spacing-md);
}

.partner-testimonial footer {
    display: flex;
    flex-direction: column;
}

.partner-testimonial footer strong {
    color: var(--color-dark);
}

.partner-testimonial footer span {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

/* Sidebar */
.partner-sidebar {
    position: sticky;
    top: calc(var(--header-height) + var(--spacing-lg));
    height: fit-content;
}

.contact-card {
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: var(--spacing-lg);
}

.contact-card h4 {
    margin-bottom: var(--spacing-md);
    padding-bottom: var(--spacing-sm);
    border-bottom: 2px solid var(--color-primary);
}

.contact-list {
    list-style: none;
    padding: 0;
    margin: 0 0 var(--spacing-lg) 0;
}

.contact-list li {
    margin-bottom: var(--spacing-sm);
}

.contact-link {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm);
    border-radius: var(--radius-md);
    color: var(--color-gray-700);
    transition: all var(--transition);
}

.contact-link:hover {
    background-color: var(--color-gray-50);
    color: var(--color-primary);
}

.contact-link i {
    width: 20px;
    text-align: center;
    color: var(--color-primary);
}

.contact-link span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-block {
    display: block;
    width: 100%;
    text-align: center;
}

.info-card {
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-sm);
}

.info-card h4 {
    margin-bottom: var(--spacing-md);
    padding-bottom: var(--spacing-sm);
    border-bottom: 2px solid var(--color-primary);
}

.info-card dl {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: var(--spacing-sm) var(--spacing-md);
}

.info-card dt {
    font-weight: 600;
    color: var(--color-gray-500);
    font-size: var(--font-size-sm);
}

.info-card dd {
    color: var(--color-dark);
}

.certified-status {
    color: var(--color-success);
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

/* Related Partners */
.related-partners {
    background-color: var(--color-gray-50);
}

.related-partners h2 {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.related-partners .partners-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-lg);
}

.related-partners .partner-card {
    position: relative;
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.related-certification-seal {
    position: absolute;
    top: var(--spacing-sm);
    right: var(--spacing-sm);
    width: 36px;
    height: 36px;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
}

.related-certification-seal img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.related-partners .partner-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.related-partners .partner-logo {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--spacing-md);
}

.related-partners .partner-logo img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
}

.related-partners .partner-logo-placeholder {
    width: 60px;
    background-color: var(--color-gray-100);
    border-radius: var(--radius-md);
    color: var(--color-gray-400);
    font-size: var(--font-size-xl);
}

.related-partners .partner-meta {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    margin-bottom: var(--spacing-xs);
}

.related-partners .type-badge {
    font-size: var(--font-size-xs);
    padding: 2px var(--spacing-xs);
    border-radius: var(--radius-sm);
    font-weight: 600;
}

.related-partners .type-badge.type-agency {
    background-color: #eef2ff;
    color: #4f46e5;
}

.related-partners .type-badge.type-tech_partner {
    background-color: #ecfdf5;
    color: #059669;
}

.related-partners h3 {
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-xs);
}

.related-partners .partner-location {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    margin-bottom: var(--spacing-sm);
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

/* CTA Section */
.partner-cta {
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    color: var(--color-white);
}

.cta-box {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.cta-box h2 {
    color: white;
    margin-bottom: var(--spacing-md);
}

.cta-box p {
    opacity: 0.9;
    margin-bottom: var(--spacing-lg);
}

.cta-box .btn-primary {
    background-color: white;
    color: var(--color-primary);
}

.cta-box .btn-primary:hover {
    background-color: var(--color-gray-100);
}

/* Responsive */
@media (max-width: 1024px) {
    .partner-layout {
        grid-template-columns: 1fr;
    }

    .partner-sidebar {
        position: static;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-lg);
    }

    .related-partners .partners-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .partner-hero {
        min-height: 300px;
        padding-top: calc(var(--spacing-2xl) + var(--header-height));
    }

    .partner-hero-content {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-md);
    }

    .partner-hero-logo {
        width: 100px;
        height: 100px;
    }

    .partner-hero-info h1 {
        font-size: var(--font-size-2xl);
    }

    .partner-certification-seal-hero {
        position: absolute;
        top: calc(var(--header-height) + var(--spacing-lg));
        right: var(--spacing-lg);
        width: 80px;
        height: 80px;
    }

    .partner-sidebar {
        grid-template-columns: 1fr;
    }

    .related-partners .partners-grid {
        grid-template-columns: 1fr;
    }
}
</style>
