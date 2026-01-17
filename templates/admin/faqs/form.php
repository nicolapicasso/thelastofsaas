<?php
/**
 * FAQ Form Template
 * Omniwallet CMS
 */
$isEdit = isset($faq) && $faq;
$action = $isEdit ? "/admin/faqs/{$faq['id']}" : "/admin/faqs";
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/faqs" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<form method="POST" action="<?= $action ?>" class="faq-form">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="editor-layout">
        <!-- Main Content -->
        <div class="editor-main">
            <!-- Question & Answer -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Pregunta y Respuesta</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="question">Pregunta *</label>
                        <input type="text" id="question" name="question"
                               value="<?= htmlspecialchars($faq['question'] ?? '') ?>"
                               required placeholder="¿Cómo puedo...?">
                    </div>

                    <div class="form-group">
                        <label for="answer">Respuesta *</label>
                        <textarea id="answer" name="answer" rows="10"
                                  class="rich-editor"
                                  required placeholder="Escribe la respuesta a la pregunta..."><?= htmlspecialchars($faq['answer'] ?? '') ?></textarea>
                        <small class="form-hint">Puedes usar HTML para formatear la respuesta</small>
                    </div>
                </div>
            </div>

            <!-- Schema.org Info -->
            <div class="editor-card">
                <div class="card-header">
                    <h3><i class="fas fa-code"></i> SEO - Schema.org</h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <p><strong>FAQPage Schema:</strong> Las FAQs se exportan automáticamente con el formato Schema.org FAQPage, lo que mejora la visibilidad en los resultados de búsqueda de Google.</p>
                        <p class="mt-sm">El schema se genera automáticamente basándose en la pregunta y respuesta.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="editor-sidebar">
            <!-- Settings -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Configuración</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="faq_group">Grupo</label>
                        <input type="text" id="faq_group" name="faq_group"
                               value="<?= htmlspecialchars($faq['faq_group'] ?? '') ?>"
                               placeholder="Ej: General, Pagos, Soporte..."
                               list="group-suggestions">
                        <datalist id="group-suggestions">
                            <?php if (!empty($groups)): ?>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?= htmlspecialchars($group) ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                        <small class="form-hint">Para agrupar FAQs relacionadas</small>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="<?= $faq['sort_order'] ?? 0 ?>"
                               min="0" step="1">
                        <small class="form-hint">Menor número = primero</small>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" <?= ($faq['is_active'] ?? true) ? 'checked' : '' ?>>
                            <span>FAQ activa</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear FAQ' ?>
                    </button>
                    <?php if ($isEdit): ?>
                        <a href="/admin/faqs/create" class="btn btn-outline btn-block">
                            <i class="fas fa-plus"></i> Crear nueva
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info -->
            <?php if ($isEdit): ?>
                <div class="editor-card">
                    <div class="card-header">
                        <h3>Información</h3>
                    </div>
                    <div class="card-body">
                        <div class="meta-list">
                            <div class="meta-item">
                                <span class="meta-label">ID</span>
                                <span class="meta-value"><?= $faq['id'] ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Creado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($faq['created_at'])) ?></span>
                            </div>
                            <?php if ($faq['updated_at']): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Actualizado</span>
                                    <span class="meta-value"><?= date('d/m/Y H:i', strtotime($faq['updated_at'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Add -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Añadir más FAQs</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted text-sm">Después de guardar, puedes añadir más FAQs rápidamente.</p>
                </div>
                <div class="card-footer">
                    <button type="submit" name="add_another" value="1" class="btn btn-outline btn-block">
                        <i class="fas fa-plus"></i> Guardar y crear otra
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.mt-sm {
    margin-top: var(--spacing-sm);
}
.text-sm {
    font-size: 13px;
}
</style>
