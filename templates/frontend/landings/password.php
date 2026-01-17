<?php
/**
 * Password Protected Landing - Access Form
 * Omniwallet CMS
 */
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?? 'es' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($seo->getTitle()) ?></title>
    <link rel="icon" href="<?= htmlspecialchars($favicon ?? '/favicon.ico') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
        :root {
            /* We're Sinapsis Brand Colors */
            --primary: #215A6B;
            --primary-light: #4C8693;
            --primary-dark: #12414C;
            --secondary: #F9AF00;
            --dark: #1A1A1A;
            --white: #FFFFFF;
            --gray-100: #F5F5F5;
            --gray-200: #E0E0E0;
            --gray-500: #757575;
            --gray-600: #616161;
            --danger: #ef4444;
            --radius: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .password-container {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        .password-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 30px;
            text-align: center;
            color: var(--white);
        }

        .password-header img {
            height: 40px;
            margin-bottom: 20px;
        }

        .password-header .lock-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }

        .password-header h1 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .password-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .password-body {
            padding: 30px;
        }

        .landing-info {
            background: var(--gray-100);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            text-align: center;
        }

        .landing-info h2 {
            font-size: 16px;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .landing-info p {
            font-size: 13px;
            color: var(--gray-600);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .password-input-wrapper {
            position: relative;
        }

        .password-input-wrapper input {
            width: 100%;
            padding: 14px 50px 14px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.2s;
        }

        .password-input-wrapper input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(62, 149, 176, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            padding: 5px;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: var(--danger);
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-message i {
            flex-shrink: 0;
        }

        .submit-btn {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(62, 149, 176, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .password-footer {
            text-align: center;
            padding: 0 30px 30px;
        }

        .password-footer a {
            color: var(--gray-500);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .password-footer a:hover {
            color: var(--primary);
        }

        @media (max-width: 480px) {
            .password-header {
                padding: 25px 20px;
            }
            .password-body {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="password-container">
        <div class="password-header">
            <?php if (!empty($logoHeader)): ?>
                <img src="<?= htmlspecialchars($logoHeader) ?>" alt="Logo">
            <?php endif; ?>
            <div class="lock-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h1>Contenido Protegido</h1>
            <p>Esta página requiere contraseña para acceder</p>
        </div>

        <div class="password-body">
            <div class="landing-info">
                <h2><?= htmlspecialchars($landing['title']) ?></h2>
                <?php if (!empty($landing['theme_title'])): ?>
                    <p><?= htmlspecialchars($landing['theme_title']) ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="/lp/<?= htmlspecialchars($themeSlug) ?>/<?= htmlspecialchars($landingSlug) ?>/access">
                <div class="form-group">
                    <label for="password">Contraseña de acceso</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" required autofocus
                               placeholder="Introduce la contraseña">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-unlock"></i>
                    Acceder
                </button>
            </form>
        </div>

        <div class="password-footer">
            <a href="/lp"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
