<?php
/**
 * SEO Audit Log Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><i class="fas fa-history"></i> Historial SEO</h1>
        <p>Registro de cambios y generaciones de SEO</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/seo" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Últimas Acciones</h3>
    </div>
    <div class="card-body">
        <?php if (empty($logs)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No hay registros de actividad</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Acción</th>
                        <th>Tipo</th>
                        <th>ID</th>
                        <th>Campo</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <span class="log-date"><?= date('d/m/Y', strtotime($log['created_at'])) ?></span>
                                <span class="log-time"><?= date('H:i:s', strtotime($log['created_at'])) ?></span>
                            </td>
                            <td>
                                <?php
                                $actionBadge = [
                                    'generate' => 'success',
                                    'update' => 'info',
                                    'delete' => 'danger'
                                ];
                                ?>
                                <span class="badge badge-<?= $actionBadge[$log['action']] ?? 'secondary' ?>">
                                    <?= ucfirst($log['action']) ?>
                                </span>
                            </td>
                            <td><?= $entityTypes[$log['entity_type']] ?? $log['entity_type'] ?></td>
                            <td>
                                <a href="/admin/seo/edit?type=<?= $log['entity_type'] ?>&id=<?= $log['entity_id'] ?>">
                                    #<?= $log['entity_id'] ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($log['field_changed'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($log['user_name'] ?? 'Sistema') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<style>
.log-date {
    font-weight: 500;
}

.log-time {
    font-size: var(--font-size-sm);
    color: var(--gray-500);
    display: block;
}

.empty-state {
    text-align: center;
    padding: var(--spacing-3xl);
    color: var(--gray-500);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: var(--spacing-md);
}
</style>
