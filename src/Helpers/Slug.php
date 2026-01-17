<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Slug Helper
 * Omniwallet CMS
 */
class Slug
{
    /**
     * Generate URL-friendly slug from string
     */
    public static function generate(string $text, string $separator = '-'): string
    {
        // Convert to lowercase
        $text = mb_strtolower($text, 'UTF-8');

        // Replace accented characters
        $text = self::removeAccents($text);

        // Replace non-alphanumeric characters with separator
        $text = preg_replace('/[^a-z0-9]+/u', $separator, $text);

        // Remove duplicate separators
        $text = preg_replace('/' . preg_quote($separator, '/') . '+/', $separator, $text);

        // Trim separators from ends
        $text = trim($text, $separator);

        return $text;
    }

    /**
     * Remove accented characters
     */
    private static function removeAccents(string $text): string
    {
        $map = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n', 'ç' => 'c',
            'ß' => 'ss',
        ];

        return strtr($text, $map);
    }

    /**
     * Generate unique slug for a table
     */
    public static function unique(
        string $text,
        string $table,
        string $column = 'slug',
        ?int $excludeId = null
    ): string {
        $db = \App\Core\Database::getInstance();
        $baseSlug = self::generate($text);
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
            $params = [$slug];

            if ($excludeId !== null) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $count = (int) $db->fetchColumn($sql, $params);

            if ($count === 0) {
                break;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
