<div class="block-form">
    <div class="form-section">
        <h4>Configuración del bloque Casos de Éxito</h4>
        <div class="form-group">
            <label>Título de la sección</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Quiénes confían en Omniwallet') ?>">
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
                <label>Modo</label>
                <select data-setting="display_mode">
                    <option value="extended" <?= ($settings['display_mode'] ?? 'extended') === 'extended' ? 'selected' : '' ?>>Extendido (completo)</option>
                    <option value="compact" <?= ($settings['display_mode'] ?? '') === 'compact' ? 'selected' : '' ?>>Resumido (logo, testimonio, autor)</option>
                </select>
                <small class="form-help">El modo resumido muestra solo el logo, título, testimonio y autor</small>
            </div>
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
        <h4>Filtros</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Número máximo</label>
                <input type="number" data-setting="limit" value="<?= $settings['limit'] ?? 6 ?>" min="1" max="20">
            </div>
            <div class="form-group">
                <label class="checkbox-label" style="margin-top: 28px;">
                    <input type="checkbox" data-setting="featured_only" <?= ($settings['featured_only'] ?? false) ? 'checked' : '' ?>>
                    <span>Solo casos destacados</span>
                </label>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Estilo</h4>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_logo_only" <?= ($settings['show_logo_only'] ?? false) ? 'checked' : '' ?>>
                <span>Mostrar solo logos (sin tarjetas)</span>
            </label>
            <small class="form-help">Muestra únicamente los logos de las empresas en una fila</small>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>

    <div class="info-box">
        <p><strong>Nota:</strong> Los casos de éxito se cargan desde la base de datos. Gestiona los casos desde <a href="/admin/cases" target="_blank">Administrar Casos de Éxito</a>.</p>
    </div>
</div>
