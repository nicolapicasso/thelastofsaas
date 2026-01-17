<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\SuccessCase;
use App\Models\Client;
use App\Models\Category;
use App\Models\Service;
use App\Models\Tool;
use App\Helpers\Sanitizer;

/**
 * Success Cases Controller
 * We're Sinapsis CMS Admin
 */
class SuccessCasesController extends Controller
{
    private SuccessCase $successCase;
    private Client $client;
    private Category $category;
    private Service $service;
    private Tool $tool;

    public function __construct()
    {
        parent::__construct();
        $this->successCase = new SuccessCase();
        $this->client = new Client();
        $this->category = new Category();
        $this->service = new Service();
        $this->tool = new Tool();
    }

    /**
     * List all success cases
     */
    public function index(): void
    {
        $this->requireAuth();

        $categoryId = $this->getQuery('category');
        $clientId = $this->getQuery('client');
        $industry = $this->getQuery('industry');

        // Use the new method that includes client joins
        $cases = $this->successCase->getAllForAdmin(
            $categoryId ? (int) $categoryId : null,
            $clientId ? (int) $clientId : null
        );

        // Filter by industry if specified
        if ($industry) {
            $cases = array_filter($cases, function ($case) use ($industry) {
                return ($case['client_industry'] ?? '') === $industry;
            });
            $cases = array_values($cases);
        }

        $categories = $this->category->getActive();
        $clients = $this->client->getActive();
        $industries = $this->successCase->getIndustries();

        $this->renderAdmin('cases/index', [
            'title' => 'Casos de Éxito',
            'cases' => $cases,
            'categories' => $categories,
            'clients' => $clients,
            'industries' => $industries,
            'currentCategory' => $categoryId,
            'currentClient' => $clientId,
            'currentIndustry' => $industry,
            'flash' => $this->getFlash(),
            '_csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $clients = $this->client->getActive();
        $categories = $this->category->getActive();
        $services = $this->service->getActive();
        $tools = $this->tool->getActive();

        $this->renderAdmin('cases/form', [
            'title' => 'Nuevo Caso de Éxito',
            'case' => null,
            'clients' => $clients,
            'categories' => $categories,
            'services' => $services,
            'tools' => $tools,
            'selectedServices' => [],
            'selectedTools' => [],
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Store new success case
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/cases/create');
        }

        $data = $this->getFormData();

        if (empty($data['title'])) {
            $this->flash('error', 'El título es obligatorio');
            $this->redirect('/admin/cases/create');
        }

        if (empty($data['client_id'])) {
            $this->flash('error', 'Debe seleccionar un cliente');
            $this->redirect('/admin/cases/create');
        }

        try {
            $caseId = $this->successCase->createCase($data);

            // Sync services and tools
            $serviceIds = $this->getPost('services', []);
            $toolIds = $this->getPost('tools', []);

            if (!empty($serviceIds)) {
                $this->successCase->syncServices($caseId, array_map('intval', $serviceIds));
            }
            if (!empty($toolIds)) {
                $this->successCase->syncTools($caseId, array_map('intval', $toolIds));
            }

            $this->flash('success', 'Caso de éxito creado correctamente');
            $this->redirect('/admin/cases');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el caso de éxito: ' . $e->getMessage());
            $this->redirect('/admin/cases/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $case = $this->successCase->findWithRelations((int) $id);

        if (!$case) {
            $this->flash('error', 'Caso de éxito no encontrado');
            $this->redirect('/admin/cases');
        }

        $clients = $this->client->getActive();
        $categories = $this->category->getActive();
        $services = $this->service->getActive();
        $tools = $this->tool->getActive();

        // Get related service and tool IDs
        $selectedServices = $this->successCase->getServiceIds((int) $id);
        $selectedTools = $this->successCase->getToolIds((int) $id);

        $this->renderAdmin('cases/form', [
            'title' => 'Editar Caso de Éxito',
            'case' => $case,
            'clients' => $clients,
            'categories' => $categories,
            'services' => $services,
            'tools' => $tools,
            'selectedServices' => $selectedServices,
            'selectedTools' => $selectedTools,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Update success case
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/cases/' . $id . '/edit');
        }

        $case = $this->successCase->find((int) $id);

        if (!$case) {
            $this->flash('error', 'Caso de éxito no encontrado');
            $this->redirect('/admin/cases');
        }

        $data = $this->getFormData();

        if (empty($data['title'])) {
            $this->flash('error', 'El título es obligatorio');
            $this->redirect('/admin/cases/' . $id . '/edit');
        }

        if (empty($data['client_id'])) {
            $this->flash('error', 'Debe seleccionar un cliente');
            $this->redirect('/admin/cases/' . $id . '/edit');
        }

        try {
            $this->successCase->updateCase((int) $id, $data);

            // Sync services and tools
            $serviceIds = $this->getPost('services', []);
            $toolIds = $this->getPost('tools', []);

            $this->successCase->syncServices((int) $id, array_map('intval', $serviceIds ?: []));
            $this->successCase->syncTools((int) $id, array_map('intval', $toolIds ?: []));

            $this->flash('success', 'Caso de éxito actualizado correctamente');
            $this->redirect('/admin/cases');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar el caso de éxito: ' . $e->getMessage());
            $this->redirect('/admin/cases/' . $id . '/edit');
        }
    }

    /**
     * Delete success case
     */
    public function delete(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/cases');
        }

        try {
            // Relations will be deleted by CASCADE
            $this->successCase->delete((int) $id);
            $this->flash('success', 'Caso de éxito eliminado');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar el caso de éxito');
        }

        $this->redirect('/admin/cases');
    }

    /**
     * Reorder cases via AJAX
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
            foreach ($ids as $order => $id) {
                $this->successCase->update((int) $id, ['sort_order' => $order]);
            }
            $this->jsonResponse(['success' => true]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate Q&A content using GPT (AJAX endpoint)
     */
    public function generateQA(string $id): void
    {
        $this->requireAuth();

        header('Content-Type: application/json');

        try {
            $case = $this->successCase->findWithRelations((int) $id);

            if (!$case) {
                echo json_encode(['success' => false, 'error' => 'Caso de éxito no encontrado']);
                return;
            }

            // Build content from case fields
            $content = ($case['title'] ?? '') . "\n\n";

            if (!empty($case['challenge'])) {
                $content .= "El reto: " . strip_tags($case['challenge']) . "\n\n";
            }
            if (!empty($case['solution'])) {
                $content .= "La solución: " . strip_tags($case['solution']) . "\n\n";
            }
            if (!empty($case['results'])) {
                $content .= "Los resultados: " . strip_tags($case['results']) . "\n\n";
            }
            if (!empty($case['testimonial'])) {
                $content .= "Testimonio: " . $case['testimonial'] . "\n\n";
            }
            if (!empty($case['client_name'])) {
                $content .= "Cliente: " . $case['client_name'] . "\n";
            }
            if (!empty($case['client_industry'])) {
                $content .= "Industria: " . $case['client_industry'] . "\n";
            }

            // Use Q&A Generator Service
            $qaGenerator = new \App\Services\QAGeneratorService();

            if (!$qaGenerator->isConfigured()) {
                echo json_encode(['success' => false, 'error' => 'OpenAI API no configurada. Configura tu API key en Ajustes.']);
                return;
            }

            $qaItems = $qaGenerator->generateQA($case['title'] ?? '', $content, 'success_case', 4);

            if (!$qaItems) {
                echo json_encode(['success' => false, 'error' => 'No se pudo generar el contenido Q&A. Inténtalo de nuevo.']);
                return;
            }

            echo json_encode([
                'success' => true,
                'qa_items' => $qaItems
            ]);
        } catch (\Exception $e) {
            error_log("Generate QA Error (Success Case): " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Error interno: ' . $e->getMessage()]);
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

        // Parse metrics JSON
        $metricsJson = $this->getPost('metrics', '[]');
        $metrics = is_string($metricsJson) ? json_decode($metricsJson, true) : $metricsJson;

        // Parse LLM Q&A JSON
        $llmQaContent = $this->getPost('llm_qa_content', '[]');

        return [
            'title' => Sanitizer::string($this->getPost('title')),
            'slug' => Sanitizer::string($this->getPost('slug')),
            'client_id' => Sanitizer::int($this->getPost('client_id')) ?: null,
            'category_id' => Sanitizer::int($this->getPost('category_id')) ?: null,
            'featured_image' => Sanitizer::string($this->getPost('featured_image')),
            'video_url' => Sanitizer::string($this->getPost('video_url')),
            'gallery' => $gallery ?: [],
            'gallery_display' => Sanitizer::string($this->getPost('gallery_display', 'carousel')),
            'challenge' => $this->getPost('challenge'),
            'solution' => $this->getPost('solution'),
            'results' => $this->getPost('results'),
            'metrics' => $metrics ?: [],
            'testimonial' => $this->getPost('testimonial'),
            'testimonial_author' => Sanitizer::string($this->getPost('testimonial_author')),
            'testimonial_role' => Sanitizer::string($this->getPost('testimonial_role')),
            'sort_order' => Sanitizer::int($this->getPost('sort_order', 0)),
            'is_featured' => $this->getPost('is_featured') ? 1 : 0,
            'status' => Sanitizer::string($this->getPost('status', 'draft')),
            'published_at' => $this->getPost('status') === 'published' ? date('Y-m-d H:i:s') : null,
            'meta_title' => Sanitizer::string($this->getPost('meta_title')),
            'meta_description' => Sanitizer::string($this->getPost('meta_description')),
            'enable_llm_qa' => $this->getPost('enable_llm_qa') ? 1 : 0,
            'llm_qa_content' => $llmQaContent,
        ];
    }
}
