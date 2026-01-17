<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * Page Model
 * Omniwallet CMS
 */
class Page extends Model
{
    protected string $table = 'pages';

    protected array $fillable = [
        'slug',
        'title',
        'status',
        'template',
        'meta_title',
        'meta_description',
        'enable_llm_qa',
        'llm_qa_content',
        'show_header',
        'show_footer',
        'custom_css',
        'custom_js',
        'author_id',
        'published_at',
    ];

    /**
     * Get published pages
     */
    public function getPublished(): array
    {
        return $this->where(['status' => 'published'], ['title' => 'ASC']);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->first(['slug' => $slug, 'status' => 'published']);
    }

    /**
     * Get page with blocks
     */
    public function getWithBlocks(int $id): ?array
    {
        $page = $this->find($id);

        if (!$page) {
            return null;
        }

        $sql = "SELECT * FROM page_blocks WHERE page_id = ? AND is_active = 1 ORDER BY sort_order ASC";
        $page['blocks'] = $this->db->fetchAll($sql, [$id]);

        return $page;
    }

    /**
     * Get published page with blocks
     */
    public function getPublishedWithBlocks(string $slug): ?array
    {
        $page = $this->findBySlug($slug);

        if (!$page) {
            return null;
        }

        $sql = "SELECT * FROM page_blocks WHERE page_id = ? AND is_active = 1 ORDER BY sort_order ASC";
        $page['blocks'] = $this->db->fetchAll($sql, [$page['id']]);

        return $page;
    }

    /**
     * Create with auto-generated slug
     */
    public function createWithSlug(array $data): int
    {
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Slug::unique($data['title'], $this->table);
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
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
     * Get pages for menu
     */
    public function getForMenu(): array
    {
        $sql = "SELECT id, slug, title FROM `{$this->table}` WHERE status = 'published' ORDER BY title ASC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Duplicate page with blocks
     */
    public function duplicate(int $id): ?int
    {
        $page = $this->getWithBlocks($id);

        if (!$page) {
            return null;
        }

        // Create new page
        $newPageData = $page;
        unset($newPageData['id'], $newPageData['blocks'], $newPageData['created_at'], $newPageData['updated_at']);
        $newPageData['title'] = $page['title'] . ' (copy)';
        $newPageData['slug'] = Slug::unique($newPageData['title'], $this->table);
        $newPageData['status'] = 'draft';
        $newPageData['published_at'] = null;

        $newPageId = $this->create($newPageData);

        // Duplicate blocks
        if (!empty($page['blocks'])) {
            $blockModel = new Block();
            foreach ($page['blocks'] as $block) {
                $newBlockData = $block;
                unset($newBlockData['id'], $newBlockData['created_at'], $newBlockData['updated_at']);
                $newBlockData['page_id'] = $newPageId;
                $blockModel->create($newBlockData);
            }
        }

        return $newPageId;
    }
}
