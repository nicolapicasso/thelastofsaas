<?php
/**
 * Cookie Consent Banner & Modal
 * Omniwallet CMS
 */

// Cookie icon SVG
$cookieIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10c0-.33-.02-.65-.05-.97-.27.05-.55.08-.85.08-2.3 0-4.17-1.87-4.17-4.17 0-.3.03-.58.08-.85-.32-.03-.64-.05-.97-.05-1.38 0-2.67.35-3.8.97-.23-.29-.38-.65-.38-1.05 0-.95.77-1.72 1.72-1.72.4 0 .76.15 1.05.38.62-.13 1.27-.2 1.93-.2.33 0 .65.02.97.05.03-.3.05-.63.05-.97 0-1.1-.9-2-2-2-.55 0-1.05.22-1.41.59-.65-.38-1.38-.59-2.17-.59zm-2 5.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5-1.5-.67-1.5-1.5.67-1.5 1.5-1.5zm-3 4c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5-1.5-.67-1.5-1.5.67-1.5 1.5-1.5zm5 3c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5-1.5-.67-1.5-1.5.67-1.5 1.5-1.5z"/></svg>';

$closeIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>';

$settingsIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.31.06-.63.06-.94 0-.31-.02-.63-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>';
?>

<!-- Cookie Consent Banner -->
<div class="cookie-consent">
    <!-- Initial Banner -->
    <div class="cookie-banner" id="cookieBanner">
        <div class="cookie-banner-content">
            <div class="cookie-banner-text">
                <h3><?= $cookieIcon ?> Utilizamos cookies</h3>
                <p>
                    Usamos cookies propias y de terceros para mejorar tu experiencia y analizar el uso del sitio.
                    <a href="/privacidad" target="_blank">Más información</a>
                </p>
            </div>
            <div class="cookie-banner-actions">
                <button class="cookie-btn cookie-btn-link" onclick="CookieConsent.showPreferences()">
                    Configurar
                </button>
                <button class="cookie-btn cookie-btn-secondary" onclick="CookieConsent.rejectAll()">
                    Rechazar
                </button>
                <button class="cookie-btn cookie-btn-primary" onclick="CookieConsent.acceptAll()">
                    Aceptar todo
                </button>
            </div>
        </div>
    </div>

    <!-- Preferences Modal -->
    <div class="cookie-modal-overlay" id="cookieModal">
        <div class="cookie-modal">
            <div class="cookie-modal-header">
                <h2><?= $cookieIcon ?> Preferencias de Cookies</h2>
                <button class="cookie-modal-close" onclick="CookieConsent.hidePreferences()">
                    <?= $closeIcon ?>
                </button>
            </div>
            <div class="cookie-modal-body">
                <p class="cookie-modal-intro">
                    Utilizamos cookies para mejorar tu experiencia en nuestro sitio web.
                    Puedes elegir qué categorías de cookies deseas permitir.
                    Ten en cuenta que bloquear algunas cookies puede afectar tu experiencia.
                </p>

                <!-- Necessary Cookies -->
                <div class="cookie-category">
                    <div class="cookie-category-header" onclick="CookieConsent.toggleCategory(this)">
                        <div class="cookie-category-info">
                            <h4>Cookies Necesarias</h4>
                            <p>Esenciales para el funcionamiento del sitio</p>
                        </div>
                        <label class="cookie-toggle">
                            <input type="checkbox" checked disabled data-category="necessary">
                            <span class="cookie-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="cookie-category-details">
                        <ul>
                            <li>Sesión de usuario</li>
                            <li>Token de seguridad (CSRF)</li>
                            <li>Preferencias de cookies</li>
                            <li>reCAPTCHA (protección contra spam)</li>
                        </ul>
                    </div>
                </div>

                <!-- Analytics Cookies -->
                <div class="cookie-category">
                    <div class="cookie-category-header" onclick="CookieConsent.toggleCategory(this)">
                        <div class="cookie-category-info">
                            <h4>Cookies Analíticas</h4>
                            <p>Nos ayudan a entender cómo usas el sitio</p>
                        </div>
                        <label class="cookie-toggle" onclick="event.stopPropagation()">
                            <input type="checkbox" data-category="analytics">
                            <span class="cookie-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="cookie-category-details">
                        <ul>
                            <li>Google Analytics</li>
                            <li>Estadísticas de uso</li>
                            <li>Páginas visitadas</li>
                        </ul>
                    </div>
                </div>

                <!-- Marketing Cookies -->
                <div class="cookie-category">
                    <div class="cookie-category-header" onclick="CookieConsent.toggleCategory(this)">
                        <div class="cookie-category-info">
                            <h4>Cookies de Marketing</h4>
                            <p>Utilizadas para mostrarte publicidad relevante</p>
                        </div>
                        <label class="cookie-toggle" onclick="event.stopPropagation()">
                            <input type="checkbox" data-category="marketing">
                            <span class="cookie-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="cookie-category-details">
                        <ul>
                            <li>Google Ads</li>
                            <li>Facebook Pixel</li>
                            <li>Remarketing</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="cookie-modal-footer">
                <div class="cookie-modal-footer-left">
                    <button class="cookie-btn cookie-btn-secondary" onclick="CookieConsent.rejectAll()">
                        Rechazar todo
                    </button>
                </div>
                <button class="cookie-btn cookie-btn-primary" onclick="CookieConsent.savePreferences()">
                    Guardar preferencias
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Settings Button (shown after consent) -->
    <button class="cookie-settings-btn" id="cookieSettingsBtn" onclick="CookieConsent.showPreferences()" title="Configurar cookies">
        <?= $cookieIcon ?>
    </button>
</div>
