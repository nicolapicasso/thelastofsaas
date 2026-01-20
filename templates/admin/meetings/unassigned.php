<?php
/**
 * Unassigned Matches Template
 * TLOS - The Last of SaaS
 */

// Get current event slug for public links
$currentEventSlug = '';
foreach ($events as $evt) {
    if ($evt['id'] == $currentEventId) {
        $currentEventSlug = $evt['slug'] ?? '';
        break;
    }
}
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Matches Sin Asignar</h1>
        <p>Matches mutuos pendientes de asignar reunión</p>
    </div>
    <div class="page-header-actions">
        <?php if ($currentEventSlug): ?>
            <a href="/eventos/<?= htmlspecialchars($currentEventSlug) ?>/reuniones" class="btn btn-outline" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Público</a>
        <?php endif; ?>
        <a href="/admin/meetings/blocks?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-clock"></i> Bloques</a>
        <a href="/admin/meetings/assignments?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-handshake"></i> Asignadas</a>
        <a href="/admin/meetings/matching?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-project-diagram"></i> Resumen</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<!-- Event Selector -->
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
        <div>
            <strong><?= count($matches) ?></strong> <span class="text-muted">matches sin asignar</span>
        </div>
    </div>
</div>

<!-- Unassigned Matches -->
<div class="card">
    <?php if (empty($matches)): ?>
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <h3>Todos los matches asignados</h3>
            <p>No hay matches pendientes de asignar reunión</p>
            <a href="/admin/meetings/assignments?event_id=<?= $currentEventId ?>" class="btn btn-primary">Ver reuniones asignadas</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sponsor</th>
                        <th>Empresa</th>
                        <th>Match desde</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <?php if ($match['sponsor_logo']): ?>
                                        <img src="<?= htmlspecialchars($match['sponsor_logo']) ?>" alt="" style="width: 32px; height: 32px; object-fit: contain; border-radius: 4px;">
                                    <?php endif; ?>
                                    <strong><?= htmlspecialchars($match['sponsor_name']) ?></strong>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <?php if ($match['company_logo']): ?>
                                        <img src="<?= htmlspecialchars($match['company_logo']) ?>" alt="" style="width: 32px; height: 32px; object-fit: contain; border-radius: 4px;">
                                    <?php endif; ?>
                                    <?= htmlspecialchars($match['company_name']) ?>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime(max($match['sponsor_selected_at'], $match['company_selected_at']))) ?>
                                </small>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="openAssignModal(<?= $match['sponsor_id'] ?>, <?= $match['company_id'] ?>, '<?= htmlspecialchars(addslashes($match['sponsor_name'])) ?>', '<?= htmlspecialchars(addslashes($match['company_name'])) ?>')">
                                    <i class="fas fa-calendar-plus"></i> Asignar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Assign Modal -->
<div id="assignModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3>Asignar Reunión</h3>
            <button type="button" class="modal-close" onclick="closeAssignModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p><strong>Sponsor:</strong> <span id="modalSponsorName"></span></p>
            <p><strong>Empresa:</strong> <span id="modalCompanyName"></span></p>

            <div id="slotsLoading" style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p>Cargando slots disponibles...</p>
            </div>

            <div id="slotsContainer" style="display: none;">
                <label class="form-label">Selecciona un slot</label>
                <select id="slotSelect" class="form-control">
                    <option value="">-- Seleccionar --</option>
                </select>
            </div>

            <div id="noSlotsMessage" style="display: none;" class="alert alert-warning">
                No hay slots disponibles para este match. Verifica la disponibilidad de ambas partes.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeAssignModal()">Cancelar</button>
            <button type="button" id="assignBtn" class="btn btn-primary" onclick="assignMeeting()" disabled>
                <i class="fas fa-check"></i> Asignar
            </button>
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
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
    border-bottom: 1px solid var(--border-color, #e5e7eb);
    background: var(--bg-secondary, #f9fafb);
}
.modal-header h3 {
    margin: 0;
    color: var(--text-primary, #111827);
}
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary, #6b7280);
}
.modal-close:hover {
    color: var(--text-primary, #111827);
}
.modal-body {
    padding: 1.5rem;
    background: var(--bg-primary, #ffffff);
    color: var(--text-primary, #111827);
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border-color, #e5e7eb);
    background: var(--bg-secondary, #f9fafb);
}
</style>

<script>
let currentSponsorId = null;
let currentCompanyId = null;

function openAssignModal(sponsorId, companyId, sponsorName, companyName) {
    currentSponsorId = sponsorId;
    currentCompanyId = companyId;

    document.getElementById('modalSponsorName').textContent = sponsorName;
    document.getElementById('modalCompanyName').textContent = companyName;
    document.getElementById('slotsLoading').style.display = 'block';
    document.getElementById('slotsContainer').style.display = 'none';
    document.getElementById('noSlotsMessage').style.display = 'none';
    document.getElementById('assignBtn').disabled = true;
    document.getElementById('assignModal').style.display = 'flex';

    // Load available slots
    fetch(`/admin/meetings/available-slots?event_id=<?= $currentEventId ?>&sponsor_id=${sponsorId}&company_id=${companyId}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('slotsLoading').style.display = 'none';

            // Check if we have slots (count > 0 or non-empty grouped object)
            const hasSlots = data.count > 0 || (data.slots && Object.keys(data.slots).length > 0);

            if (hasSlots) {
                const select = document.getElementById('slotSelect');
                select.innerHTML = '<option value="">-- Seleccionar --</option>';

                // Handle grouped slots (object with block names as keys)
                if (typeof data.slots === 'object' && !Array.isArray(data.slots)) {
                    Object.keys(data.slots).forEach(blockName => {
                        const optgroup = document.createElement('optgroup');
                        optgroup.label = blockName;
                        data.slots[blockName].forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.id;
                            option.textContent = `${slot.slot_time.substring(0,5)} - ${slot.room_name || 'Mesa ' + slot.room_number}`;
                            optgroup.appendChild(option);
                        });
                        select.appendChild(optgroup);
                    });
                } else if (Array.isArray(data.slots)) {
                    // Handle flat array of slots
                    data.slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.id;
                        option.textContent = `${slot.event_date} ${slot.slot_time.substring(0,5)} - ${slot.room_name || 'Mesa ' + slot.room_number} (${slot.block_name})`;
                        select.appendChild(option);
                    });
                }

                document.getElementById('slotsContainer').style.display = 'block';
            } else {
                document.getElementById('noSlotsMessage').style.display = 'block';
            }
        })
        .catch(err => {
            document.getElementById('slotsLoading').style.display = 'none';
            document.getElementById('noSlotsMessage').style.display = 'block';
            console.error(err);
        });
}

function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
    currentSponsorId = null;
    currentCompanyId = null;
}

document.getElementById('slotSelect').addEventListener('change', function() {
    document.getElementById('assignBtn').disabled = !this.value;
});

function assignMeeting() {
    const slotId = document.getElementById('slotSelect').value;
    if (!slotId || !currentSponsorId || !currentCompanyId) return;

    document.getElementById('assignBtn').disabled = true;
    document.getElementById('assignBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Asignando...';

    fetch('/admin/meetings/assign', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            _csrf_token: '<?= $csrf_token ?>',
            slot_id: slotId,
            event_id: '<?= $currentEventId ?>',
            sponsor_id: currentSponsorId,
            company_id: currentCompanyId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al asignar');
            document.getElementById('assignBtn').disabled = false;
            document.getElementById('assignBtn').innerHTML = '<i class="fas fa-check"></i> Asignar';
        }
    })
    .catch(err => {
        alert('Error de conexión');
        document.getElementById('assignBtn').disabled = false;
        document.getElementById('assignBtn').innerHTML = '<i class="fas fa-check"></i> Asignar';
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAssignModal();
});

// Close modal on background click
document.getElementById('assignModal').addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});
</script>
