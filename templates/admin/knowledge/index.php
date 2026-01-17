<?php
/**
 * Knowledge Articles List Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Base de Conocimiento</h1>
        <p>Gestiona los artículos de ayuda y documentación</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/knowledge/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Artículo
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Filters -->
<div class="card filters-card">
    <form method="GET" action="/admin/knowledge" class="filters-form">
        <div class="filter-group">
            <label>Estado</label>
            <select name="status" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="draft" <?= $currentStatus === 'draft' ? 'selected' : '' ?>>Borrador</option>
                <option value="published" <?= $currentStatus === 'published' ? 'selected' : '' ?>>Publicado</option>
                <option value="archived" <?= $currentStatus === 'archived' ? 'selected' : '' ?>>Archivado</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Categoría</label>
            <select name="category" onchange="this.form.submit()">
                <option value="">Todas las categorías</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= $currentCategory == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($currentStatus || $currentCategory): ?>
            <a href="/admin/knowledge" class="btn btn-outline btn-sm">Limpiar filtros</a>
        <?php endif; ?>
    </form>
</div>

<!-- Articles Table -->
<div class="card">
    <?php if (empty($articles)): ?>
        <div class="empty-state">
            <i class="fas fa-book"></i>
            <h3>No hay artículos</h3>
            <p>Crea tu primer artículo de ayuda</p>
            <a href="/admin/knowledge/create" class="btn btn-primary">Crear Artículo</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50">Orden</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Vistas</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td>
                                <span class="sort-badge"><?= $article['sort_order'] ?></span>
                            </td>
                            <td>
                                <div class="article-title-cell">
                                    <strong><?= htmlspecialchars($article['title']) ?></strong>
                                    <?php if ($article['is_featured']): ?>
                                        <span class="badge badge-warning"><i class="fas fa-star"></i></span>
                                    <?php endif; ?>
                                    <?php if ($article['slug']): ?>
                                        <small class="text-muted">/ayuda/<?= htmlspecialchars($article['slug']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if (isset($article['category_name'])): ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($article['category_name']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Sin categoría</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($article['status']) {
                                    'published' => 'success',
                                    'draft' => 'secondary',
                                    'archived' => 'warning',
                                    default => 'secondary'
                                };
                                $statusText = match($article['status']) {
                                    'published' => 'Publicado',
                                    'draft' => 'Borrador',
                                    'archived' => 'Archivado',
                                    default => $article['status']
                                };
                                ?>
                                <span class="badge badge-<?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                            <td>
                                <span class="view-count">
                                    <i class="fas fa-eye"></i> <?= number_format($article['view_count'] ?? 0) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/knowledge/<?= $article['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($article['status'] === 'published'): ?>
                                        <a href="/ayuda/<?= htmlspecialchars($article['slug']) ?>" target="_blank" class="btn btn-sm btn-outline" title="Ver">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                    <form method="POST" action="/admin/knowledge/<?= $article['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('¿Eliminar este artículo?')">
                                        <input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
                                        <button type="submit" class="btn btn-sm btn-outline btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.article-title-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.article-title-cell strong {
    color: var(--color-dark);
}
.article-title-cell small {
    display: block;
}
.view-count {
    display: flex;
    align-items: center;
    gap: 4px;
    color: var(--color-gray-500);
    font-size: 13px;
}
.view-count i {
    font-size: 11px;
}
.sort-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background-color: var(--color-gray-100);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    color: var(--color-gray-600);
}
</style>
