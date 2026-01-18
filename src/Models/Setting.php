<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Setting Model
 * Omniwallet CMS
 */
class Setting extends Model
{
    protected string $table = 'settings';

    protected array $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'setting_group',
    ];

    /**
     * Get setting by key
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = $this->first(['setting_key' => $key]);

        if (!$setting) {
            return $default;
        }

        return $this->castValue($setting['setting_value'], $setting['setting_type']);
    }

    /**
     * Set a setting value
     */
    public function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        $existing = $this->first(['setting_key' => $key]);

        $data = [
            'setting_key' => $key,
            'setting_value' => $this->serializeValue($value, $type),
            'setting_type' => $type,
            'setting_group' => $group,
        ];

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->create($data);
        }
    }

    /**
     * Get all settings
     */
    public function getAll(): array
    {
        $settings = $this->all(['setting_key' => 'ASC']);
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $this->castValue($setting['setting_value'], $setting['setting_type']);
        }

        return $result;
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group): array
    {
        $settings = $this->where(['setting_group' => $group], ['setting_key' => 'ASC']);
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = [
                'value' => $this->castValue($setting['setting_value'], $setting['setting_type']),
                'type' => $setting['setting_type'],
            ];
        }

        return $result;
    }

    /**
     * Update multiple settings at once
     */
    public function updateBatch(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $existing = $this->first(['setting_key' => $key]);
            if ($existing) {
                $this->update($existing['id'], [
                    'setting_value' => $this->serializeValue($value, $existing['setting_type']),
                ]);
            } else {
                // Create new setting if it doesn't exist
                $this->create([
                    'setting_key' => $key,
                    'setting_value' => $this->serializeValue($value, 'string'),
                    'setting_type' => 'string',
                    'setting_group' => $this->guessGroup($key),
                ]);
            }
        }
    }

    /**
     * Guess the group based on the key prefix
     */
    private function guessGroup(string $key): string
    {
        if (str_starts_with($key, 'footer_')) {
            return 'footer';
        }
        if (str_starts_with($key, 'site_')) {
            return 'general';
        }
        if (str_starts_with($key, 'google_') || str_starts_with($key, 'analytics_')) {
            return 'analytics';
        }
        if (str_contains($key, 'color')) {
            return 'branding';
        }
        if (str_contains($key, 'language') || str_contains($key, 'locale')) {
            return 'localization';
        }
        if (str_contains($key, 'api_key') || str_contains($key, 'openai')) {
            return 'integrations';
        }
        return 'general';
    }

    /**
     * Cast value based on type
     */
    private function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'number' => (int) $value,
            'boolean' => $value === '1' || $value === 'true',
            'json' => json_decode($value, true) ?? [],
            default => $value,
        };
    }

    /**
     * Serialize value for storage
     */
    private function serializeValue(mixed $value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE),
            default => (string) $value,
        };
    }
}
