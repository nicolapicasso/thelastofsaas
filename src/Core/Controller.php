<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base Controller Class
 * Omniwallet CMS
 */
abstract class Controller
{
    protected Database $db;
    protected View $view;
    protected string $language = 'es';

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->view = new View();
    }

    /**
     * Set current language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
        $this->view->setLanguage($language);
    }

    /**
     * Get current language
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Render a view
     */
    protected function render(string $template, array $data = []): void
    {
        $this->view->render($template, $data);
    }

    /**
     * Render admin view with layout
     */
    protected function renderAdmin(string $template, array $data = []): void
    {
        $this->view->renderAdmin($template, $data);
    }

    /**
     * Render frontend view with layout
     */
    protected function renderFrontend(string $template, array $data = []): void
    {
        $this->view->renderFrontend($template, $data);
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        // Clean any previous output buffer (catches PHP errors/notices)
        while (ob_get_level()) {
            ob_end_clean();
        }

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * Get POST data
     */
    protected function getPost(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_POST;
        }

        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     */
    protected function getQuery(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_GET;
        }

        return $_GET[$key] ?? $default;
    }

    /**
     * Get uploaded file
     */
    protected function getFile(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * Check if request is AJAX
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if request is POST
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrf(): bool
    {
        $token = $this->getPost('_csrf_token') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    /**
     * Generate CSRF token
     */
    protected function generateCsrf(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Set flash message
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /**
     * Get and clear flash message
     */
    protected function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Get current user ID
     */
    protected function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user
     */
    protected function getUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return $this->db->fetch(
            "SELECT id, email, name, role, avatar FROM users WHERE id = ?",
            [$this->getUserId()]
        );
    }

    /**
     * Require authentication
     */
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('/admin/login');
        }
    }

    /**
     * Require specific role
     */
    protected function requireRole(string $role): void
    {
        $this->requireAuth();

        $user = $this->getUser();

        if (!$user || $user['role'] !== $role) {
            http_response_code(403);
            echo 'Access denied';
            exit;
        }
    }
}
