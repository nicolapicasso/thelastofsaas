<?php
/**
 * Team Members List Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Equipo</h1>
        <p>Gestiona los miembros del equipo</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/team/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Miembro
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Team Members Table -->
<div class="card">
    <?php if (empty($members)): ?>
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>No hay miembros del equipo</h3>
            <p>Anade los miembros de tu equipo</p>
            <a href="/admin/team/create" class="btn btn-primary">Nuevo Miembro</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="60">Foto</th>
                        <th>Nombre</th>
                        <th>Puesto</th>
                        <th>Contacto</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td>
                                <div class="member-avatar">
                                    <?php if (!empty($member['photo'])): ?>
                                        <img src="<?= htmlspecialchars($member['photo']) ?>" alt="<?= htmlspecialchars($member['name']) ?>">
                                    <?php else: ?>
                                        <i class="fas fa-user"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="member-name-cell">
                                    <strong><?= htmlspecialchars($member['name']) ?></strong>
                                    <small class="text-muted d-block">/equipo/<?= htmlspecialchars($member['slug']) ?></small>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($member['position'])): ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($member['position']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="member-contact">
                                    <?php if (!empty($member['email'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($member['email']) ?>" title="Email">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['linkedin_url'])): ?>
                                        <a href="<?= htmlspecialchars($member['linkedin_url']) ?>" target="_blank" title="LinkedIn">
                                            <i class="fab fa-linkedin"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (empty($member['email']) && empty($member['linkedin_url'])): ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/equipo/<?= htmlspecialchars($member['slug']) ?>" class="btn btn-sm btn-outline" title="Ver" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin/team/<?= $member['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="/admin/team/<?= $member['id'] ?>/delete" class="inline-form"
                                          onsubmit="return confirm('¿Eliminar este miembro del equipo?')">
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
.member-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    background-color: var(--color-gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-gray-400);
}
.member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.member-avatar i {
    font-size: 20px;
}
.member-name-cell {
    line-height: 1.4;
}
.member-contact {
    display: flex;
    gap: 12px;
}
.member-contact a {
    color: var(--color-gray-500);
    font-size: 18px;
    transition: color 0.2s ease;
}
.member-contact a:hover {
    color: var(--color-primary);
}
</style>
