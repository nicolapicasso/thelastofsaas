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
    preg_match('/(generate-qa|\/api\/|reorder|translate|toggle|validar-codigo)/i', $_SERVER['REQUEST_URI'] ?? '')
) || (
    // POST requests to registration endpoints should be treated as AJAX
    $_SERVER['REQUEST_METHOD'] === 'POST' && preg_match('/\/registro($|\?)/', $_SERVER['REQUEST_URI'] ?? '')
);

// For AJAX requests, don't display errors as HTML - handle them as JSON
if ($isAjaxRequest) {
    // Prevent browser caching of AJAX responses
    header('Cache-Control: no-cache, no-store, must-revalidate, private');
    header('Pragma: no-cache');
    header('Expires: 0');

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
            'error' => $exception->getMessage(),
            'file' => basename($exception->getFile()),
            'line' => $exception->getLine(),
            'trace' => array_slice(array_map(function($t) {
                return ($t['file'] ?? 'unknown') . ':' . ($t['line'] ?? 0) . ' ' . ($t['function'] ?? '');
            }, $exception->getTrace()), 0, 5)
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

// Start session with secure cookie settings
if (session_status() === PHP_SESSION_NONE) {
    // Configure session cookie to prevent caching issues
    session_set_cookie_params([
        'lifetime' => 0,           // Session cookie (deleted on browser close)
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();

    // Regenerate session ID periodically to prevent fixation
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        // Regenerate session ID every 30 minutes
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

// Prevent caching for admin and authenticated routes
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$isAdminRoute = strpos($requestUri, '/admin') === 0;
$isSponsorPanel = strpos($requestUri, '/sponsor/') === 0;
$isCompanyPanel = strpos($requestUri, '/empresa/') === 0;
$isAuthenticatedRoute = $isAdminRoute || $isSponsorPanel || $isCompanyPanel;

if ($isAuthenticatedRoute && !$isAjaxRequest) {
    // Strong anti-cache headers for all authenticated routes
    header('Cache-Control: no-cache, no-store, must-revalidate, private, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    header('Vary: Cookie');
    // Prevent ETags from causing 304 responses
    header_remove('ETag');
    header_remove('Last-Modified');
} elseif (!$isAjaxRequest) {
    // Frontend dynamic pages: force browser to validate with server
    // This ensures changes to blocks are reflected immediately on F5 refresh
    header('Cache-Control: no-cache, must-revalidate, private');
    header('Pragma: no-cache');
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
