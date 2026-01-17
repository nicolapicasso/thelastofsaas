<?php
// Include image picker partial
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';

$videoThumbnail = $content['video_thumbnail'] ?? '';
$hasVideoThumbnail = !empty($videoThumbnail);
?>
<div class="block-form">
    <div class="form-section">
        <h4>Contenido del Video</h4>

        <div class="form-group">
            <label>Título</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? '') ?>" placeholder="Título de la sección">
        </div>

        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
        </div>

        <div class="form-group">
            <label>URL del Video <span class="required">*</span></label>
            <input type="url" data-content="video_url" value="<?= htmlspecialchars($content['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=... o https://vimeo.com/...">
            <small class="form-help">Soporta YouTube y Vimeo. Pega la URL completa del video.</small>
        </div>

        <div class="form-group">
            <label>Miniatura personalizada (opcional)</label>
            <div class="image-picker-field">
                <input type="text" data-content="video_thumbnail" value="<?= htmlspecialchars($videoThumbnail) ?>">
                <div class="image-picker-preview <?= $hasVideoThumbnail ? 'has-image' : '' ?>">
                    <?php if ($hasVideoThumbnail): ?>
                        <img src="<?= htmlspecialchars($videoThumbnail) ?>" alt="Preview">
                    <?php else: ?>
                        <div class="preview-placeholder">
                            <i class="fas fa-image"></i>
                            <span>Sin imagen</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="image-picker-actions">
                    <button type="button" class="btn btn-sm btn-outline image-picker-select">
                        <i class="fas fa-upload"></i> <?= $hasVideoThumbnail ? 'Cambiar' : 'Seleccionar' ?>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= $hasVideoThumbnail ? 'flex' : 'none' ?>;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <small class="form-help">Si no se proporciona, se usará la miniatura de YouTube automáticamente.</small>
        </div>

        <div class="form-group">
            <label>Descripción (para layout con texto)</label>
            <textarea data-content="description" rows="4" placeholder="Texto que aparecerá junto al video cuando se use layout izquierda/derecha"><?= htmlspecialchars($content['description'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="form-section">
        <h4>Configuración de Layout</h4>

        <div class="form-row">
            <div class="form-group">
                <label>Estilo de visualización</label>
                <select data-setting="display_style" id="videoDisplayStyle">
                    <option value="styled" <?= ($settings['display_style'] ?? 'styled') === 'styled' ? 'selected' : '' ?>>Con estilo (sombra, bordes redondeados)</option>
                    <option value="minimal" <?= ($settings['display_style'] ?? '') === 'minimal' ? 'selected' : '' ?>>Minimal (sin decoración, para grids)</option>
                </select>
                <small class="form-help">El modo minimal elimina padding, sombras y bordes para poder colocar videos en línea.</small>
            </div>
            <div class="form-group video-width-setting" style="<?= ($settings['display_style'] ?? 'styled') === 'minimal' ? '' : 'display:none;' ?>">
                <label>Ancho del video</label>
                <select data-setting="video_width">
                    <option value="100%" <?= ($settings['video_width'] ?? '100%') === '100%' ? 'selected' : '' ?>>100%</option>
                    <option value="50%" <?= ($settings['video_width'] ?? '') === '50%' ? 'selected' : '' ?>>50% (2 videos en fila)</option>
                    <option value="33.333%" <?= ($settings['video_width'] ?? '') === '33.333%' ? 'selected' : '' ?>>33% (3 videos en fila)</option>
                    <option value="25%" <?= ($settings['video_width'] ?? '') === '25%' ? 'selected' : '' ?>>25% (4 videos en fila)</option>
                </select>
            </div>
        </div>

        <div class="form-row styled-only-settings" style="<?= ($settings['display_style'] ?? 'styled') === 'minimal' ? 'display:none;' : '' ?>">
            <div class="form-group">
                <label>Disposición</label>
                <select data-setting="layout">
                    <option value="full" <?= ($settings['layout'] ?? 'full') === 'full' ? 'selected' : '' ?>>Video centrado (ancho completo)</option>
                    <option value="left" <?= ($settings['layout'] ?? '') === 'left' ? 'selected' : '' ?>>Video izquierda + texto derecha</option>
                    <option value="right" <?= ($settings['layout'] ?? '') === 'right' ? 'selected' : '' ?>>Video derecha + texto izquierda</option>
                </select>
            </div>
            <div class="form-group">
                <label>Color de fondo</label>
                <select data-setting="background">
                    <option value="" <?= empty($settings['background']) ? 'selected' : '' ?>>Blanco</option>
                    <option value="transparent" <?= ($settings['background'] ?? '') === 'transparent' ? 'selected' : '' ?>>Transparente</option>
                    <option value="light" <?= ($settings['background'] ?? '') === 'light' ? 'selected' : '' ?>>Gris claro</option>
                    <option value="dark" <?= ($settings['background'] ?? '') === 'dark' ? 'selected' : '' ?>>Oscuro</option>
                    <option value="primary" <?= ($settings['background'] ?? '') === 'primary' ? 'selected' : '' ?>>Primario</option>
                </select>
            </div>
        </div>

        <div class="form-row styled-only-settings" style="<?= ($settings['display_style'] ?? 'styled') === 'minimal' ? 'display:none;' : '' ?>">
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

    <script>
    (function() {
        const displayStyleSelect = document.getElementById('videoDisplayStyle');
        if (!displayStyleSelect) return;

        displayStyleSelect.addEventListener('change', function() {
            const isMinimal = this.value === 'minimal';
            const blockForm = this.closest('.block-form');

            // Toggle video width setting
            blockForm.querySelectorAll('.video-width-setting').forEach(el => {
                el.style.display = isMinimal ? '' : 'none';
            });

            // Toggle styled-only settings
            blockForm.querySelectorAll('.styled-only-settings').forEach(el => {
                el.style.display = isMinimal ? 'none' : '';
            });

            // Also hide overlap section in minimal mode
            const overlapSection = blockForm.querySelector('.form-section:has([data-setting="overlap_enabled"])');
            if (overlapSection) {
                overlapSection.style.display = isMinimal ? 'none' : '';
            }
        });
    })();
    </script>

    <div class="form-section">
        <h4>Modo de reproducción</h4>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="lightbox_enabled" <?= ($settings['lightbox_enabled'] ?? false) ? 'checked' : '' ?>>
                <span>Abrir video en lightbox</span>
            </label>
            <small class="form-help">Al hacer clic en reproducir, el video se abrirá en una capa sobre el sitio. Si está desactivado, el video se reproducirá en el mismo contenedor.</small>
        </div>
    </div>

    <div class="form-section">
        <h4>Efecto de solapamiento</h4>
        <p class="form-help">Permite que el video se solape con el bloque anterior, creando un efecto visual de superposición.</p>

        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="overlap_enabled" <?= ($settings['overlap_enabled'] ?? false) ? 'checked' : '' ?>>
                    <span>Activar solapamiento</span>
                </label>
            </div>
            <div class="form-group">
                <label>Cantidad de solapamiento</label>
                <select data-setting="overlap_amount">
                    <option value="100px" <?= ($settings['overlap_amount'] ?? '100px') === '100px' ? 'selected' : '' ?>>100px (Pequeño)</option>
                    <option value="150px" <?= ($settings['overlap_amount'] ?? '') === '150px' ? 'selected' : '' ?>>150px (Medio)</option>
                    <option value="200px" <?= ($settings['overlap_amount'] ?? '') === '200px' ? 'selected' : '' ?>>200px (Grande)</option>
                    <option value="250px" <?= ($settings['overlap_amount'] ?? '') === '250px' ? 'selected' : '' ?>>250px (Muy grande)</option>
                    <option value="custom" <?= ($settings['overlap_amount'] ?? '') === 'custom' ? 'selected' : '' ?>>Personalizado</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Solapamiento personalizado</label>
                <input type="text" data-setting="overlap_custom" value="<?= htmlspecialchars($settings['overlap_custom'] ?? '') ?>" placeholder="Ej: 180px, 30%">
                <small class="form-hint">Solo si seleccionas "Personalizado"</small>
            </div>
            <div class="form-group">
                <label class="checkbox-label" style="margin-top: 24px;">
                    <input type="checkbox" data-setting="overlap_shadow" <?= ($settings['overlap_shadow'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar sombra en el video</span>
                </label>
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
