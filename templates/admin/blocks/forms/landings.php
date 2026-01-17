<?php
/**
 * Landings Block Admin Form
 * Displays landing pages in grid format
 * Omniwallet CMS
 */

use App\Models\Landing;
use App\Models\LandingTheme;

$landingModel = new Landing();
$themeModel = new LandingTheme();

$allLandings = $landingModel->getPublished(100);
$allThemes = $themeModel->getActive();

// Get selected landings and ensure it's an array of integers
$selectedLandings = $settings['selected_landings'] ?? [];
if (is_string($selectedLandings)) {
    $selectedLandings = json_decode($selectedLandings, true) ?? [];
}
$selectedLandings = array_map('intval', $selectedLandings);

// Get selected themes for themes mode
$selectedThemes = $settings['selected_themes'] ?? [];
if (is_string($selectedThemes)) {
    $selectedThemes = json_decode($selectedThemes, true) ?? [];
}
$selectedThemes = array_map('intval', $selectedThemes);
?>

<div class="block-form">
    <div class="form-section">
        <h4>Configuracion del bloque Landing Pages</h4>
        <div class="form-group">
            <label>Titulo de la seccion</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Descubre nuestras soluciones') ?>">
        </div>
        <div class="form-group">
            <label>Subtitulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtitulo opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Modo de visualizacion</h4>
        <div class="form-group">
            <label>Estilo de presentacion</label>
            <div class="display-mode-selector landings-display-mode">
                <label class="mode-option">
                    <input type="radio" name="landings_display_mode" value="simple"
                           <?= ($settings['display_mode'] ?? 'extended') === 'simple' ? 'checked' : '' ?>>
                    <div class="mode-card">
                        <div class="mode-preview mode-simple">
                            <div class="preview-img"></div>
                            <div class="preview-title"></div>
                        </div>
                        <span class="mode-name">Simplificado</span>
                        <small>Imagen y titulo</small>
                    </div>
                </label>
                <label class="mode-option">
                    <input type="radio" name="landings_display_mode" value="extended"
                           <?= ($settings['display_mode'] ?? 'extended') === 'extended' ? 'checked' : '' ?>>
                    <div class="mode-card">
                        <div class="mode-preview mode-extended">
                            <div class="preview-img"></div>
                            <div class="preview-title"></div>
                            <div class="preview-desc"></div>
                        </div>
                        <span class="mode-name">Extendido</span>
                        <small>Imagen, titulo y descripcion</small>
                    </div>
                </label>
            </div>
            <input type="hidden" data-setting="display_mode" id="display-mode-input" value="<?= htmlspecialchars($settings['display_mode'] ?? 'extended') ?>">
        </div>
    </div>

    <div class="form-section">
        <h4>Layout</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Columnas</label>
                <select data-setting="columns">
                    <option value="2" <?= ($settings['columns'] ?? 3) == 2 ? 'selected' : '' ?>>2 columnas</option>
                    <option value="3" <?= ($settings['columns'] ?? 3) == 3 ? 'selected' : '' ?>>3 columnas</option>
                    <option value="4" <?= ($settings['columns'] ?? 3) == 4 ? 'selected' : '' ?>>4 columnas</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Seleccion de contenido</h4>
        <div class="form-group">
            <label>Modo de seleccion</label>
            <select data-setting="selection_mode" id="landings-selection-mode">
                <option value="manual" <?= ($settings['selection_mode'] ?? 'manual') === 'manual' ? 'selected' : '' ?>>Seleccionar landings especificas</option>
                <option value="theme" <?= ($settings['selection_mode'] ?? 'manual') === 'theme' ? 'selected' : '' ?>>Por tematica (landings de una tematica)</option>
                <option value="themes" <?= ($settings['selection_mode'] ?? 'manual') === 'themes' ? 'selected' : '' ?>>Categorias de tematicas</option>
            </select>
        </div>

        <!-- Manual selection -->
        <div class="form-group" id="landings-manual-group" style="<?= ($settings['selection_mode'] ?? 'manual') !== 'manual' ? 'display: none;' : '' ?>">
            <label>Seleccionar landings</label>
            <?php if (empty($allLandings)): ?>
                <div class="info-box">
                    <p>No hay landings publicadas. <a href="/admin/landings/create" target="_blank">Crear una landing</a></p>
                </div>
            <?php else: ?>
                <div class="landings-checklist">
                    <?php foreach ($allLandings as $landing): ?>
                        <label class="checkbox-label landing-checkbox">
                            <input type="checkbox"
                                   name="selected_landings[]"
                                   value="<?= $landing['id'] ?>"
                                   <?= in_array((int)$landing['id'], $selectedLandings, true) ? 'checked' : '' ?>>
                            <span class="landing-checkbox-content">
                                <?php if (!empty($landing['image'])): ?>
                                    <img src="<?= htmlspecialchars($landing['image']) ?>" alt="" class="landing-checkbox-img">
                                <?php else: ?>
                                    <span class="landing-checkbox-img placeholder"><i class="fas fa-rocket"></i></span>
                                <?php endif; ?>
                                <span class="landing-checkbox-info">
                                    <span class="landing-checkbox-title"><?= htmlspecialchars($landing['title']) ?></span>
                                    <?php if ($landing['theme_title'] ?? null): ?>
                                        <span class="landing-checkbox-theme"><?= htmlspecialchars($landing['theme_title']) ?></span>
                                    <?php endif; ?>
                                </span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <input type="hidden" data-setting="selected_landings" id="selected-landings-input" value="<?= htmlspecialchars(json_encode($selectedLandings)) ?>">
        </div>

        <!-- Theme selection -->
        <div class="form-group" id="landings-theme-group" style="<?= ($settings['selection_mode'] ?? 'manual') !== 'theme' ? 'display: none;' : '' ?>">
            <label>Seleccionar tematica</label>
            <select data-setting="selected_theme" id="selected-theme-input">
                <option value="">-- Seleccionar tematica --</option>
                <?php foreach ($allThemes as $theme): ?>
                    <option value="<?= $theme['id'] ?>" <?= ($settings['selected_theme'] ?? '') == $theme['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($theme['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="form-help">Se mostraran las landings de esta tematica</small>
        </div>

        <!-- Themes categories selection -->
        <div class="form-group" id="landings-themes-group" style="<?= ($settings['selection_mode'] ?? 'manual') !== 'themes' ? 'display: none;' : '' ?>">
            <label>Seleccionar tematicas</label>
            <?php if (empty($allThemes)): ?>
                <div class="info-box">
                    <p>No hay tematicas creadas. <a href="/admin/landing-themes/create" target="_blank">Crear una tematica</a></p>
                </div>
            <?php else: ?>
                <div class="themes-checklist">
                    <?php foreach ($allThemes as $theme): ?>
                        <label class="checkbox-label theme-checkbox">
                            <input type="checkbox"
                                   name="selected_themes[]"
                                   value="<?= $theme['id'] ?>"
                                   <?= in_array((int)$theme['id'], $selectedThemes, true) ? 'checked' : '' ?>>
                            <span class="theme-checkbox-content">
                                <span class="theme-checkbox-title"><?= htmlspecialchars($theme['title']) ?></span>
                                <?php if ($theme['subtitle']): ?>
                                    <span class="theme-checkbox-subtitle"><?= htmlspecialchars($theme['subtitle']) ?></span>
                                <?php endif; ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <input type="hidden" data-setting="selected_themes" id="selected-themes-input" value="<?= htmlspecialchars(json_encode($selectedThemes)) ?>">
            <small class="form-help">Se mostraran las tematicas como categorias con acceso a sus landings</small>
        </div>

        <div class="form-group" id="landings-limit-group" style="<?= ($settings['selection_mode'] ?? 'manual') === 'manual' ? 'display: none;' : '' ?>">
            <label>Numero maximo a mostrar</label>
            <input type="number" data-setting="limit" value="<?= $settings['limit'] ?? 6 ?>" min="1" max="20">
        </div>
    </div>

    <div class="form-section">
        <h4>Comportamiento</h4>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="open_new_window" <?= ($settings['open_new_window'] ?? false) ? 'checked' : '' ?>>
                <span>Abrir enlaces en ventana nueva</span>
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-content="show_more" <?= ($content['show_more'] ?? false) ? 'checked' : '' ?>>
                <span>Mostrar boton "Ver mas"</span>
            </label>
        </div>
        <div class="form-row" id="show-more-options" style="<?= ($content['show_more'] ?? false) ? '' : 'display: none;' ?>">
            <div class="form-group">
                <label>Texto del boton</label>
                <input type="text" data-content="more_text" value="<?= htmlspecialchars($content['more_text'] ?? 'Ver todas') ?>">
            </div>
            <div class="form-group">
                <label>URL del boton</label>
                <input type="text" data-content="more_url" value="<?= htmlspecialchars($content['more_url'] ?? '/lp') ?>">
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/animation-settings.php'; ?>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <div class="info-box">
        <p><strong>Nota:</strong> Gestiona las landing pages desde <a href="/admin/landings" target="_blank">Administrar Landings</a> y las tematicas desde <a href="/admin/landing-themes" target="_blank">Administrar Tematicas</a>.</p>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<style>
/* Display Mode Selector for Landings */
.landings-display-mode {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-md);
}

.landings-display-mode .mode-preview {
    width: 80px;
    height: 60px;
    margin: 0 auto var(--spacing-sm);
    background-color: var(--color-gray-100);
    border-radius: var(--radius-sm);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 4px;
    gap: 3px;
}

.landings-display-mode .mode-preview .preview-img {
    flex: 1;
    background: linear-gradient(135deg, var(--color-gray-300), var(--color-gray-200));
    border-radius: 2px;
}

.landings-display-mode .mode-preview .preview-title {
    height: 8px;
    background-color: var(--color-gray-400);
    border-radius: 2px;
}

.landings-display-mode .mode-preview .preview-desc {
    height: 6px;
    background-color: var(--color-gray-300);
    border-radius: 2px;
}

/* Landings Checklist */
.landings-checklist,
.themes-checklist {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    background: var(--color-gray-50);
}

.landing-checkbox,
.theme-checkbox {
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

.landing-checkbox:hover,
.theme-checkbox:hover {
    border-color: var(--color-primary);
    background: var(--color-primary-light);
}

.landing-checkbox input[type="checkbox"],
.theme-checkbox input[type="checkbox"] {
    margin-right: var(--spacing-sm);
    flex-shrink: 0;
}

.landing-checkbox-content,
.theme-checkbox-content {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex: 1;
    min-width: 0;
}

.landing-checkbox-img {
    width: 48px;
    height: 32px;
    border-radius: var(--radius-sm);
    object-fit: cover;
    flex-shrink: 0;
}

.landing-checkbox-img.placeholder {
    background-color: var(--color-gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-gray-400);
    font-size: 14px;
}

.landing-checkbox-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
    min-width: 0;
}

.landing-checkbox-title,
.theme-checkbox-title {
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.landing-checkbox-theme,
.theme-checkbox-subtitle {
    font-size: 11px;
    color: var(--color-gray-500);
}
</style>

<script>
(function() {
    // Selection mode toggle
    const selectionMode = document.getElementById('landings-selection-mode');
    const manualGroup = document.getElementById('landings-manual-group');
    const themeGroup = document.getElementById('landings-theme-group');
    const themesGroup = document.getElementById('landings-themes-group');
    const limitGroup = document.getElementById('landings-limit-group');

    if (selectionMode) {
        selectionMode.addEventListener('change', function() {
            manualGroup.style.display = this.value === 'manual' ? '' : 'none';
            themeGroup.style.display = this.value === 'theme' ? '' : 'none';
            themesGroup.style.display = this.value === 'themes' ? '' : 'none';
            limitGroup.style.display = this.value === 'manual' ? 'none' : '';
        });
    }

    // Update hidden input for selected landings
    const landingCheckboxes = document.querySelectorAll('.landings-checklist input[type="checkbox"]');
    const landingsHiddenInput = document.getElementById('selected-landings-input');

    function updateSelectedLandings() {
        const selected = [];
        landingCheckboxes.forEach(function(cb) {
            if (cb.checked) {
                selected.push(parseInt(cb.value));
            }
        });
        if (landingsHiddenInput) {
            landingsHiddenInput.value = JSON.stringify(selected);
        }
    }

    landingCheckboxes.forEach(function(cb) {
        cb.addEventListener('change', updateSelectedLandings);
    });

    // Update hidden input for selected themes
    const themeCheckboxes = document.querySelectorAll('.themes-checklist input[type="checkbox"]');
    const themesHiddenInput = document.getElementById('selected-themes-input');

    function updateSelectedThemes() {
        const selected = [];
        themeCheckboxes.forEach(function(cb) {
            if (cb.checked) {
                selected.push(parseInt(cb.value));
            }
        });
        if (themesHiddenInput) {
            themesHiddenInput.value = JSON.stringify(selected);
        }
    }

    themeCheckboxes.forEach(function(cb) {
        cb.addEventListener('change', updateSelectedThemes);
    });

    // Show more toggle
    const showMoreCheckbox = document.querySelector('input[data-content="show_more"]');
    const showMoreOptions = document.getElementById('show-more-options');

    if (showMoreCheckbox && showMoreOptions) {
        showMoreCheckbox.addEventListener('change', function() {
            showMoreOptions.style.display = this.checked ? '' : 'none';
        });
    }

    // Handle display mode radio buttons - update hidden input
    const modeRadios = document.querySelectorAll('input[name="landings_display_mode"]');
    const displayModeInput = document.getElementById('display-mode-input');

    modeRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (displayModeInput && this.checked) {
                displayModeInput.value = this.value;
            }
        });
    });
})();
</script>
