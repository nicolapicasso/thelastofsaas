<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * Success Case Model
 * We're Sinapsis CMS
 */
class SuccessCase extends Model
{
    protected string $table = 'success_cases';

    protected array $fillable = [
        'title',
        'slug',
        'client_id',
        'category_id',
        'featured_image',
        'video_url',
        'gallery',
        'gallery_display',
        'challenge',
        'solution',
        'results',
        'metrics',
        'testimonial',
        'testimonial_author',
        'testimonial_role',
        'is_featured',
        'status',
        'sort_order',
        'meta_title',
        'meta_description',
        'published_at',
        'enable_llm_qa',
        'llm_qa_content',
    ];

    /**
     * Get all cases for admin with client info
     */
    public function getAllForAdmin(?int $categoryId = null, ?int $clientId = null): array
    {
        $sql = "SELECT sc.*,
                       cl.name as client_name, cl.logo as client_logo, cl.slug as client_slug,
                       cl.industry as client_industry,
                       cat.name as category_name, cat.slug as category_slug
                FROM `{$this->table}` sc
                LEFT JOIN clients cl ON cl.id = sc.client_id
                LEFT JOIN categories cat ON cat.id = sc.category_id";

        $params = [];
        $conditions = [];

        if ($categoryId) {
            $conditions[] = "sc.category_id = ?";
            $params[] = $categoryId;
        }

        if ($clientId) {
            $conditions[] = "sc.client_id = ?";
            $params[] = $clientId;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY sc.sort_order ASC, sc.created_at DESC";

        $cases = $this->db->fetchAll($sql, $params);
        return array_map([$this, 'decodeJsonFields'], $cases);
    }

    /**
     * Get unique industries from clients
     */
    public function getIndustries(): array
    {
        $sql = "SELECT DISTINCT cl.industry
                FROM `{$this->table}` sc
                INNER JOIN clients cl ON cl.id = sc.client_id
                WHERE cl.industry IS NOT NULL AND cl.industry != ''
                ORDER BY cl.industry";
        $results = $this->db->fetchAll($sql);
        return array_column($results, 'industry');
    }

    /**
     * Get industries with case count
     */
    public function getIndustriesWithCaseCount(): array
    {
        $sql = "SELECT cl.industry, COUNT(sc.id) as case_count
                FROM `{$this->table}` sc
                INNER JOIN clients cl ON cl.id = sc.client_id
                WHERE sc.status = 'published' AND cl.industry IS NOT NULL AND cl.industry != ''
                GROUP BY cl.industry
                ORDER BY cl.industry";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get clients with case count
     */
    public function getClientsWithCaseCount(): array
    {
        $sql = "SELECT cl.id, cl.name, cl.slug, COUNT(sc.id) as case_count
                FROM `{$this->table}` sc
                INNER JOIN clients cl ON cl.id = sc.client_id
                WHERE sc.status = 'published'
                GROUP BY cl.id, cl.name, cl.slug
                ORDER BY cl.name";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get active (published) success cases
     */
    public function getActive(int $limit = 20): array
    {
        $sql = "SELECT sc.*,
                       cl.name as client_name, cl.logo as client_logo, cl.slug as client_slug,
                       cl.industry as client_industry, cl.location as client_location,
                       cat.name as category_name, cat.slug as category_slug, cat.color as category_color
                FROM `{$this->table}` sc
                LEFT JOIN clients cl ON cl.id = sc.client_id
                LEFT JOIN categories cat ON cat.id = sc.category_id
                WHERE sc.status = 'published'
                ORDER BY sc.is_featured DESC, sc.sort_order ASC, sc.published_at DESC
                LIMIT ?";
        $cases = $this->db->fetchAll($sql, [$limit]);
        return array_map([$this, 'decodeJsonFields'], $cases);
    }

    /**
     * Get featured success cases
     */
    public function getFeatured(int $limit = 4): array
    {
        $sql = "SELECT sc.*,
                       cl.name as client_name, cl.logo as client_logo, cl.slug as client_slug,
                       cat.name as category_name, cat.slug as category_slug, cat.color as category_color
                FROM `{$this->table}` sc
                LEFT JOIN clients cl ON cl.id = sc.client_id
                LEFT JOIN categories cat ON cat.id = sc.category_id
                WHERE sc.status = 'published' AND sc.is_featured = 1
                ORDER BY sc.sort_order ASC, sc.published_at DESC
                LIMIT ?";
        $cases = $this->db->fetchAll($sql, [$limit]);
        return array_map([$this, 'decodeJsonFields'], $cases);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT sc.*,
                       cl.name as client_name, cl.logo as client_logo, cl.slug as client_slug,
                       cl.industry as client_industry, cl.location as client_location,
                       cl.website as client_website, cl.description as client_description,
                       cat.name as category_name, cat.slug as category_slug, cat.color as category_color
                FROM `{$this->table}` sc
                LEFT JOIN clients cl ON cl.id = sc.client_id
                LEFT JOIN categories cat ON cat.id = sc.category_id
                WHERE sc.slug = ? AND sc.status = 'published'
                LIMIT 1";
        $case = $this->db->fetch($sql, [$slug]);
        return $case ? $this->decodeJsonFields($case) : null;
    }

    /**
     * Find by ID with relations
     */
    public function findWithRelations(int $id): ?array
    {
        $sql = "SELECT sc.*,
                       cl.name as client_name, cl.logo as client_logo, cl.slug as client_slug,
                       cat.name as category_name, cat.slug as category_slug
                FROM `{$this->table}` sc
                LEFT JOIN clients cl ON cl.id = sc.client_id
                LEFT JOIN categories cat ON cat.id = sc.category_id
                WHERE sc.id = ?
                LIMIT 1";
        $case = $this->db->fetch($sql, [$id]);
        return $case ? $this->decodeJsonFields($case) : null;
    }

    /**
     * Get cases by client
     */
    public function getByClient(int $clientId, int $limit = 100): array
    {
        $sql = "SELECT sc.*, cat.name as category_name, cat.slug as category_slug
                FROM `{$this->table}` sc
                LEFT JOIN categories cat ON cat.id = sc.category_id
                WHERE sc.client_id = ? AND sc.status = 'published'
                ORDER BY sc.is_featured DESC, sc.sort_order ASC
                LIMIT ?";
        $cases = $this->db->fetchAll($sql, [$clientId, $limit]);
        return array_map([$this, 'decodeJsonFields'], $cases);
    }

    /**
     * Get cases by category
     */
    public function getByCategory(int $categoryId, int $limit = 100): array
    {
        $sql = "SELECT sc.*,
                       cl.name as client_name, cl.logo as client_logo, cl.slug as client_slug,
                       cat.name as category_name, cat.slug as category_slug
                FROM `{$this->table}` sc
                LEFT JOIN clients cl ON cl.id = sc.client_id
                LEFT JOIN categories cat ON cat.id = sc.category_id
                WHERE sc.category_id = ? AND sc.status = 'published'
                ORDER BY sc.is_featured DESC, sc.sort_order ASC
                LIMIT ?";
        $cases = $this->db->fetchAll($sql, [$categoryId, $limit]);
        return array_map([$this, 'decodeJsonFields'], $cases);
    }

    /**
     * Get cases by category slug
     */
    public function getByCategorySlug(string $categorySlug, int $limit = 100): array
    {
        $sql = "SELECT sc.*,
                       cl.name as client_name, cl.logo as client_logo, cl.slug as client_slug,
                       cat.name as category_name, cat.slug as category_slug
                FROM `{$this->table}` sc
                LEFT JOIN clients cl ON cl.id = sc.client_id
                INNER JOIN categories cat ON cat.id = sc.category_id AND cat.slug = ?
                WHERE sc.status = 'published'
                ORDER BY sc.is_featured DESC, sc.sort_order ASC
                LIMIT ?";
        $cases = $this->db->fetchAll($sql, [$categorySlug, $limit]);
        return array_map([$this, 'decodeJsonFields'], $cases);
    }

    /**
     * Get other cases from the same client
     */
    public function getOtherClientCases(int $caseId, int $clientId, int $limit = 3): array
    {
        $sql = "SELECT sc.*, cat.name as category_name, cat.slug as category_slug
                FROM `{$this->table}` sc
                LEFT JOIN categories cat ON cat.id = sc.category_id
                WHERE sc.client_id = ? AND sc.id != ? AND sc.status = 'published'
                ORDER BY sc.is_featured DESC, sc.sort_order ASC
                LIMIT ?";
        $cases = $this->db->fetchAll($sql, [$clientId, $caseId, $limit]);
        return array_map([$this, 'decodeJsonFields'], $cases);
    }

    /**
     * Get related cases (same category or service)
     */
    public function getRelated(int $caseId, ?int $categoryId = null, int $limit = 3): array
    {
        $related = [];

        // First try to get cases from same category
        if ($categoryId) {
            $sql = "SELECT sc.*,
                           cl.name as client_name, cl.logo as client_logo,
                           cat.name as category_name, cat.slug as category_slug
                    FROM `{$this->table}` sc
                    LEFT JOIN clients cl ON cl.id = sc.client_id
                    LEFT JOIN categories cat ON cat.id = sc.category_id
                    WHERE sc.category_id = ? AND sc.id != ? AND sc.status = 'published'
                    ORDER BY sc.is_featured DESC, sc.sort_order ASC
                    LIMIT ?";
            $related = $this->db->fetchAll($sql, [$categoryId, $caseId, $limit]);
        }

        // If not enough, get cases with same services
        if (count($related) < $limit) {
            $remaining = $limit - count($related);
            $excludeIds = array_merge([$caseId], array_column($related, 'id'));
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));

            $sql = "SELECT DISTINCT sc.*,
                           cl.name as client_name, cl.logo as client_logo,
                           cat.name as category_name, cat.slug as category_slug
                    FROM `{$this->table}` sc
                    INNER JOIN service_cases svc ON svc.case_id = sc.id
                    INNER JOIN service_cases svc2 ON svc2.service_id = svc.service_id AND svc2.case_id = ?
                    LEFT JOIN clients cl ON cl.id = sc.client_id
                    LEFT JOIN categories cat ON cat.id = sc.category_id
                    WHERE sc.id NOT IN ({$placeholders}) AND sc.status = 'published'
                    ORDER BY sc.is_featured DESC
                    LIMIT ?";
            $params = array_merge([$caseId], $excludeIds, [$remaining]);
            $more = $this->db->fetchAll($sql, $params);
            $related = array_merge($related, $more);
        }

        return array_map([$this, 'decodeJsonFields'], $related);
    }

    /**
     * Search cases
     */
    public function search(string $query, ?int $categoryId = null, ?string $industry = null, ?int $clientId = null): array
    {
        $params = [];
        $conditions = ["sc.status = 'published'"];

        if (!empty($query)) {
            $conditions[] = "(sc.title LIKE ? OR sc.challenge LIKE ? OR sc.solution LIKE ? OR cl.name LIKE ?)";
            $searchTerm = '%' . $query . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($categoryId)) {
            $conditions[] = "sc.category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($industry)) {
            $conditions[] = "cl.industry = ?";
            $params[] = $industry;
        }

        if (!empty($clientId)) {
            $conditions[] = "sc.client_id = ?";
            $params[] = $clientId;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT sc.*,
                       cl.name as client_name, cl.logo as client_logo, cl.slug as client_slug,
                       cl.industry as client_industry,
                       cat.name as category_name, cat.slug as category_slug
                FROM `{$this->table}` sc
                LEFT JOIN clients cl ON cl.id = sc.client_id
                LEFT JOIN categories cat ON cat.id = sc.category_id
                WHERE {$where}
                ORDER BY sc.is_featured DESC, sc.sort_order ASC";

        $cases = $this->db->fetchAll($sql, $params);
        return array_map([$this, 'decodeJsonFields'], $cases);
    }

    /**
     * Get services related to this case
     */
    public function getServices(int $caseId): array
    {
        $sql = "SELECT s.*, svc.sort_order as relation_order
                FROM services s
                INNER JOIN service_cases svc ON svc.service_id = s.id AND svc.case_id = ?
                WHERE s.is_active = 1
                ORDER BY svc.sort_order ASC";
        return $this->db->fetchAll($sql, [$caseId]);
    }

    /**
     * Get tools used in this case
     */
    public function getTools(int $caseId): array
    {
        $sql = "SELECT t.*, ct.sort_order as relation_order
                FROM tools t
                INNER JOIN case_tools ct ON ct.tool_id = t.id AND ct.case_id = ?
                WHERE t.is_active = 1
                ORDER BY ct.sort_order ASC";
        return $this->db->fetchAll($sql, [$caseId]);
    }

    /**
     * Get service IDs for a case
     */
    public function getServiceIds(int $caseId): array
    {
        $sql = "SELECT service_id FROM service_cases WHERE case_id = ? ORDER BY sort_order ASC";
        $results = $this->db->fetchAll($sql, [$caseId]);
        return array_column($results, 'service_id');
    }

    /**
     * Get tool IDs for a case
     */
    public function getToolIds(int $caseId): array
    {
        $sql = "SELECT tool_id FROM case_tools WHERE case_id = ? ORDER BY sort_order ASC";
        $results = $this->db->fetchAll($sql, [$caseId]);
        return array_column($results, 'tool_id');
    }

    /**
     * Create with auto-slug
     */
    public function createCase(array $data): int
    {
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Slug::unique($data['title'], $this->table);
        }

        // Handle JSON fields
        $data = $this->encodeJsonFields($data);

        return $this->create($data);
    }

    /**
     * Update case
     */
    public function updateCase(int $id, array $data): bool
    {
        if (isset($data['title']) && empty($data['slug'])) {
            $data['slug'] = Slug::unique($data['title'], $this->table, 'slug', $id);
        }

        // Handle JSON fields
        $data = $this->encodeJsonFields($data);

        return $this->update($id, $data);
    }

    /**
     * Sync services relation
     */
    public function syncServices(int $caseId, array $serviceIds): void
    {
        $this->db->query("DELETE FROM service_cases WHERE case_id = ?", [$caseId]);

        foreach ($serviceIds as $order => $serviceId) {
            $this->db->query(
                "INSERT INTO service_cases (service_id, case_id, sort_order) VALUES (?, ?, ?)",
                [$serviceId, $caseId, $order]
            );
        }
    }

    /**
     * Sync tools relation
     */
    public function syncTools(int $caseId, array $toolIds): void
    {
        $this->db->query("DELETE FROM case_tools WHERE case_id = ?", [$caseId]);

        foreach ($toolIds as $order => $toolId) {
            $this->db->query(
                "INSERT INTO case_tools (case_id, tool_id, sort_order) VALUES (?, ?, ?)",
                [$caseId, $toolId, $order]
            );
        }
    }

    /**
     * Count published cases
     */
    public function countPublished(): int
    {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}` WHERE status = 'published'";
        $result = $this->db->fetch($sql);
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Decode JSON fields
     */
    private function decodeJsonFields(array $case): array
    {
        if (!empty($case['metrics'])) {
            $case['metrics_array'] = json_decode($case['metrics'], true) ?? [];
        } else {
            $case['metrics_array'] = [];
        }

        if (!empty($case['gallery'])) {
            $case['gallery_array'] = json_decode($case['gallery'], true) ?? [];
        } else {
            $case['gallery_array'] = [];
        }

        return $case;
    }

    /**
     * Encode JSON fields
     */
    private function encodeJsonFields(array $data): array
    {
        if (isset($data['metrics']) && is_array($data['metrics'])) {
            $data['metrics'] = json_encode($data['metrics']);
        }

        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $data['gallery'] = json_encode($data['gallery']);
        }

        return $data;
    }
}
