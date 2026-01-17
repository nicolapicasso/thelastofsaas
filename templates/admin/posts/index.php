<?php
/**
 * Posts List Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Blog</h1>
        <p>Gestiona los artículos del blog</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/posts/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Post
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
    <form method="GET" action="/admin/posts" class="filters-form">
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
            <a href="/admin/posts" class="btn btn-outline btn-sm">Limpiar filtros</a>
        <?php endif; ?>
    </form>
</div>

<!-- Posts Table -->
<div class="card">
    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <i class="fas fa-newspaper"></i>
            <h3>No hay posts</h3>
            <p>Crea tu primer artículo del blog</p>
            <a href="/admin/posts/create" class="btn btn-primary">Crear Post</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50"></th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <?php if ($post['thumbnail']): ?>
                                    <img src="<?= htmlspecialchars($post['thumbnail']) ?>" alt="" class="table-thumbnail">
                                <?php else: ?>
                                    <div class="table-thumbnail-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="post-title-cell">
                                    <strong><?= htmlspecialchars($post['title']) ?></strong>
                                    <?php if ($post['is_featured']): ?>
                                        <span class="badge badge-warning"><i class="fas fa-star"></i></span>
                                    <?php endif; ?>
                                    <?php if ($post['slug']): ?>
                                        <small class="text-muted">/blog/<?= htmlspecialchars($post['slug']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if (isset($post['category_name'])): ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($post['category_name']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Sin categoría</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($post['status']) {
                                    'published' => 'success',
                                    'draft' => 'secondary',
                                    'archived' => 'warning',
                                    default => 'secondary'
                                };
                                $statusText = match($post['status']) {
                                    'published' => 'Publicado',
                                    'draft' => 'Borrador',
                                    'archived' => 'Archivado',
                                    default => $post['status']
                                };
                                ?>
                                <span class="badge badge-<?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($post['created_at'])) ?></small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/posts/<?= $post['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($post['status'] === 'published'): ?>
                                        <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" target="_blank" class="btn btn-sm btn-outline" title="Ver">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                    <form method="POST" action="/admin/posts/<?= $post['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('¿Eliminar este post?')">
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

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="card-footer">
                <div class="pagination">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?><?= $currentCategory ? '&category=' . $currentCategory : '' ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <span class="pagination-info">
                        Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?>
                    </span>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?><?= $currentCategory ? '&category=' . $currentCategory : '' ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
