<div class="page-header">
    <h2>Configuración</h2>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>">
    <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<form method="POST" action="/admin/settings">
    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <!-- General Settings -->
    <div class="settings-section">
        <h3>General</h3>
        <div class="settings-card">
            <div class="form-group">
                <label for="site_name">Nombre del sitio</label>
                <input type="text" id="site_name" name="settings[site_name]"
                       value="<?= htmlspecialchars($settings['site_name'] ?? 'Omniwallet') ?>">
            </div>
            <div class="form-group">
                <label for="site_tagline">Eslogan</label>
                <input type="text" id="site_tagline" name="settings[site_tagline]"
                       value="<?= htmlspecialchars($settings['site_tagline'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="site_email">Email de contacto</label>
                <input type="email" id="site_email" name="settings[site_email]"
                       value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="site_phone">Teléfono</label>
                <input type="text" id="site_phone" name="settings[site_phone]"
                       value="<?= htmlspecialchars($settings['site_phone'] ?? '') ?>">
            </div>
        </div>
    </div>

    <!-- Branding Settings -->
    <div class="settings-section">
        <h3>Branding</h3>
        <div class="settings-card">
            <div class="form-row">
                <div class="form-group">
                    <label for="primary_color">Color Primario</label>
                    <input type="color" id="primary_color" name="settings[primary_color]"
                           value="<?= htmlspecialchars($settings['primary_color'] ?? '#3E95B0') ?>">
                </div>
                <div class="form-group">
                    <label for="secondary_color">Color Secundario</label>
                    <input type="color" id="secondary_color" name="settings[secondary_color]"
                           value="<?= htmlspecialchars($settings['secondary_color'] ?? '#255664') ?>">
                </div>
                <div class="form-group">
                    <label for="accent_color">Color de Acento</label>
                    <input type="color" id="accent_color" name="settings[accent_color]"
                           value="<?= htmlspecialchars($settings['accent_color'] ?? '#4DBBDD') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Logo Settings -->
    <div class="settings-section">
        <h3>Logotipos</h3>
        <div class="settings-card">
            <div class="form-row form-row-2">
                <?php
                $logoHeader = $settings['logo_header'] ?? '/assets/images/logo.svg';
                $logoFooter = $settings['logo_footer'] ?? '';
                $favicon = $settings['favicon'] ?? '/favicon.ico';
                ?>
                <div class="form-group">
                    <label>Logo Header (Principal)</label>
                    <div class="image-picker-field">
                        <input type="text" id="logo_header" name="settings[logo_header]" value="<?= htmlspecialchars($logoHeader) ?>">
                        <div class="image-picker-preview <?= !empty($logoHeader) ? 'has-image' : '' ?>">
                            <?php if (!empty($logoHeader)): ?>
                                <img src="<?= htmlspecialchars($logoHeader) ?>" alt="Logo Header">
                            <?php else: ?>
                                <div class="preview-placeholder"><i class="fas fa-image"></i><span>Sin imagen</span></div>
                            <?php endif; ?>
                        </div>
                        <div class="image-picker-actions">
                            <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                <i class="fas fa-upload"></i> <?= !empty($logoHeader) ? 'Cambiar' : 'Seleccionar' ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= !empty($logoHeader) ? 'flex' : 'none' ?>;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <small class="form-hint">Logo para el header (fondo claro). Recomendado: SVG o PNG transparente.</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Logo Footer (Alternativo)</label>
                    <div class="image-picker-field logo-field-dark">
                        <input type="text" id="logo_footer" name="settings[logo_footer]" value="<?= htmlspecialchars($logoFooter) ?>">
                        <div class="image-picker-preview <?= !empty($logoFooter) ? 'has-image' : '' ?>">
                            <?php if (!empty($logoFooter)): ?>
                                <img src="<?= htmlspecialchars($logoFooter) ?>" alt="Logo Footer">
                            <?php else: ?>
                                <div class="preview-placeholder"><i class="fas fa-image"></i><span>Sin imagen</span></div>
                            <?php endif; ?>
                        </div>
                        <div class="image-picker-actions">
                            <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                <i class="fas fa-upload"></i> <?= !empty($logoFooter) ? 'Cambiar' : 'Seleccionar' ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= !empty($logoFooter) ? 'flex' : 'none' ?>;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <small class="form-hint">Logo para el footer (fondo oscuro). Si esta vacio, se usara el logo principal.</small>
                    </div>
                </div>
            </div>
            <div class="form-group" style="margin-top: var(--spacing-md);">
                <label>Favicon</label>
                <div class="image-picker-field">
                    <input type="text" id="favicon" name="settings[favicon]" value="<?= htmlspecialchars($favicon) ?>">
                    <div class="image-picker-preview favicon-preview <?= !empty($favicon) ? 'has-image' : '' ?>">
                        <?php if (!empty($favicon)): ?>
                            <img src="<?= htmlspecialchars($favicon) ?>" alt="Favicon">
                        <?php else: ?>
                            <div class="preview-placeholder"><i class="fas fa-image"></i><span>Sin imagen</span></div>
                        <?php endif; ?>
                    </div>
                    <div class="image-picker-actions">
                        <button type="button" class="btn btn-sm btn-outline image-picker-select">
                            <i class="fas fa-upload"></i> <?= !empty($favicon) ? 'Cambiar' : 'Seleccionar' ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= !empty($favicon) ? 'flex' : 'none' ?>;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <small class="form-hint">Icono del sitio. Recomendado: .ico, .png 32x32 o .svg</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Typography Settings -->
    <div class="settings-section">
        <h3>Tipografia</h3>
        <div class="settings-card">
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label for="font_primary">Fuente Principal (Titulos)</label>
                    <select id="font_primary" name="settings[font_primary]" class="font-selector">
                        <?php
                        $fonts = [
                            'Inter' => 'Inter',
                            'Roboto' => 'Roboto',
                            'Open Sans' => 'Open Sans',
                            'Lato' => 'Lato',
                            'Montserrat' => 'Montserrat',
                            'Poppins' => 'Poppins',
                            'Raleway' => 'Raleway',
                            'Nunito' => 'Nunito',
                            'Playfair Display' => 'Playfair Display',
                            'Merriweather' => 'Merriweather',
                            'Source Sans Pro' => 'Source Sans Pro',
                            'Ubuntu' => 'Ubuntu',
                            'Work Sans' => 'Work Sans',
                            'DM Sans' => 'DM Sans',
                            'Plus Jakarta Sans' => 'Plus Jakarta Sans',
                        ];
                        $selectedPrimary = $settings['font_primary'] ?? 'Inter';
                        foreach ($fonts as $value => $label):
                        ?>
                            <option value="<?= $value ?>" <?= $selectedPrimary === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="font-preview" id="font_primary_preview" style="font-family: '<?= htmlspecialchars($selectedPrimary) ?>', sans-serif;">
                        Aa Bb Cc Dd Ee Ff Gg - 123456789
                    </div>
                </div>
                <div class="form-group">
                    <label for="font_secondary">Fuente Secundaria (Cuerpo)</label>
                    <select id="font_secondary" name="settings[font_secondary]" class="font-selector">
                        <?php
                        $selectedSecondary = $settings['font_secondary'] ?? 'Inter';
                        foreach ($fonts as $value => $label):
                        ?>
                            <option value="<?= $value ?>" <?= $selectedSecondary === $value ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="font-preview" id="font_secondary_preview" style="font-family: '<?= htmlspecialchars($selectedSecondary) ?>', sans-serif;">
                        Aa Bb Cc Dd Ee Ff Gg - 123456789
                    </div>
                </div>
            </div>
            <div class="info-box" style="margin-top: var(--spacing-md);">
                <p><i class="fas fa-info-circle"></i> Las fuentes se cargan automaticamente desde Google Fonts. El cambio sera visible en el frontend despues de guardar.</p>
            </div>
        </div>
    </div>

    <!-- Localization Settings -->
    <div class="settings-section">
        <h3>Localización</h3>
        <div class="settings-card">
            <div class="form-group">
                <label for="default_language">Idioma por defecto</label>
                <select id="default_language" name="settings[default_language]">
                    <option value="es" <?= ($settings['default_language'] ?? 'es') === 'es' ? 'selected' : '' ?>>Español</option>
                    <option value="en" <?= ($settings['default_language'] ?? '') === 'en' ? 'selected' : '' ?>>English</option>
                    <option value="it" <?= ($settings['default_language'] ?? '') === 'it' ? 'selected' : '' ?>>Italiano</option>
                    <option value="fr" <?= ($settings['default_language'] ?? '') === 'fr' ? 'selected' : '' ?>>Français</option>
                    <option value="de" <?= ($settings['default_language'] ?? '') === 'de' ? 'selected' : '' ?>>Deutsch</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Analytics & Tracking Settings -->
    <div class="settings-section">
        <h3>Analytics y Seguimiento</h3>
        <div class="settings-card">
            <div class="form-group">
                <label for="gtm_id">Google Tag Manager ID</label>
                <input type="text" id="gtm_id" name="settings[gtm_id]"
                       value="<?= htmlspecialchars($settings['gtm_id'] ?? '') ?>"
                       placeholder="GTM-XXXXXXX">
                <small class="form-help">Recomendado. GTM permite gestionar todos tus tags (Analytics, pixels, conversiones) desde un solo lugar.</small>
            </div>
            <div class="form-group">
                <label for="google_analytics_id">Google Analytics ID (directo)</label>
                <input type="text" id="google_analytics_id" name="settings[google_analytics_id]"
                       value="<?= htmlspecialchars($settings['google_analytics_id'] ?? '') ?>"
                       placeholder="G-XXXXXXXXXX">
                <small class="form-help">Solo si no usas GTM. Si configuras GTM, añade Analytics desde allí.</small>
            </div>

            <div class="tracking-info-box">
                <h4><i class="fas fa-chart-line"></i> Eventos de seguimiento disponibles</h4>
                <p>El sistema envía automáticamente estos eventos al dataLayer de GTM:</p>
                <ul class="events-list">
                    <li><code>contact_form_submit</code> - Envío de formulario de contacto</li>
                    <li><code>contact_form_success</code> - Formulario enviado con éxito (conversión)</li>
                    <li><code>pricing_calculator_use</code> - Uso de la calculadora de precios</li>
                    <li><code>cta_click</code> - Click en botones CTA principales</li>
                    <li><code>page_view</code> - Vista de página (con datos adicionales)</li>
                </ul>
                <p class="events-note">Configura estos eventos como conversiones en GTM para trackear campañas.</p>
            </div>
        </div>
    </div>

    <!-- Integrations Settings -->
    <div class="settings-section">
        <h3>Integraciones</h3>
        <div class="settings-card">
            <div class="form-group">
                <label for="openai_api_key">OpenAI API Key</label>
                <input type="password" id="openai_api_key" name="settings[openai_api_key]"
                       value="<?= htmlspecialchars($settings['openai_api_key'] ?? '') ?>"
                       placeholder="sk-...">
                <small class="form-help">Necesaria para las traducciones automáticas con IA</small>
            </div>
        </div>
    </div>

    <!-- Floating Contact Form Settings -->
    <div class="settings-section">
        <h3>Formulario de Contacto Flotante</h3>
        <div class="settings-card">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="settings[floating_form_enabled]" value="1"
                           <?= ($settings['floating_form_enabled'] ?? true) ? 'checked' : '' ?>>
                    <span>Activar formulario flotante</span>
                </label>
                <small class="form-help">Muestra un botón flotante en la esquina inferior derecha para contacto rápido.</small>
            </div>

            <div class="form-row form-row-2" style="margin-top: var(--spacing-md);">
                <div class="form-group">
                    <label for="floating_form_title">Título del formulario</label>
                    <input type="text" id="floating_form_title" name="settings[floating_form_title]"
                           value="<?= htmlspecialchars($settings['floating_form_title'] ?? 'Contáctanos') ?>"
                           placeholder="Contáctanos">
                </div>
                <div class="form-group">
                    <label for="floating_form_subtitle">Subtítulo</label>
                    <input type="text" id="floating_form_subtitle" name="settings[floating_form_subtitle]"
                           value="<?= htmlspecialchars($settings['floating_form_subtitle'] ?? '¿En qué podemos ayudarte?') ?>"
                           placeholder="¿En qué podemos ayudarte?">
                </div>
            </div>

            <div class="form-row form-row-2">
                <div class="form-group">
                    <label for="floating_form_button_text">Texto del botón (opcional)</label>
                    <input type="text" id="floating_form_button_text" name="settings[floating_form_button_text]"
                           value="<?= htmlspecialchars($settings['floating_form_button_text'] ?? '') ?>"
                           placeholder="Contactar">
                    <small class="form-help">Si está vacío, solo se muestra el icono.</small>
                </div>
                <div class="form-group">
                    <label for="floating_form_button_icon">Icono del botón</label>
                    <input type="text" id="floating_form_button_icon" name="settings[floating_form_button_icon]"
                           value="<?= htmlspecialchars($settings['floating_form_button_icon'] ?? 'fas fa-comment-dots') ?>"
                           placeholder="fas fa-comment-dots">
                    <small class="form-help">Clase de FontAwesome (ej: fas fa-envelope, fas fa-headset)</small>
                </div>
            </div>

            <div class="form-row form-row-2">
                <div class="form-group">
                    <label for="floating_form_success_title">Título de éxito</label>
                    <input type="text" id="floating_form_success_title" name="settings[floating_form_success_title]"
                           value="<?= htmlspecialchars($settings['floating_form_success_title'] ?? '¡Mensaje enviado!') ?>"
                           placeholder="¡Mensaje enviado!">
                </div>
                <div class="form-group">
                    <label for="floating_form_success_message">Mensaje de éxito</label>
                    <input type="text" id="floating_form_success_message" name="settings[floating_form_success_message]"
                           value="<?= htmlspecialchars($settings['floating_form_success_message'] ?? 'Te responderemos lo antes posible.') ?>"
                           placeholder="Te responderemos lo antes posible.">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Settings -->
    <div class="settings-section">
        <h3>Footer</h3>
        <div class="settings-card">
            <div class="form-group">
                <label for="footer_tagline">Tagline del Footer (Español)</label>
                <textarea id="footer_tagline" name="settings[footer_tagline]" rows="2"
                          placeholder="La plataforma de pagos y wallet digital que impulsa tu negocio."><?= htmlspecialchars($settings['footer_tagline'] ?? '') ?></textarea>
            </div>

            <div class="translations-section">
                <h4><i class="fas fa-language"></i> Traducciones del Tagline</h4>
                <div class="translations-grid">
                    <div class="form-group">
                        <label for="footer_tagline_en">English</label>
                        <textarea id="footer_tagline_en" name="settings[footer_tagline_en]" rows="2"
                                  placeholder="The payment platform and digital wallet that powers your business."><?= htmlspecialchars($settings['footer_tagline_en'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="footer_tagline_it">Italiano</label>
                        <textarea id="footer_tagline_it" name="settings[footer_tagline_it]" rows="2"
                                  placeholder="La piattaforma di pagamento e wallet digitale che potenzia il tuo business."><?= htmlspecialchars($settings['footer_tagline_it'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="footer_tagline_fr">Français</label>
                        <textarea id="footer_tagline_fr" name="settings[footer_tagline_fr]" rows="2"
                                  placeholder="La plateforme de paiement et portefeuille numérique qui propulse votre entreprise."><?= htmlspecialchars($settings['footer_tagline_fr'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="footer_tagline_de">Deutsch</label>
                        <textarea id="footer_tagline_de" name="settings[footer_tagline_de]" rows="2"
                                  placeholder="Die Zahlungsplattform und digitale Wallet, die Ihr Unternehmen antreibt."><?= htmlspecialchars($settings['footer_tagline_de'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: var(--spacing-lg);">
                <label for="footer_copyright">Copyright (Español)</label>
                <input type="text" id="footer_copyright" name="settings[footer_copyright]"
                       value="<?= htmlspecialchars($settings['footer_copyright'] ?? '© {year} Omniwallet. Todos los derechos reservados.') ?>"
                       placeholder="© {year} Omniwallet. Todos los derechos reservados.">
                <small class="form-help">Usa {year} para insertar el año actual automáticamente</small>
            </div>

            <!-- Partner Badges Section -->
            <div class="partner-badges-section" style="margin-top: var(--spacing-xl); padding-top: var(--spacing-lg); border-top: 1px solid var(--color-gray-200);">
                <h4><i class="fas fa-certificate"></i> Sellos de Partner Certificado</h4>
                <p class="section-description" style="color: var(--color-gray-600); margin-bottom: var(--spacing-md);">
                    Añade los sellos de certificación de partners (Google Partner, Meta Business Partner, etc.)
                </p>

                <?php
                $partnerBadges = json_decode($settings['partner_badges'] ?? '[]', true) ?: [];
                ?>

                <div class="form-group">
                    <label>Imágenes de Sellos</label>
                    <div id="partnerBadgesContainer" class="partner-badges-grid">
                        <?php foreach ($partnerBadges as $index => $badge): ?>
                        <div class="partner-badge-item" data-index="<?= $index ?>">
                            <img src="<?= htmlspecialchars($badge['image'] ?? '') ?>" alt="<?= htmlspecialchars($badge['name'] ?? 'Partner Badge') ?>">
                            <input type="hidden" name="partner_badges[<?= $index ?>][image]" value="<?= htmlspecialchars($badge['image'] ?? '') ?>">
                            <input type="hidden" name="partner_badges[<?= $index ?>][name]" value="<?= htmlspecialchars($badge['name'] ?? '') ?>">
                            <input type="hidden" name="partner_badges[<?= $index ?>][url]" value="<?= htmlspecialchars($badge['url'] ?? '') ?>">
                            <div class="badge-overlay">
                                <button type="button" class="btn-badge-edit" onclick="editPartnerBadge(<?= $index ?>)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn-badge-delete" onclick="removePartnerBadge(<?= $index ?>)" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" onclick="addPartnerBadge()" style="margin-top: var(--spacing-sm);">
                        <i class="fas fa-plus"></i> Añadir sello de imagen
                    </button>
                    <small class="form-hint">Sube imágenes de tus certificaciones de partner. Formatos recomendados: PNG transparente o SVG.</small>
                </div>

                <div class="form-group" style="margin-top: var(--spacing-lg);">
                    <label for="partner_scripts">Scripts de Sellos (Google Partner, Meta, etc.)</label>
                    <textarea id="partner_scripts" name="settings[partner_scripts]" rows="6"
                              placeholder="<!-- Pega aquí los scripts de verificación de partner -->"
                              style="font-family: monospace; font-size: 13px;"><?= htmlspecialchars($settings['partner_scripts'] ?? '') ?></textarea>
                    <small class="form-hint">Pega aquí los scripts HTML/JavaScript proporcionados por Google, Meta u otras plataformas para mostrar sus badges de certificación.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar configuración</button>
    </div>
</form>

<!-- Partner Badge Modal -->
<div class="badge-modal" id="badgeModal">
    <div class="badge-modal-content">
        <h4 id="badgeModalTitle">Añadir Sello de Partner</h4>
        <input type="hidden" id="badgeEditIndex" value="">

        <div class="form-group">
            <label for="badgeName">Nombre del Partner *</label>
            <input type="text" id="badgeName" placeholder="Ej: Google Partner" required>
        </div>

        <div class="form-group">
            <label for="badgeImage">Imagen del Sello *</label>
            <div class="image-picker-field">
                <input type="text" id="badgeImage" placeholder="URL de la imagen" required>
                <div class="image-picker-preview" id="badgeImagePreview">
                    <div class="preview-placeholder"><i class="fas fa-certificate"></i><span>Sin imagen</span></div>
                </div>
                <div class="image-picker-actions">
                    <button type="button" class="btn btn-sm btn-outline image-picker-select">
                        <i class="fas fa-upload"></i> Seleccionar
                    </button>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="badgeUrl">URL de enlace (opcional)</label>
            <input type="url" id="badgeUrl" placeholder="https://...">
            <small class="form-hint">Si se especifica, el sello enlazará a esta URL</small>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-outline" onclick="closeBadgeModal()">Cancelar</button>
            <button type="button" class="btn btn-primary" onclick="saveBadge()">
                <i class="fas fa-save"></i> Guardar
            </button>
        </div>
    </div>
</div>

<style>
.settings-section {
    margin-bottom: var(--spacing-xl);
}

.settings-section h3 {
    margin-bottom: var(--spacing-md);
    padding-bottom: var(--spacing-sm);
    border-bottom: 1px solid var(--color-gray-200);
}

.settings-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-sm);
}

.form-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-md);
}

input[type="color"] {
    width: 60px;
    height: 40px;
    padding: 4px;
    border-radius: var(--radius-md);
}

.form-actions {
    margin-top: var(--spacing-xl);
}

/* Translations section */
.translations-section {
    margin-top: var(--spacing-lg);
    padding-top: var(--spacing-md);
    border-top: 1px solid var(--color-gray-200);
}

.translations-section h4 {
    margin: 0 0 var(--spacing-md) 0;
    font-size: 14px;
    color: var(--color-gray-600);
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.translations-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-md);
}

@media (max-width: 768px) {
    .translations-grid {
        grid-template-columns: 1fr;
    }
    .form-row-2 {
        grid-template-columns: 1fr;
    }
}

/* Logo settings */
.form-row-2 {
    grid-template-columns: repeat(2, 1fr);
}

/* Image picker in settings */
.settings-section .image-picker-field {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.settings-section .image-picker-field input[type="text"] {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-size: 14px;
}

.settings-section .image-picker-preview {
    padding: var(--spacing-md);
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.settings-section .logo-field-dark .image-picker-preview {
    background: var(--color-gray-800);
}

.settings-section .image-picker-preview img {
    max-height: 60px;
    max-width: 100%;
    object-fit: contain;
}

.settings-section .favicon-preview img {
    max-height: 32px;
}

.settings-section .image-picker-preview .preview-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--color-gray-400);
}

.settings-section .logo-field-dark .preview-placeholder {
    color: var(--color-gray-500);
}

.settings-section .image-picker-preview .preview-placeholder i {
    font-size: 24px;
}

.settings-section .image-picker-actions {
    display: flex;
    gap: var(--spacing-xs);
}

.settings-section .form-hint {
    font-size: 12px;
    color: var(--color-gray-500);
}

/* Font settings */
.font-selector {
    width: 100%;
}

.font-preview {
    margin-top: var(--spacing-sm);
    padding: var(--spacing-md);
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    font-size: 18px;
    text-align: center;
}

.info-box {
    background: var(--color-info-light, #e8f4fd);
    border: 1px solid var(--color-info, #3b82f6);
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    font-size: 14px;
    color: var(--color-info-dark, #1e40af);
}

.info-box i {
    margin-right: var(--spacing-xs);
}

/* Tracking info box */
.tracking-info-box {
    margin-top: var(--spacing-lg);
    padding: var(--spacing-lg);
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #86efac;
    border-radius: var(--radius-lg);
}

.tracking-info-box h4 {
    margin: 0 0 var(--spacing-sm) 0;
    font-size: 14px;
    color: #166534;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.tracking-info-box p {
    margin: 0 0 var(--spacing-md) 0;
    font-size: 13px;
    color: #15803d;
}

.events-list {
    list-style: none;
    padding: 0;
    margin: 0 0 var(--spacing-md) 0;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-xs);
}

.events-list li {
    font-size: 12px;
    color: #166534;
    padding: var(--spacing-xs) var(--spacing-sm);
    background: rgba(255,255,255,0.7);
    border-radius: var(--radius-sm);
}

.events-list code {
    font-weight: 600;
    color: #14532d;
}

.events-note {
    font-size: 12px !important;
    font-style: italic;
    margin: 0 !important;
}

@media (max-width: 768px) {
    .events-list {
        grid-template-columns: 1fr;
    }
}

/* Partner Badges Styles */
.partner-badges-grid {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-md);
    min-height: 80px;
    padding: var(--spacing-md);
    background: var(--color-gray-50);
    border-radius: var(--radius-md);
    border: 2px dashed var(--color-gray-300);
}

.partner-badge-item {
    position: relative;
    width: 120px;
    height: 80px;
    background: white;
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.partner-badge-item img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 8px;
}

.partner-badge-item .badge-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.2s;
}

.partner-badge-item:hover .badge-overlay {
    opacity: 1;
}

.btn-badge-edit,
.btn-badge-delete {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.btn-badge-edit {
    background: var(--color-primary);
    color: white;
}

.btn-badge-delete {
    background: var(--color-danger);
    color: white;
}

.btn-badge-edit:hover,
.btn-badge-delete:hover {
    transform: scale(1.1);
}

/* Partner Badge Modal */
.badge-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10000;
}

.badge-modal.active {
    display: flex;
}

.badge-modal-content {
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    width: 100%;
    max-width: 450px;
    box-shadow: var(--shadow-xl);
}

.badge-modal-content h4 {
    margin: 0 0 var(--spacing-md) 0;
}

.badge-modal-content .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-sm);
    margin-top: var(--spacing-lg);
}
</style>

<script>
// Partner Badge Management Functions
let badgeIndex = <?= count($partnerBadges) ?>;

function addPartnerBadge() {
    document.getElementById('badgeModalTitle').textContent = 'Añadir Sello de Partner';
    document.getElementById('badgeEditIndex').value = '';
    document.getElementById('badgeName').value = '';
    document.getElementById('badgeImage').value = '';
    document.getElementById('badgeUrl').value = '';
    document.getElementById('badgeImagePreview').innerHTML = '<div class="preview-placeholder"><i class="fas fa-certificate"></i><span>Sin imagen</span></div>';
    document.getElementById('badgeModal').classList.add('active');
}

function editPartnerBadge(index) {
    const item = document.querySelector('.partner-badge-item[data-index="' + index + '"]');
    if (!item) return;

    const name = item.querySelector('input[name*="[name]"]').value;
    const image = item.querySelector('input[name*="[image]"]').value;
    const url = item.querySelector('input[name*="[url]"]').value;

    document.getElementById('badgeModalTitle').textContent = 'Editar Sello de Partner';
    document.getElementById('badgeEditIndex').value = index;
    document.getElementById('badgeName').value = name;
    document.getElementById('badgeImage').value = image;
    document.getElementById('badgeUrl').value = url;

    const preview = document.getElementById('badgeImagePreview');
    if (image) {
        preview.innerHTML = '<img src="' + image + '" alt="Preview">';
    } else {
        preview.innerHTML = '<div class="preview-placeholder"><i class="fas fa-certificate"></i><span>Sin imagen</span></div>';
    }

    document.getElementById('badgeModal').classList.add('active');
}

function saveBadge() {
    const name = document.getElementById('badgeName').value.trim();
    const image = document.getElementById('badgeImage').value.trim();
    const url = document.getElementById('badgeUrl').value.trim();
    const editIndex = document.getElementById('badgeEditIndex').value;

    if (!name || !image) {
        alert('El nombre y la imagen son obligatorios');
        return;
    }

    if (editIndex !== '') {
        // Update existing badge
        const item = document.querySelector('.partner-badge-item[data-index="' + editIndex + '"]');
        if (item) {
            item.querySelector('img').src = image;
            item.querySelector('input[name*="[name]"]').value = name;
            item.querySelector('input[name*="[image]"]').value = image;
            item.querySelector('input[name*="[url]"]').value = url;
        }
    } else {
        // Add new badge
        const container = document.getElementById('partnerBadgesContainer');
        const newItem = document.createElement('div');
        newItem.className = 'partner-badge-item';
        newItem.dataset.index = badgeIndex;
        newItem.innerHTML = `
            <img src="${image}" alt="${name}">
            <input type="hidden" name="partner_badges[${badgeIndex}][image]" value="${image}">
            <input type="hidden" name="partner_badges[${badgeIndex}][name]" value="${name}">
            <input type="hidden" name="partner_badges[${badgeIndex}][url]" value="${url}">
            <div class="badge-overlay">
                <button type="button" class="btn-badge-edit" onclick="editPartnerBadge(${badgeIndex})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn-badge-delete" onclick="removePartnerBadge(${badgeIndex})" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newItem);
        badgeIndex++;
    }

    closeBadgeModal();
}

function removePartnerBadge(index) {
    if (!confirm('¿Eliminar este sello de partner?')) return;
    const item = document.querySelector('.partner-badge-item[data-index="' + index + '"]');
    if (item) {
        item.remove();
    }
}

function closeBadgeModal() {
    document.getElementById('badgeModal').classList.remove('active');
}

// Close modal on overlay click
document.getElementById('badgeModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeBadgeModal();
});

// Badge image picker integration
document.addEventListener('DOMContentLoaded', function() {
    const badgeImageInput = document.getElementById('badgeImage');
    if (badgeImageInput) {
        badgeImageInput.addEventListener('change', function() {
            const preview = document.getElementById('badgeImagePreview');
            if (this.value) {
                preview.innerHTML = '<img src="' + this.value + '" alt="Preview">';
            } else {
                preview.innerHTML = '<div class="preview-placeholder"><i class="fas fa-certificate"></i><span>Sin imagen</span></div>';
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Update preview when input value changes (e.g., after ImagePicker selection or manual edit)
    ['logo_header', 'logo_footer', 'favicon'].forEach(function(id) {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('change', function() {
                const pickerField = this.closest('.image-picker-field');
                if (pickerField) {
                    const preview = pickerField.querySelector('.image-picker-preview');
                    if (preview && this.value) {
                        preview.innerHTML = '<img src="' + this.value + '" alt="Preview">';
                        preview.classList.add('has-image');
                    } else if (preview) {
                        preview.innerHTML = '<div class="preview-placeholder"><i class="fas fa-image"></i><span>Sin imagen</span></div>';
                        preview.classList.remove('has-image');
                    }
                }
            });
        }
    });

    // Font preview update
    const fontSelectors = document.querySelectorAll('.font-selector');
    fontSelectors.forEach(function(select) {
        select.addEventListener('change', function() {
            const previewId = this.id + '_preview';
            const preview = document.getElementById(previewId);
            if (preview) {
                // Load the font from Google Fonts
                const fontName = this.value.replace(/ /g, '+');
                const link = document.createElement('link');
                link.href = 'https://fonts.googleapis.com/css2?family=' + fontName + ':wght@400;700&display=swap';
                link.rel = 'stylesheet';
                document.head.appendChild(link);

                // Update preview
                preview.style.fontFamily = "'" + this.value + "', sans-serif";
            }
        });
    });

    // Load initial fonts for preview
    const fonts = [];
    fontSelectors.forEach(function(select) {
        if (select.value && !fonts.includes(select.value)) {
            fonts.push(select.value);
        }
    });
    if (fonts.length > 0) {
        const fontFamilies = fonts.map(f => f.replace(/ /g, '+')).join('&family=');
        const link = document.createElement('link');
        link.href = 'https://fonts.googleapis.com/css2?family=' + fontFamilies + ':wght@400;700&display=swap';
        link.rel = 'stylesheet';
        document.head.appendChild(link);
    }
});
</script>
