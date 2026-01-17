<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Page;
use App\Models\Block;
use App\Helpers\Sanitizer;

/**
 * Pages Controller
 * Omniwallet CMS
 */
class PagesController extends Controller
{
    private Page $pageModel;
    private Block $blockModel;

    public function __construct()
    {
        parent::__construct();
        $this->pageModel = new Page();
        $this->blockModel = new Block();
    }

    /**
     * List all pages
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) ($this->getQuery('page', 1));
        $status = $this->getQuery('status');

        $conditions = [];
        if ($status) {
            $conditions['status'] = $status;
        }

        $result = $this->pageModel->paginate($page, 20, $conditions, ['updated_at' => 'DESC']);

        $this->renderAdmin('pages/index', [
            'title' => 'Páginas',
            'pages' => $result['data'],
            'pagination' => $result['pagination'],
            'currentStatus' => $status,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $this->renderAdmin('pages/form', [
            'title' => 'Nueva Página',
            'page' => null,
            'blocks' => [],
            'blockTypes' => Block::TYPES,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new page
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/pages/create');
        }

        $data = $this->validatePageData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/pages/create');
        }

        $data['author_id'] = $this->getUserId();

        try {
            $pageId = $this->pageModel->createWithSlug($data);
            $this->flash('success', 'Página creada correctamente.');
            $this->redirect('/admin/pages/' . $pageId . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear la página: ' . $e->getMessage());
            $this->redirect('/admin/pages/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $page = $this->pageModel->find((int) $id);

        if (!$page) {
            $this->flash('error', 'Página no encontrada.');
            $this->redirect('/admin/pages');
        }

        $blocks = $this->blockModel->getActiveForPage((int) $id);

        $this->renderAdmin('pages/form', [
            'title' => 'Editar Página',
            'page' => $page,
            'blocks' => $blocks,
            'blockTypes' => Block::TYPES,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update page
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/pages/' . $id . '/edit');
        }

        $page = $this->pageModel->find((int) $id);

        if (!$page) {
            $this->flash('error', 'Página no encontrada.');
            $this->redirect('/admin/pages');
        }

        $data = $this->validatePageData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/pages/' . $id . '/edit');
        }

        try {
            $this->pageModel->updateWithSlug((int) $id, $data);
            $this->flash('success', 'Página actualizada correctamente.');
            $this->redirect('/admin/pages/' . $id . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar la página: ' . $e->getMessage());
            $this->redirect('/admin/pages/' . $id . '/edit');
        }
    }

    /**
     * Delete page
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/pages');
        }

        $page = $this->pageModel->find((int) $id);

        if (!$page) {
            $this->flash('error', 'Página no encontrada.');
            $this->redirect('/admin/pages');
        }

        // Prevent deleting home page
        if ($page['slug'] === 'home') {
            $this->flash('error', 'No se puede eliminar la página de inicio.');
            $this->redirect('/admin/pages');
        }

        try {
            $this->pageModel->delete((int) $id);
            $this->flash('success', 'Página eliminada correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar la página: ' . $e->getMessage());
        }

        $this->redirect('/admin/pages');
    }

    /**
     * Validate page form data
     */
    private function validatePageData(): array
    {
        $errors = [];

        $title = Sanitizer::string($this->getPost('title'));
        $slug = Sanitizer::string($this->getPost('slug'));
        $status = $this->getPost('status', 'draft');
        $template = Sanitizer::string($this->getPost('template', 'default'));
        $metaTitle = Sanitizer::string($this->getPost('meta_title'));
        $metaDescription = Sanitizer::string($this->getPost('meta_description'));
        $metaKeywords = Sanitizer::string($this->getPost('meta_keywords'));
        $ogImage = Sanitizer::string($this->getPost('og_image'));
        $excerpt = $this->getPost('excerpt');
        $content = $this->getPost('content');
        $enableLlmQa = $this->getPost('enable_llm_qa') ? 1 : 0;
        $llmQaContent = $this->getPost('llm_qa_content', '[]');

        // Validate required
        if (empty($title)) {
            $errors[] = 'El título es obligatorio.';
        }

        // Validate status
        if (!in_array($status, ['draft', 'published', 'archived'])) {
            $errors[] = 'Estado no válido.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'title' => $title,
            'slug' => $slug ?: null,
            'status' => $status,
            'template' => $template,
            'excerpt' => $excerpt ?: null,
            'content' => $content ?: null,
            'meta_title' => $metaTitle ?: null,
            'meta_description' => $metaDescription ?: null,
            'meta_keywords' => $metaKeywords ?: null,
            'og_image' => $ogImage ?: null,
            'enable_llm_qa' => $enableLlmQa,
            'llm_qa_content' => $llmQaContent,
        ];
    }

    /**
     * Generate Q&A content using GPT (AJAX endpoint)
     */
    public function generateQA(int $id): void
    {
        // Suppress PHP errors from being output as HTML
        ob_start();
        $previousErrorReporting = error_reporting(0);

        try {
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                ob_end_clean();
                error_reporting($previousErrorReporting);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
                return;
            }

            $page = $this->pageModel->find($id);
            if (!$page) {
                ob_end_clean();
                error_reporting($previousErrorReporting);
                echo json_encode(['success' => false, 'error' => 'Página no encontrada']);
                return;
            }

            // Get page blocks content
            $blockModel = new \App\Models\Block();
            $blocks = $blockModel->getForPage($id);

            // Extract text content from blocks
            $content = $page['title'] . "\n\n";
            foreach ($blocks as $block) {
                $blockContent = json_decode($block['content'] ?? '{}', true);
                if (is_array($blockContent)) {
                    $content .= $this->extractTextFromBlock($blockContent) . "\n";
                }
            }

            // Use Q&A Generator Service
            $qaGenerator = new \App\Services\QAGeneratorService();

            if (!$qaGenerator->isConfigured()) {
                ob_end_clean();
                error_reporting($previousErrorReporting);
                echo json_encode(['success' => false, 'error' => 'OpenAI API no configurada. Configura tu API key en Ajustes.']);
                return;
            }

            $qaItems = $qaGenerator->generateQA($page['title'], $content, 'page', 4);

            ob_end_clean();
            error_reporting($previousErrorReporting);

            if (!$qaItems) {
                echo json_encode(['success' => false, 'error' => 'No se pudo generar el contenido Q&A. Inténtalo de nuevo.']);
                return;
            }

            echo json_encode([
                'success' => true,
                'qa_items' => $qaItems
            ]);
        } catch (\Exception $e) {
            ob_end_clean();
            error_reporting($previousErrorReporting);
            error_log("Generate QA Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Extract text content from block data
     */
    private function extractTextFromBlock(array $content): string
    {
        $text = '';

        $textFields = ['title', 'subtitle', 'heading', 'subheading', 'content', 'text', 'description', 'excerpt'];

        foreach ($content as $key => $value) {
            if (in_array($key, $textFields) && is_string($value)) {
                $text .= strip_tags($value) . " ";
            } elseif (is_array($value)) {
                // Handle items arrays
                if (isset($value[0])) {
                    foreach ($value as $item) {
                        if (is_array($item)) {
                            $text .= $this->extractTextFromBlock($item) . " ";
                        }
                    }
                } else {
                    $text .= $this->extractTextFromBlock($value) . " ";
                }
            }
        }

        return trim($text);
    }
}
