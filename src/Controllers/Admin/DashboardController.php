<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\Category;

/**
 * Dashboard Controller
 * Omniwallet CMS
 */
class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Dashboard index
     */
    public function index(): void
    {
        $this->requireAuth();

        // Get stats
        $pageModel = new Page();
        $categoryModel = new Category();

        $stats = [
            'pages' => [
                'total' => $pageModel->count(),
                'published' => $pageModel->count(['status' => 'published']),
                'draft' => $pageModel->count(['status' => 'draft']),
            ],
            'categories' => [
                'total' => $categoryModel->count(),
                'active' => $categoryModel->count(['is_active' => 1]),
            ],
        ];

        // Get recent pages
        $recentPages = $pageModel->paginate(1, 5, [], ['updated_at' => 'DESC'])['data'];

        $this->renderAdmin('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'recentPages' => $recentPages,
            'flash' => $this->getFlash(),
        ]);
    }
}
