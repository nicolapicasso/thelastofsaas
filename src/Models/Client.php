<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * Client Model
 * We're Sinapsis CMS
 */
class Client extends Model
{
    protected string $table = 'clients';

    protected array $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'industry',
        'company_size',
        'location',
        'is_featured',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'enable_llm_qa',
        'llm_qa_content',
    ];

    /**
     * Get all clients for admin (regardless of status)
     */
    public function getAll(int $limit = 100): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                ORDER BY is_featured DESC, sort_order ASC, name ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get all active clients
     */
    public function getActive(int $limit = 100): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE is_active = 1
                ORDER BY is_featured DESC, sort_order ASC, name ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get featured clients
     */
    public function getFeatured(int $limit = 10): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE is_active = 1 AND is_featured = 1
                ORDER BY sort_order ASC, name ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get clients with logos (for logo grid/carousel)
     */
    public function getWithLogos(int $limit = 20): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE is_active = 1 AND logo IS NOT NULL AND logo != ''
                ORDER BY is_featured DESC, sort_order ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE slug = ? AND is_active = 1 LIMIT 1";
        return $this->db->fetch($sql, [$slug]) ?: null;
    }

    /**
     * Get by industry
     */
    public function getByIndustry(string $industry, int $limit = 100): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE is_active = 1 AND industry = ?
                ORDER BY is_featured DESC, sort_order ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$industry, $limit]);
    }

    /**
     * Get unique industries
     */
    public function getIndustries(): array
    {
        $sql = "SELECT DISTINCT industry FROM `{$this->table}`
                WHERE is_active = 1 AND industry IS NOT NULL AND industry != ''
                ORDER BY industry ASC";
        $results = $this->db->fetchAll($sql);
        return array_column($results, 'industry');
    }

    /**
     * Get industries with counts
     */
    public function getIndustriesWithCounts(): array
    {
        $sql = "SELECT industry, COUNT(*) as count
                FROM `{$this->table}`
                WHERE is_active = 1 AND industry IS NOT NULL AND industry != ''
                GROUP BY industry
                ORDER BY industry ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get unique company sizes
     */
    public function getCompanySizes(): array
    {
        $sql = "SELECT DISTINCT company_size FROM `{$this->table}`
                WHERE is_active = 1 AND company_size IS NOT NULL AND company_size != ''
                ORDER BY company_size ASC";
        $results = $this->db->fetchAll($sql);
        return array_column($results, 'company_size');
    }

    /**
     * Get unique locations
     */
    public function getLocations(): array
    {
        $sql = "SELECT DISTINCT location FROM `{$this->table}`
                WHERE is_active = 1 AND location IS NOT NULL AND location != ''
                ORDER BY location ASC";
        $results = $this->db->fetchAll($sql);
        return array_column($results, 'location');
    }

    /**
     * Search clients
     */
    public function search(string $query, ?string $industry = null, ?string $location = null): array
    {
        $params = [];
        $conditions = ["is_active = 1"];

        if (!empty($query)) {
            $conditions[] = "(name LIKE ? OR description LIKE ? OR industry LIKE ? OR location LIKE ?)";
            $searchTerm = '%' . $query . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($industry)) {
            $conditions[] = "industry = ?";
            $params[] = $industry;
        }

        if (!empty($location)) {
            $conditions[] = "location LIKE ?";
            $params[] = '%' . $location . '%';
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT * FROM `{$this->table}`
                WHERE {$where}
                ORDER BY is_featured DESC, sort_order ASC, name ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get client with success cases count
     */
    public function getWithCasesCount(): array
    {
        $sql = "SELECT c.*, COUNT(sc.id) as cases_count
                FROM `{$this->table}` c
                LEFT JOIN success_cases sc ON sc.client_id = c.id AND sc.status = 'published'
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.is_featured DESC, c.sort_order ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get success cases for a client
     */
    public function getSuccessCases(int $clientId): array
    {
        $sql = "SELECT * FROM success_cases
                WHERE client_id = ? AND status = 'published'
                ORDER BY is_featured DESC, sort_order ASC";
        return $this->db->fetchAll($sql, [$clientId]);
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
     * Reorder clients
     */
    public function reorder(array $ids): void
    {
        foreach ($ids as $order => $id) {
            $this->update((int) $id, ['sort_order' => $order]);
        }
    }

    /**
     * Count active clients
     */
    public function countActive(): int
    {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}` WHERE is_active = 1";
        $result = $this->db->fetch($sql);
        return (int) ($result['total'] ?? 0);
    }
}
