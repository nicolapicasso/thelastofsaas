<?php
/**
 * Landing Themes List Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Temáticas de Landing</h1>
        <p>Organiza tus landing pages por temáticas</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/landing-themes/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Temática
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="card">
    <?php if (empty($themes)): ?>
        <div class="empty-state">
            <i class="fas fa-layer-group"></i>
            <h3>No hay temáticas</h3>
            <p>Crea temáticas para organizar tus landing pages</p>
            <a href="/admin/landing-themes/create" class="btn btn-primary">Nueva Temática</a>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th width="60">Orden</th>
                        <th>Temática</th>
                        <th>Slug</th>
                        <th>Landings</th>
                        <th>Estado</th>
                        <th width="180">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($themes as $theme): ?>
                        <tr>
                            <td>
                                <span class="sort-handle"><i class="fas fa-grip-vertical"></i></span>
                                <?= $theme['sort_order'] ?>
                            </td>
                            <td>
                                <div class="item-with-icon">
                                    <?php if (!empty($theme['icon'])): ?>
                                        <i class="<?= htmlspecialchars($theme['icon']) ?> item-icon"></i>
                                    <?php elseif (!empty($theme['image'])): ?>
                                        <img src="<?= htmlspecialchars($theme['image']) ?>" alt="" class="item-thumbnail">
                                    <?php else: ?>
                                        <div class="item-icon-placeholder"><i class="fas fa-layer-group"></i></div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= htmlspecialchars($theme['title']) ?></strong>
                                        <?php if (!empty($theme['subtitle'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($theme['subtitle']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><code>/lp/<?= htmlspecialchars($theme['slug']) ?>/</code></td>
                            <td>
                                <a href="/admin/landings?theme=<?= $theme['id'] ?>" class="badge badge-info">
                                    <?= $theme['landing_count'] ?? 0 ?> landings
                                </a>
                            </td>
                            <td>
                                <?php if ($theme['is_active']): ?>
                                    <span class="badge badge-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/lp/<?= $theme['slug'] ?>/" target="_blank" class="btn btn-sm btn-outline" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin/landings?theme=<?= $theme['id'] ?>" class="btn btn-sm btn-outline" title="Ver Landings">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    <a href="/admin/landing-themes/<?= $theme['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="/admin/landing-themes/<?= $theme['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('¿Eliminar esta temática y todas sus landings?')">
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
</style>
