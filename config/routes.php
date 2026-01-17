<?php

declare(strict_types=1);

/**
 * Routes Configuration
 * We're Sinapsis CMS
 */

use App\Core\Router;

/** @var Router $router */

// ============================================
// Frontend Routes
// ============================================

// Home
$router->get('/', 'Frontend\\PageController', 'home');

// Blog
$router->get('/blog', 'Frontend\\BlogController', 'index');
$router->get('/blog/{slug}', 'Frontend\\BlogController', 'show');

// Success Cases
$router->get('/casos-de-exito', 'Frontend\\CasesController', 'index');
$router->get('/casos-de-exito/{slug}', 'Frontend\\CasesController', 'show');

// Services (formerly Features)
$router->get('/servicios', 'Frontend\\ServicesController', 'index');
$router->get('/servicios/{slug}', 'Frontend\\ServicesController', 'show');

// Tools (formerly Integrations)
$router->get('/herramientas', 'Frontend\\ToolsController', 'index');
$router->get('/herramientas/{slug}', 'Frontend\\ToolsController', 'show');

// Clients Directory
$router->get('/clientes', 'Frontend\\ClientsController', 'index');
$router->get('/clientes/{slug}', 'Frontend\\ClientsController', 'show');

// Categories
$router->get('/categorias', 'Frontend\\CategoriesController', 'index');
$router->get('/categorias/{slug}', 'Frontend\\CategoriesController', 'show');

// Knowledge Base (disabled - table doesn't exist in current schema)
// $router->get('/ayuda', 'Frontend\\KnowledgeController', 'index');
// $router->get('/ayuda/buscar', 'Frontend\\KnowledgeController', 'search');
// $router->get('/ayuda/categoria/{slug}', 'Frontend\\KnowledgeController', 'category');
// $router->get('/ayuda/{slug}', 'Frontend\\KnowledgeController', 'article');

// Team
$router->get('/equipo', 'Frontend\\TeamController', 'index');
$router->get('/equipo/{slug}', 'Frontend\\TeamController', 'show');

// Partners Directory (disabled - use Clients instead)
// $router->get('/partners', 'Frontend\\PartnersController', 'index');
// $router->get('/partners/cities', 'Frontend\\PartnersController', 'getCities');
// $router->get('/partners/{slug}', 'Frontend\\PartnersController', 'show');

// Landing Pages
$router->get('/lp', 'Frontend\\LandingsController', 'index');
$router->get('/lp/{themeSlug}', 'Frontend\\LandingsController', 'themeIndex');
$router->get('/lp/{themeSlug}/{landingSlug}', 'Frontend\\LandingsController', 'show');
$router->post('/lp/{themeSlug}/{landingSlug}/access', 'Frontend\\LandingsController', 'verifyPassword');

// Search
$router->get('/search', 'Frontend\\SearchController', 'index');
$router->post('/search', 'Frontend\\SearchController', 'results');

// ============================================
// Admin Routes
// ============================================

// Auth
$router->get('/admin/login', 'Admin\\AuthController', 'loginForm');
$router->post('/admin/login', 'Admin\\AuthController', 'login');
$router->get('/admin/logout', 'Admin\\AuthController', 'logout');

// Dashboard
$router->get('/admin', 'Admin\\DashboardController', 'index');
$router->get('/admin/dashboard', 'Admin\\DashboardController', 'index');

// Categories
$router->get('/admin/categories', 'Admin\\CategoriesController', 'index');
$router->get('/admin/categories/create', 'Admin\\CategoriesController', 'create');
$router->post('/admin/categories', 'Admin\\CategoriesController', 'store');
$router->post('/admin/categories/reorder', 'Admin\\CategoriesController', 'reorder');
$router->get('/admin/categories/{id}/edit', 'Admin\\CategoriesController', 'edit');
$router->post('/admin/categories/{id}', 'Admin\\CategoriesController', 'update');
$router->post('/admin/categories/{id}/delete', 'Admin\\CategoriesController', 'destroy');
$router->post('/admin/categories/{id}/generate-qa', 'Admin\\CategoriesController', 'generateQA');

// Pages
$router->get('/admin/pages', 'Admin\\PagesController', 'index');
$router->get('/admin/pages/create', 'Admin\\PagesController', 'create');
$router->post('/admin/pages', 'Admin\\PagesController', 'store');
$router->get('/admin/pages/{id}/edit', 'Admin\\PagesController', 'edit');
$router->post('/admin/pages/{id}', 'Admin\\PagesController', 'update');
$router->post('/admin/pages/{id}/delete', 'Admin\\PagesController', 'destroy');
$router->post('/admin/pages/{id}/generate-qa', 'Admin\\PagesController', 'generateQA');

// Blocks (AJAX)
$router->get('/admin/blocks/form', 'Admin\\BlocksController', 'form');
$router->post('/admin/blocks/reorder', 'Admin\\BlocksController', 'reorder'); // Must be before {id} routes
$router->get('/admin/pages/{id}/blocks', 'Admin\\BlocksController', 'index');
$router->post('/admin/pages/{id}/blocks', 'Admin\\BlocksController', 'store');
$router->get('/admin/blocks/{id}', 'Admin\\BlocksController', 'get');
$router->post('/admin/blocks/{id}', 'Admin\\BlocksController', 'update');
$router->post('/admin/blocks/{id}/delete', 'Admin\\BlocksController', 'destroy');
$router->post('/admin/blocks/{id}/clone', 'Admin\\BlocksController', 'clone');

// Posts
$router->get('/admin/posts', 'Admin\\PostsController', 'index');
$router->get('/admin/posts/create', 'Admin\\PostsController', 'create');
$router->post('/admin/posts', 'Admin\\PostsController', 'store');
$router->get('/admin/posts/{id}/edit', 'Admin\\PostsController', 'edit');
$router->post('/admin/posts/{id}', 'Admin\\PostsController', 'update');
$router->post('/admin/posts/{id}/delete', 'Admin\\PostsController', 'destroy');
$router->post('/admin/posts/{id}/generate-qa', 'Admin\\PostsController', 'generateQA');

// Services (formerly Features)
$router->get('/admin/services', 'Admin\\ServicesController', 'index');
$router->get('/admin/services/create', 'Admin\\ServicesController', 'create');
$router->post('/admin/services', 'Admin\\ServicesController', 'store');
$router->post('/admin/services/reorder', 'Admin\\ServicesController', 'reorder');
$router->get('/admin/services/{id}/edit', 'Admin\\ServicesController', 'edit');
$router->post('/admin/services/{id}', 'Admin\\ServicesController', 'update');
$router->post('/admin/services/{id}/delete', 'Admin\\ServicesController', 'delete');
$router->post('/admin/services/{id}/generate-qa', 'Admin\\ServicesController', 'generateQA');

// Service Blocks (AJAX)
$router->get('/admin/service-blocks/form', 'Admin\\ServiceBlocksController', 'form');
$router->post('/admin/service-blocks/reorder', 'Admin\\ServiceBlocksController', 'reorder');
$router->get('/admin/services/{id}/blocks', 'Admin\\ServiceBlocksController', 'index');
$router->post('/admin/services/{id}/blocks', 'Admin\\ServiceBlocksController', 'store');
$router->get('/admin/service-blocks/{id}', 'Admin\\ServiceBlocksController', 'get');
$router->post('/admin/service-blocks/{id}', 'Admin\\ServiceBlocksController', 'update');
$router->post('/admin/service-blocks/{id}/delete', 'Admin\\ServiceBlocksController', 'destroy');
$router->post('/admin/service-blocks/{id}/clone', 'Admin\\ServiceBlocksController', 'clone');

// Tools (formerly Integrations)
$router->get('/admin/tools', 'Admin\\ToolsController', 'index');
$router->get('/admin/tools/create', 'Admin\\ToolsController', 'create');
$router->post('/admin/tools', 'Admin\\ToolsController', 'store');
$router->post('/admin/tools/reorder', 'Admin\\ToolsController', 'reorder');
$router->get('/admin/tools/{id}/edit', 'Admin\\ToolsController', 'edit');
$router->post('/admin/tools/{id}', 'Admin\\ToolsController', 'update');
$router->post('/admin/tools/{id}/delete', 'Admin\\ToolsController', 'delete');
$router->post('/admin/tools/{id}/generate-qa', 'Admin\\ToolsController', 'generateQA');

// Clients
$router->get('/admin/clients', 'Admin\\ClientsController', 'index');
$router->get('/admin/clients/create', 'Admin\\ClientsController', 'create');
$router->post('/admin/clients', 'Admin\\ClientsController', 'store');
$router->get('/admin/clients/{id}/edit', 'Admin\\ClientsController', 'edit');
$router->post('/admin/clients/{id}', 'Admin\\ClientsController', 'update');
$router->post('/admin/clients/{id}/delete', 'Admin\\ClientsController', 'delete');
$router->post('/admin/clients/{id}/generate-qa', 'Admin\\ClientsController', 'generateQA');

// FAQs
$router->get('/admin/faqs', 'Admin\\FAQController', 'index');
$router->get('/admin/faqs/create', 'Admin\\FAQController', 'create');
$router->post('/admin/faqs', 'Admin\\FAQController', 'store');
$router->get('/admin/faqs/{id}/edit', 'Admin\\FAQController', 'edit');
$router->post('/admin/faqs/{id}', 'Admin\\FAQController', 'update');
$router->post('/admin/faqs/{id}/delete', 'Admin\\FAQController', 'destroy');

// Success Cases
$router->get('/admin/cases', 'Admin\\SuccessCasesController', 'index');
$router->get('/admin/cases/create', 'Admin\\SuccessCasesController', 'create');
$router->post('/admin/cases', 'Admin\\SuccessCasesController', 'store');
$router->post('/admin/cases/reorder', 'Admin\\SuccessCasesController', 'reorder');
$router->get('/admin/cases/{id}/edit', 'Admin\\SuccessCasesController', 'edit');
$router->post('/admin/cases/{id}/update', 'Admin\\SuccessCasesController', 'update');
$router->post('/admin/cases/{id}/delete', 'Admin\\SuccessCasesController', 'delete');
$router->post('/admin/cases/{id}/generate-qa', 'Admin\\SuccessCasesController', 'generateQA');

// Knowledge Base (disabled - table doesn't exist in current schema)
// $router->get('/admin/knowledge', 'Admin\\KnowledgeController', 'index');
// $router->get('/admin/knowledge/create', 'Admin\\KnowledgeController', 'create');
// $router->post('/admin/knowledge', 'Admin\\KnowledgeController', 'store');
// $router->get('/admin/knowledge/{id}/edit', 'Admin\\KnowledgeController', 'edit');
// $router->post('/admin/knowledge/{id}/update', 'Admin\\KnowledgeController', 'update');
// $router->post('/admin/knowledge/{id}/delete', 'Admin\\KnowledgeController', 'delete');
// $router->post('/admin/knowledge/{id}/generate-qa', 'Admin\\KnowledgeController', 'generateQA');

// Team
$router->get('/admin/team', 'Admin\\TeamController', 'index');
$router->get('/admin/team/create', 'Admin\\TeamController', 'create');
$router->post('/admin/team', 'Admin\\TeamController', 'store');
$router->get('/admin/team/{id}/edit', 'Admin\\TeamController', 'edit');
$router->post('/admin/team/{id}', 'Admin\\TeamController', 'update');
$router->post('/admin/team/{id}/delete', 'Admin\\TeamController', 'destroy');

// Landing Themes
$router->get('/admin/landing-themes', 'Admin\\LandingThemesController', 'index');
$router->get('/admin/landing-themes/create', 'Admin\\LandingThemesController', 'create');
$router->post('/admin/landing-themes', 'Admin\\LandingThemesController', 'store');
$router->get('/admin/landing-themes/{id}/edit', 'Admin\\LandingThemesController', 'edit');
$router->post('/admin/landing-themes/{id}/update', 'Admin\\LandingThemesController', 'update');
$router->post('/admin/landing-themes/{id}/delete', 'Admin\\LandingThemesController', 'destroy');

// Landings
$router->get('/admin/landings', 'Admin\\LandingsController', 'index');
$router->get('/admin/landings/create', 'Admin\\LandingsController', 'create');
$router->post('/admin/landings', 'Admin\\LandingsController', 'store');
$router->get('/admin/landings/{id}/edit', 'Admin\\LandingsController', 'edit');
$router->get('/admin/landings/{id}/preview', 'Admin\\LandingsController', 'preview');
$router->post('/admin/landings/{id}/update', 'Admin\\LandingsController', 'update');
$router->post('/admin/landings/{id}/delete', 'Admin\\LandingsController', 'destroy');
$router->post('/admin/landings/{id}/translate-html', 'Admin\\LandingsController', 'translateHtml');

// Partners (disabled - use Clients instead)
// $router->get('/admin/partners', 'Admin\\PartnersController', 'index');
// $router->get('/admin/partners/create', 'Admin\\PartnersController', 'create');
// $router->post('/admin/partners', 'Admin\\PartnersController', 'store');
// $router->get('/admin/partners/{id}/edit', 'Admin\\PartnersController', 'edit');
// $router->post('/admin/partners/{id}/update', 'Admin\\PartnersController', 'update');
// $router->post('/admin/partners/{id}/delete', 'Admin\\PartnersController', 'destroy');

// Translations
$router->get('/admin/translations', 'Admin\\TranslationsController', 'index');
$router->get('/admin/translations/batch-info', 'Admin\\TranslationsController', 'getBatchInfo');
$router->post('/admin/translations/process-batch', 'Admin\\TranslationsController', 'processBatch');
$router->get('/admin/translations/error-log', 'Admin\\TranslationsController', 'errorLog');
$router->post('/admin/translations/clear-log', 'Admin\\TranslationsController', 'clearLog');
$router->get('/admin/translations/{id}/edit', 'Admin\\TranslationsController', 'edit');
$router->post('/admin/translations/{id}/update', 'Admin\\TranslationsController', 'update');
$router->post('/admin/translations/{id}/approve', 'Admin\\TranslationsController', 'approve');
$router->post('/admin/translations/{id}/delete', 'Admin\\TranslationsController', 'delete');
$router->post('/admin/translations/batch', 'Admin\\TranslationsController', 'batchTranslate');
$router->post('/admin/translations/bulk-approve', 'Admin\\TranslationsController', 'bulkApprove');
$router->post('/admin/translations/translate-entity', 'Admin\\TranslationsController', 'translateEntity');
$router->post('/admin/translations/create', 'Admin\\TranslationsController', 'create');

// SEO Management
$router->get('/admin/seo', 'Admin\\SEOController', 'index');
$router->get('/admin/seo/mass-generate', 'Admin\\SEOController', 'massGenerate');
$router->post('/admin/seo/generate-batch', 'Admin\\SEOController', 'generateBatch');
$router->post('/admin/seo/generate-single', 'Admin\\SEOController', 'generateSingle');
$router->get('/admin/seo/edit', 'Admin\\SEOController', 'edit');
$router->post('/admin/seo/save', 'Admin\\SEOController', 'save');
$router->get('/admin/seo/sitemap', 'Admin\\SEOController', 'sitemap');
$router->post('/admin/seo/generate-sitemap', 'Admin\\SEOController', 'generateSitemap');
$router->get('/admin/seo/robots', 'Admin\\SEOController', 'robots');
$router->post('/admin/seo/save-robots', 'Admin\\SEOController', 'saveRobots');
$router->get('/admin/seo/settings', 'Admin\\SEOController', 'settings');
$router->post('/admin/seo/save-settings', 'Admin\\SEOController', 'saveSettings');
$router->get('/admin/seo/audit-log', 'Admin\\SEOController', 'auditLog');
$router->get('/admin/seo/preview', 'Admin\\SEOController', 'preview');

// Media
$router->get('/admin/media', 'Admin\\MediaController', 'index');
$router->post('/admin/media/upload', 'Admin\\MediaController', 'upload');
$router->get('/admin/media/{id}/info', 'Admin\\MediaController', 'info');
$router->post('/admin/media/{id}/update', 'Admin\\MediaController', 'update');
$router->post('/admin/media/{id}/delete', 'Admin\\MediaController', 'delete');
$router->get('/admin/media/picker', 'Admin\\MediaController', 'picker');
$router->get('/admin/media/browse', 'Admin\\MediaController', 'browse');

// Menus
$router->get('/admin/menus', 'Admin\\MenusController', 'index');
$router->get('/admin/menus/create', 'Admin\\MenusController', 'create');
$router->post('/admin/menus', 'Admin\\MenusController', 'store');
$router->get('/admin/menus/{id}/edit', 'Admin\\MenusController', 'edit');
$router->post('/admin/menus/{id}/update', 'Admin\\MenusController', 'update');
$router->post('/admin/menus/{id}/delete', 'Admin\\MenusController', 'delete');
// Menu Items (AJAX)
$router->get('/admin/menus/{id}/items', 'Admin\\MenusController', 'getItems');
$router->post('/admin/menus/{id}/items', 'Admin\\MenusController', 'addItem');
$router->post('/admin/menus/{id}/items/reorder', 'Admin\\MenusController', 'reorderItems'); // Must be before {itemId} routes
$router->post('/admin/menus/{id}/items/{itemId}', 'Admin\\MenusController', 'updateItem');
$router->post('/admin/menus/{id}/items/{itemId}/delete', 'Admin\\MenusController', 'deleteItem');

// Settings
$router->get('/admin/settings', 'Admin\\SettingsController', 'index');
$router->post('/admin/settings', 'Admin\\SettingsController', 'update');

// Users
$router->get('/admin/users', 'Admin\\UsersController', 'index');
$router->get('/admin/users/create', 'Admin\\UsersController', 'create');
$router->post('/admin/users', 'Admin\\UsersController', 'store');
$router->get('/admin/users/{id}/edit', 'Admin\\UsersController', 'edit');
$router->post('/admin/users/{id}', 'Admin\\UsersController', 'update');
$router->post('/admin/users/{id}/delete', 'Admin\\UsersController', 'destroy');

// Profile
$router->get('/admin/profile', 'Admin\\ProfileController', 'edit');
$router->post('/admin/profile', 'Admin\\ProfileController', 'update');
$router->post('/admin/profile/password', 'Admin\\ProfileController', 'updatePassword');

// ============================================
// API Routes (for AJAX)
// ============================================

$router->post('/api/translate', 'Api\\TranslationController', 'translate');
$router->get('/api/search', 'Api\\SearchController', 'search');
$router->post('/api/contact/submit', 'Api\\ContactController', 'submit');

// ============================================
// Catch-all Route (MUST be last!)
// ============================================

// Pages (catch-all for dynamic pages by slug)
$router->get('/{slug}', 'Frontend\\PageController', 'show');
