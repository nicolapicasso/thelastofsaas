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
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * Get setting by key
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = $this->first(['key' => $key]);

        if (!$setting) {
            return $default;
        }

        return $this->castValue($setting['value'], $setting['type']);
    }

    /**
     * Set a setting value
     */
    public function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        $existing = $this->first(['key' => $key]);

        $data = [
            'key' => $key,
            'value' => $this->serializeValue($value, $type),
            'type' => $type,
            'group' => $group,
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
        $settings = $this->all(['key' => 'ASC']);
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['key']] = $this->castValue($setting['value'], $setting['type']);
        }

        return $result;
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group): array
    {
        $settings = $this->where(['group' => $group], ['key' => 'ASC']);
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['key']] = [
                'value' => $this->castValue($setting['value'], $setting['type']),
                'type' => $setting['type'],
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
            $existing = $this->first(['key' => $key]);
            if ($existing) {
                $this->update($existing['id'], [
                    'value' => $this->serializeValue($value, $existing['type']),
                ]);
            } else {
                // Create new setting if it doesn't exist
                $this->create([
                    'key' => $key,
                    'value' => $this->serializeValue($value, 'string'),
                    'type' => 'string',
                    'group' => $this->guessGroup($key),
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
