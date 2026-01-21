<?php
/**
 * Company Login Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Empresas - The Last of SaaS</title>

    <!-- Fonts - TLOS Brand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --bg-dark: #000000;
            --bg-card: #0a0a0a;
            --text-light: #FFFFFF;
            --text-grey: #86868B;
            --border-color: rgba(255, 255, 255, 0.1);
            --error-color: #EF4444;
            --font-heading: 'Montserrat', sans-serif;
            --font-mono: 'Roboto Mono', monospace;
            --transition: all 0.3s ease-in-out;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-heading);
            background: var(--bg-dark);
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            padding: 2rem;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 3rem;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .auth-logo {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 2rem;
            color: var(--text-grey);
        }

        .auth-header h1 {
            font-size: clamp(28px, 5vw, 36px);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin-bottom: 1rem;
        }

        .auth-header p {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .alert {
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }

        .auth-form {
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-grey);
            margin-bottom: 0.75rem;
        }

        .form-control {
            width: 100%;
            padding: 1.25rem 1.5rem;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            font-family: var(--font-mono);
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            text-align: center;
            transition: var(--transition);
        }

        .form-control::placeholder {
            color: var(--text-grey);
            font-size: 14px;
            letter-spacing: 0.15em;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--text-light);
        }

        .form-text {
            display: block;
            margin-top: 0.75rem;
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-align: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 1.25rem 2rem;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-decoration: none;
            border: 2px solid transparent;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--text-light);
            color: var(--bg-dark);
            border-color: var(--text-light);
        }

        .btn-primary:hover {
            background: transparent;
            color: var(--text-light);
        }

        .auth-footer {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        .auth-footer p {
            margin: 0.75rem 0;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .auth-footer a {
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .auth-footer a:hover {
            color: var(--text-grey);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">THE LAST OF SAAS</div>
                <h1>PANEL EMPRESAS</h1>
                <p>ACCEDE CON TU CODIGO DE EMPRESA</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                <div class="form-group">
                    <label class="form-label">CODIGO DE ACCESO</label>
                    <input type="text"
                           name="code"
                           class="form-control"
                           placeholder="XXXX-XXXX-XXXX"
                           autocomplete="off"
                           autofocus
                           required>
                    <small class="form-text">INTRODUCE EL CODIGO QUE RECIBISTE POR EMAIL</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> ACCEDER
                </button>
            </form>

            <div class="auth-footer">
                <p>NO TIENES CODIGO? <a href="/contacto">CONTACTANOS</a></p>
                <p><a href="/"><i class="fas fa-arrow-left"></i> VOLVER AL INICIO</a></p>
            </div>
        </div>
    </div>
</body>
</html>
