<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Menu Model
 * Omniwallet CMS
 */
class Menu extends Model
{
    protected string $table = 'menus';

    protected array $fillable = [
        'name',
        'slug',
        'location',
        'description',
    ];

    /**
     * Menu locations
     */
    public const LOCATIONS = [
        'header' => 'Cabecera (Header)',
        'header_buttons' => 'Botones Cabecera',
        'footer' => 'Pie de página (Footer)',
        'footer_social' => 'Redes Sociales Footer',
        'sidebar' => 'Barra lateral',
        'other' => 'Otro'
    ];

    /**
     * Item types
     */
    public const ITEM_TYPES = [
        'link' => 'Enlace',
        'button' => 'Botón',
        'social' => 'Red Social'
    ];

    /**
     * Button styles
     */
    public const BUTTON_STYLES = [
        'primary' => 'Primario (oscuro)',
        'outline' => 'Outline (claro)'
    ];

    /**
     * Get all menus
     */
    public function getAll(): array
    {
        return $this->all(['name' => 'ASC']);
    }

    /**
     * Get menu by location
     */
    public function getByLocation(string $location): ?array
    {
        return $this->first(['location' => $location]);
    }

    /**
     * Get menu by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->first(['slug' => $slug]);
    }

    /**
     * Get menu with items
     */
    public function getWithItems(int $menuId): ?array
    {
        $menu = $this->find($menuId);

        if (!$menu) {
            return null;
        }

        $menu['items'] = $this->getMenuItems($menuId);

        return $menu;
    }

    /**
     * Get menu with items by location
     */
    public function getWithItemsByLocation(string $location): ?array
    {
        $menu = $this->getByLocation($location);

        if (!$menu) {
            return null;
        }

        $menu['items'] = $this->getMenuItems((int)$menu['id']);

        return $menu;
    }

    /**
     * Get menu items for a menu (hierarchical)
     */
    public function getMenuItems(int $menuId, ?int $parentId = null): array
    {
        $sql = "SELECT * FROM menu_items
                WHERE menu_id = ? AND parent_id " . ($parentId === null ? "IS NULL" : "= ?") . "
                AND is_active = 1
                ORDER BY display_order ASC";

        $params = $parentId === null ? [$menuId] : [$menuId, $parentId];
        $items = $this->db->fetchAll($sql, $params);

        // Get children for each item
        foreach ($items as &$item) {
            $item['children'] = $this->getMenuItems($menuId, (int)$item['id']);
        }

        return $items;
    }

    /**
     * Get all items for a menu (flat list)
     */
    public function getAllItems(int $menuId): array
    {
        $sql = "SELECT * FROM menu_items WHERE menu_id = ? ORDER BY parent_id ASC, display_order ASC";
        return $this->db->fetchAll($sql, [$menuId]);
    }

    /**
     * Get a single menu item
     */
    public function getItem(int $itemId): ?array
    {
        $sql = "SELECT * FROM menu_items WHERE id = ?";
        return $this->db->fetch($sql, [$itemId]);
    }

    /**
     * Create menu item
     */
    public function createItem(array $data): int
    {
        $sql = "INSERT INTO menu_items (menu_id, parent_id, title, url, target, icon, css_class, item_type, button_style, translations, display_order, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $translations = $data['translations'] ?? null;
        if (is_array($translations)) {
            $translations = json_encode($translations);
        }

        $this->db->query($sql, [
            $data['menu_id'],
            $data['parent_id'] ?: null,
            $data['title'],
            $data['url'],
            $data['target'] ?? '_self',
            $data['icon'] ?? null,
            $data['css_class'] ?? null,
            $data['item_type'] ?? 'link',
            $data['button_style'] ?? 'primary',
            $translations,
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update menu item
     */
    public function updateItem(int $itemId, array $data): bool
    {
        $sql = "UPDATE menu_items SET
                title = ?,
                url = ?,
                target = ?,
                icon = ?,
                css_class = ?,
                item_type = ?,
                button_style = ?,
                translations = ?,
                display_order = ?,
                is_active = ?,
                parent_id = ?
                WHERE id = ?";

        $translations = $data['translations'] ?? null;
        if (is_array($translations)) {
            $translations = json_encode($translations);
        }

        return $this->db->query($sql, [
            $data['title'],
            $data['url'],
            $data['target'] ?? '_self',
            $data['icon'] ?? null,
            $data['css_class'] ?? null,
            $data['item_type'] ?? 'link',
            $data['button_style'] ?? 'primary',
            $translations,
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1,
            $data['parent_id'] ?: null,
            $itemId,
        ]);
    }

    /**
     * Get menu items with translations applied for a specific language
     */
    public function getMenuItemsTranslated(int $menuId, string $language = 'es', ?int $parentId = null): array
    {
        $items = $this->getMenuItems($menuId, $parentId);

        foreach ($items as &$item) {
            $item = $this->applyTranslation($item, $language);
            if (!empty($item['children'])) {
                foreach ($item['children'] as &$child) {
                    $child = $this->applyTranslation($child, $language);
                }
            }
        }

        return $items;
    }

    /**
     * Apply translation to a menu item
     */
    private function applyTranslation(array $item, string $language): array
    {
        if ($language === 'es' || empty($item['translations'])) {
            return $item;
        }

        $translations = is_string($item['translations'])
            ? json_decode($item['translations'], true)
            : $item['translations'];

        if (isset($translations[$language])) {
            $item['title'] = $translations[$language];
        }

        return $item;
    }

    /**
     * Get menu with translated items by location
     */
    public function getWithItemsByLocationTranslated(string $location, string $language = 'es'): ?array
    {
        $menu = $this->getByLocation($location);

        if (!$menu) {
            return null;
        }

        $menu['items'] = $this->getMenuItemsTranslated((int)$menu['id'], $language);

        return $menu;
    }

    /**
     * Delete menu item
     */
    public function deleteItem(int $itemId): bool
    {
        // First delete children
        $sql = "DELETE FROM menu_items WHERE parent_id = ?";
        $this->db->query($sql, [$itemId]);

        // Then delete the item itself
        $sql = "DELETE FROM menu_items WHERE id = ?";
        return $this->db->query($sql, [$itemId]);
    }

    /**
     * Reorder menu items
     * Accepts either:
     * - Array of objects: [{id: 1, display_order: 0}, {id: 2, display_order: 1}]
     * - Simple array: [itemId1, itemId2] where index is the order
     */
    public function reorderItems(array $items): bool
    {
        foreach ($items as $order => $item) {
            // Handle object format from JavaScript
            if (is_array($item) && isset($item['id'])) {
                $itemId = (int) $item['id'];
                $displayOrder = isset($item['display_order']) ? (int) $item['display_order'] : $order;
            } else {
                // Handle simple format (index as order, value as id)
                $itemId = (int) $item;
                $displayOrder = $order;
            }

            $sql = "UPDATE menu_items SET display_order = ? WHERE id = ?";
            $this->db->query($sql, [$displayOrder, $itemId]);
        }
        return true;
    }

    /**
     * Create menu with slug
     */
    public function createMenu(array $data): int
    {
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        return $this->create($data);
    }

    /**
     * Generate unique slug
     */
    private function generateSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        $baseSlug = $slug;
        $counter = 1;

        while ($this->getBySlug($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
