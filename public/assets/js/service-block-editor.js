/**
 * We're Sinapsis CMS - Service Block Editor
 * Drag & drop block management for services
 */

(function() {
    'use strict';

    const ServiceBlockEditor = {
        serviceId: null,
        csrfToken: null,
        currentBlockId: null,
        currentBlockType: null,

        init: function() {
            const container = document.getElementById('blocks-container');
            const csrfInput = document.querySelector('input[name="_csrf_token"]');

            // Gallery handlers work via event delegation (must init before early return)
            this.initGalleryHandlers();

            if (!container || !csrfInput) return;

            this.serviceId = container.dataset.serviceId;
            this.csrfToken = csrfInput.value;

            this.initDragDrop();
            this.initModals();
            this.initBlockActions();
        },

        // Gallery handlers using event delegation (works for dynamically loaded content)
        initGalleryHandlers: function() {
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

                    if (window.imagePicker && window.imagePicker.openForEditor) {
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
                        alert('Selector de imágenes no disponible');
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
                    console.log('Add video clicked');

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
                    ServiceBlockEditor.saveBlockOrder();
                }
            });

            container.addEventListener('dragover', function(e) {
                e.preventDefault();
                const afterElement = ServiceBlockEditor.getDragAfterElement(container, e.clientY);
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
            const body = 'service_id=' + this.serviceId + '&block_ids[]=' + blockIds.join('&block_ids[]=') + '&_csrf_token=' + this.csrfToken;

            fetch('/admin/service-blocks/reorder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': this.csrfToken
                },
                body: body
            })
            .then(response => response.json())
            .then(data => {
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

            fetch('/admin/services/' + this.serviceId + '/blocks', {
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

            // Load block form (using same form endpoint as pages)
            fetch(`/admin/service-blocks/form?block_id=${blockId}&type=${blockType}`)
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

            fetch('/admin/service-blocks/' + this.currentBlockId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': this.csrfToken
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

            // Handle items pattern
            const itemsContainer = form.querySelector('.items-container');
            if (itemsContainer) {
                content.items = [];
                itemsContainer.querySelectorAll('.item-card').forEach(function(item) {
                    const itemData = {};
                    item.querySelectorAll('[data-item-field]').forEach(function(field) {
                        itemData[field.dataset.itemField] = field.value;
                    });
                    content.items.push(itemData);
                });
            }

            return content;
        },

        collectBlockSettings: function(form) {
            const settings = {};
            const settingsInputs = form.querySelectorAll('[data-setting]');

            settingsInputs.forEach(function(input) {
                const key = input.dataset.setting;
                if (input.type === 'checkbox') {
                    settings[key] = input.checked;
                } else if (input.type === 'radio') {
                    if (input.checked) {
                        settings[key] = input.value;
                    }
                } else if (input.type === 'number') {
                    settings[key] = parseFloat(input.value) || 0;
                } else {
                    const value = input.value;
                    if (value && (value.startsWith('[') || value.startsWith('{'))) {
                        try {
                            settings[key] = JSON.parse(value);
                        } catch (e) {
                            settings[key] = value;
                        }
                    } else {
                        settings[key] = value;
                    }
                }
            });

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

            fetch('/admin/service-blocks/' + blockId + '/delete', {
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
                                <p>Este servicio no tiene bloques todavía.</p>
                                <p>Haz clic en "Añadir bloque" para empezar a diseñar la página del servicio.</p>
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

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            fetch('/admin/service-blocks/' + blockId + '/clone', {
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
            const self = this;
            const form = document.getElementById('block-editor-body');
            if (!form) return;

            // Handle "Add slide" button for hero block
            const addSlideBtn = form.querySelector('.add-slide-btn');
            if (addSlideBtn) {
                addSlideBtn.addEventListener('click', function() {
                    const container = form.querySelector('.slides-container');
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

            // Handle "Add item" buttons
            const addItemBtns = form.querySelectorAll('.add-item-btn');
            addItemBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const containerId = btn.dataset.container;
                    const container = document.getElementById(containerId);
                    if (!container) return;

                    const index = container.querySelectorAll('.item-card').length;
                    const itemHtml = self.getItemTemplate(containerId, index);
                    container.insertAdjacentHTML('beforeend', itemHtml);

                    if (window.imagePicker) {
                        window.imagePicker.bindPickers();
                    }
                    if (window.iconPicker) {
                        window.iconPicker.bindInputs();
                    }
                });
            });

            // Handle remove item buttons
            form.querySelectorAll('.items-container').forEach(function(container) {
                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-item-btn') || e.target.closest('.remove-item-btn')) {
                        const btn = e.target.classList.contains('remove-item-btn') ? e.target : e.target.closest('.remove-item-btn');
                        btn.closest('.item-card').remove();
                    }
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

            // ========== SERVICES BLOCK HANDLERS ==========

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

            if (selectedServicesInput && serviceCheckboxes.length > 0) {
                function updateSelectedServices() {
                    const selected = [];
                    serviceCheckboxes.forEach(function(cb) {
                        if (cb.checked) {
                            selected.push(parseInt(cb.value, 10));
                        }
                    });
                    selectedServicesInput.value = JSON.stringify(selected);
                }

                serviceCheckboxes.forEach(function(cb) {
                    cb.addEventListener('change', updateSelectedServices);
                });
            }

            // ========== CLIENTS BLOCK HANDLERS ==========

            // Handle Clients block - display mode toggle
            const clientsDisplayMode = form.querySelector('#clients-display-mode');
            const clientsGridOptions = form.querySelector('#grid-options');
            const clientsCarouselOptions = form.querySelector('#carousel-options');

            if (clientsDisplayMode && clientsGridOptions && clientsCarouselOptions) {
                clientsDisplayMode.addEventListener('change', function() {
                    clientsGridOptions.style.display = this.value === 'grid' ? '' : 'none';
                    clientsCarouselOptions.style.display = this.value === 'carousel' ? '' : 'none';
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
                }

                themeCheckboxes.forEach(function(cb) {
                    cb.addEventListener('change', updateSelectedThemes);
                });
            }

            // ========== TOOLS BLOCK HANDLERS ==========

            // Handle Tools block - display mode toggle
            const toolsDisplayMode = form.querySelector('#tools-display-mode');
            const toolsGridOptions = form.querySelector('#tools-grid-options');
            const toolsCarouselOptions = form.querySelector('#tools-carousel-options');

            if (toolsDisplayMode && toolsGridOptions && toolsCarouselOptions) {
                toolsDisplayMode.addEventListener('change', function() {
                    toolsGridOptions.style.display = this.value === 'grid' ? '' : 'none';
                    toolsCarouselOptions.style.display = this.value === 'carousel' ? '' : 'none';
                });
            }

            // Handle Tools block - selection mode toggle
            const toolsSelectionMode = form.querySelector('#tools-selection-mode');
            const toolsCategoryGroup = form.querySelector('#tools-category-filter-group');
            const toolsManualGroup = form.querySelector('#tools-manual-selection-group');
            const toolsLimitGroup = form.querySelector('#tools-limit-group');

            if (toolsSelectionMode) {
                toolsSelectionMode.addEventListener('change', function() {
                    if (toolsCategoryGroup) toolsCategoryGroup.style.display = this.value === 'category' ? '' : 'none';
                    if (toolsManualGroup) toolsManualGroup.style.display = this.value === 'manual' ? '' : 'none';
                    if (toolsLimitGroup) toolsLimitGroup.style.display = this.value === 'manual' ? 'none' : '';
                });
            }

            // Handle Tools block - autoplay toggle
            const toolsAutoplay = form.querySelector('#tools-autoplay');
            const toolsAutoplaySpeed = form.querySelector('#tools-autoplay-speed-group');

            if (toolsAutoplay && toolsAutoplaySpeed) {
                toolsAutoplay.addEventListener('change', function() {
                    toolsAutoplaySpeed.style.display = this.checked ? '' : 'none';
                });
            }

            // Handle Tools block - manual tool selection checkboxes
            const selectedToolsInput = form.querySelector('#selected-tools-input');
            const toolCheckboxes = form.querySelectorAll('.tools-checklist input[type="checkbox"]');

            if (selectedToolsInput && toolCheckboxes.length > 0) {
                function updateSelectedTools() {
                    const selected = [];
                    toolCheckboxes.forEach(function(cb) {
                        if (cb.checked) {
                            selected.push(parseInt(cb.value, 10));
                        }
                    });
                    selectedToolsInput.value = JSON.stringify(selected);
                }

                toolCheckboxes.forEach(function(cb) {
                    cb.addEventListener('change', updateSelectedTools);
                });
            }
        },

        getItemTemplate: function(containerId, index) {
            if (containerId === 'benefitsContainer') {
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
    window.BLOCK_TYPES = window.BLOCK_TYPES || {
        'hero': 'Hero',
        'text_image_left': 'Texto + Imagen (Izq)',
        'text_image_right': 'Texto + Imagen (Der)',
        'text_full_width': 'Texto Ancho Completo',
        'areas': 'Áreas',
        'success_cases': 'Casos de Éxito',
        'faq': 'FAQs',
        'features': 'Funcionalidades',
        'posts': 'Posts/Blog',
        'business_types': 'Características Resumidas',
        'integrations': 'Integraciones',
        'pricing': 'Precios',
        'pricing_calculator': 'Calculadora de Precios',
        'cta_banner': 'Banner CTA',
        'benefits': 'Características Detalladas',
        'video': 'Video',
        'clients': 'Clientes (Logos)',
        'partners': 'Partners',
        'team': 'Equipo',
        'landings': 'Landings',
        'contact_form': 'Formulario Contacto',
        'custom_html': 'HTML Personalizado'
    };

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        ServiceBlockEditor.init();
    });

})();
