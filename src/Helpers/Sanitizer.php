<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Sanitizer Helper
 * Omniwallet CMS
 */
class Sanitizer
{
    /**
     * Sanitize string (trim only, HTML escaping should be done at display time)
     */
    public static function string(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return trim($value);
    }

    /**
     * Sanitize integer
     */
    public static function int(mixed $value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize float
     */
    public static function float(mixed $value): float
    {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Sanitize email
     */
    public static function email(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return filter_var(trim($value), FILTER_SANITIZE_EMAIL) ?: '';
    }

    /**
     * Sanitize URL
     */
    public static function url(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return filter_var(trim($value), FILTER_SANITIZE_URL) ?: '';
    }

    /**
     * Sanitize HTML content (allow specific tags)
     */
    public static function html(?string $value, array $allowedTags = null): string
    {
        if ($value === null) {
            return '';
        }

        $defaultTags = [
            '<p>', '<br>', '<br/>', '<br />',
            '<strong>', '<b>', '<em>', '<i>', '<u>',
            '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>',
            '<ul>', '<ol>', '<li>',
            '<a>', '<img>',
            '<blockquote>', '<pre>', '<code>',
            '<table>', '<thead>', '<tbody>', '<tr>', '<th>', '<td>',
            '<div>', '<span>',
        ];

        $tags = $allowedTags ?? $defaultTags;

        return strip_tags($value, implode('', $tags));
    }

    /**
     * Sanitize filename
     */
    public static function filename(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        // Remove path components
        $value = basename($value);

        // Replace spaces with dashes
        $value = str_replace(' ', '-', $value);

        // Remove special characters except allowed ones
        $value = preg_replace('/[^a-zA-Z0-9\-_\.]/', '', $value);

        // Remove multiple consecutive dots or dashes
        $value = preg_replace('/\.{2,}/', '.', $value);
        $value = preg_replace('/\-{2,}/', '-', $value);

        return $value;
    }

    /**
     * Sanitize array of strings
     */
    public static function array(array $values): array
    {
        return array_map([self::class, 'string'], $values);
    }

    /**
     * Sanitize JSON input
     */
    public static function json(?string $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $decoded;
    }

    /**
     * Validate and sanitize boolean
     */
    public static function bool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Strip all tags and trim
     */
    public static function plain(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return trim(strip_tags($value));
    }
}
