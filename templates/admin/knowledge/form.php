<?php
/**
 * Knowledge Article Form Template
 * Omniwallet CMS
 */
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
$isEdit = isset($article) && $article;
$action = $isEdit ? "/admin/knowledge/{$article['id']}/update" : "/admin/knowledge";
$featuredImage = $article['featured_image'] ?? '';
$hasFeaturedImage = !empty($featuredImage);
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/knowledge" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<form method="POST" action="<?= $action ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="editor-layout">
        <!-- Main Content -->
        <div class="editor-main">
            <!-- Basic Info -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Información básica</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Título *</label>
                        <input type="text" id="title" name="title"
                               value="<?= htmlspecialchars($article['title'] ?? '') ?>"
                               required placeholder="Título del artículo">
                    </div>

                    <div class="form-group">
                        <label for="slug">URL (slug)</label>
                        <div class="input-group">
                            <span class="input-prefix">/ayuda/</span>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($article['slug'] ?? '') ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="excerpt">Extracto</label>
                        <textarea id="excerpt" name="excerpt" rows="2"
                                  placeholder="Breve descripción del artículo"><?= htmlspecialchars($article['excerpt'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tags">Etiquetas</label>
                        <input type="text" id="tags" name="tags"
                               value="<?= htmlspecialchars($article['tags'] ?? '') ?>"
                               placeholder="Separadas por comas: pagos, facturación, soporte">
                        <small class="form-hint">Ayudan a encontrar el artículo en búsquedas</small>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Contenido</h3>
                </div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom: 0;">
                        <?php
                        $editorId = 'article-content';
                        $editorName = 'content';
                        $editorContent = $article['content'] ?? '';
                        $editorRows = 25;
                        $editorPlaceholder = 'Escribe el contenido del artículo...';
                        include TEMPLATES_PATH . '/admin/partials/wysiwyg-editor.php';
                        ?>
                    </div>
                </div>
            </div>

            <!-- Media -->
            <div class="editor-card">
                <div class="card-header collapsible" onclick="toggleCard(this)">
                    <h3>Multimedia</h3>
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Imagen principal</label>
                        <div class="image-picker-field">
                            <input type="hidden" name="featured_image" value="<?= htmlspecialchars($featuredImage) ?>">
                            <div class="image-picker-preview <?= $hasFeaturedImage ? 'has-image' : '' ?>">
                                <?php if ($hasFeaturedImage): ?>
                                    <img src="<?= htmlspecialchars($featuredImage) ?>" alt="Preview">
                                <?php else: ?>
                                    <div class="preview-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>Sin imagen</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="image-picker-actions">
                                <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                    <i class="fas fa-upload"></i> <?= $hasFeaturedImage ? 'Cambiar' : 'Seleccionar' ?>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= $hasFeaturedImage ? 'flex' : 'none' ?>;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="video_url">Video tutorial</label>
                        <input type="url" id="video_url" name="video_url"
                               value="<?= htmlspecialchars($article['video_url'] ?? '') ?>"
                               placeholder="https://youtube.com/watch?v=...">
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="editor-card">
                <div class="card-header collapsible" onclick="toggleCard(this)">
                    <h3>SEO</h3>
                    <div class="card-header-actions">
                        <?php if ($isEdit): ?>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); generateSEO('knowledge', <?= $article['id'] ?>)" title="Generar SEO con IA">
                            <i class="fas fa-magic"></i> Generar
                        </button>
                        <a href="/admin/seo/edit?type=knowledge&id=<?= $article['id'] ?>" class="btn btn-sm btn-outline" onclick="event.stopPropagation()" title="Editar SEO avanzado">
                            <i class="fas fa-cog"></i>
                        </a>
                        <?php endif; ?>
                        <i class="fas fa-chevron-down collapse-icon"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta título</label>
                        <input type="text" id="meta_title" name="meta_title"
                               value="<?= htmlspecialchars($article['meta_title'] ?? '') ?>"
                               placeholder="Título para buscadores">
                    </div>
                    <div class="form-group">
                        <label for="meta_description">Meta descripción</label>
                        <textarea id="meta_description" name="meta_description" rows="2"
                                  placeholder="Descripción para buscadores"><?= htmlspecialchars($article['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- LLM Q&A -->
            <?php
            $entity = $article ?? [];
            $entityType = 'knowledge_article';
            $entityId = $article['id'] ?? null;
            include TEMPLATES_PATH . '/admin/partials/llm-qa-editor.php';
            ?>
        </div>

        <!-- Sidebar -->
        <div class="editor-sidebar">
            <!-- Publish -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Publicar</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="status">Estado</label>
                        <select id="status" name="status">
                            <option value="draft" <?= ($article['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Borrador</option>
                            <option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option>
                            <option value="archived" <?= ($article['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archivado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Categoría</label>
                        <select id="category_id" name="category_id">
                            <option value="">Sin categoría</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= ($article['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="<?= $article['sort_order'] ?? 0 ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= ($article['is_featured'] ?? false) ? 'checked' : '' ?>>
                            <span><i class="fas fa-star"></i> Artículo destacado</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear artículo' ?>
                    </button>
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
                                <span class="meta-value"><?= $article['id'] ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Vistas</span>
                                <span class="meta-value"><?= number_format($article['view_count'] ?? 0) ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Creado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($article['created_at'])) ?></span>
                            </div>
                            <?php if ($article['updated_at']): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Actualizado</span>
                                    <span class="meta-value"><?= date('d/m/Y H:i', strtotime($article['updated_at'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<script>
function toggleCard(header) {
    const body = header.nextElementSibling;
    const icon = header.querySelector('.collapse-icon');
    body.classList.toggle('collapsed');
    icon.classList.toggle('rotated');
}

// Auto-generate slug
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
            alert('SEO generado correctamente. Revisa los campos y guarda el articulo.');
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
