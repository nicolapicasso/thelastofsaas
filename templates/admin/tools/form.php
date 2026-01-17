<?php
/**
 * Tool Form Template
 * We're Sinapsis CMS
 */
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
$isEdit = isset($tool) && $tool;
$action = $isEdit ? "/admin/tools/{$tool['id']}" : "/admin/tools";
$logoImage = $tool['logo'] ?? '';
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/tools" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<form method="POST" action="<?= $action ?>" class="tool-form">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="editor-layout">
        <!-- Main Content -->
        <div class="editor-main">
            <!-- Basic Info -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Informacion basica</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Nombre de la herramienta *</label>
                            <input type="text" id="title" name="title"
                                   value="<?= htmlspecialchars($tool['title'] ?? '') ?>"
                                   required placeholder="React, Docker, AWS...">
                        </div>

                        <div class="form-group">
                            <label for="subtitle">Subtitulo</label>
                            <input type="text" id="subtitle" name="subtitle"
                                   value="<?= htmlspecialchars($tool['subtitle'] ?? '') ?>"
                                   placeholder="Breve descripcion">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug">URL (slug)</label>
                        <div class="input-group">
                            <span class="input-prefix">/herramientas/</span>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($tool['slug'] ?? '') ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                        <small class="form-hint">Dejalo vacio para generar automaticamente</small>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripcion</label>
                        <?php
                        $editorId = 'description';
                        $editorName = 'description';
                        $editorContent = $tool['description'] ?? '';
                        $editorRows = 10;
                        $editorPlaceholder = 'Describe la herramienta, sus caracteristicas y como la utilizamos...';
                        include TEMPLATES_PATH . '/admin/partials/wysiwyg-editor.php';
                        ?>
                    </div>

                    <div class="form-group">
                        <label for="platform_url">Sitio web oficial</label>
                        <input type="url" id="platform_url" name="platform_url"
                               value="<?= htmlspecialchars($tool['platform_url'] ?? '') ?>"
                               placeholder="https://...">
                    </div>
                </div>
            </div>

            <!-- Logo -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Logo</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Logo de la herramienta</label>
                        <div class="image-picker-field">
                            <input type="hidden" name="logo" value="<?= htmlspecialchars($logoImage) ?>">
                            <div class="image-picker-preview <?= !empty($logoImage) ? 'has-image' : '' ?>">
                                <?php if (!empty($logoImage)): ?>
                                    <img src="<?= htmlspecialchars($logoImage) ?>" alt="Logo">
                                <?php else: ?>
                                    <div class="preview-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>Sin logo</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="image-picker-actions">
                                <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                    <i class="fas fa-upload"></i> <?= !empty($logoImage) ? 'Cambiar' : 'Seleccionar' ?>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= !empty($logoImage) ? 'flex' : 'none' ?>;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <small class="form-hint">Logo de la herramienta (preferiblemente PNG o SVG)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>SEO</h3>
                    <div class="card-header-actions">
                        <?php if ($isEdit): ?>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="generateSEO('tool', <?= $tool['id'] ?>)" title="Generar SEO con IA">
                            <i class="fas fa-magic"></i> Generar
                        </button>
                        <a href="/admin/seo/edit?type=tool&id=<?= $tool['id'] ?>" class="btn btn-sm btn-outline" title="Editar SEO avanzado y traducciones">
                            <i class="fas fa-cog"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta titulo</label>
                        <input type="text" id="meta_title" name="meta_title"
                               value="<?= htmlspecialchars($tool['meta_title'] ?? '') ?>"
                               placeholder="Titulo para buscadores (opcional)">
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta descripcion</label>
                        <textarea id="meta_description" name="meta_description" rows="3"
                                  placeholder="Descripcion para buscadores (opcional)"><?= htmlspecialchars($tool['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- LLM Q&A -->
            <?php
            $entity = $tool ?? [];
            $entityType = 'tool';
            $entityId = $tool['id'] ?? null;
            include TEMPLATES_PATH . '/admin/partials/llm-qa-editor.php';
            ?>
        </div>

        <!-- Sidebar -->
        <div class="editor-sidebar">
            <!-- Publish -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Publicacion</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" <?= ($tool['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <span><i class="fas fa-eye"></i> Activo (visible en el sitio)</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Categoria</label>
                        <select id="category_id" name="category_id">
                            <option value="">Sin categoria</option>
                            <?php foreach ($categories ?? [] as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($tool['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="<?= $tool['sort_order'] ?? 0 ?>"
                               min="0" step="1">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= ($tool['is_featured'] ?? false) ? 'checked' : '' ?>>
                            <span><i class="fas fa-star"></i> Herramienta destacada</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear herramienta' ?>
                    </button>
                    <?php if ($isEdit): ?>
                        <a href="/herramientas/<?= $tool['slug'] ?>" target="_blank" class="btn btn-outline btn-block">
                            <i class="fas fa-eye"></i> Ver pagina
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info -->
            <?php if ($isEdit): ?>
                <div class="editor-card">
                    <div class="card-header">
                        <h3>Informacion</h3>
                    </div>
                    <div class="card-body">
                        <div class="meta-list">
                            <div class="meta-item">
                                <span class="meta-label">ID</span>
                                <span class="meta-value"><?= $tool['id'] ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Creado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($tool['created_at'])) ?></span>
                            </div>
                            <?php if ($tool['updated_at']): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Actualizado</span>
                                    <span class="meta-value"><?= date('d/m/Y H:i', strtotime($tool['updated_at'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<style>
.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-lg);
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function toggleCard(header) {
    const body = header.nextElementSibling;
    const icon = header.querySelector('.collapse-icon');
    body.classList.toggle('collapsed');
    icon.classList.toggle('rotated');
}

// Auto-generate slug from title
document.getElementById('title').addEventListener('blur', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value && this.value) {
        slugInput.value = this.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    }
});

// SEO Generation
function generateSEO(entityType, entityId) {
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

    fetch('/admin/seo/generate-single', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            entity_type: entityType,
            entity_id: entityId,
            language: 'es',
            overwrite: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            if (data.data.meta_title) {
                document.getElementById('meta_title').value = data.data.meta_title;
            }
            if (data.data.meta_description) {
                document.getElementById('meta_description').value = data.data.meta_description;
            }
            alert('SEO generado correctamente. Revisa los campos y guarda la herramienta.');
        } else {
            alert('Error: ' + (data.message || 'No se pudo generar el SEO'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexion al generar SEO');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>
