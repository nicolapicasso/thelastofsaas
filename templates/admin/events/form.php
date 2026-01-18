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
        <?php if ($isEdit && !empty($event['slug'])): ?>
            <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>" class="btn btn-outline" target="_blank">
                <i class="fas fa-external-link-alt"></i> Ver en web
            </a>
        <?php endif; ?>
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

<form method="POST" action="<?= $isEdit ? '/admin/events/' . $event['id'] : '/admin/events' ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="form-grid">
        <!-- Main Content -->
        <div class="form-main">
            <div class="card">
                <div class="card-header">
                    <h3>Informacion del Evento</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nombre del Evento *</label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="<?= htmlspecialchars($event['name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="short_description">Descripcion Corta</label>
                        <input type="text" id="short_description" name="short_description" class="form-control"
                               value="<?= htmlspecialchars($event['short_description'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Descripcion</label>
                        <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">Fecha Inicio *</label>
                            <input type="date" id="start_date" name="start_date" class="form-control"
                                   value="<?= $event['start_date'] ?? '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">Fecha Fin</label>
                            <input type="date" id="end_date" name="end_date" class="form-control"
                                   value="<?= $event['end_date'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_time">Hora Inicio</label>
                            <input type="time" id="start_time" name="start_time" class="form-control"
                                   value="<?= $event['start_time'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="end_time">Hora Fin</label>
                            <input type="time" id="end_time" name="end_time" class="form-control"
                                   value="<?= $event['end_time'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Ubicacion</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="location">Nombre del Lugar</label>
                        <input type="text" id="location" name="location" class="form-control"
                               value="<?= htmlspecialchars($event['location'] ?? '') ?>"
                               placeholder="Ej: Palacio de Congresos">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="address">Direccion</label>
                            <input type="text" id="address" name="address" class="form-control"
                                   value="<?= htmlspecialchars($event['address'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="city">Ciudad</label>
                            <input type="text" id="city" name="city" class="form-control"
                                   value="<?= htmlspecialchars($event['city'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="coordinates">Coordenadas (opcional)</label>
                        <input type="text" id="coordinates" name="coordinates" class="form-control"
                               value="<?= htmlspecialchars($event['coordinates'] ?? '') ?>"
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
                                    <?php if ($sponsor['logo_url'] ?? null): ?>
                                        <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="" style="height: 30px; margin-right: 10px;">
                                    <?php endif; ?>
                                    <?= htmlspecialchars($sponsor['name']) ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= ($sponsor['level'] ?? '') === 'platinum' ? 'warning' : (($sponsor['level'] ?? '') === 'gold' ? 'warning' : 'secondary') ?>">
                                        <?= ucfirst($sponsor['level'] ?? 'bronze') ?>
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
                                <label>Anadir Sponsor</label>
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

            <?php if ($isEdit): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Empresas del Evento</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($companies)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th width="100">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($companies as $company): ?>
                            <tr id="company-row-<?= $company['id'] ?>">
                                <td>
                                    <?php if ($company['logo_url'] ?? null): ?>
                                        <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="" style="height: 30px; margin-right: 10px;">
                                    <?php endif; ?>
                                    <?= htmlspecialchars($company['name']) ?>
                                </td>
                                <td>
                                    <a href="/admin/companies/<?= $company['id'] ?>/edit" class="btn btn-sm btn-outline" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline btn-danger" onclick="removeCompany(<?= $company['id'] ?>)" title="Quitar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="text-muted">No hay empresas asignadas a este evento.</p>
                    <?php endif; ?>

                    <?php if (!empty($allCompanies)): ?>
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                        <div class="form-row" style="align-items: end;">
                            <div class="form-group" style="flex: 2;">
                                <label>Anadir Empresa</label>
                                <select id="new_company_id" class="form-control">
                                    <option value="">Seleccionar...</option>
                                    <?php
                                    $existingCompanyIds = array_column($companies ?? [], 'id');
                                    foreach ($allCompanies as $c):
                                        if (!in_array($c['id'], $existingCompanyIds)):
                                    ?>
                                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-outline" onclick="addCompany()">
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
                    <h3>Publicacion</h3>
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
                        <label for="max_attendees">Aforo Total</label>
                        <input type="number" id="max_attendees" name="max_attendees" class="form-control"
                               value="<?= $event['max_attendees'] ?? 100 ?>" min="1">
                    </div>

                    <div class="form-group">
                        <label for="featured_image_file">Imagen Destacada</label>
                        <?php if (!empty($event['featured_image'])): ?>
                            <div class="image-preview" style="margin-bottom: 10px;">
                                <img src="<?= htmlspecialchars($event['featured_image']) ?>" alt="Imagen actual"
                                     style="max-width: 200px; max-height: 120px; border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" id="featured_image_file" name="featured_image_file" class="form-control"
                               accept="image/png,image/jpeg,image/gif,image/webp">
                        <small class="form-help">PNG, JPG, GIF, WebP. Max: 2MB</small>
                        <input type="hidden" name="featured_image" value="<?= htmlspecialchars($event['featured_image'] ?? '') ?>">
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
            alert(data.error || 'Error al anadir sponsor');
        }
    });
}

function addCompany() {
    const companyId = document.getElementById('new_company_id').value;

    if (!companyId) {
        alert('Selecciona una empresa');
        return;
    }

    const formData = new FormData();
    formData.append('_csrf_token', '<?= $csrf_token ?>');
    formData.append('company_id', companyId);

    fetch('/admin/events/<?= $event['id'] ?>/companies', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al anadir empresa');
        }
    });
}

function removeCompany(companyId) {
    if (!confirm('¿Quitar esta empresa del evento?')) return;

    const formData = new FormData();
    formData.append('_csrf_token', '<?= $csrf_token ?>');

    fetch('/admin/events/<?= $event['id'] ?>/companies/' + companyId + '/delete', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('company-row-' + companyId).remove();
        } else {
            alert(data.error || 'Error al quitar empresa');
        }
    });
}
</script>
<?php endif; ?>
