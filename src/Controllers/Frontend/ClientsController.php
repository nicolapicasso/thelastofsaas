<?php
/**
 * Clients Controller (Frontend)
 * We're Sinapsis CMS
 */

namespace App\Controllers\Frontend;

use App\Models\Client;

class ClientsController extends BaseController
{
    private Client $clientModel;

    public function __construct()
    {
        parent::__construct();
        $this->clientModel = new Client();
    }

    /**
     * Clients directory listing
     */
    public function index(): void
    {
        $industry = $_GET['industria'] ?? null;
        $search = trim($_GET['buscar'] ?? '');

        // Get clients based on filters
        if ($search) {
            $clients = $this->clientModel->search($search, $industry);
        } elseif ($industry) {
            $clients = $this->clientModel->getByIndustry($industry);
        } else {
            $clients = $this->clientModel->getActive();
        }

        // Get industries for filter
        $industries = $this->clientModel->getIndustries();

        // Get featured clients for hero section
        $featured = $this->clientModel->getFeatured(6);

        // Get clients with logos for logo wall
        $withLogos = $this->clientModel->getWithLogos(20);

        // Total count
        $totalClients = count($this->clientModel->getActive());

        // SEO
        $title = $industry
            ? "Clientes - {$industry} | We're Sinapsis"
            : "Nuestros Clientes | We're Sinapsis";
        $this->seo->setTitle($title);
        $this->seo->setDescription('Conoce las empresas que han confiado en nosotros para su transformación digital. Clientes de diversos sectores y tamaños.');
        $this->seo->setCanonical('/clientes');

        $this->view('clients/index', [
            'clients' => $clients,
            'featured' => $featured,
            'withLogos' => $withLogos,
            'industries' => $industries,
            'totalClients' => $totalClients,
            'currentIndustry' => $industry,
            'searchQuery' => $search
        ]);
    }

    /**
     * Show single client profile
     */
    public function show(string $slug): void
    {
        $client = $this->clientModel->findBySlug($slug);

        if (!$client) {
            $this->notFound();
            return;
        }

        // Get success cases for this client
        $successCases = $this->clientModel->getSuccessCases($client['id']);

        // Get other clients from same industry
        $relatedClients = [];
        if (!empty($client['industry'])) {
            $relatedClients = array_filter(
                $this->clientModel->getByIndustry($client['industry']),
                fn($c) => $c['id'] !== $client['id']
            );
            $relatedClients = array_slice($relatedClients, 0, 4);
        }

        // SEO
        $metaTitle = $client['meta_title'] ?? "{$client['name']} | Clientes | We're Sinapsis";
        $metaDesc = $client['meta_description'] ?? $client['description'] ?? "Conoce cómo {$client['name']} ha transformado su negocio con nosotros.";

        $this->seo->setTitle($metaTitle);
        $this->seo->setDescription($metaDesc);
        $this->seo->setCanonical("/clientes/{$slug}");

        if (!empty($client['logo'])) {
            $this->seo->setImage($client['logo']);
        }

        // Schema.org Organization
        $schema = [
            '@type' => 'Organization',
            'name' => $client['name']
        ];
        if (!empty($client['website'])) {
            $schema['url'] = $client['website'];
        }
        if (!empty($client['logo'])) {
            $schema['logo'] = $client['logo'];
        }
        if (!empty($client['industry'])) {
            $schema['industry'] = $client['industry'];
        }
        $this->seo->addSchema($schema);

        $this->view('clients/show', [
            'client' => $client,
            'successCases' => $successCases,
            'relatedClients' => $relatedClients
        ]);
    }

    /**
     * 404 handler
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->seo->setTitle('Cliente no encontrado');
        $this->seo->setRobots('noindex, nofollow');
        $this->view('errors/404', []);
    }
}
