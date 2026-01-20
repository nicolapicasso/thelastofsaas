<?php
/**
 * Ticket Detail Template
 * TLOS - The Last of SaaS
 */
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Ticket #<?= $ticket['id'] ?></h1>
        <p>Detalles del ticket</p>
    </div>
    <div class="page-header-actions">
        <a href="/admin/tickets?event_id=<?= $ticket['event_id'] ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Volver</a>
        <?php if ($ticket['status'] === 'pending'): ?>
            <button type="button" class="btn btn-success" onclick="approveTicket()"><i class="fas fa-check"></i> Aprobar</button>
        <?php endif; ?>
        <?php if ($ticket['status'] !== 'cancelled'): ?>
            <button type="button" class="btn btn-danger" onclick="cancelTicket()"><i class="fas fa-times"></i> Cancelar</button>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Main Info -->
    <div>
        <!-- Ticket Info -->
        <div class="card">
            <div class="card-header">
                <h3>Información del Ticket</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="180">Código</th>
                        <td><code style="font-size: 1.2rem; letter-spacing: 2px;"><?= $ticket['code'] ?></code></td>
                    </tr>
                    <tr>
                        <th>Evento</th>
                        <td><?= htmlspecialchars($event['name'] ?? $ticket['event_name'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Tipo de Ticket</th>
                        <td><?= htmlspecialchars($ticketType['name'] ?? $ticket['ticket_type_name'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Precio</th>
                        <td>
                            <?php if ($ticket['price'] > 0): ?>
                                <strong><?= number_format($ticket['price'], 2) ?> €</strong>
                            <?php else: ?>
                                <span class="badge badge-success">Gratis</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Estado</th>
                        <td>
                            <?php
                            $statusClass = match($ticket['status']) {
                                'confirmed' => 'success',
                                'pending' => 'warning',
                                'used' => 'info',
                                'cancelled' => 'danger',
                                default => 'secondary'
                            };
                            ?>
                            <span class="badge badge-<?= $statusClass ?>"><?= $statusOptions[$ticket['status']] ?? $ticket['status'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Creado</th>
                        <td><?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></td>
                    </tr>
                    <?php if ($ticket['used_at']): ?>
                    <tr>
                        <th>Utilizado</th>
                        <td><?= date('d/m/Y H:i', strtotime($ticket['used_at'])) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Attendee Info -->
        <div class="card">
            <div class="card-header">
                <h3>Datos del Asistente</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="180">Nombre</th>
                        <td><?= htmlspecialchars($ticket['attendee_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><a href="mailto:<?= htmlspecialchars($ticket['attendee_email']) ?>"><?= htmlspecialchars($ticket['attendee_email']) ?></a></td>
                    </tr>
                    <?php if ($ticket['attendee_phone']): ?>
                    <tr>
                        <th>Teléfono</th>
                        <td><?= htmlspecialchars($ticket['attendee_phone']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($ticket['attendee_company']): ?>
                    <tr>
                        <th>Empresa</th>
                        <td><?= htmlspecialchars($ticket['attendee_company']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($ticket['attendee_position']): ?>
                    <tr>
                        <th>Cargo</th>
                        <td><?= htmlspecialchars($ticket['attendee_position']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Sponsor Info (if applicable) -->
        <?php if (!empty($sponsor)): ?>
        <div class="card">
            <div class="card-header">
                <h3>Sponsor Asociado</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <?php if (!empty($sponsor['logo_url'])): ?>
                        <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="" style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px;">
                    <?php endif; ?>
                    <div>
                        <strong><?= htmlspecialchars($sponsor['name'] ?? '') ?></strong>
                        <?php if (!empty($sponsor['code'])): ?>
                            <br><small class="text-muted">Código: <?= htmlspecialchars($sponsor['code']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Payment Info (if paid) -->
        <?php if ($ticket['price'] > 0 && $ticket['stripe_payment_id']): ?>
        <div class="card">
            <div class="card-header">
                <h3>Información de Pago</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="180">Stripe Payment ID</th>
                        <td><code><?= $ticket['stripe_payment_id'] ?></code></td>
                    </tr>
                    <tr>
                        <th>Importe</th>
                        <td><?= number_format($ticket['price'], 2) ?> €</td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Notes -->
        <?php if ($ticket['notes']): ?>
        <div class="card">
            <div class="card-header">
                <h3>Notas</h3>
            </div>
            <div class="card-body">
                <?= nl2br(htmlspecialchars($ticket['notes'])) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- QR Code -->
        <div class="card">
            <div class="card-header">
                <h3>Código QR</h3>
            </div>
            <div class="card-body" style="text-align: center;">
                <div id="qrcode" style="display: inline-block; padding: 1rem; background: white; border-radius: 8px;"></div>
                <p style="margin-top: 1rem;">
                    <code style="font-size: 1.1rem;"><?= $ticket['code'] ?></code>
                </p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3>Acciones</h3>
            </div>
            <div class="card-body">
                <?php if ($ticket['status'] === 'pending'): ?>
                    <button type="button" class="btn btn-success btn-block" onclick="approveTicket()">
                        <i class="fas fa-check"></i> Aprobar Ticket
                    </button>
                <?php endif; ?>

                <?php if ($ticket['status'] === 'confirmed'): ?>
                    <button type="button" class="btn btn-info btn-block" onclick="markAsUsed()">
                        <i class="fas fa-check-circle"></i> Marcar como Usado
                    </button>
                <?php endif; ?>

                <?php if (in_array($ticket['status'], ['pending', 'confirmed'])): ?>
                    <button type="button" class="btn btn-warning btn-block" onclick="cancelTicket()">
                        <i class="fas fa-ban"></i> Cancelar Ticket
                    </button>
                <?php endif; ?>

                <hr style="margin: 1rem 0; border-color: var(--color-gray-200);">

                <button type="button" class="btn btn-outline btn-block" onclick="openChangeStatusModal()">
                    <i class="fas fa-exchange-alt"></i> Cambiar Estado
                </button>

                <button type="button" class="btn btn-outline btn-block" onclick="resendEmail()">
                    <i class="fas fa-envelope"></i> Reenviar Email
                </button>

                <a href="/admin/tickets/<?= $ticket['id'] ?>/download" class="btn btn-outline btn-block">
                    <i class="fas fa-download"></i> Descargar PDF
                </a>

                <hr style="margin: 1rem 0; border-color: var(--color-gray-200);">

                <button type="button" class="btn btn-danger btn-block" onclick="deleteTicket()">
                    <i class="fas fa-trash"></i> Eliminar Ticket
                </button>
            </div>
        </div>

        <!-- Change Status Modal -->
        <div id="status-modal" class="status-modal" style="display: none;">
            <div class="status-modal-backdrop" onclick="closeChangeStatusModal()"></div>
            <div class="status-modal-content">
                <div class="status-modal-header">
                    <h4>Cambiar Estado</h4>
                    <button type="button" onclick="closeChangeStatusModal()">&times;</button>
                </div>
                <div class="status-modal-body">
                    <select id="new-ticket-status" class="form-control">
                        <?php foreach ($statusOptions as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $ticket['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="status-modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeChangeStatusModal()">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveStatusChange()">Guardar</button>
                </div>
            </div>
        </div>

        <style>
        .status-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .status-modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .status-modal-content {
            position: relative;
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 350px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .status-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--color-gray-200);
        }
        .status-modal-header h4 {
            margin: 0;
            font-size: 1rem;
        }
        .status-modal-header button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--color-gray-500);
        }
        .status-modal-body {
            padding: 1.25rem;
        }
        .status-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--color-gray-200);
        }
        </style>

        <!-- Activity Log -->
        <?php if (!empty($activityLog)): ?>
        <div class="card">
            <div class="card-header">
                <h3>Actividad</h3>
            </div>
            <div class="card-body">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php foreach ($activityLog as $log): ?>
                        <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border-color);">
                            <small class="text-muted"><?= date('d/m H:i', strtotime($log['created_at'])) ?></small>
                            <br><?= htmlspecialchars($log['action']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- QRCode.js library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
// Generate QR code
QRCode.toCanvas(document.createElement('canvas'), '<?= $ticket['code'] ?>', { width: 180 }, function(error, canvas) {
    if (!error) {
        document.getElementById('qrcode').appendChild(canvas);
    }
});

function approveTicket() {
    if (!confirm('¿Aprobar este ticket?')) return;
    fetch('/admin/tickets/<?= $ticket['id'] ?>/approve', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => d.success ? location.reload() : alert(d.error));
}

function cancelTicket() {
    if (!confirm('¿Cancelar este ticket?')) return;
    fetch('/admin/tickets/<?= $ticket['id'] ?>/cancel', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => d.success ? location.reload() : alert(d.error));
}

function markAsUsed() {
    if (!confirm('¿Marcar este ticket como usado (check-in)?')) return;
    fetch('/admin/tickets/<?= $ticket['id'] ?>/check-in', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => d.success ? location.reload() : alert(d.error || d.message));
}

function resendEmail() {
    if (!confirm('¿Reenviar email de confirmación?')) return;
    fetch('/admin/tickets/<?= $ticket['id'] ?>/resend-email', {
        method: 'POST',
        body: new URLSearchParams({_csrf_token: '<?= $csrf_token ?>'})
    }).then(r => r.json()).then(d => {
        if (d.success) {
            alert('Email enviado correctamente');
        } else {
            alert(d.error || 'Error al enviar email');
        }
    });
}

function deleteTicket() {
    if (!confirm('¿Estás seguro de que quieres eliminar este ticket? Esta acción no se puede deshacer.')) return;
    fetch('/admin/tickets/bulk-action', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            ids: [<?= $ticket['id'] ?>],
            action: 'delete'
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            window.location.href = '/admin/tickets?event_id=<?= $ticket['event_id'] ?>';
        } else {
            alert(d.error || 'Error al eliminar');
        }
    });
}

function openChangeStatusModal() {
    document.getElementById('status-modal').style.display = 'flex';
}

function closeChangeStatusModal() {
    document.getElementById('status-modal').style.display = 'none';
}

function saveStatusChange() {
    const newStatus = document.getElementById('new-ticket-status').value;
    fetch('/admin/tickets/bulk-action', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            ids: [<?= $ticket['id'] ?>],
            action: 'status',
            value: newStatus
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            location.reload();
        } else {
            alert(d.error || 'Error al cambiar estado');
        }
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeChangeStatusModal();
    }
});
</script>
