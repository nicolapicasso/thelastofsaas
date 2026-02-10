<?php
/**
 * Page Controller (Frontend)
 * Omniwallet CMS
 */

namespace App\Controllers\Frontend;

use App\Models\Page;
use App\Models\Block;
use App\Services\BlockRenderer;

class PageController extends BaseController
{
    private Page $pageModel;
    private Block $blockModel;
    private BlockRenderer $blockRenderer;

    public function __construct()
    {
        parent::__construct();
        $this->pageModel = new Page();
        $this->blockModel = new Block();
        $this->blockRenderer = new BlockRenderer();
    }

    /**
     * Home page
     */
    public function home(): void
    {
        $page = $this->pageModel->findBySlug('home');

        if (!$page) {
            // Create default home page data
            $page = [
                'title' => "We're Sinapsis - Agencia de Marketing Digital",
                'meta_title' => "We're Sinapsis | Agencia de Marketing Digital",
                'meta_description' => "We're Sinapsis es tu agencia de marketing digital. Estrategia, creatividad y tecnología para impulsar tu negocio."
            ];
            $blocks = [];
        } else {
            $blocks = $this->blockModel->getActiveForPage($page['id']);
            // Translate page
            $this->translator->translateEntity('page', $page);
        }

        // Set SEO
        $this->seo->setTitle($page['meta_title'] ?? $page['title']);
        $this->seo->setDescription($page['meta_description'] ?? '');
        $this->seo->setCanonical('/');
        $this->seo->addSchema($this->getOrganizationSchema());

        $this->view('pages/home', [
            'page' => $page,
            'blocks' => $blocks,
            'renderedBlocks' => $this->blockRenderer->renderBlocks($blocks),
            'adminEditUrl' => isset($page['id']) ? '/admin/pages/' . $page['id'] . '/edit' : null
        ]);
    }

    /**
     * Show page by slug
     */
    public function show(string $slug): void
    {
        $page = $this->pageModel->findBySlug($slug);

        if (!$page) {
            $this->notFound();
            return;
        }

        $blocks = $this->blockModel->getActiveForPage($page['id']);

        // Translate page
        $this->translator->translateEntity('page', $page);

        // Set SEO
        $this->seo->setTitle($page['meta_title'] ?? $page['title']);
        $this->seo->setDescription($page['meta_description'] ?? '');
        $this->seo->setCanonical("/{$slug}");

        if (!empty($page['schema_type'])) {
            $this->seo->addSchema($this->getPageSchema($page));
        }

        $this->view('pages/show', [
            'page' => $page,
            'blocks' => $blocks,
            'renderedBlocks' => $this->blockRenderer->renderBlocks($blocks),
            'adminEditUrl' => '/admin/pages/' . $page['id'] . '/edit'
        ]);
    }

    /**
     * Redirect old event URL to home (301 permanent)
     */
    public function redirectToHome(): void
    {
        header('Location: /', true, 301);
        exit;
    }

    /**
     * 404 Not Found
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->seo->setTitle('Página no encontrada');
        $this->seo->setRobots('noindex, nofollow');

        $this->view('errors/404', []);
    }

    /**
     * Get Organization schema
     */
    private function getOrganizationSchema(): array
    {
        return [
            '@type' => 'Organization',
            'name' => "We're Sinapsis",
            'url' => 'https://sinapsis.agency',
            'logo' => 'https://sinapsis.agency/assets/images/logo.png',
            'description' => 'Agencia de marketing digital',
            'sameAs' => [
                'https://linkedin.com/company/sinapsis',
                'https://instagram.com/weresinapsis'
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'availableLanguage' => ['Spanish', 'English']
            ]
        ];
    }

    /**
     * Get page-specific schema
     */
    private function getPageSchema(array $page): array
    {
        return [
            '@type' => $page['schema_type'] ?? 'WebPage',
            'name' => $page['title'],
            'description' => $page['meta_description'] ?? '',
            'url' => "https://sinapsis.agency/{$page['slug']}"
        ];
    }
}
