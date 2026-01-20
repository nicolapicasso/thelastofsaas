<?php
/**
 * Ticket Scanner Template
 * TLOS - The Last of SaaS
 * PWA-ready for mobile installation
 */

// Add PWA manifest to extra head content
$extraCss = $extraCss ?? '';
$extraCss .= '
<link rel="manifest" href="/scanner-manifest.json">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="TLOS Scanner">
<link rel="apple-touch-icon" href="/assets/images/scanner-icon-192.png">
';
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Scanner Check-in</h1>
        <p>Escanea códigos QR con la cámara o introduce códigos manualmente</p>
    </div>
    <div class="page-header-actions">
        <button type="button" id="btn-install-pwa" class="btn btn-success" style="display: none;" onclick="installPWA()">
            <i class="fas fa-download"></i> Instalar App
        </button>
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

        <!-- Camera Scanner -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-camera"></i> Escanear con Cámara</h3>
            </div>
            <div class="card-body">
                <div id="qr-reader-container">
                    <div id="qr-reader"></div>
                </div>
                <div class="scanner-controls" style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" id="btn-start-camera" class="btn btn-success" onclick="startCamera()">
                        <i class="fas fa-play"></i> Iniciar Cámara
                    </button>
                    <button type="button" id="btn-stop-camera" class="btn btn-danger" onclick="stopCamera()" style="display: none;">
                        <i class="fas fa-stop"></i> Detener Cámara
                    </button>
                    <select id="camera-select" class="form-control" style="width: auto; display: none;" onchange="switchCamera(this.value)">
                        <option value="">Seleccionar cámara...</option>
                    </select>
                </div>
                <p class="text-muted mt-2" style="font-size: 12px;">
                    <i class="fas fa-info-circle"></i> Funciona con cámara de portátil o móvil.
                    En móvil se seleccionará automáticamente la cámara trasera.
                </p>
            </div>
        </div>

        <!-- Manual Code Entry -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-keyboard"></i> Introducir Código Manual</h3>
            </div>
            <div class="card-body">
                <form id="check-in-form" onsubmit="return validateTicket(event)">
                    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
                    <div class="form-group">
                        <label for="ticket_code">Código del Ticket</label>
                        <input type="text" id="ticket_code" name="code" class="form-control form-control-lg"
                               placeholder="Introduce el código del ticket">
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
                        <p class="text-muted mt-3">Esperando código de ticket...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Check-ins -->
        <div class="card">
            <div class="card-header">
                <h3>Check-ins Recientes <span id="checkin-count" class="badge badge-primary">0</span></h3>
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

/* Camera Scanner Styles */
#qr-reader-container {
    background: #000;
    border-radius: 8px;
    overflow: hidden;
    min-height: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
}
#qr-reader {
    width: 100%;
}
#qr-reader video {
    width: 100% !important;
    border-radius: 8px;
}
#qr-reader__scan_region {
    min-height: 250px;
}
#qr-reader__dashboard {
    padding: 10px !important;
}
#qr-reader__dashboard_section_csr button {
    background: var(--color-primary) !important;
    border: none !important;
    padding: 8px 16px !important;
    border-radius: 4px !important;
}
.camera-placeholder {
    color: #666;
    text-align: center;
    padding: 40px 20px;
}
.camera-placeholder i {
    font-size: 48px;
    margin-bottom: 15px;
    color: #444;
}

/* Sound feedback */
.scan-success-sound {
    display: none;
}
</style>

<!-- HTML5 QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let recentCheckins = [];
let html5QrCode = null;
let isScanning = false;
let lastScannedCode = '';
let lastScanTime = 0;

function changeEvent(eventId) {
    window.location.href = '/admin/tickets/scanner?event_id=' + eventId;
}

// Camera Functions
async function startCamera() {
    try {
        // Get available cameras
        const devices = await Html5Qrcode.getCameras();
        if (devices && devices.length > 0) {
            // Populate camera select
            const select = document.getElementById('camera-select');
            select.innerHTML = '<option value="">Seleccionar cámara...</option>';
            devices.forEach((device, index) => {
                const option = document.createElement('option');
                option.value = device.id;
                // Prefer back camera on mobile
                const isBackCamera = device.label.toLowerCase().includes('back') ||
                                     device.label.toLowerCase().includes('trasera') ||
                                     device.label.toLowerCase().includes('rear');
                option.textContent = device.label || `Cámara ${index + 1}`;
                if (isBackCamera) option.textContent += ' (Recomendada)';
                select.appendChild(option);
            });

            // Auto-select back camera if available, otherwise first camera
            let selectedCamera = devices[0].id;
            for (const device of devices) {
                const label = device.label.toLowerCase();
                if (label.includes('back') || label.includes('trasera') || label.includes('rear')) {
                    selectedCamera = device.id;
                    break;
                }
            }

            select.value = selectedCamera;
            select.style.display = 'inline-block';

            // Start scanning
            await startScanning(selectedCamera);
        } else {
            showResult('error', 'No se encontraron cámaras disponibles');
        }
    } catch (err) {
        console.error('Error accessing cameras:', err);
        showResult('error', 'Error al acceder a la cámara: ' + err.message);
    }
}

async function startScanning(cameraId) {
    if (html5QrCode && isScanning) {
        await html5QrCode.stop();
    }

    html5QrCode = new Html5Qrcode("qr-reader");

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    try {
        await html5QrCode.start(
            cameraId,
            config,
            onScanSuccess,
            onScanFailure
        );
        isScanning = true;
        document.getElementById('btn-start-camera').style.display = 'none';
        document.getElementById('btn-stop-camera').style.display = 'inline-block';
    } catch (err) {
        console.error('Error starting camera:', err);
        showResult('error', 'Error al iniciar la cámara. Asegúrate de dar permisos.');
    }
}

async function stopCamera() {
    if (html5QrCode && isScanning) {
        try {
            await html5QrCode.stop();
            isScanning = false;
        } catch (err) {
            console.error('Error stopping camera:', err);
        }
    }
    document.getElementById('qr-reader').innerHTML = `
        <div class="camera-placeholder">
            <i class="fas fa-video-slash"></i>
            <p>Cámara detenida</p>
        </div>
    `;
    document.getElementById('btn-start-camera').style.display = 'inline-block';
    document.getElementById('btn-stop-camera').style.display = 'none';
    document.getElementById('camera-select').style.display = 'none';
}

async function switchCamera(cameraId) {
    if (cameraId && isScanning) {
        await startScanning(cameraId);
    }
}

function onScanSuccess(decodedText, decodedResult) {
    // Prevent duplicate scans (same code within 3 seconds)
    const now = Date.now();
    if (decodedText === lastScannedCode && (now - lastScanTime) < 3000) {
        return;
    }

    lastScannedCode = decodedText;
    lastScanTime = now;

    // Play success sound
    playBeep();

    // Process the scanned code
    processCode(decodedText);
}

function onScanFailure(error) {
    // Ignore scan failures (normal when no QR in view)
}

function playBeep() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        gainNode.gain.value = 0.3;

        oscillator.start();
        setTimeout(() => {
            oscillator.stop();
            audioContext.close();
        }, 150);
    } catch (e) {
        // Audio not supported
    }
}

function processCode(code) {
    const eventId = document.getElementById('event_id').value;
    const csrfToken = document.querySelector('input[name="_csrf_token"]').value;

    if (!eventId) {
        showResult('error', 'Selecciona un evento primero');
        return;
    }

    // Show loading
    document.getElementById('scan-result').innerHTML = `
        <div class="scan-result-placeholder">
            <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
            <p class="text-muted mt-3">Validando...</p>
        </div>
    `;

    // Send validation and check-in request
    fetch('/admin/tickets/validate', {
        method: 'POST',
        credentials: 'include', // Important: include cookies for session
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            code: code,
            event_id: eventId
        })
    })
    .then(response => {
        // Check if response is OK
        if (!response.ok) {
            // Try to get error message from response
            return response.text().then(text => {
                try {
                    const data = JSON.parse(text);
                    throw new Error(data.error || data.message || `Error ${response.status}`);
                } catch (e) {
                    if (response.status === 401 || response.status === 403) {
                        throw new Error('Sesión expirada. Por favor, vuelve a iniciar sesión.');
                    }
                    throw new Error(`Error del servidor: ${response.status}`);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showResult('success', data.message, data.ticket);
            addToRecentCheckins(data.ticket);
        } else {
            showResult(data.already_checked_in ? 'warning' : 'error', data.message || data.error, data.ticket);
        }
    })
    .catch(error => {
        console.error('Scan error:', error);
        showResult('error', error.message || 'Error de conexión. Comprueba tu conexión a internet.');
    });
}

function validateTicket(e) {
    e.preventDefault();

    const code = document.getElementById('ticket_code').value.trim();

    if (!code) {
        showResult('error', 'Introduce un código de ticket');
        return false;
    }

    processCode(code);
    document.getElementById('ticket_code').value = '';

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
            <p class="mb-1"><strong>${ticket.name || ticket.attendee_name || 'Sin nombre'}</strong></p>
            <p class="text-muted mb-0">${ticket.email || ticket.attendee_email || ''}</p>
            ${ticket.company ? `<p class="text-muted mb-0"><small>${ticket.company}</small></p>` : ''}
        `;
    }

    html += '</div>';
    resultDiv.innerHTML = html;
}

function addToRecentCheckins(ticket) {
    if (!ticket) return;

    recentCheckins.unshift({
        name: ticket.name || ticket.attendee_name || 'Sin nombre',
        email: ticket.email || ticket.attendee_email || '',
        time: new Date().toLocaleTimeString()
    });

    // Keep only last 10
    recentCheckins = recentCheckins.slice(0, 10);

    updateRecentCheckins();
}

function updateRecentCheckins() {
    const container = document.getElementById('recent-checkins');
    const countBadge = document.getElementById('checkin-count');

    if (countBadge) {
        countBadge.textContent = recentCheckins.length;
    }

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

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Show placeholder in camera area
    document.getElementById('qr-reader').innerHTML = `
        <div class="camera-placeholder">
            <i class="fas fa-camera"></i>
            <p>Pulsa "Iniciar Cámara" para escanear</p>
        </div>
    `;
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (html5QrCode && isScanning) {
        html5QrCode.stop();
    }
});

// PWA Install Support
let deferredPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent the mini-infobar from appearing on mobile
    e.preventDefault();
    // Store the event for later use
    deferredPrompt = e;
    // Show install button
    document.getElementById('btn-install-pwa').style.display = 'inline-block';
});

async function installPWA() {
    if (!deferredPrompt) {
        alert('La app ya está instalada o tu navegador no soporta instalación de PWA');
        return;
    }

    // Show the install prompt
    deferredPrompt.prompt();

    // Wait for user choice
    const { outcome } = await deferredPrompt.userChoice;

    if (outcome === 'accepted') {
        console.log('PWA installed');
        document.getElementById('btn-install-pwa').style.display = 'none';
    }

    deferredPrompt = null;
}

// Check if already installed
window.addEventListener('appinstalled', () => {
    document.getElementById('btn-install-pwa').style.display = 'none';
    deferredPrompt = null;
});

// Register service worker for PWA
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/scanner-sw.js').catch(err => {
        console.log('Service worker registration failed:', err);
    });
}
</script>
