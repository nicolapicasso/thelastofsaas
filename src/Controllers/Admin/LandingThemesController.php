<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\LandingTheme;
use App\Helpers\Sanitizer;

/**
 * Landing Themes Controller (Admin)
 * Omniwallet CMS
 */
class LandingThemesController extends Controller
{
    private LandingTheme $themeModel;

    public function __construct()
    {
        parent::__construct();
        $this->themeModel = new LandingTheme();
    }

    /**
     * List all themes
     */
    public function index(): void
    {
        $this->requireAuth();

        $themes = $this->themeModel->getAllForAdmin();

        $this->renderAdmin('landing-themes/index', [
            'title' => 'Temáticas de Landing',
            'themes' => $themes,
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $this->requireAuth();

        $this->renderAdmin('landing-themes/form', [
            'title' => 'Nueva Temática',
            'theme' => null,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Store new theme
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/landing-themes/create');
        }

        $data = $this->validateThemeData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/landing-themes/create');
        }

        try {
            $this->themeModel->createWithSlug($data);
            $this->flash('success', 'Temática creada correctamente.');
            $this->redirect('/admin/landing-themes');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear la temática: ' . $e->getMessage());
            $this->redirect('/admin/landing-themes/create');
        }
    }

    /**
     * Show edit form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $theme = $this->themeModel->find((int) $id);

        if (!$theme) {
            $this->flash('error', 'Temática no encontrada.');
            $this->redirect('/admin/landing-themes');
        }

        $this->renderAdmin('landing-themes/form', [
            'title' => 'Editar Temática',
            'theme' => $theme,
            'csrf_token' => $this->generateCsrf(),
        ]);
    }

    /**
     * Update theme
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/landing-themes/' . $id . '/edit');
        }

        $theme = $this->themeModel->find((int) $id);

        if (!$theme) {
            $this->flash('error', 'Temática no encontrada.');
            $this->redirect('/admin/landing-themes');
        }

        $data = $this->validateThemeData();

        if (isset($data['errors'])) {
            $this->flash('error', implode('<br>', $data['errors']));
            $this->redirect('/admin/landing-themes/' . $id . '/edit');
        }

        try {
            $this->themeModel->updateWithSlug((int) $id, $data);
            $this->flash('success', 'Temática actualizada correctamente.');
            $this->redirect('/admin/landing-themes');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar: ' . $e->getMessage());
            $this->redirect('/admin/landing-themes/' . $id . '/edit');
        }
    }

    /**
     * Delete theme
     */
    public function destroy(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada.');
            $this->redirect('/admin/landing-themes');
        }

        try {
            $this->themeModel->delete((int) $id);
            $this->flash('success', 'Temática eliminada (y todas sus landings).');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar: ' . $e->getMessage());
        }

        $this->redirect('/admin/landing-themes');
    }

    /**
     * Validate theme form data
     */
    private function validateThemeData(): array
    {
        $errors = [];

        $title = Sanitizer::string($this->getPost('title'));
        $slug = Sanitizer::string($this->getPost('slug'));
        $subtitle = Sanitizer::string($this->getPost('subtitle'));
        $description = $this->getPost('description');
        $image = Sanitizer::url($this->getPost('image'));
        $icon = Sanitizer::string($this->getPost('icon'));
        $sortOrder = Sanitizer::int($this->getPost('sort_order', 0));
        $isActive = Sanitizer::bool($this->getPost('is_active'));
        $metaTitle = Sanitizer::string($this->getPost('meta_title'));
        $metaDescription = $this->getPost('meta_description');

        if (empty($title)) {
            $errors[] = 'El título es obligatorio.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'title' => $title,
            'slug' => $slug ?: null,
            'subtitle' => $subtitle ?: null,
            'description' => $description ?: null,
            'image' => $image ?: null,
            'icon' => $icon ?: null,
            'sort_order' => $sortOrder,
            'is_active' => $isActive ? 1 : 0,
            'meta_title' => $metaTitle ?: null,
            'meta_description' => $metaDescription ?: null,
        ];
    }
}
