<?php

declare(strict_types=1);

namespace App\Core;

/**
 * View Class
 * Omniwallet CMS
 */
class View
{
    private string $language = 'es';
    private array $data = [];
    private array $sections = [];
    private ?string $currentSection = null;
    private ?string $layout = null;

    /**
     * Set language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * Get language
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Render template
     */
    public function render(string $template, array $data = []): void
    {
        $this->data = array_merge($this->data, $data);
        $this->data['_language'] = $this->language;
        $this->data['_csrf_token'] = $this->getCsrfToken();

        $templatePath = TEMPLATES_PATH . '/' . str_replace('.', '/', $template) . '.php';

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template not found: {$template}");
        }

        extract($this->data);

        ob_start();
        include $templatePath;
        $content = ob_get_clean();

        if ($this->layout) {
            $layoutPath = TEMPLATES_PATH . '/' . str_replace('.', '/', $this->layout) . '.php';
            $this->data['_content'] = $content;
            extract($this->data);

            include $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Render admin view with layout
     */
    public function renderAdmin(string $template, array $data = []): void
    {
        // Prevent browser caching of admin pages
        header('Cache-Control: no-cache, no-store, must-revalidate, private');
        header('Pragma: no-cache');
        header('Expires: 0');

        $this->layout = 'admin/layouts/main';
        $this->render('admin/' . $template, $data);
    }

    /**
     * Render frontend view with layout
     */
    public function renderFrontend(string $template, array $data = []): void
    {
        $this->layout = 'frontend/layouts/main';
        $this->render('frontend/' . $template, $data);
    }

    /**
     * Set layout (or null for no layout)
     */
    public function setLayout(?string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Start section
     */
    public function startSection(string $name): void
    {
        $this->currentSection = $name;
        ob_start();
    }

    /**
     * End section
     */
    public function endSection(): void
    {
        if ($this->currentSection === null) {
            throw new \RuntimeException('No section started');
        }

        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }

    /**
     * Yield section content
     */
    public function yieldSection(string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }

    /**
     * Include partial template
     */
    public function partial(string $template, array $data = []): void
    {
        $templatePath = TEMPLATES_PATH . '/' . str_replace('.', '/', $template) . '.php';

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Partial not found: {$template}");
        }

        extract(array_merge($this->data, $data));
        include $templatePath;
    }

    /**
     * Escape HTML
     */
    public function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Alias for escape
     */
    public function e(string $value): string
    {
        return $this->escape($value);
    }

    /**
     * Generate URL with language
     */
    public function url(string $path): string
    {
        return Router::url($path, $this->language);
    }

    /**
     * Generate asset URL
     */
    public function asset(string $path): string
    {
        $baseUrl = $_ENV['APP_URL'] ?? '';

        return $baseUrl . '/assets/' . ltrim($path, '/');
    }

    /**
     * Generate upload URL
     */
    public function upload(string $path): string
    {
        $baseUrl = $_ENV['APP_URL'] ?? '';

        return $baseUrl . '/uploads/' . ltrim($path, '/');
    }

    /**
     * Get CSRF token
     */
    public function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Generate CSRF input field
     */
    public function csrfField(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . $this->getCsrfToken() . '">';
    }

    /**
     * Format date
     */
    public function formatDate(string $date, string $format = 'd/m/Y'): string
    {
        return date($format, strtotime($date));
    }

    /**
     * Format number (European format)
     */
    public function formatNumber(float|int $number, int $decimals = 0): string
    {
        return number_format($number, $decimals, ',', '.');
    }

    /**
     * Format price (European format)
     */
    public function formatPrice(float|int $price, int $decimals = 2): string
    {
        return number_format($price, $decimals, ',', '.') . ' €';
    }

    /**
     * Check if current language matches
     */
    public function isLanguage(string $lang): bool
    {
        return $this->language === $lang;
    }

    /**
     * Get flash message
     */
    public function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }

    /**
     * Truncate text
     */
    public function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . $suffix;
    }

    /**
     * Get active class for nav items
     */
    public function activeClass(string $path, string $activeClass = 'active'): string
    {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        return $currentPath === $path ? $activeClass : '';
    }

    /**
     * Add data to view
     */
    public function with(string $key, mixed $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }
}
