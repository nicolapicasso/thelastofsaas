<?php
/**
 * Landing Page Layout
 * Special layout that doesn't load frontend.css to avoid style conflicts
 * Omniwallet CMS
 */
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?? 'es' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $seo->renderMetaTags() ?>

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Minimal Header Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
        }

        /* Floating Navigation Menu */
        .landing-nav {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            gap: 10px;
        }

        .landing-nav-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.95);
            color: #232323;
            text-decoration: none;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .landing-nav-btn:hover {
            background: #215A6B;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(33, 90, 107, 0.3);
        }

        .landing-nav-btn i {
            font-size: 16px;
        }

        .landing-nav-btn.btn-print:hover {
            background: #4C8693;
        }

        .landing-nav-btn.btn-pdf:hover {
            background: #F9AF00;
            color: #1A1A1A;
        }

        /* Print Styles - Comprehensive fix for landing pages */
        @media print {
            /* Hide navigation and UI elements */
            .landing-nav,
            .page-breadcrumb-bar,
            .pdf-modal-overlay {
                display: none !important;
            }

            /* Force colors and backgrounds to print */
            * {
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                /* Force visibility on ALL elements */
                opacity: 1 !important;
                visibility: visible !important;
                /* Disable all animations */
                animation: none !important;
                animation-delay: 0s !important;
                transition: none !important;
            }

            /* Override transforms - using attribute selector to target inline styles */
            *[style*="transform"] {
                transform: none !important;
            }

            /* Override opacity - target inline styles specifically */
            *[style*="opacity"] {
                opacity: 1 !important;
            }

            /* Body setup */
            body {
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            /* Fix all containers */
            html, body, section, div, article, header, main, footer, aside {
                visibility: visible !important;
                opacity: 1 !important;
                transform: none !important;
                overflow: visible !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
            }

            /* Fix sections with full viewport height */
            section, [class*="hero"], [class*="section"], [class*="slide"] {
                height: auto !important;
                min-height: auto !important;
                position: relative !important;
                page-break-inside: avoid;
                page-break-after: auto;
            }

            /* Ensure all text elements are visible */
            h1, h2, h3, h4, h5, h6, p, span, li, a, label, strong, em, b, i {
                visibility: visible !important;
                opacity: 1 !important;
                transform: none !important;
            }

            /* Fix positioned elements */
            *[style*="position: fixed"],
            *[style*="position:fixed"] {
                position: relative !important;
            }

            /* Ensure images print properly */
            img, svg, picture {
                max-width: 100% !important;
                page-break-inside: avoid;
                opacity: 1 !important;
                visibility: visible !important;
            }

            /* Fix flexbox/grid - keep layout but ensure visibility */
            .flex, [class*="flex"], [class*="grid"] {
                opacity: 1 !important;
                visibility: visible !important;
            }
        }

        /* Print-ready class added by JavaScript before printing */
        body.print-ready * {
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
            animation: none !important;
            transition: none !important;
        }

        /* Dropdown Menu */
        .landing-nav-dropdown {
            position: relative;
        }

        .landing-nav-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 10px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            min-width: 280px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .landing-nav-dropdown:hover .landing-nav-menu,
        .landing-nav-dropdown:focus-within .landing-nav-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .landing-nav-menu-header {
            padding: 15px 20px;
            background: linear-gradient(135deg, #3E95B0, #232323);
            color: white;
        }

        .landing-nav-menu-header h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .landing-nav-menu-header p {
            font-size: 12px;
            opacity: 0.8;
        }

        .landing-nav-menu-items {
            max-height: 300px;
            overflow-y: auto;
        }

        .landing-nav-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #232323;
            text-decoration: none;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .landing-nav-menu-item:last-child {
            border-bottom: none;
        }

        .landing-nav-menu-item:hover {
            background: #f8f9fa;
            padding-left: 25px;
        }

        .landing-nav-menu-item i {
            width: 20px;
            color: #3E95B0;
        }

        .landing-nav-menu-item span {
            font-size: 14px;
        }

        /* Breadcrumb Bar (alternative to dropdown) */
        .landing-breadcrumb {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.98);
            padding: 12px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            font-size: 14px;
        }

        .landing-breadcrumb-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .landing-breadcrumb-logo {
            height: 30px;
        }

        .landing-breadcrumb-separator {
            color: #ccc;
        }

        .landing-breadcrumb a {
            color: #3E95B0;
            text-decoration: none;
            font-weight: 500;
        }

        .landing-breadcrumb a:hover {
            text-decoration: underline;
        }

        .landing-breadcrumb-current {
            color: #666;
        }

        @media (max-width: 768px) {
            .landing-nav {
                top: 10px;
                right: 10px;
            }

            .landing-nav-btn {
                padding: 10px 15px;
                font-size: 13px;
            }

            .landing-nav-btn span {
                display: none;
            }

            .landing-breadcrumb {
                padding: 10px 20px;
                font-size: 13px;
            }

            .landing-breadcrumb-logo {
                height: 24px;
            }
        }
    </style>

    <!-- Landing Page Custom Styles -->
    <?= $styles ?>
</head>
<body>
    <!-- Floating Navigation -->
    <nav class="landing-nav">
        <?php if (!empty($isPrivate)): ?>
            <!-- Private Landing: Only Home, Print and PDF buttons -->
            <button onclick="window.print()" class="landing-nav-btn btn-print" title="Imprimir">
                <i class="fas fa-print"></i>
                <span>Imprimir</span>
            </button>
            <button onclick="saveLandingAsPDF()" class="landing-nav-btn btn-pdf" title="Guardar como PDF">
                <i class="fas fa-file-pdf"></i>
                <span>Guardar PDF</span>
            </button>
            <a href="<?= _url('/') ?>" class="landing-nav-btn" title="<?= __('breadcrumb_home') ?>">
                <i class="fas fa-home"></i>
            </a>
        <?php else: ?>
            <!-- Public Landing: Back, More landings dropdown, Home -->
            <a href="<?= _url('/lp/' . htmlspecialchars($landing['theme_slug'])) ?>" class="landing-nav-btn">
                <i class="fas fa-arrow-left"></i>
                <span><?= __('back') ?></span>
            </a>

            <?php if (!empty($otherLandings)): ?>
                <div class="landing-nav-dropdown">
                    <button class="landing-nav-btn">
                        <i class="fas fa-th-list"></i>
                        <span><?= __('view_more') ?> <?= htmlspecialchars($landing['theme_title']) ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                    </button>
                    <div class="landing-nav-menu">
                        <div class="landing-nav-menu-header">
                            <h4><?= htmlspecialchars($landing['theme_title']) ?></h4>
                            <p><?= __('see_all') ?></p>
                        </div>
                        <div class="landing-nav-menu-items">
                            <?php foreach ($otherLandings as $other): ?>
                                <a href="<?= _url('/lp/' . htmlspecialchars($landing['theme_slug']) . '/' . htmlspecialchars($other['slug'])) ?>" class="landing-nav-menu-item">
                                    <i class="<?= htmlspecialchars($other['icon'] ?? 'fas fa-file-alt') ?>"></i>
                                    <span><?= htmlspecialchars($other['title']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <a href="<?= _url('/') ?>" class="landing-nav-btn" title="<?= __('breadcrumb_home') ?>">
                <i class="fas fa-home"></i>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Landing Content -->
    <?= $_content ?? '' ?>

    <?php if (empty($isPrivate)): ?>
    <!-- Breadcrumb Bar (only for public landings) -->
    <div class="page-breadcrumb-bar">
        <div class="page-breadcrumb-container">
            <nav class="page-breadcrumb-nav">
                <a href="<?= _url('/') ?>"><?= __('breadcrumb_home') ?></a>
                <span>/</span>
                <a href="<?= _url('/lp/' . htmlspecialchars($landing['theme_slug'])) ?>"><?= htmlspecialchars($landing['theme_title']) ?></a>
                <span>/</span>
                <span class="current"><?= htmlspecialchars($landing['title']) ?></span>
            </nav>
        </div>
    </div>
    <?php endif; ?>

    <style>
    .page-breadcrumb-bar {
        padding: 16px 0;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
    .page-breadcrumb-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 40px;
    }
    .page-breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #6c757d;
    }
    .page-breadcrumb-nav a {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.2s;
    }
    .page-breadcrumb-nav a:hover {
        color: #3E95B0;
    }
    .page-breadcrumb-nav .current {
        color: #495057;
        font-weight: 500;
    }
    @media (max-width: 768px) {
        .page-breadcrumb-container {
            padding: 0 20px;
        }
        .page-breadcrumb-nav {
            flex-wrap: wrap;
            font-size: 13px;
        }
    }
    </style>

    <!-- Landing Page Custom Scripts -->
    <?= $scripts ?>

    <?php if (!empty($isPrivate)): ?>
    <!-- PDF/Print functionality for private landings -->
    <script>
        function saveLandingAsPDF() {
            // Show instructions modal
            const modal = document.createElement('div');
            modal.id = 'pdf-instructions-modal';
            modal.innerHTML = `
                <div class="pdf-modal-overlay">
                    <div class="pdf-modal-content">
                        <div class="pdf-modal-header">
                            <i class="fas fa-file-pdf"></i>
                            <h3>Guardar como PDF</h3>
                        </div>
                        <div class="pdf-modal-body">
                            <p>Para guardar esta presentación como PDF:</p>
                            <ol>
                                <li>Se abrirá el diálogo de impresión</li>
                                <li>En <strong>"Destino"</strong> selecciona <strong>"Guardar como PDF"</strong></li>
                                <li>Asegúrate de activar <strong>"Gráficos de fondo"</strong> en las opciones</li>
                                <li>Haz clic en <strong>"Guardar"</strong></li>
                            </ol>
                        </div>
                        <div class="pdf-modal-footer">
                            <button onclick="closePdfModal()" class="pdf-modal-btn pdf-modal-btn-secondary">Cancelar</button>
                            <button onclick="openPrintDialog()" class="pdf-modal-btn pdf-modal-btn-primary">
                                <i class="fas fa-print"></i> Continuar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        function closePdfModal() {
            const modal = document.getElementById('pdf-instructions-modal');
            if (modal) modal.remove();
        }

        function openPrintDialog() {
            closePdfModal();
            prepareForPrint();
            window.print();
        }

        // Prepare page for printing by making all animated/hidden elements visible
        function prepareForPrint() {
            // Add print-ready class to body for CSS cascade
            document.body.classList.add('print-ready');

            // Process ALL elements in the DOM
            document.querySelectorAll('*').forEach(el => {
                // Store original style for potential restoration
                const currentStyle = el.getAttribute('style');
                if (currentStyle && !el.hasAttribute('data-original-style')) {
                    el.setAttribute('data-original-style', currentStyle);
                }

                // Remove problematic inline style properties directly
                if (currentStyle) {
                    // Remove opacity, transform, visibility from inline styles
                    let newStyle = currentStyle
                        .replace(/opacity\s*:\s*[^;]+;?/gi, '')
                        .replace(/transform\s*:\s*[^;]+;?/gi, '')
                        .replace(/visibility\s*:\s*[^;]+;?/gi, '')
                        .replace(/animation[^;]*;?/gi, '')
                        .trim();

                    // Set cleaned style plus forced visibility
                    el.setAttribute('style', newStyle + '; opacity: 1 !important; visibility: visible !important; transform: none !important;');
                } else {
                    // Elements without inline styles - add forced visibility
                    el.style.cssText = 'opacity: 1 !important; visibility: visible !important;';
                }
            });

            // Trigger visibility class on animated elements
            document.querySelectorAll('[class*="animate"], [class*="fade"], [class*="slide"], [data-aos]').forEach(el => {
                el.classList.add('visible', 'animated', 'show', 'aos-animate');
            });

            // Force reflow to apply changes
            document.body.offsetHeight;
        }

        // Restore original styles after printing (optional)
        function restoreAfterPrint() {
            document.body.classList.remove('print-ready');
            document.querySelectorAll('[data-original-style]').forEach(el => {
                el.setAttribute('style', el.getAttribute('data-original-style'));
            });
        }

        // Event listeners for print
        window.addEventListener('beforeprint', prepareForPrint);
        window.addEventListener('afterprint', restoreAfterPrint);
    </script>
    <style>
        .pdf-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100000;
            animation: fadeIn 0.2s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .pdf-modal-content {
            background: white;
            border-radius: 16px;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .pdf-modal-header {
            padding: 24px 24px 16px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .pdf-modal-header i {
            font-size: 48px;
            color: #F9AF00;
            margin-bottom: 12px;
            display: block;
        }
        .pdf-modal-header h3 {
            margin: 0;
            font-size: 20px;
            color: #1A1A1A;
        }
        .pdf-modal-body {
            padding: 20px 24px;
        }
        .pdf-modal-body p {
            margin: 0 0 16px;
            color: #666;
            font-size: 15px;
        }
        .pdf-modal-body ol {
            margin: 0;
            padding-left: 20px;
            color: #333;
        }
        .pdf-modal-body li {
            margin-bottom: 10px;
            font-size: 14px;
            line-height: 1.5;
        }
        .pdf-modal-body strong {
            color: #215A6B;
        }
        .pdf-modal-footer {
            padding: 16px 24px 24px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        .pdf-modal-btn {
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .pdf-modal-btn-secondary {
            background: #f0f0f0;
            color: #666;
        }
        .pdf-modal-btn-secondary:hover {
            background: #e0e0e0;
        }
        .pdf-modal-btn-primary {
            background: #215A6B;
            color: white;
        }
        .pdf-modal-btn-primary:hover {
            background: #12414C;
        }
    </style>
    <?php endif; ?>

    <!-- Admin Edit Button (only for admins) -->
    <?php include __DIR__ . '/../partials/admin-edit-button.php'; ?>
</body>
</html>
