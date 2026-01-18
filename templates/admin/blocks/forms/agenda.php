<?php
/**
 * Agenda Block Admin Form
 * TLOS - The Last of SaaS
 */

use App\Models\Event;
use App\Models\Room;
use App\Models\Activity;

$eventModel = new Event();
$roomModel = new Room();
$activityModel = new Activity();

$events = $eventModel->getActive();
$rooms = $roomModel->getActive();
$activityTypes = Activity::getActivityTypes();
?>

<div class="block-form">
    <div class="form-section">
        <h4>Configuracion del bloque Agenda</h4>
        <div class="form-group">
            <label>Titulo de la seccion</label>
            <input type="text" data-content="title" value="<?= htmlspecialchars($content['title'] ?? 'Agenda del Evento') ?>">
        </div>
        <div class="form-group">
            <label>Subtitulo</label>
            <input type="text" data-content="subtitle" value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>" placeholder="Subtitulo opcional">
        </div>
    </div>

    <div class="form-section">
        <h4>Evento</h4>
        <div class="form-group">
            <label>Seleccionar evento *</label>
            <select data-setting="event_id" id="agenda-event-id" required>
                <option value="">-- Seleccionar evento --</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= $event['id'] ?>" <?= ($settings['event_id'] ?? '') == $event['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($event['name']) ?>
                        <?php if ($event['start_date'] ?? null): ?>
                            (<?= date('d/m/Y', strtotime($event['start_date'])) ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="form-help">Obligatorio: selecciona el evento del que mostrar la agenda</small>
        </div>
    </div>

    <div class="form-section">
        <h4>Modo de visualizacion</h4>
        <div class="form-group">
            <label>Formato de visualizacion</label>
            <select data-setting="display_mode">
                <option value="timeline" <?= ($settings['display_mode'] ?? 'timeline') === 'timeline' ? 'selected' : '' ?>>Linea temporal (Timeline)</option>
                <option value="cards" <?= ($settings['display_mode'] ?? 'timeline') === 'cards' ? 'selected' : '' ?>>Tarjetas</option>
                <option value="compact" <?= ($settings['display_mode'] ?? 'timeline') === 'compact' ? 'selected' : '' ?>>Compacto (lista)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Agrupar por</label>
            <select data-setting="group_by">
                <option value="date" <?= ($settings['group_by'] ?? 'date') === 'date' ? 'selected' : '' ?>>Fecha</option>
                <option value="room" <?= ($settings['group_by'] ?? 'date') === 'room' ? 'selected' : '' ?>>Sala</option>
                <option value="type" <?= ($settings['group_by'] ?? 'date') === 'type' ? 'selected' : '' ?>>Tipo de actividad</option>
            </select>
        </div>
    </div>

    <div class="form-section">
        <h4>Elementos a mostrar</h4>
        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_time" <?= ($settings['show_time'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar hora</span>
                </label>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_room" <?= ($settings['show_room'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar sala</span>
                </label>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_speaker" <?= ($settings['show_speaker'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar speaker</span>
                </label>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" data-setting="show_description" <?= ($settings['show_description'] ?? true) ? 'checked' : '' ?>>
                    <span>Mostrar descripcion</span>
                </label>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h4>Filtros (opcional)</h4>
        <div class="form-group">
            <label>Filtrar por fecha</label>
            <input type="date" data-setting="filter_by_date" value="<?= $settings['filter_by_date'] ?? '' ?>">
            <small class="form-help">Dejar vacio para mostrar todas las fechas</small>
        </div>

        <div class="form-group">
            <label>Filtrar por sala</label>
            <select data-setting="filter_by_room">
                <option value="">Todas las salas</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['id'] ?>" <?= ($settings['filter_by_room'] ?? '') == $room['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($room['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Filtrar por tipo de actividad</label>
            <select data-setting="filter_by_type">
                <option value="">Todos los tipos</option>
                <?php foreach ($activityTypes as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ($settings['filter_by_type'] ?? '') === $value ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php include TEMPLATES_PATH . '/admin/partials/text-color-settings.php'; ?>
    <?php include TEMPLATES_PATH . '/admin/partials/visibility-settings.php'; ?>

    <div class="info-box">
        <p><strong>Nota:</strong> Las actividades se obtienen automaticamente del evento seleccionado.</p>
        <p>Gestiona las actividades desde <a href="/admin/activities" target="_blank">Administrar Actividades</a>.</p>
    </div>
</div>
