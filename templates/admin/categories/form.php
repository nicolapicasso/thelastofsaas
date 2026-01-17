<?php
/**
 * Category Form Template
 * We're Sinapsis CMS
 */
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
$isEdit = isset($category) && $category;
$action = $isEdit ? "/admin/categories/{$category['id']}" : "/admin/categories";
$featuredImage = $category['featured_image'] ?? '';
$iconImage = $category['icon_image'] ?? '';
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/categories" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
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
                    <h3>Informacion basica</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nombre *</label>
                        <input type="text" id="name" name="name"
                               value="<?= htmlspecialchars($category['name'] ?? '') ?>"
                               required placeholder="Ej: Tecnologia, E-commerce...">
                    </div>

                    <div class="form-group">
                        <label for="slug">URL (slug)</label>
                        <div class="input-group">
                            <span class="input-prefix">/categorias/</span>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($category['slug'] ?? '') ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                        <small class="form-hint">Solo letras, numeros y guiones</small>
                    </div>

                    <?php if (!empty($parentCategories)): ?>
                    <div class="form-group">
                        <label for="parent_id">Categoria padre</label>
                        <select id="parent_id" name="parent_id">
                            <option value="">Ninguna (categoria principal)</option>
                            <?php foreach ($parentCategories as $parent): ?>
                                <option value="<?= $parent['id'] ?>" <?= ($category['parent_id'] ?? '') == $parent['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($parent['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="description">Descripcion</label>
                        <?php
                        $editorId = 'description';
                        $editorName = 'description';
                        $editorContent = $category['description'] ?? '';
                        $editorRows = 8;
                        $editorPlaceholder = 'Descripcion de la categoria (se mostrara en la pagina de la categoria)';
                        include TEMPLATES_PATH . '/admin/partials/wysiwyg-editor.php';
                        ?>
                    </div>
                </div>
            </div>

            <!-- Visual -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Visual</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="color">Color</label>
                            <div class="color-input-group">
                                <input type="color" id="color-picker"
                                       value="<?= htmlspecialchars($category['color'] ?? '#3E95B0') ?>"
                                       onchange="document.getElementById('color').value = this.value">
                                <input type="text" id="color" name="color"
                                       value="<?= htmlspecialchars($category['color'] ?? '') ?>"
                                       placeholder="#3E95B0" pattern="^#[0-9A-Fa-f]{6}$">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="icon">Icono</label>
                            <div class="icon-input-wrapper">
                                <div class="icon-input-preview">
                                    <?php if (!empty($category['icon'])): ?>
                                        <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-icons" style="color: #ccc;"></i>
                                    <?php endif; ?>
                                </div>
                                <input type="text" id="icon" name="icon"
                                       value="<?= htmlspecialchars($category['icon'] ?? '') ?>"
                                       placeholder="fas fa-folder" readonly>
                                <button type="button" class="icon-input-btn" onclick="openIconPickerForCategory()">
                                    <i class="fas fa-th"></i> Elegir
                                </button>
                                <?php if (!empty($category['icon'])): ?>
                                <button type="button" class="btn btn-sm btn-outline" onclick="clearCategoryIcon()" title="Quitar icono">
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Imagen destacada</label>
                        <div class="image-picker-field">
                            <input type="hidden" name="featured_image" value="<?= htmlspecialchars($featuredImage) ?>">
                            <div class="image-picker-preview <?= !empty($featuredImage) ? 'has-image' : '' ?>">
                                <?php if (!empty($featuredImage)): ?>
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
                                    <i class="fas fa-upload"></i> <?= !empty($featuredImage) ? 'Cambiar' : 'Seleccionar' ?>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= !empty($featuredImage) ? 'flex' : 'none' ?>;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <small class="form-hint">Se mostrara como cabecera en la pagina de la categoria</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Imagen icono (GIF animado)</label>
                        <div class="image-picker-field" data-field="icon_image">
                            <input type="hidden" name="icon_image" value="<?= htmlspecialchars($iconImage) ?>">
                            <div class="image-picker-preview <?= !empty($iconImage) ? 'has-image' : '' ?>">
                                <?php if (!empty($iconImage)): ?>
                                    <img src="<?= htmlspecialchars($iconImage) ?>" alt="Preview">
                                <?php else: ?>
                                    <div class="preview-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>Sin imagen</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="image-picker-actions">
                                <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                    <i class="fas fa-upload"></i> <?= !empty($iconImage) ? 'Cambiar' : 'Seleccionar' ?>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= !empty($iconImage) ? 'flex' : 'none' ?>;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <small class="form-hint">Se mostrara sobre el hero. Si existe, reemplaza al icono. Puede ser un GIF animado.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="editor-card">
                <div class="card-header collapsible" onclick="toggleCard(this)">
                    <h3>SEO</h3>
                    <div class="card-header-actions">
                        <?php if ($isEdit): ?>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); generateSEO('category', <?= $category['id'] ?>, false)" title="Generar SEO en español">
                            <i class="fas fa-magic"></i> Generar (ES)
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="event.stopPropagation(); generateSEO('category', <?= $category['id'] ?>, true)" title="Generar SEO en todos los idiomas">
                            <i class="fas fa-globe"></i> Todos los idiomas
                        </button>
                        <?php endif; ?>
                        <i class="fas fa-chevron-down collapse-icon"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta titulo</label>
                        <input type="text" id="meta_title" name="meta_title"
                               value="<?= htmlspecialchars($category['meta_title'] ?? '') ?>"
                               placeholder="Titulo para buscadores (opcional)">
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta descripcion</label>
                        <textarea id="meta_description" name="meta_description" rows="3"
                                  placeholder="Descripcion para buscadores (opcional)"><?= htmlspecialchars($category['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- LLM Q&A -->
            <?php
            $entity = $category ?? [];
            $entityType = 'category';
            $entityId = $category['id'] ?? null;
            include TEMPLATES_PATH . '/admin/partials/llm-qa-editor.php';
            ?>

            <!-- Content Preview -->
            <?php if ($isEdit && !empty($content)): ?>
            <div class="editor-card">
                <div class="card-header collapsible" onclick="toggleCard(this)">
                    <h3>Contenido de esta categoria</h3>
                    <div class="card-header-actions">
                        <i class="fas fa-chevron-down collapse-icon"></i>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($content['posts'])): ?>
                    <div class="content-section">
                        <h4>Posts (<?= count($content['posts']) ?>)</h4>
                        <ul class="content-list">
                            <?php foreach (array_slice($content['posts'], 0, 5) as $item): ?>
                                <li><a href="/admin/posts/<?= $item['id'] ?>/edit"><?= htmlspecialchars($item['title']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($content['services'])): ?>
                    <div class="content-section">
                        <h4>Servicios (<?= count($content['services']) ?>)</h4>
                        <ul class="content-list">
                            <?php foreach (array_slice($content['services'], 0, 5) as $item): ?>
                                <li><a href="/admin/services/<?= $item['id'] ?>/edit"><?= htmlspecialchars($item['title']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($content['cases'])): ?>
                    <div class="content-section">
                        <h4>Casos de exito (<?= count($content['cases']) ?>)</h4>
                        <ul class="content-list">
                            <?php foreach (array_slice($content['cases'], 0, 5) as $item): ?>
                                <li><a href="/admin/cases/<?= $item['id'] ?>/edit"><?= htmlspecialchars($item['title']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($content['tools'])): ?>
                    <div class="content-section">
                        <h4>Herramientas (<?= count($content['tools']) ?>)</h4>
                        <ul class="content-list">
                            <?php foreach (array_slice($content['tools'], 0, 5) as $item): ?>
                                <li><a href="/admin/tools/<?= $item['id'] ?>/edit"><?= htmlspecialchars($item['title']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="editor-sidebar">
            <div class="editor-card">
                <div class="card-header">
                    <h3>Configuracion</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="<?= $category['sort_order'] ?? 0 ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1"
                                   <?= ($category['is_active'] ?? true) ? 'checked' : '' ?>>
                            <span>Categoria activa</span>
                        </label>
                        <small class="form-hint">Visible en el sitio publico</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear categoria' ?>
                    </button>
                    <?php if ($isEdit): ?>
                        <a href="/categorias/<?= $category['slug'] ?>" target="_blank" class="btn btn-outline btn-block">
                            <i class="fas fa-eye"></i> Ver pagina
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stats -->
            <?php if ($isEdit && isset($category['total_content'])): ?>
            <div class="editor-card">
                <div class="card-header">
                    <h3>Estadisticas</h3>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-value"><?= $category['posts_count'] ?? 0 ?></span>
                            <span class="stat-label">Posts</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= $category['services_count'] ?? 0 ?></span>
                            <span class="stat-label">Servicios</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= $category['cases_count'] ?? 0 ?></span>
                            <span class="stat-label">Casos</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= $category['tools_count'] ?? 0 ?></span>
                            <span class="stat-label">Herramientas</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

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
                            <span class="meta-value"><?= $category['id'] ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Creado</span>
                            <span class="meta-value"><?= date('d/m/Y H:i', strtotime($category['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php include TEMPLATES_PATH . '/admin/partials/icon-picker.php'; ?>

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

.color-input-group {
    display: flex;
    gap: var(--spacing-sm);
    align-items: center;
}

.color-input-group input[type="color"] {
    width: 40px;
    height: 40px;
    padding: 0;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    cursor: pointer;
}

.icon-input-wrapper {
    display: flex;
    gap: var(--spacing-sm);
    align-items: center;
}

.icon-input-preview {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    font-size: 18px;
}

.icon-input-wrapper input {
    flex: 1;
}

.icon-input-btn {
    padding: 8px 12px;
    background: var(--color-gray-100);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    cursor: pointer;
}

.content-section {
    margin-bottom: var(--spacing-lg);
}

.content-section h4 {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-sm);
}

.content-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.content-list li {
    padding: var(--spacing-xs) 0;
    border-bottom: 1px solid var(--color-gray-100);
}

.content-list a {
    color: var(--color-primary);
    text-decoration: none;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-sm);
}

.stat-item {
    text-align: center;
    padding: var(--spacing-sm);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
}

.stat-value {
    display: block;
    font-size: var(--font-size-xl);
    font-weight: 600;
    color: var(--color-primary);
}

.stat-label {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
}
</style>

<script>
function toggleCard(header) {
    const body = header.nextElementSibling;
    const icon = header.querySelector('.collapse-icon');
    body.classList.toggle('collapsed');
    icon.classList.toggle('rotated');
}

function openIconPickerForCategory() {
    const input = document.getElementById('icon');
    iconPicker.open(function(iconClass) {
        input.value = iconClass;
        const preview = document.querySelector('.icon-input-preview i');
        if (preview) {
            preview.className = iconClass;
            preview.style.color = '';
        }
    }, input);
}

function clearCategoryIcon() {
    const input = document.getElementById('icon');
    input.value = '';
    const preview = document.querySelector('.icon-input-preview i');
    if (preview) {
        preview.className = 'fas fa-icons';
        preview.style.color = '#ccc';
    }
}

// Auto-generate slug from name
document.getElementById('name').addEventListener('blur', function() {
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

// Sync color inputs
document.getElementById('color').addEventListener('input', function() {
    const colorPicker = document.getElementById('color-picker');
    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
        colorPicker.value = this.value;
    }
});

// Generate SEO with AI
function generateSEO(entityType, entityId, allLanguages = false) {
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
            all_languages: allLanguages,
            overwrite: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.data) {
                if (data.data.meta_title) {
                    document.getElementById('meta_title').value = data.data.meta_title;
                }
                if (data.data.meta_description) {
                    document.getElementById('meta_description').value = data.data.meta_description;
                }
            }
            const msg = allLanguages
                ? 'SEO generado para todos los idiomas. Revisa los campos y guarda la categoría.'
                : 'SEO generado correctamente. Revisa los campos y guarda la categoría.';
            alert(msg);
        } else {
            alert('Error: ' + (data.message || 'No se pudo generar el SEO'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión al generar SEO');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>
