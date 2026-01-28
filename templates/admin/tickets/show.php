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

        <!-- Assignment to Company/SaaS -->
        <div class="card" style="border: 2px solid var(--color-primary); border-style: dashed; margin-top: 1.5rem;">
            <div class="card-header" style="background: rgba(79, 70, 229, 0.1);">
                <h3><i class="fas fa-user-plus"></i> Asignación a Empresa/SaaS</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($assignedCompany)): ?>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <?php if (!empty($assignedCompany['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($assignedCompany['logo_url']) ?>" alt="" style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px; background: #f5f5f5;">
                        <?php else: ?>
                            <div style="width: 50px; height: 50px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-building" style="color: #9ca3af;"></i></div>
                        <?php endif; ?>
                        <div style="flex: 1;">
                            <span class="badge badge-info" style="margin-bottom: 0.25rem;">EMPRESA</span>
                            <br><strong><?= htmlspecialchars($assignedCompany['name']) ?></strong>
                            <br><small class="text-muted">Código: <?= htmlspecialchars($assignedCompany['code']) ?></small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAssignment()" title="Quitar asignación">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <a href="/admin/companies/<?= $assignedCompany['id'] ?>/edit" class="btn btn-sm btn-outline" style="width: 100%;">
                        <i class="fas fa-external-link-alt"></i> Ver Empresa
                    </a>
                <?php elseif (!empty($assignedSponsor)): ?>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <?php if (!empty($assignedSponsor['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($assignedSponsor['logo_url']) ?>" alt="" style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px; background: #f5f5f5;">
                        <?php else: ?>
                            <div style="width: 50px; height: 50px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-rocket" style="color: #9ca3af;"></i></div>
                        <?php endif; ?>
                        <div style="flex: 1;">
                            <span class="badge badge-success" style="margin-bottom: 0.25rem;">SAAS</span>
                            <br><strong><?= htmlspecialchars($assignedSponsor['name']) ?></strong>
                            <br><small class="text-muted">Código: <?= htmlspecialchars($assignedSponsor['code']) ?></small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAssignment()" title="Quitar asignación">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <a href="/admin/sponsors/<?= $assignedSponsor['id'] ?>/edit" class="btn btn-sm btn-outline" style="width: 100%;">
                        <i class="fas fa-external-link-alt"></i> Ver SaaS
                    </a>
                <?php else: ?>
                    <p class="text-muted" style="margin-bottom: 1rem; font-size: 0.9rem;">
                        Este usuario aún no está asignado a ninguna empresa o SaaS. Asígnalo para darle acceso al portal correspondiente.
                    </p>
                    <button type="button" class="btn btn-primary btn-block" onclick="openAssignModal()">
                        <i class="fas fa-user-plus"></i> Asignar a Empresa/SaaS
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sponsor Info (if applicable) -->
        <?php if (!empty($sponsor)): ?>
        <div class="card" style="margin-top: 1.5rem;">
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
        .assign-tabs {
            display: flex;
            border-bottom: 2px solid var(--color-gray-200);
            margin-bottom: 1rem;
        }
        .assign-tab {
            flex: 1;
            padding: 0.75rem 1rem;
            text-align: center;
            cursor: pointer;
            border: none;
            background: none;
            font-weight: 500;
            color: var(--color-gray-500);
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }
        .assign-tab:hover {
            color: var(--color-gray-700);
        }
        .assign-tab.active {
            color: var(--color-primary);
            border-bottom-color: var(--color-primary);
        }
        .assign-panel {
            display: none;
        }
        .assign-panel.active {
            display: block;
        }
        .entity-search-results {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid var(--color-gray-200);
            border-radius: 6px;
            margin-top: 0.5rem;
        }
        .entity-search-results:empty {
            display: none;
        }
        .entity-result {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid var(--color-gray-100);
        }
        .entity-result:last-child {
            border-bottom: none;
        }
        .entity-result:hover {
            background: var(--color-gray-50);
        }
        .entity-result.selected {
            background: rgba(79, 70, 229, 0.1);
        }
        .entity-result img {
            width: 36px;
            height: 36px;
            object-fit: contain;
            border-radius: 4px;
            background: #f5f5f5;
        }
        .entity-result .placeholder-icon {
            width: 36px;
            height: 36px;
            background: var(--color-gray-200);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-gray-400);
        }
        .create-new-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--color-gray-300);
        }
        .create-form-fields {
            display: none;
            margin-top: 1rem;
        }
        .create-form-fields.active {
            display: block;
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

<!-- Assignment Modal -->
<div id="assign-modal" class="status-modal" style="display: none;">
    <div class="status-modal-backdrop" onclick="closeAssignModal()"></div>
    <div class="status-modal-content" style="max-width: 500px;">
        <div class="status-modal-header">
            <h4><i class="fas fa-user-plus"></i> Asignar a Empresa/SaaS</h4>
            <button type="button" onclick="closeAssignModal()">&times;</button>
        </div>
        <div class="status-modal-body">
            <!-- Tabs -->
            <div class="assign-tabs">
                <button type="button" class="assign-tab active" data-tab="company" onclick="switchAssignTab('company')">
                    <i class="fas fa-building"></i> Empresa
                </button>
                <button type="button" class="assign-tab" data-tab="sponsor" onclick="switchAssignTab('sponsor')">
                    <i class="fas fa-rocket"></i> SaaS
                </button>
            </div>

            <!-- Company Panel -->
            <div id="panel-company" class="assign-panel active">
                <div class="form-group">
                    <label>Buscar Empresa</label>
                    <input type="text" id="search-company" class="form-control" placeholder="Escribe para buscar..." oninput="searchEntities('company', this.value)">
                    <div id="results-company" class="entity-search-results"></div>
                </div>
                <input type="hidden" id="selected-company-id" value="">

                <div class="create-new-section">
                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleCreateForm('company')">
                        <i class="fas fa-plus"></i> Crear Nueva Empresa
                    </button>
                    <div id="create-form-company" class="create-form-fields">
                        <div class="form-group">
                            <label>Nombre de la Empresa *</label>
                            <input type="text" id="new-company-name" class="form-control" value="<?= htmlspecialchars($ticket['attendee_company_name'] ?? $ticket['attendee_company'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Sector</label>
                            <input type="text" id="new-company-sector" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Sitio Web</label>
                            <input type="url" id="new-company-website" class="form-control" placeholder="https://...">
                        </div>
                        <button type="button" class="btn btn-success btn-block" onclick="createAndAssign('company')">
                            <i class="fas fa-check"></i> Crear Empresa y Asignar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sponsor (SaaS) Panel -->
            <div id="panel-sponsor" class="assign-panel">
                <div class="form-group">
                    <label>Buscar SaaS</label>
                    <input type="text" id="search-sponsor" class="form-control" placeholder="Escribe para buscar..." oninput="searchEntities('sponsor', this.value)">
                    <div id="results-sponsor" class="entity-search-results"></div>
                </div>
                <input type="hidden" id="selected-sponsor-id" value="">

                <div class="create-new-section">
                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleCreateForm('sponsor')">
                        <i class="fas fa-plus"></i> Crear Nuevo SaaS
                    </button>
                    <div id="create-form-sponsor" class="create-form-fields">
                        <div class="form-group">
                            <label>Nombre del SaaS *</label>
                            <input type="text" id="new-sponsor-name" class="form-control" value="<?= htmlspecialchars($ticket['attendee_company_name'] ?? $ticket['attendee_company'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Tagline</label>
                            <input type="text" id="new-sponsor-tagline" class="form-control" placeholder="Una frase que describe tu producto">
                        </div>
                        <div class="form-group">
                            <label>Sitio Web</label>
                            <input type="url" id="new-sponsor-website" class="form-control" placeholder="https://...">
                        </div>
                        <div class="form-group">
                            <label>Nivel</label>
                            <select id="new-sponsor-level" class="form-control">
                                <option value="bronze">Bronze</option>
                                <option value="silver">Silver</option>
                                <option value="gold">Gold</option>
                                <option value="platinum">Platinum</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-success btn-block" onclick="createAndAssign('sponsor')">
                            <i class="fas fa-check"></i> Crear SaaS y Asignar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Send Email Option -->
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-gray-200);">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" id="send-welcome-email" checked>
                    <span>Enviar email de bienvenida con datos de acceso al portal</span>
                </label>
            </div>
        </div>
        <div class="status-modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeAssignModal()">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btn-assign" onclick="assignToEntity()" disabled>
                <i class="fas fa-check"></i> Asignar
            </button>
        </div>
    </div>
</div>

<!-- QRCode.js library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
// Global variables for assignment modal (must be declared first to avoid TDZ)
let searchTimeout = null;
let currentTab = 'company';

// Generate QR code (with fallback if library fails to load)
function generateQRCode() {
    if (typeof QRCode !== 'undefined') {
        QRCode.toCanvas(document.createElement('canvas'), '<?= $ticket['code'] ?>', { width: 180 }, function(error, canvas) {
            if (!error) {
                document.getElementById('qrcode').appendChild(canvas);
            } else {
                showQRFallback();
            }
        });
    } else {
        showQRFallback();
    }
}

function showQRFallback() {
    const container = document.getElementById('qrcode');
    container.innerHTML = '<div style="padding: 1rem; text-align: center; color: var(--color-gray-500);"><i class="fas fa-qrcode" style="font-size: 4rem; opacity: 0.3;"></i><br><small>QR no disponible</small></div>';
}

// Try to generate QR code on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', generateQRCode);
} else {
    generateQRCode();
}

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
        closeAssignModal();
    }
});

// ====== Assignment Functions ======

function openAssignModal() {
    document.getElementById('assign-modal').style.display = 'flex';
}

function closeAssignModal() {
    document.getElementById('assign-modal').style.display = 'none';
    // Reset form
    document.getElementById('search-company').value = '';
    document.getElementById('search-sponsor').value = '';
    document.getElementById('results-company').innerHTML = '';
    document.getElementById('results-sponsor').innerHTML = '';
    document.getElementById('selected-company-id').value = '';
    document.getElementById('selected-sponsor-id').value = '';
    document.getElementById('create-form-company').classList.remove('active');
    document.getElementById('create-form-sponsor').classList.remove('active');
    document.getElementById('btn-assign').disabled = true;
}

function switchAssignTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.assign-tab').forEach(t => t.classList.remove('active'));
    document.querySelector(`.assign-tab[data-tab="${tab}"]`).classList.add('active');
    document.querySelectorAll('.assign-panel').forEach(p => p.classList.remove('active'));
    document.getElementById(`panel-${tab}`).classList.add('active');

    // Update button state
    updateAssignButton();
}

function searchEntities(type, query) {
    clearTimeout(searchTimeout);

    if (query.length < 2) {
        document.getElementById(`results-${type}`).innerHTML = '';
        return;
    }

    searchTimeout = setTimeout(() => {
        const url = type === 'company'
            ? '/admin/tickets/search-companies?q=' + encodeURIComponent(query)
            : '/admin/tickets/search-sponsors?q=' + encodeURIComponent(query);

        fetch(url, {
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(r => {
                if (!r.ok) {
                    throw new Error('Error de servidor');
                }
                const contentType = r.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Sesión expirada. Por favor, recarga la página.');
                }
                return r.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                renderSearchResults(type, data.results || []);
            })
            .catch(err => {
                const container = document.getElementById(`results-${type}`);
                container.innerHTML = `<div style="padding: 1rem; text-align: center; color: var(--color-danger);">${err.message}</div>`;
            });
    }, 300);
}

function renderSearchResults(type, results) {
    const container = document.getElementById(`results-${type}`);
    const selectedId = document.getElementById(`selected-${type}-id`).value;

    if (results.length === 0) {
        container.innerHTML = '<div style="padding: 1rem; text-align: center; color: var(--color-gray-500);">No se encontraron resultados</div>';
        return;
    }

    container.innerHTML = results.map(item => `
        <div class="entity-result ${item.id == selectedId ? 'selected' : ''}" onclick="selectEntity('${type}', ${item.id}, '${item.name.replace(/'/g, "\\'")}')">
            ${item.logo_url
                ? `<img src="${item.logo_url}" alt="">`
                : `<div class="placeholder-icon"><i class="fas fa-${type === 'company' ? 'building' : 'rocket'}"></i></div>`
            }
            <div>
                <strong>${item.name}</strong>
                ${item.contact_email ? `<br><small class="text-muted">${item.contact_email}</small>` : ''}
            </div>
        </div>
    `).join('');
}

function selectEntity(type, id, name) {
    document.getElementById(`selected-${type}-id`).value = id;

    // Update visual selection
    document.querySelectorAll(`#results-${type} .entity-result`).forEach(el => {
        el.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');

    // Hide create form if open
    document.getElementById(`create-form-${type}`).classList.remove('active');

    updateAssignButton();
}

function toggleCreateForm(type) {
    const form = document.getElementById(`create-form-${type}`);
    form.classList.toggle('active');

    // Clear selection when showing create form
    if (form.classList.contains('active')) {
        document.getElementById(`selected-${type}-id`).value = '';
        document.querySelectorAll(`#results-${type} .entity-result`).forEach(el => {
            el.classList.remove('selected');
        });
    }

    updateAssignButton();
}

function updateAssignButton() {
    const btn = document.getElementById('btn-assign');
    const selectedId = document.getElementById(`selected-${currentTab}-id`).value;
    const createFormActive = document.getElementById(`create-form-${currentTab}`).classList.contains('active');

    btn.disabled = !selectedId && !createFormActive;
}

function assignToEntity() {
    const selectedId = document.getElementById(`selected-${currentTab}-id`).value;
    const createFormActive = document.getElementById(`create-form-${currentTab}`).classList.contains('active');

    if (createFormActive) {
        createAndAssign(currentTab);
        return;
    }

    if (!selectedId) {
        alert('Selecciona una entidad primero');
        return;
    }

    const sendEmail = document.getElementById('send-welcome-email').checked;

    fetch('/admin/tickets/<?= $ticket['id'] ?>/assign', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>'
        },
        body: JSON.stringify({
            entity_type: currentTab,
            entity_id: parseInt(selectedId),
            send_welcome_email: sendEmail
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(d.message);
            location.reload();
        } else {
            alert(d.error || 'Error al asignar');
        }
    })
    .catch(e => alert('Error de conexión'));
}

function createAndAssign(type) {
    let data = {};

    if (type === 'company') {
        data = {
            name: document.getElementById('new-company-name').value,
            sector: document.getElementById('new-company-sector').value,
            website: document.getElementById('new-company-website').value
        };

        if (!data.name) {
            alert('El nombre de la empresa es obligatorio');
            return;
        }
    } else {
        data = {
            name: document.getElementById('new-sponsor-name').value,
            tagline: document.getElementById('new-sponsor-tagline').value,
            website: document.getElementById('new-sponsor-website').value,
            level: document.getElementById('new-sponsor-level').value
        };

        if (!data.name) {
            alert('El nombre del SaaS es obligatorio');
            return;
        }
    }

    const endpoint = type === 'company'
        ? '/admin/tickets/<?= $ticket['id'] ?>/create-company'
        : '/admin/tickets/<?= $ticket['id'] ?>/create-sponsor';

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(d.message);
            location.reload();
        } else {
            alert(d.error || 'Error al crear');
        }
    })
    .catch(e => alert('Error de conexión'));
}

function removeAssignment() {
    if (!confirm('¿Quitar la asignación de este usuario?')) return;

    fetch('/admin/tickets/<?= $ticket['id'] ?>/remove-assignment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>'
        }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            location.reload();
        } else {
            alert(d.error || 'Error al quitar asignación');
        }
    });
}
</script>
