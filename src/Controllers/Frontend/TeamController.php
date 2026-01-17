<?php
/**
 * Team Controller (Frontend)
 * Omniwallet CMS
 */

namespace App\Controllers\Frontend;

use App\Models\TeamMember;
use App\Models\Page;
use App\Models\Block;
use App\Services\BlockRenderer;

class TeamController extends BaseController
{
    private TeamMember $teamModel;
    private Page $pageModel;
    private Block $blockModel;
    private BlockRenderer $blockRenderer;

    public function __construct()
    {
        parent::__construct();
        $this->teamModel = new TeamMember();
        $this->pageModel = new Page();
        $this->blockModel = new Block();
        $this->blockRenderer = new BlockRenderer();
    }

    /**
     * Team listing page
     * First checks if there's a custom page with slug 'equipo'
     */
    public function index(): void
    {
        // Check if there's a custom page with this slug
        $page = $this->pageModel->findBySlug('equipo');

        if ($page) {
            $blocks = $this->blockModel->getActiveForPage($page['id']);

            // If page has blocks, render it as a custom page
            if (!empty($blocks)) {
                // Translate page
                $this->translator->translateEntity('page', $page);

                // Set SEO
                $this->seo->setTitle($page['meta_title'] ?? $page['title']);
                $this->seo->setDescription($page['meta_description'] ?? '');
                $this->seo->setCanonical('/equipo');

                $this->view('pages/show', [
                    'page' => $page,
                    'blocks' => $blocks,
                    'renderedBlocks' => $this->blockRenderer->renderBlocks($blocks),
                    'adminEditUrl' => '/admin/pages/' . $page['id'] . '/edit'
                ]);
                return;
            }
        }

        // Fallback to default team listing
        $members = $this->teamModel->getAll();

        // SEO
        $this->seo->setTitle('Nuestro Equipo | Omniwallet');
        $this->seo->setDescription('Conoce al equipo de profesionales detras de Omniwallet. Expertos en tecnologia financiera comprometidos con tu exito.');
        $this->seo->setCanonical('/equipo');

        $this->view('team/index', [
            'members' => $members
        ]);
    }

    /**
     * Show single team member detail
     */
    public function show(string $slug): void
    {
        $member = $this->teamModel->findBySlug($slug);

        if (!$member) {
            $this->notFound();
            return;
        }

        // Get other team members for "others" section
        $otherMembers = array_filter(
            $this->teamModel->getAll(10),
            fn($m) => $m['id'] !== $member['id']
        );
        $otherMembers = array_slice($otherMembers, 0, 4);

        // SEO
        $title = $member['name'];
        if (!empty($member['role'])) {
            $title .= ' - ' . $member['role'];
        }
        $this->seo->setTitle("{$title} | Equipo | Omniwallet");

        $description = !empty($member['bio'])
            ? substr(strip_tags($member['bio']), 0, 160)
            : "Conoce a {$member['name']}, parte del equipo de Omniwallet.";
        $this->seo->setDescription($description);
        $this->seo->setCanonical("/equipo/{$slug}");

        if (!empty($member['photo'])) {
            $this->seo->setImage($member['photo']);
        }

        $this->view('team/show', [
            'member' => $member,
            'otherMembers' => $otherMembers
        ]);
    }

    /**
     * 404 handler
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->seo->setTitle('Miembro no encontrado');
        $this->seo->setRobots('noindex, nofollow');
        $this->view('errors/404', []);
    }
}
