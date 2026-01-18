<?php
/**
 * Ticket Scanner Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Scanner Check-in</h1>
        <p>Escanea codigos QR o introduce codigos de tickets manualmente</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/tickets" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <!-- Event Selector -->
        <div class="card">
            <div class="card-header">
                <h3>Seleccionar Evento</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="event_id">Evento</label>
                    <select id="event_id" class="form-control" onchange="changeEvent(this.value)">
                        <?php if (empty($events)): ?>
                            <option value="">No hay eventos disponibles</option>
                        <?php else: ?>
                            <?php foreach ($events as $event): ?>
                                <option value="<?= $event['id'] ?>" <?= ($currentEventId ?? 0) == $event['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($event['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Manual Code Entry -->
        <div class="card">
            <div class="card-header">
                <h3>Introducir Codigo</h3>
            </div>
            <div class="card-body">
                <form id="check-in-form" onsubmit="return validateTicket(event)">
                    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
                    <div class="form-group">
                        <label for="ticket_code">Codigo del Ticket</label>
                        <input type="text" id="ticket_code" name="code" class="form-control form-control-lg"
                               placeholder="Introduce o escanea el codigo" autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-check-circle"></i> Validar Check-in
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Result Display -->
        <div class="card">
            <div class="card-header">
                <h3>Resultado</h3>
            </div>
            <div class="card-body">
                <div id="scan-result" class="scan-result">
                    <div class="scan-result-placeholder">
                        <i class="fas fa-qrcode fa-4x text-muted"></i>
                        <p class="text-muted mt-3">Esperando codigo de ticket...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Check-ins -->
        <div class="card">
            <div class="card-header">
                <h3>Check-ins Recientes</h3>
            </div>
            <div class="card-body">
                <div id="recent-checkins">
                    <p class="text-muted text-center">No hay check-ins recientes</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.scan-result {
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.scan-result-placeholder {
    opacity: 0.7;
}
.scan-result-success {
    background: #d4edda;
    border: 2px solid #28a745;
    border-radius: 8px;
    padding: 30px;
    width: 100%;
}
.scan-result-error {
    background: #f8d7da;
    border: 2px solid #dc3545;
    border-radius: 8px;
    padding: 30px;
    width: 100%;
}
.scan-result-warning {
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 8px;
    padding: 30px;
    width: 100%;
}
.scan-result h4 {
    margin-bottom: 10px;
}
.recent-checkin-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.recent-checkin-item:last-child {
    border-bottom: none;
}
</style>

<script>
let recentCheckins = [];

function changeEvent(eventId) {
    window.location.href = '/admin/tickets/scanner?event_id=' + eventId;
}

function validateTicket(e) {
    e.preventDefault();

    const code = document.getElementById('ticket_code').value.trim();
    const eventId = document.getElementById('event_id').value;
    const csrfToken = document.querySelector('input[name="_csrf_token"]').value;

    if (!code) {
        showResult('error', 'Introduce un codigo de ticket');
        return false;
    }

    if (!eventId) {
        showResult('error', 'Selecciona un evento');
        return false;
    }

    // Show loading
    document.getElementById('scan-result').innerHTML = `
        <div class="scan-result-placeholder">
            <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
            <p class="text-muted mt-3">Validando...</p>
        </div>
    `;

    // Send validation request
    fetch('/admin/tickets/validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({
            code: code,
            event_id: eventId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResult('success', data.message, data.ticket);
            addToRecentCheckins(data.ticket);
        } else {
            showResult(data.already_checked_in ? 'warning' : 'error', data.message, data.ticket);
        }

        // Clear and focus input
        document.getElementById('ticket_code').value = '';
        document.getElementById('ticket_code').focus();
    })
    .catch(error => {
        showResult('error', 'Error de conexion');
        document.getElementById('ticket_code').focus();
    });

    return false;
}

function showResult(type, message, ticket) {
    const resultDiv = document.getElementById('scan-result');
    const icon = type === 'success' ? 'check-circle' : (type === 'warning' ? 'exclamation-triangle' : 'times-circle');
    const colorClass = type === 'success' ? 'text-success' : (type === 'warning' ? 'text-warning' : 'text-danger');

    let html = `
        <div class="scan-result-${type}">
            <i class="fas fa-${icon} fa-3x ${colorClass}"></i>
            <h4 class="mt-3">${message}</h4>
    `;

    if (ticket) {
        html += `
            <p class="mb-1"><strong>${ticket.attendee_name || 'Sin nombre'}</strong></p>
            <p class="text-muted mb-0">${ticket.attendee_email || ''}</p>
            <p class="mt-2"><span class="badge badge-${ticket.type === 'vip' ? 'warning' : 'primary'}">${ticket.type || 'general'}</span></p>
        `;
    }

    html += '</div>';
    resultDiv.innerHTML = html;
}

function addToRecentCheckins(ticket) {
    if (!ticket) return;

    recentCheckins.unshift({
        name: ticket.attendee_name || 'Sin nombre',
        email: ticket.attendee_email || '',
        time: new Date().toLocaleTimeString()
    });

    // Keep only last 5
    recentCheckins = recentCheckins.slice(0, 5);

    updateRecentCheckins();
}

function updateRecentCheckins() {
    const container = document.getElementById('recent-checkins');

    if (recentCheckins.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">No hay check-ins recientes</p>';
        return;
    }

    let html = '';
    recentCheckins.forEach(checkin => {
        html += `
            <div class="recent-checkin-item">
                <div>
                    <strong>${checkin.name}</strong><br>
                    <small class="text-muted">${checkin.email}</small>
                </div>
                <small class="text-muted">${checkin.time}</small>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Auto-focus on input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('ticket_code').focus();
});
</script>
