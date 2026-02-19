<?php
$videos = $content['videos'] ?? [];
if (is_string($videos)) {
    $videos = json_decode($videos, true) ?? [];
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
        <h4>Videos de la galería</h4>
        <p class="form-help">Añade videos de YouTube o Vimeo. Puedes personalizar la miniatura de cada video.</p>

        <div class="vgallery-videos-list" id="vgalleryVideosList">
            <?php foreach ($videos as $index => $video): ?>
                <div class="vgallery-video-item" data-index="<?= $index ?>">
                    <div class="vgallery-video-thumbnail">
                        <?php if (!empty($video['thumbnail'])): ?>
                            <img src="<?= htmlspecialchars($video['thumbnail']) ?>" alt="">
                        <?php else: ?>
                            <div class="vgallery-video-placeholder">
                                <i class="fas fa-video"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="vgallery-video-info">
                        <input type="url" class="video-url-input" value="<?= htmlspecialchars($video['url'] ?? '') ?>" placeholder="URL del video (YouTube/Vimeo)">
                        <input type="text" class="video-description-input" value="<?= htmlspecialchars($video['description'] ?? '') ?>" placeholder="Descripción del video (opcional)">
                        <div class="vgallery-video-thumb-actions">
                            <button type="button" class="btn btn-xs btn-outline vgallery-thumb-select">
                                <i class="fas fa-image"></i> Miniatura
                            </button>
                            <input type="hidden" class="video-thumbnail-input" value="<?= htmlspecialchars($video['thumbnail'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="vgallery-video-actions">
                        <button type="button" class="btn btn-sm btn-danger vgallery-video-remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <input type="hidden" data-content="videos" value="<?= htmlspecialchars(json_encode($videos)) ?>">

        <button type="button" class="btn btn-outline" id="addVgalleryVideo">
            <i class="fas fa-plus"></i> Añadir video
        </button>
    </div>

    <div class="form-section">
        <h4>Configuración de Layout</h4>

        <div class="form-row">
            <div class="form-group">
                <label>Modo de visualización</label>
                <select data-setting="layout_mode" id="vgalleryLayoutMode">
                    <option value="grid" <?= ($settings['layout_mode'] ?? 'grid') === 'grid' ? 'selected' : '' ?>>Cuadrícula (Grid)</option>
                    <option value="carousel" <?= ($settings['layout_mode'] ?? '') === 'carousel' ? 'selected' : '' ?>>Carrusel</option>
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

        <div class="form-row grid-settings">
            <div class="form-group">
                <label>Columnas (Grid)</label>
                <select data-setting="columns">
                    <option value="2" <?= ($settings['columns'] ?? '') === '2' ? 'selected' : '' ?>>2 columnas</option>
                    <option value="3" <?= ($settings['columns'] ?? '3') === '3' ? 'selected' : '' ?>>3 columnas</option>
                    <option value="4" <?= ($settings['columns'] ?? '') === '4' ? 'selected' : '' ?>>4 columnas</option>
                </select>
            </div>
        </div>

        <div class="form-row carousel-settings" style="<?= ($settings['layout_mode'] ?? 'grid') === 'carousel' ? '' : 'display:none;' ?>">
            <div class="form-group">
                <label>Videos visibles (Carrusel)</label>
                <select data-setting="visible_items">
                    <option value="2" <?= ($settings['visible_items'] ?? '') === '2' ? 'selected' : '' ?>>2 videos</option>
                    <option value="3" <?= ($settings['visible_items'] ?? '3') === '3' ? 'selected' : '' ?>>3 videos</option>
                    <option value="4" <?= ($settings['visible_items'] ?? '') === '4' ? 'selected' : '' ?>>4 videos</option>
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
.vgallery-videos-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 16px;
}

.vgallery-video-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--color-gray-50);
    border-radius: 8px;
    border: 1px solid var(--color-gray-200);
}

.vgallery-video-thumbnail {
    width: 120px;
    height: 68px;
    border-radius: 6px;
    overflow: hidden;
    background: var(--color-gray-200);
    flex-shrink: 0;
}

.vgallery-video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.vgallery-video-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-800);
}

.vgallery-video-placeholder i {
    font-size: 24px;
    color: var(--color-gray-500);
}

.vgallery-video-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.vgallery-video-info input {
    width: 100%;
}

.vgallery-video-thumb-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.vgallery-video-actions {
    flex-shrink: 0;
}

.vgallery-video-actions .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-xs {
    font-size: 12px;
    padding: 4px 8px;
}
</style>

<!-- JavaScript handlers are in block-editor.js initBlockFormHandlers() -->
