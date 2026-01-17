<?php
/**
 * Services Block Admin Form
 * Displays services in grid or carousel with multiple styles
 * We're Sinapsis CMS
 */

use App\Models\Service;
use App\Models\Category;

$serviceModel = new Service();
$categoryModel = new Category();
$categories = $categoryModel->getActive();
$allServices = $serviceModel->getAllForAdmin();

// Get selected services and ensure it's an array of integers
$selectedServices = $settings['selected_services'] ?? [];
if (is_string($selectedServices)) {
    $selectedServices = json_decode($selectedServices, true) ?? [];
}
$selectedServices = array_map('intval', $selectedServices);
?>

<div class="block-form">
    <div class="form-section">
        <h4>Contenido</h4>
        <div class="form-group">
            <label>Título de la sección</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Nuestros Servicios') ?>">
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
                <label>Estilo de presentación</label>
                <select data-setting="display_mode" id="services-display-mode">
                    <option value="cards" <?= ($settings['display_mode'] ?? 'cards') === 'cards' ? 'selected' : '' ?>>Tarjetas completas</option>
                    <option value="list" <?= ($settings['display_mode'] ?? 'cards') === 'list' ? 'selected' : '' ?>>Lista horizontal</option>
                    <option value="compact" <?= ($settings['display_mode'] ?? 'cards') === 'compact' ? 'selected' : '' ?>>Tarjetas compactas</option>
                    <option value="icons_only" <?= ($settings['display_mode'] ?? 'cards') === 'icons_only' ? 'selected' : '' ?>>Solo iconos</option>
                </select>
            </div>
            <div class="form-group">
                <label>Disposición</label>
                <select data-setting="layout_mode" id="services-layout-mode">
                    <option value="grid" <?= ($settings['layout_mode'] ?? 'grid') === 'grid' ? 'selected' : '' ?>>Cuadrícula (Grid)</option>
                    <option value="carousel" <?= ($settings['layout_mode'] ?? 'grid') === 'carousel' ? 'selected' : '' ?>>Carrusel horizontal</option>
                </select>
            </div>
        </div>

        <div class="form-row" id="grid-options" style="<?= ($settings['layout_mode'] ?? 'grid') !== 'grid' ? 'display: none;' : '' ?>">
            <div class="form-group">
                <label>Columnas</label>
                <select data-setting="columns">
                    <option value="2" <?= ($settings['columns'] ?? 3) == 2 ? 'selected' : '' ?>>2 columnas</option>
                    <option value="3" <?= ($settings['columns'] ?? 3) == 3 ? 'selected' : '' ?>>3 columnas</option>
                    <option value="4" <?= ($settings['columns'] ?? 3) == 4 ? 'selected' : '' ?>>4 columnas</option>
                </select>
            </div>
        </div>

        <div class="form-row" id="carousel-options" style="<?= ($settings['layout_mode'] ?? 'grid') !== 'carousel' ? 'display: none;' : '' ?>">
            <div class="form-group">
                <label>Servicios visibles</label>
                <select data-setting="visible_items">
                    <option value="2" <?= ($settings['visible_items'] ?? 3) == 2 ? 'selected' : '' ?>>2 servicios</option>
                    <option value="3" <?= ($settings['visible_items'] ?? 3) == 3 ? 'selected' : '' ?>>3 servicios</option>
                    <option value="4" <?= ($settings['visible_items'] ?? 3) == 4 ? 'selected' : '' ?>>4 servicios</option>
                </select>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="autoplay" <?= !empty($settings['autoplay']) ? 'checked' : '' ?>>
                    <span>Reproducción automática</span>
                </label>
            </div>
            <div class="form-group" id="autoplay-speed-group" style="<?= empty($settings['autoplay']) ? 'display: none;' : '' ?>">
                <label>Velocidad</label>
                <select data-setting="autoplay_speed">
                    <option value="slow" <?= ($settings['autoplay_speed'] ?? 'normal') === 'slow' ? 'selected' : '' ?>>Lenta</option>
                    <option value="normal" <?= ($settings['autoplay_speed'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="fast" <?= ($settings['autoplay_speed'] ?? 'normal') === 'fast' ? 'selected' : '' ?>>Rápida</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section" id="card-style-section">
        <h4>Estilo de tarjetas</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Estilo de tarjeta</label>
                <select data-setting="card_style">
                    <option value="shadow" <?= ($settings['card_style'] ?? 'shadow') === 'shadow' ? 'selected' : '' ?>>Con sombra</option>
                    <option value="bordered" <?= ($settings['card_style'] ?? 'shadow') === 'bordered' ? 'selected' : '' ?>>Con borde</option>
                    <option value="minimal" <?= ($settings['card_style'] ?? 'shadow') === 'minimal' ? 'selected' : '' ?>>Minimalista</option>
                    <option value="filled" <?= ($settings['card_style'] ?? 'shadow') === 'filled' ? 'selected' : '' ?>>Relleno con color</option>
                </select>
            </div>
            <div class="form-group">
                <label>Efecto hover</label>
                <select data-setting="hover_effect">
                    <option value="lift" <?= ($settings['hover_effect'] ?? 'lift') === 'lift' ? 'selected' : '' ?>>Elevar</option>
                    <option value="glow" <?= ($settings['hover_effect'] ?? 'lift') === 'glow' ? 'selected' : '' ?>>Brillar</option>
                    <option value="border" <?= ($settings['hover_effect'] ?? 'lift') === 'border' ? 'selected' : '' ?>>Borde de color</option>
                    <option value="none" <?= ($settings['hover_effect'] ?? 'lift') === 'none' ? 'selected' : '' ?>>Ninguno</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_icon" <?= ($settings['show_icon'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar icono</span>
                </label>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_description" <?= ($settings['show_description'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar descripción</span>
                </label>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_category" <?= ($settings['show_category'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar categoría</span>
                </label>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_link" <?= ($settings['show_link'] ?? true) ? 'checked' : '' ?>>
                    <span>Enlazar a la página del servicio</span>
                </label>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Selección de servicios</h4>
        <div class="form-group">
            <label>Modo de selección</label>
            <select data-setting="selection_mode" id="services-selection-mode">
                <option value="all" <?= ($settings['selection_mode'] ?? 'all') === 'all' ? 'selected' : '' ?>>Todos los servicios</option>
                <option value="category" <?= ($settings['selection_mode'] ?? 'all') === 'category' ? 'selected' : '' ?>>Filtrar por categoría</option>
                <option value="manual" <?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'selected' : '' ?>>Selección manual</option>
            </select>
        </div>

        <div class="form-group" id="category-filter-group" style="<?= ($settings['selection_mode'] ?? 'all') !== 'category' ? 'display: none;' : '' ?>">
            <label>Categoría</label>
            <select data-setting="category_filter">
                <option value="">-- Seleccionar categoría --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= ($settings['category_filter'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" id="manual-selection-group" style="<?= ($settings['selection_mode'] ?? 'all') !== 'manual' ? 'display: none;' : '' ?>">
            <label>Seleccionar servicios</label>
            <div class="services-checklist">
                <?php foreach ($allServices as $service): ?>
                    <label class="checkbox-label service-checkbox">
                        <input type="checkbox"
                               name="selected_services[]"
                               value="<?= $service['id'] ?>"
                               <?= in_array((int)$service['id'], $selectedServices, true) ? 'checked' : '' ?>>
                        <span class="service-checkbox-content">
                            <?php if (!empty($service['icon_class'])): ?>
                                <i class="<?= htmlspecialchars($service['icon_class']) ?> service-checkbox-icon"></i>
                            <?php endif; ?>
                            <span class="service-checkbox-name"><?= htmlspecialchars($service['title']) ?></span>
                            <?php if (!empty($service['category_name'])): ?>
                                <span class="service-checkbox-category"><?= htmlspecialchars($service['category_name']) ?></span>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <input type="hidden" data-setting="selected_services" id="selected-services-input" value="<?= htmlspecialchars(json_encode($selectedServices)) ?>">
        </div>

        <div class="form-group" id="limit-group" style="<?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'display: none;' : '' ?>">
            <label>Número máximo de servicios</label>
            <input type="number" data-setting="limit" value="<?= $settings['limit'] ?? 6 ?>" min="1" max="24">
        </div>
    </div>

    <div class="form-section">
        <h4>Llamada a la acción</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Texto del botón</label>
                <input type="text" data-content="cta_text" value="<?= htmlspecialchars($content['cta_text'] ?? '') ?>" placeholder="Ver todos los servicios">
            </div>
            <div class="form-group">
                <label>URL del botón</label>
                <input type="text" data-content="cta_url" value="<?= htmlspecialchars($content['cta_url'] ?? '') ?>" placeholder="/servicios">
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <div class="info-box">
        <p><strong>Nota:</strong> Solo se muestran servicios activos. Gestiona los servicios desde <a href="/admin/services" target="_blank">Administrar Servicios</a>.</p>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<style>
.services-checklist {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    background: var(--color-gray-50);
}

.service-checkbox {
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

.service-checkbox:hover {
    border-color: var(--color-primary);
    background: var(--color-primary-light);
}

.service-checkbox input[type="checkbox"] {
    margin-right: var(--spacing-sm);
    flex-shrink: 0;
}

.service-checkbox-content {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex: 1;
    min-width: 0;
}

.service-checkbox-icon {
    font-size: 16px;
    color: var(--color-primary);
    width: 24px;
    text-align: center;
    flex-shrink: 0;
}

.service-checkbox-name {
    font-weight: 500;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.service-checkbox-category {
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
    // Toggle layout mode options
    const layoutModeSelect = document.getElementById('services-layout-mode');
    const gridOptions = document.getElementById('grid-options');
    const carouselOptions = document.getElementById('carousel-options');

    if (layoutModeSelect) {
        layoutModeSelect.addEventListener('change', function() {
            gridOptions.style.display = this.value === 'grid' ? '' : 'none';
            carouselOptions.style.display = this.value === 'carousel' ? '' : 'none';
        });
    }

    // Toggle selection mode options
    const selectionModeSelect = document.getElementById('services-selection-mode');
    const categoryFilterGroup = document.getElementById('category-filter-group');
    const manualSelectionGroup = document.getElementById('manual-selection-group');
    const limitGroup = document.getElementById('limit-group');

    if (selectionModeSelect) {
        selectionModeSelect.addEventListener('change', function() {
            categoryFilterGroup.style.display = this.value === 'category' ? '' : 'none';
            manualSelectionGroup.style.display = this.value === 'manual' ? '' : 'none';
            limitGroup.style.display = this.value === 'manual' ? 'none' : '';
        });
    }

    // Toggle autoplay speed visibility
    const autoplayCheckbox = document.querySelector('[data-setting="autoplay"]');
    const autoplaySpeedGroup = document.getElementById('autoplay-speed-group');

    if (autoplayCheckbox && autoplaySpeedGroup) {
        autoplayCheckbox.addEventListener('change', function() {
            autoplaySpeedGroup.style.display = this.checked ? '' : 'none';
        });
    }

    // Handle manual selection checkboxes
    const selectedServicesInput = document.getElementById('selected-services-input');
    const checkboxes = document.querySelectorAll('input[name="selected_services[]"]');

    function updateSelectedServices() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value));
        selectedServicesInput.value = JSON.stringify(selected);
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedServices);
    });
})();
</script>
