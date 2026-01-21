<?php
/**
 * Activity Form Template
 * TLOS - The Last of SaaS
 */
$isEdit = isset($activity) && $activity;
$selectedEventId = $activity['event_id'] ?? $preselectedEventId ?? null;
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= $isEdit ? 'Editar Actividad' : 'Nueva Actividad' ?></h1>
        <p><?= $isEdit ? htmlspecialchars($activity['title']) : 'Crear una nueva actividad' ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/activities<?= $selectedEventId ? '?event_id=' . $selectedEventId : '' ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? '/admin/activities/' . $activity['id'] : '/admin/activities' ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="form-grid">
        <div class="form-main">
            <div class="card">
                <div class="card-header">
                    <h3>Informacion Basica</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Titulo *</label>
                        <input type="text" id="title" name="title" class="form-control"
                               value="<?= htmlspecialchars($activity['title'] ?? '') ?>" required
                               placeholder="Ej: Keynote de apertura, Almuerzo networking...">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="activity_type">Tipo de Actividad *</label>
                            <select id="activity_type" name="activity_type" class="form-control" required>
                                <?php foreach ($activityTypes as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($activity['activity_type'] ?? 'charla') === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Categoria (opcional)</label>
                            <select id="category_id" name="category_id" class="form-control">
                                <option value="">Sin categoria</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= ($activity['category_id'] ?? null) == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripcion</label>
                        <textarea id="description" name="description" class="form-control" rows="4"
                                  placeholder="Descripcion de la actividad..."><?= htmlspecialchars($activity['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Fecha y Hora</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="event_id">Evento *</label>
                            <select id="event_id" name="event_id" class="form-control" required>
                                <option value="">Seleccionar evento...</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= $event['id'] ?>" <?= $selectedEventId == $event['id'] ? 'selected' : '' ?>
                                            data-start="<?= $event['start_date'] ?? '' ?>"
                                            data-end="<?= $event['end_date'] ?? '' ?>">
                                        <?= htmlspecialchars($event['name']) ?>
                                        <?php if ($event['start_date'] ?? null): ?>
                                            (<?= date('d/m/Y', strtotime($event['start_date'])) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="activity_date">Fecha *</label>
                            <input type="date" id="activity_date" name="activity_date" class="form-control"
                                   value="<?= $activity['activity_date'] ?? '' ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_time">Hora Inicio *</label>
                            <input type="time" id="start_time" name="start_time" class="form-control"
                                   value="<?= isset($activity['start_time']) ? substr($activity['start_time'], 0, 5) : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time">Hora Fin *</label>
                            <input type="time" id="end_time" name="end_time" class="form-control"
                                   value="<?= isset($activity['end_time']) ? substr($activity['end_time'], 0, 5) : '' ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Ubicacion y Speaker</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="room_id">Sala</label>
                            <select id="room_id" name="room_id" class="form-control">
                                <option value="">Sin sala asignada</option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?= $room['id'] ?>" <?= ($activity['room_id'] ?? null) == $room['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($room['name']) ?>
                                        <?php if ($room['capacity']): ?>
                                            (Cap: <?= $room['capacity'] ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-help">
                                <a href="/admin/rooms/create" target="_blank">+ Crear nueva sala</a>
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="speaker_id">Speaker / Ponente</label>
                            <select id="speaker_id" name="speaker_id" class="form-control">
                                <option value="">Sin speaker</option>
                                <?php foreach ($speakers as $speaker): ?>
                                    <option value="<?= $speaker['id'] ?>" <?= ($activity['speaker_id'] ?? null) == $speaker['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($speaker['name']) ?>
                                        <?php if ($speaker['position'] ?? null): ?>
                                            - <?= htmlspecialchars($speaker['position']) ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-help">
                                <a href="/admin/team/create" target="_blank">+ Crear nuevo speaker</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Multimedia</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="image_url">Imagen (URL)</label>
                            <?php if (!empty($activity['image_url'])): ?>
                                <div class="image-preview" style="margin-bottom: 10px;">
                                    <img src="<?= htmlspecialchars($activity['image_url']) ?>" alt="Imagen actual"
                                         style="max-width: 200px; max-height: 120px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                            <?php endif; ?>
                            <input type="url" id="image_url" name="image_url" class="form-control"
                                   value="<?= htmlspecialchars($activity['image_url'] ?? '') ?>"
                                   placeholder="https://...">
                        </div>
                        <div class="form-group">
                            <label for="video_url">Video (URL YouTube/Vimeo)</label>
                            <input type="url" id="video_url" name="video_url" class="form-control"
                                   value="<?= htmlspecialchars($activity['video_url'] ?? '') ?>"
                                   placeholder="https://youtube.com/watch?v=...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Opciones</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="active">Estado</label>
                        <select id="active" name="active" class="form-control">
                            <option value="1" <?= ($activity['active'] ?? 1) == 1 ? 'selected' : '' ?>>Activa</option>
                            <option value="0" <?= ($activity['active'] ?? 1) == 0 ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1"
                                   <?= ($activity['is_featured'] ?? 0) ? 'checked' : '' ?>>
                            Actividad Destacada
                        </label>
                        <small class="form-help">Se mostrara en la seccion destacada</small>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-control"
                               value="<?= htmlspecialchars($activity['sort_order'] ?? '0') ?>" min="0">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Registro</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="requires_registration" value="1"
                                   <?= ($activity['requires_registration'] ?? 0) ? 'checked' : '' ?>>
                            Requiere registro previo
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="max_attendees">Aforo maximo</label>
                        <input type="number" id="max_attendees" name="max_attendees" class="form-control"
                               value="<?= htmlspecialchars($activity['max_attendees'] ?? '') ?>"
                               min="0" placeholder="Sin limite">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar Actividad' : 'Crear Actividad' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Update activity date based on selected event
document.getElementById('event_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const startDate = selected.dataset.start;
    if (startDate && !document.getElementById('activity_date').value) {
        document.getElementById('activity_date').value = startDate;
    }
});
</script>
