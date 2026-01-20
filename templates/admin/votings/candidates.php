<?php
/**
 * Voting Candidates Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Candidatos: <?= htmlspecialchars($voting['title']) ?></h1>
        <p>Gestiona los candidatos de esta votación</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/votings" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Votaciones</a>
        <a href="/admin/votings/<?= $voting['id'] ?>/edit" class="btn btn-outline"><i class="fas fa-edit"></i> Editar Votación</a>
        <a href="/admin/votings/<?= $voting['id'] ?>/results" class="btn btn-outline"><i class="fas fa-chart-bar"></i> Resultados</a>
        <button type="button" class="btn btn-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Añadir Candidato</button>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<!-- Candidates List -->
<div class="card">
    <?php if (empty($candidates)): ?>
        <div class="empty-state">
            <i class="fas fa-user-plus"></i>
            <h3>No hay candidatos</h3>
            <p>Añade candidatos para que los usuarios puedan votar</p>
            <button type="button" class="btn btn-primary" onclick="openAddModal()">Añadir candidato</button>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table" id="candidatesTable">
                <thead>
                    <tr>
                        <th width="50"></th>
                        <th>Candidato</th>
                        <th>Descripción</th>
                        <th class="text-center">Votos Reales</th>
                        <th class="text-center">Votos Base</th>
                        <th class="text-center">Total</th>
                        <th>Estado</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $candidate): ?>
                        <tr data-id="<?= $candidate['id'] ?>">
                            <td>
                                <i class="fas fa-grip-vertical drag-handle" style="cursor: grab; color: var(--text-muted);"></i>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <?php if ($candidate['logo_url']): ?>
                                        <img src="<?= htmlspecialchars($candidate['logo_url']) ?>" alt="" style="width: 40px; height: 40px; object-fit: contain; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; background: var(--bg-secondary); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= htmlspecialchars($candidate['name']) ?></strong>
                                        <?php if ($candidate['website_url']): ?>
                                            <br><a href="<?= htmlspecialchars($candidate['website_url']) ?>" target="_blank" class="text-muted"><small><?= htmlspecialchars($candidate['website_url']) ?></small></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><small><?= htmlspecialchars(mb_substr($candidate['description'] ?? '', 0, 100)) ?><?= mb_strlen($candidate['description'] ?? '') > 100 ? '...' : '' ?></small></td>
                            <td class="text-center"><?= $candidate['votes'] ?></td>
                            <td class="text-center"><?= $candidate['base_votes'] ?></td>
                            <td class="text-center"><strong><?= $candidate['votes'] + $candidate['base_votes'] ?></strong></td>
                            <td>
                                <span class="badge badge-<?= $candidate['active'] ? 'success' : 'secondary' ?>">
                                    <?= $candidate['active'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline" onclick="editCandidate(<?= htmlspecialchars(json_encode($candidate)) ?>)" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline btn-danger" onclick="deleteCandidate(<?= $candidate['id'] ?>)" title="Eliminar">
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
<div id="candidateModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 id="modalTitle">Añadir Candidato</h3>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="candidateForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="candidate_id" id="candidateId" value="">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label required">Nombre</label>
                    <input type="text" name="name" id="candidateName" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" id="candidateDescription" class="form-control" rows="3"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">URL del Logo</label>
                        <input type="url" name="logo_url" id="candidateLogo" class="form-control" placeholder="https://...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Website</label>
                        <input type="url" name="website_url" id="candidateWebsite" class="form-control" placeholder="https://...">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Votos Base</label>
                        <input type="number" name="base_votes" id="candidateBaseVotes" class="form-control" value="0" min="0">
                        <small class="text-muted">Votos iniciales (no registrados)</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Orden</label>
                        <input type="number" name="display_order" id="candidateOrder" class="form-control" value="0" min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="active" id="candidateActive" value="1" checked>
                        <span>Activo</span>
                    </label>
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
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}
.modal-header h3 { margin: 0; }
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary, #6b7280);
}
.modal-body {
    padding: 1.5rem;
    background: var(--bg-primary, #ffffff);
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border-color, #e5e7eb);
    background: var(--bg-primary, #ffffff);
}
.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}
.drag-handle:hover { color: var(--primary-color, #3b82f6) !important; }
</style>

<script>
let isEditMode = false;
let editCandidateId = null;

function openAddModal() {
    isEditMode = false;
    editCandidateId = null;
    document.getElementById('modalTitle').textContent = 'Añadir Candidato';
    document.getElementById('candidateId').value = '';
    document.getElementById('candidateName').value = '';
    document.getElementById('candidateDescription').value = '';
    document.getElementById('candidateLogo').value = '';
    document.getElementById('candidateWebsite').value = '';
    document.getElementById('candidateBaseVotes').value = '0';
    document.getElementById('candidateOrder').value = '0';
    document.getElementById('candidateActive').checked = true;
    document.getElementById('candidateModal').style.display = 'flex';
}

function editCandidate(candidate) {
    isEditMode = true;
    editCandidateId = candidate.id;
    document.getElementById('modalTitle').textContent = 'Editar Candidato';
    document.getElementById('candidateId').value = candidate.id;
    document.getElementById('candidateName').value = candidate.name;
    document.getElementById('candidateDescription').value = candidate.description || '';
    document.getElementById('candidateLogo').value = candidate.logo_url || '';
    document.getElementById('candidateWebsite').value = candidate.website_url || '';
    document.getElementById('candidateBaseVotes').value = candidate.base_votes || 0;
    document.getElementById('candidateOrder').value = candidate.display_order || 0;
    document.getElementById('candidateActive').checked = candidate.active == 1;
    document.getElementById('candidateModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('candidateModal').style.display = 'none';
}

function deleteCandidate(id) {
    if (!confirm('¿Eliminar este candidato? Se perderán sus votos.')) return;
    fetch('/admin/votings/<?= $voting['id'] ?>/candidates/' + id + '/delete', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => d.success ? location.reload() : alert(d.error));
}

// Handle form submission via fetch
document.getElementById('candidateForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    // Determine URL based on mode
    let url = '/admin/votings/<?= $voting['id'] ?>/candidates';
    if (isEditMode && editCandidateId) {
        url = '/admin/votings/<?= $voting['id'] ?>/candidates/' + editCandidateId;
    }

    fetch(url, {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            location.reload();
        } else {
            alert(d.error || 'Error al guardar el candidato');
        }
    })
    .catch(err => {
        alert('Error de conexión: ' + err.message);
    });
});

// Close modal on escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
document.getElementById('candidateModal').addEventListener('click', e => { if (e.target.id === 'candidateModal') closeModal(); });
</script>
