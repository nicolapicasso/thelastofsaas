<div class="block-form">
    <div class="form-section">
        <h4>Contenido</h4>

        <div class="form-group">
            <label>Título de la sección (opcional)</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? '') ?>" placeholder="Nuestras Áreas">
        </div>

        <div class="form-group">
            <label>Subtítulo (opcional)</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Descripción breve">
        </div>
    </div>

    <div class="form-section">
        <h4>Áreas</h4>
        <p class="form-help">Configura cada área con su título, color de fondo e imagen que se mostrará en hover.</p>

        <div class="items-container" id="areasContainer">
            <?php
            $items = $content['items'] ?? [];
            foreach ($items as $index => $item):
            ?>
            <div class="item-card" data-item-index="<?= $index ?>">
                <div class="item-header">
                    <span>Área <?= $index + 1 ?></span>
                    <button type="button" class="btn btn-sm btn-danger remove-item-btn">&times;</button>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" data-item-field="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" placeholder="Desarrollo">
                    </div>
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" data-item-field="url" value="<?= htmlspecialchars($item['url'] ?? '#') ?>" placeholder="/servicios/desarrollo">
                    </div>
                </div>

                <div class="form-group">
                    <label>Subtítulo (opcional)</label>
                    <input type="text" data-item-field="subtitle" value="<?= htmlspecialchars($item['subtitle'] ?? '') ?>" placeholder="Soluciones digitales">
                </div>

                <div class="form-group">
                    <label>Descripción (opcional)</label>
                    <textarea data-item-field="description" rows="2" placeholder="Breve descripción..."><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Color de fondo</label>
                        <div class="color-input-wrapper">
                            <input type="color" data-item-field="background_color" value="<?= htmlspecialchars($item['background_color'] ?? '#1A1A1A') ?>">
                            <input type="text" data-item-field="background_color" value="<?= htmlspecialchars($item['background_color'] ?? '#1A1A1A') ?>" placeholder="#1A1A1A" class="color-text-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Color del texto (opcional)</label>
                        <div class="color-input-wrapper">
                            <input type="color" data-item-field="text_color" value="<?= htmlspecialchars($item['text_color'] ?? '#ffffff') ?>">
                            <input type="text" data-item-field="text_color" value="<?= htmlspecialchars($item['text_color'] ?? '#ffffff') ?>" placeholder="#ffffff" class="color-text-input">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Imagen (se muestra en hover)</label>
                    <div class="image-picker-field">
                        <input type="hidden" data-item-field="image" value="<?= htmlspecialchars($item['image'] ?? '') ?>">
                        <div class="image-picker-preview <?= !empty($item['image']) ? 'has-image' : '' ?>">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="">
                            <?php else: ?>
                                <div class="preview-placeholder">
                                    <i class="fas fa-image"></i>
                                    <span>Sin imagen</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="image-picker-actions">
                            <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                <i class="fas fa-upload"></i> <?= !empty($item['image']) ? 'Cambiar' : 'Seleccionar' ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= !empty($item['image']) ? 'flex' : 'none' ?>;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline add-item-btn" data-container="areasContainer">+ Añadir área</button>
    </div>

    <div class="form-section">
        <h4>Efecto de Hover</h4>
        <div class="form-group">
            <label>Tipo de efecto</label>
            <select data-setting="effect">
                <optgroup label="Deslizar">
                    <option value="reveal-up" <?= ($settings['effect'] ?? 'reveal-up') === 'reveal-up' ? 'selected' : '' ?>>Deslizar hacia arriba</option>
                    <option value="reveal-down" <?= ($settings['effect'] ?? '') === 'reveal-down' ? 'selected' : '' ?>>Deslizar hacia abajo</option>
                    <option value="reveal-left" <?= ($settings['effect'] ?? '') === 'reveal-left' ? 'selected' : '' ?>>Deslizar hacia izquierda</option>
                    <option value="reveal-right" <?= ($settings['effect'] ?? '') === 'reveal-right' ? 'selected' : '' ?>>Deslizar hacia derecha</option>
                </optgroup>
                <optgroup label="Transiciones">
                    <option value="zoom" <?= ($settings['effect'] ?? '') === 'zoom' ? 'selected' : '' ?>>Zoom</option>
                    <option value="flip" <?= ($settings['effect'] ?? '') === 'flip' ? 'selected' : '' ?>>Voltear (Flip)</option>
                    <option value="tilt" <?= ($settings['effect'] ?? '') === 'tilt' ? 'selected' : '' ?>>Inclinación 3D (Tilt)</option>
                    <option value="gradient" <?= ($settings['effect'] ?? '') === 'gradient' ? 'selected' : '' ?>>Degradado</option>
                </optgroup>
                <optgroup label="Máscaras">
                    <option value="split" <?= ($settings['effect'] ?? '') === 'split' ? 'selected' : '' ?>>División diagonal</option>
                    <option value="curtain" <?= ($settings['effect'] ?? '') === 'curtain' ? 'selected' : '' ?>>Cortina (centro)</option>
                    <option value="circle" <?= ($settings['effect'] ?? '') === 'circle' ? 'selected' : '' ?>>Círculo expandiendo</option>
                </optgroup>
            </select>
        </div>
    </div>

    <div class="form-section">
        <h4>Disposición</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Columnas</label>
                <select data-setting="columns">
                    <option value="1" <?= ($settings['columns'] ?? 4) == 1 ? 'selected' : '' ?>>1 columna</option>
                    <option value="2" <?= ($settings['columns'] ?? 4) == 2 ? 'selected' : '' ?>>2 columnas</option>
                    <option value="3" <?= ($settings['columns'] ?? 4) == 3 ? 'selected' : '' ?>>3 columnas</option>
                    <option value="4" <?= ($settings['columns'] ?? 4) == 4 ? 'selected' : '' ?>>4 columnas</option>
                    <option value="5" <?= ($settings['columns'] ?? 4) == 5 ? 'selected' : '' ?>>5 columnas</option>
                    <option value="6" <?= ($settings['columns'] ?? 4) == 6 ? 'selected' : '' ?>>6 columnas</option>
                </select>
            </div>
            <div class="form-group">
                <label>Altura</label>
                <select data-setting="height">
                    <option value="350px" <?= ($settings['height'] ?? '500px') === '350px' ? 'selected' : '' ?>>350px (Compacto)</option>
                    <option value="400px" <?= ($settings['height'] ?? '500px') === '400px' ? 'selected' : '' ?>>400px</option>
                    <option value="500px" <?= ($settings['height'] ?? '500px') === '500px' ? 'selected' : '' ?>>500px (Por defecto)</option>
                    <option value="600px" <?= ($settings['height'] ?? '500px') === '600px' ? 'selected' : '' ?>>600px</option>
                    <option value="700px" <?= ($settings['height'] ?? '500px') === '700px' ? 'selected' : '' ?>>700px</option>
                    <option value="100vh" <?= ($settings['height'] ?? '500px') === '100vh' ? 'selected' : '' ?>>100vh (Pantalla completa)</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Espacio entre columnas</label>
                <select data-setting="gap">
                    <option value="0" <?= ($settings['gap'] ?? '0') === '0' ? 'selected' : '' ?>>Sin espacio</option>
                    <option value="4px" <?= ($settings['gap'] ?? '0') === '4px' ? 'selected' : '' ?>>4px</option>
                    <option value="8px" <?= ($settings['gap'] ?? '0') === '8px' ? 'selected' : '' ?>>8px</option>
                    <option value="16px" <?= ($settings['gap'] ?? '0') === '16px' ? 'selected' : '' ?>>16px</option>
                </select>
            </div>
            <div class="form-group">
                <label>Bordes redondeados</label>
                <select data-setting="border_radius">
                    <option value="0" <?= ($settings['border_radius'] ?? '0') === '0' ? 'selected' : '' ?>>Sin bordes</option>
                    <option value="8px" <?= ($settings['border_radius'] ?? '0') === '8px' ? 'selected' : '' ?>>8px</option>
                    <option value="16px" <?= ($settings['border_radius'] ?? '0') === '16px' ? 'selected' : '' ?>>16px</option>
                    <option value="24px" <?= ($settings['border_radius'] ?? '0') === '24px' ? 'selected' : '' ?>>24px</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Texto</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Posición del texto</label>
                <select data-setting="text_position">
                    <option value="top" <?= ($settings['text_position'] ?? 'center') === 'top' ? 'selected' : '' ?>>Arriba</option>
                    <option value="center" <?= ($settings['text_position'] ?? 'center') === 'center' ? 'selected' : '' ?>>Centro</option>
                    <option value="bottom" <?= ($settings['text_position'] ?? 'center') === 'bottom' ? 'selected' : '' ?>>Abajo</option>
                </select>
            </div>
            <div class="form-group">
                <label>Color del texto (general)</label>
                <div class="color-input-wrapper">
                    <input type="color" data-setting="text_color" value="<?= htmlspecialchars($settings['text_color'] ?? '#ffffff') ?>">
                    <input type="text" data-setting="text_color" value="<?= htmlspecialchars($settings['text_color'] ?? '#ffffff') ?>" placeholder="#ffffff" class="color-text-input">
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_subtitle" <?= ($settings['show_subtitle'] ?? true) ? 'checked' : '' ?>>
                    Mostrar subtítulo
                </label>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_description" <?= ($settings['show_description'] ?? false) ? 'checked' : '' ?>>
                    Mostrar descripción
                </label>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Opciones adicionales</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Oscurecer imagen</label>
                <select data-setting="overlay_opacity">
                    <option value="0" <?= ($settings['overlay_opacity'] ?? 0) == 0 ? 'selected' : '' ?>>Sin oscurecer</option>
                    <option value="0.2" <?= ($settings['overlay_opacity'] ?? 0) == 0.2 ? 'selected' : '' ?>>20%</option>
                    <option value="0.4" <?= ($settings['overlay_opacity'] ?? 0) == 0.4 ? 'selected' : '' ?>>40%</option>
                    <option value="0.6" <?= ($settings['overlay_opacity'] ?? 0) == 0.6 ? 'selected' : '' ?>>60%</option>
                </select>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="full_width" <?= ($settings['full_width'] ?? true) ? 'checked' : '' ?>>
                    Ancho completo (sin márgenes)
                </label>
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<style>
.color-input-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}
.color-input-wrapper input[type="color"] {
    width: 40px;
    height: 32px;
    padding: 2px;
    border: 1px solid var(--color-border);
    border-radius: 4px;
    cursor: pointer;
}
.color-input-wrapper .color-text-input {
    flex: 1;
    font-family: monospace;
}
</style>
