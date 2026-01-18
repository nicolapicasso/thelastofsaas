<?php
/**
 * Company Panel Main Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Empresa - <?= htmlspecialchars($company['name'] ?? '') ?></title>

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

        .nav-badge {
            margin-left: auto;
            background: var(--success-color);
            color: var(--bg-dark);
            font-size: 10px;
            padding: 0.2rem 0.5rem;
            font-weight: 700;
        }

        .sidebar-footer {
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: var(--transition);
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

        .btn-primary {
            background: var(--primary-color);
            color: var(--text-light);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: #047857;
            border-color: #047857;
        }

        .btn-block { width: 100%; }
        .btn-sm { padding: 0.5rem 1rem; font-size: 11px; }
        .btn-lg { padding: 1rem 2rem; font-size: 13px; }

        /* Main Content */
        .panel-main {
            margin-left: 280px;
            padding: 2rem 3rem;
            min-height: 100vh;
            width: calc(100% - 280px);
            max-width: calc(100% - 280px);
        }

        .panel-header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .panel-header h1 {
            font-size: 28px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .panel-header p {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: var(--transition);
        }

        .stat-card:hover {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 1.5rem;
        }

        .stat-icon.green { background: var(--primary-color); }
        .stat-icon.emerald { background: var(--success-color); }
        .stat-icon.blue { background: var(--info-color); }

        .stat-value {
            display: block;
            font-family: var(--font-accent);
            font-size: 28px;
            font-weight: 700;
        }

        .stat-label {
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-grey);
        }

        /* Panel Sections */
        .panel-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .section-header h2 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
        }

        .section-header h2 i {
            color: var(--primary-color);
        }

        /* Matches Grid */
        .matches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
        }

        .match-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            padding: 1.25rem;
            text-align: center;
            position: relative;
            transition: var(--transition);
        }

        .match-card:hover {
            border-color: var(--success-color);
        }

        .match-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            background: var(--text-light);
            padding: 0.5rem;
            margin: 0 auto 1rem;
        }

        .match-logo-placeholder {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: var(--text-grey);
        }

        .match-info strong {
            display: block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .match-info small {
            font-size: 11px;
            color: var(--text-grey);
        }

        .match-badge {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: var(--success-color);
            color: var(--bg-dark);
            font-family: var(--font-mono);
            font-size: 9px;
            font-weight: 700;
            padding: 0.25rem 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* Selections List */
        .selections-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .selection-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .selection-item:hover {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .selection-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
            background: var(--text-light);
            padding: 0.25rem;
        }

        .selection-logo-placeholder {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-grey);
        }

        .selection-info {
            flex: 1;
        }

        .selection-info strong {
            display: block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .selection-info small {
            font-size: 11px;
            color: var(--text-grey);
        }

        .selection-status .badge-success {
            background: var(--success-color);
            color: var(--bg-dark);
        }

        .selection-status .badge-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-grey);
        }

        .panel-cta {
            text-align: center;
            padding: 3rem 2rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--text-grey);
            margin-bottom: 1.5rem;
        }

        .empty-state h2 {
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-grey);
            font-size: 14px;
        }

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
    </style>
</head>
<body>
    <div class="panel-layout">
        <!-- Sidebar -->
        <aside class="panel-sidebar">
            <div class="sidebar-header">
                <?php if ($company['logo_url'] ?? null): ?>
                    <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="" class="company-logo">
                <?php endif; ?>
                <h2><?= htmlspecialchars($company['name']) ?></h2>
                <span class="badge">EMPRESA</span>
            </div>

            <nav class="sidebar-nav">
                <a href="/empresa/panel" class="nav-item active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <?php if ($currentEvent): ?>
                <a href="/empresa/sponsors/<?= $currentEvent['id'] ?>" class="nav-item">
                    <i class="fas fa-rocket"></i> Ver Sponsors
                </a>
                <a href="/empresa/matches/<?= $currentEvent['id'] ?>" class="nav-item">
                    <i class="fas fa-heart"></i> Mis Matches
                    <?php if (count($matches) > 0): ?>
                        <span class="nav-badge"><?= count($matches) ?></span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <a href="/empresa/logout" class="btn btn-outline btn-sm btn-block">
                    <i class="fas fa-sign-out-alt"></i> CERRAR SESION
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="panel-main">
            <header class="panel-header">
                <h1>BIENVENIDO, <?= strtoupper(htmlspecialchars($company['name'])) ?></h1>
                <?php if ($currentEvent): ?>
                    <p>Evento actual: <?= htmlspecialchars($currentEvent['name']) ?></p>
                <?php endif; ?>
            </header>

            <?php if (!$currentEvent): ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h2>NO HAY EVENTOS ACTIVOS</h2>
                    <p>Actualmente no participas en ningun evento activo. Te notificaremos cuando haya uno disponible.</p>
                </div>
            <?php else: ?>
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?= $stats['total_selections'] ?? 0 ?> / <?= $stats['max_selections'] ?? 5 ?></span>
                            <span class="stat-label">SPONSORS SELECCIONADOS</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon emerald">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?= $stats['total_matches'] ?? 0 ?></span>
                            <span class="stat-label">MATCHES MUTUOS</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?= $stats['remaining_selections'] ?? 0 ?></span>
                            <span class="stat-label">SELECCIONES DISPONIBLES</span>
                        </div>
                    </div>
                </div>

                <!-- Matches Section -->
                <?php if (!empty($matches)): ?>
                <section class="panel-section">
                    <div class="section-header">
                        <h2><i class="fas fa-heart"></i> TUS MATCHES</h2>
                        <a href="/empresa/matches/<?= $currentEvent['id'] ?>" class="btn btn-outline btn-sm">VER TODOS</a>
                    </div>
                    <div class="matches-grid">
                        <?php foreach (array_slice($matches, 0, 4) as $match): ?>
                            <div class="match-card">
                                <?php if ($match['logo_url'] ?? null): ?>
                                    <img src="<?= htmlspecialchars($match['logo_url']) ?>" alt="" class="match-logo">
                                <?php else: ?>
                                    <div class="match-logo-placeholder">
                                        <i class="fas fa-rocket"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="match-info">
                                    <strong><?= htmlspecialchars($match['name']) ?></strong>
                                    <small><?= htmlspecialchars($match['tagline'] ?? '') ?></small>
                                </div>
                                <span class="match-badge"><i class="fas fa-check"></i> MATCH</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Recent Selections -->
                <?php if (!empty($selections)): ?>
                <section class="panel-section">
                    <div class="section-header">
                        <h2><i class="fas fa-list"></i> TUS SELECCIONES</h2>
                    </div>
                    <div class="selections-list">
                        <?php foreach ($selections as $selection): ?>
                            <div class="selection-item">
                                <?php if ($selection['logo_url'] ?? null): ?>
                                    <img src="<?= htmlspecialchars($selection['logo_url']) ?>" alt="" class="selection-logo">
                                <?php else: ?>
                                    <div class="selection-logo-placeholder">
                                        <i class="fas fa-rocket"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="selection-info">
                                    <strong><?= htmlspecialchars($selection['name']) ?></strong>
                                    <small><?= htmlspecialchars($selection['tagline'] ?? 'Sin descripcion') ?></small>
                                </div>
                                <div class="selection-status">
                                    <?php if ($selection['is_mutual'] ?? false): ?>
                                        <span class="badge badge-success"><i class="fas fa-heart"></i> MATCH</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">PENDIENTE</span>
                                    <?php endif; ?>
                                </div>
                                <a href="/empresa/sponsors/<?= $currentEvent['id'] ?>/<?= $selection['id'] ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- CTA -->
                <section class="panel-cta">
                    <a href="/empresa/sponsors/<?= $currentEvent['id'] ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-search"></i> EXPLORAR SPONSORS
                    </a>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
