<?php
/**
 * Translation Model
 * Omniwallet CMS
 */

namespace App\Models;

use App\Core\Model;

class Translation extends Model
{
    protected string $table = 'translations';

    protected array $fillable = [
        'entity_type',
        'entity_id',
        'field_name',
        'language',
        'original_content',
        'translated_content',
        'is_auto_translated',
        'is_approved'
    ];

    /**
     * Supported languages
     */
    public const LANGUAGES = [
        'es' => 'Español',
        'en' => 'English',
        'it' => 'Italiano',
        'fr' => 'Français',
        'de' => 'Deutsch'
    ];

    /**
     * Default language
     */
    public const DEFAULT_LANGUAGE = 'es';

    /**
     * Translatable entity types
     */
    public const ENTITY_TYPES = [
        'page' => 'Página',
        'post' => 'Post',
        'category' => 'Categoría',
        'service' => 'Servicio',
        'tool' => 'Herramienta',
        'client' => 'Cliente',
        'faq' => 'FAQ',
        'success_case' => 'Caso de Éxito',
        'block' => 'Bloque',
        'landing' => 'Landing',
        'landing_theme' => 'Tema Landing',
        'ui' => 'Interfaz'
    ];

    /**
     * Get translation for an entity field
     */
    public function getTranslation(string $entityType, int $entityId, string $field, string $language): ?string
    {
        $stmt = $this->db->prepare("
            SELECT translated_content
            FROM {$this->table}
            WHERE entity_type = ? AND entity_id = ? AND field_name = ? AND language = ?
        ");
        $stmt->execute([$entityType, $entityId, $field, $language]);
        $result = $stmt->fetch();

        return $result ? $result['translated_content'] : null;
    }

    /**
     * Get approved translation for an entity field (for frontend display)
     */
    public function getApprovedTranslation(string $entityType, int $entityId, string $field, string $language): ?string
    {
        $stmt = $this->db->prepare("
            SELECT translated_content
            FROM {$this->table}
            WHERE entity_type = ? AND entity_id = ? AND field_name = ? AND language = ? AND is_approved = 1
        ");
        $stmt->execute([$entityType, $entityId, $field, $language]);
        $result = $stmt->fetch();

        return $result ? $result['translated_content'] : null;
    }

    /**
     * Get all translations for an entity
     */
    public function getEntityTranslations(string $entityType, int $entityId, ?string $language = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE entity_type = ? AND entity_id = ?";
        $params = [$entityType, $entityId];

        if ($language) {
            $sql .= " AND language = ?";
            $params[] = $language;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Save translation
     */
    public function saveTranslation(
        string $entityType,
        int $entityId,
        string $field,
        string $language,
        string $originalContent,
        string $translatedContent,
        bool $isAutoTranslated = false,
        bool $isApproved = false
    ): bool {
        // Check if translation exists
        $existing = $this->first([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'field_name' => $field,
            'language' => $language
        ]);

        $data = [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'field_name' => $field,
            'language' => $language,
            'original_content' => $originalContent,
            'translated_content' => $translatedContent,
            'is_auto_translated' => $isAutoTranslated ? 1 : 0,
            'is_approved' => $isApproved ? 1 : 0
        ];

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return $this->create($data) !== false;
    }

    /**
     * Get pending translations (auto-translated but not approved)
     */
    public function getPendingTranslations(?string $entityType = null, ?string $language = null): array
    {
        return $this->getAllTranslations($entityType, $language, 'pending');
    }

    /**
     * Get all translations with optional filters
     * @param string|null $status - 'pending', 'approved', or null for all
     * @param string|null $search - search term for original or translated content
     */
    public function getAllTranslations(?string $entityType = null, ?string $language = null, ?string $status = null, ?string $search = null): array
    {
        $sql = "SELECT t.*,
                CASE t.entity_type
                    WHEN 'page' THEN (SELECT title FROM pages WHERE id = t.entity_id)
                    WHEN 'post' THEN (SELECT title FROM posts WHERE id = t.entity_id)
                    WHEN 'category' THEN (SELECT name FROM categories WHERE id = t.entity_id)
                    WHEN 'service' THEN (SELECT title FROM services WHERE id = t.entity_id)
                    WHEN 'tool' THEN (SELECT title FROM tools WHERE id = t.entity_id)
                    WHEN 'client' THEN (SELECT name FROM clients WHERE id = t.entity_id)
                    WHEN 'faq' THEN (SELECT question FROM faqs WHERE id = t.entity_id)
                    WHEN 'success_case' THEN (SELECT title FROM success_cases WHERE id = t.entity_id)
                    WHEN 'block' THEN CONCAT('Bloque #', t.entity_id)
                    WHEN 'landing' THEN (SELECT title FROM landings WHERE id = t.entity_id)
                    WHEN 'landing_theme' THEN (SELECT title FROM landing_themes WHERE id = t.entity_id)
                    ELSE NULL
                END as entity_name
                FROM {$this->table} t
                WHERE 1=1";
        $params = [];

        // Status filter
        if ($status === 'pending') {
            $sql .= " AND t.is_auto_translated = 1 AND t.is_approved = 0";
        } elseif ($status === 'approved') {
            $sql .= " AND t.is_approved = 1";
        }

        if ($entityType) {
            $sql .= " AND t.entity_type = ?";
            $params[] = $entityType;
        }

        if ($language) {
            $sql .= " AND t.language = ?";
            $params[] = $language;
        }

        // Search filter
        if ($search) {
            $sql .= " AND (t.original_content LIKE ? OR t.translated_content LIKE ? OR t.field_name LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY t.language ASC, t.entity_type ASC, t.updated_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Approve translation
     */
    public function approve(int $id): bool
    {
        return $this->update($id, ['is_approved' => 1]);
    }

    /**
     * Get translation statistics
     */
    public function getStatistics(): array
    {
        $stats = [];

        foreach (self::LANGUAGES as $code => $name) {
            if ($code === self::DEFAULT_LANGUAGE) continue;

            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total,
                    SUM(is_approved) as approved,
                    SUM(CASE WHEN is_auto_translated = 1 AND is_approved = 0 THEN 1 ELSE 0 END) as pending
                FROM {$this->table}
                WHERE language = ?
            ");
            $stmt->execute([$code]);
            $result = $stmt->fetch();

            $stats[$code] = [
                'name' => $name,
                'total' => (int)$result['total'],
                'approved' => (int)$result['approved'],
                'pending' => (int)$result['pending']
            ];
        }

        return $stats;
    }

    /**
     * Delete all translations for an entity
     */
    public function deleteEntityTranslations(string $entityType, int $entityId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE entity_type = ? AND entity_id = ?");
        return $stmt->execute([$entityType, $entityId]);
    }

    /**
     * Get untranslated content count per language
     */
    public function getUntranslatedStats(string $targetLanguage): array
    {
        // This gets a rough count of entities that might need translation
        // It compares existing translations against source entities
        $stats = [];

        try {
            // Pages
            $stmt = $this->db->query("SELECT COUNT(*) FROM pages WHERE status = 'published'");
            $totalPages = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'page' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedPages = (int)$stmt->fetchColumn();
            $stats['page'] = max(0, $totalPages - $translatedPages);

            // Posts
            $stmt = $this->db->query("SELECT COUNT(*) FROM posts WHERE status = 'published'");
            $totalPosts = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'post' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedPosts = (int)$stmt->fetchColumn();
            $stats['post'] = max(0, $totalPosts - $translatedPosts);

            // Services
            $stmt = $this->db->query("SELECT COUNT(*) FROM services WHERE is_active = 1");
            $totalServices = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'service' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedServices = (int)$stmt->fetchColumn();
            $stats['service'] = max(0, $totalServices - $translatedServices);

            // Tools
            $stmt = $this->db->query("SELECT COUNT(*) FROM tools WHERE is_active = 1");
            $totalTools = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'tool' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedTools = (int)$stmt->fetchColumn();
            $stats['tool'] = max(0, $totalTools - $translatedTools);

            // Success Cases
            $stmt = $this->db->query("SELECT COUNT(*) FROM success_cases WHERE status = 'published'");
            $totalCases = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'success_case' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedCases = (int)$stmt->fetchColumn();
            $stats['success_case'] = max(0, $totalCases - $translatedCases);

            // Landings
            $stmt = $this->db->query("SELECT COUNT(*) FROM landings WHERE is_active = 1");
            $totalLandings = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'landing' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedLandings = (int)$stmt->fetchColumn();
            $stats['landing'] = max(0, $totalLandings - $translatedLandings);
        } catch (\PDOException $e) {
            // If any table doesn't exist or has different structure, return empty stats
            error_log("Translation stats error: " . $e->getMessage());
            return [];
        }

        return $stats;
    }

    /**
     * Get untranslated content for a specific language
     * Returns entities that don't have translations yet
     */
    public function getUntranslatedContent(string $targetLanguage, ?string $entityType = null, int $limit = 100): array
    {
        $results = [];

        try {
            // Define entity configurations: table, fields to translate, name field, filter
            $entities = [
                'page' => [
                    'table' => 'pages',
                    'fields' => ['title', 'meta_title', 'meta_description'],
                    'name_field' => 'title',
                    'filter' => "status = 'published'"
                ],
                'post' => [
                    'table' => 'posts',
                    'fields' => ['title', 'excerpt', 'meta_title', 'meta_description'],
                    'name_field' => 'title',
                    'filter' => "status = 'published'"
                ],
                'service' => [
                    'table' => 'services',
                    'fields' => ['title', 'short_description', 'full_description'],
                    'name_field' => 'title',
                    'filter' => "is_active = 1"
                ],
                'tool' => [
                    'table' => 'tools',
                    'fields' => ['title', 'subtitle', 'description'],
                    'name_field' => 'title',
                    'filter' => "is_active = 1"
                ],
                'client' => [
                    'table' => 'clients',
                    'fields' => ['name', 'description'],
                    'name_field' => 'name',
                    'filter' => "is_active = 1"
                ],
                'success_case' => [
                    'table' => 'success_cases',
                    'fields' => ['title', 'challenge', 'solution', 'results', 'testimonial'],
                    'name_field' => 'title',
                    'filter' => "status = 'published'"
                ],
                'landing' => [
                    'table' => 'landings',
                    'fields' => ['title', 'subtitle', 'description', 'meta_title', 'meta_description'],
                    'name_field' => 'title',
                    'filter' => "is_active = 1"
                ],
                'faq' => [
                    'table' => 'faqs',
                    'fields' => ['question', 'answer'],
                    'name_field' => 'question',
                    'filter' => "is_active = 1"
                ],
                'category' => [
                    'table' => 'categories',
                    'fields' => ['name', 'description'],
                    'name_field' => 'name',
                    'filter' => "1=1"
                ]
            ];

            // Filter by entity type if specified
            if ($entityType && isset($entities[$entityType])) {
                $entities = [$entityType => $entities[$entityType]];
            }

            foreach ($entities as $type => $config) {
                // Get IDs that already have translations for this language
                $stmt = $this->db->prepare("
                    SELECT DISTINCT entity_id
                    FROM {$this->table}
                    WHERE entity_type = ? AND language = ?
                ");
                $stmt->execute([$type, $targetLanguage]);
                $translatedIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

                // Build query to get untranslated entities
                $sql = "SELECT id, {$config['name_field']} as entity_name";
                foreach ($config['fields'] as $field) {
                    $sql .= ", `{$field}`";
                }
                $sql .= " FROM {$config['table']} WHERE {$config['filter']}";

                if (!empty($translatedIds)) {
                    $placeholders = implode(',', array_fill(0, count($translatedIds), '?'));
                    $sql .= " AND id NOT IN ({$placeholders})";
                }
                $sql .= " ORDER BY id DESC LIMIT " . (int)$limit;

                $stmt = $this->db->prepare($sql);
                $stmt->execute(!empty($translatedIds) ? $translatedIds : []);
                $rows = $stmt->fetchAll();

                // Convert to translation-like format for each field
                foreach ($rows as $row) {
                    foreach ($config['fields'] as $field) {
                        if (!empty($row[$field])) {
                            $results[] = [
                                'id' => null, // No translation ID yet
                                'entity_type' => $type,
                                'entity_id' => $row['id'],
                                'field_name' => $field,
                                'language' => $targetLanguage,
                                'original_content' => $row[$field],
                                'translated_content' => '', // Empty - needs translation
                                'is_auto_translated' => 0,
                                'is_approved' => 0,
                                'entity_name' => $row['entity_name'],
                                'is_new' => true // Flag to identify this is not yet in DB
                            ];
                        }
                    }
                }
            }

            // Sort by entity type and entity name
            usort($results, function($a, $b) {
                $cmp = strcmp($a['entity_type'], $b['entity_type']);
                if ($cmp !== 0) return $cmp;
                return strcmp($a['entity_name'] ?? '', $b['entity_name'] ?? '');
            });

        } catch (\PDOException $e) {
            error_log("Error getting untranslated content: " . $e->getMessage());
            return [];
        }

        return array_slice($results, 0, $limit);
    }
}
