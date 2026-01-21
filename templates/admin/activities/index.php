<?php
/**
 * Activities List Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Actividades</h1>
        <p>Gestiona las actividades de los eventos (charlas, networking, etc.)</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/activities/create<?= $currentEventId ? '?event_id=' . $currentEventId : '' ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Actividad
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
    <form method="GET" action="/admin/activities" class="filters-form">
        <div class="filter-group">
            <label>Evento</label>
            <select name="event_id" onchange="this.form.submit()">
                <option value="">Todos los eventos</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= $event['id'] ?>" <?= ($currentEventId ?? null) == $event['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($event['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (!empty($dates)): ?>
            <div class="filter-group">
                <label>Fecha</label>
                <select name="date" onchange="this.form.submit()">
                    <option value="">Todas las fechas</option>
                    <?php foreach ($dates as $date): ?>
                        <option value="<?= $date ?>" <?= ($currentDate ?? null) === $date ? 'selected' : '' ?>>
                            <?= date('d/m/Y', strtotime($date)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <?php if ($currentEventId || $currentDate): ?>
            <a href="/admin/activities" class="btn btn-outline btn-sm">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Activities Table -->
<div class="card">
    <?php if (empty($activities)): ?>
        <div class="empty-state">
            <i class="fas fa-calendar-alt"></i>
            <h3>No hay actividades</h3>
            <p>Crea tu primera actividad para este evento</p>
            <a href="/admin/activities/create<?= $currentEventId ? '?event_id=' . $currentEventId : '' ?>" class="btn btn-primary">Crear Actividad</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="100">Fecha/Hora</th>
                        <th>Titulo</th>
                        <th>Tipo</th>
                        <th>Sala</th>
                        <th>Speaker</th>
                        <th>Estado</th>
                        <th width="140">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentDisplayDate = null;
                    foreach ($activities as $activity):
                        $activityDate = $activity['activity_date'];
                        $showDateHeader = ($currentDisplayDate !== $activityDate);
                        $currentDisplayDate = $activityDate;
                    ?>
                        <?php if ($showDateHeader && !$currentDate): ?>
                            <tr class="date-header-row">
                                <td colspan="7">
                                    <strong><i class="fas fa-calendar"></i> <?= date('l, d F Y', strtotime($activityDate)) ?></strong>
                                    <?php if ($activity['event_name'] ?? null): ?>
                                        <span class="text-muted">- <?= htmlspecialchars($activity['event_name']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td>
                                <strong><?= date('H:i', strtotime($activity['start_time'])) ?></strong>
                                <br>
                                <small class="text-muted"><?= date('H:i', strtotime($activity['end_time'])) ?></small>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($activity['title']) ?></strong>
                                <?php if ($activity['is_featured']): ?>
                                    <span class="badge badge-warning" title="Destacada"><i class="fas fa-star"></i></span>
                                <?php endif; ?>
                                <?php if ($activity['description'] ?? null): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars(substr($activity['description'], 0, 60)) ?><?= strlen($activity['description']) > 60 ? '...' : '' ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-outline">
                                    <?= $activityTypes[$activity['activity_type']] ?? $activity['activity_type'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($activity['room_name'] ?? null): ?>
                                    <span class="room-badge" style="border-left: 3px solid <?= htmlspecialchars($activity['room_color'] ?? '#6B7280') ?>">
                                        <?= htmlspecialchars($activity['room_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($activity['speaker_name'] ?? null): ?>
                                    <div class="speaker-info">
                                        <?php if ($activity['speaker_photo'] ?? null): ?>
                                            <img src="<?= htmlspecialchars($activity['speaker_photo']) ?>" alt="" class="speaker-thumb">
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($activity['speaker_name']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $activity['active'] ? 'success' : 'secondary' ?>">
                                    <?= $activity['active'] ? 'Activa' : 'Inactiva' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/activities/<?= $activity['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="/admin/activities/<?= $activity['id'] ?>/duplicate" class="inline-form">
                                        <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">
                                        <button type="submit" class="btn btn-sm btn-outline" title="Duplicar">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="/admin/activities/<?= $activity['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('Eliminar esta actividad?')">
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
    <?php endif; ?>
</div>

<style>
.date-header-row td {
    background-color: #f8f9fa;
    padding: 10px 15px !important;
}
.room-badge {
    display: inline-block;
    padding: 2px 8px;
    background: #f3f4f6;
    border-radius: 4px;
    font-size: 0.85em;
}
.speaker-info {
    display: flex;
    align-items: center;
    gap: 8px;
}
.speaker-thumb {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
}
</style>
