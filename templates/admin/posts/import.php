<?php
/**
 * WordPress Import Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Importar desde WordPress</h1>
        <p>Importa posts del blog desde un archivo de exportación WordPress (WXR/XML)</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/posts" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver a Posts
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Subir archivo XML</h2>
    </div>
    <div class="card-body">
        <div class="import-instructions">
            <h4>Instrucciones:</h4>
            <ol>
                <li>En WordPress, ve a <strong>Herramientas → Exportar</strong></li>
                <li>Selecciona <strong>Entradas</strong> y haz clic en <strong>Descargar archivo de exportación</strong></li>
                <li>Sube el archivo XML descargado aquí</li>
            </ol>
        </div>

        <form id="import-form" enctype="multipart/form-data">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <label for="xml_file">Archivo XML de WordPress</label>
                <input type="file" id="xml_file" name="xml_file" accept=".xml" required>
                <small class="form-text">Máximo 50MB. Solo archivos .xml</small>
            </div>

            <div class="form-actions">
                <button type="submit" id="analyze-btn" class="btn btn-primary">
                    <i class="fas fa-search"></i> Analizar archivo
                </button>
            </div>
        </form>

        <!-- Analysis Results (hidden by default) -->
        <div id="analysis-results" style="display: none;">
            <hr>
            <h3>Resumen del archivo</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" id="stat-posts">0</div>
                    <div class="stat-label">Posts a importar</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-images">0</div>
                    <div class="stat-label">Imágenes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-categories">0</div>
                    <div class="stat-label">Categorías</div>
                </div>
            </div>

            <div id="categories-list" class="detail-list" style="display: none;">
                <h4>Categorías encontradas:</h4>
                <ul id="categories-ul"></ul>
            </div>

            <div id="authors-list" class="detail-list" style="display: none;">
                <h4>Autores:</h4>
                <ul id="authors-ul"></ul>
            </div>

            <div class="import-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <p>La importación descargará las imágenes destacadas y creará los posts en estado <strong>publicado</strong>. Las categorías se crearán automáticamente si no existen.</p>
            </div>

            <div class="form-actions">
                <button type="button" id="import-btn" class="btn btn-success btn-lg">
                    <i class="fas fa-download"></i> Importar posts
                </button>
                <button type="button" id="cancel-btn" class="btn btn-outline">
                    Cancelar
                </button>
            </div>
        </div>

        <!-- Import Progress (hidden by default) -->
        <div id="import-progress" style="display: none;">
            <hr>
            <h3>Importando...</h3>
            <div class="progress-bar">
                <div class="progress-bar-fill" id="progress-fill"></div>
            </div>
            <p id="progress-text">Procesando archivo...</p>
        </div>

        <!-- Import Results (hidden by default) -->
        <div id="import-results" style="display: none;">
            <hr>
            <h3>Importación completada</h3>
            <div class="alert alert-success" id="success-message"></div>

            <div id="import-errors" style="display: none;">
                <h4>Errores durante la importación:</h4>
                <ul id="errors-ul" class="error-list"></ul>
            </div>

            <div id="imported-posts" style="display: none;">
                <h4>Posts importados:</h4>
                <ul id="posts-ul" class="imported-list"></ul>
            </div>

            <div class="form-actions">
                <a href="/admin/posts" class="btn btn-primary">
                    <i class="fas fa-list"></i> Ver todos los posts
                </a>
                <button type="button" id="new-import-btn" class="btn btn-outline">
                    <i class="fas fa-redo"></i> Nueva importación
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.import-instructions {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.import-instructions h4 {
    margin: 0 0 1rem 0;
    color: var(--gray-700);
}

.import-instructions ol {
    margin: 0;
    padding-left: 1.5rem;
}

.import-instructions li {
    margin-bottom: 0.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin: 1.5rem 0;
}

.stat-card {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-top: 0.5rem;
}

.detail-list {
    background: var(--gray-50);
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin: 1rem 0;
}

.detail-list h4 {
    margin: 0 0 0.75rem 0;
    font-size: 0.875rem;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.detail-list ul {
    margin: 0;
    padding-left: 1.5rem;
}

.detail-list li {
    margin-bottom: 0.25rem;
}

.import-warning {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    background: #fef3cd;
    border: 1px solid #ffc107;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin: 1.5rem 0;
}

.import-warning i {
    color: #856404;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.import-warning p {
    margin: 0;
    color: #856404;
}

.progress-bar {
    width: 100%;
    height: 20px;
    background: var(--gray-200);
    border-radius: 10px;
    overflow: hidden;
    margin: 1rem 0;
}

.progress-bar-fill {
    height: 100%;
    background: var(--primary-color);
    width: 0%;
    transition: width 0.3s ease;
}

#progress-text {
    text-align: center;
    color: var(--gray-600);
}

.error-list {
    background: #f8d7da;
    padding: 1rem;
    border-radius: 8px;
    max-height: 200px;
    overflow-y: auto;
}

.error-list li {
    color: #721c24;
    margin-bottom: 0.5rem;
}

.imported-list {
    max-height: 300px;
    overflow-y: auto;
    background: var(--gray-50);
    padding: 1rem;
    border-radius: 8px;
}

.imported-list li {
    margin-bottom: 0.5rem;
}

.imported-list a {
    color: var(--primary-color);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('import-form');
    const analyzeBtn = document.getElementById('analyze-btn');
    const importBtn = document.getElementById('import-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const newImportBtn = document.getElementById('new-import-btn');

    const analysisResults = document.getElementById('analysis-results');
    const importProgress = document.getElementById('import-progress');
    const importResults = document.getElementById('import-results');

    // Analyze file
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        analyzeBtn.disabled = true;
        analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analizando...';

        try {
            const response = await fetch('/admin/posts/import/analyze', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showAnalysisResults(data.stats);
            } else {
                alert('Error: ' + data.error);
            }
        } catch (error) {
            alert('Error de conexión');
        } finally {
            analyzeBtn.disabled = false;
            analyzeBtn.innerHTML = '<i class="fas fa-search"></i> Analizar archivo';
        }
    });

    // Show analysis results
    function showAnalysisResults(stats) {
        document.getElementById('stat-posts').textContent = stats.posts || 0;
        document.getElementById('stat-images').textContent = stats.attachments || 0;
        document.getElementById('stat-categories').textContent = (stats.categories || []).length;

        // Show categories
        if (stats.categories && stats.categories.length > 0) {
            const categoriesUl = document.getElementById('categories-ul');
            categoriesUl.innerHTML = stats.categories.map(c => `<li>${c}</li>`).join('');
            document.getElementById('categories-list').style.display = 'block';
        }

        // Show authors
        if (stats.authors && stats.authors.length > 0) {
            const authorsUl = document.getElementById('authors-ul');
            authorsUl.innerHTML = stats.authors.map(a => `<li>${a}</li>`).join('');
            document.getElementById('authors-list').style.display = 'block';
        }

        analysisResults.style.display = 'block';
        form.style.display = 'none';
    }

    // Cancel
    cancelBtn.addEventListener('click', function() {
        analysisResults.style.display = 'none';
        form.style.display = 'block';
        form.reset();
    });

    // Execute import
    importBtn.addEventListener('click', async function() {
        if (!confirm('¿Estás seguro de que quieres importar los posts? Esta acción no se puede deshacer.')) {
            return;
        }

        analysisResults.style.display = 'none';
        importProgress.style.display = 'block';

        const progressFill = document.getElementById('progress-fill');
        const progressText = document.getElementById('progress-text');

        // Simulate progress
        progressFill.style.width = '30%';
        progressText.textContent = 'Descargando imágenes...';

        try {
            const response = await fetch('/admin/posts/import/execute', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: '_csrf_token=' + encodeURIComponent('<?= htmlspecialchars($csrf_token) ?>')
            });

            progressFill.style.width = '80%';
            progressText.textContent = 'Creando posts...';

            const data = await response.json();

            progressFill.style.width = '100%';

            setTimeout(() => {
                importProgress.style.display = 'none';
                showImportResults(data);
            }, 500);

        } catch (error) {
            importProgress.style.display = 'none';
            alert('Error de conexión durante la importación');
            form.style.display = 'block';
        }
    });

    // Show import results
    function showImportResults(data) {
        if (data.success) {
            document.getElementById('success-message').innerHTML =
                `<i class="fas fa-check-circle"></i> Se han importado <strong>${data.imported}</strong> posts correctamente.`;

            // Show errors if any
            if (data.errors && data.errors.length > 0) {
                const errorsUl = document.getElementById('errors-ul');
                errorsUl.innerHTML = data.errors.map(e => `<li>${e}</li>`).join('');
                document.getElementById('import-errors').style.display = 'block';
            }

            // Show imported posts
            if (data.posts && data.posts.length > 0) {
                const postsUl = document.getElementById('posts-ul');
                postsUl.innerHTML = data.posts.map(p =>
                    `<li><a href="/admin/posts/${p.id}/edit" target="_blank">${p.title}</a></li>`
                ).join('');
                document.getElementById('imported-posts').style.display = 'block';
            }
        } else {
            document.getElementById('success-message').className = 'alert alert-error';
            document.getElementById('success-message').innerHTML =
                `<i class="fas fa-times-circle"></i> Error: ${data.error}`;
        }

        importResults.style.display = 'block';
    }

    // New import
    newImportBtn.addEventListener('click', function() {
        importResults.style.display = 'none';
        document.getElementById('import-errors').style.display = 'none';
        document.getElementById('imported-posts').style.display = 'none';
        document.getElementById('categories-list').style.display = 'none';
        document.getElementById('authors-list').style.display = 'none';
        form.style.display = 'block';
        form.reset();
    });
});
</script>
