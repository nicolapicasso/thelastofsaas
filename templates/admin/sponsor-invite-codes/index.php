<?php
/**
 * Sponsor Invite Codes List Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Codigos de Invitacion</h1>
        <p>Gestiona los codigos de invitacion de sponsors</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/sponsor-invite-codes/export<?= $currentEventId ? '?event_id=' . $currentEventId : '' ?><?= $currentSponsorId ? ($currentEventId ? '&' : '?') . 'sponsor_id=' . $currentSponsorId : '' ?>" class="btn btn-outline">
            <i class="fas fa-download"></i> Exportar
        </a>
        <a href="/admin/sponsor-invite-codes/bulk" class="btn btn-outline">
            <i class="fas fa-layer-group"></i> Crear en Lote
        </a>
        <a href="/admin/sponsor-invite-codes/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Codigo
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
    <form method="GET" action="/admin/sponsor-invite-codes" class="filters-form">
        <div class="filter-group">
            <label>Evento</label>
            <select name="event_id" onchange="this.form.submit()">
                <option value="">Todos los eventos</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= $event['id'] ?>" <?= ($currentEventId ?? '') == $event['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($event['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Sponsor</label>
            <select name="sponsor_id" onchange="this.form.submit()">
                <option value="">Todos los sponsors</option>
                <?php foreach ($sponsors as $sponsor): ?>
                    <option value="<?= $sponsor['id'] ?>" <?= ($currentSponsorId ?? '') == $sponsor['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sponsor['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Estado</label>
            <select name="active" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" <?= ($currentActive ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
                <option value="0" <?= ($currentActive ?? '') === '0' ? 'selected' : '' ?>>Inactivos</option>
            </select>
        </div>
        <?php if ($currentEventId || $currentSponsorId || ($currentActive !== null && $currentActive !== '')): ?>
            <a href="/admin/sponsor-invite-codes" class="btn btn-outline btn-sm">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Codes Table -->
<div class="card">
    <?php if (empty($codes)): ?>
        <div class="empty-state">
            <i class="fas fa-ticket-alt"></i>
            <h3>No hay codigos de invitacion</h3>
            <p>Crea codigos para que los sponsors puedan invitar asistentes</p>
            <a href="/admin/sponsor-invite-codes/create" class="btn btn-primary">Crear Codigo</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Sponsor</th>
                        <th>Evento</th>
                        <th>Uso</th>
                        <th>Descuento</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($codes as $code): ?>
                        <tr>
                            <td>
                                <strong style="font-family: monospace;"><?= htmlspecialchars($code['code']) ?></strong>
                                <?php if ($code['description'] ?? null): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($code['description']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($code['sponsor'] ?? null): ?>
                                    <a href="/admin/sponsors/<?= $code['sponsor']['id'] ?>/edit">
                                        <?= htmlspecialchars($code['sponsor']['name']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($code['event'] ?? null): ?>
                                    <a href="/admin/events/<?= $code['event']['id'] ?>/edit">
                                        <?= htmlspecialchars($code['event']['name']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $code['times_used'] > 0 ? 'info' : 'secondary' ?>">
                                    <?= $code['times_used'] ?> / <?= $code['max_uses'] ?? '&infin;' ?>
                                </span>
                                <?php if (($code['stats']['confirmed'] ?? 0) > 0): ?>
                                    <br><small class="text-success"><?= $code['stats']['confirmed'] ?> confirmados</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($code['discount_type'] === 'none'): ?>
                                    <span class="text-muted">-</span>
                                <?php elseif ($code['discount_type'] === 'percentage'): ?>
                                    <span class="badge badge-success"><?= $code['discount_amount'] ?>%</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?= number_format($code['discount_amount'], 2) ?> &euro;</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $code['active'] ? 'success' : 'secondary' ?>"
                                      style="cursor: pointer;"
                                      onclick="toggleActive(<?= $code['id'] ?>, this)"
                                      title="Click para cambiar estado">
                                    <?= $code['active'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline"
                                            onclick="copyCode('<?= htmlspecialchars($code['code']) ?>')"
                                            title="Copiar codigo">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <a href="/admin/sponsor-invite-codes/<?= $code['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (($code['stats']['total_tickets'] ?? 0) == 0): ?>
                                    <form method="POST" action="/admin/sponsor-invite-codes/<?= $code['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('Eliminar este codigo?')">
                                        <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">
                                        <button type="submit" class="btn btn-sm btn-outline btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
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
                    <?php
                    $queryParams = [];
                    if ($currentEventId) $queryParams['event_id'] = $currentEventId;
                    if ($currentSponsorId) $queryParams['sponsor_id'] = $currentSponsorId;
                    if ($currentActive !== null && $currentActive !== '') $queryParams['active'] = $currentActive;
                    $baseQuery = $queryParams ? '&' . http_build_query($queryParams) : '';
                    ?>
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?><?= $baseQuery ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    <span class="pagination-info">
                        Pagina <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?>
                    </span>
                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?><?= $baseQuery ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Codigo copiado: ' + code);
    }).catch(() => {
        prompt('Copia este codigo:', code);
    });
}

function toggleActive(id, element) {
    const formData = new FormData();
    formData.append('_csrf_token', '<?= $csrf_token ?>');

    fetch('/admin/sponsor-invite-codes/' + id + '/toggle-active', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            element.textContent = data.active ? 'Activo' : 'Inactivo';
            element.className = 'badge badge-' + (data.active ? 'success' : 'secondary');
        } else {
            alert(data.error || 'Error');
        }
    });
}
</script>
