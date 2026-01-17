<?php
/**
 * 404 Error Page Template
 * Omniwallet CMS
 */
?>

<section class="error-page">
    <div class="container">
        <div class="error-content">
            <div class="error-code">404</div>
            <h1>Página no encontrada</h1>
            <p>Lo sentimos, la página que buscas no existe o ha sido movida.</p>

            <div class="error-actions">
                <a href="<?= _url('/') ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i> <?= __('go_home') ?>
                </a>
                <a href="<?= _url('/contacto') ?>" class="btn btn-outline">
                    <i class="fas fa-envelope"></i> <?= __('contact') ?>
                </a>
            </div>

            <div class="error-suggestions">
                <h3><?= __('what_can_you_do') ?></h3>
                <ul>
                    <li><?= __('check_url') ?></li>
                    <li><?= __('go_back_to') ?> <a href="<?= _url('/') ?>"><?= __('main_page') ?></a></li>
                    <li><?= __('use_navigation_menu') ?></li>
                    <li><?= __('contact_if_problem') ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<style>
.error-page {
    min-height: calc(100vh - var(--header-height) - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-3xl) 0;
    text-align: center;
}

.error-content {
    max-width: 600px;
}

.error-code {
    font-size: 120px;
    font-weight: 700;
    line-height: 1;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: var(--spacing-md);
}

.error-page h1 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-md);
}

.error-page > .container > .error-content > p {
    font-size: var(--font-size-lg);
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-xl);
}

.error-actions {
    display: flex;
    justify-content: center;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-2xl);
}

.error-actions .btn i {
    margin-right: var(--spacing-sm);
}

.error-suggestions {
    background-color: var(--color-gray-50);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    text-align: left;
}

.error-suggestions h3 {
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-md);
}

.error-suggestions ul {
    list-style: none;
}

.error-suggestions li {
    position: relative;
    padding-left: var(--spacing-lg);
    margin-bottom: var(--spacing-sm);
    color: var(--color-gray-600);
}

.error-suggestions li::before {
    content: '→';
    position: absolute;
    left: 0;
    color: var(--color-primary);
}

.error-suggestions a {
    color: var(--color-primary);
    text-decoration: underline;
}

@media (max-width: 768px) {
    .error-code {
        font-size: 80px;
    }

    .error-page h1 {
        font-size: var(--font-size-2xl);
    }

    .error-actions {
        flex-direction: column;
    }
}
</style>
