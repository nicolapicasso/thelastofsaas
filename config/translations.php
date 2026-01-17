<?php

declare(strict_types=1);

/**
 * Translation Configuration
 * Omniwallet CMS
 */

return [
    'openai' => [
        'api_key' => $_ENV['OPENAI_API_KEY'] ?? '',
        'model' => $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini',
        'max_tokens' => 4000,
        'temperature' => 0.3,
    ],

    'languages' => [
        'es' => [
            'name' => 'Español',
            'native_name' => 'Español',
            'flag' => '🇪🇸',
            'is_default' => true,
        ],
        'en' => [
            'name' => 'Inglés',
            'native_name' => 'English',
            'flag' => '🇬🇧',
        ],
        'it' => [
            'name' => 'Italiano',
            'native_name' => 'Italiano',
            'flag' => '🇮🇹',
        ],
        'fr' => [
            'name' => 'Francés',
            'native_name' => 'Français',
            'flag' => '🇫🇷',
        ],
        'de' => [
            'name' => 'Alemán',
            'native_name' => 'Deutsch',
            'flag' => '🇩🇪',
        ],
    ],

    'translatable_fields' => [
        'page' => ['title', 'meta_title', 'meta_description', 'llm_qa_content'],
        'post' => ['title', 'subtitle', 'excerpt', 'content', 'meta_title', 'meta_description'],
        'feature' => ['title', 'short_description', 'full_description'],
        'faq' => ['question', 'answer'],
        'success_case' => ['title', 'short_description', 'full_description', 'meta_title', 'meta_description'],
        'knowledge_article' => ['title', 'excerpt', 'content', 'meta_title', 'meta_description'],
        'category' => ['name', 'description'],
    ],

    'rate_limit' => [
        'max_requests_per_minute' => 50,
        'batch_size' => 10,
    ],
];
