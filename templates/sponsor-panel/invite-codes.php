<?php
/**
 * Sponsor Panel - Invite Codes Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Codigos de Invitacion - <?= htmlspecialchars($sponsor['name']) ?></title>

    <!-- Fonts - TLOS Brand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
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
            --warning-color: #F59E0B;
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

        .panel-layout { display: flex; min-height: 100vh; }

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
        }

        .panel-header p {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .panel-header p strong { color: var(--text-light); }

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

        /* Codes Table */
        .codes-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
        }

        .codes-table th,
        .codes-table td {
            padding: 1.25rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .codes-table th {
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-grey);
            background: rgba(255, 255, 255, 0.02);
        }

        .codes-table td {
            font-size: 13px;
        }

        .code-value {
            font-family: var(--font-mono);
            font-size: 14px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.05);
            padding: 0.5rem 1rem;
            display: inline-block;
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: var(--transition);
        }

        .code-value:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--text-light);
        }

        .code-url {
            display: block;
            margin-top: 0.5rem;
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            word-break: break-all;
            cursor: pointer;
            transition: var(--transition);
            padding: 0.25rem 0;
        }

        .code-url:hover {
            color: var(--text-light);
        }

        .code-url i {
            margin-right: 0.25rem;
        }

        .usage-badge {
            font-family: var(--font-mono);
            font-size: 11px;
            padding: 0.25rem 0.75rem;
        }

        .usage-badge.available { background: var(--success-color); color: var(--bg-dark); }
        .usage-badge.limited { background: var(--warning-color); color: var(--bg-dark); }
        .usage-badge.exhausted { background: var(--error-color); color: var(--text-light); }

        .status-badge {
            font-family: var(--font-mono);
            font-size: 10px;
            padding: 0.25rem 0.75rem;
            text-transform: uppercase;
        }

        .status-badge.active { background: var(--success-color); color: var(--bg-dark); }
        .status-badge.inactive { background: rgba(255, 255, 255, 0.1); color: var(--text-grey); }

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

        .btn-sm { padding: 0.5rem 1rem; font-size: 10px; }
        .btn-block { display: flex; width: 100%; }

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
            .panel-sidebar { display: none; }
            .panel-main {
                margin-left: 0;
                padding: 1.5rem;
                width: 100%;
                max-width: 100%;
            }
            .stats-grid { grid-template-columns: 1fr; }
            .codes-table { display: block; overflow-x: auto; }
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
                <a href="/sponsor/codigos/<?= $event['id'] ?>" class="nav-item active">
                    <i class="fas fa-ticket-alt"></i> Mis Codigos
                </a>
                <a href="/sponsor/invitados/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-users"></i> Mis Invitados
                </a>
                <a href="/sponsor/mensajes/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-envelope"></i> Mensajes
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
                <h1>MIS CODIGOS</h1>
                <?php if (count($events ?? []) > 1): ?>
                <select class="event-selector" onchange="window.location.href='/sponsor/codigos/' + this.value" style="background: #0a0a0a; border: 1px solid rgba(255,255,255,0.1); color: #fff; font-family: 'Roboto Mono', monospace; font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.5rem 1rem; cursor: pointer; margin-top: 0.5rem;">
                    <?php foreach ($events as $evt): ?>
                    <option value="<?= $evt['id'] ?>" <?= $evt['id'] == $event['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($evt['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                <p>EVENTO: <strong><?= htmlspecialchars(strtoupper($event['name'])) ?></strong></p>
                <?php endif; ?>
            </header>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?= $overallStats['total_codes'] ?? 0 ?></span>
                        <span class="stat-label">CODIGOS DISPONIBLES</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?= $overallStats['total_uses'] ?? 0 ?></span>
                        <span class="stat-label">VECES USADO</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?= $overallStats['confirmed_tickets'] ?? 0 ?></span>
                        <span class="stat-label">INVITADOS CONFIRMADOS</span>
                    </div>
                </div>
            </div>

            <?php if (empty($codes)): ?>
                <div class="empty-state">
                    <i class="fas fa-ticket-alt"></i>
                    <h2>SIN CODIGOS DE INVITACION</h2>
                    <p>Aun no tienes codigos de invitacion asignados para este evento. Contacta con la organizacion si necesitas codigos.</p>
                </div>
            <?php else: ?>
                <table class="codes-table">
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Descripcion</th>
                            <th>Uso</th>
                            <th>Descuento</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($codes as $code): ?>
                            <?php
                            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
                            $fullUrl = $baseUrl . '/eventos/' . htmlspecialchars($event['slug']) . '/registro?code=' . htmlspecialchars($code['code']);
                            ?>
                            <tr>
                                <td>
                                    <span class="code-value" onclick="copyCode('<?= htmlspecialchars($code['code']) ?>')" title="Click para copiar codigo">
                                        <?= htmlspecialchars($code['code']) ?>
                                    </span>
                                    <span class="code-url" onclick="copyUrl('<?= $fullUrl ?>')" title="Click para copiar URL completa">
                                        <i class="fas fa-link"></i> <?= $fullUrl ?>
                                    </span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($code['description'] ?? 'Codigo de invitacion') ?>
                                </td>
                                <td>
                                    <?php
                                    $maxUses = $code['max_uses'];
                                    $used = $code['times_used'];
                                    $remaining = $maxUses !== null ? ($maxUses - $used) : null;

                                    if ($maxUses === null): ?>
                                        <span class="usage-badge available"><?= $used ?> / ILIMITADO</span>
                                    <?php elseif ($remaining <= 0): ?>
                                        <span class="usage-badge exhausted"><?= $used ?> / <?= $maxUses ?> AGOTADO</span>
                                    <?php elseif ($remaining <= 2): ?>
                                        <span class="usage-badge limited"><?= $used ?> / <?= $maxUses ?></span>
                                    <?php else: ?>
                                        <span class="usage-badge available"><?= $used ?> / <?= $maxUses ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($code['discount_type'] === 'none'): ?>
                                        <span style="color: var(--text-grey);">-</span>
                                    <?php elseif ($code['discount_type'] === 'percentage'): ?>
                                        <?= $code['discount_amount'] ?>%
                                    <?php else: ?>
                                        <?= number_format($code['discount_amount'], 2) ?> &euro;
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($code['active']): ?>
                                        <span class="status-badge active">Activo</span>
                                    <?php else: ?>
                                        <span class="status-badge inactive">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="text-align: center; margin-top: 2rem;">
                    <a href="/sponsor/invitados/<?= $event['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-users"></i> VER MIS INVITADOS
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
    function copyCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            showToast('Codigo copiado: ' + code);
        }).catch(() => {
            prompt('Copia este codigo:', code);
        });
    }

    function copyUrl(url) {
        navigator.clipboard.writeText(url).then(() => {
            showToast('URL copiada al portapapeles');
        }).catch(() => {
            prompt('Copia esta URL:', url);
        });
    }

    function showToast(message) {
        // Remove existing toast if any
        const existingToast = document.querySelector('.copy-toast');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.className = 'copy-toast';
        toast.textContent = message;
        toast.style.cssText = 'position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: var(--success-color); color: var(--bg-dark); padding: 1rem 2rem; font-family: var(--font-mono); font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; z-index: 9999; animation: fadeInOut 2s ease-in-out forwards;';
        document.body.appendChild(toast);

        setTimeout(() => toast.remove(), 2000);
    }
    </script>
    <style>
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateX(-50%) translateY(20px); }
        15% { opacity: 1; transform: translateX(-50%) translateY(0); }
        85% { opacity: 1; transform: translateX(-50%) translateY(0); }
        100% { opacity: 0; transform: translateX(-50%) translateY(-20px); }
    }
    </style>
</body>
</html>
