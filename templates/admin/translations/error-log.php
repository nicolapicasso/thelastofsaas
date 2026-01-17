<?php
/**
 * Translation Error Log Template
 * Omniwallet CMS Admin
 */
?>

<div class="page-header">
    <h1>Log de Errores de Traducción</h1>
    <div class="page-actions">
        <a href="/admin/translations" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver a Traducciones
        </a>
        <?php if (!empty($errors)): ?>
            <button type="button" class="btn btn-danger" onclick="clearLog()">
                <i class="fas fa-trash"></i> Limpiar Log
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($errors)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>Sin errores</h3>
                <p>No hay errores de traducción registrados.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Mensaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($errors as $index => $error): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <code style="font-size: 12px; display: block; white-space: pre-wrap; word-break: break-all;">
                                        <?= htmlspecialchars($error) ?>
                                    </code>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="text-muted mt-3">
                <i class="fas fa-info-circle"></i>
                Mostrando los últimos <?= count($errors) ?> errores (ordenados del más reciente al más antiguo).
            </p>
        <?php endif; ?>
    </div>
</div>

<style>
.empty-state {
    text-align: center;
    padding: 60px 20px;
}
.empty-state i {
    font-size: 64px;
    color: var(--success-color, #28a745);
    margin-bottom: 20px;
}
.empty-state h3 {
    margin-bottom: 10px;
    color: #333;
}
.empty-state p {
    color: #666;
}
</style>

<script>
function clearLog() {
    if (!confirm('¿Estás seguro de que quieres limpiar el log de errores?')) {
        return;
    }

    fetch('/admin/translations/clear-log', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al limpiar el log');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al limpiar el log');
    });
}
</script>
