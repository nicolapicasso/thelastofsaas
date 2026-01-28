<?php
/**
 * Admin - Bulk Email Composer
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div>
        <h1>Email Masivo</h1>
        <p class="text-muted">Envia emails personalizados a todos los SaaS o Empresas de un evento</p>
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
    <a href="/admin/emails/bulk" class="tab active">
        <i class="fas fa-paper-plane"></i> Email Masivo
    </a>
    <a href="/admin/emails/history" class="tab">
        <i class="fas fa-history"></i> Historial
    </a>
    <a href="/admin/emails/smtp" class="tab">
        <i class="fas fa-cog"></i> SMTP
    </a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Email Composer -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-edit"></i> Componer Email</h3>
            </div>
            <div class="card-body">
                <form id="bulk-email-form">
                    <!-- Event & Recipient Selection -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label for="event_id">Evento *</label>
                            <select id="event_id" name="event_id" class="form-control" required>
                                <option value="">Selecciona un evento</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= $event['id'] ?>">
                                        <?= htmlspecialchars($event['name']) ?>
                                        (<?= date('d/m/Y', strtotime($event['start_date'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient_type">Destinatarios *</label>
                            <select id="recipient_type" name="recipient_type" class="form-control" required>
                                <option value="sponsors">Todos los SaaS del evento</option>
                                <option value="companies">Todas las Empresas del evento</option>
                            </select>
                        </div>
                    </div>

                    <!-- Recipients Counter -->
                    <div id="recipients-info" class="alert alert-info" style="display: none; margin-bottom: 1.5rem;">
                        <i class="fas fa-users"></i>
                        <span id="recipients-count">0</span> destinatarios encontrados
                        <button type="button" class="btn btn-sm btn-outline" style="margin-left: 1rem;" onclick="showRecipientsList()">
                            <i class="fas fa-eye"></i> Ver lista
                        </button>
                    </div>

                    <!-- Subject -->
                    <div class="form-group">
                        <label for="subject">Asunto *</label>
                        <input type="text" id="subject" name="subject" class="form-control" required
                               placeholder="Ej: Informacion importante para {{entity_name}}">
                        <small class="text-muted">Puedes usar variables como {{event_name}}, {{entity_name}}, etc.</small>
                    </div>

                    <!-- Body -->
                    <div class="form-group">
                        <label for="body_html">Contenido del Email *</label>
                        <div class="editor-toolbar">
                            <button type="button" onclick="insertVariable('{{entity_name}}')" class="btn btn-sm btn-outline" title="Nombre de la empresa/SaaS">
                                <i class="fas fa-building"></i> Nombre
                            </button>
                            <button type="button" onclick="insertVariable('{{access_code}}')" class="btn btn-sm btn-outline" title="Codigo de acceso">
                                <i class="fas fa-key"></i> Codigo
                            </button>
                            <button type="button" onclick="insertVariable('{{event_name}}')" class="btn btn-sm btn-outline" title="Nombre del evento">
                                <i class="fas fa-calendar"></i> Evento
                            </button>
                            <button type="button" onclick="insertVariable('{{event_date}}')" class="btn btn-sm btn-outline" title="Fecha del evento">
                                <i class="fas fa-clock"></i> Fecha
                            </button>
                            <button type="button" onclick="insertVariable('{{{portal_url}}}')" class="btn btn-sm btn-outline" title="URL del portal">
                                <i class="fas fa-link"></i> Portal URL
                            </button>
                        </div>
                        <textarea id="body_html" name="body_html" class="form-control" rows="15" required
                                  placeholder="Escribe el contenido del email aqui..."></textarea>
                    </div>

                    <!-- BCC Email -->
                    <div class="form-group">
                        <label for="bcc_email">Copia Oculta (CCO)</label>
                        <input type="email" id="bcc_email" name="bcc_email" class="form-control"
                               placeholder="tu@email.com">
                        <small class="text-muted">Recibiras una copia de cada email enviado en esta direccion</small>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                        <button type="button" class="btn btn-outline" onclick="previewEmail()">
                            <i class="fas fa-eye"></i> Vista Previa
                        </button>
                        <button type="submit" class="btn btn-primary" id="send-button" disabled>
                            <i class="fas fa-paper-plane"></i> Enviar Email Masivo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar - Variables Reference -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-code"></i> Variables Disponibles</h3>
            </div>
            <div class="card-body">
                <p class="text-muted" style="font-size: 13px; margin-bottom: 1rem;">
                    Usa estas variables en el asunto y contenido. Se reemplazaran con los datos de cada destinatario.
                </p>

                <div class="variable-group">
                    <h4><i class="fas fa-building"></i> Destinatario</h4>
                    <ul>
                        <li><code>{{entity_name}}</code> - Nombre</li>
                        <li><code>{{entity_type}}</code> - Tipo (SaaS/Empresa)</li>
                        <li><code>{{access_code}}</code> - Codigo de acceso</li>
                        <li><code>{{contact_name}}</code> - Nombre contacto</li>
                        <li><code>{{contact_email}}</code> - Email</li>
                        <li><code>{{{portal_url}}}</code> - URL del portal</li>
                    </ul>
                </div>

                <div class="variable-group">
                    <h4><i class="fas fa-calendar"></i> Evento</h4>
                    <ul>
                        <li><code>{{event_name}}</code> - Nombre</li>
                        <li><code>{{event_date}}</code> - Fecha</li>
                        <li><code>{{event_time}}</code> - Hora</li>
                        <li><code>{{event_location}}</code> - Ubicacion</li>
                        <li><code>{{event_address}}</code> - Direccion</li>
                    </ul>
                </div>

                <div class="alert alert-warning" style="font-size: 12px; margin-top: 1rem;">
                    <strong>Nota:</strong> Usa triple llave <code>{{{url}}}</code> para URLs sin escapar HTML.
                </div>
            </div>
        </div>

        <!-- Sample Templates -->
        <div class="card" style="margin-top: 1rem;">
            <div class="card-header">
                <h3><i class="fas fa-lightbulb"></i> Plantillas Rapidas</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="template-item" onclick="loadTemplate('recordatorio')">
                    <i class="fas fa-bell"></i>
                    <span>Recordatorio de Evento</span>
                </div>
                <div class="template-item" onclick="loadTemplate('bienvenida')">
                    <i class="fas fa-hand-wave"></i>
                    <span>Bienvenida al Portal</span>
                </div>
                <div class="template-item" onclick="loadTemplate('informacion')">
                    <i class="fas fa-info-circle"></i>
                    <span>Informacion General</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recipients Modal -->
<div id="recipients-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h4><i class="fas fa-users"></i> Lista de Destinatarios</h4>
            <button type="button" onclick="closeRecipientsModal()">&times;</button>
        </div>
        <div class="modal-body" id="recipients-list" style="max-height: 400px; overflow-y: auto;">
            <!-- Recipients will be loaded here -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeRecipientsModal()">Cerrar</button>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 700px; max-height: 90vh;">
        <div class="modal-header">
            <h4><i class="fas fa-eye"></i> Vista Previa del Email</h4>
            <button type="button" onclick="closePreviewModal()">&times;</button>
        </div>
        <div class="modal-body" style="padding: 0;">
            <div style="padding: 1rem; background: var(--color-gray-100); border-bottom: 1px solid var(--color-gray-200);">
                <strong>Asunto:</strong> <span id="preview-subject"></span>
            </div>
            <iframe id="preview-iframe" style="width: 100%; height: 500px; border: none;"></iframe>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closePreviewModal()">Cerrar</button>
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
.editor-toolbar {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    background: var(--color-gray-50);
    border-radius: 6px 6px 0 0;
    border: 1px solid var(--color-gray-200);
    border-bottom: none;
    flex-wrap: wrap;
}
.editor-toolbar + textarea {
    border-radius: 0 0 6px 6px;
}
.variable-group {
    margin-bottom: 1rem;
}
.variable-group h4 {
    font-size: 12px;
    text-transform: uppercase;
    color: var(--color-gray-500);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.variable-group ul {
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 12px;
}
.variable-group li {
    padding: 0.25rem 0;
}
.variable-group code {
    background: var(--color-gray-100);
    padding: 0.125rem 0.375rem;
    border-radius: 3px;
    font-size: 11px;
}
.template-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid var(--color-gray-100);
    transition: background 0.2s;
}
.template-item:hover {
    background: var(--color-gray-50);
}
.template-item:last-child {
    border-bottom: none;
}
.template-item i {
    color: var(--color-primary);
}
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}
.modal-content {
    background: white;
    border-radius: 12px;
    width: 100%;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    overflow: hidden;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--color-gray-200);
}
.modal-header h4 {
    margin: 0;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.modal-header button {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--color-gray-500);
}
.modal-body {
    padding: 1.25rem;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--color-gray-200);
}
.recipient-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-bottom: 1px solid var(--color-gray-100);
}
.recipient-item:last-child {
    border-bottom: none;
}
.recipient-item img {
    width: 32px;
    height: 32px;
    object-fit: contain;
    border-radius: 4px;
    background: var(--color-gray-100);
}
.recipient-item .placeholder-icon {
    width: 32px;
    height: 32px;
    background: var(--color-gray-200);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-gray-400);
    font-size: 12px;
}
</style>

<script>
let currentRecipients = [];

document.getElementById('event_id').addEventListener('change', loadRecipients);
document.getElementById('recipient_type').addEventListener('change', loadRecipients);

document.getElementById('bulk-email-form').addEventListener('submit', function(e) {
    e.preventDefault();
    sendBulkEmail();
});

function loadRecipients() {
    const eventId = document.getElementById('event_id').value;
    const recipientType = document.getElementById('recipient_type').value;

    if (!eventId) {
        document.getElementById('recipients-info').style.display = 'none';
        document.getElementById('send-button').disabled = true;
        return;
    }

    fetch(`/admin/emails/bulk/recipients?event_id=${eventId}&recipient_type=${recipientType}`, {
        credentials: 'include'
    })
    .then(r => r.json())
    .then(data => {
        currentRecipients = data.recipients || [];
        document.getElementById('recipients-count').textContent = data.count;
        document.getElementById('recipients-info').style.display = data.count > 0 ? 'block' : 'none';
        document.getElementById('send-button').disabled = data.count === 0;

        if (data.count === 0) {
            document.getElementById('recipients-info').style.display = 'block';
            document.getElementById('recipients-info').className = 'alert alert-warning';
            document.getElementById('recipients-info').innerHTML = '<i class="fas fa-exclamation-triangle"></i> No hay destinatarios con email valido para este evento';
        } else {
            document.getElementById('recipients-info').className = 'alert alert-info';
        }
    });
}

function showRecipientsList() {
    const container = document.getElementById('recipients-list');
    if (currentRecipients.length === 0) {
        container.innerHTML = '<p class="text-muted">No hay destinatarios</p>';
    } else {
        container.innerHTML = currentRecipients.map(r => `
            <div class="recipient-item">
                ${r.logo_url
                    ? `<img src="${r.logo_url}" alt="">`
                    : `<div class="placeholder-icon"><i class="fas fa-${r.type === 'sponsor' ? 'rocket' : 'building'}"></i></div>`
                }
                <div>
                    <strong>${r.name}</strong>
                    <br><small class="text-muted">${r.email}</small>
                </div>
            </div>
        `).join('');
    }
    document.getElementById('recipients-modal').style.display = 'flex';
}

function closeRecipientsModal() {
    document.getElementById('recipients-modal').style.display = 'none';
}

function insertVariable(variable) {
    const textarea = document.getElementById('body_html');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    textarea.value = text.substring(0, start) + variable + text.substring(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + variable.length;
}

function previewEmail() {
    const eventId = document.getElementById('event_id').value;
    const subject = document.getElementById('subject').value;
    const bodyHtml = document.getElementById('body_html').value;

    if (!subject || !bodyHtml) {
        alert('Por favor, completa el asunto y el contenido del email');
        return;
    }

    fetch('/admin/emails/bulk/preview', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>'
        },
        body: JSON.stringify({
            event_id: eventId,
            subject: subject,
            body_html: bodyHtml
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('preview-subject').textContent = data.subject;
            const iframe = document.getElementById('preview-iframe');
            iframe.srcdoc = data.html;
            document.getElementById('preview-modal').style.display = 'flex';
        } else {
            alert(data.error || 'Error al generar la vista previa');
        }
    });
}

function closePreviewModal() {
    document.getElementById('preview-modal').style.display = 'none';
}

function sendBulkEmail() {
    const eventId = document.getElementById('event_id').value;
    const recipientType = document.getElementById('recipient_type').value;
    const subject = document.getElementById('subject').value;
    const bodyHtml = document.getElementById('body_html').value;
    const bccEmail = document.getElementById('bcc_email').value;

    if (!eventId || !subject || !bodyHtml) {
        alert('Por favor, completa todos los campos obligatorios');
        return;
    }

    const recipientLabel = recipientType === 'sponsors' ? 'SaaS' : 'Empresas';
    if (!confirm(`Se enviara este email a ${currentRecipients.length} ${recipientLabel}. ¿Continuar?`)) {
        return;
    }

    const button = document.getElementById('send-button');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    fetch('/admin/emails/bulk/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>'
        },
        body: JSON.stringify({
            event_id: eventId,
            recipient_type: recipientType,
            subject: subject,
            body_html: bodyHtml,
            bcc_email: bccEmail
        })
    })
    .then(r => r.json())
    .then(data => {
        button.disabled = false;
        button.innerHTML = originalText;

        if (data.success) {
            alert(data.message);
            // Redirect to history
            window.location.href = '/admin/emails/history';
        } else {
            alert(data.error || 'Error al enviar los emails');
        }
    })
    .catch(e => {
        button.disabled = false;
        button.innerHTML = originalText;
        alert('Error de conexion. Intenta de nuevo.');
    });
}

function loadTemplate(templateType) {
    const templates = {
        recordatorio: {
            subject: 'Recordatorio: {{event_name}} es pronto',
            body: `<h2>Hola {{entity_name}},</h2>
<p>Te recordamos que <strong>{{event_name}}</strong> se acerca.</p>
<div style="background: #f3f4f6; padding: 20px; border-radius: 6px; margin: 20px 0;">
    <p><strong>Fecha:</strong> {{event_date}}</p>
    <p><strong>Hora:</strong> {{event_time}}</p>
    <p><strong>Lugar:</strong> {{event_location}}</p>
</div>
<p>Tu codigo de acceso al portal es: <strong style="font-size: 18px;">{{access_code}}</strong></p>
<p style="margin-top: 30px;">
    <a href="{{{portal_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Acceder al Portal</a>
</p>`
        },
        bienvenida: {
            subject: 'Bienvenido a {{event_name}}',
            body: `<h2>Bienvenido al portal, {{entity_name}}</h2>
<p>Nos alegra tenerte como participante en <strong>{{event_name}}</strong>.</p>
<p>Desde tu portal podras:</p>
<ul>
    <li>Explorar las empresas y SaaS participantes</li>
    <li>Seleccionar tus favoritos para hacer match</li>
    <li>Programar reuniones durante el evento</li>
    <li>Enviar y recibir mensajes</li>
</ul>
<div style="background: #059669; color: white; padding: 20px; border-radius: 6px; text-align: center; margin: 20px 0;">
    <p style="margin: 0 0 10px 0; font-size: 14px;">TU CODIGO DE ACCESO</p>
    <p style="margin: 0; font-size: 28px; font-family: monospace; letter-spacing: 3px; font-weight: bold;">{{access_code}}</p>
</div>
<p style="margin-top: 30px;">
    <a href="{{{portal_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Acceder al Portal</a>
</p>`
        },
        informacion: {
            subject: 'Informacion importante - {{event_name}}',
            body: `<h2>Hola {{entity_name}},</h2>
<p>Queremos compartir contigo informacion importante sobre <strong>{{event_name}}</strong>.</p>
<p>[Escribe tu mensaje aqui]</p>
<div style="background: #f3f4f6; padding: 20px; border-radius: 6px; margin: 20px 0;">
    <p><strong>Evento:</strong> {{event_name}}</p>
    <p><strong>Fecha:</strong> {{event_date}}</p>
    <p><strong>Lugar:</strong> {{event_location}}</p>
</div>
<p>Si tienes alguna duda, no dudes en contactarnos.</p>
<p style="margin-top: 30px;">
    <a href="{{{portal_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Acceder al Portal</a>
</p>`
        }
    };

    const template = templates[templateType];
    if (template) {
        document.getElementById('subject').value = template.subject;
        document.getElementById('body_html').value = template.body;
    }
}

// Close modals on escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRecipientsModal();
        closePreviewModal();
    }
});
</script>
