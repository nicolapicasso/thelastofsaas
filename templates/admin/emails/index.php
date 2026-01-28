<?php
/**
 * Admin - Email Configuration
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div>
        <h1>Configuracion de Emails</h1>
        <p class="text-muted">Gestiona las plantillas de email y la configuracion SMTP</p>
    </div>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Quick Stats -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--color-primary);">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= count($templates ?? []) ?></span>
            <span class="stat-label">Plantillas</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: <?= ($smtpSettings['smtp_enabled'] ?? 'false') === 'true' ? 'var(--color-success)' : 'var(--color-warning)' ?>;">
            <i class="fas fa-server"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= ($smtpSettings['smtp_enabled'] ?? 'false') === 'true' ? 'Activo' : 'PHP mail()' ?></span>
            <span class="stat-label">SMTP</span>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="tabs" style="margin-bottom: 2rem;">
    <a href="/admin/emails" class="tab active">
        <i class="fas fa-file-alt"></i> Plantillas
    </a>
    <a href="/admin/emails/smtp" class="tab">
        <i class="fas fa-cog"></i> Configuracion SMTP
    </a>
</div>

<!-- Templates List -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Plantillas de Email</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if (empty($templates)): ?>
            <div class="empty-state" style="padding: 3rem;">
                <i class="fas fa-envelope-open-text" style="font-size: 3rem; color: var(--color-gray-400); margin-bottom: 1rem;"></i>
                <h3>No hay plantillas configuradas</h3>
                <p class="text-muted">Ejecuta la migracion para crear las plantillas por defecto.</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Plantilla</th>
                        <th>Identificador</th>
                        <th>Asunto</th>
                        <th>Estado</th>
                        <th style="width: 180px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $template): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($template['display_name']) ?></strong>
                            </td>
                            <td>
                                <code style="background: var(--color-gray-100); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 12px;">
                                    <?= htmlspecialchars($template['name']) ?>
                                </code>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 13px;">
                                    <?= htmlspecialchars(substr($template['subject'], 0, 50)) ?><?= strlen($template['subject']) > 50 ? '...' : '' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($template['is_active']): ?>
                                    <span class="badge badge-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="/admin/emails/templates/<?= $template['id'] ?>/edit" class="btn btn-sm btn-outline">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="/admin/emails/templates/<?= $template['id'] ?>/preview" target="_blank" class="btn btn-sm btn-outline">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Variables Reference -->
<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        <h3><i class="fas fa-code"></i> Variables Disponibles</h3>
    </div>
    <div class="card-body">
        <p class="text-muted" style="margin-bottom: 1rem;">
            Usa estas variables en tus plantillas con la sintaxis <code>{{variable}}</code> o <code>{{{variable}}}</code> para URLs sin escapar.
        </p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <div>
                <h4 style="font-size: 13px; text-transform: uppercase; color: var(--color-gray-500); margin-bottom: 0.5rem;">
                    <i class="fas fa-calendar"></i> Evento
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 13px;">
                    <li><code>event_name</code> - Nombre del evento</li>
                    <li><code>event_date</code> - Fecha formateada</li>
                    <li><code>event_time</code> - Hora</li>
                    <li><code>event_location</code> - Ubicacion</li>
                    <li><code>event_address</code> - Direccion completa</li>
                </ul>
            </div>
            <div>
                <h4 style="font-size: 13px; text-transform: uppercase; color: var(--color-gray-500); margin-bottom: 0.5rem;">
                    <i class="fas fa-ticket-alt"></i> Entrada
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 13px;">
                    <li><code>attendee_name</code> - Nombre asistente</li>
                    <li><code>ticket_code</code> - Codigo de entrada</li>
                    <li><code>qr_code</code> - URL del QR</li>
                    <li><code>ticket_url</code> - URL de la entrada</li>
                </ul>
            </div>
            <div>
                <h4 style="font-size: 13px; text-transform: uppercase; color: var(--color-gray-500); margin-bottom: 0.5rem;">
                    <i class="fas fa-building"></i> Portal
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 13px;">
                    <li><code>entity_name</code> - Nombre empresa/SaaS</li>
                    <li><code>entity_type</code> - Tipo (Empresa/SaaS)</li>
                    <li><code>access_code</code> - Codigo de acceso</li>
                    <li><code>portal_url</code> - URL del portal</li>
                </ul>
            </div>
            <div>
                <h4 style="font-size: 13px; text-transform: uppercase; color: var(--color-gray-500); margin-bottom: 0.5rem;">
                    <i class="fas fa-heart"></i> Matching
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 13px;">
                    <li><code>recipient_name</code> - Destinatario</li>
                    <li><code>selector_name</code> - Quien selecciono</li>
                    <li><code>match_name</code> - Nombre del match</li>
                    <li><code>panel_url</code> - URL del panel</li>
                </ul>
            </div>
            <div>
                <h4 style="font-size: 13px; text-transform: uppercase; color: var(--color-gray-500); margin-bottom: 0.5rem;">
                    <i class="fas fa-handshake"></i> Reunion
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 13px;">
                    <li><code>other_party_name</code> - Otra parte</li>
                    <li><code>meeting_date</code> - Fecha reunion</li>
                    <li><code>meeting_time</code> - Hora reunion</li>
                    <li><code>meeting_location</code> - Sala/ubicacion</li>
                </ul>
            </div>
            <div>
                <h4 style="font-size: 13px; text-transform: uppercase; color: var(--color-gray-500); margin-bottom: 0.5rem;">
                    <i class="fas fa-comment"></i> Mensajes
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 13px;">
                    <li><code>sender_name</code> - Quien envia</li>
                    <li><code>message_preview</code> - Vista previa</li>
                </ul>
            </div>
        </div>
    </div>
</div>

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
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}
.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--color-white);
    padding: 1.25rem;
    border-radius: 8px;
    border: 1px solid var(--color-gray-200);
}
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}
.stat-info {
    display: flex;
    flex-direction: column;
}
.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.2;
}
.stat-label {
    font-size: 0.875rem;
    color: var(--color-gray-500);
}
</style>
