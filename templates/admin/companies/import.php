<?php
/**
 * Company Import Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Importar Empresas</h1>
        <p>Importar empresas desde un archivo CSV</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/companies" class="btn btn-outline">
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
        <form method="POST" action="/admin/companies/import" enctype="multipart/form-data">
            <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

            <div class="form-group">
                <label for="csv_file">Archivo CSV *</label>
                <input type="file" id="csv_file" name="csv_file" class="form-control" accept=".csv" required>
                <small class="form-text text-muted">Selecciona un archivo CSV con los datos de las empresas.</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Importar
                </button>
                <a href="/admin/companies" class="btn btn-outline">Cancelar</a>
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
                    <td>Nombre de la empresa</td>
                </tr>
                <tr>
                    <td><code>description</code></td>
                    <td>No</td>
                    <td>Descripcion de la empresa</td>
                </tr>
                <tr>
                    <td><code>code</code></td>
                    <td>No</td>
                    <td>Codigo unico de la empresa</td>
                </tr>
                <tr>
                    <td><code>sector</code></td>
                    <td>No</td>
                    <td>Sector/industria</td>
                </tr>
                <tr>
                    <td><code>employees</code></td>
                    <td>No</td>
                    <td>Numero de empleados (1-10, 11-50, 51-200, 201-500, 500+)</td>
                </tr>
                <tr>
                    <td><code>website</code></td>
                    <td>No</td>
                    <td>URL del sitio web</td>
                </tr>
                <tr>
                    <td><code>logo_url</code></td>
                    <td>No</td>
                    <td>URL del logo. Si es una URL externa (http/https), se descargará automáticamente y se guardará en el servidor. Formatos soportados: PNG, JPG, GIF, SVG, WebP, ICO</td>
                </tr>
                <tr>
                    <td><code>contacts</code></td>
                    <td>No</td>
                    <td>Contactos de la empresa. Formato: <code>nombre|cargo|email|telefono</code>. Para varios contactos, separar con <code>;</code>. Ejemplo: <code>Juan Perez|CEO|juan@empresa.com|+34666111222;Maria Garcia|CTO|maria@empresa.com</code></td>
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
                <tr>
                    <td><code>saas</code></td>
                    <td>No</td>
                    <td>SaaS que utiliza la empresa, separados por comas. Se vincularan automaticamente con los sponsors existentes. Ejemplo: <code>Slack, HubSpot, Notion</code></td>
                </tr>
            </tbody>
        </table>
        <p class="mt-3"><strong>Nota:</strong> También se aceptan las columnas <code>saas_usage</code>, <code>saas que utiliza</code> o <code>herramientas</code> como alternativas a <code>saas</code>.</p>
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> <strong>Descarga automática de logos:</strong> Si incluyes una URL externa en la columna <code>logo_url</code>, el sistema descargará automáticamente la imagen y la guardará en el servidor. Al finalizar la importación se mostrará el número de logos descargados.
        </div>
        <p class="mt-3"><strong>Ejemplo básico:</strong></p>
        <pre class="bg-light p-3"><code>name;description;sector;employees;logo_url;contact_email;saas
"Tech Solutions";"Empresa de tecnologia";"Tecnologia";"51-200";"https://techsol.com/logo.png";"info@techsol.com";"Slack, HubSpot, Salesforce"
"Innovate Corp";"Servicios digitales";"Servicios";"11-50";"https://innovate.io/brand/logo.svg";"contacto@innovate.io";"Notion, Figma"</code></pre>
        <p class="mt-3"><strong>Ejemplo con múltiples contactos:</strong></p>
        <pre class="bg-light p-3"><code>name;description;sector;logo_url;contacts;saas
"Tech Solutions";"Empresa de tecnologia";"Tecnologia";"https://techsol.com/logo.png";"Juan Perez|CEO|juan@tech.com|+34666111222;Maria Garcia|CTO|maria@tech.com";"Slack, HubSpot"
"Innovate Corp";"Servicios digitales";"Servicios";"";"Pedro Lopez|Director|pedro@innovate.io";"Notion, Figma"</code></pre>
    </div>
</div>
