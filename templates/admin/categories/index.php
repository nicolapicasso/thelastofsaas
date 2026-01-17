<div class="page-header">
    <div class="page-header-left">
        <h2>Listado de Categorías</h2>
    </div>
    <div class="page-header-right">
        <a href="/admin/categories/create" class="btn btn-primary">+ Nueva Categoría</a>
    </div>
</div>

<?php if (!empty($categories)): ?>
<div class="table-wrapper">
    <table class="data-table categories-table">
        <thead>
            <tr>
                <th style="width: 60px;">Orden</th>
                <th>Nombre</th>
                <th>Slug</th>
                <th style="width: 120px;">Contenidos</th>
                <th style="width: 80px;">Estado</th>
                <th style="width: 150px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category):
                $depth = $category['depth'] ?? 0;
                $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth);
                $isChild = $depth > 0;
            ?>
            <tr class="<?= $isChild ? 'category-child depth-' . $depth : 'category-parent' ?>">
                <td class="text-center">
                    <?= $category['sort_order'] ?? 0 ?>
                </td>
                <td>
                    <div class="category-name-cell">
                        <?php if ($isChild): ?>
                            <span class="category-indent"><?= $indent ?></span>
                            <span class="category-tree-icon"><i class="fas fa-level-up-alt fa-rotate-90"></i></span>
                        <?php endif; ?>
                        <?php if (!empty($category['icon'])): ?>
                            <span class="category-icon" style="<?= !empty($category['color']) ? 'color:' . htmlspecialchars($category['color']) : '' ?>">
                                <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                            </span>
                        <?php endif; ?>
                        <strong><?= htmlspecialchars($category['name']) ?></strong>
                    </div>
                </td>
                <td>
                    <code><?= htmlspecialchars($category['slug']) ?></code>
                </td>
                <td class="text-center">
                    <?php
                    $total = ($category['total_content'] ?? 0);
                    if ($total > 0):
                    ?>
                        <span class="content-count" title="<?= ($category['posts_count'] ?? 0) ?> posts, <?= ($category['services_count'] ?? 0) ?> servicios, <?= ($category['cases_count'] ?? 0) ?> casos, <?= ($category['tools_count'] ?? 0) ?> herramientas">
                            <?= $total ?>
                        </span>
                    <?php else: ?>
                        <span class="text-muted">0</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($category['is_active']): ?>
                    <span class="status-badge status-published">Activa</span>
                    <?php else: ?>
                    <span class="status-badge status-draft">Inactiva</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="/admin/categories/<?= $category['id'] ?>/edit" class="btn btn-sm">Editar</a>
                        <form method="POST" action="/admin/categories/<?= $category['id'] ?>/delete" class="inline-form" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?');">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token) ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php else: ?>
<div class="empty-state">
    <p>No hay categorías todavía.</p>
    <a href="/admin/categories/create" class="btn btn-primary">Crear primera categoría</a>
</div>
<?php endif; ?>

<style>
.categories-table .category-name-cell {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.categories-table .category-indent {
    display: inline;
}

.categories-table .category-tree-icon {
    color: var(--color-gray-400);
    font-size: 0.75rem;
    margin-right: var(--spacing-xs);
}

.categories-table .category-icon {
    font-size: 1rem;
    min-width: 20px;
    text-align: center;
}

.categories-table .category-child {
    background-color: var(--color-gray-50);
}

.categories-table .category-child.depth-2 {
    background-color: var(--color-gray-100);
}

.categories-table .content-count {
    display: inline-block;
    background: var(--color-primary);
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: var(--font-size-sm);
    font-weight: 500;
    cursor: help;
}
</style>
