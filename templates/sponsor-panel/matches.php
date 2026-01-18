<?php
/**
 * Sponsor Panel - Matches Template
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
            <a href="/sponsor/empresas/<?= $event['id'] ?>" class="nav-item">
                <i class="fas fa-building"></i> Ver Empresas
            </a>
            <a href="/sponsor/matches/<?= $event['id'] ?>" class="nav-item active">
                <i class="fas fa-heart"></i> Mis Matches
                <?php if (count($matches) > 0): ?>
                    <span class="nav-badge"><?= count($matches) ?></span>
                <?php endif; ?>
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
            <h1><i class="fas fa-heart"></i> Tus Matches</h1>
            <p class="text-muted"><?= htmlspecialchars($event['name']) ?></p>
        </header>

        <?php if (empty($matches)): ?>
            <div class="empty-state">
                <i class="fas fa-heart-broken"></i>
                <h2>Aun no tienes matches</h2>
                <p>Cuando una empresa que has seleccionado tambien te seleccione, aparecera aqui como match mutuo.</p>
                <a href="/sponsor/empresas/<?= $event['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-search"></i> Explorar empresas
                </a>
            </div>
        <?php else: ?>
            <div class="matches-info">
                <div class="info-card">
                    <i class="fas fa-info-circle"></i>
                    <p>Estos son tus matches mutuos. Ambas partes se han seleccionado, por lo que os pondremos en contacto para coordinar una reunion.</p>
                </div>
            </div>

            <div class="matches-list">
                <?php foreach ($matches as $match): ?>
                    <div class="match-item">
                        <div class="match-logo">
                            <?php if ($match['company_logo']): ?>
                                <img src="<?= htmlspecialchars($match['company_logo']) ?>" alt="<?= htmlspecialchars($match['company_name']) ?>">
                            <?php else: ?>
                                <div class="logo-placeholder"><i class="fas fa-building"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="match-info">
                            <h3><?= htmlspecialchars($match['company_name']) ?></h3>
                            <?php if ($match['company_sector']): ?>
                                <span class="sector"><?= htmlspecialchars($match['company_sector']) ?></span>
                            <?php endif; ?>
                            <?php if ($match['matched_at']): ?>
                                <small class="match-date">Match: <?= date('d/m/Y', strtotime($match['matched_at'])) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="match-status">
                            <span class="status-badge"><i class="fas fa-check-circle"></i> Match</span>
                        </div>
                        <div class="match-actions">
                            <a href="/sponsor/empresas/<?= $event['id'] ?>/<?= $match['company_id'] ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-eye"></i> Ver empresa
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($meetings)): ?>
                <section class="meetings-section">
                    <h2><i class="fas fa-calendar-check"></i> Reuniones programadas</h2>
                    <div class="meetings-list">
                        <?php foreach ($meetings as $meeting): ?>
                            <div class="meeting-item">
                                <div class="meeting-time">
                                    <span class="time"><?= date('H:i', strtotime($meeting['start_time'])) ?></span>
                                    <span class="date"><?= date('d M', strtotime($meeting['meeting_date'])) ?></span>
                                </div>
                                <div class="meeting-info">
                                    <strong><?= htmlspecialchars($meeting['company_name']) ?></strong>
                                    <?php if ($meeting['location']): ?>
                                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($meeting['location']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="meeting-duration">
                                    <i class="fas fa-clock"></i> <?= $meeting['duration'] ?? 30 ?> min
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
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

.sidebar-nav { flex: 1; }
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
.nav-item:hover { background: rgba(255,255,255,0.1); color: white; }
.nav-item.active { background: var(--primary-color); color: white; }
.nav-item i { width: 20px; text-align: center; }
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
    background: var(--bg-secondary, #F3F4F6);
    padding: 2rem;
    overflow-y: auto;
}

.panel-header {
    margin-bottom: 2rem;
}
.panel-header h1 {
    font-size: 1.75rem;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.panel-header h1 i {
    color: var(--success-color);
}

.matches-info {
    margin-bottom: 2rem;
}
.info-card {
    display: flex;
    gap: 1rem;
    background: var(--info-light, #DBEAFE);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    color: var(--info-color, #1D4ED8);
}
.info-card i { font-size: 1.25rem; flex-shrink: 0; margin-top: 0.2rem; }
.info-card p { margin: 0; }

.matches-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.match-item {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-left: 4px solid var(--success-color);
}

.match-logo {
    width: 60px;
    height: 60px;
    flex-shrink: 0;
}
.match-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 8px;
}
.match-logo .logo-placeholder {
    width: 100%;
    height: 100%;
    background: var(--bg-secondary);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-size: 1.5rem;
}

.match-info {
    flex: 1;
}
.match-info h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1.1rem;
}
.match-info .sector {
    display: inline-block;
    background: var(--bg-secondary);
    padding: 0.2rem 0.6rem;
    border-radius: 4px;
    font-size: 0.8rem;
    color: var(--text-secondary);
}
.match-info .match-date {
    display: block;
    margin-top: 0.25rem;
    color: var(--text-muted);
}

.match-status .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: var(--success-color);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.meetings-section {
    margin-top: 3rem;
}
.meetings-section h2 {
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}
.meetings-section h2 i {
    color: var(--primary-color);
}

.meetings-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.meeting-item {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.meeting-time {
    text-align: center;
    min-width: 60px;
}
.meeting-time .time {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
}
.meeting-time .date {
    font-size: 0.8rem;
    color: var(--text-secondary);
}
.meeting-info {
    flex: 1;
}
.meeting-info strong {
    display: block;
    margin-bottom: 0.25rem;
}
.meeting-info span {
    font-size: 0.9rem;
    color: var(--text-secondary);
}
.meeting-duration {
    color: var(--text-secondary);
    font-size: 0.9rem;
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
    .panel-layout { grid-template-columns: 1fr; }
    .panel-sidebar { display: none; }
}
@media (max-width: 600px) {
    .match-item { flex-wrap: wrap; }
    .match-status { order: -1; width: 100%; margin-bottom: 0.5rem; }
}
</style>
