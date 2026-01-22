<?php
/**
 * Blog Controller (Frontend)
 * Omniwallet CMS
 */

namespace App\Controllers\Frontend;

use App\Models\Post;
use App\Models\Category;

class BlogController extends BaseController
{
    private Post $postModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->postModel = new Post();
        $this->categoryModel = new Category();
    }

    /**
     * Blog listing
     */
    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $categorySlug = $_GET['categoria'] ?? null;
        $categoryId = null;
        $currentCategory = null;

        if ($categorySlug) {
            $currentCategory = $this->categoryModel->findBySlug($categorySlug);
            $categoryId = $currentCategory ? $currentCategory['id'] : null;
        }

        $result = $this->postModel->getPaginated($page, 12, $categoryId);
        $allCategories = $this->categoryModel->getWithPostCount() ?? [];
        $categories = array_filter($allCategories, fn($c) => $c['post_count'] > 0);
        $featuredPosts = $this->postModel->getFeatured(3) ?? [];

        // Translate posts (ensure arrays are not null)
        if (!empty($result['items'])) {
            $this->translator->translateEntities('post', $result['items']);
        }
        if (!empty($featuredPosts)) {
            $this->translator->translateEntities('post', $featuredPosts);
        }
        if (!empty($categories)) {
            $this->translator->translateEntities('category', $categories);
        }

        // SEO
        $title = $currentCategory
            ? "Observatorio SaaS: {$currentCategory['name']} | The Last of SaaS"
            : 'Observatorio SaaS | The Last of SaaS';

        $this->seo->setTitle($title);
        $this->seo->setDescription('Descubre las últimas noticias, guías y artículos sobre SaaS, herramientas digitales y tendencias del sector en el Observatorio SaaS.');
        $this->seo->setCanonical('/observatorio-saas');

        // Blog list schema
        $this->seo->addSchema([
            '@type' => 'Blog',
            'name' => 'Observatorio SaaS - The Last of SaaS',
            'description' => 'Noticias y artículos sobre SaaS y herramientas digitales',
            'url' => 'https://thelastofsaas.es/observatorio-saas'
        ]);

        $this->view('blog/index', [
            'posts' => $result['items'],
            'pagination' => [
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
                'total' => $result['total']
            ],
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'featuredPosts' => $featuredPosts
        ]);
    }

    /**
     * Show single post
     */
    public function show(string $slug): void
    {
        $post = $this->postModel->findBySlug($slug);

        if (!$post) {
            $this->notFound();
            return;
        }

        // Get related posts
        $relatedPosts = $this->postModel->getRelated($post['id'], $post['category_id'], 3) ?? [];

        // Get categories with post count (only those with posts)
        $allCategories = $this->categoryModel->getWithPostCount() ?? [];
        $categories = array_filter($allCategories, fn($c) => $c['post_count'] > 0);

        // Translate post and related posts
        $this->translator->translateEntity('post', $post);
        if (!empty($relatedPosts)) {
            $this->translator->translateEntities('post', $relatedPosts);
        }

        // SEO
        $this->seo->setTitle($post['meta_title'] ?? $post['title'] . ' | Observatorio SaaS');
        $this->seo->setDescription($post['meta_description'] ?? $post['excerpt'] ?? '');
        $this->seo->setCanonical("/observatorio-saas/{$slug}");
        $this->seo->setImage($post['hero_image'] ?? '');
        $this->seo->setType('article');

        // Article schema
        $this->seo->addSchema([
            '@type' => 'Article',
            'headline' => $post['title'],
            'description' => $post['excerpt'] ?? '',
            'image' => $post['hero_image'] ?? '',
            'datePublished' => $post['published_at'] ?? $post['created_at'],
            'dateModified' => $post['updated_at'] ?? $post['created_at'],
            'author' => [
                '@type' => 'Organization',
                'name' => 'The Last of SaaS'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'The Last of SaaS',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => 'https://thelastofsaas.es/assets/images/logo.png'
                ]
            ]
        ]);

        $this->view('blog/show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'categories' => $categories,
            'adminEditUrl' => '/admin/posts/' . $post['id'] . '/edit'
        ]);
    }

    /**
     * Redirect /blog to /observatorio-saas (301)
     */
    public function redirectToObservatorio(): void
    {
        header('Location: /observatorio-saas', true, 301);
        exit;
    }

    /**
     * Redirect /blog/{slug} to /observatorio-saas/{slug} (301)
     */
    public function redirectPostToObservatorio(string $slug): void
    {
        header('Location: /observatorio-saas/' . $slug, true, 301);
        exit;
    }

    /**
     * 404 handler
     */
    protected function notFound(): void
    {
        http_response_code(404);
        $this->seo->setTitle('Artículo no encontrado');
        $this->seo->setRobots('noindex, nofollow');
        $this->view('errors/404', []);
    }
}
