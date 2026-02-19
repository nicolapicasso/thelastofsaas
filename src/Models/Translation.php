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
     * Translatable entity types (TLOS)
     */
    public const ENTITY_TYPES = [
        'page' => 'Página',
        'post' => 'Post',
        'category' => 'Categoría',
        'event' => 'Evento',
        'activity' => 'Actividad',
        'sponsor' => 'Sponsor',
        'company' => 'Empresa',
        'team_member' => 'Miembro Equipo',
        'ticket_type' => 'Tipo de Entrada',
        'faq' => 'FAQ',
        'block' => 'Bloque',
        'landing' => 'Landing',
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
                    WHEN 'event' THEN (SELECT name FROM events WHERE id = t.entity_id)
                    WHEN 'activity' THEN (SELECT title FROM activities WHERE id = t.entity_id)
                    WHEN 'sponsor' THEN (SELECT name FROM sponsors WHERE id = t.entity_id)
                    WHEN 'company' THEN (SELECT name FROM companies WHERE id = t.entity_id)
                    WHEN 'team_member' THEN (SELECT name FROM team_members WHERE id = t.entity_id)
                    WHEN 'faq' THEN (SELECT question FROM faqs WHERE id = t.entity_id)
                    WHEN 'ticket_type' THEN (SELECT name FROM ticket_types WHERE id = t.entity_id)
                    WHEN 'block' THEN CONCAT('Bloque #', t.entity_id)
                    WHEN 'landing' THEN (SELECT title FROM landings WHERE id = t.entity_id)
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
     * Get untranslated content count per language (TLOS)
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

            // Events
            $stmt = $this->db->query("SELECT COUNT(*) FROM events WHERE status IN ('published', 'active')");
            $totalEvents = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'event' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedEvents = (int)$stmt->fetchColumn();
            $stats['event'] = max(0, $totalEvents - $translatedEvents);

            // Activities
            $stmt = $this->db->query("SELECT COUNT(*) FROM activities WHERE active = 1");
            $totalActivities = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'activity' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedActivities = (int)$stmt->fetchColumn();
            $stats['activity'] = max(0, $totalActivities - $translatedActivities);

            // Sponsors
            $stmt = $this->db->query("SELECT COUNT(*) FROM sponsors WHERE active = 1");
            $totalSponsors = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'sponsor' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedSponsors = (int)$stmt->fetchColumn();
            $stats['sponsor'] = max(0, $totalSponsors - $translatedSponsors);

            // Companies
            $stmt = $this->db->query("SELECT COUNT(*) FROM companies WHERE active = 1");
            $totalCompanies = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'company' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedCompanies = (int)$stmt->fetchColumn();
            $stats['company'] = max(0, $totalCompanies - $translatedCompanies);

            // Team Members
            $stmt = $this->db->query("SELECT COUNT(*) FROM team_members WHERE is_active = 1");
            $totalTeam = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'team_member' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedTeam = (int)$stmt->fetchColumn();
            $stats['team_member'] = max(0, $totalTeam - $translatedTeam);

            // Landings
            $stmt = $this->db->query("SELECT COUNT(*) FROM landings WHERE is_active = 1");
            $totalLandings = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'landing' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedLandings = (int)$stmt->fetchColumn();
            $stats['landing'] = max(0, $totalLandings - $translatedLandings);

            // Ticket Types
            $stmt = $this->db->query("SELECT COUNT(*) FROM ticket_types WHERE active = 1");
            $totalTicketTypes = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'ticket_type' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedTicketTypes = (int)$stmt->fetchColumn();
            $stats['ticket_type'] = max(0, $totalTicketTypes - $translatedTicketTypes);

            // Blocks (from published pages)
            $stmt = $this->db->query("SELECT COUNT(*) FROM page_blocks pb JOIN pages p ON p.id = pb.page_id WHERE pb.is_active = 1 AND p.status = 'published'");
            $totalBlocks = (int)$stmt->fetchColumn();
            $stmt = $this->db->prepare("SELECT COUNT(DISTINCT entity_id) FROM {$this->table} WHERE entity_type = 'block' AND language = ?");
            $stmt->execute([$targetLanguage]);
            $translatedBlocks = (int)$stmt->fetchColumn();
            $stats['block'] = max(0, $totalBlocks - $translatedBlocks);
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
            // Define entity configurations: table, fields to translate, name field, filter (TLOS)
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
                'event' => [
                    'table' => 'events',
                    'fields' => ['name', 'description'],
                    'name_field' => 'name',
                    'filter' => "status IN ('published', 'active')"
                ],
                'activity' => [
                    'table' => 'activities',
                    'fields' => ['title', 'description'],
                    'name_field' => 'title',
                    'filter' => "active = 1"
                ],
                'sponsor' => [
                    'table' => 'sponsors',
                    'fields' => ['name', 'description', 'tagline'],
                    'name_field' => 'name',
                    'filter' => "active = 1"
                ],
                'company' => [
                    'table' => 'companies',
                    'fields' => ['name', 'description'],
                    'name_field' => 'name',
                    'filter' => "active = 1"
                ],
                'team_member' => [
                    'table' => 'team_members',
                    'fields' => ['name', 'position', 'bio'],
                    'name_field' => 'name',
                    'filter' => "is_active = 1"
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
                ],
                'ticket_type' => [
                    'table' => 'ticket_types',
                    'fields' => ['name', 'description'],
                    'name_field' => 'name',
                    'filter' => "active = 1"
                ]
            ];

            // Filter by entity type if specified
            if ($entityType && isset($entities[$entityType])) {
                $entities = [$entityType => $entities[$entityType]];
            }

            // Handle blocks separately (JSON content, not direct columns)
            if (!$entityType || $entityType === 'block') {
                $blockResults = $this->getUntranslatedBlocks($targetLanguage, $limit);
                $results = array_merge($results, $blockResults);
            }

            // Skip generic loop if only blocks were requested
            if ($entityType === 'block') {
                // Sort and return early
                usort($results, function($a, $b) {
                    $cmp = strcmp($a['entity_type'], $b['entity_type']);
                    if ($cmp !== 0) return $cmp;
                    return strcmp($a['entity_name'] ?? '', $b['entity_name'] ?? '');
                });
                return array_slice($results, 0, $limit);
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

    /**
     * Get untranslated blocks for a specific language
     * Blocks store translatable content as JSON, so they need special handling
     */
    private function getUntranslatedBlocks(string $targetLanguage, int $limit = 100): array
    {
        $results = [];

        $translatableKeys = [
            'title', 'subtitle', 'description', 'text', 'content', 'cta_text',
            'link_text', 'button_text', 'label', 'placeholder', 'heading',
            'subheading', 'caption', 'quote', 'author', 'name', 'message',
            'success_title', 'success_message', 'submit_text', 'more_text',
            'price_suffix', 'badge_text', 'empty_text', 'helper_text'
        ];

        try {
            // Get block IDs that already have translations
            $stmt = $this->db->prepare("
                SELECT DISTINCT entity_id
                FROM {$this->table}
                WHERE entity_type = 'block' AND language = ?
            ");
            $stmt->execute([$targetLanguage]);
            $translatedIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            // Get active blocks from published pages that don't have translations yet
            $sql = "SELECT pb.id, pb.type, pb.content, p.title as page_title
                    FROM page_blocks pb
                    JOIN pages p ON p.id = pb.page_id
                    WHERE pb.is_active = 1 AND p.status = 'published'";

            if (!empty($translatedIds)) {
                $placeholders = implode(',', array_fill(0, count($translatedIds), '?'));
                $sql .= " AND pb.id NOT IN ({$placeholders})";
            }
            $sql .= " ORDER BY pb.id DESC LIMIT " . (int)$limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute(!empty($translatedIds) ? $translatedIds : []);
            $rows = $stmt->fetchAll();

            foreach ($rows as $row) {
                $content = json_decode($row['content'], true);
                if (!$content) continue;

                // Extract top-level translatable fields as a preview
                $fields = $this->extractTopLevelTranslatable($content, $translatableKeys);

                foreach ($fields as $fieldPath => $fieldValue) {
                    $results[] = [
                        'id' => null,
                        'entity_type' => 'block',
                        'entity_id' => $row['id'],
                        'field_name' => $fieldPath,
                        'language' => $targetLanguage,
                        'original_content' => $fieldValue,
                        'translated_content' => '',
                        'is_auto_translated' => 0,
                        'is_approved' => 0,
                        'entity_name' => 'Bloque ' . ucfirst($row['type']) . ' (' . $row['page_title'] . ')',
                        'is_new' => true
                    ];
                }
            }
        } catch (\PDOException $e) {
            error_log("Error getting untranslated blocks: " . $e->getMessage());
        }

        return $results;
    }

    /**
     * Extract top-level translatable fields from block content for admin display
     */
    private function extractTopLevelTranslatable(array $content, array $translatableKeys, string $prefix = ''): array
    {
        $fields = [];

        foreach ($content as $key => $value) {
            $fieldPath = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_string($value) && !empty($value) && in_array($key, $translatableKeys)) {
                $fields[$fieldPath] = $value;
            } elseif (is_array($value)) {
                if (isset($value[0]) && is_array($value[0])) {
                    // Array of objects (slides, plans, items...)
                    foreach ($value as $index => $item) {
                        if (is_array($item)) {
                            $nested = $this->extractTopLevelTranslatable($item, $translatableKeys, "{$fieldPath}.{$index}");
                            $fields = array_merge($fields, $nested);
                        }
                    }
                } elseif (!isset($value[0])) {
                    // Associative array, recurse
                    $nested = $this->extractTopLevelTranslatable($value, $translatableKeys, $fieldPath);
                    $fields = array_merge($fields, $nested);
                }
            }
        }

        return $fields;
    }
}
