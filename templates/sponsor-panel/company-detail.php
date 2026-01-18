<?php
/**
 * Sponsor Panel - Company Detail Template
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
            <a href="/sponsor/empresas/<?= $event['id'] ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver a empresas
            </a>
        </header>

        <div class="company-detail-card">
            <?php if ($isMatch): ?>
                <div class="detail-badge match"><i class="fas fa-heart"></i> Match mutuo</div>
            <?php elseif ($isSelected): ?>
                <div class="detail-badge selected"><i class="fas fa-check"></i> Seleccionada</div>
            <?php endif; ?>

            <div class="detail-header">
                <div class="detail-logo">
                    <?php if ($company['logo_url']): ?>
                        <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="<?= htmlspecialchars($company['name']) ?>">
                    <?php else: ?>
                        <div class="logo-placeholder"><i class="fas fa-building"></i></div>
                    <?php endif; ?>
                </div>
                <div class="detail-info">
                    <h1><?= htmlspecialchars($company['name']) ?></h1>
                    <?php if ($company['sector']): ?>
                        <span class="sector-badge"><?= htmlspecialchars($company['sector']) ?></span>
                    <?php endif; ?>
                    <?php if ($company['website']): ?>
                        <a href="<?= htmlspecialchars($company['website']) ?>" target="_blank" class="website-link">
                            <i class="fas fa-globe"></i> <?= htmlspecialchars(parse_url($company['website'], PHP_URL_HOST)) ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($company['description']): ?>
                <div class="detail-section">
                    <h3>Sobre la empresa</h3>
                    <p><?= nl2br(htmlspecialchars($company['description'])) ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($company['employees']) || !empty($company['revenue'])): ?>
                <div class="detail-section">
                    <h3>Datos</h3>
                    <div class="data-grid">
                        <?php if ($company['employees']): ?>
                            <div class="data-item">
                                <i class="fas fa-users"></i>
                                <span><?= htmlspecialchars($company['employees']) ?> empleados</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($company['revenue']): ?>
                            <div class="data-item">
                                <i class="fas fa-chart-line"></i>
                                <span><?= htmlspecialchars($company['revenue']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($saasUsage)): ?>
                <div class="detail-section">
                    <h3>Software que utilizan</h3>
                    <div class="saas-grid">
                        <?php foreach ($saasUsage as $saas): ?>
                            <div class="saas-item">
                                <?php if ($saas['logo_url']): ?>
                                    <img src="<?= htmlspecialchars($saas['logo_url']) ?>" alt="<?= htmlspecialchars($saas['name']) ?>">
                                <?php endif; ?>
                                <span><?= htmlspecialchars($saas['name']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="detail-actions">
                <?php if (!$isSelected): ?>
                    <button type="button" class="btn btn-primary btn-lg btn-select" data-company="<?= $company['id'] ?>" data-event="<?= $event['id'] ?>">
                        <i class="fas fa-plus"></i> Seleccionar empresa
                    </button>
                <?php elseif (!$isMatch): ?>
                    <button type="button" class="btn btn-danger btn-lg btn-unselect" data-company="<?= $company['id'] ?>" data-event="<?= $event['id'] ?>">
                        <i class="fas fa-minus"></i> Quitar seleccion
                    </button>
                <?php else: ?>
                    <div class="match-message">
                        <i class="fas fa-heart"></i>
                        <p>Esta empresa tambien te ha seleccionado. Os pondremos en contacto pronto.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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
.sidebar-footer {
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.panel-main {
    background: var(--bg-secondary, #F3F4F6);
    padding: 2rem;
    overflow-y: auto;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    text-decoration: none;
    margin-bottom: 1.5rem;
}
.back-link:hover { color: var(--primary-color); }

.company-detail-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    max-width: 800px;
    position: relative;
}

.detail-badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}
.detail-badge.selected { background: var(--primary-color); color: white; }
.detail-badge.match { background: var(--success-color); color: white; }

.detail-header {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}
.detail-logo {
    width: 120px;
    height: 120px;
    flex-shrink: 0;
}
.detail-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 12px;
}
.detail-logo .logo-placeholder {
    width: 100%;
    height: 100%;
    background: var(--bg-secondary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--text-muted);
}
.detail-info h1 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}
.sector-badge {
    display: inline-block;
    background: var(--bg-secondary);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}
.website-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-color);
    text-decoration: none;
}

.detail-section {
    margin-bottom: 1.5rem;
}
.detail-section h3 {
    font-size: 1rem;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.detail-section p { line-height: 1.7; color: var(--text-primary); }

.data-grid {
    display: flex;
    gap: 2rem;
}
.data-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
}
.data-item i { color: var(--primary-color); }

.saas-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.saas-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--bg-secondary);
    padding: 0.5rem 1rem;
    border-radius: 8px;
}
.saas-item img {
    width: 24px;
    height: 24px;
    object-fit: contain;
}

.detail-actions {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
    text-align: center;
}

.match-message {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--success-light, #D1FAE5);
    padding: 1.5rem;
    border-radius: 12px;
    color: var(--success-color);
}
.match-message i { font-size: 2rem; }
.match-message p { margin: 0; text-align: left; }

@media (max-width: 992px) {
    .panel-layout { grid-template-columns: 1fr; }
    .panel-sidebar { display: none; }
}
@media (max-width: 600px) {
    .detail-header { flex-direction: column; text-align: center; }
    .detail-logo { margin: 0 auto; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectBtn = document.querySelector('.btn-select');
    const unselectBtn = document.querySelector('.btn-unselect');

    if (selectBtn) {
        selectBtn.addEventListener('click', function() {
            const companyId = this.dataset.company;
            const eventId = this.dataset.event;

            fetch('/sponsor/api/select', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
    }

    if (unselectBtn) {
        unselectBtn.addEventListener('click', function() {
            if (!confirm('Quitar seleccion?')) return;

            const companyId = this.dataset.company;
            const eventId = this.dataset.event;

            fetch('/sponsor/api/unselect', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
    }
});
</script>
