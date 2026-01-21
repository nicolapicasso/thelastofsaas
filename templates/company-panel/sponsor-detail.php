<?php
/**
 * Company Panel - Sponsor Detail Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($sponsor['name'] ?? 'SaaS') ?> - <?= htmlspecialchars($event['name'] ?? '') ?></title>

    <!-- Fonts - TLOS Brand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* TLOS Company Panel Styles */
        :root {
            --bg-dark: #000000;
            --bg-card: #0a0a0a;
            --text-light: #FFFFFF;
            --text-grey: #86868B;
            --border-color: rgba(255, 255, 255, 0.1);
            --success-color: #10B981;
            --primary-color: #059669;
            --error-color: #EF4444;
            --info-color: #3B82F6;
            --font-heading: 'Montserrat', sans-serif;
            --font-mono: 'Roboto Mono', monospace;
            --font-accent: 'Prompt', sans-serif;
            --transition: all 0.3s ease-in-out;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-heading);
            background: var(--bg-dark);
            color: var(--text-light);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Layout */
        .panel-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .panel-sidebar {
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        .brand-logo {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-grey);
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }

        .brand-logo a {
            color: inherit;
            text-decoration: none;
        }

        .brand-logo a:hover {
            color: var(--text-light);
        }

        .sidebar-header {
            text-align: center;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .company-logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            background: var(--text-light);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .sidebar-header h2 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.5rem;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            background: var(--primary-color);
            color: var(--text-light);
        }

        .sidebar-nav {
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            color: var(--text-grey);
            text-decoration: none;
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border: 1px solid transparent;
            transition: var(--transition);
            margin-bottom: 0.5rem;
        }

        .nav-item:hover {
            color: var(--text-light);
            border-color: var(--border-color);
            background: rgba(255, 255, 255, 0.02);
        }

        .nav-item.active {
            color: var(--bg-dark);
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .nav-item i { width: 20px; text-align: center; }

        .sidebar-footer {
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        /* Main Content */
        .panel-main {
            margin-left: 280px;
            padding: 2rem 3rem;
            min-height: 100vh;
            width: calc(100% - 280px);
            max-width: calc(100% - 280px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-grey);
            text-decoration: none;
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--text-light);
        }

        /* Sponsor Detail Card */
        .sponsor-detail-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 3rem;
            max-width: 900px;
            position: relative;
        }

        .detail-badge {
            position: absolute;
            top: 2rem;
            right: 2rem;
            padding: 0.75rem 1.5rem;
            font-family: var(--font-mono);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-badge.selected {
            background: var(--primary-color);
            color: var(--text-light);
        }

        .detail-badge.match {
            background: var(--success-color);
            color: var(--bg-dark);
        }

        .detail-badge.interested {
            background: var(--info-color);
            color: var(--text-light);
        }

        .detail-header {
            display: flex;
            gap: 2rem;
            margin-bottom: 3rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-logo {
            width: 120px;
            height: 120px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .detail-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            background: var(--text-light);
            padding: 0.5rem;
        }

        .detail-logo .logo-placeholder {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--text-grey);
        }

        .detail-info h1 {
            font-size: clamp(24px, 4vw, 32px);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin-bottom: 1rem;
        }

        .level-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            padding: 0.5rem 1rem;
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 1rem;
        }

        .tagline {
            font-size: 14px;
            color: var(--text-grey);
            font-style: italic;
            margin-bottom: 1rem;
        }

        .website-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: var(--transition);
        }

        .website-link:hover {
            color: var(--text-light);
        }

        /* Detail Sections */
        .detail-section {
            margin-bottom: 2.5rem;
        }

        .detail-section h3 {
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 500;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 1rem;
        }

        .detail-section p {
            font-size: 14px;
            line-height: 1.8;
            color: var(--text-light);
        }

        /* Sponsor Message */
        .sponsor-message {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid var(--info-color);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .sponsor-message h4 {
            font-family: var(--font-mono);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--info-color);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sponsor-message p {
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-light);
        }

        /* Actions */
        .detail-actions {
            margin-top: 3rem;
            padding-top: 3rem;
            border-top: 1px solid var(--border-color);
            text-align: center;
        }

        .match-message {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success-color);
            padding: 2rem;
            color: var(--success-color);
        }

        .match-message i {
            font-size: 2.5rem;
        }

        .match-message p {
            margin: 0;
            text-align: left;
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            line-height: 1.6;
        }

        .interested-message {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid var(--info-color);
            padding: 1.5rem;
            color: var(--info-color);
            margin-bottom: 2rem;
        }

        .interested-message i {
            font-size: 1.5rem;
        }

        .interested-message p {
            margin: 0;
            font-family: var(--font-mono);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1.25rem 2.5rem;
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
            background: var(--primary-color);
            color: var(--text-light);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: transparent;
            color: var(--primary-color);
        }

        .btn-outline {
            background: transparent;
            border-color: var(--text-light);
            color: var(--text-light);
        }

        .btn-outline:hover {
            background: var(--text-light);
            color: var(--bg-dark);
        }

        .btn-danger {
            background: var(--error-color);
            color: var(--text-light);
            border-color: var(--error-color);
        }

        .btn-danger:hover {
            background: transparent;
            color: var(--error-color);
        }

        .btn-lg {
            padding: 1.5rem 3rem;
            font-size: 16px;
        }

        .btn-block {
            display: flex;
            width: 100%;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 11px;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .panel-sidebar {
                display: none;
            }

            .panel-main {
                margin-left: 0;
                padding: 1.5rem;
                width: 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .detail-header {
                flex-direction: column;
                text-align: center;
            }

            .detail-logo {
                margin: 0 auto;
            }

            .detail-badge {
                position: static;
                margin-bottom: 2rem;
                display: inline-flex;
            }
        }
    </style>
</head>
<body>
    <div class="panel-layout">
        <!-- Sidebar -->
        <aside class="panel-sidebar">
            <div class="brand-logo">
                <a href="/">THE LAST OF SAAS</a>
            </div>
            <div class="sidebar-header">
                <?php if (!empty($company['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="" class="company-logo">
                <?php endif; ?>
                <h2><?= htmlspecialchars($company['name'] ?? '') ?></h2>
                <span class="badge">EMPRESA</span>
            </div>

            <nav class="sidebar-nav">
                <a href="/empresa/panel" class="nav-item">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="/empresa/sponsors/<?= $event['id'] ?>" class="nav-item active">
                    <i class="fas fa-rocket"></i> Ver SaaS
                </a>
                <a href="/empresa/matches/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-heart"></i> Mis Matches
                </a>
                <a href="/empresa/mensajes/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-envelope"></i> Mensajes
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="/empresa/logout" class="btn btn-outline btn-block btn-sm">
                    <i class="fas fa-sign-out-alt"></i> CERRAR SESION
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="panel-main">
            <a href="/empresa/sponsors/<?= $event['id'] ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> VOLVER A SAAS
            </a>

            <div class="sponsor-detail-card">
                <?php if ($isMatch): ?>
                    <div class="detail-badge match"><i class="fas fa-heart"></i> MATCH MUTUO</div>
                <?php elseif ($isSelected): ?>
                    <div class="detail-badge selected"><i class="fas fa-check"></i> SELECCIONADO</div>
                <?php elseif ($isInterested): ?>
                    <div class="detail-badge interested"><i class="fas fa-star"></i> INTERESADO EN TI</div>
                <?php endif; ?>

                <div class="detail-header">
                    <div class="detail-logo">
                        <?php if (!empty($sponsor['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="<?= htmlspecialchars($sponsor['name'] ?? '') ?>">
                        <?php else: ?>
                            <div class="logo-placeholder"><i class="fas fa-rocket"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="detail-info">
                        <h1><?= htmlspecialchars($sponsor['name'] ?? '') ?></h1>
                        <?php if (!empty($sponsor['level'])): ?>
                            <span class="level-badge"><?= htmlspecialchars(strtoupper($sponsor['level'])) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($sponsor['tagline'])): ?>
                            <p class="tagline"><?= htmlspecialchars($sponsor['tagline']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($sponsor['website'])): ?>
                            <a href="<?= htmlspecialchars($sponsor['website']) ?>" target="_blank" class="website-link">
                                <i class="fas fa-globe"></i> <?= htmlspecialchars(parse_url($sponsor['website'], PHP_URL_HOST) ?: $sponsor['website']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($isInterested && !$isSelected): ?>
                    <div class="interested-message">
                        <i class="fas fa-info-circle"></i>
                        <p>ESTE SAAS TE HA SELECCIONADO. SI TU TAMBIEN LO SELECCIONAS, HAREIS MATCH.</p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sponsorMessage)): ?>
                    <div class="sponsor-message">
                        <h4><i class="fas fa-comment"></i> MENSAJE DEL SPONSOR</h4>
                        <p><?= nl2br(htmlspecialchars($sponsorMessage['message'] ?? '')) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sponsor['description'])): ?>
                    <div class="detail-section">
                        <h3>SOBRE ESTE SAAS</h3>
                        <p><?= nl2br(htmlspecialchars($sponsor['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="detail-actions">
                    <?php if (!$isSelected): ?>
                        <button type="button" class="btn btn-primary btn-lg btn-select" data-sponsor="<?= $sponsor['id'] ?>" data-event="<?= $event['id'] ?>">
                            <i class="fas fa-plus"></i> SELECCIONAR SAAS
                        </button>
                    <?php elseif (!$isMatch): ?>
                        <button type="button" class="btn btn-danger btn-lg btn-unselect" data-sponsor="<?= $sponsor['id'] ?>" data-event="<?= $event['id'] ?>">
                            <i class="fas fa-minus"></i> QUITAR SELECCION
                        </button>
                    <?php else: ?>
                        <div class="match-message">
                            <i class="fas fa-heart"></i>
                            <p>ESTE SAAS TAMBIEN TE HA SELECCIONADO. OS PONDREMOS EN CONTACTO PRONTO.</p>
                        </div>
                    <?php endif; ?>

                    <a href="/empresa/mensajes/<?= $event['id'] ?>/<?= $sponsor['id'] ?>" class="btn btn-outline btn-lg" style="margin-top: 1rem;">
                        <i class="fas fa-envelope"></i> ENVIAR MENSAJE
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectBtn = document.querySelector('.btn-select');
        const unselectBtn = document.querySelector('.btn-unselect');

        if (selectBtn) {
            selectBtn.addEventListener('click', function() {
                const sponsorId = this.dataset.sponsor;
                const eventId = this.dataset.event;

                fetch('/empresa/seleccionar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `sponsor_id=${sponsorId}&event_id=${eventId}&_csrf_token=<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Error al seleccionar');
                    }
                });
            });
        }

        if (unselectBtn) {
            unselectBtn.addEventListener('click', function() {
                if (!confirm('Quitar seleccion?')) return;

                const sponsorId = this.dataset.sponsor;
                const eventId = this.dataset.event;

                fetch('/empresa/deseleccionar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `sponsor_id=${sponsorId}&event_id=${eventId}&_csrf_token=<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Error al quitar');
                    }
                });
            });
        }
    });
    </script>
</body>
</html>
