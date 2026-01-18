<?php

namespace App\Services;

use App\Models\TlosSetting;
use App\Models\EmailNotification;

/**
 * Email Service
 * TLOS - The Last of SaaS
 *
 * Handles all email notifications for the platform
 */
class EmailService
{
    private string $senderEmail;
    private string $senderName;
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUser;
    private string $smtpPassword;
    private bool $smtpSecure;

    private TlosSetting $settings;
    private EmailNotification $notificationModel;

    public function __construct()
    {
        $this->settings = new TlosSetting();
        $this->notificationModel = new EmailNotification();

        // Load configuration
        $this->senderEmail = $this->settings->get('sender_email') ?: ($_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@thelastofsaas.com');
        $this->senderName = $this->settings->get('sender_name') ?: ($_ENV['MAIL_FROM_NAME'] ?? 'The Last of SaaS');
        $this->smtpHost = $_ENV['MAIL_HOST'] ?? 'localhost';
        $this->smtpPort = (int)($_ENV['MAIL_PORT'] ?? 587);
        $this->smtpUser = $_ENV['MAIL_USERNAME'] ?? '';
        $this->smtpPassword = $_ENV['MAIL_PASSWORD'] ?? '';
        $this->smtpSecure = ($_ENV['MAIL_ENCRYPTION'] ?? 'tls') === 'tls';
    }

    /**
     * Send ticket confirmation email
     */
    public function sendTicketConfirmation(array $ticket, array $event): bool
    {
        $qrService = new QRService();
        $qrDataUri = $qrService->generateDataUri($ticket['code']);

        $subject = "Tu entrada para {$event['name']}";

        $variables = [
            'attendee_name' => $ticket['attendee_name'],
            'event_name' => $event['name'],
            'event_date' => $this->formatDate($event['start_date']),
            'event_location' => $event['location'] ?? '',
            'ticket_code' => $ticket['code'],
            'ticket_type' => $ticket['ticket_type_name'] ?? 'General',
            'qr_code' => $qrDataUri,
            'ticket_url' => url("/eventos/{$event['slug']}/ticket/{$ticket['code']}")
        ];

        $html = $this->renderTemplate('ticket_confirmation', $variables);

        $sent = $this->send($ticket['attendee_email'], $subject, $html);

        // Log notification
        $this->logNotification($ticket['event_id'], 'ticket_confirmation', $ticket['attendee_email'], $sent);

        return $sent;
    }

    /**
     * Send selection notification
     */
    public function sendSelectionNotification(string $recipientType, array $recipient, array $selector, array $event, ?string $message = null): bool
    {
        if ($recipientType === 'company') {
            $subject = "{$selector['name']} te ha seleccionado en {$event['name']}";
            $templateName = 'company_selected';
            $recipientEmail = $recipient['contact_email'];
        } else {
            $subject = "{$selector['name']} te ha seleccionado en {$event['name']}";
            $templateName = 'sponsor_selected';
            $recipientEmail = $recipient['contact_email'];
        }

        if (!$recipientEmail) {
            return false;
        }

        $variables = [
            'recipient_name' => $recipient['name'],
            'selector_name' => $selector['name'],
            'selector_logo' => $selector['logo_url'] ?? '',
            'event_name' => $event['name'],
            'event_date' => $this->formatDate($event['start_date']),
            'personal_message' => $message,
            'panel_url' => $recipientType === 'company'
                ? url('/empresa/login')
                : url('/sponsor/login')
        ];

        $html = $this->renderTemplate($templateName, $variables);

        $sent = $this->send($recipientEmail, $subject, $html);

        $this->logNotification($event['id'], $templateName, $recipientEmail, $sent, [
            'selector_id' => $selector['id'],
            'selector_type' => $recipientType === 'company' ? 'sponsor' : 'company'
        ]);

        return $sent;
    }

    /**
     * Send match notification to both parties
     */
    public function sendMatchNotification(array $sponsor, array $company, array $event): bool
    {
        $subject = "¡Match confirmado en {$event['name']}!";

        // Send to sponsor
        if ($sponsor['contact_email']) {
            $html = $this->renderTemplate('match_notification', [
                'recipient_name' => $sponsor['name'],
                'match_name' => $company['name'],
                'match_logo' => $company['logo_url'] ?? '',
                'event_name' => $event['name'],
                'event_date' => $this->formatDate($event['start_date']),
                'panel_url' => url('/sponsor/login'),
                'is_sponsor' => true
            ]);

            $this->send($sponsor['contact_email'], $subject, $html);
            $this->logNotification($event['id'], 'match_notification', $sponsor['contact_email'], true, [
                'sponsor_id' => $sponsor['id'],
                'company_id' => $company['id']
            ]);
        }

        // Send to company
        if ($company['contact_email']) {
            $html = $this->renderTemplate('match_notification', [
                'recipient_name' => $company['name'],
                'match_name' => $sponsor['name'],
                'match_logo' => $sponsor['logo_url'] ?? '',
                'event_name' => $event['name'],
                'event_date' => $this->formatDate($event['start_date']),
                'panel_url' => url('/empresa/login'),
                'is_sponsor' => false
            ]);

            $this->send($company['contact_email'], $subject, $html);
            $this->logNotification($event['id'], 'match_notification', $company['contact_email'], true, [
                'sponsor_id' => $sponsor['id'],
                'company_id' => $company['id']
            ]);
        }

        return true;
    }

    /**
     * Send meeting assignment notification
     */
    public function sendMeetingNotification(array $meeting, array $sponsor, array $company, array $event): bool
    {
        $subject = "Reunión programada - {$event['name']}";

        $commonVars = [
            'event_name' => $event['name'],
            'meeting_date' => $this->formatDate($meeting['event_date']),
            'meeting_time' => substr($meeting['slot_time'], 0, 5),
            'meeting_location' => $meeting['room_name'] ?? 'Mesa ' . $meeting['room_number'],
            'meeting_notes' => $meeting['notes'] ?? ''
        ];

        // Send to sponsor
        if ($sponsor['contact_email']) {
            $html = $this->renderTemplate('meeting_assigned', array_merge($commonVars, [
                'recipient_name' => $sponsor['name'],
                'other_party_name' => $company['name'],
                'other_party_logo' => $company['logo_url'] ?? '',
                'panel_url' => url('/sponsor/login')
            ]));

            $this->send($sponsor['contact_email'], $subject, $html);
        }

        // Send to company
        if ($company['contact_email']) {
            $html = $this->renderTemplate('meeting_assigned', array_merge($commonVars, [
                'recipient_name' => $company['name'],
                'other_party_name' => $sponsor['name'],
                'other_party_logo' => $sponsor['logo_url'] ?? '',
                'panel_url' => url('/empresa/login')
            ]));

            $this->send($company['contact_email'], $subject, $html);
        }

        return true;
    }

    /**
     * Send event reminder
     */
    public function sendEventReminder(array $ticket, array $event): bool
    {
        $subject = "Recordatorio: {$event['name']} es mañana";

        $variables = [
            'attendee_name' => $ticket['attendee_name'],
            'event_name' => $event['name'],
            'event_date' => $this->formatDate($event['start_date']),
            'event_time' => $event['start_time'] ? substr($event['start_time'], 0, 5) : '',
            'event_location' => $event['location'] ?? '',
            'event_address' => $event['address'] ?? '',
            'ticket_code' => $ticket['code'],
            'ticket_url' => url("/eventos/{$event['slug']}/ticket/{$ticket['code']}")
        ];

        $html = $this->renderTemplate('event_reminder', $variables);

        return $this->send($ticket['attendee_email'], $subject, $html);
    }

    /**
     * Send generic email
     */
    public function send(string $to, string $subject, string $html, ?string $plainText = null): bool
    {
        if (!$plainText) {
            $plainText = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html));
        }

        // Use PHP's mail() for simplicity, or SMTP if configured
        if ($this->smtpHost && $this->smtpHost !== 'localhost' && $this->smtpUser) {
            return $this->sendViaSMTP($to, $subject, $html, $plainText);
        }

        return $this->sendViaMail($to, $subject, $html, $plainText);
    }

    /**
     * Send via PHP mail()
     */
    private function sendViaMail(string $to, string $subject, string $html, string $plainText): bool
    {
        $boundary = md5(time());

        $headers = [
            'From: ' . $this->senderName . ' <' . $this->senderEmail . '>',
            'Reply-To: ' . $this->senderEmail,
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"'
        ];

        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $body .= $plainText . "\r\n\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $body .= $html . "\r\n\r\n";
        $body .= "--{$boundary}--";

        return mail($to, $subject, $body, implode("\r\n", $headers));
    }

    /**
     * Send via SMTP
     */
    private function sendViaSMTP(string $to, string $subject, string $html, string $plainText): bool
    {
        try {
            $socket = fsockopen(
                ($this->smtpSecure ? 'tls://' : '') . $this->smtpHost,
                $this->smtpPort,
                $errno,
                $errstr,
                30
            );

            if (!$socket) {
                throw new \Exception("SMTP connection failed: {$errstr}");
            }

            // Simple SMTP conversation
            $this->smtpCommand($socket, null); // Read greeting
            $this->smtpCommand($socket, "EHLO " . gethostname());

            if ($this->smtpUser) {
                $this->smtpCommand($socket, "AUTH LOGIN");
                $this->smtpCommand($socket, base64_encode($this->smtpUser));
                $this->smtpCommand($socket, base64_encode($this->smtpPassword));
            }

            $this->smtpCommand($socket, "MAIL FROM:<{$this->senderEmail}>");
            $this->smtpCommand($socket, "RCPT TO:<{$to}>");
            $this->smtpCommand($socket, "DATA");

            // Build message
            $boundary = md5(time());
            $message = "From: {$this->senderName} <{$this->senderEmail}>\r\n";
            $message .= "To: {$to}\r\n";
            $message .= "Subject: {$subject}\r\n";
            $message .= "MIME-Version: 1.0\r\n";
            $message .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n\r\n";
            $message .= "--{$boundary}\r\n";
            $message .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $message .= $plainText . "\r\n\r\n";
            $message .= "--{$boundary}\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
            $message .= $html . "\r\n\r\n";
            $message .= "--{$boundary}--\r\n.\r\n";

            fwrite($socket, $message);
            $this->smtpCommand($socket, "QUIT");

            fclose($socket);
            return true;

        } catch (\Exception $e) {
            error_log('SMTP Error: ' . $e->getMessage());
            // Fallback to mail()
            return $this->sendViaMail($to, $subject, $html, $plainText);
        }
    }

    /**
     * SMTP command helper
     */
    private function smtpCommand($socket, ?string $command): string
    {
        if ($command !== null) {
            fwrite($socket, $command . "\r\n");
        }
        return fgets($socket, 512);
    }

    /**
     * Render email template
     */
    private function renderTemplate(string $templateName, array $variables): string
    {
        // Try to load custom template from database
        $template = $this->getTemplate($templateName);

        if (!$template) {
            // Use default template
            $template = $this->getDefaultTemplate($templateName);
        }

        // Replace variables
        foreach ($variables as $key => $value) {
            if ($value === null) $value = '';
            $template = str_replace('{{' . $key . '}}', htmlspecialchars($value), $template);
            $template = str_replace('{{{' . $key . '}}}', $value, $template); // Raw (for URLs, HTML)
        }

        // Wrap in base template
        return $this->wrapInBaseTemplate($template);
    }

    /**
     * Get template from database
     */
    private function getTemplate(string $name): ?string
    {
        // TODO: Implement EmailTemplate model lookup
        return null;
    }

    /**
     * Get default template content
     */
    private function getDefaultTemplate(string $name): string
    {
        $templates = [
            'ticket_confirmation' => '
                <h2>¡Gracias por tu registro, {{attendee_name}}!</h2>
                <p>Tu entrada para <strong>{{event_name}}</strong> está confirmada.</p>
                <div style="text-align: center; margin: 30px 0;">
                    <img src="{{{qr_code}}}" alt="QR Code" style="width: 200px; height: 200px;">
                    <p style="font-size: 24px; font-family: monospace; letter-spacing: 3px; margin-top: 10px;">{{ticket_code}}</p>
                </div>
                <p><strong>Fecha:</strong> {{event_date}}</p>
                <p><strong>Lugar:</strong> {{event_location}}</p>
                <p style="margin-top: 30px;">
                    <a href="{{{ticket_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Ver mi entrada</a>
                </p>
            ',

            'company_selected' => '
                <h2>¡{{selector_name}} te ha seleccionado!</h2>
                <p>Hola {{recipient_name}},</p>
                <p>El sponsor <strong>{{selector_name}}</strong> te ha seleccionado para el evento <strong>{{event_name}}</strong>.</p>
                ' . '{{#personal_message}}<div style="background: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;"><strong>Mensaje:</strong><br>{{personal_message}}</div>{{/personal_message}}' . '
                <p>Accede a tu panel para ver los detalles y confirmar el match.</p>
                <p style="margin-top: 30px;">
                    <a href="{{{panel_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Acceder al panel</a>
                </p>
            ',

            'sponsor_selected' => '
                <h2>¡{{selector_name}} te ha seleccionado!</h2>
                <p>Hola {{recipient_name}},</p>
                <p>La empresa <strong>{{selector_name}}</strong> está interesada en conocerte en <strong>{{event_name}}</strong>.</p>
                <p>Accede a tu panel para ver los detalles.</p>
                <p style="margin-top: 30px;">
                    <a href="{{{panel_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Acceder al panel</a>
                </p>
            ',

            'match_notification' => '
                <h2>¡Match confirmado! 🎉</h2>
                <p>Hola {{recipient_name}},</p>
                <p>¡Enhorabuena! Tienes un match mutuo con <strong>{{match_name}}</strong> para el evento <strong>{{event_name}}</strong>.</p>
                <p>Próximamente recibirás información sobre tu reunión programada.</p>
                <p style="margin-top: 30px;">
                    <a href="{{{panel_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Ver mis matches</a>
                </p>
            ',

            'meeting_assigned' => '
                <h2>Reunión programada</h2>
                <p>Hola {{recipient_name}},</p>
                <p>Se ha programado tu reunión con <strong>{{other_party_name}}</strong> durante <strong>{{event_name}}</strong>.</p>
                <div style="background: #f3f4f6; padding: 20px; border-radius: 6px; margin: 20px 0;">
                    <p><strong>📅 Fecha:</strong> {{meeting_date}}</p>
                    <p><strong>🕐 Hora:</strong> {{meeting_time}}</p>
                    <p><strong>📍 Ubicación:</strong> {{meeting_location}}</p>
                </div>
                <p style="margin-top: 30px;">
                    <a href="{{{panel_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Ver detalles</a>
                </p>
            ',

            'event_reminder' => '
                <h2>Recordatorio: ¡Tu evento es mañana!</h2>
                <p>Hola {{attendee_name}},</p>
                <p>Te recordamos que <strong>{{event_name}}</strong> es mañana.</p>
                <div style="background: #f3f4f6; padding: 20px; border-radius: 6px; margin: 20px 0;">
                    <p><strong>📅 Fecha:</strong> {{event_date}}</p>
                    <p><strong>🕐 Hora:</strong> {{event_time}}</p>
                    <p><strong>📍 Lugar:</strong> {{event_location}}</p>
                    <p><strong>📍 Dirección:</strong> {{event_address}}</p>
                </div>
                <p>Tu código de entrada: <strong style="font-size: 20px; letter-spacing: 2px;">{{ticket_code}}</strong></p>
                <p style="margin-top: 30px;">
                    <a href="{{{ticket_url}}}" style="background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;">Ver mi entrada</a>
                </p>
            '
        ];

        return $templates[$name] ?? '<p>Template not found</p>';
    }

    /**
     * Wrap content in base email template
     */
    private function wrapInBaseTemplate(string $content): string
    {
        $siteName = $this->settings->get('site_name', 'The Last of SaaS');

        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">' . htmlspecialchars($siteName) . '</h1>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            ' . $content . '
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="color: #6b7280; font-size: 14px; margin: 0;">' . htmlspecialchars($siteName) . '</p>
                            <p style="color: #9ca3af; font-size: 12px; margin: 10px 0 0 0;">Este email fue enviado automáticamente. Por favor no respondas a este mensaje.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
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

    /**
     * Log email notification
     */
    private function logNotification(int $eventId, string $type, string $recipient, bool $sent, array $extraData = []): void
    {
        try {
            $this->notificationModel->log([
                'event_id' => $eventId,
                'type' => $type,
                'recipient_email' => $recipient,
                'status' => $sent ? 'sent' : 'failed',
                'extra_data' => json_encode($extraData)
            ]);
        } catch (\Exception $e) {
            error_log('Failed to log email notification: ' . $e->getMessage());
        }
    }
}
