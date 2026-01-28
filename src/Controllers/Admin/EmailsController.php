<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\EmailService;
use App\Models\Event;
use App\Models\Sponsor;
use App\Models\Company;

/**
 * Emails Controller
 * TLOS - The Last of SaaS
 *
 * Manages email templates and SMTP configuration
 */
class EmailsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Main emails dashboard - shows templates list
     */
    public function index(): void
    {
        $this->requireAuth();

        // Get all email templates
        $templates = $this->db->query(
            "SELECT * FROM email_templates ORDER BY name ASC"
        )->fetchAll();

        // Get SMTP settings
        $smtpSettings = $this->getSmtpSettings();

        $this->renderAdmin('emails/index', [
            'title' => 'Configuración de Emails',
            'templates' => $templates,
            'smtpSettings' => $smtpSettings,
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Edit email template
     */
    public function editTemplate(string $id): void
    {
        $this->requireAuth();

        $template = $this->db->query(
            "SELECT * FROM email_templates WHERE id = ?",
            [(int)$id]
        )->fetch();

        if (!$template) {
            $this->flash('error', 'Plantilla no encontrada.');
            $this->redirect('/admin/emails');
            return;
        }

        // Parse variables JSON
        $variables = json_decode($template['variables'] ?? '[]', true) ?: [];

        $this->renderAdmin('emails/edit-template', [
            'title' => 'Editar Plantilla: ' . $template['display_name'],
            'template' => $template,
            'variables' => $variables,
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Update email template
     */
    public function updateTemplate(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/emails/templates/' . $id . '/edit');
            return;
        }

        $template = $this->db->query(
            "SELECT * FROM email_templates WHERE id = ?",
            [(int)$id]
        )->fetch();

        if (!$template) {
            $this->flash('error', 'Plantilla no encontrada.');
            $this->redirect('/admin/emails');
            return;
        }

        $displayName = trim($this->getPost('display_name', ''));
        $subject = trim($this->getPost('subject', ''));
        $bodyHtml = $this->getPost('body_html', '');
        $isActive = $this->getPost('is_active') ? 1 : 0;

        if (empty($displayName) || empty($subject) || empty($bodyHtml)) {
            $this->flash('error', 'Nombre, asunto y contenido son obligatorios.');
            $this->redirect('/admin/emails/templates/' . $id . '/edit');
            return;
        }

        try {
            $this->db->query(
                "UPDATE email_templates SET display_name = ?, subject = ?, body_html = ?, is_active = ?, updated_at = NOW() WHERE id = ?",
                [$displayName, $subject, $bodyHtml, $isActive, (int)$id]
            );

            $this->flash('success', 'Plantilla actualizada correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar: ' . $e->getMessage());
        }

        $this->redirect('/admin/emails/templates/' . $id . '/edit');
    }

    /**
     * Reset template to default
     */
    public function resetTemplate(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $template = $this->db->query(
            "SELECT * FROM email_templates WHERE id = ?",
            [(int)$id]
        )->fetch();

        if (!$template) {
            $this->jsonError('Plantilla no encontrada.');
            return;
        }

        // Get default template from EmailService
        $emailService = new EmailService();
        $defaultContent = $emailService->getDefaultTemplateContent($template['name']);

        if (!$defaultContent) {
            $this->jsonError('No se encontró la plantilla por defecto.');
            return;
        }

        try {
            $this->db->query(
                "UPDATE email_templates SET body_html = ?, updated_at = NOW() WHERE id = ?",
                [$defaultContent, (int)$id]
            );

            $this->jsonSuccess(['message' => 'Plantilla restaurada a valores por defecto.']);
        } catch (\Exception $e) {
            $this->jsonError('Error al restaurar: ' . $e->getMessage());
        }
    }

    /**
     * SMTP Settings page
     */
    public function smtpSettings(): void
    {
        $this->requireAuth();

        $settings = $this->getSmtpSettings();

        $this->renderAdmin('emails/smtp-settings', [
            'title' => 'Configuración SMTP',
            'settings' => $settings,
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Update SMTP Settings
     */
    public function updateSmtpSettings(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/emails/smtp');
            return;
        }

        $settings = [
            'smtp_enabled' => $this->getPost('smtp_enabled') ? 'true' : 'false',
            'smtp_host' => trim($this->getPost('smtp_host', '')),
            'smtp_port' => trim($this->getPost('smtp_port', '587')),
            'smtp_username' => trim($this->getPost('smtp_username', '')),
            'smtp_encryption' => trim($this->getPost('smtp_encryption', 'tls')),
            'smtp_from_email' => trim($this->getPost('smtp_from_email', '')),
            'smtp_from_name' => trim($this->getPost('smtp_from_name', '')),
            'smtp_reply_to' => trim($this->getPost('smtp_reply_to', '')),
            'email_footer_text' => trim($this->getPost('email_footer_text', '')),
        ];

        // Only update password if provided
        $password = $this->getPost('smtp_password', '');
        if (!empty($password)) {
            $settings['smtp_password'] = $password;
        }

        try {
            foreach ($settings as $key => $value) {
                $this->db->query(
                    "INSERT INTO email_settings (setting_key, setting_value, updated_at)
                     VALUES (?, ?, NOW())
                     ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()",
                    [$key, $value, $value]
                );
            }

            $this->flash('success', 'Configuración SMTP guardada correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al guardar: ' . $e->getMessage());
        }

        $this->redirect('/admin/emails/smtp');
    }

    /**
     * Test SMTP connection
     */
    public function testSmtp(): void
    {
        $this->requireAuth();

        $testEmail = $this->getPost('test_email');

        if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->jsonError('Email de prueba no válido.');
            return;
        }

        try {
            $emailService = new EmailService();
            $result = $emailService->sendTestEmail($testEmail);

            if ($result) {
                $this->jsonSuccess(['message' => 'Email de prueba enviado correctamente a ' . $testEmail]);
            } else {
                $this->jsonError('No se pudo enviar el email de prueba. Revisa la configuración SMTP.');
            }
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Preview email template
     */
    public function previewTemplate(string $id): void
    {
        $this->requireAuth();

        $template = $this->db->query(
            "SELECT * FROM email_templates WHERE id = ?",
            [(int)$id]
        )->fetch();

        if (!$template) {
            echo '<p>Plantilla no encontrada</p>';
            exit;
        }

        // Sample data for preview
        $sampleData = [
            'attendee_name' => 'Juan García',
            'event_name' => 'TLOS Madrid 2026',
            'event_date' => '15 de Febrero, 2026',
            'event_time' => '09:00',
            'event_location' => 'Madrid, España',
            'event_address' => 'Calle Example 123, 28001 Madrid',
            'ticket_code' => 'TLOS-ABC123',
            'qr_code' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'ticket_url' => '#preview',
            'entity_name' => 'Acme Solutions S.L.',
            'entity_type' => 'Empresa',
            'access_code' => 'ACME2026',
            'portal_url' => '#preview',
            'recipient_name' => 'María López',
            'selector_name' => 'Tech Startup Inc.',
            'match_name' => 'Innovation Labs',
            'sender_name' => 'Carlos Martínez',
            'message_preview' => 'Hola, me gustaría conocer más sobre vuestros servicios...',
            'other_party_name' => 'Digital Services Corp.',
            'meeting_date' => '15 de Febrero, 2026',
            'meeting_time' => '10:30',
            'meeting_location' => 'Sala de Reuniones 3',
            'panel_url' => '#preview',
        ];

        $emailService = new EmailService();
        $html = $emailService->renderTemplateWithData($template['body_html'], $sampleData);

        // Wrap in email layout
        $fullHtml = $emailService->wrapInEmailLayout($html, $template['subject']);

        header('Content-Type: text/html; charset=utf-8');
        echo $fullHtml;
        exit;
    }

    /**
     * Get SMTP settings from database
     */
    private function getSmtpSettings(): array
    {
        $defaults = [
            'smtp_enabled' => 'false',
            'smtp_host' => '',
            'smtp_port' => '587',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'smtp_from_email' => 'noreply@thelastofsaas.com',
            'smtp_from_name' => 'The Last of SaaS',
            'smtp_reply_to' => '',
            'email_footer_text' => 'Este email fue enviado automaticamente. Por favor no respondas a este mensaje.',
        ];

        try {
            $results = $this->db->query("SELECT setting_key, setting_value FROM email_settings")->fetchAll();

            foreach ($results as $row) {
                if (array_key_exists($row['setting_key'], $defaults)) {
                    $defaults[$row['setting_key']] = $row['setting_value'];
                }
            }
        } catch (\Exception $e) {
            // Table might not exist yet
            error_log('Email settings query error: ' . $e->getMessage());
        }

        return $defaults;
    }

    /**
     * Bulk email composer page
     */
    public function bulk(): void
    {
        $this->requireAuth();

        $eventModel = new Event();
        $events = $eventModel->all(['start_date' => 'DESC']);

        $this->renderAdmin('emails/bulk', [
            'title' => 'Email Masivo',
            'events' => $events,
            'variables' => EmailService::getAvailableVariables(),
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Get recipients for bulk email (AJAX)
     */
    public function getRecipients(): void
    {
        $this->requireAuth();

        $eventId = (int) $this->getQuery('event_id');
        $recipientType = $this->getQuery('recipient_type', 'sponsors'); // 'sponsors' or 'companies'

        if (!$eventId) {
            $this->json(['recipients' => [], 'count' => 0]);
            return;
        }

        $recipients = [];

        if ($recipientType === 'sponsors') {
            $sponsorModel = new Sponsor();
            $sponsors = $sponsorModel->getAllByEvent($eventId);

            foreach ($sponsors as $sponsor) {
                if (!empty($sponsor['contact_email'])) {
                    // Handle multiple emails
                    $emails = array_filter(array_map('trim', explode(',', $sponsor['contact_email'])));
                    foreach ($emails as $email) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $recipients[] = [
                                'id' => $sponsor['id'],
                                'name' => $sponsor['name'],
                                'email' => $email,
                                'type' => 'sponsor',
                                'code' => $sponsor['code'] ?? '',
                                'logo_url' => $sponsor['logo_url'] ?? null,
                            ];
                        }
                    }
                }
            }
        } else {
            $companyModel = new Company();
            $companies = $companyModel->getByEvent($eventId);

            foreach ($companies as $company) {
                if (!empty($company['contact_email'])) {
                    // Handle multiple emails
                    $emails = array_filter(array_map('trim', explode(',', $company['contact_email'])));
                    foreach ($emails as $email) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $recipients[] = [
                                'id' => $company['id'],
                                'name' => $company['name'],
                                'email' => $email,
                                'type' => 'company',
                                'code' => $company['code'] ?? '',
                                'logo_url' => $company['logo_url'] ?? null,
                            ];
                        }
                    }
                }
            }
        }

        $this->json([
            'recipients' => $recipients,
            'count' => count($recipients),
        ]);
    }

    /**
     * Preview bulk email
     */
    public function previewBulk(): void
    {
        $this->requireAuth();

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') === false) {
            $this->jsonError('Content-Type debe ser application/json');
            return;
        }

        $json = json_decode(file_get_contents('php://input'), true);
        if (!$json) {
            $this->jsonError('JSON invalido');
            return;
        }

        $subject = $json['subject'] ?? '';
        $bodyHtml = $json['body_html'] ?? '';
        $eventId = (int) ($json['event_id'] ?? 0);

        if (empty($subject) || empty($bodyHtml)) {
            $this->jsonError('Asunto y contenido son obligatorios');
            return;
        }

        // Get event data for variables
        $eventModel = new Event();
        $event = $eventId ? $eventModel->find($eventId) : null;

        // Sample data for preview
        $sampleData = [
            'event_name' => $event['name'] ?? 'Nombre del Evento',
            'event_date' => $event ? $this->formatDate($event['start_date']) : '15 de Febrero, 2026',
            'event_time' => $event['start_time'] ?? '09:00',
            'event_location' => $event['location'] ?? 'Madrid, Espana',
            'event_address' => $event['address'] ?? 'Calle Example 123',
            'entity_name' => 'Empresa de Ejemplo S.L.',
            'entity_type' => 'Empresa',
            'access_code' => 'EJEMPLO2026',
            'portal_url' => url('/empresa/login'),
            'contact_name' => 'Juan Garcia',
            'contact_email' => 'juan@ejemplo.com',
        ];

        $emailService = new EmailService();
        $renderedSubject = $emailService->renderTemplateWithData($subject, $sampleData);
        $renderedBody = $emailService->renderTemplateWithData($bodyHtml, $sampleData);
        $fullHtml = $emailService->wrapInEmailLayout($renderedBody, $renderedSubject);

        $this->json([
            'success' => true,
            'subject' => $renderedSubject,
            'html' => $fullHtml,
        ]);
    }

    /**
     * Send bulk email
     */
    public function sendBulk(): void
    {
        $this->requireAuth();

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') === false) {
            $this->jsonError('Content-Type debe ser application/json');
            return;
        }

        $json = json_decode(file_get_contents('php://input'), true);
        if (!$json) {
            $this->jsonError('JSON invalido');
            return;
        }

        $subject = trim($json['subject'] ?? '');
        $bodyHtml = $json['body_html'] ?? '';
        $eventId = (int) ($json['event_id'] ?? 0);
        $recipientType = $json['recipient_type'] ?? 'sponsors';
        $bccEmail = trim($json['bcc_email'] ?? '');

        if (empty($subject) || empty($bodyHtml) || !$eventId) {
            $this->jsonError('Asunto, contenido y evento son obligatorios');
            return;
        }

        // Get event
        $eventModel = new Event();
        $event = $eventModel->find($eventId);

        if (!$event) {
            $this->jsonError('Evento no encontrado');
            return;
        }

        // Get recipients
        $recipients = [];
        if ($recipientType === 'sponsors') {
            $sponsorModel = new Sponsor();
            $sponsors = $sponsorModel->getAllByEvent($eventId);
            foreach ($sponsors as $sponsor) {
                if (!empty($sponsor['contact_email'])) {
                    $emails = array_filter(array_map('trim', explode(',', $sponsor['contact_email'])));
                    foreach ($emails as $email) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $recipients[] = [
                                'entity' => $sponsor,
                                'email' => $email,
                                'type' => 'sponsor',
                            ];
                        }
                    }
                }
            }
        } else {
            $companyModel = new Company();
            $companies = $companyModel->getByEvent($eventId);
            foreach ($companies as $company) {
                if (!empty($company['contact_email'])) {
                    $emails = array_filter(array_map('trim', explode(',', $company['contact_email'])));
                    foreach ($emails as $email) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $recipients[] = [
                                'entity' => $company,
                                'email' => $email,
                                'type' => 'company',
                            ];
                        }
                    }
                }
            }
        }

        if (empty($recipients)) {
            $this->jsonError('No hay destinatarios validos');
            return;
        }

        $emailService = new EmailService();
        $sent = 0;
        $failed = 0;
        $errors = [];

        // Create bulk email log entry
        $bulkEmailId = $this->logBulkEmail($eventId, $recipientType, $subject, $bodyHtml, count($recipients), $bccEmail);

        foreach ($recipients as $recipient) {
            $entity = $recipient['entity'];
            $email = $recipient['email'];

            // Prepare variables for this recipient
            $variables = [
                'event_name' => $event['name'],
                'event_date' => $this->formatDate($event['start_date']),
                'event_time' => $event['start_time'] ?? '',
                'event_location' => $event['location'] ?? '',
                'event_address' => $event['address'] ?? '',
                'entity_name' => $entity['name'],
                'entity_type' => $recipientType === 'sponsors' ? 'SaaS' : 'Empresa',
                'access_code' => $entity['code'] ?? '',
                'portal_url' => $recipientType === 'sponsors' ? url('/sponsor/login') : url('/empresa/login'),
                'contact_name' => $entity['contact_name'] ?? $entity['name'],
                'contact_email' => $email,
            ];

            // Render subject and body
            $renderedSubject = $emailService->renderTemplateWithData($subject, $variables);
            $renderedBody = $emailService->renderTemplateWithData($bodyHtml, $variables);
            $fullHtml = $emailService->wrapInEmailLayout($renderedBody, $renderedSubject);

            try {
                $result = $emailService->send($email, $renderedSubject, $fullHtml);

                // Send BCC if configured
                if ($result && !empty($bccEmail) && filter_var($bccEmail, FILTER_VALIDATE_EMAIL)) {
                    $emailService->send($bccEmail, "[CCO] " . $renderedSubject, $fullHtml);
                }

                if ($result) {
                    $sent++;
                    $this->logBulkEmailRecipient($bulkEmailId, $entity['id'], $recipientType, $email, 'sent');
                } else {
                    $failed++;
                    $errors[] = $email;
                    $this->logBulkEmailRecipient($bulkEmailId, $entity['id'], $recipientType, $email, 'failed');
                }
            } catch (\Exception $e) {
                $failed++;
                $errors[] = $email . ' (' . $e->getMessage() . ')';
                $this->logBulkEmailRecipient($bulkEmailId, $entity['id'], $recipientType, $email, 'failed', $e->getMessage());
            }

            // Small delay to avoid overwhelming the mail server
            usleep(100000); // 100ms
        }

        // Update bulk email status
        $this->updateBulkEmailStatus($bulkEmailId, $sent, $failed);

        $message = "Email enviado a $sent destinatarios";
        if ($failed > 0) {
            $message .= ". Fallidos: $failed";
        }

        $this->jsonSuccess([
            'message' => $message,
            'sent' => $sent,
            'failed' => $failed,
            'errors' => $errors,
        ]);
    }

    /**
     * Email history page
     */
    public function history(): void
    {
        $this->requireAuth();

        // Ensure tables exist before querying
        $this->ensureBulkEmailTables();

        $eventId = (int) $this->getQuery('event_id');
        $page = (int) ($this->getQuery('page', 1));
        $perPage = 20;

        $eventModel = new Event();
        $events = $eventModel->all(['start_date' => 'DESC']);

        // Get bulk emails
        $whereClause = $eventId ? "WHERE be.event_id = ?" : "";
        $params = $eventId ? [$eventId] : [];

        $totalQuery = $this->db->query(
            "SELECT COUNT(*) as total FROM bulk_emails be $whereClause",
            $params
        )->fetch();
        $total = (int) ($totalQuery['total'] ?? 0);

        $offset = ($page - 1) * $perPage;
        $bulkEmails = $this->db->query(
            "SELECT be.*, e.name as event_name
             FROM bulk_emails be
             LEFT JOIN events e ON e.id = be.event_id
             $whereClause
             ORDER BY be.created_at DESC
             LIMIT $perPage OFFSET $offset",
            $params
        )->fetchAll();

        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage),
        ];

        $this->renderAdmin('emails/history', [
            'title' => 'Historial de Emails',
            'bulkEmails' => $bulkEmails,
            'events' => $events,
            'currentEventId' => $eventId,
            'pagination' => $pagination,
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Log bulk email to database
     */
    private function logBulkEmail(int $eventId, string $recipientType, string $subject, string $bodyHtml, int $totalRecipients, string $bccEmail = ''): int
    {
        // Ensure table exists
        $this->ensureBulkEmailTables();

        $this->db->query(
            "INSERT INTO bulk_emails (event_id, recipient_type, subject, body_html, total_recipients, bcc_email, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, 'sending', NOW())",
            [$eventId, $recipientType, $subject, $bodyHtml, $totalRecipients, $bccEmail]
        );

        return (int) $this->db->lastInsertId();
    }

    /**
     * Log individual recipient status
     */
    private function logBulkEmailRecipient(int $bulkEmailId, int $entityId, string $entityType, string $email, string $status, ?string $errorMessage = null): void
    {
        $this->db->query(
            "INSERT INTO bulk_email_recipients (bulk_email_id, entity_id, entity_type, email, status, error_message, sent_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())",
            [$bulkEmailId, $entityId, $entityType, $email, $status, $errorMessage]
        );
    }

    /**
     * Update bulk email final status
     */
    private function updateBulkEmailStatus(int $bulkEmailId, int $sent, int $failed): void
    {
        $status = $failed === 0 ? 'completed' : ($sent === 0 ? 'failed' : 'partial');
        $this->db->query(
            "UPDATE bulk_emails SET status = ?, sent_count = ?, failed_count = ?, completed_at = NOW() WHERE id = ?",
            [$status, $sent, $failed, $bulkEmailId]
        );
    }

    /**
     * Ensure bulk email tables exist
     */
    private function ensureBulkEmailTables(): void
    {
        // Create bulk_emails table if not exists
        $this->db->query("
            CREATE TABLE IF NOT EXISTS bulk_emails (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_id INT NOT NULL,
                recipient_type VARCHAR(20) NOT NULL,
                subject VARCHAR(500) NOT NULL,
                body_html TEXT NOT NULL,
                total_recipients INT DEFAULT 0,
                sent_count INT DEFAULT 0,
                failed_count INT DEFAULT 0,
                bcc_email VARCHAR(255) DEFAULT NULL,
                status ENUM('sending', 'completed', 'partial', 'failed') DEFAULT 'sending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                completed_at DATETIME DEFAULT NULL,
                INDEX idx_event_id (event_id),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Create bulk_email_recipients table if not exists
        $this->db->query("
            CREATE TABLE IF NOT EXISTS bulk_email_recipients (
                id INT AUTO_INCREMENT PRIMARY KEY,
                bulk_email_id INT NOT NULL,
                entity_id INT NOT NULL,
                entity_type VARCHAR(20) NOT NULL,
                email VARCHAR(255) NOT NULL,
                status ENUM('sent', 'failed') DEFAULT 'sent',
                error_message VARCHAR(500) DEFAULT NULL,
                sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_bulk_email_id (bulk_email_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Format date for display
     */
    private function formatDate(string $date): string
    {
        $dt = new \DateTime($date);
        $months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        return $dt->format('j') . ' de ' . $months[$dt->format('n') - 1] . ' de ' . $dt->format('Y');
    }
}
