<?php
/**
 * Sponsor Panel - Companies List Template
 * TLOS - The Last of SaaS
 */
?>

<div class="panel-layout">
    <!-- Sidebar -->
    <aside class="panel-sidebar">
        <div class="sidebar-header">
            <?php if ($sponsor['logo_url']): ?>
                <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="" class="sponsor-logo">
            <?php endif; ?>
            <h2><?= htmlspecialchars($sponsor['name']) ?></h2>
            <span class="badge badge-primary">Sponsor</span>
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
            <a href="/sponsor/logout" class="btn btn-outline btn-sm btn-block">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesion
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="panel-main">
        <header class="panel-header">
            <div class="header-top">
                <div>
                    <h1>Empresas</h1>
                    <p class="text-muted"><?= htmlspecialchars($event['name']) ?></p>
                </div>
                <div class="selection-counter">
                    <span class="counter-value"><?= $currentSelections ?> / <?= $maxSelections ?></span>
                    <span class="counter-label">seleccionadas</span>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-bar">
                <form method="GET" class="filters-form" id="filtersForm">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($currentSearch) ?>" placeholder="Buscar empresas...">
                    </div>
                    <select name="sector" onchange="document.getElementById('filtersForm').submit()">
                        <option value="">Todos los sectores</option>
                        <?php foreach ($sectors as $sec): ?>
                            <option value="<?= htmlspecialchars($sec) ?>" <?= $currentSector === $sec ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sec) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="filter" onchange="document.getElementById('filtersForm').submit()">
                        <option value="all" <?= $currentFilter === 'all' ? 'selected' : '' ?>>Todas</option>
                        <option value="available" <?= $currentFilter === 'available' ? 'selected' : '' ?>>Disponibles</option>
                        <option value="selected" <?= $currentFilter === 'selected' ? 'selected' : '' ?>>Seleccionadas</option>
                        <option value="matched" <?= $currentFilter === 'matched' ? 'selected' : '' ?>>Matches</option>
                    </select>
                </form>
            </div>
        </header>

        <?php if (empty($companies)): ?>
            <div class="empty-state">
                <i class="fas fa-building"></i>
                <h2>No hay empresas</h2>
                <p>No se encontraron empresas con los filtros seleccionados.</p>
                <a href="/sponsor/empresas/<?= $event['id'] ?>" class="btn btn-primary">Ver todas</a>
            </div>
        <?php else: ?>
            <div class="companies-grid">
                <?php foreach ($companies as $company): ?>
                    <?php
                    $isSelected = in_array($company['id'], $selectedIds);
                    $isMatched = in_array($company['id'], $matchedIds);
                    ?>
                    <div class="company-card <?= $isSelected ? 'selected' : '' ?> <?= $isMatched ? 'matched' : '' ?>" data-company-id="<?= $company['id'] ?>">
                        <?php if ($isMatched): ?>
                            <div class="card-badge match-badge"><i class="fas fa-heart"></i> Match</div>
                        <?php elseif ($isSelected): ?>
                            <div class="card-badge selected-badge"><i class="fas fa-check"></i> Seleccionada</div>
                        <?php endif; ?>

                        <div class="company-logo">
                            <?php if ($company['logo_url']): ?>
                                <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="<?= htmlspecialchars($company['name']) ?>">
                            <?php else: ?>
                                <div class="logo-placeholder">
                                    <i class="fas fa-building"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="company-info">
                            <h3><?= htmlspecialchars($company['name']) ?></h3>
                            <?php if ($company['sector']): ?>
                                <span class="company-sector"><?= htmlspecialchars($company['sector']) ?></span>
                            <?php endif; ?>
                            <?php if ($company['description']): ?>
                                <p class="company-desc"><?= htmlspecialchars(mb_substr($company['description'], 0, 100)) ?>...</p>
                            <?php endif; ?>
                        </div>

                        <div class="company-actions">
                            <a href="/sponsor/empresas/<?= $event['id'] ?>/<?= $company['id'] ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-eye"></i> Ver detalle
                            </a>
                            <?php if (!$isSelected && $currentSelections < $maxSelections): ?>
                                <button type="button" class="btn btn-primary btn-sm btn-select" data-company="<?= $company['id'] ?>" data-event="<?= $event['id'] ?>">
                                    <i class="fas fa-plus"></i> Seleccionar
                                </button>
                            <?php elseif ($isSelected && !$isMatched): ?>
                                <button type="button" class="btn btn-danger btn-sm btn-unselect" data-company="<?= $company['id'] ?>" data-event="<?= $event['id'] ?>">
                                    <i class="fas fa-minus"></i> Quitar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
.panel-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    min-height: 100vh;
}

.panel-sidebar {
    background: var(--bg-dark, #1F2937);
    color: white;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
}
.sidebar-header {
    text-align: center;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 1.5rem;
}
.sponsor-logo {
    width: 80px;
    height: 80px;
    object-fit: contain;
    background: white;
    border-radius: 12px;
    padding: 0.5rem;
    margin-bottom: 1rem;
}
.sidebar-header h2 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.sidebar-nav {
    flex: 1;
}
.nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s;
    margin-bottom: 0.25rem;
}
.nav-item:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}
.nav-item.active {
    background: var(--primary-color);
    color: white;
}
.nav-item i {
    width: 20px;
    text-align: center;
}

.sidebar-footer {
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.panel-main {
    background: var(--bg-secondary, #F3F4F6);
    padding: 2rem;
    overflow-y: auto;
}

.panel-header {
    margin-bottom: 2rem;
}
.header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}
.panel-header h1 {
    font-size: 1.75rem;
    margin-bottom: 0.25rem;
}

.selection-counter {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.counter-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}
.counter-label {
    font-size: 0.85rem;
    color: var(--text-secondary);
}

.filters-bar {
    background: white;
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.filters-form {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.search-box {
    flex: 1;
    min-width: 200px;
    position: relative;
}
.search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}
.search-box input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
}
.filters-form select {
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    min-width: 150px;
}

.companies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.company-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
}
.company-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.company-card.selected {
    border: 2px solid var(--primary-color);
}
.company-card.matched {
    border: 2px solid var(--success-color);
}

.card-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.selected-badge {
    background: var(--primary-color);
    color: white;
}
.match-badge {
    background: var(--success-color);
    color: white;
}

.company-logo {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
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
    background: var(--bg-secondary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--text-muted);
}

.company-info {
    text-align: center;
    margin-bottom: 1rem;
}
.company-info h3 {
    font-size: 1.1rem;
    margin-bottom: 0.25rem;
}
.company-sector {
    display: inline-block;
    background: var(--bg-secondary);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}
.company-desc {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin: 0;
}

.company-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
}
.empty-state i {
    font-size: 4rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

@media (max-width: 992px) {
    .panel-layout {
        grid-template-columns: 1fr;
    }
    .panel-sidebar {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select company
    document.querySelectorAll('.btn-select').forEach(btn => {
        btn.addEventListener('click', function() {
            const companyId = this.dataset.company;
            const eventId = this.dataset.event;

            fetch('/sponsor/api/select', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `company_id=${companyId}&event_id=${eventId}&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>`
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

            fetch('/sponsor/api/unselect', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `company_id=${companyId}&event_id=${eventId}&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>`
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
