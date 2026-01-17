<?php
/**
 * Frontend Knowledge Base Controller
 * Omniwallet CMS
 */

namespace App\Controllers\Frontend;

use App\Models\KnowledgeArticle;
use App\Models\Category;

class KnowledgeController extends BaseController
{
    private KnowledgeArticle $articleModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->articleModel = new KnowledgeArticle();
        $this->categoryModel = new Category();
    }

    /**
     * Knowledge base index - show all categories and articles
     */
    public function index(): void
    {
        // Get categories with article counts
        $categories = $this->articleModel->getCategoriesWithCounts();

        // Get all articles grouped by category for sidebar
        $groupedArticles = $this->articleModel->getGroupedByCategory();

        // Get featured/popular articles
        $popularArticles = $this->articleModel->getPopular(6);

        // Get recent articles
        $recentArticles = $this->articleModel->getRecent(5);

        // Translate content
        $this->translator->translateEntities('category', $categories);
        $this->translator->translateEntities('knowledge_article', $popularArticles);
        $this->translator->translateEntities('knowledge_article', $recentArticles);
        foreach ($groupedArticles as &$group) {
            if (isset($group['name']) && isset($group['id'])) {
                // Translate category name in grouped articles
                $categoryEntity = ['id' => $group['id'], 'name' => $group['name']];
                $this->translator->translateEntity('category', $categoryEntity);
                $group['name'] = $categoryEntity['name'];
            }
            if (isset($group['articles'])) {
                $this->translator->translateEntities('knowledge_article', $group['articles']);
            }
        }

        // SEO
        $this->seo->setTitle('Base de Conocimiento');
        $this->seo->setDescription('Encuentra respuestas a tus preguntas sobre Omniwallet. Guías, tutoriales y documentación.');

        $this->view('knowledge/index', [
            'categories' => $categories,
            'groupedArticles' => $groupedArticles,
            'popularArticles' => $popularArticles,
            'recentArticles' => $recentArticles
        ]);
    }

    /**
     * Show category with articles
     */
    public function category(string $categorySlug): void
    {
        // Get category info
        $category = $this->categoryModel->findBySlug($categorySlug);

        if (!$category) {
            $this->notFound();
            return;
        }

        // Get articles for this category
        $articles = $this->articleModel->getByCategory($categorySlug);

        // Get all categories for sidebar
        $allCategories = $this->articleModel->getCategoriesWithCounts();

        // Get all articles grouped for sidebar navigation
        $groupedArticles = $this->articleModel->getGroupedByCategory();

        // Translate content
        $this->translator->translateEntity('category', $category);
        $this->translator->translateEntities('category', $allCategories);
        $this->translator->translateEntities('knowledge_article', $articles);
        foreach ($groupedArticles as &$group) {
            if (isset($group['name']) && isset($group['id'])) {
                $categoryEntity = ['id' => $group['id'], 'name' => $group['name']];
                $this->translator->translateEntity('category', $categoryEntity);
                $group['name'] = $categoryEntity['name'];
            }
            if (isset($group['articles'])) {
                $this->translator->translateEntities('knowledge_article', $group['articles']);
            }
        }

        // SEO
        $this->seo->setTitle($category['name'] . ' - Base de Conocimiento');
        $this->seo->setDescription('Artículos de ayuda sobre ' . $category['name']);

        $this->view('knowledge/category', [
            'category' => $category,
            'articles' => $articles,
            'allCategories' => $allCategories,
            'groupedArticles' => $groupedArticles
        ]);
    }

    /**
     * Show single article
     */
    public function article(string $articleSlug): void
    {
        $article = $this->articleModel->getBySlug($articleSlug);

        if (!$article || $article['status'] !== 'published') {
            $this->notFound();
            return;
        }

        // Increment view count
        $this->articleModel->incrementViews($article['id']);

        // Get related articles (same category)
        $relatedArticles = $this->articleModel->getRelated(
            $article['id'],
            $article['category_id'] ? (int)$article['category_id'] : null,
            4
        );

        // Get all articles grouped for sidebar navigation
        $groupedArticles = $this->articleModel->getGroupedByCategory();

        // Get all categories for sidebar
        $allCategories = $this->articleModel->getCategoriesWithCounts();

        // Translate content
        $this->translator->translateEntity('knowledge_article', $article);
        $this->translator->translateEntities('category', $allCategories);
        $this->translator->translateEntities('knowledge_article', $relatedArticles);
        foreach ($groupedArticles as &$group) {
            if (isset($group['name']) && isset($group['id'])) {
                $categoryEntity = ['id' => $group['id'], 'name' => $group['name']];
                $this->translator->translateEntity('category', $categoryEntity);
                $group['name'] = $categoryEntity['name'];
            }
            if (isset($group['articles'])) {
                $this->translator->translateEntities('knowledge_article', $group['articles']);
            }
        }

        // SEO
        $this->seo->setTitle($article['title'] . ' - Base de Conocimiento');
        $this->seo->setDescription($article['excerpt'] ?? substr(strip_tags($article['content']), 0, 160));
        $this->seo->setCanonical("/ayuda/{$articleSlug}");

        // Get base URL for schema
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

        // Breadcrumb schema
        $breadcrumbItems = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Base de Conocimiento',
                'item' => $baseUrl . '/ayuda'
            ]
        ];

        if ($article['category_name']) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $article['category_name'],
                'item' => $baseUrl . '/ayuda/categoria/' . $article['category_slug']
            ];
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $article['title']
            ];
        } else {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $article['title']
            ];
        }

        $this->seo->addSchema([
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems
        ]);

        // Article schema
        $this->seo->addSchema([
            '@type' => 'TechArticle',
            'headline' => $article['title'],
            'description' => $article['excerpt'] ?? '',
            'datePublished' => $article['published_at'] ?? $article['created_at'],
            'dateModified' => $article['updated_at']
        ]);

        $this->view('knowledge/article', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'groupedArticles' => $groupedArticles,
            'allCategories' => $allCategories
        ]);
    }

    /**
     * Search knowledge base
     */
    public function search(): void
    {
        $query = $_GET['q'] ?? '';
        $results = [];

        if (strlen($query) >= 2) {
            $results = $this->articleModel->search($query);
        }

        // Get all articles grouped for sidebar
        $groupedArticles = $this->articleModel->getGroupedByCategory();

        // If AJAX request, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'results' => $results,
                'count' => count($results)
            ]);
            exit;
        }

        // SEO
        $this->seo->setTitle('Buscar - Base de Conocimiento');
        $this->seo->setRobots('noindex, follow');

        $this->view('knowledge/search', [
            'query' => $query,
            'results' => $results,
            'groupedArticles' => $groupedArticles
        ]);
    }

    /**
     * Handle 404
     */
    private function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404');
    }
}
