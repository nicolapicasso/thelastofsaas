<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\SuccessCase;
use App\Models\Block;
use App\Models\ServiceBlock;
use App\Helpers\Sanitizer;

/**
 * Services Controller (formerly Features)
 * We're Sinapsis CMS Admin
 */
class ServicesController extends Controller
{
    private Service $service;
    private Category $category;
    private SuccessCase $successCase;

    public function __construct()
    {
        parent::__construct();
        $this->service = new Service();
        $this->category = new Category();
        $this->successCase = new SuccessCase();
    }

    /**
     * List all services
     */
    public function index(): void
    {
        $this->requireAuth();

        $categoryId = $this->getQuery('category');
        $services = $this->service->getAllForAdmin($categoryId ? (int) $categoryId : null);

        $categories = $this->category->getActive();

        $this->renderAdmin('services/index', [
            'title' => 'Servicios',
            'services' => $services,
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
        $cases = $this->successCase->getActive(100);

        $this->renderAdmin('services/form', [
            'title' => 'Nuevo Servicio',
            'service' => null,
            'categories' => $categories,
            'cases' => $cases,
            'selectedCases' => [],
            'blocks' => [],
            'blockTypes' => Block::TYPES,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Store new service
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/services/create');
        }

        $data = $this->getFormData();

        if (empty($data['title'])) {
            $this->flash('error', 'El título es obligatorio');
            $this->redirect('/admin/services/create');
        }

        try {
            $serviceId = $this->service->createWithSlug($data);

            // Sync cases relation
            $caseIds = $this->getPost('cases', []);
            if (!empty($caseIds) && is_array($caseIds)) {
                $this->service->syncCases($serviceId, array_map('intval', $caseIds));
            }

            $this->flash('success', 'Servicio creado correctamente');
            $this->redirect('/admin/services');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el servicio: ' . $e->getMessage());
            $this->redirect('/admin/services/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $service = $this->service->find((int) $id);

        if (!$service) {
            $this->flash('error', 'Servicio no encontrado');
            $this->redirect('/admin/services');
        }

        $categories = $this->category->getActive();
        $cases = $this->successCase->getActive(100);
        $selectedCases = $this->service->getCaseIds((int) $id);

        // Get blocks for this service
        $serviceBlock = new ServiceBlock();
        $blocks = $serviceBlock->getActiveForService((int) $id);

        $this->renderAdmin('services/form', [
            'title' => 'Editar Servicio',
            'service' => $service,
            'categories' => $categories,
            'cases' => $cases,
            'selectedCases' => $selectedCases,
            'blocks' => $blocks,
            'blockTypes' => Block::TYPES,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Update service
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/services/' . $id . '/edit');
        }

        $service = $this->service->find((int) $id);

        if (!$service) {
            $this->flash('error', 'Servicio no encontrado');
            $this->redirect('/admin/services');
        }

        $data = $this->getFormData();

        if (empty($data['title'])) {
            $this->flash('error', 'El título es obligatorio');
            $this->redirect('/admin/services/' . $id . '/edit');
        }

        try {
            $this->service->updateWithSlug((int) $id, $data);

            // Sync cases relation
            $caseIds = $this->getPost('cases', []);
            $this->service->syncCases((int) $id, is_array($caseIds) ? array_map('intval', $caseIds) : []);

            $this->flash('success', 'Servicio actualizado correctamente');
            $this->redirect('/admin/services');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar el servicio: ' . $e->getMessage());
            $this->redirect('/admin/services/' . $id . '/edit');
        }
    }

    /**
     * Delete service
     */
    public function delete(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/services');
        }

        try {
            $this->service->delete((int) $id);
            $this->flash('success', 'Servicio eliminado correctamente');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar el servicio');
        }

        $this->redirect('/admin/services');
    }

    /**
     * Reorder services via AJAX
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
            $this->service->reorder(array_map('intval', $ids));
            $this->jsonResponse(['success' => true]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate Q&A for a service using AI
     */
    public function generateQA(string $id): void
    {
        $this->requireAuth();

        header('Content-Type: application/json');

        try {
            $service = $this->service->find((int) $id);

            if (!$service) {
                echo json_encode(['success' => false, 'error' => 'Servicio no encontrado']);
                return;
            }

            // Build content from service fields
            $content = ($service['title'] ?? '') . "\n\n";

            if (!empty($service['short_description'])) {
                $content .= strip_tags($service['short_description']) . "\n\n";
            }

            if (!empty($service['full_description'])) {
                $content .= strip_tags($service['full_description']);
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
                $service['title'] ?? '',
                $content,
                'service',
                4
            );

            echo json_encode([
                'success' => true,
                'qa_items' => $qaItems
            ]);

        } catch (\Exception $e) {
            error_log("Generate QA Error (Service): " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Error interno: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get form data from POST
     */
    private function getFormData(): array
    {
        return [
            'title' => Sanitizer::string($this->getPost('title')),
            'slug' => Sanitizer::string($this->getPost('slug')),
            'short_description' => $this->getPost('short_description'),
            'full_description' => $this->getPost('full_description'),
            'icon_class' => Sanitizer::string($this->getPost('icon_class')),
            'icon_svg' => $this->getPost('icon_svg'),
            'image' => Sanitizer::string($this->getPost('image')),
            'video_url' => Sanitizer::string($this->getPost('video_url')),
            'category_id' => Sanitizer::int($this->getPost('category_id')) ?: null,
            'sort_order' => Sanitizer::int($this->getPost('sort_order', 0)),
            'is_active' => $this->getPost('is_active') ? 1 : 0,
            'is_featured' => $this->getPost('is_featured') ? 1 : 0,
            'meta_title' => Sanitizer::string($this->getPost('meta_title')),
            'meta_description' => Sanitizer::string($this->getPost('meta_description')),
            'enable_llm_qa' => $this->getPost('enable_llm_qa') ? 1 : 0,
            'llm_qa_content' => $this->getPost('llm_qa_content', '[]'),
        ];
    }
}
