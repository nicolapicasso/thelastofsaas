<?php

declare(strict_types=1);

/**
 * Application Configuration
 * We're Sinapsis CMS
 */

return [
    'name' => $_ENV['APP_NAME'] ?? "We're Sinapsis",
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'key' => $_ENV['APP_KEY'] ?? '',

    'timezone' => 'Europe/Madrid',
    'locale' => 'es',
    'fallback_locale' => 'es',

    'supported_locales' => ['es', 'en'],
    'default_locale' => 'es',

    // Contact
    'contact_email' => 'info@weresinapsis.com',
];
