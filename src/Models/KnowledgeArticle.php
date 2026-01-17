<?php
/**
 * Knowledge Article Model
 * Omniwallet CMS
 */

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

class KnowledgeArticle extends Model
{
    protected string $table = 'knowledge_articles';
    private ?bool $hasNewColumns = null;

    /**
     * Check if database has the new column structure
     */
    private function hasNewSchema(): bool
    {
        if ($this->hasNewColumns === null) {
            try {
                $stmt = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'category_id'");
                $this->hasNewColumns = $stmt->fetch() !== false;
            } catch (\Exception $e) {
                $this->hasNewColumns = false;
            }
        }
        return $this->hasNewColumns;
    }

    /**
     * Map form data to database columns
     */
    private function mapToDbColumns(array $data): array
    {
        if (!$this->hasNewSchema()) {
            // Map new field names to old schema
            if (isset($data['category_id'])) {
                $data['topic_id'] = $data['category_id'];
                unset($data['category_id']);
            }
            if (isset($data['tags'])) {
                $data['search_keywords'] = $data['tags'];
                unset($data['tags']);
            }
            // Remove columns that don't exist in old schema
            unset($data['sort_order']);
            unset($data['view_count']);
        }
        return $data;
    }

    /**
     * Get the category join clause based on schema
     */
    private function getCategoryJoin(): string
    {
        if ($this->hasNewSchema()) {
            return "LEFT JOIN categories c ON ka.category_id = c.id";
        }
        return "LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id LEFT JOIN categories c ON c.id = NULL";
    }

    /**
     * Get the category ID column name
     */
    private function getCategoryColumn(): string
    {
        return $this->hasNewSchema() ? 'category_id' : 'topic_id';
    }

    /**
     * Get order by clause
     */
    private function getOrderClause(): string
    {
        if ($this->hasNewSchema()) {
            return "ka.sort_order ASC, ka.created_at DESC";
        }
        return "ka.created_at DESC";
    }

    /**
     * Get published articles
     */
    public function getPublished(?int $categoryId = null, int $limit = 20): array
    {
        $catCol = $this->getCategoryColumn();
        $orderClause = $this->getOrderClause();

        if ($this->hasNewSchema()) {
            $sql = "SELECT ka.*, c.name as category_name, c.slug as category_slug
                    FROM {$this->table} ka
                    LEFT JOIN categories c ON ka.category_id = c.id
                    WHERE ka.status = 'published'";
        } else {
            $sql = "SELECT ka.*, kt.name as category_name, kt.slug as category_slug
                    FROM {$this->table} ka
                    LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                    WHERE ka.status = 'published'";
        }
        $params = [];

        if ($categoryId) {
            $sql .= " AND ka.{$catCol} = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY {$orderClause} LIMIT ?";
        $params[] = $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get featured articles
     */
    public function getFeatured(int $limit = 5): array
    {
        $orderClause = $this->getOrderClause();

        if ($this->hasNewSchema()) {
            $sql = "SELECT ka.*, c.name as category_name
                    FROM {$this->table} ka
                    LEFT JOIN categories c ON ka.category_id = c.id
                    WHERE ka.status = 'published' AND ka.is_featured = 1
                    ORDER BY {$orderClause}
                    LIMIT ?";
        } else {
            $sql = "SELECT ka.*, kt.name as category_name
                    FROM {$this->table} ka
                    LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                    WHERE ka.status = 'published' AND ka.is_featured = 1
                    ORDER BY ka.created_at DESC
                    LIMIT ?";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get by slug
     */
    public function findBySlug(string $slug): ?array
    {
        if ($this->hasNewSchema()) {
            $sql = "SELECT ka.*, c.name as category_name, c.slug as category_slug
                    FROM {$this->table} ka
                    LEFT JOIN categories c ON ka.category_id = c.id
                    WHERE ka.slug = ? AND ka.status = 'published'";
        } else {
            $sql = "SELECT ka.*, kt.name as category_name, kt.slug as category_slug
                    FROM {$this->table} ka
                    LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                    WHERE ka.slug = ? AND ka.status = 'published'";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Search articles
     */
    public function search(string $query, int $limit = 20): array
    {
        $searchTerm = '%' . $query . '%';
        $orderClause = $this->getOrderClause();

        if ($this->hasNewSchema()) {
            // New schema with tags column
            $sql = "SELECT ka.*, c.name as category_name
                    FROM {$this->table} ka
                    LEFT JOIN categories c ON ka.category_id = c.id
                    WHERE ka.status = 'published'
                      AND (ka.title LIKE ? OR ka.content LIKE ? OR ka.tags LIKE ?)
                    ORDER BY ka.created_at DESC
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit]);
        } else {
            // Old schema with search_keywords
            $sql = "SELECT ka.*, kt.name as category_name
                    FROM {$this->table} ka
                    LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                    WHERE ka.status = 'published'
                      AND (ka.title LIKE ? OR ka.content LIKE ? OR ka.search_keywords LIKE ?)
                    ORDER BY ka.created_at DESC
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit]);
        }
        return $stmt->fetchAll();
    }

    /**
     * Get related articles
     */
    public function getRelated(int $articleId, ?int $categoryId = null, int $limit = 5): array
    {
        $catCol = $this->getCategoryColumn();
        $orderClause = $this->getOrderClause();

        if ($this->hasNewSchema()) {
            $sql = "SELECT ka.*, c.name as category_name
                    FROM {$this->table} ka
                    LEFT JOIN categories c ON ka.category_id = c.id
                    WHERE ka.status = 'published' AND ka.id != ?";
        } else {
            $sql = "SELECT ka.*, kt.name as category_name
                    FROM {$this->table} ka
                    LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                    WHERE ka.status = 'published' AND ka.id != ?";
        }
        $params = [$articleId];

        if ($categoryId) {
            $sql .= " AND ka.{$catCol} = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY {$orderClause} LIMIT ?";
        $params[] = $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Increment view count
     */
    public function incrementViews(int $id): bool
    {
        $viewCol = $this->hasNewSchema() ? 'view_count' : 'views';
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$viewCol} = {$viewCol} + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get popular articles
     */
    public function getPopular(int $limit = 10): array
    {
        $viewCol = $this->hasNewSchema() ? 'view_count' : 'views';

        if ($this->hasNewSchema()) {
            $sql = "SELECT ka.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
                    FROM {$this->table} ka
                    LEFT JOIN categories c ON ka.category_id = c.id
                    WHERE ka.status = 'published'
                    ORDER BY ka.{$viewCol} DESC
                    LIMIT ?";
        } else {
            $sql = "SELECT ka.*, kt.name as category_name, kt.slug as category_slug, kt.icon as category_icon
                    FROM {$this->table} ka
                    LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                    WHERE ka.status = 'published'
                    ORDER BY ka.{$viewCol} DESC
                    LIMIT ?";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Create with auto-slug
     */
    public function createArticle(array $data): int
    {
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Slug::generate($data['title'], $this->table);
        }

        // Map to database columns
        $data = $this->mapToDbColumns($data);

        return parent::create($data);
    }

    /**
     * Update with auto-slug
     */
    public function update(int $id, array $data): bool
    {
        if (isset($data['slug']) && empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Slug::generate($data['title'], $this->table, $id);
        }

        // Map to database columns
        $data = $this->mapToDbColumns($data);

        return parent::update($id, $data);
    }

    /**
     * Get all with category info
     */
    public function allWithCategory(?string $status = null, ?int $categoryId = null): array
    {
        $catCol = $this->getCategoryColumn();

        if ($this->hasNewSchema()) {
            $sql = "SELECT ka.*, c.name as category_name
                    FROM {$this->table} ka
                    LEFT JOIN categories c ON ka.category_id = c.id
                    WHERE 1=1";
        } else {
            $sql = "SELECT ka.*, kt.name as category_name
                    FROM {$this->table} ka
                    LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                    WHERE 1=1";
        }
        $params = [];

        if ($status) {
            $sql .= " AND ka.status = ?";
            $params[] = $status;
        }

        if ($categoryId) {
            $sql .= " AND ka.{$catCol} = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY ka.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get articles grouped by category for navigation
     */
    public function getGroupedByCategory(): array
    {
        if ($this->hasNewSchema()) {
            $stmt = $this->db->query("
                SELECT ka.id, ka.title, ka.slug, ka.sort_order as article_sort,
                       c.id as category_id, c.name as category_name, c.slug as category_slug, c.icon as category_icon, c.sort_order as cat_sort
                FROM {$this->table} ka
                LEFT JOIN categories c ON ka.category_id = c.id
                WHERE ka.status = 'published'
                ORDER BY c.sort_order ASC, ka.sort_order ASC, ka.title ASC
            ");
        } else {
            $stmt = $this->db->query("
                SELECT ka.id, ka.title, ka.slug, 0 as article_sort,
                       kt.id as category_id, kt.name as category_name, kt.slug as category_slug, kt.icon as category_icon, kt.sort_order as cat_sort
                FROM {$this->table} ka
                LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                WHERE ka.status = 'published'
                ORDER BY kt.sort_order ASC, ka.title ASC
            ");
        }

        $articles = $stmt->fetchAll();
        $grouped = [];

        foreach ($articles as $article) {
            $categoryId = $article['category_id'] ?? 0;
            $categoryName = $article['category_name'] ?? 'Sin categoría';

            if (!isset($grouped[$categoryId])) {
                $grouped[$categoryId] = [
                    'name' => $categoryName,
                    'slug' => $article['category_slug'] ?? '',
                    'icon' => $article['category_icon'] ?? '',
                    'articles' => []
                ];
            }

            $grouped[$categoryId]['articles'][] = [
                'id' => $article['id'],
                'title' => $article['title'],
                'slug' => $article['slug'],
                'sort_order' => $article['article_sort'] ?? 0
            ];
        }

        return $grouped;
    }

    /**
     * Get categories with article counts (for sidebar/index)
     */
    public function getCategoriesWithCounts(): array
    {
        if ($this->hasNewSchema()) {
            $stmt = $this->db->query("
                SELECT c.id, c.slug, c.name, c.icon, c.color, c.description,
                       COUNT(ka.id) as article_count
                FROM categories c
                LEFT JOIN {$this->table} ka ON ka.category_id = c.id AND ka.status = 'published'
                WHERE c.is_active = 1
                GROUP BY c.id
                HAVING article_count > 0
                ORDER BY c.sort_order ASC, c.name ASC
            ");
        } else {
            $stmt = $this->db->query("
                SELECT kt.id, kt.slug, kt.name, kt.icon, kt.color, kt.description,
                       COUNT(ka.id) as article_count
                FROM knowledge_topics kt
                LEFT JOIN {$this->table} ka ON ka.topic_id = kt.id AND ka.status = 'published'
                WHERE kt.is_active = 1
                GROUP BY kt.id
                HAVING article_count > 0
                ORDER BY kt.sort_order ASC, kt.name ASC
            ");
        }
        return $stmt->fetchAll();
    }

    /**
     * Get recent articles
     */
    public function getRecent(int $limit = 10): array
    {
        if ($this->hasNewSchema()) {
            $sql = "SELECT ka.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
                    FROM {$this->table} ka
                    LEFT JOIN categories c ON ka.category_id = c.id
                    WHERE ka.status = 'published'
                    ORDER BY ka.created_at DESC
                    LIMIT ?";
        } else {
            $sql = "SELECT ka.*, kt.name as category_name, kt.slug as category_slug, kt.icon as category_icon
                    FROM {$this->table} ka
                    LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                    WHERE ka.status = 'published'
                    ORDER BY ka.created_at DESC
                    LIMIT ?";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get articles by category slug
     */
    public function getByCategory(string $categorySlug, int $limit = 50): array
    {
        $orderClause = $this->getOrderClause();

        if ($this->hasNewSchema()) {
            $sql = "SELECT ka.*, c.name as category_name, c.slug as category_slug
                    FROM {$this->table} ka
                    INNER JOIN categories c ON ka.category_id = c.id
                    WHERE ka.status = 'published' AND c.slug = ?
                    ORDER BY {$orderClause}
                    LIMIT ?";
        } else {
            $sql = "SELECT ka.*, kt.name as category_name, kt.slug as category_slug
                    FROM {$this->table} ka
                    INNER JOIN knowledge_topics kt ON ka.topic_id = kt.id
                    WHERE ka.status = 'published' AND kt.slug = ?
                    ORDER BY ka.created_at DESC
                    LIMIT ?";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categorySlug, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get single article by slug (any status for admin preview)
     */
    public function getBySlug(string $slug): ?array
    {
        if ($this->hasNewSchema()) {
            $sql = "SELECT ka.*, c.name as category_name, c.slug as category_slug
                    FROM {$this->table} ka
                    LEFT JOIN categories c ON ka.category_id = c.id
                    WHERE ka.slug = ?";
        } else {
            $sql = "SELECT ka.*, kt.name as category_name, kt.slug as category_slug
                    FROM {$this->table} ka
                    LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                    WHERE ka.slug = ?";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Get all articles (published) for sitemap/listing
     */
    public function getAllPublished(): array
    {
        if ($this->hasNewSchema()) {
            $stmt = $this->db->query("
                SELECT ka.*, c.name as category_name, c.slug as category_slug
                FROM {$this->table} ka
                LEFT JOIN categories c ON ka.category_id = c.id
                WHERE ka.status = 'published'
                ORDER BY c.sort_order ASC, ka.sort_order ASC, ka.title ASC
            ");
        } else {
            $stmt = $this->db->query("
                SELECT ka.*, kt.name as category_name, kt.slug as category_slug
                FROM {$this->table} ka
                LEFT JOIN knowledge_topics kt ON ka.topic_id = kt.id
                WHERE ka.status = 'published'
                ORDER BY kt.sort_order ASC, ka.title ASC
            ");
        }
        return $stmt->fetchAll();
    }
}
