<?php
/**
 * Companies List Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Empresas</h1>
        <p>Gestiona las empresas participantes</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/companies/export" class="btn btn-outline"><i class="fas fa-download"></i> Exportar</a>
        <a href="/admin/companies/import" class="btn btn-outline"><i class="fas fa-upload"></i> Importar</a>
        <a href="/admin/companies/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Empresa</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<div class="card filters-card">
    <form method="GET" action="/admin/companies" class="filters-form">
        <div class="filter-group">
            <label>Estado</label>
            <select name="active" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" <?= $currentActive === '1' ? 'selected' : '' ?>>Activos</option>
                <option value="0" <?= $currentActive === '0' ? 'selected' : '' ?>>Inactivos</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Industria</label>
            <select name="industry" onchange="this.form.submit()">
                <option value="">Todas</option>
                <?php foreach ($industries as $ind): ?>
                    <option value="<?= htmlspecialchars($ind) ?>" <?= $currentIndustry === $ind ? 'selected' : '' ?>><?= htmlspecialchars($ind) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($currentActive !== null || $currentIndustry): ?>
            <a href="/admin/companies" class="btn btn-outline btn-sm">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <?php if (empty($companies)): ?>
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>No hay empresas</h3>
            <p>Crea tu primera empresa o importa desde CSV</p>
            <a href="/admin/companies/create" class="btn btn-primary">Crear Empresa</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50"></th>
                        <th>Nombre</th>
                        <th>Industria</th>
                        <th>Tamaño</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($companies as $company): ?>
                        <tr>
                            <td>
                                <?php if ($company['logo_url']): ?>
                                    <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="" class="table-thumbnail">
                                <?php else: ?>
                                    <div class="table-thumbnail-placeholder"><i class="fas fa-building"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($company['name']) ?></strong>
                                <?php if ($company['contact_emails']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars(explode(',', $company['contact_emails'])[0]) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= $company['industry'] ? htmlspecialchars($company['industry']) : '<span class="text-muted">-</span>' ?></td>
                            <td><?= $company['company_size'] ? '<span class="badge badge-info">' . $company['company_size'] . '</span>' : '-' ?></td>
                            <td><span class="badge badge-<?= $company['active'] ? 'success' : 'secondary' ?>"><?= $company['active'] ? 'Activo' : 'Inactivo' ?></span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/companies/<?= $company['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="/admin/companies/<?= $company['id'] ?>/delete" class="inline-form" onsubmit="return confirm('¿Eliminar?')">
                                        <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
                                        <button type="submit" class="btn btn-sm btn-outline btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="card-footer">
                <div class="pagination">
                    <?php if ($pagination['current_page'] > 1): ?><a href="?page=<?= $pagination['current_page'] - 1 ?>" class="btn btn-sm btn-outline"><i class="fas fa-chevron-left"></i></a><?php endif; ?>
                    <span class="pagination-info">Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?></span>
                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?><a href="?page=<?= $pagination['current_page'] + 1 ?>" class="btn btn-sm btn-outline"><i class="fas fa-chevron-right"></i></a><?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
