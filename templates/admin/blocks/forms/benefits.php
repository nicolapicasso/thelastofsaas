<?php
/**
 * Benefits (Características Detalladas) Block Admin Form
 * Display detailed characteristics with multiple modes
 * We're Sinapsis CMS
 */
?>
<div class="block-form">
    <div class="form-section">
        <h4>Contenido</h4>

        <div class="form-group">
            <label>Título de la sección</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Características') ?>" placeholder="Características">
        </div>

        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Descripción opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Características</h4>
        <p class="form-help">Añade las características detalladas que quieres mostrar.</p>

        <div class="items-container" id="benefitsContainer">
            <?php
            $items = $content['items'] ?? [];
            foreach ($items as $index => $item):
            ?>
            <div class="item-card" data-item-index="<?= $index ?>">
                <div class="item-header">
                    <span>Característica <?= $index + 1 ?></span>
                    <button type="button" class="btn btn-sm btn-danger remove-item-btn">&times;</button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" data-item-field="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" placeholder="Título de la característica">
                    </div>
                    <div class="form-group">
                        <label>Icono</label>
                        <div class="icon-input-wrapper">
                            <div class="icon-input-preview">
                                <i class="<?= htmlspecialchars($item['icon'] ?? 'fas fa-check') ?>"></i>
                            </div>
                            <input type="text" data-item-field="icon" value="<?= htmlspecialchars($item['icon'] ?? '') ?>" placeholder="fas fa-check">
                            <button type="button" class="icon-input-btn">Elegir</button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea data-item-field="description" rows="3" placeholder="Descripción detallada de la característica..."><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline add-item-btn" data-container="benefitsContainer">+ Añadir característica</button>
    </div>

    <div class="form-section">
        <h4>Modo de visualización</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Tipo de presentación</label>
                <select data-setting="display_mode" id="benefits-display-mode">
                    <option value="cards" <?= ($settings['display_mode'] ?? 'cards') === 'cards' ? 'selected' : '' ?>>Tarjetas</option>
                    <option value="list" <?= ($settings['display_mode'] ?? 'cards') === 'list' ? 'selected' : '' ?>>Lista</option>
                    <option value="accordion" <?= ($settings['display_mode'] ?? 'cards') === 'accordion' ? 'selected' : '' ?>>Acordeón (expandible)</option>
                </select>
            </div>
            <div class="form-group" id="benefits-columns-group">
                <label>Columnas</label>
                <select data-setting="columns">
                    <option value="1" <?= ($settings['columns'] ?? 2) == 1 ? 'selected' : '' ?>>1 columna</option>
                    <option value="2" <?= ($settings['columns'] ?? 2) == 2 ? 'selected' : '' ?>>2 columnas</option>
                    <option value="3" <?= ($settings['columns'] ?? 2) == 3 ? 'selected' : '' ?>>3 columnas</option>
                </select>
            </div>
        </div>

        <!-- Cards mode options -->
        <div class="form-row display-mode-options" id="cards-mode-options" style="<?= ($settings['display_mode'] ?? 'cards') !== 'cards' ? 'display: none;' : '' ?>">
            <div class="form-group">
                <label>Estilo de tarjeta</label>
                <select data-setting="card_style">
                    <option value="bordered" <?= ($settings['card_style'] ?? 'bordered') === 'bordered' ? 'selected' : '' ?>>Con borde</option>
                    <option value="shadow" <?= ($settings['card_style'] ?? 'bordered') === 'shadow' ? 'selected' : '' ?>>Con sombra</option>
                    <option value="minimal" <?= ($settings['card_style'] ?? 'bordered') === 'minimal' ? 'selected' : '' ?>>Minimalista</option>
                    <option value="filled" <?= ($settings['card_style'] ?? 'bordered') === 'filled' ? 'selected' : '' ?>>Relleno con color</option>
                </select>
            </div>
            <div class="form-group">
                <label>Posición del icono</label>
                <select data-setting="icon_position">
                    <option value="top" <?= ($settings['icon_position'] ?? 'top') === 'top' ? 'selected' : '' ?>>Arriba</option>
                    <option value="left" <?= ($settings['icon_position'] ?? 'top') === 'left' ? 'selected' : '' ?>>Izquierda</option>
                    <option value="inline" <?= ($settings['icon_position'] ?? 'top') === 'inline' ? 'selected' : '' ?>>En línea con título</option>
                </select>
            </div>
        </div>

        <div class="form-row display-mode-options" id="hover-effect-options" style="<?= ($settings['display_mode'] ?? 'cards') === 'accordion' ? 'display: none;' : '' ?>">
            <div class="form-group">
                <label>Efecto hover</label>
                <select data-setting="hover_effect">
                    <option value="none" <?= ($settings['hover_effect'] ?? 'lift') === 'none' ? 'selected' : '' ?>>Ninguno</option>
                    <option value="lift" <?= ($settings['hover_effect'] ?? 'lift') === 'lift' ? 'selected' : '' ?>>Elevar</option>
                    <option value="glow" <?= ($settings['hover_effect'] ?? 'lift') === 'glow' ? 'selected' : '' ?>>Brillar</option>
                    <option value="border" <?= ($settings['hover_effect'] ?? 'lift') === 'border' ? 'selected' : '' ?>>Borde de color</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_icon" <?= ($settings['show_icon'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar iconos</span>
                </label>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_description" <?= ($settings['show_description'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar descripciones</span>
                </label>
            </div>
        </div>

        <div class="form-row" id="icon-style-options">
            <div class="form-group">
                <label>Estilo de icono</label>
                <select data-setting="icon_style">
                    <option value="gradient" <?= ($settings['icon_style'] ?? 'gradient') === 'gradient' ? 'selected' : '' ?>>Degradado (colores marca)</option>
                    <option value="light" <?= ($settings['icon_style'] ?? 'gradient') === 'light' ? 'selected' : '' ?>>Claro (icono negro sobre blanco)</option>
                    <option value="dark" <?= ($settings['icon_style'] ?? 'gradient') === 'dark' ? 'selected' : '' ?>>Oscuro (icono blanco sobre negro)</option>
                </select>
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <?php include TEMPLATES_PATH . '/admin/partials/animation-settings.php'; ?>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<script>
(function() {
    // Toggle display mode options
    const displayModeSelect = document.getElementById('benefits-display-mode');
    const cardsModeOptions = document.getElementById('cards-mode-options');
    const hoverEffectOptions = document.getElementById('hover-effect-options');
    const columnsGroup = document.getElementById('benefits-columns-group');

    if (displayModeSelect) {
        displayModeSelect.addEventListener('change', function() {
            const mode = this.value;

            // Show/hide cards-specific options
            cardsModeOptions.style.display = mode === 'cards' ? '' : 'none';

            // Show/hide hover effect (not for accordion)
            hoverEffectOptions.style.display = mode === 'accordion' ? 'none' : '';

            // Accordion is always 1 column
            if (mode === 'accordion') {
                columnsGroup.querySelector('select').value = '1';
            }
        });
    }
})();
</script>
