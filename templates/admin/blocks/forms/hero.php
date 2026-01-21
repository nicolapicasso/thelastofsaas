<?php
// Include image picker partial
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
?>
<div class="block-form">
    <div class="form-section">
        <h4>Slides del Hero</h4>
        <p class="form-help">El hero puede tener múltiples slides que se mostrarán en un carrusel.</p>

        <div class="slides-container">
            <?php
            $slides = $content['slides'] ?? [['title' => '', 'subtitle' => '', 'background_image' => '', 'cta_text' => 'Reservar demo', 'cta_url' => '/reservar-demo']];
            foreach ($slides as $index => $slide):
            $bgImage = $slide['background_image'] ?? '';
            $hasBgImage = !empty($bgImage);
            ?>
            <div class="slide-item" data-slide-index="<?= $index ?>">
                <div class="slide-header">
                    <span>Slide <?= $index + 1 ?></span>
                    <?php if ($index > 0): ?>
                    <button type="button" class="btn btn-sm btn-danger remove-slide-btn">&times;</button>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Título principal</label>
                    <input type="text" data-slide-field="title" value="<?= htmlspecialchars($slide['title'] ?? '') ?>" placeholder="Ej: Fideliza a tus clientes">
                </div>

                <div class="form-group">
                    <label>Subtítulo</label>
                    <input type="text" data-slide-field="subtitle" value="<?= htmlspecialchars($slide['subtitle'] ?? '') ?>" placeholder="Texto secundario">
                </div>

                <div class="form-group">
                    <label>Fondo: Video (YouTube/Vimeo) o Imagen</label>
                    <p class="form-help" style="margin-bottom: 0.5rem; font-size: 0.85em; color: #666;">
                        Si añades un video, se reproducirá automáticamente sin sonido. La imagen se usará como fallback.
                    </p>
                    <input type="text" data-slide-field="background_video" value="<?= htmlspecialchars($slide['background_video'] ?? '') ?>" placeholder="URL de YouTube o Vimeo (ej: https://www.youtube.com/watch?v=...)">
                </div>

                <div class="form-group">
                    <label>Imagen de fondo</label>
                    <div class="image-picker-field">
                        <input type="text" data-slide-field="background_image" value="<?= htmlspecialchars($bgImage) ?>">
                        <div class="image-picker-preview <?= $hasBgImage ? 'has-image' : '' ?>">
                            <?php if ($hasBgImage): ?>
                                <img src="<?= htmlspecialchars($bgImage) ?>" alt="Preview">
                            <?php else: ?>
                                <div class="preview-placeholder">
                                    <i class="fas fa-image"></i>
                                    <span>Sin imagen</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="image-picker-actions">
                            <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                <i class="fas fa-upload"></i> <?= $hasBgImage ? 'Cambiar' : 'Seleccionar' ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= $hasBgImage ? 'flex' : 'none' ?>;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Texto del botón CTA</label>
                        <input type="text" data-slide-field="cta_text" value="<?= htmlspecialchars($slide['cta_text'] ?? 'Reservar demo') ?>">
                    </div>
                    <div class="form-group">
                        <label>URL del botón</label>
                        <input type="text" data-slide-field="cta_url" value="<?= htmlspecialchars($slide['cta_url'] ?? '/reservar-demo') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Color del título</label>
                        <div class="color-picker-inline">
                            <input type="color" data-slide-field="title_color" value="<?= htmlspecialchars($slide['title_color'] ?? '#ffffff') ?>">
                            <input type="text" data-slide-field="title_color" value="<?= htmlspecialchars($slide['title_color'] ?? '#ffffff') ?>" placeholder="#ffffff">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Color del subtítulo</label>
                        <div class="color-picker-inline">
                            <input type="color" data-slide-field="subtitle_color" value="<?= htmlspecialchars($slide['subtitle_color'] ?? '#ffffffcc') ?>">
                            <input type="text" data-slide-field="subtitle_color" value="<?= htmlspecialchars($slide['subtitle_color'] ?? '#ffffffcc') ?>" placeholder="#ffffffcc">
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline add-slide-btn">+ Añadir slide</button>
    </div>

    <div class="form-section">
        <h4>Configuración</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Altura del hero</label>
                <input type="text" data-setting="height" value="<?= htmlspecialchars($settings['height'] ?? '600px') ?>" placeholder="600px">
            </div>
            <div class="form-group">
                <label>Opacidad overlay (0-1)</label>
                <input type="number" data-setting="overlay_opacity" value="<?= $settings['overlay_opacity'] ?? 0.6 ?>" min="0" max="1" step="0.1">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Alineación del texto</label>
                <select data-setting="text_alignment">
                    <option value="left" <?= ($settings['text_alignment'] ?? 'left') === 'left' ? 'selected' : '' ?>>Izquierda</option>
                    <option value="center" <?= ($settings['text_alignment'] ?? '') === 'center' ? 'selected' : '' ?>>Centro</option>
                    <option value="right" <?= ($settings['text_alignment'] ?? '') === 'right' ? 'selected' : '' ?>>Derecha</option>
                </select>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="parallax_enabled" <?= ($settings['parallax_enabled'] ?? true) ? 'checked' : '' ?>>
                    <span>Efecto parallax</span>
                </label>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Márgenes</h4>
        <p class="form-help">Configura los márgenes superior e inferior del hero. Usa 0 para eliminar el margen.</p>
        <div class="form-row">
            <div class="form-group">
                <label>Margen superior</label>
                <select data-setting="margin_top">
                    <option value="auto" <?= ($settings['margin_top'] ?? 'auto') === 'auto' ? 'selected' : '' ?>>Automático (altura del header)</option>
                    <option value="0" <?= ($settings['margin_top'] ?? '') === '0' ? 'selected' : '' ?>>0 (Sin margen)</option>
                    <option value="20px" <?= ($settings['margin_top'] ?? '') === '20px' ? 'selected' : '' ?>>20px</option>
                    <option value="40px" <?= ($settings['margin_top'] ?? '') === '40px' ? 'selected' : '' ?>>40px</option>
                    <option value="60px" <?= ($settings['margin_top'] ?? '') === '60px' ? 'selected' : '' ?>>60px</option>
                    <option value="80px" <?= ($settings['margin_top'] ?? '') === '80px' ? 'selected' : '' ?>>80px</option>
                    <option value="100px" <?= ($settings['margin_top'] ?? '') === '100px' ? 'selected' : '' ?>>100px</option>
                    <option value="custom" <?= ($settings['margin_top'] ?? '') === 'custom' ? 'selected' : '' ?>>Personalizado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Margen superior personalizado</label>
                <input type="text" data-setting="margin_top_custom" value="<?= htmlspecialchars($settings['margin_top_custom'] ?? '') ?>" placeholder="Ej: 50px, 5rem">
                <small class="form-hint">Solo si seleccionas "Personalizado"</small>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Margen inferior</label>
                <select data-setting="margin_bottom">
                    <option value="0" <?= ($settings['margin_bottom'] ?? '0') === '0' ? 'selected' : '' ?>>0 (Sin margen)</option>
                    <option value="20px" <?= ($settings['margin_bottom'] ?? '') === '20px' ? 'selected' : '' ?>>20px</option>
                    <option value="40px" <?= ($settings['margin_bottom'] ?? '') === '40px' ? 'selected' : '' ?>>40px</option>
                    <option value="60px" <?= ($settings['margin_bottom'] ?? '') === '60px' ? 'selected' : '' ?>>60px</option>
                    <option value="80px" <?= ($settings['margin_bottom'] ?? '') === '80px' ? 'selected' : '' ?>>80px</option>
                    <option value="100px" <?= ($settings['margin_bottom'] ?? '') === '100px' ? 'selected' : '' ?>>100px</option>
                    <option value="custom" <?= ($settings['margin_bottom'] ?? '') === 'custom' ? 'selected' : '' ?>>Personalizado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Margen inferior personalizado</label>
                <input type="text" data-setting="margin_bottom_custom" value="<?= htmlspecialchars($settings['margin_bottom_custom'] ?? '') ?>" placeholder="Ej: 50px, 5rem">
                <small class="form-hint">Solo si seleccionas "Personalizado"</small>
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
