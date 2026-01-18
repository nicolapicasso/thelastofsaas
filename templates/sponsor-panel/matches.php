<?php
/**
 * Sponsor Panel - Matches Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches - <?= htmlspecialchars($event['name']) ?></title>

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
            --success-color: #10B981;
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
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        .panel-sidebar {
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            width: 280px;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            text-align: center;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
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

        .sidebar-nav { flex: 1; }

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

        .panel-main {
            margin-left: 280px;
            padding: 3rem;
            min-height: 100vh;
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
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .panel-header h1 i {
            color: var(--success-color);
        }

        .panel-header p {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .info-card {
            display: flex;
            gap: 1rem;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid var(--info-color);
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--info-color);
        }

        .info-card i { flex-shrink: 0; margin-top: 0.1rem; }
        .info-card p { margin: 0; line-height: 1.6; }

        .matches-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .match-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-left: 4px solid var(--success-color);
            padding: 1.5rem 2rem;
            transition: var(--transition);
        }

        .match-item:hover {
            border-color: rgba(255, 255, 255, 0.2);
            border-left-color: var(--success-color);
        }

        .match-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .logo-placeholder {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-grey);
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .match-info { flex: 1; }

        .match-info h3 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .match-info .sector {
            display: inline-block;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            padding: 0.2rem 0.6rem;
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            text-transform: uppercase;
        }

        .match-info .match-date {
            display: block;
            margin-top: 0.5rem;
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
        }

        .match-status .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--success-color);
            color: var(--bg-dark);
            padding: 0.5rem 1rem;
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .match-actions {
            margin-left: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-decoration: none;
            border: 2px solid transparent;
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
            background: var(--text-light);
            color: var(--bg-dark);
            border-color: var(--text-light);
        }

        .btn-primary:hover {
            background: transparent;
            color: var(--text-light);
        }

        .btn-block { display: flex; width: 100%; }

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
            margin-bottom: 2rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Meetings Section */
        .meetings-section {
            margin-top: 3rem;
        }

        .meetings-section h2 {
            font-size: 16px;
            font-family: var(--font-mono);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .meetings-section h2 i {
            color: var(--info-color);
        }

        .meeting-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            margin-bottom: 0.75rem;
        }

        .meeting-time {
            text-align: center;
            min-width: 70px;
        }

        .meeting-time .time {
            display: block;
            font-family: var(--font-accent);
            font-size: 24px;
            font-weight: 700;
            color: var(--info-color);
            line-height: 1;
        }

        .meeting-time .date {
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            text-transform: uppercase;
        }

        .meeting-info { flex: 1; }

        .meeting-info strong {
            display: block;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .meeting-info span {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
        }

        .meeting-duration {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
        }

        @media (max-width: 1200px) {
            .panel-layout { grid-template-columns: 1fr; }
            .panel-sidebar { display: none; }
            .panel-main { margin-left: 0; padding: 1.5rem; }
        }

        @media (max-width: 768px) {
            .match-item { flex-wrap: wrap; }
            .match-status { order: -1; width: 100%; margin-bottom: 0.5rem; }
            .match-actions { margin-left: 0; width: 100%; margin-top: 1rem; }
            .match-actions .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="panel-layout">
        <!-- Sidebar -->
        <aside class="panel-sidebar">
            <div class="sidebar-header">
                <?php if (!empty($sponsor['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="" class="sponsor-logo">
                <?php endif; ?>
                <h2><?= htmlspecialchars($sponsor['name']) ?></h2>
                <span class="badge">SPONSOR</span>
            </div>

            <nav class="sidebar-nav">
                <a href="/sponsor/panel" class="nav-item">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="/sponsor/empresas/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-building"></i> Ver Empresas
                </a>
                <a href="/sponsor/matches/<?= $event['id'] ?>" class="nav-item active">
                    <i class="fas fa-heart"></i> Mis Matches
                    <?php if (count($matches ?? []) > 0): ?>
                        <span class="nav-badge"><?= count($matches) ?></span>
                    <?php endif; ?>
                </a>
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
                <h1><i class="fas fa-heart"></i> TUS MATCHES</h1>
                <p><?= htmlspecialchars(strtoupper($event['name'])) ?></p>
            </header>

            <?php if (empty($matches ?? [])): ?>
                <div class="empty-state">
                    <i class="fas fa-heart-broken"></i>
                    <h2>AUN NO TIENES MATCHES</h2>
                    <p>Cuando una empresa que has seleccionado tambien te seleccione, aparecera aqui como match mutuo.</p>
                    <a href="/sponsor/empresas/<?= $event['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-search"></i> EXPLORAR EMPRESAS
                    </a>
                </div>
            <?php else: ?>
                <div class="info-card">
                    <i class="fas fa-info-circle"></i>
                    <p>ESTOS SON TUS MATCHES MUTUOS. AMBAS PARTES SE HAN SELECCIONADO, POR LO QUE OS PONDREMOS EN CONTACTO PARA COORDINAR UNA REUNION.</p>
                </div>

                <div class="matches-list">
                    <?php foreach ($matches as $match): ?>
                        <div class="match-item">
                            <?php if (!empty($match['company_logo'])): ?>
                                <img src="<?= htmlspecialchars($match['company_logo']) ?>" alt="" class="match-logo">
                            <?php else: ?>
                                <div class="logo-placeholder"><i class="fas fa-building"></i></div>
                            <?php endif; ?>
                            <div class="match-info">
                                <h3><?= htmlspecialchars($match['company_name']) ?></h3>
                                <?php if (!empty($match['company_sector'])): ?>
                                    <span class="sector"><?= htmlspecialchars(strtoupper($match['company_sector'])) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($match['matched_at'])): ?>
                                    <small class="match-date">MATCH: <?= date('d/m/Y', strtotime($match['matched_at'])) ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="match-status">
                                <span class="status-badge"><i class="fas fa-check-circle"></i> MATCH</span>
                            </div>
                            <div class="match-actions">
                                <a href="/sponsor/empresas/<?= $event['id'] ?>/<?= $match['company_id'] ?>" class="btn btn-outline">
                                    <i class="fas fa-eye"></i> VER
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($meetings)): ?>
                    <section class="meetings-section">
                        <h2><i class="fas fa-calendar-check"></i> REUNIONES PROGRAMADAS</h2>
                        <div class="meetings-list">
                            <?php foreach ($meetings as $meeting): ?>
                                <div class="meeting-item">
                                    <div class="meeting-time">
                                        <span class="time"><?= date('H:i', strtotime($meeting['start_time'])) ?></span>
                                        <span class="date"><?= strtoupper(date('d M', strtotime($meeting['meeting_date']))) ?></span>
                                    </div>
                                    <div class="meeting-info">
                                        <strong><?= htmlspecialchars($meeting['company_name']) ?></strong>
                                        <?php if (!empty($meeting['location'])): ?>
                                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($meeting['location']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="meeting-duration">
                                        <i class="fas fa-clock"></i> <?= $meeting['duration'] ?? 30 ?> MIN
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
