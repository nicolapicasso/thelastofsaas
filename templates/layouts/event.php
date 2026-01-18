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

    <!-- Fonts - TLOS Brand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700&family=Big+Shoulders+Text:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- TLOS Brand Styles -->
    <style>
        /* ============================================
           THE LAST OF SAAS - Brand Stylesheet
           ============================================ */

        :root {
            /* Colors */
            --bg-dark: #000000;
            --bg-navy: #030925;
            --text-light: #FFFFFF;
            --text-dark: #000000;
            --text-grey: #86868B;
            --accent-dot: #222222;

            /* Semantic */
            --primary-color: #000000;
            --primary-hover: #030925;
            --success-color: #10B981;
            --error-color: #EF4444;
            --warning-color: #F59E0B;
            --border-color: rgba(255, 255, 255, 0.1);

            /* Typography */
            --font-heading: 'Montserrat', sans-serif;
            --font-accent: 'Prompt', sans-serif;
            --font-display: 'Big Shoulders Text', sans-serif;
            --font-mono: 'Roboto Mono', monospace;
            --font-body: 'Montserrat', sans-serif;

            /* Transitions */
            --transition-standard: all 0.3s ease-in-out;

            /* Spacing */
            --section-padding: 100px;
            --container-max: 1200px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-body);
            color: var(--text-light);
            line-height: 1.6;
            background: var(--bg-dark);
            -webkit-font-smoothing: antialiased;
        }

        /* Container */
        .container {
            width: 100%;
            max-width: var(--container-max);
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.1;
        }

        h1 {
            font-size: clamp(50px, 10vw, 100px);
        }

        h2 {
            font-size: clamp(36px, 6vw, 60px);
        }

        h3 {
            font-size: clamp(24px, 4vw, 36px);
        }

        .text-accent {
            font-family: var(--font-accent);
        }

        .text-display {
            font-family: var(--font-display);
        }

        .text-mono {
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
        }

        .text-grey {
            color: var(--text-grey);
        }

        /* Buttons - TLOS Style */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-decoration: none;
            border: 2px solid transparent;
            cursor: pointer;
            transition: var(--transition-standard);
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

        .btn-outline {
            background: transparent;
            border: 2px solid var(--text-light);
            color: var(--text-light);
        }
        .btn-outline:hover {
            background: var(--text-light);
            color: var(--bg-dark);
        }

        .btn-dark {
            background: var(--bg-dark);
            color: var(--text-light);
            border-color: var(--bg-dark);
        }
        .btn-dark:hover {
            background: var(--bg-navy);
            border-color: var(--bg-navy);
        }

        .btn-lg {
            padding: 1.25rem 3rem;
            font-size: 16px;
        }

        .btn-block {
            display: flex;
            width: 100%;
        }

        /* ============================================
           HEADER
           ============================================ */
        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            padding: 1.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .site-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .site-header .logo {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 18px;
            color: var(--text-light);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .site-header .logo img {
            height: 40px;
            filter: brightness(0) invert(1);
        }

        .site-header nav {
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }

        .site-header nav a {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            transition: var(--transition-standard);
        }

        .site-header nav a:hover {
            color: var(--text-light);
        }

        /* ============================================
           HERO SECTION
           ============================================ */
        .event-hero {
            min-height: 100vh;
            display: grid;
            place-items: center;
            position: relative;
            background-size: cover;
            background-position: center;
            padding-top: 80px;
        }

        .event-hero__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.9) 100%);
        }

        .event-hero__content {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 4rem 2rem;
        }

        .event-hero__date {
            display: inline-block;
            margin-bottom: 2rem;
        }

        .event-hero__date-inner {
            background: var(--text-light);
            color: var(--bg-dark);
            padding: 1.5rem 2rem;
            text-align: center;
        }

        .event-hero__date .day {
            display: block;
            font-family: var(--font-accent);
            font-size: 64px;
            font-weight: 700;
            line-height: 1;
        }

        .event-hero__date .month {
            display: block;
            font-family: var(--font-heading);
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
        }

        .event-hero__date .year {
            display: block;
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
            margin-top: 0.25rem;
        }

        .event-hero__info h1 {
            margin-bottom: 1.5rem;
        }

        .event-hero__info .lead {
            font-size: clamp(18px, 2vw, 24px);
            color: var(--text-grey);
            max-width: 600px;
            margin: 0 auto 2rem;
            font-weight: 400;
        }

        .event-hero__meta {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-bottom: 3rem;
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
        }

        .event-hero__meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-hero__meta i {
            color: var(--text-light);
        }

        /* ============================================
           EVENT CONTENT
           ============================================ */
        .event-content {
            padding: var(--section-padding) 0;
            background: var(--bg-dark);
        }

        .event-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 4rem;
        }

        .event-section {
            margin-bottom: 4rem;
        }

        .event-section h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 2rem;
            position: relative;
            display: inline-block;
        }

        .event-section h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-light);
        }

        .prose {
            color: var(--text-grey);
            font-size: 16px;
            line-height: 1.8;
        }

        .prose p {
            margin-bottom: 1.5rem;
        }

        /* Features List */
        .features-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        .features-list li {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition-standard);
        }

        .features-list li:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .features-list i {
            color: var(--text-light);
            font-size: 1.25rem;
        }

        .features-list span {
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
        }

        /* Sponsors */
        .sponsors-level {
            margin-bottom: 2.5rem;
        }

        .sponsors-level h3 {
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--text-grey);
            margin-bottom: 1rem;
        }

        .sponsors-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .sponsor-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-standard);
        }

        .sponsor-card:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .sponsors-level--platinum .sponsor-card {
            min-width: 200px;
            min-height: 100px;
        }

        .sponsors-level--gold .sponsor-card {
            min-width: 160px;
            min-height: 80px;
        }

        .sponsors-level--silver .sponsor-card,
        .sponsors-level--bronze .sponsor-card {
            min-width: 130px;
            min-height: 70px;
        }

        .sponsor-card img {
            max-width: 100%;
            max-height: 60px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.8;
            transition: var(--transition-standard);
        }

        .sponsor-card:hover img {
            opacity: 1;
        }

        /* ============================================
           SIDEBAR
           ============================================ */
        .event-sidebar {
            position: sticky;
            top: 120px;
            height: fit-content;
        }

        .sidebar-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }

        .sidebar-card h3 {
            font-size: 14px;
            font-family: var(--font-mono);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Ticket Types */
        .ticket-type {
            padding: 1.25rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .ticket-type:last-of-type {
            border-bottom: none;
            margin-bottom: 1.5rem;
        }

        .ticket-type__info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .ticket-type__info strong {
            font-family: var(--font-heading);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }

        .ticket-type .price {
            font-family: var(--font-accent);
            font-weight: 700;
            font-size: 20px;
        }

        .ticket-type__desc {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            margin: 0;
        }

        /* Info List */
        .info-list {
            list-style: none;
        }

        .info-list li {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .info-list i {
            width: 20px;
            text-align: center;
            color: var(--text-light);
            margin-top: 0.25rem;
        }

        .info-list strong {
            display: block;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-grey);
            margin-bottom: 0.25rem;
        }

        .info-list span {
            font-size: 14px;
        }

        /* Share Buttons */
        .share-buttons {
            display: flex;
            gap: 0.75rem;
        }

        .share-btn {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition-standard);
        }

        .share-btn:hover {
            background: var(--text-light);
            color: var(--bg-dark);
        }

        /* ============================================
           FOOTER
           ============================================ */
        .site-footer {
            background: var(--bg-dark);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 4rem 0 2rem;
            margin-top: 0;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .footer-logo {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 16px;
            color: var(--text-light);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .footer-logo img {
            height: 30px;
            filter: brightness(0) invert(1);
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-links a {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: var(--transition-standard);
        }

        .footer-links a:hover {
            color: var(--text-light);
        }

        .footer-bottom {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .footer-bottom p {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
        }

        /* ============================================
           UTILITIES
           ============================================ */
        .text-muted {
            color: var(--text-grey);
        }

        .text-center {
            text-align: center;
        }

        .uppercase {
            text-transform: uppercase;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 992px) {
            .event-layout {
                grid-template-columns: 1fr;
            }

            .event-sidebar {
                position: static;
            }

            .event-hero__content {
                padding: 2rem 1rem;
            }

            .event-hero__meta {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 768px) {
            :root {
                --section-padding: 60px;
            }

            .site-header nav {
                display: none;
            }

            .event-hero__date .day {
                font-size: 48px;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <a href="/" class="logo">THE LAST OF SAAS</a>
            <nav>
                <a href="/">Inicio</a>
                <a href="/eventos">Eventos</a>
                <a href="/sponsor/login">Sponsors</a>
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
                <a href="/" class="footer-logo">THE LAST OF SAAS</a>
                <div class="footer-links">
                    <a href="/eventos">Eventos</a>
                    <a href="/sponsor/login">Sponsors</a>
                    <a href="/privacidad">Privacidad</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> The Last of SaaS. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
