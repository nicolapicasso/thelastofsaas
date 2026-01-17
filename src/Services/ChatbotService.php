<?php
/**
 * Chatbot Service - "Loyalty Master"
 * AI-powered chatbot with RAG (Retrieval Augmented Generation)
 * Uses knowledge base articles for context-aware responses
 * Omniwallet CMS
 */

namespace App\Services;

use App\Models\Setting;
use PDO;

class ChatbotService
{
    private ?string $apiKey;
    private string $model;
    private PDO $db;
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/translations.php';
        $dbConfig = require __DIR__ . '/../../config/database.php';

        // Database connection
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
        $this->db = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        // Get API key from settings or config
        $settingModel = new Setting();
        $dbApiKey = $settingModel->get('openai_api_key');

        $this->apiKey = !empty($dbApiKey)
            ? $dbApiKey
            : ($config['openai']['api_key'] ?? null);

        $this->model = $config['openai']['model'] ?? 'gpt-4o-mini';

        // Base URL for generating links
        $this->baseUrl = rtrim($_ENV['APP_URL'] ?? 'https://new.omniwallet.net', '/');
    }

    /**
     * Process a chat message and return AI response
     */
    public function chat(string $userMessage, array $conversationHistory = []): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'El servicio de chat no está configurado correctamente.'
            ];
        }

        if (empty(trim($userMessage))) {
            return [
                'success' => false,
                'error' => 'Por favor, escribe tu pregunta.'
            ];
        }

        try {
            // 1. Search for relevant knowledge articles
            $relevantArticles = $this->searchKnowledgeBase($userMessage);

            // 2. Build the context from articles
            $knowledgeContext = $this->buildKnowledgeContext($relevantArticles);

            // 3. Build messages array for OpenAI
            $messages = $this->buildMessages($userMessage, $knowledgeContext, $conversationHistory);

            // 4. Call OpenAI API
            $response = $this->callOpenAI($messages);

            // 5. Log the conversation (for future analytics)
            $this->logConversation($userMessage, $response, $relevantArticles);

            return [
                'success' => true,
                'message' => $response,
                'sources' => array_map(function($article) {
                    return [
                        'title' => $article['title'],
                        'url' => $this->baseUrl . '/ayuda/' . $article['slug']
                    ];
                }, array_slice($relevantArticles, 0, 3))
            ];

        } catch (\Exception $e) {
            error_log("ChatbotService error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Lo siento, ha ocurrido un error. Por favor, inténtalo de nuevo.'
            ];
        }
    }

    /**
     * Search knowledge base for relevant articles using FULLTEXT search
     */
    private function searchKnowledgeBase(string $query, int $limit = 5): array
    {
        // Clean and prepare search terms
        $searchTerms = $this->extractSearchTerms($query);

        if (empty($searchTerms)) {
            return [];
        }

        // Use FULLTEXT search with NATURAL LANGUAGE MODE
        $sql = "SELECT id, title, slug, excerpt, content, topic, topic_slug,
                       MATCH(title, content, excerpt) AGAINST(:query IN NATURAL LANGUAGE MODE) as relevance
                FROM knowledge_articles
                WHERE status = 'published'
                AND MATCH(title, content, excerpt) AGAINST(:query2 IN NATURAL LANGUAGE MODE)
                ORDER BY relevance DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':query', $searchTerms, PDO::PARAM_STR);
        $stmt->bindValue(':query2', $searchTerms, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Extract meaningful search terms from user query
     */
    private function extractSearchTerms(string $query): string
    {
        // Remove common Spanish stop words
        $stopWords = ['el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'de', 'del',
                      'en', 'con', 'por', 'para', 'que', 'qué', 'como', 'cómo', 'cuando',
                      'donde', 'cual', 'cuál', 'es', 'son', 'está', 'están', 'hay', 'tiene',
                      'se', 'su', 'sus', 'mi', 'mis', 'tu', 'tus', 'al', 'lo', 'le', 'les',
                      'me', 'te', 'nos', 'si', 'no', 'ya', 'muy', 'más', 'menos', 'este',
                      'esta', 'estos', 'estas', 'ese', 'esa', 'esos', 'esas', 'y', 'o', 'a'];

        // Clean query
        $query = mb_strtolower($query);
        $query = preg_replace('/[¿?¡!.,;:()"\']/', ' ', $query);

        $words = preg_split('/\s+/', $query);
        $words = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });

        return implode(' ', $words);
    }

    /**
     * Build knowledge context from retrieved articles
     */
    private function buildKnowledgeContext(array $articles): string
    {
        if (empty($articles)) {
            return "No se encontraron artículos específicos en la base de conocimiento para esta consulta.";
        }

        $context = "ARTÍCULOS RELEVANTES DE LA BASE DE CONOCIMIENTO:\n\n";

        foreach ($articles as $index => $article) {
            $content = strip_tags($article['content'] ?? '');
            // Truncate content if too long
            if (strlen($content) > 1500) {
                $content = substr($content, 0, 1500) . '...';
            }

            $context .= "---\n";
            $context .= "ARTÍCULO " . ($index + 1) . ": " . $article['title'] . "\n";
            $context .= "URL: /ayuda/" . $article['slug'] . "\n";
            if (!empty($article['excerpt'])) {
                $context .= "RESUMEN: " . $article['excerpt'] . "\n";
            }
            $context .= "CONTENIDO:\n" . $content . "\n\n";
        }

        return $context;
    }

    /**
     * Build messages array for OpenAI API
     */
    private function buildMessages(string $userMessage, string $knowledgeContext, array $history = []): array
    {
        $systemPrompt = $this->getSystemPrompt($knowledgeContext);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Add conversation history (last 10 messages max)
        $recentHistory = array_slice($history, -10);
        foreach ($recentHistory as $msg) {
            if (isset($msg['role']) && isset($msg['content'])) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }
        }

        // Add current user message
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        return $messages;
    }

    /**
     * Get the system prompt for Loyalty Master
     */
    private function getSystemPrompt(string $knowledgeContext): string
    {
        return "Eres \"Loyalty Master\", el asistente virtual experto de Omniwallet.

## SOBRE TI
- Eres un especialista amable y profesional en programas de fidelización y loyalty marketing
- Conoces en profundidad todas las funcionalidades de Omniwallet
- Respondes siempre en español, de forma clara, concisa y útil
- Tu tono es profesional pero cercano, nunca robótico

## TU MISIÓN
Ayudar a usuarios y potenciales clientes a:
- Entender cómo funciona Omniwallet
- Resolver dudas sobre configuración y uso
- Explicar las ventajas de los programas de fidelización
- Guiar en la implementación de estrategias de loyalty

## RESTRICCIONES IMPORTANTES
- SOLO respondes sobre temas relacionados con:
  · Omniwallet y sus funcionalidades
  · Programas de fidelización y loyalty
  · Marketing de retención de clientes
  · Puntos, niveles, recompensas, promociones
  · Integraciones y configuración técnica

- Si te preguntan sobre temas NO relacionados (política, deportes, recetas, etc.), responde amablemente:
  \"Esa pregunta está fuera de mi especialidad. Soy Loyalty Master, el asistente de Omniwallet, y estoy aquí para ayudarte con todo lo relacionado con programas de fidelización. ¿Hay algo sobre Omniwallet en lo que pueda ayudarte?\"

## CÓMO RESPONDER
1. Usa la información del CONTEXTO DE CONOCIMIENTO cuando sea relevante
2. Si mencionas información de un artículo, sugiere el enlace: \"Puedes ver más detalles en: [URL]\"
3. Si no tienes información específica, sé honesto: \"No tengo información detallada sobre eso, pero puedes contactar con soporte en /contacto\"
4. Mantén las respuestas concisas pero completas (2-4 párrafos máximo)
5. Usa formato con saltos de línea para mejor legibilidad

## PÁGINAS PRINCIPALES DE OMNIWALLET
- Inicio: /
- La herramienta (funcionalidades): /la-herramienta
- Precios: /precios
- Casos de éxito: /casos-de-exito
- Centro de ayuda: /ayuda
- Contacto: /contacto
- Blog: /blog

## CONTEXTO DE CONOCIMIENTO
{$knowledgeContext}

Recuerda: Eres Loyalty Master, experto en fidelización. ¡Ayuda al usuario con entusiasmo!";
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI(array $messages): string
    {
        $url = 'https://api.openai.com/v1/chat/completions';

        $data = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1000,
            'presence_penalty' => 0.1,
            'frequency_penalty' => 0.1
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
     * Log conversation for analytics (optional)
     */
    private function logConversation(string $userMessage, string $botResponse, array $articlesUsed): void
    {
        try {
            // Check if table exists, if not skip logging
            $tableExists = $this->db->query("SHOW TABLES LIKE 'chatbot_logs'")->rowCount() > 0;

            if (!$tableExists) {
                return;
            }

            $sql = "INSERT INTO chatbot_logs (user_message, bot_response, articles_used, session_id, created_at)
                    VALUES (:user_message, :bot_response, :articles_used, :session_id, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_message' => $userMessage,
                ':bot_response' => $botResponse,
                ':articles_used' => json_encode(array_column($articlesUsed, 'id')),
                ':session_id' => session_id() ?: 'anonymous'
            ]);
        } catch (\Exception $e) {
            // Silently fail - logging is not critical
            error_log("Chatbot logging error: " . $e->getMessage());
        }
    }

    /**
     * Check if service is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
