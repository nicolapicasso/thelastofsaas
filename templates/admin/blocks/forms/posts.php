<div class="block-form">
    <div class="form-section">
        <h4>Configuración del bloque Posts</h4>
        <div class="form-group">
            <label>Título de la sección</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Últimas novedades') ?>">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Filtros</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Número de posts</label>
                <input type="number" data-setting="limit" value="<?= $settings['limit'] ?? 3 ?>" min="1" max="12">
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
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="featured_only" <?= ($settings['featured_only'] ?? false) ? 'checked' : '' ?>>
                <span>Solo posts destacados</span>
            </label>
        </div>
    </div>

    <div class="form-section">
        <h4>Estilo</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Color de fondo</label>
                <input type="color" data-setting="background_color" value="<?= htmlspecialchars($settings['background_color'] ?? '#ffffff') ?>">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_excerpt" <?= ($settings['show_excerpt'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar extracto</span>
                </label>
            </div>
        </div>
    </div>

    <div class="info-box">
        <p><strong>Nota:</strong> Los posts se cargan desde la base de datos. Gestiona el blog desde <a href="/admin/posts" target="_blank">Administrar Blog</a>.</p>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
