<?php
/**
 * SEO Generator Service
 * Uses OpenAI GPT to generate SEO metadata for content
 * We're Sinapsis CMS
 */

namespace App\Services;

use App\Models\SEOMetadata;
use App\Models\Setting;
use App\Models\Translation;

class SEOGeneratorService
{
    private SEOMetadata $seoModel;
    private ?string $apiKey;
    private string $model;
    private string $logFile;
    private array $sessionErrors = [];

    public function __construct()
    {
        $this->seoModel = new SEOMetadata();
        $this->logFile = __DIR__ . '/../../logs/seo.log';

        // Ensure logs directory exists
        $logsDir = dirname($this->logFile);
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0755, true);
        }

        // Get API key from database or .env
        $settingModel = new Setting();
        $dbApiKey = $settingModel->get('openai_api_key');
        $this->apiKey = !empty($dbApiKey) ? $dbApiKey : ($_ENV['OPENAI_API_KEY'] ?? null);
        $this->model = $settingModel->get('openai_model') ?? 'gpt-4o-mini';
    }

    /**
     * Check if service is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Log error
     */
    private function logError(string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logLine = "[{$timestamp}] ERROR: {$message}{$contextStr}\n";
        @file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
        $this->sessionErrors[] = ['time' => $timestamp, 'message' => $message, 'context' => $context];
    }

    /**
     * Log info
     */
    private function logInfo(string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logLine = "[{$timestamp}] INFO: {$message}{$contextStr}\n";
        @file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * Get session errors
     */
    public function getSessionErrors(): array
    {
        return $this->sessionErrors;
    }

    /**
     * Generate SEO metadata for an entity using GPT
     */
    public function generateForEntity(
        string $entityType,
        int $entityId,
        string $language = 'es',
        bool $overwrite = false
    ): array {
        // Check if SEO already exists
        $existing = $this->seoModel->getForEntity($entityType, $entityId, $language);
        if ($existing && !$overwrite) {
            return [
                'success' => false,
                'message' => 'SEO metadata already exists. Use overwrite option to regenerate.',
                'data' => $existing
            ];
        }

        // Get entity content
        $entityContent = $this->seoModel->getEntityContent($entityType, $entityId);
        if (!$entityContent) {
            return [
                'success' => false,
                'message' => 'Entity not found'
            ];
        }

        // Build content summary for GPT
        $contentSummary = $this->buildContentSummary($entityType, $entityContent);

        // Generate SEO with GPT
        $generatedSeo = $this->callGPTForSEO($contentSummary, $entityType, $language);

        if (!$generatedSeo) {
            return [
                'success' => false,
                'message' => $this->lastError ?? 'Error al generar SEO con IA'
            ];
        }

        // Save to database
        $seoId = $this->seoModel->saveForEntity($entityType, $entityId, $language, [
            'meta_title' => $generatedSeo['meta_title'] ?? null,
            'meta_description' => $generatedSeo['meta_description'] ?? null,
            'og_title' => $generatedSeo['og_title'] ?? $generatedSeo['meta_title'] ?? null,
            'og_description' => $generatedSeo['og_description'] ?? $generatedSeo['meta_description'] ?? null,
            'keywords' => $generatedSeo['keywords'] ?? null,
            'schema_type' => $this->getSchemaTypeForEntity($entityType),
            'is_auto_generated' => 1,
            'generation_source' => 'gpt',
            'generated_at' => date('Y-m-d H:i:s')
        ]);

        // Log audit
        $this->seoModel->logAudit($seoId, $entityType, $entityId, 'generate', null, null, null, null);

        $this->logInfo("SEO generated", [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'language' => $language
        ]);

        return [
            'success' => true,
            'message' => 'SEO metadata generated successfully',
            'data' => $generatedSeo
        ];
    }

    /**
     * Generate SEO for entity in all languages
     */
    public function generateForEntityAllLanguages(
        string $entityType,
        int $entityId,
        bool $overwrite = false
    ): array {
        $results = [];

        foreach (SEOMetadata::LANGUAGES as $langCode => $langName) {
            $results[$langCode] = $this->generateForEntity($entityType, $entityId, $langCode, $overwrite);
        }

        return $results;
    }

    /**
     * Mass generate SEO for entity type
     */
    public function massGenerate(
        string $entityType,
        string $language = 'es',
        int $offset = 0,
        int $batchSize = 5,
        bool $overwrite = false
    ): array {
        $stats = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'processed' => 0,
            'hasMore' => false,
            'errors' => [],
            'debug' => [] // Debug info
        ];

        // Log the request
        $this->logInfo("massGenerate called", [
            'entityType' => $entityType,
            'language' => $language,
            'offset' => $offset,
            'batchSize' => $batchSize,
            'overwrite' => $overwrite
        ]);

        // For overwrite mode, use offset pagination
        // For normal mode, always get first N entities without SEO
        // (because after processing, processed entities won't appear in the list anymore)
        if ($overwrite) {
            $batch = $this->getAllEntities($entityType, $batchSize, $offset);
            $totalCount = $this->countAllEntities($entityType);
            $stats['hasMore'] = ($offset + $batchSize) < $totalCount;
            $stats['nextOffset'] = $offset + $batchSize;
            $stats['total'] = $totalCount;
        } else {
            // Always get first batch of entities without SEO for this language
            // After processing, they'll have SEO and won't appear in next call
            $batch = $this->seoModel->getEntitiesWithoutSEO($entityType, $batchSize, $language);
            $totalRemaining = count($this->seoModel->getEntitiesWithoutSEO($entityType, 100, $language));
            $stats['hasMore'] = $totalRemaining > $batchSize;
            $stats['nextOffset'] = 0; // Always 0 since list changes after processing
            $stats['total'] = $totalRemaining;
        }

        $stats['processed'] = count($batch);
        $stats['debug']['batchCount'] = count($batch);
        $stats['debug']['entityType'] = $entityType;

        // Log batch info
        $this->logInfo("Batch retrieved", [
            'count' => count($batch),
            'entityType' => $entityType,
            'batch' => array_map(fn($e) => $e['id'] ?? 'no-id', $batch)
        ]);

        foreach ($batch as $entity) {
            try {
                $result = $this->generateForEntity($entityType, $entity['id'], $language, $overwrite);

                if ($result['success']) {
                    $stats['success']++;
                } else {
                    if (strpos($result['message'] ?? '', 'already exists') !== false) {
                        $stats['skipped']++;
                    } else {
                        $stats['failed']++;
                        $stats['errors'][] = [
                            'entity_id' => $entity['id'],
                            'message' => $result['message'] ?? 'Unknown error'
                        ];
                    }
                }
            } catch (\Exception $e) {
                $stats['failed']++;
                $stats['errors'][] = [
                    'entity_id' => $entity['id'],
                    'message' => $e->getMessage()
                ];
                $this->logError("Mass generate error", [
                    'entity_type' => $entityType,
                    'entity_id' => $entity['id'],
                    'error' => $e->getMessage()
                ]);
            }

            // Small delay to avoid rate limiting
            usleep(500000); // 0.5 second
        }

        return $stats;
    }

    /**
     * Count all entities of a type
     */
    private function countAllEntities(string $entityType): int
    {
        $db = \App\Core\Database::getInstance()->getConnection();

        $tableConfig = [
            'page' => ['table' => 'pages', 'condition' => "status = 'published'"],
            'post' => ['table' => 'posts', 'condition' => "status = 'published'"],
            'success_case' => ['table' => 'success_cases', 'condition' => "status = 'published'"],
            'feature' => ['table' => 'features', 'condition' => "is_active = 1"],
            'partner' => ['table' => 'partners', 'condition' => "is_active = 1"],
            'landing' => ['table' => 'landings', 'condition' => "is_active = 1"],
            'knowledge' => ['table' => 'knowledge_articles', 'condition' => "status = 'published'"],
            'integration' => ['table' => 'integrations', 'condition' => "is_active = 1"],
            'category' => ['table' => 'categories', 'condition' => "is_active = 1"]
        ];

        $config = $tableConfig[$entityType] ?? null;
        if (!$config) return 0;

        $sql = "SELECT COUNT(*) FROM {$config['table']} WHERE {$config['condition']}";
        return (int) $db->query($sql)->fetchColumn();
    }

    /**
     * Get all entities of a type
     */
    private function getAllEntities(string $entityType, int $limit = 50, int $offset = 0): array
    {
        $db = \App\Core\Database::getInstance()->getConnection();

        $tableConfig = [
            'page' => ['table' => 'pages', 'title' => 'title', 'condition' => "status = 'published'"],
            'post' => ['table' => 'posts', 'title' => 'title', 'condition' => "status = 'published'"],
            'success_case' => ['table' => 'success_cases', 'title' => 'title', 'condition' => "status = 'published'"],
            'feature' => ['table' => 'features', 'title' => 'title', 'condition' => "is_active = 1"],
            'partner' => ['table' => 'partners', 'title' => 'name', 'condition' => "is_active = 1"],
            'landing' => ['table' => 'landings', 'title' => 'title', 'condition' => "is_active = 1"],
            'knowledge' => ['table' => 'knowledge_articles', 'title' => 'title', 'condition' => "status = 'published'"],
            'integration' => ['table' => 'integrations', 'title' => 'title', 'condition' => "is_active = 1"],
            'category' => ['table' => 'categories', 'title' => 'name', 'condition' => "is_active = 1"]
        ];

        $config = $tableConfig[$entityType] ?? null;
        if (!$config) return [];

        $sql = "SELECT id, {$config['title']} as title, slug
                FROM {$config['table']}
                WHERE {$config['condition']}
                ORDER BY id DESC
                LIMIT ? OFFSET ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$limit, $offset]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Build content summary for GPT
     */
    private function buildContentSummary(string $entityType, array $entity): string
    {
        $summary = [];

        // Get relevant fields based on entity type
        $fieldMaps = [
            'page' => ['title', 'content', 'meta_title', 'meta_description'],
            'post' => ['title', 'subtitle', 'excerpt', 'content'],
            'success_case' => ['title', 'challenge', 'solution', 'results', 'testimonial'],
            'feature' => ['title', 'short_description', 'full_description'],
            'partner' => ['name', 'description', 'partner_type', 'country'],
            'landing' => ['title', 'subtitle', 'description'],
            'knowledge' => ['title', 'excerpt', 'content'],
            'integration' => ['title', 'subtitle', 'description'],
            'service' => ['title', 'short_description', 'full_description'],
            'tool' => ['title', 'short_description', 'description'],
            'client' => ['name', 'description', 'industry', 'location'],
            'category' => ['name', 'description', 'meta_title', 'meta_description']
        ];

        $fields = $fieldMaps[$entityType] ?? ['title', 'content', 'description'];

        foreach ($fields as $field) {
            if (!empty($entity[$field])) {
                $value = strip_tags($entity[$field]);
                $value = mb_substr($value, 0, 1000, 'UTF-8'); // Limit content length
                // Clean invalid UTF-8 characters
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value); // Remove control characters
                $summary[] = ucfirst(str_replace('_', ' ', $field)) . ": " . $value;
            }
        }

        return implode("\n\n", $summary);
    }

    /**
     * Store last error message for user feedback
     */
    private ?string $lastError = null;

    /**
     * Get last error message
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Call GPT to generate SEO metadata
     */
    private function callGPTForSEO(string $contentSummary, string $entityType, string $language): ?array
    {
        $this->lastError = null;

        if (empty($this->apiKey)) {
            $this->lastError = "API key de OpenAI no configurada. Ve a Configuración > Integraciones.";
            $this->logError("API key not configured");
            return null;
        }

        $languageNames = [
            'es' => 'Spanish',
            'en' => 'English',
            'it' => 'Italian',
            'fr' => 'French',
            'de' => 'German'
        ];

        $langName = $languageNames[$language] ?? 'Spanish';
        $entityLabel = SEOMetadata::ENTITY_TYPES[$entityType] ?? $entityType;

        $prompt = $this->buildSEOPrompt($contentSummary, $entityLabel, $langName);

        $url = 'https://api.openai.com/v1/chat/completions';

        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert SEO specialist for We\'re Sinapsis, a digital marketing and technology agency specializing in web development, branding, digital strategy, and creative solutions. Generate optimized SEO metadata that is compelling, accurate, and follows best practices. Always respond with valid JSON only, no explanations.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.4,
            'max_tokens' => 1000
        ];

        // Encode JSON with proper UTF-8 handling
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($jsonData === false) {
            $this->lastError = "Error al codificar JSON: " . json_last_error_msg();
            $this->logError("JSON encode error", ['error' => json_last_error_msg()]);
            return null;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $this->lastError = "Error de conexión: {$curlError}";
            $this->logError("cURL error: {$curlError}");
            return null;
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? "HTTP {$httpCode}";
            $this->lastError = "Error de OpenAI: {$errorMsg}";
            $this->logError("OpenAI API error: {$errorMsg}");
            return null;
        }

        $result = json_decode($response, true);

        if (isset($result['choices'][0]['message']['content'])) {
            $content = trim($result['choices'][0]['message']['content']);

            // Clean up potential markdown code blocks
            $content = preg_replace('/^```json?\s*/i', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);

            $seoData = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($seoData)) {
                return $seoData;
            }

            $this->lastError = "Respuesta inválida de GPT. Intenta de nuevo.";
            $this->logError("Invalid JSON response from GPT", ['response' => $content]);
        } else {
            $this->lastError = "Respuesta vacía de GPT. Intenta de nuevo.";
        }

        return null;
    }

    /**
     * Build SEO generation prompt
     */
    private function buildSEOPrompt(string $content, string $entityType, string $language): string
    {
        return "Generate SEO metadata for the following {$entityType} content in {$language}.

CONTENT:
{$content}

REQUIREMENTS:
1. meta_title: Maximum 60 characters, include main keyword, compelling and click-worthy
2. meta_description: Maximum 155 characters, include call-to-action, summarize value proposition
3. og_title: Can be slightly longer than meta_title, more descriptive
4. og_description: Maximum 200 characters, engaging for social sharing
5. keywords: 5-7 relevant keywords, comma-separated

RULES:
- Write in {$language}
- Keep brand name 'We're Sinapsis' or 'Sinapsis' unchanged
- Focus on user benefits and value
- Use action words when appropriate
- Avoid keyword stuffing
- Make it natural and readable

RESPOND WITH VALID JSON ONLY:
{
    \"meta_title\": \"...\",
    \"meta_description\": \"...\",
    \"og_title\": \"...\",
    \"og_description\": \"...\",
    \"keywords\": \"...\"
}";
    }

    /**
     * Get schema type for entity
     */
    private function getSchemaTypeForEntity(string $entityType): string
    {
        $schemaTypes = [
            'page' => 'WebPage',
            'post' => 'Article',
            'success_case' => 'Article',
            'feature' => 'Product',
            'partner' => 'Organization',
            'landing' => 'WebPage',
            'knowledge' => 'Article',
            'integration' => 'SoftwareApplication',
            'service' => 'Service',
            'tool' => 'SoftwareApplication',
            'client' => 'Organization',
            'category' => 'CollectionPage'
        ];

        return $schemaTypes[$entityType] ?? 'WebPage';
    }

    /**
     * Translate existing SEO metadata to other languages
     */
    public function translateSEO(string $entityType, int $entityId, string $targetLanguage): array
    {
        // Get Spanish SEO as source
        $sourceSeo = $this->seoModel->getForEntity($entityType, $entityId, 'es');

        if (!$sourceSeo) {
            return [
                'success' => false,
                'message' => 'Source SEO (Spanish) not found'
            ];
        }

        // Use TranslationService to translate
        $translationService = new TranslationService();

        $fieldsToTranslate = [
            'meta_title' => $sourceSeo['meta_title'],
            'meta_description' => $sourceSeo['meta_description'],
            'og_title' => $sourceSeo['og_title'],
            'og_description' => $sourceSeo['og_description'],
            'keywords' => $sourceSeo['keywords']
        ];

        $translatedFields = [];
        foreach ($fieldsToTranslate as $field => $value) {
            if (!empty($value)) {
                $translated = $translationService->translateText($value, $targetLanguage, 'es');
                $translatedFields[$field] = $translated ?? $value;
            }
        }

        // Save translated SEO
        $seoId = $this->seoModel->saveForEntity($entityType, $entityId, $targetLanguage, array_merge($translatedFields, [
            'schema_type' => $sourceSeo['schema_type'],
            'is_auto_generated' => 1,
            'generation_source' => 'gpt',
            'generated_at' => date('Y-m-d H:i:s')
        ]));

        return [
            'success' => true,
            'message' => 'SEO translated successfully',
            'data' => $translatedFields
        ];
    }

    /**
     * Get SEO audit log
     */
    public function getAuditLog(int $limit = 100): array
    {
        try {
            $db = \App\Core\Database::getInstance()->getConnection();

            $sql = "SELECT l.*, u.name as user_name
                    FROM seo_audit_log l
                    LEFT JOIN users u ON u.id = l.user_id
                    ORDER BY l.created_at DESC
                    LIMIT ?";

            $stmt = $db->prepare($sql);
            $stmt->execute([$limit]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("SEO Audit Log Query Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate robots.txt content
     */
    public function generateRobotsTxt(): string
    {
        $settingModel = new Setting();
        $customRobots = $settingModel->get('robots_txt_content');

        if (!empty($customRobots)) {
            return $customRobots;
        }

        // Default robots.txt
        return "User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /assets/
Disallow: /*.json$
Disallow: /*?*

# Sitemap
Sitemap: https://weresinapsis.com/sitemap.xml

# Crawl-delay (optional, for politeness)
Crawl-delay: 1";
    }

    /**
     * Save robots.txt to file
     */
    public function saveRobotsTxt(string $content): bool
    {
        $path = __DIR__ . '/../../public/robots.txt';
        return file_put_contents($path, $content) !== false;
    }
}
