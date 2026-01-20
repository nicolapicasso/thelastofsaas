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

    <!-- Save Button -->
    <div class="card">
        <div class="card-body">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Guardar Configuración
            </button>
        </div>
    </div>
</form>

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
</style>
