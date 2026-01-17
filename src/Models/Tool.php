<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * Tool Model (formerly Integrations)
 * We're Sinapsis CMS
 */
class Tool extends Model
{
    protected string $table = 'tools';

    protected array $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'logo',
        'platform_url',
        'video_url',
        'gallery',
        'category_id',
        'is_featured',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'enable_llm_qa',
        'llm_qa_content',
    ];

    /**
     * Get all active tools
     */
    public function getActive(int $limit = 100): array
    {
        $sql = "SELECT t.*, c.name as category_name, c.slug as category_slug, c.color as category_color
                FROM `{$this->table}` t
                LEFT JOIN categories c ON c.id = t.category_id
                WHERE t.is_active = 1
                ORDER BY t.is_featured DESC, t.sort_order ASC, t.title ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get featured tools
     */
    public function getFeatured(int $limit = 8): array
    {
        $sql = "SELECT t.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` t
                LEFT JOIN categories c ON c.id = t.category_id
                WHERE t.is_active = 1 AND t.is_featured = 1
                ORDER BY t.sort_order ASC, t.title ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT t.*, c.name as category_name, c.slug as category_slug, c.color as category_color
                FROM `{$this->table}` t
                LEFT JOIN categories c ON c.id = t.category_id
                WHERE t.slug = ? AND t.is_active = 1
                LIMIT 1";
        $tool = $this->db->fetch($sql, [$slug]);

        if ($tool && !empty($tool['gallery'])) {
            $tool['gallery_array'] = json_decode($tool['gallery'], true) ?? [];
        }

        return $tool ?: null;
    }

    /**
     * Find with gallery decoded
     */
    public function findWithGallery(int $id): ?array
    {
        $tool = $this->find($id);

        if ($tool && !empty($tool['gallery'])) {
            $tool['gallery_array'] = json_decode($tool['gallery'], true) ?? [];
        }

        return $tool;
    }

    /**
     * Get by category
     */
    public function getByCategory(int $categoryId, int $limit = 100): array
    {
        $sql = "SELECT t.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` t
                LEFT JOIN categories c ON c.id = t.category_id
                WHERE t.is_active = 1 AND t.category_id = ?
                ORDER BY t.sort_order ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$categoryId, $limit]);
    }

    /**
     * Get by category slug
     */
    public function getByCategorySlug(string $categorySlug, int $limit = 100): array
    {
        $sql = "SELECT t.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` t
                INNER JOIN categories c ON c.id = t.category_id AND c.slug = ?
                WHERE t.is_active = 1
                ORDER BY t.sort_order ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$categorySlug, $limit]);
    }

    /**
     * Get tools grouped by category
     */
    public function getGroupedByCategory(): array
    {
        $tools = $this->getActive();
        $grouped = [];

        foreach ($tools as $tool) {
            $categoryName = $tool['category_name'] ?? 'Otros';
            $grouped[$categoryName][] = $tool;
        }

        return $grouped;
    }

    /**
     * Get success cases that use this tool
     */
    public function getSuccessCases(int $toolId, int $limit = 10): array
    {
        $sql = "SELECT sc.*, cl.name as client_name, cl.logo as client_logo
                FROM success_cases sc
                INNER JOIN case_tools ct ON ct.case_id = sc.id AND ct.tool_id = ?
                LEFT JOIN clients cl ON cl.id = sc.client_id
                WHERE sc.status = 'published'
                ORDER BY ct.sort_order ASC, sc.is_featured DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$toolId, $limit]);
    }

    /**
     * Get tool IDs for a success case
     */
    public function getToolIdsForCase(int $caseId): array
    {
        $sql = "SELECT tool_id FROM case_tools WHERE case_id = ? ORDER BY sort_order ASC";
        $results = $this->db->fetchAll($sql, [$caseId]);
        return array_column($results, 'tool_id');
    }

    /**
     * Get tools for a success case
     */
    public function getForCase(int $caseId): array
    {
        $sql = "SELECT t.*, ct.sort_order as relation_order
                FROM `{$this->table}` t
                INNER JOIN case_tools ct ON ct.tool_id = t.id AND ct.case_id = ?
                WHERE t.is_active = 1
                ORDER BY ct.sort_order ASC";
        return $this->db->fetchAll($sql, [$caseId]);
    }

    /**
     * Create with auto-generated slug
     */
    public function createWithSlug(array $data): int
    {
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Slug::unique($data['title'], $this->table);
        }

        // Handle gallery JSON
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $data['gallery'] = json_encode($data['gallery']);
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

        // Handle gallery JSON
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $data['gallery'] = json_encode($data['gallery']);
        }

        return $this->update($id, $data);
    }

    /**
     * Reorder tools
     */
    public function reorder(array $ids): void
    {
        foreach ($ids as $order => $id) {
            $this->update((int) $id, ['sort_order' => $order]);
        }
    }

    /**
     * Count active tools
     */
    public function countActive(): int
    {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}` WHERE is_active = 1";
        $result = $this->db->fetch($sql);
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Get published (active) tools - alias for getActive for block compatibility
     */
    public function getPublished(int $limit = 100): array
    {
        return $this->getActive($limit);
    }

    /**
     * Get all categories that have active tools
     */
    public function getCategoriesWithTools(): array
    {
        $sql = "SELECT DISTINCT c.id, c.name, c.slug, c.color,
                       (SELECT COUNT(*) FROM tools t WHERE t.category_id = c.id AND t.is_active = 1) as tool_count
                FROM categories c
                INNER JOIN tools t ON t.category_id = c.id AND t.is_active = 1
                WHERE c.is_active = 1
                ORDER BY c.name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all tools for admin (including inactive)
     */
    public function getAllForAdmin(?int $categoryId = null): array
    {
        $sql = "SELECT t.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` t
                LEFT JOIN categories c ON c.id = t.category_id";

        $params = [];

        if ($categoryId) {
            $sql .= " WHERE t.category_id = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY t.is_featured DESC, t.sort_order ASC, t.title ASC";

        return $this->db->fetchAll($sql, $params);
    }
}
