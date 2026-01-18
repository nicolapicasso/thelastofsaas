<?php
/**
 * Company Login Template
 * TLOS - The Last of SaaS
 */
?>

<section class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Panel de Empresas</h1>
                <p>Accede con tu codigo de empresa para seleccionar sponsors</p>
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
                    <label class="form-label">Codigo de acceso</label>
                    <input type="text"
                           name="code"
                           class="form-control form-control-lg code-input"
                           placeholder="XXXX-XXXX-XXXX"
                           autocomplete="off"
                           autofocus
                           required>
                    <small class="form-text">Introduce el codigo que recibiste por email</small>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    <i class="fas fa-sign-in-alt"></i> Acceder
                </button>
            </form>

            <div class="auth-footer">
                <p>¿No tienes un codigo? <a href="/contacto">Contacta con nosotros</a></p>
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
    background: linear-gradient(135deg, #059669 0%, #10B981 100%);
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
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
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
.btn-primary {
    background: #059669;
    border-color: #059669;
}
.btn-primary:hover {
    background: #047857;
    border-color: #047857;
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
    color: #059669;
    text-decoration: none;
}
.auth-footer a:hover {
    text-decoration: underline;
}
</style>
