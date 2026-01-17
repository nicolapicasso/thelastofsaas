<?php
/**
 * LLM Q&A Section Partial
 * Reusable Q&A section for pages optimized for LLM indexing
 * Omniwallet CMS
 *
 * Expects:
 * - $llmQaItems array with 'question' and 'answer' keys
 * - $llmQaEntityType (optional) entity type for translation (page, post, feature, etc.)
 * - $llmQaEntityId (optional) entity ID for translation
 */

use App\Helpers\TranslationHelper;

if (empty($llmQaItems)) return;

// Apply translations if entity info provided
if (!empty($llmQaEntityType) && !empty($llmQaEntityId)) {
    $translator = TranslationHelper::getInstance();
    $llmQaItems = $translator->translateLlmQaContent($llmQaEntityType, (int)$llmQaEntityId, $llmQaItems);
}

// Filter valid items
$validItems = array_filter($llmQaItems, function($qa) {
    return !empty($qa['question']) && !empty($qa['answer']);
});

if (empty($validItems)) return;
?>

<!-- LLM Q&A Section -->
<section class="llm-qa-section section">
    <div class="container">
        <div class="llm-qa-header">
            <h2><?= __('faq_title') ?></h2>
            <p><?= __('faq_subtitle') ?></p>
        </div>
        <div class="llm-qa-list">
            <?php foreach ($validItems as $index => $qa): ?>
            <div class="llm-qa-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                <button class="llm-qa-question" aria-expanded="false" aria-controls="qa-answer-<?= $index ?>">
                    <span itemprop="name"><?= htmlspecialchars($qa['question']) ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="llm-qa-answer" id="qa-answer-<?= $index ?>" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="llm-qa-answer-content" itemprop="text">
                        <?= nl2br(htmlspecialchars($qa['answer'])) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FAQPage Schema for LLM optimization -->
<script type="application/ld+json">
<?php
$faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_values(array_map(function($qa) {
        return [
            '@type' => 'Question',
            'name' => $qa['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $qa['answer']
            ]
        ];
    }, $validItems))
];
echo json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>
</script>

<style>
.llm-qa-section {
    background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-white) 100%);
}

.llm-qa-header {
    text-align: center;
    margin-bottom: var(--spacing-2xl);
}

.llm-qa-header h2 {
    color: var(--color-dark);
    margin-bottom: var(--spacing-sm);
}

.llm-qa-header p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
}

.llm-qa-list {
    max-width: 800px;
    margin: 0 auto;
}

.llm-qa-item {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: box-shadow var(--transition);
}

.llm-qa-item:hover {
    box-shadow: var(--shadow-md);
}

.llm-qa-question {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-lg);
    background: none;
    border: none;
    text-align: left;
    font-size: var(--font-size-base);
    font-weight: 600;
    color: var(--color-dark);
    cursor: pointer;
    transition: color var(--transition);
}

.llm-qa-question:hover {
    color: var(--color-primary);
}

.llm-qa-question i {
    flex-shrink: 0;
    font-size: 14px;
    color: var(--color-primary);
    transition: transform var(--transition);
}

.llm-qa-item.active .llm-qa-question i {
    transform: rotate(180deg);
}

.llm-qa-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
}

.llm-qa-item.active .llm-qa-answer {
    max-height: 1000px;
}

.llm-qa-answer-content {
    padding: 0 var(--spacing-lg) var(--spacing-lg);
    color: var(--color-gray-600);
    line-height: 1.7;
}
</style>

<script>
(function() {
    document.querySelectorAll('.llm-qa-question').forEach(function(button) {
        button.addEventListener('click', function() {
            const item = this.closest('.llm-qa-item');
            const isActive = item.classList.contains('active');

            // Close all items in this section
            item.closest('.llm-qa-list').querySelectorAll('.llm-qa-item').forEach(function(i) {
                i.classList.remove('active');
                i.querySelector('.llm-qa-question').setAttribute('aria-expanded', 'false');
            });

            // Toggle current
            if (!isActive) {
                item.classList.add('active');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
})();
</script>
