<?php
/**
 * Partner Form Template
 * Omniwallet CMS
 */
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
$isEdit = isset($partner) && $partner;
$action = $isEdit ? "/admin/partners/{$partner['id']}/update" : "/admin/partners";
$logoImage = $partner['logo'] ?? '';
$featuredImage = $partner['featured_image'] ?? '';
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/partners" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<form method="POST" action="<?= $action ?>" class="partner-form">
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
                    <div class="form-row">
                        <div class="form-group form-group-2">
                            <label for="name">Nombre de la agencia/empresa *</label>
                            <input type="text" id="name" name="name"
                                   value="<?= htmlspecialchars($partner['name'] ?? '') ?>"
                                   required placeholder="Nombre del partner">
                        </div>

                        <div class="form-group">
                            <label for="partner_type">Tipo de partner *</label>
                            <select id="partner_type" name="partner_type" required>
                                <?php foreach ($partnerTypes as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($partner['partner_type'] ?? 'agency') === $value ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug">URL (slug)</label>
                        <div class="input-group">
                            <span class="input-prefix">/partners/</span>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($partner['slug'] ?? '') ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                        <small class="form-hint">Dejalo vacio para generar automaticamente</small>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripcion</label>
                        <?php
                        $editorId = 'description';
                        $editorName = 'description';
                        $editorContent = $partner['description'] ?? '';
                        $editorRows = 10;
                        $editorPlaceholder = 'Describe al partner, sus servicios y especialidades...';
                        include TEMPLATES_PATH . '/admin/partials/wysiwyg-editor.php';
                        ?>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Informacion de contacto</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="website">Sitio web</label>
                            <input type="url" id="website" name="website"
                                   value="<?= htmlspecialchars($partner['website'] ?? '') ?>"
                                   placeholder="https://...">
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email"
                                   value="<?= htmlspecialchars($partner['email'] ?? '') ?>"
                                   placeholder="contacto@partner.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Telefono</label>
                            <input type="text" id="phone" name="phone"
                                   value="<?= htmlspecialchars($partner['phone'] ?? '') ?>"
                                   placeholder="+34 600 000 000">
                        </div>

                        <div class="form-group">
                            <label for="linkedin">Perfil de LinkedIn</label>
                            <input type="url" id="linkedin" name="linkedin"
                                   value="<?= htmlspecialchars($partner['linkedin'] ?? '') ?>"
                                   placeholder="https://linkedin.com/company/...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Ubicacion</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="country">Pais</label>
                            <input type="text" id="country" name="country"
                                   value="<?= htmlspecialchars($partner['country'] ?? '') ?>"
                                   placeholder="Espana"
                                   list="country-suggestions">
                            <datalist id="country-suggestions">
                                <option value="Espana">
                                <option value="Mexico">
                                <option value="Argentina">
                                <option value="Colombia">
                                <option value="Chile">
                                <option value="Peru">
                                <option value="Estados Unidos">
                                <option value="Reino Unido">
                                <option value="Francia">
                                <option value="Alemania">
                                <option value="Italia">
                                <option value="Portugal">
                            </datalist>
                        </div>

                        <div class="form-group">
                            <label for="city">Ciudad</label>
                            <input type="text" id="city" name="city"
                                   value="<?= htmlspecialchars($partner['city'] ?? '') ?>"
                                   placeholder="Madrid, Barcelona...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Imagenes</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Logotipo</label>
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
                                <small class="form-hint">Logo (preferiblemente PNG transparente)</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Imagen destacada</label>
                            <div class="image-picker-field">
                                <input type="hidden" name="featured_image" value="<?= htmlspecialchars($featuredImage) ?>">
                                <div class="image-picker-preview <?= !empty($featuredImage) ? 'has-image' : '' ?>">
                                    <?php if (!empty($featuredImage)): ?>
                                        <img src="<?= htmlspecialchars($featuredImage) ?>" alt="Featured">
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
                                <small class="form-hint">Imagen para la pagina del partner</small>
                            </div>
                        </div>
                    </div>
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
                                  placeholder="Un testimonio del partner sobre la plataforma..."><?= htmlspecialchars($partner['testimonial'] ?? '') ?></textarea>
                        <small class="form-hint">Opcional: un testimonio o cita sobre la colaboracion</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="testimonial_author">Autor del testimonio</label>
                            <input type="text" id="testimonial_author" name="testimonial_author"
                                   value="<?= htmlspecialchars($partner['testimonial_author'] ?? '') ?>"
                                   placeholder="Nombre y apellidos">
                        </div>

                        <div class="form-group">
                            <label for="testimonial_role">Cargo</label>
                            <input type="text" id="testimonial_role" name="testimonial_role"
                                   value="<?= htmlspecialchars($partner['testimonial_role'] ?? '') ?>"
                                   placeholder="CEO, Director de Marketing...">
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
                        <button type="button" class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); generateSEO('partner', <?= $partner['id'] ?>)" title="Generar SEO con IA">
                            <i class="fas fa-magic"></i> Generar
                        </button>
                        <a href="/admin/seo/edit?type=partner&id=<?= $partner['id'] ?>" class="btn btn-sm btn-outline" onclick="event.stopPropagation()" title="Editar SEO avanzado">
                            <i class="fas fa-cog"></i>
                        </a>
                        <?php endif; ?>
                        <i class="fas fa-chevron-down collapse-icon"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta titulo</label>
                        <input type="text" id="meta_title" name="meta_title"
                               value="<?= htmlspecialchars($partner['meta_title'] ?? '') ?>"
                               placeholder="Titulo para buscadores (opcional)">
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta descripcion</label>
                        <textarea id="meta_description" name="meta_description" rows="3"
                                  placeholder="Descripcion para buscadores (opcional)"><?= htmlspecialchars($partner['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="editor-sidebar">
            <!-- Configuration -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Configuracion</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="<?= $partner['sort_order'] ?? 0 ?>"
                               min="0" step="1">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label certification-checkbox">
                            <input type="checkbox" name="is_certified" value="1" <?= ($partner['is_certified'] ?? false) ? 'checked' : '' ?>>
                            <span><i class="fas fa-certificate"></i> Partner certificado</span>
                        </label>
                        <small class="form-hint">Tiene certificacion oficial</small>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= ($partner['is_featured'] ?? false) ? 'checked' : '' ?>>
                            <span>Partner destacado</span>
                        </label>
                        <small class="form-hint">Se mostrara en posiciones destacadas</small>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" <?= ($partner['is_active'] ?? true) ? 'checked' : '' ?>>
                            <span>Partner activo</span>
                        </label>
                        <small class="form-hint">Visible en el directorio publico</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear partner' ?>
                    </button>
                    <?php if ($isEdit): ?>
                        <a href="/partners/<?= $partner['slug'] ?>" target="_blank" class="btn btn-outline btn-block">
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
                                <span class="meta-value"><?= $partner['id'] ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Creado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($partner['created_at'])) ?></span>
                            </div>
                            <?php if ($partner['updated_at']): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Actualizado</span>
                                    <span class="meta-value"><?= date('d/m/Y H:i', strtotime($partner['updated_at'])) ?></span>
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

.form-group-2 {
    grid-column: span 1;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.certification-checkbox span {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.certification-checkbox span i {
    color: var(--color-success);
}
</style>

<script>
function toggleCard(header) {
    const body = header.nextElementSibling;
    const icon = header.querySelector('.collapse-icon');
    body.classList.toggle('collapsed');
    icon.classList.toggle('rotated');
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
            alert('SEO generado correctamente. Revisa los campos y guarda el partner.');
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
