<?php
/**
 * Menu Form Template
 * Omniwallet CMS
 */
$isEdit = isset($menu) && $menu;
$action = $isEdit ? "/admin/menus/{$menu['id']}/update" : "/admin/menus";
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/menus" class="back-link"><i class="fas fa-arrow-left"></i> Volver</a>
        <h1><?= $title ?></h1>
    </div>
</div>

<div class="menu-editor-layout">
    <!-- Menu Settings -->
    <div class="menu-settings">
        <form method="POST" action="<?= $action ?>" id="menuForm">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

            <div class="editor-card">
                <div class="card-header">
                    <h3>Configuración del Menú</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nombre del menú *</label>
                        <input type="text" id="name" name="name"
                               value="<?= htmlspecialchars($menu['name'] ?? '') ?>"
                               required placeholder="Ej: Menú Principal">
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug"
                               value="<?= htmlspecialchars($menu['slug'] ?? '') ?>"
                               placeholder="Se genera automáticamente">
                        <small class="form-hint">Identificador único para uso en plantillas</small>
                    </div>

                    <div class="form-group">
                        <label for="location">Ubicación</label>
                        <select id="location" name="location">
                            <?php foreach (\App\Models\Menu::LOCATIONS as $loc => $label): ?>
                            <option value="<?= $loc ?>" <?= ($menu['location'] ?? 'header') === $loc ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea id="description" name="description" rows="2"
                                  placeholder="Descripción opcional del menú"><?= htmlspecialchars($menu['description'] ?? '') ?></textarea>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar cambios' : 'Crear menú' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php if ($isEdit): ?>
    <!-- Menu Items -->
    <div class="menu-items-editor">
        <div class="editor-card">
            <div class="card-header">
                <h3>Elementos del Menú</h3>
                <button type="button" class="btn btn-sm btn-primary" onclick="openAddItemModal()">
                    <i class="fas fa-plus"></i> Añadir elemento
                </button>
            </div>
            <div class="card-body">
                <div id="menuItemsList" class="menu-items-list">
                    <?php if (empty($menu['items'])): ?>
                    <div class="empty-items">
                        <i class="fas fa-list"></i>
                        <p>No hay elementos en este menú</p>
                        <button type="button" class="btn btn-outline" onclick="openAddItemModal()">
                            <i class="fas fa-plus"></i> Añadir primer elemento
                        </button>
                    </div>
                    <?php else: ?>
                    <?php renderMenuItems($menu['items']); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if ($isEdit): ?>
<!-- Add/Edit Item Modal -->
<div class="modal" id="itemModal">
    <div class="modal-overlay" onclick="closeItemModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="itemModalTitle">Añadir Elemento</h3>
            <button type="button" class="modal-close" onclick="closeItemModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="itemForm">
                <input type="hidden" id="itemId" value="">
                <input type="hidden" id="parentId" value="">

                <div class="form-group">
                    <label for="itemTitle">Título (Español) *</label>
                    <input type="text" id="itemTitle" required placeholder="Texto del enlace">
                </div>

                <div class="form-group">
                    <label for="itemUrl">URL *</label>
                    <input type="text" id="itemUrl" required placeholder="/pagina o https://...">
                    <small class="form-hint">URL interna (ej: /contacto) o externa (ej: https://...)</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="itemType">Tipo de elemento</label>
                        <select id="itemType" onchange="toggleButtonStyle()">
                            <?php foreach (\App\Models\Menu::ITEM_TYPES as $type => $label): ?>
                            <option value="<?= $type ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" id="buttonStyleGroup" style="display: none;">
                        <label for="itemButtonStyle">Estilo del botón</label>
                        <select id="itemButtonStyle">
                            <?php foreach (\App\Models\Menu::BUTTON_STYLES as $style => $label): ?>
                            <option value="<?= $style ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="itemTarget">Abrir en</label>
                        <select id="itemTarget">
                            <option value="_self">Misma ventana</option>
                            <option value="_blank">Nueva ventana</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="itemParent">Elemento padre</label>
                        <select id="itemParent">
                            <option value="">-- Ninguno (nivel principal) --</option>
                            <?php if (!empty($allItems)): ?>
                            <?php foreach ($allItems as $item): ?>
                            <?php if (empty($item['parent_id'])): ?>
                            <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['title']) ?></option>
                            <?php // Show children (level 2) as possible parents for level 3 ?>
                            <?php foreach ($allItems as $child): ?>
                            <?php if ($child['parent_id'] == $item['id']): ?>
                            <option value="<?= $child['id'] ?>">&nbsp;&nbsp;&nbsp;↳ <?= htmlspecialchars($child['title']) ?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-hint">Soporta hasta 3 niveles de anidación</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="itemIcon">Icono (FontAwesome)</label>
                    <input type="text" id="itemIcon" placeholder="fas fa-home">
                    <small class="form-hint">Clase de FontAwesome (requerido para redes sociales)</small>
                </div>

                <!-- Translations Section -->
                <div class="translations-section">
                    <h4><i class="fas fa-language"></i> Traducciones</h4>
                    <div class="translations-grid">
                        <?php
                        $languages = \App\Models\Translation::LANGUAGES;
                        unset($languages['es']); // Spanish is the main title
                        foreach ($languages as $code => $name):
                        ?>
                        <div class="form-group">
                            <label for="itemTrans_<?= $code ?>"><?= $name ?></label>
                            <input type="text" id="itemTrans_<?= $code ?>" placeholder="Traducción en <?= $name ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="itemActive" checked>
                        <span>Elemento activo</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeItemModal()">Cancelar</button>
            <button type="button" class="btn btn-primary" onclick="saveItem()">
                <i class="fas fa-save"></i> Guardar
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
function renderMenuItems(array $items, int $level = 0): void {
    foreach ($items as $item):
        $itemType = $item['item_type'] ?? 'link';
?>
    <div class="menu-item" data-id="<?= $item['id'] ?>" data-level="<?= $level ?>">
        <div class="menu-item-content" style="padding-left: <?= 16 + ($level * 24) ?>px;">
            <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
            <?php if (!empty($item['icon'])): ?>
            <span class="item-icon"><i class="<?= htmlspecialchars($item['icon']) ?>"></i></span>
            <?php endif; ?>
            <span class="item-title"><?= htmlspecialchars($item['title']) ?></span>
            <span class="item-url"><?= htmlspecialchars($item['url']) ?></span>
            <?php if ($itemType !== 'link'): ?>
            <span class="item-type-badge <?= $itemType ?>"><?= $itemType === 'button' ? 'Botón' : 'Social' ?></span>
            <?php endif; ?>
            <?php if ($item['target'] === '_blank'): ?>
            <span class="item-badge"><i class="fas fa-external-link-alt"></i></span>
            <?php endif; ?>
            <?php if (!$item['is_active']): ?>
            <span class="item-badge inactive">Inactivo</span>
            <?php endif; ?>
            <div class="item-actions">
                <button type="button" class="btn-icon" onclick="editItem(<?= $item['id'] ?>)" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn-icon btn-icon-danger" onclick="deleteItem(<?= $item['id'] ?>)" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    <?php if (!empty($item['children'])): ?>
    <?php renderMenuItems($item['children'], $level + 1); ?>
    <?php endif; ?>
<?php
    endforeach;
}
?>

<style>
.menu-editor-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: var(--spacing-lg);
    align-items: start;
}

@media (max-width: 1024px) {
    .menu-editor-layout {
        grid-template-columns: 1fr;
    }
}

.menu-items-list {
    min-height: 200px;
}

.menu-item {
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-sm);
    background: white;
    transition: box-shadow 0.2s, transform 0.15s, opacity 0.15s;
    cursor: default;
}

.menu-item:hover {
    box-shadow: var(--shadow-sm);
}

/* Drag & Drop Styles */
.menu-item.dragging {
    opacity: 0.5;
    transform: scale(1.02);
    box-shadow: var(--shadow-lg);
    z-index: 100;
}

.menu-item.drag-over {
    border-color: var(--color-primary);
    border-style: dashed;
    background: rgba(80, 70, 229, 0.05);
}

.menu-item.drag-over-top {
    border-top: 3px solid var(--color-primary);
}

.menu-item.drag-over-bottom {
    border-bottom: 3px solid var(--color-primary);
}

.menu-item-content {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    gap: var(--spacing-sm);
}

.drag-handle {
    cursor: grab;
    color: var(--color-gray-400);
    padding: 4px;
}

.drag-handle:active {
    cursor: grabbing;
}

.item-icon {
    color: var(--color-primary);
    width: 20px;
    text-align: center;
}

.item-title {
    font-weight: 500;
    color: var(--color-gray-800);
}

.item-url {
    color: var(--color-gray-500);
    font-size: 13px;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.item-badge {
    font-size: 11px;
    color: var(--color-gray-500);
    padding: 2px 6px;
    background: var(--color-gray-100);
    border-radius: 4px;
}

.item-badge.inactive {
    background: #fef3c7;
    color: #92400e;
}

.item-actions {
    display: flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s;
}

.menu-item:hover .item-actions {
    opacity: 1;
}

.btn-icon {
    width: 28px;
    height: 28px;
    border: none;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-gray-600);
    transition: all 0.2s;
}

.btn-icon:hover {
    background: var(--color-gray-200);
    color: var(--color-gray-800);
}

.btn-icon-danger:hover {
    background: #fee2e2;
    color: var(--color-danger);
}

.empty-items {
    text-align: center;
    padding: var(--spacing-xl);
    color: var(--color-gray-500);
}

.empty-items i {
    font-size: 32px;
    margin-bottom: var(--spacing-md);
    color: var(--color-gray-300);
}

.empty-items p {
    margin-bottom: var(--spacing-md);
}

/* Modal styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    position: relative;
    background: white;
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--spacing-md) var(--spacing-lg);
    border-bottom: 1px solid var(--color-gray-200);
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
}

.modal-close {
    width: 32px;
    height: 32px;
    border: none;
    background: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--color-gray-500);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-body {
    padding: var(--spacing-lg);
    overflow-y: auto;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-sm);
    padding: var(--spacing-md) var(--spacing-lg);
    border-top: 1px solid var(--color-gray-200);
    background: var(--color-gray-50);
}

/* Translations section */
.translations-section {
    margin-top: var(--spacing-md);
    padding-top: var(--spacing-md);
    border-top: 1px solid var(--color-gray-200);
}

.translations-section h4 {
    margin: 0 0 var(--spacing-sm) 0;
    font-size: 14px;
    color: var(--color-gray-600);
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.translations-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-sm);
}

@media (max-width: 600px) {
    .translations-grid {
        grid-template-columns: 1fr;
    }
}

/* Item type badges */
.item-type-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 4px;
    text-transform: uppercase;
    font-weight: 600;
}

.item-type-badge.button {
    background: var(--color-primary-light);
    color: var(--color-primary);
}

.item-type-badge.social {
    background: #e0f2fe;
    color: #0369a1;
}
</style>

<?php if ($isEdit): ?>
<script>
const menuId = <?= $menu['id'] ?>;
let currentItemId = null;

function openAddItemModal(parentId = null) {
    currentItemId = null;
    document.getElementById('itemModalTitle').textContent = 'Añadir Elemento';
    document.getElementById('itemForm').reset();
    document.getElementById('itemId').value = '';
    document.getElementById('itemParent').value = parentId || '';
    document.getElementById('itemType').value = 'link';
    document.getElementById('itemButtonStyle').value = 'primary';
    document.getElementById('itemActive').checked = true;
    clearTranslations();
    toggleButtonStyle();
    document.getElementById('itemModal').classList.add('active');
}

function toggleButtonStyle() {
    const type = document.getElementById('itemType').value;
    const buttonStyleGroup = document.getElementById('buttonStyleGroup');
    buttonStyleGroup.style.display = type === 'button' ? 'block' : 'none';
}

function clearTranslations() {
    const langCodes = ['en', 'it', 'fr', 'de'];
    langCodes.forEach(code => {
        const input = document.getElementById('itemTrans_' + code);
        if (input) input.value = '';
    });
}

function setTranslations(translations) {
    if (!translations) return;
    const trans = typeof translations === 'string' ? JSON.parse(translations) : translations;
    Object.keys(trans).forEach(code => {
        const input = document.getElementById('itemTrans_' + code);
        if (input) input.value = trans[code] || '';
    });
}

function getTranslations() {
    const translations = {};
    const langCodes = ['en', 'it', 'fr', 'de'];
    langCodes.forEach(code => {
        const input = document.getElementById('itemTrans_' + code);
        if (input && input.value.trim()) {
            translations[code] = input.value.trim();
        }
    });
    return Object.keys(translations).length > 0 ? translations : null;
}

function closeItemModal() {
    document.getElementById('itemModal').classList.remove('active');
    currentItemId = null;
}

function editItem(itemId) {
    currentItemId = itemId;
    document.getElementById('itemModalTitle').textContent = 'Editar Elemento';

    // Find item in the list
    const itemEl = document.querySelector(`.menu-item[data-id="${itemId}"]`);
    if (!itemEl) return;

    // Fetch item details via AJAX
    fetch(`/admin/menus/${menuId}/items`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const item = findItemById(data.items, itemId);
                if (item) {
                    document.getElementById('itemId').value = item.id;
                    document.getElementById('itemTitle').value = item.title;
                    document.getElementById('itemUrl').value = item.url;
                    document.getElementById('itemTarget').value = item.target || '_self';
                    document.getElementById('itemIcon').value = item.icon || '';
                    document.getElementById('itemParent').value = item.parent_id || '';
                    document.getElementById('itemType').value = item.item_type || 'link';
                    document.getElementById('itemButtonStyle').value = item.button_style || 'primary';
                    document.getElementById('itemActive').checked = item.is_active == 1;
                    setTranslations(item.translations);
                    toggleButtonStyle();
                    document.getElementById('itemModal').classList.add('active');
                }
            }
        });
}

function findItemById(items, id) {
    for (const item of items) {
        if (item.id == id) return item;
        if (item.children) {
            const found = findItemById(item.children, id);
            if (found) return found;
        }
    }
    return null;
}

function saveItem() {
    const formData = new FormData();
    formData.append('title', document.getElementById('itemTitle').value);
    formData.append('url', document.getElementById('itemUrl').value);
    formData.append('target', document.getElementById('itemTarget').value);
    formData.append('icon', document.getElementById('itemIcon').value);
    formData.append('parent_id', document.getElementById('itemParent').value);
    formData.append('item_type', document.getElementById('itemType').value);
    formData.append('button_style', document.getElementById('itemButtonStyle').value);
    formData.append('is_active', document.getElementById('itemActive').checked ? '1' : '0');

    // Add translations
    const translations = getTranslations();
    if (translations) {
        formData.append('translations', JSON.stringify(translations));
    }

    const url = currentItemId
        ? `/admin/menus/${menuId}/items/${currentItemId}`
        : `/admin/menus/${menuId}/items`;

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        const text = await response.text();
        console.log('Response status:', response.status);
        console.log('Response text:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error. Raw response:', text);
            throw new Error('La respuesta del servidor no es JSON válido');
        }
    })
    .then(data => {
        if (data.success) {
            closeItemModal();
            location.reload();
        } else {
            alert(data.error || 'Error al guardar');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        // Si ya se guardó (el item aparece), simplemente recargar
        if (confirm('Hubo un error pero el elemento puede haberse guardado. ¿Recargar la página?')) {
            location.reload();
        }
    });
}

function deleteItem(itemId) {
    if (!confirm('¿Eliminar este elemento? Los subelementos también serán eliminados.')) {
        return;
    }

    fetch(`/admin/menus/${menuId}/items/${itemId}/delete`, {
        method: 'POST'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al eliminar');
        }
    });
}

// Auto-generate slug
document.getElementById('name')?.addEventListener('blur', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value && this.value) {
        slugInput.value = this.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    }
});

// ====================================
// Drag & Drop functionality for menu items
// ====================================
(function() {
    const itemsList = document.getElementById('menuItemsList');
    if (!itemsList) return;

    let draggedItem = null;
    let draggedId = null;

    function initDragAndDrop() {
        const items = itemsList.querySelectorAll('.menu-item');

        items.forEach(item => {
            const handle = item.querySelector('.drag-handle');
            if (!handle) return;

            // Make item draggable when grabbing handle
            handle.addEventListener('mousedown', function(e) {
                item.setAttribute('draggable', 'true');
            });

            handle.addEventListener('mouseup', function(e) {
                item.setAttribute('draggable', 'false');
            });

            // Drag events
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragend', handleDragEnd);
            item.addEventListener('dragover', handleDragOver);
            item.addEventListener('dragenter', handleDragEnter);
            item.addEventListener('dragleave', handleDragLeave);
            item.addEventListener('drop', handleDrop);
        });
    }

    function handleDragStart(e) {
        draggedItem = this;
        draggedId = this.dataset.id;
        this.classList.add('dragging');

        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', draggedId);

        // Hide drag image slightly
        setTimeout(() => {
            this.style.opacity = '0.4';
        }, 0);
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
        this.style.opacity = '';
        this.setAttribute('draggable', 'false');

        // Clear all drag-over classes
        document.querySelectorAll('.menu-item').forEach(item => {
            item.classList.remove('drag-over', 'drag-over-top', 'drag-over-bottom');
        });

        draggedItem = null;
        draggedId = null;
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';

        if (this === draggedItem) return;

        // Determine if dropping above or below
        const rect = this.getBoundingClientRect();
        const midY = rect.top + rect.height / 2;

        this.classList.remove('drag-over-top', 'drag-over-bottom');

        if (e.clientY < midY) {
            this.classList.add('drag-over-top');
        } else {
            this.classList.add('drag-over-bottom');
        }
    }

    function handleDragEnter(e) {
        e.preventDefault();
        if (this !== draggedItem) {
            this.classList.add('drag-over');
        }
    }

    function handleDragLeave(e) {
        // Only remove class if leaving the item (not entering a child)
        if (!this.contains(e.relatedTarget)) {
            this.classList.remove('drag-over', 'drag-over-top', 'drag-over-bottom');
        }
    }

    function handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();

        if (this === draggedItem) return;

        const targetId = this.dataset.id;
        const rect = this.getBoundingClientRect();
        const dropAbove = e.clientY < rect.top + rect.height / 2;

        // Clear classes
        this.classList.remove('drag-over', 'drag-over-top', 'drag-over-bottom');

        // Perform the reorder
        reorderItems(draggedId, targetId, dropAbove);
    }

    function reorderItems(draggedId, targetId, dropAbove) {
        // Visual reorder first
        const draggedEl = document.querySelector(`.menu-item[data-id="${draggedId}"]`);
        const targetEl = document.querySelector(`.menu-item[data-id="${targetId}"]`);

        if (draggedEl && targetEl) {
            if (dropAbove) {
                targetEl.parentNode.insertBefore(draggedEl, targetEl);
            } else {
                targetEl.parentNode.insertBefore(draggedEl, targetEl.nextSibling);
            }
        }

        // Build new order from DOM
        const newOrder = [];
        document.querySelectorAll('.menu-item').forEach((item, index) => {
            newOrder.push({
                id: parseInt(item.dataset.id),
                sort_order: index
            });
        });

        // Send to server
        fetch(`/admin/menus/${menuId}/items/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ items: newOrder })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                // Revert on error
                alert('Error al reordenar: ' + (data.error || 'Error desconocido'));
                location.reload();
            }
        })
        .catch(err => {
            console.error('Reorder error:', err);
            location.reload();
        });
    }

    // Initialize drag & drop
    initDragAndDrop();

    // Re-initialize after any DOM changes
    const observer = new MutationObserver(function(mutations) {
        initDragAndDrop();
    });

    observer.observe(itemsList, { childList: true, subtree: true });
})();
</script>
<?php endif; ?>
