<?php
/**
 * Sponsor Panel - Companies List Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresas - <?= htmlspecialchars($event['name']) ?></title>

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

        .sidebar-footer {
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        /* Main Content */
        .panel-main {
            margin-left: 280px;
            padding: 2rem 3rem;
            min-height: 100vh;
            width: calc(100% - 280px);
            max-width: calc(100% - 280px);
        }

        .panel-header {
            margin-bottom: 3rem;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .panel-header h1 {
            font-size: clamp(36px, 5vw, 48px);
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

        .selection-counter {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
            text-align: center;
        }

        .counter-value {
            display: block;
            font-family: var(--font-accent);
            font-size: 32px;
            font-weight: 700;
        }

        .counter-label {
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        /* Filters */
        .filters-bar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        .filters-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-grey);
        }

        .search-box input,
        .filters-form select {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            font-family: var(--font-mono);
            font-size: 12px;
            transition: var(--transition);
        }

        .filters-form select {
            padding-left: 1rem;
            min-width: 160px;
            cursor: pointer;
        }

        .search-box input:focus,
        .filters-form select:focus {
            outline: none;
            border-color: var(--text-light);
        }

        /* Companies Grid */
        .companies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .company-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 2rem;
            position: relative;
            transition: var(--transition);
        }

        .company-card:hover {
            border-color: rgba(255, 255, 255, 0.3);
        }

        .company-card.selected {
            border-color: var(--text-light);
        }

        .company-card.matched {
            border-color: var(--success-color);
        }

        .card-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            font-family: var(--font-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .selected-badge {
            background: var(--text-light);
            color: var(--bg-dark);
        }

        .match-badge {
            background: var(--success-color);
            color: var(--bg-dark);
        }

        .company-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .company-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--text-grey);
        }

        .company-info {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .company-info h3 {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .company-sector {
            display: inline-block;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            padding: 0.25rem 0.75rem;
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.75rem;
        }

        .company-desc {
            font-size: 13px;
            color: var(--text-grey);
            line-height: 1.6;
            margin: 0;
        }

        .company-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
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

        .btn-danger {
            background: var(--error-color);
            color: var(--text-light);
            border-color: var(--error-color);
        }

        .btn-danger:hover {
            background: transparent;
            color: var(--error-color);
        }

        .btn-sm {
            padding: 0.625rem 1rem;
            font-size: 10px;
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
            margin-top: 3rem;
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
        }

        @media (max-width: 768px) {
            .header-top {
                flex-direction: column;
                gap: 1.5rem;
            }

            .selection-counter {
                width: 100%;
            }

            .companies-grid {
                grid-template-columns: 1fr;
            }
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
                <a href="/sponsor/empresas/<?= $event['id'] ?>" class="nav-item active">
                    <i class="fas fa-building"></i> Ver Empresas
                </a>
                <a href="/sponsor/matches/<?= $event['id'] ?>" class="nav-item">
                    <i class="fas fa-heart"></i> Mis Matches
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
                <div class="header-top">
                    <div>
                        <h1>EMPRESAS</h1>
                        <p><?= htmlspecialchars($event['name']) ?></p>
                    </div>
                    <div class="selection-counter">
                        <span class="counter-value"><?= $currentSelections ?? 0 ?> / <?= $maxSelections ?? 10 ?></span>
                        <span class="counter-label">SELECCIONADAS</span>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-bar">
                    <form method="GET" class="filters-form" id="filtersForm">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($currentSearch ?? '') ?>" placeholder="BUSCAR EMPRESAS...">
                        </div>
                        <select name="sector" onchange="document.getElementById('filtersForm').submit()">
                            <option value="">TODOS LOS SECTORES</option>
                            <?php foreach (($sectors ?? []) as $sec): ?>
                                <option value="<?= htmlspecialchars($sec) ?>" <?= ($currentSector ?? '') === $sec ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(strtoupper($sec)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="filter" onchange="document.getElementById('filtersForm').submit()">
                            <option value="all" <?= ($currentFilter ?? '') === 'all' ? 'selected' : '' ?>>TODAS</option>
                            <option value="available" <?= ($currentFilter ?? '') === 'available' ? 'selected' : '' ?>>DISPONIBLES</option>
                            <option value="selected" <?= ($currentFilter ?? '') === 'selected' ? 'selected' : '' ?>>SELECCIONADAS</option>
                            <option value="matched" <?= ($currentFilter ?? '') === 'matched' ? 'selected' : '' ?>>MATCHES</option>
                        </select>
                    </form>
                </div>
            </header>

            <?php if (empty($companies ?? [])): ?>
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <h2>NO HAY EMPRESAS</h2>
                    <p>No se encontraron empresas con los filtros seleccionados.</p>
                    <a href="/sponsor/empresas/<?= $event['id'] ?>" class="btn btn-primary">VER TODAS</a>
                </div>
            <?php else: ?>
                <div class="companies-grid">
                    <?php foreach ($companies as $company): ?>
                        <?php
                        $isSelected = in_array($company['id'], $selectedIds ?? []);
                        $isMatched = in_array($company['id'], $matchedIds ?? []);
                        ?>
                        <div class="company-card <?= $isSelected ? 'selected' : '' ?> <?= $isMatched ? 'matched' : '' ?>" data-company-id="<?= $company['id'] ?>">
                            <?php if ($isMatched): ?>
                                <div class="card-badge match-badge"><i class="fas fa-heart"></i> MATCH</div>
                            <?php elseif ($isSelected): ?>
                                <div class="card-badge selected-badge"><i class="fas fa-check"></i> SELECCIONADA</div>
                            <?php endif; ?>

                            <div class="company-logo">
                                <?php if (!empty($company['logo_url'])): ?>
                                    <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="<?= htmlspecialchars($company['name']) ?>">
                                <?php else: ?>
                                    <div class="logo-placeholder">
                                        <i class="fas fa-building"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="company-info">
                                <h3><?= htmlspecialchars($company['name']) ?></h3>
                                <?php if (!empty($company['sector'])): ?>
                                    <span class="company-sector"><?= htmlspecialchars($company['sector']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($company['description'])): ?>
                                    <p class="company-desc"><?= htmlspecialchars(mb_substr($company['description'], 0, 100)) ?>...</p>
                                <?php endif; ?>
                            </div>

                            <div class="company-actions">
                                <a href="/sponsor/empresas/<?= $event['id'] ?>/<?= $company['id'] ?>" class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> VER
                                </a>
                                <?php if (!$isSelected && ($currentSelections ?? 0) < ($maxSelections ?? 10)): ?>
                                    <button type="button" class="btn btn-primary btn-sm btn-select" data-company="<?= $company['id'] ?>" data-event="<?= $event['id'] ?>">
                                        <i class="fas fa-plus"></i> SELECCIONAR
                                    </button>
                                <?php elseif ($isSelected && !$isMatched): ?>
                                    <button type="button" class="btn btn-danger btn-sm btn-unselect" data-company="<?= $company['id'] ?>" data-event="<?= $event['id'] ?>">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Select company
        document.querySelectorAll('.btn-select').forEach(btn => {
            btn.addEventListener('click', function() {
                const companyId = this.dataset.company;
                const eventId = this.dataset.event;

                fetch('/sponsor/seleccionar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `company_id=${companyId}&event_id=${eventId}&_csrf_token=<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Error al seleccionar');
                    }
                });
            });
        });

        // Unselect company
        document.querySelectorAll('.btn-unselect').forEach(btn => {
            btn.addEventListener('click', function() {
                const companyId = this.dataset.company;
                const eventId = this.dataset.event;

                if (!confirm('Quitar seleccion?')) return;

                fetch('/sponsor/deseleccionar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `company_id=${companyId}&event_id=${eventId}&_csrf_token=<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Error al quitar');
                    }
                });
            });
        });
    });
    </script>
</body>
</html>
