<?php
/**
 * Sponsor Panel - Messages Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes - <?= htmlspecialchars($event['name']) ?></title>

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
            display: flex;
            min-height: 100vh;
        }

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
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .panel-header h1 i {
            color: var(--info-color);
        }

        .panel-header p {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.15em;
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

        .conversation-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }

        .conversation-item:hover {
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateX(4px);
        }

        .conversation-item.unread {
            border-left: 4px solid var(--info-color);
        }

        .conversation-logo {
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

        .conversation-info { flex: 1; }

        .conversation-info h3 {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .conversation-preview {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
            margin-top: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .conversation-meta {
            text-align: right;
        }

        .conversation-time {
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            display: block;
            margin-bottom: 0.5rem;
        }

        .unread-badge {
            display: inline-block;
            background: var(--info-color);
            color: var(--text-light);
            font-family: var(--font-mono);
            font-size: 10px;
            font-weight: 700;
            padding: 0.25rem 0.5rem;
        }

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

        @media (max-width: 1200px) {
            .panel-sidebar { display: none; }
            .panel-main {
                margin-left: 0;
                padding: 1.5rem;
                width: 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .conversation-item { flex-wrap: wrap; }
            .conversation-meta { order: -1; width: 100%; text-align: left; margin-bottom: 0.5rem; }
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
                <a href="/sponsor/panel" class="nav-item">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="/sponsor/empresas/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-building"></i> Ver Empresas
                </a>
                <a href="/sponsor/matches/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-heart"></i> Mis Matches
                </a>
                <a href="/sponsor/codigos/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-ticket-alt"></i> Mis Codigos
                </a>
                <a href="/sponsor/invitados/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-users"></i> Mis Invitados
                </a>
                <a href="/sponsor/mensajes/<?= $event['id'] ?>" class="nav-item active">
                    <i class="fas fa-envelope"></i> Mensajes
                    <?php if ($unreadCount > 0): ?>
                        <span class="nav-badge"><?= $unreadCount ?></span>
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
                <h1><i class="fas fa-envelope"></i> MENSAJES</h1>
                <p><?= htmlspecialchars(strtoupper($event['name'])) ?></p>
            </header>

            <?php if (empty($inbox)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h2>NO HAY MENSAJES</h2>
                    <p>Aun no tienes conversaciones. Explora las empresas y enviales un mensaje para empezar.</p>
                    <a href="/sponsor/empresas/<?= $event['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-search"></i> EXPLORAR EMPRESAS
                    </a>
                </div>
            <?php else: ?>
                <div class="conversation-list">
                    <?php foreach ($inbox as $conversation): ?>
                        <a href="/sponsor/mensajes/<?= $event['id'] ?>/<?= $conversation['other_party_id'] ?>" class="conversation-item <?= $conversation['unread_count'] > 0 ? 'unread' : '' ?>">
                            <?php if (!empty($conversation['other_party_logo'])): ?>
                                <img src="<?= htmlspecialchars($conversation['other_party_logo']) ?>" alt="" class="conversation-logo">
                            <?php else: ?>
                                <div class="logo-placeholder"><i class="fas fa-building"></i></div>
                            <?php endif; ?>
                            <div class="conversation-info">
                                <h3><?= htmlspecialchars($conversation['other_party_name']) ?></h3>
                                <p class="conversation-preview">
                                    <?php if ($conversation['last_message_is_mine']): ?>
                                        <span style="color: var(--text-grey);">Tu: </span>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($conversation['last_message_preview']) ?>
                                </p>
                            </div>
                            <div class="conversation-meta">
                                <span class="conversation-time"><?= date('d/m/Y H:i', strtotime($conversation['last_message_at'])) ?></span>
                                <?php if ($conversation['unread_count'] > 0): ?>
                                    <span class="unread-badge"><?= $conversation['unread_count'] ?> NUEVO<?= $conversation['unread_count'] > 1 ? 'S' : '' ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
