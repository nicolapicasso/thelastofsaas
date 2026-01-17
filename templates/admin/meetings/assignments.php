<?php
/**
 * Meeting Assignments Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Reuniones Asignadas</h1>
        <p>Gestiona las reuniones programadas</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/meetings/blocks?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-clock"></i> Bloques</a>
        <a href="/admin/meetings/unassigned?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-user-plus"></i> Sin Asignar</a>
        <a href="/admin/meetings/export?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-download"></i> Exportar</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<!-- Event Selector + Stats -->
<div class="card">
    <div class="card-body" style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
        <div class="filter-group">
            <label>Evento</label>
            <select onchange="location.href='?event_id='+this.value" class="form-control">
                <?php foreach ($events as $evt): ?>
                    <option value="<?= $evt['id'] ?>" <?= $currentEventId == $evt['id'] ? 'selected' : '' ?>><?= htmlspecialchars($evt['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (!empty($stats)): ?>
        <div style="display: flex; gap: 1.5rem;">
            <div><strong><?= $stats['total'] ?? 0 ?></strong> <span class="text-muted">Total</span></div>
            <div><strong class="text-success"><?= $stats['confirmed'] ?? 0 ?></strong> <span class="text-muted">Confirmadas</span></div>
            <div><strong class="text-info"><?= $stats['completed'] ?? 0 ?></strong> <span class="text-muted">Completadas</span></div>
            <div><strong class="text-danger"><?= $stats['cancelled'] ?? 0 ?></strong> <span class="text-muted">Canceladas</span></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Assignments Table -->
<div class="card">
    <?php if (empty($assignments)): ?>
        <div class="empty-state">
            <i class="fas fa-handshake"></i>
            <h3>No hay reuniones</h3>
            <p>Asigna reuniones desde la sección "Sin Asignar"</p>
            <a href="/admin/meetings/unassigned?event_id=<?= $currentEventId ?>" class="btn btn-primary">Ver matches sin asignar</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Mesa</th>
                        <th>Sponsor</th>
                        <th>Empresa</th>
                        <th>Estado</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $a): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($a['event_date'])) ?></td>
                            <td><strong><?= substr($a['slot_time'], 0, 5) ?></strong> <small class="text-muted">(<?= $a['meeting_duration'] ?>min)</small></td>
                            <td><?= $a['room_name'] ?? 'Mesa ' . $a['room_number'] ?></td>
                            <td><?= htmlspecialchars($a['sponsor_name']) ?></td>
                            <td><?= htmlspecialchars($a['company_name']) ?></td>
                            <td>
                                <?php
                                $statusClass = match($a['status']) {
                                    'confirmed' => 'success', 'completed' => 'info', 'cancelled' => 'danger', 'no_show' => 'warning', default => 'secondary'
                                };
                                ?>
                                <span class="badge badge-<?= $statusClass ?>"><?= $statusOptions[$a['status']] ?? $a['status'] ?></span>
                            </td>
                            <td>
                                <?php if ($a['status'] === 'confirmed'): ?>
                                    <button type="button" class="btn btn-sm btn-outline btn-danger" onclick="cancelMeeting(<?= $a['id'] ?>)" title="Cancelar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function cancelMeeting(id) {
    if (!confirm('¿Cancelar esta reunión?')) return;
    fetch('/admin/meetings/assignments/' + id + '/cancel', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => d.success ? location.reload() : alert(d.error));
}
</script>
