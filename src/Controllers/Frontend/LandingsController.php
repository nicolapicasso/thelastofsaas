<?php
/**
 * Landings Controller (Frontend)
 * Omniwallet CMS
 */

namespace App\Controllers\Frontend;

use App\Core\View;
use App\Models\Landing;
use App\Models\LandingTheme;

class LandingsController extends BaseController
{
    private Landing $landingModel;
    private LandingTheme $themeModel;

    public function __construct()
    {
        parent::__construct();
        $this->landingModel = new Landing();
        $this->themeModel = new LandingTheme();
    }

    /**
     * Landing themes index page
     */
    public function index(): void
    {
        $themes = $this->themeModel->getActiveWithCount();

        // Translate themes
        $this->translator->translateEntities('landing_theme', $themes);

        // SEO
        $this->seo->setTitle('Landing Pages | Omniwallet');
        $this->seo->setDescription('Descubre casos de uso, guías y recursos sobre Omniwallet.');
        $this->seo->setCanonical('/lp');

        $this->view('landings/themes-index', [
            'themes' => $themes
        ]);
    }

    /**
     * Landings by theme index page
     */
    public function themeIndex(string $themeSlug): void
    {
        $theme = $this->themeModel->findBySlug($themeSlug);

        if (!$theme) {
            $this->notFound();
            return;
        }

        $landings = $this->landingModel->getByThemeSlug($themeSlug);

        // Translate theme and landings
        $this->translator->translateEntity('landing_theme', $theme);
        $this->translator->translateEntities('landing', $landings);

        // SEO
        $title = $theme['meta_title'] ?: $theme['title'] . ' | Omniwallet';
        $description = $theme['meta_description'] ?: $theme['description'] ?: "Explora {$theme['title']} de Omniwallet.";

        $this->seo->setTitle($title);
        $this->seo->setDescription($description);
        $this->seo->setCanonical("/lp/{$themeSlug}");

        if (!empty($theme['image'])) {
            $this->seo->setImage($theme['image']);
        }

        $this->view('landings/index', [
            'theme' => $theme,
            'landings' => $landings
        ]);
    }

    /**
     * Show single landing page
     */
    public function show(string $themeSlug, string $landingSlug): void
    {
        // Use method that includes private landings
        $landing = $this->landingModel->findByThemeAndSlugIncludingPrivate($themeSlug, $landingSlug);

        if (!$landing) {
            $this->notFound();
            return;
        }

        // Check if landing is private
        $isPrivate = $this->landingModel->isPrivate($landing);

        // Check if landing is private and requires password
        if ($isPrivate) {
            // Check if user has access in session
            $sessionKey = 'landing_access_' . $landing['id'];
            if (!isset($_SESSION[$sessionKey]) || $_SESSION[$sessionKey] !== true) {
                // Show password form
                $this->showPasswordForm($landing, $themeSlug, $landingSlug);
                return;
            }

            // Private landing - set noindex-nofollow
            $this->seo->setRobots('noindex, nofollow');
        }

        // Increment view count
        $this->landingModel->incrementViews((int) $landing['id']);

        // Get other landings in the same theme for navigation (exclude private ones)
        // For private landings, we don't show other landings
        $otherLandings = $isPrivate ? [] : $this->landingModel->getOthersInTheme(
            (int) $landing['theme_id'],
            (int) $landing['id'],
            10
        );

        // Translate other landings
        if (!empty($otherLandings)) {
            $this->translator->translateEntities('landing', $otherLandings);
        }

        // SEO
        $title = $landing['meta_title'] ?: $landing['title'] . ' | ' . $landing['theme_title'];
        $description = $landing['meta_description'] ?: $landing['description'] ?: $landing['subtitle'];

        $this->seo->setTitle($title);
        $this->seo->setDescription($description ?: "Descubre {$landing['title']}.");
        $this->seo->setCanonical("/lp/{$themeSlug}/{$landingSlug}");

        if (!empty($landing['image'])) {
            $this->seo->setImage($landing['image']);
        }

        // Render with special landing layout
        $this->renderLanding($landing, $otherLandings, $isPrivate);
    }

    /**
     * Verify password for private landing (POST)
     */
    public function verifyPassword(string $themeSlug, string $landingSlug): void
    {
        $landing = $this->landingModel->findByThemeAndSlugIncludingPrivate($themeSlug, $landingSlug);

        if (!$landing || !$this->landingModel->isPrivate($landing)) {
            $this->redirect("/lp/{$themeSlug}/{$landingSlug}");
            return;
        }

        $password = $_POST['password'] ?? '';

        if ($this->landingModel->verifyPassword($landing, $password)) {
            // Grant access in session
            $_SESSION['landing_access_' . $landing['id']] = true;
            $this->redirect("/lp/{$themeSlug}/{$landingSlug}");
        } else {
            // Show password form with error
            $this->showPasswordForm($landing, $themeSlug, $landingSlug, 'Contraseña incorrecta. Inténtalo de nuevo.');
        }
    }

    /**
     * Show password form for private landing
     * Renders directly without the main layout since password.php is a complete HTML document
     */
    private function showPasswordForm(array $landing, string $themeSlug, string $landingSlug, ?string $error = null): void
    {
        // SEO for password page - definitely noindex
        $this->seo->setTitle('Acceso Protegido | ' . $landing['title']);
        $this->seo->setRobots('noindex, nofollow');

        // Render password page directly without main layout wrapper
        $view = new View();
        $view->setLayout(null); // No layout - password.php is a complete HTML document
        $view->render('frontend/landings/password', [
            'landing' => $landing,
            'themeSlug' => $themeSlug,
            'landingSlug' => $landingSlug,
            'error' => $error,
            'logoHeader' => $this->getSetting('logo_header', '/assets/images/logo.svg'),
            'favicon' => $this->getSetting('favicon', '/favicon.ico'),
            'seo' => $this->seo,
            'currentLang' => $this->currentLang,
        ]);
    }

    /**
     * Render landing page with special layout
     */
    private function renderLanding(array $landing, array $otherLandings, bool $isPrivate = false): void
    {
        // Get HTML content based on current language
        // Uses translated HTML if available, falls back to Spanish
        $html = $this->landingModel->getHtmlContent($landing, $this->currentLang);
        $bodyContent = $html;
        $styles = '';
        $scripts = '';

        // Try to extract body content
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
            $bodyContent = $matches[1];
        }

        // Extract styles
        if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $styleMatches)) {
            $styles = implode("\n", $styleMatches[0]);
        }

        // Extract scripts (exclude external ones with src that might not be available)
        if (preg_match_all('/<script(?![^>]*src=)[^>]*>(.*?)<\/script>/is', $html, $scriptMatches)) {
            $scripts = implode("\n", $scriptMatches[0]);
        }

        // Translate landing metadata fields (title, subtitle, description, etc.)
        $this->translator->translateEntity('landing', $landing);

        // Prepare data for layout
        $data = [
            'landing' => $landing,
            'otherLandings' => $otherLandings,
            'bodyContent' => $bodyContent,
            'styles' => $styles,
            'scripts' => $scripts,
            'currentLang' => $this->currentLang,
            'seo' => $this->seo,
            'isPrivate' => $isPrivate,
            // Branding for header
            'logoHeader' => $this->getSetting('logo_header', '/assets/images/logo.svg'),
            'adminEditUrl' => '/admin/landings/' . $landing['id'] . '/edit',
        ];

        $view = new View();
        $view->setLayout('frontend/layouts/landing');
        $view->render('frontend/landings/show', $data);
    }

    /**
     * 404 handler
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->seo->setTitle('Página no encontrada');
        $this->seo->setRobots('noindex, nofollow');
        $this->view('errors/404', []);
    }
}
