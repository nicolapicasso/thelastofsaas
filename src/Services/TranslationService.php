<?php
/**
 * Translation Service
 * Integrates with OpenAI API for automatic translations
 * Omniwallet CMS
 */

namespace App\Services;

use App\Models\Translation;
use App\Models\Setting;

class TranslationService
{
    private Translation $translationModel;
    private ?string $apiKey;
    private string $model;
    private array $config;
    private string $logFile;
    private array $sessionErrors = [];

    public function __construct()
    {
        $this->translationModel = new Translation();
        $this->config = require __DIR__ . '/../../config/translations.php';
        $this->logFile = __DIR__ . '/../../logs/translations.log';

        // Ensure logs directory exists
        $logsDir = dirname($this->logFile);
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0755, true);
        }

        // Try to get API key from database first (admin settings), then fall back to .env
        $settingModel = new Setting();
        $dbApiKey = $settingModel->get('openai_api_key');

        $this->apiKey = !empty($dbApiKey)
            ? $dbApiKey
            : ($this->config['openai']['api_key'] ?? null);

        $this->model = $this->config['openai']['model'] ?? 'gpt-4o-mini';
    }

    /**
     * Log translation error
     */
    private function logError(string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logLine = "[{$timestamp}] ERROR: {$message}{$contextStr}\n";

        @file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);

        // Also store in session for immediate feedback
        $this->sessionErrors[] = [
            'time' => $timestamp,
            'message' => $message,
            'context' => $context
        ];
    }

    /**
     * Get recent translation errors from log file
     */
    public function getRecentErrors(int $limit = 50): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_slice($lines, -$limit);
        return array_reverse($lines);
    }

    /**
     * Get session errors (errors from current translation batch)
     */
    public function getSessionErrors(): array
    {
        return $this->sessionErrors;
    }

    /**
     * Clear session errors
     */
    public function clearSessionErrors(): void
    {
        $this->sessionErrors = [];
    }

    /**
     * Clear translation log file
     */
    public function clearLog(): bool
    {
        if (file_exists($this->logFile)) {
            return @unlink($this->logFile);
        }
        return true;
    }

    /**
     * Maximum content length for translation (characters)
     * Content larger than this will be skipped to avoid timeouts
     * Testing shows content >10k chars consistently times out with OpenAI
     */
    private const MAX_CONTENT_LENGTH = 10000;

    /**
     * Translate text using OpenAI API
     */
    public function translateText(string $text, string $targetLanguage, string $sourceLanguage = 'es'): ?string
    {
        if (empty($text) || empty($this->apiKey)) {
            return null;
        }

        // Skip very large content to avoid timeouts
        $textLength = strlen($text);
        if ($textLength > self::MAX_CONTENT_LENGTH) {
            $this->logError("Content too large, skipping translation", [
                'target_language' => $targetLanguage,
                'content_length' => $textLength,
                'max_allowed' => self::MAX_CONTENT_LENGTH,
                'text_preview' => substr($text, 0, 100)
            ]);
            return null;
        }

        $languageNames = Translation::LANGUAGES;
        $sourceLangName = $languageNames[$sourceLanguage] ?? $sourceLanguage;
        $targetLangName = $languageNames[$targetLanguage] ?? $targetLanguage;

        $prompt = $this->buildPrompt($text, $sourceLangName, $targetLangName);

        try {
            $response = $this->callOpenAI($prompt);
            return $response;
        } catch (\Exception $e) {
            $this->logError("Translation API error: " . $e->getMessage(), [
                'target_language' => $targetLanguage,
                'content_length' => $textLength,
                'text_preview' => substr($text, 0, 100)
            ]);
            return null;
        }
    }

    /**
     * Build translation prompt
     */
    private function buildPrompt(string $text, string $sourceLang, string $targetLang): string
    {
        return "You are a professional translator for Omniwallet, a fintech company that provides digital wallet solutions for businesses.

Translate the following text from {$sourceLang} to {$targetLang}.

Rules:
- Maintain the same tone and style
- Keep technical terms consistent with fintech industry standards
- Preserve any HTML tags, markdown formatting, or special characters
- Keep brand names unchanged (Omniwallet, etc.)
- If the text contains JSON, only translate the string values
- Do not add any explanations, just return the translated text

Text to translate:
{$text}";
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI(string $prompt): ?string
    {
        $url = 'https://api.openai.com/v1/chat/completions';

        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional translator. Return only the translated text without any additional commentary.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.3,
            'max_tokens' => 4000
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => 90,
            CURLOPT_CONNECTTIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        // Check for cURL errors first
        if ($curlErrno !== 0) {
            throw new \Exception("cURL error ({$curlErrno}): {$curlError}");
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? "HTTP {$httpCode}";
            throw new \Exception("OpenAI API error: {$errorMsg}");
        }

        $result = json_decode($response, true);

        if (isset($result['choices'][0]['message']['content'])) {
            return trim($result['choices'][0]['message']['content']);
        }

        return null;
    }

    /**
     * Translate entity fields
     */
    public function translateEntity(
        string $entityType,
        int $entityId,
        array $fields,
        string $targetLanguage,
        string $sourceLanguage = 'es'
    ): array {
        $results = [];

        foreach ($fields as $fieldName => $originalContent) {
            if (empty($originalContent)) {
                continue;
            }

            // Check if translation already exists and is approved
            $existing = $this->translationModel->getTranslation(
                $entityType,
                $entityId,
                $fieldName,
                $targetLanguage
            );

            if ($existing) {
                $results[$fieldName] = [
                    'status' => 'exists',
                    'content' => $existing
                ];
                continue;
            }

            // Translate
            $translated = $this->translateText($originalContent, $targetLanguage, $sourceLanguage);

            if ($translated) {
                $this->translationModel->saveTranslation(
                    $entityType,
                    $entityId,
                    $fieldName,
                    $targetLanguage,
                    $originalContent,
                    $translated,
                    true, // is_auto_translated
                    false // is_approved
                );

                $results[$fieldName] = [
                    'status' => 'translated',
                    'content' => $translated
                ];
            } else {
                $results[$fieldName] = [
                    'status' => 'error',
                    'content' => null
                ];
            }
        }

        return $results;
    }

    /**
     * Get entities for translation (public wrapper)
     */
    public function getEntitiesForTranslation(string $targetLanguage): array
    {
        return $this->getEntitiesNeedingTranslation($targetLanguage);
    }

    /**
     * Translate a batch of entities (for AJAX processing)
     */
    public function translateBatch(string $targetLanguage, int $offset = 0, int $batchSize = 5): array
    {
        $stats = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'processed' => 0,
            'hasMore' => false
        ];

        // Get all entities
        $allEntities = $this->getEntitiesNeedingTranslation($targetLanguage);
        $totalEntities = count($allEntities);

        // Get the batch
        $batch = array_slice($allEntities, $offset, $batchSize);
        $stats['processed'] = count($batch);
        $stats['hasMore'] = ($offset + $batchSize) < $totalEntities;
        $stats['total'] = $totalEntities;
        $stats['nextOffset'] = $offset + $batchSize;

        foreach ($batch as $entity) {
            try {
                $result = $this->translateEntity(
                    $entity['type'],
                    $entity['id'],
                    $entity['fields'],
                    $targetLanguage
                );

                foreach ($result as $field => $data) {
                    if ($data['status'] === 'translated') {
                        $stats['success']++;
                    } elseif ($data['status'] === 'exists') {
                        $stats['skipped']++;
                    } else {
                        $stats['failed']++;
                        $this->logError("Field translation failed", [
                            'entity_type' => $entity['type'],
                            'entity_id' => $entity['id'],
                            'field' => $field,
                            'target_language' => $targetLanguage
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $stats['failed']++;
                $this->logError("Translation batch error: " . $e->getMessage(), [
                    'entity_type' => $entity['type'],
                    'entity_id' => $entity['id'],
                    'target_language' => $targetLanguage
                ]);
            }
        }

        // Add errors to stats for immediate feedback
        $stats['errors'] = $this->sessionErrors;

        return $stats;
    }

    /**
     * Translate all pending content for a language
     */
    public function translateAllPending(string $targetLanguage): array
    {
        $stats = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0
        ];

        // Get entities that need translation
        $entities = $this->getEntitiesNeedingTranslation($targetLanguage);

        foreach ($entities as $entity) {
            try {
                $result = $this->translateEntity(
                    $entity['type'],
                    $entity['id'],
                    $entity['fields'],
                    $targetLanguage
                );

                foreach ($result as $field => $data) {
                    if ($data['status'] === 'translated') {
                        $stats['success']++;
                    } elseif ($data['status'] === 'exists') {
                        $stats['skipped']++;
                    } else {
                        $stats['failed']++;
                    }
                }
            } catch (\Exception $e) {
                $stats['failed']++;
                error_log("Translation batch error: " . $e->getMessage());
            }
        }

        return $stats;
    }

    /**
     * Get entities that need translation for a language
     */
    private function getEntitiesNeedingTranslation(string $targetLanguage): array
    {
        $entities = [];
        $db = \App\Core\Database::getInstance()->getConnection();

        // Pages
        try {
            $stmt = $db->query("SELECT id, title, meta_title, meta_description, llm_qa_content FROM pages WHERE status = 'published'");
            foreach ($stmt->fetchAll() as $row) {
                $fields = [
                    'title' => $row['title'],
                    'meta_title' => $row['meta_title'],
                    'meta_description' => $row['meta_description']
                ];
                // Add LLM Q&A fields if present
                $llmFields = $this->extractLlmQaFields($row['llm_qa_content'] ?? null, 'page', $row['id']);
                $fields = array_merge($fields, $llmFields);

                $entities[] = [
                    'type' => 'page',
                    'id' => $row['id'],
                    'fields' => $fields
                ];
            }
        } catch (\Exception $e) {
            $this->logError("Error loading pages for translation: " . $e->getMessage());
        }

        // Posts
        try {
            $stmt = $db->query("SELECT id, title, subtitle, excerpt, content, meta_title, meta_description, llm_qa_content FROM posts WHERE status = 'published'");
            foreach ($stmt->fetchAll() as $row) {
                $fields = [
                    'title' => $row['title'],
                    'subtitle' => $row['subtitle'],
                    'excerpt' => $row['excerpt'],
                    'content' => $row['content'],
                    'meta_title' => $row['meta_title'],
                    'meta_description' => $row['meta_description']
                ];
                // Add LLM Q&A fields if present
                $llmFields = $this->extractLlmQaFields($row['llm_qa_content'] ?? null, 'post', $row['id']);
                $fields = array_merge($fields, $llmFields);

                $entities[] = [
                    'type' => 'post',
                    'id' => $row['id'],
                    'fields' => $fields
                ];
            }
        } catch (\Exception $e) {
            $this->logError("Error loading posts for translation: " . $e->getMessage());
        }

        // Features
        try {
            $stmt = $db->query("SELECT id, title, short_description, full_description, llm_qa_content FROM features WHERE is_active = 1");
            foreach ($stmt->fetchAll() as $row) {
                $fields = [
                    'title' => $row['title'],
                    'short_description' => $row['short_description'],
                    'full_description' => $row['full_description']
                ];
                // Add LLM Q&A fields if present
                $llmFields = $this->extractLlmQaFields($row['llm_qa_content'] ?? null, 'feature', $row['id']);
                $fields = array_merge($fields, $llmFields);

                $entities[] = [
                    'type' => 'feature',
                    'id' => $row['id'],
                    'fields' => $fields
                ];
            }
        } catch (\Exception $e) {
            $this->logError("Error loading features for translation: " . $e->getMessage());
        }

        // FAQs
        try {
            $stmt = $db->query("SELECT id, question, answer FROM faqs WHERE is_active = 1");
            foreach ($stmt->fetchAll() as $row) {
                $entities[] = [
                    'type' => 'faq',
                    'id' => $row['id'],
                    'fields' => [
                        'question' => $row['question'],
                        'answer' => $row['answer']
                    ]
                ];
            }
        } catch (\Exception $e) {
            $this->logError("Error loading faqs for translation: " . $e->getMessage());
        }

        // Success Cases
        try {
            // First check what columns exist
            $columns = $db->query("SHOW COLUMNS FROM success_cases")->fetchAll(\PDO::FETCH_COLUMN);
            $availableColumns = ['id'];

            // Only select columns that exist
            $wantedColumns = ['company_name', 'short_description', 'challenge', 'solution', 'results', 'testimonial', 'meta_title', 'meta_description', 'llm_qa_content'];
            foreach ($wantedColumns as $col) {
                if (in_array($col, $columns)) {
                    $availableColumns[] = $col;
                }
            }

            $columnList = implode(', ', $availableColumns);
            $stmt = $db->query("SELECT {$columnList} FROM success_cases WHERE status = 'published'");
            $rows = $stmt->fetchAll();

            foreach ($rows as $row) {
                $fields = [];
                foreach (['company_name', 'short_description', 'challenge', 'solution', 'results', 'testimonial', 'meta_title', 'meta_description'] as $field) {
                    if (isset($row[$field]) && !empty($row[$field])) {
                        $fields[$field] = $row[$field];
                    }
                }

                // Add LLM Q&A fields if present
                if (!empty($row['llm_qa_content'])) {
                    $llmFields = $this->extractLlmQaFields($row['llm_qa_content'], 'success_case', $row['id']);
                    $fields = array_merge($fields, $llmFields);
                }

                if (!empty($fields)) {
                    $entities[] = [
                        'type' => 'success_case',
                        'id' => $row['id'],
                        'fields' => $fields
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->logError("Error loading success_cases for translation: " . $e->getMessage());
        }

        // Categories
        try {
            $stmt = $db->query("SELECT id, name, description FROM categories WHERE is_active = 1");
            foreach ($stmt->fetchAll() as $row) {
                $entities[] = [
                    'type' => 'category',
                    'id' => $row['id'],
                    'fields' => [
                        'name' => $row['name'],
                        'description' => $row['description']
                    ]
                ];
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Knowledge Articles
        try {
            $stmt = $db->query("SELECT id, title, excerpt, content, meta_title, meta_description, llm_qa_content FROM knowledge_articles WHERE status = 'published'");
            foreach ($stmt->fetchAll() as $row) {
                $fields = [
                    'title' => $row['title'],
                    'excerpt' => $row['excerpt'],
                    'content' => $row['content'],
                    'meta_title' => $row['meta_title'],
                    'meta_description' => $row['meta_description']
                ];
                // Add LLM Q&A fields if present
                $llmFields = $this->extractLlmQaFields($row['llm_qa_content'] ?? null, 'knowledge_article', $row['id']);
                $fields = array_merge($fields, $llmFields);

                $entities[] = [
                    'type' => 'knowledge_article',
                    'id' => $row['id'],
                    'fields' => $fields
                ];
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Page Blocks
        try {
            $stmt = $db->query("SELECT pb.id, pb.content, pb.type FROM page_blocks pb JOIN pages p ON p.id = pb.page_id WHERE pb.is_active = 1 AND p.status = 'published'");
            foreach ($stmt->fetchAll() as $row) {
                $content = json_decode($row['content'], true);
                if ($content) {
                    $fields = $this->extractBlockTranslatableFields($content, '');
                    if (!empty($fields)) {
                        $entities[] = [
                            'type' => 'block',
                            'id' => $row['id'],
                            'fields' => $fields
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Service Blocks (use different entity_type to avoid ID conflicts with page_blocks)
        try {
            $stmt = $db->query("SELECT sb.id, sb.content, sb.type FROM service_blocks sb JOIN services s ON s.id = sb.service_id WHERE sb.is_active = 1 AND s.is_active = 1");
            foreach ($stmt->fetchAll() as $row) {
                $content = json_decode($row['content'], true);
                if ($content) {
                    $fields = $this->extractBlockTranslatableFields($content, '');
                    if (!empty($fields)) {
                        $entities[] = [
                            'type' => 'service_block',
                            'id' => $row['id'],
                            'fields' => $fields
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Landings (admin-managed fields only, not HTML content)
        try {
            $stmt = $db->query("SELECT id, title, subtitle, description, meta_title, meta_description FROM landings WHERE is_active = 1");
            foreach ($stmt->fetchAll() as $row) {
                $entities[] = [
                    'type' => 'landing',
                    'id' => $row['id'],
                    'fields' => [
                        'title' => $row['title'],
                        'subtitle' => $row['subtitle'],
                        'description' => $row['description'],
                        'meta_title' => $row['meta_title'],
                        'meta_description' => $row['meta_description']
                    ]
                ];
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Landing Themes
        try {
            $stmt = $db->query("SELECT id, title, description FROM landing_themes WHERE is_active = 1");
            foreach ($stmt->fetchAll() as $row) {
                $entities[] = [
                    'type' => 'landing_theme',
                    'id' => $row['id'],
                    'fields' => [
                        'title' => $row['title'],
                        'description' => $row['description']
                    ]
                ];
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Integrations
        try {
            $stmt = $db->query("SELECT id, title, subtitle, description, llm_qa_content FROM integrations WHERE is_active = 1");
            foreach ($stmt->fetchAll() as $row) {
                $fields = [
                    'title' => $row['title'],
                    'subtitle' => $row['subtitle'],
                    'description' => $row['description']
                ];
                // Add LLM Q&A fields if present
                $llmFields = $this->extractLlmQaFields($row['llm_qa_content'] ?? null, 'integration', $row['id']);
                $fields = array_merge($fields, $llmFields);

                $entities[] = [
                    'type' => 'integration',
                    'id' => $row['id'],
                    'fields' => $fields
                ];
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Partners
        try {
            $stmt = $db->query("SELECT id, name, description, testimonial, testimonial_author, testimonial_role, meta_title, meta_description FROM partners WHERE is_active = 1");
            foreach ($stmt->fetchAll() as $row) {
                $fields = [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'testimonial' => $row['testimonial'],
                    'testimonial_author' => $row['testimonial_author'],
                    'testimonial_role' => $row['testimonial_role'],
                    'meta_title' => $row['meta_title'],
                    'meta_description' => $row['meta_description']
                ];

                $entities[] = [
                    'type' => 'partner',
                    'id' => $row['id'],
                    'fields' => $fields
                ];
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        return $entities;
    }

    /**
     * Extract translatable fields from LLM Q&A content
     * Converts JSON Q&A pairs into individual translatable fields
     */
    private function extractLlmQaFields(?string $llmQaContent, string $entityType, int $entityId): array
    {
        if (empty($llmQaContent)) {
            return [];
        }

        $qaItems = json_decode($llmQaContent, true);
        if (!is_array($qaItems) || empty($qaItems)) {
            return [];
        }

        $fields = [];
        foreach ($qaItems as $index => $qa) {
            if (!empty($qa['question'])) {
                $fields["llm_qa.{$index}.question"] = $qa['question'];
            }
            if (!empty($qa['answer'])) {
                $fields["llm_qa.{$index}.answer"] = $qa['answer'];
            }
        }

        return $fields;
    }

    /**
     * Extract translatable fields from block content recursively
     */
    private function extractBlockTranslatableFields(array $content, string $prefix): array
    {
        $translatableKeys = [
            'title', 'subtitle', 'description', 'text', 'content', 'cta_text',
            'link_text', 'button_text', 'label', 'placeholder', 'heading',
            'subheading', 'caption', 'quote', 'author', 'name', 'message',
            'success_title', 'success_message', 'submit_text', 'more_text',
            'price_suffix', 'badge_text', 'empty_text', 'helper_text'
        ];

        // Keys that contain arrays of translatable strings (like features list)
        $translatableArrayKeys = ['features', 'items', 'benefits', 'bullet_points'];

        $fields = [];

        foreach ($content as $key => $value) {
            $fieldPath = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                // Check if it's an array of strings (like features)
                if (in_array($key, $translatableArrayKeys) && isset($value[0]) && is_string($value[0])) {
                    foreach ($value as $index => $item) {
                        if (is_string($item) && !empty($item)) {
                            $fields["{$fieldPath}.{$index}"] = $item;
                        }
                    }
                }
                // Check if it's an indexed array of objects (like slides, items, plans, etc.)
                elseif (isset($value[0]) && is_array($value[0])) {
                    foreach ($value as $index => $item) {
                        if (is_array($item)) {
                            $nestedFields = $this->extractBlockTranslatableFields($item, "{$fieldPath}.{$index}");
                            $fields = array_merge($fields, $nestedFields);
                        }
                    }
                }
                // Associative array, recurse
                elseif (!isset($value[0])) {
                    $nestedFields = $this->extractBlockTranslatableFields($value, $fieldPath);
                    $fields = array_merge($fields, $nestedFields);
                }
            } elseif (is_string($value) && !empty($value) && in_array($key, $translatableKeys)) {
                // This is a translatable field
                $fields[$fieldPath] = $value;
            }
        }

        return $fields;
    }

    /**
     * Translate complete HTML landing page
     * Uses specialized prompt to preserve HTML structure while translating visible text
     */
    public function translateHtmlLanding(string $htmlContent, string $targetLanguage): ?string
    {
        if (empty($htmlContent) || empty($this->apiKey)) {
            return null;
        }

        $languageNames = Translation::LANGUAGES;
        $targetLangName = $languageNames[$targetLanguage] ?? $targetLanguage;

        $prompt = $this->buildHtmlLandingPrompt($htmlContent, $targetLangName, $targetLanguage);

        try {
            // Use a larger model for HTML translation due to complexity
            $response = $this->callOpenAIForHtml($prompt);
            return $response;
        } catch (\Exception $e) {
            $this->logError("HTML Landing translation error: " . $e->getMessage(), [
                'target_language' => $targetLanguage,
                'html_size' => strlen($htmlContent)
            ]);
            throw $e; // Re-throw for controller to handle
        }
    }

    /**
     * Build specialized prompt for HTML landing translation
     */
    private function buildHtmlLandingPrompt(string $html, string $targetLangName, string $targetLangCode): string
    {
        return "Necesito que traduzcas el siguiente HTML completo al idioma {$targetLangName}.

REGLAS OBLIGATORIAS (MUY IMPORTANTE):

1. NO modifiques absolutamente nada de:
   - Estructura HTML
   - Etiquetas, atributos, IDs, clases
   - CSS, JavaScript ni SVG
   - URLs, endpoints, rutas, nombres de variables
   - Contenido dentro de <style> y <script>

2. SOLO debes traducir:
   - Textos visibles para el usuario
   - Contenido de títulos, párrafos, botones, labels y textos descriptivos
   - El atributo lang del <html> (cámbialo a \"{$targetLangCode}\")
   - El contenido del <title>

3. RESTRICCIONES DE CARACTERES (CRÍTICO):
   - Usa EXCLUSIVAMENTE comillas rectas: ' y \"
   - NO uses comillas tipográficas (comillas curvas o inglesas)
   - NO uses caracteres especiales que puedan romper encoding
   - Evita emojis
   - Evita símbolos raros o Unicode innecesario

4. SEGURIDAD DE MAQUETACIÓN:
   - NO introduzcas saltos de línea nuevos dentro de etiquetas
   - NO rompas bloques <code>, <pre> o ejemplos técnicos
   - Mantén el mismo número de elementos HTML
   - Mantén exactamente el mismo orden de nodos

5. CALIDAD DE LA TRADUCCIÓN:
   - Traducción profesional, natural y técnica
   - Vocabulario SaaS / técnico
   - Tono corporativo, claro y preciso
   - NO traduzcas nombres de producto, marca o tecnología (Omniwallet, API, REST, JSON:API, Webhooks, etc.)

6. ENTREGA:
   - Devuelve el HTML COMPLETO
   - Sin explicaciones ni comentarios adicionales
   - Listo para usar directamente

HTML a traducir:

{$html}";
    }

    /**
     * Call OpenAI API specifically for HTML translation (uses larger context)
     */
    private function callOpenAIForHtml(string $prompt): ?string
    {
        $url = 'https://api.openai.com/v1/chat/completions';

        // For HTML translation, we might need more tokens
        // Use gpt-4o for better context and quality
        $model = 'gpt-4o';

        $data = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional HTML translator. You translate ONLY the visible text content in HTML documents while preserving ALL HTML structure, CSS, JavaScript, and technical attributes exactly as they are. Return ONLY the translated HTML without any additional text, explanations, or markdown code blocks.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.2,
            'max_tokens' => 16000
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => 120 // Longer timeout for larger HTML
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \Exception("cURL error: {$curlError}");
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? "HTTP {$httpCode}";
            throw new \Exception("OpenAI API error: {$errorMsg}");
        }

        $result = json_decode($response, true);

        if (isset($result['choices'][0]['message']['content'])) {
            $translated = trim($result['choices'][0]['message']['content']);

            // Clean up potential markdown code blocks if GPT added them
            $translated = preg_replace('/^```html?\s*/i', '', $translated);
            $translated = preg_replace('/\s*```$/', '', $translated);

            return $translated;
        }

        return null;
    }

    /**
     * Check if API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Get supported languages
     */
    public function getSupportedLanguages(): array
    {
        return Translation::LANGUAGES;
    }
}
