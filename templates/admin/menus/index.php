<?php
/**
 * Menus Index Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Gestión de Menús</h1>
        <p class="page-description">Administra los menús de navegación de tu sitio web</p>
    </div>
    <div class="page-actions">
        <a href="/admin/menus/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Menú
        </a>
    </div>
</div>

<?php if (empty($menus)): ?>
<div class="empty-state">
    <div class="empty-icon"><i class="fas fa-bars"></i></div>
    <h3>No hay menús creados</h3>
    <p>Crea tu primer menú para empezar a gestionar la navegación de tu sitio.</p>
    <a href="/admin/menus/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Crear primer menú
    </a>
</div>
<?php else: ?>
<div class="menus-grid">
    <?php foreach ($menus as $menu): ?>
    <div class="menu-card">
        <div class="menu-card-header">
            <div class="menu-info">
                <h3><?= htmlspecialchars($menu['name']) ?></h3>
                <span class="menu-location location-<?= $menu['location'] ?>">
                    <?php
                    $locations = [
                        'header' => 'Cabecera',
                        'footer' => 'Pie de página',
                        'sidebar' => 'Barra lateral',
                        'other' => 'Otro'
                    ];
                    echo $locations[$menu['location']] ?? $menu['location'];
                    ?>
                </span>
            </div>
            <div class="menu-status <?= $menu['is_active'] ? 'active' : 'inactive' ?>">
                <?= $menu['is_active'] ? 'Activo' : 'Inactivo' ?>
            </div>
        </div>

        <?php if (!empty($menu['description'])): ?>
        <p class="menu-description"><?= htmlspecialchars($menu['description']) ?></p>
        <?php endif; ?>

        <div class="menu-stats">
            <div class="stat">
                <span class="stat-value"><?= $menu['item_count'] ?></span>
                <span class="stat-label">elementos</span>
            </div>
            <div class="stat">
                <span class="stat-value"><?= htmlspecialchars($menu['slug']) ?></span>
                <span class="stat-label">slug</span>
            </div>
        </div>

        <div class="menu-card-actions">
            <a href="/admin/menus/<?= $menu['id'] ?>/edit" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form method="POST" action="/admin/menus/<?= $menu['id'] ?>/delete"
                  onsubmit="return confirm('¿Eliminar este menú y todos sus elementos?');" style="display: inline;">
                <input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<style>
.menus-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: var(--spacing-lg);
}

.menu-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--color-gray-200);
}

.menu-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-md);
}

.menu-info h3 {
    margin: 0 0 var(--spacing-xs);
    font-size: 18px;
    font-weight: 600;
}

.menu-location {
    display: inline-block;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 500;
    border-radius: 4px;
    text-transform: uppercase;
}

.menu-location.location-header {
    background-color: #dbeafe;
    color: #1e40af;
}

.menu-location.location-footer {
    background-color: #fef3c7;
    color: #92400e;
}

.menu-location.location-sidebar {
    background-color: #d1fae5;
    color: #065f46;
}

.menu-location.location-other {
    background-color: var(--color-gray-100);
    color: var(--color-gray-600);
}

.menu-status {
    font-size: 12px;
    font-weight: 500;
    padding: 4px 10px;
    border-radius: 12px;
}

.menu-status.active {
    background-color: #d1fae5;
    color: #065f46;
}

.menu-status.inactive {
    background-color: var(--color-gray-100);
    color: var(--color-gray-600);
}

.menu-description {
    color: var(--color-gray-600);
    font-size: 14px;
    margin: 0 0 var(--spacing-md);
}

.menu-stats {
    display: flex;
    gap: var(--spacing-lg);
    padding: var(--spacing-md) 0;
    border-top: 1px solid var(--color-gray-100);
    border-bottom: 1px solid var(--color-gray-100);
    margin-bottom: var(--spacing-md);
}

.menu-stats .stat {
    display: flex;
    flex-direction: column;
}

.menu-stats .stat-value {
    font-size: 16px;
    font-weight: 600;
    color: var(--color-gray-800);
}

.menu-stats .stat-label {
    font-size: 12px;
    color: var(--color-gray-500);
}

.menu-card-actions {
    display: flex;
    gap: var(--spacing-sm);
}

.menu-card-actions .btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.menu-card-actions .btn-danger {
    flex: 0;
    padding: 6px 12px;
}

.empty-state {
    text-align: center;
    padding: var(--spacing-xl) * 2;
    background: white;
    border-radius: var(--radius-lg);
    border: 2px dashed var(--color-gray-200);
}

.empty-icon {
    font-size: 48px;
    color: var(--color-gray-300);
    margin-bottom: var(--spacing-md);
}

.empty-state h3 {
    margin: 0 0 var(--spacing-sm);
    color: var(--color-gray-700);
}

.empty-state p {
    color: var(--color-gray-500);
    margin-bottom: var(--spacing-lg);
}
</style>
