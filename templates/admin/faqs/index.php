<?php
/**
 * FAQs List Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Preguntas Frecuentes</h1>
        <p>Gestiona las FAQs del sitio</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/faqs/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva FAQ
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Filters -->
<?php if (!empty($categories)): ?>
    <div class="card filters-card">
        <form method="GET" action="/admin/faqs" class="filters-form">
            <div class="filter-group">
                <label>Categoría</label>
                <select name="category" onchange="this.form.submit()">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= ($currentCategory ?? '') == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!empty($currentCategory)): ?>
                <a href="/admin/faqs" class="btn btn-outline btn-sm">Limpiar filtro</a>
            <?php endif; ?>
        </form>
    </div>
<?php endif; ?>

<!-- FAQs List -->
<div class="card">
    <?php if (empty($faqs)): ?>
        <div class="empty-state">
            <i class="fas fa-question-circle"></i>
            <h3>No hay FAQs</h3>
            <p>Añade las preguntas frecuentes de tu sitio</p>
            <a href="/admin/faqs/create" class="btn btn-primary">Nueva FAQ</a>
        </div>
    <?php else: ?>
        <div class="faq-list">
            <?php foreach ($faqs as $faq): ?>
                <div class="faq-item <?= $faq['is_active'] ? '' : 'inactive' ?>">
                    <div class="faq-handle">
                        <span class="sort-badge"><?= $faq['sort_order'] ?></span>
                    </div>
                    <div class="faq-content">
                        <div class="faq-question">
                            <strong><?= htmlspecialchars($faq['question']) ?></strong>
                            <?php if (!empty($faq['faq_group'])): ?>
                                <span class="badge badge-primary"><?= htmlspecialchars($faq['faq_group']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($faq['category_name'])): ?>
                                <span class="badge badge-info"><?= htmlspecialchars($faq['category_name']) ?></span>
                            <?php endif; ?>
                            <?php if (!$faq['is_active']): ?>
                                <span class="badge badge-secondary">Inactiva</span>
                            <?php endif; ?>
                        </div>
                        <div class="faq-answer">
                            <?= htmlspecialchars(strip_tags(substr($faq['answer'], 0, 150))) ?>...
                        </div>
                    </div>
                    <div class="faq-actions">
                        <a href="/admin/faqs/<?= $faq['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="/admin/faqs/<?= $faq['id'] ?>/delete" class="inline-form"
                              onsubmit="return confirm('¿Eliminar esta FAQ?')">
                            <input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
                            <button type="submit" class="btn btn-sm btn-outline btn-danger" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.faq-list {
    padding: var(--spacing-md);
}

.faq-item {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-md);
    padding: var(--spacing-md);
    background-color: var(--color-gray-50);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-sm);
    transition: all var(--transition);
}

.faq-item:hover {
    background-color: white;
    box-shadow: var(--shadow-sm);
}

.faq-item.inactive {
    opacity: 0.6;
}

.faq-handle {
    flex-shrink: 0;
}

.faq-content {
    flex: 1;
    min-width: 0;
}

.faq-question {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
    margin-bottom: var(--spacing-xs);
}

.faq-question strong {
    color: var(--color-dark);
}

.faq-answer {
    font-size: 13px;
    color: var(--color-gray-600);
    line-height: 1.5;
}

.faq-actions {
    display: flex;
    gap: var(--spacing-xs);
    flex-shrink: 0;
}

.sort-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background-color: var(--color-gray-200);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    color: var(--color-gray-600);
}
</style>
