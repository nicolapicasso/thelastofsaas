<div class="page-header">
    <div class="page-header-left">
        <a href="/admin/pages" class="back-link">&larr; Volver a páginas</a>
    </div>
    <?php if ($page): ?>
    <div class="page-header-right">
        <!-- Debug info - remove after debugging -->
        <small style="color: #888; margin-right: 15px; font-size: 11px;">
            DB: <?= date('H:i:s') ?> | Updated: <?= $page['updated_at'] ?? 'N/A' ?>
        </small>
        <?php if ($page['status'] === 'published'): ?>
        <a href="/<?= $page['slug'] === 'home' ? '' : htmlspecialchars($page['slug']) ?>" target="_blank" class="btn btn-outline">Ver página</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<form method="POST" action="<?= $page ? '/admin/pages/' . $page['id'] : '/admin/pages' ?>" id="page-form">
    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <div class="editor-layout">
        <!-- Main Content -->
        <div class="editor-main">
            <!-- Page Info Card -->
            <div class="editor-card">
                <div class="card-header">
                    <h3>Información de la página</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group form-group-large">
                            <label for="title">Título <span class="required">*</span></label>
                            <input type="text" id="title" name="title" value="<?= htmlspecialchars($page['title'] ?? '') ?>" required placeholder="Título de la página">
                        </div>
                        <div class="form-group">
                            <label for="slug">Slug (URL)</label>
                            <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($page['slug'] ?? '') ?>" placeholder="auto-generado" <?= ($page['slug'] ?? '') === 'home' ? 'readonly' : '' ?>>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blocks Editor -->
            <?php if ($page): ?>
            <div class="editor-card">
                <div class="card-header">
                    <h3>Bloques de contenido</h3>
                    <button type="button" class="btn btn-sm btn-primary" id="add-block-btn">+ Añadir bloque</button>
                </div>
                <div class="card-body">
                    <div id="blocks-container" class="blocks-container" data-page-id="<?= $page['id'] ?>">
                        <?php if (empty($blocks)): ?>
                        <div class="blocks-empty" id="blocks-empty">
                            <p>Esta página no tiene bloques todavía.</p>
                            <p>Haz clic en "Añadir bloque" para empezar a diseñar tu página.</p>
                        </div>
                        <?php endif; ?>

                        <?php foreach ($blocks as $block): ?>
                        <?php $blockType = $block['type'] ?? ''; ?>
                        <div class="block-item" data-block-id="<?= $block['id'] ?>" data-block-type="<?= htmlspecialchars($blockType) ?>">
                            <div class="block-handle">
                                <span class="handle-icon">&#9776;</span>
                            </div>
                            <div class="block-info">
                                <span class="block-type-badge"><?= htmlspecialchars($blockTypes[$blockType] ?? $blockType) ?></span>
                                <?php
                                    $content = is_array($block['content']) ? $block['content'] : [];
                                    $previewTitle = $content['title'] ?? $content['slides'][0]['title'] ?? 'Sin título';
                                ?>
                                <span class="block-preview"><?= htmlspecialchars((string)$previewTitle) ?></span>
                            </div>
                            <div class="block-actions">
                                <button type="button" class="btn btn-sm edit-block-btn" data-block-id="<?= $block['id'] ?>">Editar</button>
                                <button type="button" class="btn btn-sm btn-secondary clone-block-btn" data-block-id="<?= $block['id'] ?>" title="Clonar bloque"><i class="fas fa-clone"></i></button>
                                <button type="button" class="btn btn-sm btn-danger delete-block-btn" data-block-id="<?= $block['id'] ?>">Eliminar</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="editor-card">
                <div class="card-body">
                    <div class="info-message">
                        <p>Guarda la página primero para poder añadir bloques de contenido.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- SEO Card -->
            <div class="editor-card">
                <div class="card-header collapsible" data-target="seo-content">
                    <h3>SEO y Meta Tags</h3>
                    <div class="card-header-actions">
                        <?php if ($page): ?>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="generateSEO('page', <?= $page['id'] ?>)" title="Generar SEO con IA">
                            <i class="fas fa-magic"></i> Generar SEO
                        </button>
                        <a href="/admin/seo/edit?type=page&id=<?= $page['id'] ?>" class="btn btn-sm btn-outline" title="Editar SEO avanzado">
                            <i class="fas fa-cog"></i>
                        </a>
                        <?php endif; ?>
                        <span class="collapse-icon">&#9660;</span>
                    </div>
                </div>
                <div class="card-body" id="seo-content">
                    <div class="form-group">
                        <label for="meta_title">Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title" value="<?= htmlspecialchars($page['meta_title'] ?? '') ?>" placeholder="Título para buscadores (50-60 caracteres)" maxlength="70">
                        <small class="form-help char-count" data-target="meta_title">0/70 caracteres</small>
                    </div>
                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" rows="3" placeholder="Descripción para buscadores (150-160 caracteres)" maxlength="170"><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
                        <small class="form-help char-count" data-target="meta_description">0/170 caracteres</small>
                    </div>
                </div>
            </div>

            <!-- LLM Optimization Card -->
            <?php
            $entity = $page ?? [];
            $entityType = 'page';
            $entityId = $page['id'] ?? null;
            include TEMPLATES_PATH . '/admin/partials/llm-qa-editor.php';
            ?>
        </div>

        <!-- Sidebar -->
        <div class="editor-sidebar">
            <div class="editor-card sticky">
                <div class="card-header">
                    <h3>Publicación</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="status">Estado</label>
                        <select id="status" name="status">
                            <option value="draft" <?= ($page['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Borrador</option>
                            <option value="published" <?= ($page['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publicada</option>
                            <option value="archived" <?= ($page['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archivada</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="template">Plantilla</label>
                        <select id="template" name="template">
                            <option value="default" <?= ($page['template'] ?? 'default') === 'default' ? 'selected' : '' ?>>Por defecto</option>
                            <option value="landing" <?= ($page['template'] ?? '') === 'landing' ? 'selected' : '' ?>>Landing Page</option>
                            <option value="full-width" <?= ($page['template'] ?? '') === 'full-width' ? 'selected' : '' ?>>Ancho completo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="show_header" value="1" <?= ($page['show_header'] ?? true) ? 'checked' : '' ?>>
                            <span>Mostrar header</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="show_footer" value="1" <?= ($page['show_footer'] ?? true) ? 'checked' : '' ?>>
                            <span>Mostrar footer</span>
                        </label>
                    </div>

                    <?php if ($page && $page['published_at']): ?>
                    <div class="meta-info">
                        <small>Publicada: <?= date('d/m/Y H:i', strtotime($page['published_at'])) ?></small>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-full">
                        <?= $page ? 'Guardar cambios' : 'Crear página' ?>
                    </button>
                    <a href="/admin/pages" class="btn btn-outline btn-full">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Block Type Selector Modal -->
<div id="block-type-modal" class="modal" style="display:none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>Seleccionar tipo de bloque</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="block-types-grid">
                <?php foreach ($blockTypes as $type => $label): ?>
                <button type="button" class="block-type-option" data-type="<?= $type ?>">
                    <span class="block-type-icon"><?= getBlockIcon($type) ?></span>
                    <span class="block-type-name"><?= htmlspecialchars($label) ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Block Editor Modal -->
<div id="block-editor-modal" class="modal" style="display:none;">
    <div class="modal-overlay"></div>
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3 id="block-editor-title">Editar bloque</h3>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="block-editor-body">
            <!-- Block form loaded via AJAX -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" id="block-editor-cancel">Cancelar</button>
            <button type="button" class="btn btn-primary" id="block-editor-save">Guardar bloque</button>
        </div>
    </div>
</div>

<?php
function getBlockIcon(string $type): string {
    $icons = [
        'hero' => '&#127937;',              // 🏁
        'text_image_left' => '&#9635;',     // ◳
        'text_image_right' => '&#9634;',    // ◲
        'text_full_width' => '&#9644;',     // ▬
        'areas' => '&#9632;',               // ■
        'services' => '&#128736;',          // 🔧 (Servicios)
        'success_cases' => '&#10004;',      // ✔
        'faq' => '&#10067;',                // ❓
        'posts' => '&#128240;',             // 📰
        'business_types' => '&#9881;',      // ⚙ (Características Resumidas)
        'tools' => '&#128295;',             // 🔧 (Herramientas)
        'cta_banner' => '&#128226;',        // 📢
        'benefits' => '&#9733;',            // ★ (Características Detalladas)
        'video' => '&#9658;',               // ▶
        'clients' => '&#128101;',           // 👥
        'team' => '&#128106;',              // 👪
        'landings' => '&#128640;',          // 🚀
        'contact_form' => '&#128231;',      // ✉
        'custom_html' => '&#60;&#47;&#62;', // </>
    ];
    return $icons[$type] ?? '&#9632;';
}
?>

<script src="/assets/js/block-editor.js?v=<?= time() ?>"></script>
<script>
function generateSEO(entityType, entityId) {
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

    fetch('/admin/seo/generate-single', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            entity_type: entityType,
            entity_id: entityId,
            language: 'es',
            overwrite: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            // Update form fields
            if (data.data.meta_title) {
                document.getElementById('meta_title').value = data.data.meta_title;
            }
            if (data.data.meta_description) {
                document.getElementById('meta_description').value = data.data.meta_description;
            }
            // Update character counts
            document.querySelectorAll('.char-count').forEach(el => {
                const targetId = el.dataset.target;
                const input = document.getElementById(targetId);
                if (input) {
                    el.textContent = input.value.length + '/' + input.maxLength + ' caracteres';
                }
            });
            alert('SEO generado correctamente. Revisa los campos y guarda la página.');
        } else {
            alert('Error: ' + (data.message || 'No se pudo generar el SEO'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión al generar SEO');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>
