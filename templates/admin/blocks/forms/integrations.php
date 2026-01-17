<?php
/**
 * Integrations Block Form
 * Admin template for configuring integrations block
 * Omniwallet CMS
 */

use App\Models\Integration;

$integrationModel = new Integration();
$categories = $integrationModel->getCategories();
?>
<div class="block-form">
    <div class="form-section">
        <h4>Contenido</h4>

        <div class="form-group">
            <label>Título de la sección</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Nuestras integraciones') ?>" placeholder="Nuestras integraciones">
        </div>

        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Descripción opcional">
        </div>

        <div class="form-group">
            <label>Filtrar por categoría</label>
            <select data-content="category">
                <option value="">Todas las integraciones</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= ($content['category'] ?? '') === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="form-help">Muestra solo integraciones de una categoría específica</small>
        </div>
    </div>

    <div class="form-section">
        <h4>Configuración de visualización</h4>

        <div class="form-row">
            <div class="form-group">
                <label>Estilo</label>
                <select data-setting="style">
                    <option value="logos" <?= ($settings['style'] ?? 'logos') === 'logos' ? 'selected' : '' ?>>Solo logos (minimalista)</option>
                    <option value="cards" <?= ($settings['style'] ?? '') === 'cards' ? 'selected' : '' ?>>Tarjetas con info</option>
                    <option value="detailed" <?= ($settings['style'] ?? '') === 'detailed' ? 'selected' : '' ?>>Tarjetas detalladas</option>
                </select>
            </div>
            <div class="form-group">
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

        <div class="form-row">
            <div class="form-group">
                <label>Límite de integraciones</label>
                <input type="number" data-setting="limit" value="<?= $settings['limit'] ?? 12 ?>" min="1" max="50">
            </div>
            <div class="form-group">
                <label class="checkbox-label" style="margin-top: 28px;">
                    <input type="checkbox" data-setting="clickable" <?= !empty($settings['clickable']) ? 'checked' : '' ?>>
                    <span>Elementos clicables (enlazar a página de integración)</span>
                </label>
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <div class="form-section">
        <h4>Enlace "Ver más"</h4>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-content="show_more" <?= ($content['show_more'] ?? false) ? 'checked' : '' ?>>
                <span>Mostrar enlace "Ver más"</span>
            </label>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>URL del enlace</label>
                <input type="text" data-content="more_url" value="<?= htmlspecialchars($content['more_url'] ?? '/integraciones') ?>" placeholder="/integraciones">
            </div>
            <div class="form-group">
                <label>Texto del enlace</label>
                <input type="text" data-content="more_text" value="<?= htmlspecialchars($content['more_text'] ?? 'Ver todas las integraciones') ?>" placeholder="Ver todas las integraciones">
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
