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
$router->post('/auth', 'Admin\\AuthController', 'login');
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
// TLOS Admin Routes
// ============================================

// Events
$router->get('/admin/events', 'Admin\\EventsController', 'index');
$router->get('/admin/events/create', 'Admin\\EventsController', 'create');
$router->post('/admin/events', 'Admin\\EventsController', 'store');
$router->get('/admin/events/{id}/edit', 'Admin\\EventsController', 'edit');
$router->post('/admin/events/{id}', 'Admin\\EventsController', 'update');
$router->post('/admin/events/{id}/delete', 'Admin\\EventsController', 'destroy');
$router->get('/admin/events/{id}/stats', 'Admin\\EventsController', 'stats');
$router->post('/admin/events/{id}/sponsors', 'Admin\\EventsController', 'addSponsor');
$router->post('/admin/events/{id}/sponsors/{sponsorId}/delete', 'Admin\\EventsController', 'removeSponsor');
$router->post('/admin/events/{id}/sponsors/{sponsorId}/level', 'Admin\\EventsController', 'updateSponsorLevel');
$router->post('/admin/events/{id}/companies', 'Admin\\EventsController', 'addCompany');
$router->post('/admin/events/{id}/companies/{companyId}/delete', 'Admin\\EventsController', 'removeCompany');
$router->post('/admin/events/{id}/features', 'Admin\\EventsController', 'addFeature');

// Sponsors
$router->get('/admin/sponsors', 'Admin\\SponsorsController', 'index');
$router->get('/admin/sponsors/create', 'Admin\\SponsorsController', 'create');
$router->post('/admin/sponsors', 'Admin\\SponsorsController', 'store');
$router->get('/admin/sponsors/import', 'Admin\\SponsorsController', 'import');
$router->post('/admin/sponsors/import', 'Admin\\SponsorsController', 'import');
$router->get('/admin/sponsors/export', 'Admin\\SponsorsController', 'export');
$router->get('/admin/sponsors/{id}/edit', 'Admin\\SponsorsController', 'edit');
$router->post('/admin/sponsors/{id}', 'Admin\\SponsorsController', 'update');
$router->post('/admin/sponsors/{id}/delete', 'Admin\\SponsorsController', 'destroy');
$router->post('/admin/sponsors/{id}/regenerate-code', 'Admin\\SponsorsController', 'regenerateCode');

// Companies
$router->get('/admin/companies', 'Admin\\CompaniesController', 'index');
$router->get('/admin/companies/create', 'Admin\\CompaniesController', 'create');
$router->post('/admin/companies', 'Admin\\CompaniesController', 'store');
$router->get('/admin/companies/import', 'Admin\\CompaniesController', 'import');
$router->post('/admin/companies/import', 'Admin\\CompaniesController', 'import');
$router->get('/admin/companies/export', 'Admin\\CompaniesController', 'export');
$router->get('/admin/companies/{id}/edit', 'Admin\\CompaniesController', 'edit');
$router->post('/admin/companies/{id}', 'Admin\\CompaniesController', 'update');
$router->post('/admin/companies/{id}/delete', 'Admin\\CompaniesController', 'destroy');
$router->post('/admin/companies/{id}/regenerate-code', 'Admin\\CompaniesController', 'regenerateCode');

// Tickets
$router->get('/admin/tickets', 'Admin\\TicketsController', 'index');
$router->get('/admin/tickets/export', 'Admin\\TicketsController', 'export');
$router->get('/admin/tickets/scanner', 'Admin\\TicketsController', 'scanner');
$router->post('/admin/tickets/validate-code', 'Admin\\TicketsController', 'validateCode');
$router->get('/admin/tickets/types', 'Admin\\TicketsController', 'types');
$router->post('/admin/tickets/types', 'Admin\\TicketsController', 'createType');
$router->post('/admin/tickets/types/{id}', 'Admin\\TicketsController', 'updateType');
$router->post('/admin/tickets/types/{id}/delete', 'Admin\\TicketsController', 'deleteType');
$router->get('/admin/tickets/{id}', 'Admin\\TicketsController', 'show');
$router->post('/admin/tickets/{id}/check-in', 'Admin\\TicketsController', 'checkIn');
$router->post('/admin/tickets/{id}/cancel', 'Admin\\TicketsController', 'cancel');

// Meetings
$router->get('/admin/meetings/blocks', 'Admin\\MeetingsController', 'blocks');
$router->post('/admin/meetings/blocks', 'Admin\\MeetingsController', 'createBlock');
$router->post('/admin/meetings/blocks/{id}', 'Admin\\MeetingsController', 'updateBlock');
$router->post('/admin/meetings/blocks/{id}/delete', 'Admin\\MeetingsController', 'deleteBlock');
$router->post('/admin/meetings/blocks/{id}/regenerate-slots', 'Admin\\MeetingsController', 'regenerateSlots');
$router->get('/admin/meetings/assignments', 'Admin\\MeetingsController', 'assignments');
$router->get('/admin/meetings/unassigned', 'Admin\\MeetingsController', 'unassigned');
$router->get('/admin/meetings/available-slots', 'Admin\\MeetingsController', 'availableSlots');
$router->post('/admin/meetings/assign', 'Admin\\MeetingsController', 'assign');
$router->post('/admin/meetings/assignments/{id}/cancel', 'Admin\\MeetingsController', 'cancelAssignment');
$router->get('/admin/meetings/export', 'Admin\\MeetingsController', 'export');
$router->get('/admin/meetings/matching', 'Admin\\MeetingsController', 'matching');

// Votings
$router->get('/admin/votings', 'Admin\\VotingsController', 'index');
$router->get('/admin/votings/create', 'Admin\\VotingsController', 'create');
$router->post('/admin/votings', 'Admin\\VotingsController', 'store');
$router->get('/admin/votings/{id}/edit', 'Admin\\VotingsController', 'edit');
$router->post('/admin/votings/{id}', 'Admin\\VotingsController', 'update');
$router->post('/admin/votings/{id}/delete', 'Admin\\VotingsController', 'destroy');
$router->get('/admin/votings/{id}/results', 'Admin\\VotingsController', 'results');
$router->post('/admin/votings/{id}/reset-votes', 'Admin\\VotingsController', 'resetVotes');
$router->get('/admin/votings/{id}/candidates', 'Admin\\VotingsController', 'candidates');
$router->post('/admin/votings/{id}/candidates', 'Admin\\VotingsController', 'addCandidate');
$router->post('/admin/votings/{id}/candidates/{candidateId}', 'Admin\\VotingsController', 'updateCandidate');
$router->post('/admin/votings/{id}/candidates/{candidateId}/delete', 'Admin\\VotingsController', 'deleteCandidate');

// TLOS Settings
$router->get('/admin/tlos-settings', 'Admin\\TlosSettingsController', 'index');
$router->post('/admin/tlos-settings', 'Admin\\TlosSettingsController', 'update');
$router->get('/admin/tlos-settings/get', 'Admin\\TlosSettingsController', 'get');
$router->post('/admin/tlos-settings/set', 'Admin\\TlosSettingsController', 'set');
$router->post('/admin/tlos-settings/test-email', 'Admin\\TlosSettingsController', 'testEmail');
$router->post('/admin/tlos-settings/test-stripe', 'Admin\\TlosSettingsController', 'testStripe');

// Rooms
$router->get('/admin/rooms', 'Admin\\RoomsController', 'index');
$router->get('/admin/rooms/create', 'Admin\\RoomsController', 'create');
$router->post('/admin/rooms', 'Admin\\RoomsController', 'store');
$router->post('/admin/rooms/reorder', 'Admin\\RoomsController', 'reorder');
$router->get('/admin/rooms/{id}/edit', 'Admin\\RoomsController', 'edit');
$router->post('/admin/rooms/{id}', 'Admin\\RoomsController', 'update');
$router->post('/admin/rooms/{id}/delete', 'Admin\\RoomsController', 'destroy');

// Activities
$router->get('/admin/activities', 'Admin\\ActivitiesController', 'index');
$router->get('/admin/activities/create', 'Admin\\ActivitiesController', 'create');
$router->post('/admin/activities', 'Admin\\ActivitiesController', 'store');
$router->post('/admin/activities/reorder', 'Admin\\ActivitiesController', 'reorder');
$router->get('/admin/activities/{id}/edit', 'Admin\\ActivitiesController', 'edit');
$router->post('/admin/activities/{id}', 'Admin\\ActivitiesController', 'update');
$router->post('/admin/activities/{id}/delete', 'Admin\\ActivitiesController', 'destroy');
$router->post('/admin/activities/{id}/duplicate', 'Admin\\ActivitiesController', 'duplicate');
$router->get('/admin/activities/event/{eventId}', 'Admin\\ActivitiesController', 'getByEvent');

// ============================================
// TLOS Frontend Routes
// ============================================

// Events
$router->get('/eventos', 'EventsController', 'index');
$router->get('/eventos/{slug}', 'EventsController', 'show');
$router->get('/eventos/{slug}/agenda', 'EventsController', 'agenda');
$router->get('/eventos/{slug}/sponsors', 'EventsController', 'sponsors');

// Tickets
$router->get('/eventos/{slug}/registro', 'TicketsController', 'register');
$router->post('/eventos/{slug}/registro', 'TicketsController', 'store');
$router->get('/eventos/{slug}/ticket/confirmacion', 'TicketsController', 'paymentSuccess');
$router->get('/eventos/{slug}/ticket/{code}', 'TicketsController', 'show');
$router->get('/eventos/{slug}/ticket/{code}/download', 'TicketsController', 'download');
$router->post('/eventos/{slug}/ticket/{code}/resend', 'TicketsController', 'resendEmail');

// Sponsor Panel
$router->get('/sponsor/login', 'SponsorPanelController', 'login');
$router->post('/sponsor/login', 'SponsorPanelController', 'login');
$router->get('/sponsor/logout', 'SponsorPanelController', 'logout');
$router->get('/sponsor/panel', 'SponsorPanelController', 'panel');
$router->get('/sponsor/empresas/{eventId}', 'SponsorPanelController', 'companies');
$router->get('/sponsor/empresas/{eventId}/{companyId}', 'SponsorPanelController', 'companyDetail');
$router->post('/sponsor/seleccionar', 'SponsorPanelController', 'selectCompany');
$router->post('/sponsor/deseleccionar', 'SponsorPanelController', 'unselectCompany');
$router->get('/sponsor/matches/{eventId}', 'SponsorPanelController', 'matches');

// Company Panel
$router->get('/empresa/login', 'CompanyPanelController', 'login');
$router->post('/empresa/login', 'CompanyPanelController', 'login');
$router->get('/empresa/logout', 'CompanyPanelController', 'logout');
$router->get('/empresa/panel', 'CompanyPanelController', 'panel');
$router->get('/empresa/sponsors/{eventId}', 'CompanyPanelController', 'sponsors');
$router->get('/empresa/sponsors/{eventId}/{sponsorId}', 'CompanyPanelController', 'sponsorDetail');
$router->post('/empresa/seleccionar', 'CompanyPanelController', 'selectSponsor');
$router->post('/empresa/deseleccionar', 'CompanyPanelController', 'unselectSponsor');
$router->get('/empresa/matches/{eventId}', 'CompanyPanelController', 'matches');
$router->post('/empresa/perfil', 'CompanyPanelController', 'updateProfile');

// Voting
$router->get('/votar/{slug}', 'VotingController', 'show');
$router->post('/votar/{slug}', 'VotingController', 'vote');
$router->get('/votar/{slug}/resultados', 'VotingController', 'results');

// Stripe Webhook
$router->post('/webhook/stripe', 'WebhookController', 'stripe');

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
