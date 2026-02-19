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
            <h1>Bienvenido</h1>
            <p class="hero-subtitle">Configura los bloques de esta pagina desde el panel de administracion para personalizar el contenido.</p>
            <div class="hero-actions">
                <a href="/admin" class="btn btn-primary btn-lg">Ir al Admin</a>
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
