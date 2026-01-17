<?php
/**
 * Contact API Controller
 * Handles contact form submissions
 * Omniwallet CMS
 */

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Block;
use App\Models\ContactSubmission;

class ContactController extends Controller
{
    /**
     * Submit contact form
     */
    public function submit(): void
    {
        // Get JSON body
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $this->json(['success' => false, 'error' => 'Datos inválidos'], 400);
        }

        $blockId = $input['block_id'] ?? null;

        // Get block configuration
        $blockModel = new Block();
        $block = $blockModel->find((int)$blockId);

        if (!$block) {
            $this->json(['success' => false, 'error' => 'Formulario no encontrado'], 404);
        }

        $content = json_decode($block['content'], true) ?? [];
        $settings = json_decode($block['settings'], true) ?? [];

        // Get enabled fields from configuration
        $configuredFields = $content['fields'] ?? [];
        $enabledFields = array_filter($configuredFields, fn($f) => !empty($f['enabled']));

        // Validate required fields
        $errors = [];
        $submissionData = [];

        foreach ($enabledFields as $field) {
            $fieldName = $field['name'] ?? '';
            $isRequired = !empty($field['required']);
            $value = trim($input[$fieldName] ?? '');

            if ($isRequired && empty($value)) {
                $errors[$fieldName] = 'Este campo es obligatorio';
            } elseif ($value) {
                // Type-specific validation
                if ($field['type'] === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$fieldName] = 'Email inválido';
                }
            }

            if (!empty($value)) {
                $submissionData[$fieldName] = $value;
            }
        }

        if (!empty($errors)) {
            $this->json(['success' => false, 'error' => 'Por favor, corrige los errores del formulario', 'errors' => $errors], 400);
        }

        // Verify reCAPTCHA if enabled
        if (!empty($settings['recaptcha_enabled']) && !empty($settings['recaptcha_secret_key'])) {
            $recaptchaToken = $input['recaptcha_token'] ?? '';

            if (!$this->verifyRecaptcha($recaptchaToken, $settings['recaptcha_secret_key'])) {
                $this->json(['success' => false, 'error' => 'Verificación anti-spam fallida. Por favor, inténtalo de nuevo.'], 400);
            }
        }

        // Send email
        $recipientEmail = $settings['recipient_email'] ?? '';
        if (!empty($recipientEmail)) {
            $this->sendEmail($recipientEmail, $submissionData, $content);
        }

        // Save to database if enabled
        if (!empty($settings['save_submissions'])) {
            $this->saveSubmission($blockId, $submissionData);
        }

        $this->json(['success' => true, 'message' => 'Formulario enviado correctamente']);
    }

    /**
     * Verify reCAPTCHA token
     */
    private function verifyRecaptcha(string $token, string $secretKey): bool
    {
        if (empty($token) || empty($secretKey)) {
            return false;
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($data),
                'timeout' => 10
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);

        // Check success and score (v3 returns a score 0.0 - 1.0)
        if (isset($result['success']) && $result['success']) {
            // For reCAPTCHA v3, check score (0.5 is Google's recommended threshold)
            $score = $result['score'] ?? 1.0;
            return $score >= 0.5;
        }

        return false;
    }

    /**
     * Send notification email
     */
    private function sendEmail(string $to, array $data, array $content): bool
    {
        $subject = 'Nuevo mensaje de contacto - ' . ($_SERVER['HTTP_HOST'] ?? 'Omniwallet');

        // Build email body
        $body = "Has recibido un nuevo mensaje de contacto:\n\n";
        $body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        $labels = [
            'name' => 'Nombre',
            'email' => 'Email',
            'phone' => 'Teléfono',
            'company' => 'Empresa',
            'subject' => 'Asunto',
            'message' => 'Mensaje'
        ];

        foreach ($data as $key => $value) {
            $label = $labels[$key] ?? ucfirst($key);
            $body .= "{$label}: {$value}\n\n";
        }

        $body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        $body .= "Fecha: " . date('d/m/Y H:i:s') . "\n";
        $body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A') . "\n";
        $body .= "Página: " . ($_SERVER['HTTP_REFERER'] ?? 'N/A') . "\n";

        // Headers
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'From: noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

        // Reply-to if email provided
        if (!empty($data['email'])) {
            $headers[] = 'Reply-To: ' . $data['email'];
        }

        return @mail($to, $subject, $body, implode("\r\n", $headers));
    }

    /**
     * Save submission to database
     */
    private function saveSubmission(int $blockId, array $data): void
    {
        $submissionModel = new ContactSubmission();
        $submissionModel->create([
            'block_id' => $blockId,
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'page_url' => $_SERVER['HTTP_REFERER'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
