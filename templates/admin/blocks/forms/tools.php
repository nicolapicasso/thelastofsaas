<?php
/**
 * Tools Block Admin Form
 * Displays tool logos in grid or carousel with filtering options
 * We're Sinapsis CMS
 */

use App\Models\Tool;

$toolModel = new Tool();
$categories = $toolModel->getCategoriesWithTools();
$allTools = $toolModel->getActive(100);

// Get selected tools and ensure it's an array of integers
$selectedTools = $settings['selected_tools'] ?? [];
if (is_string($selectedTools)) {
    $selectedTools = json_decode($selectedTools, true) ?? [];
}
// Ensure all values are integers for proper comparison
$selectedTools = array_map('intval', $selectedTools);
?>

<div class="block-form">
    <div class="form-section">
        <h4>Configuración del bloque Herramientas</h4>
        <div class="form-group">
            <label>Título de la sección</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Herramientas que utilizamos') ?>">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Modo de visualización</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Tipo de visualización</label>
                <select data-setting="display_mode" id="tools-display-mode">
                    <option value="grid" <?= ($settings['display_mode'] ?? 'grid') === 'grid' ? 'selected' : '' ?>>Cuadrícula (Grid)</option>
                    <option value="carousel" <?= ($settings['display_mode'] ?? 'grid') === 'carousel' ? 'selected' : '' ?>>Carrusel horizontal</option>
                </select>
            </div>
        </div>

        <div class="form-row" id="tools-grid-options" style="<?= ($settings['display_mode'] ?? 'grid') !== 'grid' ? 'display: none;' : '' ?>">
            <div class="form-group">
                <label>Columnas</label>
                <select data-setting="columns">
                    <option value="3" <?= ($settings['columns'] ?? 4) == 3 ? 'selected' : '' ?>>3 columnas</option>
                    <option value="4" <?= ($settings['columns'] ?? 4) == 4 ? 'selected' : '' ?>>4 columnas</option>
                    <option value="5" <?= ($settings['columns'] ?? 4) == 5 ? 'selected' : '' ?>>5 columnas</option>
                    <option value="6" <?= ($settings['columns'] ?? 4) == 6 ? 'selected' : '' ?>>6 columnas</option>
                    <option value="8" <?= ($settings['columns'] ?? 4) == 8 ? 'selected' : '' ?>>8 columnas</option>
                </select>
            </div>
        </div>

        <div class="form-row" id="tools-carousel-options" style="<?= ($settings['display_mode'] ?? 'grid') !== 'carousel' ? 'display: none;' : '' ?>">
            <div class="form-group">
                <label>Logos visibles</label>
                <select data-setting="visible_items">
                    <option value="3" <?= ($settings['visible_items'] ?? 5) == 3 ? 'selected' : '' ?>>3 logos</option>
                    <option value="4" <?= ($settings['visible_items'] ?? 5) == 4 ? 'selected' : '' ?>>4 logos</option>
                    <option value="5" <?= ($settings['visible_items'] ?? 5) == 5 ? 'selected' : '' ?>>5 logos</option>
                    <option value="6" <?= ($settings['visible_items'] ?? 5) == 6 ? 'selected' : '' ?>>6 logos</option>
                    <option value="8" <?= ($settings['visible_items'] ?? 5) == 8 ? 'selected' : '' ?>>8 logos</option>
                </select>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="autoplay" id="tools-autoplay" <?= !empty($settings['autoplay']) ? 'checked' : '' ?>>
                    <span>Reproducción automática</span>
                </label>
            </div>
            <div class="form-group" id="tools-autoplay-speed-group" style="<?= empty($settings['autoplay']) ? 'display: none;' : '' ?>">
                <label>Velocidad de animación</label>
                <select data-setting="autoplay_speed">
                    <option value="slow" <?= ($settings['autoplay_speed'] ?? 'normal') === 'slow' ? 'selected' : '' ?>>Lenta (40s)</option>
                    <option value="normal" <?= ($settings['autoplay_speed'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Normal (25s)</option>
                    <option value="fast" <?= ($settings['autoplay_speed'] ?? 'normal') === 'fast' ? 'selected' : '' ?>>Rápida (15s)</option>
                </select>
                <small class="form-help">Tiempo para completar un ciclo completo</small>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Selección de herramientas</h4>
        <div class="form-group">
            <label>Modo de selección</label>
            <select data-setting="selection_mode" id="tools-selection-mode">
                <option value="all" <?= ($settings['selection_mode'] ?? 'all') === 'all' ? 'selected' : '' ?>>Todas las herramientas</option>
                <option value="category" <?= ($settings['selection_mode'] ?? 'all') === 'category' ? 'selected' : '' ?>>Filtrar por categoría</option>
                <option value="manual" <?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'selected' : '' ?>>Selección manual</option>
            </select>
        </div>

        <div class="form-group" id="tools-category-filter-group" style="<?= ($settings['selection_mode'] ?? 'all') !== 'category' ? 'display: none;' : '' ?>">
            <label>Categoría</label>
            <select data-setting="category_filter">
                <option value="">-- Seleccionar categoría --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= ($settings['category_filter'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?> (<?= $category['tool_count'] ?> herramientas)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" id="tools-manual-selection-group" style="<?= ($settings['selection_mode'] ?? 'all') !== 'manual' ? 'display: none;' : '' ?>">
            <label>Seleccionar herramientas</label>
            <div class="tools-checklist">
                <?php foreach ($allTools as $tool): ?>
                    <label class="checkbox-label tool-checkbox">
                        <input type="checkbox"
                               name="selected_tools[]"
                               value="<?= $tool['id'] ?>"
                               <?= in_array((int)$tool['id'], $selectedTools, true) ? 'checked' : '' ?>>
                        <span class="tool-checkbox-content">
                            <?php if (!empty($tool['logo'])): ?>
                                <img src="<?= htmlspecialchars($tool['logo']) ?>" alt="" class="tool-checkbox-logo">
                            <?php endif; ?>
                            <span class="tool-checkbox-name"><?= htmlspecialchars($tool['title'] ?? $tool['name'] ?? '') ?></span>
                            <?php if (!empty($tool['category_name'])): ?>
                                <span class="tool-checkbox-category"><?= htmlspecialchars($tool['category_name']) ?></span>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <input type="hidden" data-setting="selected_tools" id="selected-tools-input" value="<?= htmlspecialchars(json_encode($selectedTools)) ?>">
        </div>

        <div class="form-group" id="tools-limit-group" style="<?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'display: none;' : '' ?>">
            <label>Número máximo de logos</label>
            <input type="number" data-setting="limit" value="<?= $settings['limit'] ?? 12 ?>" min="1" max="50">
        </div>
    </div>

    <div class="form-section">
        <h4>Estilo de logos</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Altura de logos (px)</label>
                <input type="number" data-setting="logo_height" value="<?= $settings['logo_height'] ?? 60 ?>" min="30" max="150" step="5">
                <small class="form-help">Los logos mantendrán su proporción con esta altura fija</small>
            </div>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="grayscale" <?= ($settings['grayscale'] ?? true) ? 'checked' : '' ?>>
                <span>Efecto escala de grises (color al pasar el cursor)</span>
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_name" <?= !empty($settings['show_name']) ? 'checked' : '' ?>>
                <span>Mostrar nombre de la herramienta</span>
            </label>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <div class="form-section">
        <h4>Enlace "Ver más"</h4>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-content="show_more" <?= !empty($content['show_more']) ? 'checked' : '' ?>>
                <span>Mostrar enlace "Ver más"</span>
            </label>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>URL del enlace</label>
                <input type="text" data-content="more_url" value="<?= htmlspecialchars($content['more_url'] ?? '/herramientas') ?>" placeholder="/herramientas">
            </div>
            <div class="form-group">
                <label>Texto del enlace</label>
                <input type="text" data-content="more_text" value="<?= htmlspecialchars($content['more_text'] ?? 'Ver todas las herramientas') ?>" placeholder="Ver todas las herramientas">
            </div>
        </div>
    </div>

    <div class="info-box">
        <p><strong>Nota:</strong> Solo se muestran herramientas que tengan logo configurado. Gestiona las herramientas desde <a href="/admin/tools" target="_blank">Administrar Herramientas</a>.</p>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<style>
.tools-checklist {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    background: var(--color-gray-50);
}

.tool-checkbox {
    display: flex;
    align-items: center;
    padding: var(--spacing-sm);
    margin-bottom: var(--spacing-xs);
    background: white;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-gray-100);
    cursor: pointer;
    transition: all 0.2s ease;
}

.tool-checkbox:hover {
    border-color: var(--color-primary);
    background: var(--color-primary-light);
}

.tool-checkbox input[type="checkbox"] {
    margin-right: var(--spacing-sm);
    flex-shrink: 0;
}

.tool-checkbox-content {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex: 1;
    min-width: 0;
}

.tool-checkbox-logo {
    height: 24px;
    width: auto;
    max-width: 60px;
    object-fit: contain;
    flex-shrink: 0;
}

.tool-checkbox-name {
    font-weight: 500;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.tool-checkbox-category {
    font-size: 11px;
    color: var(--color-gray-500);
    background: var(--color-gray-100);
    padding: 2px 8px;
    border-radius: var(--radius-full);
    flex-shrink: 0;
}
</style>

<script>
(function() {
    // Toggle display mode options
    const displayModeSelect = document.getElementById('tools-display-mode');
    const gridOptions = document.getElementById('tools-grid-options');
    const carouselOptions = document.getElementById('tools-carousel-options');

    if (displayModeSelect) {
        displayModeSelect.addEventListener('change', function() {
            gridOptions.style.display = this.value === 'grid' ? '' : 'none';
            carouselOptions.style.display = this.value === 'carousel' ? '' : 'none';
        });
    }

    // Toggle selection mode options
    const selectionModeSelect = document.getElementById('tools-selection-mode');
    const categoryFilterGroup = document.getElementById('tools-category-filter-group');
    const manualSelectionGroup = document.getElementById('tools-manual-selection-group');
    const limitGroup = document.getElementById('tools-limit-group');

    if (selectionModeSelect) {
        selectionModeSelect.addEventListener('change', function() {
            categoryFilterGroup.style.display = this.value === 'category' ? '' : 'none';
            manualSelectionGroup.style.display = this.value === 'manual' ? '' : 'none';
            limitGroup.style.display = this.value === 'manual' ? 'none' : '';
        });
    }

    // Toggle autoplay speed visibility
    const autoplayCheckbox = document.getElementById('tools-autoplay');
    const autoplaySpeedGroup = document.getElementById('tools-autoplay-speed-group');

    if (autoplayCheckbox && autoplaySpeedGroup) {
        autoplayCheckbox.addEventListener('change', function() {
            autoplaySpeedGroup.style.display = this.checked ? '' : 'none';
        });
    }

    // Handle manual selection checkboxes
    const selectedToolsInput = document.getElementById('selected-tools-input');
    const checkboxes = document.querySelectorAll('input[name="selected_tools[]"]');

    function updateSelectedTools() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value));
        selectedToolsInput.value = JSON.stringify(selected);
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedTools);
    });
})();
</script>
