<div class="page-header">
    <div class="page-header-left">
        <h2>Listado de Páginas</h2>
        <div class="filter-tabs">
            <a href="/admin/pages" class="filter-tab <?= !$currentStatus ? 'active' : '' ?>">Todas</a>
            <a href="/admin/pages?status=published" class="filter-tab <?= $currentStatus === 'published' ? 'active' : '' ?>">Publicadas</a>
            <a href="/admin/pages?status=draft" class="filter-tab <?= $currentStatus === 'draft' ? 'active' : '' ?>">Borradores</a>
        </div>
    </div>
    <div class="page-header-right">
        <a href="/admin/pages/create" class="btn btn-primary">+ Nueva Página</a>
    </div>
</div>

<?php if (!empty($pages)): ?>
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Slug</th>
                <th>Estado</th>
                <th>Plantilla</th>
                <th>Actualizada</th>
                <th style="width: 180px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($page['title']) ?></strong>
                    <?php if ($page['slug'] === 'home'): ?>
                    <span class="badge badge-info">Inicio</span>
                    <?php endif; ?>
                </td>
                <td>
                    <code>/<?= htmlspecialchars($page['slug']) ?></code>
                </td>
                <td>
                    <span class="status-badge status-<?= $page['status'] ?>">
                        <?= $page['status'] === 'published' ? 'Publicada' : ($page['status'] === 'draft' ? 'Borrador' : 'Archivada') ?>
                    </span>
                </td>
                <td>
                    <?= htmlspecialchars($page['template'] ?? 'default') ?>
                </td>
                <td>
                    <?= date('d/m/Y H:i', strtotime($page['updated_at'])) ?>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="/admin/pages/<?= $page['id'] ?>/edit" class="btn btn-sm">Editar</a>
                        <?php if ($page['status'] === 'published'): ?>
                        <a href="/<?= $page['slug'] === 'home' ? '' : $page['slug'] ?>" target="_blank" class="btn btn-sm btn-outline">Ver</a>
                        <?php endif; ?>
                        <?php if ($page['slug'] !== 'home'): ?>
                        <form method="POST" action="/admin/pages/<?= $page['id'] ?>/delete" class="inline-form" onsubmit="return confirm('¿Eliminar esta página?');">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_csrf_token) ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
<div class="pagination">
    <?php if ($pagination['has_prev']): ?>
    <a href="?page=<?= $pagination['current_page'] - 1 ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?>" class="pagination-btn">&laquo; Anterior</a>
    <?php endif; ?>
    <span class="pagination-info">Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?></span>
    <?php if ($pagination['has_next']): ?>
    <a href="?page=<?= $pagination['current_page'] + 1 ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?>" class="pagination-btn">Siguiente &raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php else: ?>
<div class="empty-state">
    <p>No hay páginas<?= $currentStatus ? ' con este estado' : '' ?>.</p>
    <a href="/admin/pages/create" class="btn btn-primary">Crear primera página</a>
</div>
<?php endif; ?>

<style>
.filter-tabs {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}
.filter-tab {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    color: var(--color-gray-600);
    background: var(--color-gray-100);
}
.filter-tab:hover {
    background: var(--color-gray-200);
    color: var(--color-gray-700);
}
.filter-tab.active {
    background: var(--color-primary);
    color: white;
}
.badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    margin-left: 8px;
}
.badge-info {
    background: var(--color-primary-light);
    color: white;
}
</style>
