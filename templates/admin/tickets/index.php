<?php
/**
 * Tickets List Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Entradas</h1>
        <p>Gestiona las entradas del evento</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/tickets/scanner?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-qrcode"></i> Scanner</a>
        <a href="/admin/tickets/types?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-tags"></i> Tipos</a>
        <a href="/admin/tickets/export?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-download"></i> Exportar</a>
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
            <div><strong class="text-info"><?= $stats['checked_in'] ?? 0 ?></strong> <span class="text-muted">Check-in</span></div>
            <div><strong class="text-warning"><?= number_format($stats['total_revenue'] ?? 0, 2) ?>€</strong> <span class="text-muted">Ingresos</span></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tickets Table -->
<div class="card">
    <?php if (empty($tickets)): ?>
        <div class="empty-state">
            <i class="fas fa-ticket-alt"></i>
            <h3>No hay entradas</h3>
            <p>Las entradas aparecerán aquí cuando los participantes se registren</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Asistente</th>
                        <th>Empresa</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($ticket['ticket_code']) ?></code></td>
                            <td>
                                <strong><?= htmlspecialchars($ticket['attendee_first_name'] . ' ' . $ticket['attendee_last_name']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($ticket['attendee_email']) ?></small>
                            </td>
                            <td><?= $ticket['attendee_company_name'] ? htmlspecialchars($ticket['attendee_company_name']) : '-' ?></td>
                            <td>
                                <?= htmlspecialchars($ticket['ticket_type_name']) ?>
                                <?php if ($ticket['sponsor_name']): ?>
                                    <br><small class="text-muted">por <?= htmlspecialchars($ticket['sponsor_name']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($ticket['status']) {
                                    'confirmed' => 'success', 'checked_in' => 'info', 'pending' => 'warning', 'cancelled' => 'danger', default => 'secondary'
                                };
                                ?>
                                <span class="badge badge-<?= $statusClass ?>"><?= $statusOptions[$ticket['status']] ?? $ticket['status'] ?></span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/tickets/<?= $ticket['id'] ?>" class="btn btn-sm btn-outline" title="Ver"><i class="fas fa-eye"></i></a>
                                    <?php if ($ticket['status'] === 'confirmed'): ?>
                                        <button type="button" class="btn btn-sm btn-outline btn-success" onclick="checkIn(<?= $ticket['id'] ?>)" title="Check-in"><i class="fas fa-check"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function checkIn(id) {
    if (!confirm('¿Confirmar check-in?')) return;
    fetch('/admin/tickets/' + id + '/check-in', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => d.success ? location.reload() : alert(d.error));
}
</script>
