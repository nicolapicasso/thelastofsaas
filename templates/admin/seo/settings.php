<?php
/**
 * SEO Settings Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><i class="fas fa-cog"></i> Configuración SEO</h1>
        <p>Ajustes generales para la optimización de motores de búsqueda</p>
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

<?php if (!$isConfigured): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>API no configurada:</strong> Configura la API key de OpenAI en
        <a href="/admin/settings">Configuración > Integraciones</a> para usar la generación automática de SEO.
    </div>
<?php endif; ?>

<form method="POST" action="/admin/seo/save-settings">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-magic"></i> Generación Automática</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="hidden" name="seo_auto_generate_on_publish" value="0">
                    <input type="checkbox"
                           name="seo_auto_generate_on_publish"
                           value="1"
                           <?= $seoSettings['seo_auto_generate_on_publish'] === '1' ? 'checked' : '' ?>>
                    Generar SEO automáticamente al publicar contenido
                </label>
                <small class="help-text">
                    Si está activado, se generará automáticamente meta title y description cuando se publique nuevo contenido
                </small>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-tags"></i> Meta Tags por Defecto</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="seo_title_suffix">Sufijo del Título</label>
                <input type="text"
                       id="seo_title_suffix"
                       name="seo_title_suffix"
                       value="<?= htmlspecialchars($seoSettings['seo_title_suffix']) ?>"
                       class="form-control"
                       placeholder=" | Omniwallet">
                <small class="help-text">
                    Se añade al final de todos los títulos. Ej: "Mi Página | Omniwallet"
                </small>
            </div>

            <div class="form-group">
                <label for="seo_default_robots">Robots por Defecto</label>
                <select id="seo_default_robots" name="seo_default_robots" class="form-control">
                    <option value="index, follow" <?= $seoSettings['seo_default_robots'] === 'index, follow' ? 'selected' : '' ?>>
                        index, follow (recomendado)
                    </option>
                    <option value="noindex, follow" <?= $seoSettings['seo_default_robots'] === 'noindex, follow' ? 'selected' : '' ?>>
                        noindex, follow
                    </option>
                    <option value="index, nofollow" <?= $seoSettings['seo_default_robots'] === 'index, nofollow' ? 'selected' : '' ?>>
                        index, nofollow
                    </option>
                    <option value="noindex, nofollow" <?= $seoSettings['seo_default_robots'] === 'noindex, nofollow' ? 'selected' : '' ?>>
                        noindex, nofollow
                    </option>
                </select>
                <small class="help-text">
                    Directiva robots por defecto para nuevo contenido
                </small>
            </div>

            <div class="form-group">
                <label for="seo_default_og_image">Imagen OG por Defecto</label>
                <input type="text"
                       id="seo_default_og_image"
                       name="seo_default_og_image"
                       value="<?= htmlspecialchars($seoSettings['seo_default_og_image']) ?>"
                       class="form-control"
                       placeholder="/assets/images/og-default.jpg">
                <small class="help-text">
                    Imagen que se usa cuando el contenido no tiene imagen específica
                </small>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Configuración
        </button>
    </div>
</form>

<style>
.checkbox-label {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    cursor: pointer;
    font-weight: 500;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: var(--spacing-xl);
}
</style>
