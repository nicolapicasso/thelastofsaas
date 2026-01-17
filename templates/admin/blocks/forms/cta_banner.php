<div class="block-form">
    <div class="form-section">
        <h4>Contenido del Banner</h4>
        <div class="form-group">
            <label>Título</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? '') ?>" placeholder="¿Listo para fidelizar?">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Empieza hoy mismo">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Texto del botón</label>
                <input type="text" data-content="cta_text" value="<?= htmlspecialchars($content['cta_text'] ?? 'Reservar demo') ?>">
            </div>
            <div class="form-group">
                <label>URL del botón</label>
                <input type="text" data-content="cta_url" value="<?= htmlspecialchars($content['cta_url'] ?? '/reservar-demo') ?>">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Estilo</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Color de fondo</label>
                <input type="color" data-setting="background_color" value="<?= htmlspecialchars($settings['background_color'] ?? '#3E95B0') ?>">
            </div>
            <div class="form-group">
                <label>Color del texto</label>
                <input type="color" data-setting="text_color" value="<?= htmlspecialchars($settings['text_color'] ?? '#ffffff') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Estilo del botón</label>
            <select data-setting="button_style">
                <option value="white" <?= ($settings['button_style'] ?? 'white') === 'white' ? 'selected' : '' ?>>Blanco</option>
                <option value="dark" <?= ($settings['button_style'] ?? '') === 'dark' ? 'selected' : '' ?>>Oscuro</option>
                <option value="outline" <?= ($settings['button_style'] ?? '') === 'outline' ? 'selected' : '' ?>>Outline</option>
            </select>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
