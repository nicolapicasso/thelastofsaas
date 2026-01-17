<?php
/**
 * Success Cases Controller (Frontend)
 * We're Sinapsis CMS
 */

namespace App\Controllers\Frontend;

use App\Models\SuccessCase;
use App\Models\Client;
use App\Models\Category;
use App\Models\Page;
use App\Services\BlockRenderer;

class CasesController extends BaseController
{
    private SuccessCase $caseModel;
    private Client $clientModel;
    private Category $categoryModel;
    private BlockRenderer $blockRenderer;

    public function __construct()
    {
        parent::__construct();
        $this->caseModel = new SuccessCase();
        $this->clientModel = new Client();
        $this->categoryModel = new Category();
        $this->blockRenderer = new BlockRenderer();
    }

    /**
     * Success cases listing
     */
    public function index(): void
    {
        // Check if there's a page builder page with slug "casos-de-exito"
        $pageModel = new Page();
        $page = $pageModel->getPublishedWithBlocks('casos-de-exito');

        // If page exists in page builder, use ONLY that page (full control to user)
        if ($page) {
            $this->renderPageBuilderPage($page);
            return;
        }

        // Only show default listing if NO page with slug "casos-de-exito" exists
        $this->renderDefaultListing();
    }

    /**
     * Render page using page builder blocks
     */
    private function renderPageBuilderPage(array $page): void
    {
        // Translate page
        $this->translator->translateEntity('page', $page);

        // SEO
        $metaTitle = $page['meta_title'] ?? $page['title'] ?? "Casos de Éxito | We're Sinapsis";
        $metaDesc = $page['meta_description'] ?? $page['excerpt'] ?? '';

        $this->seo->setTitle($metaTitle);
        $this->seo->setDescription($metaDesc);
        $this->seo->setCanonical('/casos-de-exito');

        if (!empty($page['featured_image'])) {
            $this->seo->setImage($page['featured_image']);
        }

        // Render blocks (if any)
        $renderedBlocks = $this->blockRenderer->renderBlocks($page['blocks'] ?? []);

        $this->view('pages/show', [
            'page' => $page,
            'renderedBlocks' => $renderedBlocks,
            'blocks' => $page['blocks'] ?? []
        ]);
    }

    /**
     * Render default cases listing
     */
    private function renderDefaultListing(): void
    {
        $categorySlug = $_GET['categoria'] ?? null;
        $industry = $_GET['industria'] ?? null;
        $clientId = !empty($_GET['cliente']) ? (int)$_GET['cliente'] : null;
        $search = trim($_GET['buscar'] ?? '');

        // Get current category if filtering
        $currentCategory = null;
        $categoryId = null;
        if ($categorySlug) {
            $currentCategory = $this->categoryModel->findBySlug($categorySlug);
            $categoryId = $currentCategory['id'] ?? null;
        }

        // Get current client if filtering
        $currentClient = null;
        if ($clientId) {
            $currentClient = $this->clientModel->find($clientId);
        }

        // Get cases based on filters
        if ($search || $categoryId || $industry || $clientId) {
            $cases = $this->caseModel->search($search, $categoryId, $industry, $clientId);
        } else {
            $cases = $this->caseModel->getActive(50);
        }

        // Get categories with case counts
        $categories = $this->categoryModel->getWithCaseCount();

        // Get unique industries from clients with case count
        $industries = $this->caseModel->getIndustriesWithCaseCount();

        // Get clients with case count
        $clients = $this->caseModel->getClientsWithCaseCount();

        $totalCases = $this->caseModel->countPublished();
        $featuredCases = $this->caseModel->getFeatured(3);

        // Translate cases
        $this->translator->translateEntities('success_case', $cases);
        $this->translator->translateEntities('success_case', $featuredCases);

        // SEO
        $title = $currentCategory
            ? "Casos de Éxito - {$currentCategory['name']} | We're Sinapsis"
            : "Casos de Éxito | We're Sinapsis";
        $this->seo->setTitle($title);
        $this->seo->setDescription('Descubre cómo empresas de diferentes sectores han transformado sus operaciones con nuestras soluciones. Casos reales con resultados medibles.');
        $this->seo->setCanonical('/casos-de-exito');

        $this->view('cases/index', [
            'cases' => $cases,
            'featuredCases' => $featuredCases,
            'categories' => $categories,
            'industries' => $industries,
            'clients' => $clients,
            'totalCases' => $totalCases,
            'currentCategory' => $currentCategory,
            'currentIndustry' => $industry,
            'currentClient' => $currentClient,
            'searchQuery' => $search
        ]);
    }

    /**
     * Show single case
     */
    public function show(string $slug): void
    {
        $case = $this->caseModel->findBySlug($slug);

        if (!$case) {
            $this->notFound();
            return;
        }

        // Get services and tools for this case
        $services = $this->caseModel->getServices($case['id']);
        $tools = $this->caseModel->getTools($case['id']);

        // Get related cases (same category or services)
        $relatedCases = $this->caseModel->getRelated(
            $case['id'],
            $case['category_id'] ?? null,
            3
        );

        // Get other cases from same client
        $clientCases = [];
        if (!empty($case['client_id'])) {
            $clientCases = $this->caseModel->getOtherClientCases(
                $case['id'],
                $case['client_id'],
                3
            );
        }

        // Translate case and related
        $this->translator->translateEntity('success_case', $case);
        $this->translator->translateEntities('success_case', $relatedCases);
        $this->translator->translateEntities('success_case', $clientCases);

        // SEO
        $companyName = $case['client_name'] ?? 'Cliente';
        $metaTitle = $case['meta_title'] ?? "Caso de Éxito: {$case['title']} | We're Sinapsis";
        $metaDesc = $case['meta_description'] ?? ($case['challenge'] ? substr(strip_tags($case['challenge']), 0, 160) : '');

        $this->seo->setTitle($metaTitle);
        $this->seo->setDescription($metaDesc);
        $this->seo->setCanonical("/casos-de-exito/{$slug}");
        $this->seo->setImage($case['featured_image'] ?? $case['client_logo'] ?? '');

        // Review schema
        if ($case['testimonial']) {
            $this->seo->addSchema([
                '@type' => 'Review',
                'itemReviewed' => [
                    '@type' => 'Organization',
                    'name' => "We're Sinapsis"
                ],
                'author' => [
                    '@type' => 'Person',
                    'name' => $case['testimonial_author'] ?? $companyName,
                    'jobTitle' => $case['testimonial_role'] ?? ''
                ],
                'reviewBody' => strip_tags($case['testimonial']),
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => 5,
                    'bestRating' => 5
                ]
            ]);
        }

        // Case Study schema
        $this->seo->addSchema([
            '@type' => 'Article',
            'headline' => $case['title'],
            'description' => $metaDesc,
            'author' => [
                '@type' => 'Organization',
                'name' => "We're Sinapsis"
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => "We're Sinapsis"
            ],
            'datePublished' => $case['published_at'] ?? $case['created_at'],
            'about' => [
                '@type' => 'Organization',
                'name' => $companyName
            ]
        ]);

        $this->view('cases/show', [
            'case' => $case,
            'services' => $services,
            'tools' => $tools,
            'relatedCases' => $relatedCases,
            'clientCases' => $clientCases
        ]);
    }

    /**
     * 404 handler
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->seo->setTitle('Caso no encontrado');
        $this->seo->setRobots('noindex, nofollow');
        $this->view('errors/404', []);
    }
}
