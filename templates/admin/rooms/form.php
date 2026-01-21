<?php
/**
 * Room Form Template
 * TLOS - The Last of SaaS
 */
$isEdit = isset($room) && $room;
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= $isEdit ? 'Editar Sala' : 'Nueva Sala' ?></h1>
        <p><?= $isEdit ? htmlspecialchars($room['name']) : 'Crear una nueva sala' ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/rooms" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $isEdit ? '/admin/rooms/' . $room['id'] : '/admin/rooms' ?>">
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
                               value="<?= htmlspecialchars($room['name'] ?? '') ?>" required
                               placeholder="Ej: Sala Principal, Sala A, Auditorio...">
                    </div>

                    <div class="form-group">
                        <label for="description">Descripcion</label>
                        <textarea id="description" name="description" class="form-control" rows="3"
                                  placeholder="Descripcion de la sala, equipamiento destacado..."><?= htmlspecialchars($room['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="capacity">Capacidad</label>
                            <input type="number" id="capacity" name="capacity" class="form-control"
                                   value="<?= htmlspecialchars($room['capacity'] ?? '') ?>"
                                   min="0" placeholder="Numero de personas">
                        </div>
                        <div class="form-group">
                            <label for="color">Color</label>
                            <select id="color" name="color" class="form-control">
                                <?php foreach ($colorOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($room['color'] ?? '#3B82F6') === $value ? 'selected' : '' ?>
                                            style="background-color: <?= $value ?>; color: white;">
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Ubicacion</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="location">Ubicacion / Direccion</label>
                            <input type="text" id="location" name="location" class="form-control"
                                   value="<?= htmlspecialchars($room['location'] ?? '') ?>"
                                   placeholder="Ej: Edificio principal, Ala norte...">
                        </div>
                        <div class="form-group">
                            <label for="floor">Planta / Piso</label>
                            <input type="text" id="floor" name="floor" class="form-control"
                                   value="<?= htmlspecialchars($room['floor'] ?? '') ?>"
                                   placeholder="Ej: Planta baja, 1er piso...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Equipamiento e Imagen</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="equipment">Equipamiento Disponible</label>
                        <textarea id="equipment" name="equipment" class="form-control" rows="3"
                                  placeholder="Proyector, pizarra, sistema de audio, videoconferencia..."><?= htmlspecialchars($room['equipment'] ?? '') ?></textarea>
                        <small class="form-help">Lista el equipamiento disponible en esta sala</small>
                    </div>

                    <div class="form-group">
                        <label for="image_url">Imagen de la Sala</label>
                        <?php if (!empty($room['image_url'])): ?>
                            <div class="image-preview" style="margin-bottom: 10px;">
                                <img src="<?= htmlspecialchars($room['image_url']) ?>" alt="Imagen actual" id="image-preview-img"
                                     style="max-width: 100%; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        <?php else: ?>
                            <div class="image-preview" id="image-preview-container" style="margin-bottom: 10px; display: none;">
                                <img src="" alt="Vista previa" id="image-preview-img"
                                     style="max-width: 100%; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        <?php endif; ?>
                        <div class="input-group">
                            <input type="text" id="image_url" name="image_url" class="form-control"
                                   value="<?= htmlspecialchars($room['image_url'] ?? '') ?>"
                                   placeholder="https://... o usa el selector de medios">
                            <button type="button" class="btn btn-outline" onclick="openMediaPicker('image_url')">
                                <i class="fas fa-images"></i> Seleccionar
                            </button>
                        </div>
                        <small class="form-help">Imagen de la sala para mostrar en la pagina del evento</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Evento</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="event_id">Asociar a Evento</label>
                        <select id="event_id" name="event_id" class="form-control">
                            <option value="">-- Sin evento asignado --</option>
                            <?php if (!empty($events)): ?>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= $event['id'] ?>" <?= ($room['event_id'] ?? '') == $event['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($event['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-help">Asigna esta sala a un evento para mostrarla en la pagina del evento</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Opciones</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="active">Estado</label>
                        <select id="active" name="active" class="form-control">
                            <option value="1" <?= ($room['active'] ?? 1) == 1 ? 'selected' : '' ?>>Activa</option>
                            <option value="0" <?= ($room['active'] ?? 1) == 0 ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-control"
                               value="<?= htmlspecialchars($room['sort_order'] ?? '0') ?>" min="0">
                        <small class="form-help">Menor numero = primero en la lista</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar Sala' : 'Crear Sala' ?>
                    </button>
                </div>
            </div>

            <?php if ($isEdit): ?>
                <div class="card">
                    <div class="card-header">
                        <h3>Vista Previa Color</h3>
                    </div>
                    <div class="card-body">
                        <div id="color-preview" class="color-preview-box" style="background-color: <?= htmlspecialchars($room['color'] ?? '#3B82F6') ?>">
                            <span><?= htmlspecialchars($room['name']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<style>
.color-preview-box {
    padding: 15px;
    border-radius: 8px;
    color: white;
    text-align: center;
    font-weight: 600;
}
</style>

<script>
document.getElementById('color').addEventListener('change', function() {
    const preview = document.getElementById('color-preview');
    if (preview) {
        preview.style.backgroundColor = this.value;
    }
});

// Image URL preview
document.getElementById('image_url').addEventListener('change', function() {
    const url = this.value;
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview-img');

    if (url && previewImg) {
        previewImg.src = url;
        if (previewContainer) {
            previewContainer.style.display = 'block';
        }
    }
});

// Media picker integration
function openMediaPicker(targetField) {
    const picker = window.open('/admin/media/picker?target=' + targetField, 'mediaPicker',
        'width=1000,height=700,scrollbars=yes,resizable=yes');
}

window.selectMedia = function(url, targetField) {
    const input = document.getElementById(targetField);
    if (input) {
        input.value = url;
        input.dispatchEvent(new Event('change'));
    }
};
</script>
