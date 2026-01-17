<?php
/**
 * Client Form Template
 * We're Sinapsis CMS
 */
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
$isEdit = isset($client) && $client;
$action = $isEdit ? "/admin/clients/{$client['id']}" : "/admin/clients";
$logoImage = $client['logo'] ?? '';
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/clients" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<form method="POST" action="<?= $action ?>" class="client-form">
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
                        <div class="form-group form-group-2">
                            <label for="name">Nombre del cliente *</label>
                            <input type="text" id="name" name="name"
                                   value="<?= htmlspecialchars($client['name'] ?? '') ?>"
                                   required placeholder="Nombre de la empresa">
                        </div>

                        <div class="form-group">
                            <label for="industry">Industria</label>
                            <input type="text" id="industry" name="industry"
                                   value="<?= htmlspecialchars($client['industry'] ?? '') ?>"
                                   placeholder="Tecnologia, Retail, Finanzas..."
                                   list="industry-suggestions">
                            <datalist id="industry-suggestions">
                                <option value="Tecnologia">
                                <option value="Fintech">
                                <option value="E-commerce">
                                <option value="Retail">
                                <option value="Salud">
                                <option value="Educacion">
                                <option value="Servicios">
                                <option value="Manufactura">
                                <option value="Logistica">
                            </datalist>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slug">URL (slug)</label>
                        <div class="input-group">
                            <span class="input-prefix">/clientes/</span>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($client['slug'] ?? '') ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                        <small class="form-hint">Dejalo vacio para generar automaticamente</small>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripcion</label>
                        <?php
                        $editorId = 'description';
                        $editorName = 'description';
                        $editorContent = $client['description'] ?? '';
                        $editorRows = 8;
                        $editorPlaceholder = 'Describe al cliente, su sector y relacion con nosotros...';
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
                                   value="<?= htmlspecialchars($client['website'] ?? '') ?>"
                                   placeholder="https://...">
                        </div>

                        <div class="form-group">
                            <label for="location">Ubicacion</label>
                            <input type="text" id="location" name="location"
                                   value="<?= htmlspecialchars($client['location'] ?? '') ?>"
                                   placeholder="Madrid, Espana">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Logotipo</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Logo del cliente</label>
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
                            <small class="form-hint">Logo del cliente (preferiblemente PNG transparente)</small>
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
                        <button type="button" class="btn btn-sm btn-secondary" onclick="generateSEO('client', <?= $client['id'] ?>)" title="Generar SEO con IA">
                            <i class="fas fa-magic"></i> Generar
                        </button>
                        <a href="/admin/seo/edit?type=client&id=<?= $client['id'] ?>" class="btn btn-sm btn-outline" title="Editar SEO avanzado">
                            <i class="fas fa-cog"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta titulo</label>
                        <input type="text" id="meta_title" name="meta_title"
                               value="<?= htmlspecialchars($client['meta_title'] ?? '') ?>"
                               placeholder="Titulo para buscadores (opcional)">
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta descripcion</label>
                        <textarea id="meta_description" name="meta_description" rows="3"
                                  placeholder="Descripcion para buscadores (opcional)"><?= htmlspecialchars($client['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- LLM Q&A -->
            <?php
            $entity = $client ?? [];
            $entityType = 'client';
            $entityId = $client['id'] ?? null;
            include TEMPLATES_PATH . '/admin/partials/llm-qa-editor.php';
            ?>
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
                               value="<?= $client['sort_order'] ?? 0 ?>"
                               min="0" step="1">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= ($client['is_featured'] ?? false) ? 'checked' : '' ?>>
                            <span><i class="fas fa-star"></i> Cliente destacado</span>
                        </label>
                        <small class="form-hint">Se mostrara en posiciones destacadas</small>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" <?= ($client['is_active'] ?? true) ? 'checked' : '' ?>>
                            <span>Cliente activo</span>
                        </label>
                        <small class="form-hint">Visible en el directorio publico</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear cliente' ?>
                    </button>
                    <?php if ($isEdit): ?>
                        <a href="/clientes/<?= $client['slug'] ?>" target="_blank" class="btn btn-outline btn-block">
                            <i class="fas fa-eye"></i> Ver pagina
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cases -->
            <?php if ($isEdit && !empty($cases)): ?>
                <div class="editor-card">
                    <div class="card-header">
                        <h3>Casos de exito</h3>
                    </div>
                    <div class="card-body">
                        <ul class="related-list">
                            <?php foreach ($cases as $case): ?>
                                <li>
                                    <a href="/admin/cases/<?= $case['id'] ?>/edit">
                                        <?= htmlspecialchars($case['title']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
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
                                <span class="meta-value"><?= $client['id'] ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Creado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($client['created_at'])) ?></span>
                            </div>
                            <?php if ($client['updated_at']): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Actualizado</span>
                                    <span class="meta-value"><?= date('d/m/Y H:i', strtotime($client['updated_at'])) ?></span>
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

.related-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.related-list li {
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid var(--color-gray-100);
}

.related-list li:last-child {
    border-bottom: none;
}

.related-list a {
    color: var(--color-primary);
    text-decoration: none;
}

.related-list a:hover {
    text-decoration: underline;
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

// Generate SEO with AI
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
            document.getElementById('meta_title').value = data.data.meta_title || '';
            document.getElementById('meta_description').value = data.data.meta_description || '';
            alert('SEO generado correctamente');
        } else {
            alert(data.error || 'Error al generar SEO');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>
