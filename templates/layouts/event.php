<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($meta_title ?? 'Evento - The Last of SaaS') ?></title>
    <?php if (!empty($meta_description)): ?>
        <meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
    <?php endif; ?>
    <?php if (!empty($meta_image)): ?>
        <meta property="og:image" content="<?= htmlspecialchars($meta_image) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Base Styles -->
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-hover: #4338CA;
            --secondary-color: #7C3AED;
            --success-color: #10B981;
            --error-color: #EF4444;
            --warning-color: #F59E0B;
            --info-color: #3B82F6;

            --text-primary: #1F2937;
            --text-secondary: #4B5563;
            --text-muted: #9CA3AF;

            --bg-primary: #FFFFFF;
            --bg-secondary: #F3F4F6;
            --bg-dark: #1F2937;

            --border-color: #E5E7EB;

            --font-primary: 'Inter', sans-serif;
            --font-secondary: 'Inter', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-primary);
            color: var(--text-primary);
            line-height: 1.6;
            background: var(--bg-primary);
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }
        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        /* Header */
        .site-header {
            background: var(--bg-dark);
            color: white;
            padding: 1rem 0;
        }
        .site-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .site-header .logo img {
            height: 40px;
        }
        .site-header nav a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            margin-left: 2rem;
        }
        .site-header nav a:hover {
            color: white;
        }

        /* Footer */
        .site-footer {
            background: var(--bg-dark);
            color: rgba(255,255,255,0.7);
            padding: 3rem 0 1.5rem;
            margin-top: 4rem;
        }
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .footer-logo img {
            height: 30px;
        }
        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            margin-left: 1.5rem;
        }
        .footer-links a:hover {
            color: white;
        }
        .footer-bottom {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            font-size: 0.9rem;
        }

        /* Utilities */
        .text-muted {
            color: var(--text-muted);
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <a href="/" class="logo">
                <img src="/assets/images/logo-white.svg" alt="The Last of SaaS" onerror="this.src='/assets/images/logo.svg'">
            </a>
            <nav>
                <a href="/">Inicio</a>
                <a href="/eventos">Eventos</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <?= $_content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <a href="/" class="footer-logo">
                    <img src="/assets/images/logo-white.svg" alt="The Last of SaaS" onerror="this.src='/assets/images/logo.svg'">
                </a>
                <div class="footer-links">
                    <a href="/eventos">Eventos</a>
                    <a href="/privacidad">Privacidad</a>
                    <a href="/contacto">Contacto</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> The Last of SaaS. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
