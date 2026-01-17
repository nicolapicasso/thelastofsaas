<?php
/**
 * Tools List Template
 * We're Sinapsis CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Herramientas</h1>
        <p>Gestiona las herramientas y tecnologias que utilizamos</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/tools/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Herramienta
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Filters -->
<?php if (!empty($categories)): ?>
<div class="card filters-card">
    <form method="GET" action="/admin/tools" class="filters-form">
        <div class="filter-group">
            <label>Categoria</label>
            <select name="category" onchange="this.form.submit()">
                <option value="">Todas las categorias</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($currentCategory ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (!empty($currentCategory)): ?>
            <a href="/admin/tools" class="btn btn-outline btn-sm">Limpiar filtros</a>
        <?php endif; ?>
    </form>
</div>
<?php endif; ?>

<!-- Tools Grid -->
<div class="card">
    <?php if (empty($tools)): ?>
        <div class="empty-state">
            <i class="fas fa-tools"></i>
            <h3>No hay herramientas</h3>
            <p>Empieza a agregar las herramientas y tecnologias</p>
            <a href="/admin/tools/create" class="btn btn-primary">Nueva Herramienta</a>
        </div>
    <?php else: ?>
        <div class="tools-grid">
            <?php foreach ($tools as $tool): ?>
                <div class="tool-card <?= empty($tool['is_active']) ? 'inactive' : '' ?>">
                    <div class="tool-logo">
                        <?php if (!empty($tool['logo'])): ?>
                            <img src="<?= htmlspecialchars($tool['logo']) ?>" alt="<?= htmlspecialchars($tool['title']) ?>">
                        <?php else: ?>
                            <div class="logo-placeholder">
                                <i class="fas fa-puzzle-piece"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="tool-info">
                        <h3><?= htmlspecialchars($tool['title']) ?></h3>
                        <?php if (!empty($tool['subtitle'])): ?>
                            <p class="tool-subtitle"><?= htmlspecialchars($tool['subtitle']) ?></p>
                        <?php endif; ?>
                        <div class="tool-meta">
                            <?php if (!empty($tool['category_name'])): ?>
                                <span class="badge badge-outline"><?= htmlspecialchars($tool['category_name']) ?></span>
                            <?php endif; ?>
                            <?php if ($tool['is_featured']): ?>
                                <span class="badge badge-warning"><i class="fas fa-star"></i></span>
                            <?php endif; ?>
                            <?php if (empty($tool['is_active'])): ?>
                                <span class="badge badge-secondary">Borrador</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="tool-actions">
                        <a href="/admin/tools/<?= $tool['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/herramientas/<?= $tool['slug'] ?>" target="_blank" class="btn btn-sm btn-outline" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="/admin/tools/<?= $tool['id'] ?>/delete" class="inline-form"
                              onsubmit="return confirm('¿Eliminar esta herramienta?')">
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
.tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
}

.tool-card {
    display: flex;
    flex-direction: column;
    background: white;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all var(--transition);
}

.tool-card:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
}

.tool-card.inactive {
    opacity: 0.6;
}

.tool-logo {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-gray-50);
    padding: var(--spacing-md);
}

.tool-logo img {
    max-width: 100%;
    max-height: 50px;
    object-fit: contain;
}

.tool-logo .logo-placeholder {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    color: var(--color-gray-400);
    font-size: 20px;
}

.tool-info {
    padding: var(--spacing-md) var(--spacing-lg);
    flex: 1;
}

.tool-info h3 {
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-xs);
}

.tool-subtitle {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-sm);
}

.tool-meta {
    display: flex;
    gap: var(--spacing-xs);
    flex-wrap: wrap;
}

.tool-actions {
    display: flex;
    gap: var(--spacing-xs);
    padding: var(--spacing-sm) var(--spacing-lg);
    border-top: 1px solid var(--color-gray-100);
    background: var(--color-gray-50);
}

.tool-actions .inline-form {
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
