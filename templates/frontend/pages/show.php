<?php
/**
 * Dynamic Page Template
 * Renders pages built with blocks
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

<?php if (empty($blocks)): ?>
    <!-- Fallback for pages without blocks -->
    <section class="page-content section">
        <div class="container">
            <h1><?= htmlspecialchars($page['title']) ?></h1>
            <?php if (!empty($page['content'])): ?>
                <div class="content">
                    <?= $page['content'] ?>
                </div>
            <?php endif; ?>
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
.page-content {
    padding-top: calc(var(--spacing-3xl) + var(--header-height));
}

.page-content h1 {
    margin-bottom: var(--spacing-xl);
}

.page-content .content {
    max-width: 800px;
    line-height: 1.8;
    color: var(--color-gray-700);
}

.page-content .content h2 {
    margin-top: var(--spacing-2xl);
    margin-bottom: var(--spacing-md);
}

.page-content .content h3 {
    margin-top: var(--spacing-xl);
    margin-bottom: var(--spacing-md);
}

.page-content .content p {
    margin-bottom: var(--spacing-md);
}

.page-content .content ul,
.page-content .content ol {
    margin-bottom: var(--spacing-md);
    padding-left: var(--spacing-xl);
}

.page-content .content li {
    margin-bottom: var(--spacing-sm);
}

.page-content .content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-lg);
    margin: var(--spacing-lg) 0;
}

.page-content .content blockquote {
    border-left: 4px solid var(--color-primary);
    padding-left: var(--spacing-lg);
    margin: var(--spacing-lg) 0;
    font-style: italic;
    color: var(--color-gray-600);
}
</style>
