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
                    <h3>Contactos</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addContact()">
                        <i class="fas fa-plus"></i> Anadir Contacto
                    </button>
                </div>
                <div class="card-body">
                    <div id="contacts-container">
                        <?php
                        $contactsList = $contacts ?? [];
                        if (empty($contactsList)) {
                            $contactsList = [['id' => '', 'name' => '', 'position' => '', 'email' => '', 'phone' => '', 'is_primary' => 1]];
                        }
                        ?>
                        <?php foreach ($contactsList as $idx => $contact): ?>
                        <div class="contact-item" data-index="<?= $idx ?>">
                            <div class="contact-header">
                                <span class="contact-number">Contacto <?= $idx + 1 ?></span>
                                <div class="contact-actions">
                                    <label class="primary-label">
                                        <input type="radio" name="primary_contact" value="<?= $idx ?>" <?= ($contact['is_primary'] ?? 0) ? 'checked' : '' ?>>
                                        Principal
                                    </label>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeContact(this)" title="Eliminar contacto">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

            <div class="card">
                <div class="card-header">
                    <h3>Redes Sociales</h3>
                </div>
                <div class="card-body">
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
                    <div class="form-check" style="margin-top: 0.75rem;">
                        <input type="checkbox" id="is_hidden" name="is_hidden" value="1"
                               <?= ($sponsor['is_hidden'] ?? 0) ? 'checked' : '' ?>>
                        <label for="is_hidden">Oculto en portal</label>
                        <small class="form-help" style="display: block; margin-top: 0.25rem; margin-left: 1.5rem;">
                            El sponsor puede acceder a su panel pero no aparece en listados publicos
                        </small>
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
                        <small class="form-help">URL de acceso directo:</small>
                        <code style="font-size: 0.75rem; word-break: break-all;">
                            /sponsor/login?code=<?= htmlspecialchars($sponsor['code'] ?? '') ?>
                        </code>
                        <button type="button" class="btn btn-outline btn-sm mt-2" onclick="copyToClipboard('/sponsor/login?code=<?= htmlspecialchars($sponsor['code'] ?? '') ?>')">
                            <i class="fas fa-copy"></i> Copiar enlace
                        </button>
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
}

function removeContact(button) {
    const contactItem = button.closest('.contact-item');
    const container = document.getElementById('contacts-container');

    if (container.querySelectorAll('.contact-item').length <= 1) {
        alert('Debe haber al menos un contacto');
        return;
    }

    if (confirm('Eliminar este contacto?')) {
        contactItem.remove();
        updateContactNumbers();
    }
}

function updateContactNumbers() {
    const contacts = document.querySelectorAll('.contact-item');
    contacts.forEach((contact, index) => {
        contact.querySelector('.contact-number').textContent = 'Contacto ' + (index + 1);
    });
}

<?php if ($isEdit): ?>
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
<?php endif; ?>

function copyToClipboard(path) {
    const url = window.location.origin + path;
    navigator.clipboard.writeText(url).then(() => {
        alert('Enlace copiado al portapapeles');
    }).catch(() => {
        prompt('Copia este enlace:', url);
    });
}
</script>
