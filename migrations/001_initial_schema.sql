-- ============================================
-- Omniwallet CMS - Migración Inicial
-- Version: 1.0.0
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------
-- Tabla: users
-- --------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'editor', 'viewer') DEFAULT 'editor',
    `avatar` VARCHAR(255) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_email` (`email`),
    INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario admin por defecto (password: admin123 - CAMBIAR EN PRODUCCIÓN)
INSERT INTO `users` (`email`, `password`, `name`, `role`) VALUES
('admin@omniwallet.net', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'admin');

-- --------------------------------------------
-- Tabla: languages
-- --------------------------------------------
DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(5) NOT NULL UNIQUE,
    `name` VARCHAR(50) NOT NULL,
    `native_name` VARCHAR(50) NOT NULL,
    `is_default` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_code` (`code`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `languages` (`code`, `name`, `native_name`, `is_default`, `sort_order`) VALUES
('es', 'Español', 'Español', 1, 1),
('en', 'Inglés', 'English', 0, 2),
('it', 'Italiano', 'Italiano', 0, 3),
('fr', 'Francés', 'Français', 0, 4),
('de', 'Alemán', 'Deutsch', 0, 5);

-- --------------------------------------------
-- Tabla: categories
-- --------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `color` VARCHAR(7) NULL,
    `icon` VARCHAR(50) NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_slug` (`slug`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categorías de ejemplo
INSERT INTO `categories` (`slug`, `name`, `description`, `color`, `sort_order`) VALUES
('retail', 'Retail y Ecommerce', 'Casos de éxito en retail y comercio electrónico', '#3E95B0', 1),
('restauracion', 'Restauración', 'Restaurantes, cafeterías y franquicias', '#255664', 2),
('servicios', 'Servicios', 'Empresas de servicios', '#4DBBDD', 3),
('fidelizacion', 'Fidelización', 'Artículos sobre estrategias de fidelización', '#22c55e', 4),
('tecnologia', 'Tecnología', 'Novedades tecnológicas y actualizaciones', '#3b82f6', 5);

-- --------------------------------------------
-- Tabla: pages
-- --------------------------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `template` VARCHAR(50) DEFAULT 'default',
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `enable_llm_qa` TINYINT(1) DEFAULT 0,
    `llm_qa_content` TEXT NULL,
    `show_header` TINYINT(1) DEFAULT 1,
    `show_footer` TINYINT(1) DEFAULT 1,
    `custom_css` TEXT NULL,
    `custom_js` TEXT NULL,
    `author_id` INT UNSIGNED NULL,
    `published_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`),
    INDEX `idx_published` (`published_at`),
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Página home de ejemplo
INSERT INTO `pages` (`slug`, `title`, `status`, `meta_title`, `meta_description`, `author_id`, `published_at`) VALUES
('home', 'Inicio', 'published', 'Omniwallet - Plataforma de Fidelización Omnicanal', 'Potencia la relación con tus clientes con un programa de fidelización omnicanal. Aumenta la recurrencia de compra y retención.', 1, NOW());

-- --------------------------------------------
-- Tabla: blocks
-- --------------------------------------------
DROP TABLE IF EXISTS `blocks`;
CREATE TABLE `blocks` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `page_id` INT UNSIGNED NOT NULL,
    `block_type` VARCHAR(50) NOT NULL,
    `position` INT DEFAULT 0,
    `content` JSON NOT NULL,
    `settings` JSON NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_page` (`page_id`),
    INDEX `idx_position` (`position`),
    INDEX `idx_type` (`block_type`),
    FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- Tabla: posts
-- --------------------------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `category_id` INT UNSIGNED NULL,
    `author_id` INT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `subtitle` VARCHAR(255) NULL,
    `excerpt` TEXT NULL,
    `content` LONGTEXT NULL,
    `hero_image` VARCHAR(255) NULL,
    `thumbnail` VARCHAR(255) NULL,
    `gallery` JSON NULL,
    `video_url` VARCHAR(255) NULL,
    `video_thumbnail` VARCHAR(255) NULL,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `enable_llm_qa` TINYINT(1) DEFAULT 0,
    `llm_qa_content` TEXT NULL,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `is_featured` TINYINT(1) DEFAULT 0,
    `views` INT UNSIGNED DEFAULT 0,
    `published_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_slug` (`slug`),
    INDEX `idx_category` (`category_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_published` (`published_at`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- Tabla: success_cases
-- --------------------------------------------
DROP TABLE IF EXISTS `success_cases`;
CREATE TABLE `success_cases` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `category_id` INT UNSIGNED NULL,
    `client_name` VARCHAR(255) NOT NULL,
    `client_logo` VARCHAR(255) NULL,
    `client_website` VARCHAR(255) NULL,
    `loyalty_page_url` VARCHAR(255) NULL,
    `title` VARCHAR(255) NOT NULL,
    `short_description` TEXT NULL,
    `full_description` LONGTEXT NULL,
    `featured_image` VARCHAR(255) NULL,
    `gallery` JSON NULL,
    `metrics` JSON NULL,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `is_featured` TINYINT(1) DEFAULT 0,
    `sort_order` INT DEFAULT 0,
    `published_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_slug` (`slug`),
    INDEX `idx_category` (`category_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_featured` (`is_featured`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- Tabla: features
-- --------------------------------------------
DROP TABLE IF EXISTS `features`;
CREATE TABLE `features` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `short_description` TEXT NULL,
    `full_description` LONGTEXT NULL,
    `icon_svg` TEXT NULL,
    `icon_class` VARCHAR(100) NULL,
    `image` VARCHAR(255) NULL,
    `video_url` VARCHAR(255) NULL,
    `feature_group` VARCHAR(50) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_slug` (`slug`),
    INDEX `idx_group` (`feature_group`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Features de ejemplo basadas en Omniwallet
INSERT INTO `features` (`slug`, `title`, `short_description`, `feature_group`, `sort_order`) VALUES
('programa-puntos', 'Programa de puntos', 'Sistema completo de acumulación y canje de puntos', 'core', 1),
('wallet-cards', 'Wallet Cards', 'Tarjetas digitales para Apple Wallet y Google Pay', 'core', 2),
('programa-referidos', 'Programa de referidos', 'Recompensa a tus clientes por traer nuevos usuarios', 'core', 3),
('notificaciones-push', 'Notificaciones push', 'Comunicación directa e instantánea con tus clientes', 'core', 4),
('emails-personalizables', 'Emails personalizables', 'Plantillas de email adaptadas a tu marca', 'core', 5),
('integraciones', 'Integraciones ilimitadas', 'Conecta con tu ecommerce, TPV y herramientas', 'core', 6),
('api-acceso', 'Acceso a la API', 'API REST completa para integraciones personalizadas', 'core', 7),
('loyalty-master', 'Loyalty Master', 'Panel de control para gestionar tu programa', 'core', 8),
('niveles-cliente', 'Niveles de cliente', 'Crea jerarquías para premiar a los más fieles', 'plus', 9),
('bloqueo-puntos', 'Bloqueo de puntos', 'Protección contra fraude y abusos', 'plus', 10),
('analitica-plus', 'Analítica Plus', 'Métricas avanzadas de tu programa', 'plus', 11),
('campanas', 'Campañas', 'Crea campañas promocionales temporales', 'advanced', 12),
('juegos', 'Juegos', 'Gamificación para aumentar engagement', 'advanced', 13),
('catalogo', 'Catálogo', 'Catálogo de recompensas personalizables', 'advanced', 14),
('tarjetas-regalo', 'Tarjetas de regalo', 'Vende y gestiona gift cards', 'advanced', 15);

-- --------------------------------------------
-- Tabla: faqs
-- --------------------------------------------
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `question` TEXT NOT NULL,
    `answer` LONGTEXT NOT NULL,
    `faq_group` VARCHAR(50) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_group` (`faq_group`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FAQs de ejemplo
INSERT INTO `faqs` (`question`, `answer`, `faq_group`, `sort_order`) VALUES
('¿Qué es Omniwallet?', 'Omniwallet es una plataforma de fidelización omnicanal que te permite crear y gestionar programas de puntos, tarjetas de fidelidad digitales, programas de referidos y mucho más.', 'general', 1),
('¿Cuánto cuesta Omniwallet?', 'Omniwallet tiene un plan gratuito con hasta 250 actividades mensuales. Los planes de pago empiezan desde 39€/mes para Starter, 179€/mes para Plus y 399€/mes para Advanced.', 'pricing', 2),
('¿Qué son las actividades?', 'Las actividades son las interacciones de los clientes con tu programa: acumulación de puntos, canjes, referidos, etc. Se calculan aproximadamente como ventas × 1.3.', 'pricing', 3),
('¿Puedo integrar Omniwallet con mi tienda online?', 'Sí, Omniwallet se integra con las principales plataformas de ecommerce como PrestaShop, WooCommerce, Shopify, Magento y más. También ofrecemos una API REST completa.', 'technical', 4),
('¿Hay periodo de prueba?', 'Sí, puedes empezar gratis con hasta 250 actividades mensuales sin límite de tiempo. Cuando necesites más, puedes actualizar a un plan de pago.', 'general', 5);

-- --------------------------------------------
-- Tabla: knowledge_topics
-- --------------------------------------------
DROP TABLE IF EXISTS `knowledge_topics`;
CREATE TABLE `knowledge_topics` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `parent_id` INT UNSIGNED NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(100) NULL,
    `color` VARCHAR(7) NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_slug` (`slug`),
    INDEX `idx_parent` (`parent_id`),
    INDEX `idx_active` (`is_active`),
    FOREIGN KEY (`parent_id`) REFERENCES `knowledge_topics`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Temas de ejemplo
INSERT INTO `knowledge_topics` (`slug`, `name`, `description`, `sort_order`) VALUES
('primeros-pasos', 'Primeros pasos', 'Guías para empezar con Omniwallet', 1),
('integraciones', 'Integraciones', 'Cómo conectar Omniwallet con otras plataformas', 2),
('programa-puntos', 'Programa de puntos', 'Todo sobre la configuración de puntos', 3),
('api', 'API y desarrollo', 'Documentación técnica para desarrolladores', 4);

-- --------------------------------------------
-- Tabla: knowledge_articles
-- --------------------------------------------
DROP TABLE IF EXISTS `knowledge_articles`;
CREATE TABLE `knowledge_articles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `topic_id` INT UNSIGNED NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `excerpt` TEXT NULL,
    `content` LONGTEXT NOT NULL,
    `featured_image` VARCHAR(255) NULL,
    `images` JSON NULL,
    `video_url` VARCHAR(255) NULL,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `search_keywords` TEXT NULL,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `is_featured` TINYINT(1) DEFAULT 0,
    `views` INT UNSIGNED DEFAULT 0,
    `helpful_yes` INT UNSIGNED DEFAULT 0,
    `helpful_no` INT UNSIGNED DEFAULT 0,
    `published_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_slug` (`slug`),
    INDEX `idx_topic` (`topic_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_featured` (`is_featured`),
    FULLTEXT `idx_search` (`title`, `content`, `search_keywords`),
    FOREIGN KEY (`topic_id`) REFERENCES `knowledge_topics`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- Tabla: translations
-- --------------------------------------------
DROP TABLE IF EXISTS `translations`;
CREATE TABLE `translations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `translatable_type` VARCHAR(50) NOT NULL,
    `translatable_id` INT UNSIGNED NOT NULL,
    `field_name` VARCHAR(100) NOT NULL,
    `language_code` VARCHAR(5) NOT NULL,
    `translated_content` LONGTEXT NOT NULL,
    `status` ENUM('pending', 'auto', 'reviewed', 'manual') DEFAULT 'pending',
    `translated_at` DATETIME NULL,
    `reviewed_by` INT UNSIGNED NULL,
    `reviewed_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY `unique_translation` (`translatable_type`, `translatable_id`, `field_name`, `language_code`),
    INDEX `idx_translatable` (`translatable_type`, `translatable_id`),
    INDEX `idx_language` (`language_code`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- Tabla: translation_queue
-- --------------------------------------------
DROP TABLE IF EXISTS `translation_queue`;
CREATE TABLE `translation_queue` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `translatable_type` VARCHAR(50) NOT NULL,
    `translatable_id` INT UNSIGNED NOT NULL,
    `fields` JSON NOT NULL,
    `target_languages` JSON NOT NULL,
    `status` ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    `attempts` INT DEFAULT 0,
    `error_message` TEXT NULL,
    `priority` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `processed_at` DATETIME NULL,
    
    INDEX `idx_status` (`status`),
    INDEX `idx_priority` (`priority` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- Tabla: settings
-- --------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` LONGTEXT NULL,
    `setting_type` ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_key` (`setting_key`),
    INDEX `idx_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuraciones iniciales
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `description`) VALUES
('site_name', 'Omniwallet', 'string', 'general', 'Nombre del sitio'),
('site_tagline', 'Plataforma de fidelización omnicanal', 'string', 'general', 'Lema del sitio'),
('site_url', 'https://www.omniwallet.net', 'string', 'general', 'URL del sitio'),
('contact_email', 'info@omniwallet.net', 'string', 'contact', 'Email de contacto'),
('demo_url', 'https://www.omniwallet.net/reservar-demo/', 'string', 'links', 'URL para reservar demo'),
('openai_api_key', '', 'string', 'api', 'API Key de OpenAI para traducciones'),
('openai_model', 'gpt-4o-mini', 'string', 'api', 'Modelo de OpenAI a usar'),
('translation_auto_enabled', '1', 'boolean', 'translation', 'Activar traducción automática'),
('default_meta_title', 'Omniwallet - Fidelización Omnicanal', 'string', 'seo', 'Meta title por defecto'),
('default_meta_description', 'Potencia la relación con tus clientes con un programa de fidelización omnicanal', 'string', 'seo', 'Meta description por defecto'),
('pricing_multiplier', '1.3', 'number', 'pricing', 'Multiplicador de actividades (ventas × X)'),
('pricing_enterprise_threshold', '50000', 'number', 'pricing', 'Umbral de actividades para plan Enterprise');

-- --------------------------------------------
-- Tabla: media
-- --------------------------------------------
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `path` VARCHAR(500) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `file_size` INT UNSIGNED NOT NULL,
    `width` INT UNSIGNED NULL,
    `height` INT UNSIGNED NULL,
    `alt_text` VARCHAR(255) NULL,
    `caption` TEXT NULL,
    `folder` VARCHAR(100) DEFAULT 'general',
    `uploaded_by` INT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_folder` (`folder`),
    INDEX `idx_mime` (`mime_type`),
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------
-- Tabla: menu_items
-- --------------------------------------------
DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `menu_location` VARCHAR(50) NOT NULL,
    `parent_id` INT UNSIGNED NULL,
    `title` VARCHAR(100) NOT NULL,
    `url` VARCHAR(255) NULL,
    `page_id` INT UNSIGNED NULL,
    `target` VARCHAR(20) DEFAULT '_self',
    `css_class` VARCHAR(100) NULL,
    `icon` VARCHAR(50) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_location` (`menu_location`),
    INDEX `idx_parent` (`parent_id`),
    FOREIGN KEY (`parent_id`) REFERENCES `menu_items`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menú header de ejemplo
INSERT INTO `menu_items` (`menu_location`, `title`, `url`, `sort_order`) VALUES
('header', 'Funcionalidades', '/funcionalidades', 1),
('header', 'Casos de uso', '/casos-de-uso', 2),
('header', 'Precios', '/precios', 3),
('header', 'Recursos', '/recursos', 4),
('header', 'Reservar demo', 'https://www.omniwallet.net/reservar-demo/', 5);

SET FOREIGN_KEY_CHECKS = 1;
