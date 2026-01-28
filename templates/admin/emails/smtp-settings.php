<?php
/**
 * Admin - SMTP Settings
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div>
        <h1>Configuracion SMTP</h1>
        <p class="text-muted">Configura el servidor de correo para mejorar la entregabilidad</p>
    </div>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Navigation Tabs -->
<div class="tabs" style="margin-bottom: 2rem;">
    <a href="/admin/emails" class="tab">
        <i class="fas fa-file-alt"></i> Plantillas
    </a>
    <a href="/admin/emails/smtp" class="tab active">
        <i class="fas fa-cog"></i> Configuracion SMTP
    </a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- SMTP Form -->
    <form method="POST" action="/admin/emails/smtp">
        <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-server"></i> Servidor SMTP</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="smtp_enabled" value="1"
                               <?= ($settings['smtp_enabled'] ?? 'false') === 'true' ? 'checked' : '' ?>
                               onchange="toggleSmtpFields(this.checked)">
                        <strong>Usar servidor SMTP</strong>
                    </label>
                    <small class="text-muted">Si esta desactivado, se usara la funcion mail() de PHP</small>
                </div>

                <div id="smtp-fields" style="<?= ($settings['smtp_enabled'] ?? 'false') !== 'true' ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <label for="smtp_host">Host SMTP</label>
                            <input type="text" id="smtp_host" name="smtp_host" class="form-control"
                                   value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>"
                                   placeholder="smtp.ejemplo.com">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="smtp_port">Puerto</label>
                            <input type="number" id="smtp_port" name="smtp_port" class="form-control"
                                   value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>"
                                   placeholder="587">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="smtp_encryption">Encriptacion</label>
                            <select id="smtp_encryption" name="smtp_encryption" class="form-control">
                                <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                <option value="" <?= ($settings['smtp_encryption'] ?? '') === '' ? 'selected' : '' ?>>Ninguna</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="flex: 1;">
                            <label for="smtp_username">Usuario</label>
                            <input type="text" id="smtp_username" name="smtp_username" class="form-control"
                                   value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>"
                                   placeholder="usuario@ejemplo.com">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="smtp_password">Contrasena</label>
                            <input type="password" id="smtp_password" name="smtp_password" class="form-control"
                                   placeholder="<?= !empty($settings['smtp_password']) ? '********' : 'Introduce la contrasena' ?>">
                            <small class="text-muted">Deja en blanco para mantener la actual</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h3><i class="fas fa-envelope"></i> Datos del Remitente</h3>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label for="smtp_from_email">Email del Remitente *</label>
                        <input type="email" id="smtp_from_email" name="smtp_from_email" class="form-control"
                               value="<?= htmlspecialchars($settings['smtp_from_email'] ?? '') ?>"
                               placeholder="noreply@thelastofsaas.com" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="smtp_from_name">Nombre del Remitente *</label>
                        <input type="text" id="smtp_from_name" name="smtp_from_name" class="form-control"
                               value="<?= htmlspecialchars($settings['smtp_from_name'] ?? '') ?>"
                               placeholder="The Last of SaaS" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="smtp_reply_to">Email de Respuesta (Reply-To)</label>
                    <input type="email" id="smtp_reply_to" name="smtp_reply_to" class="form-control"
                           value="<?= htmlspecialchars($settings['smtp_reply_to'] ?? '') ?>"
                           placeholder="contacto@thelastofsaas.com">
                    <small class="text-muted">Opcional. Si se deja vacio, las respuestas iran al email del remitente.</small>
                </div>

                <div class="form-group">
                    <label for="email_footer_text">Texto del Pie de Email</label>
                    <textarea id="email_footer_text" name="email_footer_text" class="form-control" rows="2"><?= htmlspecialchars($settings['email_footer_text'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Configuracion
                </button>
            </div>
        </div>
    </form>

    <!-- Test Email Panel -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-paper-plane"></i> Probar Configuracion</h3>
            </div>
            <div class="card-body">
                <p class="text-muted" style="margin-bottom: 1rem;">
                    Envia un email de prueba para verificar que la configuracion funciona correctamente.
                </p>
                <div class="form-group">
                    <label for="test_email">Email de Prueba</label>
                    <input type="email" id="test_email" class="form-control" placeholder="tu@email.com">
                </div>
                <button type="button" class="btn btn-outline btn-block" onclick="sendTestEmail()">
                    <i class="fas fa-paper-plane"></i> Enviar Email de Prueba
                </button>
                <div id="test-result" style="margin-top: 1rem; display: none;"></div>
            </div>
        </div>

        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Proveedores Populares</h3>
            </div>
            <div class="card-body" style="font-size: 13px;">
                <div style="margin-bottom: 1rem;">
                    <strong>Amazon SES</strong>
                    <ul style="margin: 0.25rem 0 0 1rem; color: var(--color-gray-600);">
                        <li>Host: email-smtp.[region].amazonaws.com</li>
                        <li>Puerto: 587 (TLS) o 465 (SSL)</li>
                    </ul>
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong>SendGrid</strong>
                    <ul style="margin: 0.25rem 0 0 1rem; color: var(--color-gray-600);">
                        <li>Host: smtp.sendgrid.net</li>
                        <li>Puerto: 587 (TLS)</li>
                        <li>Usuario: apikey</li>
                    </ul>
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong>Mailgun</strong>
                    <ul style="margin: 0.25rem 0 0 1rem; color: var(--color-gray-600);">
                        <li>Host: smtp.mailgun.org</li>
                        <li>Puerto: 587 (TLS)</li>
                    </ul>
                </div>
                <div>
                    <strong>Gmail</strong>
                    <ul style="margin: 0.25rem 0 0 1rem; color: var(--color-gray-600);">
                        <li>Host: smtp.gmail.com</li>
                        <li>Puerto: 587 (TLS)</li>
                        <li>Requiere App Password</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSmtpFields(enabled) {
    const fields = document.getElementById('smtp-fields');
    fields.style.opacity = enabled ? '1' : '0.5';
    fields.style.pointerEvents = enabled ? 'auto' : 'none';
}

function sendTestEmail() {
    const email = document.getElementById('test_email').value;
    const resultDiv = document.getElementById('test-result');

    if (!email || !email.includes('@')) {
        resultDiv.innerHTML = '<div class="alert alert-danger">Introduce un email valido</div>';
        resultDiv.style.display = 'block';
        return;
    }

    resultDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Enviando...</div>';
    resultDiv.style.display = 'block';

    const formData = new FormData();
    formData.append('test_email', email);
    formData.append('_csrf_token', '<?= $csrf_token ?>');

    fetch('/admin/emails/test-smtp', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check"></i> ' + data.message + '</div>';
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times"></i> ' + (data.error || 'Error al enviar') + '</div>';
        }
    })
    .catch(e => {
        resultDiv.innerHTML = '<div class="alert alert-danger">Error de conexion</div>';
    });
}
</script>

<style>
.tabs {
    display: flex;
    gap: 0.5rem;
    border-bottom: 2px solid var(--color-gray-200);
    padding-bottom: 0;
}
.tab {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    color: var(--color-gray-500);
    text-decoration: none;
    font-weight: 500;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s;
}
.tab:hover {
    color: var(--color-gray-700);
}
.tab.active {
    color: var(--color-primary);
    border-bottom-color: var(--color-primary);
}
.form-row {
    display: flex;
    gap: 1rem;
}
@media (max-width: 992px) {
    .page-header ~ div {
        grid-template-columns: 1fr !important;
    }
    .form-row {
        flex-direction: column;
    }
}
</style>
