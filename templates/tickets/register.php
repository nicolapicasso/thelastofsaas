<?php
/**
 * Ticket Registration Template
 * TLOS - The Last of SaaS
 */
?>

<section class="register-hero">
    <div class="container">
        <a href="/eventos/<?= $event['slug'] ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al evento
        </a>
        <h1>Registro: <?= htmlspecialchars($event['name']) ?></h1>
        <p class="lead"><?= date('d/m/Y', strtotime($event['start_date'])) ?> · <?= htmlspecialchars($event['location']) ?></p>
    </div>
</section>

<section class="register-form-section">
    <div class="container">
        <?php if (isset($_GET['cancelled'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                El pago fue cancelado. Puedes volver a intentarlo.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i>
                Hubo un error con el pago. Por favor, inténtalo de nuevo.
            </div>
        <?php endif; ?>

        <div class="register-layout">
            <!-- Form -->
            <div class="register-main">
                <form id="registerForm" method="POST" class="register-form">
                    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
                    <?php if ($sponsorCode): ?>
                        <input type="hidden" name="sponsor_code" value="<?= htmlspecialchars($sponsorCode) ?>">
                    <?php endif; ?>

                    <!-- Ticket Type Selection -->
                    <div class="form-section">
                        <h2><span class="step-number">1</span> Tipo de entrada</h2>

                        <div class="ticket-types">
                            <?php foreach ($ticketTypes as $index => $type): ?>
                                <label class="ticket-type-option <?= $index === 0 ? 'selected' : '' ?>">
                                    <input type="radio" name="ticket_type_id" value="<?= $type['id'] ?>" <?= $index === 0 ? 'checked' : '' ?> data-price="<?= $type['price'] ?>">
                                    <div class="ticket-type-content">
                                        <div class="ticket-type-info">
                                            <strong><?= htmlspecialchars($type['name']) ?></strong>
                                            <?php if ($type['description']): ?>
                                                <p><?= htmlspecialchars($type['description']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ticket-type-price">
                                            <?php if ($sponsor && $type['price'] > 0): ?>
                                                <span class="original-price"><?= number_format($type['price'], 2) ?> €</span>
                                                <span class="free-badge">GRATIS</span>
                                                <small>Cortesía de <?= htmlspecialchars($sponsor['name']) ?></small>
                                            <?php elseif ($type['price'] > 0): ?>
                                                <span><?= number_format($type['price'], 2) ?> €</span>
                                            <?php else: ?>
                                                <span class="free-badge">GRATIS</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <i class="fas fa-check-circle check-icon"></i>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Attendee Info -->
                    <div class="form-section">
                        <h2><span class="step-number">2</span> Tus datos</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Nombre completo</label>
                                <input type="text" name="attendee_name" class="form-control" required placeholder="Tu nombre y apellidos">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Email</label>
                                <input type="email" name="attendee_email" class="form-control" required placeholder="tu@email.com">
                                <small class="form-text">Recibirás la confirmación en este email</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" name="attendee_phone" class="form-control" placeholder="+34 600 000 000">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Empresa</label>
                                <input type="text" name="attendee_company" class="form-control" placeholder="Nombre de tu empresa">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Cargo</label>
                                <input type="text" name="attendee_position" class="form-control" placeholder="Tu cargo">
                            </div>
                        </div>
                    </div>

                    <?php if (!$sponsor): ?>
                    <!-- Sponsor Code (optional) -->
                    <div class="form-section">
                        <h2><span class="step-number">3</span> Código de descuento <small>(opcional)</small></h2>
                        <div class="form-group">
                            <label class="form-label">¿Tienes un código de sponsor?</label>
                            <input type="text" name="sponsor_code" class="form-control" placeholder="Introduce tu código" style="max-width: 300px;">
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Submit -->
                    <div class="form-section form-submit">
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-lg">
                            <span class="btn-text">Confirmar registro</span>
                            <span class="btn-loading" style="display: none;"><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                        </button>
                        <p class="form-legal">
                            Al registrarte aceptas nuestros <a href="/terminos" target="_blank">términos y condiciones</a>
                            y <a href="/privacidad" target="_blank">política de privacidad</a>.
                        </p>
                    </div>
                </form>
            </div>

            <!-- Summary Sidebar -->
            <aside class="register-sidebar">
                <div class="summary-card">
                    <h3>Resumen</h3>

                    <div class="summary-event">
                        <?php if ($event['featured_image']): ?>
                            <img src="<?= htmlspecialchars($event['featured_image']) ?>" alt="">
                        <?php endif; ?>
                        <div>
                            <strong><?= htmlspecialchars($event['name']) ?></strong>
                            <span><?= date('d M Y', strtotime($event['start_date'])) ?></span>
                        </div>
                    </div>

                    <?php if ($sponsor): ?>
                    <div class="summary-sponsor">
                        <small>Invitación de</small>
                        <div class="sponsor-info">
                            <?php if ($sponsor['logo_url']): ?>
                                <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="">
                            <?php endif; ?>
                            <span><?= htmlspecialchars($sponsor['name']) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="summary-line">
                        <span>Entrada</span>
                        <span id="summaryTicketType">-</span>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span id="summaryTotal"><?= $sponsor ? 'GRATIS' : '-' ?></span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<style>
.register-hero {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 2rem 0;
}
.back-link {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}
.back-link:hover {
    color: white;
}
.register-hero h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
.register-hero .lead {
    opacity: 0.9;
}

.register-form-section {
    padding: 3rem 0;
    background: var(--bg-secondary);
}
.register-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
    align-items: start;
}
.register-main {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}
.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.form-section h2 {
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.form-section h2 small {
    font-weight: normal;
    color: var(--text-muted);
}
.step-number {
    width: 28px;
    height: 28px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
}

.ticket-types {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.ticket-type-option {
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    position: relative;
}
.ticket-type-option:hover {
    border-color: var(--primary-color);
}
.ticket-type-option.selected {
    border-color: var(--primary-color);
    background: rgba(79, 70, 229, 0.05);
}
.ticket-type-option input {
    display: none;
}
.ticket-type-content {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.ticket-type-info strong {
    display: block;
    font-size: 1.1rem;
}
.ticket-type-info p {
    margin: 0.25rem 0 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
}
.ticket-type-price {
    text-align: right;
}
.ticket-type-price span {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
}
.ticket-type-price .original-price {
    font-size: 0.9rem;
    text-decoration: line-through;
    color: var(--text-muted);
}
.ticket-type-price .free-badge {
    background: var(--success-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
}
.ticket-type-price small {
    font-size: 0.75rem;
    color: var(--text-secondary);
}
.check-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary-color);
    font-size: 1.25rem;
    opacity: 0;
    transition: opacity 0.2s;
}
.ticket-type-option.selected .check-icon {
    opacity: 1;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}
.form-group {
    margin-bottom: 0;
}
.form-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
}
.form-label.required::after {
    content: '*';
    color: var(--danger-color);
    margin-left: 0.25rem;
}
.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
.form-text {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

.form-submit {
    text-align: center;
}
.form-legal {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 1rem;
}
.form-legal a {
    color: var(--primary-color);
}

.summary-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    position: sticky;
    top: 2rem;
}
.summary-card h3 {
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border-color);
}
.summary-event {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}
.summary-event img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}
.summary-event strong {
    display: block;
}
.summary-event span {
    font-size: 0.85rem;
    color: var(--text-secondary);
}
.summary-sponsor {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}
.summary-sponsor small {
    color: var(--text-muted);
}
.sponsor-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
}
.sponsor-info img {
    width: 24px;
    height: 24px;
    object-fit: contain;
}
.summary-line {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    font-size: 0.95rem;
}
.summary-total {
    display: flex;
    justify-content: space-between;
    padding-top: 1rem;
    margin-top: 0.5rem;
    border-top: 2px solid var(--border-color);
    font-size: 1.25rem;
    font-weight: 700;
}

@media (max-width: 992px) {
    .register-layout {
        grid-template-columns: 1fr;
    }
    .summary-card {
        position: static;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const ticketOptions = document.querySelectorAll('.ticket-type-option');
    const submitBtn = document.getElementById('submitBtn');
    const hasSponsor = <?= $sponsor ? 'true' : 'false' ?>;

    // Ticket type selection
    ticketOptions.forEach(option => {
        option.addEventListener('click', function() {
            ticketOptions.forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input').checked = true;
            updateSummary();
        });
    });

    function updateSummary() {
        const selected = document.querySelector('input[name="ticket_type_id"]:checked');
        if (selected) {
            const option = selected.closest('.ticket-type-option');
            const name = option.querySelector('.ticket-type-info strong').textContent;
            const price = parseFloat(selected.dataset.price);

            document.getElementById('summaryTicketType').textContent = name;

            if (hasSponsor || price === 0) {
                document.getElementById('summaryTotal').textContent = 'GRATIS';
            } else {
                document.getElementById('summaryTotal').textContent = price.toFixed(2) + ' €';
            }
        }
    }

    // Initial update
    updateSummary();

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        submitBtn.querySelector('.btn-text').style.display = 'none';
        submitBtn.querySelector('.btn-loading').style.display = 'inline';
        submitBtn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            } else {
                alert(data.error || 'Error al procesar el registro');
                submitBtn.querySelector('.btn-text').style.display = 'inline';
                submitBtn.querySelector('.btn-loading').style.display = 'none';
                submitBtn.disabled = false;
            }
        })
        .catch(err => {
            alert('Error de conexión');
            submitBtn.querySelector('.btn-text').style.display = 'inline';
            submitBtn.querySelector('.btn-loading').style.display = 'none';
            submitBtn.disabled = false;
        });
    });
});
</script>
