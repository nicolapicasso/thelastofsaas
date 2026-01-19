<?php
/**
 * Company Panel - Sponsors List Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver SaaS - <?= htmlspecialchars($event['name'] ?? '') ?></title>

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

        .btn-block { width: 100%; }
        .btn-sm { padding: 0.5rem 1rem; font-size: 11px; }

        /* Main Content */
        .panel-main {
            margin-left: 280px;
            padding: 2rem 3rem;
            min-height: 100vh;
            width: calc(100% - 280px);
            max-width: calc(100% - 280px);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .page-header h1 {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .counter-box {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            text-align: center;
        }

        .counter-box .number {
            font-family: var(--font-accent);
            font-size: 24px;
            font-weight: 700;
        }

        .counter-box .label {
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-grey);
        }

        /* Sponsors Grid */
        .sponsors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .sponsor-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            transition: var(--transition);
            position: relative;
        }

        .sponsor-card:hover {
            border-color: var(--primary-color);
        }

        .sponsor-card.selected {
            border-color: var(--primary-color);
            background: rgba(5, 150, 105, 0.1);
        }

        .selected-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--text-light);
            color: var(--bg-dark);
            padding: 0.25rem 0.75rem;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sponsor-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            background: var(--text-light);
            padding: 0.5rem;
            margin-bottom: 1rem;
        }

        .sponsor-card h3 {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .sponsor-level {
            display: inline-block;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.25rem 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            margin-bottom: 0.75rem;
        }

        .sponsor-description {
            font-size: 13px;
            color: var(--text-grey);
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .sponsor-actions {
            display: flex;
            gap: 0.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--text-grey);
            margin-bottom: 1.5rem;
        }

        .empty-state h2 {
            font-size: 18px;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-grey);
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
    </style>
</head>
<body>
    <div class="panel-layout">
        <!-- Sidebar -->
        <aside class="panel-sidebar">
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
            </nav>

            <div class="sidebar-footer">
                <a href="/empresa/logout" class="btn btn-outline btn-sm btn-block">
                    <i class="fas fa-sign-out-alt"></i> CERRAR SESION
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="panel-main">
            <div class="page-header">
                <h1>SAAS DISPONIBLES</h1>
                <div class="counter-box">
                    <div class="number"><?= count($selections ?? []) ?> / <?= $maxSelections ?? 10 ?></div>
                    <div class="label">SELECCIONADOS</div>
                </div>
            </div>

            <?php if (empty($sponsors)): ?>
                <div class="empty-state">
                    <i class="fas fa-rocket"></i>
                    <h2>NO HAY SAAS DISPONIBLES</h2>
                    <p>Actualmente no hay SaaS participando en este evento.</p>
                </div>
            <?php else: ?>
                <div class="sponsors-grid">
                    <?php
                    $selectedIds = array_column($selections ?? [], 'sponsor_id');
                    foreach ($sponsors as $sponsor):
                        $isSelected = in_array($sponsor['id'], $selectedIds);
                    ?>
                        <div class="sponsor-card <?= $isSelected ? 'selected' : '' ?>">
                            <?php if ($isSelected): ?>
                                <div class="selected-badge"><i class="fas fa-check"></i> SELECCIONADO</div>
                            <?php endif; ?>

                            <?php if (!empty($sponsor['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="" class="sponsor-logo">
                            <?php endif; ?>

                            <h3><?= htmlspecialchars($sponsor['name'] ?? '') ?></h3>

                            <?php if (!empty($sponsor['level'])): ?>
                                <span class="sponsor-level"><?= strtoupper($sponsor['level']) ?></span>
                            <?php endif; ?>

                            <?php if (!empty($sponsor['description'])): ?>
                                <p class="sponsor-description"><?= htmlspecialchars(mb_substr($sponsor['description'], 0, 100)) ?>...</p>
                            <?php endif; ?>

                            <div class="sponsor-actions">
                                <a href="/empresa/sponsors/<?= $event['id'] ?>/<?= $sponsor['id'] ?>" class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> VER
                                </a>
                                <?php if (!$isSelected): ?>
                                    <button type="button" class="btn btn-primary btn-sm btn-select" data-sponsor="<?= $sponsor['id'] ?>">
                                        <i class="fas fa-plus"></i> SELECCIONAR
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-outline btn-sm btn-unselect" data-sponsor="<?= $sponsor['id'] ?>">
                                        <i class="fas fa-minus"></i> QUITAR
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
    document.querySelectorAll('.btn-select').forEach(btn => {
        btn.addEventListener('click', async function() {
            const sponsorId = this.dataset.sponsor;
            try {
                const response = await fetch('/empresa/seleccionar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `sponsor_id=${sponsorId}&event_id=<?= $event['id'] ?>&_csrf_token=<?= htmlspecialchars($csrf_token ?? '') ?>`
                });

                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Response not JSON:', text);
                    alert('Error del servidor. Revisa la consola para mas detalles.');
                    return;
                }

                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error al seleccionar');
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Error de conexion');
            }
        });
    });

    document.querySelectorAll('.btn-unselect').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Quitar seleccion?')) return;
            const sponsorId = this.dataset.sponsor;
            try {
                const response = await fetch('/empresa/deseleccionar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `sponsor_id=${sponsorId}&event_id=<?= $event['id'] ?>&_csrf_token=<?= htmlspecialchars($csrf_token ?? '') ?>`
                });

                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Response not JSON:', text);
                    alert('Error del servidor. Revisa la consola para mas detalles.');
                    return;
                }

                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Error al quitar seleccion');
                }
            } catch (err) {
                console.error('Error:', err);
                alert('Error de conexion');
            }
        });
    });
    </script>
</body>
</html>
