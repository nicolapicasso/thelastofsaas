<?php
/**
 * FAQ Block Template
 * Omniwallet CMS
 */
use App\Models\FAQ;
use App\Services\SEOService;
use App\Helpers\TranslationHelper;

$faqModel = new FAQ();

$group = $settings['faq_group'] ?? null;
$limit = (int)($settings['limit'] ?? 10);
$layout = $settings['layout'] ?? 'accordion';

$faqs = $group
    ? $faqModel->getByGroup($group, $limit)
    : $faqModel->getActive($limit);

// Translate FAQs
$translator = TranslationHelper::getInstance();
$translator->translateEntities('faq', $faqs);

// Add FAQ Schema
if (!empty($faqs)) {
    $schemaScript = '<script type="application/ld+json">' . json_encode(SEOService::getFAQSchema($faqs), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

$blockId = 'faq-block-' . uniqid();
?>

<section class="block block-faq block-faq-<?= $layout ?> section <?= $renderer->getBlockClasses($block, $settings) ?>"
         id="<?= $blockId ?>"
         style="<?= $renderer->getBlockStyles($settings) ?>">
    <div class="container">
        <?php if (!empty($content['title']) || !empty($content['subtitle'])): ?>
            <div class="section-header">
                <?php if (!empty($content['title'])): ?>
                    <h2><?= htmlspecialchars($content['title']) ?></h2>
                <?php endif; ?>
                <?php if (!empty($content['subtitle'])): ?>
                    <p><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="faq-list">
            <?php foreach ($faqs as $faq): ?>
                <?php if ($layout === 'list'): ?>
                    <!-- List Layout - Always expanded -->
                    <div class="faq-item expanded">
                        <h3 class="faq-question-text">
                            <?= htmlspecialchars($faq['question']) ?>
                        </h3>
                        <div class="faq-answer-content">
                            <?= $faq['answer'] ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Accordion Layout -->
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false" type="button">
                            <span><?= htmlspecialchars($faq['question']) ?></span>
                            <i class="fas fa-plus"></i>
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                <?= $faq['answer'] ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($content['cta_text']) && !empty($content['cta_url'])): ?>
            <div class="text-center mt-xl">
                <a href="<?= htmlspecialchars($content['cta_url']) ?>" class="btn btn-primary">
                    <?= htmlspecialchars($content['cta_text']) ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $schemaScript ?? '' ?>

<style>
/* FAQ Block - Common Styles */
.block-faq .faq-list {
    max-width: 800px;
    margin: 0 auto;
}

.block-faq .faq-item {
    background-color: var(--color-white);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

/* Accordion Layout */
.block-faq-accordion .faq-question {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-lg);
    background: none;
    border: none;
    font-size: var(--font-size-base);
    font-weight: 600;
    text-align: left;
    cursor: pointer;
    transition: all var(--transition);
    color: inherit;
}

.block-faq-accordion .faq-question:hover {
    color: var(--color-primary);
}

.block-faq-accordion .faq-question i {
    flex-shrink: 0;
    transition: transform 0.3s ease;
    color: var(--color-primary);
    font-size: 16px;
}

.block-faq-accordion .faq-item.active .faq-question i {
    transform: rotate(45deg);
}

.block-faq-accordion .faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.block-faq-accordion .faq-item.active .faq-answer {
    max-height: 1000px;
}

.block-faq-accordion .faq-answer-content {
    padding: 0 var(--spacing-lg) var(--spacing-lg);
    color: var(--color-gray-600);
    line-height: 1.7;
}

/* List Layout - Always Expanded */
.block-faq-list .faq-item {
    padding: var(--spacing-lg);
}

.block-faq-list .faq-question-text {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--spacing-md);
    color: var(--color-dark);
}

.block-faq-list .faq-answer-content {
    color: var(--color-gray-600);
    line-height: 1.7;
}

/* Common Content Styles */
.block-faq .faq-answer-content p {
    margin-bottom: var(--spacing-md);
}

.block-faq .faq-answer-content p:last-child {
    margin-bottom: 0;
}

.block-faq .faq-answer-content ul,
.block-faq .faq-answer-content ol {
    margin-bottom: var(--spacing-md);
    padding-left: var(--spacing-lg);
}

.block-faq .faq-answer-content li {
    margin-bottom: var(--spacing-xs);
}
</style>
