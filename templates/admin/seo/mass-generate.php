<?php
/**
 * Mass SEO Generation Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><i class="fas fa-magic"></i> Generación Masiva de SEO</h1>
        <p>Genera automáticamente metadatos SEO usando IA</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/seo" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
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
        <a href="/admin/settings">Configuración > Integraciones</a> para usar la generación automática.
    </div>
<?php else: ?>

<!-- Filters -->
<div class="card filters-card">
    <form method="GET" action="/admin/seo/mass-generate" class="filters-form">
        <div class="filter-group">
            <label>Tipo de Contenido</label>
            <select name="type" onchange="this.form.submit()">
                <?php foreach ($entityTypes as $type => $name): ?>
                    <option value="<?= $type ?>" <?= $currentType === $type ? 'selected' : '' ?>>
                        <?= $name ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Idioma</label>
            <select name="language" onchange="this.form.submit()">
                <?php foreach ($languages as $code => $name): ?>
                    <option value="<?= $code ?>" <?= $currentLanguage === $code ? 'selected' : '' ?>>
                        <?= $name ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<!-- Generation Panel -->
<div class="card">
    <div class="card-header">
        <h3>
            <i class="fas fa-list"></i>
            Contenido sin SEO: <?= $entityTypes[$currentType] ?? $currentType ?>
            <span class="badge badge-info"><?= count($entitiesWithoutSeo) ?> pendientes</span>
        </h3>
    </div>
    <div class="card-body">
        <?php if (empty($entitiesWithoutSeo)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h4>Todo el contenido tiene SEO</h4>
                <p>No hay contenido pendiente de este tipo.</p>
            </div>
        <?php else: ?>
            <!-- Progress Section (hidden initially) -->
            <div id="progressSection" style="display: none;">
                <div class="generation-progress">
                    <div class="progress-info">
                        <span id="progressText">Preparando...</span>
                        <span id="progressPercent">0%</span>
                    </div>
                    <div class="progress-bar-large">
                        <div class="progress-fill" id="progressBar" style="width: 0%"></div>
                    </div>
                    <div class="progress-stats">
                        <span class="text-success"><i class="fas fa-check"></i> <span id="successCount">0</span> generados</span>
                        <span class="text-warning"><i class="fas fa-forward"></i> <span id="skippedCount">0</span> omitidos</span>
                        <span class="text-danger"><i class="fas fa-times"></i> <span id="failedCount">0</span> errores</span>
                    </div>
                </div>
            </div>

            <!-- Start Section -->
            <div id="startSection">
                <div class="generation-options">
                    <label class="checkbox-label">
                        <input type="checkbox" id="overwriteExisting">
                        Sobrescribir SEO existente
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" id="generateAllLanguages">
                        Generar para todos los idiomas
                    </label>
                </div>

                <div class="generation-actions">
                    <button type="button" class="btn btn-primary btn-lg" onclick="startGeneration()">
                        <i class="fas fa-play"></i> Iniciar Generación
                    </button>
                    <p class="help-text">
                        Se generarán metadatos SEO para <?= count($entitiesWithoutSeo) ?> contenidos.
                        Este proceso puede tardar varios minutos.
                    </p>
                </div>
            </div>

            <!-- Entities List -->
            <div class="entities-list">
                <h4>Contenidos pendientes:</h4>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Slug</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($entitiesWithoutSeo, 0, 20) as $entity): ?>
                            <tr id="entity-row-<?= $entity['id'] ?>">
                                <td>#<?= $entity['id'] ?></td>
                                <td><?= htmlspecialchars($entity['title'] ?? 'Sin título') ?></td>
                                <td><code><?= htmlspecialchars($entity['slug'] ?? '-') ?></code></td>
                                <td>
                                    <span class="entity-status status-pending">
                                        <i class="fas fa-clock"></i> Pendiente
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($entitiesWithoutSeo) > 20): ?>
                            <tr>
                                <td colspan="4" class="text-muted text-center">
                                    ... y <?= count($entitiesWithoutSeo) - 20 ?> más
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
let isGenerating = false;
let currentOffset = 0;
const batchSize = 3;
const entityType = '<?= $currentType ?>';
const allLanguages = <?= json_encode(array_keys($languages)) ?>;
let languagesToProcess = [];
let currentLanguageIndex = 0;
let totalStats = { success: 0, skipped: 0, failed: 0 };

function startGeneration() {
    if (isGenerating) return;

    isGenerating = true;
    currentOffset = 0;
    totalStats = { success: 0, skipped: 0, failed: 0 };
    currentLanguageIndex = 0;

    const generateAll = document.getElementById('generateAllLanguages').checked;
    if (generateAll) {
        languagesToProcess = [...allLanguages];
    } else {
        languagesToProcess = ['<?= $currentLanguage ?>'];
    }

    document.getElementById('startSection').style.display = 'none';
    document.getElementById('progressSection').style.display = 'block';

    updateLanguageProgress();
    processBatch();
}

function updateLanguageProgress() {
    const currentLang = languagesToProcess[currentLanguageIndex];
    const langNames = <?= json_encode($languages) ?>;
    document.getElementById('progressText').textContent =
        `Procesando idioma: ${langNames[currentLang] || currentLang} (${currentLanguageIndex + 1}/${languagesToProcess.length})`;
}

function processBatch() {
    const overwrite = document.getElementById('overwriteExisting').checked;
    const currentLang = languagesToProcess[currentLanguageIndex];

    fetch('/admin/seo/generate-batch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            entity_type: entityType,
            language: currentLang,
            offset: currentOffset,
            batch_size: batchSize,
            overwrite: overwrite
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error del servidor: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.stats) {
            const stats = data.stats;

            // Accumulate stats
            totalStats.success += stats.success || 0;
            totalStats.skipped += stats.skipped || 0;
            totalStats.failed += stats.failed || 0;

            // Update counters display
            document.getElementById('successCount').textContent = totalStats.success;
            document.getElementById('skippedCount').textContent = totalStats.skipped;
            document.getElementById('failedCount').textContent = totalStats.failed;

            // Calculate progress including all languages
            const processed = currentOffset + (stats.processed || 0);
            const totalForLang = stats.total || 1;
            const langProgress = (processed / totalForLang);
            const overallProgress = ((currentLanguageIndex + langProgress) / languagesToProcess.length) * 100;
            const percent = Math.round(overallProgress);

            document.getElementById('progressBar').style.width = percent + '%';
            document.getElementById('progressPercent').textContent = percent + '%';

            if (stats.hasMore) {
                currentOffset = stats.nextOffset || (currentOffset + batchSize);
                setTimeout(processBatch, 1000);
            } else {
                // Current language done, move to next
                currentLanguageIndex++;
                if (currentLanguageIndex < languagesToProcess.length) {
                    currentOffset = 0;
                    updateLanguageProgress();
                    setTimeout(processBatch, 1000);
                } else {
                    // All languages done
                    document.getElementById('progressText').textContent =
                        `¡Completado! ${languagesToProcess.length} idioma(s) procesados`;
                    document.getElementById('progressBar').style.width = '100%';
                    document.getElementById('progressPercent').textContent = '100%';
                    isGenerating = false;

                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            }
        } else {
            const errorMsg = data.message || 'Error desconocido en la generación';
            alert('Error: ' + errorMsg);
            document.getElementById('progressText').textContent = 'Error: ' + errorMsg;
            isGenerating = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMsg = error.message || 'Error de conexión';
        alert('Error: ' + errorMsg);
        document.getElementById('progressText').textContent = 'Error: ' + errorMsg;
        isGenerating = false;
    });
}
</script>

<style>
.generation-options {
    display: flex;
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
    padding: var(--spacing-lg);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    cursor: pointer;
}

.generation-actions {
    text-align: center;
    padding: var(--spacing-xl);
}

.generation-actions .help-text {
    margin-top: var(--spacing-md);
    color: var(--color-gray-500);
    font-size: 13px;
}

.generation-progress {
    padding: var(--spacing-xl);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-xl);
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: var(--spacing-sm);
    font-weight: 500;
}

.progress-bar-large {
    height: 24px;
    background: var(--color-gray-200);
    border-radius: var(--radius-md);
    overflow: hidden;
    margin-bottom: var(--spacing-md);
}

.progress-bar-large .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-success) 100%);
    border-radius: var(--radius-md);
    transition: width 0.3s ease;
}

.progress-stats {
    display: flex;
    justify-content: center;
    gap: var(--spacing-xl);
}

.progress-stats span {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.entities-list {
    margin-top: var(--spacing-xl);
    padding-top: var(--spacing-xl);
    border-top: 1px solid var(--color-gray-200);
}

.entities-list h4 {
    margin-bottom: var(--spacing-md);
}

.entity-status {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: 13px;
    padding: 2px 8px;
    border-radius: var(--radius-sm);
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-success {
    background: #dcfce7;
    color: #166534;
}

.status-error {
    background: #fee2e2;
    color: #991b1b;
}

.empty-state {
    text-align: center;
    padding: 48px 32px;
    color: var(--color-gray-500);
}

.empty-state i {
    font-size: 3rem;
    color: var(--color-success);
    margin-bottom: var(--spacing-md);
}
</style>

<?php endif; ?>
