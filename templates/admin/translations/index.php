<?php
/**
 * Translations List Template
 * Omniwallet CMS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Traducciones</h1>
        <p>Gestiona las traducciones del contenido</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/translations/error-log" class="btn btn-outline" title="Ver log de errores">
            <i class="fas fa-bug"></i> Log de Errores
        </a>
        <?php if ($isConfigured): ?>
            <button type="button" class="btn btn-primary" onclick="openBatchModal()">
                <i class="fas fa-language"></i> Traducir Automáticamente
            </button>
        <?php else: ?>
            <span class="badge badge-warning">
                <i class="fas fa-exclamation-triangle"></i> API no configurada
            </span>
        <?php endif; ?>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Statistics -->
<div class="stats-grid translations-stats">
    <?php foreach ($statistics as $code => $stat): ?>
        <a href="/admin/translations?language=<?= $code ?>" class="stat-card stat-card-link <?= $currentLanguage === $code ? 'active' : '' ?>">
            <div class="stat-icon">
                <span class="flag-icon"><?= strtoupper($code) ?></span>
            </div>
            <span class="stat-value"><?= $stat['total'] ?></span>
            <span class="stat-label"><?= $stat['name'] ?></span>
            <div class="stat-meta">
                <span class="text-success"><i class="fas fa-check"></i> <?= $stat['approved'] ?> aprobadas</span>
                <span class="text-warning"><i class="fas fa-clock"></i> <?= $stat['pending'] ?> pendientes</span>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Untranslated Stats (if language selected) -->
<?php if ($currentLanguage && $untranslatedStats): ?>
    <?php $totalUntranslated = array_sum($untranslatedStats); ?>
    <?php if ($totalUntranslated > 0): ?>
        <div class="card untranslated-card">
            <div class="card-header">
                <h3><i class="fas fa-exclamation-circle text-warning"></i> Contenido sin traducir (<?= strtoupper($currentLanguage) ?>)</h3>
                <span class="badge badge-warning"><?= $totalUntranslated ?> entidades</span>
            </div>
            <div class="card-body">
                <div class="untranslated-list">
                    <?php foreach ($untranslatedStats as $type => $count): ?>
                        <?php if ($count > 0): ?>
                            <div class="untranslated-item">
                                <span class="untranslated-type"><?= $entityTypes[$type] ?? $type ?></span>
                                <span class="untranslated-count"><?= $count ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <p class="untranslated-hint">
                    <i class="fas fa-info-circle"></i>
                    Usa "Traducir Automáticamente" para traducir este contenido pendiente.
                </p>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Filters -->
<div class="card filters-card">
    <form method="GET" action="/admin/translations" class="filters-form" id="filtersForm">
        <div class="filter-group filter-search">
            <label>Buscar</label>
            <div class="search-input-wrapper">
                <input type="text"
                       name="search"
                       value="<?= htmlspecialchars($search ?? '') ?>"
                       placeholder="Buscar en traducciones..."
                       class="form-control">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="filter-group">
            <label>Idioma</label>
            <select name="language" onchange="this.form.submit()">
                <option value="">Todos los idiomas</option>
                <?php foreach ($languages as $code => $name): ?>
                    <?php if ($code !== 'es'): ?>
                        <option value="<?= $code ?>" <?= $currentLanguage === $code ? 'selected' : '' ?>>
                            <?= $name ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Estado</label>
            <select name="status" onchange="handleStatusChange(this)">
                <option value="" <?= empty($currentStatus) ? 'selected' : '' ?>>Todas</option>
                <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Pendientes de revisión</option>
                <option value="approved" <?= $currentStatus === 'approved' ? 'selected' : '' ?>>Aprobadas</option>
                <option value="untranslated" <?= $currentStatus === 'untranslated' ? 'selected' : '' ?> <?= empty($currentLanguage) ? 'disabled' : '' ?>>
                    Sin traducir <?= empty($currentLanguage) ? '(selecciona idioma)' : '' ?>
                </option>
            </select>
        </div>
        <div class="filter-group">
            <label>Tipo</label>
            <select name="type" onchange="this.form.submit()">
                <option value="">Todos los tipos</option>
                <?php foreach ($entityTypes as $type => $name): ?>
                    <option value="<?= $type ?>" <?= $currentType === $type ? 'selected' : '' ?>>
                        <?= $name ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($currentLanguage || $currentType || $currentStatus || !empty($search)): ?>
            <a href="/admin/translations" class="btn btn-outline btn-sm">Limpiar filtros</a>
        <?php endif; ?>
    </form>
</div>

<?php if (!empty($search)): ?>
    <div class="search-results-info">
        <i class="fas fa-search"></i>
        Mostrando resultados para: <strong>"<?= htmlspecialchars($search) ?>"</strong>
        <a href="?<?= http_build_query(array_filter(['language' => $currentLanguage, 'type' => $currentType, 'status' => $currentStatus])) ?>"
           class="clear-search">&times; Limpiar búsqueda</a>
    </div>
<?php endif; ?>

<!-- Translations List -->
<div class="card">
    <div class="card-header">
        <h3>
            <?php if ($currentStatus === 'pending'): ?>
                Traducciones Pendientes de Revisión
            <?php elseif ($currentStatus === 'approved'): ?>
                Traducciones Aprobadas
            <?php elseif ($currentStatus === 'untranslated'): ?>
                Contenido Sin Traducir
            <?php else: ?>
                Todas las Traducciones
            <?php endif; ?>
            <?php if ($currentLanguage): ?>
                <span class="badge badge-info"><?= strtoupper($currentLanguage) ?></span>
            <?php endif; ?>
        </h3>
        <span class="translations-count"><?= count($translations) ?> <?= $currentStatus === 'untranslated' ? 'campos' : 'traducciones' ?></span>
    </div>

    <?php if (empty($translations)): ?>
        <div class="empty-state">
            <i class="fas fa-<?= $currentStatus === 'pending' ? 'check-circle' : ($currentStatus === 'untranslated' ? 'check-double' : 'language') ?>"></i>
            <h3><?php
                if ($currentStatus === 'pending') echo 'Todo al día';
                elseif ($currentStatus === 'untranslated') echo '¡Todo traducido!';
                else echo 'Sin traducciones';
            ?></h3>
            <p><?php
                if ($currentStatus === 'pending') echo 'No hay traducciones pendientes de revisión';
                elseif ($currentStatus === 'untranslated') echo 'Todo el contenido ya tiene traducción para este idioma';
                else echo 'No hay traducciones con los filtros seleccionados';
            ?></p>
        </div>
    <?php elseif ($currentStatus === 'untranslated'): ?>
        <!-- Untranslated content view with editable fields -->
        <div class="card-body" style="padding: 0;">
            <div class="untranslated-info">
                <i class="fas fa-info-circle"></i>
                Escribe la traducción y haz clic en "Guardar" para cada campo. Las traducciones manuales se aprueban automáticamente.
            </div>

            <div class="translations-list">
                <?php foreach ($translations as $index => $translation): ?>
                    <div class="translation-item untranslated-item" id="item-<?= $index ?>">
                        <div class="translation-content" style="flex: 1;">
                            <div class="translation-header">
                                <span class="badge badge-info"><?= $entityTypes[$translation['entity_type']] ?? $translation['entity_type'] ?></span>
                                <span class="badge badge-secondary"><?= strtoupper($translation['language']) ?></span>
                                <span class="badge badge-danger"><i class="fas fa-exclamation"></i> Sin traducir</span>
                                <span class="field-name"><?= htmlspecialchars($translation['field_name']) ?></span>
                                <?php if ($translation['entity_name']): ?>
                                    <span class="entity-name">— <?= htmlspecialchars($translation['entity_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php
                            $contentLength = strlen($translation['original_content']);
                            $isLong = $contentLength > 300;
                            $previewContent = $isLong ? substr($translation['original_content'], 0, 300) . '...' : $translation['original_content'];
                            ?>
                            <div class="translation-comparison">
                                <div class="original">
                                    <label>Original (ES) <span class="content-length">(<?= number_format($contentLength) ?> caracteres)</span></label>
                                    <div class="content content-preview" id="preview-<?= $index ?>"><?= nl2br(htmlspecialchars($previewContent)) ?></div>
                                    <?php if ($isLong): ?>
                                        <div class="content content-full" id="full-<?= $index ?>" style="display: none;"><?= nl2br(htmlspecialchars($translation['original_content'])) ?></div>
                                        <button type="button" class="btn-show-more" id="showMore-<?= $index ?>" onclick="toggleContent(<?= $index ?>)">
                                            <i class="fas fa-chevron-down"></i> Ver contenido completo
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div class="translated editable">
                                    <label>Traducción (<?= strtoupper($translation['language']) ?>)</label>
                                    <textarea class="form-control translation-input"
                                              id="input-<?= $index ?>"
                                              rows="<?= $contentLength > 500 ? 8 : ($contentLength > 100 ? 4 : 2) ?>"
                                              placeholder="Escribe la traducción aquí..."
                                              data-entity-type="<?= htmlspecialchars($translation['entity_type']) ?>"
                                              data-entity-id="<?= (int)$translation['entity_id'] ?>"
                                              data-field-name="<?= htmlspecialchars($translation['field_name']) ?>"
                                              data-language="<?= htmlspecialchars($translation['language']) ?>"
                                              data-original="<?= htmlspecialchars($translation['original_content']) ?>"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="translation-actions">
                            <button type="button" class="btn btn-sm btn-primary save-translation-btn" onclick="saveTranslation(<?= $index ?>)" title="Guardar traducción">
                                <i class="fas fa-save"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Normal translations view -->
        <form method="POST" action="/admin/translations/bulk-approve" id="bulkForm">
            <input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">

            <div class="card-body" style="padding: 0;">
                <div class="bulk-actions">
                    <label class="checkbox-label">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        <span>Seleccionar todos</span>
                    </label>
                    <button type="submit" class="btn btn-sm btn-primary" disabled id="bulkApproveBtn">
                        <i class="fas fa-check"></i> Aprobar seleccionadas
                    </button>
                </div>

                <div class="translations-list">
                    <?php foreach ($translations as $translation): ?>
                        <div class="translation-item">
                            <div class="translation-checkbox">
                                <input type="checkbox" name="ids[]" value="<?= $translation['id'] ?>"
                                       class="translation-select" onchange="updateBulkButton()">
                            </div>
                            <div class="translation-content">
                                <div class="translation-header">
                                    <span class="badge badge-info"><?= $entityTypes[$translation['entity_type']] ?? $translation['entity_type'] ?></span>
                                    <span class="badge badge-secondary"><?= strtoupper($translation['language']) ?></span>
                                    <?php if ($translation['is_approved']): ?>
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Aprobada</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Pendiente</span>
                                    <?php endif; ?>
                                    <span class="field-name"><?= htmlspecialchars($translation['field_name']) ?></span>
                                    <?php if ($translation['entity_name']): ?>
                                        <span class="entity-name">— <?= htmlspecialchars($translation['entity_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="translation-comparison">
                                    <div class="original">
                                        <label>Original (ES)</label>
                                        <div class="content"><?= htmlspecialchars(substr($translation['original_content'], 0, 200)) ?><?= strlen($translation['original_content']) > 200 ? '...' : '' ?></div>
                                    </div>
                                    <div class="translated">
                                        <label>Traducción (<?= strtoupper($translation['language']) ?>)</label>
                                        <div class="content"><?= htmlspecialchars(substr($translation['translated_content'], 0, 200)) ?><?= strlen($translation['translated_content']) > 200 ? '...' : '' ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="translation-actions">
                                <a href="/admin/translations/<?= $translation['id'] ?>/edit" class="btn btn-sm btn-outline" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (!$translation['is_approved']): ?>
                                    <form method="POST" action="/admin/translations/<?= $translation['id'] ?>/approve" class="inline-form">
                                        <input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
                                        <button type="submit" class="btn btn-sm btn-outline btn-success" title="Aprobar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" action="/admin/translations/<?= $translation['id'] ?>/delete" class="inline-form"
                                      onsubmit="return confirm('¿Eliminar esta traducción?')">
                                    <input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
                                    <button type="submit" class="btn btn-sm btn-outline btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<!-- Batch Translation Modal -->
<div id="batchModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Traducir Automáticamente</h3>
            <button type="button" class="modal-close" onclick="closeBatchModal()" id="modalCloseBtn">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Step 1: Language and Entity Type Selection -->
            <div id="batchStep1">
                <div class="form-group">
                    <label for="target_language">Idioma destino</label>
                    <select name="target_language" id="target_language" required>
                        <option value="">Selecciona un idioma</option>
                        <?php foreach ($languages as $code => $name): ?>
                            <?php if ($code !== 'es'): ?>
                                <option value="<?= $code ?>"><?= $name ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="batch_entity_type">Tipo de contenido</label>
                    <select name="entity_type" id="batch_entity_type">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($entityTypes as $type => $name): ?>
                            <option value="<?= $type ?>"><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-help">Selecciona un tipo para traducir solo ese contenido, o deja "Todos" para traducir todo.</small>
                </div>
                <div class="info-box">
                    <p><strong>Nota:</strong> Esta acción traducirá automáticamente el contenido publicado que no tenga traducción.</p>
                    <p>Las traducciones generadas quedarán pendientes de revisión (excepto bloques, que se aprueban automáticamente).</p>
                </div>
            </div>

            <!-- Step 2: Progress -->
            <div id="batchStep2" style="display: none;">
                <div class="progress-container">
                    <div class="progress-header">
                        <span id="progressStatus">Preparando traducción...</span>
                        <span id="progressPercent">0%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                    </div>
                    <div class="progress-details">
                        <div class="progress-stat">
                            <span class="stat-label">Procesados:</span>
                            <span id="statProcessed">0</span> / <span id="statTotal">0</span>
                        </div>
                        <div class="progress-stat text-success">
                            <i class="fas fa-check"></i>
                            <span id="statSuccess">0</span> éxitos
                        </div>
                        <div class="progress-stat text-warning">
                            <i class="fas fa-forward"></i>
                            <span id="statSkipped">0</span> omitidos
                        </div>
                        <div class="progress-stat text-danger">
                            <i class="fas fa-times"></i>
                            <span id="statFailed">0</span> errores
                        </div>
                    </div>
                </div>
                <div id="progressLog" class="progress-log"></div>
            </div>

            <!-- Step 3: Complete -->
            <div id="batchStep3" style="display: none;">
                <div class="complete-message">
                    <i class="fas fa-check-circle text-success"></i>
                    <h4>Traducción completada</h4>
                    <p id="completeStats"></p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeBatchModal()" id="cancelBtn">Cancelar</button>
            <button type="button" class="btn btn-primary" onclick="startBatchTranslation()" id="startBtn">
                <i class="fas fa-language"></i> Iniciar Traducción
            </button>
            <button type="button" class="btn btn-primary" onclick="location.reload()" id="finishBtn" style="display: none;">
                <i class="fas fa-check"></i> Finalizar
            </button>
        </div>
    </div>
</div>

<style>
/* Untranslated stats card */
.untranslated-card {
    margin-bottom: var(--spacing-lg);
    border-left: 4px solid var(--color-warning);
}
.untranslated-card .card-header {
    background-color: var(--color-warning-light, #fff3cd);
}
.untranslated-list {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-md);
}
.untranslated-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) var(--spacing-md);
    background-color: var(--color-gray-100);
    border-radius: var(--radius-md);
}
.untranslated-type {
    font-weight: 500;
    color: var(--color-gray-700);
}
.untranslated-count {
    font-weight: 700;
    color: var(--color-warning-dark, #856404);
    background-color: var(--color-warning-light, #fff3cd);
    padding: 2px 8px;
    border-radius: var(--radius-sm);
}
.untranslated-hint {
    font-size: 13px;
    color: var(--color-gray-600);
    margin: 0;
    padding-top: var(--spacing-sm);
    border-top: 1px solid var(--color-gray-200);
}
.untranslated-hint i {
    margin-right: var(--spacing-xs);
    color: var(--color-primary);
}

/* Search input */
.filter-search {
    flex: 1;
    min-width: 200px;
    max-width: 300px;
}
.search-input-wrapper {
    display: flex;
    position: relative;
}
.search-input-wrapper input {
    padding-right: 40px;
}
.search-btn {
    position: absolute;
    right: 0;
    top: 0;
    height: 100%;
    width: 40px;
    background: none;
    border: none;
    color: var(--color-gray-500);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.search-btn:hover {
    color: var(--color-primary);
}

/* Search results info */
.search-results-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) var(--spacing-md);
    background-color: var(--color-primary-light);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-md);
    font-size: 14px;
    color: var(--color-primary-dark);
}
.search-results-info i {
    color: var(--color-primary);
}
.clear-search {
    margin-left: auto;
    color: var(--color-gray-500);
    text-decoration: none;
    font-size: 16px;
}
.clear-search:hover {
    color: var(--color-danger);
}

/* Stat cards as links */
.stat-card-link {
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.stat-card-link:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.stat-card-link.active {
    border: 2px solid var(--color-primary);
    box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.1);
}

.translations-stats .stat-icon {
    background-color: var(--color-primary-light);
}
.translations-stats .flag-icon {
    font-weight: 700;
    font-size: 14px;
    color: var(--color-primary-dark);
}
.translations-stats .stat-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.translations-stats .stat-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
}

/* Card header with count */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.translations-count {
    font-size: 13px;
    color: var(--color-gray-500);
}

/* Status badges */
.badge-success {
    background-color: var(--color-success-light, #d4edda);
    color: var(--color-success, #28a745);
}
.badge-warning {
    background-color: var(--color-warning-light, #fff3cd);
    color: var(--color-warning-dark, #856404);
}

.bulk-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--spacing-md) var(--spacing-lg);
    background-color: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.translations-list {
    padding: var(--spacing-md);
}

.translation-item {
    display: flex;
    gap: var(--spacing-md);
    padding: var(--spacing-md);
    background-color: var(--color-gray-50);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-sm);
}

.translation-checkbox {
    padding-top: 4px;
}

.translation-content {
    flex: 1;
    min-width: 0;
}

.translation-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-sm);
    flex-wrap: wrap;
}

.field-name {
    font-weight: 600;
    color: var(--color-dark);
}

.entity-name {
    color: var(--color-gray-500);
    font-size: 13px;
}

.translation-comparison {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-md);
}

.translation-comparison label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: var(--color-gray-500);
    text-transform: uppercase;
    margin-bottom: 4px;
}

.translation-comparison .content {
    background-color: white;
    padding: var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 13px;
    color: var(--color-gray-700);
    border: 1px solid var(--color-gray-200);
}

.translation-actions {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.btn-success {
    color: var(--color-success);
    border-color: var(--color-success);
}
.btn-success:hover {
    background-color: var(--color-success);
    color: white;
}

/* Modal */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background-color: white;
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 500px;
    box-shadow: var(--shadow-lg);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-lg);
    border-bottom: 1px solid var(--color-gray-200);
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--color-gray-400);
}

.modal-body {
    padding: var(--spacing-lg);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-sm);
    padding: var(--spacing-lg);
    border-top: 1px solid var(--color-gray-200);
}

/* Progress UI */
.progress-container {
    margin-bottom: var(--spacing-md);
}

.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: var(--spacing-sm);
    font-size: 14px;
}

#progressStatus {
    color: var(--color-gray-600);
}

#progressPercent {
    font-weight: 600;
    color: var(--color-primary);
}

.progress-bar-container {
    height: 20px;
    background-color: var(--color-gray-200);
    border-radius: var(--radius-md);
    overflow: hidden;
    margin-bottom: var(--spacing-md);
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--color-primary), var(--color-primary-dark));
    border-radius: var(--radius-md);
    transition: width 0.3s ease;
}

.progress-details {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-md);
    font-size: 13px;
}

.progress-stat {
    display: flex;
    align-items: center;
    gap: 4px;
}

.progress-stat .stat-label {
    color: var(--color-gray-500);
}

.progress-log {
    max-height: 150px;
    overflow-y: auto;
    font-size: 12px;
    font-family: monospace;
    background-color: var(--color-gray-50);
    border-radius: var(--radius-sm);
    padding: var(--spacing-sm);
    margin-top: var(--spacing-md);
}

.progress-log .log-entry {
    padding: 2px 0;
    border-bottom: 1px solid var(--color-gray-100);
}

.progress-log .log-entry:last-child {
    border-bottom: none;
}

.progress-log .log-success { color: var(--color-success); }
.progress-log .log-error { color: var(--color-danger); }
.progress-log .log-skip { color: var(--color-warning-dark); }

.complete-message {
    text-align: center;
    padding: var(--spacing-lg) 0;
}

.complete-message i {
    font-size: 48px;
    margin-bottom: var(--spacing-md);
}

.complete-message h4 {
    margin: 0 0 var(--spacing-sm) 0;
    font-size: 18px;
}

.complete-message p {
    color: var(--color-gray-600);
    margin: 0;
}

@media (max-width: 768px) {
    .translation-comparison {
        grid-template-columns: 1fr;
    }
}

/* Untranslated content editing */
.untranslated-info {
    background-color: var(--color-info-light, #d1ecf1);
    color: var(--color-info-dark, #0c5460);
    padding: var(--spacing-md) var(--spacing-lg);
    border-bottom: 1px solid var(--color-info, #17a2b8);
    font-size: 14px;
}
.untranslated-info i {
    margin-right: var(--spacing-sm);
}

.untranslated-item {
    border-left: 3px solid var(--color-warning);
}
.untranslated-item.saved {
    border-left-color: var(--color-success);
    background-color: var(--color-success-light, #d4edda);
}

.translated.editable {
    position: relative;
}
.translation-input {
    width: 100%;
    min-height: 60px;
    padding: var(--spacing-sm);
    border: 2px solid var(--color-gray-300);
    border-radius: var(--radius-sm);
    font-size: 13px;
    resize: vertical;
    transition: border-color 0.2s;
}
.translation-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.1);
}
.translation-input:disabled {
    background-color: var(--color-gray-100);
    cursor: not-allowed;
}

.save-translation-btn {
    min-width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.save-translation-btn.saving {
    pointer-events: none;
    opacity: 0.7;
}
.save-translation-btn.saved {
    background-color: var(--color-success);
    border-color: var(--color-success);
    color: white;
}

/* Show more/less content */
.content-length {
    font-weight: normal;
    color: var(--color-gray-400);
    font-size: 10px;
}

.content-full {
    max-height: 400px;
    overflow-y: auto;
}

.btn-show-more {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    margin-top: var(--spacing-sm);
    padding: var(--spacing-xs) var(--spacing-sm);
    background: none;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-sm);
    font-size: 12px;
    color: var(--color-primary);
    cursor: pointer;
    transition: all 0.2s;
}

.btn-show-more:hover {
    background-color: var(--color-primary-light);
    border-color: var(--color-primary);
}

.btn-show-more.expanded i {
    transform: rotate(180deg);
}
</style>

<script>
const csrfToken = '<?= $_csrf_token ?>';

// Toggle content preview/full
function toggleContent(index) {
    const preview = document.getElementById('preview-' + index);
    const full = document.getElementById('full-' + index);
    const btn = document.getElementById('showMore-' + index);

    if (full.style.display === 'none') {
        // Show full content
        preview.style.display = 'none';
        full.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-chevron-up"></i> Ver menos';
        btn.classList.add('expanded');
    } else {
        // Show preview
        preview.style.display = 'block';
        full.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-chevron-down"></i> Ver contenido completo';
        btn.classList.remove('expanded');
    }
}

// Batch translation state
let batchState = {
    isRunning: false,
    language: '',
    entityType: '',
    offset: 0,
    batchSize: 5,
    total: 0,
    processed: 0,
    success: 0,
    failed: 0,
    skipped: 0
};

function openBatchModal() {
    // Reset state
    batchState = {
        isRunning: false,
        language: '',
        entityType: '',
        offset: 0,
        batchSize: 5,
        total: 0,
        processed: 0,
        success: 0,
        failed: 0,
        skipped: 0
    };

    // Reset UI
    document.getElementById('batchStep1').style.display = 'block';
    document.getElementById('batchStep2').style.display = 'none';
    document.getElementById('batchStep3').style.display = 'none';
    document.getElementById('startBtn').style.display = 'inline-flex';
    document.getElementById('cancelBtn').style.display = 'inline-flex';
    document.getElementById('finishBtn').style.display = 'none';
    document.getElementById('modalCloseBtn').style.display = 'block';
    document.getElementById('target_language').value = '';
    document.getElementById('batch_entity_type').value = '';
    document.getElementById('progressLog').innerHTML = '';

    document.getElementById('batchModal').style.display = 'flex';
}

function closeBatchModal() {
    if (batchState.isRunning) {
        if (!confirm('¿Seguro que deseas cancelar la traducción en curso?')) {
            return;
        }
        batchState.isRunning = false;
    }
    document.getElementById('batchModal').style.display = 'none';
}

async function startBatchTranslation() {
    const language = document.getElementById('target_language').value;
    if (!language) {
        alert('Por favor, selecciona un idioma');
        return;
    }

    const entityType = document.getElementById('batch_entity_type').value;

    batchState.language = language;
    batchState.entityType = entityType;
    batchState.isRunning = true;

    // Switch to progress view
    document.getElementById('batchStep1').style.display = 'none';
    document.getElementById('batchStep2').style.display = 'block';
    document.getElementById('startBtn').style.display = 'none';
    document.getElementById('cancelBtn').textContent = 'Cancelar';
    document.getElementById('modalCloseBtn').style.display = 'none';

    const entityLabel = entityType ? document.getElementById('batch_entity_type').selectedOptions[0].text : 'todos los tipos';
    addLogEntry(`Obteniendo información para ${entityLabel}...`, 'info');

    try {
        // Get batch info first
        let batchInfoUrl = `/admin/translations/batch-info?language=${language}`;
        if (entityType) batchInfoUrl += `&entity_type=${entityType}`;
        const infoResponse = await fetch(batchInfoUrl);
        const info = await infoResponse.json();

        if (info.error) {
            throw new Error(info.error);
        }

        batchState.total = info.total_entities;
        document.getElementById('statTotal').textContent = batchState.total;

        addLogEntry(`Encontrados ${batchState.total} elementos para traducir`, 'info');

        if (batchState.total === 0) {
            showComplete();
            return;
        }

        // Start processing batches
        await processBatches();

    } catch (error) {
        addLogEntry(`Error: ${error.message}`, 'error');
        batchState.isRunning = false;
        document.getElementById('cancelBtn').textContent = 'Cerrar';
    }
}

async function processBatches() {
    while (batchState.isRunning && batchState.offset < batchState.total) {
        updateProgress(`Procesando lote ${Math.floor(batchState.offset / batchState.batchSize) + 1}...`);

        try {
            const formData = new FormData();
            formData.append('language', batchState.language);
            formData.append('offset', batchState.offset);
            formData.append('batch_size', batchState.batchSize);
            formData.append('_csrf_token', csrfToken);
            if (batchState.entityType) {
                formData.append('entity_type', batchState.entityType);
            }

            const response = await fetch('/admin/translations/process-batch', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            // Update stats
            batchState.success += result.success || 0;
            batchState.failed += result.failed || 0;
            batchState.skipped += result.skipped || 0;
            batchState.processed += result.processed || 0;
            batchState.offset = result.nextOffset || (batchState.offset + batchState.batchSize);

            // Update UI
            updateStats();

            // Add log entries
            if (result.success > 0) {
                addLogEntry(`✓ ${result.success} traducciones completadas`, 'success');
            }
            if (result.skipped > 0) {
                addLogEntry(`→ ${result.skipped} ya existentes (omitidas)`, 'skip');
            }
            if (result.failed > 0) {
                addLogEntry(`✗ ${result.failed} errores`, 'error');
            }

            // Check if done
            if (!result.hasMore) {
                batchState.isRunning = false;
            }

        } catch (error) {
            addLogEntry(`Error en lote: ${error.message}`, 'error');
            batchState.failed++;
            batchState.offset += batchState.batchSize;
            updateStats();
        }

        // Small delay to avoid overwhelming the server
        await sleep(500);
    }

    showComplete();
}

function updateProgress(status) {
    document.getElementById('progressStatus').textContent = status;
    const percent = batchState.total > 0
        ? Math.round((batchState.processed / batchState.total) * 100)
        : 0;
    document.getElementById('progressPercent').textContent = percent + '%';
    document.getElementById('progressBar').style.width = percent + '%';
}

function updateStats() {
    document.getElementById('statProcessed').textContent = batchState.processed;
    document.getElementById('statSuccess').textContent = batchState.success;
    document.getElementById('statFailed').textContent = batchState.failed;
    document.getElementById('statSkipped').textContent = batchState.skipped;

    const percent = batchState.total > 0
        ? Math.round((batchState.processed / batchState.total) * 100)
        : 0;
    document.getElementById('progressPercent').textContent = percent + '%';
    document.getElementById('progressBar').style.width = percent + '%';
}

function addLogEntry(message, type = 'info') {
    const log = document.getElementById('progressLog');
    const entry = document.createElement('div');
    entry.className = 'log-entry log-' + type;
    entry.textContent = message;
    log.appendChild(entry);
    log.scrollTop = log.scrollHeight;
}

function showComplete() {
    batchState.isRunning = false;

    document.getElementById('batchStep2').style.display = 'none';
    document.getElementById('batchStep3').style.display = 'block';
    document.getElementById('cancelBtn').style.display = 'none';
    document.getElementById('finishBtn').style.display = 'inline-flex';

    document.getElementById('completeStats').textContent =
        `${batchState.success} traducciones exitosas, ${batchState.skipped} omitidas, ${batchState.failed} errores`;
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.translation-select');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateBulkButton();
}

function updateBulkButton() {
    const checkboxes = document.querySelectorAll('.translation-select:checked');
    const btn = document.getElementById('bulkApproveBtn');
    btn.disabled = checkboxes.length === 0;
}

// Handle status filter change
function handleStatusChange(select) {
    const value = select.value;
    const languageSelect = document.querySelector('select[name="language"]');
    const currentLanguage = languageSelect ? languageSelect.value : '';

    // If selecting "untranslated" without a language selected, warn user
    if (value === 'untranslated' && !currentLanguage) {
        alert('Para ver contenido sin traducir, primero selecciona un idioma.');
        select.value = '';
        return;
    }

    // Submit the form
    select.form.submit();
}

// Save manual translation
async function saveTranslation(index) {
    const item = document.getElementById('item-' + index);
    const input = document.getElementById('input-' + index);
    const btn = item.querySelector('.save-translation-btn');

    const entityType = input.dataset.entityType;
    const entityId = input.dataset.entityId;
    const fieldName = input.dataset.fieldName;
    const language = input.dataset.language;
    const originalContent = input.dataset.original;
    const translatedContent = input.value.trim();

    // Validate
    if (!translatedContent) {
        alert('La traducción no puede estar vacía');
        input.focus();
        return;
    }

    // Disable input and show loading
    input.disabled = true;
    btn.classList.add('saving');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const formData = new FormData();
        formData.append('entity_type', entityType);
        formData.append('entity_id', entityId);
        formData.append('field_name', fieldName);
        formData.append('language', language);
        formData.append('original_content', originalContent);
        formData.append('translated_content', translatedContent);
        formData.append('_csrf_token', csrfToken);

        const response = await fetch('/admin/translations/create', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            // Mark as saved
            item.classList.add('saved');
            btn.classList.remove('saving');
            btn.classList.add('saved');
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.disabled = true;
            input.disabled = true;

            // Update the badge
            const dangerBadge = item.querySelector('.badge-danger');
            if (dangerBadge) {
                dangerBadge.className = 'badge badge-success';
                dangerBadge.innerHTML = '<i class="fas fa-check"></i> Guardada';
            }
        } else {
            throw new Error(result.error || 'Error desconocido');
        }
    } catch (error) {
        alert('Error al guardar: ' + error.message);
        input.disabled = false;
        btn.classList.remove('saving');
        btn.innerHTML = '<i class="fas fa-save"></i>';
    }
}
</script>
