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
        this.multipleMode = false;
        this.selectedUrls = [];
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
                                <p class="upload-text">Arrastra una imagen aquí o</p>
                                <label class="btn btn-primary">
                                    <span class="upload-btn-text">Seleccionar archivo</span>
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
        fileInput.addEventListener('change', (e) => {
            if (this.multipleMode && e.target.files.length > 1) {
                this.handleMultipleFileSelect(e.target.files);
            } else if (e.target.files.length > 0) {
                this.handleFileSelect(e.target.files[0]);
            }
        });

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
                if (this.multipleMode && e.dataTransfer.files.length > 1) {
                    this.handleMultipleFileSelect(e.dataTransfer.files);
                } else {
                    this.handleFileSelect(e.dataTransfer.files[0]);
                }
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
                if (this.multipleMode) {
                    this.selectedUrls.push(result.media.url);
                } else {
                    this.selectedUrl = result.media.url;
                }
                this.modal.querySelector('#confirmPickerBtn').disabled = false;

                // Show preview in upload area
                if (this.multipleMode) {
                    uploadProgress.innerHTML = `
                        <div class="upload-success">
                            <img src="${result.media.url}" alt="Uploaded">
                            <p class="success-text"><i class="fas fa-check-circle"></i> Imagen añadida (${this.selectedUrls.length} seleccionadas)</p>
                            <button type="button" class="btn btn-outline btn-sm" id="uploadMoreBtn">
                                <i class="fas fa-plus"></i> Subir más
                            </button>
                        </div>
                    `;
                    // Bind upload more button
                    uploadProgress.querySelector('#uploadMoreBtn')?.addEventListener('click', () => {
                        uploadArea.style.display = 'block';
                        uploadProgress.style.display = 'none';
                        this.modal.querySelector('#imageFileInput').value = '';
                    });
                } else {
                    uploadProgress.innerHTML = `
                        <div class="upload-success">
                            <img src="${result.media.url}" alt="Uploaded">
                            <p class="success-text"><i class="fas fa-check-circle"></i> Imagen subida correctamente</p>
                        </div>
                    `;
                }
            } else {
                throw new Error(result.error || 'Error al subir la imagen');
            }
        } catch (error) {
            uploadArea.style.display = 'block';
            uploadProgress.style.display = 'none';
            alert(error.message || 'Error al subir la imagen');
        }
    }

    async handleMultipleFileSelect(files) {
        if (!files || files.length === 0) return;

        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
        const validFiles = Array.from(files).filter(file => {
            if (!validTypes.includes(file.type)) {
                console.warn(`Archivo ignorado (tipo no válido): ${file.name}`);
                return false;
            }
            if (file.size > 10 * 1024 * 1024) {
                console.warn(`Archivo ignorado (muy grande): ${file.name}`);
                return false;
            }
            return true;
        });

        if (validFiles.length === 0) {
            alert('Ningún archivo válido seleccionado. Use PNG, JPG, GIF, SVG o WebP (max 10MB).');
            return;
        }

        // Show progress
        const uploadArea = this.modal.querySelector('#uploadArea');
        const uploadProgress = this.modal.querySelector('#uploadProgress');
        uploadArea.style.display = 'none';
        uploadProgress.style.display = 'block';
        uploadProgress.innerHTML = `
            <div class="progress-bar">
                <div class="progress-fill" style="width: 0%"></div>
            </div>
            <p class="progress-text">Subiendo 0 de ${validFiles.length} imágenes...</p>
        `;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_csrf_token"]')?.value
            || document.querySelector('input[name="_csrf"]')?.value;

        const uploadedUrls = [];
        let completed = 0;

        for (const file of validFiles) {
            try {
                const formData = new FormData();
                formData.append('file', file);
                if (csrfToken) {
                    formData.append('_csrf_token', csrfToken);
                }

                const response = await fetch('/admin/media/upload', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    uploadedUrls.push(result.media.url);
                }
            } catch (error) {
                console.error(`Error subiendo ${file.name}:`, error);
            }

            completed++;
            const progressPercent = (completed / validFiles.length) * 100;
            uploadProgress.querySelector('.progress-fill').style.width = `${progressPercent}%`;
            uploadProgress.querySelector('.progress-text').textContent =
                `Subiendo ${completed} de ${validFiles.length} imágenes...`;
        }

        if (uploadedUrls.length > 0) {
            this.selectedUrls = [...this.selectedUrls, ...uploadedUrls];
            this.modal.querySelector('#confirmPickerBtn').disabled = false;

            // Show success with thumbnails
            uploadProgress.innerHTML = `
                <div class="upload-success multiple">
                    <div class="uploaded-thumbnails">
                        ${uploadedUrls.map(url => `<img src="${url}" alt="Uploaded">`).join('')}
                    </div>
                    <p class="success-text"><i class="fas fa-check-circle"></i> ${uploadedUrls.length} imágenes subidas correctamente</p>
                </div>
            `;
        } else {
            uploadArea.style.display = 'block';
            uploadProgress.style.display = 'none';
            alert('No se pudo subir ninguna imagen');
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

        // Determine which items are selected based on mode
        const isSelected = (url) => {
            if (this.multipleMode) {
                return this.selectedUrls.includes(url);
            }
            return this.selectedUrl === url;
        };

        grid.innerHTML = filtered.map(media => `
            <div class="library-item ${isSelected(media.url) ? 'selected' : ''}" data-url="${media.url}">
                <img src="${media.url}" alt="${media.alt_text || media.original_filename || ''}">
                <div class="library-item-info">
                    <span class="filename">${media.original_filename || media.filename}</span>
                </div>
                ${this.multipleMode ? '<div class="library-item-check"><i class="fas fa-check"></i></div>' : ''}
            </div>
        `).join('');

        // Bind click events
        grid.querySelectorAll('.library-item').forEach(item => {
            item.addEventListener('click', () => {
                const url = item.dataset.url;

                if (this.multipleMode) {
                    // Toggle selection in multiple mode
                    const index = this.selectedUrls.indexOf(url);
                    if (index > -1) {
                        this.selectedUrls.splice(index, 1);
                        item.classList.remove('selected');
                    } else {
                        this.selectedUrls.push(url);
                        item.classList.add('selected');
                    }
                    this.modal.querySelector('#confirmPickerBtn').disabled = this.selectedUrls.length === 0;

                    // Update count display
                    this.updateSelectionCount();
                } else {
                    // Single selection mode
                    grid.querySelectorAll('.library-item').forEach(i => i.classList.remove('selected'));
                    item.classList.add('selected');
                    this.selectedUrl = url;
                    this.modal.querySelector('#confirmPickerBtn').disabled = false;
                }
            });
        });
    }

    updateSelectionCount() {
        let countDisplay = this.modal.querySelector('.selection-count');
        if (!countDisplay) {
            countDisplay = document.createElement('div');
            countDisplay.className = 'selection-count';
            this.modal.querySelector('.image-picker-footer').prepend(countDisplay);
        }
        if (this.selectedUrls.length > 0) {
            countDisplay.innerHTML = `<i class="fas fa-images"></i> ${this.selectedUrls.length} imágenes seleccionadas`;
            countDisplay.style.display = 'block';
        } else {
            countDisplay.style.display = 'none';
        }
    }

    filterLibrary(query) {
        this.renderLibrary(query);
    }

    confirmSelection() {
        // Handle multiple mode
        if (this.multipleMode) {
            if (this.selectedUrls.length === 0) return;

            if (this.onSelectMultipleCallback) {
                this.onSelectMultipleCallback(this.selectedUrls);
                this.close();
                return;
            }
        } else {
            if (!this.selectedUrl) return;

            // If callback mode (for WYSIWYG), call the callback
            if (this.onSelectCallback) {
                this.onSelectCallback(this.selectedUrl);
                this.close();
                return;
            }
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
        this.multipleMode = false;
        this.onSelectCallback = callback;

        // Reset modal state
        this.switchTab('upload');
        this.modal.querySelector('#imageFileInput').value = '';
        this.modal.querySelector('#imageFileInput').removeAttribute('multiple');
        this.modal.querySelector('#externalUrlInput').value = '';
        this.modal.querySelector('#urlPreview').innerHTML = '';
        this.modal.querySelector('#confirmPickerBtn').disabled = true;
        this.modal.querySelector('#uploadProgress').style.display = 'none';
        this.modal.querySelector('#uploadArea').style.display = 'block';

        // Update text for single mode
        this.modal.querySelector('.upload-text').textContent = 'Arrastra una imagen aquí o';
        this.modal.querySelector('.upload-btn-text').textContent = 'Seleccionar archivo';

        // Load library
        this.loadLibrary();

        // Show modal
        this.modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Open picker for multiple image selection (galleries)
     * @param {Function} callback - Called with array of selected URLs
     */
    openForMultipleSelection(callback) {
        this.currentInput = null;
        this.currentPicker = null;
        this.selectedUrl = null;
        this.selectedUrls = [];
        this.multipleMode = true;
        this.onSelectMultipleCallback = callback;
        this.onSelectCallback = null;

        // Reset modal state
        this.switchTab('upload');
        const fileInput = this.modal.querySelector('#imageFileInput');
        fileInput.value = '';
        fileInput.setAttribute('multiple', 'multiple');

        this.modal.querySelector('#externalUrlInput').value = '';
        this.modal.querySelector('#urlPreview').innerHTML = '';
        this.modal.querySelector('#confirmPickerBtn').disabled = true;
        this.modal.querySelector('#uploadProgress').style.display = 'none';
        this.modal.querySelector('#uploadArea').style.display = 'block';

        // Update text for multiple mode
        this.modal.querySelector('.upload-text').textContent = 'Arrastra imágenes aquí o';
        this.modal.querySelector('.upload-btn-text').textContent = 'Seleccionar archivos';

        // Remove selection count if exists
        const countDisplay = this.modal.querySelector('.selection-count');
        if (countDisplay) countDisplay.remove();

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
        this.onSelectMultipleCallback = null;
        this.multipleMode = false;
        this.selectedUrls = [];
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
