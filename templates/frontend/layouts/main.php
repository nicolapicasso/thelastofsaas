<?php
// Get font settings with defaults
$fontPrimary = $fontPrimary ?? 'Inter';
$fontSecondary = $fontSecondary ?? 'Inter';
$logoHeader = $logoHeader ?? '/assets/images/logo.svg';
$logoFooter = $logoFooter ?? $logoHeader;
$faviconUrl = $favicon ?? '/favicon.ico';

// Tracking settings
$gtmId = $gtmId ?? '';
$gaId = $gaId ?? '';

// Build Google Fonts URL
$fonts = array_unique([$fontPrimary, $fontSecondary]);
$fontFamilies = array_map(fn($f) => str_replace(' ', '+', $f) . ':wght@400;500;600;700', $fonts);
$googleFontsUrl = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $fontFamilies) . '&display=swap';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?? 'es' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (!empty($gtmId)): ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?= htmlspecialchars($gtmId) ?>');</script>
    <!-- End Google Tag Manager -->
    <?php elseif (!empty($gaId)): ?>
    <!-- Google Analytics (direct) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaId) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= htmlspecialchars($gaId) ?>');
    </script>
    <?php else: ?>
    <!-- Initialize dataLayer for future use -->
    <script>window.dataLayer = window.dataLayer || [];</script>
    <?php endif; ?>

    <?= $seo->renderMetaTags() ?>

    <!-- Favicon -->
    <link rel="icon" href="<?= htmlspecialchars($faviconUrl) ?>">
    <link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?= htmlspecialchars($googleFontsUrl) ?>" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/frontend.css?v=<?= filemtime(PUBLIC_PATH . '/assets/css/frontend.css') ?>">
    <link rel="stylesheet" href="/assets/css/animations.css">
    <link rel="stylesheet" href="/assets/css/cookies.css">

    <!-- Dynamic Font Variables (must come after frontend.css to override defaults) -->
    <style>
        :root {
            --font-primary: '<?= htmlspecialchars($fontPrimary) ?>', sans-serif;
            --font-secondary: '<?= htmlspecialchars($fontSecondary) ?>', sans-serif;
        }
    </style>
    <?php if (isset($extraCss)): ?>
    <?= $extraCss ?>
    <?php endif; ?>

    <!-- Schema.org -->
    <?= $seo->renderSchemas() ?>
</head>
<body>
    <?php if (!empty($gtmId)): ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= htmlspecialchars($gtmId) ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php endif; ?>

    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <nav class="main-nav">
                <?php
                // Check if logo is a GIF (for hover animation)
                $isAnimatedLogo = preg_match('/\.gif$/i', $logoHeader);
                $logoClass = $isAnimatedLogo ? 'logo has-animated-logo' : 'logo';
                ?>
                <a href="<?= _url('/') ?>" class="<?= $logoClass ?>" <?= $isAnimatedLogo ? 'data-animated-src="' . htmlspecialchars($logoHeader) . '"' : '' ?>>
                    <?php if ($isAnimatedLogo): ?>
                        <canvas class="logo-static" height="40"></canvas>
                        <img src="<?= htmlspecialchars($logoHeader) ?>" alt="We're Sinapsis" height="40" class="logo-animated">
                    <?php else: ?>
                        <img src="<?= htmlspecialchars($logoHeader) ?>" alt="We're Sinapsis" height="40">
                    <?php endif; ?>
                </a>

                <ul class="nav-menu" id="navMenu">
                    <?php foreach ($mainNav as $item): ?>
                        <?php $hasChildren = !empty($item['children']); ?>
                        <li class="<?= $hasChildren ? 'has-submenu' : '' ?>">
                            <a href="<?= $item['url'] ?>" class="<?= strpos($_SERVER['REQUEST_URI'], $item['url']) === 0 && $item['url'] !== '/' ? 'active' : '' ?>">
                                <?= htmlspecialchars($item['label']) ?>
                                <?php if ($hasChildren): ?>
                                    <i class="fas fa-chevron-down submenu-arrow"></i>
                                <?php endif; ?>
                            </a>
                            <?php if ($hasChildren): ?>
                                <ul class="submenu">
                                    <?php foreach ($item['children'] as $child): ?>
                                        <?php $hasGrandchildren = !empty($child['children']); ?>
                                        <li class="<?= $hasGrandchildren ? 'has-submenu' : '' ?>">
                                            <a href="<?= $child['url'] ?>">
                                                <?php if (!empty($child['icon'])): ?>
                                                    <i class="<?= htmlspecialchars($child['icon']) ?>"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($child['label']) ?>
                                                <?php if ($hasGrandchildren): ?>
                                                    <i class="fas fa-chevron-right submenu-arrow-right"></i>
                                                <?php endif; ?>
                                            </a>
                                            <?php if ($hasGrandchildren): ?>
                                                <ul class="submenu submenu-level-3">
                                                    <?php foreach ($child['children'] as $grandchild): ?>
                                                        <li>
                                                            <a href="<?= $grandchild['url'] ?>">
                                                                <?php if (!empty($grandchild['icon'])): ?>
                                                                    <i class="<?= htmlspecialchars($grandchild['icon']) ?>"></i>
                                                                <?php endif; ?>
                                                                <?= htmlspecialchars($grandchild['label']) ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="nav-actions">
                    <?php if (!empty($headerButtons)): ?>
                        <?php foreach ($headerButtons as $btn): ?>
                            <a href="<?= $btn['url'] ?>" class="btn btn-<?= $btn['button_style'] ?? 'primary' ?>"<?= $btn['target'] === '_blank' ? ' target="_blank" rel="noopener"' : '' ?>>
                                <?= htmlspecialchars($btn['title']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback buttons -->
                        <a href="<?= _url('/login') ?>" class="btn btn-outline">Acceder</a>
                        <a href="<?= _url('/registro') ?>" class="btn btn-primary">Empezar Gratis</a>
                    <?php endif; ?>

                    <?php if (!empty($sidebarMenu)): ?>
                        <button class="sidebar-toggle" id="sidebarToggle" aria-label="<?= htmlspecialchars($sidebarMenu['name'] ?? 'Menu') ?>">
                            <span><?= htmlspecialchars($sidebarMenu['name'] ?? 'Services') ?></span>
                            <span class="sidebar-toggle-icon">
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    <?php endif; ?>
                </div>

                <button class="mobile-toggle" id="mobileToggle" aria-label="Menú">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>

    <?php if (!empty($sidebarMenu)): ?>
    <!-- Sidebar Menu -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside class="sidebar-menu" id="sidebarMenu">
        <nav class="sidebar-nav">
            <?php foreach ($sidebarMenu['items'] as $item): ?>
                <?php if (!empty($item['children'])): ?>
                    <!-- Parent item with children - collapsible section -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-header">
                            <a href="<?= htmlspecialchars($item['url']) ?>" class="sidebar-section-link"<?= ($item['target'] ?? '_self') === '_blank' ? ' target="_blank" rel="noopener"' : '' ?>>
                                <?php if (!empty($item['icon'])): ?>
                                    <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                                <?php endif; ?>
                                <span class="sidebar-section-title"><?= htmlspecialchars($item['title']) ?></span>
                            </a>
                            <button class="sidebar-section-toggle" aria-expanded="false" aria-label="Expandir sección">
                                <i class="fas fa-chevron-down sidebar-section-arrow"></i>
                            </button>
                        </div>
                        <ul class="sidebar-section-items">
                            <?php foreach ($item['children'] as $child): ?>
                                <?php if (!empty($child['children'])): ?>
                                    <!-- Level 2 item with children (level 3) -->
                                    <li class="sidebar-subsection">
                                        <div class="sidebar-subsection-header">
                                            <a href="<?= htmlspecialchars($child['url']) ?>" class="sidebar-subsection-link"<?= ($child['target'] ?? '_self') === '_blank' ? ' target="_blank" rel="noopener"' : '' ?>>
                                                <?php if (!empty($child['icon'])): ?>
                                                    <i class="<?= htmlspecialchars($child['icon']) ?>"></i>
                                                <?php endif; ?>
                                                <span><?= htmlspecialchars($child['title']) ?></span>
                                            </a>
                                            <button class="sidebar-subsection-toggle" aria-expanded="false" aria-label="Expandir subsección">
                                                <i class="fas fa-chevron-down sidebar-subsection-arrow"></i>
                                            </button>
                                        </div>
                                        <ul class="sidebar-subsection-items">
                                            <?php foreach ($child['children'] as $grandchild): ?>
                                                <li>
                                                    <a href="<?= htmlspecialchars($grandchild['url']) ?>"<?= ($grandchild['target'] ?? '_self') === '_blank' ? ' target="_blank" rel="noopener"' : '' ?>>
                                                        <?php if (!empty($grandchild['icon'])): ?>
                                                            <i class="<?= htmlspecialchars($grandchild['icon']) ?>"></i>
                                                        <?php endif; ?>
                                                        <?= htmlspecialchars($grandchild['title']) ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php else: ?>
                                    <!-- Simple level 2 item -->
                                    <li>
                                        <a href="<?= htmlspecialchars($child['url']) ?>"<?= ($child['target'] ?? '_self') === '_blank' ? ' target="_blank" rel="noopener"' : '' ?>>
                                            <?php if (!empty($child['icon'])): ?>
                                                <i class="<?= htmlspecialchars($child['icon']) ?>"></i>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($child['title']) ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Single item without children -->
                    <div class="sidebar-item">
                        <a href="<?= htmlspecialchars($item['url']) ?>"<?= ($item['target'] ?? '_self') === '_blank' ? ' target="_blank" rel="noopener"' : '' ?>>
                            <?php if (!empty($item['icon'])): ?>
                                <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                            <?php endif; ?>
                            <span class="sidebar-item-title"><?= htmlspecialchars($item['title']) ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="site-main">
        <?= $_content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand -->
                <div class="footer-brand">
                    <a href="<?= _url('/') ?>" class="footer-logo">
                        <img src="<?= htmlspecialchars($logoFooter) ?>" alt="We're Sinapsis" height="32">
                    </a>
                    <p><?= htmlspecialchars($footerTagline ?? 'Agencia de marketing digital que impulsa tu negocio.') ?></p>
                    <div class="social-links">
                        <?php if (!empty($socialLinks)): ?>
                            <?php foreach ($socialLinks as $social): ?>
                                <a href="<?= $social['url'] ?>" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($social['title']) ?>">
                                    <i class="<?= htmlspecialchars($social['icon'] ?? 'fas fa-link') ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback social links -->
                            <a href="https://linkedin.com/company/sinapsis" target="_blank" rel="noopener" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="https://instagram.com/weresinapsis" target="_blank" rel="noopener" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Navigation -->
                <?php foreach ($footerNav as $section): ?>
                    <div class="footer-nav">
                        <h4><?= htmlspecialchars($section['title']) ?></h4>
                        <ul>
                            <?php foreach ($section['items'] as $item): ?>
                                <li><a href="<?= $item['url'] ?>"><?= htmlspecialchars($item['label']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($partnerBadges)): ?>
            <!-- Partner Certification Badges -->
            <div class="footer-partners">
                <div class="partner-badges">
                    <?php foreach ($partnerBadges as $badge): ?>
                        <?php if (!empty($badge['url'])): ?>
                            <a href="<?= htmlspecialchars($badge['url']) ?>" target="_blank" rel="noopener" class="partner-badge" title="<?= htmlspecialchars($badge['name'] ?? '') ?>">
                                <img src="<?= htmlspecialchars($badge['image']) ?>" alt="<?= htmlspecialchars($badge['name'] ?? 'Partner Badge') ?>">
                            </a>
                        <?php else: ?>
                            <span class="partner-badge" title="<?= htmlspecialchars($badge['name'] ?? '') ?>">
                                <img src="<?= htmlspecialchars($badge['image']) ?>" alt="<?= htmlspecialchars($badge['name'] ?? 'Partner Badge') ?>">
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="footer-bottom">
                <div class="footer-lang">
                    <?php
                    // Generate language URLs with prefixes
                    $pathOnly = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
                    $langCodes = array_keys($availableLangs);
                    $segments = explode('/', trim($pathOnly, '/'));
                    if (!empty($segments[0]) && in_array($segments[0], $langCodes)) {
                        array_shift($segments);
                    }
                    $cleanPath = '/' . implode('/', $segments);
                    if ($cleanPath === '/') $cleanPath = '/';
                    ?>
                    <?php foreach ($availableLangs as $code => $lang): ?>
                        <?php $langUrl = ($code === 'es') ? $cleanPath : '/' . $code . $cleanPath; ?>
                        <a href="<?= $langUrl ?>"
                           class="<?= $currentLang === $code ? 'active' : '' ?>"
                           title="<?= $lang['native'] ?>">
                            <?= strtoupper($code) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p><?= str_replace('{year}', date('Y'), htmlspecialchars($footerCopyright ?? "© {year} We're Sinapsis. Todos los derechos reservados.")) ?></p>
            </div>
        </div>
    </footer>

    <!-- Cookie Consent -->
    <?php include __DIR__ . '/../partials/cookies.php'; ?>

    <!-- Floating Contact Form -->
    <?php include __DIR__ . '/../partials/floating-form.php'; ?>

    <!-- Admin Edit Button (only for admins) -->
    <?php include __DIR__ . '/../partials/admin-edit-button.php'; ?>

    <!-- Language Selection Prompt (only for first-time visitors) -->
    <?php include __DIR__ . '/../partials/language-prompt.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/frontend.js"></script>
    <script src="/assets/js/animations.js"></script>
    <script src="/assets/js/tracking.js"></script>
    <script src="/assets/js/cookies.js"></script>
    <?php if (isset($extraJs)): ?>
    <?= $extraJs ?>
    <?php endif; ?>

    <?php if (!empty($partnerScripts)): ?>
    <!-- Partner Verification Scripts -->
    <?= $partnerScripts ?>
    <?php endif; ?>

    <!-- Page tracking data -->
    <script>
    (function() {
        // Push page view event with additional data
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event': 'page_view',
            'page_type': '<?= htmlspecialchars($pageType ?? 'page') ?>',
            'page_title': '<?= htmlspecialchars($seo->getTitle() ?? '') ?>',
            'page_path': '<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/') ?>',
            'language': '<?= htmlspecialchars($currentLang ?? 'es') ?>'
        });
    })();
    </script>
</body>
</html>
