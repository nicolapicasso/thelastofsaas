<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Landing;
use App\Models\LandingTheme;
use App\Models\SEOMetadata;
use App\Helpers\Sanitizer;
use App\Services\TranslationService;

/**
 * Landings Controller (Admin)
 * Omniwallet CMS
 */
class LandingsController extends Controller
{
    private Landing $landingModel;
    private LandingTheme $themeModel;
    private SEOMetadata $seoModel;

    public function __construct()
    {
        parent::__construct();
        $this->landingModel = new Landing();
        $this->themeModel = new LandingTheme();
        $this->seoModel = new SEOMetadata();
    }

    /**
     * List all landings
     */
    public function index(): void
    {
        $this->requireAuth();

        $themeId = $this->getQuery('theme');

        $landings = $themeId
            ? $this->landingModel->getByThemeForAdmin((int) $themeId)
            : $this->landingModel->getAllForAdmin();

        $themes = $this->themeModel->getAllForAdmin();
        $currentTheme = $themeId ? $this->themeModel->find((int) $themeId) : null;

        $this->renderAdmin('landings/index', [
            'title' => $currentTheme ? 'Landings: ' . $currentTheme['title'] : 'Todas las Landings',
            'landings' => $landings,
            'themes' => $themes,
            'currentTheme' => $currentTheme,
            'currentThemeId' => $themeId,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $themeId = $this->getQuery('theme');
        $themes = $this->themeModel->getActive();

        if (empty($themes)) {
            $this->flash('error', 'Primero debes crear una temática.');
            $this->redirect('/admin/landing-themes/create');
        }

        $this->renderAdmin('landings/form', [
            'title' => 'Nueva Landing',
            'landing' => null,
            'themes' => $themes,
            'selectedThemeId' => $themeId,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new landing
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/landings/create');
        }

        $data = $this->validateLandingData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/landings/create');
        }

        try {
            $this->landingModel->createWithSlug($data);
            $this->flash('success', 'Landing creada correctamente.');
            $this->redirect('/admin/landings?theme=' . $data['theme_id']);
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear la landing: ' . $e->getMessage());
            $this->redirect('/admin/landings/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        // Use findWithTheme to include theme_slug for the public URL
        $landing = $this->landingModel->findWithTheme((int) $id);

        if (!$landing) {
            $this->flash('error', 'Landing no encontrada.');
            $this->redirect('/admin/landings');
        }

        // Merge SEO metadata from seo_metadata table (for mass generation support)
        $seoData = $this->seoModel->getForEntity('landing', (int) $id, 'es');
        if ($seoData) {
            if (empty($landing['meta_title']) && !empty($seoData['meta_title'])) {
                $landing['meta_title'] = $seoData['meta_title'];
            }
            if (empty($landing['meta_description']) && !empty($seoData['meta_description'])) {
                $landing['meta_description'] = $seoData['meta_description'];
            }
        }

        $themes = $this->themeModel->getActive();

        $this->renderAdmin('landings/form', [
            'title' => 'Editar Landing',
            'landing' => $landing,
            'themes' => $themes,
            'selectedThemeId' => $landing['theme_id'],
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update landing
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/landings/' . $id . '/edit');
        }

        $landing = $this->landingModel->find((int) $id);

        if (!$landing) {
            $this->flash('error', 'Landing no encontrada.');
            $this->redirect('/admin/landings');
        }

        $data = $this->validateLandingData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/landings/' . $id . '/edit');
        }

        try {
            $this->landingModel->updateWithSlug((int) $id, $data);

            // Also save to seo_metadata for consistency with mass generation
            $metaTitle = $data['meta_title'] ?? null;
            $metaDescription = $data['meta_description'] ?? null;
            if (!empty($metaTitle) || !empty($metaDescription)) {
                $this->seoModel->saveForEntity('landing', (int) $id, 'es', [
                    'meta_title' => $metaTitle,
                    'meta_description' => $metaDescription,
                ]);
            }

            $this->flash('success', 'Landing actualizada correctamente.');
            $this->redirect('/admin/landings?theme=' . $data['theme_id']);
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar: ' . $e->getMessage());
            $this->redirect('/admin/landings/' . $id . '/edit');
        }
    }

    /**
     * Delete landing
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/landings');
        }

        $landing = $this->landingModel->find((int) $id);
        $themeId = $landing['theme_id'] ?? null;

        try {
            $this->landingModel->delete((int) $id);
            $this->flash('success', 'Landing eliminada.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar: ' . $e->getMessage());
        }

        $redirect = $themeId ? '/admin/landings?theme=' . $themeId : '/admin/landings';
        $this->redirect($redirect);
    }

    /**
     * Preview landing
     */
    public function preview(string $id): void
    {
        $this->requireAuth();

        $landing = $this->landingModel->find((int) $id);

        if (!$landing) {
            $this->flash('error', 'Landing no encontrada.');
            $this->redirect('/admin/landings');
        }

        $theme = $this->themeModel->find((int) $landing['theme_id']);

        // Render the landing HTML directly
        $this->renderLandingPreview($landing, $theme);
    }

    /**
     * Render landing preview
     */
    private function renderLandingPreview(array $landing, ?array $theme): void
    {
        // Extract content between <body> and </body>
        $html = $landing['html_content'] ?? '';

        // Try to extract body content
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
            $bodyContent = $matches[1];
        } else {
            $bodyContent = $html;
        }

        // Extract styles
        $styles = '';
        if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $styleMatches)) {
            $styles = implode("\n", $styleMatches[0]);
        }

        echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: ' . htmlspecialchars($landing['title']) . '</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    ' . $styles . '
</head>
<body>
    <div style="position: fixed; top: 10px; right: 10px; z-index: 99999; background: #3E95B0; color: white; padding: 10px 20px; border-radius: 5px; font-family: sans-serif;">
        <strong>PREVIEW</strong> |
        <a href="/admin/landings/' . $landing['id'] . '/edit" style="color: white;">Editar</a> |
        <a href="/admin/landings" style="color: white;">Cerrar</a>
    </div>
    ' . $bodyContent . '
</body>
</html>';
        exit;
    }

    /**
     * Translate landing HTML content via AI
     */
    public function translateHtml(string $id): void
    {
        $this->requireAuth();

        header('Content-Type: application/json');

        try {
            // Validate request
            $targetLanguage = $this->getPost('target_language');
            $sourceHtml = $this->getPost('source_html');

            if (empty($targetLanguage)) {
                echo json_encode(['success' => false, 'error' => 'Idioma destino requerido']);
                return;
            }

            if (empty($sourceHtml)) {
                echo json_encode(['success' => false, 'error' => 'HTML fuente requerido']);
                return;
            }

            // Validate language
            $validLanguages = ['en', 'it', 'fr', 'de'];
            if (!in_array($targetLanguage, $validLanguages)) {
                echo json_encode(['success' => false, 'error' => 'Idioma no soportado']);
                return;
            }

            // Initialize translation service
            $translationService = new TranslationService();

            if (!$translationService->isConfigured()) {
                echo json_encode(['success' => false, 'error' => 'API de traducción no configurada']);
                return;
            }

            // Translate the HTML
            $translatedHtml = $translationService->translateHtmlLanding($sourceHtml, $targetLanguage);

            if ($translatedHtml === null) {
                echo json_encode(['success' => false, 'error' => 'Error al traducir. Por favor, inténtalo de nuevo.']);
                return;
            }

            echo json_encode([
                'success' => true,
                'translated_html' => $translatedHtml
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Validate landing form data
     */
    private function validateLandingData(): array
    {
        $errors = [];

        $themeId = Sanitizer::int($this->getPost('theme_id'));
        $title = Sanitizer::string($this->getPost('title'));
        $slug = Sanitizer::string($this->getPost('slug'));
        $subtitle = Sanitizer::string($this->getPost('subtitle'));
        $description = $this->getPost('description');
        $image = Sanitizer::url($this->getPost('image'));
        $icon = Sanitizer::string($this->getPost('icon'));
        $htmlContent = $this->getPost('html_content'); // No sanitize - raw HTML
        $sortOrder = Sanitizer::int($this->getPost('sort_order', 0));
        $isActive = Sanitizer::bool($this->getPost('is_active'));
        $isFeatured = Sanitizer::bool($this->getPost('is_featured'));
        $isPrivate = Sanitizer::bool($this->getPost('is_private'));
        $accessPassword = $this->getPost('access_password');
        $metaTitle = Sanitizer::string($this->getPost('meta_title'));
        $metaDescription = $this->getPost('meta_description');

        // Handle multi-language HTML translations
        $htmlTranslations = $this->getPost('html_content_translations');
        $htmlTranslationsJson = null;

        if (is_array($htmlTranslations)) {
            // Filter out empty translations
            $filteredTranslations = array_filter($htmlTranslations, function($content) {
                return !empty(trim($content));
            });

            if (!empty($filteredTranslations)) {
                $htmlTranslationsJson = json_encode($filteredTranslations, JSON_UNESCAPED_UNICODE);
            }
        }

        if (empty($themeId)) {
            $errors[] = 'La temática es obligatoria.';
        }

        if (empty($title)) {
            $errors[] = 'El título es obligatorio.';
        }

        // Validate private landing requires password (only for new private landings)
        if ($isPrivate && empty($accessPassword)) {
            // Check if we're editing and already have a password
            $landingId = $this->getRouteParam('id');
            if ($landingId) {
                $existingLanding = $this->landingModel->find((int) $landingId);
                if (empty($existingLanding['access_password'])) {
                    $errors[] = 'Las landings privadas requieren una contraseña de acceso.';
                }
            } else {
                $errors[] = 'Las landings privadas requieren una contraseña de acceso.';
            }
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        // Build result array
        $result = [
            'theme_id' => $themeId,
            'title' => $title,
            'slug' => $slug ?: null,
            'subtitle' => $subtitle ?: null,
            'description' => $description ?: null,
            'image' => $image ?: null,
            'icon' => $icon ?: null,
            'html_content' => $htmlContent ?: null,
            'html_content_translations' => $htmlTranslationsJson,
            'sort_order' => $sortOrder,
            'is_active' => $isActive ? 1 : 0,
            'is_featured' => $isFeatured ? 1 : 0,
            'is_private' => $isPrivate ? 1 : 0,
            'meta_title' => $metaTitle ?: null,
            'meta_description' => $metaDescription ?: null,
        ];

        // Hash password if provided (new or changed)
        if (!empty($accessPassword)) {
            $result['access_password'] = $this->landingModel->hashPassword($accessPassword);
        }
        // If private is unchecked, clear the password
        elseif (!$isPrivate) {
            $result['access_password'] = null;
        }
        // Otherwise, don't include password in update (keep existing)

        return $result;
    }

    /**
     * Get route parameter (from URL path)
     */
    private function getRouteParam(string $key): ?string
    {
        // Extract from current URL
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (preg_match('#/admin/landings/(\d+)/#', $path, $matches)) {
            if ($key === 'id') {
                return $matches[1];
            }
        }
        return null;
    }
}
