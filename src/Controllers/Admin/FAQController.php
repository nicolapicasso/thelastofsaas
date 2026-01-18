<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\FAQ;
use App\Models\Category;
use App\Helpers\Sanitizer;

/**
 * FAQ Controller
 * Omniwallet CMS
 */
class FAQController extends Controller
{
    private FAQ $faqModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->faqModel = new FAQ();
        $this->categoryModel = new Category();
    }

    /**
     * List all FAQs
     */
    public function index(): void
    {
        $this->requireAuth();

        $categoryId = $this->getQuery('category');

        // Get FAQs with category info
        $faqs = $this->faqModel->getActive(100);

        // Filter by category if specified
        if ($categoryId) {
            $faqs = array_filter($faqs, fn($faq) => $faq['category_id'] == $categoryId);
        }

        // Get categories (shared across all entities)
        $categories = $this->categoryModel->where(['is_active' => 1], ['name' => 'ASC']);

        $this->renderAdmin('faqs/index', [
            'title' => 'Preguntas Frecuentes',
            'faqs' => $faqs,
            'categories' => $categories,
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

        $categories = $this->categoryModel->where(['is_active' => 1], ['name' => 'ASC']);
        $groups = $this->faqModel->getUniqueGroups();

        $this->renderAdmin('faqs/form', [
            'title' => 'Nueva FAQ',
            'faq' => null,
            'categories' => $categories,
            'groups' => $groups,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new FAQ
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/faqs/create');
        }

        $data = $this->validateFAQData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/faqs/create');
        }

        try {
            $this->faqModel->create($data);
            $this->flash('success', 'FAQ creada correctamente.');
            $this->redirect('/admin/faqs');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear la FAQ: ' . $e->getMessage());
            $this->redirect('/admin/faqs/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $faq = $this->faqModel->find((int) $id);

        if (!$faq) {
            $this->flash('error', 'FAQ no encontrada.');
            $this->redirect('/admin/faqs');
        }

        $categories = $this->categoryModel->where(['is_active' => 1], ['name' => 'ASC']);
        $groups = $this->faqModel->getUniqueGroups();

        $this->renderAdmin('faqs/form', [
            'title' => 'Editar FAQ',
            'faq' => $faq,
            'categories' => $categories,
            'groups' => $groups,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update FAQ
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/faqs/' . $id . '/edit');
        }

        $faq = $this->faqModel->find((int) $id);

        if (!$faq) {
            $this->flash('error', 'FAQ no encontrada.');
            $this->redirect('/admin/faqs');
        }

        $data = $this->validateFAQData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/faqs/' . $id . '/edit');
        }

        try {
            $this->faqModel->update((int) $id, $data);
            $this->flash('success', 'FAQ actualizada correctamente.');
            $this->redirect('/admin/faqs');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar: ' . $e->getMessage());
            $this->redirect('/admin/faqs/' . $id . '/edit');
        }
    }

    /**
     * Delete FAQ
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/faqs');
        }

        try {
            $this->faqModel->delete((int) $id);
            $this->flash('success', 'FAQ eliminada.');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar: ' . $e->getMessage());
        }

        $this->redirect('/admin/faqs');
    }

    /**
     * Validate FAQ form data
     */
    private function validateFAQData(): array
    {
        $errors = [];

        $question = Sanitizer::string($this->getPost('question'));
        $answer = $this->getPost('answer'); // Allow HTML
        $categoryId = Sanitizer::int($this->getPost('category_id'));
        $faqGroup = Sanitizer::string($this->getPost('faq_group'));
        $displayOrder = Sanitizer::int($this->getPost('display_order', 0));
        $isActive = Sanitizer::bool($this->getPost('is_active', true));

        if (empty($question)) {
            $errors[] = 'La pregunta es obligatoria.';
        }

        if (empty($answer)) {
            $errors[] = 'La respuesta es obligatoria.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'question' => $question,
            'answer' => $answer,
            'category_id' => $categoryId ?: null,
            'faq_group' => $faqGroup ?: null,
            'display_order' => $displayOrder,
            'is_active' => $isActive ? 1 : 0,
        ];
    }
}
