<?php
/**
 * Services List Template
 * We're Sinapsis CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Servicios</h1>
        <p>Gestiona los servicios que ofrece la empresa</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/services/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Servicio
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Filters -->
<?php if (!empty($categories)): ?>
<div class="card filters-card">
    <form method="GET" action="/admin/services" class="filters-form">
        <div class="filter-group">
            <label>Categoria</label>
            <select name="category" onchange="this.form.submit()">
                <option value="">Todas las categorias</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($currentCategory ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (!empty($currentCategory)): ?>
            <a href="/admin/services" class="btn btn-outline btn-sm">Limpiar filtros</a>
        <?php endif; ?>
    </form>
</div>
<?php endif; ?>

<!-- Services List -->
<div class="card">
    <?php if (empty($services)): ?>
        <div class="empty-state">
            <i class="fas fa-cogs"></i>
            <h3>No hay servicios</h3>
            <p>Empieza a agregar los servicios de la empresa</p>
            <a href="/admin/services/create" class="btn btn-primary">Nuevo Servicio</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover" id="services-table">
                <thead>
                    <tr>
                        <th width="40"><i class="fas fa-grip-vertical"></i></th>
                        <th width="60">Imagen</th>
                        <th>Titulo</th>
                        <th>Categoria</th>
                        <th width="100">Estado</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody class="sortable-list" data-url="/admin/services/reorder">
                    <?php foreach ($services as $service): ?>
                        <tr data-id="<?= $service['id'] ?>" class="<?= empty($service['is_active']) ? 'row-inactive' : '' ?>">
                            <td class="drag-handle"><i class="fas fa-grip-vertical"></i></td>
                            <td>
                                <?php if (!empty($service['icon_class'])): ?>
                                    <div class="table-icon">
                                        <i class="<?= htmlspecialchars($service['icon_class']) ?>"></i>
                                    </div>
                                <?php elseif (!empty($service['image'])): ?>
                                    <img src="<?= htmlspecialchars($service['image']) ?>" alt="" class="table-thumb">
                                <?php else: ?>
                                    <div class="table-icon">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($service['title']) ?></strong>
                                <?php if (!empty($service['short_description'])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars(substr($service['short_description'], 0, 80)) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($service['category_name'])): ?>
                                    <span class="badge badge-outline"><?= htmlspecialchars($service['category_name']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($service['is_active'])): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactivo</span>
                                <?php endif; ?>
                                <?php if (!empty($service['is_featured'])): ?>
                                    <span class="badge badge-warning"><i class="fas fa-star"></i></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="/admin/services/<?= $service['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/servicios/<?= $service['slug'] ?>" target="_blank" class="btn btn-sm btn-outline" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" action="/admin/services/<?= $service['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('¿Eliminar este servicio?')">
                                        <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
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
.table-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    color: var(--color-primary);
    font-size: 18px;
}

.table-thumb {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.row-inactive {
    opacity: 0.6;
}

.drag-handle {
    cursor: grab;
    color: var(--color-gray-400);
}

.drag-handle:active {
    cursor: grabbing;
}

.filters-card {
    margin-bottom: var(--spacing-lg);
    padding: var(--spacing-md) var(--spacing-lg);
}

.filters-form {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.filter-group label {
    font-weight: 500;
    color: var(--color-gray-600);
    margin: 0;
}

.filter-group select {
    min-width: 180px;
}
</style>

<script>
// Sortable functionality
document.addEventListener('DOMContentLoaded', function() {
    const sortableList = document.querySelector('.sortable-list');
    if (sortableList && typeof Sortable !== 'undefined') {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function() {
                const ids = Array.from(sortableList.querySelectorAll('tr[data-id]'))
                    .map(row => row.dataset.id);

                fetch(sortableList.dataset.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ ids: ids })
                });
            }
        });
    }
});
</script>
