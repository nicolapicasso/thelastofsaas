<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Helpers\Sanitizer;
use App\Traits\GeneratesQA;

/**
 * Posts Controller
 * Omniwallet CMS
 */
class PostsController extends Controller
{
    use GeneratesQA;
    private Post $postModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->postModel = new Post();
        $this->categoryModel = new Category();
    }

    /**
     * List all posts
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) ($this->getQuery('page', 1));
        $status = $this->getQuery('status');
        $categoryId = $this->getQuery('category');

        $conditions = [];
        if ($status) {
            $conditions['status'] = $status;
        }
        if ($categoryId) {
            $conditions['category_id'] = (int) $categoryId;
        }

        $result = $this->postModel->paginate($page, 20, $conditions, ['created_at' => 'DESC']);
        $posts = $this->postModel->getAllWithRelations($conditions, ['created_at' => 'DESC']);
        $categories = $this->categoryModel->getActive();

        $this->renderAdmin('posts/index', [
            'title' => 'Blog',
            'posts' => $posts,
            'pagination' => $result['pagination'],
            'categories' => $categories,
            'currentStatus' => $status,
            'currentCategory' => $categoryId,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $categories = $this->categoryModel->getActive();

        $this->renderAdmin('posts/form', [
            'title' => 'Nuevo Post',
            'post' => null,
            'categories' => $categories,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new post
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/posts/create');
        }

        $data = $this->validatePostData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/posts/create');
        }

        $data['author_id'] = $this->getUserId();

        try {
            $postId = $this->postModel->createWithSlug($data);
            $this->flash('success', 'Post creado correctamente.');
            $this->redirect('/admin/posts/' . $postId . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el post: ' . $e->getMessage());
            $this->redirect('/admin/posts/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $post = $this->postModel->find((int) $id);

        if (!$post) {
            $this->flash('error', 'Post no encontrado.');
            $this->redirect('/admin/posts');
        }

        $categories = $this->categoryModel->getActive();

        $this->renderAdmin('posts/form', [
            'title' => 'Editar Post',
            'post' => $post,
            'categories' => $categories,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update post
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/posts/' . $id . '/edit');
        }

        $post = $this->postModel->find((int) $id);

        if (!$post) {
            $this->flash('error', 'Post no encontrado.');
            $this->redirect('/admin/posts');
        }

        $data = $this->validatePostData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/posts/' . $id . '/edit');
        }

        try {
            $this->postModel->updateWithSlug((int) $id, $data);
            $this->flash('success', 'Post actualizado correctamente.');
            $this->redirect('/admin/posts/' . $id . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar el post: ' . $e->getMessage());
            $this->redirect('/admin/posts/' . $id . '/edit');
        }
    }

    /**
     * Delete post
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/posts');
        }

        $post = $this->postModel->find((int) $id);

        if (!$post) {
            $this->flash('error', 'Post no encontrado.');
            $this->redirect('/admin/posts');
        }

        try {
            $this->postModel->delete((int) $id);
            $this->flash('success', 'Post eliminado correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar el post: ' . $e->getMessage());
        }

        $this->redirect('/admin/posts');
    }

    /**
     * Validate post form data
     */
    private function validatePostData(): array
    {
        $errors = [];

        $title = Sanitizer::string($this->getPost('title'));
        $slug = Sanitizer::string($this->getPost('slug'));
        $categoryId = Sanitizer::int($this->getPost('category_id')) ?: null;
        $subtitle = Sanitizer::string($this->getPost('subtitle'));
        $excerpt = Sanitizer::string($this->getPost('excerpt'));
        $content = $this->getPost('content'); // Allow HTML
        $heroImage = Sanitizer::url($this->getPost('hero_image'));
        $thumbnail = Sanitizer::url($this->getPost('thumbnail'));
        $videoUrl = Sanitizer::url($this->getPost('video_url'));
        $metaTitle = Sanitizer::string($this->getPost('meta_title'));
        $metaDescription = Sanitizer::string($this->getPost('meta_description'));
        $status = $this->getPost('status', 'draft');
        $isFeatured = Sanitizer::bool($this->getPost('is_featured'));

        if (empty($title)) {
            $errors[] = 'El título es obligatorio.';
        }

        if (!in_array($status, ['draft', 'published', 'archived'])) {
            $errors[] = 'Estado no válido.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        $enableLlmQa = Sanitizer::bool($this->getPost('enable_llm_qa'));
        $llmQaContent = $this->getPost('llm_qa_content');

        return [
            'title' => $title,
            'slug' => $slug ?: null,
            'category_id' => $categoryId,
            'subtitle' => $subtitle ?: null,
            'excerpt' => $excerpt ?: null,
            'content' => $content ?: null,
            'hero_image' => $heroImage ?: null,
            'thumbnail' => $thumbnail ?: null,
            'video_url' => $videoUrl ?: null,
            'meta_title' => $metaTitle ?: null,
            'meta_description' => $metaDescription ?: null,
            'status' => $status,
            'is_featured' => $isFeatured ? 1 : 0,
            'enable_llm_qa' => $enableLlmQa ? 1 : 0,
            'llm_qa_content' => $llmQaContent ?: null,
        ];
    }

    /**
     * Generate Q&A with AI
     */
    public function generateQA(string $id): void
    {
        $this->handleGenerateQA((int) $id, Post::class, 'post', ['title', 'subtitle', 'excerpt', 'content']);
    }

    /**
     * Show WordPress import form
     */
    public function importForm(): void
    {
        $this->requireAuth();

        $this->renderAdmin('posts/import', [
            'title' => 'Importar desde WordPress',
            'csrf_token' => $this->generateCsrf(),
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Analyze WordPress XML file
     */
    public function analyzeImport(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'Token inválido'], 403);
            return;
        }

        if (!isset($_FILES['xml_file']) || $_FILES['xml_file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'error' => 'No se ha subido ningún archivo']);
            return;
        }

        $file = $_FILES['xml_file'];

        // Validate file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'xml') {
            $this->json(['success' => false, 'error' => 'El archivo debe ser XML']);
            return;
        }

        // Move to temp location
        $tempPath = sys_get_temp_dir() . '/wp_import_' . uniqid() . '.xml';
        if (!move_uploaded_file($file['tmp_name'], $tempPath)) {
            $this->json(['success' => false, 'error' => 'Error al procesar el archivo']);
            return;
        }

        // Store path in session for later import
        $_SESSION['wp_import_file'] = $tempPath;

        // Analyze the file
        $importer = new \App\Services\WordPressImporter();
        $stats = $importer->analyze($tempPath);

        if (isset($stats['error'])) {
            $this->json(['success' => false, 'error' => $stats['error']]);
            return;
        }

        $this->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Execute WordPress import
     */
    public function executeImport(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'error' => 'Token inválido'], 403);
            return;
        }

        $tempPath = $_SESSION['wp_import_file'] ?? null;
        if (!$tempPath || !file_exists($tempPath)) {
            $this->json(['success' => false, 'error' => 'Archivo de importación no encontrado. Vuelve a subir el archivo.']);
            return;
        }

        // Execute import
        $importer = new \App\Services\WordPressImporter();
        $result = $importer->import($tempPath);

        // Clean up temp file
        @unlink($tempPath);
        unset($_SESSION['wp_import_file']);

        if (!$result['success']) {
            $this->json(['success' => false, 'error' => $result['error']]);
            return;
        }

        $this->json([
            'success' => true,
            'imported' => $result['imported'],
            'posts' => $result['posts'],
            'errors' => $result['errors']
        ]);
    }
}
