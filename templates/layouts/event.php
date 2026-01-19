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

    <style>
        /* ============================================
           THE LAST OF SAAS - Event Page Styles
           Black/White Alternating Design
           ============================================ */

        :root {
            --bg-dark: #000000;
            --bg-light: #FFFFFF;
            --text-light: #FFFFFF;
            --text-dark: #000000;
            --text-grey: #86868B;
            --text-grey-dark: #555555;
            --border-light: rgba(255, 255, 255, 0.1);
            --border-dark: rgba(0, 0, 0, 0.1);
            --font-heading: 'Montserrat', sans-serif;
            --font-accent: 'Prompt', sans-serif;
            --font-display: 'Big Shoulders Text', sans-serif;
            --font-mono: 'Roboto Mono', monospace;
            --font-body: 'Montserrat', sans-serif;
            --transition: all 0.3s ease-in-out;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Container - Full width with padding */
        .container-wide {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 4rem;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.1;
        }

        /* Buttons - No border radius */
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
            transition: var(--transition);
            border-radius: 0;
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

        .btn-dark {
            background: var(--bg-dark);
            color: var(--text-light);
            border-color: var(--bg-dark);
        }
        .btn-dark:hover {
            background: transparent;
            color: var(--bg-dark);
            border-color: var(--bg-dark);
        }

        .btn-lg {
            padding: 1.25rem 3rem;
            font-size: 16px;
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
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border-light);
        }

        .site-header .container-wide {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .site-header .logo {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 16px;
            color: var(--text-light);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.15em;
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
            transition: var(--transition);
        }

        .site-header nav a:hover {
            color: var(--text-light);
        }

        /* ============================================
           SECTION A: Hero Minimal (White bg)
           ============================================ */
        .event-hero-minimal {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 140px 0 80px;
        }

        .event-hero-grid {
            display: flex;
            align-items: flex-start;
            gap: 4rem;
        }

        .event-date-block {
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 2rem 2.5rem;
            text-align: center;
            flex-shrink: 0;
        }

        .event-date-block .day {
            display: block;
            font-family: var(--font-accent);
            font-size: 72px;
            font-weight: 700;
            line-height: 1;
        }

        .event-date-block .month {
            display: block;
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-top: 0.25rem;
        }

        .event-date-block .year {
            display: block;
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
            margin-top: 0.5rem;
        }

        .event-title-block {
            flex: 1;
        }

        .event-title-block h1 {
            font-size: clamp(40px, 6vw, 72px);
            margin-bottom: 1.5rem;
            color: var(--text-dark);
        }

        .event-meta-inline {
            display: flex;
            gap: 3rem;
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey-dark);
        }

        .event-meta-inline span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-meta-inline i {
            color: var(--text-dark);
        }

        /* ============================================
           SECTION B: Intro (Black bg)
           ============================================ */
        .event-intro {
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 80px 0;
        }

        .intro-text {
            font-size: clamp(24px, 3vw, 36px);
            line-height: 1.5;
            font-weight: 400;
            max-width: 1000px;
        }

        /* ============================================
           SECTION C: CTA (Light version)
           ============================================ */
        .event-cta--light {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 60px 0;
            border-top: 1px solid var(--border-dark);
            border-bottom: 1px solid var(--border-dark);
        }

        .cta-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        .cta-info {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .cta-price {
            font-family: var(--font-accent);
            font-size: 32px;
            font-weight: 700;
        }

        .cta-label {
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-grey-dark);
        }

        /* ============================================
           SECTION D: Description (Black bg)
           ============================================ */
        .event-description {
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 100px 0;
        }

        .event-description h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-description h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-light);
        }

        .description-content {
            font-size: 18px;
            line-height: 1.9;
            color: var(--text-grey);
            max-width: 900px;
        }

        .description-content p {
            margin-bottom: 1.5rem;
        }

        .description-content strong {
            color: var(--text-light);
        }

        /* ============================================
           SECTION E: Companies (White bg)
           ============================================ */
        .event-companies {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 100px 0;
        }

        .event-companies h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-companies h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-dark);
        }

        .participants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .participant-card {
            background: var(--bg-dark);
            border: 1px solid var(--bg-dark);
            padding: 1.5rem 2rem;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            text-decoration: none;
        }

        .participant-card:hover {
            background: transparent;
        }

        .participant-card img {
            max-width: 100%;
            max-height: 50px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            transition: var(--transition);
        }

        .participant-card:hover img {
            filter: none;
        }

        .participant-name {
            font-family: var(--font-mono);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-light);
        }

        .participant-card:hover .participant-name {
            color: var(--text-dark);
        }

        /* ============================================
           SECTION F: Agenda (Black bg)
           ============================================ */
        .event-agenda {
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 100px 0;
        }

        .event-agenda h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-agenda h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-light);
        }

        .agenda-day {
            margin-bottom: 3rem;
        }

        .agenda-date {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 14px;
            font-family: var(--font-mono);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-light);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-light);
            padding: 0.75rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .agenda-timeline {
            border-left: 2px solid var(--border-light);
            padding-left: 2rem;
            margin-left: 1rem;
        }

        .agenda-item {
            display: flex;
            gap: 1.5rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
        }

        .agenda-item::before {
            content: '';
            position: absolute;
            left: -2rem;
            top: 1.75rem;
            width: 12px;
            height: 12px;
            background: var(--bg-dark);
            border: 2px solid rgba(255, 255, 255, 0.3);
            margin-left: -6px;
        }

        .agenda-item.featured::before {
            background: var(--text-light);
            border-color: var(--text-light);
        }

        .agenda-time {
            flex-shrink: 0;
            width: 60px;
            text-align: right;
        }

        .agenda-time .time-start {
            display: block;
            font-family: var(--font-accent);
            font-size: 18px;
            font-weight: 700;
        }

        .agenda-time .time-end {
            display: block;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
        }

        .agenda-content { flex: 1; }

        .agenda-type {
            display: inline-block;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.25rem 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            margin-bottom: 0.5rem;
        }

        .agenda-content h4 {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .agenda-content p {
            font-size: 14px;
            color: var(--text-grey);
            line-height: 1.6;
            margin-bottom: 0.75rem;
        }

        .agenda-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .agenda-speaker {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
        }

        .agenda-speaker img {
            width: 24px;
            height: 24px;
            object-fit: cover;
        }

        .agenda-room {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
            padding-left: 0.75rem;
            border-left: 3px solid rgba(255, 255, 255, 0.3);
        }

        /* ============================================
           SECTION G: Speakers (White bg)
           ============================================ */
        .event-speakers {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 100px 0;
        }

        .event-speakers h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-speakers h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-dark);
        }

        .speakers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 2.5rem;
        }

        .speaker-card {
            text-align: center;
        }

        .speaker-photo {
            width: 140px;
            height: 140px;
            margin: 0 auto 1.25rem;
            overflow: hidden;
            border: 3px solid var(--bg-dark);
        }

        .speaker-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .speaker-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.05);
            color: var(--text-grey-dark);
            font-size: 3rem;
        }

        .speaker-info strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
            color: var(--text-dark);
        }

        .speaker-position,
        .speaker-company {
            display: block;
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey-dark);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* ============================================
           SECTION H: Details (Black bg)
           ============================================ */
        .event-details {
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 100px 0;
        }

        .event-details h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-details h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-light);
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-light);
        }

        .detail-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--text-light);
            color: var(--bg-dark);
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            display: block;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-grey);
            margin-bottom: 0.5rem;
        }

        .detail-value {
            font-size: 18px;
            font-weight: 500;
        }

        /* ============================================
           SECTION I: Sponsors (White bg)
           ============================================ */
        .event-sponsors-section {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 100px 0;
        }

        .event-sponsors-section h2 {
            font-size: clamp(28px, 4vw, 40px);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .event-sponsors-section h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--text-dark);
        }

        .sponsors-level {
            margin-bottom: 3rem;
        }

        .sponsors-level h3 {
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--text-grey-dark);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-dark);
        }

        .sponsors-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .sponsor-card {
            background: var(--bg-dark);
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            text-decoration: none;
        }

        .sponsor-card:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        .sponsors-level--platinum .sponsor-card {
            min-width: 220px;
            min-height: 110px;
        }

        .sponsors-level--gold .sponsor-card {
            min-width: 180px;
            min-height: 90px;
        }

        .sponsors-level--silver .sponsor-card,
        .sponsors-level--bronze .sponsor-card {
            min-width: 140px;
            min-height: 70px;
        }

        .sponsor-card img {
            max-width: 100%;
            max-height: 60px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.9;
            transition: var(--transition);
        }

        .sponsor-card:hover img {
            opacity: 1;
        }

        .sponsor-name {
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-light);
        }

        /* ============================================
           SECTION J: Final CTA (Black bg)
           ============================================ */
        .event-cta--dark {
            background: var(--bg-dark);
            color: var(--text-light);
            padding: 100px 0;
        }

        .cta-final {
            text-align: center;
        }

        .cta-final h2 {
            font-size: clamp(32px, 5vw, 56px);
            margin-bottom: 1rem;
        }

        .cta-final p {
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
            margin-bottom: 2.5rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* ============================================
           FOOTER
           ============================================ */
        .site-footer {
            background: var(--bg-dark);
            border-top: 1px solid var(--border-light);
            padding: 4rem 0 2rem;
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
            font-size: 14px;
            color: var(--text-light);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.15em;
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
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--text-light);
        }

        .footer-bottom {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-light);
            text-align: center;
        }

        .footer-bottom p {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 992px) {
            .container-wide {
                padding: 0 2rem;
            }

            .event-hero-grid {
                flex-direction: column;
                gap: 2rem;
            }

            .event-date-block {
                align-self: flex-start;
            }

            .cta-content {
                flex-direction: column;
                text-align: center;
            }

            .cta-info {
                flex-direction: column;
                gap: 0.5rem;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .site-header nav {
                display: none;
            }

            .event-hero-minimal {
                padding: 120px 0 60px;
            }

            .event-date-block .day {
                font-size: 56px;
            }

            .intro-text {
                font-size: 20px;
            }

            .event-intro,
            .event-description,
            .event-companies,
            .event-agenda,
            .event-speakers,
            .event-details,
            .event-sponsors-section,
            .event-cta--dark {
                padding: 60px 0;
            }

            .agenda-item {
                flex-direction: column;
                gap: 0.75rem;
            }

            .agenda-time {
                text-align: left;
                width: auto;
            }

            .speakers-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }

            .speaker-photo {
                width: 100px;
                height: 100px;
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
        <div class="container-wide">
            <a href="/" class="logo">THE LAST OF SAAS</a>
            <nav>
                <a href="/">Inicio</a>
                <a href="/eventos">Eventos</a>
                <a href="/sponsor/login">Sponsors</a>
                <a href="/empresa/login">Empresas</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <?= $_content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container-wide">
            <div class="footer-content">
                <a href="/" class="footer-logo">THE LAST OF SAAS</a>
                <div class="footer-links">
                    <a href="/eventos">Eventos</a>
                    <a href="/sponsor/login">Sponsors</a>
                    <a href="/empresa/login">Empresas</a>
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
