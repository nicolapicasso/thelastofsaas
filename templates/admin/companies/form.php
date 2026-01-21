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

<form method="POST" action="<?= $isEdit ? '/admin/companies/' . $company['id'] : '/admin/companies' ?>" enctype="multipart/form-data">
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
                <div class="card-header">
                    <h3>Contactos</h3>
                    <button type="button" class="btn btn-sm btn-outline" onclick="addContact()">
                        <i class="fas fa-plus"></i> Anadir Contacto
                    </button>
                </div>
                <div class="card-body">
                    <div id="contacts-container">
                        <?php
                        $contacts = $contacts ?? [];
                        if (empty($contacts)) {
                            // Show one empty contact form for new companies
                            $contacts = [['id' => '', 'name' => '', 'position' => '', 'email' => '', 'phone' => '', 'is_primary' => 1]];
                        }
                        foreach ($contacts as $idx => $contact):
                        ?>
                        <div class="contact-item" data-index="<?= $idx ?>">
                            <div class="contact-header">
                                <span class="contact-number">Contacto <?= $idx + 1 ?></span>
                                <div class="contact-actions">
                                    <label class="primary-label">
                                        <input type="radio" name="primary_contact" value="<?= $idx ?>" <?= ($contact['is_primary'] ?? 0) ? 'checked' : '' ?>>
                                        Principal
                                    </label>
                                    <?php if ($idx > 0 || count($contacts) > 1): ?>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeContact(this)" title="Eliminar contacto">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <input type="hidden" name="contacts[<?= $idx ?>][id]" value="<?= $contact['id'] ?? '' ?>">
                            <div class="form-row">
                                <div class="form-group" style="flex: 2;">
                                    <label>Nombre</label>
                                    <input type="text" name="contacts[<?= $idx ?>][name]" class="form-control" value="<?= htmlspecialchars($contact['name'] ?? '') ?>" placeholder="Nombre completo">
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Cargo</label>
                                    <input type="text" name="contacts[<?= $idx ?>][position]" class="form-control" value="<?= htmlspecialchars($contact['position'] ?? '') ?>" placeholder="Ej: CEO, CTO...">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="contacts[<?= $idx ?>][email]" class="form-control" value="<?= htmlspecialchars($contact['email'] ?? '') ?>" placeholder="email@empresa.com">
                                </div>
                                <div class="form-group">
                                    <label>Telefono</label>
                                    <input type="text" name="contacts[<?= $idx ?>][phone]" class="form-control" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>" placeholder="+34...">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <template id="contact-template">
                <div class="contact-item" data-index="__INDEX__">
                    <div class="contact-header">
                        <span class="contact-number">Contacto __NUM__</span>
                        <div class="contact-actions">
                            <label class="primary-label">
                                <input type="radio" name="primary_contact" value="__INDEX__">
                                Principal
                            </label>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeContact(this)" title="Eliminar contacto">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="contacts[__INDEX__][id]" value="">
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <label>Nombre</label>
                            <input type="text" name="contacts[__INDEX__][name]" class="form-control" placeholder="Nombre completo">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Cargo</label>
                            <input type="text" name="contacts[__INDEX__][position]" class="form-control" placeholder="Ej: CEO, CTO...">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="contacts[__INDEX__][email]" class="form-control" placeholder="email@empresa.com">
                        </div>
                        <div class="form-group">
                            <label>Telefono</label>
                            <input type="text" name="contacts[__INDEX__][phone]" class="form-control" placeholder="+34...">
                        </div>
                    </div>
                </div>
            </template>

            <style>
                .card-header { display: flex; justify-content: space-between; align-items: center; }
                .contact-item { background: #f8f9fa; border: 1px solid #dee2e6; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
                .contact-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #dee2e6; }
                .contact-number { font-weight: 600; font-size: 0.9rem; }
                .contact-actions { display: flex; gap: 1rem; align-items: center; }
                .primary-label { display: flex; align-items: center; gap: 0.25rem; font-size: 0.85rem; cursor: pointer; }
                .primary-label input { margin: 0; }
            </style>

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
                    <small class="form-help">URL de acceso directo:</small>
                    <code style="font-size: 0.75rem; word-break: break-all;">/empresa/login?code=<?= htmlspecialchars($company['code'] ?? '') ?></code>
                    <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem;">
                        <button type="button" class="btn btn-outline btn-sm" onclick="copyToClipboard('/empresa/login?code=<?= htmlspecialchars($company['code'] ?? '') ?>')"><i class="fas fa-copy"></i> Copiar enlace</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="regenerateCode()"><i class="fas fa-sync"></i> Regenerar</button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="logo_file">Logo</label>
                <?php if (!empty($company['logo_url'])): ?>
                    <div class="logo-preview" style="margin-bottom: 10px;">
                        <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="Logo actual"
                             style="max-width: 120px; max-height: 60px; border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                    </div>
                <?php endif; ?>
                <input type="file" id="logo_file" name="logo_file" class="form-control"
                       accept="image/png,image/jpeg,image/gif,image/svg+xml,image/webp">
                <small class="form-help">PNG, JPG, GIF, SVG, WebP. Max: 2MB</small>
                <input type="hidden" name="logo_url" value="<?= htmlspecialchars($company['logo_url'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> <?= $isEdit ? 'Guardar' : 'Crear' ?></button>
        </div>
    </div>
</form>

<script>
let contactIndex = <?= count($contacts ?? []) ?>;

function addContact() {
    const container = document.getElementById('contacts-container');
    const template = document.getElementById('contact-template');
    const html = template.innerHTML
        .replace(/__INDEX__/g, contactIndex)
        .replace(/__NUM__/g, contactIndex + 1);

    const div = document.createElement('div');
    div.innerHTML = html;
    container.appendChild(div.firstElementChild);
    contactIndex++;
    updateContactNumbers();
}

function removeContact(btn) {
    const item = btn.closest('.contact-item');
    if (document.querySelectorAll('.contact-item').length > 1) {
        item.remove();
        updateContactNumbers();
    } else {
        alert('Debe haber al menos un contacto.');
    }
}

function updateContactNumbers() {
    document.querySelectorAll('.contact-item').forEach((item, idx) => {
        item.querySelector('.contact-number').textContent = 'Contacto ' + (idx + 1);
    });
}

<?php if ($isEdit): ?>
function regenerateCode() {
    if (!confirm('¿Regenerar el codigo?')) return;
    fetch('/admin/companies/<?= $company['id'] ?>/regenerate-code', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => d.success ? location.reload() : alert(d.error));
}

function copyToClipboard(path) {
    const url = window.location.origin + path;
    navigator.clipboard.writeText(url).then(() => {
        alert('Enlace copiado al portapapeles');
    }).catch(() => {
        prompt('Copia este enlace:', url);
    });
}
<?php endif; ?>
</script>
