<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Login - We're Sinapsis CMS</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-logo">Sinapsis</h1>
                <span class="login-badge">CMS</span>
            </div>

            <?php if (isset($flash) && $flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= $flash['message'] ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="/admin/do-login" class="login-form">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        autofocus
                        placeholder="tu@email.com"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="********"
                    >
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        <span>Recordarme</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    Iniciar sesión
                </button>
            </form>
        </div>

        <p class="login-footer">
            We're Sinapsis CMS &copy; <?= date('Y') ?>
        </p>
    </div>
</body>
</html>
