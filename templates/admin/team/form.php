<?php
/**
 * Team Member Form Template
 * Omniwallet CMS
 */
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';

$isEdit = isset($member) && $member;
$action = $isEdit ? "/admin/team/{$member['id']}" : "/admin/team";
$memberPhoto = $member['photo'] ?? '';
$memberPhotoHover = $member['photo_hover'] ?? '';
$hasPhoto = !empty($memberPhoto);
$hasPhotoHover = !empty($memberPhotoHover);
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/team" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<form method="POST" action="<?= $action ?>" class="team-form">
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
                               value="<?= htmlspecialchars($member['name'] ?? '') ?>"
                               required placeholder="Nombre completo">
                    </div>

                    <div class="form-group">
                        <label for="slug">URL (slug)</label>
                        <div class="input-group">
                            <span class="input-prefix">/equipo/</span>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($member['slug'] ?? '') ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                        <small class="form-hint">Dejalo vacio para generar automaticamente</small>
                    </div>

                    <div class="form-group">
                        <label for="position">Puesto / Cargo</label>
                        <input type="text" id="position" name="position"
                               value="<?= htmlspecialchars($member['role'] ?? '') ?>"
                               placeholder="Ej: Director de Tecnologia, Desarrollador Senior...">
                    </div>

                    <div class="form-group">
                        <label for="description">Descripcion / Biografia</label>
                        <textarea id="description" name="description" rows="6"
                                  placeholder="Descripcion o biografia del miembro del equipo..."><?= htmlspecialchars($member['bio'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Photos -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Fotografias</h3>
                </div>
                <div class="card-body">
                    <div class="photos-grid">
                        <!-- Main Photo -->
                        <div class="form-group">
                            <label>Foto Principal</label>
                            <div class="image-picker-field" id="photo-picker">
                                <input type="hidden" name="photo" value="<?= htmlspecialchars($memberPhoto) ?>">
                                <div class="image-picker-preview <?= $hasPhoto ? 'has-image' : '' ?>">
                                    <?php if ($hasPhoto): ?>
                                        <img src="<?= htmlspecialchars($memberPhoto) ?>" alt="Preview">
                                    <?php else: ?>
                                        <div class="preview-placeholder">
                                            <i class="fas fa-user"></i>
                                            <span>Sin foto</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="image-picker-actions">
                                    <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                        <i class="fas fa-upload"></i> <?= $hasPhoto ? 'Cambiar' : 'Seleccionar' ?>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= $hasPhoto ? 'flex' : 'none' ?>;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <small class="form-hint">Foto del miembro (cuadrada recomendada)</small>
                            </div>
                        </div>

                        <!-- Hover Photo -->
                        <div class="form-group">
                            <label>Foto al Pasar el Cursor (Hover)</label>
                            <div class="image-picker-field" id="photo-hover-picker">
                                <input type="hidden" name="photo_hover" value="<?= htmlspecialchars($memberPhotoHover) ?>">
                                <div class="image-picker-preview <?= $hasPhotoHover ? 'has-image' : '' ?>">
                                    <?php if ($hasPhotoHover): ?>
                                        <img src="<?= htmlspecialchars($memberPhotoHover) ?>" alt="Preview">
                                    <?php else: ?>
                                        <div class="preview-placeholder">
                                            <i class="fas fa-user-circle"></i>
                                            <span>Sin foto</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="image-picker-actions">
                                    <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                        <i class="fas fa-upload"></i> <?= $hasPhotoHover ? 'Cambiar' : 'Seleccionar' ?>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= $hasPhotoHover ? 'flex' : 'none' ?>;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <small class="form-hint">Puede ser un GIF animado para efecto dinamico</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="editor-sidebar">
            <!-- Publish -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Publicar</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-md">Los miembros aparecen en orden aleatorio en la web.</p>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear miembro' ?>
                    </button>
                    <?php if ($isEdit): ?>
                        <a href="/admin/team/create" class="btn btn-outline btn-block">
                            <i class="fas fa-plus"></i> Crear nuevo
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Contacto</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email"
                                   value="<?= htmlspecialchars($member['email'] ?? '') ?>"
                                   placeholder="email@ejemplo.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="linkedin_url">LinkedIn</label>
                        <div class="input-with-icon">
                            <i class="fab fa-linkedin"></i>
                            <input type="url" id="linkedin_url" name="linkedin_url"
                                   value="<?= htmlspecialchars($member['linkedin'] ?? '') ?>"
                                   placeholder="https://linkedin.com/in/usuario">
                        </div>
                    </div>
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
                                <span class="meta-value"><?= $member['id'] ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Creado</span>
                                <span class="meta-value"><?= date('d/m/Y H:i', strtotime($member['created_at'])) ?></span>
                            </div>
                            <?php if ($member['updated_at']): ?>
                                <div class="meta-item">
                                    <span class="meta-label">Actualizado</span>
                                    <span class="meta-value"><?= date('d/m/Y H:i', strtotime($member['updated_at'])) ?></span>
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
.photos-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-xl);
}
@media (max-width: 768px) {
    .photos-grid {
        grid-template-columns: 1fr;
    }
}
.input-with-icon {
    position: relative;
}
.input-with-icon i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-gray-400);
}
.input-with-icon input {
    padding-left: 38px;
}
</style>

<script>
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
</script>
