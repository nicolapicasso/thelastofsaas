<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * URL Helper
 * TLOS - The Last of SaaS
 *
 * Centralizes URL generation for the application.
 */
class UrlHelper
{
    /**
     * Get the application base URL
     */
    public static function getBaseUrl(): string
    {
        // Try to get from environment/config first
        $appUrl = $_ENV['APP_URL'] ?? null;

        if ($appUrl) {
            return rtrim($appUrl, '/');
        }

        // Fallback to server variables
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $protocol . '://' . $host;
    }

    /**
     * Generate a full URL from a path
     */
    public static function url(string $path = ''): string
    {
        $baseUrl = self::getBaseUrl();

        if (empty($path)) {
            return $baseUrl;
        }

        return $baseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Generate event registration URL
     */
    public static function eventRegistration(string $eventSlug): string
    {
        return self::url('eventos/' . $eventSlug . '/registro');
    }

    /**
     * Generate event registration URL with sponsor/invite code
     */
    public static function eventRegistrationWithCode(string $eventSlug, string $code): string
    {
        return self::eventRegistration($eventSlug) . '?code=' . urlencode($code);
    }

    /**
     * Generate sponsor invite code registration URL
     *
     * @param array $event Event data with 'slug' key
     * @param array $code Code data with 'code' key
     * @return string Full registration URL with the code
     */
    public static function sponsorCodeUrl(array $event, array $code): string
    {
        return self::eventRegistrationWithCode($event['slug'], $code['code']);
    }

    /**
     * Generate event URL
     */
    public static function event(string $eventSlug): string
    {
        return self::url('eventos/' . $eventSlug);
    }

    /**
     * Generate sponsor panel URL
     */
    public static function sponsorPanel(int $eventId, string $section = 'panel'): string
    {
        if ($section === 'panel') {
            return self::url('sponsor/panel');
        }

        return self::url('sponsor/' . $section . '/' . $eventId);
    }

    /**
     * Generate admin URL
     */
    public static function admin(string $path = ''): string
    {
        return self::url('admin/' . ltrim($path, '/'));
    }
}
