<?php
/**
 * Team Block Admin Form
 * Displays team members with multiple display modes and layouts
 * We're Sinapsis CMS
 */

use App\Models\TeamMember;

$teamModel = new TeamMember();
$allMembers = $teamModel->getAllOrdered();

// Get selected members and ensure it's an array of integers
$selectedMembers = $settings['selected_members'] ?? [];
if (is_string($selectedMembers)) {
    $selectedMembers = json_decode($selectedMembers, true) ?? [];
}
$selectedMembers = array_map('intval', $selectedMembers);
?>

<div class="block-form">
    <div class="form-section">
        <h4>Contenido</h4>
        <div class="form-group">
            <label>Título de la sección</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Nuestro Equipo') ?>">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Estilo de visualización</h4>
        <div class="form-group">
            <div class="display-mode-selector">
                <label class="mode-option">
                    <input type="radio" name="display_mode" value="minimalista"
                           data-setting="display_mode"
                           <?= ($settings['display_mode'] ?? 'basica') === 'minimalista' ? 'checked' : '' ?>>
                    <div class="mode-card">
                        <div class="mode-preview mode-minimalista">
                            <div class="preview-photo"></div>
                        </div>
                        <span class="mode-name">Minimalista</span>
                        <small>Solo fotos, info al pasar cursor</small>
                    </div>
                </label>
                <label class="mode-option">
                    <input type="radio" name="display_mode" value="basica"
                           data-setting="display_mode"
                           <?= ($settings['display_mode'] ?? 'basica') === 'basica' ? 'checked' : '' ?>>
                    <div class="mode-card">
                        <div class="mode-preview mode-basica">
                            <div class="preview-photo"></div>
                            <div class="preview-info"></div>
                        </div>
                        <span class="mode-name">Básica</span>
                        <small>Fotos con nombre y puesto</small>
                    </div>
                </label>
                <label class="mode-option">
                    <input type="radio" name="display_mode" value="sinapsis"
                           data-setting="display_mode"
                           <?= ($settings['display_mode'] ?? 'basica') === 'sinapsis' ? 'checked' : '' ?>>
                    <div class="mode-card">
                        <div class="mode-preview mode-sinapsis">
                            <div class="preview-photo"></div>
                            <div class="preview-info-dark"></div>
                        </div>
                        <span class="mode-name">Sinapsis</span>
                        <small>Fondo oscuro, hover con GIF</small>
                    </div>
                </label>
                <label class="mode-option">
                    <input type="radio" name="display_mode" value="detallada"
                           data-setting="display_mode"
                           <?= ($settings['display_mode'] ?? 'basica') === 'detallada' ? 'checked' : '' ?>>
                    <div class="mode-card">
                        <div class="mode-preview mode-detallada">
                            <div class="preview-photo"></div>
                            <div class="preview-info"></div>
                            <div class="preview-desc"></div>
                        </div>
                        <span class="mode-name">Detallada</span>
                        <small>Incluye descripción</small>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Disposición</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Modo de layout</label>
                <select data-setting="layout_mode" id="team-layout-mode">
                    <option value="grid" <?= ($settings['layout_mode'] ?? 'grid') === 'grid' ? 'selected' : '' ?>>Grid (cuadrícula)</option>
                    <option value="carousel" <?= ($settings['layout_mode'] ?? 'grid') === 'carousel' ? 'selected' : '' ?>>Carrusel (deslizable)</option>
                </select>
            </div>
            <div class="form-group" id="team-columns-group">
                <label>Columnas</label>
                <select data-setting="columns">
                    <option value="2" <?= ($settings['columns'] ?? 4) == 2 ? 'selected' : '' ?>>2 columnas</option>
                    <option value="3" <?= ($settings['columns'] ?? 4) == 3 ? 'selected' : '' ?>>3 columnas</option>
                    <option value="4" <?= ($settings['columns'] ?? 4) == 4 ? 'selected' : '' ?>>4 columnas</option>
                    <option value="5" <?= ($settings['columns'] ?? 4) == 5 ? 'selected' : '' ?>>5 columnas</option>
                    <option value="6" <?= ($settings['columns'] ?? 4) == 6 ? 'selected' : '' ?>>6 columnas</option>
                </select>
            </div>
        </div>

        <!-- Carousel Settings -->
        <div id="team-carousel-settings" style="<?= ($settings['layout_mode'] ?? 'grid') !== 'carousel' ? 'display: none;' : '' ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Miembros visibles</label>
                    <select data-setting="visible_items">
                        <option value="2" <?= ($settings['visible_items'] ?? 4) == 2 ? 'selected' : '' ?>>2</option>
                        <option value="3" <?= ($settings['visible_items'] ?? 4) == 3 ? 'selected' : '' ?>>3</option>
                        <option value="4" <?= ($settings['visible_items'] ?? 4) == 4 ? 'selected' : '' ?>>4</option>
                        <option value="5" <?= ($settings['visible_items'] ?? 4) == 5 ? 'selected' : '' ?>>5</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Velocidad de reproducción</label>
                    <select data-setting="autoplay_speed">
                        <option value="slow" <?= ($settings['autoplay_speed'] ?? 'normal') === 'slow' ? 'selected' : '' ?>>Lenta (5s)</option>
                        <option value="normal" <?= ($settings['autoplay_speed'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Normal (3s)</option>
                        <option value="fast" <?= ($settings['autoplay_speed'] ?? 'normal') === 'fast' ? 'selected' : '' ?>>Rápida (2s)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="autoplay" <?= ($settings['autoplay'] ?? true) ? 'checked' : '' ?>>
                    Reproducción automática
                </label>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Selección de miembros</h4>
        <div class="form-group">
            <label>Modo de selección</label>
            <select data-setting="selection_mode" id="team-selection-mode">
                <option value="all" <?= ($settings['selection_mode'] ?? 'all') === 'all' ? 'selected' : '' ?>>Todos los miembros</option>
                <option value="manual" <?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'selected' : '' ?>>Selección manual</option>
            </select>
        </div>

        <div class="form-group" id="team-manual-selection-group" style="<?= ($settings['selection_mode'] ?? 'all') !== 'manual' ? 'display: none;' : '' ?>">
            <label>Seleccionar miembros</label>
            <div class="members-checklist">
                <?php foreach ($allMembers as $member): ?>
                    <label class="checkbox-label member-checkbox">
                        <input type="checkbox"
                               name="selected_members[]"
                               value="<?= $member['id'] ?>"
                               <?= in_array((int)$member['id'], $selectedMembers, true) ? 'checked' : '' ?>>
                        <span class="member-checkbox-content">
                            <?php if ($member['photo']): ?>
                                <img src="<?= htmlspecialchars($member['photo']) ?>" alt="" class="member-checkbox-photo">
                            <?php else: ?>
                                <span class="member-checkbox-photo placeholder"><i class="fas fa-user"></i></span>
                            <?php endif; ?>
                            <span class="member-checkbox-name"><?= htmlspecialchars($member['name']) ?></span>
                            <?php if (!empty($member['role'])): ?>
                                <span class="member-checkbox-role"><?= htmlspecialchars($member['role']) ?></span>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <input type="hidden" data-setting="selected_members" id="selected-members-input" value="<?= htmlspecialchars(json_encode($selectedMembers)) ?>">
        </div>

        <div class="form-group" id="team-limit-group" style="<?= ($settings['selection_mode'] ?? 'all') === 'manual' ? 'display: none;' : '' ?>">
            <label>Número máximo de miembros</label>
            <input type="number" data-setting="limit" value="<?= $settings['limit'] ?? 12 ?>" min="1" max="50">
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <div class="info-box">
        <p><strong>Nota:</strong> Los miembros del equipo se cargan desde la base de datos. Gestiona el equipo desde <a href="/admin/team" target="_blank">Administrar Equipo</a>.</p>
        <p><strong>Foto Hover:</strong> Para el efecto de hover con GIF animado, sube la imagen animada en el campo "Foto Hover" de cada miembro del equipo.</p>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<style>
/* Display Mode Selector */
.display-mode-selector {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-md);
}

.mode-option {
    cursor: pointer;
}

.mode-option input {
    display: none;
}

.mode-card {
    padding: var(--spacing-md);
    border: 2px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    text-align: center;
    transition: all 0.2s ease;
}

.mode-option input:checked + .mode-card {
    border-color: var(--color-primary);
    background-color: var(--color-primary-light);
}

.mode-preview {
    width: 60px;
    height: 70px;
    margin: 0 auto var(--spacing-sm);
    background-color: var(--color-gray-100);
    border-radius: var(--radius-sm);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.mode-preview .preview-photo {
    flex: 1;
    background: linear-gradient(135deg, var(--color-gray-300), var(--color-gray-200));
}

.mode-preview .preview-info {
    height: 12px;
    background-color: var(--color-gray-300);
}

.mode-preview .preview-info-dark {
    height: 12px;
    background-color: #1A1A1A;
}

.mode-preview .preview-desc {
    height: 8px;
    background-color: var(--color-gray-200);
}

.mode-preview.mode-sinapsis {
    background-color: #1A1A1A;
}

.mode-preview.mode-sinapsis .preview-photo {
    background: linear-gradient(135deg, #444, #333);
    border-radius: 4px;
    margin: 4px;
    flex: 1;
}

.mode-name {
    display: block;
    font-weight: 600;
    font-size: var(--font-size-sm);
    margin-bottom: 2px;
}

.mode-card small {
    font-size: 10px;
    color: var(--color-gray-500);
}

/* Members Checklist */
.members-checklist {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    background: var(--color-gray-50);
}

.member-checkbox {
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

.member-checkbox:hover {
    border-color: var(--color-primary);
    background: var(--color-primary-light);
}

.member-checkbox input[type="checkbox"] {
    margin-right: var(--spacing-sm);
    flex-shrink: 0;
}

.member-checkbox-content {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    flex: 1;
    min-width: 0;
}

.member-checkbox-photo {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.member-checkbox-photo.placeholder {
    background-color: var(--color-gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-gray-400);
    font-size: 14px;
}

.member-checkbox-name {
    font-weight: 500;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.member-checkbox-role {
    font-size: 11px;
    color: var(--color-gray-500);
    background: var(--color-gray-100);
    padding: 2px 8px;
    border-radius: var(--radius-full);
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .display-mode-selector {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
(function() {
    // Layout mode toggle
    const layoutMode = document.getElementById('team-layout-mode');
    const columnsGroup = document.getElementById('team-columns-group');
    const carouselSettings = document.getElementById('team-carousel-settings');

    if (layoutMode) {
        layoutMode.addEventListener('change', function() {
            if (this.value === 'carousel') {
                carouselSettings.style.display = '';
            } else {
                carouselSettings.style.display = 'none';
            }
        });
    }

    // Selection mode toggle
    const selectionMode = document.getElementById('team-selection-mode');
    const manualGroup = document.getElementById('team-manual-selection-group');
    const limitGroup = document.getElementById('team-limit-group');

    if (selectionMode) {
        selectionMode.addEventListener('change', function() {
            if (this.value === 'manual') {
                manualGroup.style.display = '';
                limitGroup.style.display = 'none';
            } else {
                manualGroup.style.display = 'none';
                limitGroup.style.display = '';
            }
        });
    }

    // Update hidden input when checkboxes change
    const checkboxes = document.querySelectorAll('.members-checklist input[type="checkbox"]');
    const hiddenInput = document.getElementById('selected-members-input');

    function updateSelectedMembers() {
        const selected = [];
        checkboxes.forEach(function(cb) {
            if (cb.checked) {
                selected.push(parseInt(cb.value));
            }
        });
        hiddenInput.value = JSON.stringify(selected);
    }

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateSelectedMembers);
    });
})();
</script>
