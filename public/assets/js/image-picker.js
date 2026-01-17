/**
 * Image Picker Component
 * Allows uploading and selecting images inline
 * Omniwallet CMS
 */
class ImagePicker {
    constructor() {
        this.modal = null;
        this.currentInput = null;
        this.mediaLibrary = [];
        this.init();
    }

    init() {
        // Create modal once
        this.createModal();

        // Bind to all image pickers
        this.bindPickers();

        // Observe DOM for dynamically added pickers (e.g., hero slides)
        this.observeDOM();
    }

    createModal() {
        const modalHTML = `
            <div class="image-picker-modal" id="imagePickerModal">
                <div class="image-picker-overlay"></div>
                <div class="image-picker-content">
                    <div class="image-picker-header">
                        <h3>Seleccionar imagen</h3>
                        <button type="button" class="image-picker-close">&times;</button>
                    </div>
                    <div class="image-picker-tabs">
                        <button type="button" class="tab-btn active" data-tab="upload">Subir imagen</button>
                        <button type="button" class="tab-btn" data-tab="library">Biblioteca</button>
                        <button type="button" class="tab-btn" data-tab="url">URL externa</button>
                    </div>
                    <div class="image-picker-body">
                        <!-- Upload Tab -->
                        <div class="tab-content active" id="tab-upload">
                            <div class="upload-area" id="uploadArea">
                                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <p>Arrastra una imagen aquí o</p>
                                <label class="btn btn-primary">
                                    Seleccionar archivo
                                    <input type="file" id="imageFileInput" accept="image/*" hidden>
                                </label>
                                <p class="upload-hint">PNG, JPG, GIF, SVG, WebP (max 10MB)</p>
                            </div>
                            <div class="upload-progress" id="uploadProgress" style="display: none;">
                                <div class="progress-bar">
                                    <div class="progress-fill"></div>
                                </div>
                                <p class="progress-text">Subiendo...</p>
                            </div>
                        </div>

                        <!-- Library Tab -->
                        <div class="tab-content" id="tab-library">
                            <div class="library-search">
                                <input type="text" id="librarySearch" placeholder="Buscar imagen...">
                            </div>
                            <div class="library-grid" id="libraryGrid">
                                <div class="library-loading">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando imágenes...
                                </div>
                            </div>
                        </div>

                        <!-- URL Tab -->
                        <div class="tab-content" id="tab-url">
                            <div class="url-input-group">
                                <label>URL de la imagen</label>
                                <input type="url" id="externalUrlInput" placeholder="https://example.com/imagen.jpg">
                                <div class="url-preview" id="urlPreview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="image-picker-footer">
                        <button type="button" class="btn btn-outline" id="cancelPickerBtn">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirmPickerBtn" disabled>Seleccionar</button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('imagePickerModal');
        this.bindModalEvents();
    }

    bindModalEvents() {
        // Close modal
        this.modal.querySelector('.image-picker-overlay').addEventListener('click', () => this.close());
        this.modal.querySelector('.image-picker-close').addEventListener('click', () => this.close());
        this.modal.querySelector('#cancelPickerBtn').addEventListener('click', () => this.close());

        // Tab switching
        this.modal.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchTab(e.target.dataset.tab));
        });

        // File upload
        const fileInput = this.modal.querySelector('#imageFileInput');
        fileInput.addEventListener('change', (e) => this.handleFileSelect(e.target.files[0]));

        // Drag and drop
        const uploadArea = this.modal.querySelector('#uploadArea');
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                this.handleFileSelect(e.dataTransfer.files[0]);
            }
        });

        // URL input
        const urlInput = this.modal.querySelector('#externalUrlInput');
        urlInput.addEventListener('input', () => this.handleUrlInput(urlInput.value));

        // Library search
        const searchInput = this.modal.querySelector('#librarySearch');
        searchInput.addEventListener('input', () => this.filterLibrary(searchInput.value));

        // Confirm selection
        this.modal.querySelector('#confirmPickerBtn').addEventListener('click', () => this.confirmSelection());
    }

    bindPickers() {
        document.querySelectorAll('.image-picker-field').forEach(picker => {
            if (!picker.dataset.bound) {
                this.bindSinglePicker(picker);
                picker.dataset.bound = 'true';
            }
        });
    }

    bindSinglePicker(picker) {
        const selectBtn = picker.querySelector('.image-picker-select');
        const clearBtn = picker.querySelector('.image-picker-clear');
        const input = picker.querySelector('input[type="hidden"], input[type="text"]');

        if (selectBtn) {
            selectBtn.addEventListener('click', () => this.open(input, picker));
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', () => this.clearImage(input, picker));
        }
    }

    observeDOM() {
        const observer = new MutationObserver(() => {
            this.bindPickers();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    open(input, picker) {
        this.currentInput = input;
        this.currentPicker = picker;
        this.selectedUrl = null;

        // Reset modal state
        this.switchTab('upload');
        this.modal.querySelector('#imageFileInput').value = '';
        this.modal.querySelector('#externalUrlInput').value = '';
        this.modal.querySelector('#urlPreview').innerHTML = '';
        this.modal.querySelector('#confirmPickerBtn').disabled = true;
        this.modal.querySelector('#uploadProgress').style.display = 'none';
        this.modal.querySelector('#uploadArea').style.display = 'block';

        // Load library
        this.loadLibrary();

        // Show modal
        this.modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    close() {
        this.modal.classList.remove('active');
        document.body.style.overflow = '';
        this.currentInput = null;
        this.currentPicker = null;
    }

    switchTab(tab) {
        // Update buttons
        this.modal.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tab);
        });

        // Update content
        this.modal.querySelectorAll('.tab-content').forEach(content => {
            content.classList.toggle('active', content.id === `tab-${tab}`);
        });

        // Load library if switching to it
        if (tab === 'library') {
            this.loadLibrary();
        }
    }

    async handleFileSelect(file) {
        if (!file) return;

        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Tipo de archivo no permitido. Use PNG, JPG, GIF, SVG o WebP.');
            return;
        }

        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('El archivo es demasiado grande. Máximo 10MB.');
            return;
        }

        // Show progress
        const uploadArea = this.modal.querySelector('#uploadArea');
        const uploadProgress = this.modal.querySelector('#uploadProgress');
        uploadArea.style.display = 'none';
        uploadProgress.style.display = 'block';

        try {
            const formData = new FormData();
            formData.append('file', file);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('input[name="_csrf_token"]')?.value
                || document.querySelector('input[name="_csrf"]')?.value;

            if (csrfToken) {
                formData.append('_csrf_token', csrfToken);
            }

            const response = await fetch('/admin/media/upload', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.selectedUrl = result.media.url;
                this.modal.querySelector('#confirmPickerBtn').disabled = false;

                // Show preview in upload area
                uploadProgress.innerHTML = `
                    <div class="upload-success">
                        <img src="${result.media.url}" alt="Uploaded">
                        <p class="success-text"><i class="fas fa-check-circle"></i> Imagen subida correctamente</p>
                    </div>
                `;
            } else {
                throw new Error(result.error || 'Error al subir la imagen');
            }
        } catch (error) {
            uploadArea.style.display = 'block';
            uploadProgress.style.display = 'none';
            alert(error.message || 'Error al subir la imagen');
        }
    }

    handleUrlInput(url) {
        const preview = this.modal.querySelector('#urlPreview');
        const confirmBtn = this.modal.querySelector('#confirmPickerBtn');

        if (!url || !this.isValidUrl(url)) {
            preview.innerHTML = '';
            confirmBtn.disabled = true;
            this.selectedUrl = null;
            return;
        }

        // Show preview
        preview.innerHTML = `
            <div class="url-preview-loading">
                <i class="fas fa-spinner fa-spin"></i> Cargando vista previa...
            </div>
        `;

        const img = new Image();
        img.onload = () => {
            preview.innerHTML = `<img src="${url}" alt="Preview">`;
            this.selectedUrl = url;
            confirmBtn.disabled = false;
        };
        img.onerror = () => {
            preview.innerHTML = `<div class="url-preview-error"><i class="fas fa-exclamation-triangle"></i> No se pudo cargar la imagen</div>`;
            confirmBtn.disabled = true;
            this.selectedUrl = null;
        };
        img.src = url;
    }

    isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    async loadLibrary() {
        const grid = this.modal.querySelector('#libraryGrid');

        // Only load if empty
        if (this.mediaLibrary.length > 0) {
            this.renderLibrary();
            return;
        }

        grid.innerHTML = '<div class="library-loading"><i class="fas fa-spinner fa-spin"></i> Cargando imágenes...</div>';

        try {
            const response = await fetch('/admin/media?type=images&format=json');
            const result = await response.json();

            if (result.success) {
                this.mediaLibrary = result.media || [];
                this.renderLibrary();
            } else {
                throw new Error(result.error || 'Error al cargar la biblioteca');
            }
        } catch (error) {
            grid.innerHTML = `<div class="library-error"><i class="fas fa-exclamation-triangle"></i> ${error.message}</div>`;
        }
    }

    renderLibrary(filter = '') {
        const grid = this.modal.querySelector('#libraryGrid');
        const filtered = filter
            ? this.mediaLibrary.filter(m =>
                m.filename?.toLowerCase().includes(filter.toLowerCase()) ||
                m.original_filename?.toLowerCase().includes(filter.toLowerCase()) ||
                m.alt_text?.toLowerCase().includes(filter.toLowerCase())
            )
            : this.mediaLibrary;

        if (filtered.length === 0) {
            grid.innerHTML = `<div class="library-empty"><i class="fas fa-images"></i><p>No hay imágenes${filter ? ' que coincidan' : ''}</p></div>`;
            return;
        }

        grid.innerHTML = filtered.map(media => `
            <div class="library-item ${this.selectedUrl === media.url ? 'selected' : ''}" data-url="${media.url}">
                <img src="${media.url}" alt="${media.alt_text || media.original_filename || ''}">
                <div class="library-item-info">
                    <span class="filename">${media.original_filename || media.filename}</span>
                </div>
            </div>
        `).join('');

        // Bind click events
        grid.querySelectorAll('.library-item').forEach(item => {
            item.addEventListener('click', () => {
                grid.querySelectorAll('.library-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                this.selectedUrl = item.dataset.url;
                this.modal.querySelector('#confirmPickerBtn').disabled = false;
            });
        });
    }

    filterLibrary(query) {
        this.renderLibrary(query);
    }

    confirmSelection() {
        if (!this.selectedUrl) return;

        // If callback mode (for WYSIWYG), call the callback
        if (this.onSelectCallback) {
            this.onSelectCallback(this.selectedUrl);
            this.close();
            return;
        }

        // Normal mode - update input
        if (!this.currentInput) return;

        // Update input value
        this.currentInput.value = this.selectedUrl;

        // Update preview
        if (this.currentPicker) {
            const preview = this.currentPicker.querySelector('.image-picker-preview');
            if (preview) {
                preview.innerHTML = `<img src="${this.selectedUrl}" alt="Preview">`;
                preview.classList.add('has-image');
            }

            // Show clear button, hide select button text
            const clearBtn = this.currentPicker.querySelector('.image-picker-clear');
            if (clearBtn) clearBtn.style.display = 'flex';
        }

        // Trigger change event
        this.currentInput.dispatchEvent(new Event('change', { bubbles: true }));

        this.close();
    }

    /**
     * Open picker for WYSIWYG editor with callback
     * @param {Function} callback - Called with selected URL
     */
    openForEditor(callback) {
        this.currentInput = null;
        this.currentPicker = null;
        this.selectedUrl = null;
        this.onSelectCallback = callback;

        // Reset modal state
        this.switchTab('upload');
        this.modal.querySelector('#imageFileInput').value = '';
        this.modal.querySelector('#externalUrlInput').value = '';
        this.modal.querySelector('#urlPreview').innerHTML = '';
        this.modal.querySelector('#confirmPickerBtn').disabled = true;
        this.modal.querySelector('#uploadProgress').style.display = 'none';
        this.modal.querySelector('#uploadArea').style.display = 'block';

        // Load library
        this.loadLibrary();

        // Show modal
        this.modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    close() {
        this.modal.classList.remove('active');
        document.body.style.overflow = '';
        this.currentInput = null;
        this.currentPicker = null;
        this.onSelectCallback = null;
    }

    clearImage(input, picker) {
        // Clear input
        input.value = '';

        // Clear preview
        const preview = picker.querySelector('.image-picker-preview');
        if (preview) {
            preview.innerHTML = `
                <div class="preview-placeholder">
                    <i class="fas fa-image"></i>
                    <span>Sin imagen</span>
                </div>
            `;
            preview.classList.remove('has-image');
        }

        // Hide clear button
        const clearBtn = picker.querySelector('.image-picker-clear');
        if (clearBtn) clearBtn.style.display = 'none';

        // Trigger change event
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.imagePicker = new ImagePicker();
});

// Helper to create image picker HTML for forms
function createImagePickerField(name, value = '', label = 'Imagen', dataAttr = '', hint = '') {
    const hasImage = value && value.length > 0;
    const dataAttribute = dataAttr ? `data-${dataAttr}` : 'name';

    return `
        <div class="form-group">
            <label>${label}</label>
            <div class="image-picker-field">
                <input type="hidden" ${dataAttribute}="${name}" value="${value || ''}">
                <div class="image-picker-preview ${hasImage ? 'has-image' : ''}">
                    ${hasImage
                        ? `<img src="${value}" alt="Preview">`
                        : `<div class="preview-placeholder"><i class="fas fa-image"></i><span>Sin imagen</span></div>`
                    }
                </div>
                <div class="image-picker-actions">
                    <button type="button" class="btn btn-sm btn-outline image-picker-select">
                        <i class="fas fa-upload"></i> ${hasImage ? 'Cambiar' : 'Seleccionar'}
                    </button>
                    <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: ${hasImage ? 'flex' : 'none'};">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                ${hint ? `<small class="form-hint">${hint}</small>` : ''}
            </div>
        </div>
    `;
}
