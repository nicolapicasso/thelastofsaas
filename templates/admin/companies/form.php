<?php
/**
 * Company Form Template
 * TLOS - The Last of SaaS
 */
$isEdit = isset($company) && $company;
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= $isEdit ? 'Editar Empresa' : 'Nueva Empresa' ?></h1>
        <p><?= $isEdit ? htmlspecialchars($company['name']) : 'Crear una nueva empresa' ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/companies" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? '/admin/companies/' . $company['id'] : '/admin/companies' ?>">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="form-grid">
        <div class="form-main">
            <div class="card">
                <div class="card-header"><h3>Informacion Basica</h3></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <label for="name">Nombre *</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($company['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="sector">Sector</label>
                            <input type="text" id="sector" name="sector" class="form-control" value="<?= htmlspecialchars($company['sector'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripcion</label>
                        <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($company['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="website">Web</label>
                            <input type="url" id="website" name="website" class="form-control" value="<?= htmlspecialchars($company['website'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="employees">Tamano</label>
                            <select id="employees" name="employees" class="form-control">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($sizeOptions as $val => $lbl): ?>
                                    <option value="<?= $val ?>" <?= ($company['employees'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Contacto</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="contact_name">Nombre de Contacto</label>
                        <input type="text" id="contact_name" name="contact_name" class="form-control" value="<?= htmlspecialchars($company['contact_name'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_email">Email de Contacto</label>
                            <input type="text" id="contact_email" name="contact_email" class="form-control" value="<?= htmlspecialchars($company['contact_email'] ?? '') ?>" placeholder="email@empresa.com">
                        </div>
                        <div class="form-group">
                            <label for="contact_phone">Telefono</label>
                            <input type="text" id="contact_phone" name="contact_phone" class="form-control" value="<?= htmlspecialchars($company['contact_phone'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($sponsors)): ?>
            <div class="card">
                <div class="card-header"><h3>SaaS que utiliza</h3></div>
                <div class="card-body">
                    <div class="checkbox-grid">
                        <?php foreach ($sponsors as $s): ?>
                            <div class="form-check">
                                <input type="checkbox" id="saas_<?= $s['id'] ?>" name="saas_usage[]" value="<?= $s['id'] ?>"
                                       <?= in_array($s['id'], $saasUsageIds ?? []) ? 'checked' : '' ?>>
                                <label for="saas_<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="notes">Notas Internas</label>
                <textarea id="notes" name="notes" class="form-control" rows="2"><?= htmlspecialchars($company['notes'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-sidebar">
            <div class="card">
                <div class="card-header"><h3>Estado</h3></div>
                <div class="card-body">
                    <div class="form-check">
                        <input type="checkbox" id="active" name="active" value="1" <?= ($company['active'] ?? 1) ? 'checked' : '' ?>>
                        <label for="active">Empresa Activa</label>
                    </div>
                </div>
            </div>

            <?php if ($isEdit): ?>
            <div class="card">
                <div class="card-header"><h3>Codigo de Acceso</h3></div>
                <div class="card-body">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($company['code'] ?? '') ?>" readonly style="font-family: monospace; font-size: 0.85rem;">
                    <small class="form-help">URL de seleccion:</small>
                    <code style="font-size: 0.75rem; word-break: break-all;">/seleccion-empresa?code=<?= htmlspecialchars($company['code'] ?? '') ?></code>
                    <button type="button" class="btn btn-outline btn-sm btn-block" style="margin-top: 0.5rem;" onclick="regenerateCode()"><i class="fas fa-sync"></i> Regenerar</button>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="logo_url">URL del Logo</label>
                <div class="input-with-button">
                    <input type="text" id="logo_url" name="logo_url" class="form-control" value="<?= htmlspecialchars($company['logo_url'] ?? '') ?>">
                    <button type="button" class="btn btn-outline" onclick="openMediaPicker('logo_url')"><i class="fas fa-image"></i></button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> <?= $isEdit ? 'Guardar' : 'Crear' ?></button>
        </div>
    </div>
</form>

<?php if ($isEdit): ?>
<script>
function regenerateCode() {
    if (!confirm('¿Regenerar el codigo?')) return;
    fetch('/admin/companies/<?= $company['id'] ?>/regenerate-code', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => d.success ? location.reload() : alert(d.error));
}
</script>
<?php endif; ?>
