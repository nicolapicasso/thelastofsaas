<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * FAQ Model
 * Omniwallet CMS
 */
class FAQ extends Model
{
    protected string $table = 'faqs';

    protected array $fillable = [
        'question',
        'answer',
        'category_id',
        'faq_group',
        'is_active',
        'sort_order',
        'views',
    ];

    /**
     * Get active FAQs
     */
    public function getActive(int $limit = 100): array
    {
        $sql = "SELECT f.*, c.name as category_name, c.slug as category_slug
                FROM `{$this->table}` f
                LEFT JOIN categories c ON f.category_id = c.id
                WHERE f.is_active = 1
                ORDER BY f.sort_order ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get FAQs by category
     */
    public function getByCategory(int $categoryId): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE category_id = ? AND is_active = 1 ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql, [$categoryId]);
    }

    /**
     * Get grouped FAQs by category
     */
    public function getGrouped(): array
    {
        $faqs = $this->getActive();
        $grouped = [];

        foreach ($faqs as $faq) {
            $group = $faq['category_name'] ?? 'General';
            $grouped[$group][] = $faq;
        }

        return $grouped;
    }

    /**
     * Reorder FAQs
     */
    public function reorder(array $ids): void
    {
        foreach ($ids as $order => $id) {
            $this->update((int) $id, ['sort_order' => $order]);
        }
    }

    /**
     * Get FAQ categories (groups)
     */
    public function getGroups(): array
    {
        $sql = "SELECT DISTINCT c.id, c.name, c.slug
                FROM categories c
                INNER JOIN `{$this->table}` f ON c.id = f.category_id
                WHERE c.is_active = 1
                ORDER BY c.name";

        return $this->db->fetchAll($sql);
    }

    /**
     * Get unique FAQ groups
     */
    public function getUniqueGroups(): array
    {
        try {
            $sql = "SELECT DISTINCT faq_group FROM `{$this->table}`
                    WHERE faq_group IS NOT NULL AND faq_group != ''
                    ORDER BY faq_group ASC";
            $results = $this->db->fetchAll($sql);
            return array_column($results, 'faq_group');
        } catch (\Exception $e) {
            // Column might not exist yet
            return [];
        }
    }

    /**
     * Get FAQs by faq_group
     */
    public function getByGroup(string $group, int $limit = 100): array
    {
        try {
            $sql = "SELECT f.*, c.name as category_name, c.slug as category_slug
                    FROM `{$this->table}` f
                    LEFT JOIN categories c ON f.category_id = c.id
                    WHERE f.is_active = 1 AND f.faq_group = ?
                    ORDER BY f.sort_order ASC
                    LIMIT ?";
            return $this->db->fetchAll($sql, [$group, $limit]);
        } catch (\Exception $e) {
            // Column might not exist yet, return all active
            return $this->getActive($limit);
        }
    }

    /**
     * Search FAQs
     */
    public function search(string $query): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE is_active = 1
                AND (question LIKE ? OR answer LIKE ?)
                ORDER BY sort_order ASC";

        $searchTerm = '%' . $query . '%';

        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm]);
    }

    /**
     * Get FAQs for Schema.org structured data
     */
    public function getForSchema(?int $categoryId = null): array
    {
        if ($categoryId) {
            $faqs = $this->getByCategory($categoryId);
        } else {
            $faqs = $this->getActive();
        }

        return array_map(function ($faq) {
            return [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq['answer']),
                ],
            ];
        }, $faqs);
    }
}
