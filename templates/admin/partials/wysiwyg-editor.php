<?php
/**
 * WYSIWYG Editor Partial
 * Rich text editor with HTML toggle
 * Omniwallet CMS
 *
 * Variables:
 * - $editorId: unique ID for the editor
 * - $editorName: name attribute for the form
 * - $editorContent: initial content
 * - $editorRows: textarea rows (default 20)
 * - $editorPlaceholder: placeholder text
 */

$editorId = $editorId ?? 'editor-' . uniqid();
$editorName = $editorName ?? 'content';
$editorContent = $editorContent ?? '';
$editorRows = $editorRows ?? 20;
$editorPlaceholder = $editorPlaceholder ?? 'Escribe el contenido...';
?>

<div class="wysiwyg-editor" data-editor-id="<?= $editorId ?>">
    <div class="wysiwyg-toolbar">
        <div class="toolbar-group">
            <button type="button" class="toolbar-btn" data-command="bold" title="Negrita (Ctrl+B)">
                <i class="fas fa-bold"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="italic" title="Cursiva (Ctrl+I)">
                <i class="fas fa-italic"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="underline" title="Subrayado (Ctrl+U)">
                <i class="fas fa-underline"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="strikeThrough" title="Tachado">
                <i class="fas fa-strikethrough"></i>
            </button>
        </div>
        <div class="toolbar-separator"></div>
        <div class="toolbar-group">
            <select class="toolbar-select" data-command="formatBlock" title="Formato">
                <option value="">Formato</option>
                <option value="p">Parrafo</option>
                <option value="h1">Titulo 1</option>
                <option value="h2">Titulo 2</option>
                <option value="h3">Titulo 3</option>
                <option value="h4">Titulo 4</option>
                <option value="blockquote">Cita</option>
                <option value="pre">Codigo</option>
            </select>
        </div>
        <div class="toolbar-separator"></div>
        <div class="toolbar-group">
            <button type="button" class="toolbar-btn" data-command="insertUnorderedList" title="Lista con viñetas">
                <i class="fas fa-list-ul"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="insertOrderedList" title="Lista numerada">
                <i class="fas fa-list-ol"></i>
            </button>
        </div>
        <div class="toolbar-separator"></div>
        <div class="toolbar-group">
            <button type="button" class="toolbar-btn" data-command="justifyLeft" title="Alinear izquierda">
                <i class="fas fa-align-left"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="justifyCenter" title="Centrar">
                <i class="fas fa-align-center"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="justifyRight" title="Alinear derecha">
                <i class="fas fa-align-right"></i>
            </button>
        </div>
        <div class="toolbar-separator"></div>
        <div class="toolbar-group">
            <button type="button" class="toolbar-btn" data-command="createLink" title="Insertar enlace">
                <i class="fas fa-link"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="unlink" title="Eliminar enlace">
                <i class="fas fa-unlink"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="insertImage" title="Insertar imagen">
                <i class="fas fa-image"></i>
            </button>
        </div>
        <div class="toolbar-separator"></div>
        <div class="toolbar-group">
            <button type="button" class="toolbar-btn" data-command="removeFormat" title="Limpiar formato">
                <i class="fas fa-eraser"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="undo" title="Deshacer (Ctrl+Z)">
                <i class="fas fa-undo"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="redo" title="Rehacer (Ctrl+Y)">
                <i class="fas fa-redo"></i>
            </button>
        </div>
        <div class="toolbar-spacer"></div>
        <div class="toolbar-group">
            <button type="button" class="toolbar-btn toggle-fullscreen" title="Pantalla completa">
                <i class="fas fa-expand"></i>
            </button>
            <button type="button" class="toolbar-btn toggle-mode" title="Ver HTML">
                <i class="fas fa-code"></i>
                <span>HTML</span>
            </button>
        </div>
    </div>

    <div class="wysiwyg-content-area">
        <div class="wysiwyg-visual" contenteditable="true" data-placeholder="<?= htmlspecialchars($editorPlaceholder) ?>"></div>
        <textarea class="wysiwyg-source" name="<?= htmlspecialchars($editorName) ?>" id="<?= $editorId ?>" rows="<?= $editorRows ?>" placeholder="<?= htmlspecialchars($editorPlaceholder) ?>"><?= htmlspecialchars($editorContent) ?></textarea>
    </div>
</div>

<style>
.wysiwyg-editor {
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: white;
}

.wysiwyg-editor.fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    border-radius: 0;
    display: flex;
    flex-direction: column;
}

.wysiwyg-editor.fullscreen .wysiwyg-content-area {
    flex: 1;
    display: flex;
}

.wysiwyg-editor.fullscreen .wysiwyg-visual,
.wysiwyg-editor.fullscreen .wysiwyg-source {
    height: 100%;
    max-height: none;
}

.wysiwyg-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.toolbar-group {
    display: flex;
    gap: 2px;
}

.toolbar-separator {
    width: 1px;
    height: 24px;
    background: var(--color-gray-300);
    margin: 0 4px;
}

.toolbar-spacer {
    flex: 1;
}

.toolbar-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: auto;
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    background: white;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all 0.15s ease;
    color: var(--color-gray-700);
    font-size: 13px;
}

.toolbar-btn:hover {
    background: var(--color-primary-light);
    border-color: var(--color-primary);
    color: var(--color-primary);
}

.toolbar-btn.active {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: white;
}

.toolbar-btn i {
    font-size: 14px;
}

.toolbar-btn span {
    font-size: 12px;
    font-weight: 500;
}

.toolbar-select {
    height: 32px;
    padding: 0 8px;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-sm);
    background: white;
    font-size: 13px;
    cursor: pointer;
}

.toolbar-select:hover {
    border-color: var(--color-primary);
}

.wysiwyg-content-area {
    position: relative;
    min-height: 300px;
}

.wysiwyg-visual {
    width: 100%;
    min-height: 300px;
    max-height: 600px;
    overflow-y: auto;
    padding: 16px;
    font-size: 15px;
    line-height: 1.7;
    outline: none;
}

.wysiwyg-visual:empty:before {
    content: attr(data-placeholder);
    color: var(--color-gray-400);
    pointer-events: none;
}

.wysiwyg-visual:focus {
    outline: none;
}

.wysiwyg-visual h1,
.wysiwyg-visual h2,
.wysiwyg-visual h3,
.wysiwyg-visual h4 {
    margin-top: 1.5em;
    margin-bottom: 0.5em;
}

.wysiwyg-visual h1:first-child,
.wysiwyg-visual h2:first-child,
.wysiwyg-visual h3:first-child,
.wysiwyg-visual h4:first-child {
    margin-top: 0;
}

.wysiwyg-visual p {
    margin-bottom: 1em;
}

.wysiwyg-visual ul,
.wysiwyg-visual ol {
    padding-left: 1.5em;
    margin-bottom: 1em;
}

.wysiwyg-visual blockquote {
    border-left: 4px solid var(--color-primary);
    padding-left: 1em;
    margin: 1em 0;
    color: var(--color-gray-600);
    font-style: italic;
}

.wysiwyg-visual pre {
    background: var(--color-gray-100);
    padding: 1em;
    border-radius: var(--radius-md);
    overflow-x: auto;
    font-family: monospace;
}

.wysiwyg-visual img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-md);
}

.wysiwyg-visual a {
    color: var(--color-primary);
    text-decoration: underline;
}

.wysiwyg-source {
    display: none;
    width: 100%;
    min-height: 300px;
    max-height: 600px;
    padding: 16px;
    border: none;
    resize: vertical;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 13px;
    line-height: 1.6;
    tab-size: 2;
    background: var(--color-gray-900);
    color: #f8f8f2;
}

.wysiwyg-source:focus {
    outline: none;
}

.wysiwyg-editor.html-mode .wysiwyg-visual {
    display: none;
}

.wysiwyg-editor.html-mode .wysiwyg-source {
    display: block;
}

.wysiwyg-editor.html-mode .toggle-mode {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .wysiwyg-toolbar {
        padding: 6px;
    }

    .toolbar-separator {
        display: none;
    }

    .toolbar-btn span {
        display: none;
    }

    .toolbar-btn {
        min-width: 28px;
        height: 28px;
        padding: 0 6px;
    }
}
</style>

<script>
(function() {
    document.querySelectorAll('.wysiwyg-editor').forEach(function(editor) {
        const visual = editor.querySelector('.wysiwyg-visual');
        const source = editor.querySelector('.wysiwyg-source');
        const toolbar = editor.querySelector('.wysiwyg-toolbar');
        const toggleModeBtn = editor.querySelector('.toggle-mode');
        const toggleFullscreenBtn = editor.querySelector('.toggle-fullscreen');

        // Initialize visual content from source
        visual.innerHTML = source.value;

        // Sync visual to source on input
        visual.addEventListener('input', function() {
            source.value = visual.innerHTML;
        });

        // Also sync on blur
        visual.addEventListener('blur', function() {
            source.value = visual.innerHTML;
        });

        // Toolbar button commands
        toolbar.querySelectorAll('.toolbar-btn[data-command]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const command = this.dataset.command;

                if (editor.classList.contains('html-mode')) {
                    return; // Don't execute commands in HTML mode
                }

                visual.focus();

                if (command === 'createLink') {
                    const url = prompt('Introduce la URL del enlace:', 'https://');
                    if (url) {
                        document.execCommand('createLink', false, url);
                    }
                } else if (command === 'insertImage') {
                    // Save selection before opening modal
                    const selection = window.getSelection();
                    const range = selection.rangeCount > 0 ? selection.getRangeAt(0).cloneRange() : null;

                    // Use ImagePicker if available
                    if (window.imagePicker && window.imagePicker.openForEditor) {
                        window.imagePicker.openForEditor(function(url) {
                            // Restore selection
                            visual.focus();
                            if (range) {
                                selection.removeAllRanges();
                                selection.addRange(range);
                            }
                            // Insert image
                            document.execCommand('insertImage', false, url);
                            source.value = visual.innerHTML;
                        });
                    } else {
                        // Fallback to prompt
                        const url = prompt('Introduce la URL de la imagen:', 'https://');
                        if (url) {
                            document.execCommand('insertImage', false, url);
                        }
                    }
                    return; // Don't sync immediately, wait for callback
                } else {
                    document.execCommand(command, false, null);
                }

                source.value = visual.innerHTML;
                updateToolbarState();
            });
        });

        // Format block select
        toolbar.querySelectorAll('.toolbar-select[data-command]').forEach(function(select) {
            select.addEventListener('change', function() {
                if (editor.classList.contains('html-mode')) {
                    this.value = '';
                    return;
                }

                const command = this.dataset.command;
                const value = this.value;

                if (value) {
                    visual.focus();
                    document.execCommand(command, false, value);
                    source.value = visual.innerHTML;
                }

                this.value = '';
            });
        });

        // Toggle HTML mode
        toggleModeBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (editor.classList.contains('html-mode')) {
                // Switching to visual mode
                visual.innerHTML = source.value;
                editor.classList.remove('html-mode');
                toggleModeBtn.querySelector('span').textContent = 'HTML';
            } else {
                // Switching to HTML mode
                source.value = visual.innerHTML;
                editor.classList.add('html-mode');
                toggleModeBtn.querySelector('span').textContent = 'Visual';
            }
        });

        // Toggle fullscreen
        toggleFullscreenBtn.addEventListener('click', function(e) {
            e.preventDefault();
            editor.classList.toggle('fullscreen');

            const icon = this.querySelector('i');
            if (editor.classList.contains('fullscreen')) {
                icon.classList.remove('fa-expand');
                icon.classList.add('fa-compress');
            } else {
                icon.classList.remove('fa-compress');
                icon.classList.add('fa-expand');
            }
        });

        // ESC to exit fullscreen
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && editor.classList.contains('fullscreen')) {
                editor.classList.remove('fullscreen');
                const icon = toggleFullscreenBtn.querySelector('i');
                icon.classList.remove('fa-compress');
                icon.classList.add('fa-expand');
            }
        });

        // Update toolbar state based on selection
        function updateToolbarState() {
            toolbar.querySelectorAll('.toolbar-btn[data-command]').forEach(function(btn) {
                const command = btn.dataset.command;
                if (['bold', 'italic', 'underline', 'strikeThrough'].includes(command)) {
                    if (document.queryCommandState(command)) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                }
            });
        }

        visual.addEventListener('mouseup', updateToolbarState);
        visual.addEventListener('keyup', updateToolbarState);

        // Keyboard shortcuts
        visual.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key.toLowerCase()) {
                    case 'b':
                        e.preventDefault();
                        document.execCommand('bold', false, null);
                        source.value = visual.innerHTML;
                        updateToolbarState();
                        break;
                    case 'i':
                        e.preventDefault();
                        document.execCommand('italic', false, null);
                        source.value = visual.innerHTML;
                        updateToolbarState();
                        break;
                    case 'u':
                        e.preventDefault();
                        document.execCommand('underline', false, null);
                        source.value = visual.innerHTML;
                        updateToolbarState();
                        break;
                }
            }
        });

        // Prevent form submission on enter in toolbar
        toolbar.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        // Ensure content is synced before form submission
        const form = editor.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                if (!editor.classList.contains('html-mode')) {
                    source.value = visual.innerHTML;
                }
            });
        }
    });
})();
</script>
