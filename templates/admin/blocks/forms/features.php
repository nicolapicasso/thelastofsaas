<div class="block-form">
    <div class="form-section">
        <h4>Configuración del bloque Funcionalidades</h4>
        <div class="form-group">
            <label>Título de la sección</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Funcionalidades') ?>">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Filtros</h4>
        <div class="form-group">
            <label>Grupo de funcionalidades</label>
            <select data-setting="feature_group">
                <option value="" <?= empty($settings['feature_group']) ? 'selected' : '' ?>>Todas</option>
                <option value="core" <?= ($settings['feature_group'] ?? '') === 'core' ? 'selected' : '' ?>>Core</option>
                <option value="plus" <?= ($settings['feature_group'] ?? '') === 'plus' ? 'selected' : '' ?>>Plus</option>
                <option value="advanced" <?= ($settings['feature_group'] ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced</option>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Número máximo</label>
                <input type="number" data-setting="limit" value="<?= $settings['limit'] ?? 8 ?>" min="1" max="20">
            </div>
            <div class="form-group">
                <label>Columnas</label>
                <select data-setting="columns">
                    <option value="2" <?= ($settings['columns'] ?? 4) == 2 ? 'selected' : '' ?>>2 columnas</option>
                    <option value="3" <?= ($settings['columns'] ?? 4) == 3 ? 'selected' : '' ?>>3 columnas</option>
                    <option value="4" <?= ($settings['columns'] ?? 4) == 4 ? 'selected' : '' ?>>4 columnas</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Estilo</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Estilo de tarjetas</label>
                <select data-setting="style">
                    <option value="cards" <?= ($settings['style'] ?? 'cards') === 'cards' ? 'selected' : '' ?>>Tarjetas</option>
                    <option value="icons" <?= ($settings['style'] ?? '') === 'icons' ? 'selected' : '' ?>>Solo iconos</option>
                    <option value="list" <?= ($settings['style'] ?? '') === 'list' ? 'selected' : '' ?>>Lista</option>
                </select>
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>

    <div class="info-box">
        <p><strong>Nota:</strong> Las funcionalidades se cargan desde la base de datos. Gestiona las funcionalidades desde <a href="/admin/features" target="_blank">Administrar Funcionalidades</a>.</p>
    </div>
</div>
