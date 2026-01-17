/**
 * Cookie Consent Manager
 * Omniwallet CMS
 *
 * Manages user cookie preferences and consent
 */

const CookieConsent = {
    // Configuration
    config: {
        cookieName: 'omniwallet_cookie_consent',
        cookieExpiry: 365, // days
        version: '1.0', // Increment to re-ask consent
    },

    // Default preferences
    defaults: {
        necessary: true,  // Always true
        analytics: false,
        marketing: false,
    },

    // Current preferences
    preferences: null,

    /**
     * Initialize the consent manager
     */
    init() {
        this.preferences = this.getStoredPreferences();

        if (this.preferences) {
            // User already consented
            this.hideBanner();
            this.showSettingsButton();
            this.applyPreferences();
        } else {
            // Show banner for new visitors
            this.showBanner();
        }

        // Initialize toggle states in modal
        this.initToggles();
    },

    /**
     * Get stored preferences from cookie
     */
    getStoredPreferences() {
        const cookie = this.getCookie(this.config.cookieName);
        if (!cookie) return null;

        try {
            const data = JSON.parse(cookie);
            // Check version
            if (data.version !== this.config.version) {
                return null; // Re-ask if version changed
            }
            return data.preferences;
        } catch (e) {
            return null;
        }
    },

    /**
     * Save preferences to cookie
     */
    savePreferencesToCookie(preferences) {
        const data = {
            version: this.config.version,
            preferences: preferences,
            timestamp: new Date().toISOString(),
        };

        this.setCookie(
            this.config.cookieName,
            JSON.stringify(data),
            this.config.cookieExpiry
        );

        this.preferences = preferences;
    },

    /**
     * Accept all cookies
     */
    acceptAll() {
        const preferences = {
            necessary: true,
            analytics: true,
            marketing: true,
        };

        this.savePreferencesToCookie(preferences);
        this.hideBanner();
        this.hidePreferences();
        this.showSettingsButton();
        this.applyPreferences();

        // Dispatch event for other scripts
        this.dispatchConsentEvent('all');
    },

    /**
     * Reject all optional cookies
     */
    rejectAll() {
        const preferences = {
            necessary: true,
            analytics: false,
            marketing: false,
        };

        this.savePreferencesToCookie(preferences);
        this.hideBanner();
        this.hidePreferences();
        this.showSettingsButton();
        this.applyPreferences();

        // Dispatch event
        this.dispatchConsentEvent('necessary');
    },

    /**
     * Save current preferences from modal toggles
     */
    savePreferences() {
        const preferences = {
            necessary: true, // Always true
            analytics: document.querySelector('[data-category="analytics"]')?.checked || false,
            marketing: document.querySelector('[data-category="marketing"]')?.checked || false,
        };

        this.savePreferencesToCookie(preferences);
        this.hideBanner();
        this.hidePreferences();
        this.showSettingsButton();
        this.applyPreferences();

        // Dispatch event
        this.dispatchConsentEvent('custom');
    },

    /**
     * Apply preferences - enable/disable scripts based on consent
     */
    applyPreferences() {
        if (!this.preferences) return;

        // Analytics
        if (this.preferences.analytics) {
            this.enableAnalytics();
        } else {
            this.disableAnalytics();
        }

        // Marketing
        if (this.preferences.marketing) {
            this.enableMarketing();
        } else {
            this.disableMarketing();
        }
    },

    /**
     * Enable Google Analytics
     */
    enableAnalytics() {
        // Enable GA if it exists
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }

        // Load deferred analytics scripts
        document.querySelectorAll('script[data-cookie-category="analytics"]').forEach(script => {
            if (!script.src && script.textContent) {
                eval(script.textContent);
            } else if (script.dataset.src) {
                const newScript = document.createElement('script');
                newScript.src = script.dataset.src;
                document.head.appendChild(newScript);
            }
        });
    },

    /**
     * Disable Google Analytics
     */
    disableAnalytics() {
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
        }
    },

    /**
     * Enable Marketing scripts
     */
    enableMarketing() {
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'ad_storage': 'granted',
                'ad_user_data': 'granted',
                'ad_personalization': 'granted'
            });
        }

        // Load deferred marketing scripts
        document.querySelectorAll('script[data-cookie-category="marketing"]').forEach(script => {
            if (script.dataset.src) {
                const newScript = document.createElement('script');
                newScript.src = script.dataset.src;
                document.head.appendChild(newScript);
            }
        });
    },

    /**
     * Disable Marketing scripts
     */
    disableMarketing() {
        if (typeof gtag === 'function') {
            gtag('consent', 'update', {
                'ad_storage': 'denied',
                'ad_user_data': 'denied',
                'ad_personalization': 'denied'
            });
        }
    },

    /**
     * Show cookie banner
     */
    showBanner() {
        const banner = document.getElementById('cookieBanner');
        if (banner) {
            setTimeout(() => banner.classList.add('is-visible'), 100);
        }
    },

    /**
     * Hide cookie banner
     */
    hideBanner() {
        const banner = document.getElementById('cookieBanner');
        if (banner) {
            banner.classList.remove('is-visible');
        }
    },

    /**
     * Show preferences modal
     */
    showPreferences() {
        const modal = document.getElementById('cookieModal');
        if (modal) {
            // Update toggles to current preferences
            this.updateToggles();
            modal.classList.add('is-visible');
            document.body.style.overflow = 'hidden';
        }
    },

    /**
     * Hide preferences modal
     */
    hidePreferences() {
        const modal = document.getElementById('cookieModal');
        if (modal) {
            modal.classList.remove('is-visible');
            document.body.style.overflow = '';
        }
    },

    /**
     * Show settings button
     */
    showSettingsButton() {
        const btn = document.getElementById('cookieSettingsBtn');
        if (btn) {
            btn.classList.add('is-visible');
        }
    },

    /**
     * Initialize toggle states
     */
    initToggles() {
        // Close modal when clicking overlay
        const overlay = document.getElementById('cookieModal');
        if (overlay) {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    this.hidePreferences();
                }
            });
        }

        // ESC key closes modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hidePreferences();
            }
        });
    },

    /**
     * Update toggles to reflect current preferences
     */
    updateToggles() {
        if (this.preferences) {
            const analyticsToggle = document.querySelector('[data-category="analytics"]');
            const marketingToggle = document.querySelector('[data-category="marketing"]');

            if (analyticsToggle) analyticsToggle.checked = this.preferences.analytics;
            if (marketingToggle) marketingToggle.checked = this.preferences.marketing;
        }
    },

    /**
     * Toggle category expansion in modal
     */
    toggleCategory(header) {
        const category = header.closest('.cookie-category');
        if (category) {
            category.classList.toggle('is-expanded');
        }
    },

    /**
     * Dispatch custom event for consent changes
     */
    dispatchConsentEvent(type) {
        const event = new CustomEvent('cookieConsent', {
            detail: {
                type: type,
                preferences: this.preferences
            }
        });
        document.dispatchEvent(event);
    },

    /**
     * Check if a category is allowed
     */
    isAllowed(category) {
        if (!this.preferences) return false;
        return this.preferences[category] === true;
    },

    /**
     * Cookie helper - get
     */
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return decodeURIComponent(parts.pop().split(';').shift());
        }
        return null;
    },

    /**
     * Cookie helper - set
     */
    setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${encodeURIComponent(value)};${expires};path=/;SameSite=Lax`;
    },

    /**
     * Cookie helper - delete
     */
    deleteCookie(name) {
        document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/`;
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    CookieConsent.init();
});

// Also expose globally for manual control
window.CookieConsent = CookieConsent;
