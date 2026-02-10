-- Migration: Add ticket assignment fields and email templates table
-- Date: 2026-01-28
-- Description: Adds support for assigning ticket attendees to companies/sponsors
--              and configurable email templates

-- Add assignment fields to tickets table
ALTER TABLE tickets
ADD COLUMN assigned_company_id INT UNSIGNED NULL AFTER notes,
ADD COLUMN assigned_sponsor_id INT UNSIGNED NULL AFTER assigned_company_id,
ADD COLUMN assigned_at DATETIME NULL AFTER assigned_sponsor_id;

-- Add indexes for assignment lookups
ALTER TABLE tickets
ADD INDEX idx_tickets_assigned_company (assigned_company_id),
ADD INDEX idx_tickets_assigned_sponsor (assigned_sponsor_id);

-- Add foreign keys (optional, depending on your setup)
-- ALTER TABLE tickets
-- ADD CONSTRAINT fk_tickets_assigned_company FOREIGN KEY (assigned_company_id) REFERENCES companies(id) ON DELETE SET NULL,
-- ADD CONSTRAINT fk_tickets_assigned_sponsor FOREIGN KEY (assigned_sponsor_id) REFERENCES sponsors(id) ON DELETE SET NULL;

-- Create email_templates table for configurable email templates
CREATE TABLE IF NOT EXISTS email_templates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE COMMENT 'Template identifier (e.g., ticket_confirmation)',
    display_name VARCHAR(255) NOT NULL COMMENT 'Human readable name',
    subject VARCHAR(255) NOT NULL COMMENT 'Email subject with variable support',
    body_html TEXT NOT NULL COMMENT 'HTML body with variable support',
    body_text TEXT NULL COMMENT 'Plain text body (optional)',
    variables JSON NULL COMMENT 'List of available variables for this template',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email_templates_active (is_active),
    INDEX idx_email_templates_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default email templates
INSERT INTO email_templates (name, display_name, subject, body_html, variables) VALUES
('ticket_confirmation', 'Confirmacion de Entrada', 'Tu entrada para {{event_name}}',
'<h2>Gracias por tu registro, {{attendee_name}}!</h2>
<p>Tu entrada para <strong>{{event_name}}</strong> esta confirmada.</p>
<div style="text-align: center; margin: 30px 0;">
    <img src="{{{qr_code}}}" alt="QR Code" style="width: 200px; height: 200px;">
    <p style="font-size: 24px; font-family: monospace; letter-spacing: 3px; margin-top: 10px;">{{ticket_code}}</p>
</div>
<p><strong>Fecha:</strong> {{event_date}}</p>
<p><strong>Lugar:</strong> {{event_location}}</p>
<p style="margin-top: 30px;">
    <a href="{{{ticket_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Ver mi entrada</a>
</p>',
'["attendee_name", "event_name", "event_date", "event_location", "ticket_code", "qr_code", "ticket_url"]'),

('portal_welcome', 'Bienvenida al Portal', 'Bienvenido al portal de {{entity_type}}s - {{event_name}}',
'<h2>Bienvenido al Portal de {{entity_type}}s</h2>
<p>Hola {{attendee_name}},</p>
<p>Te damos la bienvenida al portal de <strong>{{entity_name}}</strong> para el evento <strong>{{event_name}}</strong>.</p>
<div style="background: #f3f4f6; padding: 20px; border-radius: 6px; margin: 20px 0;">
    <p style="margin: 0 0 10px 0;"><strong>Evento:</strong> {{event_name}}</p>
    <p style="margin: 0 0 10px 0;"><strong>Fecha:</strong> {{event_date}}</p>
    <p style="margin: 0 0 10px 0;"><strong>Lugar:</strong> {{event_location}}</p>
</div>
<div style="background: #059669; color: white; padding: 20px; border-radius: 6px; margin: 20px 0; text-align: center;">
    <p style="margin: 0 0 10px 0; font-size: 14px;">TU CODIGO DE ACCESO</p>
    <p style="margin: 0; font-size: 28px; font-family: monospace; letter-spacing: 3px; font-weight: bold;">{{access_code}}</p>
</div>
<p>Con este codigo podras acceder al portal y:</p>
<ul style="padding-left: 20px; color: #4b5563;">
    <li>Explorar empresas y SaaS participantes</li>
    <li>Seleccionar tus favoritos para hacer match</li>
    <li>Programar reuniones durante el evento</li>
    <li>Enviar y recibir mensajes</li>
</ul>
<p style="margin-top: 30px;">
    <a href="{{{portal_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Acceder al Portal</a>
</p>',
'["attendee_name", "entity_name", "entity_type", "event_name", "event_date", "event_location", "access_code", "portal_url"]'),

('company_selected', 'Seleccion Recibida (Empresa)', '{{selector_name}} te ha seleccionado en {{event_name}}',
'<h2>{{selector_name}} te ha seleccionado!</h2>
<p>Hola {{recipient_name}},</p>
<p>El sponsor <strong>{{selector_name}}</strong> te ha seleccionado para el evento <strong>{{event_name}}</strong>.</p>
<p>Accede a tu panel para ver los detalles y confirmar el match.</p>
<p style="margin-top: 30px;">
    <a href="{{{panel_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Acceder al panel</a>
</p>',
'["recipient_name", "selector_name", "event_name", "event_date", "panel_url", "personal_message"]'),

('sponsor_selected', 'Seleccion Recibida (SaaS)', '{{selector_name}} te ha seleccionado en {{event_name}}',
'<h2>{{selector_name}} te ha seleccionado!</h2>
<p>Hola {{recipient_name}},</p>
<p>La empresa <strong>{{selector_name}}</strong> esta interesada en conocerte en <strong>{{event_name}}</strong>.</p>
<p>Accede a tu panel para ver los detalles.</p>
<p style="margin-top: 30px;">
    <a href="{{{panel_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Acceder al panel</a>
</p>',
'["recipient_name", "selector_name", "event_name", "event_date", "panel_url"]'),

('match_notification', 'Notificacion de Match', 'Match confirmado en {{event_name}}!',
'<h2>Match confirmado!</h2>
<p>Hola {{recipient_name}},</p>
<p>Enhorabuena! Tienes un match mutuo con <strong>{{match_name}}</strong> para el evento <strong>{{event_name}}</strong>.</p>
<p>Proximamente recibiras informacion sobre tu reunion programada.</p>
<p style="margin-top: 30px;">
    <a href="{{{panel_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Ver mis matches</a>
</p>',
'["recipient_name", "match_name", "event_name", "event_date", "panel_url"]'),

('meeting_assigned', 'Reunion Programada', 'Reunion programada - {{event_name}}',
'<h2>Reunion programada</h2>
<p>Hola {{recipient_name}},</p>
<p>Se ha programado tu reunion con <strong>{{other_party_name}}</strong> durante <strong>{{event_name}}</strong>.</p>
<div style="background: #f3f4f6; padding: 20px; border-radius: 6px; margin: 20px 0;">
    <p><strong>Fecha:</strong> {{meeting_date}}</p>
    <p><strong>Hora:</strong> {{meeting_time}}</p>
    <p><strong>Ubicacion:</strong> {{meeting_location}}</p>
</div>
<p style="margin-top: 30px;">
    <a href="{{{panel_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Ver detalles</a>
</p>',
'["recipient_name", "other_party_name", "event_name", "meeting_date", "meeting_time", "meeting_location", "panel_url"]'),

('event_reminder', 'Recordatorio de Evento', 'Recordatorio: {{event_name}} es manana',
'<h2>Recordatorio: Tu evento es manana!</h2>
<p>Hola {{attendee_name}},</p>
<p>Te recordamos que <strong>{{event_name}}</strong> es manana.</p>
<div style="background: #f3f4f6; padding: 20px; border-radius: 6px; margin: 20px 0;">
    <p><strong>Fecha:</strong> {{event_date}}</p>
    <p><strong>Hora:</strong> {{event_time}}</p>
    <p><strong>Lugar:</strong> {{event_location}}</p>
    <p><strong>Direccion:</strong> {{event_address}}</p>
</div>
<p>Tu codigo de entrada: <strong style="font-size: 20px; letter-spacing: 2px;">{{ticket_code}}</strong></p>
<p style="margin-top: 30px;">
    <a href="{{{ticket_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Ver mi entrada</a>
</p>',
'["attendee_name", "event_name", "event_date", "event_time", "event_location", "event_address", "ticket_code", "ticket_url"]'),

('message_notification', 'Nuevo Mensaje', 'Nuevo mensaje de {{sender_name}} - {{event_name}}',
'<h2>Nuevo mensaje de {{sender_name}}</h2>
<p>Hola {{recipient_name}},</p>
<p>Has recibido un nuevo mensaje de <strong>{{sender_name}}</strong> en el evento <strong>{{event_name}}</strong>.</p>
<div style="background: #f3f4f6; padding: 20px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #4F46E5;">
    <p style="font-style: italic; margin: 0;">{{message_preview}}</p>
</div>
<p>Accede a tu panel para ver el mensaje completo y responder.</p>
<p style="margin-top: 30px;">
    <a href="{{{panel_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Ver mensaje</a>
</p>',
'["recipient_name", "sender_name", "event_name", "message_preview", "panel_url"]'),

('like_received', 'Like Recibido', '{{selector_name}} esta interesado en ti - {{event_name}}',
'<h2>{{selector_name}} esta interesado en ti</h2>
<p>Hola {{recipient_name}},</p>
<p><strong>{{selector_name}}</strong> te ha dado like en el evento <strong>{{event_name}}</strong>.</p>
<p>Si tu tambien le das like, se creara un match y podreis programar una reunion.</p>
<p style="margin-top: 30px;">
    <a href="{{{panel_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Ver en el Portal</a>
</p>',
'["recipient_name", "selector_name", "event_name", "panel_url"]');

-- Create email_settings table for SMTP configuration
CREATE TABLE IF NOT EXISTS email_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_type ENUM('string', 'boolean', 'integer', 'json') DEFAULT 'string',
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default email settings
INSERT INTO email_settings (setting_key, setting_value, setting_type, description) VALUES
('smtp_host', '', 'string', 'SMTP server hostname'),
('smtp_port', '587', 'integer', 'SMTP server port'),
('smtp_username', '', 'string', 'SMTP authentication username'),
('smtp_password', '', 'string', 'SMTP authentication password'),
('smtp_encryption', 'tls', 'string', 'SMTP encryption (tls, ssl, or empty)'),
('smtp_from_email', 'noreply@thelastofsaas.com', 'string', 'Default sender email address'),
('smtp_from_name', 'The Last of SaaS', 'string', 'Default sender name'),
('smtp_reply_to', '', 'string', 'Reply-to email address'),
('smtp_enabled', 'false', 'boolean', 'Whether to use SMTP (false = use PHP mail())'),
('email_footer_text', 'Este email fue enviado automaticamente. Por favor no respondas a este mensaje.', 'string', 'Footer text for all emails');
