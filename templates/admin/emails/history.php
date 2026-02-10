<?php
/**
 * Admin - Email History
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div>
        <h1>Historial de Emails</h1>
        <p class="text-muted">Registro de todos los emails masivos enviados</p>
    </div>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Navigation Tabs -->
<div class="tabs" style="margin-bottom: 2rem;">
    <a href="/admin/emails" class="tab">
        <i class="fas fa-file-alt"></i> Plantillas
    </a>
    <a href="/admin/emails/bulk" class="tab">
        <i class="fas fa-paper-plane"></i> Email Masivo
    </a>
    <a href="/admin/emails/history" class="tab active">
        <i class="fas fa-history"></i> Historial
    </a>
    <a href="/admin/emails/smtp" class="tab">
        <i class="fas fa-cog"></i> SMTP
    </a>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body" style="padding: 1rem;">
        <form method="GET" action="/admin/emails/history" style="display: flex; gap: 1rem; align-items: center;">
            <div class="form-group" style="margin: 0; flex: 1; max-width: 300px;">
                <select name="event_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos los eventos</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['id'] ?>" <?= $currentEventId == $event['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($event['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($currentEventId): ?>
                <a href="/admin/emails/history" class="btn btn-outline btn-sm">
                    <i class="fas fa-times"></i> Limpiar filtro
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- History Table -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-history"></i> Envios Realizados</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if (empty($bulkEmails)): ?>
            <div class="empty-state" style="padding: 3rem; text-align: center;">
                <i class="fas fa-inbox" style="font-size: 3rem; color: var(--color-gray-400); margin-bottom: 1rem;"></i>
                <h3>No hay envios registrados</h3>
                <p class="text-muted">Los emails masivos que envies apareceran aqui.</p>
                <a href="/admin/emails/bulk" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-paper-plane"></i> Enviar Email Masivo
                </a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Evento</th>
                        <th>Tipo</th>
                        <th>Asunto</th>
                        <th>Destinatarios</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bulkEmails as $email): ?>
                        <tr>
                            <td>
                                <span title="<?= date('d/m/Y H:i:s', strtotime($email['created_at'])) ?>">
                                    <?= date('d/m/Y', strtotime($email['created_at'])) ?>
                                    <br><small class="text-muted"><?= date('H:i', strtotime($email['created_at'])) ?></small>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($email['event_name'] ?? 'N/A') ?>
                            </td>
                            <td>
                                <?php if ($email['recipient_type'] === 'sponsors'): ?>
                                    <span class="badge badge-info"><i class="fas fa-rocket"></i> SaaS</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><i class="fas fa-building"></i> Empresas</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span title="<?= htmlspecialchars($email['subject']) ?>">
                                    <?= htmlspecialchars(mb_substr($email['subject'], 0, 40)) ?><?= mb_strlen($email['subject']) > 40 ? '...' : '' ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-success"><i class="fas fa-check"></i> <?= $email['sent_count'] ?></span>
                                <?php if ($email['failed_count'] > 0): ?>
                                    / <span class="text-danger"><i class="fas fa-times"></i> <?= $email['failed_count'] ?></span>
                                <?php endif; ?>
                                <br><small class="text-muted">de <?= $email['total_recipients'] ?></small>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($email['status']) {
                                    'completed' => 'success',
                                    'partial' => 'warning',
                                    'failed' => 'danger',
                                    'sending' => 'info',
                                    default => 'secondary'
                                };
                                $statusText = match($email['status']) {
                                    'completed' => 'Completado',
                                    'partial' => 'Parcial',
                                    'failed' => 'Fallido',
                                    'sending' => 'Enviando...',
                                    default => $email['status']
                                };
                                ?>
                                <span class="badge badge-<?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="pagination-wrapper" style="padding: 1rem; display: flex; justify-content: center; gap: 0.5rem;">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1])) ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <?php if ($i == $pagination['current_page']): ?>
                            <span class="btn btn-sm btn-primary"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="btn btn-sm btn-outline"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1])) ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Stats Summary -->
<?php if (!empty($bulkEmails)): ?>
<div class="stats-grid" style="margin-top: 1.5rem;">
    <?php
    $totalSent = array_sum(array_column($bulkEmails, 'sent_count'));
    $totalFailed = array_sum(array_column($bulkEmails, 'failed_count'));
    $totalRecipients = array_sum(array_column($bulkEmails, 'total_recipients'));
    ?>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--color-success);">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= count($bulkEmails) ?></span>
            <span class="stat-label">Envios totales</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--color-primary);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $totalSent ?></span>
            <span class="stat-label">Emails enviados</span>
        </div>
    </div>
    <?php if ($totalFailed > 0): ?>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--color-danger);">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $totalFailed ?></span>
            <span class="stat-label">Emails fallidos</span>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<style>
.tabs {
    display: flex;
    gap: 0.5rem;
    border-bottom: 2px solid var(--color-gray-200);
    padding-bottom: 0;
}
.tab {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    color: var(--color-gray-500);
    text-decoration: none;
    font-weight: 500;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s;
}
.tab:hover {
    color: var(--color-gray-700);
}
.tab.active {
    color: var(--color-primary);
    border-bottom-color: var(--color-primary);
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}
.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--color-white);
    padding: 1.25rem;
    border-radius: 8px;
    border: 1px solid var(--color-gray-200);
}
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}
.stat-info {
    display: flex;
    flex-direction: column;
}
.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.2;
}
.stat-label {
    font-size: 0.875rem;
    color: var(--color-gray-500);
}
</style>
