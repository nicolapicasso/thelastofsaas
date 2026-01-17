<?php
/**
 * Block Layout Settings Partial
 * Controls block width and layout options
 * We're Sinapsis CMS
 */
?>
<div class="form-section">
    <h4>Layout</h4>
    <p class="form-section-description">Configura el ancho del bloque para crear layouts de columnas</p>

    <div class="form-row">
        <div class="form-group">
            <label>Ancho del bloque</label>
            <select data-setting="block_width">
                <option value="full" <?= ($settings['block_width'] ?? 'full') === 'full' ? 'selected' : '' ?>>Ancho completo</option>
                <option value="half" <?= ($settings['block_width'] ?? '') === 'half' ? 'selected' : '' ?>>Mitad (50%)</option>
                <option value="third" <?= ($settings['block_width'] ?? '') === 'third' ? 'selected' : '' ?>>Tercio (33%)</option>
                <option value="two-thirds" <?= ($settings['block_width'] ?? '') === 'two-thirds' ? 'selected' : '' ?>>Dos tercios (66%)</option>
                <option value="quarter" <?= ($settings['block_width'] ?? '') === 'quarter' ? 'selected' : '' ?>>Cuarto (25%)</option>
                <option value="three-quarters" <?= ($settings['block_width'] ?? '') === 'three-quarters' ? 'selected' : '' ?>>Tres cuartos (75%)</option>
            </select>
            <small class="form-help">Los bloques con anchos parciales se colocarán lado a lado automáticamente.</small>
        </div>
        <div class="form-group">
            <label>Alineación vertical</label>
            <select data-setting="vertical_align">
                <option value="stretch" <?= ($settings['vertical_align'] ?? 'stretch') === 'stretch' ? 'selected' : '' ?>>Estirar</option>
                <option value="start" <?= ($settings['vertical_align'] ?? '') === 'start' ? 'selected' : '' ?>>Arriba</option>
                <option value="center" <?= ($settings['vertical_align'] ?? '') === 'center' ? 'selected' : '' ?>>Centro</option>
                <option value="end" <?= ($settings['vertical_align'] ?? '') === 'end' ? 'selected' : '' ?>>Abajo</option>
            </select>
        </div>
    </div>
</div>
