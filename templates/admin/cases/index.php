<?php
/**
 * Success Cases List Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Casos de Éxito</h1>
        <p>Gestiona los casos de éxito de clientes</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/cases/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Caso
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Filters -->
<?php if (!empty($industries)): ?>
    <div class="card filters-card">
        <form method="GET" action="/admin/cases" class="filters-form">
            <div class="filter-group">
                <label>Industria</label>
                <select name="industry" onchange="this.form.submit()">
                    <option value="">Todas las industrias</option>
                    <?php foreach ($industries as $industry): ?>
                        <option value="<?= htmlspecialchars($industry) ?>" <?= $currentIndustry === $industry ? 'selected' : '' ?>>
                            <?= htmlspecialchars($industry) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($currentIndustry): ?>
                <a href="/admin/cases" class="btn btn-outline btn-sm">Limpiar filtro</a>
            <?php endif; ?>
        </form>
    </div>
<?php endif; ?>

<!-- Cases Grid -->
<div class="card">
    <?php if (empty($cases)): ?>
        <div class="empty-state">
            <i class="fas fa-trophy"></i>
            <h3>No hay casos de éxito</h3>
            <p>Añade tu primer caso de éxito</p>
            <a href="/admin/cases/create" class="btn btn-primary">Crear Caso</a>
        </div>
    <?php else: ?>
        <div class="cases-grid">
            <?php foreach ($cases as $case): ?>
                <div class="case-card <?= ($case['status'] ?? '') === 'published' ? '' : 'inactive' ?>">
                    <div class="case-image">
                        <?php if (!empty($case['featured_image'])): ?>
                            <img src="<?= htmlspecialchars($case['featured_image']) ?>" alt="<?= htmlspecialchars($case['title'] ?? '') ?>">
                        <?php else: ?>
                            <div class="case-image-placeholder">
                                <i class="fas fa-building"></i>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($case['is_featured'])): ?>
                            <span class="featured-badge"><i class="fas fa-star"></i></span>
                        <?php endif; ?>
                    </div>
                    <div class="case-content">
                        <div class="case-header">
                            <?php if (!empty($case['client_logo'])): ?>
                                <img src="<?= htmlspecialchars($case['client_logo']) ?>" alt="" class="company-logo">
                            <?php endif; ?>
                            <div>
                                <h3><?= htmlspecialchars($case['title'] ?? '') ?></h3>
                                <?php if (!empty($case['client_name'])): ?>
                                    <span class="client-name"><?= htmlspecialchars($case['client_name']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($case['client_industry'])): ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($case['client_industry']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (($case['status'] ?? '') !== 'published'): ?>
                            <span class="badge badge-secondary"><?= ucfirst($case['status'] ?? 'borrador') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="case-actions">
                        <a href="/admin/cases/<?= $case['id'] ?>/edit" class="btn btn-sm btn-outline">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form method="POST" action="/admin/cases/<?= $case['id'] ?>/delete" class="inline-form"
                              onsubmit="return confirm('¿Eliminar este caso de éxito?')">
                            <input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
                            <button type="submit" class="btn btn-sm btn-outline btn-danger">
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
.cases-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
}

.case-card {
    background-color: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--color-gray-200);
    transition: all var(--transition);
}

.case-card:hover {
    box-shadow: var(--shadow-md);
}

.case-card.inactive {
    opacity: 0.6;
}

.case-image {
    position: relative;
    height: 160px;
    background-color: var(--color-gray-100);
    overflow: hidden;
}

.case-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.case-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-gray-300);
    font-size: 48px;
}

.featured-badge {
    position: absolute;
    top: var(--spacing-sm);
    right: var(--spacing-sm);
    background-color: var(--color-warning);
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.case-content {
    padding: var(--spacing-md);
}

.case-header {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-sm);
}

.company-logo {
    width: 40px;
    height: 40px;
    object-fit: contain;
    border-radius: var(--radius-sm);
    background-color: var(--color-gray-50);
}

.case-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--color-dark);
    margin-bottom: 4px;
}

.client-name {
    font-size: 12px;
    color: var(--color-gray-500);
    display: block;
    margin-bottom: 4px;
}


.case-actions {
    display: flex;
    gap: var(--spacing-xs);
    padding: var(--spacing-md);
    border-top: 1px solid var(--color-gray-100);
    background-color: var(--color-gray-50);
}

.case-actions .btn {
    flex: 1;
}

.case-actions form {
    flex-shrink: 0;
}
</style>
