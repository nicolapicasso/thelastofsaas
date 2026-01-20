<?php
/**
 * Sponsor Import Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Importar Sponsors</h1>
        <p>Importar sponsors desde un archivo CSV</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/sponsors" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Subir archivo CSV</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/sponsors/import" enctype="multipart/form-data">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

            <div class="form-group">
                <label for="csv_file">Archivo CSV *</label>
                <input type="file" id="csv_file" name="csv_file" class="form-control" accept=".csv" required>
                <small class="form-text text-muted">Selecciona un archivo CSV con los datos de los sponsors.</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Importar
                </button>
                <a href="/admin/sponsors" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Formato del archivo</h3>
    </div>
    <div class="card-body">
        <p>El archivo CSV debe contener las siguientes columnas:</p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Columna</th>
                    <th>Requerido</th>
                    <th>Descripcion</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>name</code></td>
                    <td>Si</td>
                    <td>Nombre del sponsor</td>
                </tr>
                <tr>
                    <td><code>description</code></td>
                    <td>No</td>
                    <td>Descripcion completa del sponsor</td>
                </tr>
                <tr>
                    <td><code>code</code></td>
                    <td>No</td>
                    <td>Codigo unico del sponsor</td>
                </tr>
                <tr>
                    <td><code>tagline</code></td>
                    <td>No</td>
                    <td>Descripcion corta (eslogan)</td>
                </tr>
                <tr>
                    <td><code>website</code></td>
                    <td>No</td>
                    <td>URL del sitio web</td>
                </tr>
                <tr>
                    <td><code>contacts</code></td>
                    <td>No</td>
                    <td>Contactos del sponsor. Formato: <code>nombre|email|telefono</code>. Para varios contactos, separar con <code>;</code>. Ejemplo: <code>Juan Perez|juan@empresa.com|+34666111222;Maria Garcia|maria@empresa.com</code></td>
                </tr>
                <tr>
                    <td><code>contact_name</code></td>
                    <td>No</td>
                    <td>(Alternativa simple) Nombre del contacto principal</td>
                </tr>
                <tr>
                    <td><code>contact_email</code></td>
                    <td>No</td>
                    <td>(Alternativa simple) Email del contacto principal</td>
                </tr>
                <tr>
                    <td><code>contact_phone</code></td>
                    <td>No</td>
                    <td>(Alternativa simple) Telefono del contacto principal</td>
                </tr>
                <tr>
                    <td><code>active</code></td>
                    <td>No</td>
                    <td>1 = activo, 0 = inactivo (por defecto: 1)</td>
                </tr>
            </tbody>
        </table>
        <p class="mt-3"><strong>Ejemplo basico:</strong></p>
        <pre class="bg-light p-3"><code>name;description;tagline;website;contact_email
"Empresa ABC";"Empresa lider en tecnologia";"Innovacion tecnologica";"https://abc.com";"contacto@abc.com"
"Startup XYZ";"Startup de inteligencia artificial";"El futuro es ahora";"https://xyz.io";"info@xyz.io"</code></pre>
        <p class="mt-3"><strong>Ejemplo con multiples contactos:</strong></p>
        <pre class="bg-light p-3"><code>name;description;tagline;website;contacts
"Empresa ABC";"Empresa lider en tecnologia";"Innovacion tecnologica";"https://abc.com";"Juan Perez|juan@abc.com|+34666111222;Maria Garcia|maria@abc.com"
"Startup XYZ";"Startup de inteligencia artificial";"El futuro es ahora";"https://xyz.io";"Pedro Lopez|pedro@xyz.io"</code></pre>
    </div>
</div>
