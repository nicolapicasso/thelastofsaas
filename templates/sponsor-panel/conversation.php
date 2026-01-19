<?php
/**
 * Sponsor Panel - Conversation Template
 * TLOS - The Last of SaaS
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversacion con <?= htmlspecialchars($company['name']) ?></title>

    <!-- Fonts - TLOS Brand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Prompt:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --bg-dark: #000000;
            --bg-card: #0a0a0a;
            --text-light: #FFFFFF;
            --text-grey: #86868B;
            --border-color: rgba(255, 255, 255, 0.1);
            --success-color: #10B981;
            --info-color: #3B82F6;
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

        .conversation-layout {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .conversation-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .back-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .back-btn:hover {
            background: var(--text-light);
            color: var(--bg-dark);
        }

        .header-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .logo-placeholder {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-grey);
            font-size: 1.2rem;
        }

        .header-info h1 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .header-info p {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
            text-transform: uppercase;
        }

        .messages-container {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
        }

        .message {
            max-width: 70%;
            padding: 1.25rem 1.5rem;
            border: 1px solid var(--border-color);
            position: relative;
        }

        .message.sent {
            margin-left: auto;
            background: rgba(79, 70, 229, 0.2);
            border-color: rgba(79, 70, 229, 0.4);
        }

        .message.received {
            margin-right: auto;
            background: var(--bg-card);
        }

        .message-content {
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .message-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border-color);
        }

        .message-time {
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
        }

        .message-sender {
            font-family: var(--font-mono);
            font-size: 10px;
            color: var(--text-grey);
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .message-sender a {
            color: var(--info-color);
            text-decoration: none;
        }

        .message-sender a:hover {
            text-decoration: underline;
        }

        .message-form-container {
            background: var(--bg-card);
            border-top: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
            position: sticky;
            bottom: 0;
        }

        .message-form {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group textarea {
            width: 100%;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            padding: 1rem;
            font-family: var(--font-heading);
            font-size: 14px;
            resize: vertical;
            min-height: 100px;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: var(--text-light);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .char-count {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--text-grey);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 11px;
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

        .empty-conversation {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-grey);
        }

        .empty-conversation i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        .info-message {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid var(--info-color);
            padding: 1rem 1.5rem;
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--info-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .warning-message {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid #F59E0B;
            padding: 1rem 1.5rem;
            font-family: var(--font-mono);
            font-size: 12px;
            color: #F59E0B;
            text-align: center;
        }

        @media (max-width: 768px) {
            .message { max-width: 85%; }
            .messages-container { padding: 1rem; }
            .conversation-header { padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="conversation-layout">
        <!-- Header -->
        <header class="conversation-header">
            <a href="/sponsor/mensajes/<?= $event['id'] ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <?php if (!empty($company['logo_url'])): ?>
                <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="" class="header-logo">
            <?php else: ?>
                <div class="logo-placeholder"><i class="fas fa-building"></i></div>
            <?php endif; ?>
            <div class="header-info">
                <h1><?= htmlspecialchars($company['name']) ?></h1>
                <p><?= htmlspecialchars($event['name']) ?></p>
            </div>
        </header>

        <!-- Messages -->
        <div class="messages-container" id="messagesContainer">
            <?php if (empty($messages)): ?>
                <div class="empty-conversation">
                    <i class="fas fa-comments"></i>
                    <p>Inicia la conversacion enviando un mensaje.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <?php $isMine = ($msg['sender_type'] === 'sponsor' && $msg['sender_id'] == $sponsor['id']); ?>
                    <div class="message <?= $isMine ? 'sent' : 'received' ?>">
                        <div class="message-content"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                        <div class="message-meta">
                            <span class="message-time"><?= date('d/m/Y H:i', strtotime($msg['sent_at'])) ?></span>
                            <?php if (!$isMine && ($msg['sender_name'] || $msg['sender_email'] || $msg['sender_phone'])): ?>
                                <div class="message-sender">
                                    <?php if ($msg['sender_name']): ?>
                                        <span><?= htmlspecialchars($msg['sender_name']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($msg['sender_email']): ?>
                                        <a href="mailto:<?= htmlspecialchars($msg['sender_email']) ?>"><?= htmlspecialchars($msg['sender_email']) ?></a>
                                    <?php endif; ?>
                                    <?php if ($msg['sender_phone']): ?>
                                        <a href="tel:<?= htmlspecialchars($msg['sender_phone']) ?>"><?= htmlspecialchars($msg['sender_phone']) ?></a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Message Form -->
        <div class="message-form-container">
            <?php if (!$canSend['can_send'] && !($canSend['is_reply'] ?? false)): ?>
                <div class="warning-message">
                    <i class="fas fa-info-circle"></i> Ya has enviado un mensaje a esta empresa. Podras responder cuando ellos te respondan.
                </div>
            <?php else: ?>
                <?php if (empty($messages)): ?>
                    <div class="info-message">
                        <i class="fas fa-info-circle"></i>
                        <span>Puedes enviar un mensaje inicial. Si la empresa responde, podras continuar la conversacion.</span>
                    </div>
                <?php endif; ?>

                <form class="message-form" id="messageForm">
                    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                    <input type="hidden" name="company_id" value="<?= $company['id'] ?>">
                    <?php if (!empty($messages)): ?>
                        <?php
                        // Find last received message to reply to
                        $lastReceived = null;
                        foreach (array_reverse($messages) as $msg) {
                            if ($msg['sender_type'] === 'company') {
                                $lastReceived = $msg;
                                break;
                            }
                        }
                        if ($lastReceived):
                        ?>
                            <input type="hidden" name="message_id" value="<?= $lastReceived['id'] ?>">
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="form-group">
                        <textarea name="message" id="messageText" placeholder="Escribe tu mensaje..." maxlength="2000" required></textarea>
                    </div>

                    <div class="form-actions">
                        <span class="char-count"><span id="charCount">0</span>/2000</span>
                        <button type="submit" class="btn btn-primary" id="sendBtn">
                            <i class="fas fa-paper-plane"></i> ENVIAR
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const form = document.getElementById('messageForm');
        const textarea = document.getElementById('messageText');
        const charCount = document.getElementById('charCount');
        const sendBtn = document.getElementById('sendBtn');
        const messagesContainer = document.getElementById('messagesContainer');

        if (textarea) {
            textarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }

        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ENVIANDO...';

                try {
                    // Determine endpoint based on whether this is a reply
                    const isReply = formData.has('message_id') && formData.get('message_id');
                    const endpoint = isReply ? '/sponsor/mensaje/responder' : '/sponsor/mensaje/enviar';

                    const response = await fetch(endpoint, {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Reload page to show new message
                        window.location.reload();
                    } else {
                        alert(data.error || 'Error al enviar el mensaje');
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> ENVIAR';
                    }
                } catch (error) {
                    alert('Error de conexion');
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> ENVIAR';
                }
            });
        }

        // Scroll to bottom on load
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    </script>
</body>
</html>
