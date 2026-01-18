<?php
/**
 * Events List Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Eventos</h1>
        <p>Gestiona los eventos TLOS</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/events/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Evento
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
    <form method="GET" action="/admin/events" class="filters-form">
        <div class="filter-group">
            <label>Estado</label>
            <select name="status" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <?php foreach ($statusOptions as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $currentStatus === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($currentStatus): ?>
            <a href="/admin/events" class="btn btn-outline btn-sm">Limpiar filtros</a>
        <?php endif; ?>
    </form>
</div>

<!-- Events Table -->
<div class="card">
    <?php if (empty($events)): ?>
        <div class="empty-state">
            <i class="fas fa-calendar-alt"></i>
            <h3>No hay eventos</h3>
            <p>Crea tu primer evento</p>
            <a href="/admin/events/create" class="btn btn-primary">Crear Evento</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Aforo</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($event['name']) ?></strong>
                                <?php if ($event['slug']): ?>
                                    <br><small class="text-muted">/eventos/<?= htmlspecialchars($event['slug']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($event['start_date'] ?? null): ?>
                                    <i class="fas fa-calendar"></i>
                                    <?= date('d/m/Y', strtotime($event['start_date'])) ?>
                                    <?php if (($event['end_date'] ?? null) && $event['end_date'] !== $event['start_date']): ?>
                                        <br><small class="text-muted">al <?= date('d/m/Y', strtotime($event['end_date'])) ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin fecha</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($event['location'] ?? null): ?>
                                    <?= htmlspecialchars($event['location']) ?>
                                    <?php if ($event['city'] ?? null): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($event['city']) ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin definir</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-info"><?= (int)($event['max_attendees'] ?? 0) ?></span>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($event['status']) {
                                    'published' => 'primary',
                                    'active' => 'success',
                                    'draft' => 'secondary',
                                    'finished' => 'dark',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge badge-<?= $statusClass ?>">
                                    <?= $statusOptions[$event['status']] ?? $event['status'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <?php if ($event['slug'] && $event['status'] === 'published'): ?>
                                        <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>" class="btn btn-sm btn-outline" title="Ver en web" target="_blank">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="/admin/events/<?= $event['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="/admin/events/<?= $event['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('¿Eliminar este evento? Se eliminarán todos los datos asociados.')">
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

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="card-footer">
                <div class="pagination">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <span class="pagination-info">
                        Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?>
                    </span>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?><?= $currentStatus ? '&status=' . $currentStatus : '' ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
