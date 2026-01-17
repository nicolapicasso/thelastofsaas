<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * Landing Theme Model
 * Manages landing page themes/categories
 * Omniwallet CMS
 */
class LandingTheme extends Model
{
    protected string $table = 'landing_themes';

    protected array $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'image',
        'icon',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    /**
     * Get all active themes
     */
    public function getActive(int $limit = 100): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE is_active = 1 ORDER BY sort_order ASC, title ASC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get theme with landing count
     */
    public function getActiveWithCount(): array
    {
        $sql = "SELECT t.*, COUNT(l.id) as landing_count
                FROM `{$this->table}` t
                LEFT JOIN landings l ON l.theme_id = t.id AND l.is_active = 1
                WHERE t.is_active = 1
                GROUP BY t.id
                ORDER BY t.sort_order ASC, t.title ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->first(['slug' => $slug, 'is_active' => 1]);
    }

    /**
     * Find by slug (admin - including inactive)
     */
    public function findBySlugAdmin(string $slug): ?array
    {
        return $this->first(['slug' => $slug]);
    }

    /**
     * Create with auto-generated slug
     */
    public function createWithSlug(array $data): int
    {
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Slug::unique($data['title'], $this->table);
        }

        return $this->create($data);
    }

    /**
     * Update with slug handling
     */
    public function updateWithSlug(int $id, array $data): bool
    {
        if (isset($data['title']) && empty($data['slug'])) {
            $data['slug'] = Slug::unique($data['title'], $this->table, 'slug', $id);
        }

        return $this->update($id, $data);
    }

    /**
     * Reorder themes
     */
    public function reorder(array $ids): void
    {
        foreach ($ids as $order => $id) {
            $this->update((int) $id, ['sort_order' => $order]);
        }
    }

    /**
     * Get for select dropdown
     */
    public function getForSelect(): array
    {
        $themes = $this->getActive();
        $result = [];
        foreach ($themes as $theme) {
            $result[$theme['id']] = $theme['title'];
        }
        return $result;
    }

    /**
     * Get all for admin (including inactive)
     */
    public function getAllForAdmin(): array
    {
        $sql = "SELECT t.*, COUNT(l.id) as landing_count
                FROM `{$this->table}` t
                LEFT JOIN landings l ON l.theme_id = t.id
                GROUP BY t.id
                ORDER BY t.sort_order ASC, t.title ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get themes by array of IDs
     */
    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM `{$this->table}`
                WHERE id IN ({$placeholders}) AND is_active = 1
                ORDER BY FIELD(id, {$placeholders})";

        // Pass IDs twice: once for IN clause, once for FIELD order
        $params = array_merge($ids, $ids);
        return $this->db->fetchAll($sql, $params);
    }
}
