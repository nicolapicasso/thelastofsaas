<?php
/**
 * Event Form Template
 * TLOS - The Last of SaaS
 */
$isEdit = isset($event) && $event;
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= $isEdit ? 'Editar Evento' : 'Nuevo Evento' ?></h1>
        <p><?= $isEdit ? htmlspecialchars($event['name']) : 'Crear un nuevo evento' ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/events" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<?php if ($isEdit && !empty($stats)): ?>
<!-- Stats Cards -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: bold; color: var(--primary);"><?= $stats['sponsors_count'] ?? 0 ?></div>
        <div style="color: var(--text-muted); font-size: 0.85rem;">Sponsors</div>
    </div>
    <div class="card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: bold; color: var(--success);"><?= $stats['tickets_confirmed'] ?? 0 ?></div>
        <div style="color: var(--text-muted); font-size: 0.85rem;">Entradas</div>
    </div>
    <div class="card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: bold; color: var(--info);"><?= $stats['matches_count'] ?? 0 ?></div>
        <div style="color: var(--text-muted); font-size: 0.85rem;">Matches</div>
    </div>
    <div class="card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: bold; color: var(--warning);"><?= $stats['meetings_count'] ?? 0 ?></div>
        <div style="color: var(--text-muted); font-size: 0.85rem;">Reuniones</div>
    </div>
</div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? '/admin/events/' . $event['id'] : '/admin/events' ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="form-grid">
        <!-- Main Content -->
        <div class="form-main">
            <div class="card">
                <div class="card-header">
                    <h3>Información del Evento</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nombre del Evento *</label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="<?= htmlspecialchars($event['name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="event_date">Fecha Inicio</label>
                            <input type="date" id="event_date" name="event_date" class="form-control"
                                   value="<?= $event['event_date'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="event_end_date">Fecha Fin</label>
                            <input type="date" id="event_end_date" name="event_end_date" class="form-control"
                                   value="<?= $event['event_end_date'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Ubicación</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="venue_name">Nombre del Lugar</label>
                        <input type="text" id="venue_name" name="venue_name" class="form-control"
                               value="<?= htmlspecialchars($event['venue_name'] ?? '') ?>"
                               placeholder="Ej: Palacio de Congresos">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="venue_address">Dirección</label>
                            <input type="text" id="venue_address" name="venue_address" class="form-control"
                                   value="<?= htmlspecialchars($event['venue_address'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="venue_city">Ciudad</label>
                            <input type="text" id="venue_city" name="venue_city" class="form-control"
                                   value="<?= htmlspecialchars($event['venue_city'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="venue_coordinates">Coordenadas (opcional)</label>
                        <input type="text" id="venue_coordinates" name="venue_coordinates" class="form-control"
                               value="<?= htmlspecialchars($event['venue_coordinates'] ?? '') ?>"
                               placeholder="41.3851, 2.1734">
                    </div>
                </div>
            </div>

            <?php if ($isEdit && !empty($sponsors)): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Sponsors del Evento</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sponsor</th>
                                <th>Nivel</th>
                                <th width="100">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sponsors as $sponsor): ?>
                            <tr>
                                <td>
                                    <?php if ($sponsor['logo_url']): ?>
                                        <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="" style="height: 30px; margin-right: 10px;">
                                    <?php endif; ?>
                                    <?= htmlspecialchars($sponsor['name']) ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $sponsor['priority_level'] === 'platinum' ? 'warning' : ($sponsor['priority_level'] === 'gold' ? 'warning' : 'secondary') ?>">
                                        <?= ucfirst($sponsor['priority_level']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/admin/sponsors/<?= $sponsor['id'] ?>/edit" class="btn btn-sm btn-outline" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if (!empty($allSponsors)): ?>
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                        <div class="form-row" style="align-items: end;">
                            <div class="form-group" style="flex: 2;">
                                <label>Añadir Sponsor</label>
                                <select id="new_sponsor_id" class="form-control">
                                    <option value="">Seleccionar...</option>
                                    <?php
                                    $existingIds = array_column($sponsors, 'id');
                                    foreach ($allSponsors as $s):
                                        if (!in_array($s['id'], $existingIds)):
                                    ?>
                                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label>Nivel</label>
                                <select id="new_sponsor_level" class="form-control">
                                    <?php foreach ($levelOptions as $val => $lbl): ?>
                                        <option value="<?= $val ?>"><?= $lbl ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-outline" onclick="addSponsor()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="form-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Publicación</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="status">Estado</label>
                        <select id="status" name="status" class="form-control">
                            <?php foreach ($statusOptions as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($event['status'] ?? 'draft') === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="total_capacity">Aforo Total *</label>
                        <input type="number" id="total_capacity" name="total_capacity" class="form-control"
                               value="<?= $event['total_capacity'] ?? 100 ?>" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="featured_image">Imagen Destacada</label>
                        <div class="media-picker">
                            <input type="hidden" id="featured_image" name="featured_image"
                                   value="<?= htmlspecialchars($event['featured_image'] ?? '') ?>">
                            <div class="media-preview" id="featured_image_preview">
                                <?php if (!empty($event['featured_image'])): ?>
                                    <img src="<?= htmlspecialchars($event['featured_image']) ?>" alt="">
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-outline btn-sm"
                                    onclick="openMediaPicker('featured_image')">
                                <i class="fas fa-image"></i> Seleccionar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Opciones</h3>
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <input type="checkbox" id="registration_open" name="registration_open" value="1"
                               <?= ($event['registration_open'] ?? 1) ? 'checked' : '' ?>>
                        <label for="registration_open">Registro abierto</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="matching_enabled" name="matching_enabled" value="1"
                               <?= ($event['matching_enabled'] ?? 1) ? 'checked' : '' ?>>
                        <label for="matching_enabled">Matching habilitado</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="meetings_enabled" name="meetings_enabled" value="1"
                               <?= ($event['meetings_enabled'] ?? 1) ? 'checked' : '' ?>>
                        <label for="meetings_enabled">Reuniones habilitadas</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar Cambios' : 'Crear Evento' ?>
            </button>
        </div>
    </div>
</form>

<?php if ($isEdit): ?>
<script>
function addSponsor() {
    const sponsorId = document.getElementById('new_sponsor_id').value;
    const level = document.getElementById('new_sponsor_level').value;

    if (!sponsorId) {
        alert('Selecciona un sponsor');
        return;
    }

    const formData = new FormData();
    formData.append('_csrf_token', '<?= $csrf_token ?>');
    formData.append('sponsor_id', sponsorId);
    formData.append('level', level);

    fetch('/admin/events/<?= $event['id'] ?>/sponsors', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al añadir sponsor');
        }
    });
}
</script>
<?php endif; ?>
