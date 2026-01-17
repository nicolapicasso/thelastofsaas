<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router Class
 * Omniwallet CMS
 */
class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private string $currentLanguage = 'es';
    private array $supportedLanguages = ['es', 'en', 'it', 'fr', 'de'];

    /**
     * Add GET route
     */
    public function get(string $path, string $controller, string $action, array $middlewares = []): self
    {
        return $this->addRoute('GET', $path, $controller, $action, $middlewares);
    }

    /**
     * Add POST route
     */
    public function post(string $path, string $controller, string $action, array $middlewares = []): self
    {
        return $this->addRoute('POST', $path, $controller, $action, $middlewares);
    }

    /**
     * Add PUT route
     */
    public function put(string $path, string $controller, string $action, array $middlewares = []): self
    {
        return $this->addRoute('PUT', $path, $controller, $action, $middlewares);
    }

    /**
     * Add DELETE route
     */
    public function delete(string $path, string $controller, string $action, array $middlewares = []): self
    {
        return $this->addRoute('DELETE', $path, $controller, $action, $middlewares);
    }

    /**
     * Add route with all methods
     */
    public function any(string $path, string $controller, string $action, array $middlewares = []): self
    {
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) {
            $this->addRoute($method, $path, $controller, $action, $middlewares);
        }

        return $this;
    }

    /**
     * Add route
     */
    private function addRoute(string $method, string $path, string $controller, string $action, array $middlewares): self
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
            'middlewares' => $middlewares,
        ];

        return $this;
    }

    /**
     * Add global middleware
     */
    public function addMiddleware(string $middleware): self
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    /**
     * Group routes with prefix
     */
    public function group(string $prefix, callable $callback, array $middlewares = []): self
    {
        $previousMiddlewares = $this->middlewares;

        $this->middlewares = array_merge($this->middlewares, $middlewares);

        $callback($this, $prefix);

        $this->middlewares = $previousMiddlewares;

        return $this;
    }

    /**
     * Dispatch the request
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->parseUri();

        // Detect language from URL
        $this->detectLanguage($uri);

        // Find matching route
        $route = $this->findRoute($method, $uri);

        if ($route === null) {
            $this->notFound();
            return;
        }

        // Run middlewares
        $allMiddlewares = array_merge($this->middlewares, $route['middlewares']);
        foreach ($allMiddlewares as $middleware) {
            $middlewareClass = "App\\Middleware\\{$middleware}";
            if (class_exists($middlewareClass)) {
                $middlewareInstance = new $middlewareClass();
                if (!$middlewareInstance->handle()) {
                    return;
                }
            }
        }

        // Call controller action
        $this->callAction($route['controller'], $route['action'], $route['params']);
    }

    /**
     * Parse URI from request
     */
    private function parseUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Remove trailing slash except for root
        $uri = rtrim($uri, '/') ?: '/';

        return $uri;
    }

    /**
     * Detect language from URL prefix (e.g., /en/page, /fr/page)
     * Always strips language prefix from URI for routing
     */
    private function detectLanguage(string &$uri): void
    {
        $segments = explode('/', trim($uri, '/'));

        if (!empty($segments[0]) && in_array($segments[0], $this->supportedLanguages)) {
            $this->currentLanguage = $segments[0];

            // Always remove language prefix from URI for routing
            array_shift($segments);
            $uri = '/' . implode('/', $segments);
            if ($uri === '/') {
                $uri = '/';
            }
        }

        // Store language in session with consistent key 'lang'
        $_SESSION['lang'] = $this->currentLanguage;
    }

    /**
     * Find matching route
     */
    private function findRoute(string $method, string $uri): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);

            if (preg_match($pattern, $uri, $matches)) {
                // Remove full match and filter to only string keys (named groups)
                // Then get values only to avoid PHP 8+ named argument issues
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key) && $value !== '') {
                        $params[] = $value;
                    }
                }

                return array_merge($route, ['params' => $params]);
            }
        }

        return null;
    }

    /**
     * Convert route path to regex pattern
     */
    private function convertToRegex(string $path): string
    {
        // Convert {param} to named groups
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);

        // Convert {param?} for optional parameters
        $pattern = preg_replace('/\{([a-zA-Z_]+)\?\}/', '(?P<$1>[^/]*)?', $pattern);

        return '#^' . $pattern . '$#';
    }

    /**
     * Call controller action
     */
    private function callAction(string $controller, string $action, array $params): void
    {
        $controllerClass = "App\\Controllers\\{$controller}";

        if (!class_exists($controllerClass)) {
            $this->notFound("Controller {$controller} not found");
            return;
        }

        $controllerInstance = new $controllerClass();

        if (!method_exists($controllerInstance, $action)) {
            $this->notFound("Action {$action} not found in {$controller}");
            return;
        }

        // Set language on controller
        if (method_exists($controllerInstance, 'setLanguage')) {
            $controllerInstance->setLanguage($this->currentLanguage);
        }

        call_user_func_array([$controllerInstance, $action], $params);
    }

    /**
     * Handle 404 Not Found
     */
    private function notFound(string $message = 'Page not found'): void
    {
        http_response_code(404);

        if (file_exists(TEMPLATES_PATH . '/frontend/pages/404.php')) {
            include TEMPLATES_PATH . '/frontend/pages/404.php';
        } else {
            echo $message;
        }
    }

    /**
     * Get current language
     */
    public function getLanguage(): string
    {
        return $this->currentLanguage;
    }

    /**
     * Generate URL with language prefix
     */
    public static function url(string $path, ?string $language = null): string
    {
        $language = $language ?? ($_SESSION['lang'] ?? 'es');
        $baseUrl = $_ENV['APP_URL'] ?? '';

        // Spanish (default) doesn't need prefix
        if ($language === 'es') {
            return $baseUrl . $path;
        }

        return $baseUrl . '/' . $language . $path;
    }

    /**
     * Get current language from session
     */
    public static function getCurrentLanguage(): string
    {
        return $_SESSION['lang'] ?? 'es';
    }
}
