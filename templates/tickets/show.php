<?php
/**
 * Ticket Confirmation/Show Template
 * TLOS - The Last of SaaS
 */
use App\Services\QRService;
$qrService = new QRService();
$qrDataUri = $qrService->generateDataUri($ticket['code']);
?>

<section class="ticket-page">
    <div class="container">
        <div class="ticket-card">
            <!-- Header -->
            <div class="ticket-header">
                <div class="ticket-header__content">
                    <span class="ticket-status ticket-status--<?= $ticket['status'] ?>">
                        <?php
                        $statusLabels = [
                            'confirmed' => '✓ Confirmada',
                            'pending' => '⏳ Pendiente',
                            'used' => '✓ Utilizada',
                            'cancelled' => '✗ Cancelada'
                        ];
                        echo $statusLabels[$ticket['status']] ?? $ticket['status'];
                        ?>
                    </span>
                    <h1><?= htmlspecialchars($event['name']) ?></h1>
                    <p class="ticket-meta">
                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($event['start_date'])) ?></span>
                        <?php if ($event['start_time']): ?>
                            <span><i class="fas fa-clock"></i> <?= substr($event['start_time'], 0, 5) ?></span>
                        <?php endif; ?>
                        <?php if ($event['location']): ?>
                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Body -->
            <div class="ticket-body">
                <div class="ticket-layout">
                    <!-- QR Code -->
                    <div class="ticket-qr">
                        <img src="<?= $qrDataUri ?>" alt="QR Code">
                        <p class="ticket-code"><?= $ticket['code'] ?></p>
                        <small>Muestra este código en la entrada</small>
                    </div>

                    <!-- Attendee Info -->
                    <div class="ticket-info">
                        <h2>Datos del asistente</h2>
                        <dl class="info-list">
                            <div>
                                <dt>Nombre</dt>
                                <dd><?= htmlspecialchars($ticket['attendee_name']) ?></dd>
                            </div>
                            <div>
                                <dt>Email</dt>
                                <dd><?= htmlspecialchars($ticket['attendee_email']) ?></dd>
                            </div>
                            <?php if ($ticket['attendee_company']): ?>
                            <div>
                                <dt>Empresa</dt>
                                <dd><?= htmlspecialchars($ticket['attendee_company']) ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($ticket['attendee_position']): ?>
                            <div>
                                <dt>Cargo</dt>
                                <dd><?= htmlspecialchars($ticket['attendee_position']) ?></dd>
                            </div>
                            <?php endif; ?>
                            <div>
                                <dt>Tipo de entrada</dt>
                                <dd><?= htmlspecialchars($ticketType['name']) ?></dd>
                            </div>
                        </dl>

                        <?php if ($sponsor): ?>
                        <div class="ticket-sponsor">
                            <small>Invitación de</small>
                            <div class="sponsor-badge">
                                <?php if ($sponsor['logo_url']): ?>
                                    <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="">
                                <?php endif; ?>
                                <span><?= htmlspecialchars($sponsor['name']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="ticket-footer">
                <div class="ticket-actions">
                    <button onclick="window.print()" class="btn btn-outline">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <a href="/eventos/<?= $event['slug'] ?>/ticket/<?= $ticket['code'] ?>/download" class="btn btn-outline">
                        <i class="fas fa-download"></i> Descargar PDF
                    </a>
                    <button onclick="resendEmail()" class="btn btn-outline" id="resendBtn">
                        <i class="fas fa-envelope"></i> Reenviar email
                    </button>
                </div>
                <p class="ticket-note">
                    <i class="fas fa-info-circle"></i>
                    Guarda esta página o haz una captura de pantalla. También te hemos enviado la confirmación por email.
                </p>
            </div>
        </div>

        <!-- Event Details Card -->
        <div class="event-details-card">
            <h3>Información del evento</h3>
            <div class="event-details">
                <?php if ($event['address']): ?>
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Dirección</strong>
                        <p><?= htmlspecialchars($event['address']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="detail-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <strong>Fecha y hora</strong>
                        <p><?= date('l, d \d\e F \d\e Y', strtotime($event['start_date'])) ?><?php if ($event['start_time']): ?> a las <?= substr($event['start_time'], 0, 5) ?><?php endif; ?></p>
                    </div>
                </div>
            </div>

            <a href="/eventos/<?= $event['slug'] ?>" class="btn btn-primary">
                Ver detalles del evento
            </a>
        </div>
    </div>
</section>

<style>
.ticket-page {
    padding: 3rem 0;
    background: var(--bg-secondary);
    min-height: 100vh;
}
.ticket-page .container {
    max-width: 800px;
}

.ticket-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    margin-bottom: 2rem;
}

.ticket-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 2rem;
}
.ticket-header h1 {
    font-size: 1.75rem;
    margin: 0.5rem 0;
}
.ticket-status {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}
.ticket-status--confirmed {
    background: rgba(16, 185, 129, 0.2);
    color: #10B981;
}
.ticket-status--pending {
    background: rgba(245, 158, 11, 0.2);
    color: #F59E0B;
}
.ticket-status--used {
    background: rgba(59, 130, 246, 0.2);
    color: #3B82F6;
}
.ticket-status--cancelled {
    background: rgba(239, 68, 68, 0.2);
    color: #EF4444;
}
.ticket-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-top: 1rem;
    opacity: 0.9;
}
.ticket-meta i {
    margin-right: 0.5rem;
}

.ticket-body {
    padding: 2rem;
}
.ticket-layout {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 2rem;
}

.ticket-qr {
    text-align: center;
    padding: 1.5rem;
    background: var(--bg-secondary);
    border-radius: 12px;
}
.ticket-qr img {
    width: 180px;
    height: 180px;
    margin-bottom: 1rem;
}
.ticket-code {
    font-family: monospace;
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: 3px;
    margin: 0;
}
.ticket-qr small {
    display: block;
    margin-top: 0.5rem;
    color: var(--text-muted);
}

.ticket-info h2 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    color: var(--text-secondary);
}
.info-list {
    display: grid;
    gap: 1rem;
}
.info-list > div {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 0.5rem;
}
.info-list dt {
    font-size: 0.85rem;
    color: var(--text-muted);
}
.info-list dd {
    margin: 0;
    font-weight: 500;
}

.ticket-sponsor {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}
.ticket-sponsor small {
    color: var(--text-muted);
}
.sponsor-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
}
.sponsor-badge img {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.ticket-footer {
    padding: 1.5rem 2rem;
    background: var(--bg-secondary);
    border-top: 1px dashed var(--border-color);
}
.ticket-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.ticket-note {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin: 0;
}
.ticket-note i {
    margin-right: 0.5rem;
    color: var(--primary-color);
}

.event-details-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.event-details-card h3 {
    margin-bottom: 1.5rem;
}
.event-details {
    display: grid;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.detail-item {
    display: flex;
    gap: 1rem;
}
.detail-item i {
    color: var(--primary-color);
    font-size: 1.25rem;
    margin-top: 0.25rem;
}
.detail-item strong {
    display: block;
    font-size: 0.85rem;
    color: var(--text-secondary);
}
.detail-item p {
    margin: 0;
}

@media (max-width: 768px) {
    .ticket-layout {
        grid-template-columns: 1fr;
    }
    .ticket-qr {
        order: -1;
    }
    .info-list > div {
        grid-template-columns: 1fr;
    }
}

@media print {
    .ticket-page {
        background: white;
        padding: 0;
    }
    .ticket-footer,
    .event-details-card .btn {
        display: none;
    }
}
</style>

<script>
function resendEmail() {
    const btn = document.getElementById('resendBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    fetch('/eventos/<?= $event['slug'] ?>/ticket/<?= $ticket['code'] ?>/resend', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: '_csrf_token=<?= $csrf_token ?>'
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
</script>
