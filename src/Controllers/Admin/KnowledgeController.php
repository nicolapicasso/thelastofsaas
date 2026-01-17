<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\KnowledgeArticle;
use App\Models\Category;
use App\Models\SEOMetadata;
use App\Helpers\Sanitizer;
use App\Traits\GeneratesQA;

/**
 * Knowledge Base Controller
 * Omniwallet CMS Admin
 */
class KnowledgeController extends Controller
{
    use GeneratesQA;
    private KnowledgeArticle $article;
    private Category $category;
    private SEOMetadata $seoModel;

    public function __construct()
    {
        parent::__construct();
        $this->article = new KnowledgeArticle();
        $this->category = new Category();
        $this->seoModel = new SEOMetadata();
    }

    /**
     * List all articles
     */
    public function index(): void
    {
        $this->requireAuth();

        $currentStatus = $this->getQuery('status');
        $currentCategory = $this->getQuery('category');

        $articles = $this->article->allWithCategory($currentStatus, $currentCategory ? (int)$currentCategory : null);
        $categories = $this->category->where(['is_active' => 1], ['name' => 'ASC']);

        $this->renderAdmin('knowledge/index', [
            'title' => 'Base de Conocimiento',
            'articles' => $articles,
            'categories' => $categories,
            'currentStatus' => $currentStatus,
            'currentCategory' => $currentCategory,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $categories = $this->category->where(['is_active' => 1], ['name' => 'ASC']);

        $this->renderAdmin('knowledge/form', [
            'title' => 'Nuevo Artículo',
            'article' => null,
            'categories' => $categories,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Store new article
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/knowledge/create');
        }

        $data = $this->getFormData();

        if (empty($data['title'])) {
            $this->flash('error', 'El título es obligatorio');
            $this->redirect('/admin/knowledge/create');
        }

        try {
            $this->article->createArticle($data);
            $this->flash('success', 'Artículo creado correctamente');
            $this->redirect('/admin/knowledge');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el artículo');
            $this->redirect('/admin/knowledge/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $article = $this->article->find((int) $id);

        if (!$article) {
            $this->flash('error', 'Artículo no encontrado');
            $this->redirect('/admin/knowledge');
        }

        // Merge SEO metadata from seo_metadata table (for mass generation support)
        $seoData = $this->seoModel->getForEntity('knowledge', (int) $id, 'es');
        if ($seoData) {
            if (empty($article['meta_title']) && !empty($seoData['meta_title'])) {
                $article['meta_title'] = $seoData['meta_title'];
            }
            if (empty($article['meta_description']) && !empty($seoData['meta_description'])) {
                $article['meta_description'] = $seoData['meta_description'];
            }
        }

        $categories = $this->category->where(['is_active' => 1], ['name' => 'ASC']);

        $this->renderAdmin('knowledge/form', [
            'title' => 'Editar Artículo',
            'article' => $article,
            'categories' => $categories,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Update article
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/knowledge/' . $id . '/edit');
        }

        $article = $this->article->find((int) $id);

        if (!$article) {
            $this->flash('error', 'Artículo no encontrado');
            $this->redirect('/admin/knowledge');
        }

        $data = $this->getFormData();

        if (empty($data['title'])) {
            $this->flash('error', 'El título es obligatorio');
            $this->redirect('/admin/knowledge/' . $id . '/edit');
        }

        try {
            $this->article->update((int) $id, $data);

            // Also save to seo_metadata for consistency with mass generation
            $metaTitle = $data['meta_title'] ?? null;
            $metaDescription = $data['meta_description'] ?? null;
            if (!empty($metaTitle) || !empty($metaDescription)) {
                $this->seoModel->saveForEntity('knowledge', (int) $id, 'es', [
                    'meta_title' => $metaTitle,
                    'meta_description' => $metaDescription,
                ]);
            }

            $this->flash('success', 'Artículo actualizado correctamente');
            $this->redirect('/admin/knowledge');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar el artículo');
            $this->redirect('/admin/knowledge/' . $id . '/edit');
        }
    }

    /**
     * Delete article
     */
    public function delete(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/knowledge');
        }

        try {
            $this->article->delete((int) $id);
            $this->flash('success', 'Artículo eliminado');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar el artículo');
        }

        $this->redirect('/admin/knowledge');
    }

    /**
     * Get form data from POST
     */
    private function getFormData(): array
    {
        return [
            'title' => Sanitizer::string($this->getPost('title')),
            'slug' => Sanitizer::string($this->getPost('slug')),
            'category_id' => Sanitizer::int($this->getPost('category_id')) ?: null,
            'excerpt' => $this->getPost('excerpt'),
            'content' => $this->getPost('content'),
            'tags' => Sanitizer::string($this->getPost('tags')),
            'featured_image' => Sanitizer::string($this->getPost('featured_image')),
            'sort_order' => Sanitizer::int($this->getPost('sort_order', 0)),
            'is_featured' => $this->getPost('is_featured') ? 1 : 0,
            'status' => Sanitizer::string($this->getPost('status', 'draft')),
            'meta_title' => Sanitizer::string($this->getPost('meta_title')),
            'meta_description' => Sanitizer::string($this->getPost('meta_description')),
            'enable_llm_qa' => $this->getPost('enable_llm_qa') ? 1 : 0,
            'llm_qa_content' => $this->getPost('llm_qa_content') ?: null,
        ];
    }

    /**
     * Generate Q&A with AI
     */
    public function generateQA(string $id): void
    {
        $this->handleGenerateQA((int) $id, KnowledgeArticle::class, 'knowledge_article', ['title', 'excerpt', 'content']);
    }
}
