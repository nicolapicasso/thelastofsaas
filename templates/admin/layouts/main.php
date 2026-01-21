<?php
// Load site settings for admin layout
$_settingModel = new \App\Models\Setting();
$_siteName = $_settingModel->get('site_name', 'The Last of SaaS');
$_siteLogo = $_settingModel->get('logo_header', '');

// Get pending tickets count for badge
$_ticketModel = new \App\Models\Ticket();
$_pendingTicketsCount = $_ticketModel->count(['status' => 'pending']);
?>
<!DOCTYPE html>
<html lang="es" data-page-time="<?= time() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <!-- Immediate cache detection - runs before page renders -->
    <script>
    (function(){
        var pageTime = parseInt(document.documentElement.getAttribute('data-page-time')) * 1000;
        var now = Date.now();
        // If page is more than 10 seconds old, it's cached - reload
        if (now - pageTime > 10000) {
            window.location.reload(true);
        }
    })();
    </script>
    <title><?= htmlspecialchars($title ?? 'Admin') ?> - <?= htmlspecialchars($_siteName) ?></title>
    <meta name="csrf-token" content="<?= $_csrf_token ?? '' ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <?php if (isset($extraCss)): ?>
    <?= $extraCss ?>
    <?php endif; ?>
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="/admin" class="sidebar-logo">
                    <?php if (!empty($_siteLogo)): ?>
                        <img src="<?= htmlspecialchars($_siteLogo) ?>" alt="<?= htmlspecialchars($_siteName) ?>" class="logo-img" style="max-height: 32px;">
                    <?php else: ?>
                        <span class="logo-text">Conectoma</span>
                    <?php endif; ?>
                    <span class="logo-badge">CMS</span>
                </a>
                <div class="sidebar-site-name"><?= htmlspecialchars($_siteName) ?></div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="/admin/dashboard" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false || $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-home"></i></span>
                            Dashboard
                        </a>
                    </li>

                    <!-- TLOS Core -->
                    <li class="nav-section-title">TLOS</li>
                    <li>
                        <a href="/admin/events" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/events') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
                            Eventos
                        </a>
                    </li>
                    <li>
                        <a href="/admin/sponsors" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/sponsors') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-handshake"></i></span>
                            Sponsors
                        </a>
                    </li>
                    <li>
                        <a href="/admin/companies" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/companies') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-building"></i></span>
                            Empresas
                        </a>
                    </li>
                    <li>
                        <a href="/admin/tickets" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/tickets') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-ticket-alt"></i></span>
                            Tickets
                            <?php if ($_pendingTicketsCount > 0): ?>
                                <span class="nav-badge"><?= $_pendingTicketsCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/sponsor-invite-codes" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/sponsor-invite-codes') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-gift"></i></span>
                            Codigos Invitacion
                        </a>
                    </li>
                    <li class="has-submenu <?= strpos($_SERVER['REQUEST_URI'], '/admin/meetings') !== false ? 'open' : '' ?>">
                        <a href="#" class="submenu-toggle" onclick="this.parentElement.classList.toggle('open'); return false;">
                            <span class="nav-icon"><i class="fas fa-users"></i></span>
                            Reuniones
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li>
                                <a href="/admin/meetings/blocks" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/meetings/blocks') !== false ? 'active' : '' ?>">
                                    <span class="nav-icon"><i class="fas fa-th-large"></i></span>
                                    Bloques
                                </a>
                            </li>
                            <li>
                                <a href="/admin/meetings/assignments" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/meetings/assignments') !== false ? 'active' : '' ?>">
                                    <span class="nav-icon"><i class="fas fa-calendar-check"></i></span>
                                    Asignaciones
                                </a>
                            </li>
                            <li>
                                <a href="/admin/meetings/matching" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/meetings/matching') !== false ? 'active' : '' ?>">
                                    <span class="nav-icon"><i class="fas fa-random"></i></span>
                                    Matching
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="/admin/votings" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/votings') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-vote-yea"></i></span>
                            Votaciones
                        </a>
                    </li>
                    <li>
                        <a href="/admin/activities" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/activities') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-calendar-day"></i></span>
                            Actividades
                        </a>
                    </li>
                    <li>
                        <a href="/admin/rooms" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/rooms') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-door-open"></i></span>
                            Salas
                        </a>
                    </li>

                    <!-- Content -->
                    <li class="nav-section-title">Contenido</li>
                    <li>
                        <a href="/admin/pages" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/pages') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                            Páginas
                        </a>
                    </li>
                    <li>
                        <a href="/admin/posts" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/posts') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-newspaper"></i></span>
                            Blog
                        </a>
                    </li>
                    <li>
                        <a href="/admin/faqs" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/faqs') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-question-circle"></i></span>
                            FAQs
                        </a>
                    </li>
                    <li>
                        <a href="/admin/team" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/team') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-users"></i></span>
                            Equipo
                        </a>
                    </li>
                    <li>
                        <a href="/admin/media" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/media') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-images"></i></span>
                            Media
                        </a>
                    </li>
                    <li>
                        <a href="/admin/menus" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/menus') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-bars"></i></span>
                            Menús
                        </a>
                    </li>

                    <!-- System -->
                    <li class="nav-section-title">Sistema</li>
                    <li>
                        <a href="/admin/users" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-user-cog"></i></span>
                            Usuarios
                        </a>
                    </li>
                    <li>
                        <a href="/admin/tlos-settings" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/tlos-settings') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-sliders-h"></i></span>
                            Configuración TLOS
                        </a>
                    </li>
                    <li>
                        <a href="/admin/settings" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-cog"></i></span>
                            Configuración General
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-brand">by We're Sinapsis</div>
                <a href="/" target="_blank" class="sidebar-link">Ver sitio</a>
                <a href="/admin/logout" class="sidebar-link logout">Cerrar sesión</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <h1 class="page-title"><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
                </div>
                <div class="header-right">
                    <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                </div>
            </header>

            <div class="admin-content">
                <?php if (isset($flash) && $flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <?= $flash['message'] ?>
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
                <?php endif; ?>

                <?= $_content ?? '' ?>
            </div>
        </main>
    </div>

    <script src="/assets/js/admin.js"></script>
    <script src="/assets/js/image-picker.js"></script>
    <?php if (isset($extraJs)): ?>
    <?= $extraJs ?>
    <?php endif; ?>

    <!-- Prevent bfcache (back-forward cache) from showing stale pages -->
    <script>
    (function() {
        // Generate unique page instance ID
        var pageInstanceId = '<?= bin2hex(random_bytes(8)) ?>';
        var pageLoadTime = Date.now();

        // Store page instance in sessionStorage
        var storageKey = 'admin_page_' + window.location.pathname;
        var storedInstance = sessionStorage.getItem(storageKey);

        // If we have a stored instance that differs, it means this is a cached page
        if (storedInstance && storedInstance !== pageInstanceId) {
            console.log('Cache detected (instance mismatch), forcing reload');
            sessionStorage.setItem(storageKey, pageInstanceId);
            window.location.reload(true);
        } else {
            sessionStorage.setItem(storageKey, pageInstanceId);
        }

        // Force reload when page is restored from bfcache
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                console.log('bfcache detected, forcing reload');
                window.location.reload(true);
            }
        });

        // Handle navigation via history API
        window.addEventListener('popstate', function() {
            window.location.reload(true);
        });

        // Detect stale pages when tab becomes visible again
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // If page has been hidden for more than 30 seconds, reload
                if (Date.now() - pageLoadTime > 30000) {
                    console.log('Stale page detected, forcing reload');
                    window.location.reload(true);
                }
            }
        });

        // Also handle focus event (some browsers don't trigger visibilitychange)
        window.addEventListener('focus', function() {
            if (Date.now() - pageLoadTime > 60000) {
                console.log('Old page detected on focus, forcing reload');
                window.location.reload(true);
            }
        });
    })();
    </script>

    <?php include TEMPLATES_PATH . '/admin/partials/icon-picker.php'; ?>
</body>
</html>
