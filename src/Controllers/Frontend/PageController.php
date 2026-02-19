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
        $siteName = $this->getSetting('site_name', '');
        $siteUrl = $this->getSetting('site_url', '');
        $siteLogo = $this->getSetting('logo_header', '/assets/images/logo.svg');
        $siteTagline = $this->getSetting('site_tagline', '');

        $schema = [
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $siteUrl,
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'availableLanguage' => ['Spanish', 'English']
            ]
        ];

        if ($siteLogo) {
            $logoUrl = strpos($siteLogo, 'http') === 0 ? $siteLogo : rtrim($siteUrl, '/') . $siteLogo;
            $schema['logo'] = $logoUrl;
        }

        if ($siteTagline) {
            $schema['description'] = $siteTagline;
        }

        return $schema;
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
