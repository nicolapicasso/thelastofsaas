<?php
/**
 * Meeting Badges QR Template
 * TLOS - The Last of SaaS
 *
 * Generates printable QR code badges for sponsors and companies
 */

// Get current event info
$currentEvent = null;
foreach ($events as $evt) {
    if ($evt['id'] == $currentEventId) {
        $currentEvent = $evt;
        break;
    }
}
?>

<div class="page-header no-print">
    <div class="page-header-content">
        <h1>Badges QR para Imprimir</h1>
        <p>Genera e imprime los badges con códigos QR para el evento</p>
    </div>
    <div class="page-header-actions">
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Imprimir</button>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?> no-print"><?= $flash['message'] ?></div>
<?php endif; ?>

<!-- Filters -->
<div class="card filters-card no-print">
    <div class="filters-form">
        <div class="filter-group">
            <label>Evento</label>
            <select onchange="updateFilters()" id="eventSelect" class="form-control">
                <?php foreach ($events as $evt): ?>
                    <option value="<?= $evt['id'] ?>" <?= $currentEventId == $evt['id'] ? 'selected' : '' ?>><?= htmlspecialchars($evt['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Tipo</label>
            <select onchange="updateFilters()" id="typeSelect" class="form-control">
                <option value="all" <?= $currentType === 'all' ? 'selected' : '' ?>>Todos</option>
                <option value="sponsors" <?= $currentType === 'sponsors' ? 'selected' : '' ?>>Solo SaaS</option>
                <option value="companies" <?= $currentType === 'companies' ? 'selected' : '' ?>>Solo Empresas</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Total</label>
            <span class="badge badge-info"><?= count($sponsors) ?> SaaS</span>
            <span class="badge badge-secondary"><?= count($companies) ?> Empresas</span>
        </div>
    </div>
</div>

<?php if (empty($sponsors) && empty($companies)): ?>
    <div class="card">
        <div class="empty-state">
            <i class="fas fa-qrcode"></i>
            <h3>No hay participantes</h3>
            <p>No hay sponsors ni empresas registrados para este evento</p>
        </div>
    </div>
<?php else: ?>

<!-- Print Instructions -->
<div class="card no-print">
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Instrucciones de impresion:</strong>
            <ul style="margin: 0.5rem 0 0 1rem;">
                <li>Usa papel A4 en orientacion vertical</li>
                <li>Configura margenes minimos en tu impresora</li>
                <li>Se imprimiran 6 badges por pagina (2 columnas x 3 filas)</li>
                <li>Recorta por las lineas de guia</li>
            </ul>
        </div>
    </div>
</div>

<!-- Badges Grid -->
<div class="badges-container">
    <?php if (!empty($sponsors)): ?>
        <?php foreach ($sponsors as $sponsor): ?>
            <div class="badge-card">
                <div class="badge-type saas">SaaS</div>
                <?php if (!empty($sponsor['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="" class="badge-logo">
                <?php endif; ?>
                <div class="badge-qr">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($sponsor['code']) ?>" alt="QR <?= htmlspecialchars($sponsor['name']) ?>">
                </div>
                <div class="badge-name"><?= htmlspecialchars($sponsor['name']) ?></div>
                <div class="badge-code"><?= htmlspecialchars(substr($sponsor['code'], 0, 8)) ?>...</div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($companies)): ?>
        <?php foreach ($companies as $company): ?>
            <div class="badge-card">
                <div class="badge-type company">Empresa</div>
                <?php if (!empty($company['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="" class="badge-logo">
                <?php endif; ?>
                <div class="badge-qr">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($company['code']) ?>" alt="QR <?= htmlspecialchars($company['name']) ?>">
                </div>
                <div class="badge-name"><?= htmlspecialchars($company['name']) ?></div>
                <div class="badge-code"><?= htmlspecialchars(substr($company['code'], 0, 8)) ?>...</div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php endif; ?>

<style>
/* Screen styles */
.badges-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    padding: 1rem;
}

.badge-card {
    background: white;
    border: 2px dashed #ddd;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    position: relative;
    min-height: 300px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.badge-type {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-type.saas {
    background: linear-gradient(135deg, #215A6B 0%, #2E7D8A 100%);
    color: white;
}

.badge-type.company {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.badge-logo {
    max-width: 80px;
    max-height: 40px;
    object-fit: contain;
    margin-bottom: 0.5rem;
}

.badge-qr {
    margin: 1rem 0;
}

.badge-qr img {
    width: 150px;
    height: 150px;
    border: 3px solid #215A6B;
    border-radius: 8px;
    padding: 5px;
    background: white;
}

.badge-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #333;
    margin-top: 0.5rem;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.badge-code {
    font-size: 0.75rem;
    color: #999;
    font-family: monospace;
    margin-top: 0.25rem;
}

/* Print styles */
@media print {
    .no-print,
    .page-header,
    .filters-card,
    .sidebar,
    .navbar,
    header,
    footer {
        display: none !important;
    }

    body {
        background: white !important;
        margin: 0;
        padding: 0;
    }

    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }

    .badges-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0;
        padding: 0;
        margin: 0;
    }

    .badge-card {
        border: 1px dashed #ccc;
        border-radius: 0;
        padding: 15mm 10mm;
        min-height: auto;
        height: 90mm;
        page-break-inside: avoid;
        box-sizing: border-box;
    }

    .badge-qr img {
        width: 35mm;
        height: 35mm;
    }

    .badge-name {
        font-size: 14pt;
    }

    .badge-code {
        font-size: 8pt;
    }

    .badge-type {
        font-size: 8pt;
        padding: 2px 8px;
    }

    .badge-logo {
        max-width: 20mm;
        max-height: 10mm;
    }
}

@page {
    size: A4;
    margin: 5mm;
}
</style>

<script>
function updateFilters() {
    const eventId = document.getElementById('eventSelect').value;
    const type = document.getElementById('typeSelect').value;
    window.location.href = '/admin/meetings/badges?event_id=' + eventId + '&type=' + type;
}
</script>
