<?php
/**
 * SEO Edit Template with Language Tabs
 * Omniwallet CMS
 */

$entityTitle = $entityContent['title'] ?? $entityContent['name'] ?? $entityContent['company_name'] ?? 'Sin título';
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><i class="fas fa-edit"></i> Editar SEO</h1>
        <p><?= htmlspecialchars($entityTypes[$entityType] ?? $entityType) ?>: <?= htmlspecialchars($entityTitle) ?></p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/seo" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <?php if ($isConfigured): ?>
            <button type="button" class="btn btn-secondary" onclick="generateSEO(false)">
                <i class="fas fa-magic"></i> Generar SEO (ES)
            </button>
            <button type="button" class="btn btn-primary" onclick="generateSEO(true)">
                <i class="fas fa-globe"></i> Generar Todos los Idiomas
            </button>
        <?php endif; ?>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<form method="POST" action="/admin/seo/save" id="seoForm">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    <input type="hidden" name="entity_type" value="<?= htmlspecialchars($entityType) ?>">
    <input type="hidden" name="entity_id" value="<?= $entityId ?>">

    <!-- Content Preview -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-file-alt"></i> Contenido Original</h3>
        </div>
        <div class="card-body">
            <div class="content-preview">
                <h4><?= htmlspecialchars($entityTitle) ?></h4>
                <?php if (!empty($entityContent['description'])): ?>
                    <p><?= htmlspecialchars(substr(strip_tags($entityContent['description']), 0, 300)) ?>...</p>
                <?php elseif (!empty($entityContent['content'])): ?>
                    <p><?= htmlspecialchars(substr(strip_tags($entityContent['content']), 0, 300)) ?>...</p>
                <?php elseif (!empty($entityContent['short_description'])): ?>
                    <p><?= htmlspecialchars(substr(strip_tags($entityContent['short_description']), 0, 300)) ?>...</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SEO Tabs by Language -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-search"></i> Metadatos SEO</h3>
        </div>
        <div class="card-body">
            <!-- Language Tabs -->
            <div class="language-tabs">
                <?php foreach ($languages as $langCode => $langName): ?>
                    <button type="button"
                            class="lang-tab <?= $langCode === 'es' ? 'active' : '' ?>"
                            data-lang="<?= $langCode ?>"
                            onclick="switchTab('<?= $langCode ?>')">
                        <span class="lang-code"><?= strtoupper($langCode) ?></span>
                        <span class="lang-name"><?= $langName ?></span>
                        <?php if (!empty($seoByLanguage[$langCode]['meta_title'])): ?>
                            <i class="fas fa-check-circle text-success"></i>
                        <?php else: ?>
                            <i class="fas fa-exclamation-circle text-warning"></i>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Tab Content -->
            <?php foreach ($languages as $langCode => $langName): ?>
                <?php
                $seo = $seoByLanguage[$langCode] ?? [];
                $prefix = "seo_{$langCode}_";
                ?>
                <div class="tab-content <?= $langCode === 'es' ? 'active' : '' ?>" id="tab-<?= $langCode ?>">
                    <div class="seo-fields">
                        <!-- Meta Title -->
                        <div class="form-group">
                            <label for="<?= $prefix ?>meta_title">
                                Meta Title
                                <span class="char-count" id="<?= $prefix ?>title_count">
                                    <?= strlen($seo['meta_title'] ?? '') ?>/60
                                </span>
                            </label>
                            <input type="text"
                                   id="<?= $prefix ?>meta_title"
                                   name="<?= $prefix ?>meta_title"
                                   value="<?= htmlspecialchars($seo['meta_title'] ?? '') ?>"
                                   maxlength="70"
                                   class="form-control"
                                   oninput="updateCharCount(this, '<?= $prefix ?>title_count', 60)">
                            <small class="help-text">Título que aparece en los resultados de búsqueda (50-60 caracteres recomendados)</small>
                        </div>

                        <!-- Meta Description -->
                        <div class="form-group">
                            <label for="<?= $prefix ?>meta_description">
                                Meta Description
                                <span class="char-count" id="<?= $prefix ?>desc_count">
                                    <?= strlen($seo['meta_description'] ?? '') ?>/160
                                </span>
                            </label>
                            <textarea id="<?= $prefix ?>meta_description"
                                      name="<?= $prefix ?>meta_description"
                                      rows="3"
                                      maxlength="170"
                                      class="form-control"
                                      oninput="updateCharCount(this, '<?= $prefix ?>desc_count', 160)"><?= htmlspecialchars($seo['meta_description'] ?? '') ?></textarea>
                            <small class="help-text">Descripción que aparece en los resultados de búsqueda (150-160 caracteres recomendados)</small>
                        </div>

                        <!-- Open Graph Title -->
                        <div class="form-group">
                            <label for="<?= $prefix ?>og_title">OG Title (Social)</label>
                            <input type="text"
                                   id="<?= $prefix ?>og_title"
                                   name="<?= $prefix ?>og_title"
                                   value="<?= htmlspecialchars($seo['og_title'] ?? '') ?>"
                                   maxlength="100"
                                   class="form-control">
                            <small class="help-text">Título para redes sociales (si es diferente del meta title)</small>
                        </div>

                        <!-- Open Graph Description -->
                        <div class="form-group">
                            <label for="<?= $prefix ?>og_description">OG Description (Social)</label>
                            <textarea id="<?= $prefix ?>og_description"
                                      name="<?= $prefix ?>og_description"
                                      rows="2"
                                      maxlength="200"
                                      class="form-control"><?= htmlspecialchars($seo['og_description'] ?? '') ?></textarea>
                            <small class="help-text">Descripción para redes sociales</small>
                        </div>

                        <!-- Keywords -->
                        <div class="form-group">
                            <label for="<?= $prefix ?>keywords">Keywords</label>
                            <input type="text"
                                   id="<?= $prefix ?>keywords"
                                   name="<?= $prefix ?>keywords"
                                   value="<?= htmlspecialchars($seo['keywords'] ?? '') ?>"
                                   class="form-control">
                            <small class="help-text">Palabras clave separadas por coma</small>
                        </div>

                        <!-- Advanced Options (collapsed by default) -->
                        <details class="advanced-options">
                            <summary>Opciones Avanzadas</summary>
                            <div class="advanced-fields">
                                <!-- OG Image -->
                                <div class="form-group">
                                    <label for="<?= $prefix ?>og_image">OG Image URL</label>
                                    <input type="text"
                                           id="<?= $prefix ?>og_image"
                                           name="<?= $prefix ?>og_image"
                                           value="<?= htmlspecialchars($seo['og_image'] ?? '') ?>"
                                           class="form-control"
                                           placeholder="/assets/images/...">
                                </div>

                                <!-- Canonical URL -->
                                <div class="form-group">
                                    <label for="<?= $prefix ?>canonical_url">Canonical URL</label>
                                    <input type="text"
                                           id="<?= $prefix ?>canonical_url"
                                           name="<?= $prefix ?>canonical_url"
                                           value="<?= htmlspecialchars($seo['canonical_url'] ?? '') ?>"
                                           class="form-control"
                                           placeholder="Dejar vacío para usar URL por defecto">
                                </div>

                                <!-- Robots -->
                                <div class="form-group">
                                    <label for="<?= $prefix ?>robots">Robots</label>
                                    <select id="<?= $prefix ?>robots"
                                            name="<?= $prefix ?>robots"
                                            class="form-control">
                                        <option value="index, follow" <?= ($seo['robots'] ?? 'index, follow') === 'index, follow' ? 'selected' : '' ?>>
                                            index, follow (default)
                                        </option>
                                        <option value="noindex, follow" <?= ($seo['robots'] ?? '') === 'noindex, follow' ? 'selected' : '' ?>>
                                            noindex, follow
                                        </option>
                                        <option value="index, nofollow" <?= ($seo['robots'] ?? '') === 'index, nofollow' ? 'selected' : '' ?>>
                                            index, nofollow
                                        </option>
                                        <option value="noindex, nofollow" <?= ($seo['robots'] ?? '') === 'noindex, nofollow' ? 'selected' : '' ?>>
                                            noindex, nofollow
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </details>

                        <!-- Auto-generated indicator -->
                        <?php if (!empty($seo['is_auto_generated'])): ?>
                            <div class="auto-generated-notice">
                                <i class="fas fa-robot"></i>
                                Generado automáticamente el <?= date('d/m/Y H:i', strtotime($seo['generated_at'] ?? $seo['created_at'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Google Preview -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fab fa-google"></i> Vista Previa en Google</h3>
        </div>
        <div class="card-body">
            <div class="google-preview" id="googlePreview">
                <div class="gp-title" id="previewTitle">
                    <?= htmlspecialchars($seoByLanguage['es']['meta_title'] ?? $entityTitle) ?>
                </div>
                <div class="gp-url">
                    https://omniwallet.es/<?= htmlspecialchars($entityContent['slug'] ?? '') ?>
                </div>
                <div class="gp-description" id="previewDescription">
                    <?= htmlspecialchars($seoByLanguage['es']['meta_description'] ?? 'Sin descripción') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <a href="/admin/seo" class="btn btn-outline">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Cambios
        </button>
    </div>
</form>

<script>
function switchTab(langCode) {
    // Update tab buttons - using data attribute for reliable selection
    document.querySelectorAll('.lang-tab').forEach(tab => tab.classList.remove('active'));
    const activeTab = document.querySelector(`.lang-tab[data-lang="${langCode}"]`);
    if (activeTab) {
        activeTab.classList.add('active');
    }

    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    const activeContent = document.getElementById(`tab-${langCode}`);
    if (activeContent) {
        activeContent.classList.add('active');
    }

    // Update preview
    updatePreview(langCode);
}

function updateCharCount(input, countId, max) {
    const count = input.value.length;
    const countEl = document.getElementById(countId);
    countEl.textContent = `${count}/${max}`;

    if (count > max) {
        countEl.classList.add('over-limit');
    } else if (count > max * 0.9) {
        countEl.classList.add('near-limit');
        countEl.classList.remove('over-limit');
    } else {
        countEl.classList.remove('near-limit', 'over-limit');
    }

    // Update preview if Spanish
    if (input.id.startsWith('seo_es_')) {
        updatePreview('es');
    }
}

function updatePreview(langCode) {
    const title = document.getElementById(`seo_${langCode}_meta_title`).value || '<?= addslashes($entityTitle) ?>';
    const desc = document.getElementById(`seo_${langCode}_meta_description`).value || 'Sin descripción';

    document.getElementById('previewTitle').textContent = title;
    document.getElementById('previewDescription').textContent = desc;
}

function generateSEO(allLanguages) {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

    fetch('/admin/seo/generate-single', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            entity_type: '<?= $entityType ?>',
            entity_id: <?= $entityId ?>,
            language: 'es',
            all_languages: allLanguages,
            overwrite: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Error desconocido'));
            btn.disabled = false;
            btn.innerHTML = allLanguages
                ? '<i class="fas fa-globe"></i> Generar Todos los Idiomas'
                : '<i class="fas fa-magic"></i> Generar SEO (ES)';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
        btn.disabled = false;
    });
}

// Initialize character counts on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[id$="_meta_title"], textarea[id$="_meta_description"]').forEach(el => {
        const event = new Event('input');
        el.dispatchEvent(event);
    });
});
</script>

<style>
.content-preview {
    background: var(--color-gray-50);
    padding: var(--spacing-lg);
    border-radius: var(--radius-md);
}

.content-preview h4 {
    margin-top: 0;
}

.content-preview p {
    margin-bottom: 0;
    color: var(--color-gray-600);
}

.language-tabs {
    display: flex;
    gap: var(--spacing-xs);
    border-bottom: 2px solid var(--color-gray-200);
    margin-bottom: var(--spacing-xl);
    padding-bottom: var(--spacing-xs);
    flex-wrap: wrap;
}

.lang-tab {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-sm) var(--spacing-md);
    background: transparent;
    border: none;
    border-radius: var(--radius-md) var(--radius-md) 0 0;
    cursor: pointer;
    font-size: 13px;
    color: var(--color-gray-600);
    transition: all 0.2s ease;
}

.lang-tab:hover {
    background: var(--color-gray-100);
}

.lang-tab.active {
    background: var(--color-primary);
    color: white !important;
}

.lang-tab.active .lang-code,
.lang-tab.active .lang-name,
.lang-tab.active i {
    color: white !important;
}

.lang-tab .lang-code {
    font-weight: 700;
}

.lang-tab .lang-name {
    color: inherit;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.seo-fields {
    max-width: 800px;
}

.char-count {
    float: right;
    font-size: 13px;
    color: var(--color-gray-500);
}

.char-count.near-limit {
    color: var(--color-warning);
}

.char-count.over-limit {
    color: var(--color-danger);
    font-weight: bold;
}

.advanced-options {
    margin-top: var(--spacing-xl);
    padding: var(--spacing-md);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
}

.advanced-options summary {
    cursor: pointer;
    font-weight: 500;
    color: var(--color-gray-700);
}

.advanced-fields {
    margin-top: var(--spacing-lg);
}

.auto-generated-notice {
    margin-top: var(--spacing-lg);
    padding: var(--spacing-sm) var(--spacing-md);
    background: #dbeafe;
    color: #1e40af;
    border-radius: var(--radius-sm);
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.google-preview {
    max-width: 600px;
    font-family: Arial, sans-serif;
}

.gp-title {
    color: #1a0dab;
    font-size: 18px;
    line-height: 1.3;
    margin-bottom: 4px;
    cursor: pointer;
}

.gp-title:hover {
    text-decoration: underline;
}

.gp-url {
    color: #006621;
    font-size: 14px;
    margin-bottom: 4px;
}

.gp-description {
    color: #545454;
    font-size: 13px;
    line-height: 1.4;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-md);
    margin-top: var(--spacing-xl);
    padding-top: var(--spacing-xl);
    border-top: 1px solid var(--color-gray-200);
}
</style>
