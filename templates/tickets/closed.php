<?php
/**
 * Ticket Registration Closed Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Cerrado - <?= htmlspecialchars($event['name'] ?? '') ?></title>

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
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Hero Section */
        .closed-hero {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 2rem 0;
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
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--text-light);
        }

        .closed-hero h1 {
            font-size: clamp(24px, 4vw, 36px);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin-bottom: 0.5rem;
        }

        .closed-hero .lead {
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* Content Section */
        .closed-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 0;
        }

        .closed-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 3rem;
            text-align: center;
            max-width: 600px;
        }

        .closed-icon {
            width: 80px;
            height: 80px;
            background: rgba(245, 158, 11, 0.1);
            border: 2px solid var(--warning-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }

        .closed-icon i {
            font-size: 2rem;
            color: var(--warning-color);
        }

        .closed-card h2 {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
        }

        .closed-card p {
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .event-info {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .event-info strong {
            display: block;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .event-info span {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
        }

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
        }

        .btn-outline {
            background: transparent;
            color: var(--text-light);
            border-color: var(--text-light);
        }

        .btn-outline:hover {
            background: var(--text-light);
            color: var(--bg-dark);
        }

        @media (max-width: 600px) {
            .container {
                padding: 0 1rem;
            }

            .closed-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <section class="closed-hero">
        <div class="container">
            <a href="/eventos/<?= htmlspecialchars($event['slug'] ?? '') ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> VOLVER AL EVENTO
            </a>
            <h1><?= htmlspecialchars($event['name'] ?? 'Evento') ?></h1>
            <p class="lead"><?= date('d/m/Y', strtotime($event['start_date'] ?? 'now')) ?> · <?= htmlspecialchars($event['location'] ?? '') ?></p>
        </div>
    </section>

    <section class="closed-content">
        <div class="container">
            <div class="closed-card">
                <div class="closed-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h2>Registro Cerrado</h2>
                <p>
                    <?php if (!empty($event['registration_end']) && strtotime($event['registration_end']) < time()): ?>
                        El periodo de registro para este evento ha finalizado.
                    <?php elseif (!empty($event['registration_start']) && strtotime($event['registration_start']) > time()): ?>
                        El registro para este evento aun no ha comenzado.
                        <?php if (!empty($event['registration_start'])): ?>
                            <br>Abrira el <?= date('d/m/Y a las H:i', strtotime($event['registration_start'])) ?>.
                        <?php endif; ?>
                    <?php else: ?>
                        El registro para este evento no esta disponible en este momento.
                    <?php endif; ?>
                </p>

                <div class="event-info">
                    <strong><?= htmlspecialchars($event['name'] ?? '') ?></strong>
                    <span>
                        <i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($event['start_date'] ?? 'now')) ?>
                        <?php if (!empty($event['location'])): ?>
                            &nbsp;&nbsp;<i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?>
                        <?php endif; ?>
                    </span>
                </div>

                <a href="/eventos/<?= htmlspecialchars($event['slug'] ?? '') ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Ver Detalles del Evento
                </a>
            </div>
        </div>
    </section>
</body>
</html>
