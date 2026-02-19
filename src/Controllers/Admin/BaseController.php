<?php
/**
 * Base Frontend Controller
 * Omniwallet CMS
 */

namespace App\Controllers\Frontend;

use App\Core\Controller;
use App\Core\View;
use App\Services\SEOService;
use App\Models\Menu;
use App\Models\Setting;
use App\Models\SiteSettings;
use App\Helpers\TranslationHelper;

abstract class BaseController extends Controller
{
    protected SEOService $seo;
    protected Menu $menuModel;
    protected ?Setting $settingModel = null;
    protected ?SiteSettings $siteSettings = null;
    protected TranslationHelper $translator;
    protected string $currentLang;
    protected array $supportedLangs = ['es', 'en', 'it', 'fr', 'de'];

    public function __construct()
    {
        parent::__construct();
        $this->seo = new SEOService();
        $this->menuModel = new Menu();
        $this->currentLang = $this->detectLanguage();

        // Initialize translator
        $this->translator = TranslationHelper::getInstance();
        $this->translator->setLanguage($this->currentLang);

        // Set language on SEO service for hreflang tags
        $this->seo->setLanguage($this->currentLang);

        // Try to load settings
        try {
            $this->settingModel = new Setting();
        } catch (\Exception $e) {
            // Settings not available yet
        }

        // Try to load site settings (for translations)
        try {
            $this->siteSettings = new SiteSettings();
        } catch (\Exception $e) {
            // Site settings not available yet (migration pending)
        }
    }

    /**
     * Detect current language
     * Priority: URL query param > session (set by Router from URL prefix) > cookie > browser
     */
    protected function detectLanguage(): string
    {
        // Check URL query parameter (?lang=en) - highest priority for manual override
        if (isset($_GET['lang']) && in_array($_GET['lang'], $this->supportedLangs)) {
            $lang = $_GET['lang'];
            $_SESSION['lang'] = $lang;
            // Set cookie for 30 days
            setcookie('lang', $lang, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            return $lang;
        }

        // Check session (already set by Router from URL prefix like /en/page)
        if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $this->supportedLangs)) {
            // Also set cookie for persistence across sessions
            if (!isset($_COOKIE['lang']) || $_COOKIE['lang'] !== $_SESSION['lang']) {
                setcookie('lang', $_SESSION['lang'], time() + (30 * 24 * 60 * 60), '/', '', false, true);
            }
            return $_SESSION['lang'];
        }

        // Check cookie for returning visitors
        if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $this->supportedLangs)) {
            $_SESSION['lang'] = $_COOKIE['lang'];
            return $_COOKIE['lang'];
        }

        // Check browser language for new visitors
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'es', 0, 2);
        if (in_array($browserLang, $this->supportedLangs)) {
            $_SESSION['lang'] = $browserLang;
            return $browserLang;
        }

        // Default to Spanish
        $_SESSION['lang'] = 'es';
        return 'es';
    }

    /**
     * Render frontend view
     */
    protected function view(string $template, array $data = []): void
    {
        // Add common data
        $data['currentLang'] = $this->currentLang;
        $data['supportedLangs'] = $this->supportedLangs;
        $data['availableLangs'] = $this->translator->getAvailableLanguages();
        $data['translator'] = $this->translator;
        $data['seo'] = $this->seo;

        // Add URL helper for language-prefixed links
        $currentLang = $this->currentLang;
        $data['langUrl'] = function(string $path) use ($currentLang): string {
            if ($currentLang === 'es') {
                return $path;
            }
            return '/' . $currentLang . $path;
        };

        // Add navigation data
        $data['mainNav'] = $this->getMainNavigation();
        $data['footerNav'] = $this->getFooterNavigation();

        // Add header buttons
        $data['headerButtons'] = $this->getHeaderButtons();

        // Add social links
        $data['socialLinks'] = $this->getSocialLinks();

        // Add sidebar menu
        $data['sidebarMenu'] = $this->getSidebarMenu();

        // Add footer settings
        $data['footerTagline'] = $this->getFooterTagline();
        $data['footerCopyright'] = $this->getFooterCopyright();

        // Add partner badges and scripts
        $data['partnerBadges'] = json_decode($this->getSetting('partner_badges', '[]'), true) ?: [];
        $data['partnerScripts'] = $this->getSetting('partner_scripts', '');

        // Add branding settings (logos, fonts)
        $data['logoHeader'] = $this->getSetting('logo_header', '/assets/images/logo.svg');
        $data['logoFooter'] = $this->getSetting('logo_footer') ?: $data['logoHeader'];
        $data['favicon'] = $this->getSetting('favicon', '/favicon.ico');
        $data['fontPrimary'] = $this->getSetting('font_primary', 'Inter');
        $data['fontSecondary'] = $this->getSetting('font_secondary', 'Inter');

        // Add tracking settings (GTM/Analytics)
        $data['gtmId'] = $this->getSetting('gtm_id');
        $data['gaId'] = $this->getSetting('ga_id');

        // Add floating form settings
        $data['settings'] = [
            'floating_form_enabled' => $this->getSetting('floating_form_enabled', '1') === '1',
            'floating_form_title' => $this->getSetting('floating_form_title', 'Contáctanos'),
            'floating_form_subtitle' => $this->getSetting('floating_form_subtitle', '¿En qué podemos ayudarte?'),
            'floating_form_button_text' => $this->getSetting('floating_form_button_text', ''),
            'floating_form_button_icon' => $this->getSetting('floating_form_button_icon', 'fas fa-comment-dots'),
            'floating_form_success_title' => $this->getSetting('floating_form_success_title', '¡Mensaje enviado!'),
            'floating_form_success_message' => $this->getSetting('floating_form_success_message', 'Te responderemos lo antes posible.'),
        ];

        // Prevent browser caching of dynamic frontend pages
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $view = new View();
        $view->setLayout('frontend/layouts/main');
        $view->render("frontend/{$template}", $data);
    }

    /**
     * Get header action buttons
     */
    protected function getHeaderButtons(): array
    {
        try {
            $menu = $this->menuModel->getWithItemsByLocationTranslated('header_buttons', $this->currentLang);

            if ($menu && !empty($menu['items'])) {
                return $menu['items'];
            }
        } catch (\Exception $e) {
            // Fall through to default
        }

        return [];
    }

    /**
     * Get social media links
     */
    protected function getSocialLinks(): array
    {
        try {
            $menu = $this->menuModel->getWithItemsByLocation('footer_social');

            if ($menu && !empty($menu['items'])) {
                return $menu['items'];
            }
        } catch (\Exception $e) {
            // Fall through to default
        }

        return [];
    }

    /**
     * Get sidebar menu
     */
    protected function getSidebarMenu(): ?array
    {
        try {
            $menu = $this->menuModel->getWithItemsByLocationTranslated('sidebar', $this->currentLang);

            if ($menu && !empty($menu['items'])) {
                return $menu;
            }
        } catch (\Exception $e) {
            // Fall through to default
        }

        return null;
    }

    /**
     * Get footer tagline (translated)
     */
    protected function getFooterTagline(): string
    {
        // Try language-specific key first (e.g., footer_tagline_en)
        if ($this->currentLang !== 'es') {
            $langKey = 'footer_tagline_' . $this->currentLang;
            $translated = $this->getSetting($langKey);
            if ($translated) {
                return $translated;
            }
        }

        // Fall back to default (Spanish)
        $tagline = $this->getSetting('footer_tagline');
        if ($tagline) {
            return $tagline;
        }

        return '';
    }

    /**
     * Get footer copyright (translated)
     */
    protected function getFooterCopyright(): string
    {
        // Try language-specific key first (e.g., footer_copyright_en)
        if ($this->currentLang !== 'es') {
            $langKey = 'footer_copyright_' . $this->currentLang;
            $translated = $this->getSetting($langKey);
            if ($translated) {
                return $translated;
            }
        }

        // Fall back to default (Spanish)
        $copyright = $this->getSetting('footer_copyright');
        if ($copyright) {
            return $copyright;
        }

        $siteName = $this->getSetting('site_name', '');
        return $siteName ? "© {year} {$siteName}." : "© {year}.";
    }

    /**
     * Get main navigation items
     */
    protected function getMainNavigation(): array
    {
        // Try to load from database with translations
        try {
            $menu = $this->menuModel->getWithItemsByLocationTranslated('header', $this->currentLang);

            if ($menu && !empty($menu['items'])) {
                return $this->formatMenuItems($menu['items']);
            }
        } catch (\Exception $e) {
            // Fall through to default
        }

        // Default fallback (with language prefix)
        return [
            ['url' => $this->localizeUrl('/'), 'label' => 'Inicio', 'icon' => 'home'],
            ['url' => $this->localizeUrl('/funcionalidades'), 'label' => 'Funcionalidades', 'icon' => 'puzzle-piece'],
            ['url' => $this->localizeUrl('/precios'), 'label' => 'Precios', 'icon' => 'tags'],
            ['url' => $this->localizeUrl('/casos-de-exito'), 'label' => 'Casos de Éxito', 'icon' => 'trophy'],
            ['url' => $this->localizeUrl('/blog'), 'label' => 'Blog', 'icon' => 'newspaper'],
            ['url' => $this->localizeUrl('/ayuda'), 'label' => 'Ayuda', 'icon' => 'book'],
            ['url' => $this->localizeUrl('/contacto'), 'label' => 'Contacto', 'icon' => 'envelope']
        ];
    }

    /**
     * Get footer navigation
     */
    protected function getFooterNavigation(): array
    {
        // Try to load from database with translations
        try {
            $menu = $this->menuModel->getWithItemsByLocationTranslated('footer', $this->currentLang);

            if ($menu && !empty($menu['items'])) {
                return $this->formatFooterMenuItems($menu['items']);
            }
        } catch (\Exception $e) {
            // Fall through to default
        }

        // Default fallback (with language prefix)
        return [
            'producto' => [
                'title' => 'Producto',
                'items' => [
                    ['url' => $this->localizeUrl('/funcionalidades'), 'label' => 'Funcionalidades'],
                    ['url' => $this->localizeUrl('/precios'), 'label' => 'Precios'],
                    ['url' => $this->localizeUrl('/integraciones'), 'label' => 'Integraciones'],
                    ['url' => $this->localizeUrl('/seguridad'), 'label' => 'Seguridad']
                ]
            ],
            'recursos' => [
                'title' => 'Recursos',
                'items' => [
                    ['url' => $this->localizeUrl('/blog'), 'label' => 'Blog'],
                    ['url' => $this->localizeUrl('/ayuda'), 'label' => 'Centro de Ayuda'],
                    ['url' => $this->localizeUrl('/casos-de-exito'), 'label' => 'Casos de Éxito'],
                    ['url' => $this->localizeUrl('/api-docs'), 'label' => 'Documentación API']
                ]
            ],
            'empresa' => [
                'title' => 'Empresa',
                'items' => [
                    ['url' => $this->localizeUrl('/sobre-nosotros'), 'label' => 'Sobre Nosotros'],
                    ['url' => $this->localizeUrl('/contacto'), 'label' => 'Contacto'],
                    ['url' => $this->localizeUrl('/empleo'), 'label' => 'Empleo']
                ]
            ],
            'legal' => [
                'title' => 'Legal',
                'items' => [
                    ['url' => $this->localizeUrl('/privacidad'), 'label' => 'Política de Privacidad'],
                    ['url' => $this->localizeUrl('/terminos'), 'label' => 'Términos de Uso'],
                    ['url' => $this->localizeUrl('/cookies'), 'label' => 'Política de Cookies']
                ]
            ]
        ];
    }

    /**
     * Format menu items from database to expected format
     * Adds language prefix to internal URLs for SEO
     */
    protected function formatMenuItems(array $items): array
    {
        $formatted = [];
        foreach ($items as $item) {
            $formatted[] = [
                'url' => $this->localizeUrl($item['url']),
                'label' => $item['title'],
                'icon' => $item['icon'] ?? null,
                'target' => $item['target'] ?? '_self',
                'children' => !empty($item['children']) ? $this->formatMenuItems($item['children']) : []
            ];
        }
        return $formatted;
    }

    /**
     * Add language prefix to URL if not default language
     */
    protected function localizeUrl(string $url): string
    {
        // Don't modify external URLs or anchor links
        if (strpos($url, 'http') === 0 || strpos($url, '#') === 0 || strpos($url, 'mailto:') === 0) {
            return $url;
        }

        // Spanish (default) doesn't need prefix
        if ($this->currentLang === 'es') {
            return $url;
        }

        // Add language prefix
        return '/' . $this->currentLang . $url;
    }

    /**
     * Format footer menu items - groups top-level items as sections
     * Top-level items become section titles, their children become the items
     * Adds language prefix to internal URLs for SEO
     */
    protected function formatFooterMenuItems(array $items): array
    {
        $sections = [];
        foreach ($items as $item) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $item['title']));
            $sections[$slug] = [
                'title' => $item['title'],
                'items' => []
            ];

            // If has children, use them as items
            if (!empty($item['children'])) {
                foreach ($item['children'] as $child) {
                    $sections[$slug]['items'][] = [
                        'url' => $this->localizeUrl($child['url']),
                        'label' => $child['title']
                    ];
                }
            } else {
                // If no children, the item itself is a link
                $sections[$slug]['items'][] = [
                    'url' => $this->localizeUrl($item['url']),
                    'label' => $item['title']
                ];
            }
        }
        return $sections;
    }

    /**
     * Get a setting value from the settings table
     */
    protected function getSetting(string $key, ?string $default = null): ?string
    {
        if ($this->settingModel) {
            try {
                $value = $this->settingModel->get($key);
                if ($value !== null) {
                    return (string) $value;
                }
            } catch (\Exception $e) {
                // Fall through to default
            }
        }
        return $default;
    }

    /**
     * Get translated content
     */
    protected function t(string $key, array $params = []): string
    {
        // TODO: Implement translation lookup
        return $key;
    }
}
