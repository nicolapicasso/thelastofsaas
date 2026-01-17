<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Setting;

/**
 * Settings Controller
 * Omniwallet CMS Admin
 */
class SettingsController extends Controller
{
    private Setting $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->settingModel = new Setting();
    }

    /**
     * Settings index page
     */
    public function index(): void
    {
        $this->requireAuth();

        $settings = $this->settingModel->getAll();

        // Group settings by category
        $groupedSettings = [
            'general' => $this->settingModel->getByGroup('general'),
            'branding' => $this->settingModel->getByGroup('branding'),
            'localization' => $this->settingModel->getByGroup('localization'),
            'analytics' => $this->settingModel->getByGroup('analytics'),
            'integrations' => $this->settingModel->getByGroup('integrations'),
        ];

        $this->renderAdmin('settings/index', [
            'title' => 'Configuración',
            'settings' => $settings,
            'groupedSettings' => $groupedSettings,
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
            $this->flash('error', 'Token inválido');
            $this->redirect('/admin/settings');
        }

        $settings = $this->getPost('settings', []);

        // Handle partner badges array - convert to JSON for storage
        $partnerBadges = $this->getPost('partner_badges', []);
        if (is_array($partnerBadges) && !empty($partnerBadges)) {
            // Re-index and clean up badges array
            $cleanBadges = [];
            foreach ($partnerBadges as $badge) {
                if (!empty($badge['image']) && !empty($badge['name'])) {
                    $cleanBadges[] = [
                        'name' => $badge['name'],
                        'image' => $badge['image'],
                        'url' => $badge['url'] ?? '',
                    ];
                }
            }
            $settings['partner_badges'] = json_encode($cleanBadges);
        } else {
            $settings['partner_badges'] = '[]';
        }

        if (is_array($settings)) {
            $this->settingModel->updateBatch($settings);
            $this->flash('success', 'Configuración actualizada correctamente');
        } else {
            $this->flash('error', 'Error al actualizar la configuración');
        }

        $this->redirect('/admin/settings');
    }
}
