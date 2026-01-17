<?php
/**
 * Categories Controller (Frontend)
 * We're Sinapsis CMS
 */

namespace App\Controllers\Frontend;

use App\Models\Category;

class CategoriesController extends BaseController
{
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    /**
     * Categories listing
     */
    public function index(): void
    {
        // Get categories that have content
        $categories = $this->categoryModel->getWithContent();

        // SEO
        $this->seo->setTitle("Categorías | We're Sinapsis");
        $this->seo->setDescription('Explora nuestro contenido organizado por categorías: servicios, casos de éxito, herramientas y más.');
        $this->seo->setCanonical('/categorias');

        $this->view('categories/index', [
            'categories' => $categories
        ]);
    }

    /**
     * Show single category with all its content
     */
    public function show(string $slug): void
    {
        $category = $this->categoryModel->findBySlug($slug);

        if (!$category) {
            $this->notFound();
            return;
        }

        // Get category with content counts
        $category = $this->categoryModel->getWithContentCounts($category['id']);

        // Get all content for this category
        $content = $this->categoryModel->getAllContent($category['id']);

        // Get child categories if hierarchical
        $children = $this->categoryModel->getChildren($category['id']);

        // Get breadcrumbs
        $breadcrumbs = $this->categoryModel->getBreadcrumbs($category['id']);

        // SEO
        $metaTitle = $category['meta_title'] ?? "{$category['name']} | We're Sinapsis";
        $metaDesc = $category['meta_description'] ?? strip_tags($category['description'] ?? '');

        $this->seo->setTitle($metaTitle);
        $this->seo->setDescription($metaDesc);
        $this->seo->setCanonical("/categorias/{$slug}");

        if (!empty($category['featured_image'])) {
            $this->seo->setImage($category['featured_image']);
        }

        // Schema.org CollectionPage
        $this->seo->addSchema([
            '@type' => 'CollectionPage',
            'name' => $category['name'],
            'description' => $metaDesc,
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $category['total_content'] ?? 0
            ]
        ]);

        $this->view('categories/show', [
            'category' => $category,
            'content' => $content,
            'children' => $children,
            'breadcrumbs' => $breadcrumbs,
            'adminEditUrl' => '/admin/categories/' . $category['id'] . '/edit'
        ]);
    }

    /**
     * 404 handler
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->seo->setTitle('Categoría no encontrada');
        $this->seo->setRobots('noindex, nofollow');
        $this->view('errors/404', []);
    }
}
