<?php
/**
 * Sitemap Management Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><i class="fas fa-sitemap"></i> Sitemap XML</h1>
        <p>Gestiona el sitemap para motores de búsqueda</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/seo" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-info-circle"></i> Estado del Sitemap</h3>
    </div>
    <div class="card-body">
        <div class="sitemap-status">
            <?php if ($sitemapExists): ?>
                <div class="status-indicator status-success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Sitemap generado</strong>
                        <p>Última actualización: <?= $sitemapLastModified ?></p>
                    </div>
                </div>
                <div class="sitemap-actions">
                    <a href="/sitemap.xml" target="_blank" class="btn btn-outline">
                        <i class="fas fa-external-link-alt"></i> Ver Sitemap
                    </a>
                    <form method="POST" action="/admin/seo/generate-sitemap" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync"></i> Regenerar Sitemap
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="status-indicator status-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Sitemap no encontrado</strong>
                        <p>Genera el sitemap para mejorar la indexación</p>
                    </div>
                </div>
                <form method="POST" action="/admin/seo/generate-sitemap">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-magic"></i> Generar Sitemap
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Contenido incluido en el Sitemap</h3>
    </div>
    <div class="card-body">
        <ul class="sitemap-includes">
            <li><i class="fas fa-check text-success"></i> Páginas estáticas (Home, Funcionalidades, Precios, etc.)</li>
            <li><i class="fas fa-check text-success"></i> Páginas dinámicas publicadas</li>
            <li><i class="fas fa-check text-success"></i> Posts del blog</li>
            <li><i class="fas fa-check text-success"></i> Casos de éxito</li>
            <li><i class="fas fa-check text-success"></i> Artículos de ayuda</li>
            <li><i class="fas fa-check text-success"></i> Integraciones</li>
            <li><i class="fas fa-check text-success"></i> Partners</li>
        </ul>
        <p class="help-text">
            <i class="fas fa-info-circle"></i>
            El sitemap se genera automáticamente con todo el contenido publicado.
            Recuerda regenerarlo cuando añadas nuevo contenido importante.
        </p>
    </div>
</div>

<style>
.sitemap-status {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: var(--spacing-lg);
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-lg);
    border-radius: var(--radius-md);
}

.status-indicator i {
    font-size: 2rem;
}

.status-indicator p {
    margin: 0;
    font-size: var(--font-size-sm);
    opacity: 0.8;
}

.status-success {
    background: var(--success-light);
    color: var(--success-dark);
}

.status-warning {
    background: var(--warning-light);
    color: var(--warning-dark);
}

.sitemap-actions {
    display: flex;
    gap: var(--spacing-md);
}

.sitemap-includes {
    list-style: none;
    padding: 0;
}

.sitemap-includes li {
    padding: var(--spacing-sm) 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}
</style>
