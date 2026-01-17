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
            <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);"><?= $matchStats['total_matches'] ?? 0 ?></div>
            <div class="text-muted">Matches Mutuos</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold; color: var(--success-color);"><?= $matchStats['with_meeting'] ?? 0 ?></div>
            <div class="text-muted">Con Reunión</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold; color: var(--warning-color);"><?= $matchStats['without_meeting'] ?? 0 ?></div>
            <div class="text-muted">Sin Reunión</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold;">
                <?= $matchStats['total_matches'] > 0 ? round(($matchStats['with_meeting'] / $matchStats['total_matches']) * 100) : 0 ?>%
            </div>
            <div class="text-muted">Cobertura</div>
        </div>
    </div>
</div>

<!-- Selection Statistics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Sponsor Selections -->
    <div class="card">
        <div class="card-header">
            <h3>Selecciones de Sponsors</h3>
        </div>
        <div class="card-body">
            <?php if (empty($sponsorStats)): ?>
                <p class="text-muted">No hay datos de selecciones</p>
            <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Sponsor</th>
                            <th class="text-center">Seleccionadas</th>
                            <th class="text-center">Matches</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sponsorStats as $stat): ?>
                            <tr>
                                <td><?= htmlspecialchars($stat['name']) ?></td>
                                <td class="text-center"><?= $stat['selections'] ?></td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $stat['matches'] > 0 ? 'success' : 'secondary' ?>">
                                        <?= $stat['matches'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Company Selections -->
    <div class="card">
        <div class="card-header">
            <h3>Selecciones de Empresas</h3>
        </div>
        <div class="card-body">
            <?php if (empty($companyStats)): ?>
                <p class="text-muted">No hay datos de selecciones</p>
            <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th class="text-center">Seleccionados</th>
                            <th class="text-center">Matches</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companyStats as $stat): ?>
                            <tr>
                                <td><?= htmlspecialchars($stat['name']) ?></td>
                                <td class="text-center"><?= $stat['selections'] ?></td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $stat['matches'] > 0 ? 'success' : 'secondary' ?>">
                                        <?= $stat['matches'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- All Matches List -->
<div class="card">
    <div class="card-header">
        <h3>Todos los Matches</h3>
    </div>
    <?php if (empty($allMatches)): ?>
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
                    <?php foreach ($allMatches as $match): ?>
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
