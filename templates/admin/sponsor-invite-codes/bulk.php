<?php
/**
 * Bulk Create Sponsor Invite Codes Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Crear Codigos en Lote</h1>
        <p>Genera multiples codigos de invitacion para un sponsor</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/sponsor-invite-codes" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<form method="POST" action="/admin/sponsor-invite-codes/bulk">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="form-grid">
        <div class="form-main">
            <div class="card">
                <div class="card-header">
                    <h3>Configuracion del Lote</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="event_id">Evento *</label>
                            <select id="event_id" name="event_id" class="form-control" required>
                                <option value="">Seleccionar evento</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= $event['id'] ?>">
                                        <?= htmlspecialchars($event['name']) ?>
                                        (<?= date('d/m/Y', strtotime($event['start_date'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sponsor_id">Sponsor *</label>
                            <select id="sponsor_id" name="sponsor_id" class="form-control" required>
                                <option value="">Seleccionar sponsor</option>
                                <?php foreach ($sponsors as $sponsor): ?>
                                    <option value="<?= $sponsor['id'] ?>">
                                        <?= htmlspecialchars($sponsor['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="quantity">Cantidad de Codigos *</label>
                            <input type="number" id="quantity" name="quantity" class="form-control"
                                   value="10" min="1" max="100" required>
                            <small class="form-help">Maximo 100 codigos por lote</small>
                        </div>
                        <div class="form-group">
                            <label for="prefix">Prefijo</label>
                            <input type="text" id="prefix" name="prefix" class="form-control"
                                   placeholder="Ej: VIP-" maxlength="10" style="text-transform: uppercase;">
                            <small class="form-help">Opcional. Por defecto usa las 3 primeras letras del sponsor</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="max_uses">Maximo de Usos por Codigo</label>
                        <input type="number" id="max_uses" name="max_uses" class="form-control"
                               min="1" placeholder="Ilimitado">
                        <small class="form-help">Dejar vacio para uso ilimitado. Tipicamente 1 para codigos personales.</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Vista Previa</h3>
                </div>
                <div class="card-body">
                    <p>Se crearan <strong id="preview_quantity">10</strong> codigos con el formato:</p>
                    <code id="preview_format" style="font-size: 1.1rem;">ABC-XXXXXXXX</code>
                    <p class="text-muted mt-2">Donde X son caracteres alfanumericos aleatorios</p>
                </div>
            </div>
        </div>

        <div class="form-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Informacion</h3>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-info-circle text-info"></i> Los codigos se crean sin descuento por defecto.</p>
                    <p><i class="fas fa-info-circle text-info"></i> Puedes editar cada codigo despues para anadir descuentos o restricciones.</p>
                    <p><i class="fas fa-info-circle text-info"></i> Todos los codigos se crearan activos.</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-layer-group"></i> Crear Codigos
            </button>
        </div>
    </div>
</form>

<script>
document.getElementById('quantity').addEventListener('input', updatePreview);
document.getElementById('prefix').addEventListener('input', updatePreview);
document.getElementById('sponsor_id').addEventListener('change', updatePreview);

function updatePreview() {
    const quantity = document.getElementById('quantity').value || 10;
    const prefix = document.getElementById('prefix').value.toUpperCase();
    const sponsorSelect = document.getElementById('sponsor_id');
    const sponsorName = sponsorSelect.options[sponsorSelect.selectedIndex]?.text || 'ABC';

    document.getElementById('preview_quantity').textContent = quantity;

    let formatPrefix = prefix;
    if (!formatPrefix && sponsorName) {
        formatPrefix = sponsorName.substring(0, 3).toUpperCase() + '-';
    }

    document.getElementById('preview_format').textContent = formatPrefix + 'XXXXXXXX';
}

updatePreview();
</script>
