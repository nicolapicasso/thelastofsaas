<?php
/**
 * Votings List Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Votaciones</h1>
        <p>Gestiona las votaciones y premios</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/votings/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Votación</a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<!-- Event Filter -->
<div class="card">
    <div class="card-body">
        <div class="filter-group">
            <label>Filtrar por Evento</label>
            <select onchange="location.href='?event_id='+this.value" class="form-control" style="max-width: 300px;">
                <option value="">Todos los eventos</option>
                <?php foreach ($events as $evt): ?>
                    <option value="<?= $evt['id'] ?>" <?= ($currentEventId ?? '') == $evt['id'] ? 'selected' : '' ?>><?= htmlspecialchars($evt['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- Votings List -->
<div class="card">
    <?php if (empty($votings)): ?>
        <div class="empty-state">
            <i class="fas fa-vote-yea"></i>
            <h3>No hay votaciones</h3>
            <p>Crea una votación para permitir que los asistentes voten</p>
            <a href="/admin/votings/create" class="btn btn-primary">Crear votación</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Votación</th>
                        <th>Evento</th>
                        <th class="text-center">Candidatos</th>
                        <th class="text-center">Votos</th>
                        <th>Periodo</th>
                        <th>Estado</th>
                        <th width="150">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($votings as $voting): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($voting['title']) ?></strong>
                                <?php if ($voting['slug']): ?>
                                    <br><small class="text-muted">/votar/<?= $voting['slug'] ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($voting['event_name'] ?? 'Sin evento') ?></td>
                            <td class="text-center"><?= $voting['candidate_count'] ?? 0 ?></td>
                            <td class="text-center"><strong><?= $voting['total_votes'] ?? 0 ?></strong></td>
                            <td>
                                <?php if ($voting['voting_start'] || $voting['voting_end']): ?>
                                    <small>
                                        <?php if ($voting['voting_start']): ?>
                                            Desde: <?= date('d/m/Y H:i', strtotime($voting['voting_start'])) ?>
                                        <?php endif; ?>
                                        <?php if ($voting['voting_end']): ?>
                                            <br>Hasta: <?= date('d/m/Y H:i', strtotime($voting['voting_end'])) ?>
                                        <?php endif; ?>
                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">Sin límite</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($voting['status']) {
                                    'active' => 'success',
                                    'draft' => 'secondary',
                                    'inactive' => 'warning',
                                    'finished' => 'info',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge badge-<?= $statusClass ?>"><?= $statusOptions[$voting['status']] ?? $voting['status'] ?></span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/votings/<?= $voting['id'] ?>/candidates" class="btn btn-sm btn-outline" title="Candidatos">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    <a href="/admin/votings/<?= $voting['id'] ?>/results" class="btn btn-sm btn-outline" title="Resultados">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="/admin/votings/<?= $voting['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline btn-danger" onclick="deleteVoting(<?= $voting['id'] ?>)" title="Eliminar">
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

<script>
function deleteVoting(id) {
    if (!confirm('¿Eliminar esta votación? Se perderán todos los votos.')) return;
    fetch('/admin/votings/' + id + '/delete', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => d.success ? location.reload() : alert(d.error));
}
</script>
