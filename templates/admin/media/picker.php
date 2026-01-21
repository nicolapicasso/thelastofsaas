<?php
/**
 * Media Picker Template
 * Standalone popup for selecting media files
 */
$targetField = $_GET['target'] ?? 'image_url';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Imagen - Media Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.5;
        }

        .picker-header {
            background: #1a1a1a;
            color: white;
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .picker-header h2 {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .picker-header .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem;
        }

        .picker-toolbar {
            background: white;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .picker-toolbar input[type="text"] {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .picker-toolbar select {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background: white;
        }

        .picker-toolbar .upload-btn {
            background: #3B82F6;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .picker-toolbar .upload-btn:hover {
            background: #2563EB;
        }

        .picker-content {
            padding: 1.5rem;
        }

        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .media-item {
            background: white;
            border: 2px solid transparent;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .media-item:hover {
            border-color: #3B82F6;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .media-item.selected {
            border-color: #10B981;
        }

        .media-item.selected::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 8px;
            right: 8px;
            background: #10B981;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .media-preview {
            aspect-ratio: 1;
            overflow: hidden;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .media-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .media-preview .file-icon {
            font-size: 3rem;
            color: #999;
        }

        .media-info {
            padding: 0.75rem;
            font-size: 12px;
        }

        .media-info .filename {
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.25rem;
        }

        .media-info .filesize {
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ddd;
        }

        .picker-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e0e0e0;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .picker-footer .selected-info {
            color: #666;
            font-size: 14px;
        }

        .picker-footer .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }

        .btn-cancel {
            background: #e0e0e0;
            color: #333;
        }

        .btn-select {
            background: #10B981;
            color: white;
        }

        .btn-select:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Upload Modal */
        .upload-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .upload-modal.active {
            display: flex;
        }

        .upload-modal-content {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
        }

        .upload-modal h3 {
            margin-bottom: 1rem;
        }

        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 3rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: #3B82F6;
            background: #f8fafc;
        }

        .upload-area i {
            font-size: 3rem;
            color: #999;
            margin-bottom: 1rem;
        }

        .upload-area input {
            display: none;
        }

        .upload-progress {
            margin-top: 1rem;
            display: none;
        }

        .upload-progress.active {
            display: block;
        }

        .progress-bar {
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: #3B82F6;
            width: 0%;
            transition: width 0.3s;
        }

        .loading-spinner {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        .loading-spinner i {
            font-size: 2rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <header class="picker-header">
        <h2><i class="fas fa-images"></i> Seleccionar Imagen</h2>
        <button class="close-btn" onclick="window.close()">&times;</button>
    </header>

    <div class="picker-toolbar">
        <input type="text" id="searchInput" placeholder="Buscar archivos..." onkeyup="debounceSearch()">
        <select id="typeFilter" onchange="loadMedia()">
            <option value="image">Imagenes</option>
            <option value="">Todos los tipos</option>
        </select>
        <button class="upload-btn" onclick="openUploadModal()">
            <i class="fas fa-upload"></i> Subir
        </button>
    </div>

    <div class="picker-content">
        <div class="media-grid" id="mediaGrid">
            <?php if (empty($items)): ?>
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <i class="fas fa-images"></i>
                    <h3>No hay archivos</h3>
                    <p>Sube imagenes para empezar</p>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <?php
                    $itemUrl = '/uploads/' . ($item['filepath'] ?? '');
                    $itemType = $item['filetype'] ?? '';
                    $itemSize = $item['filesize'] ?? 0;
                    ?>
                    <div class="media-item" data-url="<?= htmlspecialchars($itemUrl) ?>" onclick="selectItem(this)">
                        <div class="media-preview">
                            <?php if (strpos($itemType, 'image/') === 0): ?>
                                <img src="<?= htmlspecialchars($itemUrl) ?>" alt="">
                            <?php else: ?>
                                <i class="fas fa-file file-icon"></i>
                            <?php endif; ?>
                        </div>
                        <div class="media-info">
                            <div class="filename"><?= htmlspecialchars($item['filename'] ?? '') ?></div>
                            <div class="filesize"><?= \App\Models\Media::formatSize($itemSize) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="picker-footer">
        <span class="selected-info" id="selectedInfo">Ninguna imagen seleccionada</span>
        <div class="btn-group">
            <button class="btn btn-cancel" onclick="window.close()">Cancelar</button>
            <button class="btn btn-select" id="selectBtn" disabled onclick="confirmSelection()">Seleccionar</button>
        </div>
    </footer>

    <!-- Upload Modal -->
    <div class="upload-modal" id="uploadModal">
        <div class="upload-modal-content">
            <h3>Subir Imagen</h3>
            <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Haz clic o arrastra una imagen aqui</p>
                <input type="file" id="fileInput" accept="image/*" onchange="uploadFile(this.files[0])">
            </div>
            <div class="upload-progress" id="uploadProgress">
                <div class="progress-bar">
                    <div class="progress-bar-fill" id="progressFill"></div>
                </div>
                <p style="margin-top: 0.5rem; font-size: 14px; color: #666;">Subiendo...</p>
            </div>
            <button class="btn btn-cancel" style="margin-top: 1rem; width: 100%;" onclick="closeUploadModal()">Cerrar</button>
        </div>
    </div>

    <script>
    const targetField = '<?= htmlspecialchars($targetField) ?>';
    let selectedUrl = null;
    let searchTimeout = null;

    function selectItem(element) {
        // Remove previous selection
        document.querySelectorAll('.media-item.selected').forEach(el => el.classList.remove('selected'));

        // Add selection to clicked item
        element.classList.add('selected');
        selectedUrl = element.dataset.url;

        // Update UI
        document.getElementById('selectedInfo').textContent = 'Imagen seleccionada';
        document.getElementById('selectBtn').disabled = false;
    }

    function confirmSelection() {
        if (selectedUrl && window.opener) {
            // Call the parent window's selectMedia function
            if (typeof window.opener.selectMedia === 'function') {
                window.opener.selectMedia(selectedUrl, targetField);
            }
            window.close();
        }
    }

    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadMedia, 300);
    }

    function loadMedia() {
        const search = document.getElementById('searchInput').value;
        const type = document.getElementById('typeFilter').value;
        const grid = document.getElementById('mediaGrid');

        grid.innerHTML = '<div class="loading-spinner" style="grid-column: 1 / -1;"><i class="fas fa-spinner"></i><p>Cargando...</p></div>';

        fetch(`/admin/media/browse?type=${type}&search=${encodeURIComponent(search)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.items.length > 0) {
                    grid.innerHTML = data.items.map(item => `
                        <div class="media-item" data-url="${item.url}" onclick="selectItem(this)">
                            <div class="media-preview">
                                ${item.mime_type.startsWith('image/')
                                    ? `<img src="${item.url}" alt="">`
                                    : '<i class="fas fa-file file-icon"></i>'}
                            </div>
                            <div class="media-info">
                                <div class="filename">${item.filename || ''}</div>
                                <div class="filesize">${item.formatted_size || ''}</div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    grid.innerHTML = '<div class="empty-state" style="grid-column: 1 / -1;"><i class="fas fa-images"></i><h3>No hay archivos</h3></div>';
                }
            })
            .catch(err => {
                grid.innerHTML = '<div class="empty-state" style="grid-column: 1 / -1;"><i class="fas fa-exclamation-triangle"></i><h3>Error al cargar</h3></div>';
            });
    }

    function openUploadModal() {
        document.getElementById('uploadModal').classList.add('active');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.remove('active');
        document.getElementById('uploadProgress').classList.remove('active');
        document.getElementById('progressFill').style.width = '0%';
    }

    function uploadFile(file) {
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const progress = document.getElementById('uploadProgress');
        const progressFill = document.getElementById('progressFill');
        progress.classList.add('active');

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/admin/media/upload', true);

        xhr.upload.onprogress = (e) => {
            if (e.lengthComputable) {
                const percent = (e.loaded / e.total) * 100;
                progressFill.style.width = percent + '%';
            }
        };

        xhr.onload = () => {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        closeUploadModal();
                        loadMedia(); // Refresh the grid
                    } else {
                        alert('Error: ' + (response.message || 'Error al subir'));
                    }
                } catch (e) {
                    alert('Error al procesar respuesta');
                }
            } else {
                alert('Error al subir el archivo');
            }
            progress.classList.remove('active');
            progressFill.style.width = '0%';
        };

        xhr.onerror = () => {
            alert('Error de conexion');
            progress.classList.remove('active');
        };

        xhr.send(formData);
    }

    // Drag and drop support
    const uploadArea = document.getElementById('uploadArea');
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            uploadFile(e.dataTransfer.files[0]);
        }
    });
    </script>
</body>
</html>
