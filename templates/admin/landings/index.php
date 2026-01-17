<?php
/**
 * Landings List Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= $currentTheme ? htmlspecialchars($currentTheme['title']) : 'Todas las Landings' ?></h1>
        <p>Gestiona las landing pages</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/landing-themes" class="btn btn-outline">
            <i class="fas fa-layer-group"></i> Temáticas
        </a>
        <a href="/admin/landings/create<?= $currentThemeId ? '?theme=' . $currentThemeId : '' ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Landing
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Theme Filter -->
<?php if (!empty($themes)): ?>
    <div class="card filters-card">
        <form method="GET" action="/admin/landings" class="filters-form">
            <div class="filter-group">
                <label>Temática</label>
                <select name="theme" onchange="this.form.submit()">
                    <option value="">Todas las temáticas</option>
                    <?php foreach ($themes as $theme): ?>
                        <option value="<?= $theme['id'] ?>" <?= $currentThemeId == $theme['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($theme['title']) ?> (<?= $theme['landing_count'] ?? 0 ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($currentThemeId): ?>
                <a href="/admin/landings" class="btn btn-outline btn-sm">Limpiar filtro</a>
            <?php endif; ?>
        </form>
    </div>
<?php endif; ?>

<div class="card">
    <?php if (empty($landings)): ?>
        <div class="empty-state">
            <i class="fas fa-file-alt"></i>
            <h3>No hay landings</h3>
            <p>Crea tu primera landing page</p>
            <a href="/admin/landings/create<?= $currentThemeId ? '?theme=' . $currentThemeId : '' ?>" class="btn btn-primary">Nueva Landing</a>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th width="60">Orden</th>
                        <th>Landing</th>
                        <?php if (!$currentTheme): ?>
                            <th>Temática</th>
                        <?php endif; ?>
                        <th>URL</th>
                        <th>Vistas</th>
                        <th>Estado</th>
                        <th width="200">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($landings as $landing): ?>
                        <tr>
                            <td>
                                <span class="sort-handle"><i class="fas fa-grip-vertical"></i></span>
                                <?= $landing['sort_order'] ?>
                            </td>
                            <td>
                                <div class="item-with-icon">
                                    <?php if (!empty($landing['icon'])): ?>
                                        <i class="<?= htmlspecialchars($landing['icon']) ?> item-icon"></i>
                                    <?php elseif (!empty($landing['image'])): ?>
                                        <img src="<?= htmlspecialchars($landing['image']) ?>" alt="" class="item-thumbnail">
                                    <?php else: ?>
                                        <div class="item-icon-placeholder"><i class="fas fa-file-alt"></i></div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= htmlspecialchars($landing['title']) ?></strong>
                                        <?php if (!empty($landing['subtitle'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($landing['subtitle']) ?></small>
                                        <?php endif; ?>
                                        <?php if ($landing['is_featured']): ?>
                                            <span class="badge badge-warning badge-sm"><i class="fas fa-star"></i></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <?php if (!$currentTheme): ?>
                                <td>
                                    <a href="/admin/landings?theme=<?= $landing['theme_id'] ?>" class="badge badge-info">
                                        <?= htmlspecialchars($landing['theme_title'] ?? 'Sin temática') ?>
                                    </a>
                                </td>
                            <?php endif; ?>
                            <td>
                                <code>/lp/<?= htmlspecialchars($landing['theme_slug'] ?? '') ?>/<?= htmlspecialchars($landing['slug']) ?></code>
                            </td>
                            <td><?= number_format($landing['views'] ?? 0) ?></td>
                            <td>
                                <?php if ($landing['is_active']): ?>
                                    <span class="badge badge-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/landings/<?= $landing['id'] ?>/preview" target="_blank" class="btn btn-sm btn-outline" title="Preview">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($landing['is_active']): ?>
                                        <a href="/lp/<?= $landing['theme_slug'] ?? '' ?>/<?= $landing['slug'] ?>" target="_blank" class="btn btn-sm btn-outline" title="Ver en web">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="/admin/landings/<?= $landing['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="/admin/landings/<?= $landing['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('¿Eliminar esta landing?')">
                                        <input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
                                        <button type="submit" class="btn btn-sm btn-outline btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.item-with-icon {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.item-icon {
    font-size: 24px;
    color: var(--color-primary);
    width: 40px;
    text-align: center;
}

.item-thumbnail {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: var(--radius-md);
}

.item-icon-placeholder {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    color: var(--color-gray-400);
}

.sort-handle {
    cursor: grab;
    color: var(--color-gray-400);
    margin-right: var(--spacing-sm);
}

.inline-form {
    display: inline;
    margin: 0;
}

.btn-group {
    display: flex;
    gap: var(--spacing-xs);
}

.filters-card {
    margin-bottom: var(--spacing-lg);
    padding: var(--spacing-md) var(--spacing-lg);
}

.filters-form {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
}

.filter-group {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.filter-group label {
    font-weight: 500;
    color: var(--color-gray-600);
    margin: 0;
}

.filter-group select {
    min-width: 250px;
}

.badge-sm {
    font-size: 10px;
    padding: 2px 6px;
    margin-left: 4px;
}
</style>
