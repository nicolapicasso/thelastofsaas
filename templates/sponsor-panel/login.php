<?php
/**
 * Sponsor Login Template
 * TLOS - The Last of SaaS
 */
?>

<section class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Panel de Sponsors</h1>
                <p>Accede con tu código de sponsor para seleccionar empresas</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label class="form-label">Código de acceso</label>
                    <input type="text"
                           name="code"
                           class="form-control form-control-lg code-input"
                           placeholder="XXXX-XXXX-XXXX"
                           autocomplete="off"
                           autofocus
                           required>
                    <small class="form-text">Introduce el código que recibiste por email</small>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    <i class="fas fa-sign-in-alt"></i> Acceder
                </button>
            </form>

            <div class="auth-footer">
                <p>¿No tienes un código? <a href="/contacto">Contacta con nosotros</a></p>
                <p><a href="/">← Volver al inicio</a></p>
            </div>
        </div>
    </div>
</section>

<style>
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
}

.auth-container {
    width: 100%;
    max-width: 420px;
}

.auth-card {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}
.auth-header h1 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}
.auth-header p {
    color: var(--text-secondary);
}

.auth-form {
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}
.form-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
}
.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.2s;
}
.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
.form-control-lg {
    padding: 1rem 1.25rem;
    font-size: 1.1rem;
}
.code-input {
    text-transform: uppercase;
    letter-spacing: 2px;
    font-family: monospace;
    text-align: center;
}
.form-text {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: var(--text-muted);
}

.btn-block {
    width: 100%;
}
.btn-lg {
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.alert-danger {
    background: #FEE2E2;
    color: #DC2626;
}

.auth-footer {
    text-align: center;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}
.auth-footer p {
    margin: 0.5rem 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
}
.auth-footer a {
    color: var(--primary-color);
    text-decoration: none;
}
.auth-footer a:hover {
    text-decoration: underline;
}
</style>
