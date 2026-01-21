<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#215A6B">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Match Scanner - <?= htmlspecialchars($event['name']) ?></title>
    <link rel="manifest" href="/eventos/<?= htmlspecialchars($event['slug']) ?>/match-manifest.json">
    <link rel="apple-touch-icon" href="/assets/images/scanner-icon-192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #1A1A1A 0%, #2D2D2D 100%);
            color: white;
            min-height: 100vh;
            min-height: 100dvh;
            overflow-x: hidden;
        }

        .app-container {
            max-width: 500px;
            margin: 0 auto;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .app-header {
            background: rgba(33, 90, 107, 0.95);
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .app-header h1 {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .app-header .event-name {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .header-actions button {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        /* Main Content */
        .app-content {
            flex: 1;
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }

        /* States */
        .state-container {
            display: none;
            flex-direction: column;
            height: 100%;
        }

        .state-container.active {
            display: flex;
        }

        /* Scanner Section */
        .scanner-section {
            background: #333;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .scanner-header {
            background: rgba(33, 90, 107, 0.5);
            padding: 1rem;
            text-align: center;
        }

        .scanner-header h2 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .scanner-header p {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        #qr-reader {
            width: 100%;
            aspect-ratio: 1;
            background: #222;
        }

        #qr-reader video {
            object-fit: cover !important;
        }

        /* Identified Sponsor Card */
        .sponsor-card {
            background: linear-gradient(135deg, #215A6B 0%, #2E7D8A 100%);
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sponsor-card .logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .sponsor-card .logo img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .sponsor-card .logo i {
            font-size: 1.5rem;
            color: #215A6B;
        }

        .sponsor-card .info h3 {
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }

        .sponsor-card .tier {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            background: rgba(255,255,255,0.2);
        }

        .sponsor-card .tier.platinum { background: linear-gradient(135deg, #E5E4E2, #A8A9AD); color: #333; }
        .sponsor-card .tier.gold { background: linear-gradient(135deg, #FFD700, #FFA500); color: #333; }
        .sponsor-card .tier.silver { background: linear-gradient(135deg, #C0C0C0, #A0A0A0); color: #333; }
        .sponsor-card .tier.bronze { background: linear-gradient(135deg, #CD7F32, #8B4513); color: #fff; }

        .sponsor-card .change-btn {
            margin-left: auto;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
        }

        /* Company Match Card */
        .match-card {
            background: #333;
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .match-card .match-icon {
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 0.5rem;
        }

        .match-card h3 {
            margin-bottom: 0.5rem;
        }

        .match-card .company-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #4CAF50;
        }

        /* Slot Selection */
        .slots-section {
            flex: 1;
            overflow-y: auto;
        }

        .slots-section h3 {
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .block-group {
            background: #333;
            border-radius: 12px;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .block-header {
            background: rgba(33, 90, 107, 0.3);
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .block-header .date {
            opacity: 0.8;
            font-weight: 400;
        }

        .slots-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            padding: 0.75rem;
        }

        .slot-btn {
            background: rgba(33, 90, 107, 0.5);
            border: 2px solid transparent;
            color: white;
            padding: 0.6rem 0.4rem;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
            font-size: 0.85rem;
        }

        .slot-btn:hover {
            background: rgba(33, 90, 107, 0.8);
            border-color: #215A6B;
        }

        .slot-btn:active {
            transform: scale(0.95);
        }

        .slot-btn .time {
            font-weight: 600;
            display: block;
        }

        .slot-btn .room {
            font-size: 0.7rem;
            opacity: 0.8;
        }

        /* Confirmation */
        .confirmation-card {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .confirmation-card .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .confirmation-card h2 {
            margin-bottom: 0.5rem;
        }

        .meeting-details {
            background: rgba(0,0,0,0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            text-align: left;
        }

        .meeting-details .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .meeting-details .detail-row:last-child {
            border-bottom: none;
        }

        .meeting-details .label {
            opacity: 0.8;
        }

        .meeting-details .value {
            font-weight: 600;
        }

        /* Buttons */
        .btn {
            display: block;
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #215A6B 0%, #2E7D8A 100%);
            color: white;
        }

        .btn-secondary {
            background: #444;
            color: white;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Meetings List */
        .meetings-list {
            margin-top: 1rem;
        }

        .meeting-item {
            background: #333;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .meeting-item .time-badge {
            background: rgba(33, 90, 107, 0.5);
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            text-align: center;
            min-width: 70px;
        }

        .meeting-item .time-badge .time {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .meeting-item .time-badge .room {
            font-size: 0.7rem;
            opacity: 0.8;
        }

        .meeting-item .details .company {
            font-weight: 600;
        }

        .meeting-item .details .meta {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        /* Loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255,255,255,0.2);
            border-top-color: #215A6B;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            z-index: 1001;
            display: none;
            max-width: 90%;
            text-align: center;
        }

        .toast.error {
            background: #e53935;
        }

        .toast.success {
            background: #4CAF50;
        }

        .toast.active {
            display: block;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* No slots message */
        .no-slots {
            text-align: center;
            padding: 2rem;
            opacity: 0.7;
        }

        .no-slots i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header class="app-header">
            <div>
                <h1><i class="fas fa-qrcode"></i> Match Scanner</h1>
                <div class="event-name"><?= htmlspecialchars($event['name']) ?></div>
            </div>
            <div class="header-actions">
                <button onclick="showMeetings()" title="Mis Reuniones"><i class="fas fa-calendar-alt"></i></button>
            </div>
        </header>

        <main class="app-content">
            <!-- State 1: Identify Sponsor -->
            <div id="state-identify" class="state-container active">
                <div class="scanner-section">
                    <div class="scanner-header">
                        <h2><i class="fas fa-user-check"></i> Identificate</h2>
                        <p>Escanea tu codigo QR de SaaS para comenzar</p>
                    </div>
                    <div id="qr-reader-identify"></div>
                </div>
                <p style="text-align: center; opacity: 0.7; font-size: 0.9rem;">
                    Coloca tu badge frente a la camara
                </p>
            </div>

            <!-- State 2: Scan Company -->
            <div id="state-scan" class="state-container">
                <div class="sponsor-card">
                    <div class="logo">
                        <img id="sponsor-logo" src="" alt="" style="display: none;">
                        <i id="sponsor-icon" class="fas fa-building"></i>
                    </div>
                    <div class="info">
                        <h3 id="sponsor-name">-</h3>
                        <span id="sponsor-tier" class="tier">-</span>
                    </div>
                    <button class="change-btn" onclick="resetIdentification()" title="Cambiar">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>

                <div class="scanner-section">
                    <div class="scanner-header">
                        <h2><i class="fas fa-handshake"></i> Escanea la Empresa</h2>
                        <p>Escanea el QR de la empresa para hacer match</p>
                    </div>
                    <div id="qr-reader-company"></div>
                </div>
            </div>

            <!-- State 3: Select Slot -->
            <div id="state-slots" class="state-container">
                <div class="match-card">
                    <div class="match-icon"><i class="fas fa-check-circle"></i></div>
                    <h3>Match con</h3>
                    <div id="match-company-name" class="company-name">-</div>
                </div>

                <div class="slots-section">
                    <h3><i class="fas fa-clock"></i> Selecciona un horario</h3>
                    <div id="slots-container"></div>
                </div>

                <button class="btn btn-secondary" onclick="cancelMatch()" style="margin-top: 1rem;">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>

            <!-- State 4: Confirmation -->
            <div id="state-confirmation" class="state-container">
                <div class="confirmation-card">
                    <div class="icon"><i class="fas fa-calendar-check"></i></div>
                    <h2>Reunion Agendada!</h2>
                    <p>Tu reunion ha sido programada</p>

                    <div class="meeting-details" id="confirmation-details"></div>
                </div>

                <button class="btn btn-primary" onclick="newMatch()">
                    <i class="fas fa-plus"></i> Nuevo Match
                </button>

                <button class="btn btn-secondary" onclick="showMeetings()" style="margin-top: 0.5rem;">
                    <i class="fas fa-calendar-alt"></i> Ver Mis Reuniones
                </button>
            </div>

            <!-- State 5: My Meetings -->
            <div id="state-meetings" class="state-container">
                <h2 style="margin-bottom: 1rem;"><i class="fas fa-calendar-alt"></i> Mis Reuniones</h2>
                <div id="meetings-container" class="meetings-list"></div>

                <button class="btn btn-primary" onclick="backToScanner()" style="margin-top: 1rem;">
                    <i class="fas fa-qrcode"></i> Volver al Scanner
                </button>
            </div>
        </main>
    </div>

    <!-- Loading Overlay -->
    <div id="loading" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <script>
        // App State
        const APP = {
            eventSlug: '<?= htmlspecialchars($event['slug']) ?>',
            csrfToken: '<?= $csrf_token ?>',
            sponsor: null,
            currentMatch: null,
            identifyScanner: null,
            companyScanner: null,
        };

        // Storage keys
        const STORAGE_KEY = 'tlos_match_sponsor_<?= $event['id'] ?>';

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Check if sponsor is already identified
            const savedSponsor = localStorage.getItem(STORAGE_KEY);
            if (savedSponsor) {
                try {
                    APP.sponsor = JSON.parse(savedSponsor);
                    showSponsorIdentified();
                } catch (e) {
                    localStorage.removeItem(STORAGE_KEY);
                    initIdentifyScanner();
                }
            } else {
                initIdentifyScanner();
            }
        });

        // Initialize Identify Scanner
        function initIdentifyScanner() {
            if (APP.identifyScanner) {
                APP.identifyScanner.clear();
            }

            APP.identifyScanner = new Html5Qrcode("qr-reader-identify");
            APP.identifyScanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onIdentifySuccess,
                () => {}
            ).catch(err => {
                showToast('No se pudo acceder a la camara', 'error');
                console.error(err);
            });
        }

        // Initialize Company Scanner
        function initCompanyScanner() {
            if (APP.companyScanner) {
                APP.companyScanner.clear();
            }

            APP.companyScanner = new Html5Qrcode("qr-reader-company");
            APP.companyScanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onCompanyScanSuccess,
                () => {}
            ).catch(err => {
                showToast('No se pudo acceder a la camara', 'error');
                console.error(err);
            });
        }

        // On Identify QR Scanned
        async function onIdentifySuccess(code) {
            if (APP.identifyScanner) {
                APP.identifyScanner.pause();
            }

            showLoading(true);

            try {
                const response = await fetch(`/eventos/${APP.eventSlug}/match/identify`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `code=${encodeURIComponent(code)}&_csrf_token=${APP.csrfToken}`
                });

                const data = await response.json();

                if (data.success) {
                    APP.sponsor = data.sponsor;
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(APP.sponsor));
                    showSponsorIdentified();
                    showToast('Identificado correctamente', 'success');
                } else {
                    showToast(data.error || 'Error al identificar', 'error');
                    if (APP.identifyScanner) {
                        APP.identifyScanner.resume();
                    }
                }
            } catch (err) {
                showToast('Error de conexion', 'error');
                if (APP.identifyScanner) {
                    APP.identifyScanner.resume();
                }
            }

            showLoading(false);
        }

        // Show Sponsor Identified State
        function showSponsorIdentified() {
            if (APP.identifyScanner) {
                APP.identifyScanner.stop().catch(() => {});
            }

            // Update UI
            document.getElementById('sponsor-name').textContent = APP.sponsor.name;
            document.getElementById('sponsor-tier').textContent = APP.sponsor.tier;
            document.getElementById('sponsor-tier').className = 'tier ' + APP.sponsor.tier;

            if (APP.sponsor.logo_url) {
                document.getElementById('sponsor-logo').src = APP.sponsor.logo_url;
                document.getElementById('sponsor-logo').style.display = 'block';
                document.getElementById('sponsor-icon').style.display = 'none';
            } else {
                document.getElementById('sponsor-logo').style.display = 'none';
                document.getElementById('sponsor-icon').style.display = 'block';
            }

            switchState('scan');
            initCompanyScanner();
        }

        // On Company QR Scanned
        async function onCompanyScanSuccess(code) {
            if (APP.companyScanner) {
                APP.companyScanner.pause();
            }

            showLoading(true);

            try {
                const response = await fetch(`/eventos/${APP.eventSlug}/match/scan-company`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `sponsor_code=${encodeURIComponent(APP.sponsor.code)}&company_code=${encodeURIComponent(code)}&_csrf_token=${APP.csrfToken}`
                });

                const data = await response.json();

                if (data.success) {
                    APP.currentMatch = data.match;
                    showSlotSelection(data);
                } else {
                    showToast(data.error || 'Error al escanear empresa', 'error');
                    if (APP.companyScanner) {
                        APP.companyScanner.resume();
                    }
                }
            } catch (err) {
                showToast('Error de conexion', 'error');
                if (APP.companyScanner) {
                    APP.companyScanner.resume();
                }
            }

            showLoading(false);
        }

        // Show Slot Selection
        function showSlotSelection(data) {
            if (APP.companyScanner) {
                APP.companyScanner.stop().catch(() => {});
            }

            document.getElementById('match-company-name').textContent = data.match.company_name;

            const container = document.getElementById('slots-container');

            if (data.available_slots.length === 0) {
                container.innerHTML = `
                    <div class="no-slots">
                        <i class="fas fa-calendar-times"></i>
                        <p>No hay slots disponibles en este momento</p>
                    </div>
                `;
            } else {
                let html = '';
                data.available_slots.forEach(block => {
                    html += `
                        <div class="block-group">
                            <div class="block-header">
                                ${block.block_name}
                                <span class="date">${formatDate(block.event_date)}</span>
                            </div>
                            <div class="slots-grid">
                    `;
                    block.slots.forEach(slot => {
                        html += `
                            <button class="slot-btn" onclick="selectSlot(${slot.id})">
                                <span class="time">${slot.time}</span>
                                <span class="room">${slot.room}</span>
                            </button>
                        `;
                    });
                    html += `
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            }

            switchState('slots');
        }

        // Select Slot
        async function selectSlot(slotId) {
            showLoading(true);

            try {
                const response = await fetch(`/eventos/${APP.eventSlug}/match/select-slot`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `sponsor_code=${encodeURIComponent(APP.sponsor.code)}&company_id=${APP.currentMatch.company_id}&slot_id=${slotId}&_csrf_token=${APP.csrfToken}`
                });

                const data = await response.json();

                if (data.success) {
                    showConfirmation(data.meeting);
                } else {
                    showToast(data.error || 'Error al seleccionar slot', 'error');
                }
            } catch (err) {
                showToast('Error de conexion', 'error');
            }

            showLoading(false);
        }

        // Show Confirmation
        function showConfirmation(meeting) {
            const details = document.getElementById('confirmation-details');
            details.innerHTML = `
                <div class="detail-row">
                    <span class="label">Empresa</span>
                    <span class="value">${meeting.company_name}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Fecha</span>
                    <span class="value">${formatDate(meeting.date)}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Hora</span>
                    <span class="value">${meeting.time}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Mesa</span>
                    <span class="value">${meeting.room}</span>
                </div>
                ${meeting.location ? `
                <div class="detail-row">
                    <span class="label">Ubicacion</span>
                    <span class="value">${meeting.location}</span>
                </div>
                ` : ''}
            `;

            switchState('confirmation');
        }

        // Show Meetings
        async function showMeetings() {
            if (!APP.sponsor) {
                showToast('Primero debes identificarte', 'error');
                return;
            }

            // Stop any running scanner
            if (APP.companyScanner) {
                APP.companyScanner.stop().catch(() => {});
            }

            showLoading(true);

            try {
                const response = await fetch(`/eventos/${APP.eventSlug}/match/meetings?sponsor_code=${encodeURIComponent(APP.sponsor.code)}`);
                const data = await response.json();

                if (data.success) {
                    const container = document.getElementById('meetings-container');

                    if (data.meetings.length === 0) {
                        container.innerHTML = `
                            <div class="no-slots">
                                <i class="fas fa-calendar"></i>
                                <p>No tienes reuniones programadas</p>
                            </div>
                        `;
                    } else {
                        let html = '';
                        data.meetings.forEach(m => {
                            html += `
                                <div class="meeting-item">
                                    <div class="time-badge">
                                        <div class="time">${m.time}</div>
                                        <div class="room">${m.room}</div>
                                    </div>
                                    <div class="details">
                                        <div class="company">${m.company_name}</div>
                                        <div class="meta">${formatDate(m.date)} - ${m.assigned_by === 'live_matching' ? 'En vivo' : 'Pre-agendada'}</div>
                                    </div>
                                </div>
                            `;
                        });
                        container.innerHTML = html;
                    }

                    switchState('meetings');
                } else {
                    showToast(data.error || 'Error al cargar reuniones', 'error');
                }
            } catch (err) {
                showToast('Error de conexion', 'error');
            }

            showLoading(false);
        }

        // Back to Scanner
        function backToScanner() {
            switchState('scan');
            initCompanyScanner();
        }

        // New Match
        function newMatch() {
            APP.currentMatch = null;
            switchState('scan');
            initCompanyScanner();
        }

        // Cancel Match
        function cancelMatch() {
            APP.currentMatch = null;
            switchState('scan');
            initCompanyScanner();
        }

        // Reset Identification
        function resetIdentification() {
            if (confirm('Deseas cerrar la sesion y cambiar de usuario?')) {
                if (APP.companyScanner) {
                    APP.companyScanner.stop().catch(() => {});
                }
                localStorage.removeItem(STORAGE_KEY);
                APP.sponsor = null;
                APP.currentMatch = null;
                switchState('identify');
                initIdentifyScanner();
            }
        }

        // Switch State
        function switchState(state) {
            document.querySelectorAll('.state-container').forEach(el => {
                el.classList.remove('active');
            });
            document.getElementById('state-' + state).classList.add('active');
        }

        // Show Loading
        function showLoading(show) {
            document.getElementById('loading').classList.toggle('active', show);
        }

        // Show Toast
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type + ' active';
            setTimeout(() => {
                toast.classList.remove('active');
            }, 3000);
        }

        // Format Date
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'short' });
        }
    </script>
</body>
</html>
