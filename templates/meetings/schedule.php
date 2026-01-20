<?php
/**
 * Meeting Schedule - Table Format
 * Shows meeting grid by time slots and tables
 * TLOS - The Last of SaaS
 */
$siteLogo = '/assets/images/logo.svg';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #000;
            --success: #10B981;
            --warning: #F59E0B;
            --bg: #f8fafc;
            --bg-card: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* Header */
        .schedule-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1.5rem 2rem;
        }

        .header-content {
            max-width: 1800px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .site-logo {
            height: 40px;
            filter: brightness(0) invert(1);
        }

        .header-info h1 {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .header-info p {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-top: 0.25rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .header-stats {
            display: flex;
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .stat-label {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .header-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-light {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .btn-light:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Schedule Table */
        .schedule-container {
            max-width: 1800px;
            margin: 0 auto;
            padding: 2rem;
            overflow-x: auto;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .schedule-table th,
        .schedule-table td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid var(--border);
            vertical-align: top;
        }

        .schedule-table thead {
            background: var(--primary);
            color: white;
        }

        .schedule-table thead th {
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
            border-color: rgba(255,255,255,0.2);
        }

        .schedule-table thead th:first-child {
            width: 70px;
            background: var(--primary-dark);
        }

        .schedule-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .schedule-table tbody tr:hover {
            background: #f1f5f9;
        }

        .time-cell {
            font-weight: 700;
            font-size: 0.95rem;
            text-align: center;
            background: #f1f5f9;
            white-space: nowrap;
        }

        /* Meeting Cell */
        .meeting-cell {
            min-width: 140px;
            min-height: 80px;
        }

        .meeting-card {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.5rem;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 3px solid var(--primary);
        }

        .meeting-participant {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .participant-logo {
            width: 28px;
            height: 28px;
            border-radius: 4px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid var(--border);
        }

        .participant-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 2px;
        }

        .participant-logo .placeholder {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .participant-info {
            min-width: 0;
            flex: 1;
        }

        .participant-name {
            font-size: 0.8rem;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .participant-role {
            font-size: 0.65rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .meeting-divider {
            height: 1px;
            background: var(--border);
            margin: 0.25rem 0;
        }

        .empty-cell {
            color: var(--text-muted);
            font-size: 0.8rem;
            text-align: center;
            padding: 1rem;
        }

        /* Print styles */
        @media print {
            body {
                background: white;
            }

            .schedule-header {
                background: var(--text) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .header-actions {
                display: none;
            }

            .schedule-container {
                padding: 1rem 0;
            }

            .schedule-table {
                box-shadow: none;
                font-size: 0.85rem;
            }

            .schedule-table thead {
                background: var(--text) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .header-left {
                flex-direction: column;
            }

            .schedule-container {
                padding: 1rem;
            }
        }

        /* Link to room display */
        .view-switch {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .view-switch a {
            padding: 0.5rem 1rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 6px;
            text-decoration: none;
            color: var(--text);
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .view-switch a:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .view-switch a.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="schedule-header">
        <div class="header-content">
            <div class="header-left">
                <img src="<?= $siteLogo ?>" alt="Logo" class="site-logo" onerror="this.style.display='none'">
                <div class="header-info">
                    <h1><?= htmlspecialchars($block['name']) ?></h1>
                    <p>
                        <i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($block['event_date'])) ?>
                        &nbsp;&nbsp;
                        <i class="fas fa-clock"></i> <?= substr($block['start_time'], 0, 5) ?> - <?= substr($block['end_time'], 0, 5) ?>
                        <?php if (!empty($block['location'])): ?>
                        &nbsp;&nbsp;
                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($block['location']) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="header-right">
                <div class="header-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?= count($times) ?></div>
                        <div class="stat-label">Rondas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= $totalRooms ?></div>
                        <div class="stat-label">Mesas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= $block['slot_duration'] ?> min</div>
                        <div class="stat-label">Duración</div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>/reuniones/pantalla/<?= $block['id'] ?>" class="btn btn-light">
                        <i class="fas fa-tv"></i> Pantalla
                    </a>
                    <button onclick="window.print()" class="btn btn-light">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Schedule -->
    <div class="schedule-container">
        <div class="view-switch">
            <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>/reuniones">
                <i class="fas fa-list"></i> Todos los bloques
            </a>
            <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>/reuniones/horario/<?= $block['id'] ?>" class="active">
                <i class="fas fa-table"></i> Vista tabla
            </a>
            <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>/reuniones/pantalla/<?= $block['id'] ?>">
                <i class="fas fa-tv"></i> Vista pantalla
            </a>
        </div>

        <table class="schedule-table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <?php for ($i = 1; $i <= $totalRooms; $i++): ?>
                        <th>Mesa <?= $i ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($times as $time): ?>
                    <tr>
                        <td class="time-cell"><?= $time ?></td>
                        <?php for ($i = 1; $i <= $totalRooms; $i++): ?>
                            <td class="meeting-cell">
                                <?php
                                $meeting = $schedule[$time][$i] ?? null;
                                if ($meeting && $meeting['sponsor_name']):
                                ?>
                                    <div class="meeting-card">
                                        <div class="meeting-participant">
                                            <div class="participant-logo">
                                                <?php if ($meeting['sponsor_logo']): ?>
                                                    <img src="<?= htmlspecialchars($meeting['sponsor_logo']) ?>" alt="" onerror="this.parentElement.innerHTML='<span class=\'placeholder\'><i class=\'fas fa-building\'></i></span>'">
                                                <?php else: ?>
                                                    <span class="placeholder"><i class="fas fa-building"></i></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="participant-info">
                                                <div class="participant-name" title="<?= htmlspecialchars($meeting['sponsor_name']) ?>">
                                                    <?= htmlspecialchars($meeting['sponsor_name']) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="meeting-divider"></div>
                                        <div class="meeting-participant">
                                            <div class="participant-logo">
                                                <?php if ($meeting['company_logo']): ?>
                                                    <img src="<?= htmlspecialchars($meeting['company_logo']) ?>" alt="" onerror="this.parentElement.innerHTML='<span class=\'placeholder\'><i class=\'fas fa-briefcase\'></i></span>'">
                                                <?php else: ?>
                                                    <span class="placeholder"><i class="fas fa-briefcase"></i></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="participant-info">
                                                <div class="participant-name" title="<?= htmlspecialchars($meeting['company_name']) ?>">
                                                    <?= htmlspecialchars($meeting['company_name']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-cell">—</div>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
