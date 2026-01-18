<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Menu;
use App\Helpers\Sanitizer;

/**
 * Menus Controller
 * Omniwallet CMS Admin
 */
class MenusController extends Controller
{
    private Menu $menu;

    public function __construct()
    {
        parent::__construct();
        $this->menu = new Menu();
    }

    /**
     * List all menus
     */
    public function index(): void
    {
        $this->requireAuth();

        $menus = $this->menu->getAll();

        // Add item count to each menu
        foreach ($menus as &$menu) {
            $menu['item_count'] = count($this->menu->getAllItems((int)$menu['id']));
        }

        $this->renderAdmin('menus/index', [
            'title' => 'Gestión de Menús',
            'menus' => $menus,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Show create menu form
     */
    public function create(): void
    {
        $this->requireAuth();

        $this->renderAdmin('menus/form', [
            'title' => 'Nuevo Menú',
            'menu' => null,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Store new menu
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/menus/create');
        }

        $data = [
            'name' => Sanitizer::string($this->getPost('name')),
            'slug' => Sanitizer::string($this->getPost('slug')),
            'location' => Sanitizer::string($this->getPost('location', 'header')),
            'description' => Sanitizer::string($this->getPost('description')),
            'is_active' => $this->getPost('is_active') ? 1 : 0,
        ];

        if (empty($data['name'])) {
            $this->flash('error', 'El nombre del menú es obligatorio');
            $this->redirect('/admin/menus/create');
        }

        try {
            $menuId = $this->menu->createMenu($data);
            $this->flash('success', 'Menú creado correctamente');
            $this->redirect('/admin/menus/' . $menuId . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al crear el menú: ' . $e->getMessage());
            $this->redirect('/admin/menus/create');
        }
    }

    /**
     * Show edit menu form
     */
    public function edit(string $id): void
    {
        $this->requireAuth();

        $menu = $this->menu->getWithItems((int)$id);

        if (!$menu) {
            $this->flash('error', 'Menú no encontrado');
            $this->redirect('/admin/menus');
        }

        // Get flat list for parent selection
        $allItems = $this->menu->getAllItems((int)$id);

        $this->renderAdmin('menus/form', [
            'title' => 'Editar Menú: ' . $menu['name'],
            'menu' => $menu,
            'allItems' => $allItems,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Update menu
     */
    public function update(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/menus/' . $id . '/edit');
        }

        $menu = $this->menu->find((int)$id);

        if (!$menu) {
            $this->flash('error', 'Menú no encontrado');
            $this->redirect('/admin/menus');
        }

        $data = [
            'name' => Sanitizer::string($this->getPost('name')),
            'slug' => Sanitizer::string($this->getPost('slug')),
            'location' => Sanitizer::string($this->getPost('location', 'header')),
            'description' => Sanitizer::string($this->getPost('description')),
            'is_active' => $this->getPost('is_active') ? 1 : 0,
        ];

        if (empty($data['name'])) {
            $this->flash('error', 'El nombre del menú es obligatorio');
            $this->redirect('/admin/menus/' . $id . '/edit');
        }

        try {
            $this->menu->update((int)$id, $data);
            $this->flash('success', 'Menú actualizado correctamente');
            $this->redirect('/admin/menus/' . $id . '/edit');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al actualizar el menú: ' . $e->getMessage());
            $this->redirect('/admin/menus/' . $id . '/edit');
        }
    }

    /**
     * Delete menu
     */
    public function delete(string $id): void
    {
        $this->requireAuth();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesión expirada');
            $this->redirect('/admin/menus');
        }

        try {
            $this->menu->delete((int)$id);
            $this->flash('success', 'Menú eliminado');
        } catch (\Exception $e) {
            $this->flash('error', 'Error al eliminar el menú');
        }

        $this->redirect('/admin/menus');
    }

    // ===== Menu Items AJAX Endpoints =====

    /**
     * Add menu item (AJAX)
     */
    public function addItem(string $menuId): void
    {
        $this->requireAuth();

        // Parse translations if provided
        $translations = $this->getPost('translations');
        if ($translations && is_string($translations)) {
            $translations = json_decode($translations, true);
        }

        $data = [
            'menu_id' => (int)$menuId,
            'parent_id' => $this->getPost('parent_id') ?: null,
            'title' => Sanitizer::string($this->getPost('title')),
            'url' => Sanitizer::string($this->getPost('url')),
            'target' => Sanitizer::string($this->getPost('target', '_self')),
            'icon' => Sanitizer::string($this->getPost('icon')),
            'css_class' => Sanitizer::string($this->getPost('css_class')),
            'item_type' => Sanitizer::string($this->getPost('item_type', 'link')),
            'button_style' => Sanitizer::string($this->getPost('button_style', 'primary')),
            'translations' => $translations,
            'display_order' => (int)$this->getPost('display_order', 0),
            'is_active' => $this->getPost('is_active') ? 1 : 1,
        ];

        if (empty($data['title']) || empty($data['url'])) {
            $this->json(['success' => false, 'error' => 'Título y URL son obligatorios'], 400);
        }

        try {
            $itemId = $this->menu->createItem($data);
            $item = $this->menu->getItem($itemId);

            $this->json([
                'success' => true,
                'item' => $item,
                'message' => 'Elemento añadido'
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update menu item (AJAX)
     */
    public function updateItem(string $menuId, string $itemId): void
    {
        $this->requireAuth();

        // Parse translations if provided
        $translations = $this->getPost('translations');
        if ($translations && is_string($translations)) {
            $translations = json_decode($translations, true);
        }

        $data = [
            'title' => Sanitizer::string($this->getPost('title')),
            'url' => Sanitizer::string($this->getPost('url')),
            'target' => Sanitizer::string($this->getPost('target', '_self')),
            'icon' => Sanitizer::string($this->getPost('icon')),
            'css_class' => Sanitizer::string($this->getPost('css_class')),
            'item_type' => Sanitizer::string($this->getPost('item_type', 'link')),
            'button_style' => Sanitizer::string($this->getPost('button_style', 'primary')),
            'translations' => $translations,
            'display_order' => (int)$this->getPost('display_order', 0),
            'is_active' => $this->getPost('is_active') ? 1 : 0,
            'parent_id' => $this->getPost('parent_id') ?: null,
        ];

        if (empty($data['title']) || empty($data['url'])) {
            $this->json(['success' => false, 'error' => 'Título y URL son obligatorios'], 400);
        }

        try {
            $this->menu->updateItem((int)$itemId, $data);
            $item = $this->menu->getItem((int)$itemId);

            $this->json([
                'success' => true,
                'item' => $item,
                'message' => 'Elemento actualizado'
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete menu item (AJAX)
     */
    public function deleteItem(string $menuId, string $itemId): void
    {
        $this->requireAuth();

        try {
            $this->menu->deleteItem((int)$itemId);
            $this->json(['success' => true, 'message' => 'Elemento eliminado']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Reorder menu items (AJAX)
     */
    public function reorderItems(string $menuId): void
    {
        $this->requireAuth();

        // Read JSON body if Content-Type is application/json
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $jsonBody = json_decode(file_get_contents('php://input'), true);
            $items = $jsonBody['items'] ?? null;
        } else {
            $items = $this->getPost('items');
            if (!is_array($items)) {
                $items = json_decode($items, true);
            }
        }

        if (!$items) {
            $this->json(['success' => false, 'error' => 'No items provided'], 400);
            return;
        }

        try {
            $this->menu->reorderItems($items);
            $this->json(['success' => true, 'message' => 'Orden actualizado']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get menu items (AJAX)
     */
    public function getItems(string $menuId): void
    {
        $this->requireAuth();

        try {
            $items = $this->menu->getMenuItems((int)$menuId);
            $this->json(['success' => true, 'items' => $items]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
