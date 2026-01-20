<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * TlosSetting Model
 * TLOS - The Last of SaaS
 */
class TlosSetting extends Model
{
    protected string $table = 'tlos_settings';

    protected array $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'setting_group',
        'description',
    ];

    /**
     * Get setting by key
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = $this->findBy('setting_key', $key);

        if (!$setting) {
            return $default;
        }

        return $this->castValue($setting['setting_value'], $setting['setting_type']);
    }

    /**
     * Set a setting value
     */
    public function set(string $key, mixed $value): bool
    {
        $setting = $this->findBy('setting_key', $key);

        if ($setting) {
            return $this->update($setting['id'], ['setting_value' => (string) $value]);
        }

        return $this->create([
            'setting_key' => $key,
            'setting_value' => (string) $value,
        ]) > 0;
    }

    /**
     * Set a setting value with specific type
     */
    public function setWithType(string $key, mixed $value, string $type, string $group = 'general'): bool
    {
        $setting = $this->findBy('setting_key', $key);

        if ($setting) {
            return $this->update($setting['id'], [
                'setting_value' => (string) $value,
                'setting_type' => $type,
            ]);
        }

        return $this->create([
            'setting_key' => $key,
            'setting_value' => (string) $value,
            'setting_type' => $type,
            'setting_group' => $group,
        ]) > 0;
    }

    /**
     * Get all settings
     */
    public function getAll(): array
    {
        $settings = $this->all(['setting_group' => 'ASC', 'setting_key' => 'ASC']);
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
                'description' => $setting['description'],
            ];
        }

        return $result;
    }

    /**
     * Get settings grouped
     */
    public function getAllGrouped(): array
    {
        $settings = $this->all(['setting_group' => 'ASC', 'setting_key' => 'ASC']);
        $result = [];

        foreach ($settings as $setting) {
            $group = $setting['setting_group'] ?? 'general';
            if (!isset($result[$group])) {
                $result[$group] = [];
            }
            $result[$group][] = $setting;
        }

        return $result;
    }

    /**
     * Bulk update settings
     */
    public function bulkUpdate(array $settings): bool
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }

    /**
     * Cast value to appropriate type
     */
    private function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => in_array(strtolower($value), ['1', 'true', 'yes', 'on']),
            'number' => is_numeric($value) ? (int) $value : 0,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Get available groups
     */
    public static function getGroups(): array
    {
        return [
            'general' => 'General',
            'email' => 'Email',
            'matching' => 'Matching',
            'stripe' => 'Stripe / Pagos',
            'meetings' => 'Reuniones',
        ];
    }

    /**
     * Helper: Get site name
     */
    public function getSiteName(): string
    {
        return $this->get('site_name', 'The Last of SaaS');
    }

    /**
     * Helper: Get admin emails as array
     */
    public function getAdminEmails(): array
    {
        $emails = $this->get('admin_emails', '');
        if (empty($emails)) {
            return [];
        }
        return array_map('trim', explode(',', $emails));
    }

    /**
     * Helper: Check if sponsor notifications are enabled
     */
    public function notifySponsors(): bool
    {
        return $this->get('notify_sponsors', true);
    }

    /**
     * Helper: Check if company notifications are enabled
     */
    public function notifyCompanies(): bool
    {
        return $this->get('notify_companies', true);
    }

    /**
     * Helper: Check if sponsor messages are allowed
     */
    public function allowSponsorMessages(): bool
    {
        return $this->get('allow_sponsor_messages', true);
    }

    /**
     * Helper: Get Stripe public key
     */
    public function getStripePublicKey(): string
    {
        return $this->get('stripe_public_key', $_ENV['STRIPE_PUBLIC_KEY'] ?? '');
    }

    /**
     * Helper: Get Stripe secret key
     */
    public function getStripeSecretKey(): string
    {
        return $this->get('stripe_secret_key', $_ENV['STRIPE_SECRET_KEY'] ?? '');
    }

    /**
     * Helper: Get currency
     */
    public function getCurrency(): string
    {
        return $this->get('currency', 'eur');
    }
}
