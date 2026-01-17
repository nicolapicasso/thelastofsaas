<?php
/**
 * Contact Form Block Template
 * Displays a configurable contact form
 * Omniwallet CMS
 */

$fields = $content['fields'] ?? [];
$enabledFields = array_filter($fields, fn($f) => !empty($f['enabled']));
$animationAttrs = $renderer->getAnimationAttributes($settings);
$formStyle = $settings['form_style'] ?? 'card';
$recaptchaEnabled = !empty($settings['recaptcha_enabled']);
$recaptchaSiteKey = $settings['recaptcha_site_key'] ?? '';
$blockId = $block['id'] ?? uniqid();
?>

<section class="block block-contact-form section <?= $renderer->getBlockClasses($block, $settings) ?>" style="<?= $renderer->getBlockStyles($settings) ?>" <?= $animationAttrs ?>>
    <div class="container">
        <?php if (!empty($content['title'])): ?>
            <div class="section-header">
                <h2><?= htmlspecialchars($content['title']) ?></h2>
                <?php if (!empty($content['subtitle'])): ?>
                    <p><?= htmlspecialchars($content['subtitle']) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="contact-form-wrapper <?= $formStyle === 'card' ? 'contact-form-card' : '' ?>">
            <!-- Form -->
            <form class="contact-form" id="contact-form-<?= $blockId ?>" data-block-id="<?= $blockId ?>" novalidate>
                <input type="hidden" name="block_id" value="<?= $blockId ?>">

                <div class="form-fields">
                    <?php foreach ($enabledFields as $field):
                        $fieldName = $field['name'] ?? '';
                        $fieldType = $field['type'] ?? 'text';
                        $fieldLabel = $field['label'] ?? ucfirst($fieldName);
                        $fieldPlaceholder = $field['placeholder'] ?? '';
                        $isRequired = !empty($field['required']);
                        $fieldWidth = $field['width'] ?? 'full';
                    ?>
                        <div class="form-group field-<?= $fieldWidth ?>">
                            <label for="cf-<?= $blockId ?>-<?= $fieldName ?>">
                                <?= htmlspecialchars($fieldLabel) ?>
                                <?php if ($isRequired): ?><span class="required">*</span><?php endif; ?>
                            </label>

                            <?php if ($fieldType === 'textarea'): ?>
                                <textarea
                                    id="cf-<?= $blockId ?>-<?= $fieldName ?>"
                                    name="<?= htmlspecialchars($fieldName) ?>"
                                    placeholder="<?= htmlspecialchars($fieldPlaceholder) ?>"
                                    rows="5"
                                    <?= $isRequired ? 'required' : '' ?>
                                ></textarea>
                            <?php else: ?>
                                <input
                                    type="<?= htmlspecialchars($fieldType) ?>"
                                    id="cf-<?= $blockId ?>-<?= $fieldName ?>"
                                    name="<?= htmlspecialchars($fieldName) ?>"
                                    placeholder="<?= htmlspecialchars($fieldPlaceholder) ?>"
                                    <?= $isRequired ? 'required' : '' ?>
                                >
                            <?php endif; ?>

                            <span class="field-error"></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($recaptchaEnabled && $recaptchaSiteKey): ?>
                    <input type="hidden" name="recaptcha_token" id="recaptcha-token-<?= $blockId ?>">
                <?php endif; ?>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <span class="btn-text"><?= htmlspecialchars($content['submit_text'] ?? 'Enviar mensaje') ?></span>
                        <span class="btn-loading" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Enviando...
                        </span>
                    </button>
                </div>

                <div class="form-message" style="display:none;"></div>
            </form>

            <!-- Success Message -->
            <div class="contact-success" id="contact-success-<?= $blockId ?>" style="display:none;">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3><?= htmlspecialchars($content['success_title'] ?? '¡Mensaje enviado!') ?></h3>
                <p><?= htmlspecialchars($content['success_message'] ?? 'Gracias por contactar con nosotros.') ?></p>
                <div class="success-summary" id="contact-summary-<?= $blockId ?>">
                    <!-- Summary will be injected here -->
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($recaptchaEnabled && $recaptchaSiteKey): ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?= htmlspecialchars($recaptchaSiteKey) ?>"></script>
<?php endif; ?>

<style>
.block-contact-form {
    padding: var(--spacing-3xl) 0;
}

.block-contact-form .section-header {
    text-align: center;
    max-width: 600px;
    margin: 0 auto var(--spacing-2xl);
}

.block-contact-form .section-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-sm);
}

.block-contact-form .section-header p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
}

.contact-form-wrapper {
    max-width: 700px;
    margin: 0 auto;
}

.contact-form-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--spacing-2xl);
    box-shadow: var(--shadow-lg);
}

.form-fields {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-lg);
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.field-full {
    grid-column: 1 / -1;
}

.form-group.field-half {
    grid-column: span 1;
}

.form-group label {
    font-weight: 500;
    margin-bottom: var(--spacing-xs);
    color: var(--color-gray-700);
}

.form-group label .required {
    color: var(--color-danger);
}

.form-group input,
.form-group textarea,
.form-group select {
    padding: var(--spacing-sm) var(--spacing-md);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: var(--font-size-base);
    font-family: inherit;
    transition: border-color var(--transition), box-shadow var(--transition);
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px var(--color-primary-light);
}

.form-group input.error,
.form-group textarea.error {
    border-color: var(--color-danger);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.field-error {
    color: var(--color-danger);
    font-size: var(--font-size-sm);
    margin-top: var(--spacing-xs);
    min-height: 20px;
}

.form-actions {
    margin-top: var(--spacing-xl);
    text-align: center;
}

.form-actions .btn {
    min-width: 200px;
}

.form-message {
    margin-top: var(--spacing-lg);
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    text-align: center;
}

.form-message.error {
    background: #fef2f2;
    color: var(--color-danger);
    border: 1px solid #fecaca;
}

.form-message.success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

/* Success State */
.contact-success {
    text-align: center;
    padding: var(--spacing-2xl);
}

.success-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto var(--spacing-lg);
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.success-icon i {
    font-size: 40px;
    color: white;
}

.contact-success h3 {
    font-size: var(--font-size-2xl);
    margin-bottom: var(--spacing-sm);
    color: var(--color-dark);
}

.contact-success p {
    color: var(--color-gray-600);
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-xl);
}

.success-summary {
    background: var(--color-gray-50);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    text-align: left;
}

.success-summary h4 {
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    margin-bottom: var(--spacing-md);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.success-summary .summary-item {
    display: flex;
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid var(--color-gray-200);
}

.success-summary .summary-item:last-child {
    border-bottom: none;
}

.success-summary .summary-label {
    width: 120px;
    flex-shrink: 0;
    font-weight: 500;
    color: var(--color-gray-600);
}

.success-summary .summary-value {
    color: var(--color-gray-800);
    word-break: break-word;
}

@media (max-width: 600px) {
    .form-fields {
        grid-template-columns: 1fr;
    }

    .form-group.field-half {
        grid-column: 1;
    }

    .contact-form-card {
        padding: var(--spacing-lg);
    }
}
</style>

<script>
(function() {
    const blockId = '<?= $blockId ?>';
    const form = document.getElementById('contact-form-' + blockId);
    const successDiv = document.getElementById('contact-success-' + blockId);
    const summaryDiv = document.getElementById('contact-summary-' + blockId);
    const recaptchaEnabled = <?= $recaptchaEnabled ? 'true' : 'false' ?>;
    const recaptchaSiteKey = '<?= htmlspecialchars($recaptchaSiteKey) ?>';

    if (!form) return;

    // Validate field
    function validateField(input) {
        const errorSpan = input.parentElement.querySelector('.field-error');
        let isValid = true;
        let errorMessage = '';

        if (input.required && !input.value.trim()) {
            isValid = false;
            errorMessage = 'Este campo es obligatorio';
        } else if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                isValid = false;
                errorMessage = 'Introduce un email válido';
            }
        } else if (input.type === 'tel' && input.value) {
            const phoneRegex = /^[\d\s\+\-\(\)]{6,20}$/;
            if (!phoneRegex.test(input.value)) {
                isValid = false;
                errorMessage = 'Introduce un teléfono válido';
            }
        }

        input.classList.toggle('error', !isValid);
        if (errorSpan) {
            errorSpan.textContent = errorMessage;
        }

        return isValid;
    }

    // Validate all fields
    function validateForm() {
        const inputs = form.querySelectorAll('input:not([type="hidden"]), textarea');
        let isValid = true;

        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    // Show form message
    function showMessage(message, type) {
        const messageDiv = form.querySelector('.form-message');
        messageDiv.textContent = message;
        messageDiv.className = 'form-message ' + type;
        messageDiv.style.display = 'block';
    }

    // Set loading state
    function setLoading(loading) {
        const btn = form.querySelector('button[type="submit"]');
        const btnText = btn.querySelector('.btn-text');
        const btnLoading = btn.querySelector('.btn-loading');

        btn.disabled = loading;
        btnText.style.display = loading ? 'none' : '';
        btnLoading.style.display = loading ? '' : 'none';
    }

    // Get form data as object
    function getFormData() {
        const data = {};
        const formData = new FormData(form);
        formData.forEach((value, key) => {
            data[key] = value;
        });
        return data;
    }

    // Show success with summary
    function showSuccess(data) {
        form.style.display = 'none';
        successDiv.style.display = 'block';

        // Build summary
        let summaryHtml = '<h4>Resumen de tu mensaje</h4>';
        const labels = {
            name: 'Nombre',
            email: 'Email',
            phone: 'Teléfono',
            company: 'Empresa',
            subject: 'Asunto',
            message: 'Mensaje'
        };

        for (const [key, value] of Object.entries(data)) {
            if (key === 'block_id' || key === 'recaptcha_token' || !value) continue;
            const label = labels[key] || key;
            summaryHtml += `
                <div class="summary-item">
                    <span class="summary-label">${label}</span>
                    <span class="summary-value">${escapeHtml(value)}</span>
                </div>
            `;
        }

        summaryDiv.innerHTML = summaryHtml;
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

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

        // Validate
        if (!validateForm()) {
            return;
        }

        setLoading(true);

        try {
            // Get reCAPTCHA token if enabled
            if (recaptchaEnabled && recaptchaSiteKey && typeof grecaptcha !== 'undefined') {
                const token = await grecaptcha.execute(recaptchaSiteKey, {action: 'contact'});
                document.getElementById('recaptcha-token-' + blockId).value = token;
            }

            const formData = getFormData();

            // Track form submit attempt (GTM)
            if (typeof OmniTracking !== 'undefined') {
                OmniTracking.trackFormSubmit('contact_form_' + blockId, {
                    has_email: !!formData.email,
                    has_phone: !!formData.phone,
                    has_company: !!formData.company
                });
            }

            // Submit form
            const response = await fetch('/api/contact/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                // Track form success (conversion event for GTM)
                if (typeof OmniTracking !== 'undefined') {
                    OmniTracking.trackFormSuccess('contact_form_' + blockId, {
                        email: formData.email || '',
                        has_phone: !!formData.phone,
                        has_company: !!formData.company
                    });
                }
                showSuccess(formData);
            } else {
                // Track form error
                if (typeof OmniTracking !== 'undefined') {
                    OmniTracking.trackError('contact_form_error', result.error || 'Unknown error');
                }
                showMessage(result.error || 'Error al enviar el formulario. Por favor, inténtalo de nuevo.', 'error');
            }
        } catch (error) {
            console.error('Contact form error:', error);
            // Track form error
            if (typeof OmniTracking !== 'undefined') {
                OmniTracking.trackError('contact_form_error', error.message || 'Network error');
            }
            showMessage('Error al enviar el formulario. Por favor, inténtalo de nuevo.', 'error');
        } finally {
            setLoading(false);
        }
    });
})();
</script>
