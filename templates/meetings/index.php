<?php
/**
 * Meeting Blocks Index
 * Lists all meeting blocks for an event
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

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .blocks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .block-card {
            background: var(--bg-card);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .block-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15);
        }

        .block-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1.5rem;
        }

        .block-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .block-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .block-meta span {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .block-body {
            padding: 1.5rem;
        }

        .block-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat {
            text-align: center;
            padding: 0.75rem;
            background: var(--bg);
            border-radius: 8px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .block-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-outline {
            background: white;
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-outline:hover {
            background: var(--bg);
            border-color: var(--primary);
            color: var(--primary);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--text);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .blocks-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="page-header">
        <h1><i class="fas fa-handshake"></i> Reuniones</h1>
        <p><?= htmlspecialchars($event['name']) ?></p>
    </header>

    <div class="container">
        <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al evento
        </a>

        <?php if (empty($blocks)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>No hay bloques de reuniones</h3>
                <p>Aún no se han programado bloques de reuniones para este evento.</p>
            </div>
        <?php else: ?>
            <div class="blocks-grid">
                <?php foreach ($blocks as $block): ?>
                    <div class="block-card">
                        <div class="block-header">
                            <h2><?= htmlspecialchars($block['name']) ?></h2>
                            <div class="block-meta">
                                <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($block['event_date'])) ?></span>
                                <span><i class="fas fa-clock"></i> <?= substr($block['start_time'], 0, 5) ?> - <?= substr($block['end_time'], 0, 5) ?></span>
                                <?php if (!empty($block['location'])): ?>
                                    <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($block['location']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="block-body">
                            <div class="block-stats">
                                <div class="stat">
                                    <div class="stat-value"><?= (int) ($block['stats']['total_slots'] ?? 0) ?></div>
                                    <div class="stat-label">Total</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-value"><?= (int) ($block['stats']['assigned_slots'] ?? 0) ?></div>
                                    <div class="stat-label">Asignados</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-value"><?= $block['slot_duration'] ?> min</div>
                                    <div class="stat-label">Duración</div>
                                </div>
                            </div>
                            <div class="block-actions">
                                <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>/reuniones/horario/<?= $block['id'] ?>" class="btn btn-outline">
                                    <i class="fas fa-table"></i> Horario
                                </a>
                                <a href="/eventos/<?= htmlspecialchars($event['slug']) ?>/reuniones/pantalla/<?= $block['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-tv"></i> Pantalla
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
