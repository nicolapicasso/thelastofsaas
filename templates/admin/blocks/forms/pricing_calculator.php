<div class="block-form">
    <div class="form-section">
        <h4>Configuración de la Calculadora de Precios</h4>
        <div class="form-group">
            <label>Título</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Calcula tu precio') ?>">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Componentes a mostrar</h4>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_plans" <?= ($settings['show_plans'] ?? true) ? 'checked' : '' ?>>
                <span>Mostrar tarjetas de planes</span>
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_activity_pricing" <?= ($settings['show_activity_pricing'] ?? true) ? 'checked' : '' ?>>
                <span>Mostrar tabla de precios por actividad</span>
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_calculator" <?= ($settings['show_calculator'] ?? true) ? 'checked' : '' ?>>
                <span>Mostrar calculadora interactiva</span>
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="show_features_table" <?= ($settings['show_features_table'] ?? false) ? 'checked' : '' ?>>
                <span>Mostrar tabla comparativa de funcionalidades</span>
            </label>
        </div>
    </div>

    <div class="form-section">
        <h4>Plan destacado</h4>
        <div class="form-group">
            <label>Plan a destacar</label>
            <select data-setting="highlight_plan">
                <option value="starter" <?= ($settings['highlight_plan'] ?? 'plus') === 'starter' ? 'selected' : '' ?>>Starter</option>
                <option value="plus" <?= ($settings['highlight_plan'] ?? 'plus') === 'plus' ? 'selected' : '' ?>>Plus (Recomendado)</option>
                <option value="advanced" <?= ($settings['highlight_plan'] ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced</option>
            </select>
        </div>
    </div>

    <div class="form-section">
        <h4>Estilo</h4>
        <div class="form-group">
            <label>Color de fondo</label>
            <input type="color" data-setting="background_color" value="<?= htmlspecialchars($settings['background_color'] ?? '#f8f9fa') ?>">
        </div>
    </div>

    <div class="info-box">
        <p><strong>Nota:</strong> Los precios y planes se configuran en <code>config/pricing.php</code>. La calculadora usa la fórmula: Actividades = Ventas × 1.3</p>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
