<?php
/**
 * Clients Block Admin Form
 * Displays client logos in grid or carousel
 * We're Sinapsis CMS
 */

use App\Models\Client;

$clientModel = new Client();
$industries = $clientModel->getIndustries();
$allClients = $clientModel->getAll(100);

// Get selected clients and ensure it's an array of integers
$selectedClients = $settings['selected_clients'] ?? [];
if (is_string($selectedClients)) {
    $selectedClients = json_decode($selectedClients, true) ?? [];
}
// Ensure all values are integers for proper comparison
$selectedClients = array_map('intval', $selectedClients);
?>

<div class="block-form">
    <div class="form-section">
        <h4>Configuración del bloque Clientes</h4>
        <div class="form-group">
            <label>Título de la sección</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Empresas que confían en nosotros') ?>">
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
                <select data-setting="display_mode" id="clients-display-mode">
                    <option value="grid" <?= ($settings['display_mode'] ?? 'grid') === 'grid' ? 'selected' : '' ?>>Cuadrícula (Grid)</option>
                    <option value="carousel" <?= ($settings['display_mode'] ?? 'grid') === 'carousel' ? 'selected' : '' ?>>Carrusel horizontal</option>
                </select>
            </div>
        </div>

        <div class="form-row" id="grid-options" style="<?= ($settings['display_mode'] ?? 'grid') !== 'grid' ? 'display: none;' : '' ?>">
            <div class="form-group">
                <label>Columnas</label>
                <select data-setting="columns">
                    <option value="3" <?= ($settings['columns'] ?? 6) == 3 ? 'selected' : '' ?>>3 columnas</option>
                    <option value="4" <?= ($settings['columns'] ?? 6) == 4 ? 'selected' : '' ?>>4 columnas</option>
                    <option value="5" <?= ($settings['columns'] ?? 6) == 5 ? 'selected' : '' ?>>5 columnas</option>
                    <option value="6" <?= ($settings['columns'] ?? 6) == 6 ? 'selected' : '' ?>>6 columnas</option>
                    <option value="8" <?= ($settings['columns'] ?? 6) == 8 ? 'selected' : '' ?>>8 columnas</option>
                </select>
            </div>
        </div>

        <div class="form-row" id="carousel-options" style="<?= ($settings['display_mode'] ?? 'grid') !== 'carousel' ? 'display: none;' : '' ?>">
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
                    <input type="checkbox" data-setting="autoplay" <?= !empty($settings['autoplay']) ? 'checked' : '' ?>>
                    <span>Reproducción automática</span>
                </label>
            </div>
            <div class="form-group" id="autoplay-speed-group" style="<?= empty($settings['autoplay']) ? 'display: none;' : '' ?>">
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
        <h4>Selección de clientes</h4>
        <div class="form-group">
            <label>Modo de selección</label>
            <select data-setting="selection_mode" id="clients-selection-mode">
                <option value="all" <?= ($settings['selection_mode'] ?? 'all') === 'all' ? 'selected' : '' ?>>Todos los clientes</option>
                <option value="industry" <?= ($settings['selection_mode'] ?? 'all') === 'industry' ? 'selected' : '' ?>>Filtrar por industria</option>
                <option value="manual" <?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'selected' : '' ?>>Selección manual</option>
            </select>
        </div>

        <div class="form-group" id="industry-filter-group" style="<?= ($settings['selection_mode'] ?? 'all') !== 'industry' ? 'display: none;' : '' ?>">
            <label>Industria</label>
            <select data-setting="industry_filter">
                <option value="">-- Seleccionar industria --</option>
                <?php foreach ($industries as $industry): ?>
                    <option value="<?= htmlspecialchars($industry) ?>" <?= ($settings['industry_filter'] ?? '') === $industry ? 'selected' : '' ?>>
                        <?= htmlspecialchars($industry) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" id="manual-selection-group" style="<?= ($settings['selection_mode'] ?? 'all') !== 'manual' ? 'display: none;' : '' ?>">
            <label>Seleccionar clientes</label>
            <div class="clients-checklist">
                <?php foreach ($allClients as $client): ?>
                    <label class="checkbox-label client-checkbox">
                        <input type="checkbox"
                               name="selected_clients[]"
                               value="<?= $client['id'] ?>"
                               <?= in_array((int)$client['id'], $selectedClients, true) ? 'checked' : '' ?>>
                        <span class="client-checkbox-content">
                            <?php if (!empty($client['logo'])): ?>
                                <img src="<?= htmlspecialchars($client['logo']) ?>" alt="" class="client-checkbox-logo">
                            <?php endif; ?>
                            <span class="client-checkbox-name"><?= htmlspecialchars($client['name']) ?></span>
                            <?php if (!empty($client['industry'])): ?>
                                <span class="client-checkbox-industry"><?= htmlspecialchars($client['industry']) ?></span>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <input type="hidden" data-setting="selected_clients" id="selected-clients-input" value="<?= htmlspecialchars(json_encode($selectedClients)) ?>">
        </div>

        <div class="form-group" id="limit-group" style="<?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'display: none;' : '' ?>">
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
                <input type="checkbox" data-setting="show_link" <?= ($settings['show_link'] ?? true) ? 'checked' : '' ?>>
                <span>Enlazar a la página del cliente</span>
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="include_drafts" <?= !empty($settings['include_drafts']) ? 'checked' : '' ?>>
                <span>Incluir logos de clientes en borrador (sin enlace)</span>
            </label>
            <small class="form-help">Muestra logos de clientes no publicados pero sin enlace a su página</small>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <div class="info-box">
        <p><strong>Efecto:</strong> Los logos se muestran en escala de grises y se colorean al pasar el cursor.</p>
        <p><strong>Nota:</strong> Solo se muestran clientes que tengan logo configurado. Gestiona los clientes desde <a href="/admin/clients" target="_blank">Administrar Clientes</a>.</p>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<style>
.clients-checklist {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    background: var(--color-gray-50);
}

.client-checkbox {
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

.client-checkbox:hover {
    border-color: var(--color-primary);
    background: var(--color-primary-light);
}

.client-checkbox input[type="checkbox"] {
    margin-right: var(--spacing-sm);
    flex-shrink: 0;
}

.client-checkbox-content {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex: 1;
    min-width: 0;
}

.client-checkbox-logo {
    height: 24px;
    width: auto;
    max-width: 60px;
    object-fit: contain;
    flex-shrink: 0;
}

.client-checkbox-name {
    font-weight: 500;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.client-checkbox-industry {
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
    const displayModeSelect = document.getElementById('clients-display-mode');
    const gridOptions = document.getElementById('grid-options');
    const carouselOptions = document.getElementById('carousel-options');

    if (displayModeSelect) {
        displayModeSelect.addEventListener('change', function() {
            gridOptions.style.display = this.value === 'grid' ? '' : 'none';
            carouselOptions.style.display = this.value === 'carousel' ? '' : 'none';
        });
    }

    // Toggle selection mode options
    const selectionModeSelect = document.getElementById('clients-selection-mode');
    const industryFilterGroup = document.getElementById('industry-filter-group');
    const manualSelectionGroup = document.getElementById('manual-selection-group');
    const limitGroup = document.getElementById('limit-group');

    if (selectionModeSelect) {
        selectionModeSelect.addEventListener('change', function() {
            industryFilterGroup.style.display = this.value === 'industry' ? '' : 'none';
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
    const selectedClientsInput = document.getElementById('selected-clients-input');
    const checkboxes = document.querySelectorAll('input[name="selected_clients[]"]');

    function updateSelectedClients() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value));
        selectedClientsInput.value = JSON.stringify(selected);
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedClients);
    });
})();
</script>
