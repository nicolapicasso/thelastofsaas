<?php
/**
 * Ticket Registration Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - <?= htmlspecialchars($event['name'] ?? '') ?></title>

    <!-- Fonts - TLOS Brand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ============================================
           TLOS - Registration Page Styles
           ============================================ */
        :root {
            --bg-dark: #000000;
            --bg-card: #0a0a0a;
            --bg-input: #111111;
            --text-light: #FFFFFF;
            --text-grey: #86868B;
            --border-color: rgba(255, 255, 255, 0.1);
            --success-color: #10B981;
            --error-color: #EF4444;
            --warning-color: #F59E0B;
            --primary-color: #FFFFFF;
            --font-heading: 'Montserrat', sans-serif;
            --font-mono: 'Roboto Mono', monospace;
            --font-accent: 'Prompt', sans-serif;
            --transition: all 0.3s ease-in-out;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-heading);
            background: var(--bg-dark);
            color: var(--text-light);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Hero Section */
        .register-hero {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 2rem 0;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-grey);
            text-decoration: none;
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--text-light);
        }

        .register-hero h1 {
            font-size: clamp(24px, 4vw, 36px);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin-bottom: 0.5rem;
        }

        .register-hero .lead {
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid var(--warning-color);
            color: var(--warning-color);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }

        /* Form Section */
        .register-form-section {
            padding: 3rem 0;
        }

        .register-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 3rem;
            align-items: start;
        }

        .register-main {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 2.5rem;
        }

        /* Form Sections */
        .form-section {
            margin-bottom: 2.5rem;
            padding-bottom: 2.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-section h2 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .form-section h2 small {
            font-weight: 400;
            color: var(--text-grey);
            font-size: 12px;
        }

        .step-number {
            width: 32px;
            height: 32px;
            background: var(--text-light);
            color: var(--bg-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-accent);
            font-weight: 700;
            font-size: 14px;
        }

        /* Ticket Types */
        .ticket-types {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .ticket-type-option {
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            position: relative;
            background: var(--bg-dark);
        }

        .ticket-type-option:hover {
            border-color: var(--text-light);
        }

        .ticket-type-option.selected {
            border-color: var(--text-light);
            background: rgba(255, 255, 255, 0.02);
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
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .ticket-type-info p {
            margin: 0.5rem 0 0;
            font-size: 13px;
            color: var(--text-grey);
        }

        .ticket-type-price {
            text-align: right;
        }

        .ticket-type-price span {
            display: block;
            font-family: var(--font-accent);
            font-size: 24px;
            font-weight: 700;
        }

        .ticket-type-price .original-price {
            font-size: 14px;
            text-decoration: line-through;
            color: var(--text-grey);
        }

        .ticket-type-price .free-badge {
            background: var(--success-color);
            color: var(--bg-dark);
            padding: 0.5rem 1rem;
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
        }

        .ticket-type-price small {
            font-size: 11px;
            color: var(--text-grey);
            font-family: var(--font-mono);
        }

        .check-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--success-color);
            font-size: 1.5rem;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .ticket-type-option.selected .check-icon {
            opacity: 1;
        }

        /* Form Fields */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-row:last-child {
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-grey);
            margin-bottom: 0.75rem;
        }

        .form-label.required::after {
            content: ' *';
            color: var(--error-color);
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.25rem;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            font-family: var(--font-heading);
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control::placeholder {
            color: var(--text-grey);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--text-light);
            background: var(--bg-dark);
        }

        .form-text {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
            margin-top: 0.5rem;
        }

        /* Submit Section */
        .form-submit {
            text-align: center;
        }

        .form-legal {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
            margin-top: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-legal a {
            color: var(--text-light);
            text-decoration: underline;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1.25rem 3rem;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-decoration: none;
            border: 2px solid transparent;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--text-light);
            color: var(--bg-dark);
            border-color: var(--text-light);
        }

        .btn-primary:hover {
            background: transparent;
            color: var(--text-light);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-lg {
            padding: 1.5rem 4rem;
            font-size: 16px;
        }

        .btn-outline {
            background: transparent;
            color: var(--text-light);
            border-color: var(--text-light);
            padding: 1rem 1.5rem;
        }

        .btn-outline:hover {
            background: var(--text-light);
            color: var(--bg-dark);
        }

        .code-message {
            font-family: var(--font-mono);
            font-size: 12px;
            padding: 0.75rem 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .code-message.success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }

        .code-message.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }

        /* Sponsor Invitation Block */
        .sponsor-invitation {
            background: linear-gradient(135deg, var(--bg-card) 0%, rgba(255, 255, 255, 0.03) 100%);
            border: 2px solid var(--border-color);
            padding: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .sponsor-invitation-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            background: var(--text-light);
            padding: 1rem;
            margin: 0 auto 1.5rem;
            display: block;
        }

        .sponsor-invitation-text {
            font-family: var(--font-heading);
            font-size: clamp(18px, 3vw, 24px);
            font-weight: 600;
            line-height: 1.4;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .sponsor-invitation-text .sponsor-name {
            display: block;
            font-size: clamp(24px, 4vw, 32px);
            font-weight: 800;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .sponsor-invitation-text .invitation-phrase {
            display: block;
            font-size: clamp(14px, 2vw, 16px);
            font-weight: 400;
            color: var(--text-grey);
            margin-bottom: 0.5rem;
        }

        .sponsor-invitation-text .event-name {
            display: block;
            font-size: clamp(20px, 3.5vw, 28px);
            font-weight: 700;
            color: var(--success-color);
        }

        /* Summary Sidebar */
        .register-sidebar {
            position: sticky;
            top: 2rem;
        }

        .summary-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 2rem;
        }

        .summary-card h3 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-event {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-event img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

        .summary-event strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .summary-event span {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
        }

        .summary-sponsor {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-sponsor small {
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .sponsor-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        .sponsor-info img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            background: var(--text-light);
            padding: 0.25rem;
        }

        .sponsor-info span {
            font-size: 14px;
            font-weight: 600;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            padding-top: 1.5rem;
            margin-top: 1rem;
            border-top: 2px solid var(--border-color);
            font-family: var(--font-accent);
            font-size: 24px;
            font-weight: 700;
        }

        /* Error Modal */
        .error-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .error-modal-overlay.active {
            display: flex;
        }

        .error-modal {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            max-width: 450px;
            width: 100%;
            padding: 2rem;
            text-align: center;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-modal-icon {
            font-size: 48px;
            margin-bottom: 1.5rem;
        }

        .error-modal-icon.error {
            color: var(--error-color);
        }

        .error-modal-icon.warning {
            color: #f59e0b;
        }

        .error-modal-icon.info {
            color: var(--success-color);
        }

        .error-modal-title {
            font-family: var(--font-heading);
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .error-modal-message {
            color: var(--text-grey);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .error-modal-btn {
            background: transparent;
            border: 1px solid var(--text-light);
            color: var(--text-light);
            padding: 0.75rem 2rem;
            font-family: var(--font-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: var(--transition);
        }

        .error-modal-btn:hover {
            background: var(--text-light);
            color: var(--bg-dark);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .register-layout {
                grid-template-columns: 1fr;
            }

            .register-sidebar {
                position: static;
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 0 1rem;
            }

            .register-main {
                padding: 1.5rem;
            }

            .ticket-type-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .ticket-type-price {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <section class="register-hero">
        <div class="container">
            <a href="/eventos/<?= htmlspecialchars($event['slug'] ?? '') ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> VOLVER AL EVENTO
            </a>
            <h1>REGISTRO: <?= htmlspecialchars($event['name'] ?? '') ?></h1>
            <p class="lead"><?= date('d/m/Y', strtotime($event['start_date'] ?? 'now')) ?> · <?= htmlspecialchars($event['location'] ?? '') ?></p>
        </div>
    </section>

    <section class="register-form-section">
        <div class="container">
            <?php if (isset($_GET['cancelled'])): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    EL PAGO FUE CANCELADO. PUEDES VOLVER A INTENTARLO.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i>
                    HUBO UN ERROR CON EL PAGO. POR FAVOR, INTENTALO DE NUEVO.
                </div>
            <?php endif; ?>

            <!-- Sponsor Invitation Block (visible when sponsor is detected) -->
            <?php if (!empty($sponsor)): ?>
            <div class="sponsor-invitation" id="sponsorInvitationBlock">
                <?php if (!empty($sponsor['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="<?= htmlspecialchars($sponsor['name']) ?>" class="sponsor-invitation-logo">
                <?php endif; ?>
                <div class="sponsor-invitation-text">
                    <span class="sponsor-name"><?= htmlspecialchars($sponsor['name']) ?></span>
                    <span class="invitation-phrase">tiene el placer de invitarte a</span>
                    <span class="event-name"><?= htmlspecialchars($event['name']) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Dynamic Sponsor Invitation Block (shown when code is validated via JS) -->
            <div class="sponsor-invitation" id="dynamicSponsorInvitation" style="display: none;">
                <img src="" alt="" class="sponsor-invitation-logo" id="dynamicSponsorLogo" style="display: none;">
                <div class="sponsor-invitation-text">
                    <span class="sponsor-name" id="dynamicSponsorName"></span>
                    <span class="invitation-phrase">tiene el placer de invitarte a</span>
                    <span class="event-name"><?= htmlspecialchars($event['name']) ?></span>
                </div>
            </div>

            <div class="register-layout">
                <!-- Form -->
                <div class="register-main">
                    <form id="registerForm" method="POST" class="register-form">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <?php if (!empty($sponsorCode)): ?>
                            <input type="hidden" name="sponsor_code" value="<?= htmlspecialchars($sponsorCode) ?>">
                        <?php endif; ?>

                        <!-- Ticket Type Selection -->
                        <div class="form-section">
                            <h2><span class="step-number">1</span> TIPO DE ENTRADA</h2>

                            <div class="ticket-types">
                                <?php if (!empty($ticketTypes)): ?>
                                    <?php foreach ($ticketTypes as $index => $type): ?>
                                        <label class="ticket-type-option <?= $index === 0 ? 'selected' : '' ?>">
                                            <input type="radio" name="ticket_type_id" value="<?= $type['id'] ?>" <?= $index === 0 ? 'checked' : '' ?> data-price="<?= $type['price'] ?? 0 ?>">
                                            <div class="ticket-type-content">
                                                <div class="ticket-type-info">
                                                    <strong><?= htmlspecialchars($type['name'] ?? '') ?></strong>
                                                    <?php if (!empty($type['description'])): ?>
                                                        <p><?= htmlspecialchars($type['description']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ticket-type-price">
                                                    <?php if (!empty($sponsor) && ($type['price'] ?? 0) > 0): ?>
                                                        <span class="original-price"><?= number_format($type['price'], 2) ?> EUR</span>
                                                        <span class="free-badge">GRATIS</span>
                                                        <small>Cortesia de <?= htmlspecialchars($sponsor['name'] ?? '') ?></small>
                                                    <?php elseif (($type['price'] ?? 0) > 0): ?>
                                                        <span><?= number_format($type['price'], 2) ?> EUR</span>
                                                    <?php else: ?>
                                                        <span class="free-badge">GRATIS</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <i class="fas fa-check-circle check-icon"></i>
                                        </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p style="color: var(--text-grey);">No hay tipos de entrada disponibles.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Attendee Info -->
                        <div class="form-section">
                            <h2><span class="step-number">2</span> TUS DATOS</h2>

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
                                    <small class="form-text">Recibiras la confirmacion en este email</small>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Telefono</label>
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

                        <?php if (empty($sponsor)): ?>
                        <!-- Sponsor Code (optional) -->
                        <div class="form-section">
                            <h2><span class="step-number">3</span> CODIGO DE DESCUENTO <small>(opcional)</small></h2>
                            <div class="form-group">
                                <label class="form-label">Tienes un codigo de sponsor?</label>
                                <div style="display: flex; gap: 1rem; max-width: 450px;">
                                    <input type="text" name="sponsor_code" id="sponsorCode" class="form-control" placeholder="Introduce tu codigo" style="flex: 1;">
                                    <button type="button" id="applyCodeBtn" class="btn btn-outline" style="white-space: nowrap;">
                                        <span class="btn-text">APLICAR</span>
                                        <span class="btn-loading" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                                    </button>
                                </div>
                                <div id="codeMessage" class="code-message" style="margin-top: 0.75rem; display: none;"></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Submit -->
                        <div class="form-section form-submit">
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-lg">
                                <span class="btn-text">CONFIRMAR REGISTRO</span>
                                <span class="btn-loading" style="display: none;"><i class="fas fa-spinner fa-spin"></i> PROCESANDO...</span>
                            </button>
                            <p class="form-legal">
                                Al registrarte aceptas nuestros <a href="/terminos" target="_blank">terminos y condiciones</a>
                                y <a href="/privacidad" target="_blank">politica de privacidad</a>.
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Summary Sidebar -->
                <aside class="register-sidebar">
                    <div class="summary-card">
                        <h3>RESUMEN</h3>

                        <div class="summary-event">
                            <?php if (!empty($event['featured_image'])): ?>
                                <img src="<?= htmlspecialchars($event['featured_image']) ?>" alt="">
                            <?php endif; ?>
                            <div>
                                <strong><?= htmlspecialchars($event['name'] ?? '') ?></strong>
                                <span><?= date('d M Y', strtotime($event['start_date'] ?? 'now')) ?></span>
                            </div>
                        </div>

                        <?php if (!empty($sponsor)): ?>
                        <div class="summary-sponsor">
                            <small>INVITACION DE</small>
                            <div class="sponsor-info">
                                <?php if (!empty($sponsor['logo_url'])): ?>
                                    <img src="<?= htmlspecialchars($sponsor['logo_url']) ?>" alt="">
                                <?php endif; ?>
                                <span><?= htmlspecialchars($sponsor['name'] ?? '') ?></span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="summary-line">
                            <span>ENTRADA</span>
                            <span id="summaryTicketType">-</span>
                        </div>

                        <div class="summary-total">
                            <span>TOTAL</span>
                            <span id="summaryTotal"><?= !empty($sponsor) ? 'GRATIS' : '-' ?></span>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Error Modal -->
    <div class="error-modal-overlay" id="errorModal">
        <div class="error-modal">
            <div class="error-modal-icon" id="errorModalIcon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3 class="error-modal-title" id="errorModalTitle">Error</h3>
            <p class="error-modal-message" id="errorModalMessage"></p>
            <button type="button" class="error-modal-btn" onclick="closeErrorModal()">ENTENDIDO</button>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registerForm');
        const ticketOptions = document.querySelectorAll('.ticket-type-option');
        const submitBtn = document.getElementById('submitBtn');
        let hasSponsor = <?= !empty($sponsor) ? 'true' : 'false' ?>;
        let appliedDiscount = null;
        let appliedSponsorName = null;

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

                if (hasSponsor || appliedDiscount === 100 || price === 0) {
                    document.getElementById('summaryTotal').textContent = 'GRATIS';
                } else if (appliedDiscount > 0) {
                    const discountedPrice = price * (1 - appliedDiscount / 100);
                    document.getElementById('summaryTotal').textContent = discountedPrice.toFixed(2) + ' EUR';
                } else {
                    document.getElementById('summaryTotal').textContent = price.toFixed(2) + ' EUR';
                }
            }
        }

        // Initial update
        updateSummary();

        // Apply discount code button
        const applyCodeBtn = document.getElementById('applyCodeBtn');
        const sponsorCodeInput = document.getElementById('sponsorCode');
        const codeMessage = document.getElementById('codeMessage');

        if (applyCodeBtn) {
            applyCodeBtn.addEventListener('click', function() {
                const code = sponsorCodeInput.value.trim();
                if (!code) {
                    showCodeMessage('Introduce un codigo', 'error');
                    return;
                }

                const ticketTypeId = document.querySelector('input[name="ticket_type_id"]:checked')?.value;

                applyCodeBtn.querySelector('.btn-text').style.display = 'none';
                applyCodeBtn.querySelector('.btn-loading').style.display = 'inline';
                applyCodeBtn.disabled = true;

                fetch('/eventos/<?= htmlspecialchars($event['slug'] ?? '') ?>/validar-codigo', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: '_csrf_token=' + encodeURIComponent(document.querySelector('[name="_csrf_token"]').value) +
                          '&code=' + encodeURIComponent(code) +
                          (ticketTypeId ? '&ticket_type_id=' + ticketTypeId : '')
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        appliedDiscount = data.discount || 0;
                        appliedSponsorName = data.sponsor_name || '';
                        if (data.is_free || appliedDiscount === 100) {
                            hasSponsor = true;
                            showCodeMessage('Codigo aplicado - Entrada GRATIS' + (appliedSponsorName ? ' (cortesia de ' + appliedSponsorName + ')' : ''), 'success');
                        } else if (appliedDiscount > 0) {
                            showCodeMessage('Codigo aplicado - ' + appliedDiscount + '% de descuento', 'success');
                        } else {
                            showCodeMessage('Codigo valido', 'success');
                        }
                        sponsorCodeInput.readOnly = true;
                        applyCodeBtn.style.display = 'none';
                        updateSummary();

                        // Show dynamic sponsor invitation block
                        if (appliedSponsorName) {
                            showSponsorInvitation(appliedSponsorName, data.sponsor_logo || '');
                        }
                    } else {
                        showCodeMessage(data.error || 'Codigo no valido', 'error');
                    }
                })
                .catch(err => {
                    showCodeMessage('Error al validar el codigo', 'error');
                })
                .finally(() => {
                    applyCodeBtn.querySelector('.btn-text').style.display = 'inline';
                    applyCodeBtn.querySelector('.btn-loading').style.display = 'none';
                    applyCodeBtn.disabled = false;
                });
            });
        }

        function showCodeMessage(message, type) {
            codeMessage.textContent = message;
            codeMessage.className = 'code-message ' + type;
            codeMessage.style.display = 'block';
        }

        function showSponsorInvitation(sponsorName, sponsorLogo) {
            const invitationBlock = document.getElementById('dynamicSponsorInvitation');
            const sponsorNameEl = document.getElementById('dynamicSponsorName');
            const sponsorLogoEl = document.getElementById('dynamicSponsorLogo');

            if (invitationBlock && sponsorName) {
                sponsorNameEl.textContent = sponsorName;

                if (sponsorLogo) {
                    sponsorLogoEl.src = sponsorLogo;
                    sponsorLogoEl.alt = sponsorName;
                    sponsorLogoEl.style.display = 'block';
                } else {
                    sponsorLogoEl.style.display = 'none';
                }

                invitationBlock.style.display = 'block';
                // Smooth scroll to the invitation block
                invitationBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            submitBtn.querySelector('.btn-text').style.display = 'none';
            submitBtn.querySelector('.btn-loading').style.display = 'inline';
            submitBtn.disabled = true;

            fetch(form.action || window.location.href, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(r => {
                return r.text().then(text => {
                    console.log('Server response:', text);
                    try {
                        const data = JSON.parse(text);
                        if (!r.ok) {
                            throw new Error(data.error || 'Error del servidor: ' + r.status);
                        }
                        return data;
                    } catch (e) {
                        console.error('Response was not JSON:', text);
                        throw new Error('Error del servidor: ' + text.substring(0, 200));
                    }
                });
            })
            .then(data => {
                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    showErrorModal(data.error || 'Error al procesar el registro');
                    submitBtn.querySelector('.btn-text').style.display = 'inline';
                    submitBtn.querySelector('.btn-loading').style.display = 'none';
                    submitBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error('Registration error:', err);
                showErrorModal(err.message);
                submitBtn.querySelector('.btn-text').style.display = 'inline';
                submitBtn.querySelector('.btn-loading').style.display = 'none';
                submitBtn.disabled = false;
            });
        });
    });

    // Error Modal Functions
    function showErrorModal(message, type = 'error') {
        const modal = document.getElementById('errorModal');
        const icon = document.getElementById('errorModalIcon');
        const title = document.getElementById('errorModalTitle');
        const msg = document.getElementById('errorModalMessage');

        // Parse message if it's JSON
        let displayMessage = message;
        let displayTitle = 'Error';
        let iconClass = 'error';
        let iconName = 'fa-exclamation-circle';

        // Check for specific error messages
        if (message.includes('Ya existe un registro') || message.includes('already registered')) {
            displayTitle = 'Ya estas registrado';
            displayMessage = 'Este email ya tiene un registro para este evento. Revisa tu correo para encontrar tu entrada.';
            iconClass = 'warning';
            iconName = 'fa-user-check';
        } else if (message.includes('Token de seguridad')) {
            displayTitle = 'Sesion expirada';
            displayMessage = 'Tu sesion ha expirado. Por favor, recarga la pagina e intenta de nuevo.';
            iconClass = 'warning';
            iconName = 'fa-clock';
        } else if (message.includes('No quedan entradas')) {
            displayTitle = 'Entradas agotadas';
            displayMessage = 'Lo sentimos, no quedan entradas disponibles de este tipo.';
            iconClass = 'warning';
            iconName = 'fa-ticket-alt';
        } else {
            // Clean up technical error messages
            displayMessage = message.replace(/Error: |Error del servidor: /g, '').replace(/\{.*\}/g, '').trim();
            if (displayMessage.length > 150) {
                displayMessage = 'Ha ocurrido un error al procesar tu registro. Por favor, intenta de nuevo.';
            }
        }

        icon.className = 'error-modal-icon ' + iconClass;
        icon.innerHTML = '<i class="fas ' + iconName + '"></i>';
        title.textContent = displayTitle;
        msg.textContent = displayMessage;
        modal.classList.add('active');
    }

    function closeErrorModal() {
        document.getElementById('errorModal').classList.remove('active');
    }

    // Close modal on overlay click
    document.getElementById('errorModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeErrorModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeErrorModal();
        }
    });
    </script>
</body>
</html>
