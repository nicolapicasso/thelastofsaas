<?php
/**
 * GeneratesQA Trait
 * Provides Q&A generation functionality for admin controllers
 * Omniwallet CMS
 */

namespace App\Traits;

use App\Services\QAGeneratorService;

trait GeneratesQA
{
    /**
     * Generate Q&A content using GPT (AJAX endpoint)
     *
     * @param int $id Entity ID
     * @param string $modelClass The model class name (e.g., 'App\Models\Post')
     * @param string $entityType Entity type for the QA generator (e.g., 'post', 'feature')
     * @param array $contentFields Fields to extract content from
     */
    protected function handleGenerateQA(int $id, string $modelClass, string $entityType, array $contentFields = ['title', 'content']): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $model = new $modelClass();
        $entity = $model->find($id);

        if (!$entity) {
            echo json_encode(['success' => false, 'error' => 'Registro no encontrado']);
            return;
        }

        // Extract content from entity
        $content = '';
        foreach ($contentFields as $field) {
            if (!empty($entity[$field])) {
                $content .= strip_tags($entity[$field]) . "\n\n";
            }
        }

        if (strlen(trim($content)) < 50) {
            echo json_encode(['success' => false, 'error' => 'No hay suficiente contenido para generar Q&A']);
            return;
        }

        // Get title for context
        $title = $entity['title'] ?? $entity['company_name'] ?? $entity['name'] ?? 'Sin título';

        // Use Q&A Generator Service
        $qaGenerator = new QAGeneratorService();

        if (!$qaGenerator->isConfigured()) {
            echo json_encode(['success' => false, 'error' => 'OpenAI API no configurada. Configura tu API key en Ajustes.']);
            return;
        }

        $qaItems = $qaGenerator->generateQA($title, $content, $entityType, 4);

        if (!$qaItems) {
            echo json_encode(['success' => false, 'error' => 'No se pudo generar el contenido Q&A. Inténtalo de nuevo.']);
            return;
        }

        echo json_encode([
            'success' => true,
            'qa_items' => $qaItems
        ]);
    }
}
