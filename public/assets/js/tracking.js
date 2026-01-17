/**
 * Omniwallet CMS - Tracking System
 * Google Tag Manager dataLayer events
 */

(function() {
    'use strict';

    // Ensure dataLayer exists
    window.dataLayer = window.dataLayer || [];

    /**
     * OmniTracking - Global tracking utility
     */
    window.OmniTracking = {
        /**
         * Push event to dataLayer
         * @param {string} eventName - Name of the event
         * @param {Object} eventData - Additional event data
         */
        pushEvent: function(eventName, eventData) {
            eventData = eventData || {};
            eventData.event = eventName;
            window.dataLayer.push(eventData);
        },

        /**
         * Track form submission
         * @param {string} formName - Name/ID of the form
         * @param {Object} formData - Form field values (sanitized)
         */
        trackFormSubmit: function(formName, formData) {
            this.pushEvent('form_submit', {
                form_name: formName,
                form_data: formData || {}
            });
        },

        /**
         * Track form success (conversion)
         * @param {string} formName - Name/ID of the form
         * @param {Object} data - Additional conversion data
         */
        trackFormSuccess: function(formName, data) {
            this.pushEvent('form_success', {
                form_name: formName,
                conversion_type: 'lead',
                data: data || {}
            });

            // Also push specific contact form event for easier GTM setup
            if (formName.includes('contact')) {
                this.pushEvent('contact_form_success', {
                    conversion_type: 'lead',
                    form_name: formName
                });
            }
        },

        /**
         * Track CTA button click
         * @param {string} ctaText - Button text
         * @param {string} ctaUrl - Button destination URL
         * @param {string} ctaLocation - Where on page (header, hero, footer, etc.)
         */
        trackCTAClick: function(ctaText, ctaUrl, ctaLocation) {
            this.pushEvent('cta_click', {
                cta_text: ctaText,
                cta_url: ctaUrl,
                cta_location: ctaLocation || 'unknown'
            });
        },

        /**
         * Track pricing calculator usage
         * @param {Object} calculatorData - Plan, sales, result, etc.
         */
        trackPricingCalculator: function(calculatorData) {
            this.pushEvent('pricing_calculator_use', {
                plan: calculatorData.plan || '',
                monthly_sales: calculatorData.sales || 0,
                activities: calculatorData.activities || 0,
                total_cost: calculatorData.total || 0,
                is_freemium: calculatorData.isFreemium || false,
                is_high_volume: calculatorData.isHighVolume || false
            });
        },

        /**
         * Track video play
         * @param {string} videoTitle - Video title
         * @param {string} videoUrl - Video URL
         */
        trackVideoPlay: function(videoTitle, videoUrl) {
            this.pushEvent('video_play', {
                video_title: videoTitle,
                video_url: videoUrl
            });
        },

        /**
         * Track scroll depth milestones
         * @param {number} percentage - Scroll depth percentage
         */
        trackScrollDepth: function(percentage) {
            this.pushEvent('scroll_depth', {
                scroll_percentage: percentage
            });
        },

        /**
         * Track outbound link clicks
         * @param {string} url - External URL
         * @param {string} text - Link text
         */
        trackOutboundLink: function(url, text) {
            this.pushEvent('outbound_click', {
                outbound_url: url,
                link_text: text
            });
        },

        /**
         * Track navigation click
         * @param {string} menuLocation - header, footer, etc.
         * @param {string} itemText - Menu item text
         * @param {string} itemUrl - Menu item URL
         */
        trackNavigation: function(menuLocation, itemText, itemUrl) {
            this.pushEvent('navigation_click', {
                menu_location: menuLocation,
                item_text: itemText,
                item_url: itemUrl
            });
        },

        /**
         * Track error
         * @param {string} errorType - Type of error
         * @param {string} errorMessage - Error message
         */
        trackError: function(errorType, errorMessage) {
            this.pushEvent('error', {
                error_type: errorType,
                error_message: errorMessage
            });
        }
    };

    /**
     * Auto-track CTA buttons
     */
    function initCTATracking() {
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-primary, .btn-cta, [data-track-cta]');
            if (btn && btn.href) {
                const location = getElementLocation(btn);
                OmniTracking.trackCTAClick(
                    btn.textContent.trim(),
                    btn.href,
                    location
                );
            }
        });
    }

    /**
     * Auto-track outbound links
     */
    function initOutboundTracking() {
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href^="http"]');
            if (link) {
                const url = new URL(link.href);
                if (url.hostname !== window.location.hostname) {
                    OmniTracking.trackOutboundLink(link.href, link.textContent.trim());
                }
            }
        });
    }

    /**
     * Auto-track scroll depth
     */
    function initScrollTracking() {
        const milestones = [25, 50, 75, 90, 100];
        const tracked = {};

        function checkScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercent = Math.round((scrollTop / docHeight) * 100);

            milestones.forEach(function(milestone) {
                if (scrollPercent >= milestone && !tracked[milestone]) {
                    tracked[milestone] = true;
                    OmniTracking.trackScrollDepth(milestone);
                }
            });
        }

        let scrollTimeout;
        window.addEventListener('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(checkScroll, 100);
        });
    }

    /**
     * Determine element location on page
     */
    function getElementLocation(element) {
        // Check specific sections
        if (element.closest('.site-header')) return 'header';
        if (element.closest('.site-footer')) return 'footer';
        if (element.closest('.block-hero')) return 'hero';
        if (element.closest('.block-cta-banner')) return 'cta_banner';
        if (element.closest('.block-pricing')) return 'pricing';
        if (element.closest('.block-pricing-calculator')) return 'pricing_calculator';
        if (element.closest('.block-contact-form')) return 'contact_form';

        // Check by section position
        const rect = element.getBoundingClientRect();
        const viewportHeight = window.innerHeight;

        if (rect.top < viewportHeight * 0.3) return 'above_fold';
        return 'below_fold';
    }

    /**
     * Initialize all tracking
     */
    function init() {
        initCTATracking();
        initOutboundTracking();
        initScrollTracking();
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
