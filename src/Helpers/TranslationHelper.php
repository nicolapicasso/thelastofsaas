<?php
/**
 * Translation Helper
 * Provides easy access to translations in views and controllers
 * Omniwallet CMS
 */

namespace App\Helpers {

    use App\Models\Translation;

    class TranslationHelper
    {
        private static ?TranslationHelper $instance = null;
        private Translation $translationModel;
        private string $currentLanguage;
        private string $defaultLanguage = 'es';
        private array $cache = [];
        private array $strings = [];
        private bool $stringsLoaded = false;

        private function __construct()
        {
            $this->translationModel = new Translation();
            $this->currentLanguage = $_SESSION['lang'] ?? $this->defaultLanguage;
        }

        public static function getInstance(): TranslationHelper
        {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Set the current language
         */
        public function setLanguage(string $lang): void
        {
            $this->currentLanguage = $lang;
            $_SESSION['lang'] = $lang;
            // Reset strings cache when language changes
            $this->stringsLoaded = false;
            $this->strings = [];
        }

        /**
         * Get current language
         */
        public function getLanguage(): string
        {
            return $this->currentLanguage;
        }

        /**
         * Load language strings from file
         */
        private function loadStrings(): void
        {
            if ($this->stringsLoaded) {
                return;
            }

            $langFile = dirname(__DIR__, 2) . '/lang/' . $this->currentLanguage . '.php';

            if (file_exists($langFile)) {
                $this->strings = require $langFile;
            } else {
                // Fallback to default language
                $defaultFile = dirname(__DIR__, 2) . '/lang/' . $this->defaultLanguage . '.php';
                if (file_exists($defaultFile)) {
                    $this->strings = require $defaultFile;
                }
            }

            $this->stringsLoaded = true;
        }

        /**
         * Get a static UI translation string
         * Usage: __('features_title') or __('category')
         *
         * @param string $key The translation key
         * @param array $params Optional replacement parameters for sprintf
         * @return string The translated string or the key if not found
         */
        public function text(string $key, array $params = []): string
        {
            $this->loadStrings();

            $text = $this->strings[$key] ?? $key;

            if (!empty($params)) {
                $text = sprintf($text, ...$params);
            }

            return $text;
        }

        /**
         * Get translation for an entity field
         * Returns original content if no translation exists or if current language is default
         */
        public function get(string $entityType, int $entityId, string $field, string $originalContent): string
        {
            // If current language is default, return original
            if ($this->currentLanguage === $this->defaultLanguage) {
                return $originalContent;
            }

            // Check cache first
            $cacheKey = "{$entityType}_{$entityId}_{$field}_{$this->currentLanguage}";
            if (isset($this->cache[$cacheKey])) {
                return $this->cache[$cacheKey];
            }

            // Try to get approved translation
            $translation = $this->translationModel->getApprovedTranslation(
                $entityType,
                $entityId,
                $field,
                $this->currentLanguage
            );

            if ($translation) {
                $this->cache[$cacheKey] = $translation;
                return $translation;
            }

            // No translation, return original
            return $originalContent;
        }

        /**
         * Get all translations for an entity
         * Returns array with translated fields, falls back to originals
         */
        public function getEntityTranslations(string $entityType, int $entityId, array $originalFields): array
        {
            // If current language is default, return originals
            if ($this->currentLanguage === $this->defaultLanguage) {
                return $originalFields;
            }

            $result = $originalFields;

            foreach ($originalFields as $field => $originalContent) {
                if (is_string($originalContent)) {
                    $result[$field] = $this->get($entityType, $entityId, $field, $originalContent);
                }
            }

            return $result;
        }

        /**
         * Translate an entity array in place
         * Modifies common fields: title, description, content, excerpt, etc.
         */
        public function translateEntity(string $entityType, array &$entity): void
        {
            if ($this->currentLanguage === $this->defaultLanguage || empty($entity['id'])) {
                return;
            }

            $translatableFields = [
                'title', 'subtitle', 'name', 'company_name', 'description', 'short_description',
                'full_description', 'content', 'excerpt', 'question', 'answer',
                'meta_title', 'meta_description', 'challenge', 'solution', 'results', 'testimonial',
                'testimonial_author', 'testimonial_role', 'theme_title', 'category_name', 'industry'
            ];

            foreach ($translatableFields as $field) {
                if (isset($entity[$field]) && is_string($entity[$field])) {
                    $entity[$field] = $this->get($entityType, $entity['id'], $field, $entity[$field]);
                }
            }
        }

        /**
         * Translate an array of entities
         */
        public function translateEntities(string $entityType, array &$entities): void
        {
            foreach ($entities as &$entity) {
                // Only translate if it's a valid entity array with an ID
                if (is_array($entity) && !empty($entity['id'])) {
                    $this->translateEntity($entityType, $entity);
                }
            }
        }

        /**
         * Translate block content (JSON)
         * Handles nested content structure in page blocks
         */
        public function translateBlockContent(int $blockId, array $content): array
        {
            if ($this->currentLanguage === $this->defaultLanguage) {
                return $content;
            }

            return $this->translateBlockContentRecursive($blockId, $content, '');
        }

        /**
         * Recursively translate block content
         */
        private function translateBlockContentRecursive(int $blockId, array $content, string $prefix): array
        {
            $translatableKeys = [
                'title', 'subtitle', 'description', 'text', 'content', 'cta_text',
                'link_text', 'button_text', 'label', 'placeholder', 'heading',
                'subheading', 'caption', 'quote', 'author', 'name', 'message',
                'success_title', 'success_message', 'submit_text', 'more_text',
                'price_suffix', 'badge_text', 'empty_text', 'helper_text'
            ];

            // Keys that contain arrays of translatable strings
            $translatableArrayKeys = ['features', 'items', 'benefits', 'bullet_points'];

            $result = $content;

            foreach ($content as $key => $value) {
                $fieldPath = $prefix ? "{$prefix}.{$key}" : $key;

                if (is_array($value)) {
                    // Check if it's an indexed array
                    if (isset($value[0])) {
                        // Check if it's an array of simple strings (like features)
                        if (in_array($key, $translatableArrayKeys) && is_string($value[0])) {
                            foreach ($value as $index => $item) {
                                if (is_string($item) && !empty($item)) {
                                    $result[$key][$index] = $this->get('block', $blockId, "{$fieldPath}.{$index}", $item);
                                }
                            }
                        } else {
                            // Array of objects (like slides, plans, etc.)
                            foreach ($value as $index => $item) {
                                if (is_array($item)) {
                                    $result[$key][$index] = $this->translateBlockContentRecursive(
                                        $blockId,
                                        $item,
                                        "{$fieldPath}.{$index}"
                                    );
                                }
                            }
                        }
                    } else {
                        // Associative array, recurse
                        $result[$key] = $this->translateBlockContentRecursive($blockId, $value, $fieldPath);
                    }
                } elseif (is_string($value) && !empty($value) && in_array($key, $translatableKeys)) {
                    // Translate this field
                    $result[$key] = $this->get('block', $blockId, $fieldPath, $value);
                }
            }

            return $result;
        }

        /**
         * Translate LLM Q&A content
         * Translates question/answer pairs stored in llm_qa_content JSON field
         *
         * @param string $entityType Type of entity (page, post, feature, etc.)
         * @param int $entityId The entity ID
         * @param array $qaItems Array of Q&A items with 'question' and 'answer' keys
         * @return array Translated Q&A items
         */
        public function translateLlmQaContent(string $entityType, int $entityId, array $qaItems): array
        {
            if ($this->currentLanguage === $this->defaultLanguage || empty($qaItems)) {
                return $qaItems;
            }

            $translated = [];
            foreach ($qaItems as $index => $qa) {
                $translatedQa = [];

                if (!empty($qa['question'])) {
                    $translatedQa['question'] = $this->get(
                        $entityType,
                        $entityId,
                        "llm_qa.{$index}.question",
                        $qa['question']
                    );
                }

                if (!empty($qa['answer'])) {
                    $translatedQa['answer'] = $this->get(
                        $entityType,
                        $entityId,
                        "llm_qa.{$index}.answer",
                        $qa['answer']
                    );
                }

                $translated[] = $translatedQa;
            }

            return $translated;
        }

        /**
         * Get available languages with info
         */
        public function getAvailableLanguages(): array
        {
            return [
                'es' => ['code' => 'es', 'name' => 'Español', 'flag' => '🇪🇸', 'native' => 'Español'],
                'en' => ['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'native' => 'English'],
                'it' => ['code' => 'it', 'name' => 'Italiano', 'flag' => '🇮🇹', 'native' => 'Italiano'],
                'fr' => ['code' => 'fr', 'name' => 'Français', 'flag' => '🇫🇷', 'native' => 'Français'],
                'de' => ['code' => 'de', 'name' => 'Deutsch', 'flag' => '🇩🇪', 'native' => 'Deutsch'],
            ];
        }

        /**
         * Clear the translation cache
         */
        public function clearCache(): void
        {
            $this->cache = [];
        }
    }
}

// Global namespace for helper functions
namespace {
    use App\Helpers\TranslationHelper;

    /**
     * Global helper function for entity translations in views
     * Usage: __t('post', 1, 'title', 'Original title')
     */
    if (!function_exists('__t')) {
        function __t(string $entityType, int $entityId, string $field, string $originalContent): string
        {
            return TranslationHelper::getInstance()->get($entityType, $entityId, $field, $originalContent);
        }
    }

    /**
     * Global helper function for static UI strings
     * Usage: __('features_title') or __('category')
     */
    if (!function_exists('__')) {
        function __(string $key, array $params = []): string
        {
            return TranslationHelper::getInstance()->text($key, $params);
        }
    }

    /**
     * Global helper function for language-prefixed URLs
     * Usage: _url('/funcionalidades') => '/en/funcionalidades' if current lang is English
     */
    if (!function_exists('_url')) {
        function _url(string $path, ?string $language = null): string
        {
            $language = $language ?? ($_SESSION['lang'] ?? 'es');

            // Spanish (default) doesn't need prefix
            if ($language === 'es') {
                return $path;
            }

            return '/' . $language . $path;
        }
    }

    /**
     * Global helper function for generating full URLs
     * Usage: url('/empresa/login') => 'https://example.com/empresa/login'
     */
    if (!function_exists('url')) {
        function url(string $path = ''): string
        {
            $baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');

            if (empty($baseUrl)) {
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $baseUrl = $protocol . '://' . $host;
            }

            if (empty($path)) {
                return $baseUrl;
            }

            return $baseUrl . '/' . ltrim($path, '/');
        }
    }
}
