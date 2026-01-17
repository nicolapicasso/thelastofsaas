<?php
/**
 * User Form Template (Create/Edit)
 * Omniwallet CMS
 */

$isEdit = !empty($user);
$formAction = $isEdit ? "/admin/users/{$user['id']}" : '/admin/users';
?>

<div class="page-header">
    <div class="page-header-content">
        <a href="/admin/users" class="back-link">
            <i class="fas fa-arrow-left"></i> Usuarios
        </a>
        <h1><?= $isEdit ? 'Editar Usuario' : 'Nuevo Usuario' ?></h1>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $formAction ?>" class="content-form">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="form-layout">
        <!-- Main Content -->
        <div class="form-main">
            <div class="card">
                <div class="card-header">
                    <h3>Informacion del Usuario</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nombre <span class="required">*</span></label>
                        <input type="text" id="name" name="name"
                               value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                               placeholder="Nombre completo"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email"
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                               placeholder="usuario@ejemplo.com"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="password">
                            Contrasena
                            <?php if (!$isEdit): ?><span class="required">*</span><?php endif; ?>
                        </label>
                        <input type="password" id="password" name="password"
                               placeholder="<?= $isEdit ? 'Dejar en blanco para mantener la actual' : 'Minimo 6 caracteres' ?>"
                               <?= !$isEdit ? 'required' : '' ?>
                               minlength="6">
                        <?php if ($isEdit): ?>
                            <small class="form-hint">Deja este campo vacio si no quieres cambiar la contrasena.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="form-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Estado</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1"
                                   <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                            <span class="toggle-label">Usuario activo</span>
                        </label>
                        <small class="form-hint">Los usuarios inactivos no pueden iniciar sesion.</small>
                    </div>
                </div>
            </div>

            <?php if ($isEdit && !empty($user['last_login'])): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Informacion</h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Ultimo acceso</span>
                        <span class="info-value"><?= date('d/m/Y H:i', strtotime($user['last_login'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Creado</span>
                        <span class="info-value"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i>
                        <?= $isEdit ? 'Guardar Cambios' : 'Crear Usuario' ?>
                    </button>
                    <a href="/admin/users" class="btn btn-outline btn-block">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.info-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--color-gray-100);
}
.info-item:last-child {
    border-bottom: none;
}
.info-label {
    color: var(--color-gray-500);
    font-size: 13px;
}
.info-value {
    font-weight: 500;
    font-size: 13px;
}
</style>
