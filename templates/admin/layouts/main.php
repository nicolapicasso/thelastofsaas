<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($title ?? 'Admin') ?> - We're Sinapsis CMS</title>
    <meta name="csrf-token" content="<?= $_csrf_token ?? '' ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
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
                    <span class="logo-text">Sinapsis</span>
                    <span class="logo-badge">CMS</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="/admin/dashboard" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false || $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-home"></i></span>
                            Dashboard
                        </a>
                    </li>
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
                        <a href="/admin/categories" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-folder"></i></span>
                            Categorías
                        </a>
                    </li>
                    <li class="nav-divider"></li>
                    <li>
                        <a href="/admin/services" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/services') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-cogs"></i></span>
                            Servicios
                        </a>
                    </li>
                    <li>
                        <a href="/admin/tools" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/tools') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-tools"></i></span>
                            Herramientas
                        </a>
                    </li>
                    <li>
                        <a href="/admin/clients" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/clients') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-building"></i></span>
                            Clientes
                        </a>
                    </li>
                    <li>
                        <a href="/admin/cases" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/cases') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-trophy"></i></span>
                            Casos de Éxito
                        </a>
                    </li>
                    <li class="nav-divider"></li>
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
                    <li class="has-submenu <?= strpos($_SERVER['REQUEST_URI'], '/admin/landing') !== false ? 'open' : '' ?>">
                        <a href="#" class="submenu-toggle" onclick="this.parentElement.classList.toggle('open'); return false;">
                            <span class="nav-icon"><i class="fas fa-rocket"></i></span>
                            Landing Pages
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="nav-submenu">
                            <li>
                                <a href="/admin/landing-themes" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/landing-themes') !== false ? 'active' : '' ?>">
                                    <span class="nav-icon"><i class="fas fa-layer-group"></i></span>
                                    Temáticas
                                </a>
                            </li>
                            <li>
                                <a href="/admin/landings" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/landings') !== false ? 'active' : '' ?>">
                                    <span class="nav-icon"><i class="fas fa-file-code"></i></span>
                                    Landings
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-divider"></li>
                    <li>
                        <a href="/admin/translations" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/translations') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-globe"></i></span>
                            Traducciones
                        </a>
                    </li>
                    <li>
                        <a href="/admin/seo" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/seo') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-search"></i></span>
                            SEO
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
                    <li>
                        <a href="/admin/users" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-user-cog"></i></span>
                            Usuarios
                        </a>
                    </li>
                    <li>
                        <a href="/admin/settings" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-cog"></i></span>
                            Configuración
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
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

    <?php include TEMPLATES_PATH . '/admin/partials/icon-picker.php'; ?>
</body>
</html>
