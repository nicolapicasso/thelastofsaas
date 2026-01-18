<?php
/**
 * Ticket Types Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Tipos de Ticket</h1>
        <p>Gestiona los tipos de entrada disponibles</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/tickets" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Tickets</a>
        <button type="button" class="btn btn-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Nuevo Tipo</button>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<!-- Event Filter -->
<div class="card">
    <div class="card-body">
        <div class="filter-group">
            <label>Evento</label>
            <select onchange="location.href='?event_id='+this.value" class="form-control" style="max-width: 300px;">
                <?php foreach ($events as $evt): ?>
                    <option value="<?= $evt['id'] ?>" <?= $currentEventId == $evt['id'] ? 'selected' : '' ?>><?= htmlspecialchars($evt['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- Ticket Types List -->
<div class="card">
    <?php if (empty($ticketTypes)): ?>
        <div class="empty-state">
            <i class="fas fa-tags"></i>
            <h3>No hay tipos de ticket</h3>
            <p>Crea tipos de ticket para este evento</p>
            <button type="button" class="btn btn-primary" onclick="openAddModal()">Crear tipo de ticket</button>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th class="text-center">Capacidad</th>
                        <th class="text-center">Vendidos</th>
                        <th>Disponibilidad</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ticketTypes as $type): ?>
                        <?php
                        $soldPercentage = $type['max_tickets'] > 0 ? ($type['tickets_sold'] / $type['max_tickets']) * 100 : 0;
                        $remaining = $type['max_tickets'] - $type['tickets_sold'];
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($type['name']) ?></strong>
                                <?php if ($type['description']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars(mb_substr($type['description'], 0, 60)) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($type['price'] > 0): ?>
                                    <strong><?= number_format($type['price'], 2) ?> €</strong>
                                <?php else: ?>
                                    <span class="badge badge-success">Gratis</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= $type['max_tickets'] ?></td>
                            <td class="text-center"><strong><?= $type['tickets_sold'] ?></strong></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="flex: 1; max-width: 100px; background: var(--bg-secondary); border-radius: 4px; height: 8px; overflow: hidden;">
                                        <div style="background: <?= $soldPercentage >= 90 ? 'var(--danger-color)' : ($soldPercentage >= 70 ? 'var(--warning-color)' : 'var(--success-color)') ?>; height: 100%; width: <?= $soldPercentage ?>%;"></div>
                                    </div>
                                    <small class="text-muted"><?= $remaining ?> disp.</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-<?= $type['active'] ? 'success' : 'secondary' ?>">
                                    <?= $type['active'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline" onclick="editType(<?= htmlspecialchars(json_encode($type)) ?>)" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline btn-danger" onclick="deleteType(<?= $type['id'] ?>)" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add/Edit Modal -->
<div id="typeModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 id="modalTitle">Nuevo Tipo de Ticket</h3>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="typeForm" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="event_id" value="<?= $currentEventId ?>">
            <input type="hidden" name="type_id" id="typeId" value="">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label required">Nombre</label>
                    <input type="text" name="name" id="typeName" class="form-control" required placeholder="Ej: General, VIP, Early Bird...">
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" id="typeDescription" class="form-control" rows="2" placeholder="Descripción del tipo de entrada"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label required">Precio (€)</label>
                        <input type="number" name="price" id="typePrice" class="form-control" step="0.01" min="0" value="0" required>
                        <small class="text-muted">0 para entradas gratuitas</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Capacidad</label>
                        <input type="number" name="max_tickets" id="typeCapacity" class="form-control" min="1" value="100" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Venta desde</label>
                        <input type="datetime-local" name="sale_start" id="typeSaleStart" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Venta hasta</label>
                        <input type="datetime-local" name="sale_end" id="typeSaleEnd" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="active" id="typeActive" value="1" checked>
                        <span>Activo</span>
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="requires_approval" id="typeApproval" value="1">
                        <span>Requiere aprobación</span>
                    </label>
                    <small class="text-muted">Los tickets de este tipo necesitarán aprobación manual</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-content {
    background: var(--bg-primary, #ffffff);
    border-radius: 8px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
}
.modal-header h3 { margin: 0; }
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary);
}
.modal-body { padding: 1.5rem; }
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border-color);
}
.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}
</style>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Nuevo Tipo de Ticket';
    document.getElementById('typeForm').action = '/admin/tickets/types';
    document.getElementById('typeId').value = '';
    document.getElementById('typeName').value = '';
    document.getElementById('typeDescription').value = '';
    document.getElementById('typePrice').value = '0';
    document.getElementById('typeCapacity').value = '100';
    document.getElementById('typeSaleStart').value = '';
    document.getElementById('typeSaleEnd').value = '';
    document.getElementById('typeActive').checked = true;
    document.getElementById('typeApproval').checked = false;
    document.getElementById('typeModal').style.display = 'flex';
}

function editType(type) {
    document.getElementById('modalTitle').textContent = 'Editar Tipo de Ticket';
    document.getElementById('typeForm').action = '/admin/tickets/types/' + type.id;
    document.getElementById('typeId').value = type.id;
    document.getElementById('typeName').value = type.name;
    document.getElementById('typeDescription').value = type.description || '';
    document.getElementById('typePrice').value = type.price;
    document.getElementById('typeCapacity').value = type.max_tickets;
    document.getElementById('typeSaleStart').value = type.sale_start ? type.sale_start.slice(0, 16) : '';
    document.getElementById('typeSaleEnd').value = type.sale_end ? type.sale_end.slice(0, 16) : '';
    document.getElementById('typeActive').checked = type.active == 1;
    document.getElementById('typeApproval').checked = type.requires_approval == 1;
    document.getElementById('typeModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('typeModal').style.display = 'none';
}

function deleteType(id) {
    if (!confirm('¿Eliminar este tipo de ticket?')) return;
    fetch('/admin/tickets/types/' + id + '/delete', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => d.success ? location.reload() : alert(d.error));
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
document.getElementById('typeModal').addEventListener('click', e => { if (e.target.id === 'typeModal') closeModal(); });
</script>
