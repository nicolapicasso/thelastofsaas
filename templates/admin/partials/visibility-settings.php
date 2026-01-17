<?php
/**
 * Block Layout & Visibility Settings Partial
 * Controls block width, layout, and device visibility
 * We're Sinapsis CMS
 */
?>
<!-- Layout Settings -->
<?php include TEMPLATES_PATH . '/admin/partials/block-layout-settings.php'; ?>

<!-- Visibility Settings -->
<div class="form-section">
    <h4>Visibilidad</h4>
    <p class="form-section-description">Controla en qué dispositivos se muestra este bloque</p>

    <div class="form-row">
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_desktop" <?= ($settings['show_desktop'] ?? true) ? 'checked' : '' ?>>
                <span><i class="fas fa-desktop"></i> Mostrar en escritorio</span>
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_mobile" <?= ($settings['show_mobile'] ?? true) ? 'checked' : '' ?>>
                <span><i class="fas fa-mobile-alt"></i> Mostrar en móvil</span>
            </label>
        </div>
    </div>
</div>
