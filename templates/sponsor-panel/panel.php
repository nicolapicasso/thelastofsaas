<?php
/**
 * Sponsor Panel Main Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es" data-page-id="<?= uniqid('sp_', true) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <!-- bfcache detection - only reloads when restored from bfcache -->
    <script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // Page was restored from bfcache - reload to get fresh data
            var pageId = document.documentElement.getAttribute('data-page-id');
            var reloadKey = 'bfcache_reload_' + pageId;
            // Only reload once per page instance to prevent loops
            if (!sessionStorage.getItem(reloadKey)) {
                sessionStorage.setItem(reloadKey, '1');
                window.location.reload();
            }
        }
    });
    </script>
    <title>Panel Sponsor - <?= htmlspecialchars($sponsor['name']) ?></title>

    <!-- Fonts - TLOS Brand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ============================================
           TLOS SPONSOR PANEL - Brand Stylesheet
           ============================================ */
        :root {
            --bg-dark: #000000;
            --bg-navy: #030925;
            --bg-card: #0a0a0a;
            --text-light: #FFFFFF;
            --text-grey: #86868B;
            --border-color: rgba(255, 255, 255, 0.1);
            --success-color: #10B981;
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

        .sidebar-header {
            text-align: center;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
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
        }

        .brand-logo a {
            color: inherit;
            text-decoration: none;
        }

        .brand-logo a:hover {
            color: var(--text-light);
        }

        .sponsor-logo {
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
            background: var(--text-light);
            color: var(--bg-dark);
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
            background: var(--text-light);
            border-color: var(--text-light);
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

        /* Main Content */
        .panel-main {
            margin-left: 280px;
            padding: 3rem;
            min-height: 100vh;
            width: calc(100% - 280px);
            max-width: calc(100% - 280px);
        }

        .panel-header {
            margin-bottom: 3rem;
        }

        .panel-header h1 {
            font-size: clamp(32px, 4vw, 48px);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin-bottom: 0.5rem;
        }

        .panel-header p {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .panel-header p strong {
            color: var(--text-light);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.primary { background: var(--text-light); color: var(--bg-dark); }
        .stat-icon.success { background: var(--success-color); color: var(--bg-dark); }
        .stat-icon.info { background: var(--info-color); color: var(--text-light); }

        .stat-value {
            display: block;
            font-family: var(--font-accent);
            font-size: 32px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        /* Sections */
        .panel-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .section-header h2 {
            font-size: 14px;
            font-family: var(--font-mono);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-header h2 i {
            color: var(--text-grey);
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
            padding: 1.5rem;
            text-align: center;
            position: relative;
            transition: var(--transition);
        }

        .match-card:hover {
            border-color: var(--success-color);
        }

        .match-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin: 0 auto 1rem;
        }

        .match-logo-placeholder {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--text-grey);
            font-size: 1.5rem;
        }

        .match-info strong {
            display: block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .match-info small {
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            text-transform: uppercase;
        }

        .match-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--success-color);
            color: var(--bg-dark);
            font-family: var(--font-mono);
            font-size: 9px;
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
            gap: 1.25rem;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .selection-item:hover {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .selection-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .selection-logo-placeholder {
            width: 40px;
            height: 40px;
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
        }

        .selection-info small {
            font-family: var(--font-mono);
            font-size: 10px;
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

        /* CTA */
        .panel-cta {
            text-align: center;
            padding: 3rem 2rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 11px;
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

        .btn-outline {
            background: transparent;
            border-color: var(--text-light);
            color: var(--text-light);
        }

        .btn-outline:hover {
            background: var(--text-light);
            color: var(--bg-dark);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 10px;
        }

        .btn-lg {
            padding: 1.25rem 2.5rem;
            font-size: 14px;
        }

        .btn-block {
            display: flex;
            width: 100%;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 6rem 2rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--text-grey);
            margin-bottom: 1.5rem;
        }

        .empty-state h2 {
            font-size: 24px;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--text-grey);
            max-width: 400px;
            margin: 0 auto;
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

            .stats-grid {
                grid-template-columns: 1fr;
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
                <?php if (!empty($sponsor['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="" class="sponsor-logo">
                <?php endif; ?>
                <h2><?= htmlspecialchars($sponsor['name']) ?></h2>
                <span class="badge">SPONSOR</span>
            </div>

            <nav class="sidebar-nav">
                <a href="/sponsor/panel" class="nav-item active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <?php if ($currentEvent): ?>
                <a href="/sponsor/empresas/<?= $currentEvent['id'] ?>" class="nav-item">
                    <i class="fas fa-building"></i> Ver Empresas
                </a>
                <a href="/sponsor/matches/<?= $currentEvent['id'] ?>" class="nav-item">
                    <i class="fas fa-heart"></i> Mis Matches
                    <?php if (count($matches) > 0): ?>
                        <span class="nav-badge"><?= count($matches) ?></span>
                    <?php endif; ?>
                </a>
                <a href="/sponsor/codigos/<?= $currentEvent['id'] ?>" class="nav-item">
                    <i class="fas fa-ticket-alt"></i> Mis Codigos
                </a>
                <a href="/sponsor/invitados/<?= $currentEvent['id'] ?>" class="nav-item">
                    <i class="fas fa-users"></i> Mis Invitados
                </a>
                <a href="/sponsor/mensajes/<?= $currentEvent['id'] ?>" class="nav-item">
                    <i class="fas fa-envelope"></i> Mensajes
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <a href="/sponsor/logout" class="btn btn-outline btn-block">
                    <i class="fas fa-sign-out-alt"></i> CERRAR SESION
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="panel-main">
            <header class="panel-header">
                <h1>BIENVENIDO</h1>
                <?php if ($currentEvent): ?>
                    <?php if (count($events ?? []) > 1): ?>
                    <select class="event-selector" onchange="window.location.href='/sponsor/empresas/' + this.value" style="background: #0a0a0a; border: 1px solid rgba(255,255,255,0.1); color: #fff; font-family: 'Roboto Mono', monospace; font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.5rem 1rem; cursor: pointer; margin-top: 0.5rem;">
                        <?php foreach ($events as $evt): ?>
                        <option value="<?= $evt['id'] ?>" <?= $evt['id'] == $currentEvent['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($evt['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php else: ?>
                    <p>EVENTO ACTUAL: <strong><?= htmlspecialchars(strtoupper($currentEvent['name'])) ?></strong></p>
                    <?php endif; ?>
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
                        <div class="stat-icon primary">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?= $stats['total_selections'] ?? 0 ?> / <?= $stats['max_selections'] ?? 10 ?></span>
                            <span class="stat-label">EMPRESAS SELECCIONADAS</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?= $stats['total_matches'] ?? 0 ?></span>
                            <span class="stat-label">MATCHES MUTUOS</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-value"><?= $stats['remaining_selections'] ?? 10 ?></span>
                            <span class="stat-label">SELECCIONES DISPONIBLES</span>
                        </div>
                    </div>
                </div>

                <!-- Matches Section -->
                <?php if (!empty($matches)): ?>
                <section class="panel-section">
                    <div class="section-header">
                        <h2><i class="fas fa-heart"></i> TUS MATCHES</h2>
                        <a href="/sponsor/matches/<?= $currentEvent['id'] ?>" class="btn btn-outline btn-sm">VER TODOS</a>
                    </div>
                    <div class="matches-grid">
                        <?php foreach (array_slice($matches, 0, 4) as $match): ?>
                            <div class="match-card">
                                <div class="match-badge"><i class="fas fa-check"></i> MATCH</div>
                                <?php if (!empty($match['company_logo'])): ?>
                                    <img src="<?= htmlspecialchars($match['company_logo']) ?>" alt="" class="match-logo">
                                <?php else: ?>
                                    <div class="match-logo-placeholder">
                                        <i class="fas fa-building"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="match-info">
                                    <strong><?= htmlspecialchars($match['company_name'] ?? '') ?></strong>
                                    <small><?= htmlspecialchars($match['company_sector'] ?? 'SIN SECTOR') ?></small>
                                </div>
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
                                <?php if (!empty($selection['logo_url'])): ?>
                                    <img src="<?= htmlspecialchars($selection['logo_url']) ?>" alt="" class="selection-logo">
                                <?php else: ?>
                                    <div class="selection-logo-placeholder">
                                        <i class="fas fa-building"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="selection-info">
                                    <strong><?= htmlspecialchars($selection['name'] ?? '') ?></strong>
                                    <small><?= htmlspecialchars($selection['sector'] ?? 'SIN SECTOR') ?></small>
                                </div>
                                <div class="selection-status">
                                    <?php if (!empty($selection['is_mutual'])): ?>
                                        <span class="badge badge-success"><i class="fas fa-heart"></i> MATCH</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">PENDIENTE</span>
                                    <?php endif; ?>
                                </div>
                                <a href="/sponsor/empresas/<?= $currentEvent['id'] ?>/<?= $selection['company_id'] ?? $selection['id'] ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- CTA -->
                <section class="panel-cta">
                    <a href="/sponsor/empresas/<?= $currentEvent['id'] ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-search"></i> EXPLORAR EMPRESAS
                    </a>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
