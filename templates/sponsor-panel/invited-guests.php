<?php
/**
 * Sponsor Panel - Invited Guests Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Invitados - <?= htmlspecialchars($sponsor['name']) ?></title>

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

        /* Summary */
        .summary-bar {
            display: flex;
            gap: 2rem;
            padding: 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .summary-item i {
            font-size: 1.25rem;
            color: var(--text-grey);
        }

        .summary-item span {
            font-family: var(--font-mono);
            font-size: 12px;
        }

        .summary-item strong {
            font-size: 18px;
            margin-right: 0.25rem;
        }

        /* Guests Table */
        .guests-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
        }

        .guests-table th,
        .guests-table td {
            padding: 1.25rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .guests-table th {
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-grey);
            background: rgba(255, 255, 255, 0.02);
        }

        .guests-table td {
            font-size: 13px;
        }

        .guest-name {
            font-weight: 600;
        }

        .guest-email {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
        }

        .guest-company {
            font-size: 12px;
            color: var(--text-grey);
        }

        .invite-code {
            font-family: var(--font-mono);
            font-size: 11px;
            background: rgba(255, 255, 255, 0.05);
            padding: 0.25rem 0.5rem;
            border: 1px solid var(--border-color);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
        }

        .status-badge.confirmed { background: var(--success-color); color: var(--bg-dark); }
        .status-badge.pending { background: var(--warning-color); color: var(--bg-dark); }
        .status-badge.used { background: var(--info-color); color: var(--text-light); }
        .status-badge.cancelled { background: var(--error-color); color: var(--text-light); }

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

        .btn-outline {
            background: transparent;
            border-color: var(--text-light);
            color: var(--text-light);
        }

        .btn-outline:hover {
            background: var(--text-light);
            color: var(--bg-dark);
        }

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
            .summary-bar { flex-wrap: wrap; }
            .guests-table { display: block; overflow-x: auto; }
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
                <a href="/sponsor/matches/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-heart"></i> Mis Matches
                </a>
                <a href="/sponsor/codigos/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-ticket-alt"></i> Mis Codigos
                </a>
                <a href="/sponsor/invitados/<?= $event['id'] ?>" class="nav-item active">
                    <i class="fas fa-users"></i> Mis Invitados
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
                <h1>MIS INVITADOS</h1>
                <p>EVENTO: <strong><?= htmlspecialchars(strtoupper($event['name'])) ?></strong></p>
            </header>

            <?php
            // Count by status
            $confirmed = 0;
            $pending = 0;
            $used = 0;
            $cancelled = 0;
            foreach ($tickets as $ticket) {
                switch ($ticket['status']) {
                    case 'confirmed': $confirmed++; break;
                    case 'pending':
                    case 'pending_payment': $pending++; break;
                    case 'used': $used++; break;
                    case 'cancelled':
                    case 'refunded': $cancelled++; break;
                }
            }
            ?>

            <!-- Summary -->
            <div class="summary-bar">
                <div class="summary-item">
                    <i class="fas fa-users"></i>
                    <span><strong><?= count($tickets) ?></strong> TOTAL</span>
                </div>
                <div class="summary-item">
                    <i class="fas fa-check-circle" style="color: var(--success-color)"></i>
                    <span><strong><?= $confirmed ?></strong> CONFIRMADOS</span>
                </div>
                <div class="summary-item">
                    <i class="fas fa-clock" style="color: var(--warning-color)"></i>
                    <span><strong><?= $pending ?></strong> PENDIENTES</span>
                </div>
                <div class="summary-item">
                    <i class="fas fa-user-check" style="color: var(--info-color)"></i>
                    <span><strong><?= $used ?></strong> CHECK-IN</span>
                </div>
            </div>

            <?php if (empty($tickets)): ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h2>SIN INVITADOS REGISTRADOS</h2>
                    <p>Todavia no hay asistentes registrados con tus codigos de invitacion. Comparte tus codigos para invitar personas al evento.</p>
                    <a href="/sponsor/codigos/<?= $event['id'] ?>" class="btn btn-outline" style="margin-top: 2rem;">
                        <i class="fas fa-ticket-alt"></i> VER MIS CODIGOS
                    </a>
                </div>
            <?php else: ?>
                <table class="guests-table">
                    <thead>
                        <tr>
                            <th>Asistente</th>
                            <th>Empresa</th>
                            <th>Codigo Usado</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td>
                                    <div class="guest-name"><?= htmlspecialchars($ticket['attendee_name']) ?></div>
                                    <div class="guest-email"><?= htmlspecialchars($ticket['attendee_email']) ?></div>
                                </td>
                                <td>
                                    <?php if (!empty($ticket['attendee_company'])): ?>
                                        <div class="guest-company"><?= htmlspecialchars($ticket['attendee_company']) ?></div>
                                        <?php if (!empty($ticket['attendee_position'])): ?>
                                            <small style="color: var(--text-grey);"><?= htmlspecialchars($ticket['attendee_position']) ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: var(--text-grey);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="invite-code"><?= htmlspecialchars($ticket['invite_code'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match($ticket['status']) {
                                        'confirmed' => 'confirmed',
                                        'used' => 'used',
                                        'pending', 'pending_payment' => 'pending',
                                        default => 'cancelled'
                                    };
                                    $statusText = match($ticket['status']) {
                                        'confirmed' => 'Confirmado',
                                        'used' => 'Check-in',
                                        'pending' => 'Pendiente',
                                        'pending_payment' => 'Pago pendiente',
                                        'cancelled' => 'Cancelado',
                                        'refunded' => 'Reembolsado',
                                        default => ucfirst($ticket['status'])
                                    };
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td style="font-family: var(--font-mono); font-size: 11px;">
                                    <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
