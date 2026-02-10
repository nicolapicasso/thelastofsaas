<?php
/**
 * Admin - Edit Email Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div>
        <a href="/admin/emails" class="btn btn-outline btn-sm" style="margin-bottom: 0.5rem;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <h1><?= htmlspecialchars($template['display_name']) ?></h1>
        <p class="text-muted">
            Identificador: <code><?= htmlspecialchars($template['name']) ?></code>
        </p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/admin/emails/templates/<?= $template['id'] ?>/preview" target="_blank" class="btn btn-outline">
            <i class="fas fa-eye"></i> Vista Previa
        </a>
        <button type="button" class="btn btn-outline" onclick="resetToDefault()">
            <i class="fas fa-undo"></i> Restaurar
        </button>
    </div>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/admin/emails/templates/<?= $template['id'] ?>/update">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-edit"></i> Editar Plantilla</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label for="display_name">Nombre de la Plantilla *</label>
                    <input type="text" id="display_name" name="display_name" class="form-control"
                           value="<?= htmlspecialchars($template['display_name']) ?>" required>
                </div>
                <div class="form-group" style="flex: 0 0 150px;">
                    <label for="is_active">Estado</label>
                    <select id="is_active" name="is_active" class="form-control">
                        <option value="1" <?= $template['is_active'] ? 'selected' : '' ?>>Activa</option>
                        <option value="0" <?= !$template['is_active'] ? 'selected' : '' ?>>Inactiva</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="subject">Asunto del Email *</label>
                <input type="text" id="subject" name="subject" class="form-control"
                       value="<?= htmlspecialchars($template['subject']) ?>" required>
                <small class="text-muted">Puedes usar variables como <code>{{event_name}}</code></small>
            </div>

            <div class="form-group">
                <label for="body_html">Contenido HTML *</label>
                <textarea id="body_html" name="body_html" class="form-control" rows="20" style="font-family: monospace; font-size: 13px;" required><?= htmlspecialchars($template['body_html']) ?></textarea>
                <small class="text-muted">
                    Usa <code>{{variable}}</code> para texto y <code>{{{variable}}}</code> para URLs sin escapar.
                    <a href="#" onclick="document.getElementById('variables-help').style.display = document.getElementById('variables-help').style.display === 'none' ? 'block' : 'none'; return false;">Ver variables disponibles</a>
                </small>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <a href="/admin/emails" class="btn btn-outline">Cancelar</a>
        </div>
    </div>
</form>

<!-- Variables Help Panel -->
<div id="variables-help" class="card" style="margin-top: 1rem; display: none;">
    <div class="card-header">
        <h3><i class="fas fa-code"></i> Variables Disponibles para esta Plantilla</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($variables)): ?>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <?php foreach ($variables as $var): ?>
                    <code style="background: var(--color-gray-100); padding: 0.25rem 0.75rem; border-radius: 4px; cursor: pointer;"
                          onclick="insertVariable('<?= htmlspecialchars($var) ?>')" title="Click para insertar">
                        {{<?= htmlspecialchars($var) ?>}}
                    </code>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No hay variables definidas para esta plantilla.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function insertVariable(varName) {
    const textarea = document.getElementById('body_html');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const before = text.substring(0, start);
    const after = text.substring(end, text.length);
    textarea.value = before + '{{' + varName + '}}' + after;
    textarea.selectionStart = textarea.selectionEnd = start + varName.length + 4;
    textarea.focus();
}

function resetToDefault() {
    if (!confirm('¿Restaurar esta plantilla a su contenido por defecto? Se perderan los cambios actuales.')) {
        return;
    }

    fetch('/admin/emails/templates/<?= $template['id'] ?>/reset', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.error || 'Error al restaurar');
        }
    })
    .catch(e => {
        alert('Error de conexion');
    });
}
</script>

<style>
.form-row {
    display: flex;
    gap: 1rem;
}
@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
}
</style>
