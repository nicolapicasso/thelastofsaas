<?php
/**
 * Partners List Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Partners</h1>
        <p>Gestiona el directorio de partners y agencias colaboradoras</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/partners/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Partner
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Filters -->
<div class="card filters-card">
    <form method="GET" action="/admin/partners" class="filters-form">
        <div class="filter-group">
            <label>Tipo</label>
            <select name="type" onchange="this.form.submit()">
                <option value="">Todos los tipos</option>
                <?php foreach ($partnerTypes as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $currentType === $value ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>País</label>
            <select name="country" onchange="this.form.submit()">
                <option value="">Todos los países</option>
                <?php foreach ($countries as $c): ?>
                    <option value="<?= htmlspecialchars($c['country']) ?>" <?= $currentCountry === $c['country'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['country']) ?> (<?= $c['partner_count'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($currentType || $currentCountry): ?>
            <a href="/admin/partners" class="btn btn-outline btn-sm">Limpiar filtros</a>
        <?php endif; ?>
    </form>
</div>

<!-- Partners Grid -->
<div class="card">
    <?php if (empty($partners)): ?>
        <div class="empty-state">
            <i class="fas fa-handshake"></i>
            <h3>No hay partners</h3>
            <p>Empieza a construir tu red de partners</p>
            <a href="/admin/partners/create" class="btn btn-primary">Nuevo Partner</a>
        </div>
    <?php else: ?>
        <div class="partners-grid">
            <?php foreach ($partners as $partner): ?>
                <div class="partner-card <?= !$partner['is_active'] ? 'inactive' : '' ?>">
                    <div class="partner-logo">
                        <?php if (!empty($partner['logo'])): ?>
                            <img src="<?= htmlspecialchars($partner['logo']) ?>" alt="<?= htmlspecialchars($partner['name']) ?>">
                        <?php else: ?>
                            <div class="logo-placeholder">
                                <i class="fas fa-building"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="partner-info">
                        <h3><?= htmlspecialchars($partner['name']) ?></h3>
                        <div class="partner-location">
                            <?php if (!empty($partner['city']) || !empty($partner['country'])): ?>
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars(trim(($partner['city'] ?? '') . ', ' . ($partner['country'] ?? ''), ', ')) ?>
                            <?php endif; ?>
                        </div>
                        <div class="partner-meta">
                            <span class="badge badge-<?= $partner['partner_type'] === 'tech_partner' ? 'primary' : 'info' ?>">
                                <?= $partnerTypes[$partner['partner_type']] ?? $partner['partner_type'] ?>
                            </span>
                            <?php if ($partner['is_certified']): ?>
                                <span class="badge badge-success"><i class="fas fa-certificate"></i> Certificado</span>
                            <?php endif; ?>
                            <?php if ($partner['is_featured']): ?>
                                <span class="badge badge-warning"><i class="fas fa-star"></i> Destacado</span>
                            <?php endif; ?>
                            <?php if (!$partner['is_active']): ?>
                                <span class="badge badge-secondary">Inactivo</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="partner-actions">
                        <a href="/admin/partners/<?= $partner['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if (!empty($partner['website'])): ?>
                            <a href="<?= htmlspecialchars($partner['website']) ?>" target="_blank" class="btn btn-sm btn-outline" title="Ver web">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        <?php endif; ?>
                        <a href="/partners/<?= $partner['slug'] ?>" target="_blank" class="btn btn-sm btn-outline" title="Ver página">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="/admin/partners/<?= $partner['id'] ?>/delete" class="inline-form"
                              onsubmit="return confirm('¿Eliminar este partner?')">
                            <input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
                            <button type="submit" class="btn btn-sm btn-outline btn-danger" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.partners-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
}

.partner-card {
    display: flex;
    flex-direction: column;
    background: white;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all var(--transition);
}

.partner-card:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
}

.partner-card.inactive {
    opacity: 0.6;
}

.partner-logo {
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-gray-50);
    padding: var(--spacing-md);
}

.partner-logo img {
    max-width: 100%;
    max-height: 70px;
    object-fit: contain;
}

.partner-logo .logo-placeholder {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    color: var(--color-gray-400);
    font-size: 24px;
}

.partner-info {
    padding: var(--spacing-lg);
    flex: 1;
}

.partner-info h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-xs);
}

.partner-location {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-sm);
}

.partner-location i {
    margin-right: var(--spacing-xs);
    color: var(--color-gray-400);
}

.partner-meta {
    display: flex;
    gap: var(--spacing-xs);
    flex-wrap: wrap;
}

.partner-actions {
    display: flex;
    gap: var(--spacing-xs);
    padding: var(--spacing-md) var(--spacing-lg);
    border-top: 1px solid var(--color-gray-100);
    background: var(--color-gray-50);
}

.partner-actions .inline-form {
    margin: 0;
}

.filters-card {
    margin-bottom: var(--spacing-lg);
    padding: var(--spacing-md) var(--spacing-lg);
}

.filters-form {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.filter-group label {
    font-weight: 500;
    color: var(--color-gray-600);
    margin: 0;
}

.filter-group select {
    min-width: 180px;
}
</style>
