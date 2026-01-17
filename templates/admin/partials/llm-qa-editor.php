<?php
/**
 * LLM Q&A Editor Partial
 * Reusable Q&A editor component for admin forms
 *
 * Required variables:
 * - $entity: The entity data (page, post, feature, etc.)
 * - $entityType: Type string (page, post, feature, integration, success_case, knowledge_article)
 * - $entityId: The entity ID (can be null for new entities)
 */

$enableLlmQa = $entity['enable_llm_qa'] ?? false;
$llmQaContent = $entity['llm_qa_content'] ?? '[]';
$generateUrl = $entityId ? "/admin/{$entityType}s/{$entityId}/generate-qa" : '';

// Handle special pluralization
$urlPath = match($entityType) {
    'success_case' => 'cases',
    'knowledge_article' => 'knowledge',
    'category' => 'categories',
    default => $entityType . 's'
};
$generateUrl = $entityId ? "/admin/{$urlPath}/{$entityId}/generate-qa" : '';
$uniqueId = $entityType . '-' . ($entityId ?? 'new');
?>

<!-- LLM Optimization Card -->
<div class="editor-card llm-qa-card" id="llm-qa-card-<?= $uniqueId ?>">
    <div class="card-header collapsible" onclick="toggleLlmCard('<?= $uniqueId ?>')">
        <h3>Optimización para LLMs (IA)</h3>
        <i class="fas fa-chevron-down collapse-icon"></i>
    </div>
    <div class="card-body" id="llm-content-<?= $uniqueId ?>">
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="enable_llm_qa" value="1" <?= $enableLlmQa ? 'checked' : '' ?> id="enable_llm_qa_<?= $uniqueId ?>" onchange="toggleLlmEditor('<?= $uniqueId ?>')">
                <span>Activar bloque Q&A para LLMs</span>
            </label>
            <small class="form-hint">Genera contenido estructurado que ayuda a los asistentes de IA a entender y citar esta página.</small>
        </div>
        <div class="llm-qa-editor-container" id="llm-qa-editor-<?= $uniqueId ?>" style="<?= $enableLlmQa ? '' : 'display:none' ?>">
            <div class="qa-editor-header">
                <label>Preguntas y Respuestas</label>
                <?php if ($entityId): ?>
                <button type="button" class="btn btn-sm btn-secondary generate-qa-btn" data-entity-id="<?= $entityId ?>" data-generate-url="<?= $generateUrl ?>" data-unique-id="<?= $uniqueId ?>">
                    <i class="fas fa-magic"></i> Generar con IA
                </button>
                <?php endif; ?>
            </div>
            <div class="qa-items-container" id="qa-items-<?= $uniqueId ?>">
                <!-- QA items loaded via JS -->
            </div>
            <div class="qa-editor-actions">
                <button type="button" class="btn btn-sm btn-outline add-qa-btn" data-unique-id="<?= $uniqueId ?>">+ Añadir pregunta</button>
            </div>
        </div>
        <input type="hidden" name="llm_qa_content" id="llm_qa_content_<?= $uniqueId ?>" value="<?= htmlspecialchars($llmQaContent) ?>">
    </div>
</div>

<style>
.llm-qa-card .card-header {
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.llm-qa-card .collapse-icon {
    transition: transform 0.3s ease;
}
.llm-qa-card .collapse-icon.rotated {
    transform: rotate(180deg);
}
.llm-qa-card .card-body.collapsed {
    display: none;
}
.qa-editor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-md);
}
.qa-items-container {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-md);
}
.qa-item {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: var(--spacing-sm);
    padding: var(--spacing-md);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
}
.qa-item .qa-fields {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}
.qa-item input,
.qa-item textarea {
    width: 100%;
}
.qa-item .remove-qa-btn {
    align-self: start;
}
</style>

<script>
(function() {
    const uniqueId = '<?= $uniqueId ?>';
    const initialContent = <?= $llmQaContent ?: '[]' ?>;

    // Toggle card collapse
    window.toggleLlmCard = function(id) {
        const content = document.getElementById('llm-content-' + id);
        const card = document.getElementById('llm-qa-card-' + id);
        const icon = card.querySelector('.collapse-icon');

        content.classList.toggle('collapsed');
        icon.classList.toggle('rotated');
    };

    // Toggle editor visibility based on checkbox
    window.toggleLlmEditor = function(id) {
        const checkbox = document.getElementById('enable_llm_qa_' + id);
        const editor = document.getElementById('llm-qa-editor-' + id);
        editor.style.display = checkbox.checked ? '' : 'none';
    };

    // Add Q&A item
    function addQAItem(id, question, answer) {
        const container = document.getElementById('qa-items-' + id);
        const index = container.children.length;

        const itemHtml = `
            <div class="qa-item" data-index="${index}">
                <div class="qa-fields">
                    <input type="text" class="qa-question" value="${escapeHtml(question || '')}" placeholder="Pregunta" onchange="updateLlmQAContent('${id}')">
                    <textarea class="qa-answer" rows="2" placeholder="Respuesta" onchange="updateLlmQAContent('${id}')">${escapeHtml(answer || '')}</textarea>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-qa-btn" onclick="removeQAItem(this, '${id}')">&times;</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
    }

    // Remove Q&A item
    window.removeQAItem = function(btn, id) {
        btn.closest('.qa-item').remove();
        updateLlmQAContent(id);
    };

    // Update hidden input with Q&A content
    window.updateLlmQAContent = function(id) {
        const container = document.getElementById('qa-items-' + id);
        const items = container.querySelectorAll('.qa-item');
        const data = [];

        items.forEach(function(item) {
            const question = item.querySelector('.qa-question').value.trim();
            const answer = item.querySelector('.qa-answer').value.trim();
            if (question || answer) {
                data.push({ question, answer });
            }
        });

        document.getElementById('llm_qa_content_' + id).value = JSON.stringify(data);
    };

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        // Load existing Q&A items
        if (Array.isArray(initialContent)) {
            initialContent.forEach(function(qa) {
                addQAItem(uniqueId, qa.question, qa.answer);
            });
        }

        // Ensure Q&A content is synced before form submission
        const hiddenInput = document.getElementById('llm_qa_content_' + uniqueId);
        if (hiddenInput) {
            const form = hiddenInput.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    updateLlmQAContent(uniqueId);
                });
            }
        }

        // Bind add button
        const addBtn = document.querySelector('.add-qa-btn[data-unique-id="' + uniqueId + '"]');
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                addQAItem(uniqueId, '', '');
            });
        }

        // Bind generate button
        const generateBtn = document.querySelector('.generate-qa-btn[data-unique-id="' + uniqueId + '"]');
        if (generateBtn) {
            generateBtn.addEventListener('click', function() {
                const btn = this;
                const generateUrl = btn.dataset.generateUrl;
                const csrfToken = document.querySelector('input[name="_csrf_token"]').value;
                const originalText = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

                fetch(generateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-Token': csrfToken
                    },
                    body: '_csrf_token=' + encodeURIComponent(csrfToken)
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;

                    if (data.success && data.qa_items) {
                        // Clear existing items
                        document.getElementById('qa-items-' + uniqueId).innerHTML = '';

                        // Add new items
                        data.qa_items.forEach(function(qa) {
                            addQAItem(uniqueId, qa.question, qa.answer);
                        });

                        updateLlmQAContent(uniqueId);
                        alert('Q&A generado correctamente. Revisa y guarda los cambios.');
                    } else {
                        alert(data.error || 'Error al generar Q&A');
                    }
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    alert('Error de conexión');
                    console.error('Generate Q&A error:', error);
                });
            });
        }
    });
})();
</script>
