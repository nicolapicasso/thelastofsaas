<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * TeamMember Model
 * Omniwallet CMS
 */
class TeamMember extends Model
{
    protected string $table = 'team_members';

    protected array $fillable = [
        'name',
        'slug',
        'role',
        'bio',
        'photo',
        'photo_hover',
        'email',
        'linkedin',
        'twitter',
        'sort_order',
        'is_active',
    ];

    /**
     * Get all team members (random order)
     */
    public function getAll(int $limit = 100): array
    {
        $sql = "SELECT * FROM `{$this->table}` ORDER BY RAND() LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get all team members ordered by name (for admin)
     */
    public function getAllOrdered(): array
    {
        $sql = "SELECT * FROM `{$this->table}` ORDER BY name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->first(['slug' => $slug]);
    }

    /**
     * Get specific members by IDs
     */
    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM `{$this->table}` WHERE id IN ({$placeholders})";

        $members = $this->db->fetchAll($sql, array_map('intval', $ids));

        // Maintain the order specified in $ids
        $ordered = [];
        $indexed = [];
        foreach ($members as $member) {
            $indexed[$member['id']] = $member;
        }
        foreach ($ids as $id) {
            if (isset($indexed[$id])) {
                $ordered[] = $indexed[$id];
            }
        }

        return $ordered;
    }

    /**
     * Create with auto-generated slug
     */
    public function createWithSlug(array $data): int
    {
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Slug::unique($data['name'], $this->table);
        }

        return $this->create($data);
    }

    /**
     * Update with slug handling
     */
    public function updateWithSlug(int $id, array $data): bool
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Slug::unique($data['name'], $this->table, 'slug', $id);
        }

        return $this->update($id, $data);
    }

    /**
     * Count total team members
     */
    public function countAll(): int
    {
        return $this->count();
    }
}
