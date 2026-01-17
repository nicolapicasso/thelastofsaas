<?php
/**
 * Team Member Detail Template
 * Omniwallet CMS
 */
?>

<article class="team-member-detail">
    <!-- Hero Section -->
    <section class="member-hero-section">
        <div class="container">
            <div class="member-hero-content">
                <div class="member-photo-wrapper">
                    <?php if (!empty($member['photo'])): ?>
                        <div class="member-photo">
                            <img src="<?= htmlspecialchars($member['photo']) ?>"
                                 alt="<?= htmlspecialchars($member['name']) ?>"
                                 class="photo-main">
                            <?php if (!empty($member['photo_hover'])): ?>
                                <img src="<?= htmlspecialchars($member['photo_hover']) ?>"
                                     alt="<?= htmlspecialchars($member['name']) ?>"
                                     class="photo-hover">
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="member-photo-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="member-info">
                    <h1><?= htmlspecialchars($member['name']) ?></h1>

                    <?php if (!empty($member['role'])): ?>
                        <p class="member-position"><?= htmlspecialchars($member['role']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($member['bio'])): ?>
                        <div class="member-description">
                            <?= nl2br(htmlspecialchars($member['bio'])) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Contact Links -->
                    <?php if (!empty($member['email']) || !empty($member['linkedin'])): ?>
                        <div class="member-contact">
                            <?php if (!empty($member['email'])): ?>
                                <a href="mailto:<?= htmlspecialchars($member['email']) ?>" class="contact-link">
                                    <i class="fas fa-envelope"></i>
                                    <span>Enviar email</span>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($member['linkedin'])): ?>
                                <a href="<?= htmlspecialchars($member['linkedin']) ?>" target="_blank" rel="noopener" class="contact-link linkedin">
                                    <i class="fab fa-linkedin"></i>
                                    <span>LinkedIn</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
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
                    <a href="<?= _url('/equipo') ?>"><?= __('team') ?></a>
                    <span>/</span>
                    <span><?= htmlspecialchars($member['name']) ?></span>
                </nav>
            </div>
        </div>
    </div>

    <!-- Other Team Members -->
    <?php if (!empty($otherMembers)): ?>
        <section class="other-members section bg-gray">
            <div class="container">
                <h2>Otros miembros del equipo</h2>
                <div class="members-grid">
                    <?php foreach ($otherMembers as $other): ?>
                        <a href="<?= _url('/equipo/' . htmlspecialchars($other['slug'])) ?>" class="member-card">
                            <div class="card-photo">
                                <?php if (!empty($other['photo'])): ?>
                                    <img src="<?= htmlspecialchars($other['photo']) ?>"
                                         alt="<?= htmlspecialchars($other['name']) ?>"
                                         class="photo-main">
                                    <?php if (!empty($other['photo_hover'])): ?>
                                        <img src="<?= htmlspecialchars($other['photo_hover']) ?>"
                                             alt="<?= htmlspecialchars($other['name']) ?>"
                                             class="photo-hover">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="photo-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-info">
                                <h3><?= htmlspecialchars($other['name']) ?></h3>
                                <?php if (!empty($other['role'])): ?>
                                    <p class="card-position"><?= htmlspecialchars($other['role']) ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</article>

<style>
/* Member Hero Section */
.member-hero-section {
    padding: calc(var(--spacing-2xl) + var(--header-height)) 0 var(--spacing-3xl);
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
}

/* Breadcrumb */
.page-breadcrumb {
    padding: var(--spacing-md) 0;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.page-breadcrumb .container {
    max-width: 1200px;
}

.breadcrumb-wrapper {
    max-width: 960px;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    flex-wrap: wrap;
}

.breadcrumb a {
    color: var(--color-gray-500);
    text-decoration: none;
    transition: color var(--transition);
}

.breadcrumb a:hover {
    color: var(--color-primary);
}

.breadcrumb span:last-child {
    color: var(--color-gray-700);
    font-weight: 500;
}

.member-hero-content {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: var(--spacing-3xl);
    align-items: start;
}

.member-photo-wrapper {
    position: sticky;
    top: calc(var(--header-height) + var(--spacing-xl));
}

.member-photo {
    position: relative;
    width: 100%;
    padding-top: 100%;
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-xl);
}

.member-photo img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s ease;
}

.member-photo .photo-hover {
    opacity: 0;
}

.member-photo:hover .photo-main {
    opacity: 0;
}

.member-photo:hover .photo-hover {
    opacity: 1;
}

.member-photo-placeholder {
    width: 100%;
    padding-top: 100%;
    background-color: var(--color-gray-200);
    border-radius: var(--radius-xl);
    position: relative;
}

.member-photo-placeholder i {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 80px;
    color: var(--color-gray-400);
}

.member-info h1 {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-sm);
    line-height: 1.2;
    color: var(--color-white);
}

.member-position {
    font-size: var(--font-size-xl);
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    margin-bottom: var(--spacing-xl);
}

.member-description {
    color: rgba(255, 255, 255, 0.85);
    line-height: 1.8;
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-xl);
}

.member-contact {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.contact-link {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) var(--spacing-lg);
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-full);
    color: var(--color-white);
    font-weight: 500;
    transition: all var(--transition);
    backdrop-filter: blur(4px);
}

.contact-link:hover {
    background-color: rgba(255, 255, 255, 0.3);
    color: var(--color-white);
}

.contact-link.linkedin {
    background-color: #0077b5;
    color: white;
}

.contact-link.linkedin:hover {
    background-color: #005885;
}

/* Other Members Section */
.other-members {
    background-color: var(--color-gray-50);
}

.other-members h2 {
    text-align: center;
    font-size: var(--font-size-2xl);
    margin-bottom: var(--spacing-2xl);
}

.members-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-lg);
}

.member-card {
    background-color: var(--color-white);
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
    text-align: center;
}

.member-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
}

.card-photo {
    position: relative;
    width: 100%;
    padding-top: 100%;
    overflow: hidden;
}

.card-photo img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s ease;
}

.card-photo .photo-hover {
    opacity: 0;
}

.member-card:hover .card-photo .photo-main {
    opacity: 0;
}

.member-card:hover .card-photo .photo-hover {
    opacity: 1;
}

.card-photo .photo-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: var(--color-gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-photo .photo-placeholder i {
    font-size: 48px;
    color: var(--color-gray-400);
}

.card-info {
    padding: var(--spacing-lg);
}

.card-info h3 {
    font-size: var(--font-size-md);
    margin-bottom: var(--spacing-xs);
    color: var(--color-dark);
}

.card-position {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
}

/* Responsive */
@media (max-width: 1024px) {
    .member-hero-content {
        grid-template-columns: 250px 1fr;
        gap: var(--spacing-2xl);
    }

    .members-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .member-hero-content {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .member-photo-wrapper {
        position: static;
        max-width: 250px;
        margin: 0 auto;
    }

    .member-info h1 {
        font-size: var(--font-size-3xl);
    }

    .member-contact {
        justify-content: center;
    }

    .members-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .members-grid {
        grid-template-columns: 1fr;
        max-width: 300px;
        margin: 0 auto;
    }
}
</style>
