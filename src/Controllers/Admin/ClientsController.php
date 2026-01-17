<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Client;
use App\Helpers\Sanitizer;

/**
 * Clients Controller
 * We're Sinapsis CMS Admin
 */
class ClientsController extends Controller
{
    private Client $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    /**
     * List all clients
     */
    public function index(): void
    {
        $this->requireAuth();

        $industry = $this->getQuery('industry');

        if ($industry) {
            $clients = $this->client->getByIndustry($industry);
        } else {
            $clients = $this->client->all(['sort_order' => 'ASC', 'name' => 'ASC']);
        }

        $industries = $this->client->getIndustries();

        $this->renderAdmin('clients/index', [
            'title' => 'Clientes',
            'clients' => $clients,
            'industries' => $industries,
            'currentIndustry' => $industry,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $industries = $this->client->getIndustries();

        $this->renderAdmin('clients/form', [
            'title' => 'Nuevo Cliente',
            'client' => null,
            'industries' => $industries,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Store new client
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/clients/create');
        }

        $data = $this->getFormData();

        if (empty($data['name'])) {
            $this->flash('error', 'El nombre del cliente es obligatorio');
            $this->redirect('/admin/clients/create');
        }

        try {
            $this->client->createWithSlug($data);
            $this->flash('success', 'Cliente creado correctamente');
            $this->redirect('/admin/clients');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el cliente: ' . $e->getMessage());
            $this->redirect('/admin/clients/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $client = $this->client->find((int) $id);

        if (!$client) {
            $this->flash('error', 'Cliente no encontrado');
            $this->redirect('/admin/clients');
        }

        // Get success cases for this client
        $cases = $this->client->getSuccessCases((int) $id);
        $industries = $this->client->getIndustries();

        $this->renderAdmin('clients/form', [
            'title' => 'Editar Cliente',
            'client' => $client,
            'cases' => $cases,
            'industries' => $industries,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Update client
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/clients/' . $id . '/edit');
        }

        $client = $this->client->find((int) $id);

        if (!$client) {
            $this->flash('error', 'Cliente no encontrado');
            $this->redirect('/admin/clients');
        }

        $data = $this->getFormData();

        if (empty($data['name'])) {
            $this->flash('error', 'El nombre del cliente es obligatorio');
            $this->redirect('/admin/clients/' . $id . '/edit');
        }

        try {
            $this->client->updateWithSlug((int) $id, $data);
            $this->flash('success', 'Cliente actualizado correctamente');
            $this->redirect('/admin/clients');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar el cliente: ' . $e->getMessage());
            $this->redirect('/admin/clients/' . $id . '/edit');
        }
    }

    /**
     * Delete client
     */
    public function delete(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/clients');
        }

        // Check if client has cases
        $cases = $this->client->getSuccessCases((int) $id);
        if (!empty($cases)) {
            $this->flash('error', 'No se puede eliminar el cliente porque tiene casos de éxito asociados');
            $this->redirect('/admin/clients');
            return;
        }

        try {
            $this->client->delete((int) $id);
            $this->flash('success', 'Cliente eliminado correctamente');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar el cliente');
        }

        $this->redirect('/admin/clients');
    }

    /**
     * Generate Q&A with AI
     */
    public function generateQA(string $id): void
    {
        $this->requireAuth();

        header('Content-Type: application/json');

        try {
            $client = $this->client->find((int) $id);

            if (!$client) {
                echo json_encode(['success' => false, 'error' => 'Cliente no encontrado']);
                return;
            }

            // Build content from client fields
            $content = ($client['name'] ?? '') . "\n\n";

            if (!empty($client['industry'])) {
                $content .= "Industria: " . $client['industry'] . "\n\n";
            }

            if (!empty($client['location'])) {
                $content .= "Ubicación: " . $client['location'] . "\n\n";
            }

            if (!empty($client['description'])) {
                $content .= strip_tags($client['description']);
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
                $client['name'] ?? '',
                $content,
                'client',
                4
            );

            echo json_encode([
                'success' => true,
                'qa_items' => $qaItems
            ]);

        } catch (\Exception $e) {
            error_log("Generate QA Error (Client): " . $e->getMessage());
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
            'name' => Sanitizer::string($this->getPost('name')),
            'slug' => Sanitizer::string($this->getPost('slug')),
            'description' => $this->getPost('description'),
            'logo' => Sanitizer::string($this->getPost('logo')),
            'website' => Sanitizer::string($this->getPost('website')),
            'industry' => Sanitizer::string($this->getPost('industry')),
            'company_size' => Sanitizer::string($this->getPost('company_size')),
            'location' => Sanitizer::string($this->getPost('location')),
            'is_featured' => $this->getPost('is_featured') ? 1 : 0,
            'is_active' => $this->getPost('is_active') ? 1 : 0,
            'sort_order' => Sanitizer::int($this->getPost('sort_order', 0)),
            'meta_title' => Sanitizer::string($this->getPost('meta_title')),
            'meta_description' => Sanitizer::string($this->getPost('meta_description')),
            'enable_llm_qa' => $this->getPost('enable_llm_qa') ? 1 : 0,
            'llm_qa_content' => $this->getPost('llm_qa_content') ?: '[]',
        ];
    }
}
