<div class="block-form">
    <div class="form-section">
        <h4>HTML Personalizado</h4>
        <p class="form-help">Introduce código HTML personalizado. Ten cuidado con el código que introduces para evitar problemas de seguridad.</p>

        <div class="form-group">
            <label>Código HTML</label>
            <textarea data-content="html" rows="15" class="code-textarea" placeholder="<div class='custom-section'>
    <!-- Tu código HTML aquí -->
</div>"><?= htmlspecialchars($content['html'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="form-section">
        <h4>Configuración</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Contenedor</label>
                <select data-setting="container">
                    <option value="full" <?= ($settings['container'] ?? 'contained') === 'full' ? 'selected' : '' ?>>Ancho completo</option>
                    <option value="contained" <?= ($settings['container'] ?? 'contained') === 'contained' ? 'selected' : '' ?>>Contenido centrado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Color de fondo</label>
                <input type="color" data-setting="background_color" value="<?= htmlspecialchars($settings['background_color'] ?? '#ffffff') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Padding superior</label>
                <input type="text" data-setting="padding_top" value="<?= htmlspecialchars($settings['padding_top'] ?? '0px') ?>">
            </div>
            <div class="form-group">
                <label>Padding inferior</label>
                <input type="text" data-setting="padding_bottom" value="<?= htmlspecialchars($settings['padding_bottom'] ?? '0px') ?>">
            </div>
        </div>
    </div>

    <div class="info-box warning">
        <p><strong>Advertencia:</strong> El código HTML se insertará tal cual. Asegúrate de que el código es seguro y está bien formateado.</p>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<style>
.code-textarea {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 13px;
    background: #1e1e1e;
    color: #d4d4d4;
    border-radius: 8px;
    padding: 16px;
    line-height: 1.5;
}
</style>
