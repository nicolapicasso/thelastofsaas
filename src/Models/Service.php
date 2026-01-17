<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * Service Model (formerly Features)
 * We're Sinapsis CMS
 */
class Service extends Model
{
    protected string $table = 'services';

    protected array $fillable = [
        'title',
        'slug',
        'short_description',
        'full_description',
        'icon_class',
        'icon_svg',
        'image',
        'video_url',
        'category_id',
        'sort_order',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'enable_llm_qa',
        'llm_qa_content',
    ];

    /**
     * Get all services for admin (with category info)
     */
    public function getAllForAdmin(?int $categoryId = null): array
    {
        $sql = "SELECT s.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` s
                LEFT JOIN categories c ON c.id = s.category_id";

        $params = [];
        if ($categoryId) {
            $sql .= " WHERE s.category_id = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY s.sort_order ASC, s.title ASC";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get all active services
     */
    public function getActive(int $limit = 100): array
    {
        $sql = "SELECT s.*, c.name as category_name, c.slug as category_slug, c.color as category_color
                FROM `{$this->table}` s
                LEFT JOIN categories c ON c.id = s.category_id
                WHERE s.is_active = 1
                ORDER BY s.is_featured DESC, s.sort_order ASC, s.title ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get featured services
     */
    public function getFeatured(int $limit = 6): array
    {
        $sql = "SELECT s.*, c.name as category_name, c.slug as category_slug, c.color as category_color
                FROM `{$this->table}` s
                LEFT JOIN categories c ON c.id = s.category_id
                WHERE s.is_active = 1 AND s.is_featured = 1
                ORDER BY s.sort_order ASC, s.title ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT s.*, c.name as category_name, c.slug as category_slug, c.color as category_color
                FROM `{$this->table}` s
                LEFT JOIN categories c ON c.id = s.category_id
                WHERE s.slug = ? AND s.is_active = 1
                LIMIT 1";
        return $this->db->fetch($sql, [$slug]) ?: null;
    }

    /**
     * Get by category
     */
    public function getByCategory(int $categoryId, int $limit = 100): array
    {
        $sql = "SELECT s.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` s
                LEFT JOIN categories c ON c.id = s.category_id
                WHERE s.is_active = 1 AND s.category_id = ?
                ORDER BY s.sort_order ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$categoryId, $limit]);
    }

    /**
     * Get by category slug
     */
    public function getByCategorySlug(string $categorySlug, int $limit = 100): array
    {
        $sql = "SELECT s.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` s
                INNER JOIN categories c ON c.id = s.category_id AND c.slug = ?
                WHERE s.is_active = 1
                ORDER BY s.sort_order ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$categorySlug, $limit]);
    }

    /**
     * Get success cases related to this service
     */
    public function getSuccessCases(int $serviceId, int $limit = 10): array
    {
        $sql = "SELECT sc.*, cl.name as client_name, cl.logo as client_logo
                FROM success_cases sc
                INNER JOIN service_cases svc ON svc.case_id = sc.id AND svc.service_id = ?
                LEFT JOIN clients cl ON cl.id = sc.client_id
                WHERE sc.status = 'published'
                ORDER BY svc.sort_order ASC, sc.is_featured DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$serviceId, $limit]);
    }

    /**
     * Get related services (same category or featured)
     */
    public function getRelated(int $serviceId, ?int $categoryId = null, int $limit = 3): array
    {
        if ($categoryId) {
            $sql = "SELECT s.*, c.name as category_name
                    FROM `{$this->table}` s
                    LEFT JOIN categories c ON c.id = s.category_id
                    WHERE s.is_active = 1 AND s.id != ? AND s.category_id = ?
                    ORDER BY s.sort_order ASC
                    LIMIT ?";
            $related = $this->db->fetchAll($sql, [$serviceId, $categoryId, $limit]);
        } else {
            $related = [];
        }

        // If not enough, fill with featured services
        if (count($related) < $limit) {
            $remaining = $limit - count($related);
            $excludeIds = array_merge([$serviceId], array_column($related, 'id'));
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));

            $sql = "SELECT s.*, c.name as category_name
                    FROM `{$this->table}` s
                    LEFT JOIN categories c ON c.id = s.category_id
                    WHERE s.is_active = 1 AND s.id NOT IN ({$placeholders})
                    ORDER BY s.is_featured DESC, s.sort_order ASC
                    LIMIT ?";
            $params = array_merge($excludeIds, [$remaining]);
            $more = $this->db->fetchAll($sql, $params);
            $related = array_merge($related, $more);
        }

        return $related;
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
     * Sync success cases relation
     */
    public function syncCases(int $serviceId, array $caseIds): void
    {
        // Delete existing relations
        $this->db->query("DELETE FROM service_cases WHERE service_id = ?", [$serviceId]);

        // Insert new relations
        foreach ($caseIds as $order => $caseId) {
            $this->db->query(
                "INSERT INTO service_cases (service_id, case_id, sort_order) VALUES (?, ?, ?)",
                [$serviceId, $caseId, $order]
            );
        }
    }

    /**
     * Get case IDs for a service
     */
    public function getCaseIds(int $serviceId): array
    {
        $sql = "SELECT case_id FROM service_cases WHERE service_id = ? ORDER BY sort_order ASC";
        $results = $this->db->fetchAll($sql, [$serviceId]);
        return array_column($results, 'case_id');
    }

    /**
     * Reorder services
     */
    public function reorder(array $ids): void
    {
        foreach ($ids as $order => $id) {
            $this->update((int) $id, ['sort_order' => $order]);
        }
    }

    /**
     * Count active services
     */
    public function countActive(): int
    {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}` WHERE is_active = 1";
        $result = $this->db->fetch($sql);
        return (int) ($result['total'] ?? 0);
    }
}
