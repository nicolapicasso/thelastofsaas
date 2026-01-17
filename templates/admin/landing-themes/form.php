<?php
/**
 * Landing Theme Form Template
 * Omniwallet CMS
 */
$isEdit = !empty($theme);
$action = $isEdit ? "/admin/landing-themes/{$theme['id']}/update" : '/admin/landing-themes';
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= $isEdit ? 'Editar Temática' : 'Nueva Temática' ?></h1>
        <p><?= $isEdit ? 'Modifica los datos de la temática' : 'Crea una nueva temática de landing pages' ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/landing-themes" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<form method="POST" action="<?= $action ?>" class="form-container">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="form-grid">
        <!-- Main Content -->
        <div class="form-main">
            <div class="card">
                <div class="card-header">
                    <h3>Información General</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Título *</label>
                        <input type="text" id="title" name="title" required
                               value="<?= htmlspecialchars($theme['title'] ?? '') ?>"
                               placeholder="Ej: Casos de Uso">
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug (URL)</label>
                        <input type="text" id="slug" name="slug"
                               value="<?= htmlspecialchars($theme['slug'] ?? '') ?>"
                               placeholder="casos-de-uso (se genera automáticamente)">
                        <small class="form-text">Déjalo vacío para generarlo automáticamente</small>
                    </div>

                    <div class="form-group">
                        <label for="subtitle">Subtítulo</label>
                        <input type="text" id="subtitle" name="subtitle"
                               value="<?= htmlspecialchars($theme['subtitle'] ?? '') ?>"
                               placeholder="Breve descripción">
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea id="description" name="description" rows="4"
                                  placeholder="Descripción detallada de esta temática"><?= htmlspecialchars($theme['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="card">
                <div class="card-header">
                    <h3>SEO</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta Título</label>
                        <input type="text" id="meta_title" name="meta_title"
                               value="<?= htmlspecialchars($theme['meta_title'] ?? '') ?>"
                               placeholder="Título para buscadores">
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta Descripción</label>
                        <textarea id="meta_description" name="meta_description" rows="3"
                                  placeholder="Descripción para buscadores (150-160 caracteres)"><?= htmlspecialchars($theme['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="form-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Publicación</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1"
                                   <?= ($theme['is_active'] ?? true) ? 'checked' : '' ?>>
                            <span>Activa</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="<?= $theme['sort_order'] ?? 0 ?>" min="0">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar Cambios' : 'Crear Temática' ?>
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Apariencia</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="icon">Icono (FontAwesome)</label>
                        <input type="text" id="icon" name="icon"
                               value="<?= htmlspecialchars($theme['icon'] ?? '') ?>"
                               placeholder="fas fa-lightbulb">
                        <small class="form-text">Clase de FontAwesome</small>
                    </div>

                    <div class="form-group">
                        <label for="image">Imagen Destacada</label>
                        <div class="image-input-group">
                            <input type="text" id="image" name="image"
                                   value="<?= htmlspecialchars($theme['image'] ?? '') ?>"
                                   placeholder="URL de la imagen">
                            <button type="button" class="btn btn-outline" onclick="openMediaLibrary('image')">
                                <i class="fas fa-image"></i>
                            </button>
                        </div>
                        <?php if (!empty($theme['image'])): ?>
                            <div class="image-preview">
                                <img src="<?= htmlspecialchars($theme['image']) ?>" alt="Preview">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.form-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: var(--spacing-lg);
}

.form-main .card {
    margin-bottom: var(--spacing-lg);
}

.image-input-group {
    display: flex;
    gap: var(--spacing-sm);
}

.image-input-group input {
    flex: 1;
}

.image-preview {
    margin-top: var(--spacing-sm);
}

.image-preview img {
    max-width: 100%;
    max-height: 150px;
    border-radius: var(--radius-md);
    border: 1px solid var(--color-gray-200);
}

@media (max-width: 1024px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
