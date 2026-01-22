<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * Category Model (Shared across entities)
 * We're Sinapsis CMS
 */
class Category extends Model
{
    protected string $table = 'categories';

    protected array $fillable = [
        'name',
        'slug',
        'description',
        'featured_image',
        'icon_image',
        'parent_id',
        'color',
        'icon',
        'display_order',
        'is_active',
        'meta_title',
        'meta_description',
        'enable_llm_qa',
        'llm_qa_content',
    ];

    /**
     * Get active categories ordered
     */
    public function getActive(): array
    {
        return $this->where(['is_active' => 1], ['display_order' => 'ASC', 'name' => 'ASC']);
    }

    /**
     * Get categories with hierarchical structure
     */
    public function getHierarchical(): array
    {
        $categories = $this->getActive();
        return $this->buildTree($categories);
    }

    /**
     * Build category tree
     */
    private function buildTree(array $categories, ?int $parentId = null): array
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $category['children'] = $this->buildTree($categories, $category['id']);
                $tree[] = $category;
            }
        }
        return $tree;
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
     * Get category with all related content counts
     * Handles missing tables gracefully
     */
    public function getWithContentCounts(int $id): ?array
    {
        $category = $this->find($id);
        if (!$category) return null;

        // Get posts count
        try {
            $sql = "SELECT COUNT(*) as count FROM posts WHERE category_id = ? AND status = 'published'";
            $result = $this->db->fetch($sql, [$id]);
            $category['posts_count'] = (int) ($result['count'] ?? 0);
        } catch (\PDOException $e) {
            $category['posts_count'] = 0;
        }

        // Get services count (table may not exist)
        try {
            $sql = "SELECT COUNT(*) as count FROM services WHERE category_id = ? AND is_active = 1";
            $result = $this->db->fetch($sql, [$id]);
            $category['services_count'] = (int) ($result['count'] ?? 0);
        } catch (\PDOException $e) {
            $category['services_count'] = 0;
        }

        // Get cases count (table may not exist)
        try {
            $sql = "SELECT COUNT(*) as count FROM success_cases WHERE category_id = ? AND status = 'published'";
            $result = $this->db->fetch($sql, [$id]);
            $category['cases_count'] = (int) ($result['count'] ?? 0);
        } catch (\PDOException $e) {
            $category['cases_count'] = 0;
        }

        // Get tools count (table may not exist)
        try {
            $sql = "SELECT COUNT(*) as count FROM tools WHERE category_id = ? AND is_active = 1";
            $result = $this->db->fetch($sql, [$id]);
            $category['tools_count'] = (int) ($result['count'] ?? 0);
        } catch (\PDOException $e) {
            $category['tools_count'] = 0;
        }

        // Get FAQs count (table may not exist)
        try {
            $sql = "SELECT COUNT(*) as count FROM faqs WHERE category_id = ? AND is_active = 1";
            $result = $this->db->fetch($sql, [$id]);
            $category['faqs_count'] = (int) ($result['count'] ?? 0);
        } catch (\PDOException $e) {
            $category['faqs_count'] = 0;
        }

        $category['total_content'] = $category['posts_count'] + $category['services_count'] +
                                     $category['cases_count'] + $category['tools_count'] +
                                     $category['faqs_count'];

        return $category;
    }

    /**
     * Get categories with post count
     */
    public function getWithPostCount(): array
    {
        $sql = "SELECT c.*, COUNT(p.id) as post_count
                FROM `{$this->table}` c
                LEFT JOIN posts p ON p.category_id = c.id AND p.status = 'published'
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.display_order ASC, c.name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get categories with case count
     */
    public function getWithCaseCount(): array
    {
        $sql = "SELECT c.*, COUNT(sc.id) as case_count
                FROM `{$this->table}` c
                LEFT JOIN success_cases sc ON sc.category_id = c.id AND sc.status = 'published'
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.display_order ASC, c.name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get categories with service count
     */
    public function getWithServiceCount(): array
    {
        $sql = "SELECT c.*, COUNT(s.id) as service_count
                FROM `{$this->table}` c
                LEFT JOIN services s ON s.category_id = c.id AND s.is_active = 1
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.display_order ASC, c.name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get categories with tool count
     */
    public function getWithToolCount(): array
    {
        $sql = "SELECT c.*, COUNT(t.id) as tool_count
                FROM `{$this->table}` c
                LEFT JOIN tools t ON t.category_id = c.id AND t.is_active = 1
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.display_order ASC, c.name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all content for a category
     */
    public function getAllContent(int $categoryId): array
    {
        $content = [];

        // Posts
        $sql = "SELECT id, title, slug, thumbnail, excerpt, published_at, 'post' as type
                FROM posts WHERE category_id = ? AND status = 'published'
                ORDER BY published_at DESC LIMIT 10";
        $content['posts'] = $this->db->fetchAll($sql, [$categoryId]);

        // Services
        $sql = "SELECT id, title, slug, image, icon_class as icon, short_description, 'service' as type
                FROM services WHERE category_id = ? AND is_active = 1
                ORDER BY display_order ASC LIMIT 10";
        $content['services'] = $this->db->fetchAll($sql, [$categoryId]);

        // Success Cases
        $sql = "SELECT sc.id, sc.title, sc.slug, sc.featured_image, cl.name as client_name, 'case' as type
                FROM success_cases sc
                LEFT JOIN clients cl ON cl.id = sc.client_id
                WHERE sc.category_id = ? AND sc.status = 'published'
                ORDER BY sc.is_featured DESC, sc.display_order ASC LIMIT 10";
        $content['cases'] = $this->db->fetchAll($sql, [$categoryId]);

        // Tools
        $sql = "SELECT id, title, slug, logo, subtitle, 'tool' as type
                FROM tools WHERE category_id = ? AND is_active = 1
                ORDER BY display_order ASC LIMIT 10";
        $content['tools'] = $this->db->fetchAll($sql, [$categoryId]);

        // FAQs
        $sql = "SELECT id, question, answer, 'faq' as type
                FROM faqs WHERE category_id = ? AND is_active = 1
                ORDER BY display_order ASC LIMIT 10";
        $content['faqs'] = $this->db->fetchAll($sql, [$categoryId]);

        return $content;
    }

    /**
     * Get categories that have content
     */
    public function getWithContent(): array
    {
        $sql = "SELECT c.*,
                       (SELECT COUNT(*) FROM posts WHERE category_id = c.id AND status = 'published') as posts_count,
                       (SELECT COUNT(*) FROM services WHERE category_id = c.id AND is_active = 1) as services_count,
                       (SELECT COUNT(*) FROM success_cases WHERE category_id = c.id AND status = 'published') as cases_count,
                       (SELECT COUNT(*) FROM tools WHERE category_id = c.id AND is_active = 1) as tools_count,
                       (SELECT COUNT(*) FROM faqs WHERE category_id = c.id AND is_active = 1) as faqs_count
                FROM `{$this->table}` c
                WHERE c.is_active = 1
                HAVING posts_count > 0 OR services_count > 0 OR cases_count > 0 OR tools_count > 0 OR faqs_count > 0
                ORDER BY c.display_order ASC, c.name ASC";
        return $this->db->fetchAll($sql);
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
     * Reorder categories
     */
    public function reorder(array $ids): void
    {
        foreach ($ids as $order => $id) {
            $this->update((int) $id, ['display_order' => $order]);
        }
    }

    /**
     * Get child categories
     */
    public function getChildren(int $parentId): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE parent_id = ? AND is_active = 1
                ORDER BY display_order ASC, name ASC";
        return $this->db->fetchAll($sql, [$parentId]);
    }

    /**
     * Get parent category
     */
    public function getParent(int $categoryId): ?array
    {
        $category = $this->find($categoryId);
        if (!$category || !$category['parent_id']) return null;

        return $this->find($category['parent_id']);
    }

    /**
     * Get breadcrumb trail
     */
    public function getBreadcrumbs(int $categoryId): array
    {
        $breadcrumbs = [];
        $category = $this->find($categoryId);

        while ($category) {
            array_unshift($breadcrumbs, $category);
            $category = $category['parent_id'] ? $this->find($category['parent_id']) : null;
        }

        return $breadcrumbs;
    }

    /**
     * Get all categories in hierarchical order (flattened for display)
     * Returns categories with 'depth' field indicating nesting level
     */
    public function getAllHierarchical(): array
    {
        $sql = "SELECT * FROM `{$this->table}` ORDER BY display_order ASC, name ASC";
        $categories = $this->db->fetchAll($sql);
        return $this->flattenTree($categories);
    }

    /**
     * Flatten tree structure with depth info
     */
    private function flattenTree(array $categories, ?int $parentId = null, int $depth = 0): array
    {
        $result = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $category['depth'] = $depth;
                $result[] = $category;
                $children = $this->flattenTree($categories, $category['id'], $depth + 1);
                $result = array_merge($result, $children);
            }
        }
        return $result;
    }
}
