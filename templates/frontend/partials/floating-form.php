<?php
/**
 * Floating Contact Form Partial
 * We're Sinapsis CMS
 *
 * A configurable floating contact form that slides from the right.
 * Include this partial in your layout to enable the floating form.
 */

// Get settings from global or passed variable
$floatingFormEnabled = $settings['floating_form_enabled'] ?? true;
$floatingFormTitle = $settings['floating_form_title'] ?? __('contact_us');
$floatingFormButtonText = $settings['floating_form_button_text'] ?? '';
$floatingFormButtonIcon = $settings['floating_form_button_icon'] ?? 'fas fa-comment-dots';
$floatingFormSuccessTitle = $settings['floating_form_success_title'] ?? __('message_sent');
$floatingFormSuccessMessage = $settings['floating_form_success_message'] ?? __('floating_form_success');
$whatsappNumber = '+34933658191';
$whatsappNumberClean = '34933658191';

// Default fields
$floatingFormFields = $settings['floating_form_fields'] ?? [
    ['name' => 'name', 'type' => 'text', 'label' => __('name'), 'required' => true, 'placeholder' => __('your_name')],
    ['name' => 'email', 'type' => 'email', 'label' => __('email'), 'required' => true, 'placeholder' => __('your_email')],
    ['name' => 'phone', 'type' => 'tel', 'label' => __('phone'), 'required' => false, 'placeholder' => __('your_phone')],
    ['name' => 'message', 'type' => 'textarea', 'label' => __('message'), 'required' => true, 'placeholder' => __('your_message_placeholder')],
];

if (!$floatingFormEnabled) {
    return;
}
?>

<!-- Floating Contact Form -->
<div class="floating-form-container" id="floating-form">
    <!-- Overlay -->
    <div class="floating-form-overlay" id="floating-form-overlay"></div>

    <!-- Toggle Button -->
    <button class="floating-form-toggle" id="floating-form-toggle" aria-label="<?= __('open_contact_form') ?>">
        <span class="toggle-icon-open">
            <i class="<?= htmlspecialchars($floatingFormButtonIcon) ?>"></i>
            <?php if ($floatingFormButtonText): ?>
                <span class="toggle-text"><?= htmlspecialchars($floatingFormButtonText) ?></span>
            <?php endif; ?>
        </span>
        <span class="toggle-icon-close" style="display:none;">
            <i class="fas fa-times"></i>
        </span>
    </button>

    <!-- Form Panel (Slide from right) -->
    <div class="floating-form-panel" id="floating-form-panel">
        <button class="floating-form-close" id="floating-form-close" aria-label="<?= __('close') ?>">
            <i class="fas fa-times"></i>
        </button>

        <div class="floating-form-header">
            <p class="floating-form-description"><?= __('floating_form_description') ?></p>

            <div class="floating-form-whatsapp">
                <a href="https://wa.me/<?= $whatsappNumberClean ?>" target="_blank" rel="noopener" class="whatsapp-btn">
                    <i class="fab fa-whatsapp"></i>
                    <span><?= __('floating_form_or_contact') ?> <?= $whatsappNumber ?></span>
                </a>
            </div>
        </div>

        <form class="floating-form-content" id="floating-form-form" novalidate>
            <input type="hidden" name="form_source" value="floating">

            <?php foreach ($floatingFormFields as $field):
                $fieldName = $field['name'] ?? '';
                $fieldType = $field['type'] ?? 'text';
                $fieldLabel = $field['label'] ?? ucfirst($fieldName);
                $fieldPlaceholder = $field['placeholder'] ?? '';
                $isRequired = !empty($field['required']);
            ?>
                <div class="floating-form-field">
                    <label for="ff-<?= $fieldName ?>">
                        <?= htmlspecialchars($fieldLabel) ?>
                        <?php if ($isRequired): ?><span class="required">*</span><?php endif; ?>
                    </label>

                    <?php if ($fieldType === 'textarea'): ?>
                        <textarea
                            id="ff-<?= $fieldName ?>"
                            name="<?= htmlspecialchars($fieldName) ?>"
                            placeholder="<?= htmlspecialchars($fieldPlaceholder) ?>"
                            rows="4"
                            <?= $isRequired ? 'required' : '' ?>
                        ></textarea>
                    <?php else: ?>
                        <input
                            type="<?= htmlspecialchars($fieldType) ?>"
                            id="ff-<?= $fieldName ?>"
                            name="<?= htmlspecialchars($fieldName) ?>"
                            placeholder="<?= htmlspecialchars($fieldPlaceholder) ?>"
                            <?= $isRequired ? 'required' : '' ?>
                        >
                    <?php endif; ?>
                    <span class="field-error"></span>
                </div>
            <?php endforeach; ?>

            <div class="floating-form-actions">
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="btn-text"><?= __('send_message') ?></span>
                    <span class="btn-loading" style="display:none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>

            <div class="floating-form-message" style="display:none;"></div>
        </form>

        <!-- Success State -->
        <div class="floating-form-success" id="floating-form-success" style="display:none;">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h4><?= htmlspecialchars($floatingFormSuccessTitle) ?></h4>
            <p><?= htmlspecialchars($floatingFormSuccessMessage) ?></p>
            <button type="button" class="btn btn-outline" onclick="FloatingForm.reset()">
                <?= __('send_another') ?>
            </button>
        </div>
    </div>
</div>

<style>
/* Floating Form Container */
.floating-form-container {
    position: fixed;
    z-index: 1000;
    font-family: var(--font-family-base);
}

/* Overlay */
.floating-form-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    visibility: hidden;
    z-index: 1000;
}

.floating-form-container.is-open .floating-form-overlay {
    opacity: 1;
    visibility: visible;
}

/* Toggle Button */
.floating-form-toggle {
    position: fixed;
    bottom: var(--spacing-lg);
    right: var(--spacing-lg);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md) var(--spacing-lg);
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark, #1a3a44) 100%);
    color: white;
    border: none;
    border-radius: var(--radius-full);
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    font-size: var(--font-size-base);
    font-weight: 500;
    z-index: 999;
}

.floating-form-toggle:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 25px rgba(0,0,0,0.25);
}

.floating-form-toggle i {
    font-size: 20px;
}

.floating-form-toggle .toggle-text {
    display: inline;
}

.floating-form-container.is-open .floating-form-toggle {
    opacity: 0;
    pointer-events: none;
}

/* Form Panel - Slide from right */
.floating-form-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 50%;
    max-width: 600px;
    min-width: 400px;
    height: 100vh;
    background: white;
    box-shadow: -10px 0 40px rgba(0,0,0,0.15);
    transform: translateX(100%);
    visibility: hidden;
    display: flex;
    flex-direction: column;
    z-index: 1001;
    overflow: hidden;
}

/* Only enable transitions after page load */
.floating-form-container.initialized .floating-form-panel {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s;
}

.floating-form-container.initialized .floating-form-overlay {
    transition: all 0.3s ease;
}

.floating-form-container.is-open .floating-form-panel {
    transform: translateX(0);
    visibility: visible;
}

/* Close Button */
.floating-form-close {
    position: absolute;
    top: var(--spacing-md);
    right: var(--spacing-md);
    width: 32px;
    height: 32px;
    border: none;
    background: rgba(255,255,255,0.2);
    color: white;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.2s ease;
    z-index: 10;
}

.floating-form-close:hover {
    background: rgba(255,255,255,0.3);
}

/* Header */
.floating-form-header {
    padding: var(--spacing-lg) var(--spacing-xl);
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark, #1a3a44) 100%);
    color: white;
    flex-shrink: 0;
}

.floating-form-description {
    margin: 0;
    font-size: var(--font-size-sm);
    line-height: 1.5;
    opacity: 0.95;
}

/* WhatsApp Section */
.floating-form-whatsapp {
    display: flex;
    align-items: center;
    margin-top: var(--spacing-md);
}

.whatsapp-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-xs) var(--spacing-sm);
    background: #25D366;
    color: white;
    text-decoration: none;
    border-radius: var(--radius-md);
    font-weight: 500;
    font-size: var(--font-size-sm);
    transition: all 0.2s ease;
}

.whatsapp-btn:hover {
    background: #128C7E;
    color: white;
}

.whatsapp-btn i {
    font-size: 16px;
}

/* Form Content */
.floating-form-content {
    padding: var(--spacing-md) var(--spacing-xl);
    flex: 1;
    overflow-y: auto;
}

.floating-form-field {
    margin-bottom: var(--spacing-md);
}

.floating-form-field label {
    display: block;
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--color-gray-700);
    margin-bottom: var(--spacing-xs);
}

.floating-form-field label .required {
    color: var(--color-danger);
}

.floating-form-field input,
.floating-form-field textarea {
    width: 100%;
    padding: var(--spacing-md);
    border: 2px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    font-size: var(--font-size-base);
    font-family: inherit;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.floating-form-field input:focus,
.floating-form-field textarea:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px var(--color-primary-light, rgba(33, 90, 107, 0.1));
}

.floating-form-field input.error,
.floating-form-field textarea.error {
    border-color: var(--color-danger);
}

.floating-form-field textarea {
    resize: vertical;
    min-height: 100px;
}

.floating-form-field .field-error {
    display: block;
    color: var(--color-danger);
    font-size: var(--font-size-xs);
    margin-top: var(--spacing-xs);
    min-height: 18px;
}

.floating-form-actions {
    margin-top: var(--spacing-lg);
}

.floating-form-actions .btn {
    width: 100%;
    padding: var(--spacing-md) var(--spacing-xl);
    font-size: var(--font-size-base);
    font-weight: 600;
}

.floating-form-message {
    margin-top: var(--spacing-md);
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
    text-align: center;
}

.floating-form-message.error {
    background: #fef2f2;
    color: var(--color-danger);
    border: 1px solid #fecaca;
}

.floating-form-message.success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

/* Success State */
.floating-form-success {
    padding: var(--spacing-3xl) var(--spacing-2xl);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
}

.floating-form-success .success-icon {
    width: 80px;
    height: 80px;
    margin-bottom: var(--spacing-lg);
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.floating-form-success .success-icon i {
    font-size: 36px;
    color: white;
}

.floating-form-success h4 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-sm);
    color: var(--color-gray-900);
}

.floating-form-success p {
    color: var(--color-gray-600);
    font-size: var(--font-size-base);
    margin-bottom: var(--spacing-xl);
    max-width: 300px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .floating-form-toggle {
        bottom: var(--spacing-md);
        right: var(--spacing-md);
    }

    .floating-form-toggle .toggle-text {
        display: none;
    }

    .floating-form-toggle {
        padding: var(--spacing-md);
        border-radius: 50%;
    }

    .floating-form-panel {
        width: 100%;
        min-width: unset;
        max-width: unset;
    }

    .floating-form-header {
        padding: var(--spacing-xl);
        padding-top: calc(var(--spacing-xl) + 50px);
    }

    .floating-form-header h3 {
        font-size: var(--font-size-xl);
    }

    .floating-form-description {
        font-size: var(--font-size-sm);
    }

    .floating-form-content {
        padding: var(--spacing-lg);
    }

    .floating-form-close {
        top: var(--spacing-md);
        right: var(--spacing-md);
    }
}

/* Hide on print */
@media print {
    .floating-form-container {
        display: none !important;
    }
}
</style>

<script>
const FloatingForm = (function() {
    const container = document.getElementById('floating-form');
    const toggle = document.getElementById('floating-form-toggle');
    const panel = document.getElementById('floating-form-panel');
    const overlay = document.getElementById('floating-form-overlay');
    const closeBtn = document.getElementById('floating-form-close');
    const form = document.getElementById('floating-form-form');
    const successDiv = document.getElementById('floating-form-success');

    if (!container || !toggle || !panel || !form) return {};

    // Toggle form visibility
    function toggleForm() {
        const isOpen = container.classList.contains('is-open');
        if (isOpen) {
            close();
        } else {
            open();
        }
    }

    function open() {
        container.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        // Focus first input
        setTimeout(() => {
            const firstInput = form.querySelector('input, textarea');
            if (firstInput) firstInput.focus();
        }, 400);
    }

    function close() {
        container.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function reset() {
        form.reset();
        form.style.display = 'block';
        successDiv.style.display = 'none';
        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        form.querySelectorAll('.field-error').forEach(el => el.textContent = '');
        const messageDiv = form.querySelector('.floating-form-message');
        if (messageDiv) {
            messageDiv.style.display = 'none';
            messageDiv.textContent = '';
        }
    }

    // Validate field
    function validateField(input) {
        const errorSpan = input.parentElement.querySelector('.field-error');
        let isValid = true;
        let errorMessage = '';

        if (input.required && !input.value.trim()) {
            isValid = false;
            errorMessage = '<?= __('field_required') ?>';
        } else if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                isValid = false;
                errorMessage = '<?= __('invalid_email') ?>';
            }
        } else if (input.type === 'tel' && input.value) {
            const phoneRegex = /^[\d\s\+\-\(\)]{6,20}$/;
            if (!phoneRegex.test(input.value)) {
                isValid = false;
                errorMessage = '<?= __('invalid_phone') ?>';
            }
        }

        input.classList.toggle('error', !isValid);
        if (errorSpan) errorSpan.textContent = errorMessage;

        return isValid;
    }

    function validateForm() {
        const inputs = form.querySelectorAll('input:not([type="hidden"]), textarea');
        let isValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) isValid = false;
        });
        return isValid;
    }

    function showMessage(message, type) {
        const messageDiv = form.querySelector('.floating-form-message');
        messageDiv.textContent = message;
        messageDiv.className = 'floating-form-message ' + type;
        messageDiv.style.display = 'block';
    }

    function setLoading(loading) {
        const btn = form.querySelector('button[type="submit"]');
        const btnText = btn.querySelector('.btn-text');
        const btnLoading = btn.querySelector('.btn-loading');

        btn.disabled = loading;
        btnText.style.display = loading ? 'none' : '';
        btnLoading.style.display = loading ? '' : 'none';
    }

    function showSuccess() {
        form.style.display = 'none';
        successDiv.style.display = 'flex';
    }

    // Enable transitions after page load to prevent flash on navigation
    setTimeout(() => {
        container.classList.add('initialized');
    }, 100);

    // Event listeners
    toggle.addEventListener('click', toggleForm);
    closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', close);

    // Close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && container.classList.contains('is-open')) {
            close();
        }
    });

    // Real-time validation
    form.querySelectorAll('input, textarea').forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => {
            if (input.classList.contains('error')) {
                validateField(input);
            }
        });
    });

    // Form submit
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!validateForm()) return;

        setLoading(true);

        try {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => { data[key] = value; });

            const response = await fetch('/api/contact/submit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showSuccess();
            } else {
                showMessage(result.error || '<?= __('form_error') ?>', 'error');
            }
        } catch (error) {
            console.error('Floating form error:', error);
            showMessage('<?= __('form_error') ?>', 'error');
        } finally {
            setLoading(false);
        }
    });

    return { open, close, toggle: toggleForm, reset };
})();
</script>
