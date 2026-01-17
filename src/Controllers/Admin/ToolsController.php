<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Tool;
use App\Models\Category;
use App\Helpers\Sanitizer;

/**
 * Tools Controller (formerly Integrations)
 * We're Sinapsis CMS Admin
 */
class ToolsController extends Controller
{
    private Tool $tool;
    private Category $category;

    public function __construct()
    {
        parent::__construct();
        $this->tool = new Tool();
        $this->category = new Category();
    }

    /**
     * List all tools
     */
    public function index(): void
    {
        $this->requireAuth();

        $categoryId = $this->getQuery('category');

        $tools = $this->tool->getAllForAdmin($categoryId ? (int) $categoryId : null);

        $categories = $this->category->getActive();

        $this->renderAdmin('tools/index', [
            'title' => 'Herramientas',
            'tools' => $tools,
            'categories' => $categories,
            'currentCategory' => $categoryId,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $categories = $this->category->getActive();

        $this->renderAdmin('tools/form', [
            'title' => 'Nueva Herramienta',
            'tool' => null,
            'categories' => $categories,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Store new tool
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/tools/create');
        }

        $data = $this->getFormData();

        if (empty($data['title'])) {
            $this->flash('error', 'El título es obligatorio');
            $this->redirect('/admin/tools/create');
        }

        try {
            $this->tool->createWithSlug($data);
            $this->flash('success', 'Herramienta creada correctamente');
            $this->redirect('/admin/tools');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear la herramienta: ' . $e->getMessage());
            $this->redirect('/admin/tools/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $tool = $this->tool->findWithGallery((int) $id);

        if (!$tool) {
            $this->flash('error', 'Herramienta no encontrada');
            $this->redirect('/admin/tools');
        }

        $categories = $this->category->getActive();
        $cases = $this->tool->getSuccessCases((int) $id);

        $this->renderAdmin('tools/form', [
            'title' => 'Editar Herramienta',
            'tool' => $tool,
            'categories' => $categories,
            'cases' => $cases,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Update tool
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/tools/' . $id . '/edit');
        }

        $tool = $this->tool->find((int) $id);

        if (!$tool) {
            $this->flash('error', 'Herramienta no encontrada');
            $this->redirect('/admin/tools');
        }

        $data = $this->getFormData();

        if (empty($data['title'])) {
            $this->flash('error', 'El título es obligatorio');
            $this->redirect('/admin/tools/' . $id . '/edit');
        }

        try {
            $this->tool->updateWithSlug((int) $id, $data);
            $this->flash('success', 'Herramienta actualizada correctamente');
            $this->redirect('/admin/tools');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar la herramienta: ' . $e->getMessage());
            $this->redirect('/admin/tools/' . $id . '/edit');
        }
    }

    /**
     * Delete tool
     */
    public function delete(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/tools');
        }

        try {
            $this->tool->delete((int) $id);
            $this->flash('success', 'Herramienta eliminada correctamente');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar la herramienta');
        }

        $this->redirect('/admin/tools');
    }

    /**
     * Reorder tools via AJAX
     */
    public function reorder(): void
    {
        $this->requireAuth();

        if (!$this->isAjax()) {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
            return;
        }

        $ids = $this->getPost('ids', []);

        if (empty($ids) || !is_array($ids)) {
            $this->jsonResponse(['error' => 'No IDs provided'], 400);
            return;
        }

        try {
            $this->tool->reorder(array_map('intval', $ids));
            $this->jsonResponse(['success' => true]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get form data from POST
     */
    private function getFormData(): array
    {
        // Parse gallery JSON
        $galleryJson = $this->getPost('gallery', '[]');
        $gallery = is_string($galleryJson) ? json_decode($galleryJson, true) : $galleryJson;

        return [
            'title' => Sanitizer::string($this->getPost('title')),
            'slug' => Sanitizer::string($this->getPost('slug')),
            'subtitle' => Sanitizer::string($this->getPost('subtitle')),
            'description' => $this->getPost('description'),
            'logo' => Sanitizer::string($this->getPost('logo')),
            'platform_url' => Sanitizer::string($this->getPost('platform_url')),
            'video_url' => Sanitizer::string($this->getPost('video_url')),
            'gallery' => $gallery ?: [],
            'category_id' => Sanitizer::int($this->getPost('category_id')) ?: null,
            'is_featured' => $this->getPost('is_featured') ? 1 : 0,
            'is_active' => $this->getPost('is_active') ? 1 : 0,
            'sort_order' => Sanitizer::int($this->getPost('sort_order', 0)),
            'meta_title' => Sanitizer::string($this->getPost('meta_title')),
            'meta_description' => Sanitizer::string($this->getPost('meta_description')),
            'enable_llm_qa' => $this->getPost('enable_llm_qa') ? 1 : 0,
            'llm_qa_content' => $this->getPost('llm_qa_content', '[]'),
        ];
    }

    /**
     * Generate Q&A for a tool using AI
     */
    public function generateQA(string $id): void
    {
        $this->requireAuth();

        header('Content-Type: application/json');

        try {
            $tool = $this->tool->find((int) $id);

            if (!$tool) {
                echo json_encode(['success' => false, 'error' => 'Herramienta no encontrada']);
                return;
            }

            // Build content from tool fields
            $content = ($tool['title'] ?? '') . "\n\n";

            if (!empty($tool['subtitle'])) {
                $content .= $tool['subtitle'] . "\n\n";
            }

            if (!empty($tool['description'])) {
                $content .= strip_tags($tool['description']);
            }

            $qaGenerator = new \App\Services\QAGeneratorService();

            if (!$qaGenerator->isConfigured()) {
                echo json_encode([
                    'success' => false,
                    'error' => 'OpenAI API no configurada. Configura OPENAI_API_KEY en el archivo .env'
                ]);
                return;
            }

            $qaItems = $qaGenerator->generateQA(
                $tool['title'] ?? '',
                $content,
                'tool',
                4
            );

            echo json_encode([
                'success' => true,
                'qa_items' => $qaItems
            ]);

        } catch (\Exception $e) {
            error_log("Generate QA Error (Tool): " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Error interno: ' . $e->getMessage()
            ]);
        }
    }
}
