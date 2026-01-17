<?php
/**
 * Business Types (Características Resumidas) Block Admin Form
 * Display summary characteristics in various modes
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
        <p class="form-help">Añade las características que quieres mostrar.</p>

        <div class="items-container" id="businessTypesContainer">
            <?php
            $items = $content['items'] ?? [];
            foreach ($items as $index => $item):
            ?>
            <div class="item-card" data-item-index="<?= $index ?>">
                <div class="item-header">
                    <span>Elemento <?= $index + 1 ?></span>
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
                    <textarea data-item-field="description" rows="2" placeholder="Breve descripción..."><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Enlace (opcional)</label>
                    <input type="text" data-item-field="link" value="<?= htmlspecialchars($item['link'] ?? '') ?>" placeholder="/pagina">
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline add-item-btn" data-container="businessTypesContainer">+ Añadir elemento</button>
    </div>

    <div class="form-section">
        <h4>Llamada a la acción</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Texto del botón</label>
                <input type="text" data-content="cta_text" value="<?= htmlspecialchars($content['cta_text'] ?? '') ?>" placeholder="Ver todos">
            </div>
            <div class="form-group">
                <label>URL del botón</label>
                <input type="text" data-content="cta_url" value="<?= htmlspecialchars($content['cta_url'] ?? '') ?>" placeholder="/pagina">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Modo de visualización</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Estilo de presentación</label>
                <select data-setting="display_mode" id="bt-display-mode">
                    <option value="cards" <?= ($settings['display_mode'] ?? 'cards') === 'cards' ? 'selected' : '' ?>>Tarjetas con icono</option>
                    <option value="numbered" <?= ($settings['display_mode'] ?? 'cards') === 'numbered' ? 'selected' : '' ?>>Lista numerada</option>
                    <option value="lettered" <?= ($settings['display_mode'] ?? 'cards') === 'lettered' ? 'selected' : '' ?>>Lista con letras (A, B, C...)</option>
                    <option value="icons_only" <?= ($settings['display_mode'] ?? 'cards') === 'icons_only' ? 'selected' : '' ?>>Solo iconos</option>
                </select>
            </div>
            <div class="form-group">
                <label>Columnas</label>
                <select data-setting="columns">
                    <option value="3" <?= ($settings['columns'] ?? 4) == 3 ? 'selected' : '' ?>>3 columnas</option>
                    <option value="4" <?= ($settings['columns'] ?? 4) == 4 ? 'selected' : '' ?>>4 columnas</option>
                    <option value="5" <?= ($settings['columns'] ?? 4) == 5 ? 'selected' : '' ?>>5 columnas</option>
                    <option value="6" <?= ($settings['columns'] ?? 4) == 6 ? 'selected' : '' ?>>6 columnas</option>
                </select>
            </div>
        </div>

        <!-- Cards mode options -->
        <div class="form-row display-mode-options" id="cards-options" style="<?= ($settings['display_mode'] ?? 'cards') !== 'cards' ? 'display: none;' : '' ?>">
            <div class="form-group">
                <label>Estilo de tarjeta</label>
                <select data-setting="card_style">
                    <option value="minimal" <?= ($settings['card_style'] ?? 'minimal') === 'minimal' ? 'selected' : '' ?>>Minimalista</option>
                    <option value="bordered" <?= ($settings['card_style'] ?? 'minimal') === 'bordered' ? 'selected' : '' ?>>Con borde</option>
                    <option value="shadow" <?= ($settings['card_style'] ?? 'minimal') === 'shadow' ? 'selected' : '' ?>>Con sombra</option>
                    <option value="filled" <?= ($settings['card_style'] ?? 'minimal') === 'filled' ? 'selected' : '' ?>>Relleno</option>
                </select>
            </div>
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

        <!-- Numbered/Lettered mode options -->
        <div class="form-row display-mode-options" id="numbered-options" style="<?= !in_array($settings['display_mode'] ?? 'cards', ['numbered', 'lettered']) ? 'display: none;' : '' ?>">
            <div class="form-group">
                <label>Estilo de número/letra</label>
                <select data-setting="number_style">
                    <option value="large" <?= ($settings['number_style'] ?? 'large') === 'large' ? 'selected' : '' ?>>Grande</option>
                    <option value="small" <?= ($settings['number_style'] ?? 'large') === 'small' ? 'selected' : '' ?>>Pequeño</option>
                    <option value="circle" <?= ($settings['number_style'] ?? 'large') === 'circle' ? 'selected' : '' ?>>Círculo</option>
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
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <?php include TEMPLATES_PATH . '/admin/partials/animation-settings.php'; ?>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<script>
(function() {
    // Toggle display mode options
    const displayModeSelect = document.getElementById('bt-display-mode');
    const cardsOptions = document.getElementById('cards-options');
    const numberedOptions = document.getElementById('numbered-options');

    if (displayModeSelect) {
        displayModeSelect.addEventListener('change', function() {
            const mode = this.value;
            cardsOptions.style.display = mode === 'cards' ? '' : 'none';
            numberedOptions.style.display = (mode === 'numbered' || mode === 'lettered') ? '' : 'none';
        });
    }
})();
</script>
