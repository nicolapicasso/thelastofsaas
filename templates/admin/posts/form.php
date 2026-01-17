<?php
/**
 * Post Form Template
 * Omniwallet CMS
 */
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
$isEdit = isset($post) && $post;
$action = $isEdit ? "/admin/posts/{$post['id']}" : "/admin/posts";
$heroImage = $post['hero_image'] ?? '';
$thumbnail = $post['thumbnail'] ?? '';
$hasHeroImage = !empty($heroImage);
$hasThumbnail = !empty($thumbnail);
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/posts" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<form method="POST" action="<?= $action ?>" class="post-form">
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
                               value="<?= htmlspecialchars($post['title'] ?? '') ?>"
                               required placeholder="Título del artículo">
                    </div>

                    <div class="form-group">
                        <label for="slug">URL (slug)</label>
                        <div class="input-group">
                            <span class="input-prefix">/blog/</span>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($post['slug'] ?? '') ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                        <small class="form-hint">Déjalo vacío para generar automáticamente</small>
                    </div>

                    <div class="form-group">
                        <label for="subtitle">Subtítulo</label>
                        <input type="text" id="subtitle" name="subtitle"
                               value="<?= htmlspecialchars($post['subtitle'] ?? '') ?>"
                               placeholder="Subtítulo opcional">
                    </div>

                    <div class="form-group">
                        <label for="excerpt">Extracto</label>
                        <textarea id="excerpt" name="excerpt" rows="3"
                                  placeholder="Breve descripción del artículo (se muestra en listados)"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Contenido</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Contenido del artículo</label>
                        <?php
                        $editorId = 'post-content';
                        $editorName = 'content';
                        $editorContent = $post['content'] ?? '';
                        $editorRows = 20;
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
                    <div class="form-row">
                        <div class="form-group">
                            <label>Imagen principal</label>
                            <div class="image-picker-field">
                                <input type="hidden" name="hero_image" value="<?= htmlspecialchars($heroImage) ?>">
                                <div class="image-picker-preview <?= $hasHeroImage ? 'has-image' : '' ?>">
                                    <?php if ($hasHeroImage): ?>
                                        <img src="<?= htmlspecialchars($heroImage) ?>" alt="Preview">
                                    <?php else: ?>
                                        <div class="preview-placeholder">
                                            <i class="fas fa-image"></i>
                                            <span>Sin imagen</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="image-picker-actions">
                                    <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                        <i class="fas fa-upload"></i> <?= $hasHeroImage ? 'Cambiar' : 'Seleccionar' ?>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= $hasHeroImage ? 'flex' : 'none' ?>;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <small class="form-hint">Imagen destacada del artículo (1200x630px recomendado)</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Miniatura</label>
                            <div class="image-picker-field">
                                <input type="hidden" name="thumbnail" value="<?= htmlspecialchars($thumbnail) ?>">
                                <div class="image-picker-preview <?= $hasThumbnail ? 'has-image' : '' ?>">
                                    <?php if ($hasThumbnail): ?>
                                        <img src="<?= htmlspecialchars($thumbnail) ?>" alt="Preview">
                                    <?php else: ?>
                                        <div class="preview-placeholder">
                                            <i class="fas fa-image"></i>
                                            <span>Sin imagen</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="image-picker-actions">
                                    <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                        <i class="fas fa-upload"></i> <?= $hasThumbnail ? 'Cambiar' : 'Seleccionar' ?>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= $hasThumbnail ? 'flex' : 'none' ?>;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <small class="form-hint">Imagen para listados (400x300px recomendado)</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="video_url">URL de vídeo</label>
                        <input type="url" id="video_url" name="video_url"
                               value="<?= htmlspecialchars($post['video_url'] ?? '') ?>"
                               placeholder="https://youtube.com/watch?v=...">
                        <small class="form-hint">YouTube o Vimeo</small>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="editor-card">
                <div class="card-header collapsible" onclick="toggleCard(this)">
                    <h3>SEO</h3>
                    <div class="card-header-actions">
                        <?php if ($isEdit): ?>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); generateSEO('post', <?= $post['id'] ?>)" title="Generar SEO con IA">
                            <i class="fas fa-magic"></i> Generar
                        </button>
                        <a href="/admin/seo/edit?type=post&id=<?= $post['id'] ?>" class="btn btn-sm btn-outline" onclick="event.stopPropagation()" title="Editar SEO avanzado">
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
                               value="<?= htmlspecialchars($post['meta_title'] ?? '') ?>"
                               placeholder="Título para buscadores (max 60 caracteres)"
                               maxlength="70">
                        <small class="form-hint">Déjalo vacío para usar el título del post</small>
                    </div>
                    <div class="form-group">
                        <label for="meta_description">Meta descripción</label>
                        <textarea id="meta_description" name="meta_description" rows="2"
                                  placeholder="Descripción para buscadores (max 160 caracteres)"
                                  maxlength="170"><?= htmlspecialchars($post['meta_description'] ?? '') ?></textarea>
                        <small class="form-hint">Déjalo vacío para usar el extracto</small>
                    </div>
                </div>
            </div>

            <!-- LLM Q&A -->
            <?php
            $entity = $post ?? [];
            $entityType = 'post';
            $entityId = $post['id'] ?? null;
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
                            <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Borrador</option>
                            <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option>
                            <option value="archived" <?= ($post['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archivado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Categoría</label>
                        <select id="category_id" name="category_id">
                            <option value="">Sin categoría</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= ($post['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= ($post['is_featured'] ?? false) ? 'checked' : '' ?>>
                            <span><i class="fas fa-star"></i> Post destacado</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear post' ?>
                    </button>
                    <?php if ($isEdit): ?>
                        <a href="/admin/posts/create" class="btn btn-outline btn-block">
                            <i class="fas fa-plus"></i> Crear nuevo
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
                                <span class="meta-value"><?= $post['id'] ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Creado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></span>
                            </div>
                            <?php if ($post['updated_at']): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Actualizado</span>
                                    <span class="meta-value"><?= date('d/m/Y H:i', strtotime($post['updated_at'])) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($post['published_at']): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Publicado</span>
                                    <span class="meta-value"><?= date('d/m/Y H:i', strtotime($post['published_at'])) ?></span>
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
</script>
