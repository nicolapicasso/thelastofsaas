<?php
/**
 * Ticket Confirmation/Show Template
 * TLOS - The Last of SaaS
 */
use App\Services\QRService;
$qrService = new QRService(250);
$qrDataUri = $qrService->generateDataUri($ticket['code']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Entrada - <?= htmlspecialchars($event['name'] ?? '') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #3730A3;
            --success: #10B981;
            --warning: #F59E0B;
            --error: #EF4444;
            --info: #3B82F6;
            --bg: #F3F4F6;
            --card: #FFFFFF;
            --text: #1F2937;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            --border: #E5E7EB;
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
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Ticket Card */
        .ticket-card {
            background: var(--card);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        /* Header */
        .ticket-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .ticket-status {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .ticket-status--confirmed { background: rgba(16, 185, 129, 0.2); color: #6EE7B7; }
        .ticket-status--pending { background: rgba(245, 158, 11, 0.2); color: #FCD34D; }
        .ticket-status--used { background: rgba(59, 130, 246, 0.2); color: #93C5FD; }
        .ticket-status--cancelled { background: rgba(239, 68, 68, 0.2); color: #FCA5A5; }

        .ticket-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .ticket-meta {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .ticket-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* QR Section */
        .ticket-qr-section {
            padding: 2.5rem 2rem;
            text-align: center;
            border-bottom: 2px dashed var(--border);
            position: relative;
        }

        .ticket-qr-section::before,
        .ticket-qr-section::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            background: var(--bg);
            border-radius: 50%;
            bottom: -15px;
        }

        .ticket-qr-section::before { left: -15px; }
        .ticket-qr-section::after { right: -15px; }

        .qr-wrapper {
            display: inline-block;
            padding: 1rem;
            background: white;
            border: 3px solid var(--border);
            border-radius: 16px;
        }

        .qr-wrapper img {
            display: block;
            width: 200px;
            height: 200px;
        }

        .ticket-code {
            font-family: 'Space Mono', monospace;
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: 4px;
            color: var(--text);
            margin: 1.5rem 0 0.5rem;
        }

        .qr-hint {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Info Section */
        .ticket-info-section {
            padding: 2rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .info-item {
            padding: 1rem;
            background: var(--bg);
            border-radius: 12px;
        }

        .info-item.full-width {
            grid-column: 1 / -1;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-weight: 600;
            color: var(--text);
        }

        /* Sponsor Section */
        .sponsor-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, #F0FDF4 0%, #ECFDF5 100%);
            border-radius: 12px;
            border: 1px solid #D1FAE5;
        }

        .sponsor-section img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 8px;
            background: white;
            padding: 4px;
        }

        .sponsor-section .info-label {
            margin-bottom: 0.25rem;
        }

        .sponsor-section .sponsor-name {
            font-weight: 600;
            color: var(--success);
        }

        /* Actions */
        .ticket-actions {
            display: flex;
            gap: 0.75rem;
            padding: 1.5rem 2rem;
            background: var(--bg);
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            border: none;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: white;
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-outline:hover {
            background: var(--bg);
            border-color: var(--text-muted);
        }

        /* Note */
        .ticket-note {
            text-align: center;
            padding: 1rem 2rem 2rem;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .ticket-note i {
            color: var(--primary);
            margin-right: 0.5rem;
        }

        /* Event Details Card */
        .event-card {
            background: var(--card);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .event-card h3 {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: var(--text-secondary);
        }

        .event-detail {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .event-detail i {
            width: 20px;
            color: var(--primary);
            margin-top: 2px;
        }

        .event-detail strong {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .event-detail p {
            margin: 0.25rem 0 0;
        }

        .event-card .btn {
            width: 100%;
            justify-content: center;
            margin-top: 1rem;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
            }

            .container {
                padding: 0;
                max-width: 100%;
            }

            .ticket-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .ticket-actions,
            .event-card .btn,
            .btn {
                display: none !important;
            }

            .ticket-note {
                display: none;
            }
        }

        /* Responsive */
        @media (max-width: 600px) {
            .container {
                padding: 1rem;
            }

            .ticket-header h1 {
                font-size: 1.4rem;
            }

            .ticket-meta {
                flex-direction: column;
                gap: 0.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .ticket-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="ticket-card">
            <!-- Header -->
            <div class="ticket-header">
                <span class="ticket-status ticket-status--<?= $ticket['status'] ?>">
                    <?php
                    $statusLabels = [
                        'confirmed' => '✓ Confirmada',
                        'pending' => '⏳ Pendiente',
                        'used' => '✓ Utilizada',
                        'cancelled' => '✗ Cancelada'
                    ];
                    echo $statusLabels[$ticket['status']] ?? ucfirst($ticket['status']);
                    ?>
                </span>
                <h1><?= htmlspecialchars($event['name'] ?? '') ?></h1>
                <div class="ticket-meta">
                    <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($event['start_date'] ?? 'now')) ?></span>
                    <?php if (!empty($event['start_time'])): ?>
                        <span><i class="fas fa-clock"></i> <?= substr($event['start_time'], 0, 5) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($event['location'])): ?>
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- QR Code -->
            <div class="ticket-qr-section">
                <div class="qr-wrapper">
                    <img src="<?= $qrDataUri ?>" alt="QR Code" id="qrImage">
                </div>
                <p class="ticket-code"><?= htmlspecialchars($ticket['code'] ?? '') ?></p>
                <p class="qr-hint">Muestra este código en la entrada del evento</p>
            </div>

            <!-- Info -->
            <div class="ticket-info-section">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nombre</div>
                        <div class="info-value"><?= htmlspecialchars($ticket['attendee_name'] ?? '') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($ticket['attendee_email'] ?? '') ?></div>
                    </div>
                    <?php if (!empty($ticket['attendee_company'])): ?>
                    <div class="info-item">
                        <div class="info-label">Empresa</div>
                        <div class="info-value"><?= htmlspecialchars($ticket['attendee_company']) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($ticket['attendee_position'])): ?>
                    <div class="info-item">
                        <div class="info-label">Cargo</div>
                        <div class="info-value"><?= htmlspecialchars($ticket['attendee_position']) ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="info-item">
                        <div class="info-label">Tipo de entrada</div>
                        <div class="info-value"><?= htmlspecialchars($ticketType['name'] ?? 'General') ?></div>
                    </div>
                    <?php if (!empty($sponsor)): ?>
                    <div class="info-item full-width sponsor-section">
                        <?php if (!empty($sponsor['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="">
                        <?php endif; ?>
                        <div>
                            <div class="info-label">Invitación cortesía de</div>
                            <div class="sponsor-name"><?= htmlspecialchars($sponsor['name'] ?? '') ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="ticket-actions">
                <button onclick="window.print()" class="btn btn-outline">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <button onclick="downloadTicket()" class="btn btn-primary" id="downloadBtn">
                    <i class="fas fa-download"></i> Guardar entrada
                </button>
                <button onclick="resendEmail()" class="btn btn-outline" id="resendBtn">
                    <i class="fas fa-envelope"></i> Reenviar email
                </button>
            </div>

            <p class="ticket-note">
                <i class="fas fa-info-circle"></i>
                Guarda esta página o haz una captura de pantalla. También te hemos enviado la confirmación por email.
            </p>
        </div>

        <!-- Event Details -->
        <div class="event-card">
            <h3>Información del evento</h3>
            <?php if (!empty($event['address'])): ?>
            <div class="event-detail">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <strong>Dirección</strong>
                    <p><?= htmlspecialchars($event['address']) ?></p>
                </div>
            </div>
            <?php endif; ?>
            <div class="event-detail">
                <i class="fas fa-calendar-alt"></i>
                <div>
                    <strong>Fecha y hora</strong>
                    <p><?= date('l, d \d\e F \d\e Y', strtotime($event['start_date'] ?? 'now')) ?><?php if (!empty($event['start_time'])): ?> a las <?= substr($event['start_time'], 0, 5) ?><?php endif; ?></p>
                </div>
            </div>
            <a href="/eventos/<?= htmlspecialchars($event['slug'] ?? '') ?>" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i> Ver detalles del evento
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
    function resendEmail() {
        const btn = document.getElementById('resendBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

        fetch('/eventos/<?= htmlspecialchars($event['slug'] ?? '') ?>/ticket/<?= htmlspecialchars($ticket['code'] ?? '') ?>/resend', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: '_csrf_token=<?= htmlspecialchars($csrf_token ?? '') ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.innerHTML = '<i class="fas fa-check"></i> Enviado';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-envelope"></i> Reenviar email';
                    btn.disabled = false;
                }, 3000);
            } else {
                alert(data.error || 'Error al enviar');
                btn.innerHTML = '<i class="fas fa-envelope"></i> Reenviar email';
                btn.disabled = false;
            }
        })
        .catch(() => {
            alert('Error de conexión');
            btn.innerHTML = '<i class="fas fa-envelope"></i> Reenviar email';
            btn.disabled = false;
        });
    }

    function downloadTicket() {
        const btn = document.getElementById('downloadBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

        const ticketCard = document.querySelector('.ticket-card');
        const actions = document.querySelector('.ticket-actions');
        const note = document.querySelector('.ticket-note');

        // Hide actions temporarily
        actions.style.display = 'none';
        note.style.display = 'none';

        html2canvas(ticketCard, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            // Restore visibility
            actions.style.display = 'flex';
            note.style.display = 'block';

            // Create download link
            const link = document.createElement('a');
            link.download = 'entrada-<?= htmlspecialchars($ticket['code'] ?? 'ticket') ?>.png';
            link.href = canvas.toDataURL('image/png');
            link.click();

            btn.innerHTML = '<i class="fas fa-check"></i> Descargada';
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-download"></i> Guardar entrada';
                btn.disabled = false;
            }, 2000);
        }).catch(err => {
            console.error('Error generating image:', err);
            actions.style.display = 'flex';
            note.style.display = 'block';
            btn.innerHTML = '<i class="fas fa-download"></i> Guardar entrada';
            btn.disabled = false;
            alert('Error al generar la imagen. Usa la opción Imprimir para guardar como PDF.');
        });
    }
    </script>
</body>
</html>
