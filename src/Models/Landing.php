<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Helpers\Slug;

/**
 * Landing Model
 * Manages individual landing pages
 * Omniwallet CMS
 */
class Landing extends Model
{
    protected string $table = 'landings';

    protected array $fillable = [
        'theme_id',
        'title',
        'slug',
        'subtitle',
        'description',
        'image',
        'icon',
        'html_content',
        'html_content_translations',
        'sort_order',
        'is_active',
        'is_featured',
        'is_private',
        'access_password',
        'meta_title',
        'meta_description',
    ];

    /**
     * Supported languages for HTML content
     */
    public const SUPPORTED_LANGUAGES = ['es', 'en', 'it', 'fr', 'de'];

    /**
     * Get HTML content for a specific language
     * Falls back to Spanish (html_content) if translation not available
     */
    public function getHtmlContent(array $landing, string $language = 'es'): string
    {
        // Default language uses the main field
        if ($language === 'es') {
            return $landing['html_content'] ?? '';
        }

        // Check for translated content
        $translations = $landing['html_content_translations'] ?? null;
        if ($translations) {
            if (is_string($translations)) {
                $translations = json_decode($translations, true);
            }
            if (isset($translations[$language]) && !empty($translations[$language])) {
                return $translations[$language];
            }
        }

        // Fallback to Spanish content
        return $landing['html_content'] ?? '';
    }

    /**
     * Get all HTML translations as array
     */
    public function getHtmlTranslations(array $landing): array
    {
        $translations = ['es' => $landing['html_content'] ?? ''];

        $stored = $landing['html_content_translations'] ?? null;
        if ($stored) {
            if (is_string($stored)) {
                $stored = json_decode($stored, true);
            }
            if (is_array($stored)) {
                $translations = array_merge($translations, $stored);
            }
        }

        return $translations;
    }

    /**
     * Save HTML translations
     */
    public function saveHtmlTranslations(int $id, array $translations): bool
    {
        $data = [];

        // Spanish goes to html_content
        if (isset($translations['es'])) {
            $data['html_content'] = $translations['es'];
            unset($translations['es']);
        }

        // Other languages go to html_content_translations
        if (!empty($translations)) {
            $data['html_content_translations'] = json_encode($translations, JSON_UNESCAPED_UNICODE);
        }

        if (!empty($data)) {
            return $this->update($id, $data);
        }

        return true;
    }

    /**
     * Get active landings (excludes private)
     */
    public function getActive(int $limit = 100): array
    {
        $sql = "SELECT l.*, t.title as theme_title, t.slug as theme_slug
                FROM `{$this->table}` l
                JOIN landing_themes t ON t.id = l.theme_id
                WHERE l.is_active = 1 AND t.is_active = 1 AND (l.is_private = 0 OR l.is_private IS NULL)
                ORDER BY l.sort_order ASC, l.title ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get landings by theme (excludes private)
     */
    public function getByTheme(int $themeId, int $limit = 100): array
    {
        $sql = "SELECT l.*, t.title as theme_title, t.slug as theme_slug
                FROM `{$this->table}` l
                JOIN landing_themes t ON t.id = l.theme_id
                WHERE l.theme_id = ? AND l.is_active = 1 AND t.is_active = 1 AND (l.is_private = 0 OR l.is_private IS NULL)
                ORDER BY l.sort_order ASC, l.title ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$themeId, $limit]);
    }

    /**
     * Get landings by theme slug (excludes private)
     */
    public function getByThemeSlug(string $themeSlug, int $limit = 100): array
    {
        $sql = "SELECT l.*, t.title as theme_title, t.slug as theme_slug
                FROM `{$this->table}` l
                JOIN landing_themes t ON t.id = l.theme_id
                WHERE t.slug = ? AND l.is_active = 1 AND t.is_active = 1 AND (l.is_private = 0 OR l.is_private IS NULL)
                ORDER BY l.sort_order ASC, l.title ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$themeSlug, $limit]);
    }

    /**
     * Get featured landings (excludes private)
     */
    public function getFeatured(int $limit = 12): array
    {
        $sql = "SELECT l.*, t.title as theme_title, t.slug as theme_slug
                FROM `{$this->table}` l
                JOIN landing_themes t ON t.id = l.theme_id
                WHERE l.is_active = 1 AND l.is_featured = 1 AND t.is_active = 1 AND (l.is_private = 0 OR l.is_private IS NULL)
                ORDER BY l.sort_order ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Find by theme and slug (excludes private - use findByThemeAndSlugIncludingPrivate for password-protected access)
     */
    public function findByThemeAndSlug(string $themeSlug, string $landingSlug): ?array
    {
        $sql = "SELECT l.*, t.title as theme_title, t.slug as theme_slug,
                       t.icon as theme_icon, t.description as theme_description
                FROM `{$this->table}` l
                JOIN landing_themes t ON t.id = l.theme_id
                WHERE t.slug = ? AND l.slug = ? AND l.is_active = 1 AND t.is_active = 1 AND (l.is_private = 0 OR l.is_private IS NULL)";
        return $this->db->fetch($sql, [$themeSlug, $landingSlug]);
    }

    /**
     * Find by slug within a theme
     */
    public function findBySlugInTheme(int $themeId, string $slug): ?array
    {
        return $this->first(['theme_id' => $themeId, 'slug' => $slug, 'is_active' => 1]);
    }

    /**
     * Create with auto-generated slug
     */
    public function createWithSlug(array $data): int
    {
        if (empty($data['slug']) && !empty($data['title'])) {
            // Slug unique within theme
            $data['slug'] = $this->generateUniqueSlugInTheme($data['title'], (int)$data['theme_id']);
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
            $themeId = $data['theme_id'] ?? $current['theme_id'];
            $data['slug'] = $this->generateUniqueSlugInTheme($data['title'], (int)$themeId, $id);
        }

        return $this->update($id, $data);
    }

    /**
     * Generate unique slug within a theme
     */
    private function generateUniqueSlugInTheme(string $title, int $themeId, ?int $excludeId = null): string
    {
        $baseSlug = Slug::generate($title);
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM `{$this->table}` WHERE theme_id = ? AND slug = ?";
            $params = [$themeId, $slug];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $existing = $this->db->fetch($sql, $params);

            if (!$existing) {
                break;
            }

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Reorder landings
     */
    public function reorder(array $ids): void
    {
        foreach ($ids as $order => $id) {
            $this->update((int) $id, ['sort_order' => $order]);
        }
    }

    /**
     * Get all for admin with theme info
     */
    public function getAllForAdmin(): array
    {
        $sql = "SELECT l.*, t.title as theme_title, t.slug as theme_slug
                FROM `{$this->table}` l
                JOIN landing_themes t ON t.id = l.theme_id
                ORDER BY t.sort_order ASC, l.sort_order ASC, l.title ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Find landing by ID with theme info (for admin)
     */
    public function findWithTheme(int $id): ?array
    {
        $sql = "SELECT l.*, t.title as theme_title, t.slug as theme_slug
                FROM `{$this->table}` l
                JOIN landing_themes t ON t.id = l.theme_id
                WHERE l.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Get by theme for admin (including inactive)
     */
    public function getByThemeForAdmin(int $themeId): array
    {
        $sql = "SELECT * FROM `{$this->table}`
                WHERE theme_id = ?
                ORDER BY sort_order ASC, title ASC";
        return $this->db->fetchAll($sql, [$themeId]);
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
     * Get other landings in same theme (for navigation, excludes private)
     */
    public function getOthersInTheme(int $themeId, int $excludeId, int $limit = 10): array
    {
        $sql = "SELECT id, title, slug, icon, image
                FROM `{$this->table}`
                WHERE theme_id = ? AND id != ? AND is_active = 1 AND (is_private = 0 OR is_private IS NULL)
                ORDER BY sort_order ASC, title ASC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$themeId, $excludeId, $limit]);
    }

    /**
     * Get previous and next landing in theme (excludes private)
     */
    public function getPrevNext(int $themeId, int $sortOrder): array
    {
        $prev = $this->db->fetch(
            "SELECT id, title, slug FROM `{$this->table}`
             WHERE theme_id = ? AND sort_order < ? AND is_active = 1 AND (is_private = 0 OR is_private IS NULL)
             ORDER BY sort_order DESC LIMIT 1",
            [$themeId, $sortOrder]
        );

        $next = $this->db->fetch(
            "SELECT id, title, slug FROM `{$this->table}`
             WHERE theme_id = ? AND sort_order > ? AND is_active = 1 AND (is_private = 0 OR is_private IS NULL)
             ORDER BY sort_order ASC LIMIT 1",
            [$themeId, $sortOrder]
        );

        return ['prev' => $prev, 'next' => $next];
    }

    /**
     * Get published landings (alias for getActive with theme info)
     */
    public function getPublished(int $limit = 100): array
    {
        return $this->getActive($limit);
    }

    /**
     * Get landings by array of IDs (excludes private)
     */
    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT l.*, t.title as theme_title, t.slug as theme_slug, t.image as theme_image
                FROM `{$this->table}` l
                JOIN landing_themes t ON t.id = l.theme_id
                WHERE l.id IN ({$placeholders}) AND l.is_active = 1 AND t.is_active = 1 AND (l.is_private = 0 OR l.is_private IS NULL)
                ORDER BY FIELD(l.id, {$placeholders})";

        // Pass IDs twice: once for IN clause, once for FIELD order
        $params = array_merge($ids, $ids);
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Check if landing is private
     */
    public function isPrivate(array $landing): bool
    {
        return !empty($landing['is_private']) && (int) $landing['is_private'] === 1;
    }

    /**
     * Verify password for private landing
     */
    public function verifyPassword(array $landing, string $password): bool
    {
        if (empty($landing['access_password'])) {
            return false;
        }

        return password_verify($password, $landing['access_password']);
    }

    /**
     * Hash password for storage
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Find landing by theme and slug (including private landings)
     * Used for frontend access with password protection
     */
    public function findByThemeAndSlugIncludingPrivate(string $themeSlug, string $landingSlug): ?array
    {
        $sql = "SELECT l.*, t.title as theme_title, t.slug as theme_slug,
                       t.icon as theme_icon, t.description as theme_description
                FROM `{$this->table}` l
                JOIN landing_themes t ON t.id = l.theme_id
                WHERE t.slug = ? AND l.slug = ? AND l.is_active = 1 AND t.is_active = 1";
        return $this->db->fetch($sql, [$themeSlug, $landingSlug]);
    }
}
