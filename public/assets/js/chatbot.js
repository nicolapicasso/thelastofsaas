/**
 * Loyalty Master Chatbot
 * Interactive AI assistant for Omniwallet
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        apiEndpoint: '/api/chatbot',
        storageKey: 'loyaltyMasterHistory',
        maxHistory: 20,
        typingDelay: 500
    };

    // Robot SVG icon (animated)
    const ROBOT_SVG = `<span class="robot-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
        <circle class="robot-antenna" cx="32" cy="6" r="4"/>
        <rect class="robot-head" x="29" y="10" width="6" height="8" rx="2"/>
        <rect class="robot-head" x="12" y="18" width="40" height="32" rx="8"/>
        <circle class="robot-eye robot-eye-left" cx="24" cy="34" r="6"/>
        <circle class="robot-eye robot-eye-right" cx="40" cy="34" r="6"/>
        <circle fill="white" cx="22" cy="32" r="2" opacity="0.6"/>
        <circle fill="white" cx="38" cy="32" r="2" opacity="0.6"/>
        <rect class="robot-head" x="24" y="44" width="16" height="3" rx="1.5"/>
        <rect class="robot-head" x="6" y="28" width="6" height="12" rx="2"/>
        <rect class="robot-head" x="52" y="28" width="6" height="12" rx="2"/>
    </svg></span>`;

    // User avatar SVG
    const USER_SVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>`;

    // State
    let isOpen = false;
    let isLoading = false;
    let conversationHistory = [];

    // DOM Elements
    let widget, bubble, bubbleClose, modal, closeBtn, messagesContainer, form, input, sendBtn;

    /**
     * Initialize chatbot
     */
    function init() {
        // Get DOM elements
        widget = document.getElementById('loyalty-master-chatbot');
        if (!widget) return;

        bubble = document.getElementById('chatbot-bubble');
        bubbleClose = document.getElementById('bubble-close');
        modal = document.getElementById('chatbot-modal');
        closeBtn = document.getElementById('chatbot-close');
        messagesContainer = document.getElementById('chatbot-messages');
        form = document.getElementById('chatbot-form');
        input = document.getElementById('chatbot-input');
        sendBtn = document.getElementById('chatbot-send');

        // Load conversation history from localStorage
        loadHistory();

        // Bind events
        bindEvents();

        console.log('Loyalty Master chatbot initialized');
    }

    /**
     * Bind event listeners
     */
    function bindEvents() {
        // Open chat
        bubble.addEventListener('click', function(e) {
            if (e.target.closest('.bubble-close')) {
                e.stopPropagation();
                return;
            }
            if (!isOpen) {
                openChat();
            }
        });

        // Close from bubble
        if (bubbleClose) {
            bubbleClose.addEventListener('click', function(e) {
                e.stopPropagation();
                closeChat();
            });
        }

        // Close chat
        closeBtn.addEventListener('click', closeChat);

        // Close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeChat();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isOpen) {
                closeChat();
            }
        });

        // Form submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });

        // Auto-resize textarea
        input.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Send on Enter (without Shift)
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    /**
     * Open chat modal
     */
    function openChat() {
        isOpen = true;
        widget.classList.add('is-open');
        document.body.style.overflow = 'hidden';

        // Focus input after animation
        setTimeout(() => {
            input.focus();
            scrollToBottom();
        }, 300);
    }

    /**
     * Close chat modal
     */
    function closeChat() {
        isOpen = false;
        widget.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    /**
     * Send message to API
     */
    async function sendMessage() {
        const message = input.value.trim();
        if (!message || isLoading) return;

        // Clear input
        input.value = '';
        input.style.height = 'auto';

        // Add user message to UI
        addMessage(message, 'user');

        // Add to history
        conversationHistory.push({ role: 'user', content: message });

        // Show typing indicator
        showTyping();
        setLoading(true);

        try {
            const response = await fetch(CONFIG.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    history: conversationHistory.slice(-10) // Send last 10 messages for context
                })
            });

            const data = await response.json();

            // Remove typing indicator
            hideTyping();

            if (data.success) {
                // Add bot response to UI
                addMessage(data.message, 'bot', data.sources);

                // Add to history
                conversationHistory.push({ role: 'assistant', content: data.message });

                // Save history
                saveHistory();
            } else {
                addMessage(data.error || 'Lo siento, ha ocurrido un error. Por favor, inténtalo de nuevo.', 'bot', null, true);
            }
        } catch (error) {
            console.error('Chatbot error:', error);
            hideTyping();
            addMessage('Lo siento, no puedo conectar con el servidor. Por favor, inténtalo más tarde.', 'bot', null, true);
        } finally {
            setLoading(false);
        }
    }

    /**
     * Add message to UI
     */
    function addMessage(content, type, sources = null, isError = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type}-message`;

        const avatarHtml = type === 'bot' ? ROBOT_SVG : USER_SVG;

        let contentHtml = formatMessage(content);

        // Add sources if available
        let sourcesHtml = '';
        if (sources && sources.length > 0) {
            sourcesHtml = `
                <div class="message-sources">
                    <strong>Artículos relacionados:</strong>
                    ${sources.map(s => `<a href="${s.url}" target="_blank">${s.title}</a>`).join('')}
                </div>
            `;
        }

        messageDiv.innerHTML = `
            <div class="message-avatar">${avatarHtml}</div>
            <div class="message-content ${isError ? 'message-error' : ''}">
                ${contentHtml}
                ${sourcesHtml}
            </div>
        `;

        messagesContainer.appendChild(messageDiv);
        scrollToBottom();
    }

    /**
     * Format message content (convert markdown-like syntax)
     */
    function formatMessage(content) {
        // Escape HTML first
        content = escapeHtml(content);

        // Convert line breaks to paragraphs
        const paragraphs = content.split(/\n\n+/);

        let html = paragraphs.map(para => {
            // Handle bullet points
            if (para.match(/^[\-\*]\s/m)) {
                const items = para.split(/\n/).filter(line => line.trim());
                const listItems = items.map(item => {
                    const text = item.replace(/^[\-\*]\s+/, '');
                    return `<li>${text}</li>`;
                }).join('');
                return `<ul>${listItems}</ul>`;
            }

            // Handle numbered lists
            if (para.match(/^\d+\.\s/m)) {
                const items = para.split(/\n/).filter(line => line.trim());
                const listItems = items.map(item => {
                    const text = item.replace(/^\d+\.\s+/, '');
                    return `<li>${text}</li>`;
                }).join('');
                return `<ol>${listItems}</ol>`;
            }

            // Regular paragraph with line breaks
            return `<p>${para.replace(/\n/g, '<br>')}</p>`;
        }).join('');

        // Convert **bold**
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

        // Convert URLs to links
        html = html.replace(/(\/[a-z0-9\-\/]+)/gi, function(match) {
            if (match.startsWith('/ayuda') || match.startsWith('/la-herramienta') ||
                match.startsWith('/precios') || match.startsWith('/casos') ||
                match.startsWith('/contacto') || match.startsWith('/blog')) {
                return `<a href="${match}" target="_blank">${match}</a>`;
            }
            return match;
        });

        return html;
    }

    /**
     * Escape HTML entities
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Show typing indicator
     */
    function showTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typing-indicator';
        typingDiv.className = 'chat-message bot-message';
        typingDiv.innerHTML = `
            <div class="message-avatar">
                ${ROBOT_SVG}
            </div>
            <div class="message-content">
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;
        messagesContainer.appendChild(typingDiv);
        scrollToBottom();
    }

    /**
     * Hide typing indicator
     */
    function hideTyping() {
        const typing = document.getElementById('typing-indicator');
        if (typing) {
            typing.remove();
        }
    }

    /**
     * Set loading state
     */
    function setLoading(loading) {
        isLoading = loading;
        widget.classList.toggle('is-loading', loading);
        sendBtn.disabled = loading;
        input.disabled = loading;
    }

    /**
     * Scroll messages to bottom
     */
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    /**
     * Save conversation history to localStorage
     */
    function saveHistory() {
        try {
            // Keep only last N messages
            const historyToSave = conversationHistory.slice(-CONFIG.maxHistory);
            localStorage.setItem(CONFIG.storageKey, JSON.stringify(historyToSave));
        } catch (e) {
            console.warn('Could not save chat history:', e);
        }
    }

    /**
     * Load conversation history from localStorage
     */
    function loadHistory() {
        try {
            const saved = localStorage.getItem(CONFIG.storageKey);
            if (saved) {
                conversationHistory = JSON.parse(saved);

                // Restore messages to UI (skip first welcome message as it's already in HTML)
                conversationHistory.forEach(msg => {
                    addMessage(msg.content, msg.role === 'user' ? 'user' : 'bot');
                });
            }
        } catch (e) {
            console.warn('Could not load chat history:', e);
            conversationHistory = [];
        }
    }

    /**
     * Clear conversation history
     */
    function clearHistory() {
        conversationHistory = [];
        localStorage.removeItem(CONFIG.storageKey);

        // Remove all messages except welcome
        const messages = messagesContainer.querySelectorAll('.chat-message');
        messages.forEach((msg, index) => {
            if (index > 0) msg.remove();
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose public API
    window.LoyaltyMaster = {
        open: openChat,
        close: closeChat,
        clearHistory: clearHistory
    };

})();
