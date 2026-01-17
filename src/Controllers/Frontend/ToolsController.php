<?php
/**
 * Tools Controller (Frontend)
 * We're Sinapsis CMS
 */

namespace App\Controllers\Frontend;

use App\Models\Tool;
use App\Models\Category;

class ToolsController extends BaseController
{
    private Tool $toolModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->toolModel = new Tool();
        $this->categoryModel = new Category();
    }

    /**
     * Tools listing page
     */
    public function index(): void
    {
        $categorySlug = $_GET['categoria'] ?? null;

        if ($categorySlug) {
            $tools = $this->toolModel->getByCategorySlug($categorySlug);
            $currentCategory = $this->categoryModel->findBySlug($categorySlug);
        } else {
            $tools = $this->toolModel->getActive();
            $currentCategory = null;
        }

        $grouped = $this->toolModel->getGroupedByCategory();
        $categories = $this->categoryModel->getWithToolCount();

        // Translate content
        $this->translator->translateEntities('tool', $tools);
        foreach ($grouped as &$group) {
            $this->translator->translateEntities('tool', $group);
        }

        // SEO
        $title = $currentCategory
            ? "Herramientas de {$currentCategory['name']} | We're Sinapsis"
            : "Herramientas y Tecnologías | We're Sinapsis";
        $this->seo->setTitle($title);
        $this->seo->setDescription('Conoce las herramientas y tecnologías que utilizamos para crear soluciones digitales innovadoras.');
        $this->seo->setCanonical('/herramientas');

        $this->view('tools/index', [
            'tools' => $tools,
            'grouped' => $grouped,
            'categories' => $categories,
            'currentCategory' => $currentCategory
        ]);
    }

    /**
     * Show single tool detail
     */
    public function show(string $slug): void
    {
        $tool = $this->toolModel->findBySlug($slug);

        if (!$tool) {
            $this->notFound();
            return;
        }

        // Get success cases that use this tool
        $successCases = $this->toolModel->getSuccessCases($tool['id'], 6);

        // Get related tools from same category
        $relatedTools = [];
        if (!empty($tool['category_id'])) {
            $relatedTools = array_filter(
                $this->toolModel->getByCategory($tool['category_id'], 5),
                fn($t) => $t['id'] !== $tool['id']
            );
            $relatedTools = array_slice($relatedTools, 0, 4);
        }

        // If not enough related, get featured ones
        if (count($relatedTools) < 4) {
            $allTools = $this->toolModel->getFeatured(8);
            foreach ($allTools as $t) {
                if ($t['id'] !== $tool['id'] && !in_array($t['id'], array_column($relatedTools, 'id'))) {
                    $relatedTools[] = $t;
                    if (count($relatedTools) >= 4) break;
                }
            }
        }

        // Translate content
        $this->translator->translateEntity('tool', $tool);
        $this->translator->translateEntities('tool', $relatedTools);

        // SEO
        $metaTitle = $tool['meta_title'] ?? "{$tool['title']} | Herramientas | We're Sinapsis";
        $metaDesc = $tool['meta_description'] ?? $tool['subtitle'] ?? '';

        $this->seo->setTitle($metaTitle);
        $this->seo->setDescription($metaDesc);
        $this->seo->setCanonical("/herramientas/{$slug}");

        if (!empty($tool['logo'])) {
            $this->seo->setImage($tool['logo']);
        }

        $this->view('tools/show', [
            'tool' => $tool,
            'successCases' => $successCases,
            'relatedTools' => $relatedTools
        ]);
    }

    /**
     * 404 handler
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->seo->setTitle('Herramienta no encontrada');
        $this->seo->setRobots('noindex, nofollow');
        $this->view('errors/404', []);
    }
}
