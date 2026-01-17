<div class="block-form">
    <div class="form-section">
        <h4>Contenido</h4>
        <div class="form-group">
            <label>Título</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? '') ?>" placeholder="Título del bloque">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
        </div>
        <div class="form-group">
            <label>Descripción</label>
            <textarea data-content="description" rows="4" placeholder="Descripción o contenido del bloque"><?= htmlspecialchars($content['description'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="form-section">
        <h4>Configuración</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Color de fondo</label>
                <input type="color" data-setting="background_color" value="<?= htmlspecialchars($settings['background_color'] ?? '#ffffff') ?>">
            </div>
            <div class="form-group">
                <label>Padding superior</label>
                <input type="text" data-setting="padding_top" value="<?= htmlspecialchars($settings['padding_top'] ?? '80px') ?>" placeholder="80px">
            </div>
            <div class="form-group">
                <label>Padding inferior</label>
                <input type="text" data-setting="padding_bottom" value="<?= htmlspecialchars($settings['padding_bottom'] ?? '80px') ?>" placeholder="80px">
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
