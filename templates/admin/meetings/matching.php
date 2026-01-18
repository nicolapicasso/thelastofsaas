<?php
/**
 * Matching Overview Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Resumen de Matching</h1>
        <p>Vista general del matching bidireccional</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/meetings/blocks?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-clock"></i> Bloques</a>
        <a href="/admin/meetings/unassigned?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-user-plus"></i> Sin Asignar</a>
        <a href="/admin/meetings/assignments?event_id=<?= $currentEventId ?>" class="btn btn-outline"><i class="fas fa-handshake"></i> Asignadas</a>
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
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card">
        <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);"><?= $stats['total'] ?? 0 ?></div>
            <div class="text-muted">Matches Mutuos</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold; color: var(--success-color);"><?= $stats['with_meeting'] ?? 0 ?></div>
            <div class="text-muted">Con Reunion</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold; color: var(--warning-color);"><?= $stats['without_meeting'] ?? 0 ?></div>
            <div class="text-muted">Sin Reunion</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold;">
                <?= ($stats['total'] ?? 0) > 0 ? round((($stats['with_meeting'] ?? 0) / $stats['total']) * 100) : 0 ?>%
            </div>
            <div class="text-muted">Cobertura</div>
        </div>
    </div>
</div>

<!-- All Matches List -->
<div class="card">
    <div class="card-header">
        <h3>Todos los Matches</h3>
    </div>
    <?php if (empty($matches)): ?>
        <div class="empty-state">
            <i class="fas fa-heart"></i>
            <h3>No hay matches</h3>
            <p>Aún no se han producido matches mutuos entre sponsors y empresas</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sponsor</th>
                        <th>Empresa</th>
                        <th class="text-center">Estado</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($match['sponsor_name']) ?></strong></td>
                            <td><?= htmlspecialchars($match['company_name']) ?></td>
                            <td class="text-center">
                                <?php if ($match['has_meeting']): ?>
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Reunión asignada</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($match['has_meeting']): ?>
                                    <a href="/admin/meetings/assignments?event_id=<?= $currentEventId ?>" class="btn btn-sm btn-outline" title="Ver reunión">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="/admin/meetings/unassigned?event_id=<?= $currentEventId ?>" class="btn btn-sm btn-primary" title="Asignar">
                                        <i class="fas fa-calendar-plus"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
