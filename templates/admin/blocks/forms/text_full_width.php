<div class="block-form">
    <div class="form-section">
        <h4>Contenido</h4>

        <div class="form-group">
            <label>Badge (opcional)</label>
            <input type="text" data-content="badge" value="<?= htmlspecialchars($content['badge'] ?? '') ?>" placeholder="Ej: Nuevo, Destacado...">
        </div>

        <div class="form-group">
            <label>Título</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? '') ?>" placeholder="Título de la sección">
        </div>

        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtítulo opcional">
        </div>

        <div class="form-group">
            <label>Texto</label>
            <textarea data-content="text" rows="8" placeholder="Contenido del bloque..."><?= htmlspecialchars($content['text'] ?? '') ?></textarea>
            <small class="form-help">Puedes usar HTML básico para dar formato al texto.</small>
        </div>
    </div>

    <div class="form-section">
        <h4>Llamada a la acción</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Texto del botón</label>
                <input type="text" data-content="cta_text" value="<?= htmlspecialchars($content['cta_text'] ?? '') ?>" placeholder="Ver más">
            </div>
            <div class="form-group">
                <label>URL del botón</label>
                <input type="text" data-content="cta_url" value="<?= htmlspecialchars($content['cta_url'] ?? '') ?>" placeholder="/pagina">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Botón secundario</label>
                <input type="text" data-content="cta_secondary_text" value="<?= htmlspecialchars($content['cta_secondary_text'] ?? '') ?>" placeholder="Contactar">
            </div>
            <div class="form-group">
                <label>URL secundaria</label>
                <input type="text" data-content="cta_secondary_url" value="<?= htmlspecialchars($content['cta_secondary_url'] ?? '') ?>" placeholder="/contacto">
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>

    <div class="form-section">
        <h4>Configuración</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Alineación del texto</label>
                <select data-setting="text_alignment">
                    <option value="center" <?= ($settings['text_alignment'] ?? 'center') === 'center' ? 'selected' : '' ?>>Centro</option>
                    <option value="left" <?= ($settings['text_alignment'] ?? '') === 'left' ? 'selected' : '' ?>>Izquierda</option>
                    <option value="right" <?= ($settings['text_alignment'] ?? '') === 'right' ? 'selected' : '' ?>>Derecha</option>
                </select>
            </div>
            <div class="form-group">
                <label>Ancho máximo del contenido</label>
                <select data-setting="max_width">
                    <option value="800px" <?= ($settings['max_width'] ?? '800px') === '800px' ? 'selected' : '' ?>>800px (Por defecto)</option>
                    <option value="600px" <?= ($settings['max_width'] ?? '') === '600px' ? 'selected' : '' ?>>600px (Estrecho)</option>
                    <option value="1000px" <?= ($settings['max_width'] ?? '') === '1000px' ? 'selected' : '' ?>>1000px (Ancho)</option>
                    <option value="100%" <?= ($settings['max_width'] ?? '') === '100%' ? 'selected' : '' ?>>100% (Sin límite)</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Espaciado</h4>
        <p class="form-help">Configura el margen (exterior) y padding (interior) del bloque.</p>

        <div class="form-row">
            <div class="form-group">
                <label>Margen superior</label>
                <select data-setting="margin_top">
                    <option value="0" <?= ($settings['margin_top'] ?? '') === '0' ? 'selected' : '' ?>>0 (Sin margen)</option>
                    <option value="20px" <?= ($settings['margin_top'] ?? '') === '20px' ? 'selected' : '' ?>>20px</option>
                    <option value="40px" <?= ($settings['margin_top'] ?? '') === '40px' ? 'selected' : '' ?>>40px</option>
                    <option value="60px" <?= ($settings['margin_top'] ?? '') === '60px' ? 'selected' : '' ?>>60px</option>
                    <option value="80px" <?= ($settings['margin_top'] ?? '') === '80px' ? 'selected' : '' ?>>80px</option>
                    <option value="custom" <?= ($settings['margin_top'] ?? '') === 'custom' ? 'selected' : '' ?>>Personalizado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Margen inferior</label>
                <select data-setting="margin_bottom">
                    <option value="0" <?= ($settings['margin_bottom'] ?? '') === '0' ? 'selected' : '' ?>>0 (Sin margen)</option>
                    <option value="20px" <?= ($settings['margin_bottom'] ?? '') === '20px' ? 'selected' : '' ?>>20px</option>
                    <option value="40px" <?= ($settings['margin_bottom'] ?? '') === '40px' ? 'selected' : '' ?>>40px</option>
                    <option value="60px" <?= ($settings['margin_bottom'] ?? '') === '60px' ? 'selected' : '' ?>>60px</option>
                    <option value="80px" <?= ($settings['margin_bottom'] ?? '') === '80px' ? 'selected' : '' ?>>80px</option>
                    <option value="custom" <?= ($settings['margin_bottom'] ?? '') === 'custom' ? 'selected' : '' ?>>Personalizado</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Margen superior personalizado</label>
                <input type="text" data-setting="margin_top_custom" value="<?= htmlspecialchars($settings['margin_top_custom'] ?? '') ?>" placeholder="Ej: 50px, 5rem">
            </div>
            <div class="form-group">
                <label>Margen inferior personalizado</label>
                <input type="text" data-setting="margin_bottom_custom" value="<?= htmlspecialchars($settings['margin_bottom_custom'] ?? '') ?>" placeholder="Ej: 50px, 5rem">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Padding superior</label>
                <select data-setting="padding_top">
                    <option value="" <?= empty($settings['padding_top']) ? 'selected' : '' ?>>Por defecto</option>
                    <option value="0" <?= ($settings['padding_top'] ?? '') === '0' ? 'selected' : '' ?>>0 (Sin padding)</option>
                    <option value="20px" <?= ($settings['padding_top'] ?? '') === '20px' ? 'selected' : '' ?>>20px</option>
                    <option value="40px" <?= ($settings['padding_top'] ?? '') === '40px' ? 'selected' : '' ?>>40px</option>
                    <option value="60px" <?= ($settings['padding_top'] ?? '') === '60px' ? 'selected' : '' ?>>60px</option>
                    <option value="80px" <?= ($settings['padding_top'] ?? '') === '80px' ? 'selected' : '' ?>>80px</option>
                    <option value="100px" <?= ($settings['padding_top'] ?? '') === '100px' ? 'selected' : '' ?>>100px</option>
                    <option value="custom" <?= ($settings['padding_top'] ?? '') === 'custom' ? 'selected' : '' ?>>Personalizado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Padding inferior</label>
                <select data-setting="padding_bottom">
                    <option value="" <?= empty($settings['padding_bottom']) ? 'selected' : '' ?>>Por defecto</option>
                    <option value="0" <?= ($settings['padding_bottom'] ?? '') === '0' ? 'selected' : '' ?>>0 (Sin padding)</option>
                    <option value="20px" <?= ($settings['padding_bottom'] ?? '') === '20px' ? 'selected' : '' ?>>20px</option>
                    <option value="40px" <?= ($settings['padding_bottom'] ?? '') === '40px' ? 'selected' : '' ?>>40px</option>
                    <option value="60px" <?= ($settings['padding_bottom'] ?? '') === '60px' ? 'selected' : '' ?>>60px</option>
                    <option value="80px" <?= ($settings['padding_bottom'] ?? '') === '80px' ? 'selected' : '' ?>>80px</option>
                    <option value="100px" <?= ($settings['padding_bottom'] ?? '') === '100px' ? 'selected' : '' ?>>100px</option>
                    <option value="custom" <?= ($settings['padding_bottom'] ?? '') === 'custom' ? 'selected' : '' ?>>Personalizado</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Padding superior personalizado</label>
                <input type="text" data-setting="padding_top_custom" value="<?= htmlspecialchars($settings['padding_top_custom'] ?? '') ?>" placeholder="Ej: 50px, 5rem">
            </div>
            <div class="form-group">
                <label>Padding inferior personalizado</label>
                <input type="text" data-setting="padding_bottom_custom" value="<?= htmlspecialchars($settings['padding_bottom_custom'] ?? '') ?>" placeholder="Ej: 50px, 5rem">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Padding horizontal</label>
                <select data-setting="padding_horizontal">
                    <option value="" <?= empty($settings['padding_horizontal']) ? 'selected' : '' ?>>Por defecto</option>
                    <option value="0" <?= ($settings['padding_horizontal'] ?? '') === '0' ? 'selected' : '' ?>>0 (Sin padding)</option>
                    <option value="20px" <?= ($settings['padding_horizontal'] ?? '') === '20px' ? 'selected' : '' ?>>20px</option>
                    <option value="40px" <?= ($settings['padding_horizontal'] ?? '') === '40px' ? 'selected' : '' ?>>40px</option>
                    <option value="60px" <?= ($settings['padding_horizontal'] ?? '') === '60px' ? 'selected' : '' ?>>60px</option>
                    <option value="5%" <?= ($settings['padding_horizontal'] ?? '') === '5%' ? 'selected' : '' ?>>5%</option>
                    <option value="10%" <?= ($settings['padding_horizontal'] ?? '') === '10%' ? 'selected' : '' ?>>10%</option>
                    <option value="custom" <?= ($settings['padding_horizontal'] ?? '') === 'custom' ? 'selected' : '' ?>>Personalizado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Padding horizontal personalizado</label>
                <input type="text" data-setting="padding_horizontal_custom" value="<?= htmlspecialchars($settings['padding_horizontal_custom'] ?? '') ?>" placeholder="Ej: 50px, 5%">
            </div>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>
