<?php
/**
 * Service Form Template
 * We're Sinapsis CMS
 */
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
include_once TEMPLATES_PATH . '/admin/partials/icon-picker.php';
$isEdit = isset($service) && $service;
$action = $isEdit ? "/admin/services/{$service['id']}" : "/admin/services";
$serviceImage = $service['image'] ?? '';
$iconClass = $service['icon_class'] ?? '';
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/services" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<form method="POST" action="<?= $action ?>" class="service-form">
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
                        <label for="title">Titulo del servicio *</label>
                        <input type="text" id="title" name="title"
                               value="<?= htmlspecialchars($service['title'] ?? '') ?>"
                               required placeholder="Nombre del servicio">
                    </div>

                    <div class="form-group">
                        <label for="slug">URL (slug)</label>
                        <div class="input-group">
                            <span class="input-prefix">/servicios/</span>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($service['slug'] ?? '') ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                        <small class="form-hint">Dejalo vacio para generar automaticamente</small>
                    </div>

                    <div class="form-group">
                        <label for="short_description">Descripcion corta</label>
                        <textarea id="short_description" name="short_description" rows="3"
                                  placeholder="Breve descripcion del servicio (para listados)"><?= htmlspecialchars($service['short_description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="full_description">Descripcion completa</label>
                        <?php
                        $editorId = 'full_description';
                        $editorName = 'full_description';
                        $editorContent = $service['full_description'] ?? '';
                        $editorRows = 15;
                        $editorPlaceholder = 'Describe el servicio en detalle...';
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
                            <label for="icon_class">Icono (FontAwesome)</label>
                            <div class="icon-input-wrapper">
                                <div class="icon-input-preview">
                                    <i class="<?= htmlspecialchars($iconClass ?: 'fas fa-cog') ?>"></i>
                                </div>
                                <input type="text" id="icon_class" name="icon_class"
                                       value="<?= htmlspecialchars($iconClass) ?>"
                                       placeholder="fas fa-cog">
                                <button type="button" class="icon-input-btn" onclick="iconPicker.open(null, document.getElementById('icon_class'))">
                                    <i class="fas fa-icons"></i> Elegir
                                </button>
                            </div>
                            <small class="form-hint">Selecciona un icono de la biblioteca o escribe la clase</small>
                        </div>

                        <div class="form-group">
                            <label>Imagen</label>
                            <div class="image-picker-field">
                                <input type="hidden" name="image" value="<?= htmlspecialchars($serviceImage) ?>">
                                <div class="image-picker-preview <?= !empty($serviceImage) ? 'has-image' : '' ?>">
                                    <?php if (!empty($serviceImage)): ?>
                                        <img src="<?= htmlspecialchars($serviceImage) ?>" alt="Imagen">
                                    <?php else: ?>
                                        <div class="preview-placeholder">
                                            <i class="fas fa-image"></i>
                                            <span>Sin imagen</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="image-picker-actions">
                                    <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                        <i class="fas fa-upload"></i> <?= !empty($serviceImage) ? 'Cambiar' : 'Seleccionar' ?>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= !empty($serviceImage) ? 'flex' : 'none' ?>;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blocks Editor -->
            <?php if ($isEdit): ?>
            <div class="editor-card">
                <div class="card-header">
                    <h3>Bloques de contenido</h3>
                    <button type="button" class="btn btn-sm btn-primary" id="add-block-btn">+ Añadir bloque</button>
                </div>
                <div class="card-body">
                    <div id="blocks-container" class="blocks-container" data-service-id="<?= $service['id'] ?>">
                        <?php if (empty($blocks)): ?>
                        <div class="blocks-empty" id="blocks-empty">
                            <p>Este servicio no tiene bloques todavía.</p>
                            <p>Haz clic en "Añadir bloque" para empezar a diseñar la página del servicio.</p>
                        </div>
                        <?php endif; ?>

                        <?php foreach ($blocks ?? [] as $block): ?>
                        <?php $blockType = $block['type'] ?? ''; ?>
                        <div class="block-item" data-block-id="<?= $block['id'] ?>" data-block-type="<?= htmlspecialchars($blockType) ?>">
                            <div class="block-handle">
                                <span class="handle-icon">&#9776;</span>
                            </div>
                            <div class="block-info">
                                <span class="block-type-badge"><?= htmlspecialchars($blockTypes[$blockType] ?? $blockType) ?></span>
                                <?php
                                    $content = is_array($block['content']) ? $block['content'] : [];
                                    $previewTitle = $content['title'] ?? $content['slides'][0]['title'] ?? 'Sin título';
                                ?>
                                <span class="block-preview"><?= htmlspecialchars((string)$previewTitle) ?></span>
                            </div>
                            <div class="block-actions">
                                <button type="button" class="btn btn-sm edit-block-btn" data-block-id="<?= $block['id'] ?>">Editar</button>
                                <button type="button" class="btn btn-sm btn-secondary clone-block-btn" data-block-id="<?= $block['id'] ?>" title="Clonar bloque"><i class="fas fa-clone"></i></button>
                                <button type="button" class="btn btn-sm btn-danger delete-block-btn" data-block-id="<?= $block['id'] ?>">Eliminar</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="editor-card">
                <div class="card-body">
                    <div class="info-message">
                        <p>Guarda el servicio primero para poder añadir bloques de contenido.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Related Cases -->
            <?php if (!empty($cases)): ?>
            <div class="editor-card">
                <div class="card-header">
                    <h3>Casos de exito relacionados</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Selecciona los casos en los que se aplico este servicio</label>
                        <div class="checkbox-grid">
                            <?php foreach ($cases as $case): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="cases[]" value="<?= $case['id'] ?>"
                                           <?= in_array($case['id'], $selectedCases ?? []) ? 'checked' : '' ?>>
                                    <span><?= htmlspecialchars($case['title']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- SEO -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>SEO</h3>
                    <div class="card-header-actions">
                        <?php if ($isEdit): ?>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="generateSEO('service', <?= $service['id'] ?>)" title="Generar SEO con IA">
                            <i class="fas fa-magic"></i> Generar
                        </button>
                        <a href="/admin/seo/edit?type=service&id=<?= $service['id'] ?>" class="btn btn-sm btn-outline" title="Editar SEO avanzado">
                            <i class="fas fa-cog"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta titulo</label>
                        <input type="text" id="meta_title" name="meta_title"
                               value="<?= htmlspecialchars($service['meta_title'] ?? '') ?>"
                               placeholder="Titulo para buscadores (opcional)">
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta descripcion</label>
                        <textarea id="meta_description" name="meta_description" rows="3"
                                  placeholder="Descripcion para buscadores (opcional)"><?= htmlspecialchars($service['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- LLM Q&A -->
            <?php
            $entity = $service ?? [];
            $entityType = 'service';
            $entityId = $service['id'] ?? null;
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
                            <input type="checkbox" name="is_active" value="1" <?= ($service['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <span><i class="fas fa-eye"></i> Activo (visible en el sitio)</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Categoria</label>
                        <select id="category_id" name="category_id">
                            <option value="">Sin categoria</option>
                            <?php foreach ($categories ?? [] as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($service['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="<?= $service['sort_order'] ?? 0 ?>"
                               min="0" step="1">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= ($service['is_featured'] ?? false) ? 'checked' : '' ?>>
                            <span><i class="fas fa-star"></i> Servicio destacado</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear servicio' ?>
                    </button>
                    <?php if ($isEdit): ?>
                        <a href="/servicios/<?= $service['slug'] ?>" target="_blank" class="btn btn-outline btn-block">
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
                                <span class="meta-value"><?= $service['id'] ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Creado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($service['created_at'])) ?></span>
                            </div>
                            <?php if ($service['updated_at']): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Actualizado</span>
                                    <span class="meta-value"><?= date('d/m/Y H:i', strtotime($service['updated_at'])) ?></span>
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

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: var(--spacing-sm);
    max-height: 300px;
    overflow-y: auto;
    padding: var(--spacing-sm);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
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

// Update icon preview
document.getElementById('icon_class').addEventListener('input', function() {
    const wrapper = this.closest('.icon-input-wrapper');
    const preview = wrapper ? wrapper.querySelector('.icon-input-preview i') : null;
    if (preview && this.value) {
        preview.className = this.value;
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
            alert('SEO generado correctamente. Revisa los campos y guarda el servicio.');
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

<?php if ($isEdit): ?>
<?php
// Define helper function before it's used
function getBlockIconService(string $type): string {
    $icons = [
        'hero' => '&#127937;',              // 🏁
        'text_image_left' => '&#9635;',     // ◳
        'text_image_right' => '&#9634;',    // ◲
        'text_full_width' => '&#9644;',     // ▬
        'areas' => '&#9632;',               // ■
        'services' => '&#128736;',          // 🔧 (Servicios)
        'success_cases' => '&#10004;',      // ✔
        'faq' => '&#10067;',                // ❓
        'posts' => '&#128240;',             // 📰
        'business_types' => '&#9881;',      // ⚙ (Características Resumidas)
        'tools' => '&#128295;',             // 🔧 (Herramientas)
        'cta_banner' => '&#128226;',        // 📢
        'benefits' => '&#9733;',            // ★ (Características Detalladas)
        'video' => '&#9658;',               // ▶
        'clients' => '&#128101;',           // 👥
        'team' => '&#128106;',              // 👪
        'landings' => '&#128640;',          // 🚀
        'contact_form' => '&#128231;',      // ✉
        'custom_html' => '&#60;&#47;&#62;', // </>
    ];
    return $icons[$type] ?? '&#9632;';
}
?>
<!-- Block Type Selector Modal -->
<div id="block-type-modal" class="modal" style="display:none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Seleccionar tipo de bloque</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="block-types-grid">
                <?php foreach ($blockTypes as $type => $label): ?>
                <button type="button" class="block-type-option" data-type="<?= $type ?>">
                    <span class="block-type-icon"><?= getBlockIconService($type) ?></span>
                    <span class="block-type-name"><?= htmlspecialchars($label) ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Block Editor Modal -->
<div id="block-editor-modal" class="modal" style="display:none;">
    <div class="modal-overlay"></div>
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3 id="block-editor-title">Editar bloque</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="block-editor-body">
            <!-- Block form loaded via AJAX -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" id="block-editor-cancel">Cancelar</button>
            <button type="button" class="btn btn-primary" id="block-editor-save">Guardar bloque</button>
        </div>
    </div>
</div>

<script src="/assets/js/service-block-editor.js?v=<?= time() ?>"></script>
<?php endif; ?>
