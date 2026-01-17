<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * Post Model
 * Omniwallet CMS
 */
class Post extends Model
{
    protected string $table = 'posts';

    protected array $fillable = [
        'slug',
        'category_id',
        'author_id',
        'title',
        'subtitle',
        'excerpt',
        'content',
        'hero_image',
        'thumbnail',
        'gallery',
        'video_url',
        'video_thumbnail',
        'meta_title',
        'meta_description',
        'enable_llm_qa',
        'llm_qa_content',
        'status',
        'is_featured',
        'published_at',
    ];

    /**
     * Get published posts
     */
    public function getPublished(int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, u.name as author_name
                FROM `{$this->table}` p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.status = 'published'
                ORDER BY p.published_at DESC
                LIMIT {$limit} OFFSET {$offset}";

        return $this->db->fetchAll($sql);
    }

    /**
     * Get featured posts
     */
    public function getFeatured(int $limit = 3): array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published' AND p.is_featured = 1
                ORDER BY p.published_at DESC
                LIMIT {$limit}";

        return $this->db->fetchAll($sql);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, u.name as author_name
                FROM `{$this->table}` p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.slug = ? AND p.status = 'published'
                LIMIT 1";

        return $this->db->fetch($sql, [$slug]);
    }

    /**
     * Get posts by category
     */
    public function getByCategory(int $categoryId, int $limit = 10): array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = ? AND p.status = 'published'
                ORDER BY p.published_at DESC
                LIMIT {$limit}";

        return $this->db->fetchAll($sql, [$categoryId]);
    }

    /**
     * Get all posts with relations for admin
     */
    public function getAllWithRelations(array $conditions = [], array $orderBy = ['id' => 'DESC']): array
    {
        $where = '';
        $params = [];

        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $field => $value) {
                $clauses[] = "p.`{$field}` = ?";
                $params[] = $value;
            }
            $where = 'WHERE ' . implode(' AND ', $clauses);
        }

        $order = [];
        foreach ($orderBy as $field => $direction) {
            $order[] = "p.`{$field}` " . (strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC');
        }
        $orderSql = 'ORDER BY ' . implode(', ', $order);

        $sql = "SELECT p.*, c.name as category_name, u.name as author_name
                FROM `{$this->table}` p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.author_id = u.id
                {$where}
                {$orderSql}";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Create with auto-generated slug
     */
    public function createWithSlug(array $data): int
    {
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Slug::unique($data['title'], $this->table);
        }

        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        return $this->create($data);
    }

    /**
     * Update with slug handling
     */
    public function updateWithSlug(int $id, array $data): bool
    {
        $current = $this->find($id);

        if (isset($data['title']) && empty($data['slug'])) {
            $data['slug'] = Slug::unique($data['title'], $this->table, 'slug', $id);
        }

        if (isset($data['status']) && $data['status'] === 'published' && $current['status'] !== 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($id, $data);
    }

    /**
     * Increment view count
     */
    public function incrementViews(int $id): void
    {
        $sql = "UPDATE `{$this->table}` SET views = views + 1 WHERE id = ?";
        $this->db->query($sql, [$id]);
    }

    /**
     * Get related posts
     */
    public function getRelated(int $postId, int $categoryId, int $limit = 3): array
    {
        $sql = "SELECT p.*, c.name as category_name
                FROM `{$this->table}` p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id != ? AND p.category_id = ? AND p.status = 'published'
                ORDER BY p.published_at DESC
                LIMIT {$limit}";

        return $this->db->fetchAll($sql, [$postId, $categoryId]);
    }

    /**
     * Get recent published posts
     */
    public function getRecent(int $limit = 10): array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'
                ORDER BY p.published_at DESC
                LIMIT {$limit}";

        return $this->db->fetchAll($sql);
    }
}
