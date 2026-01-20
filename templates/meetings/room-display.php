<?php
/**
 * Visual Meeting Room Display
 * Shows tables with participants and manages rounds with timer
 * TLOS - The Last of SaaS
 */
$siteLogo = '/assets/images/logo.svg';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #000;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --bg: #0f172a;
            --bg-card: #1e293b;
            --bg-table: #334155;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --border: #475569;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow: hidden;
        }

        /* Header */
        .display-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .site-logo {
            height: 40px;
            filter: brightness(0) invert(1);
        }

        .header-info h1 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .header-info p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .current-time {
            font-size: 2rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        .round-info {
            text-align: right;
        }

        .round-label {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .round-time {
            font-size: 1.25rem;
            font-weight: 600;
        }

        /* Timer Section */
        .timer-section {
            background: var(--bg-card);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 3rem;
            border-bottom: 1px solid var(--border);
        }

        .timer-display {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .timer-countdown {
            font-size: 4rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            letter-spacing: -2px;
        }

        .timer-countdown.warning {
            color: var(--warning);
        }

        .timer-countdown.danger {
            color: var(--danger);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .timer-label {
            font-size: 1rem;
            color: var(--text-muted);
        }

        .timer-controls {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-start {
            background: var(--success);
            color: white;
        }

        .btn-start:hover {
            background: #059669;
        }

        .btn-pause {
            background: var(--warning);
            color: white;
        }

        .btn-reset {
            background: var(--bg-table);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-next {
            background: var(--primary);
            color: white;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .round-indicator {
            display: flex;
            gap: 0.5rem;
        }

        .round-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--border);
        }

        .round-dot.active {
            background: var(--success);
        }

        .round-dot.completed {
            background: var(--primary);
        }

        /* Tables Grid */
        .tables-container {
            padding: 2rem;
            overflow-y: auto;
            max-height: calc(100vh - 200px);
        }

        .tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1800px;
            margin: 0 auto;
        }

        /* Table Card */
        .table-card {
            background: var(--bg-card);
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid var(--border);
        }

        .table-card.has-meeting {
            border-color: var(--primary);
        }

        .table-number {
            background: var(--bg-table);
            padding: 0.75rem;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .table-participants {
            display: flex;
            flex-direction: column;
        }

        .participant {
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            min-height: 80px;
        }

        .participant:first-child {
            border-bottom: 1px dashed var(--border);
            background: rgba(79, 70, 229, 0.1);
        }

        .participant:last-child {
            background: rgba(16, 185, 129, 0.1);
        }

        .participant-logo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .participant-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
        }

        .participant-logo.empty {
            background: var(--bg-table);
        }

        .participant-logo .placeholder {
            font-size: 1.5rem;
            color: var(--text-muted);
        }

        .participant-name {
            font-weight: 600;
            font-size: 0.95rem;
            line-height: 1.3;
        }

        .participant-role {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .empty-slot {
            color: var(--text-muted);
            font-style: italic;
        }

        /* Status indicators */
        .status-bar {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            text-align: center;
        }

        .status-waiting {
            background: var(--bg);
            color: var(--text-muted);
        }

        .status-active {
            background: var(--success);
            color: white;
        }

        .status-transition {
            background: var(--warning);
            color: white;
        }

        .status-finished {
            background: var(--border);
            color: var(--text);
        }

        /* Transition screen */
        .transition-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 1000;
        }

        .transition-screen.active {
            display: flex;
        }

        .transition-message {
            font-size: 3rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
        }

        .transition-countdown {
            font-size: 8rem;
            font-weight: 800;
            color: var(--warning);
        }

        .transition-hint {
            font-size: 1.5rem;
            color: var(--text-muted);
            margin-top: 1rem;
        }

        /* Fullscreen button */
        .fullscreen-btn {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            z-index: 100;
        }

        .fullscreen-btn:hover {
            background: var(--bg-table);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .display-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .header-right {
                flex-direction: column;
                gap: 0.5rem;
            }

            .timer-section {
                flex-direction: column;
                gap: 1rem;
            }

            .timer-countdown {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="display-header">
        <div class="header-left">
            <img src="<?= $siteLogo ?>" alt="Logo" class="site-logo" onerror="this.style.display='none'">
            <div class="header-info">
                <h1><?= htmlspecialchars($block['name']) ?></h1>
                <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($block['location'] ?? 'Sala principal') ?></p>
            </div>
        </div>
        <div class="header-right">
            <div class="round-info">
                <div class="round-label">Ronda actual</div>
                <div class="round-time" id="currentRoundTime">--:--</div>
            </div>
            <div class="current-time" id="clock">--:--</div>
        </div>
    </header>

    <!-- Timer Section -->
    <div class="timer-section">
        <div class="timer-display">
            <div>
                <div class="timer-countdown" id="countdown"><?= str_pad((string) $slotDuration, 2, '0', STR_PAD_LEFT) ?>:00</div>
                <div class="timer-label">Tiempo restante</div>
            </div>
        </div>

        <div class="round-indicator" id="roundIndicator">
            <?php $roundIndex = 0; foreach ($rounds as $time => $tables): ?>
                <div class="round-dot" data-round="<?= $roundIndex ?>" title="<?= $time ?>"></div>
            <?php $roundIndex++; endforeach; ?>
        </div>

        <div class="timer-controls">
            <button class="btn btn-start" id="btnStart" onclick="startTimer()">
                <i class="fas fa-play"></i> Iniciar
            </button>
            <button class="btn btn-pause" id="btnPause" onclick="pauseTimer()" style="display: none;">
                <i class="fas fa-pause"></i> Pausar
            </button>
            <button class="btn btn-reset" id="btnReset" onclick="resetTimer()">
                <i class="fas fa-redo"></i> Reiniciar
            </button>
            <button class="btn btn-next" id="btnNext" onclick="nextRound()" disabled>
                <i class="fas fa-forward"></i> Siguiente
            </button>
        </div>

        <div class="status-bar" id="statusBar">
            <span id="statusText">Preparado para iniciar</span>
        </div>
    </div>

    <!-- Tables Display -->
    <div class="tables-container">
        <div class="tables-grid" id="tablesGrid">
            <!-- Tables will be rendered by JS -->
        </div>
    </div>

    <!-- Transition Screen -->
    <div class="transition-screen" id="transitionScreen">
        <div class="transition-message" id="transitionMessage">Cambio de ronda</div>
        <div class="transition-countdown" id="transitionCountdown">120</div>
        <div class="transition-hint">Dirígete a tu siguiente mesa</div>
    </div>

    <!-- Fullscreen Button -->
    <button class="fullscreen-btn" onclick="toggleFullscreen()" title="Pantalla completa">
        <i class="fas fa-expand"></i>
    </button>

    <!-- Audio elements for sounds -->
    <audio id="beepSound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleQdT0eyxhGM6N1jTxK6MYTZDYt++qY5kPUFj2L6riGE+QWHdwaeLYT5CYN3BqIpgPkJg3sGoi2A+QWDdwaiLYD5BYN3BqItgPkFg3cGoi2A+QWDdwaiLYD5BYN3BqItgP0Fg3cGoi2A/QWDdwaiLYD9BYN3BqItgP0Fg3cGoi2A/QWDdwaiLYD9BYN3BqItg" type="audio/wav">
    </audio>
    <audio id="startSound" preload="auto">
        <source src="data:audio/wav;base64,UklGRl9vT19teleQdT0eyxhGM6N1jTxK6MYTZDYt++qY5kPUFj2L6riGE+QWHdwaeLYT5CYN3BqIpgPkJg3sGoi2A+QWDdwaiLYD5BYN3BqItgPkFg3cGoi2A+QWDdwaiLYD5BYN3BqItgPkFg3cGoi2A/QWDdwaiLYD9BYN3BqItgP0Fg3cGoi2A/QWDdwaiLYD9BYN3BqItgP0Fg3cGoi2A/QWDdwaiLYD9BYN3BqItg" type="audio/wav">
    </audio>

    <script>
        // Meeting data from PHP
        const rounds = <?= json_encode($rounds) ?>;
        const roundTimes = Object.keys(rounds);
        const totalRooms = <?= $totalRooms ?>;
        const slotDuration = <?= $slotDuration ?>; // minutes
        const transitionTime = 120; // seconds (2 minutes)

        // State
        let currentRound = 0;
        let timeRemaining = slotDuration * 60; // in seconds
        let timerInterval = null;
        let isRunning = false;
        let inTransition = false;
        let transitionTimeRemaining = transitionTime;

        // Audio context for beeps
        let audioContext = null;

        function initAudio() {
            if (!audioContext) {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }
        }

        function playBeep(frequency = 800, duration = 200, count = 1, interval = 300) {
            initAudio();
            let played = 0;

            function beep() {
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.value = frequency;
                oscillator.type = 'sine';

                gainNode.gain.setValueAtTime(0.5, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duration / 1000);

                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + duration / 1000);

                played++;
                if (played < count) {
                    setTimeout(beep, interval);
                }
            }

            beep();
        }

        function playEndSound() {
            // 5 beeps to signal round end
            playBeep(880, 300, 5, 400);
        }

        function playStartSound() {
            // Single longer beep to signal round start
            playBeep(660, 500, 1);
        }

        // Update clock
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent =
                now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Render tables for current round
        function renderTables() {
            const grid = document.getElementById('tablesGrid');
            const roundData = rounds[roundTimes[currentRound]] || {};

            let html = '';
            for (let i = 1; i <= totalRooms; i++) {
                const meeting = roundData[i];
                const hasMeeting = meeting && meeting.sponsor_name;

                html += `
                    <div class="table-card ${hasMeeting ? 'has-meeting' : ''}">
                        <div class="table-number">Mesa ${i}</div>
                        <div class="table-participants">
                            <div class="participant">
                                ${hasMeeting ? `
                                    <div class="participant-logo">
                                        ${meeting.sponsor_logo ?
                                            `<img src="${meeting.sponsor_logo}" alt="${meeting.sponsor_name}" onerror="this.parentElement.innerHTML='<span class=\\'placeholder\\'><i class=\\'fas fa-building\\'></i></span>'">`
                                            : '<span class="placeholder"><i class="fas fa-building"></i></span>'}
                                    </div>
                                    <div>
                                        <div class="participant-role">Sponsor</div>
                                        <div class="participant-name">${meeting.sponsor_name}</div>
                                    </div>
                                ` : `
                                    <div class="participant-logo empty">
                                        <span class="placeholder"><i class="fas fa-user"></i></span>
                                    </div>
                                    <div class="empty-slot">Sin asignar</div>
                                `}
                            </div>
                            <div class="participant">
                                ${hasMeeting ? `
                                    <div class="participant-logo">
                                        ${meeting.company_logo ?
                                            `<img src="${meeting.company_logo}" alt="${meeting.company_name}" onerror="this.parentElement.innerHTML='<span class=\\'placeholder\\'><i class=\\'fas fa-briefcase\\'></i></span>'">`
                                            : '<span class="placeholder"><i class="fas fa-briefcase"></i></span>'}
                                    </div>
                                    <div>
                                        <div class="participant-role">Empresa</div>
                                        <div class="participant-name">${meeting.company_name}</div>
                                    </div>
                                ` : `
                                    <div class="participant-logo empty">
                                        <span class="placeholder"><i class="fas fa-user"></i></span>
                                    </div>
                                    <div class="empty-slot">Sin asignar</div>
                                `}
                            </div>
                        </div>
                    </div>
                `;
            }

            grid.innerHTML = html;

            // Update round time display
            document.getElementById('currentRoundTime').textContent = roundTimes[currentRound] || '--:--';

            // Update round indicators
            document.querySelectorAll('.round-dot').forEach((dot, index) => {
                dot.classList.remove('active', 'completed');
                if (index < currentRound) {
                    dot.classList.add('completed');
                } else if (index === currentRound) {
                    dot.classList.add('active');
                }
            });
        }

        // Timer functions
        function updateCountdown() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            const display = document.getElementById('countdown');

            display.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            // Color warnings
            display.classList.remove('warning', 'danger');
            if (timeRemaining <= 60 && timeRemaining > 30) {
                display.classList.add('warning');
            } else if (timeRemaining <= 30) {
                display.classList.add('danger');
            }
        }

        function startTimer() {
            if (isRunning) return;

            initAudio(); // Initialize audio on user interaction
            isRunning = true;

            document.getElementById('btnStart').style.display = 'none';
            document.getElementById('btnPause').style.display = 'inline-flex';
            document.getElementById('statusText').textContent = 'Reunión en curso';
            document.getElementById('statusBar').className = 'status-bar status-active';

            playStartSound();

            timerInterval = setInterval(() => {
                if (timeRemaining > 0) {
                    timeRemaining--;
                    updateCountdown();
                } else {
                    clearInterval(timerInterval);
                    timerInterval = null;
                    isRunning = false;
                    onRoundEnd();
                }
            }, 1000);
        }

        function pauseTimer() {
            if (!isRunning) return;

            isRunning = false;
            clearInterval(timerInterval);
            timerInterval = null;

            document.getElementById('btnStart').style.display = 'inline-flex';
            document.getElementById('btnPause').style.display = 'none';
            document.getElementById('statusText').textContent = 'Pausado';
            document.getElementById('statusBar').className = 'status-bar status-waiting';
        }

        function resetTimer() {
            pauseTimer();
            timeRemaining = slotDuration * 60;
            updateCountdown();
            document.getElementById('statusText').textContent = 'Preparado para iniciar';
        }

        function onRoundEnd() {
            playEndSound();

            document.getElementById('btnStart').style.display = 'inline-flex';
            document.getElementById('btnPause').style.display = 'none';
            document.getElementById('btnNext').disabled = false;

            if (currentRound < roundTimes.length - 1) {
                // Start transition period
                startTransition();
            } else {
                document.getElementById('statusText').textContent = 'Todas las rondas completadas';
                document.getElementById('statusBar').className = 'status-bar status-finished';
            }
        }

        function startTransition() {
            inTransition = true;
            transitionTimeRemaining = transitionTime;

            const screen = document.getElementById('transitionScreen');
            screen.classList.add('active');

            document.getElementById('transitionMessage').textContent = `Cambio a ronda ${currentRound + 2}`;
            document.getElementById('statusText').textContent = 'Transición - Cambio de mesa';
            document.getElementById('statusBar').className = 'status-bar status-transition';

            const transitionInterval = setInterval(() => {
                transitionTimeRemaining--;
                document.getElementById('transitionCountdown').textContent = transitionTimeRemaining;

                if (transitionTimeRemaining <= 0) {
                    clearInterval(transitionInterval);
                    endTransition();
                }
            }, 1000);
        }

        function endTransition() {
            inTransition = false;
            document.getElementById('transitionScreen').classList.remove('active');

            // Auto advance to next round and start
            currentRound++;
            timeRemaining = slotDuration * 60;
            updateCountdown();
            renderTables();
            document.getElementById('btnNext').disabled = true;

            // Auto start next round
            startTimer();
        }

        function nextRound() {
            if (currentRound >= roundTimes.length - 1) return;

            if (inTransition) {
                // Skip transition
                transitionTimeRemaining = 0;
            } else {
                currentRound++;
                timeRemaining = slotDuration * 60;
                updateCountdown();
                renderTables();
                document.getElementById('btnNext').disabled = true;
                document.getElementById('statusText').textContent = 'Preparado para iniciar';
                document.getElementById('statusBar').className = 'status-bar status-waiting';
            }
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        // Initialize
        renderTables();
        updateCountdown();

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                e.preventDefault();
                if (isRunning) {
                    pauseTimer();
                } else {
                    startTimer();
                }
            } else if (e.code === 'KeyN' || e.code === 'ArrowRight') {
                nextRound();
            } else if (e.code === 'KeyR') {
                resetTimer();
            } else if (e.code === 'KeyF') {
                toggleFullscreen();
            }
        });
    </script>
</body>
</html>
