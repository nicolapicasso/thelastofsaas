<?php
/**
 * Users List Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Usuarios</h1>
        <p>Gestiona los usuarios administradores del CMS</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/users/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Search -->
<div class="card mb-4">
    <form method="GET" action="/admin/users" class="search-form">
        <div class="search-input-group">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Buscar por nombre o email..." value="<?= htmlspecialchars($search ?? '') ?>">
            <?php if ($search): ?>
                <a href="/admin/users" class="search-clear"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>
</div>

<!-- Users Table -->
<div class="card">
    <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>No hay usuarios</h3>
            <p>Crea el primer usuario administrador</p>
            <a href="/admin/users/create" class="btn btn-primary">Nuevo Usuario</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="60">Avatar</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Ultimo acceso</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="user-avatar">
                                    <?php if (!empty($user['avatar'])): ?>
                                        <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="<?= htmlspecialchars($user['name']) ?>">
                                    <?php else: ?>
                                        <span class="avatar-initials"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="user-name-cell">
                                    <strong><?= htmlspecialchars($user['name']) ?></strong>
                                    <?php if ($user['id'] == ($_SESSION['user_id'] ?? 0)): ?>
                                        <span class="badge badge-info">Tu</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($user['email']) ?>">
                                    <?= htmlspecialchars($user['email']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($user['last_login'])): ?>
                                    <span title="<?= $user['last_login'] ?>">
                                        <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Nunca</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/users/<?= $user['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                                        <form method="POST" action="/admin/users/<?= $user['id'] ?>/delete" class="inline-form"
                                              onsubmit="return confirm('¿Eliminar este usuario?')">
                                            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?? '' ?>">
                                            <button type="submit" class="btn btn-sm btn-outline btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (!empty($pagination['pagination']) && $pagination['pagination']['total_pages'] > 1): ?>
            <div class="pagination-wrapper">
                <?php
                $paginationData = $pagination['pagination'];
                ?>
                <div class="pagination">
                    <?php if ($paginationData['has_prev']): ?>
                        <a href="?page=<?= $paginationData['current_page'] - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="pagination-link">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <span class="pagination-info">
                        Página <?= $paginationData['current_page'] ?> de <?= $paginationData['total_pages'] ?>
                    </span>

                    <?php if ($paginationData['has_next']): ?>
                        <a href="?page=<?= $paginationData['current_page'] + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="pagination-link">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 18px;
}
.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.user-name-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}
.search-form {
    display: flex;
    gap: 12px;
    padding: 16px;
}
.search-input-group {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}
.search-input-group i {
    position: absolute;
    left: 12px;
    color: var(--color-gray-400);
}
.search-input-group input {
    width: 100%;
    padding: 10px 40px 10px 36px;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: 14px;
}
.search-clear {
    position: absolute;
    right: 12px;
    color: var(--color-gray-400);
}
.search-clear:hover {
    color: var(--color-danger);
}
</style>
