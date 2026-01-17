<?php
/**
 * Media Library Template
 * Omniwallet CMS
 */
use App\Models\Media;
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Media Library</h1>
        <p>Gestiona los archivos multimedia</p>
    </div>
    <div class="page-header-actions">
        <button type="button" class="btn btn-primary" onclick="openUploadModal()">
            <i class="fas fa-upload"></i> Subir Archivos
        </button>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<!-- Statistics -->
<div class="stats-grid media-stats">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-file"></i></div>
        <span class="stat-value"><?= number_format($statistics['total_files']) ?></span>
        <span class="stat-label">Archivos totales</span>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-image"></i></div>
        <span class="stat-value"><?= number_format($statistics['images']) ?></span>
        <span class="stat-label">Imágenes</span>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-video"></i></div>
        <span class="stat-value"><?= number_format($statistics['videos']) ?></span>
        <span class="stat-label">Vídeos</span>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-hdd"></i></div>
        <span class="stat-value"><?= Media::formatSize($statistics['total_size']) ?></span>
        <span class="stat-label">Espacio usado</span>
    </div>
</div>

<!-- Filters -->
<div class="card filters-card">
    <form method="GET" action="/admin/media" class="filters-form">
        <div class="filter-group">
            <label>Tipo</label>
            <select name="type" onchange="this.form.submit()">
                <option value="">Todos los tipos</option>
                <option value="image" <?= $currentType === 'image' ? 'selected' : '' ?>>Imágenes</option>
                <option value="video" <?= $currentType === 'video' ? 'selected' : '' ?>>Vídeos</option>
                <option value="document" <?= $currentType === 'document' ? 'selected' : '' ?>>Documentos</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Buscar</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                   placeholder="Nombre del archivo...">
        </div>
        <button type="submit" class="btn btn-outline btn-sm">
            <i class="fas fa-search"></i> Buscar
        </button>
        <?php if ($currentType || $search): ?>
            <a href="/admin/media" class="btn btn-outline btn-sm">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Media Grid -->
<div class="card">
    <?php if (empty($items)): ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <h3>No hay archivos</h3>
            <p>Sube tu primer archivo multimedia</p>
            <button type="button" class="btn btn-primary" onclick="openUploadModal()">
                <i class="fas fa-upload"></i> Subir Archivo
            </button>
        </div>
    <?php else: ?>
        <div class="media-grid">
            <?php foreach ($items as $item): ?>
                <div class="media-item" data-id="<?= $item['id'] ?>" onclick="openMediaModal(<?= $item['id'] ?>)">
                    <div class="media-preview">
                        <?php if (strpos($item['mime_type'], 'image/') === 0): ?>
                            <img src="<?= htmlspecialchars($item['url']) ?>" alt="<?= htmlspecialchars($item['alt_text'] ?? '') ?>">
                        <?php elseif (strpos($item['mime_type'], 'video/') === 0): ?>
                            <div class="media-icon video">
                                <i class="fas fa-play-circle"></i>
                            </div>
                        <?php else: ?>
                            <div class="media-icon document">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="media-info">
                        <span class="media-name" title="<?= htmlspecialchars($item['original_filename']) ?>">
                            <?= htmlspecialchars(substr($item['original_filename'], 0, 20)) ?><?= strlen($item['original_filename']) > 20 ? '...' : '' ?>
                        </span>
                        <span class="media-size"><?= Media::formatSize($item['file_size']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['pages'] > 1): ?>
            <div class="card-footer">
                <div class="pagination">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?><?= $currentType ? '&type=' . $currentType : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <span class="pagination-info">
                        Página <?= $pagination['current_page'] ?> de <?= $pagination['pages'] ?>
                    </span>

                    <?php if ($pagination['current_page'] < $pagination['pages']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?><?= $currentType ? '&type=' . $currentType : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Subir Archivos</h3>
            <button type="button" class="modal-close" onclick="closeUploadModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="dropZone" class="drop-zone">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Arrastra archivos aquí o</p>
                <label class="btn btn-primary">
                    <i class="fas fa-folder-open"></i> Seleccionar archivos
                    <input type="file" id="fileInput" multiple accept="image/*,video/*,.pdf" style="display: none;">
                </label>
                <small>Máximo 10MB por archivo. Formatos: JPG, PNG, GIF, WebP, SVG, PDF, MP4, WebM</small>
            </div>
            <div id="uploadProgress" class="upload-progress" style="display: none;">
                <div class="progress-list"></div>
            </div>
        </div>
    </div>
</div>

<!-- Media Detail Modal -->
<div id="mediaModal" class="modal" style="display: none;">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3>Detalles del archivo</h3>
            <button type="button" class="modal-close" onclick="closeMediaModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="media-detail-layout">
                <div class="media-detail-preview">
                    <img id="mediaPreviewImg" src="" alt="">
                    <div id="mediaPreviewIcon" class="media-detail-icon" style="display: none;">
                        <i class="fas fa-file"></i>
                    </div>
                </div>
                <div class="media-detail-info">
                    <form id="mediaEditForm" method="POST">
                        <input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">

                        <div class="form-group">
                            <label>URL</label>
                            <div class="input-copy">
                                <input type="text" id="mediaUrl" readonly>
                                <button type="button" onclick="copyToClipboard('mediaUrl')" class="btn btn-sm btn-outline">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mediaAlt">Texto alternativo</label>
                            <input type="text" id="mediaAlt" name="alt_text" placeholder="Descripción de la imagen">
                        </div>

                        <div class="form-group">
                            <label for="mediaTitle">Título</label>
                            <input type="text" id="mediaTitle" name="title" placeholder="Título del archivo">
                        </div>

                        <div class="meta-list" id="mediaMetaList"></div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <button type="button" class="btn btn-outline btn-danger" onclick="deleteMedia()">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.media-stats .stat-icon i {
    font-size: 20px;
    color: var(--color-primary);
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: var(--spacing-md);
    padding: var(--spacing-lg);
}

.media-item {
    cursor: pointer;
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 2px solid transparent;
    transition: all var(--transition);
}

.media-item:hover {
    border-color: var(--color-primary);
}

.media-preview {
    aspect-ratio: 1;
    background-color: var(--color-gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.media-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.media-icon {
    font-size: 48px;
    color: var(--color-gray-400);
}

.media-icon.video { color: var(--color-primary); }
.media-icon.document { color: var(--color-danger); }

.media-info {
    padding: var(--spacing-sm);
    background-color: white;
}

.media-name {
    display: block;
    font-size: 12px;
    font-weight: 500;
    color: var(--color-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.media-size {
    font-size: 11px;
    color: var(--color-gray-500);
}

/* Drop Zone */
.drop-zone {
    border: 2px dashed var(--color-gray-300);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl) * 2;
    text-align: center;
    transition: all var(--transition);
}

.drop-zone.dragover {
    border-color: var(--color-primary);
    background-color: var(--color-gray-50);
}

.drop-zone i {
    font-size: 48px;
    color: var(--color-gray-400);
    margin-bottom: var(--spacing-md);
}

.drop-zone p {
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-md);
}

.drop-zone small {
    display: block;
    margin-top: var(--spacing-md);
    color: var(--color-gray-500);
}

/* Upload Progress */
.upload-progress {
    margin-top: var(--spacing-lg);
}

.progress-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-sm);
    background-color: var(--color-gray-50);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-sm);
}

.progress-item .filename {
    flex: 1;
    font-size: 13px;
}

.progress-item .progress-bar {
    width: 100px;
    height: 4px;
    background-color: var(--color-gray-200);
    border-radius: 2px;
    overflow: hidden;
}

.progress-item .progress-fill {
    height: 100%;
    background-color: var(--color-primary);
    transition: width 0.3s;
}

.progress-item .status-icon {
    font-size: 14px;
}

.progress-item .status-icon.success { color: var(--color-success); }
.progress-item .status-icon.error { color: var(--color-danger); }

/* Media Detail */
.modal-lg .modal-content {
    max-width: 800px;
}

.media-detail-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-lg);
}

.media-detail-preview {
    background-color: var(--color-gray-100);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    overflow: hidden;
}

.media-detail-preview img {
    max-width: 100%;
    max-height: 400px;
    object-fit: contain;
}

.media-detail-icon {
    font-size: 80px;
    color: var(--color-gray-400);
}

.input-copy {
    display: flex;
    gap: var(--spacing-xs);
}

.input-copy input {
    flex: 1;
}

.form-actions {
    display: flex;
    gap: var(--spacing-sm);
    margin-top: var(--spacing-lg);
}

@media (max-width: 768px) {
    .media-detail-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
let currentMediaId = null;

function openUploadModal() {
    document.getElementById('uploadModal').style.display = 'flex';
}

function closeUploadModal() {
    document.getElementById('uploadModal').style.display = 'none';
    document.getElementById('uploadProgress').style.display = 'none';
    document.querySelector('.progress-list').innerHTML = '';
}

function openMediaModal(id) {
    currentMediaId = id;
    document.getElementById('mediaModal').style.display = 'flex';
    loadMediaDetails(id);
}

function closeMediaModal() {
    document.getElementById('mediaModal').style.display = 'none';
    currentMediaId = null;
}

function loadMediaDetails(id) {
    fetch(`/admin/media/${id}/info`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const media = data.media;
                document.getElementById('mediaUrl').value = media.url;
                document.getElementById('mediaAlt').value = media.alt_text || '';
                document.getElementById('mediaTitle').value = media.title || '';
                document.getElementById('mediaEditForm').action = `/admin/media/${id}/update`;

                // Preview
                if (media.mime_type.startsWith('image/')) {
                    document.getElementById('mediaPreviewImg').src = media.url;
                    document.getElementById('mediaPreviewImg').style.display = 'block';
                    document.getElementById('mediaPreviewIcon').style.display = 'none';
                } else {
                    document.getElementById('mediaPreviewImg').style.display = 'none';
                    document.getElementById('mediaPreviewIcon').style.display = 'flex';
                }

                // Meta
                document.getElementById('mediaMetaList').innerHTML = `
                    <div class="meta-item"><span class="meta-label">Archivo</span><span class="meta-value">${media.original_filename}</span></div>
                    <div class="meta-item"><span class="meta-label">Tamaño</span><span class="meta-value">${media.formatted_size}</span></div>
                    <div class="meta-item"><span class="meta-label">Tipo</span><span class="meta-value">${media.mime_type}</span></div>
                    ${media.width ? `<div class="meta-item"><span class="meta-label">Dimensiones</span><span class="meta-value">${media.width} × ${media.height} px</span></div>` : ''}
                `;
            }
        });
}

function deleteMedia() {
    if (confirm('¿Eliminar este archivo?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/media/${currentMediaId}/delete`;
        form.innerHTML = `<input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">`;
        document.body.appendChild(form);
        form.submit();
    }
}

function copyToClipboard(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    document.execCommand('copy');
}

// File upload handling
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    handleFiles(e.dataTransfer.files);
});

fileInput.addEventListener('change', () => {
    handleFiles(fileInput.files);
});

function handleFiles(files) {
    if (files.length === 0) return;

    document.getElementById('uploadProgress').style.display = 'block';
    const progressList = document.querySelector('.progress-list');

    Array.from(files).forEach(file => {
        const itemId = 'upload_' + Date.now() + Math.random();
        progressList.innerHTML += `
            <div class="progress-item" id="${itemId}">
                <span class="filename">${file.name}</span>
                <div class="progress-bar"><div class="progress-fill" style="width: 0%"></div></div>
                <span class="status-icon"><i class="fas fa-spinner fa-spin"></i></span>
            </div>
        `;

        uploadFile(file, itemId);
    });
}

function uploadFile(file, itemId) {
    const formData = new FormData();
    formData.append('file', file);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/admin/media/upload');

    xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
            const percent = (e.loaded / e.total) * 100;
            document.querySelector(`#${itemId} .progress-fill`).style.width = percent + '%';
        }
    };

    xhr.onload = () => {
        const item = document.getElementById(itemId);
        const response = JSON.parse(xhr.responseText);

        if (response.success) {
            item.querySelector('.status-icon').innerHTML = '<i class="fas fa-check"></i>';
            item.querySelector('.status-icon').classList.add('success');
            setTimeout(() => location.reload(), 1000);
        } else {
            item.querySelector('.status-icon').innerHTML = '<i class="fas fa-times"></i>';
            item.querySelector('.status-icon').classList.add('error');
            item.querySelector('.filename').textContent += ' - ' + response.error;
        }
    };

    xhr.send(formData);
}
</script>
