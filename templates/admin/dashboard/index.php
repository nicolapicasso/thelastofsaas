<div class="dashboard">
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon pages-icon">&#9633;</div>
            <div class="stat-content">
                <span class="stat-value"><?= $stats['pages']['total'] ?></span>
                <span class="stat-label">Páginas</span>
            </div>
            <div class="stat-meta">
                <span class="stat-published"><?= $stats['pages']['published'] ?> publicadas</span>
                <span class="stat-draft"><?= $stats['pages']['draft'] ?> borradores</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon categories-icon">&#9776;</div>
            <div class="stat-content">
                <span class="stat-value"><?= $stats['categories']['total'] ?></span>
                <span class="stat-label">Categorías</span>
            </div>
            <div class="stat-meta">
                <span><?= $stats['categories']['active'] ?> activas</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="section">
        <h2 class="section-title">Acciones rápidas</h2>
        <div class="quick-actions">
            <a href="/admin/pages/create" class="quick-action-btn">
                <span class="action-icon">+</span>
                Nueva Página
            </a>
            <a href="/admin/posts/create" class="quick-action-btn">
                <span class="action-icon">+</span>
                Nuevo Post
            </a>
            <a href="/admin/categories/create" class="quick-action-btn">
                <span class="action-icon">+</span>
                Nueva Categoría
            </a>
        </div>
    </div>

    <!-- Recent Pages -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Páginas recientes</h2>
            <a href="/admin/pages" class="section-link">Ver todas</a>
        </div>

        <?php if (!empty($recentPages)): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Slug</th>
                        <th>Estado</th>
                        <th>Actualizada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPages as $page): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($page['title']) ?></strong>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($page['slug']) ?></code>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $page['status'] ?>">
                                <?= ucfirst($page['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?= date('d/m/Y H:i', strtotime($page['updated_at'])) ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="/admin/pages/<?= $page['id'] ?>/edit" class="btn btn-sm">Editar</a>
                                <?php if ($page['status'] === 'published'): ?>
                                <a href="/<?= $page['slug'] ?>" target="_blank" class="btn btn-sm btn-outline">Ver</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>No hay páginas todavía.</p>
            <a href="/admin/pages/create" class="btn btn-primary">Crear primera página</a>
        </div>
        <?php endif; ?>
    </div>
</div>
