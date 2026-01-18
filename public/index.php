<?php

declare(strict_types=1);

/**
 * TLOS CMS - Entry Point
 *
 * This is the main entry point for the application.
 * All requests are routed through this file.
 */

// Error reporting for development
error_reporting(E_ALL);

// Detect if this is an AJAX/JSON request
$isAjaxRequest = (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) || (
    isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
) || (
    isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
) || (
    // Check if URL suggests an API/AJAX endpoint
    preg_match('/(generate-qa|\/api\/|reorder|translate|toggle)/i', $_SERVER['REQUEST_URI'] ?? '')
);

// For AJAX requests, don't display errors as HTML - handle them as JSON
if ($isAjaxRequest) {
    ini_set('display_errors', '0');

    // Set up error handler for AJAX requests
    set_error_handler(function($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    // Set up exception handler for AJAX requests
    set_exception_handler(function($exception) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Error del servidor: ' . $exception->getMessage(),
            'debug' => [
                'file' => basename($exception->getFile()),
                'line' => $exception->getLine()
            ]
        ]);
        exit;
    });
} else {
    ini_set('display_errors', '1');
}

// Define base paths
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

// Autoload
require ROOT_PATH . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
if (file_exists(ROOT_PATH . '/.env')) {
    $dotenv->load();
}

// Set timezone
date_default_timezone_set('Europe/Madrid');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize application
use App\Core\Router;
use App\Core\Database;

// Load translation helper functions (__() and __t())
require_once ROOT_PATH . '/src/Helpers/TranslationHelper.php';

// Initialize database connection
try {
    $db = Database::getInstance();
} catch (PDOException $e) {
    if ($_ENV['APP_DEBUG'] ?? false) {
        die('Database connection failed: ' . $e->getMessage());
    }
    die('Database connection failed. Please check your configuration.');
}

// Initialize router
$router = new Router();

// Load routes
require ROOT_PATH . '/config/routes.php';

// Dispatch request
$router->dispatch();
