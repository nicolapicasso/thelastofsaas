<?php
/**
 * Sponsors List Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Sponsors</h1>
        <p>Gestiona los sponsors (empresas SaaS)</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/sponsors/export" class="btn btn-outline">
            <i class="fas fa-download"></i> Exportar
        </a>
        <a href="/admin/sponsors/import" class="btn btn-outline">
            <i class="fas fa-upload"></i> Importar
        </a>
        <a href="/admin/sponsors/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Sponsor
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
    <form method="GET" action="/admin/sponsors" class="filters-form">
        <div class="filter-group" style="flex: 1;">
            <label>Buscar</label>
            <input type="text" name="search" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="Nombre del sponsor..." class="form-control">
        </div>
        <div class="filter-group">
            <label>Estado</label>
            <select name="active" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" <?= ($currentActive ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
                <option value="0" <?= ($currentActive ?? '') === '0' ? 'selected' : '' ?>>Inactivos</option>
            </select>
        </div>
        <div class="filter-group" style="align-self: flex-end;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Buscar</button>
        </div>
        <?php if (($currentActive ?? null) !== null && $currentActive !== '' || !empty($currentSearch)): ?>
            <a href="/admin/sponsors" class="btn btn-outline btn-sm" style="align-self: flex-end;">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Sponsors Table -->
<div class="card">
    <?php if (empty($sponsors)): ?>
        <div class="empty-state">
            <i class="fas fa-building"></i>
            <h3>No hay sponsors</h3>
            <p>Crea tu primer sponsor o importa desde CSV</p>
            <a href="/admin/sponsors/create" class="btn btn-primary">Crear Sponsor</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50"></th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Web</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sponsors as $sponsor): ?>
                        <tr>
                            <td>
                                <?php if ($sponsor['logo_url'] ?? null): ?>
                                    <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="" class="table-thumbnail">
                                <?php else: ?>
                                    <div class="table-thumbnail-placeholder">
                                        <i class="fas fa-building"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($sponsor['name']) ?></strong>
                                <?php if ($sponsor['tagline'] ?? null): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($sponsor['tagline']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($sponsor['contact_email'] ?? null): ?>
                                    <small><?= htmlspecialchars(explode(',', $sponsor['contact_email'])[0]) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($sponsor['website'] ?? null): ?>
                                    <a href="<?= htmlspecialchars($sponsor['website']) ?>" target="_blank" class="text-muted">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $sponsor['active'] ? 'success' : 'secondary' ?>">
                                    <?= $sponsor['active'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/sponsors/<?= $sponsor['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="/admin/sponsors/<?= $sponsor['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('Eliminar este sponsor?')">
                                        <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">
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

        <?php if (($pagination['total_pages'] ?? 0) > 1): ?>
            <div class="card-footer">
                <div class="pagination">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    <span class="pagination-info">
                        Pagina <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?>
                    </span>
                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
