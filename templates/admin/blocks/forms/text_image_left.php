<?php
// Include image picker partial
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
$blockImage = $content['image'] ?? '';
$hasBlockImage = !empty($blockImage);
?>
<div class="block-form">
    <div class="form-section">
        <h4>Contenido</h4>
        <div class="form-group">
            <label>Título</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? '') ?>" placeholder="Título del bloque">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
        </div>
        <div class="form-group">
            <label>Descripción</label>
            <textarea data-content="description" rows="4" placeholder="Descripción del bloque"><?= htmlspecialchars($content['description'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Texto del enlace</label>
                <input type="text" data-content="link_text" value="<?= htmlspecialchars($content['link_text'] ?? '') ?>" placeholder="Saber más">
            </div>
            <div class="form-group">
                <label>URL del enlace</label>
                <input type="text" data-content="link_url" value="<?= htmlspecialchars($content['link_url'] ?? '') ?>" placeholder="/funcionalidades">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Imagen</h4>
        <div class="form-group">
            <label>Imagen del bloque</label>
            <div class="image-picker-field">
                <input type="text" data-content="image" value="<?= htmlspecialchars($blockImage) ?>">
                <div class="image-picker-preview <?= $hasBlockImage ? 'has-image' : '' ?>">
                    <?php if ($hasBlockImage): ?>
                        <img src="<?= htmlspecialchars($blockImage) ?>" alt="Preview">
                    <?php else: ?>
                        <div class="preview-placeholder">
                            <i class="fas fa-image"></i>
                            <span>Sin imagen</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="image-picker-actions">
                    <button type="button" class="btn btn-sm btn-outline image-picker-select">
                        <i class="fas fa-upload"></i> <?= $hasBlockImage ? 'Cambiar' : 'Seleccionar' ?>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= $hasBlockImage ? 'flex' : 'none' ?>;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Texto alternativo (alt)</label>
            <input type="text" data-content="image_alt" value="<?= htmlspecialchars($content['image_alt'] ?? '') ?>" placeholder="Descripción de la imagen">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Modo de visualización</label>
                <select data-setting="image_display_mode">
                    <option value="auto" <?= ($settings['image_display_mode'] ?? 'auto') === 'auto' ? 'selected' : '' ?>>Automático (100% del contenedor)</option>
                    <option value="original" <?= ($settings['image_display_mode'] ?? '') === 'original' ? 'selected' : '' ?>>Tamaño original</option>
                    <option value="fixed_height" <?= ($settings['image_display_mode'] ?? '') === 'fixed_height' ? 'selected' : '' ?>>Alto fijo (ancho proporcional)</option>
                    <option value="fixed_width" <?= ($settings['image_display_mode'] ?? '') === 'fixed_width' ? 'selected' : '' ?>>Ancho fijo (alto proporcional)</option>
                    <option value="contain" <?= ($settings['image_display_mode'] ?? '') === 'contain' ? 'selected' : '' ?>>Contener (sin recortar)</option>
                    <option value="cover" <?= ($settings['image_display_mode'] ?? '') === 'cover' ? 'selected' : '' ?>>Cubrir (puede recortar)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tamaño personalizado</label>
                <input type="text" data-setting="image_custom_size" value="<?= htmlspecialchars($settings['image_custom_size'] ?? '') ?>" placeholder="Ej: 400px, 50%">
                <small class="form-hint">Para alto/ancho fijo, contain o cover</small>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Alineación vertical</label>
                <select data-setting="image_vertical_align">
                    <option value="center" <?= ($settings['image_vertical_align'] ?? 'center') === 'center' ? 'selected' : '' ?>>Centro</option>
                    <option value="top" <?= ($settings['image_vertical_align'] ?? '') === 'top' ? 'selected' : '' ?>>Arriba</option>
                    <option value="bottom" <?= ($settings['image_vertical_align'] ?? '') === 'bottom' ? 'selected' : '' ?>>Abajo</option>
                </select>
            </div>
            <div class="form-group">
                <label>Bordes redondeados</label>
                <select data-setting="image_border_radius">
                    <option value="default" <?= ($settings['image_border_radius'] ?? 'default') === 'default' ? 'selected' : '' ?>>Por defecto</option>
                    <option value="none" <?= ($settings['image_border_radius'] ?? '') === 'none' ? 'selected' : '' ?>>Sin bordes</option>
                    <option value="small" <?= ($settings['image_border_radius'] ?? '') === 'small' ? 'selected' : '' ?>>Pequeño (8px)</option>
                    <option value="medium" <?= ($settings['image_border_radius'] ?? '') === 'medium' ? 'selected' : '' ?>>Medio (16px)</option>
                    <option value="large" <?= ($settings['image_border_radius'] ?? '') === 'large' ? 'selected' : '' ?>>Grande (24px)</option>
                    <option value="round" <?= ($settings['image_border_radius'] ?? '') === 'round' ? 'selected' : '' ?>>Circular</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="image_shadow" <?= ($settings['image_shadow'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar sombra</span>
                </label>
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <div class="form-section">
        <h4>Configuración</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Animación de imagen</label>
                <select data-setting="image_animation">
                    <option value="fade-left" <?= ($settings['image_animation'] ?? 'fade-left') === 'fade-left' ? 'selected' : '' ?>>Fade desde izquierda</option>
                    <option value="fade-up" <?= ($settings['image_animation'] ?? '') === 'fade-up' ? 'selected' : '' ?>>Fade hacia arriba</option>
                    <option value="none" <?= ($settings['image_animation'] ?? '') === 'none' ? 'selected' : '' ?>>Sin animación</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Padding superior</label>
                <input type="text" data-setting="padding_top" value="<?= htmlspecialchars($settings['padding_top'] ?? '80px') ?>">
            </div>
            <div class="form-group">
                <label>Padding inferior</label>
                <input type="text" data-setting="padding_bottom" value="<?= htmlspecialchars($settings['padding_bottom'] ?? '80px') ?>">
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
