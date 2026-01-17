<?php
/**
 * Translation Edit Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/translations" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<form method="POST" action="/admin/translations/<?= $translation['id'] ?>/update">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="editor-layout">
        <div class="editor-main">
            <!-- Info -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Información</h3>
                </div>
                <div class="card-body">
                    <div class="translation-info">
                        <div class="info-row">
                            <span class="info-label">Tipo</span>
                            <span class="info-value">
                                <span class="badge badge-info"><?= $entityTypes[$translation['entity_type']] ?? $translation['entity_type'] ?></span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">ID Entidad</span>
                            <span class="info-value"><?= $translation['entity_id'] ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Campo</span>
                            <span class="info-value"><code><?= htmlspecialchars($translation['field_name']) ?></code></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Idioma</span>
                            <span class="info-value">
                                <span class="badge badge-secondary"><?= $languages[$translation['language']] ?? $translation['language'] ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Original Content -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Contenido Original (Español)</h3>
                </div>
                <div class="card-body">
                    <div class="original-content">
                        <?= nl2br(htmlspecialchars($translation['original_content'])) ?>
                    </div>
                </div>
            </div>

            <!-- Translation -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Traducción (<?= $languages[$translation['language']] ?? $translation['language'] ?>)</h3>
                </div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom: 0;">
                        <textarea name="translated_content" rows="10" class="rich-editor"
                                  required><?= htmlspecialchars($translation['translated_content']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="editor-sidebar">
            <!-- Actions -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Acciones</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_approved" value="1" <?= $translation['is_approved'] ? 'checked' : '' ?>>
                            <span><i class="fas fa-check-circle"></i> Traducción aprobada</span>
                        </label>
                    </div>

                    <?php if ($translation['is_auto_translated'] && !$translation['is_approved']): ?>
                        <div class="info-box warning">
                            <i class="fas fa-robot"></i>
                            <span>Esta traducción fue generada automáticamente y está pendiente de revisión.</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Guardar cambios
                    </button>
                </div>
            </div>

            <!-- Meta -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Metadatos</h3>
                </div>
                <div class="card-body">
                    <div class="meta-list">
                        <div class="meta-item">
                            <span class="meta-label">ID</span>
                            <span class="meta-value"><?= $translation['id'] ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Creado</span>
                            <span class="meta-value"><?= date('d/m/Y H:i', strtotime($translation['created_at'])) ?></span>
                        </div>
                        <?php if ($translation['updated_at']): ?>
                            <div class="meta-item">
                                <span class="meta-label">Actualizado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($translation['updated_at'])) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="meta-item">
                            <span class="meta-label">Auto-traducido</span>
                            <span class="meta-value"><?= $translation['is_auto_translated'] ? 'Sí' : 'No' ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.translation-info {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid var(--color-gray-100);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 13px;
    color: var(--color-gray-500);
}

.info-value {
    font-size: 14px;
    color: var(--color-gray-700);
}

.original-content {
    background-color: var(--color-gray-50);
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    font-size: 14px;
    line-height: 1.6;
    color: var(--color-gray-700);
    max-height: 300px;
    overflow-y: auto;
}

.info-box.warning {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-sm);
    background-color: #fef3c7;
    color: #92400e;
}

.info-box.warning i {
    margin-top: 2px;
}
</style>
