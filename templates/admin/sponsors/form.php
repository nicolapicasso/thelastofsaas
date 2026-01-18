<?php
/**
 * Sponsor Form Template
 * TLOS - The Last of SaaS
 */
$isEdit = isset($sponsor) && $sponsor;
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= $isEdit ? 'Editar Sponsor' : 'Nuevo Sponsor' ?></h1>
        <p><?= $isEdit ? htmlspecialchars($sponsor['name']) : 'Crear un nuevo sponsor' ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/sponsors" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? '/admin/sponsors/' . $sponsor['id'] : '/admin/sponsors' ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="form-grid">
        <div class="form-main">
            <div class="card">
                <div class="card-header">
                    <h3>Informacion Basica</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nombre *</label>
                        <input type="text" id="name" name="name" class="form-control"
                               value="<?= htmlspecialchars($sponsor['name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="tagline">Tagline / Descripcion Corta</label>
                        <input type="text" id="tagline" name="tagline" class="form-control"
                               value="<?= htmlspecialchars($sponsor['tagline'] ?? '') ?>"
                               maxlength="255" placeholder="Una frase que describe el servicio">
                    </div>

                    <div class="form-group">
                        <label for="description">Descripcion Completa</label>
                        <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($sponsor['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="website">Web</label>
                            <input type="url" id="website" name="website" class="form-control"
                                   value="<?= htmlspecialchars($sponsor['website'] ?? '') ?>"
                                   placeholder="https://...">
                        </div>
                        <div class="form-group">
                            <label for="logo_file">Logo</label>
                            <?php if (!empty($sponsor['logo_url'])): ?>
                                <div class="logo-preview" style="margin-bottom: 10px;">
                                    <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="Logo actual"
                                         style="max-width: 150px; max-height: 80px; border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" id="logo_file" name="logo_file" class="form-control"
                                   accept="image/png,image/jpeg,image/gif,image/svg+xml,image/webp">
                            <small class="form-help">Formatos: PNG, JPG, GIF, SVG, WebP. Max: 2MB</small>
                            <input type="hidden" name="logo_url" value="<?= htmlspecialchars($sponsor['logo_url'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Contacto</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="contact_name">Nombre de Contacto</label>
                        <input type="text" id="contact_name" name="contact_name" class="form-control"
                               value="<?= htmlspecialchars($sponsor['contact_name'] ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_email">Email de Contacto</label>
                            <input type="text" id="contact_email" name="contact_email" class="form-control"
                                   value="<?= htmlspecialchars($sponsor['contact_email'] ?? '') ?>"
                                   placeholder="email@empresa.com">
                        </div>
                        <div class="form-group">
                            <label for="contact_phone">Telefono</label>
                            <input type="text" id="contact_phone" name="contact_phone" class="form-control"
                                   value="<?= htmlspecialchars($sponsor['contact_phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="linkedin_url">LinkedIn</label>
                            <input type="url" id="linkedin_url" name="linkedin_url" class="form-control"
                                   value="<?= htmlspecialchars($sponsor['linkedin_url'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="twitter_url">Twitter/X</label>
                            <input type="url" id="twitter_url" name="twitter_url" class="form-control"
                                   value="<?= htmlspecialchars($sponsor['twitter_url'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($isEdit && !empty($events)): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Eventos Asociados</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Evento</th>
                                <th>Fecha</th>
                                <th>Nivel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $evt): ?>
                            <tr>
                                <td>
                                    <a href="/admin/events/<?= $evt['id'] ?>/edit">
                                        <?= htmlspecialchars($evt['name']) ?>
                                    </a>
                                </td>
                                <td><?= ($evt['start_date'] ?? null) ? date('d/m/Y', strtotime($evt['start_date'])) : '-' ?></td>
                                <td>
                                    <span class="badge badge-<?= ($evt['level'] ?? '') === 'platinum' ? 'warning' : 'secondary' ?>">
                                        <?= ucfirst($evt['level'] ?? 'bronze') ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
                               <?= ($sponsor['active'] ?? 1) ? 'checked' : '' ?>>
                        <label for="active">Sponsor Activo</label>
                    </div>
                </div>
            </div>

            <?php if ($isEdit): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Codigo de Acceso</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="text" class="form-control" value="<?= htmlspecialchars($sponsor['code'] ?? '') ?>" readonly
                               style="font-family: monospace; font-size: 0.85rem;">
                        <small class="form-help">URL de seleccion:</small>
                        <code style="font-size: 0.75rem; word-break: break-all;">
                            /seleccion-sponsor?code=<?= htmlspecialchars($sponsor['code'] ?? '') ?>
                        </code>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm btn-block" onclick="regenerateCode()">
                        <i class="fas fa-sync"></i> Regenerar Codigo
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3>Reuniones</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="max_simultaneous_meetings">Max. Reuniones Simultaneas</label>
                        <input type="number" id="max_simultaneous_meetings" name="max_simultaneous_meetings"
                               class="form-control" min="1" max="10"
                               value="<?= $sponsor['max_simultaneous_meetings'] ?? 1 ?>">
                        <small class="form-help">Cuantas reuniones puede tener a la vez</small>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar Cambios' : 'Crear Sponsor' ?>
            </button>
        </div>
    </div>
</form>

<?php if ($isEdit): ?>
<script>
function regenerateCode() {
    if (!confirm('Regenerar el codigo? Los enlaces anteriores dejaran de funcionar.')) return;

    const formData = new FormData();
    formData.append('_csrf_token', '<?= $csrf_token ?>');

    fetch('/admin/sponsors/<?= $sponsor['id'] ?>/regenerate-code', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error');
        }
    });
}
</script>
<?php endif; ?>
