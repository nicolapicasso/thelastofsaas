<?php
/**
 * Clients List Template
 * We're Sinapsis CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Clientes</h1>
        <p>Gestiona el directorio de clientes</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/clients/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Cliente
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
    <form method="GET" action="/admin/clients" class="filters-form">
        <div class="filter-group">
            <label>Industria</label>
            <select name="industry" onchange="this.form.submit()">
                <option value="">Todas las industrias</option>
                <?php foreach ($industries as $ind): ?>
                    <option value="<?= htmlspecialchars($ind) ?>" <?= ($currentIndustry ?? '') === $ind ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ind) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (!empty($currentIndustry)): ?>
            <a href="/admin/clients" class="btn btn-outline btn-sm">Limpiar filtros</a>
        <?php endif; ?>
    </form>
</div>

<!-- Clients Grid -->
<div class="card">
    <?php if (empty($clients)): ?>
        <div class="empty-state">
            <i class="fas fa-building"></i>
            <h3>No hay clientes</h3>
            <p>Empieza a agregar clientes al directorio</p>
            <a href="/admin/clients/create" class="btn btn-primary">Nuevo Cliente</a>
        </div>
    <?php else: ?>
        <div class="clients-grid">
            <?php foreach ($clients as $client): ?>
                <div class="client-card <?= !$client['is_active'] ? 'inactive' : '' ?>">
                    <div class="client-logo">
                        <?php if (!empty($client['logo'])): ?>
                            <img src="<?= htmlspecialchars($client['logo']) ?>" alt="<?= htmlspecialchars($client['name']) ?>">
                        <?php else: ?>
                            <div class="logo-placeholder">
                                <i class="fas fa-building"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="client-info">
                        <h3><?= htmlspecialchars($client['name']) ?></h3>
                        <?php if (!empty($client['industry'])): ?>
                            <div class="client-industry">
                                <i class="fas fa-industry"></i>
                                <?= htmlspecialchars($client['industry']) ?>
                            </div>
                        <?php endif; ?>
                        <div class="client-meta">
                            <?php if ($client['is_featured']): ?>
                                <span class="badge badge-warning"><i class="fas fa-star"></i> Destacado</span>
                            <?php endif; ?>
                            <?php if (!$client['is_active']): ?>
                                <span class="badge badge-secondary">Inactivo</span>
                            <?php endif; ?>
                            <?php if (isset($client['cases_count']) && $client['cases_count'] > 0): ?>
                                <span class="badge badge-info"><?= $client['cases_count'] ?> casos</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="client-actions">
                        <a href="/admin/clients/<?= $client['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if (!empty($client['website'])): ?>
                            <a href="<?= htmlspecialchars($client['website']) ?>" target="_blank" class="btn btn-sm btn-outline" title="Ver web">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        <?php endif; ?>
                        <a href="/clientes/<?= $client['slug'] ?>" target="_blank" class="btn btn-sm btn-outline" title="Ver pagina">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="/admin/clients/<?= $client['id'] ?>/delete" class="inline-form"
                              onsubmit="return confirm('¿Eliminar este cliente?')">
                            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
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
.clients-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
}

.client-card {
    display: flex;
    flex-direction: column;
    background: white;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all var(--transition);
}

.client-card:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
}

.client-card.inactive {
    opacity: 0.6;
}

.client-logo {
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-gray-50);
    padding: var(--spacing-md);
}

.client-logo img {
    max-width: 100%;
    max-height: 70px;
    object-fit: contain;
}

.client-logo .logo-placeholder {
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

.client-info {
    padding: var(--spacing-lg);
    flex: 1;
}

.client-info h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-xs);
}

.client-industry {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-sm);
}

.client-industry i {
    margin-right: var(--spacing-xs);
    color: var(--color-gray-400);
}

.client-meta {
    display: flex;
    gap: var(--spacing-xs);
    flex-wrap: wrap;
}

.client-actions {
    display: flex;
    gap: var(--spacing-xs);
    padding: var(--spacing-md) var(--spacing-lg);
    border-top: 1px solid var(--color-gray-100);
    background: var(--color-gray-50);
}

.client-actions .inline-form {
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
