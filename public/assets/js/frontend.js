/**
 * Frontend JavaScript
 * Omniwallet CMS
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all modules
    initMobileMenu();
    initStickyHeader();
    initSmoothScroll();
    initSliders();
    initAccordions();
    initLazyLoading();
    initAnimations();
    initAnimatedLogo();
    initSidebarMenu();
});

/**
 * Mobile Menu Toggle
 */
function initMobileMenu() {
    const menuToggle = document.querySelector('.mobile-toggle, #mobileToggle');
    const navMenu = document.querySelector('.nav-menu, #navMenu');
    const mainNav = document.querySelector('.main-nav');
    const body = document.body;

    if (!menuToggle) {
        console.log('Mobile toggle not found');
        return;
    }

    console.log('Mobile menu initialized', menuToggle);

    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Mobile toggle clicked');

        this.classList.toggle('active');
        if (navMenu) navMenu.classList.toggle('active');
        if (mainNav) mainNav.classList.toggle('menu-open');
        body.classList.toggle('menu-open');
    });

    // Also handle touch events for better mobile support
    menuToggle.addEventListener('touchend', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Mobile toggle touched');

        this.classList.toggle('active');
        if (navMenu) navMenu.classList.toggle('active');
        if (mainNav) mainNav.classList.toggle('menu-open');
        body.classList.toggle('menu-open');
    });

    // Handle submenu toggles in mobile
    if (navMenu) {
        const submenus = navMenu.querySelectorAll('.has-submenu');
        submenus.forEach(item => {
            const link = item.querySelector(':scope > a');
            if (link) {
                link.addEventListener('click', function(e) {
                    // Only toggle submenu on mobile
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Close other submenus
                        submenus.forEach(other => {
                            if (other !== item) {
                                other.classList.remove('open');
                            }
                        });

                        // Toggle this submenu
                        item.classList.toggle('open');
                    }
                });
            }
        });

        // Close menu on link click (only for non-submenu links)
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const parentSubmenu = this.closest('.has-submenu');
                const isSubmenuToggle = parentSubmenu && this === parentSubmenu.querySelector(':scope > a');

                // Don't close menu if it's a submenu toggle on mobile
                if (isSubmenuToggle && window.innerWidth <= 768) {
                    return;
                }

                menuToggle.classList.remove('active');
                navMenu.classList.remove('active');
                if (mainNav) mainNav.classList.remove('menu-open');
                body.classList.remove('menu-open');

                // Close all submenus
                navMenu.querySelectorAll('.has-submenu.open').forEach(item => {
                    item.classList.remove('open');
                });
            });
        });
    }

    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && menuToggle.classList.contains('active')) {
            menuToggle.classList.remove('active');
            if (navMenu) navMenu.classList.remove('active');
            if (mainNav) mainNav.classList.remove('menu-open');
            body.classList.remove('menu-open');
        }
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (menuToggle.classList.contains('active') &&
            !menuToggle.contains(e.target) &&
            !navMenu?.contains(e.target)) {
            menuToggle.classList.remove('active');
            if (navMenu) navMenu.classList.remove('active');
            if (mainNav) mainNav.classList.remove('menu-open');
            body.classList.remove('menu-open');
        }
    });
}

/**
 * Sticky Header on Scroll
 */
function initStickyHeader() {
    const header = document.querySelector('.site-header');
    if (!header) return;

    let lastScroll = 0;
    const scrollThreshold = 100;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        // Add scrolled class when past threshold
        if (currentScroll > scrollThreshold) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        // Hide/show header on scroll direction
        if (currentScroll > lastScroll && currentScroll > 200) {
            header.classList.add('hidden');
        } else {
            header.classList.remove('hidden');
        }

        lastScroll = currentScroll;
    });
}

/**
 * Smooth Scroll for Anchor Links
 */
function initSmoothScroll() {
    const anchors = document.querySelectorAll('a[href^="#"]');

    anchors.forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');

            if (href === '#') return;

            const target = document.querySelector(href);
            if (!target) return;

            e.preventDefault();

            const headerHeight = document.querySelector('.site-header')?.offsetHeight || 0;
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        });
    });
}

/**
 * Hero Slider / Carousel
 */
function initSliders() {
    const sliders = document.querySelectorAll('.hero-slider');

    sliders.forEach(slider => {
        const slides = slider.querySelectorAll('.slide');
        const dots = slider.querySelector('.slider-dots');
        const prevBtn = slider.querySelector('.slider-prev');
        const nextBtn = slider.querySelector('.slider-next');

        if (slides.length <= 1) return;

        let currentIndex = 0;
        let autoplayInterval;

        // Create dots
        if (dots) {
            slides.forEach((_, index) => {
                const dot = document.createElement('button');
                dot.className = index === 0 ? 'dot active' : 'dot';
                dot.addEventListener('click', () => goToSlide(index));
                dots.appendChild(dot);
            });
        }

        function goToSlide(index) {
            slides[currentIndex].classList.remove('active');
            if (dots) {
                dots.children[currentIndex].classList.remove('active');
            }

            currentIndex = (index + slides.length) % slides.length;

            slides[currentIndex].classList.add('active');
            if (dots) {
                dots.children[currentIndex].classList.add('active');
            }
        }

        function nextSlide() {
            goToSlide(currentIndex + 1);
        }

        function prevSlide() {
            goToSlide(currentIndex - 1);
        }

        // Navigation buttons
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);
        if (nextBtn) nextBtn.addEventListener('click', nextSlide);

        // Autoplay
        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, 5000);
        }

        function stopAutoplay() {
            clearInterval(autoplayInterval);
        }

        // Pause on hover
        slider.addEventListener('mouseenter', stopAutoplay);
        slider.addEventListener('mouseleave', startAutoplay);

        // Start autoplay
        startAutoplay();

        // Touch support
        let touchStartX = 0;
        let touchEndX = 0;

        slider.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        });

        slider.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }
    });
}

/**
 * FAQ Accordions
 */
function initAccordions() {
    const accordions = document.querySelectorAll('.accordion, .faq-list');

    accordions.forEach(accordion => {
        const items = accordion.querySelectorAll('.accordion-item, .faq-item');

        items.forEach(item => {
            const header = item.querySelector('.accordion-header, .faq-question');

            if (!header) return;

            header.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const isActive = item.classList.contains('active');

                // Close all items in this accordion
                items.forEach(i => {
                    i.classList.remove('active');
                    const btn = i.querySelector('.accordion-header, .faq-question');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                });

                // Open clicked item if it wasn't active
                if (!isActive) {
                    item.classList.add('active');
                    header.setAttribute('aria-expanded', 'true');
                }
            });
        });
    });
}

/**
 * Lazy Loading Images
 */
function initLazyLoading() {
    if ('loading' in HTMLImageElement.prototype) {
        // Native lazy loading supported
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            if (img.dataset.src) {
                img.src = img.dataset.src;
            }
        });
    } else {
        // Fallback with Intersection Observer
        const lazyImages = document.querySelectorAll('img[data-src]');

        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                });
            });

            lazyImages.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback: load all images
            lazyImages.forEach(img => {
                img.src = img.dataset.src;
            });
        }
    }
}

/**
 * Scroll Animations
 */
function initAnimations() {
    const animatedElements = document.querySelectorAll('[data-animate]');

    if (!animatedElements.length) return;

    if ('IntersectionObserver' in window) {
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    animationObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        animatedElements.forEach(el => animationObserver.observe(el));
    } else {
        // Fallback: show all elements
        animatedElements.forEach(el => el.classList.add('animated'));
    }
}

/**
 * Cookie Consent Banner
 */
function initCookieConsent() {
    const consentKey = 'omniwallet_cookie_consent';
    const hasConsent = localStorage.getItem(consentKey);

    if (hasConsent) return;

    const banner = document.createElement('div');
    banner.className = 'cookie-banner';
    banner.innerHTML = `
        <div class="cookie-content">
            <p>Utilizamos cookies para mejorar tu experiencia. Al continuar navegando, aceptas nuestra
            <a href="/privacidad">política de privacidad</a>.</p>
            <div class="cookie-actions">
                <button class="btn btn-primary btn-sm" id="accept-cookies">Aceptar</button>
                <button class="btn btn-outline btn-sm" id="reject-cookies">Rechazar</button>
            </div>
        </div>
    `;

    document.body.appendChild(banner);

    // Show banner with animation
    setTimeout(() => banner.classList.add('visible'), 100);

    // Accept cookies
    document.getElementById('accept-cookies').addEventListener('click', function() {
        localStorage.setItem(consentKey, 'accepted');
        banner.classList.remove('visible');
        setTimeout(() => banner.remove(), 300);
    });

    // Reject cookies
    document.getElementById('reject-cookies').addEventListener('click', function() {
        localStorage.setItem(consentKey, 'rejected');
        banner.classList.remove('visible');
        setTimeout(() => banner.remove(), 300);
    });
}

/**
 * Form Validation Helper
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                removeFieldError(field);

                if (!field.value.trim()) {
                    isValid = false;
                    showFieldError(field, 'Este campo es obligatorio');
                } else if (field.type === 'email' && !isValidEmail(field.value)) {
                    isValid = false;
                    showFieldError(field, 'Introduce un email válido');
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    function showFieldError(field, message) {
        field.classList.add('error');
        const error = document.createElement('span');
        error.className = 'field-error';
        error.textContent = message;
        field.parentNode.appendChild(error);
    }

    function removeFieldError(field) {
        field.classList.remove('error');
        const error = field.parentNode.querySelector('.field-error');
        if (error) error.remove();
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
}

/**
 * Number Counter Animation
 */
function initCounters() {
    const counters = document.querySelectorAll('[data-counter]');

    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.counter, 10);
                const duration = parseInt(counter.dataset.duration, 10) || 2000;
                animateCounter(counter, target, duration);
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));

    function animateCounter(element, target, duration) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = formatNumber(target);
                clearInterval(timer);
            } else {
                element.textContent = formatNumber(Math.floor(current));
            }
        }, 16);
    }

    function formatNumber(num) {
        return num.toLocaleString('es-ES');
    }
}

/**
 * Utility: Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Utility: Throttle function
 */
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Animated Logo - GIF plays only on hover
 * Extracts first frame of GIF to show when not hovering
 */
function initAnimatedLogo() {
    const logoContainer = document.querySelector('.logo.has-animated-logo');
    if (!logoContainer) return;

    const canvas = logoContainer.querySelector('canvas.logo-static');
    const animatedImg = logoContainer.querySelector('img.logo-animated');

    if (!canvas || !animatedImg) return;

    const gifSrc = logoContainer.dataset.animatedSrc || animatedImg.src;

    // Create a temporary image to load the GIF and extract first frame
    const tempImg = new Image();
    tempImg.crossOrigin = 'anonymous';

    tempImg.onload = function() {
        // Set canvas size to match the image aspect ratio
        const aspectRatio = tempImg.width / tempImg.height;
        const canvasHeight = 40; // Match the logo height
        const canvasWidth = canvasHeight * aspectRatio;

        canvas.width = canvasWidth;
        canvas.height = canvasHeight;
        canvas.style.height = canvasHeight + 'px';
        canvas.style.width = canvasWidth + 'px';

        // Draw the first frame of the GIF to canvas
        const ctx = canvas.getContext('2d');
        ctx.drawImage(tempImg, 0, 0, canvasWidth, canvasHeight);
    };

    tempImg.onerror = function() {
        // If we can't load the image, just show the animated version always
        logoContainer.classList.remove('has-animated-logo');
    };

    tempImg.src = gifSrc;
}

/**
 * Sidebar Menu - Slide-in panel from right
 */
function initSidebarMenu() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarMenu = document.getElementById('sidebarMenu');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (!sidebarToggle || !sidebarMenu) return;

    // Toggle sidebar
    function toggleSidebar() {
        const isOpen = sidebarMenu.classList.contains('active');

        if (isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    function openSidebar() {
        sidebarToggle.classList.add('active');
        sidebarMenu.classList.add('active');
        if (sidebarOverlay) sidebarOverlay.classList.add('active');
        document.body.classList.add('sidebar-open');
    }

    function closeSidebar() {
        sidebarToggle.classList.remove('active');
        sidebarMenu.classList.remove('active');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
    }

    // Event listeners
    sidebarToggle.addEventListener('click', toggleSidebar);

    // Close on overlay click
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebarMenu.classList.contains('active')) {
            closeSidebar();
        }
    });

    // Handle collapsible sections (level 2)
    const sectionToggles = sidebarMenu.querySelectorAll('.sidebar-section-toggle');
    sectionToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const section = this.closest('.sidebar-section');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            // Toggle this section
            this.setAttribute('aria-expanded', !isExpanded);
            section.classList.toggle('expanded', !isExpanded);
        });
    });

    // Handle collapsible subsections (level 3)
    const subsectionToggles = sidebarMenu.querySelectorAll('.sidebar-subsection-toggle');
    subsectionToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const subsection = this.closest('.sidebar-subsection');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            this.setAttribute('aria-expanded', !isExpanded);
            subsection.classList.toggle('expanded', !isExpanded);
        });
    });

    // Close sidebar when clicking on a link
    const sidebarLinks = sidebarMenu.querySelectorAll('a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Small delay to allow navigation
            setTimeout(closeSidebar, 100);
        });
    });
}
