<?php
/**
 * Robots.txt Management Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><i class="fas fa-robot"></i> Robots.txt</h1>
        <p>Configura las directivas de rastreo para buscadores</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/seo" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <a href="/robots.txt" target="_blank" class="btn btn-outline">
            <i class="fas fa-external-link-alt"></i> Ver Actual
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<form method="POST" action="/admin/seo/save-robots">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-edit"></i> Contenido de robots.txt</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <textarea name="robots_content"
                          id="robotsContent"
                          rows="15"
                          class="form-control code-editor"><?= htmlspecialchars($robotsContent) ?></textarea>
                <small class="help-text">
                    Configura qué partes del sitio pueden rastrear los motores de búsqueda
                </small>
            </div>

            <div class="robots-help">
                <h4><i class="fas fa-lightbulb"></i> Directivas comunes:</h4>
                <ul>
                    <li><code>User-agent: *</code> - Aplica a todos los bots</li>
                    <li><code>Allow: /</code> - Permite rastrear todo</li>
                    <li><code>Disallow: /admin/</code> - Bloquea una carpeta</li>
                    <li><code>Sitemap: URL</code> - Indica ubicación del sitemap</li>
                    <li><code>Crawl-delay: 1</code> - Espera entre peticiones (segundos)</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Robots.txt
        </button>
    </div>
</form>

<style>
.code-editor {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 14px;
    line-height: 1.5;
    background: var(--gray-900);
    color: var(--gray-100);
    padding: var(--spacing-lg);
    border-radius: var(--radius-md);
}

.robots-help {
    margin-top: var(--spacing-xl);
    padding: var(--spacing-lg);
    background: var(--gray-50);
    border-radius: var(--radius-md);
}

.robots-help h4 {
    margin-top: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.robots-help ul {
    margin-bottom: 0;
}

.robots-help li {
    padding: var(--spacing-xs) 0;
}

.robots-help code {
    background: var(--gray-200);
    padding: 2px 6px;
    border-radius: var(--radius-sm);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: var(--spacing-xl);
}
</style>
