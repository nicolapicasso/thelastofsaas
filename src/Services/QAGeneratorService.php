<?php
/**
 * Q&A Generator Service
 * Uses OpenAI GPT to generate Q&A content optimized for LLM indexing
 * We're Sinapsis CMS
 */

namespace App\Services;

use App\Models\Setting;

class QAGeneratorService
{
    private ?string $apiKey;
    private string $model;
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/translations.php';

        // Try to get API key from database first (admin settings), then fall back to .env
        $settingModel = new Setting();
        $dbApiKey = $settingModel->get('openai_api_key');

        $this->apiKey = !empty($dbApiKey)
            ? $dbApiKey
            : ($this->config['openai']['api_key'] ?? null);

        $this->model = $this->config['openai']['model'] ?? 'gpt-4o-mini';
    }

    /**
     * Generate Q&A content from page/post content
     *
     * @param string $title The title of the content
     * @param string $content The main content (HTML allowed)
     * @param string $entityType Type of entity (page, post, success_case, etc.)
     * @param int $numQuestions Number of Q&A pairs to generate (3-5)
     * @return array|null Array of Q&A pairs or null on failure
     */
    public function generateQA(string $title, string $content, string $entityType = 'page', int $numQuestions = 4): ?array
    {
        if (empty($this->apiKey)) {
            error_log("QAGeneratorService: OpenAI API key not configured");
            return null;
        }

        // Strip HTML tags and clean content
        $cleanContent = $this->cleanContent($content);

        if (strlen($cleanContent) < 100) {
            error_log("QAGeneratorService: Content too short to generate meaningful Q&A");
            return null;
        }

        // Truncate content if too long (GPT has token limits)
        if (strlen($cleanContent) > 8000) {
            $cleanContent = substr($cleanContent, 0, 8000) . '...';
        }

        $numQuestions = max(3, min(5, $numQuestions));

        $prompt = $this->buildPrompt($title, $cleanContent, $entityType, $numQuestions);

        try {
            $response = $this->callOpenAI($prompt);
            return $this->parseResponse($response);
        } catch (\Exception $e) {
            error_log("QAGeneratorService error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Clean HTML content for processing
     */
    private function cleanContent(string $content): string
    {
        // Remove scripts and styles
        $content = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $content);
        $content = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $content);

        // Strip HTML tags
        $content = strip_tags($content);

        // Decode HTML entities
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize whitespace
        $content = preg_replace('/\s+/', ' ', $content);

        return trim($content);
    }

    /**
     * Build the GPT prompt
     */
    private function buildPrompt(string $title, string $content, string $entityType, int $numQuestions): string
    {
        $entityTypeSpanish = [
            'page' => 'página web',
            'post' => 'artículo de blog',
            'success_case' => 'caso de éxito',
            'integration' => 'integración',
            'feature' => 'funcionalidad',
            'knowledge_article' => 'artículo de ayuda',
            'category' => 'categoría de contenidos'
        ];

        $typeLabel = $entityTypeSpanish[$entityType] ?? 'contenido web';

        return "Eres un experto en SEO y optimización de contenido para motores de búsqueda e inteligencia artificial.

Tu tarea es analizar el siguiente contenido de una {$typeLabel} y generar {$numQuestions} preguntas y respuestas (Q&A) que:

1. Sean preguntas que los usuarios probablemente harían sobre este tema
2. Estén optimizadas para aparecer en resultados de búsqueda y respuestas de asistentes de IA
3. Incluyan palabras clave relevantes de forma natural
4. Tengan respuestas concisas pero completas (2-4 oraciones)
5. Cubran los aspectos más importantes del contenido

TÍTULO: {$title}

CONTENIDO:
{$content}

IMPORTANTE:
- Responde SOLO con un JSON válido
- No incluyas explicaciones adicionales
- Usa el siguiente formato exacto:

[
  {\"question\": \"¿Pregunta 1?\", \"answer\": \"Respuesta concisa 1.\"},
  {\"question\": \"¿Pregunta 2?\", \"answer\": \"Respuesta concisa 2.\"}
]

Genera exactamente {$numQuestions} preguntas y respuestas en español.";
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI(string $prompt): string
    {
        $url = 'https://api.openai.com/v1/chat/completions';

        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Eres un experto en SEO y generación de contenido estructurado. Siempre respondes con JSON válido sin formato markdown ni explicaciones adicionales.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000
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
            CURLOPT_TIMEOUT => 60
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("cURL error: {$error}");
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? "HTTP {$httpCode}";
            throw new \Exception("OpenAI API error: {$errorMsg}");
        }

        $result = json_decode($response, true);

        if (!isset($result['choices'][0]['message']['content'])) {
            throw new \Exception("Invalid OpenAI response format");
        }

        return trim($result['choices'][0]['message']['content']);
    }

    /**
     * Parse GPT response into Q&A array
     */
    private function parseResponse(string $response): ?array
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/^```(?:json)?\s*/i', '', $response);
        $response = preg_replace('/\s*```$/i', '', $response);
        $response = trim($response);

        $parsed = json_decode($response, true);

        if (!is_array($parsed)) {
            error_log("QAGeneratorService: Failed to parse JSON response: " . $response);
            return null;
        }

        // Validate structure
        $validItems = [];
        foreach ($parsed as $item) {
            if (isset($item['question']) && isset($item['answer']) &&
                !empty(trim($item['question'])) && !empty(trim($item['answer']))) {
                $validItems[] = [
                    'question' => trim($item['question']),
                    'answer' => trim($item['answer'])
                ];
            }
        }

        return !empty($validItems) ? $validItems : null;
    }

    /**
     * Check if API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
