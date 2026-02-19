<?php
/**
 * Translations Controller
 * Omniwallet CMS Admin
 */

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Translation;
use App\Services\TranslationService;

class TranslationsController extends Controller
{
    private Translation $translation;
    private TranslationService $translationService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->translation = new Translation();
        $this->translationService = new TranslationService();
    }

    /**
     * List translations and statistics
     */
    public function index(): void
    {
        $currentLanguage = $_GET['language'] ?? null;
        $currentType = $_GET['type'] ?? null;
        $currentStatus = $_GET['status'] ?? null; // 'pending', 'approved', 'untranslated', or null for all
        $search = trim($_GET['search'] ?? '');

        // Get statistics
        $statistics = $this->translation->getStatistics();

        // Get untranslated stats if a language is selected
        $untranslatedStats = null;
        if ($currentLanguage) {
            $untranslatedStats = $this->translation->getUntranslatedStats($currentLanguage);
        }

        // Get translations based on filters
        if ($currentStatus === 'untranslated' && $currentLanguage) {
            // Special case: show untranslated content
            $translations = $this->translation->getUntranslatedContent($currentLanguage, $currentType);
        } else {
            $translations = $this->translation->getAllTranslations(
                $currentType,
                $currentLanguage,
                $currentStatus,
                $search ?: null
            );
        }

        $this->renderAdmin('translations/index', [
            'title' => 'Traducciones',
            'statistics' => $statistics,
            'untranslatedStats' => $untranslatedStats,
            'translations' => $translations,
            'languages' => Translation::LANGUAGES,
            'entityTypes' => Translation::ENTITY_TYPES,
            'currentLanguage' => $currentLanguage,
            'currentType' => $currentType,
            'currentStatus' => $currentStatus,
            'search' => $search,
            'isConfigured' => $this->translationService->isConfigured(),
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Edit a translation
     */
    public function edit(int $id): void
    {
        $translation = $this->translation->find($id);

        if (!$translation) {
            $this->flash('error', 'Traducción no encontrada');
            $this->redirect('/admin/translations');
        }

        $this->renderAdmin('translations/edit', [
            'title' => 'Editar Traducción',
            'translation' => $translation,
            'languages' => Translation::LANGUAGES,
            'entityTypes' => Translation::ENTITY_TYPES,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Update translation
     */
    public function update(int $id): void
    {
        $this->validateCsrf();

        $translation = $this->translation->find($id);

        if (!$translation) {
            $this->flash('error', 'Traducción no encontrada');
            $this->redirect('/admin/translations');
        }

        $translatedContent = $_POST['translated_content'] ?? '';
        $isApproved = isset($_POST['is_approved']) ? 1 : 0;

        $this->translation->update($id, [
            'translated_content' => $translatedContent,
            'is_approved' => $isApproved,
            'is_auto_translated' => $translation['is_auto_translated'] && !$isApproved ? 1 : 0
        ]);

        $this->flash('success', 'Traducción actualizada');
        $this->redirect('/admin/translations');
    }

    /**
     * Approve a translation
     */
    public function approve(int $id): void
    {
        $this->validateCsrf();

        if ($this->translation->approve($id)) {
            $this->flash('success', 'Traducción aprobada');
        } else {
            $this->flash('error', 'Error al aprobar la traducción');
        }

        $this->redirect('/admin/translations');
    }

    /**
     * Delete translation
     */
    public function delete(int $id): void
    {
        $this->validateCsrf();

        if ($this->translation->delete($id)) {
            $this->flash('success', 'Traducción eliminada');
        } else {
            $this->flash('error', 'Error al eliminar la traducción');
        }

        $this->redirect('/admin/translations');
    }

    /**
     * Translate entity via AJAX
     */
    public function translateEntity(): void
    {
        $this->validateCsrf();

        header('Content-Type: application/json');

        if (!$this->translationService->isConfigured()) {
            echo json_encode(['success' => false, 'error' => 'API no configurada']);
            return;
        }

        $entityType = $_POST['entity_type'] ?? '';
        $entityId = (int)($_POST['entity_id'] ?? 0);
        $targetLanguage = $_POST['target_language'] ?? '';
        $fields = json_decode($_POST['fields'] ?? '{}', true);

        if (empty($entityType) || empty($entityId) || empty($targetLanguage) || empty($fields)) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $results = $this->translationService->translateEntity(
            $entityType,
            $entityId,
            $fields,
            $targetLanguage
        );

        echo json_encode([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * Batch translate all pending
     */
    public function batchTranslate(): void
    {
        $this->validateCsrf();

        if (!$this->translationService->isConfigured()) {
            $this->flash('error', 'API de OpenAI no configurada');
            $this->redirect('/admin/translations');
        }

        $targetLanguage = $_POST['target_language'] ?? '';

        if (empty($targetLanguage)) {
            $this->flash('error', 'Idioma no especificado');
            $this->redirect('/admin/translations');
        }

        $stats = $this->translationService->translateAllPending($targetLanguage);

        $message = "Traducción completada: {$stats['success']} éxitos, {$stats['failed']} errores, {$stats['skipped']} omitidos";
        $this->flash($stats['failed'] > 0 ? 'warning' : 'success', $message);
        $this->redirect('/admin/translations');
    }

    /**
     * Get translation batch info (AJAX)
     */
    public function getBatchInfo(): void
    {
        header('Content-Type: application/json');

        if (!$this->translationService->isConfigured()) {
            echo json_encode(['error' => 'API no configurada']);
            return;
        }

        $targetLanguage = $_GET['language'] ?? '';
        if (empty($targetLanguage)) {
            echo json_encode(['error' => 'Idioma no especificado']);
            return;
        }

        $entityType = !empty($_GET['entity_type']) ? $_GET['entity_type'] : null;

        $entities = $this->translationService->getEntitiesForTranslation($targetLanguage, $entityType);
        $totalFields = 0;
        foreach ($entities as $entity) {
            $totalFields += count(array_filter($entity['fields'], fn($v) => !empty($v)));
        }

        echo json_encode([
            'total_entities' => count($entities),
            'total_fields' => $totalFields,
            'language' => $targetLanguage
        ]);
    }

    /**
     * Process a single batch of translations (AJAX)
     */
    public function processBatch(): void
    {
        header('Content-Type: application/json');

        // Increase timeout for this request
        set_time_limit(120);

        if (!$this->translationService->isConfigured()) {
            echo json_encode(['error' => 'API no configurada']);
            return;
        }

        $targetLanguage = $_POST['language'] ?? '';
        $offset = (int)($_POST['offset'] ?? 0);
        $batchSize = (int)($_POST['batch_size'] ?? 5);
        $entityType = !empty($_POST['entity_type']) ? $_POST['entity_type'] : null;

        if (empty($targetLanguage)) {
            echo json_encode(['error' => 'Idioma no especificado']);
            return;
        }

        $result = $this->translationService->translateBatch($targetLanguage, $offset, $batchSize, $entityType);

        echo json_encode($result);
    }

    /**
     * Bulk approve translations
     */
    public function bulkApprove(): void
    {
        $this->validateCsrf();

        $ids = $_POST['ids'] ?? [];
        $approved = 0;

        foreach ($ids as $id) {
            if ($this->translation->approve((int)$id)) {
                $approved++;
            }
        }

        $this->flash('success', "{$approved} traducciones aprobadas");
        $this->redirect('/admin/translations');
    }

    /**
     * View translation error log
     */
    public function errorLog(): void
    {
        $errors = $this->translationService->getRecentErrors(100);

        $this->renderAdmin('translations/error-log', [
            'title' => 'Log de Errores de Traducción',
            'errors' => $errors
        ]);
    }

    /**
     * Clear translation error log (AJAX)
     */
    public function clearLog(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $result = $this->translationService->clearLog();

        echo json_encode(['success' => $result]);
    }

    /**
     * Create new translation (AJAX) - for manual translation of untranslated content
     */
    public function create(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $entityType = $_POST['entity_type'] ?? '';
        $entityId = (int)($_POST['entity_id'] ?? 0);
        $fieldName = $_POST['field_name'] ?? '';
        $language = $_POST['language'] ?? '';
        $originalContent = $_POST['original_content'] ?? '';
        $translatedContent = trim($_POST['translated_content'] ?? '');

        if (empty($entityType) || empty($entityId) || empty($fieldName) || empty($language)) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        if (empty($translatedContent)) {
            echo json_encode(['success' => false, 'error' => 'La traducción no puede estar vacía']);
            return;
        }

        // Save the translation
        $result = $this->translation->saveTranslation(
            $entityType,
            $entityId,
            $fieldName,
            $language,
            $originalContent,
            $translatedContent,
            false, // not auto-translated
            true   // manually translated = approved
        );

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Traducción guardada']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al guardar la traducción']);
        }
    }
}
