/**
 * Omniwallet CMS - Admin JavaScript
 */

(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initAlerts();
        initForms();
        initSlugGeneration();
    });

    /**
     * Auto-dismiss alerts after 5 seconds
     */
    function initAlerts() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s ease';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }, 5000);
        });
    }

    /**
     * Form enhancements
     */
    function initForms() {
        // Confirm delete forms
        const deleteForms = document.querySelectorAll('form[action*="/delete"]');
        deleteForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (!confirm('¿Estás seguro de que quieres eliminar este elemento?')) {
                    e.preventDefault();
                }
            });
        });

        // Prevent double submit
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Guardando...';
                }
            });
        });
    }

    /**
     * Auto-generate slug from title
     */
    function initSlugGeneration() {
        const nameInput = document.getElementById('name') || document.getElementById('title');
        const slugInput = document.getElementById('slug');

        if (nameInput && slugInput) {
            // Only auto-generate if slug is empty
            let manuallyEdited = slugInput.value !== '';

            slugInput.addEventListener('input', function() {
                manuallyEdited = true;
            });

            nameInput.addEventListener('input', function() {
                if (!manuallyEdited) {
                    slugInput.value = generateSlug(nameInput.value);
                }
            });
        }
    }

    /**
     * Generate URL-friendly slug
     */
    function generateSlug(text) {
        return text
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Remove accents
            .replace(/[^a-z0-9]+/g, '-')     // Replace non-alphanumeric with dash
            .replace(/-+/g, '-')              // Remove consecutive dashes
            .replace(/^-|-$/g, '');           // Trim dashes from ends
    }

    // Expose utilities globally if needed
    window.OmniwalletAdmin = {
        generateSlug: generateSlug
    };

})();
