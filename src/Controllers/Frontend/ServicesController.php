<?php
/**
 * Services Controller (Frontend)
 * We're Sinapsis CMS
 */

namespace App\Controllers\Frontend;

use App\Models\Service;
use App\Models\Category;
use App\Models\ServiceBlock;
use App\Models\Page;
use App\Services\BlockRenderer;

class ServicesController extends BaseController
{
    private Service $serviceModel;
    private Category $categoryModel;
    private ServiceBlock $blockModel;
    private BlockRenderer $blockRenderer;

    public function __construct()
    {
        parent::__construct();
        $this->serviceModel = new Service();
        $this->categoryModel = new Category();
        $this->blockModel = new ServiceBlock();
        $this->blockRenderer = new BlockRenderer();
    }

    /**
     * Services listing page
     */
    public function index(): void
    {
        // Check if there's a page builder page with slug "servicios"
        $pageModel = new Page();
        $page = $pageModel->getPublishedWithBlocks('servicios');

        // If page exists in page builder, use ONLY that page (full control to user)
        if ($page) {
            $this->renderPageBuilderPage($page);
            return;
        }

        // Only show default listing if NO page with slug "servicios" exists
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
        $metaTitle = $page['meta_title'] ?? $page['title'] ?? "Servicios | We're Sinapsis";
        $metaDesc = $page['meta_description'] ?? $page['excerpt'] ?? '';

        $this->seo->setTitle($metaTitle);
        $this->seo->setDescription($metaDesc);
        $this->seo->setCanonical('/servicios');

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
     * Render default services listing
     */
    private function renderDefaultListing(): void
    {
        $categorySlug = $_GET['categoria'] ?? null;

        if ($categorySlug) {
            $services = $this->serviceModel->getByCategorySlug($categorySlug);
            $currentCategory = $this->categoryModel->findBySlug($categorySlug);
        } else {
            $services = $this->serviceModel->getActive();
            $currentCategory = null;
        }

        $categories = $this->categoryModel->getWithServiceCount();
        $featured = $this->serviceModel->getFeatured(3);

        // Translate services
        $this->translator->translateEntities('service', $services);
        $this->translator->translateEntities('service', $featured);

        // SEO
        $title = $currentCategory
            ? "Servicios de {$currentCategory['name']} | We're Sinapsis"
            : "Servicios | We're Sinapsis";
        $this->seo->setTitle($title);
        $this->seo->setDescription('Descubre todos nuestros servicios de transformación digital, desarrollo de software y consultoría tecnológica.');
        $this->seo->setCanonical('/servicios');

        $this->view('services/index', [
            'services' => $services,
            'featured' => $featured,
            'categories' => $categories,
            'currentCategory' => $currentCategory
        ]);
    }

    /**
     * Show single service detail
     */
    public function show(string $slug): void
    {
        $service = $this->serviceModel->findBySlug($slug);

        if (!$service) {
            $this->notFound();
            return;
        }

        // Get success cases for this service
        $successCases = $this->serviceModel->getSuccessCases($service['id'], 6);

        // Get related services
        $relatedServices = $this->serviceModel->getRelated(
            $service['id'],
            $service['category_id'] ?? null,
            4
        );

        // Translate content
        $this->translator->translateEntity('service', $service);
        $this->translator->translateEntities('service', $relatedServices);

        // SEO
        $metaTitle = $service['meta_title'] ?? "{$service['title']} | Servicios | We're Sinapsis";
        $metaDesc = $service['meta_description'] ?? $service['short_description'] ?? '';

        $this->seo->setTitle($metaTitle);
        $this->seo->setDescription($metaDesc);
        $this->seo->setCanonical("/servicios/{$slug}");

        if (!empty($service['image'])) {
            $this->seo->setImage($service['image']);
        }

        // Schema.org Service
        $this->seo->addSchema([
            '@type' => 'Service',
            'name' => $service['title'],
            'description' => $service['short_description'] ?? '',
            'provider' => [
                '@type' => 'Organization',
                'name' => "We're Sinapsis"
            ]
        ]);

        // Get blocks for this service
        $blocks = $this->blockModel->getActiveForService($service['id']);
        $renderedBlocks = $this->blockRenderer->renderBlocks($blocks);

        $this->view('services/show', [
            'service' => $service,
            'successCases' => $successCases,
            'relatedServices' => $relatedServices,
            'renderedBlocks' => $renderedBlocks,
            'adminEditUrl' => '/admin/services/' . $service['id'] . '/edit'
        ]);
    }

    /**
     * 404 handler
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->seo->setTitle('Servicio no encontrado');
        $this->seo->setRobots('noindex, nofollow');
        $this->view('errors/404', []);
    }
}
