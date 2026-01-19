<?php
/**
 * Sponsor Invite Code Form Template
 * TLOS - The Last of SaaS
 */
$isEdit = isset($code) && $code;
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= $isEdit ? 'Editar Codigo' : 'Nuevo Codigo de Invitacion' ?></h1>
        <p><?= $isEdit ? 'Codigo: ' . htmlspecialchars($code['code']) : 'Crear un nuevo codigo de invitacion para sponsors' ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/sponsor-invite-codes" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? '/admin/sponsor-invite-codes/' . $code['id'] : '/admin/sponsor-invite-codes' ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="form-grid">
        <div class="form-main">
            <div class="card">
                <div class="card-header">
                    <h3>Informacion del Codigo</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="event_id">Evento *</label>
                            <select id="event_id" name="event_id" class="form-control" required onchange="updateTicketTypes()">
                                <option value="">Seleccionar evento</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= $event['id'] ?>"
                                            <?= ($code['event_id'] ?? '') == $event['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($event['name']) ?>
                                        (<?= date('d/m/Y', strtotime($event['start_date'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sponsor_id">Sponsor *</label>
                            <select id="sponsor_id" name="sponsor_id" class="form-control" required>
                                <option value="">Seleccionar sponsor</option>
                                <?php foreach ($sponsors as $sponsor): ?>
                                    <option value="<?= $sponsor['id'] ?>"
                                            <?= ($code['sponsor_id'] ?? '') == $sponsor['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sponsor['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="code">Codigo</label>
                            <input type="text" id="code" name="code" class="form-control"
                                   value="<?= htmlspecialchars($code['code'] ?? '') ?>"
                                   style="font-family: monospace; text-transform: uppercase;"
                                   placeholder="Se genera automaticamente si se deja vacio">
                            <small class="form-help">Dejar vacio para generar automaticamente</small>
                        </div>
                        <div class="form-group">
                            <label for="description">Descripcion</label>
                            <input type="text" id="description" name="description" class="form-control"
                                   value="<?= htmlspecialchars($code['description'] ?? '') ?>"
                                   placeholder="Descripcion interna del codigo">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="max_uses">Maximo de Usos</label>
                            <input type="number" id="max_uses" name="max_uses" class="form-control"
                                   value="<?= htmlspecialchars($code['max_uses'] ?? '') ?>"
                                   min="1" placeholder="Ilimitado">
                            <small class="form-help">Dejar vacio para uso ilimitado</small>
                        </div>
                        <div class="form-group">
                            <label for="ticket_type_id">Tipo de Entrada</label>
                            <select id="ticket_type_id" name="ticket_type_id" class="form-control">
                                <option value="">Cualquier tipo de entrada</option>
                                <!-- Options loaded via JavaScript -->
                            </select>
                            <small class="form-help">Restringir a un tipo especifico de entrada</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Descuento</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="discount_type">Tipo de Descuento</label>
                            <select id="discount_type" name="discount_type" class="form-control" onchange="toggleDiscountAmount()">
                                <?php foreach ($discountTypes as $key => $label): ?>
                                    <option value="<?= $key ?>"
                                            <?= ($code['discount_type'] ?? 'none') === $key ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" id="discount_amount_group" style="<?= ($code['discount_type'] ?? 'none') === 'none' ? 'display:none;' : '' ?>">
                            <label for="discount_amount">Cantidad</label>
                            <input type="number" id="discount_amount" name="discount_amount" class="form-control"
                                   value="<?= htmlspecialchars($code['discount_amount'] ?? '0') ?>"
                                   min="0" step="0.01">
                            <small class="form-help" id="discount_help">Porcentaje o cantidad fija en euros</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Periodo de Validez</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="valid_from">Valido Desde</label>
                            <input type="datetime-local" id="valid_from" name="valid_from" class="form-control"
                                   value="<?= $code['valid_from'] ? date('Y-m-d\TH:i', strtotime($code['valid_from'])) : '' ?>">
                            <small class="form-help">Dejar vacio para disponibilidad inmediata</small>
                        </div>
                        <div class="form-group">
                            <label for="valid_until">Valido Hasta</label>
                            <input type="datetime-local" id="valid_until" name="valid_until" class="form-control"
                                   value="<?= $code['valid_until'] ? date('Y-m-d\TH:i', strtotime($code['valid_until'])) : '' ?>">
                            <small class="form-help">Dejar vacio para sin fecha limite</small>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($isEdit && !empty($tickets)): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Entradas Registradas con este Codigo</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Codigo Entrada</th>
                                    <th>Asistente</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td>
                                        <a href="/admin/tickets/<?= $ticket['id'] ?>/edit" style="font-family: monospace;">
                                            <?= htmlspecialchars($ticket['code']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($ticket['attendee_name']) ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($ticket['attendee_email']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($ticket['ticket_type_name'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge badge-<?= $ticket['status'] === 'confirmed' ? 'success' : ($ticket['status'] === 'used' ? 'info' : 'secondary') ?>">
                                            <?= ucfirst($ticket['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="form-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Estado</h3>
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <input type="checkbox" id="active" name="active" value="1"
                               <?= ($code['active'] ?? 1) ? 'checked' : '' ?>>
                        <label for="active">Codigo Activo</label>
                    </div>
                </div>
            </div>

            <?php if ($isEdit): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Estadisticas</h3>
                </div>
                <div class="card-body">
                    <div class="stat-row">
                        <span class="stat-label">Veces usado:</span>
                        <span class="stat-value"><?= $code['times_used'] ?></span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Limite:</span>
                        <span class="stat-value"><?= $code['max_uses'] ?? 'Sin limite' ?></span>
                    </div>
                    <?php if (!empty($stats)): ?>
                    <hr>
                    <div class="stat-row">
                        <span class="stat-label">Entradas totales:</span>
                        <span class="stat-value"><?= $stats['total_tickets'] ?? 0 ?></span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Confirmadas:</span>
                        <span class="stat-value text-success"><?= $stats['confirmed'] ?? 0 ?></span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Check-in:</span>
                        <span class="stat-value text-info"><?= $stats['checked_in'] ?? 0 ?></span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Pendientes:</span>
                        <span class="stat-value"><?= $stats['pending'] ?? 0 ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Compartir Codigo</h3>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($code['code']) ?>" readonly
                           style="font-family: monospace; font-size: 1.1rem; text-align: center; margin-bottom: 10px;">
                    <button type="button" class="btn btn-outline btn-block" onclick="copyCode('<?= htmlspecialchars($code['code']) ?>')">
                        <i class="fas fa-copy"></i> Copiar Codigo
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar Cambios' : 'Crear Codigo' ?>
            </button>
        </div>
    </div>
</form>

<script>
// Ticket types data
const ticketTypesByEvent = <?= json_encode($ticketTypesByEvent ?? []) ?>;
const currentTicketTypeId = <?= json_encode($code['ticket_type_id'] ?? null) ?>;

function updateTicketTypes() {
    const eventId = document.getElementById('event_id').value;
    const select = document.getElementById('ticket_type_id');

    // Clear options
    select.innerHTML = '<option value="">Cualquier tipo de entrada</option>';

    if (eventId && ticketTypesByEvent[eventId]) {
        ticketTypesByEvent[eventId].forEach(function(ticketType) {
            const option = document.createElement('option');
            option.value = ticketType.id;
            option.textContent = ticketType.name + ' (' + parseFloat(ticketType.price || 0).toFixed(2) + ' EUR)';
            if (currentTicketTypeId == ticketType.id) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }
}

function toggleDiscountAmount() {
    const type = document.getElementById('discount_type').value;
    const group = document.getElementById('discount_amount_group');
    const help = document.getElementById('discount_help');

    if (type === 'none') {
        group.style.display = 'none';
    } else {
        group.style.display = 'block';
        help.textContent = type === 'percentage' ? 'Porcentaje de descuento (0-100)' : 'Cantidad fija en euros';
    }
}

function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Codigo copiado: ' + code);
    }).catch(() => {
        prompt('Copia este codigo:', code);
    });
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    updateTicketTypes();
    toggleDiscountAmount();
});
</script>

<style>
.stat-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
}
.stat-label {
    color: #888;
}
.stat-value {
    font-weight: bold;
}
</style>
