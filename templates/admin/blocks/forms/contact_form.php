<?php
/**
 * Contact Form Block Admin Form
 * Configurable contact form with field management
 * Omniwallet CMS
 */

$fields = $content['fields'] ?? [];
?>

<div class="block-form">
    <div class="form-section">
        <h4>Encabezado del formulario</h4>
        <div class="form-group">
            <label>Título</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Contacta con nosotros') ?>">
        </div>
        <div class="form-group">
            <label>Subtítulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Descripción opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Campos del formulario</h4>
        <p class="form-help">Activa/desactiva campos y marca cuáles son obligatorios.</p>

        <div class="contact-fields-list" id="contactFieldsList">
            <?php foreach ($fields as $index => $field): ?>
                <div class="contact-field-item" data-field-index="<?= $index ?>">
                    <div class="field-header">
                        <div class="field-info">
                            <label class="checkbox-label field-enabled">
                                <input type="checkbox" data-field-prop="enabled" <?= !empty($field['enabled']) ? 'checked' : '' ?>>
                                <span class="field-name"><?= htmlspecialchars($field['label'] ?? $field['name']) ?></span>
                            </label>
                            <span class="field-type-badge"><?= htmlspecialchars($field['type'] ?? 'text') ?></span>
                        </div>
                        <div class="field-options">
                            <label class="checkbox-label">
                                <input type="checkbox" data-field-prop="required" <?= !empty($field['required']) ? 'checked' : '' ?>>
                                <span>Obligatorio</span>
                            </label>
                            <select data-field-prop="width" class="field-width-select">
                                <option value="full" <?= ($field['width'] ?? 'full') === 'full' ? 'selected' : '' ?>>Ancho completo</option>
                                <option value="half" <?= ($field['width'] ?? '') === 'half' ? 'selected' : '' ?>>Medio ancho</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-details" style="<?= empty($field['enabled']) ? 'display:none;' : '' ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Etiqueta</label>
                                <input type="text" data-field-prop="label" value="<?= htmlspecialchars($field['label'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Placeholder</label>
                                <input type="text" data-field-prop="placeholder" value="<?= htmlspecialchars($field['placeholder'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <!-- Hidden inputs for field data -->
                    <input type="hidden" data-field-prop="name" value="<?= htmlspecialchars($field['name'] ?? '') ?>">
                    <input type="hidden" data-field-prop="type" value="<?= htmlspecialchars($field['type'] ?? 'text') ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Hidden input for all fields JSON -->
        <input type="hidden" data-content="fields" id="fields-data-input" value="<?= htmlspecialchars(json_encode($fields)) ?>">
    </div>

    <div class="form-section">
        <h4>Botón de envío</h4>
        <div class="form-group">
            <label>Texto del botón</label>
            <input type="text" data-content="submit_text" value="<?= htmlspecialchars($content['submit_text'] ?? 'Enviar mensaje') ?>">
        </div>
    </div>

    <div class="form-section">
        <h4>Mensaje de confirmación</h4>
        <p class="form-help">Se muestra después de enviar el formulario correctamente.</p>
        <div class="form-group">
            <label>Título de confirmación</label>
            <input type="text" data-content="success_title" value="<?= htmlspecialchars($content['success_title'] ?? '¡Mensaje enviado!') ?>">
        </div>
        <div class="form-group">
            <label>Mensaje de confirmación</label>
            <textarea data-content="success_message" rows="3"><?= htmlspecialchars($content['success_message'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="form-section">
        <h4>Configuración de envío</h4>
        <div class="form-group">
            <label>Email de destino</label>
            <input type="email" data-setting="recipient_email" value="<?= htmlspecialchars($settings['recipient_email'] ?? '') ?>" placeholder="contacto@tuempresa.com">
            <small class="form-hint">Email donde se recibirán los formularios</small>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="save_submissions" <?= ($settings['save_submissions'] ?? true) ? 'checked' : '' ?>>
                <span>Guardar envíos en la base de datos</span>
            </label>
            <small class="form-hint">Permite consultar los mensajes recibidos desde el panel de administración</small>
        </div>
    </div>

    <div class="form-section">
        <h4>Protección anti-spam (reCAPTCHA v3)</h4>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" data-setting="recaptcha_enabled" id="recaptcha-enabled" <?= !empty($settings['recaptcha_enabled']) ? 'checked' : '' ?>>
                <span>Activar reCAPTCHA v3</span>
            </label>
        </div>
        <div class="recaptcha-settings" id="recaptcha-settings" style="<?= empty($settings['recaptcha_enabled']) ? 'display:none;' : '' ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Site Key</label>
                    <input type="text" data-setting="recaptcha_site_key" value="<?= htmlspecialchars($settings['recaptcha_site_key'] ?? '') ?>" placeholder="6Lc...">
                </div>
                <div class="form-group">
                    <label>Secret Key</label>
                    <input type="password" data-setting="recaptcha_secret_key" value="<?= htmlspecialchars($settings['recaptcha_secret_key'] ?? '') ?>" placeholder="6Lc...">
                </div>
            </div>
            <div class="info-box info-box-sm">
                <p>Obtén tus claves en <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin</a>. Selecciona reCAPTCHA v3.</p>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Estilo</h4>
        <div class="form-group">
            <label>Estilo del formulario</label>
            <select data-setting="form_style">
                <option value="card" <?= ($settings['form_style'] ?? 'card') === 'card' ? 'selected' : '' ?>>Tarjeta con sombra</option>
                <option value="inline" <?= ($settings['form_style'] ?? '') === 'inline' ? 'selected' : '' ?>>Sin borde (inline)</option>
            </select>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/animation-settings.php'; ?>

    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>
</div>

<style>
.contact-fields-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.contact-field-item {
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
}

.field-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
}

.field-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.field-name {
    font-weight: 500;
}

.field-type-badge {
    background: var(--color-gray-200);
    color: var(--color-gray-600);
    padding: 2px 8px;
    border-radius: var(--radius-sm);
    font-size: var(--font-size-xs);
}

.field-options {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.field-width-select {
    padding: 4px 8px;
    font-size: var(--font-size-sm);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-sm);
}

.field-details {
    margin-top: var(--spacing-md);
    padding-top: var(--spacing-md);
    border-top: 1px solid var(--color-gray-200);
}

.recaptcha-settings {
    margin-top: var(--spacing-md);
    padding: var(--spacing-md);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
}

.info-box-sm {
    padding: var(--spacing-sm) var(--spacing-md);
    font-size: var(--font-size-sm);
}

.info-box-sm p {
    margin: 0;
}
</style>

<script>
(function() {
    const fieldsList = document.getElementById('contactFieldsList');
    const fieldsInput = document.getElementById('fields-data-input');
    const recaptchaEnabled = document.getElementById('recaptcha-enabled');
    const recaptchaSettings = document.getElementById('recaptcha-settings');

    // Update fields JSON on any change
    function updateFieldsData() {
        const fields = [];
        const fieldItems = fieldsList.querySelectorAll('.contact-field-item');

        fieldItems.forEach(item => {
            const field = {
                name: item.querySelector('[data-field-prop="name"]').value,
                type: item.querySelector('[data-field-prop="type"]').value,
                label: item.querySelector('[data-field-prop="label"]').value,
                placeholder: item.querySelector('[data-field-prop="placeholder"]').value,
                required: item.querySelector('[data-field-prop="required"]').checked,
                enabled: item.querySelector('[data-field-prop="enabled"]').checked,
                width: item.querySelector('[data-field-prop="width"]').value,
            };
            fields.push(field);
        });

        fieldsInput.value = JSON.stringify(fields);
    }

    // Toggle field details visibility
    fieldsList.addEventListener('change', function(e) {
        if (e.target.matches('[data-field-prop="enabled"]')) {
            const item = e.target.closest('.contact-field-item');
            const details = item.querySelector('.field-details');
            details.style.display = e.target.checked ? '' : 'none';
        }
        updateFieldsData();
    });

    // Update on input
    fieldsList.addEventListener('input', function(e) {
        if (e.target.matches('[data-field-prop]')) {
            updateFieldsData();
        }
    });

    // reCAPTCHA toggle
    recaptchaEnabled.addEventListener('change', function() {
        recaptchaSettings.style.display = this.checked ? '' : 'none';
    });
})();
</script>
