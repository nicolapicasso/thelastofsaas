/**
 * Omniwallet CMS - Block Editor
 * Drag & drop block management
 */

(function() {
    'use strict';

    const BlockEditor = {
        pageId: null,
        csrfToken: null,
        currentBlockId: null,
        currentBlockType: null,

        init: function() {
            console.log('BlockEditor.init() called');
            const container = document.getElementById('blocks-container');
            const csrfInput = document.querySelector('input[name="_csrf_token"]');

            // Always try to init LLM Editor (works on all admin forms)
            if (csrfInput) {
                this.csrfToken = csrfInput.value;
                this.initLLMEditor();
            }

            // Gallery handlers work via event delegation (must init before early return)
            console.log('Calling initGalleryHandlers...');
            this.initGalleryHandlers();

            // Block editor specific functionality
            if (!container) return;

            this.pageId = container.dataset.pageId;

            this.initDragDrop();
            this.initModals();
            this.initBlockActions();
            this.initCollapsible();
            this.initCharCount();
        },

        // Gallery handlers using event delegation (works for dynamically loaded content)
        initGalleryHandlers: function() {
            console.log('initGalleryHandlers() called - setting up event delegation');
            const self = this;

            // Image Gallery - Add images button (event delegation)
            document.addEventListener('click', function(e) {
                if (e.target.closest('#addGalleryImages')) {
                    e.preventDefault();

                    const form = document.getElementById('block-editor-body');
                    if (!form) return;

                    const imagesInput = form.querySelector('[data-content="images"]');
                    const imagesList = form.querySelector('#galleryImagesList');

                    if (!imagesInput || !imagesList) return;

                    // Debug logging
                    console.log('=== IMAGE PICKER DEBUG ===');
                    console.log('window.imagePicker exists:', !!window.imagePicker);
                    console.log('window.imagePicker:', window.imagePicker);
                    if (window.imagePicker) {
                        console.log('openForMultipleSelection exists:', typeof window.imagePicker.openForMultipleSelection);
                        console.log('openForEditor exists:', typeof window.imagePicker.openForEditor);
                    }

                    // Use multiple selection mode for galleries (with fallback to single)
                    if (window.imagePicker && window.imagePicker.openForMultipleSelection) {
                        window.imagePicker.openForMultipleSelection(function(urls) {
                            var images = [];
                            try {
                                images = JSON.parse(imagesInput.value) || [];
                            } catch (err) {
                                images = [];
                            }

                            // Add all selected URLs
                            urls.forEach(function(url) {
                                images.push({ url: url, alt: '' });
                            });

                            imagesInput.value = JSON.stringify(images);
                            imagesInput.dispatchEvent(new Event('change', { bubbles: true }));

                            self.renderGalleryImages(imagesList, images);
                        });
                    } else if (window.imagePicker && window.imagePicker.openForEditor) {
                        // Fallback to single selection if multiple not available
                        console.log('Fallback: using single selection mode');
                        window.imagePicker.openForEditor(function(url) {
                            var images = [];
                            try {
                                images = JSON.parse(imagesInput.value) || [];
                            } catch (err) {
                                images = [];
                            }

                            images.push({ url: url, alt: '' });

                            imagesInput.value = JSON.stringify(images);
                            imagesInput.dispatchEvent(new Event('change', { bubbles: true }));

                            self.renderGalleryImages(imagesList, images);
                        });
                    } else {
                        alert('Selector de imágenes no disponible. Por favor, recargue la página.');
                    }
                }
            });

            // Image Gallery - Remove image button (event delegation)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.gallery-image-remove')) {
                    e.preventDefault();
                    const btn = e.target.closest('.gallery-image-remove');
                    const item = btn.closest('.gallery-image-item');
                    if (!item) return;

                    const form = document.getElementById('block-editor-body');
                    if (!form) return;

                    const imagesInput = form.querySelector('[data-content="images"]');
                    const imagesList = form.querySelector('#galleryImagesList');

                    if (!imagesInput || !imagesList) return;

                    const index = parseInt(item.dataset.index);
                    var images = [];
                    try {
                        images = JSON.parse(imagesInput.value) || [];
                    } catch (err) {
                        images = [];
                    }

                    images.splice(index, 1);
                    imagesInput.value = JSON.stringify(images);
                    imagesInput.dispatchEvent(new Event('change', { bubbles: true }));

                    self.renderGalleryImages(imagesList, images);
                }
            });

            // Video Gallery - Add video button (event delegation)
            document.addEventListener('click', function(e) {
                if (e.target.closest('#addVgalleryVideo')) {
                    e.preventDefault();
                    console.log('Add video clicked (delegation)');

                    const form = document.getElementById('block-editor-body');
                    if (!form) return;

                    const videosInput = form.querySelector('[data-content="videos"]');
                    const videosList = form.querySelector('#vgalleryVideosList');

                    if (!videosInput || !videosList) {
                        console.log('Video gallery elements not found');
                        return;
                    }

                    var videos = [];
                    try {
                        videos = JSON.parse(videosInput.value) || [];
                    } catch (err) {
                        videos = [];
                    }

                    videos.push({ url: '', thumbnail: '' });
                    videosInput.value = JSON.stringify(videos);
                    videosInput.dispatchEvent(new Event('change', { bubbles: true }));

                    self.renderGalleryVideos(videosList, videos);

                    // Focus on new input
                    setTimeout(function() {
                        var lastInput = videosList.querySelector('.vgallery-video-item:last-child .video-url-input');
                        if (lastInput) lastInput.focus();
                    }, 100);
                }
            });

            // Video Gallery - Remove video button (event delegation)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.vgallery-video-remove')) {
                    e.preventDefault();
                    const btn = e.target.closest('.vgallery-video-remove');
                    const item = btn.closest('.vgallery-video-item');
                    if (!item) return;

                    const form = document.getElementById('block-editor-body');
                    if (!form) return;

                    const videosInput = form.querySelector('[data-content="videos"]');
                    const videosList = form.querySelector('#vgalleryVideosList');

                    if (!videosInput || !videosList) return;

                    const index = parseInt(item.dataset.index);
                    var videos = [];
                    try {
                        videos = JSON.parse(videosInput.value) || [];
                    } catch (err) {
                        videos = [];
                    }

                    videos.splice(index, 1);
                    videosInput.value = JSON.stringify(videos);
                    videosInput.dispatchEvent(new Event('change', { bubbles: true }));

                    self.renderGalleryVideos(videosList, videos);
                }
            });

            // Video Gallery - Thumbnail select button (event delegation)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.vgallery-thumb-select')) {
                    e.preventDefault();
                    const btn = e.target.closest('.vgallery-thumb-select');
                    const item = btn.closest('.vgallery-video-item');
                    if (!item) return;

                    const form = document.getElementById('block-editor-body');
                    if (!form) return;

                    const videosInput = form.querySelector('[data-content="videos"]');
                    if (!videosInput) return;

                    const index = parseInt(item.dataset.index);

                    if (window.imagePicker && window.imagePicker.openForEditor) {
                        window.imagePicker.openForEditor(function(url) {
                            var videos = [];
                            try {
                                videos = JSON.parse(videosInput.value) || [];
                            } catch (err) {
                                videos = [];
                            }

                            if (videos[index]) {
                                videos[index].thumbnail = url;
                                videosInput.value = JSON.stringify(videos);
                                videosInput.dispatchEvent(new Event('change', { bubbles: true }));

                                const thumbEl = item.querySelector('.vgallery-video-thumbnail');
                                if (thumbEl) {
                                    thumbEl.innerHTML = '<img src="' + url + '" alt="">';
                                }
                            }
                        });
                    } else {
                        alert('Selector de imágenes no disponible');
                    }
                }
            });

            // Video Gallery - URL input change (event delegation)
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('video-url-input')) {
                    const input = e.target;
                    const item = input.closest('.vgallery-video-item');
                    if (!item) return;

                    const form = document.getElementById('block-editor-body');
                    if (!form) return;

                    const videosInput = form.querySelector('[data-content="videos"]');
                    if (!videosInput) return;

                    const index = parseInt(item.dataset.index);
                    var videos = [];
                    try {
                        videos = JSON.parse(videosInput.value) || [];
                    } catch (err) {
                        videos = [];
                    }

                    if (videos[index]) {
                        videos[index].url = input.value;

                        // Auto-set YouTube thumbnail if no custom thumbnail
                        if (!videos[index].thumbnail) {
                            var match = input.value.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
                            if (match) {
                                var autoThumb = 'https://img.youtube.com/vi/' + match[1] + '/maxresdefault.jpg';
                                var thumbEl = item.querySelector('.vgallery-video-thumbnail');
                                if (thumbEl) {
                                    thumbEl.innerHTML = '<img src="' + autoThumb + '" alt="">';
                                }
                            }
                        }

                        videosInput.value = JSON.stringify(videos);
                        videosInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });

            // Gallery layout mode change (event delegation)
            document.addEventListener('change', function(e) {
                if (e.target.id === 'galleryLayoutMode' || e.target.id === 'vgalleryLayoutMode') {
                    const isCarousel = e.target.value === 'carousel';
                    const form = document.getElementById('block-editor-body');
                    if (form) {
                        form.querySelectorAll('.carousel-settings').forEach(function(el) {
                            el.style.display = isCarousel ? '' : 'none';
                        });
                    }
                }
            });
        },

        // Helper to render gallery images
        renderGalleryImages: function(container, images) {
            container.innerHTML = images.map(function(img, index) {
                return '<div class="gallery-image-item" data-index="' + index + '">' +
                    '<div class="gallery-image-preview">' +
                        '<img src="' + (img.url || img) + '" alt="">' +
                    '</div>' +
                    '<div class="gallery-image-actions">' +
                        '<button type="button" class="btn btn-sm btn-danger gallery-image-remove">' +
                            '<i class="fas fa-trash"></i>' +
                        '</button>' +
                    '</div>' +
                '</div>';
            }).join('');
        },

        // Helper to render gallery videos
        renderGalleryVideos: function(container, videos) {
            container.innerHTML = videos.map(function(video, index) {
                var thumbUrl = video.thumbnail || '';
                if (!thumbUrl && video.url) {
                    var match = video.url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
                    if (match) {
                        thumbUrl = 'https://img.youtube.com/vi/' + match[1] + '/maxresdefault.jpg';
                    }
                }
                var thumbHtml = thumbUrl
                    ? '<img src="' + thumbUrl + '" alt="">'
                    : '<div class="vgallery-video-placeholder"><i class="fas fa-video"></i></div>';

                return '<div class="vgallery-video-item" data-index="' + index + '">' +
                    '<div class="vgallery-video-thumbnail">' + thumbHtml + '</div>' +
                    '<div class="vgallery-video-info">' +
                        '<input type="url" class="video-url-input" value="' + (video.url || '') + '" placeholder="URL del video (YouTube/Vimeo)">' +
                        '<div class="vgallery-video-thumb-actions">' +
                            '<button type="button" class="btn btn-xs btn-outline vgallery-thumb-select">' +
                                '<i class="fas fa-image"></i> Miniatura' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="vgallery-video-actions">' +
                        '<button type="button" class="btn btn-sm btn-danger vgallery-video-remove">' +
                            '<i class="fas fa-trash"></i>' +
                        '</button>' +
                    '</div>' +
                '</div>';
            }).join('');
        },

        // Drag & Drop functionality
        initDragDrop: function() {
            const container = document.getElementById('blocks-container');
            if (!container) return;

            let draggedItem = null;

            container.addEventListener('dragstart', function(e) {
                if (e.target.classList.contains('block-item')) {
                    draggedItem = e.target;
                    e.target.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                }
            });

            container.addEventListener('dragend', function(e) {
                if (draggedItem) {
                    draggedItem.classList.remove('dragging');
                    draggedItem = null;
                    BlockEditor.saveBlockOrder();
                }
            });

            container.addEventListener('dragover', function(e) {
                e.preventDefault();
                const afterElement = BlockEditor.getDragAfterElement(container, e.clientY);
                if (draggedItem) {
                    if (afterElement == null) {
                        container.appendChild(draggedItem);
                    } else {
                        container.insertBefore(draggedItem, afterElement);
                    }
                }
            });

            // Make blocks draggable
            document.querySelectorAll('.block-item').forEach(function(block) {
                block.setAttribute('draggable', 'true');
            });
        },

        getDragAfterElement: function(container, y) {
            const draggableElements = [...container.querySelectorAll('.block-item:not(.dragging)')];

            return draggableElements.reduce(function(closest, child) {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        },

        saveBlockOrder: function() {
            const blocks = document.querySelectorAll('.block-item');
            const blockIds = Array.from(blocks).map(b => b.dataset.blockId);
            const body = 'page_id=' + this.pageId + '&block_ids[]=' + blockIds.join('&block_ids[]=') + '&_csrf_token=' + this.csrfToken;

            console.log('Saving block order:', {
                pageId: this.pageId,
                blockIds: blockIds,
                body: body
            });

            fetch('/admin/blocks/reorder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': this.csrfToken
                },
                body: body
            })
            .then(response => {
                console.log('Reorder response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Reorder response data:', data);
                if (data.success) {
                    this.showNotification('Orden actualizado', 'success');
                } else {
                    this.showNotification(data.error || 'Error al guardar el orden', 'error');
                }
            })
            .catch(error => {
                console.error('Reorder error:', error);
                this.showNotification('Error al guardar el orden', 'error');
            });
        },

        // Modal functionality
        initModals: function() {
            const self = this;

            // Add block button
            const addBlockBtn = document.getElementById('add-block-btn');
            if (addBlockBtn) {
                addBlockBtn.addEventListener('click', function() {
                    self.openModal('block-type-modal');
                });
            }

            // Block type selection
            document.querySelectorAll('.block-type-option').forEach(function(option) {
                option.addEventListener('click', function() {
                    const type = this.dataset.type;
                    self.closeModal('block-type-modal');
                    self.createBlock(type);
                });
            });

            // Close modal buttons
            document.querySelectorAll('.modal-close, .modal-overlay').forEach(function(el) {
                el.addEventListener('click', function() {
                    self.closeAllModals();
                });
            });

            // Cancel block editor
            const cancelBtn = document.getElementById('block-editor-cancel');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    self.closeModal('block-editor-modal');
                });
            }

            // Save block
            const saveBtn = document.getElementById('block-editor-save');
            if (saveBtn) {
                saveBtn.addEventListener('click', function() {
                    self.saveBlock();
                });
            }

            // ESC key to close
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    self.closeAllModals();
                }
            });
        },

        openModal: function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        },

        closeModal: function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        },

        closeAllModals: function() {
            document.querySelectorAll('.modal').forEach(function(modal) {
                modal.style.display = 'none';
            });
            document.body.style.overflow = '';
        },

        // Block CRUD operations
        initBlockActions: function() {
            const self = this;

            // Edit block buttons
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('edit-block-btn')) {
                    const blockId = e.target.dataset.blockId;
                    self.editBlock(blockId);
                }
                if (e.target.classList.contains('delete-block-btn')) {
                    const blockId = e.target.dataset.blockId;
                    if (confirm('¿Eliminar este bloque?')) {
                        self.deleteBlock(blockId);
                    }
                }
                if (e.target.closest('.clone-block-btn')) {
                    const btn = e.target.closest('.clone-block-btn');
                    const blockId = btn.dataset.blockId;
                    self.cloneBlock(blockId, btn);
                }
            });
        },

        createBlock: function(type) {
            const self = this;

            fetch('/admin/pages/' + this.pageId + '/blocks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': this.csrfToken
                },
                body: 'block_type=' + type + '&_csrf_token=' + this.csrfToken
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    self.addBlockToDOM(data.block);
                    self.editBlock(data.block.id);
                    self.showNotification('Bloque creado', 'success');
                } else {
                    self.showNotification(data.error || 'Error al crear bloque', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                self.showNotification('Error al crear bloque', 'error');
            });
        },

        addBlockToDOM: function(block) {
            const container = document.getElementById('blocks-container');
            const emptyState = document.getElementById('blocks-empty');

            if (emptyState) {
                emptyState.remove();
            }

            const blockTypes = window.BLOCK_TYPES || {};
            const typeName = blockTypes[block.block_type] || block.block_type;
            const preview = block.content.title || block.content.slides?.[0]?.title || 'Sin título';

            const blockHtml = `
                <div class="block-item" data-block-id="${block.id}" data-block-type="${block.block_type}" draggable="true">
                    <div class="block-handle">
                        <span class="handle-icon">&#9776;</span>
                    </div>
                    <div class="block-info">
                        <span class="block-type-badge">${typeName}</span>
                        <span class="block-preview">${this.escapeHtml(preview)}</span>
                    </div>
                    <div class="block-actions">
                        <button type="button" class="btn btn-sm edit-block-btn" data-block-id="${block.id}">Editar</button>
                        <button type="button" class="btn btn-sm btn-secondary clone-block-btn" data-block-id="${block.id}" title="Clonar bloque"><i class="fas fa-clone"></i></button>
                        <button type="button" class="btn btn-sm btn-danger delete-block-btn" data-block-id="${block.id}">Eliminar</button>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', blockHtml);
        },

        editBlock: function(blockId) {
            const self = this;
            const blockItem = document.querySelector(`.block-item[data-block-id="${blockId}"]`);
            const blockType = blockItem ? blockItem.dataset.blockType : null;

            this.currentBlockId = blockId;
            this.currentBlockType = blockType;

            // Add cache-busting parameter to prevent browser caching
            const cacheBuster = Date.now();

            // Load block form
            fetch(`/admin/blocks/form?block_id=${blockId}&type=${blockType}&_t=${cacheBuster}`, {
                cache: 'no-store',
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('block-editor-title').textContent = 'Editar: ' + data.typeName;
                    document.getElementById('block-editor-body').innerHTML = data.html;
                    self.openModal('block-editor-modal');
                    self.initBlockFormHandlers();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                self.showNotification('Error al cargar el formulario', 'error');
            });
        },

        saveBlock: function() {
            const self = this;
            const form = document.getElementById('block-editor-body');

            // Collect form data
            const content = this.collectBlockContent(form);
            const settings = this.collectBlockSettings(form);

            // Debug: log what we're saving
            console.log('=== SAVING BLOCK ===');
            console.log('Block ID:', this.currentBlockId);
            console.log('Content:', content);
            console.log('Settings:', settings);
            console.log('Settings JSON:', JSON.stringify(settings));
            if (settings.selected_cases) {
                console.log('selected_cases type:', typeof settings.selected_cases);
                console.log('selected_cases value:', settings.selected_cases);
            }

            fetch('/admin/blocks/' + this.currentBlockId + '?_t=' + Date.now(), {
                method: 'POST',
                cache: 'no-store',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': this.csrfToken,
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                },
                body: 'content=' + encodeURIComponent(JSON.stringify(content)) +
                      '&settings=' + encodeURIComponent(JSON.stringify(settings)) +
                      '&_csrf_token=' + this.csrfToken
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    self.updateBlockInDOM(data.block);
                    self.closeModal('block-editor-modal');
                    self.showNotification('Bloque guardado', 'success');
                } else {
                    self.showNotification(data.error || 'Error al guardar', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                self.showNotification('Error al guardar', 'error');
            });
        },

        collectBlockContent: function(form) {
            const content = {};
            const contentInputs = form.querySelectorAll('[data-content]');

            contentInputs.forEach(function(input) {
                const key = input.dataset.content;
                if (input.type === 'checkbox') {
                    content[key] = input.checked;
                } else {
                    // Try to parse JSON values (for arrays like plans, items, etc.)
                    const value = input.value;
                    if (value && (value.startsWith('[') || value.startsWith('{'))) {
                        try {
                            content[key] = JSON.parse(value);
                        } catch (e) {
                            content[key] = value;
                        }
                    } else {
                        content[key] = value;
                    }
                }
            });

            // Handle special cases like slides
            const slidesContainer = form.querySelector('.slides-container');
            if (slidesContainer) {
                content.slides = [];
                slidesContainer.querySelectorAll('.slide-item').forEach(function(slide) {
                    const slideData = {};
                    slide.querySelectorAll('[data-slide-field]').forEach(function(field) {
                        slideData[field.dataset.slideField] = field.value;
                    });
                    content.slides.push(slideData);
                });
            }

            // Handle items pattern (business_types, benefits, etc.)
            const itemsContainer = form.querySelector('.items-container');
            if (itemsContainer) {
                content.items = [];
                itemsContainer.querySelectorAll('.item-card').forEach(function(item) {
                    const itemData = {};
                    item.querySelectorAll('[data-item-field]').forEach(function(field) {
                        if (field.tagName === 'TEXTAREA') {
                            itemData[field.dataset.itemField] = field.value;
                        } else {
                            itemData[field.dataset.itemField] = field.value;
                        }
                    });
                    content.items.push(itemData);
                });
            }

            return content;
        },

        collectBlockSettings: function(form) {
            const settings = {};
            const settingsInputs = form.querySelectorAll('[data-setting]');

            console.log('=== COLLECTING SETTINGS ===');
            console.log('Found settings inputs:', settingsInputs.length);

            settingsInputs.forEach(function(input) {
                const key = input.dataset.setting;
                console.log('Processing setting:', key, 'type:', input.type, 'value:', input.value);

                if (input.type === 'checkbox') {
                    settings[key] = input.checked;
                } else if (input.type === 'radio') {
                    // Only save radio value if it's checked
                    if (input.checked) {
                        settings[key] = input.value;
                    }
                } else if (input.type === 'number') {
                    settings[key] = parseFloat(input.value) || 0;
                } else {
                    // Try to parse JSON values (for arrays and objects)
                    const value = input.value;
                    if (value && (value.startsWith('[') || value.startsWith('{'))) {
                        try {
                            settings[key] = JSON.parse(value);
                            console.log('Parsed JSON for', key, ':', settings[key]);
                        } catch (e) {
                            settings[key] = value;
                        }
                    } else {
                        settings[key] = value;
                    }
                }
            });

            console.log('Final settings object:', settings);
            return settings;
        },

        updateBlockInDOM: function(block) {
            const blockItem = document.querySelector(`.block-item[data-block-id="${block.id}"]`);
            if (blockItem) {
                const preview = block.content.title || block.content.slides?.[0]?.title || 'Sin título';
                const previewEl = blockItem.querySelector('.block-preview');
                if (previewEl) {
                    previewEl.textContent = preview;
                }
            }
        },

        deleteBlock: function(blockId) {
            const self = this;

            fetch('/admin/blocks/' + blockId + '/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': this.csrfToken
                },
                body: '_csrf_token=' + this.csrfToken
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const blockItem = document.querySelector(`.block-item[data-block-id="${blockId}"]`);
                    if (blockItem) {
                        blockItem.remove();
                    }
                    self.showNotification('Bloque eliminado', 'success');

                    // Show empty state if no blocks left
                    const container = document.getElementById('blocks-container');
                    if (container && container.querySelectorAll('.block-item').length === 0) {
                        container.innerHTML = `
                            <div class="blocks-empty" id="blocks-empty">
                                <p>Esta página no tiene bloques todavía.</p>
                                <p>Haz clic en "Añadir bloque" para empezar a diseñar tu página.</p>
                            </div>
                        `;
                    }
                } else {
                    self.showNotification(data.error || 'Error al eliminar', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                self.showNotification('Error al eliminar', 'error');
            });
        },

        cloneBlock: function(blockId, btn) {
            const self = this;
            const originalBlock = document.querySelector(`.block-item[data-block-id="${blockId}"]`);

            // Disable button during request
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            fetch('/admin/blocks/' + blockId + '/clone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': this.csrfToken
                },
                body: '_csrf_token=' + this.csrfToken
            })
            .then(response => response.json())
            .then(data => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-clone"></i>';
                }

                if (data.success) {
                    // Insert cloned block after the original
                    self.insertBlockAfter(data.block, originalBlock);
                    self.showNotification('Bloque clonado', 'success');
                } else {
                    self.showNotification(data.error || 'Error al clonar', 'error');
                }
            })
            .catch(error => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-clone"></i>';
                }
                console.error('Error:', error);
                self.showNotification('Error al clonar', 'error');
            });
        },

        insertBlockAfter: function(block, afterElement) {
            const blockTypes = window.BLOCK_TYPES || {};
            const typeName = blockTypes[block.block_type] || block.block_type;
            const preview = block.content?.title || block.content?.slides?.[0]?.title || 'Sin título';

            const blockHtml = `
                <div class="block-item" data-block-id="${block.id}" data-block-type="${block.block_type}" draggable="true">
                    <div class="block-handle">
                        <span class="handle-icon">&#9776;</span>
                    </div>
                    <div class="block-info">
                        <span class="block-type-badge">${typeName}</span>
                        <span class="block-preview">${this.escapeHtml(preview)}</span>
                    </div>
                    <div class="block-actions">
                        <button type="button" class="btn btn-sm edit-block-btn" data-block-id="${block.id}">Editar</button>
                        <button type="button" class="btn btn-sm btn-secondary clone-block-btn" data-block-id="${block.id}" title="Clonar bloque"><i class="fas fa-clone"></i></button>
                        <button type="button" class="btn btn-sm btn-danger delete-block-btn" data-block-id="${block.id}">Eliminar</button>
                    </div>
                </div>
            `;

            if (afterElement) {
                afterElement.insertAdjacentHTML('afterend', blockHtml);
            } else {
                document.getElementById('blocks-container').insertAdjacentHTML('beforeend', blockHtml);
            }
        },

        initBlockFormHandlers: function() {
            // Initialize any special handlers in block forms
            // Like image uploads, color pickers, etc.

            const self = this;
            const form = document.getElementById('block-editor-body');
            console.log('initBlockFormHandlers called, form:', form);
            if (!form) {
                console.log('Form not found!');
                return;
            }

            // Execute inline scripts from loaded HTML (they don't execute automatically via innerHTML)
            const scripts = form.querySelectorAll('script');
            scripts.forEach(function(script) {
                const newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                } else {
                    newScript.textContent = script.textContent;
                }
                script.parentNode.replaceChild(newScript, script);
            });

            // Handle "Add slide" button for hero block
            const addSlideBtn = form.querySelector('.add-slide-btn');
            console.log('addSlideBtn found:', addSlideBtn);
            if (addSlideBtn) {
                addSlideBtn.addEventListener('click', function() {
                    console.log('Add slide clicked!');
                    const container = form.querySelector('.slides-container');
                    console.log('slides-container found:', container);
                    const index = container.querySelectorAll('.slide-item').length;
                    const slideHtml = `
                        <div class="slide-item" data-slide-index="${index}">
                            <div class="slide-header">
                                <span>Slide ${index + 1}</span>
                                <button type="button" class="btn btn-sm btn-danger remove-slide-btn">&times;</button>
                            </div>
                            <div class="form-group">
                                <label>Título principal</label>
                                <input type="text" data-slide-field="title" value="" placeholder="Ej: Fideliza a tus clientes">
                            </div>
                            <div class="form-group">
                                <label>Subtítulo</label>
                                <input type="text" data-slide-field="subtitle" value="" placeholder="Texto secundario">
                            </div>
                            <div class="form-group">
                                <label>Imagen de fondo</label>
                                <div class="image-picker-field">
                                    <input type="text" data-slide-field="background_image" value="">
                                    <div class="image-picker-preview">
                                        <div class="preview-placeholder">
                                            <i class="fas fa-image"></i>
                                            <span>Sin imagen</span>
                                        </div>
                                    </div>
                                    <div class="image-picker-actions">
                                        <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                            <i class="fas fa-upload"></i> Seleccionar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Texto del botón CTA</label>
                                    <input type="text" data-slide-field="cta_text" value="Reservar demo">
                                </div>
                                <div class="form-group">
                                    <label>URL del botón</label>
                                    <input type="text" data-slide-field="cta_url" value="/reservar-demo">
                                </div>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', slideHtml);

                    // Re-bind image picker for new elements
                    if (window.imagePicker) {
                        window.imagePicker.bindPickers();
                    }
                });
            }

            // Handle remove slide buttons
            const slidesContainer = form.querySelector('.slides-container');
            if (slidesContainer) {
                slidesContainer.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-slide-btn') || e.target.closest('.remove-slide-btn')) {
                        const btn = e.target.classList.contains('remove-slide-btn') ? e.target : e.target.closest('.remove-slide-btn');
                        btn.closest('.slide-item').remove();
                    }
                });
            }

            // Handle gallery "Add image" button for integrations
            const addGalleryBtn = form.querySelector('#addGalleryImage');
            if (addGalleryBtn) {
                addGalleryBtn.addEventListener('click', function() {
                    if (window.imagePicker) {
                        window.imagePicker.open(function(url) {
                            self.addGalleryItem(form, url);
                        });
                    }
                });
            }

            // Handle gallery item remove buttons
            form.querySelectorAll('.gallery-item-remove').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    this.closest('.gallery-item').remove();
                });
            });

            // Initialize image pickers
            if (window.imagePicker) {
                window.imagePicker.bindPickers();
            }

            // Initialize icon pickers
            if (window.iconPicker) {
                window.iconPicker.bindInputs();
            }

            // Sync color pickers with text inputs
            form.querySelectorAll('.color-picker-input').forEach(function(colorPicker) {
                const targetId = colorPicker.dataset.syncTarget;
                const textInput = document.getElementById(targetId);
                if (textInput) {
                    // Color picker -> text input
                    colorPicker.addEventListener('input', function() {
                        textInput.value = this.value;
                    });
                    // Text input -> color picker
                    textInput.addEventListener('input', function() {
                        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                            colorPicker.value = this.value;
                        }
                    });
                }
            });

            // Handle "Add item" buttons for business_types, benefits, etc.
            const addItemBtns = form.querySelectorAll('.add-item-btn');
            console.log('addItemBtns found:', addItemBtns.length);
            addItemBtns.forEach(function(btn) {
                console.log('Binding click to add-item-btn:', btn);
                btn.addEventListener('click', function() {
                    console.log('Add item clicked, containerId:', btn.dataset.container);
                    const containerId = btn.dataset.container;
                    const container = document.getElementById(containerId);
                    if (!container) return;

                    const index = container.querySelectorAll('.item-card').length;
                    const itemHtml = self.getItemTemplate(containerId, index);
                    container.insertAdjacentHTML('beforeend', itemHtml);

                    // Re-bind image picker for new elements
                    if (window.imagePicker) {
                        window.imagePicker.bindPickers();
                    }
                    // Re-bind icon picker for new elements
                    if (window.iconPicker) {
                        window.iconPicker.bindInputs();
                    }
                    // Re-bind color pickers for new elements
                    const newItem = container.querySelector('.item-card:last-child');
                    if (newItem) {
                        newItem.querySelectorAll('.color-picker-input').forEach(function(colorPicker) {
                            const targetId = colorPicker.dataset.syncTarget;
                            const textInput = document.getElementById(targetId);
                            if (textInput) {
                                colorPicker.addEventListener('input', function() {
                                    textInput.value = this.value;
                                });
                                textInput.addEventListener('input', function() {
                                    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                                        colorPicker.value = this.value;
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Handle remove item buttons (event delegation)
            form.querySelectorAll('.items-container').forEach(function(container) {
                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-item-btn') || e.target.closest('.remove-item-btn')) {
                        const btn = e.target.classList.contains('remove-item-btn') ? e.target : e.target.closest('.remove-item-btn');
                        btn.closest('.item-card').remove();
                    }
                });
            });

            // Handle Clients block - display mode toggle
            const clientsDisplayMode = form.querySelector('#clients-display-mode');
            const gridOptions = form.querySelector('#grid-options');
            const carouselOptions = form.querySelector('#carousel-options');
            if (clientsDisplayMode && gridOptions && carouselOptions) {
                clientsDisplayMode.addEventListener('change', function() {
                    gridOptions.style.display = this.value === 'grid' ? '' : 'none';
                    carouselOptions.style.display = this.value === 'carousel' ? '' : 'none';
                });
            }

            // Handle Clients block - selection mode toggle
            const clientsSelectionMode = form.querySelector('#clients-selection-mode');
            const industryGroup = form.querySelector('#industry-filter-group');
            const manualGroup = form.querySelector('#manual-selection-group');
            const limitGroup = form.querySelector('#limit-group');
            if (clientsSelectionMode) {
                clientsSelectionMode.addEventListener('change', function() {
                    if (industryGroup) industryGroup.style.display = this.value === 'industry' ? '' : 'none';
                    if (manualGroup) manualGroup.style.display = this.value === 'manual' ? '' : 'none';
                    if (limitGroup) limitGroup.style.display = this.value === 'manual' ? 'none' : '';
                });
            }

            // ========== SERVICES BLOCK HANDLERS ==========
            console.log('=== SERVICES BLOCK INIT ===');

            // Handle Services block - layout mode toggle
            const servicesLayoutMode = form.querySelector('#services-layout-mode');
            const servicesGridOptions = form.querySelector('#grid-options');
            const servicesCarouselOptions = form.querySelector('#carousel-options');

            if (servicesLayoutMode && servicesGridOptions && servicesCarouselOptions) {
                servicesLayoutMode.addEventListener('change', function() {
                    servicesGridOptions.style.display = this.value === 'grid' ? '' : 'none';
                    servicesCarouselOptions.style.display = this.value === 'carousel' ? '' : 'none';
                });
            }

            // Handle Services block - selection mode toggle
            const servicesSelectionMode = form.querySelector('#services-selection-mode');
            const servicesCategoryGroup = form.querySelector('#category-filter-group');
            const servicesManualGroup = form.querySelector('#manual-selection-group');
            const servicesLimitGroup = form.querySelector('#limit-group');

            if (servicesSelectionMode) {
                servicesSelectionMode.addEventListener('change', function() {
                    if (servicesCategoryGroup) servicesCategoryGroup.style.display = this.value === 'category' ? '' : 'none';
                    if (servicesManualGroup) servicesManualGroup.style.display = this.value === 'manual' ? '' : 'none';
                    if (servicesLimitGroup) servicesLimitGroup.style.display = this.value === 'manual' ? 'none' : '';
                });
            }

            // Handle Services block - autoplay toggle
            const servicesAutoplay = form.querySelector('[data-setting="autoplay"]');
            const servicesAutoplaySpeed = form.querySelector('#autoplay-speed-group');

            if (servicesAutoplay && servicesAutoplaySpeed) {
                servicesAutoplay.addEventListener('change', function() {
                    servicesAutoplaySpeed.style.display = this.checked ? '' : 'none';
                });
            }

            // Handle Services block - manual service selection checkboxes
            const selectedServicesInput = form.querySelector('#selected-services-input');
            const serviceCheckboxes = form.querySelectorAll('.services-checklist input[type="checkbox"]');

            console.log('Services hidden input found:', !!selectedServicesInput);
            console.log('Services checkboxes found:', serviceCheckboxes.length);
            if (selectedServicesInput) {
                console.log('Hidden input current value:', selectedServicesInput.value);
            }

            if (selectedServicesInput && serviceCheckboxes.length > 0) {
                function updateSelectedServices() {
                    const selected = [];
                    serviceCheckboxes.forEach(function(cb) {
                        if (cb.checked) {
                            selected.push(parseInt(cb.value, 10));
                        }
                    });
                    selectedServicesInput.value = JSON.stringify(selected);
                    console.log('Updated selected_services to:', selectedServicesInput.value);
                }

                serviceCheckboxes.forEach(function(cb) {
                    cb.addEventListener('change', function() {
                        console.log('Service checkbox changed:', this.value, 'checked:', this.checked);
                        updateSelectedServices();
                    });
                });
                console.log('Services checkbox handlers bound successfully');
            } else {
                console.log('Services block handlers NOT bound - elements not found');
            }

            // Handle Clients block - manual case selection checkboxes
            const selectedCasesInput = form.querySelector('#selected-cases-input');
            const caseCheckboxes = form.querySelectorAll('.cases-checklist input[type="checkbox"]');

            if (selectedCasesInput && caseCheckboxes.length > 0) {
                function updateSelectedCases() {
                    const selected = [];
                    caseCheckboxes.forEach(function(cb) {
                        if (cb.checked) {
                            selected.push(parseInt(cb.value, 10));
                        }
                    });
                    selectedCasesInput.value = JSON.stringify(selected);
                }

                caseCheckboxes.forEach(function(cb) {
                    cb.addEventListener('change', updateSelectedCases);
                });
            }

            // ========== LANDINGS BLOCK HANDLERS ==========

            // Handle Landings block - selection mode toggle
            const landingsSelectionMode = form.querySelector('#landings-selection-mode');
            const landingsManualGroup = form.querySelector('#landings-manual-group');
            const landingsThemeGroup = form.querySelector('#landings-theme-group');
            const landingsThemesGroup = form.querySelector('#landings-themes-group');
            const landingsLimitGroup = form.querySelector('#landings-limit-group');

            if (landingsSelectionMode) {
                landingsSelectionMode.addEventListener('change', function() {
                    if (landingsManualGroup) landingsManualGroup.style.display = this.value === 'manual' ? '' : 'none';
                    if (landingsThemeGroup) landingsThemeGroup.style.display = this.value === 'theme' ? '' : 'none';
                    if (landingsThemesGroup) landingsThemesGroup.style.display = this.value === 'themes' ? '' : 'none';
                    if (landingsLimitGroup) landingsLimitGroup.style.display = this.value === 'manual' ? 'none' : '';
                });
            }

            // Handle Landings block - manual landing selection checkboxes
            const selectedLandingsInput = form.querySelector('#selected-landings-input');
            const landingCheckboxes = form.querySelectorAll('.landings-checklist input[type="checkbox"]');

            if (selectedLandingsInput && landingCheckboxes.length > 0) {
                function updateSelectedLandings() {
                    const selected = [];
                    landingCheckboxes.forEach(function(cb) {
                        if (cb.checked) {
                            selected.push(parseInt(cb.value, 10));
                        }
                    });
                    selectedLandingsInput.value = JSON.stringify(selected);
                    console.log('Updated selected_landings to:', selectedLandingsInput.value);
                }

                landingCheckboxes.forEach(function(cb) {
                    cb.addEventListener('change', updateSelectedLandings);
                });
            }

            // Handle Landings block - themes selection checkboxes
            const selectedThemesInput = form.querySelector('#selected-themes-input');
            const themeCheckboxes = form.querySelectorAll('.themes-checklist input[type="checkbox"]');

            if (selectedThemesInput && themeCheckboxes.length > 0) {
                function updateSelectedThemes() {
                    const selected = [];
                    themeCheckboxes.forEach(function(cb) {
                        if (cb.checked) {
                            selected.push(parseInt(cb.value, 10));
                        }
                    });
                    selectedThemesInput.value = JSON.stringify(selected);
                    console.log('Updated selected_themes to:', selectedThemesInput.value);
                }

                themeCheckboxes.forEach(function(cb) {
                    cb.addEventListener('change', updateSelectedThemes);
                });
            }

            // Handle Landings block - display mode radio buttons
            const displayModeInput = form.querySelector('#display-mode-input');
            const displayModeRadios = form.querySelectorAll('input[name="landings_display_mode"]');

            if (displayModeInput && displayModeRadios.length > 0) {
                displayModeRadios.forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        if (this.checked) {
                            displayModeInput.value = this.value;
                            console.log('Updated display_mode to:', displayModeInput.value);
                        }
                    });
                });
            }

            // Handle Landings block - show more toggle
            const showMoreCheckbox = form.querySelector('input[data-content="show_more"]');
            const showMoreOptions = form.querySelector('#show-more-options');

            if (showMoreCheckbox && showMoreOptions) {
                showMoreCheckbox.addEventListener('change', function() {
                    showMoreOptions.style.display = this.checked ? '' : 'none';
                });
            }

            // ========== IMAGE GALLERY BLOCK HANDLERS ==========
            const galleryImagesList = form.querySelector('#galleryImagesList');
            const galleryImagesInput = form.querySelector('[data-content="images"]');
            const addGalleryImagesBtn = form.querySelector('#addGalleryImages');
            const galleryLayoutMode = form.querySelector('#galleryLayoutMode');

            console.log('=== IMAGE GALLERY DEBUG ===');
            console.log('form element:', form);
            console.log('galleryImagesList:', galleryImagesList);
            console.log('galleryImagesInput:', galleryImagesInput);
            console.log('addGalleryImagesBtn:', addGalleryImagesBtn);
            console.log('MediaLibrary available:', typeof MediaLibrary !== 'undefined');

            if (galleryImagesList && galleryImagesInput && addGalleryImagesBtn) {
                console.log('Binding image gallery handlers...');
                // Toggle carousel/grid settings
                if (galleryLayoutMode) {
                    galleryLayoutMode.addEventListener('change', function() {
                        const isCarousel = this.value === 'carousel';
                        form.querySelectorAll('.carousel-settings').forEach(function(el) {
                            el.style.display = isCarousel ? '' : 'none';
                        });
                    });
                }

                function getGalleryImages() {
                    try {
                        return JSON.parse(galleryImagesInput.value) || [];
                    } catch (e) {
                        return [];
                    }
                }

                function setGalleryImages(images) {
                    galleryImagesInput.value = JSON.stringify(images);
                    galleryImagesInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                function renderGalleryImages() {
                    const images = getGalleryImages();
                    galleryImagesList.innerHTML = images.map(function(img, index) {
                        return '<div class="gallery-image-item" data-index="' + index + '">' +
                            '<div class="gallery-image-preview">' +
                                '<img src="' + (img.url || img) + '" alt="">' +
                            '</div>' +
                            '<div class="gallery-image-actions">' +
                                '<button type="button" class="btn btn-sm btn-danger gallery-image-remove">' +
                                    '<i class="fas fa-trash"></i>' +
                                '</button>' +
                            '</div>' +
                        '</div>';
                    }).join('');

                    // Bind remove buttons
                    galleryImagesList.querySelectorAll('.gallery-image-remove').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var item = this.closest('.gallery-image-item');
                            var idx = parseInt(item.dataset.index);
                            var imgs = getGalleryImages();
                            imgs.splice(idx, 1);
                            setGalleryImages(imgs);
                            renderGalleryImages();
                        });
                    });
                }

                // Add images button - uses imagePicker with multiple selection
                addGalleryImagesBtn.addEventListener('click', function() {
                    console.log('Add gallery images button clicked!');
                    if (window.imagePicker && window.imagePicker.openForMultipleSelection) {
                        console.log('Opening imagePicker for multiple selection...');
                        window.imagePicker.openForMultipleSelection(function(urls) {
                            var images = getGalleryImages();
                            urls.forEach(function(url) {
                                images.push({ url: url, alt: '' });
                            });
                            setGalleryImages(images);
                            renderGalleryImages();
                        });
                    } else {
                        alert('Selector de imágenes no disponible');
                    }
                });

                // Initial render
                renderGalleryImages();
            }

            // ========== VIDEO GALLERY BLOCK HANDLERS ==========
            const vgalleryVideosList = form.querySelector('#vgalleryVideosList');
            const vgalleryVideosInput = form.querySelector('[data-content="videos"]');
            const addVgalleryVideoBtn = form.querySelector('#addVgalleryVideo');
            const vgalleryLayoutMode = form.querySelector('#vgalleryLayoutMode');

            console.log('=== VIDEO GALLERY DEBUG ===');
            console.log('vgalleryVideosList:', vgalleryVideosList);
            console.log('vgalleryVideosInput:', vgalleryVideosInput);
            console.log('addVgalleryVideoBtn:', addVgalleryVideoBtn);

            if (vgalleryVideosList && vgalleryVideosInput && addVgalleryVideoBtn) {
                console.log('Binding video gallery handlers...');
                // Toggle carousel/grid settings
                if (vgalleryLayoutMode) {
                    vgalleryLayoutMode.addEventListener('change', function() {
                        const isCarousel = this.value === 'carousel';
                        form.querySelectorAll('.carousel-settings').forEach(function(el) {
                            el.style.display = isCarousel ? '' : 'none';
                        });
                    });
                }

                function getVgalleryVideos() {
                    try {
                        return JSON.parse(vgalleryVideosInput.value) || [];
                    } catch (e) {
                        return [];
                    }
                }

                function setVgalleryVideos(videos) {
                    vgalleryVideosInput.value = JSON.stringify(videos);
                    vgalleryVideosInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                function getYouTubeThumbnail(url) {
                    var match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
                    return match ? 'https://img.youtube.com/vi/' + match[1] + '/maxresdefault.jpg' : null;
                }

                function renderVgalleryVideos() {
                    var videos = getVgalleryVideos();
                    vgalleryVideosList.innerHTML = videos.map(function(video, index) {
                        var thumbUrl = video.thumbnail || getYouTubeThumbnail(video.url || '') || '';
                        var thumbHtml = thumbUrl
                            ? '<img src="' + thumbUrl + '" alt="">'
                            : '<div class="vgallery-video-placeholder"><i class="fas fa-video"></i></div>';

                        return '<div class="vgallery-video-item" data-index="' + index + '">' +
                            '<div class="vgallery-video-thumbnail">' + thumbHtml + '</div>' +
                            '<div class="vgallery-video-info">' +
                                '<input type="url" class="video-url-input" value="' + (video.url || '') + '" placeholder="URL del video (YouTube/Vimeo)">' +
                                '<div class="vgallery-video-thumb-actions">' +
                                    '<button type="button" class="btn btn-xs btn-outline vgallery-thumb-select">' +
                                        '<i class="fas fa-image"></i> Miniatura' +
                                    '</button>' +
                                    '<input type="hidden" class="video-thumbnail-input" value="' + (video.thumbnail || '') + '">' +
                                '</div>' +
                            '</div>' +
                            '<div class="vgallery-video-actions">' +
                                '<button type="button" class="btn btn-sm btn-danger vgallery-video-remove">' +
                                    '<i class="fas fa-trash"></i>' +
                                '</button>' +
                            '</div>' +
                        '</div>';
                    }).join('');

                    bindVgalleryItemEvents();
                }

                function bindVgalleryItemEvents() {
                    // URL input change
                    vgalleryVideosList.querySelectorAll('.video-url-input').forEach(function(input) {
                        input.addEventListener('change', function() {
                            var item = this.closest('.vgallery-video-item');
                            var idx = parseInt(item.dataset.index);
                            var videos = getVgalleryVideos();
                            videos[idx].url = this.value;

                            // Auto-set thumbnail if not custom
                            if (!videos[idx].thumbnail) {
                                var autoThumb = getYouTubeThumbnail(this.value);
                                if (autoThumb) {
                                    var thumbEl = item.querySelector('.vgallery-video-thumbnail');
                                    thumbEl.innerHTML = '<img src="' + autoThumb + '" alt="">';
                                }
                            }

                            setVgalleryVideos(videos);
                        });
                    });

                    // Thumbnail select
                    vgalleryVideosList.querySelectorAll('.vgallery-thumb-select').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var item = this.closest('.vgallery-video-item');
                            var idx = parseInt(item.dataset.index);

                            if (typeof MediaLibrary !== 'undefined') {
                                MediaLibrary.open({
                                    multiple: false,
                                    onSelect: function(selected) {
                                        var media = Array.isArray(selected) ? selected[0] : selected;
                                        var videos = getVgalleryVideos();
                                        videos[idx].thumbnail = media.url;
                                        setVgalleryVideos(videos);

                                        var thumbEl = item.querySelector('.vgallery-video-thumbnail');
                                        thumbEl.innerHTML = '<img src="' + media.url + '" alt="">';
                                    }
                                });
                            }
                        });
                    });

                    // Remove button
                    vgalleryVideosList.querySelectorAll('.vgallery-video-remove').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var item = this.closest('.vgallery-video-item');
                            var idx = parseInt(item.dataset.index);
                            var videos = getVgalleryVideos();
                            videos.splice(idx, 1);
                            setVgalleryVideos(videos);
                            renderVgalleryVideos();
                        });
                    });
                }

                // Add video button
                console.log('Binding video gallery add button...');
                addVgalleryVideoBtn.addEventListener('click', function() {
                    console.log('Add video button clicked!');
                    var videos = getVgalleryVideos();
                    videos.push({ url: '', thumbnail: '' });
                    setVgalleryVideos(videos);
                    renderVgalleryVideos();

                    // Focus on new input
                    var lastInput = vgalleryVideosList.querySelector('.vgallery-video-item:last-child .video-url-input');
                    if (lastInput) lastInput.focus();
                });

                // Initial render
                renderVgalleryVideos();
            }
        },

        getItemTemplate: function(containerId, index) {
            // Return appropriate template based on container
            if (containerId === 'businessTypesContainer') {
                return `
                    <div class="item-card" data-item-index="${index}">
                        <div class="item-header">
                            <span>Tipo ${index + 1}</span>
                            <button type="button" class="btn btn-sm btn-danger remove-item-btn">&times;</button>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Título</label>
                                <input type="text" data-item-field="title" value="" placeholder="Restaurantes">
                            </div>
                            <div class="form-group">
                                <label>Icono</label>
                                <div class="icon-input-wrapper">
                                    <div class="icon-input-preview">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <input type="text" data-item-field="icon" value="" placeholder="fas fa-utensils">
                                    <button type="button" class="icon-input-btn">Elegir</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea data-item-field="description" rows="2" placeholder="Breve descripción..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Enlace (opcional)</label>
                            <input type="text" data-item-field="link" value="" placeholder="/soluciones/restaurantes">
                        </div>
                    </div>
                `;
            } else if (containerId === 'benefitsContainer') {
                return `
                    <div class="item-card" data-item-index="${index}">
                        <div class="item-header">
                            <span>Beneficio ${index + 1}</span>
                            <button type="button" class="btn btn-sm btn-danger remove-item-btn">&times;</button>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Título</label>
                                <input type="text" data-item-field="title" value="" placeholder="Ahorro de tiempo">
                            </div>
                            <div class="form-group">
                                <label>Icono</label>
                                <div class="icon-input-wrapper">
                                    <div class="icon-input-preview">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <input type="text" data-item-field="icon" value="" placeholder="fas fa-clock">
                                    <button type="button" class="icon-input-btn">Elegir</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea data-item-field="description" rows="2" placeholder="Breve descripción del beneficio..."></textarea>
                        </div>
                    </div>
                `;
            }
            // Areas container template
            if (containerId === 'areasContainer') {
                const uniqueId = Date.now() + '-' + index;
                return `
                    <div class="item-card" data-item-index="${index}">
                        <div class="item-header">
                            <span>Área ${index + 1}</span>
                            <button type="button" class="btn btn-sm btn-danger remove-item-btn">&times;</button>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Título</label>
                                <input type="text" data-item-field="title" value="" placeholder="Desarrollo">
                            </div>
                            <div class="form-group">
                                <label>URL</label>
                                <input type="text" data-item-field="url" value="#" placeholder="/servicios/desarrollo">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Subtítulo (opcional)</label>
                            <input type="text" data-item-field="subtitle" value="" placeholder="Soluciones digitales">
                        </div>
                        <div class="form-group">
                            <label>Descripción (opcional)</label>
                            <textarea data-item-field="description" rows="2" placeholder="Breve descripción..."></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Color de fondo</label>
                                <div class="color-input-wrapper">
                                    <input type="color" class="color-picker-input" data-sync-target="bg-color-${uniqueId}" value="#1A1A1A">
                                    <input type="text" id="bg-color-${uniqueId}" data-item-field="background_color" value="#1A1A1A" placeholder="#1A1A1A" class="color-text-input">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Color del texto</label>
                                <div class="color-input-wrapper">
                                    <input type="color" class="color-picker-input" data-sync-target="text-color-${uniqueId}" value="#ffffff">
                                    <input type="text" id="text-color-${uniqueId}" data-item-field="text_color" value="#ffffff" placeholder="#ffffff" class="color-text-input">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Imagen (se muestra en hover)</label>
                            <div class="image-picker-field">
                                <input type="hidden" data-item-field="image" value="">
                                <div class="image-picker-preview">
                                    <div class="preview-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>Sin imagen</span>
                                    </div>
                                </div>
                                <div class="image-picker-actions">
                                    <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                        <i class="fas fa-upload"></i> Seleccionar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            // Generic fallback
            return `
                <div class="item-card" data-item-index="${index}">
                    <div class="item-header">
                        <span>Item ${index + 1}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-item-btn">&times;</button>
                    </div>
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" data-item-field="title" value="">
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea data-item-field="description" rows="2"></textarea>
                    </div>
                </div>
            `;
        },

        addGalleryItem: function(form, url) {
            const container = form.querySelector('.gallery-items');
            if (!container) return;

            const index = container.querySelectorAll('.gallery-item').length;
            const item = document.createElement('div');
            item.className = 'gallery-item';
            item.dataset.index = index;
            item.innerHTML = `
                <div class="gallery-item-image">
                    <img src="${url}" alt="Gallery image">
                </div>
                <input type="hidden" name="gallery[]" value="${url}">
                <button type="button" class="gallery-item-remove" title="Eliminar">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(item);

            // Bind remove event
            item.querySelector('.gallery-item-remove').addEventListener('click', function() {
                item.remove();
            });
        },

        // Collapsible sections
        initCollapsible: function() {
            document.querySelectorAll('.card-header.collapsible').forEach(function(header) {
                header.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const target = document.getElementById(targetId);
                    if (target) {
                        target.classList.toggle('collapsed');
                        this.querySelector('.collapse-icon').classList.toggle('rotated');
                    }
                });
            });
        },

        // Character count for SEO fields
        initCharCount: function() {
            document.querySelectorAll('.char-count').forEach(function(counter) {
                const targetId = counter.dataset.target;
                const target = document.getElementById(targetId);
                if (target) {
                    const maxLength = target.maxLength || 100;
                    const updateCount = function() {
                        counter.textContent = target.value.length + '/' + maxLength + ' caracteres';
                    };
                    target.addEventListener('input', updateCount);
                    updateCount();
                }
            });
        },

        // LLM Q&A Editor
        initLLMEditor: function() {
            const self = this;
            const enableCheckbox = document.getElementById('enable_llm_qa');
            const editor = document.getElementById('llm-qa-editor');
            const addBtn = document.getElementById('add-qa-btn');
            const generateBtn = document.getElementById('generate-qa-btn');
            const container = document.getElementById('qa-items');
            const hiddenInput = document.getElementById('llm_qa_content');

            if (!enableCheckbox || !editor) return;

            // Ensure Q&A content is synced before form submission
            const form = enableCheckbox.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    self.updateQAHiddenInput();
                });
            }

            // Toggle editor visibility
            enableCheckbox.addEventListener('change', function() {
                editor.style.display = this.checked ? 'block' : 'none';
            });

            // Load existing Q&A
            try {
                const existing = JSON.parse(hiddenInput.value || '[]');
                existing.forEach(function(qa, index) {
                    self.addQAItem(qa.question, qa.answer, index);
                });
            } catch (e) {}

            // Add new Q&A
            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    self.addQAItem('', '', container.children.length);
                });
            }

            // Generate Q&A with AI
            if (generateBtn) {
                generateBtn.addEventListener('click', function() {
                    const entityId = this.dataset.entityId || this.dataset.pageId;
                    const generateUrl = this.dataset.generateUrl || '/admin/pages/' + entityId + '/generate-qa';

                    if (!entityId) {
                        self.showNotification('Guarda primero antes de generar Q&A', 'error');
                        return;
                    }

                    const btn = this;
                    const originalText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

                    fetch(generateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-Token': self.csrfToken
                        },
                        body: '_csrf_token=' + encodeURIComponent(self.csrfToken)
                    })
                    .then(response => response.json())
                    .then(data => {
                        btn.disabled = false;
                        btn.innerHTML = originalText;

                        if (data.success && data.qa_items) {
                            // Clear existing items
                            container.innerHTML = '';

                            // Add new items
                            data.qa_items.forEach(function(qa, index) {
                                self.addQAItem(qa.question, qa.answer, index);
                            });

                            self.updateQAHiddenInput();
                            self.showNotification('Q&A generado correctamente. Revisa y guarda los cambios.', 'success');
                        } else {
                            self.showNotification(data.error || 'Error al generar Q&A', 'error');
                        }
                    })
                    .catch(error => {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        self.showNotification('Error de conexión', 'error');
                        console.error('Generate Q&A error:', error);
                    });
                });
            }
        },

        addQAItem: function(question, answer, index) {
            const container = document.getElementById('qa-items');
            const html = `
                <div class="qa-item" data-index="${index}">
                    <input type="text" class="qa-question" value="${this.escapeHtml(question)}" placeholder="Pregunta">
                    <textarea class="qa-answer" rows="2" placeholder="Respuesta">${this.escapeHtml(answer)}</textarea>
                    <button type="button" class="btn btn-sm btn-danger remove-qa-btn">&times;</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);

            // Add remove handler
            const self = this;
            container.querySelector('.qa-item:last-child .remove-qa-btn').addEventListener('click', function() {
                this.closest('.qa-item').remove();
                self.updateQAHiddenInput();
            });

            // Update hidden input on change
            container.querySelectorAll('.qa-item:last-child input, .qa-item:last-child textarea').forEach(function(input) {
                input.addEventListener('input', function() {
                    self.updateQAHiddenInput();
                });
            });
        },

        updateQAHiddenInput: function() {
            const items = document.querySelectorAll('.qa-item');
            const data = [];
            items.forEach(function(item) {
                const question = item.querySelector('.qa-question').value.trim();
                const answer = item.querySelector('.qa-answer').value.trim();
                if (question || answer) {
                    data.push({ question, answer });
                }
            });
            document.getElementById('llm_qa_content').value = JSON.stringify(data);
        },

        // Utilities
        showNotification: function(message, type) {
            const notification = document.createElement('div');
            notification.className = 'notification notification-' + type;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(function() {
                notification.classList.add('show');
            }, 10);

            setTimeout(function() {
                notification.classList.remove('show');
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }, 3000);
        },

        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // Block types for JavaScript
    window.BLOCK_TYPES = {
        'hero': 'Hero',
        'text_image_left': 'Texto + Imagen (Izq)',
        'text_image_right': 'Texto + Imagen (Der)',
        'text_full_width': 'Texto Ancho Completo',
        'success_cases': 'Casos de Éxito',
        'faq': 'FAQs',
        'features': 'Funcionalidades',
        'posts': 'Posts/Blog',
        'business_types': 'Características Resumidas',
        'integrations': 'Integraciones',
        'pricing_calculator': 'Calculadora de Precios',
        'cta_banner': 'Banner CTA',
        'benefits': 'Características Detalladas',
        'video': 'Video',
        'image_gallery': 'Galería de Imágenes',
        'video_gallery': 'Galería de Videos',
        'clients': 'Clientes (Logos)',
        'custom_html': 'HTML Personalizado'
    };

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        BlockEditor.init();
    });

})();
