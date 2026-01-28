<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\EmailService;

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
}
