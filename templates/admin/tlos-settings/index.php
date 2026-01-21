<?php
/**
 * TLOS Settings Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Configuración TLOS</h1>
        <p>Ajustes generales de la plataforma</p>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<form method="POST" action="/admin/tlos-settings">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <!-- General Settings -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-cog"></i> General</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Nombre del Sitio</label>
                <input type="text" name="settings[site_name]" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? 'The Last of SaaS') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Emails de Administración</label>
                <input type="text" name="settings[admin_emails]" class="form-control" value="<?= htmlspecialchars($settings['admin_emails'] ?? '') ?>" placeholder="admin@example.com, otro@example.com">
                <small class="text-muted">Separados por comas. Recibirán notificaciones importantes.</small>
            </div>
        </div>
    </div>

    <!-- Email Settings -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-envelope"></i> Notificaciones por Email</h3>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="settings[notify_sponsors]" value="1" <?= !empty($settings['notify_sponsors']) && $settings['notify_sponsors'] !== '0' ? 'checked' : '' ?>>
                        <span>Notificar a Sponsors</span>
                    </label>
                    <small class="text-muted">Enviar emails cuando reciban selecciones o matches</small>
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="settings[notify_companies]" value="1" <?= !empty($settings['notify_companies']) && $settings['notify_companies'] !== '0' ? 'checked' : '' ?>>
                        <span>Notificar a Empresas</span>
                    </label>
                    <small class="text-muted">Enviar emails cuando reciban selecciones o matches</small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="settings[allow_sponsor_messages]" value="1" <?= !empty($settings['allow_sponsor_messages']) && $settings['allow_sponsor_messages'] !== '0' ? 'checked' : '' ?>>
                    <span>Sistema de mensajería activo</span>
                </label>
                <small class="text-muted">Permite el envío de mensajes entre sponsors y empresas</small>
            </div>

            <div class="form-group">
                <label class="form-label">Email de Remitente</label>
                <input type="email" name="settings[sender_email]" class="form-control" value="<?= htmlspecialchars($settings['sender_email'] ?? '') ?>" placeholder="noreply@tudominio.com">
            </div>

            <div class="form-group">
                <label class="form-label">Nombre del Remitente</label>
                <input type="text" name="settings[sender_name]" class="form-control" value="<?= htmlspecialchars($settings['sender_name'] ?? 'The Last of SaaS') ?>">
            </div>
        </div>
    </div>

    <!-- Matching Settings -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-heart"></i> Matching</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Máximo de selecciones por Sponsor</label>
                <input type="number" name="settings[max_sponsor_selections]" class="form-control" value="<?= htmlspecialchars($settings['max_sponsor_selections'] ?? '10') ?>" min="1" max="100" style="max-width: 150px;">
                <small class="text-muted">Número máximo de empresas que un sponsor puede seleccionar</small>
            </div>

            <div class="form-group">
                <label class="form-label">Máximo de selecciones por Empresa</label>
                <input type="number" name="settings[max_company_selections]" class="form-control" value="<?= htmlspecialchars($settings['max_company_selections'] ?? '5') ?>" min="1" max="100" style="max-width: 150px;">
                <small class="text-muted">Número máximo de sponsors que una empresa puede seleccionar</small>
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="settings[auto_match_notification]" value="1" <?= !empty($settings['auto_match_notification']) && $settings['auto_match_notification'] !== '0' ? 'checked' : '' ?>>
                    <span>Notificación automática de match</span>
                </label>
                <small class="text-muted">Enviar email automáticamente cuando se produce un match mutuo</small>
            </div>
        </div>
    </div>

    <!-- Meetings Settings -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar"></i> Reuniones</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Duración por defecto (minutos)</label>
                <input type="number" name="settings[default_meeting_duration]" class="form-control" value="<?= htmlspecialchars($settings['default_meeting_duration'] ?? '15') ?>" min="5" max="120" style="max-width: 150px;">
            </div>

            <div class="form-group">
                <label class="form-label">Mesas por bloque por defecto</label>
                <input type="number" name="settings[default_rooms_per_block]" class="form-control" value="<?= htmlspecialchars($settings['default_rooms_per_block'] ?? '10') ?>" min="1" max="100" style="max-width: 150px;">
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="settings[meeting_confirmation_email]" value="1" <?= !empty($settings['meeting_confirmation_email']) && $settings['meeting_confirmation_email'] !== '0' ? 'checked' : '' ?>>
                    <span>Email de confirmación de reunión</span>
                </label>
                <small class="text-muted">Enviar email cuando se asigna una reunión</small>
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="settings[meeting_reminder_email]" value="1" <?= !empty($settings['meeting_reminder_email']) && $settings['meeting_reminder_email'] !== '0' ? 'checked' : '' ?>>
                    <span>Email de recordatorio</span>
                </label>
                <small class="text-muted">Enviar recordatorio antes del evento</small>
            </div>

            <hr style="margin: 1.5rem 0;">

            <h4 style="margin-bottom: 1rem;"><i class="fas fa-medal"></i> Límite de Reuniones Pre-agendadas por Nivel</h4>
            <p class="text-muted" style="margin-bottom: 1rem;">Número máximo de reuniones que cada nivel de sponsor puede agendar antes del evento. Durante el evento (matching en vivo) no hay límite.</p>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label"><span class="badge badge-platinum">Platino</span></label>
                    <input type="number" name="settings[pre_meeting_limit_platinum]" class="form-control" value="<?= htmlspecialchars($settings['pre_meeting_limit_platinum'] ?? '15') ?>" min="0" max="100">
                </div>
                <div class="form-group">
                    <label class="form-label"><span class="badge badge-gold">Oro</span></label>
                    <input type="number" name="settings[pre_meeting_limit_gold]" class="form-control" value="<?= htmlspecialchars($settings['pre_meeting_limit_gold'] ?? '10') ?>" min="0" max="100">
                </div>
                <div class="form-group">
                    <label class="form-label"><span class="badge badge-silver">Plata</span></label>
                    <input type="number" name="settings[pre_meeting_limit_silver]" class="form-control" value="<?= htmlspecialchars($settings['pre_meeting_limit_silver'] ?? '5') ?>" min="0" max="100">
                </div>
                <div class="form-group">
                    <label class="form-label"><span class="badge badge-bronze">Bronce</span></label>
                    <input type="number" name="settings[pre_meeting_limit_bronze]" class="form-control" value="<?= htmlspecialchars($settings['pre_meeting_limit_bronze'] ?? '0') ?>" min="0" max="100">
                </div>
            </div>
        </div>
    </div>

    <!-- Stripe Settings -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fab fa-stripe"></i> Stripe / Pagos</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Las claves de Stripe también pueden configurarse en el archivo <code>.env</code>
            </div>

            <div class="form-group">
                <label class="form-label">Stripe Public Key</label>
                <input type="text" name="settings[stripe_public_key]" class="form-control" value="<?= htmlspecialchars($settings['stripe_public_key'] ?? '') ?>" placeholder="pk_live_...">
            </div>

            <div class="form-group">
                <label class="form-label">Stripe Secret Key</label>
                <input type="password" name="settings[stripe_secret_key]" class="form-control" value="<?= htmlspecialchars($settings['stripe_secret_key'] ?? '') ?>" placeholder="sk_live_...">
                <small class="text-muted">Se almacena de forma segura</small>
            </div>

            <div class="form-group">
                <label class="form-label">Moneda</label>
                <select name="settings[currency]" class="form-control" style="max-width: 200px;">
                    <option value="eur" <?= ($settings['currency'] ?? 'eur') == 'eur' ? 'selected' : '' ?>>EUR (€)</option>
                    <option value="usd" <?= ($settings['currency'] ?? 'eur') == 'usd' ? 'selected' : '' ?>>USD ($)</option>
                    <option value="gbp" <?= ($settings['currency'] ?? 'eur') == 'gbp' ? 'selected' : '' ?>>GBP (£)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Omniwallet Integration -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-wallet"></i> Omniwallet</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Integración con <strong>Omniwallet</strong> para gestión de puntos de fidelización.
                <a href="https://omniwallet.cloud" target="_blank">Más información</a>
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="settings[omniwallet_enabled]" value="1" <?= !empty($settings['omniwallet_enabled']) && $settings['omniwallet_enabled'] !== '0' ? 'checked' : '' ?> onchange="toggleOmniwalletSettings(this.checked)">
                    <span><strong>Activar integración Omniwallet</strong></span>
                </label>
            </div>

            <div id="omniwallet-settings" style="<?= empty($settings['omniwallet_enabled']) || $settings['omniwallet_enabled'] === '0' ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
                <hr>
                <h4 style="margin-bottom: 1rem;"><i class="fas fa-key"></i> Credenciales API</h4>

                <div class="form-group">
                    <label class="form-label">Account (Subdominio)</label>
                    <input type="text" name="settings[omniwallet_account]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_account'] ?? '') ?>" placeholder="tu-cuenta" style="max-width: 300px;">
                    <small class="text-muted">El subdominio de tu cuenta Omniwallet (ej: si accedes a <code>miempresa.omniwallet.cloud</code>, escribe <code>miempresa</code>)</small>
                </div>

                <div class="form-group">
                    <label class="form-label">API Token</label>
                    <input type="password" name="settings[omniwallet_api_token]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_api_token'] ?? '') ?>" placeholder="Bearer token de la API">
                    <small class="text-muted">Puedes obtener tu token en el panel de Omniwallet → Integraciones</small>
                </div>

                <div class="form-group">
                    <button type="button" class="btn btn-outline btn-sm" onclick="testOmniwalletConnection()">
                        <i class="fas fa-plug"></i> Probar conexión
                    </button>
                    <span id="omniwallet-test-result"></span>
                </div>

                <hr>
                <h4 style="margin-bottom: 1rem;"><i class="fas fa-star"></i> Puntos por Acción</h4>
                <p class="text-muted" style="margin-bottom: 1rem;">Configura cuántos puntos se otorgan en cada acción. Usa 0 para desactivar una acción específica.</p>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Registro de Sponsor</label>
                        <input type="number" name="settings[omniwallet_points_sponsor_registration]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_points_sponsor_registration'] ?? '100') ?>" min="0">
                        <small class="text-muted">Al crear un sponsor</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Registro de Empresa</label>
                        <input type="number" name="settings[omniwallet_points_company_registration]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_points_company_registration'] ?? '50') ?>" min="0">
                        <small class="text-muted">Al crear una empresa</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Compra de Entrada</label>
                        <input type="number" name="settings[omniwallet_points_ticket_purchase]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_points_ticket_purchase'] ?? '200') ?>" min="0">
                        <small class="text-muted">Al comprar un ticket</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Check-in en Evento</label>
                        <input type="number" name="settings[omniwallet_points_checkin]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_points_checkin'] ?? '50') ?>" min="0">
                        <small class="text-muted">Al hacer check-in</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Selección de SaaS</label>
                        <input type="number" name="settings[omniwallet_points_saas_selection]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_points_saas_selection'] ?? '25') ?>" min="0">
                        <small class="text-muted">Empresa selecciona sponsor</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Match Realizado</label>
                        <input type="number" name="settings[omniwallet_points_match]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_points_match'] ?? '75') ?>" min="0">
                        <small class="text-muted">Al producirse un match</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reunión Programada</label>
                        <input type="number" name="settings[omniwallet_points_meeting_scheduled]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_points_meeting_scheduled'] ?? '100') ?>" min="0">
                        <small class="text-muted">Al agendar reunión</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Match en Vivo (Empresa)</label>
                        <input type="number" name="settings[omniwallet_points_live_match_company]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_points_live_match_company'] ?? '150') ?>" min="0">
                        <small class="text-muted">Match+reunión en vivo</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Match en Vivo (Sponsor)</label>
                        <input type="number" name="settings[omniwallet_points_live_match_sponsor]" class="form-control" value="<?= htmlspecialchars($settings['omniwallet_points_live_match_sponsor'] ?? '150') ?>" min="0">
                        <small class="text-muted">Match+reunión en vivo</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="card">
        <div class="card-body">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Guardar Configuración
            </button>
        </div>
    </div>
</form>

<script>
function toggleOmniwalletSettings(enabled) {
    const settings = document.getElementById('omniwallet-settings');
    if (enabled) {
        settings.style.opacity = '1';
        settings.style.pointerEvents = 'auto';
    } else {
        settings.style.opacity = '0.5';
        settings.style.pointerEvents = 'none';
    }
}

function testOmniwalletConnection() {
    const resultSpan = document.getElementById('omniwallet-test-result');
    resultSpan.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Probando...';

    fetch('/admin/tlos-settings/test-omniwallet', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '_csrf_token=<?= $csrf_token ?>&account=' + encodeURIComponent(document.querySelector('[name="settings[omniwallet_account]"]').value) + '&token=' + encodeURIComponent(document.querySelector('[name="settings[omniwallet_api_token]"]').value)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultSpan.innerHTML = '<span style="color: green;"><i class="fas fa-check-circle"></i> ' + data.message + '</span>';
        } else {
            resultSpan.innerHTML = '<span style="color: red;"><i class="fas fa-times-circle"></i> ' + data.message + '</span>';
        }
    })
    .catch(err => {
        resultSpan.innerHTML = '<span style="color: red;"><i class="fas fa-times-circle"></i> Error de conexión</span>';
    });
}
</script>

<style>
.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}
.form-check input[type="checkbox"] {
    width: 18px;
    height: 18px;
}
.badge-platinum {
    background: linear-gradient(135deg, #E5E4E2 0%, #A8A9AD 100%);
    color: #333;
    padding: 0.35em 0.65em;
    border-radius: 4px;
    font-weight: 600;
}
.badge-gold {
    background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
    color: #333;
    padding: 0.35em 0.65em;
    border-radius: 4px;
    font-weight: 600;
}
.badge-silver {
    background: linear-gradient(135deg, #C0C0C0 0%, #A0A0A0 100%);
    color: #333;
    padding: 0.35em 0.65em;
    border-radius: 4px;
    font-weight: 600;
}
.badge-bronze {
    background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%);
    color: #fff;
    padding: 0.35em 0.65em;
    border-radius: 4px;
    font-weight: 600;
}
</style>
