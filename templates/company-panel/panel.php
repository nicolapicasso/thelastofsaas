<?php
/**
 * Company Panel Main Template
 * TLOS - The Last of SaaS
 */
?>

<div class="panel-layout">
    <!-- Sidebar -->
    <aside class="panel-sidebar">
        <div class="sidebar-header">
            <?php if ($company['logo_url'] ?? null): ?>
                <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="" class="company-logo">
            <?php endif; ?>
            <h2><?= htmlspecialchars($company['name']) ?></h2>
            <span class="badge badge-success">Empresa</span>
        </div>

        <nav class="sidebar-nav">
            <a href="/empresa/panel" class="nav-item active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <?php if ($currentEvent): ?>
            <a href="/empresa/sponsors/<?= $currentEvent['id'] ?>" class="nav-item">
                <i class="fas fa-rocket"></i> Ver Sponsors
            </a>
            <a href="/empresa/matches/<?= $currentEvent['id'] ?>" class="nav-item">
                <i class="fas fa-heart"></i> Mis Matches
                <?php if (count($matches) > 0): ?>
                    <span class="nav-badge"><?= count($matches) ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <a href="/empresa/logout" class="btn btn-outline btn-sm btn-block">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesion
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="panel-main">
        <header class="panel-header">
            <h1>Bienvenido, <?= htmlspecialchars($company['name']) ?></h1>
            <?php if ($currentEvent): ?>
                <p class="text-muted">Evento actual: <strong><?= htmlspecialchars($currentEvent['name']) ?></strong></p>
            <?php endif; ?>
        </header>

        <?php if (!$currentEvent): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h2>No hay eventos activos</h2>
                <p>Actualmente no participas en ningun evento activo. Te notificaremos cuando haya uno disponible.</p>
            </div>
        <?php else: ?>
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #059669;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?= $stats['total_selections'] ?? 0 ?> / <?= $stats['max_selections'] ?? 5 ?></span>
                        <span class="stat-label">Sponsors seleccionados</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--success-color);">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?= $stats['total_matches'] ?? 0 ?></span>
                        <span class="stat-label">Matches mutuos</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--info-color);">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-value"><?= $stats['remaining_selections'] ?? 0 ?></span>
                        <span class="stat-label">Selecciones disponibles</span>
                    </div>
                </div>
            </div>

            <!-- Matches Section -->
            <?php if (!empty($matches)): ?>
            <section class="panel-section">
                <div class="section-header">
                    <h2><i class="fas fa-heart"></i> Tus Matches</h2>
                    <a href="/empresa/matches/<?= $currentEvent['id'] ?>" class="btn btn-outline btn-sm">Ver todos</a>
                </div>
                <div class="matches-grid">
                    <?php foreach (array_slice($matches, 0, 4) as $match): ?>
                        <div class="match-card">
                            <?php if ($match['logo_url'] ?? null): ?>
                                <img src="<?= htmlspecialchars($match['logo_url']) ?>" alt="" class="match-logo">
                            <?php else: ?>
                                <div class="match-logo-placeholder">
                                    <i class="fas fa-rocket"></i>
                                </div>
                            <?php endif; ?>
                            <div class="match-info">
                                <strong><?= htmlspecialchars($match['name']) ?></strong>
                                <small><?= htmlspecialchars($match['tagline'] ?? '') ?></small>
                            </div>
                            <span class="match-badge"><i class="fas fa-check-circle"></i> Match</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Recent Selections -->
            <?php if (!empty($selections)): ?>
            <section class="panel-section">
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> Tus Selecciones</h2>
                </div>
                <div class="selections-list">
                    <?php foreach ($selections as $selection): ?>
                        <div class="selection-item">
                            <?php if ($selection['logo_url'] ?? null): ?>
                                <img src="<?= htmlspecialchars($selection['logo_url']) ?>" alt="" class="selection-logo">
                            <?php else: ?>
                                <div class="selection-logo-placeholder">
                                    <i class="fas fa-rocket"></i>
                                </div>
                            <?php endif; ?>
                            <div class="selection-info">
                                <strong><?= htmlspecialchars($selection['name']) ?></strong>
                                <small><?= htmlspecialchars($selection['tagline'] ?? 'Sin descripcion') ?></small>
                            </div>
                            <div class="selection-status">
                                <?php if ($selection['is_mutual'] ?? false): ?>
                                    <span class="badge badge-success"><i class="fas fa-heart"></i> Match</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Pendiente</span>
                                <?php endif; ?>
                            </div>
                            <a href="/empresa/sponsors/<?= $currentEvent['id'] ?>/<?= $selection['id'] ?>" class="btn btn-sm btn-outline">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- CTA -->
            <section class="panel-cta">
                <a href="/empresa/sponsors/<?= $currentEvent['id'] ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-search"></i> Explorar sponsors
                </a>
            </section>
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
    background: #065F46;
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
.company-logo {
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
    background: #059669;
    color: white;
}
.nav-item i {
    width: 20px;
    text-align: center;
}
.nav-badge {
    margin-left: auto;
    background: var(--success-color);
    color: white;
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
}

.sidebar-footer {
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.panel-main {
    background: var(--bg-secondary, #f3f4f6);
    padding: 2rem;
    overflow-y: auto;
}

.panel-header {
    margin-bottom: 2rem;
}
.panel-header h1 {
    font-size: 1.75rem;
    margin-bottom: 0.25rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}
.stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
}
.stat-label {
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.panel-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}
.section-header h2 {
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}
.section-header h2 i {
    color: #059669;
}

.matches-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}
.match-card {
    background: var(--bg-secondary, #f3f4f6);
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    position: relative;
}
.match-logo {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border-radius: 8px;
    margin-bottom: 0.75rem;
}
.match-logo-placeholder {
    width: 60px;
    height: 60px;
    background: var(--border-color);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    color: var(--text-muted);
}
.match-info strong {
    display: block;
    font-size: 0.95rem;
}
.match-info small {
    color: var(--text-secondary);
}
.match-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: var(--success-color);
    color: white;
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
}

.selections-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.selection-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: var(--bg-secondary, #f3f4f6);
    border-radius: 8px;
}
.selection-logo {
    width: 40px;
    height: 40px;
    object-fit: contain;
    border-radius: 6px;
}
.selection-logo-placeholder {
    width: 40px;
    height: 40px;
    background: var(--border-color);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
}
.selection-info {
    flex: 1;
}
.selection-info strong {
    display: block;
}
.selection-info small {
    color: var(--text-secondary);
}

.panel-cta {
    text-align: center;
    padding: 2rem;
}
.btn-primary {
    background: #059669;
    border-color: #059669;
}
.btn-primary:hover {
    background: #047857;
    border-color: #047857;
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

.badge-success {
    background: #059669;
    color: white;
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
