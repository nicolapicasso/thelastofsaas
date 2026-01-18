<?php
/**
 * Rooms List Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Salas</h1>
        <p>Gestiona las salas para actividades y reuniones</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/rooms/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Sala
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
    <form method="GET" action="/admin/rooms" class="filters-form">
        <div class="filter-group">
            <label>Estado</label>
            <select name="active" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" <?= ($currentActive ?? '') === '1' ? 'selected' : '' ?>>Activas</option>
                <option value="0" <?= ($currentActive ?? '') === '0' ? 'selected' : '' ?>>Inactivas</option>
            </select>
        </div>
        <?php if (($currentActive ?? null) !== null && $currentActive !== ''): ?>
            <a href="/admin/rooms" class="btn btn-outline btn-sm">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Rooms Table -->
<div class="card">
    <?php if (empty($rooms)): ?>
        <div class="empty-state">
            <i class="fas fa-door-open"></i>
            <h3>No hay salas</h3>
            <p>Crea tu primera sala para asignar actividades</p>
            <a href="/admin/rooms/create" class="btn btn-primary">Crear Sala</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="30"></th>
                        <th>Nombre</th>
                        <th>Ubicacion</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td>
                                <span class="color-dot" style="background-color: <?= htmlspecialchars($room['color'] ?? '#3B82F6') ?>"></span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($room['name']) ?></strong>
                                <?php if ($room['description'] ?? null): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars(substr($room['description'], 0, 50)) ?><?= strlen($room['description']) > 50 ? '...' : '' ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($room['location'] ?? null): ?>
                                    <?= htmlspecialchars($room['location']) ?>
                                    <?php if ($room['floor'] ?? null): ?>
                                        <br><small class="text-muted">Planta: <?= htmlspecialchars($room['floor']) ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($room['capacity']): ?>
                                    <i class="fas fa-users"></i> <?= $room['capacity'] ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $room['active'] ? 'success' : 'secondary' ?>">
                                    <?= $room['active'] ? 'Activa' : 'Inactiva' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/rooms/<?= $room['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="/admin/rooms/<?= $room['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('Eliminar esta sala?')">
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

<style>
.color-dot {
    display: inline-block;
    width: 16px;
    height: 16px;
    border-radius: 50%;
}
</style>
