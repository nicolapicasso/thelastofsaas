<?php
/**
 * Home Page Template
 * Omniwallet CMS
 */

// Parse LLM Q&A content
$llmQaItems = [];
if (!empty($page['enable_llm_qa']) && !empty($page['llm_qa_content'])) {
    $llmQaItems = json_decode($page['llm_qa_content'], true) ?: [];
}
?>

<!-- Render dynamic blocks -->
<?= $renderedBlocks ?? '' ?>

<!-- Default content if no blocks -->
<?php if (empty($blocks)): ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1>La Plataforma de Pagos que Impulsa tu Negocio</h1>
            <p class="hero-subtitle">Omniwallet te ofrece las herramientas para gestionar pagos, automatizar procesos financieros y ofrecer una experiencia excepcional a tus clientes.</p>
            <div class="hero-actions">
                <a href="/registro" class="btn btn-primary btn-lg">Empezar Gratis</a>
                <a href="/demo" class="btn btn-outline btn-lg">Ver Demo</a>
            </div>
            <div class="hero-trust">
                <span>Confían en nosotros:</span>
                <div class="trust-logos">
                    <img src="/assets/images/logos/client1.svg" alt="Cliente 1">
                    <img src="/assets/images/logos/client2.svg" alt="Cliente 2">
                    <img src="/assets/images/logos/client3.svg" alt="Cliente 3">
                </div>
            </div>
        </div>
        <div class="hero-image">
            <img src="/assets/images/hero-dashboard.png" alt="Dashboard Omniwallet">
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <h2>Todo lo que necesitas para gestionar tus pagos</h2>
            <p>Una plataforma completa que se adapta a las necesidades de tu negocio</p>
        </div>
        <div class="features-grid grid grid-3">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3>Wallet Digital</h3>
                <p>Permite a tus clientes cargar saldo y pagar de forma rápida y segura.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Analíticas en Tiempo Real</h3>
                <p>Monitoriza tus ventas, transacciones y métricas clave al instante.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-plug"></i>
                </div>
                <h3>Integraciones</h3>
                <p>Conecta con tu ERP, CRM y sistemas de facturación fácilmente.</p>
            </div>
        </div>
        <div class="text-center mt-xl">
            <a href="/funcionalidades" class="btn btn-primary">Ver todas las funcionalidades</a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section bg-primary">
    <div class="container">
        <div class="cta-content">
            <h2>¿Listo para transformar tu negocio?</h2>
            <p>Únete a cientos de empresas que ya confían en Omniwallet</p>
            <div class="cta-actions">
                <a href="/registro" class="btn btn-white btn-lg">Empezar Gratis</a>
                <a href="/contacto" class="btn btn-outline btn-lg" style="border-color: white; color: white;">Contactar Ventas</a>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>

<?php if (!empty($llmQaItems)): ?>
<?php
$llmQaEntityType = 'page';
$llmQaEntityId = $page['id'];
include TEMPLATES_PATH . '/frontend/partials/llm-qa-section.php';
?>
<?php endif; ?>

<style>
/* Hero Section */
.hero-section {
    padding: calc(var(--spacing-3xl) + var(--header-height)) 0 var(--spacing-3xl);
    background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-white) 100%);
}

.hero-section .container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-2xl);
    align-items: center;
}

.hero-content h1 {
    font-size: var(--font-size-5xl);
    margin-bottom: var(--spacing-lg);
    line-height: 1.1;
}

.hero-subtitle {
    font-size: var(--font-size-xl);
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-xl);
}

.hero-actions {
    display: flex;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-2xl);
}

.hero-trust {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    color: var(--color-gray-500);
    font-size: var(--font-size-sm);
}

.trust-logos {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
}

.trust-logos img {
    height: 24px;
    opacity: 0.6;
}

.hero-image img {
    width: 100%;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
}

/* Feature Cards */
.feature-card {
    background-color: var(--color-white);
    padding: var(--spacing-xl);
    border-radius: var(--radius-xl);
    text-align: center;
    box-shadow: var(--shadow-md);
    transition: all var(--transition);
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.feature-icon {
    width: 64px;
    height: 64px;
    background-color: var(--color-primary-light);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--spacing-lg);
}

.feature-icon i {
    font-size: 24px;
    color: var(--color-primary-dark);
}

.feature-card h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-sm);
}

.feature-card p {
    color: var(--color-gray-600);
    margin: 0;
}

/* CTA Section */
.cta-content {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.cta-content h2 {
    color: var(--color-white);
    margin-bottom: var(--spacing-md);
}

.cta-content p {
    color: rgba(255, 255, 255, 0.9);
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-xl);
}

.cta-actions {
    display: flex;
    justify-content: center;
    gap: var(--spacing-md);
}

/* Utilities */
.text-center { text-align: center; }
.mt-xl { margin-top: var(--spacing-xl); }

@media (max-width: 768px) {
    .hero-section .container {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .hero-actions {
        justify-content: center;
        flex-wrap: wrap;
    }

    .hero-trust {
        flex-direction: column;
    }

    .hero-image {
        order: -1;
    }

    .cta-actions {
        flex-direction: column;
    }
}
</style>
