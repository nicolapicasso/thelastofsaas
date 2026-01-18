<?php
/**
 * Companies Block Admin Form
 * TLOS - The Last of SaaS
 */

use App\Models\Company;
use App\Models\Event;

$companyModel = new Company();
$eventModel = new Event();

$allCompanies = $companyModel->getActive();
$events = $eventModel->getActive();

// Get sectors
$sectors = [];
foreach ($allCompanies as $company) {
    if (!empty($company['sector']) && !in_array($company['sector'], $sectors)) {
        $sectors[] = $company['sector'];
    }
}
sort($sectors);

// Get selected companies
$selectedCompanies = $settings['selected_companies'] ?? [];
if (is_string($selectedCompanies)) {
    $selectedCompanies = json_decode($selectedCompanies, true) ?? [];
}
$selectedCompanies = array_map('intval', $selectedCompanies);
?>

<div class="block-form">
    <div class="form-section">
        <h4>Configuracion del bloque Empresas</h4>
        <div class="form-group">
            <label>Titulo de la seccion</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Empresas Participantes') ?>">
        </div>
        <div class="form-group">
            <label>Subtitulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtitulo opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Modo de visualizacion</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Tipo de visualizacion</label>
                <select data-setting="display_mode" id="companies-display-mode">
                    <option value="grid" <?= ($settings['display_mode'] ?? 'grid') === 'grid' ? 'selected' : '' ?>>Cuadricula (Grid)</option>
                    <option value="carousel" <?= ($settings['display_mode'] ?? 'grid') === 'carousel' ? 'selected' : '' ?>>Carrusel horizontal</option>
                </select>
            </div>
        </div>

        <div class="form-row" id="companies-grid-options" style="<?= ($settings['display_mode'] ?? 'grid') !== 'grid' ? 'display: none;' : '' ?>">
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

        <div class="form-row" id="companies-carousel-options" style="<?= ($settings['display_mode'] ?? 'grid') !== 'carousel' ? 'display: none;' : '' ?>">
            <div class="form-group">
                <label>Logos visibles</label>
                <select data-setting="visible_items">
                    <option value="3" <?= ($settings['visible_items'] ?? 5) == 3 ? 'selected' : '' ?>>3 logos</option>
                    <option value="4" <?= ($settings['visible_items'] ?? 5) == 4 ? 'selected' : '' ?>>4 logos</option>
                    <option value="5" <?= ($settings['visible_items'] ?? 5) == 5 ? 'selected' : '' ?>>5 logos</option>
                    <option value="6" <?= ($settings['visible_items'] ?? 5) == 6 ? 'selected' : '' ?>>6 logos</option>
                </select>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="autoplay" <?= !empty($settings['autoplay']) ? 'checked' : '' ?>>
                    <span>Reproduccion automatica</span>
                </label>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Seleccion de empresas</h4>
        <div class="form-group">
            <label>Modo de seleccion</label>
            <select data-setting="selection_mode" id="companies-selection-mode">
                <option value="all" <?= ($settings['selection_mode'] ?? 'all') === 'all' ? 'selected' : '' ?>>Todas las empresas</option>
                <option value="event" <?= ($settings['selection_mode'] ?? 'all') === 'event' ? 'selected' : '' ?>>Empresas de un evento</option>
                <option value="sector" <?= ($settings['selection_mode'] ?? 'all') === 'sector' ? 'selected' : '' ?>>Filtrar por sector</option>
                <option value="manual" <?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'selected' : '' ?>>Seleccion manual</option>
            </select>
        </div>

        <div class="form-group" id="companies-event-filter" style="<?= ($settings['selection_mode'] ?? 'all') !== 'event' ? 'display: none;' : '' ?>">
            <label>Evento</label>
            <select data-setting="event_id">
                <option value="">-- Seleccionar evento --</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= $event['id'] ?>" <?= ($settings['event_id'] ?? '') == $event['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($event['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" id="companies-sector-filter" style="<?= ($settings['selection_mode'] ?? 'all') !== 'sector' ? 'display: none;' : '' ?>">
            <label>Sector</label>
            <select data-setting="sector_filter">
                <option value="">-- Seleccionar sector --</option>
                <?php foreach ($sectors as $sector): ?>
                    <option value="<?= htmlspecialchars($sector) ?>" <?= ($settings['sector_filter'] ?? '') === $sector ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sector) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" id="companies-manual-selection" style="<?= ($settings['selection_mode'] ?? 'all') !== 'manual' ? 'display: none;' : '' ?>">
            <label>Seleccionar empresas</label>
            <div class="items-checklist">
                <?php foreach ($allCompanies as $company): ?>
                    <label class="checkbox-label item-checkbox">
                        <input type="checkbox" name="selected_companies[]" value="<?= $company['id'] ?>"
                               <?= in_array((int)$company['id'], $selectedCompanies, true) ? 'checked' : '' ?>>
                        <span class="item-checkbox-content">
                            <?php if (!empty($company['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="" class="item-checkbox-logo">
                            <?php endif; ?>
                            <span class="item-checkbox-name"><?= htmlspecialchars($company['name']) ?></span>
                            <?php if (!empty($company['sector'])): ?>
                                <span class="item-checkbox-badge"><?= htmlspecialchars($company['sector']) ?></span>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <input type="hidden" data-setting="selected_companies" id="selected-companies-input" value="<?= htmlspecialchars(json_encode($selectedCompanies)) ?>">
        </div>

        <div class="form-group" id="companies-limit" style="<?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'display: none;' : '' ?>">
            <label>Numero maximo de empresas</label>
            <input type="number" data-setting="limit" value="<?= $settings['limit'] ?? 12 ?>" min="1" max="50">
        </div>
    </div>

    <div class="form-section">
        <h4>Estilo</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Altura de logos (px)</label>
                <input type="number" data-setting="logo_height" value="<?= $settings['logo_height'] ?? 80 ?>" min="40" max="150" step="10">
            </div>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_name" <?= ($settings['show_name'] ?? true) ? 'checked' : '' ?>>
                <span>Mostrar nombre de la empresa</span>
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_sector" <?= ($settings['show_sector'] ?? true) ? 'checked' : '' ?>>
                <span>Mostrar sector</span>
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="grayscale" <?= !empty($settings['grayscale']) ? 'checked' : '' ?>>
                <span>Logos en escala de grises (color al hover)</span>
            </label>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>
    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<style>
.items-checklist {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    background: var(--color-gray-50);
}
.item-checkbox {
    display: flex;
    align-items: center;
    padding: var(--spacing-sm);
    margin-bottom: var(--spacing-xs);
    background: white;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-gray-100);
    cursor: pointer;
}
.item-checkbox:hover {
    border-color: var(--color-primary);
}
.item-checkbox-content {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex: 1;
}
.item-checkbox-logo {
    height: 24px;
    width: auto;
    max-width: 60px;
    object-fit: contain;
}
.item-checkbox-name {
    font-weight: 500;
    flex: 1;
}
.item-checkbox-badge {
    font-size: 11px;
    background: var(--color-gray-100);
    padding: 2px 8px;
    border-radius: var(--radius-full);
}
</style>

<script>
(function() {
    const displayModeSelect = document.getElementById('companies-display-mode');
    const gridOptions = document.getElementById('companies-grid-options');
    const carouselOptions = document.getElementById('companies-carousel-options');

    if (displayModeSelect) {
        displayModeSelect.addEventListener('change', function() {
            gridOptions.style.display = this.value === 'grid' ? '' : 'none';
            carouselOptions.style.display = this.value === 'carousel' ? '' : 'none';
        });
    }

    const selectionModeSelect = document.getElementById('companies-selection-mode');
    const eventFilter = document.getElementById('companies-event-filter');
    const sectorFilter = document.getElementById('companies-sector-filter');
    const manualSelection = document.getElementById('companies-manual-selection');
    const limitGroup = document.getElementById('companies-limit');

    if (selectionModeSelect) {
        selectionModeSelect.addEventListener('change', function() {
            eventFilter.style.display = this.value === 'event' ? '' : 'none';
            sectorFilter.style.display = this.value === 'sector' ? '' : 'none';
            manualSelection.style.display = this.value === 'manual' ? '' : 'none';
            limitGroup.style.display = this.value === 'manual' ? 'none' : '';
        });
    }

    const selectedCompaniesInput = document.getElementById('selected-companies-input');
    const checkboxes = document.querySelectorAll('input[name="selected_companies[]"]');

    function updateSelectedCompanies() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value));
        selectedCompaniesInput.value = JSON.stringify(selected);
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCompanies);
    });
})();
</script>
