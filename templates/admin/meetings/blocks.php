<?php
/**
 * Meeting Blocks Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Bloques Horarios</h1>
        <p>Configura los bloques de reuniones</p>
    </div>
    <div class="page-header-actions">
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
                <input type="number" name="meeting_duration" class="form-control" value="15" min="5" max="60">
            </div>
            <div class="form-group">
                <label>Mesas</label>
                <input type="number" name="simultaneous_meetings" class="form-control" value="10" min="1" max="50">
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
                            <td><?= $block['meeting_duration'] ?> min</td>
                            <td><?= $block['simultaneous_meetings'] ?></td>
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

<style>
.form-inline-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
    align-items: end;
}
</style>
