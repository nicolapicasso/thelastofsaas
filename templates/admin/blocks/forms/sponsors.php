<?php
/**
 * Sponsors Block Admin Form
 * TLOS - The Last of SaaS
 */

use App\Models\Sponsor;
use App\Models\Event;

$sponsorModel = new Sponsor();
$eventModel = new Event();

$allSponsors = $sponsorModel->getActive();
$events = $eventModel->getActive();

// Get sponsor levels
$levels = [];
foreach ($allSponsors as $sponsor) {
    if (!empty($sponsor['level']) && !in_array($sponsor['level'], $levels)) {
        $levels[] = $sponsor['level'];
    }
}
sort($levels);

// Get selected sponsors
$selectedSponsors = $settings['selected_sponsors'] ?? [];
if (is_string($selectedSponsors)) {
    $selectedSponsors = json_decode($selectedSponsors, true) ?? [];
}
$selectedSponsors = array_map('intval', $selectedSponsors);
?>

<div class="block-form">
    <div class="form-section">
        <h4>Configuracion del bloque Sponsors</h4>
        <div class="form-group">
            <label>Titulo de la seccion</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Nuestros Sponsors') ?>">
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
                <select data-setting="display_mode" id="sponsors-display-mode">
                    <option value="grid" <?= ($settings['display_mode'] ?? 'grid') === 'grid' ? 'selected' : '' ?>>Cuadricula (Grid)</option>
                    <option value="carousel" <?= ($settings['display_mode'] ?? 'grid') === 'carousel' ? 'selected' : '' ?>>Carrusel horizontal</option>
                </select>
            </div>
        </div>

        <div class="form-row" id="sponsors-grid-options" style="<?= ($settings['display_mode'] ?? 'grid') !== 'grid' ? 'display: none;' : '' ?>">
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

        <div class="form-row" id="sponsors-carousel-options" style="<?= ($settings['display_mode'] ?? 'grid') !== 'carousel' ? 'display: none;' : '' ?>">
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
        <h4>Seleccion de sponsors</h4>
        <div class="form-group">
            <label>Modo de seleccion</label>
            <select data-setting="selection_mode" id="sponsors-selection-mode">
                <option value="all" <?= ($settings['selection_mode'] ?? 'all') === 'all' ? 'selected' : '' ?>>Todos los sponsors</option>
                <option value="event" <?= ($settings['selection_mode'] ?? 'all') === 'event' ? 'selected' : '' ?>>Sponsors de un evento</option>
                <option value="level" <?= ($settings['selection_mode'] ?? 'all') === 'level' ? 'selected' : '' ?>>Filtrar por nivel</option>
                <option value="manual" <?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'selected' : '' ?>>Seleccion manual</option>
            </select>
        </div>

        <div class="form-group" id="sponsors-event-filter" style="<?= ($settings['selection_mode'] ?? 'all') !== 'event' ? 'display: none;' : '' ?>">
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

        <div class="form-group" id="sponsors-level-filter" style="<?= ($settings['selection_mode'] ?? 'all') !== 'level' ? 'display: none;' : '' ?>">
            <label>Nivel de sponsor</label>
            <select data-setting="level_filter">
                <option value="">-- Seleccionar nivel --</option>
                <?php foreach ($levels as $level): ?>
                    <option value="<?= htmlspecialchars($level) ?>" <?= ($settings['level_filter'] ?? '') === $level ? 'selected' : '' ?>>
                        <?= htmlspecialchars(ucfirst($level)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" id="sponsors-manual-selection" style="<?= ($settings['selection_mode'] ?? 'all') !== 'manual' ? 'display: none;' : '' ?>">
            <label>Seleccionar sponsors</label>
            <div class="items-checklist">
                <?php foreach ($allSponsors as $sponsor): ?>
                    <label class="checkbox-label item-checkbox">
                        <input type="checkbox" name="selected_sponsors[]" value="<?= $sponsor['id'] ?>"
                               <?= in_array((int)$sponsor['id'], $selectedSponsors, true) ? 'checked' : '' ?>>
                        <span class="item-checkbox-content">
                            <?php if (!empty($sponsor['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="" class="item-checkbox-logo">
                            <?php endif; ?>
                            <span class="item-checkbox-name"><?= htmlspecialchars($sponsor['name']) ?></span>
                            <?php if (!empty($sponsor['level'])): ?>
                                <span class="item-checkbox-badge"><?= htmlspecialchars($sponsor['level']) ?></span>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <input type="hidden" data-setting="selected_sponsors" id="selected-sponsors-input" value="<?= htmlspecialchars(json_encode($selectedSponsors)) ?>">
        </div>

        <div class="form-group" id="sponsors-limit" style="<?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'display: none;' : '' ?>">
            <label>Numero maximo de sponsors</label>
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
                <span>Mostrar nombre del sponsor</span>
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_level" <?= ($settings['show_level'] ?? true) ? 'checked' : '' ?>>
                <span>Mostrar nivel de sponsor</span>
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
    const displayModeSelect = document.getElementById('sponsors-display-mode');
    const gridOptions = document.getElementById('sponsors-grid-options');
    const carouselOptions = document.getElementById('sponsors-carousel-options');

    if (displayModeSelect) {
        displayModeSelect.addEventListener('change', function() {
            gridOptions.style.display = this.value === 'grid' ? '' : 'none';
            carouselOptions.style.display = this.value === 'carousel' ? '' : 'none';
        });
    }

    const selectionModeSelect = document.getElementById('sponsors-selection-mode');
    const eventFilter = document.getElementById('sponsors-event-filter');
    const levelFilter = document.getElementById('sponsors-level-filter');
    const manualSelection = document.getElementById('sponsors-manual-selection');
    const limitGroup = document.getElementById('sponsors-limit');

    if (selectionModeSelect) {
        selectionModeSelect.addEventListener('change', function() {
            eventFilter.style.display = this.value === 'event' ? '' : 'none';
            levelFilter.style.display = this.value === 'level' ? '' : 'none';
            manualSelection.style.display = this.value === 'manual' ? '' : 'none';
            limitGroup.style.display = this.value === 'manual' ? 'none' : '';
        });
    }

    const selectedSponsorsInput = document.getElementById('selected-sponsors-input');
    const checkboxes = document.querySelectorAll('input[name="selected_sponsors[]"]');

    function updateSelectedSponsors() {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value));
        selectedSponsorsInput.value = JSON.stringify(selected);
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedSponsors);
    });
})();
</script>
