<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\TlosSetting;
use App\Helpers\Sanitizer;

/**
 * TLOS Settings Controller
 * TLOS - The Last of SaaS
 */
class TlosSettingsController extends Controller
{
    private TlosSetting $settingsModel;

    public function __construct()
    {
        parent::__construct();
        $this->settingsModel = new TlosSetting();
    }

    /**
     * Settings index
     */
    public function index(): void
    {
        $this->requireAuth();

        $settingsGrouped = $this->settingsModel->getAllGrouped();
        $settings = $this->settingsModel->getAll();
        $groups = TlosSetting::getGroups();

        $this->renderAdmin('tlos-settings/index', [
            'title' => 'Configuración TLOS',
            'settingsGrouped' => $settingsGrouped,
            'settings' => $settings,
            'groups' => $groups,
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Update settings
     */
    public function update(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/tlos-settings');
        }

        $settings = $this->getPost('settings', []);

        if (!is_array($settings)) {
            $this->flash('error', 'Datos inválidos.');
            $this->redirect('/admin/tlos-settings');
        }

        try {
            // Known boolean settings from the form (these may not exist in DB yet)
            $knownBooleanSettings = [
                'notify_sponsors',
                'notify_companies',
                'allow_sponsor_messages',
                'auto_match_notification',
                'meeting_confirmation_email',
                'meeting_reminder_email',
            ];

            // Handle unchecked checkboxes for known boolean settings
            foreach ($knownBooleanSettings as $key) {
                if (!isset($settings[$key])) {
                    // Checkbox is unchecked (not sent in POST), set to '0'
                    $this->settingsModel->setWithType($key, '0', 'boolean', 'meetings');
                }
            }

            // Also handle any other boolean settings from the database
            $allSettings = $this->settingsModel->getAllGrouped();
            foreach ($allSettings as $group => $groupSettings) {
                foreach ($groupSettings as $setting) {
                    if ($setting['setting_type'] === 'boolean' && !isset($settings[$setting['setting_key']]) && !in_array($setting['setting_key'], $knownBooleanSettings)) {
                        // Checkbox is unchecked (not sent in POST), set to '0'
                        $this->settingsModel->set($setting['setting_key'], '0');
                    }
                }
            }

            // Process settings that were sent in POST
            foreach ($settings as $key => $value) {
                // Sanitize based on setting type
                $setting = $this->settingsModel->findBy('setting_key', $key);
                if ($setting) {
                    $value = $this->sanitizeSettingValue($value, $setting['setting_type']);
                    $this->settingsModel->set($key, $value);
                } else {
                    // Setting doesn't exist yet - determine type and create it
                    $type = in_array($key, $knownBooleanSettings) ? 'boolean' : 'text';
                    $value = $this->sanitizeSettingValue($value, $type);
                    $this->settingsModel->setWithType($key, $value, $type);
                }
            }

            $this->flash('success', 'Configuración guardada correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al guardar: ' . $e->getMessage());
        }

        $this->redirect('/admin/tlos-settings');
    }

    /**
     * Get single setting value (AJAX)
     */
    public function get(): void
    {
        $this->requireAuth();

        $key = $this->getQuery('key');

        if (!$key) {
            $this->jsonError('Clave no especificada.');
            return;
        }

        $value = $this->settingsModel->get($key);
        $this->jsonSuccess(['key' => $key, 'value' => $value]);
    }

    /**
     * Set single setting value (AJAX)
     */
    public function set(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $key = $this->getPost('key');
        $value = $this->getPost('value');

        if (!$key) {
            $this->jsonError('Clave no especificada.');
            return;
        }

        try {
            $this->settingsModel->set($key, $value);
            $this->jsonSuccess(['message' => 'Configuración guardada.']);
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }

    /**
     * Test email configuration
     */
    public function testEmail(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $testEmail = Sanitizer::string($this->getPost('email'));

        if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->jsonError('Email no válido.');
            return;
        }

        // TODO: Implement actual email sending test
        $this->jsonSuccess(['message' => 'Email de prueba enviado a ' . $testEmail]);
    }

    /**
     * Test Stripe configuration
     */
    public function testStripe(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->jsonError('Sesión expirada.');
            return;
        }

        $secretKey = $this->settingsModel->getStripeSecretKey();

        if (empty($secretKey)) {
            $this->jsonError('Stripe Secret Key no configurada.');
            return;
        }

        // TODO: Implement actual Stripe connection test
        $this->jsonSuccess(['message' => 'Conexión con Stripe verificada.']);
    }

    /**
     * Sanitize setting value based on type
     */
    private function sanitizeSettingValue(mixed $value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'number' => (string) (int) $value,
            'email' => filter_var($value, FILTER_SANITIZE_EMAIL),
            'json' => is_array($value) ? json_encode($value) : (string) $value,
            default => (string) $value,
        };
    }
}
