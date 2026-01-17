<?php
$images = $content['images'] ?? [];
if (is_string($images)) {
    $images = json_decode($images, true) ?? [];
}
?>
<div class="block-form">
    <div class="form-section">
        <h4>Encabezado (opcional)</h4>

        <div class="form-row">
            <div class="form-group">
                <label>Título</label>
                <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? '') ?>" placeholder="Título de la galería">
            </div>
            <div class="form-group">
                <label>Subtítulo</label>
                <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Imágenes de la galería</h4>

        <div class="gallery-images-list" id="galleryImagesList">
            <?php foreach ($images as $index => $image): ?>
                <div class="gallery-image-item" data-index="<?= $index ?>">
                    <div class="gallery-image-preview">
                        <img src="<?= htmlspecialchars($image['url'] ?? $image) ?>" alt="">
                    </div>
                    <div class="gallery-image-actions">
                        <button type="button" class="btn btn-sm btn-danger gallery-image-remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <input type="hidden" data-content="images" value="<?= htmlspecialchars(json_encode($images)) ?>">

        <button type="button" class="btn btn-outline" id="addGalleryImages">
            <i class="fas fa-plus"></i> Añadir imágenes
        </button>
    </div>

    <div class="form-section">
        <h4>Configuración de Layout</h4>

        <div class="form-row">
            <div class="form-group">
                <label>Modo de visualización</label>
                <select data-setting="layout_mode" id="galleryLayoutMode">
                    <option value="grid" <?= ($settings['layout_mode'] ?? 'grid') === 'grid' ? 'selected' : '' ?>>Cuadrícula (Grid)</option>
                    <option value="carousel" <?= ($settings['layout_mode'] ?? '') === 'carousel' ? 'selected' : '' ?>>Carrusel</option>
                </select>
            </div>
            <div class="form-group">
                <label>Proporción de imagen</label>
                <select data-setting="aspect_ratio">
                    <option value="1:1" <?= ($settings['aspect_ratio'] ?? '1:1') === '1:1' ? 'selected' : '' ?>>Cuadrada (1:1)</option>
                    <option value="4:3" <?= ($settings['aspect_ratio'] ?? '') === '4:3' ? 'selected' : '' ?>>Estándar (4:3)</option>
                    <option value="16:9" <?= ($settings['aspect_ratio'] ?? '') === '16:9' ? 'selected' : '' ?>>Panorámica (16:9)</option>
                    <option value="3:2" <?= ($settings['aspect_ratio'] ?? '') === '3:2' ? 'selected' : '' ?>>Foto (3:2)</option>
                    <option value="original" <?= ($settings['aspect_ratio'] ?? '') === 'original' ? 'selected' : '' ?>>Original</option>
                </select>
            </div>
        </div>

        <div class="form-row grid-settings">
            <div class="form-group">
                <label>Columnas (Grid)</label>
                <select data-setting="columns">
                    <option value="2" <?= ($settings['columns'] ?? '') === '2' ? 'selected' : '' ?>>2 columnas</option>
                    <option value="3" <?= ($settings['columns'] ?? '') === '3' ? 'selected' : '' ?>>3 columnas</option>
                    <option value="4" <?= ($settings['columns'] ?? '4') === '4' ? 'selected' : '' ?>>4 columnas</option>
                    <option value="5" <?= ($settings['columns'] ?? '') === '5' ? 'selected' : '' ?>>5 columnas</option>
                    <option value="6" <?= ($settings['columns'] ?? '') === '6' ? 'selected' : '' ?>>6 columnas</option>
                </select>
            </div>
            <div class="form-group">
                <label>Espaciado</label>
                <select data-setting="gap">
                    <option value="xs" <?= ($settings['gap'] ?? '') === 'xs' ? 'selected' : '' ?>>Muy pequeño</option>
                    <option value="sm" <?= ($settings['gap'] ?? '') === 'sm' ? 'selected' : '' ?>>Pequeño</option>
                    <option value="md" <?= ($settings['gap'] ?? 'md') === 'md' ? 'selected' : '' ?>>Mediano</option>
                    <option value="lg" <?= ($settings['gap'] ?? '') === 'lg' ? 'selected' : '' ?>>Grande</option>
                    <option value="xl" <?= ($settings['gap'] ?? '') === 'xl' ? 'selected' : '' ?>>Muy grande</option>
                </select>
            </div>
        </div>

        <div class="form-row carousel-settings" style="<?= ($settings['layout_mode'] ?? 'grid') === 'carousel' ? '' : 'display:none;' ?>">
            <div class="form-group">
                <label>Items visibles (Carrusel)</label>
                <select data-setting="visible_items">
                    <option value="2" <?= ($settings['visible_items'] ?? '') === '2' ? 'selected' : '' ?>>2 items</option>
                    <option value="3" <?= ($settings['visible_items'] ?? '') === '3' ? 'selected' : '' ?>>3 items</option>
                    <option value="4" <?= ($settings['visible_items'] ?? '4') === '4' ? 'selected' : '' ?>>4 items</option>
                    <option value="5" <?= ($settings['visible_items'] ?? '') === '5' ? 'selected' : '' ?>>5 items</option>
                    <option value="6" <?= ($settings['visible_items'] ?? '') === '6' ? 'selected' : '' ?>>6 items</option>
                </select>
            </div>
            <div class="form-group">
                <label>Velocidad de autoplay</label>
                <select data-setting="autoplay_speed">
                    <option value="slow" <?= ($settings['autoplay_speed'] ?? '') === 'slow' ? 'selected' : '' ?>>Lento (5s)</option>
                    <option value="normal" <?= ($settings['autoplay_speed'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Normal (3s)</option>
                    <option value="fast" <?= ($settings['autoplay_speed'] ?? '') === 'fast' ? 'selected' : '' ?>>Rápido (2s)</option>
                </select>
            </div>
        </div>

        <div class="form-group carousel-settings" style="<?= ($settings['layout_mode'] ?? 'grid') === 'carousel' ? '' : 'display:none;' ?>">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="autoplay" <?= ($settings['autoplay'] ?? true) ? 'checked' : '' ?>>
                <span>Autoplay del carrusel</span>
            </label>
        </div>
    </div>

    <div class="form-section">
        <h4>Colores y espaciado</h4>

        <div class="form-row">
            <div class="form-group">
                <label>Color de fondo</label>
                <select data-setting="background_color">
                    <option value="" <?= empty($settings['background_color']) ? 'selected' : '' ?>>Blanco</option>
                    <option value="transparent" <?= ($settings['background_color'] ?? '') === 'transparent' ? 'selected' : '' ?>>Transparente</option>
                    <option value="light" <?= ($settings['background_color'] ?? '') === 'light' ? 'selected' : '' ?>>Gris claro</option>
                    <option value="dark" <?= ($settings['background_color'] ?? '') === 'dark' ? 'selected' : '' ?>>Oscuro</option>
                </select>
            </div>
            <div class="form-group">
                <label>Color de texto</label>
                <input type="color" data-setting="text_color" value="<?= htmlspecialchars($settings['text_color'] ?? '#1a1a1a') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Padding superior</label>
                <input type="text" data-setting="padding_top" value="<?= htmlspecialchars($settings['padding_top'] ?? '80px') ?>" placeholder="80px">
            </div>
            <div class="form-group">
                <label>Padding inferior</label>
                <input type="text" data-setting="padding_bottom" value="<?= htmlspecialchars($settings['padding_bottom'] ?? '80px') ?>" placeholder="80px">
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<style>
.gallery-images-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}

.gallery-image-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: var(--color-gray-100);
}

.gallery-image-preview {
    aspect-ratio: 1;
    overflow: hidden;
}

.gallery-image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-image-actions {
    position: absolute;
    top: 8px;
    right: 8px;
    opacity: 0;
    transition: opacity 0.2s;
}

.gallery-image-item:hover .gallery-image-actions {
    opacity: 1;
}

.gallery-image-actions .btn {
    width: 28px;
    height: 28px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<!-- JavaScript handlers are in block-editor.js initBlockFormHandlers() -->
