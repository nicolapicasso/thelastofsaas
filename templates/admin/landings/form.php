<?php
/**
 * Landing Form Template
 * Omniwallet CMS
 */
include_once TEMPLATES_PATH . '/admin/partials/icon-picker.php';
include_once TEMPLATES_PATH . '/admin/partials/image-picker.php';
$isEdit = !empty($landing);
$action = $isEdit ? "/admin/landings/{$landing['id']}/update" : '/admin/landings';
?>

<div class="page-header">
    <div class="page-header-content">
        <h1><?= $isEdit ? 'Editar Landing' : 'Nueva Landing' ?></h1>
        <p><?= $isEdit ? 'Modifica el contenido de la landing' : 'Crea una nueva landing page' ?></p>
    </div>
    <div class="page-header-actions">
        <?php if ($isEdit): ?>
            <a href="/admin/landings/<?= $landing['id'] ?>/preview" target="_blank" class="btn btn-outline">
                <i class="fas fa-eye"></i> Preview
            </a>
            <?php if (!empty($landing['is_active']) && !empty($landing['theme_slug'])): ?>
                <a href="/lp/<?= htmlspecialchars($landing['theme_slug']) ?>/<?= htmlspecialchars($landing['slug']) ?>" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Ver en web
                </a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="/admin/landings<?= $selectedThemeId ? '?theme=' . $selectedThemeId : '' ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<form method="POST" action="<?= $action ?>" class="form-container">
    <input type="hidden" name="_csrf_token" value="<?= $csrf_token ?>">

    <div class="form-grid-landing">
        <!-- Main Content -->
        <div class="form-main">
            <div class="card">
                <div class="card-header">
                    <h3>Información General</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group form-group-half">
                            <label for="theme_id">Temática *</label>
                            <select id="theme_id" name="theme_id" required>
                                <option value="">Seleccionar temática...</option>
                                <?php foreach ($themes as $theme): ?>
                                    <option value="<?= $theme['id'] ?>"
                                            <?= ($landing['theme_id'] ?? $selectedThemeId) == $theme['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($theme['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group form-group-half">
                            <label for="slug">Slug (URL)</label>
                            <input type="text" id="slug" name="slug"
                                   value="<?= htmlspecialchars($landing['slug'] ?? '') ?>"
                                   placeholder="se-genera-automaticamente">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="title">Título *</label>
                        <input type="text" id="title" name="title" required
                               value="<?= htmlspecialchars($landing['title'] ?? '') ?>"
                               placeholder="Título de la landing">
                    </div>

                    <div class="form-group">
                        <label for="subtitle">Subtítulo</label>
                        <input type="text" id="subtitle" name="subtitle"
                               value="<?= htmlspecialchars($landing['subtitle'] ?? '') ?>"
                               placeholder="Breve descripción">
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción (para el índice)</label>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="Descripción que aparecerá en el listado de landings"><?= htmlspecialchars($landing['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- HTML Content -->
            <div class="card">
                <div class="card-header">
                    <h3>Contenido HTML</h3>
                    <div class="card-header-actions">
                        <button type="button" class="btn btn-sm btn-outline" onclick="toggleFullscreen()">
                            <i class="fas fa-expand"></i> Pantalla completa
                        </button>
                    </div>
                </div>
                <div class="card-body html-editor-container">
                    <!-- Language Tabs -->
                    <div class="html-lang-tabs">
                        <button type="button" class="html-lang-tab active" data-lang="es" onclick="switchHtmlTab('es')">
                            <span class="lang-flag">ES</span> Español
                        </button>
                        <button type="button" class="html-lang-tab" data-lang="en" onclick="switchHtmlTab('en')">
                            <span class="lang-flag">EN</span> English
                        </button>
                        <button type="button" class="html-lang-tab" data-lang="it" onclick="switchHtmlTab('it')">
                            <span class="lang-flag">IT</span> Italiano
                        </button>
                        <button type="button" class="html-lang-tab" data-lang="fr" onclick="switchHtmlTab('fr')">
                            <span class="lang-flag">FR</span> Français
                        </button>
                        <button type="button" class="html-lang-tab" data-lang="de" onclick="switchHtmlTab('de')">
                            <span class="lang-flag">DE</span> Deutsch
                        </button>
                    </div>

                    <?php
                    // Get existing translations
                    $htmlTranslations = ['es' => $landing['html_content'] ?? ''];
                    if (!empty($landing['html_content_translations'])) {
                        $stored = is_string($landing['html_content_translations'])
                            ? json_decode($landing['html_content_translations'], true)
                            : $landing['html_content_translations'];
                        if (is_array($stored)) {
                            $htmlTranslations = array_merge($htmlTranslations, $stored);
                        }
                    }
                    ?>

                    <!-- Spanish (default) -->
                    <div class="html-lang-content active" data-lang="es">
                        <div class="form-group">
                            <textarea id="html_content" name="html_content" rows="30" class="code-editor"
                                      placeholder="Pega aquí el código HTML completo de la landing..."><?= htmlspecialchars($htmlTranslations['es'] ?? '') ?></textarea>
                            <small class="form-text">
                                Contenido en español (idioma por defecto). Pega el código HTML completo.
                            </small>
                        </div>
                    </div>

                    <!-- English -->
                    <div class="html-lang-content" data-lang="en" style="display: none;">
                        <div class="translate-toolbar">
                            <button type="button" class="btn btn-sm btn-outline translate-ai-btn" onclick="translateLandingHtml('en')" id="translateBtn_en">
                                <i class="fas fa-magic"></i> Traducir con IA
                            </button>
                            <span class="translate-hint">Traduce automáticamente desde el español</span>
                        </div>
                        <div class="form-group">
                            <textarea id="html_content_en" name="html_content_translations[en]" rows="30" class="code-editor"
                                      placeholder="Paste the English HTML content here (optional)..."><?= htmlspecialchars($htmlTranslations['en'] ?? '') ?></textarea>
                            <small class="form-text">
                                English version. Leave empty to use Spanish version.
                            </small>
                        </div>
                    </div>

                    <!-- Italian -->
                    <div class="html-lang-content" data-lang="it" style="display: none;">
                        <div class="translate-toolbar">
                            <button type="button" class="btn btn-sm btn-outline translate-ai-btn" onclick="translateLandingHtml('it')" id="translateBtn_it">
                                <i class="fas fa-magic"></i> Traducir con IA
                            </button>
                            <span class="translate-hint">Traduce automáticamente desde el español</span>
                        </div>
                        <div class="form-group">
                            <textarea id="html_content_it" name="html_content_translations[it]" rows="30" class="code-editor"
                                      placeholder="Incolla qui il contenuto HTML italiano (opzionale)..."><?= htmlspecialchars($htmlTranslations['it'] ?? '') ?></textarea>
                            <small class="form-text">
                                Versione italiana. Lascia vuoto per usare la versione spagnola.
                            </small>
                        </div>
                    </div>

                    <!-- French -->
                    <div class="html-lang-content" data-lang="fr" style="display: none;">
                        <div class="translate-toolbar">
                            <button type="button" class="btn btn-sm btn-outline translate-ai-btn" onclick="translateLandingHtml('fr')" id="translateBtn_fr">
                                <i class="fas fa-magic"></i> Traducir con IA
                            </button>
                            <span class="translate-hint">Traduce automáticamente desde el español</span>
                        </div>
                        <div class="form-group">
                            <textarea id="html_content_fr" name="html_content_translations[fr]" rows="30" class="code-editor"
                                      placeholder="Collez ici le contenu HTML français (optionnel)..."><?= htmlspecialchars($htmlTranslations['fr'] ?? '') ?></textarea>
                            <small class="form-text">
                                Version française. Laissez vide pour utiliser la version espagnole.
                            </small>
                        </div>
                    </div>

                    <!-- German -->
                    <div class="html-lang-content" data-lang="de" style="display: none;">
                        <div class="translate-toolbar">
                            <button type="button" class="btn btn-sm btn-outline translate-ai-btn" onclick="translateLandingHtml('de')" id="translateBtn_de">
                                <i class="fas fa-magic"></i> Traducir con IA
                            </button>
                            <span class="translate-hint">Traduce automáticamente desde el español</span>
                        </div>
                        <div class="form-group">
                            <textarea id="html_content_de" name="html_content_translations[de]" rows="30" class="code-editor"
                                      placeholder="Fügen Sie hier den deutschen HTML-Inhalt ein (optional)..."><?= htmlspecialchars($htmlTranslations['de'] ?? '') ?></textarea>
                            <small class="form-text">
                                Deutsche Version. Leer lassen, um die spanische Version zu verwenden.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO -->
            <div class="card">
                <div class="card-header">
                    <h3>SEO</h3>
                    <div class="card-header-actions">
                        <?php if ($isEdit): ?>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="generateSEO('landing', <?= $landing['id'] ?>)" title="Generar SEO con IA">
                            <i class="fas fa-magic"></i> Generar
                        </button>
                        <a href="/admin/seo/edit?type=landing&id=<?= $landing['id'] ?>" class="btn btn-sm btn-outline" title="Editar SEO avanzado">
                            <i class="fas fa-cog"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_title">Meta Título</label>
                        <input type="text" id="meta_title" name="meta_title"
                               value="<?= htmlspecialchars($landing['meta_title'] ?? '') ?>"
                               placeholder="Título para buscadores">
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Meta Descripción</label>
                        <textarea id="meta_description" name="meta_description" rows="3"
                                  placeholder="Descripción para buscadores (150-160 caracteres)"><?= htmlspecialchars($landing['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="form-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3>Publicación</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1"
                                   <?= ($landing['is_active'] ?? true) ? 'checked' : '' ?>>
                            <span>Activa</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1"
                                   <?= ($landing['is_featured'] ?? false) ? 'checked' : '' ?>>
                            <span>Destacada</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Orden</label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="<?= $landing['sort_order'] ?? 0 ?>" min="0">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar Cambios' : 'Crear Landing' ?>
                    </button>
                </div>
            </div>

            <!-- Private Access -->
            <div class="card private-access-card">
                <div class="card-header">
                    <h3><i class="fas fa-lock"></i> Acceso Privado</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_private" value="1" id="is_private"
                                   <?= ($landing['is_private'] ?? false) ? 'checked' : '' ?>
                                   onchange="togglePasswordField()">
                            <span>Landing Privada</span>
                        </label>
                        <small class="form-text">Requiere contraseña para acceder</small>
                    </div>

                    <div class="form-group password-field-group" id="passwordFieldGroup" style="display: <?= ($landing['is_private'] ?? false) ? 'block' : 'none' ?>;">
                        <label for="access_password">Contraseña de Acceso</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="access_password" name="access_password"
                                   placeholder="<?= $isEdit && !empty($landing['access_password']) ? '••••••••' : 'Nueva contraseña' ?>">
                            <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility()" title="Mostrar/ocultar">
                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                        <?php if ($isEdit && !empty($landing['access_password'])): ?>
                            <small class="form-text">Deja vacío para mantener la contraseña actual</small>
                        <?php else: ?>
                            <small class="form-text">Contraseña que los visitantes deberán introducir</small>
                        <?php endif; ?>
                    </div>

                    <div class="private-info-box" id="privateInfoBox" style="display: <?= ($landing['is_private'] ?? false) ? 'block' : 'none' ?>;">
                        <p><i class="fas fa-info-circle"></i> Las landings privadas:</p>
                        <ul>
                            <li>No se indexan en buscadores (noindex)</li>
                            <li>No aparecen en listados públicos</li>
                            <li>Ideal para propuestas y presentaciones</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Apariencia (índice)</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="icon">Icono (FontAwesome)</label>
                        <div class="icon-input-wrapper">
                            <div class="icon-input-preview">
                                <i class="<?= htmlspecialchars($landing['icon'] ?? 'fas fa-rocket') ?>"></i>
                            </div>
                            <input type="text" id="icon" name="icon"
                                   value="<?= htmlspecialchars($landing['icon'] ?? '') ?>"
                                   placeholder="fas fa-rocket">
                            <button type="button" class="icon-input-btn" onclick="openLandingIconPicker()">Elegir</button>
                        </div>
                        <small class="form-text">Para el listado</small>
                    </div>

                    <?php
                    $hasImage = !empty($landing['image']);
                    ?>
                    <div class="form-group">
                        <label>Imagen Destacada</label>
                        <div class="image-picker-field">
                            <input type="hidden" name="image" value="<?= htmlspecialchars($landing['image'] ?? '') ?>">
                            <div class="image-picker-preview <?= $hasImage ? 'has-image' : '' ?>">
                                <?php if ($hasImage): ?>
                                    <img src="<?= htmlspecialchars($landing['image']) ?>" alt="Preview">
                                <?php else: ?>
                                    <div class="preview-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>Sin imagen</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="image-picker-actions">
                                <button type="button" class="btn btn-sm btn-outline image-picker-select">
                                    <i class="fas fa-upload"></i> <?= $hasImage ? 'Cambiar' : 'Seleccionar' ?>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger image-picker-clear" style="display: <?= $hasImage ? 'flex' : 'none' ?>;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <small class="form-hint">Para mostrar en el listado de landings</small>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($isEdit && !empty($landing['views'])): ?>
                <div class="card">
                    <div class="card-header">
                        <h3>Estadísticas</h3>
                    </div>
                    <div class="card-body">
                        <div class="stat-item">
                            <span class="stat-value"><?= number_format($landing['views']) ?></span>
                            <span class="stat-label">Visitas</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Prompt Helper -->
            <div class="card prompt-helper-card">
                <div class="card-header">
                    <h3><i class="fas fa-magic"></i> Generar con IA</h3>
                </div>
                <div class="card-body">
                    <p class="prompt-description">Usa este prompt con Claude para generar el HTML de la landing:</p>
                    <div class="prompt-actions">
                        <button type="button" class="btn btn-sm btn-primary btn-block" onclick="copyPrompt()">
                            <i class="fas fa-copy"></i> Copiar Prompt
                        </button>
                        <button type="button" class="btn btn-sm btn-outline btn-block" onclick="togglePromptPreview()">
                            <i class="fas fa-eye"></i> Ver Prompt
                        </button>
                    </div>
                    <div id="promptPreview" class="prompt-preview" style="display: none;">
                        <pre id="promptText">Crea una landing page HTML completa y autocontenida para [TEMA].

REQUISITOS TÉCNICOS:
1. HTML completo con &lt;!DOCTYPE html&gt;, &lt;head&gt; y &lt;body&gt;
2. Todos los CSS dentro de etiquetas &lt;style&gt;
3. NO usar archivos CSS externos (excepto Google Fonts y FontAwesome)
4. Paleta de colores We're Sinapsis:
   - Primary: #215A6B (verde-azul corporativo)
   - Primary Light: #4C8693
   - Primary Dark: #12414C
   - Secondary: #F9AF00 (amarillo/dorado)
   - Dark: #1A1A1A (negro)
   - White: #FFFFFF
   - Gray Light: #383938
   - Accent: #FED684 (amarillo claro)
5. Fuente: Montserrat (Google Fonts) - todas las variantes 100..900
6. FontAwesome 6 para iconos
7. JavaScript en &lt;script&gt; al final del body
8. 100% responsive
9. NO incluir header ni footer (el sistema los añade)
10. Usar scroll-behavior: smooth

ESTILO VISUAL:
- Diseño limpio y profesional
- Botones con border-radius: 50px (pill-shaped)
- Cards con border-radius: 20px
- Gradientes sutiles usando primary y primary-dark
- Acentos en amarillo (#F9AF00) para CTAs destacados
- Sombras suaves: 0 10px 40px rgba(0,0,0,0.1)

ESTRUCTURA RECOMENDADA:
- Hero Section (100vh, gradiente oscuro-primary, animación de fondo)
- 3-5 secciones con animaciones al scroll
- Sección CTA final con fondo degradado

ANIMACIONES:
- Intersection Observer para scroll animations
- Elementos con opacity:0 y transform inicial
- Clase .visible para estado animado
- Transiciones 0.6s-0.8s ease

NO INCLUIR:
- Menús de navegación
- Footer
- Enlaces a archivos externos propios
- Console.log ni código debug

OBJETIVO DE LA LANDING:
[Describe aquí el objetivo]

CONTENIDO A INCLUIR:
[Lista los puntos clave]</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.form-grid-landing {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: var(--spacing-lg);
}

.form-main .card {
    margin-bottom: var(--spacing-lg);
}

.form-row {
    display: flex;
    gap: var(--spacing-lg);
}

.form-group-half {
    flex: 1;
}

.code-editor {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 13px;
    line-height: 1.5;
    tab-size: 2;
    background: #1e1e1e;
    color: #d4d4d4;
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    resize: vertical;
    min-height: 400px;
}

.html-editor-container .form-group {
    margin-bottom: 0;
}

/* Language Tabs */
.html-lang-tabs {
    display: flex;
    gap: var(--spacing-xs);
    margin-bottom: var(--spacing-md);
    border-bottom: 2px solid var(--color-gray-200);
    padding-bottom: var(--spacing-sm);
}

.html-lang-tab {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-sm) var(--spacing-md);
    background: none;
    border: none;
    border-radius: var(--radius-md) var(--radius-md) 0 0;
    cursor: pointer;
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    transition: all 0.2s ease;
}

.html-lang-tab:hover {
    background: var(--color-gray-100);
    color: var(--color-gray-800);
}

.html-lang-tab.active {
    background: var(--color-primary-light);
    color: var(--color-primary-dark);
    font-weight: 600;
}

.html-lang-tab .lang-flag {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 18px;
    background: var(--color-gray-200);
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
}

.html-lang-tab.active .lang-flag {
    background: var(--color-primary);
    color: white;
}

.html-lang-content {
    display: none;
}

.html-lang-content.active {
    display: block;
}

/* Translate Toolbar */
.translate-toolbar {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-sm) 0;
    margin-bottom: var(--spacing-sm);
    border-bottom: 1px dashed var(--color-gray-300);
}

.translate-ai-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 500;
}

.translate-ai-btn:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46a1 100%);
    color: white;
}

.translate-ai-btn:disabled {
    background: var(--color-gray-300);
    cursor: not-allowed;
}

.translate-ai-btn.loading {
    pointer-events: none;
}

.translate-ai-btn.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.translate-hint {
    font-size: 12px;
    color: var(--color-gray-500);
}

.translate-success {
    color: var(--color-success);
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.translate-error {
    color: var(--color-danger);
    font-size: 12px;
}

.card-header-actions {
    margin-left: auto;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: var(--color-primary);
}

.stat-label {
    color: var(--color-gray-600);
    font-size: var(--font-size-sm);
}

/* Prompt Helper */
.prompt-helper-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px dashed var(--color-primary);
}

.prompt-helper-card .card-header {
    background: transparent;
    border-bottom: 1px dashed var(--color-gray-300);
}

.prompt-helper-card .card-header h3 {
    color: var(--color-primary);
    font-size: var(--font-size-base);
}

.prompt-helper-card .card-header h3 i {
    margin-right: var(--spacing-sm);
}

.prompt-description {
    font-size: var(--font-size-sm);
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-md);
}

.prompt-actions {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.prompt-preview {
    margin-top: var(--spacing-md);
    max-height: 300px;
    overflow-y: auto;
    background: #1e1e1e;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
}

.prompt-preview pre {
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 11px;
    line-height: 1.5;
    color: #d4d4d4;
    white-space: pre-wrap;
    word-wrap: break-word;
    margin: 0;
}

@media (max-width: 1024px) {
    .form-grid-landing {
        grid-template-columns: 1fr;
    }

    .form-row {
        flex-direction: column;
    }
}

/* Private Access Card */
.private-access-card {
    border: 1px solid var(--color-warning, #f59e0b);
}

.private-access-card .card-header {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-bottom: 1px solid var(--color-warning, #f59e0b);
}

.private-access-card .card-header h3 {
    color: #92400e;
    font-size: var(--font-size-base);
}

.private-access-card .card-header h3 i {
    margin-right: var(--spacing-sm);
}

.password-input-wrapper {
    display: flex;
    gap: 0;
    align-items: stretch;
}

.password-input-wrapper input {
    flex: 1;
    border-radius: var(--radius-md) 0 0 var(--radius-md);
    border-right: none;
}

.btn-toggle-password {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 var(--spacing-md);
    background: var(--color-gray-100);
    border: 1px solid var(--color-gray-300);
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    cursor: pointer;
    color: var(--color-gray-600);
    transition: all 0.2s ease;
}

.btn-toggle-password:hover {
    background: var(--color-gray-200);
    color: var(--color-gray-800);
}

.private-info-box {
    margin-top: var(--spacing-md);
    padding: var(--spacing-md);
    background: #fef9e7;
    border: 1px solid #f59e0b;
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
}

.private-info-box p {
    margin: 0 0 var(--spacing-xs) 0;
    color: #92400e;
    font-weight: 500;
}

.private-info-box ul {
    margin: 0;
    padding-left: var(--spacing-lg);
    color: #78350f;
}

.private-info-box li {
    margin-bottom: 4px;
}
</style>

<script>
// Switch between language tabs for HTML content
function switchHtmlTab(lang) {
    // Update tab buttons
    document.querySelectorAll('.html-lang-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.lang === lang) {
            tab.classList.add('active');
        }
    });

    // Update content panels
    document.querySelectorAll('.html-lang-content').forEach(content => {
        content.classList.remove('active');
        content.style.display = 'none';
        if (content.dataset.lang === lang) {
            content.classList.add('active');
            content.style.display = 'block';
        }
    });
}

function toggleFullscreen() {
    const activeContent = document.querySelector('.html-lang-content.active');
    const editor = activeContent ? activeContent.querySelector('.code-editor') : document.getElementById('html_content');
    const container = document.querySelector('.html-editor-container');

    if (container.classList.contains('fullscreen')) {
        container.classList.remove('fullscreen');
        document.body.style.overflow = '';
    } else {
        container.classList.add('fullscreen');
        document.body.style.overflow = 'hidden';
    }
}

// ESC to exit fullscreen
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const container = document.querySelector('.html-editor-container.fullscreen');
        if (container) {
            container.classList.remove('fullscreen');
            document.body.style.overflow = '';
        }
    }
});

// Prompt Helper Functions
function getPromptText() {
    return `Crea una landing page HTML completa y autocontenida para [TEMA].

REQUISITOS TÉCNICOS:
1. HTML completo con <!DOCTYPE html>, <head> y <body>
2. Todos los CSS dentro de etiquetas <style>
3. NO usar archivos CSS externos (excepto Google Fonts y FontAwesome)
4. Paleta de colores Omniwallet:
   - Primary: #3E95B0
   - Primary Dark: #255664
   - Dark: #232323
   - White: #ffffff
5. Fuente: Montserrat (Google Fonts)
6. FontAwesome 6 para iconos
7. JavaScript en <script> al final del body
8. 100% responsive
9. NO incluir header ni footer (el sistema los añade)
10. Usar scroll-behavior: smooth

ESTRUCTURA RECOMENDADA:
- Hero Section (100vh, gradiente, animación de fondo)
- 3-5 secciones con animaciones al scroll
- Sección CTA final

ANIMACIONES:
- Intersection Observer para scroll animations
- Elementos con opacity:0 y transform inicial
- Clase .visible para estado animado
- Transiciones 0.6s-0.8s ease

NO INCLUIR:
- Menús de navegación
- Footer
- Enlaces a archivos externos propios
- Console.log ni código debug

OBJETIVO DE LA LANDING:
[Describe aquí el objetivo]

CONTENIDO A INCLUIR:
[Lista los puntos clave]`;
}

function copyPrompt() {
    const prompt = getPromptText();
    navigator.clipboard.writeText(prompt).then(function() {
        // Show success feedback
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-primary');

        setTimeout(function() {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
        }, 2000);
    }).catch(function(err) {
        alert('Error al copiar. Por favor, usa el botón "Ver Prompt" y copia manualmente.');
    });
}

function togglePromptPreview() {
    const preview = document.getElementById('promptPreview');
    const btn = event.target.closest('button');

    if (preview.style.display === 'none') {
        preview.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar Prompt';
    } else {
        preview.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-eye"></i> Ver Prompt';
    }
}

// Open icon picker for landings
function openLandingIconPicker() {
    const input = document.getElementById('icon');
    const previewIcon = document.querySelector('.icon-input-wrapper .icon-input-preview i');

    window.iconPicker.open(function(iconClass) {
        input.value = iconClass;
        if (previewIcon) {
            previewIcon.className = iconClass;
        }
    }, input);
}

// Update preview when typing in icon field
document.getElementById('icon').addEventListener('input', function() {
    const previewIcon = document.querySelector('.icon-input-wrapper .icon-input-preview i');
    if (previewIcon && this.value.trim()) {
        previewIcon.className = this.value.trim();
    } else if (previewIcon) {
        previewIcon.className = 'fas fa-rocket';
    }
});

// Translate landing HTML with AI
const landingId = <?= $landing['id'] ?? 0 ?>;
const csrfToken = '<?= $csrf_token ?>';

async function translateLandingHtml(targetLang) {
    const btn = document.getElementById('translateBtn_' + targetLang);
    const toolbar = btn.closest('.translate-toolbar');
    const targetTextarea = document.getElementById('html_content_' + targetLang);
    const sourceTextarea = document.getElementById('html_content');

    // Validate source HTML exists
    const sourceHtml = sourceTextarea.value.trim();
    if (!sourceHtml) {
        alert('No hay contenido HTML en español para traducir. Primero agrega el contenido en la pestaña ES.');
        return;
    }

    // Confirm if target already has content
    if (targetTextarea.value.trim()) {
        if (!confirm('Ya hay contenido en este idioma. ¿Deseas reemplazarlo con una nueva traducción?')) {
            return;
        }
    }

    // Check if landing is saved (has ID)
    if (!landingId) {
        alert('Primero guarda la landing para poder traducir el contenido.');
        return;
    }

    // Update button state
    const originalBtnHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner"></i> Traduciendo...';
    btn.classList.add('loading');
    btn.disabled = true;

    // Remove any previous status messages
    const existingStatus = toolbar.querySelector('.translate-success, .translate-error');
    if (existingStatus) existingStatus.remove();

    try {
        const formData = new FormData();
        formData.append('target_language', targetLang);
        formData.append('source_html', sourceHtml);
        formData.append('_csrf_token', csrfToken);

        const response = await fetch('/admin/landings/' + landingId + '/translate-html', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            targetTextarea.value = result.translated_html;

            // Show success message
            const successMsg = document.createElement('span');
            successMsg.className = 'translate-success';
            successMsg.innerHTML = '<i class="fas fa-check-circle"></i> Traducido correctamente';
            toolbar.appendChild(successMsg);

            // Flash the textarea to show the change
            targetTextarea.style.borderColor = 'var(--color-success)';
            setTimeout(() => {
                targetTextarea.style.borderColor = '';
            }, 2000);

            // Remove success message after delay
            setTimeout(() => {
                successMsg.remove();
            }, 5000);
        } else {
            throw new Error(result.error || 'Error desconocido');
        }

    } catch (error) {
        // Show error message
        const errorMsg = document.createElement('span');
        errorMsg.className = 'translate-error';
        errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + error.message;
        toolbar.appendChild(errorMsg);

        // Remove error message after delay
        setTimeout(() => {
            errorMsg.remove();
        }, 10000);
    } finally {
        // Restore button state
        btn.innerHTML = originalBtnHtml;
        btn.classList.remove('loading');
        btn.disabled = false;
    }
}

function generateSEO(entityType, entityId) {
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

    fetch('/admin/seo/generate-single', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            entity_type: entityType,
            entity_id: entityId,
            language: 'es',
            overwrite: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            if (data.data.meta_title) {
                document.getElementById('meta_title').value = data.data.meta_title;
            }
            if (data.data.meta_description) {
                document.getElementById('meta_description').value = data.data.meta_description;
            }
            alert('SEO generado correctamente. Revisa los campos y guarda la landing.');
        } else {
            alert('Error: ' + (data.message || 'No se pudo generar el SEO'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexion al generar SEO');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

// Private Landing Functions
function togglePasswordField() {
    const isPrivate = document.getElementById('is_private').checked;
    const passwordGroup = document.getElementById('passwordFieldGroup');
    const infoBox = document.getElementById('privateInfoBox');

    if (isPrivate) {
        passwordGroup.style.display = 'block';
        infoBox.style.display = 'block';
    } else {
        passwordGroup.style.display = 'none';
        infoBox.style.display = 'none';
    }
}

function togglePasswordVisibility() {
    const passwordInput = document.getElementById('access_password');
    const icon = document.getElementById('passwordToggleIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<style>
.html-editor-container.fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    background: white;
    padding: var(--spacing-lg);
    display: flex;
    flex-direction: column;
}

.html-editor-container.fullscreen .form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.html-editor-container.fullscreen .code-editor {
    flex: 1;
    min-height: auto;
}
</style>
