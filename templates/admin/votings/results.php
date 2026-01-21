<?php
/**
 * Voting Results Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Resultados: <?= htmlspecialchars($voting['title']) ?></h1>
        <p>Visualiza los resultados de la votación</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/votings" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Votaciones</a>
        <a href="/admin/votings/<?= $voting['id'] ?>/edit" class="btn btn-outline"><i class="fas fa-edit"></i> Editar</a>
        <a href="/admin/votings/<?= $voting['id'] ?>/candidates" class="btn btn-outline"><i class="fas fa-users"></i> Candidatos</a>
    </div>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<!-- Summary Stats -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card">
        <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);"><?= $totalVotes ?></div>
            <div class="text-muted">Votos Totales</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold;"><?= count($candidates) ?></div>
            <div class="text-muted">Candidatos</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="text-align: center;">
            <?php
            $statusClass = match($voting['status']) {
                'active' => 'success',
                'draft' => 'secondary',
                'inactive' => 'warning',
                'finished' => 'info',
                default => 'secondary'
            };
            ?>
            <div style="font-size: 1.5rem;">
                <span class="badge badge-<?= $statusClass ?>" style="font-size: 1rem;"><?= $statusOptions[$voting['status']] ?? $voting['status'] ?></span>
            </div>
            <div class="text-muted">Estado</div>
        </div>
    </div>
</div>

<!-- Results Chart -->
<?php if (!empty($candidates)): ?>
<div class="card">
    <div class="card-header">
        <h3>Ranking</h3>
    </div>
    <div class="card-body">
        <?php
        $maxVotes = max(array_column($candidates, 'total_votes')) ?: 1;
        $rank = 1;
        ?>
        <?php foreach ($candidates as $candidate): ?>
            <?php
            $percentage = $totalVotes > 0 ? ($candidate['total_votes'] / $totalVotes) * 100 : 0;
            $barWidth = ($candidate['total_votes'] / $maxVotes) * 100;
            ?>
            <div style="margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                    <div style="width: 40px; height: 40px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                        <?= $rank++ ?>
                    </div>
                    <?php if ($candidate['logo_url']): ?>
                        <img src="<?= htmlspecialchars($candidate['logo_url']) ?>" alt="" style="width: 40px; height: 40px; object-fit: contain; border-radius: 4px;">
                    <?php endif; ?>
                    <div style="flex: 1;">
                        <strong><?= htmlspecialchars($candidate['name']) ?></strong>
                    </div>
                    <div style="text-align: right;">
                        <strong><?= $candidate['total_votes'] ?></strong> votos
                        <small class="text-muted">(<?= number_format($percentage, 1) ?>%)</small>
                    </div>
                </div>
                <div style="background: var(--bg-secondary); border-radius: 4px; height: 24px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, var(--primary-color), var(--primary-hover)); height: 100%; width: <?= $barWidth ?>%; transition: width 0.3s ease;"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Detailed Table -->
<div class="card">
    <div class="card-header">
        <h3>Detalle de Votos</h3>
    </div>
    <?php if (empty($candidates)): ?>
        <div class="empty-state">
            <i class="fas fa-chart-bar"></i>
            <h3>Sin datos</h3>
            <p>No hay candidatos en esta votación</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Candidato</th>
                        <th class="text-center">Votos Reales</th>
                        <th class="text-center">Votos Base</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $position = 1;
                    foreach ($candidates as $candidate):
                        $percentage = $totalVotes > 0 ? ($candidate['total_votes'] / $totalVotes) * 100 : 0;
                    ?>
                        <tr>
                            <td>
                                <?php if ($position <= 3): ?>
                                    <span style="font-size: 1.5rem;">
                                        <?= match($position) { 1 => '🥇', 2 => '🥈', 3 => '🥉' } ?>
                                    </span>
                                <?php else: ?>
                                    <?= $position ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <?php if ($candidate['logo_url']): ?>
                                        <img src="<?= htmlspecialchars($candidate['logo_url']) ?>" alt="" style="width: 32px; height: 32px; object-fit: contain; border-radius: 4px;">
                                    <?php endif; ?>
                                    <strong><?= htmlspecialchars($candidate['name']) ?></strong>
                                </div>
                            </td>
                            <td class="text-center"><?= $candidate['votes'] ?></td>
                            <td class="text-center"><?= $candidate['base_votes'] ?></td>
                            <td class="text-center"><strong><?= $candidate['total_votes'] ?></strong></td>
                            <td class="text-center"><?= number_format($percentage, 1) ?>%</td>
                        </tr>
                    <?php
                        $position++;
                    endforeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Total:</strong></td>
                        <td class="text-center"><strong><?= $totalVotes ?></strong></td>
                        <td class="text-center">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Voting Info -->
<div class="card">
    <div class="card-header">
        <h3>Información de la Votación</h3>
    </div>
    <div class="card-body">
        <table class="table table-sm">
            <tr>
                <th width="200">Título</th>
                <td><?= htmlspecialchars($voting['title']) ?></td>
            </tr>
            <tr>
                <th>Slug</th>
                <td><code>/votar/<?= $voting['slug'] ?></code></td>
            </tr>
            <tr>
                <th>Inicio</th>
                <td><?= $voting['voting_start'] ? date('d/m/Y H:i', strtotime($voting['voting_start'])) : 'Sin límite' ?></td>
            </tr>
            <tr>
                <th>Fin</th>
                <td><?= $voting['voting_end'] ? date('d/m/Y H:i', strtotime($voting['voting_end'])) : 'Sin límite' ?></td>
            </tr>
            <tr>
                <th>Mostrar contador</th>
                <td><?= $voting['show_vote_counts'] ? 'Sí' : 'No' ?></td>
            </tr>
            <tr>
                <th>Mostrar ranking</th>
                <td><?= $voting['show_ranking'] ? 'Sí' : 'No' ?></td>
            </tr>
            <tr>
                <th>Múltiples votos</th>
                <td><?= $voting['allow_multiple_votes'] ? 'Sí' : 'No' ?></td>
            </tr>
        </table>
    </div>
</div>
