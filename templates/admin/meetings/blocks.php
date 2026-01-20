<?php
/**
 * Meeting Blocks Template
 * TLOS - The Last of SaaS
 */

// Get current event slug for public links
$currentEventSlug = '';
foreach ($events as $evt) {
    if ($evt['id'] == $currentEventId) {
        $currentEventSlug = $evt['slug'] ?? '';
        break;
    }
}
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Bloques Horarios</h1>
        <p>Configura los bloques de reuniones</p>
    </div>
    <div class="page-header-actions">
        <?php if ($currentEventSlug): ?>
            <a href="/eventos/<?= htmlspecialchars($currentEventSlug) ?>/reuniones" class="btn btn-outline" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Público</a>
        <?php endif; ?>
        <a href="/admin/meetings/assignments?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-calendar-check"></i> Reuniones</a>
        <a href="/admin/meetings/matching?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-handshake"></i> Matching</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<!-- Event Selector -->
<div class="card filters-card">
    <div class="filters-form">
        <div class="filter-group">
            <label>Evento</label>
            <select onchange="location.href='?event_id='+this.value" class="form-control">
                <?php foreach ($events as $evt): ?>
                    <option value="<?= $evt['id'] ?>" <?= $currentEventId == $evt['id'] ? 'selected' : '' ?>><?= htmlspecialchars($evt['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- Create Block Form -->
<?php if ($currentEventId): ?>
<div class="card">
    <div class="card-header"><h3>Crear Bloque</h3></div>
    <div class="card-body">
        <form method="POST" action="/admin/meetings/blocks" class="form-inline-grid">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="event_id" value="<?= $currentEventId ?>">

            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="name" class="form-control" required placeholder="Sesión Mañana">
            </div>
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Hora Inicio</label>
                <input type="time" name="start_time" class="form-control" required value="10:00">
            </div>
            <div class="form-group">
                <label>Hora Fin</label>
                <input type="time" name="end_time" class="form-control" required value="13:00">
            </div>
            <div class="form-group">
                <label>Duración (min)</label>
                <input type="number" name="slot_duration" class="form-control" value="15" min="5" max="60">
            </div>
            <div class="form-group">
                <label>Mesas</label>
                <input type="number" name="total_rooms" class="form-control" value="10" min="1" max="50">
            </div>
            <div class="form-group">
                <label>Ubicación</label>
                <input type="text" name="location" class="form-control" placeholder="Sala Principal">
            </div>
            <div class="form-group" style="align-self: end;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Crear</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Blocks List -->
<div class="card">
    <?php if (empty($blocks)): ?>
        <div class="empty-state">
            <i class="fas fa-clock"></i>
            <h3>No hay bloques</h3>
            <p>Crea un bloque horario para empezar a asignar reuniones</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Duración</th>
                        <th>Mesas</th>
                        <th>Slots</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blocks as $block): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($block['name']) ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($block['event_date'])) ?></td>
                            <td><?= substr($block['start_time'], 0, 5) ?> - <?= substr($block['end_time'], 0, 5) ?></td>
                            <td><?= $block['slot_duration'] ?? 15 ?> min</td>
                            <td><?= $block['total_rooms'] ?? 10 ?></td>
                            <td>
                                <span class="badge badge-info"><?= $block['stats']['assigned_slots'] ?? 0 ?></span> /
                                <span class="text-muted"><?= $block['stats']['total_slots'] ?? 0 ?></span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $block['active'] ? 'success' : 'secondary' ?>">
                                    <?= $block['active'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <?php if ($currentEventSlug): ?>
                                        <a href="/eventos/<?= htmlspecialchars($currentEventSlug) ?>/reuniones/pantalla/<?= $block['id'] ?>" class="btn btn-sm btn-outline btn-info" target="_blank" title="Pantalla">
                                            <i class="fas fa-tv"></i>
                                        </a>
                                        <a href="/eventos/<?= htmlspecialchars($currentEventSlug) ?>/reuniones/horario/<?= $block['id'] ?>" class="btn btn-sm btn-outline" target="_blank" title="Horario">
                                            <i class="fas fa-table"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-outline" onclick="editBlock(<?= htmlspecialchars(json_encode($block)) ?>)" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if (($block['stats']['assigned_slots'] ?? 0) == 0): ?>
                                        <form method="POST" action="/admin/meetings/blocks/<?= $block['id'] ?>/delete" class="inline-form" onsubmit="return confirm('¿Eliminar bloque?')">
                                            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
                                            <button type="submit" class="btn btn-sm btn-outline btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
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

<!-- Edit Modal -->
<div id="editModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Bloque</h3>
            <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editForm" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="event_date" id="edit_event_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Hora Inicio</label>
                        <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Hora Fin</label>
                        <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Duracion (min)</label>
                        <input type="number" name="slot_duration" id="edit_slot_duration" class="form-control" min="5" max="60">
                    </div>
                    <div class="form-group">
                        <label>Mesas</label>
                        <input type="number" name="total_rooms" id="edit_total_rooms" class="form-control" min="1" max="50">
                    </div>
                </div>
                <div class="form-group">
                    <label>Ubicacion</label>
                    <input type="text" name="location" id="edit_location" class="form-control">
                </div>
                <div class="form-check">
                    <input type="checkbox" name="active" id="edit_active" value="1">
                    <label for="edit_active">Activo</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<style>
.form-inline-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
    align-items: end;
}
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-content {
    background: white;
    border-radius: 12px;
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
}
.modal-header h3 { margin: 0; }
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-muted);
}
.modal-body {
    padding: 1.5rem;
}
.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}
.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
}
</style>

<script>
function editBlock(block) {
    document.getElementById('editForm').action = '/admin/meetings/blocks/' + block.id;
    document.getElementById('edit_name').value = block.name || '';
    document.getElementById('edit_event_date').value = block.event_date || '';
    document.getElementById('edit_start_time').value = block.start_time ? block.start_time.substring(0,5) : '';
    document.getElementById('edit_end_time').value = block.end_time ? block.end_time.substring(0,5) : '';
    document.getElementById('edit_slot_duration').value = block.slot_duration || 15;
    document.getElementById('edit_total_rooms').value = block.total_rooms || 10;
    document.getElementById('edit_location').value = block.location || '';
    document.getElementById('edit_active').checked = block.active == 1;
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
