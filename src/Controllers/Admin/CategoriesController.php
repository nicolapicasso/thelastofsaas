<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Category;
use App\Helpers\Sanitizer;

/**
 * Categories Controller
 * We're Sinapsis CMS Admin
 */
class CategoriesController extends Controller
{
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    /**
     * List all categories
     */
    public function index(): void
    {
        $this->requireAuth();

        // Get categories in hierarchical order (flattened with depth)
        $categories = $this->categoryModel->getAllHierarchical();

        // Get content counts for each category
        foreach ($categories as &$category) {
            $counts = $this->categoryModel->getWithContentCounts($category['id']);
            if ($counts) {
                $category = array_merge($category, [
                    'posts_count' => $counts['posts_count'] ?? 0,
                    'services_count' => $counts['services_count'] ?? 0,
                    'cases_count' => $counts['cases_count'] ?? 0,
                    'tools_count' => $counts['tools_count'] ?? 0,
                    'faqs_count' => $counts['faqs_count'] ?? 0,
                    'total_content' => $counts['total_content'] ?? 0,
                ]);
            }
        }

        $this->renderAdmin('categories/index', [
            'title' => 'Categorías',
            'categories' => $categories,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        // Get parent categories for hierarchical selection
        $parentCategories = $this->categoryModel->getActive();

        $this->renderAdmin('categories/form', [
            'title' => 'Nueva Categoría',
            'category' => null,
            'parentCategories' => $parentCategories,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new category
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada. Por favor, inténtalo de nuevo.');
            $this->redirect('/admin/categories/create');
        }

        $data = $this->validateCategoryData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/categories/create');
        }

        try {
            $this->categoryModel->createWithSlug($data);
            $this->flash('success', 'Categoría creada correctamente.');
            $this->redirect('/admin/categories');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear la categoría: ' . $e->getMessage());
            $this->redirect('/admin/categories/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $category = $this->categoryModel->getWithContentCounts((int) $id);

        if (!$category) {
            $this->flash('error', 'Categoría no encontrada.');
            $this->redirect('/admin/categories');
        }

        // Get parent categories excluding this one and its children
        $parentCategories = array_filter(
            $this->categoryModel->getActive(),
            fn($cat) => $cat['id'] != $id
        );

        // Get all content for this category
        $content = $this->categoryModel->getAllContent((int) $id);

        $this->renderAdmin('categories/form', [
            'title' => 'Editar Categoría',
            'category' => $category,
            'parentCategories' => $parentCategories,
            'content' => $content,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update category
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada. Por favor, inténtalo de nuevo.');
            $this->redirect('/admin/categories/' . $id . '/edit');
        }

        $category = $this->categoryModel->find((int) $id);

        if (!$category) {
            $this->flash('error', 'Categoría no encontrada.');
            $this->redirect('/admin/categories');
        }

        $data = $this->validateCategoryData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/categories/' . $id . '/edit');
        }

        // Prevent self-referencing parent
        if (isset($data['parent_id']) && $data['parent_id'] == $id) {
            $this->flash('error', 'Una categoría no puede ser su propio padre.');
            $this->redirect('/admin/categories/' . $id . '/edit');
        }

        try {
            $this->categoryModel->updateWithSlug((int) $id, $data);
            $this->flash('success', 'Categoría actualizada correctamente.');
            $this->redirect('/admin/categories');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar la categoría: ' . $e->getMessage());
            $this->redirect('/admin/categories/' . $id . '/edit');
        }
    }

    /**
     * Delete category
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/categories');
        }

        $category = $this->categoryModel->getWithContentCounts((int) $id);

        if (!$category) {
            $this->flash('error', 'Categoría no encontrada.');
            $this->redirect('/admin/categories');
        }

        // Check if category has content
        if (($category['total_content'] ?? 0) > 0) {
            $this->flash('error', 'No se puede eliminar una categoría que tiene contenido asociado. Mueve o elimina el contenido primero.');
            $this->redirect('/admin/categories');
        }

        // Check for child categories
        $children = $this->categoryModel->getChildren((int) $id);
        if (!empty($children)) {
            $this->flash('error', 'No se puede eliminar una categoría que tiene subcategorías.');
            $this->redirect('/admin/categories');
        }

        try {
            $this->categoryModel->delete((int) $id);
            $this->flash('success', 'Categoría eliminada correctamente.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar la categoría: ' . $e->getMessage());
        }

        $this->redirect('/admin/categories');
    }

    /**
     * Reorder categories via AJAX
     */
    public function reorder(): void
    {
        $this->requireAuth();

        if (!$this->isAjax()) {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
            return;
        }

        $ids = $this->getPost('ids', []);

        if (empty($ids) || !is_array($ids)) {
            $this->jsonResponse(['error' => 'No IDs provided'], 400);
            return;
        }

        try {
            $this->categoryModel->reorder(array_map('intval', $ids));
            $this->jsonResponse(['success' => true]);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate Q&A content via AJAX
     */
    public function generateQA(string $id): void
    {
        $this->requireAuth();

        header('Content-Type: application/json');

        try {
            $category = $this->categoryModel->find((int) $id);

            if (!$category) {
                echo json_encode(['success' => false, 'error' => 'Categoría no encontrada']);
                return;
            }

            // Build content from category fields
            $content = ($category['name'] ?? '') . "\n\n";

            if (!empty($category['description'])) {
                $content .= strip_tags($category['description']);
            }

            $qaGenerator = new \App\Services\QAGeneratorService();

            if (!$qaGenerator->isConfigured()) {
                echo json_encode([
                    'success' => false,
                    'error' => 'OpenAI API no configurada. Configura OPENAI_API_KEY en Configuración > Integraciones.'
                ]);
                return;
            }

            $qaItems = $qaGenerator->generateQA(
                $category['name'] ?? '',
                $content,
                'category',
                4
            );

            echo json_encode([
                'success' => true,
                'qa_items' => $qaItems
            ]);

        } catch (\Exception $e) {
            error_log("Generate QA Error (Category): " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Error interno: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Validate category form data
     */
    private function validateCategoryData(): array
    {
        $errors = [];

        $name = Sanitizer::string($this->getPost('name'));
        $slug = Sanitizer::string($this->getPost('slug'));
        $description = $this->getPost('description'); // Allow HTML for WYSIWYG
        $featuredImage = Sanitizer::string($this->getPost('featured_image'));
        $iconImage = Sanitizer::string($this->getPost('icon_image'));
        $parentId = Sanitizer::int($this->getPost('parent_id')) ?: null;
        $color = Sanitizer::string($this->getPost('color'));
        $icon = Sanitizer::string($this->getPost('icon'));
        $sortOrder = Sanitizer::int($this->getPost('sort_order', 0));
        $isActive = $this->getPost('is_active') ? 1 : 0;
        $metaTitle = Sanitizer::string($this->getPost('meta_title'));
        $metaDescription = Sanitizer::string($this->getPost('meta_description'));
        $enableLlmQa = $this->getPost('enable_llm_qa') ? 1 : 0;
        $llmQaContent = $this->getPost('llm_qa_content'); // JSON string

        // Validate required fields
        if (empty($name)) {
            $errors[] = 'El nombre es obligatorio.';
        }

        // Validate color format
        if (!empty($color) && !preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $errors[] = 'El formato del color debe ser hexadecimal (#RRGGBB).';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'name' => $name,
            'slug' => $slug ?: null,
            'description' => $description ?: null,
            'featured_image' => $featuredImage ?: null,
            'icon_image' => $iconImage ?: null,
            'parent_id' => $parentId,
            'color' => $color ?: null,
            'icon' => $icon ?: null,
            'sort_order' => $sortOrder,
            'is_active' => $isActive,
            'meta_title' => $metaTitle ?: null,
            'meta_description' => $metaDescription ?: null,
            'enable_llm_qa' => $enableLlmQa,
            'llm_qa_content' => $llmQaContent ?: null,
        ];
    }
}
