<?php
/**
 * Success Case Form Template
 * We're Sinapsis CMS
 */
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
$isEdit = isset($case) && $case;
$action = $isEdit ? "/admin/cases/{$case['id']}/update" : "/admin/cases";
$metrics = $isEdit && isset($case['metrics_array']) ? $case['metrics_array'] : [];
$gallery = $isEdit && isset($case['gallery_array']) ? $case['gallery_array'] : [];
$featuredImage = $case['featured_image'] ?? '';
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/cases" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<form method="POST" action="<?= $action ?>" id="caseForm">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
    <input type="hidden" name="metrics" id="metricsInput" value="<?= htmlspecialchars(json_encode($metrics)) ?>">
    <input type="hidden" name="gallery" id="galleryInput" value="<?= htmlspecialchars(json_encode($gallery)) ?>">

    <div class="editor-layout">
        <!-- Main Content -->
        <div class="editor-main">
            <!-- Basic Info -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Informacion del Caso</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Titulo del caso *</label>
                        <input type="text" id="title" name="title"
                               value="<?= htmlspecialchars($case['title'] ?? '') ?>"
                               required placeholder="Ej: Transformacion digital de TechCorp">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="client_id">Cliente *</label>
                            <select id="client_id" name="client_id" required>
                                <option value="">Seleccionar cliente</option>
                                <?php foreach ($clients ?? [] as $client): ?>
                                    <option value="<?= $client['id'] ?>" <?= ($case['client_id'] ?? '') == $client['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($client['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-hint">
                                <a href="/admin/clients/create" target="_blank">+ Crear nuevo cliente</a>
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="category_id">Categoria</label>
                            <select id="category_id" name="category_id">
                                <option value="">Sin categoria</option>
                                <?php foreach ($categories ?? [] as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($case['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug">URL (slug)</label>
                        <div class="input-group">
                            <span class="input-prefix">/casos-de-exito/</span>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($case['slug'] ?? '') ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Case Details -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Detalles del Caso</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="challenge">El Reto</label>
                        <?php
                        $editorId = 'challenge';
                        $editorName = 'challenge';
                        $editorContent = $case['challenge'] ?? '';
                        $editorRows = 6;
                        $editorPlaceholder = 'Describe el problema o desafio que enfrentaba el cliente...';
                        include TEMPLATES_PATH . '/admin/partials/wysiwyg-editor.php';
                        ?>
                    </div>

                    <div class="form-group">
                        <label for="solution">La Solucion</label>
                        <?php
                        $editorId = 'solution';
                        $editorName = 'solution';
                        $editorContent = $case['solution'] ?? '';
                        $editorRows = 6;
                        $editorPlaceholder = 'Como resolvimos el problema...';
                        include TEMPLATES_PATH . '/admin/partials/wysiwyg-editor.php';
                        ?>
                    </div>

                    <div class="form-group">
                        <label for="results">Los Resultados</label>
                        <?php
                        $editorId = 'results';
                        $editorName = 'results';
                        $editorContent = $case['results'] ?? '';
                        $editorRows = 6;
                        $editorPlaceholder = 'Resultados obtenidos tras implementar la solucion...';
                        include TEMPLATES_PATH . '/admin/partials/wysiwyg-editor.php';
                        ?>
                    </div>
                </div>
            </div>

            <!-- Services & Tools -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Servicios y Herramientas</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($services)): ?>
                    <div class="form-group">
                        <label>Servicios aplicados</label>
                        <div class="checkbox-grid">
                            <?php foreach ($services as $service): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="services[]" value="<?= $service['id'] ?>"
                                           <?= in_array($service['id'], $selectedServices ?? []) ? 'checked' : '' ?>>
                                    <span><?= htmlspecialchars($service['title']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($tools)): ?>
                    <div class="form-group">
                        <label>Herramientas utilizadas</label>
                        <div class="checkbox-grid">
                            <?php foreach ($tools as $tool): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="tools[]" value="<?= $tool['id'] ?>"
                                           <?= in_array($tool['id'], $selectedTools ?? []) ? 'checked' : '' ?>>
                                    <span><?= htmlspecialchars($tool['title']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Metrics -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Metricas de Exito</h3>
                </div>
                <div class="card-body">
                    <div id="metricsContainer"></div>
                    <button type="button" class="btn btn-outline" onclick="addMetric()">
                        <i class="fas fa-plus"></i> Añadir Metrica
                    </button>
                </div>
            </div>

            <!-- Testimonial -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Testimonio</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="testimonial">Texto del testimonio</label>
                        <textarea id="testimonial" name="testimonial" rows="4"
                                  placeholder="&quot;Gracias a su ayuda hemos transformado nuestra operacion...&quot;"><?= htmlspecialchars($case['testimonial'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="testimonial_author">Autor</label>
                            <input type="text" id="testimonial_author" name="testimonial_author"
                                   value="<?= htmlspecialchars($case['testimonial_author'] ?? '') ?>"
                                   placeholder="Juan Garcia">
                        </div>
                        <div class="form-group">
                            <label for="testimonial_role">Cargo</label>
                            <input type="text" id="testimonial_role" name="testimonial_role"
                                   value="<?= htmlspecialchars($case['testimonial_role'] ?? '') ?>"
                                   placeholder="CEO">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Multimedia</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Imagen principal</label>
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
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="video_url">Video (YouTube/Vimeo)</label>
                        <input type="url" id="video_url" name="video_url"
                               value="<?= htmlspecialchars($case['video_url'] ?? '') ?>"
                               placeholder="https://youtube.com/watch?v=... o https://vimeo.com/...">
                    </div>

                    <div class="form-group">
                        <label for="gallery_display">Tipo de galeria</label>
                        <select id="gallery_display" name="gallery_display">
                            <option value="carousel" <?= ($case['gallery_display'] ?? 'carousel') === 'carousel' ? 'selected' : '' ?>>Carrusel</option>
                            <option value="grid" <?= ($case['gallery_display'] ?? '') === 'grid' ? 'selected' : '' ?>>Cuadricula</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Galeria de imagenes</label>
                        <div id="galleryContainer"></div>
                        <button type="button" class="btn btn-outline" onclick="addGalleryImage()">
                            <i class="fas fa-plus"></i> Añadir imagen
                        </button>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>SEO</h3>
                    <div class="card-header-actions">
                        <?php if ($isEdit): ?>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="generateSEO('success_case', <?= $case['id'] ?>)" title="Generar SEO con IA">
                            <i class="fas fa-magic"></i> Generar
                        </button>
                        <a href="/admin/seo/edit?type=success_case&id=<?= $case['id'] ?>" class="btn btn-sm btn-outline" title="Editar SEO avanzado">
                            <i class="fas fa-cog"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta titulo</label>
                        <input type="text" id="meta_title" name="meta_title"
                               value="<?= htmlspecialchars($case['meta_title'] ?? '') ?>"
                               placeholder="Caso de exito: Titulo | We're Sinapsis">
                    </div>
                    <div class="form-group">
                        <label for="meta_description">Meta descripcion</label>
                        <textarea id="meta_description" name="meta_description" rows="2"
                                  placeholder="Descubre como [Cliente] logro [resultado]..."><?= htmlspecialchars($case['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- LLM Q&A -->
            <?php
            $entity = $case ?? [];
            $entityType = 'success_case';
            $entityId = $case['id'] ?? null;
            include TEMPLATES_PATH . '/admin/partials/llm-qa-editor.php';
            ?>
        </div>

        <!-- Sidebar -->
        <div class="editor-sidebar">
            <div class="editor-card">
                <div class="card-header">
                    <h3>Publicar</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="status">Estado</label>
                        <select id="status" name="status">
                            <option value="draft" <?= ($case['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Borrador</option>
                            <option value="published" <?= ($case['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="<?= $case['sort_order'] ?? 0 ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= ($case['is_featured'] ?? false) ? 'checked' : '' ?>>
                            <span><i class="fas fa-star"></i> Caso destacado</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear caso' ?>
                    </button>
                    <?php if ($isEdit): ?>
                        <a href="/casos-de-exito/<?= $case['slug'] ?>" target="_blank" class="btn btn-outline btn-block">
                            <i class="fas fa-eye"></i> Ver pagina
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($isEdit): ?>
                <div class="editor-card">
                    <div class="card-header">
                        <h3>Informacion</h3>
                    </div>
                    <div class="card-body">
                        <div class="meta-list">
                            <div class="meta-item">
                                <span class="meta-label">ID</span>
                                <span class="meta-value"><?= $case['id'] ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Creado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($case['created_at'])) ?></span>
                            </div>
                            <?php if (!empty($case['published_at'])): ?>
                            <div class="meta-item">
                                <span class="meta-label">Publicado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($case['published_at'])) ?></span>
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
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: var(--spacing-sm);
    max-height: 200px;
    overflow-y: auto;
    padding: var(--spacing-sm);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
}

.metric-item, .gallery-item {
    display: grid;
    grid-template-columns: 1fr 2fr auto;
    gap: var(--spacing-sm);
    padding: var(--spacing-md);
    background-color: var(--color-gray-50);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-sm);
    align-items: center;
}

.gallery-item {
    grid-template-columns: 1fr auto;
    align-items: start;
}

.gallery-item-content {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: var(--spacing-md);
    align-items: start;
}

.gallery-item .image-picker-field {
    margin: 0;
}

.gallery-item .image-picker-preview {
    width: 140px;
    height: 100px;
    border-radius: var(--radius-md);
    overflow: hidden;
}

.gallery-item .image-picker-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-item .caption-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.gallery-item .caption-group label {
    font-size: 12px;
    color: var(--color-gray-600);
    margin: 0;
}

.gallery-item .caption-input {
    padding: 8px 12px;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: 14px;
    width: 100%;
}

.metric-item input {
    padding: 8px 12px;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: 14px;
}

@media (max-width: 600px) {
    .gallery-item-content {
        grid-template-columns: 1fr;
    }
}

.remove-btn {
    background: none;
    border: none;
    color: var(--color-danger);
    cursor: pointer;
    padding: 8px;
}
</style>

<script>
let metrics = <?= json_encode($metrics) ?>;
let gallery = <?= json_encode($gallery) ?>;

// Metrics
function renderMetrics() {
    const container = document.getElementById('metricsContainer');
    container.innerHTML = '';

    metrics.forEach((metric, index) => {
        const item = document.createElement('div');
        item.className = 'metric-item';
        item.innerHTML = `
            <input type="text" placeholder="Ej: +150%" value="${metric.value || ''}"
                   onchange="updateMetric(${index}, 'value', this.value)">
            <input type="text" placeholder="Descripcion (Ej: Incremento en ventas)" value="${metric.label || ''}"
                   onchange="updateMetric(${index}, 'label', this.value)">
            <button type="button" class="remove-btn" onclick="removeMetric(${index})">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(item);
    });

    updateMetricsInput();
}

function addMetric() {
    metrics.push({ value: '', label: '' });
    renderMetrics();
}

function updateMetric(index, field, value) {
    metrics[index][field] = value;
    updateMetricsInput();
}

function removeMetric(index) {
    metrics.splice(index, 1);
    renderMetrics();
}

function updateMetricsInput() {
    document.getElementById('metricsInput').value = JSON.stringify(metrics);
}

// Gallery
function renderGallery() {
    const container = document.getElementById('galleryContainer');
    container.innerHTML = '';

    gallery.forEach((item, index) => {
        const div = document.createElement('div');
        div.className = 'gallery-item';
        div.dataset.index = index;
        const hasImage = item.url && item.url.length > 0;

        div.innerHTML = `
            <div class="gallery-item-content">
                <div class="image-picker-field">
                    <input type="hidden" class="gallery-url-input" value="${item.url || ''}">
                    <div class="image-picker-preview ${hasImage ? 'has-image' : ''}">
                        ${hasImage
                            ? `<img src="${item.url}" alt="">`
                            : `<div class="preview-placeholder"><i class="fas fa-image"></i><span>Sin imagen</span></div>`
                        }
                    </div>
                    <div class="image-picker-actions">
                        <button type="button" class="btn btn-sm btn-outline image-picker-select">
                            <i class="fas fa-upload"></i> ${hasImage ? 'Cambiar' : 'Subir'}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: ${hasImage ? 'flex' : 'none'};">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="caption-group">
                    <label>Pie de foto (opcional)</label>
                    <input type="text" class="caption-input" placeholder="Descripción de la imagen..."
                           value="${item.caption || ''}" onchange="updateGalleryCaption(${index}, this.value)">
                </div>
            </div>
            <button type="button" class="remove-btn" onclick="removeGalleryItem(${index})" title="Eliminar imagen">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
    });

    // Re-bind image pickers for the new elements
    if (window.imagePicker) {
        window.imagePicker.bindPickers();
    }

    // Add change listeners for the URL inputs
    container.querySelectorAll('.gallery-url-input').forEach((input, index) => {
        input.addEventListener('change', function() {
            gallery[index].url = this.value;
            updateGalleryInput();
        });
    });

    updateGalleryInput();
}

function addGalleryImage() {
    gallery.push({ url: '', caption: '' });
    renderGallery();
}

function updateGalleryCaption(index, value) {
    gallery[index].caption = value;
    updateGalleryInput();
}

function removeGalleryItem(index) {
    gallery.splice(index, 1);
    renderGallery();
}

function updateGalleryInput() {
    document.getElementById('galleryInput').value = JSON.stringify(gallery);
}

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

// Initialize
renderMetrics();
renderGallery();

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
            alert('SEO generado correctamente. Revisa los campos y guarda el caso de éxito.');
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
