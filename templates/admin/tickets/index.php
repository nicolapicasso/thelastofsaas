<?php
/**
 * Tickets List Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Entradas</h1>
        <p>Gestiona las entradas del evento</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/tickets/scanner?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-qrcode"></i> Scanner</a>
        <a href="/admin/tickets/types?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-tags"></i> Tipos</a>
        <a href="/admin/tickets/export?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-download"></i> Exportar</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<!-- Event Selector + Stats -->
<div class="card">
    <div class="card-body" style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
        <div class="filter-group">
            <label>Evento</label>
            <select onchange="location.href='?event_id='+this.value" class="form-control">
                <?php foreach ($events as $evt): ?>
                    <option value="<?= $evt['id'] ?>" <?= $currentEventId == $evt['id'] ? 'selected' : '' ?>><?= htmlspecialchars($evt['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Estado</label>
            <select onchange="location.href='?event_id=<?= $currentEventId ?>&status='+this.value" class="form-control">
                <option value="">Todos</option>
                <?php foreach ($statusOptions as $key => $label): ?>
                    <option value="<?= $key ?>" <?= ($currentStatus ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (!empty($stats)): ?>
        <div style="display: flex; gap: 1.5rem; margin-left: auto;">
            <div><strong><?= $stats['total'] ?? 0 ?></strong> <span class="text-muted">Total</span></div>
            <div><strong class="text-success"><?= $stats['confirmed'] ?? 0 ?></strong> <span class="text-muted">Confirmadas</span></div>
            <div><strong class="text-info"><?= $stats['checked_in'] ?? 0 ?></strong> <span class="text-muted">Check-in</span></div>
            <div><strong class="text-warning"><?= number_format($stats['total_revenue'] ?? 0, 2) ?>€</strong> <span class="text-muted">Ingresos</span></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bulk Actions Bar (hidden by default) -->
<div id="bulk-actions-bar" class="card bulk-actions-bar" style="display: none;">
    <div class="card-body" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <span id="selected-count">0 seleccionados</span>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('check-in')">
                <i class="fas fa-check"></i> Check-in
            </button>
            <button type="button" class="btn btn-sm btn-primary" onclick="bulkAction('approve')">
                <i class="fas fa-thumbs-up"></i> Aprobar
            </button>
            <div class="dropdown" style="display: inline-block;">
                <button type="button" class="btn btn-sm btn-outline dropdown-toggle" onclick="toggleDropdown(this)">
                    <i class="fas fa-exchange-alt"></i> Cambiar estado
                </button>
                <div class="dropdown-menu">
                    <a href="#" class="dropdown-item" onclick="bulkAction('status', 'confirmed'); return false;">Confirmado</a>
                    <a href="#" class="dropdown-item" onclick="bulkAction('status', 'pending'); return false;">Pendiente</a>
                    <a href="#" class="dropdown-item" onclick="bulkAction('status', 'cancelled'); return false;">Cancelado</a>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </div>
        <button type="button" class="btn btn-sm btn-outline" onclick="clearSelection()" style="margin-left: auto;">
            <i class="fas fa-times"></i> Cancelar
        </button>
    </div>
</div>

<!-- Tickets Table -->
<div class="card">
    <?php if (empty($tickets)): ?>
        <div class="empty-state">
            <i class="fas fa-ticket-alt"></i>
            <h3>No hay entradas</h3>
            <p>Las entradas aparecerán aquí cuando los participantes se registren</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)">
                        </th>
                        <th>Código</th>
                        <th>Asistente</th>
                        <th>Empresa</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th width="180">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr data-id="<?= $ticket['id'] ?>">
                            <td>
                                <input type="checkbox" class="ticket-checkbox" value="<?= $ticket['id'] ?>" onchange="updateSelection()">
                            </td>
                            <td><code><?= htmlspecialchars($ticket['code'] ?? $ticket['ticket_code'] ?? '') ?></code></td>
                            <td>
                                <strong><?= htmlspecialchars($ticket['attendee_name'] ?? (($ticket['attendee_first_name'] ?? '') . ' ' . ($ticket['attendee_last_name'] ?? ''))) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($ticket['attendee_email'] ?? '') ?></small>
                            </td>
                            <td><?= !empty($ticket['attendee_company'] ?? $ticket['attendee_company_name'] ?? '') ? htmlspecialchars($ticket['attendee_company'] ?? $ticket['attendee_company_name'] ?? '') : '-' ?></td>
                            <td>
                                <?= htmlspecialchars($ticket['ticket_type_name']) ?>
                                <?php if ($ticket['sponsor_name']): ?>
                                    <br><small class="text-muted">por <?= htmlspecialchars($ticket['sponsor_name']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($ticket['status']) {
                                    'confirmed' => 'success', 'checked_in', 'used' => 'info', 'pending' => 'warning', 'cancelled' => 'danger', default => 'secondary'
                                };
                                ?>
                                <span class="badge badge-<?= $statusClass ?>"><?= $statusOptions[$ticket['status']] ?? $ticket['status'] ?></span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/tickets/<?= $ticket['id'] ?>" class="btn btn-sm btn-outline" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($ticket['status'] === 'confirmed'): ?>
                                        <button type="button" class="btn btn-sm btn-outline btn-success" onclick="singleAction(<?= $ticket['id'] ?>, 'check-in')" title="Check-in">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($ticket['status'] === 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-outline btn-primary" onclick="singleAction(<?= $ticket['id'] ?>, 'approve')" title="Aprobar">
                                            <i class="fas fa-thumbs-up"></i>
                                        </button>
                                    <?php endif; ?>
                                    <div class="dropdown" style="display: inline-block;">
                                        <button type="button" class="btn btn-sm btn-outline dropdown-toggle" onclick="toggleDropdown(this)" title="Más acciones">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item" onclick="changeStatus(<?= $ticket['id'] ?>); return false;">
                                                <i class="fas fa-exchange-alt"></i> Cambiar estado
                                            </a>
                                            <a href="#" class="dropdown-item text-danger" onclick="singleAction(<?= $ticket['id'] ?>, 'delete'); return false;">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Change Status Modal -->
<div id="status-modal" class="modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeStatusModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Cambiar estado</h3>
            <button type="button" class="modal-close" onclick="closeStatusModal()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="status-ticket-id">
            <div class="form-group">
                <label>Nuevo estado</label>
                <select id="new-status" class="form-control">
                    <?php foreach ($statusOptions as $key => $label): ?>
                        <option value="<?= $key ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeStatusModal()">Cancelar</button>
            <button type="button" class="btn btn-primary" onclick="submitStatusChange()">Guardar</button>
        </div>
    </div>
</div>

<style>
.bulk-actions-bar {
    background: var(--color-primary-light);
    border: 2px solid var(--color-primary);
    position: sticky;
    top: 70px;
    z-index: 100;
    overflow: visible !important;
}
.bulk-actions-bar .card-body {
    padding: 0.75rem 1rem;
    overflow: visible !important;
}
#selected-count {
    font-weight: 600;
    min-width: 120px;
}
.dropdown {
    position: relative;
}
.dropdown-toggle::after {
    content: '';
    margin-left: 0.3em;
}
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
    min-width: 160px;
    z-index: 1001;
}
.bulk-actions-bar .dropdown-menu {
    top: calc(100% + 5px);
    z-index: 1002;
}
.dropdown-menu-right {
    left: auto;
    right: 0;
}
.dropdown.open .dropdown-menu {
    display: block;
}
.dropdown-item {
    display: block;
    padding: 0.5rem 1rem;
    color: var(--color-gray-700);
    white-space: nowrap;
}
.dropdown-item:hover {
    background: var(--color-gray-100);
}
.dropdown-item.text-danger {
    color: var(--color-danger);
}

/* Modal */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}
.modal-content {
    position: relative;
    background: white;
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 400px;
    box-shadow: var(--shadow-lg);
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--color-gray-200);
}
.modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
}
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--color-gray-500);
}
.modal-body {
    padding: 1.5rem;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--color-gray-200);
}

/* Toast notification */
.toast-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: var(--color-success);
    color: white;
    padding: 12px 20px;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    z-index: 3000;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s ease;
}
.toast-notification.show {
    opacity: 1;
    transform: translateY(0);
}
.toast-notification i {
    margin-right: 8px;
}
</style>

<script>
const csrfToken = '<?= $csrf_token ?? '' ?>';
let selectedIds = [];

// Selection functions
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.ticket-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.ticket-checkbox:checked');
    selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));

    const bulkBar = document.getElementById('bulk-actions-bar');
    const countSpan = document.getElementById('selected-count');

    if (selectedIds.length > 0) {
        bulkBar.style.display = 'block';
        countSpan.textContent = selectedIds.length + ' seleccionado' + (selectedIds.length > 1 ? 's' : '');
    } else {
        bulkBar.style.display = 'none';
    }

    // Update select-all checkbox state
    const allCheckboxes = document.querySelectorAll('.ticket-checkbox');
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
        selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
    }
}

function clearSelection() {
    document.querySelectorAll('.ticket-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    selectedIds = [];
    document.getElementById('bulk-actions-bar').style.display = 'none';
}

// Dropdown functions
function toggleDropdown(btn) {
    const dropdown = btn.closest('.dropdown');
    const wasOpen = dropdown.classList.contains('open');

    // Close all dropdowns
    document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));

    if (!wasOpen) {
        dropdown.classList.add('open');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
    }
});

// Single ticket actions
function singleAction(id, action) {
    if (action === 'delete') {
        if (!confirm('¿Estás seguro de que quieres eliminar este ticket? Esta acción no se puede deshacer.')) return;
    } else if (action === 'check-in') {
        if (!confirm('¿Confirmar check-in?')) return;
    } else if (action === 'approve') {
        if (!confirm('¿Aprobar este ticket?')) return;
    }

    performAction([id], action);
}

// Bulk actions
function bulkAction(action, value = null) {
    if (selectedIds.length === 0) {
        alert('Selecciona al menos un ticket');
        return;
    }

    if (action === 'delete') {
        if (!confirm(`¿Estás seguro de que quieres eliminar ${selectedIds.length} ticket(s)? Esta acción no se puede deshacer.`)) return;
    } else if (action === 'check-in') {
        if (!confirm(`¿Confirmar check-in de ${selectedIds.length} ticket(s)?`)) return;
    } else if (action === 'approve') {
        if (!confirm(`¿Aprobar ${selectedIds.length} ticket(s)?`)) return;
    } else if (action === 'status') {
        if (!confirm(`¿Cambiar estado de ${selectedIds.length} ticket(s) a "${value}"?`)) return;
    }

    performAction(selectedIds, action, value);
}

// Status options for UI update
const statusLabels = <?= json_encode($statusOptions) ?>;
const statusClasses = {
    'confirmed': 'success',
    'checked_in': 'info',
    'used': 'info',
    'pending': 'warning',
    'cancelled': 'danger'
};

// Perform action (single or bulk)
function performAction(ids, action, value = null) {
    const body = {
        ids: ids,
        action: action
    };
    if (value) body.value = value;

    fetch('/admin/tickets/bulk-action', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update UI without reload
            if (action === 'delete') {
                // Remove deleted rows
                ids.forEach(id => {
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) row.remove();
                });
            } else if (action === 'status' || action === 'check-in' || action === 'approve') {
                // Determine new status
                let newStatus = value;
                if (action === 'check-in') newStatus = 'used';
                if (action === 'approve') newStatus = 'confirmed';

                // Update status badge in each row
                ids.forEach(id => {
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        const badgeCell = row.querySelector('td:nth-child(6)');
                        if (badgeCell) {
                            const statusClass = statusClasses[newStatus] || 'secondary';
                            const statusLabel = statusLabels[newStatus] || newStatus;
                            badgeCell.innerHTML = `<span class="badge badge-${statusClass}">${statusLabel}</span>`;
                        }

                        // Update action buttons
                        const actionsCell = row.querySelector('td:nth-child(7) .btn-group');
                        if (actionsCell) {
                            // Remove old conditional buttons (check-in and approve)
                            actionsCell.querySelectorAll('button[title="Check-in"], button[title="Aprobar"]').forEach(btn => btn.remove());

                            // Add new buttons based on status
                            const viewBtn = actionsCell.querySelector('a[title="Ver detalles"]');
                            if (newStatus === 'confirmed') {
                                const checkInBtn = document.createElement('button');
                                checkInBtn.type = 'button';
                                checkInBtn.className = 'btn btn-sm btn-outline btn-success';
                                checkInBtn.onclick = () => singleAction(id, 'check-in');
                                checkInBtn.title = 'Check-in';
                                checkInBtn.innerHTML = '<i class="fas fa-check"></i>';
                                viewBtn.after(checkInBtn);
                            } else if (newStatus === 'pending') {
                                const approveBtn = document.createElement('button');
                                approveBtn.type = 'button';
                                approveBtn.className = 'btn btn-sm btn-outline btn-primary';
                                approveBtn.onclick = () => singleAction(id, 'approve');
                                approveBtn.title = 'Aprobar';
                                approveBtn.innerHTML = '<i class="fas fa-thumbs-up"></i>';
                                viewBtn.after(approveBtn);
                            }
                        }
                    }
                });
            }

            // Clear selection
            clearSelection();

            // Update pending badge in sidebar
            if (typeof data.pendingCount !== 'undefined') {
                const badge = document.querySelector('.sidebar-nav a[href="/admin/tickets"] .nav-badge');
                if (data.pendingCount > 0) {
                    if (badge) {
                        badge.textContent = data.pendingCount;
                    } else {
                        // Create badge if it doesn't exist
                        const navLink = document.querySelector('.sidebar-nav a[href="/admin/tickets"]');
                        if (navLink) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'nav-badge';
                            newBadge.textContent = data.pendingCount;
                            navLink.appendChild(newBadge);
                        }
                    }
                } else {
                    // Remove badge if count is 0
                    if (badge) badge.remove();
                }
            }

            // Show success message
            showToast(data.message || 'Acción realizada correctamente');
        } else {
            alert(data.error || 'Error al realizar la acción');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error de conexión');
    });
}

// Toast notification
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Change status modal
function changeStatus(id) {
    document.getElementById('status-ticket-id').value = id;
    document.getElementById('status-modal').style.display = 'flex';
}

function closeStatusModal() {
    document.getElementById('status-modal').style.display = 'none';
}

function submitStatusChange() {
    const id = document.getElementById('status-ticket-id').value;
    const status = document.getElementById('new-status').value;

    performAction([parseInt(id)], 'status', status);
    closeStatusModal();
}

// Close modal on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStatusModal();
    }
});
</script>
