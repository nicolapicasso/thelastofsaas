/**
 * Omniwallet CMS - Animation System
 * Scroll-triggered animations using Intersection Observer
 */

(function() {
    'use strict';

    const AnimationSystem = {
        observer: null,
        options: {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        },

        init: function() {
            // Check if IntersectionObserver is supported
            if (!('IntersectionObserver' in window)) {
                // Fallback: show all animated elements immediately
                this.showAllAnimated();
                return;
            }

            this.createObserver();
            this.observeElements();
        },

        createObserver: function() {
            const self = this;

            this.observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        self.animateElement(entry.target);

                        // Optionally unobserve after animation (one-time animation)
                        if (!entry.target.hasAttribute('data-animate-repeat')) {
                            self.observer.unobserve(entry.target);
                        }
                    } else if (entry.target.hasAttribute('data-animate-repeat')) {
                        // Reset animation for elements that should repeat
                        entry.target.classList.remove('animated');
                    }
                });
            }, this.options);
        },

        observeElements: function() {
            const animatedElements = document.querySelectorAll('[data-animate]');
            const self = this;

            animatedElements.forEach(function(el) {
                self.observer.observe(el);
            });

            // Also observe elements with stagger animation
            const staggerContainers = document.querySelectorAll('[data-animate-stagger]');
            staggerContainers.forEach(function(container) {
                const animation = container.getAttribute('data-animate-stagger');
                const children = container.children;

                Array.from(children).forEach(function(child) {
                    if (!child.hasAttribute('data-animate')) {
                        child.setAttribute('data-animate', animation);
                        self.observer.observe(child);
                    }
                });
            });
        },

        animateElement: function(element) {
            // Add small delay before adding class to ensure CSS transition works
            requestAnimationFrame(function() {
                element.classList.add('animated');
            });
        },

        showAllAnimated: function() {
            // Fallback for browsers without IntersectionObserver
            document.querySelectorAll('[data-animate]').forEach(function(el) {
                el.classList.add('animated');
            });
        },

        // Manual trigger for dynamically added elements
        refresh: function() {
            this.observeElements();
        },

        // Animate a specific element immediately
        triggerNow: function(element) {
            if (element.hasAttribute('data-animate')) {
                this.animateElement(element);
            }
        }
    };

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            AnimationSystem.init();
        });
    } else {
        AnimationSystem.init();
    }

    // Expose globally for manual control
    window.AnimationSystem = AnimationSystem;

})();
