<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * SiteSettings Model
 * Manages global site configuration and translations
 * Omniwallet CMS
 */
class SiteSettings extends Model
{
    protected string $table = 'site_settings';

    protected array $fillable = [
        'key',
        'value',
        'translations',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Setting types
     */
    public const TYPES = [
        'text' => 'Texto',
        'textarea' => 'Texto largo',
        'html' => 'HTML',
        'json' => 'JSON',
        'boolean' => 'Sí/No',
        'number' => 'Número'
    ];

    /**
     * Setting groups
     */
    public const GROUPS = [
        'general' => 'General',
        'footer' => 'Footer',
        'header' => 'Cabecera',
        'seo' => 'SEO',
        'social' => 'Redes Sociales'
    ];

    /**
     * Get setting by key
     */
    public function get(string $key, ?string $default = null): ?string
    {
        $setting = $this->first(['key' => $key]);
        return $setting ? $setting['value'] : $default;
    }

    /**
     * Get setting with translation for a specific language
     */
    public function getTranslated(string $key, string $language = 'es', ?string $default = null): ?string
    {
        $setting = $this->first(['key' => $key]);

        if (!$setting) {
            return $default;
        }

        // If Spanish or no translations, return original value
        if ($language === 'es' || empty($setting['translations'])) {
            return $setting['value'] ?? $default;
        }

        // Parse translations
        $translations = is_string($setting['translations'])
            ? json_decode($setting['translations'], true)
            : $setting['translations'];

        // Return translated value if exists, otherwise original
        return $translations[$language] ?? $setting['value'] ?? $default;
    }

    /**
     * Get all settings in a group
     */
    public function getGroup(string $group): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE `group` = ? ORDER BY `key`";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group]);
        return $stmt->fetchAll();
    }

    /**
     * Get all settings organized by group
     */
    public function getAllByGroup(): array
    {
        $settings = $this->all(['group' => 'ASC', 'key' => 'ASC']);
        $grouped = [];

        foreach ($settings as $setting) {
            $group = $setting['group'] ?? 'general';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $setting;
        }

        return $grouped;
    }

    /**
     * Set a setting value
     */
    public function set(string $key, string $value, ?array $translations = null): bool
    {
        $existing = $this->first(['key' => $key]);

        $data = ['value' => $value];
        if ($translations !== null) {
            $data['translations'] = json_encode($translations);
        }

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        $data['key'] = $key;
        return $this->create($data) !== false;
    }

    /**
     * Update setting with translations
     */
    public function updateWithTranslations(int $id, string $value, array $translations): bool
    {
        $sql = "UPDATE {$this->table} SET value = ?, translations = ? WHERE id = ?";
        return $this->db->query($sql, [$value, json_encode($translations), $id]);
    }

    /**
     * Get a setting as array (for JSON type)
     */
    public function getJson(string $key, array $default = []): array
    {
        $value = $this->get($key);
        if (!$value) {
            return $default;
        }
        return json_decode($value, true) ?? $default;
    }

    /**
     * Get a setting as boolean
     */
    public function getBool(string $key, bool $default = false): bool
    {
        $value = $this->get($key);
        if ($value === null) {
            return $default;
        }
        return in_array(strtolower($value), ['1', 'true', 'yes', 'on']);
    }

    /**
     * Get setting with full data including translations
     */
    public function getWithTranslations(string $key): ?array
    {
        $setting = $this->first(['key' => $key]);

        if (!$setting) {
            return null;
        }

        if (!empty($setting['translations']) && is_string($setting['translations'])) {
            $setting['translations'] = json_decode($setting['translations'], true);
        }

        return $setting;
    }

    /**
     * Create or update a setting
     */
    public function upsert(string $key, array $data): bool
    {
        $existing = $this->first(['key' => $key]);

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        $data['key'] = $key;
        return $this->create($data) !== false;
    }
}
