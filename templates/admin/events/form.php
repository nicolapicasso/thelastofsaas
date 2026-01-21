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
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: bold; color: var(--primary);"><?= $stats['sponsors_count'] ?? 0 ?></div>
        <div style="color: var(--text-muted); font-size: 0.85rem;">Sponsors</div>
    </div>
    <div class="card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: bold; color: #059669;"><?= $stats['companies_count'] ?? 0 ?></div>
        <div style="color: var(--text-muted); font-size: 0.85rem;">Empresas</div>
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
                        <label for="description">Descripcion Larga (HTML)</label>
                        <textarea id="description" name="description" class="form-control wysiwyg" rows="10"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
                        <small class="form-text">Editor de texto enriquecido para la descripcion completa del evento</small>
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

            <?php if ($isEdit): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Sponsors del Evento</h3>
                    <span class="badge" id="sponsors-count"><?= count($sponsors ?? []) ?> seleccionados</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($allSponsors)): ?>
                    <div class="checkbox-list-header">
                        <input type="text" id="sponsors-search" class="form-control" placeholder="Buscar sponsors..." style="margin-bottom: 1rem;">
                    </div>
                    <div class="checkbox-list" id="sponsors-list" style="max-height: 400px; overflow-y: auto; border: 1px solid var(--border); border-radius: 4px;">
                        <?php
                        $existingSponsorIds = array_column($sponsors ?? [], 'id');
                        $sponsorLevels = [];
                        foreach ($sponsors ?? [] as $s) {
                            $sponsorLevels[$s['id']] = $s['level'] ?? 'bronze';
                        }
                        foreach ($allSponsors as $s):
                            $isChecked = in_array($s['id'], $existingSponsorIds);
                            $currentLevel = $sponsorLevels[$s['id']] ?? 'bronze';
                        ?>
                        <div class="checkbox-item" data-name="<?= strtolower(htmlspecialchars($s['name'])) ?>" style="display: flex; align-items: center; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); gap: 1rem;">
                            <input type="checkbox"
                                   id="sponsor-<?= $s['id'] ?>"
                                   name="sponsors[]"
                                   value="<?= $s['id'] ?>"
                                   <?= $isChecked ? 'checked' : '' ?>
                                   onchange="updateSponsor(<?= $s['id'] ?>, this.checked)"
                                   style="width: 20px; height: 20px;">
                            <?php if (!empty($s['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($s['logo_url']) ?>" alt="" style="height: 32px; width: 60px; object-fit: contain; background: #f5f5f5; border-radius: 4px;">
                            <?php else: ?>
                                <div style="height: 32px; width: 60px; background: var(--bg-secondary); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-building" style="color: var(--text-muted);"></i>
                                </div>
                            <?php endif; ?>
                            <label for="sponsor-<?= $s['id'] ?>" style="flex: 1; cursor: pointer; margin: 0; font-weight: 500;">
                                <?= htmlspecialchars($s['name']) ?>
                            </label>
                            <select id="sponsor-level-<?= $s['id'] ?>"
                                    onchange="updateSponsorLevel(<?= $s['id'] ?>, this.value)"
                                    class="form-control"
                                    style="width: 120px; <?= !$isChecked ? 'opacity: 0.5;' : '' ?>"
                                    <?= !$isChecked ? 'disabled' : '' ?>>
                                <?php foreach ($levelOptions as $val => $lbl): ?>
                                    <option value="<?= $val ?>" <?= $currentLevel === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 0.5rem; display: flex; gap: 1rem;">
                        <button type="button" class="btn btn-sm btn-outline" onclick="selectAllSponsors(true)">
                            <i class="fas fa-check-double"></i> Seleccionar todos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="selectAllSponsors(false)">
                            <i class="fas fa-times"></i> Deseleccionar todos
                        </button>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No hay sponsors disponibles. <a href="/admin/sponsors/create">Crear uno nuevo</a></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($isEdit): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Empresas del Evento</h3>
                    <span class="badge" id="companies-count"><?= count($companies ?? []) ?> seleccionadas</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($allCompanies)): ?>
                    <div class="checkbox-list-header">
                        <input type="text" id="companies-search" class="form-control" placeholder="Buscar empresas..." style="margin-bottom: 1rem;">
                    </div>
                    <div class="checkbox-list" id="companies-list" style="max-height: 400px; overflow-y: auto; border: 1px solid var(--border); border-radius: 4px;">
                        <?php
                        $existingCompanyIds = array_column($companies ?? [], 'id');
                        foreach ($allCompanies as $c):
                            $isChecked = in_array($c['id'], $existingCompanyIds);
                        ?>
                        <div class="checkbox-item" data-name="<?= strtolower(htmlspecialchars($c['name'])) ?>" style="display: flex; align-items: center; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); gap: 1rem;">
                            <input type="checkbox"
                                   id="company-<?= $c['id'] ?>"
                                   name="companies[]"
                                   value="<?= $c['id'] ?>"
                                   <?= $isChecked ? 'checked' : '' ?>
                                   onchange="updateCompany(<?= $c['id'] ?>, this.checked)"
                                   style="width: 20px; height: 20px;">
                            <?php if (!empty($c['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($c['logo_url']) ?>" alt="" style="height: 32px; width: 60px; object-fit: contain; background: #f5f5f5; border-radius: 4px;">
                            <?php else: ?>
                                <div style="height: 32px; width: 60px; background: var(--bg-secondary); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-building" style="color: var(--text-muted);"></i>
                                </div>
                            <?php endif; ?>
                            <label for="company-<?= $c['id'] ?>" style="flex: 1; cursor: pointer; margin: 0; font-weight: 500;">
                                <?= htmlspecialchars($c['name']) ?>
                            </label>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">
                                <?= htmlspecialchars($c['sector'] ?? '') ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 0.5rem; display: flex; gap: 1rem;">
                        <button type="button" class="btn btn-sm btn-outline" onclick="selectAllCompanies(true)">
                            <i class="fas fa-check-double"></i> Seleccionar todas
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="selectAllCompanies(false)">
                            <i class="fas fa-times"></i> Deseleccionar todas
                        </button>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No hay empresas disponibles. <a href="/admin/companies/create">Crear una nueva</a></p>
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
const csrfToken = '<?= $csrf_token ?>';
const eventId = <?= $event['id'] ?>;

// === SPONSORS ===
function updateSponsor(sponsorId, isChecked) {
    const levelSelect = document.getElementById('sponsor-level-' + sponsorId);
    const formData = new FormData();
    formData.append('_csrf_token', csrfToken);

    if (isChecked) {
        // Add sponsor
        formData.append('sponsor_id', sponsorId);
        formData.append('level', levelSelect.value);
        levelSelect.disabled = false;
        levelSelect.style.opacity = '1';

        fetch('/admin/events/' + eventId + '/sponsors', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('sponsor-' + sponsorId).checked = false;
                alert(data.error || 'Error al anadir sponsor');
            }
            updateSponsorsCount();
        });
    } else {
        // Remove sponsor
        levelSelect.disabled = true;
        levelSelect.style.opacity = '0.5';

        fetch('/admin/events/' + eventId + '/sponsors/' + sponsorId + '/delete', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('sponsor-' + sponsorId).checked = true;
                alert(data.error || 'Error al quitar sponsor');
            }
            updateSponsorsCount();
        });
    }
}

function updateSponsorLevel(sponsorId, level) {
    const checkbox = document.getElementById('sponsor-' + sponsorId);
    if (!checkbox.checked) return;

    const formData = new FormData();
    formData.append('_csrf_token', csrfToken);
    formData.append('level', level);

    fetch('/admin/events/' + eventId + '/sponsors/' + sponsorId + '/level', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            alert(data.error || 'Error al actualizar nivel');
        }
    });
}

function selectAllSponsors(select) {
    const checkboxes = document.querySelectorAll('#sponsors-list input[type="checkbox"]');
    checkboxes.forEach(cb => {
        if (cb.checked !== select) {
            cb.checked = select;
            updateSponsor(cb.value, select);
        }
    });
}

function updateSponsorsCount() {
    const count = document.querySelectorAll('#sponsors-list input[type="checkbox"]:checked').length;
    document.getElementById('sponsors-count').textContent = count + ' seleccionados';
}

// === COMPANIES ===
function updateCompany(companyId, isChecked) {
    const formData = new FormData();
    formData.append('_csrf_token', csrfToken);

    if (isChecked) {
        // Add company
        formData.append('company_id', companyId);

        fetch('/admin/events/' + eventId + '/companies', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('company-' + companyId).checked = false;
                alert(data.error || 'Error al anadir empresa');
            }
            updateCompaniesCount();
        });
    } else {
        // Remove company
        fetch('/admin/events/' + eventId + '/companies/' + companyId + '/delete', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('company-' + companyId).checked = true;
                alert(data.error || 'Error al quitar empresa');
            }
            updateCompaniesCount();
        });
    }
}

function selectAllCompanies(select) {
    const checkboxes = document.querySelectorAll('#companies-list input[type="checkbox"]');
    checkboxes.forEach(cb => {
        if (cb.checked !== select) {
            cb.checked = select;
            updateCompany(cb.value, select);
        }
    });
}

function updateCompaniesCount() {
    const count = document.querySelectorAll('#companies-list input[type="checkbox"]:checked').length;
    document.getElementById('companies-count').textContent = count + ' seleccionadas';
}

// === SEARCH FILTERS ===
document.getElementById('sponsors-search')?.addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('#sponsors-list .checkbox-item').forEach(item => {
        const name = item.dataset.name || '';
        item.style.display = name.includes(query) ? 'flex' : 'none';
    });
});

document.getElementById('companies-search')?.addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('#companies-list .checkbox-item').forEach(item => {
        const name = item.dataset.name || '';
        item.style.display = name.includes(query) ? 'flex' : 'none';
    });
});
</script>
<?php endif; ?>

<!-- TinyMCE WYSIWYG Editor (Self-hosted via jsDelivr - no API key needed) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.2/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: 'textarea.wysiwyg',
    plugins: 'lists link autolink',
    toolbar: 'undo redo | bold italic underline | bullist numlist | link | removeformat',
    menubar: false,
    statusbar: false,
    height: 300,
    branding: false,
    promotion: false,
    content_style: 'body { font-family: Montserrat, sans-serif; font-size: 14px; }',
    setup: function(editor) {
        editor.on('change', function() {
            tinymce.triggerSave();
        });
    }
});
</script>
