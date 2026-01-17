-- We're Sinapsis CMS Database Schema
-- MySQL 8.0+

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- Users
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'editor', 'author') DEFAULT 'editor',
    `avatar` VARCHAR(255) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Categories (Shared across all entities)
-- ============================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL COMMENT 'Supports HTML/WYSIWYG content',
    `featured_image` VARCHAR(255) NULL COMMENT 'Hero image for category page',
    `parent_id` INT UNSIGNED NULL,
    `color` VARCHAR(7) NULL COMMENT 'Hex color code like #FF5733',
    `icon` VARCHAR(50) NULL COMMENT 'FontAwesome icon class',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_parent` (`parent_id`),
    INDEX `idx_active` (`is_active`),
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Pages
-- ============================================
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` LONGTEXT NULL,
    `excerpt` TEXT NULL,
    `template` VARCHAR(50) DEFAULT 'default',
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `meta_keywords` VARCHAR(255) NULL,
    `og_image` VARCHAR(255) NULL,
    `is_homepage` TINYINT(1) DEFAULT 0,
    `author_id` INT UNSIGNED NULL,
    `published_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Page Blocks
-- ============================================
CREATE TABLE IF NOT EXISTS `page_blocks` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `page_id` INT UNSIGNED NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `content` JSON NULL,
    `settings` JSON NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_page` (`page_id`),
    INDEX `idx_sort` (`sort_order`),
    FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Posts (Blog)
-- ============================================
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` LONGTEXT NULL,
    `excerpt` TEXT NULL,
    `thumbnail` VARCHAR(255) NULL,
    `hero_image` VARCHAR(255) NULL,
    `category_id` INT UNSIGNED NULL,
    `author_id` INT UNSIGNED NULL,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `is_featured` TINYINT(1) DEFAULT 0,
    `views` INT UNSIGNED DEFAULT 0,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `tags` VARCHAR(500) NULL,
    `published_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`),
    INDEX `idx_category` (`category_id`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_published` (`published_at`),
    FULLTEXT INDEX `idx_search` (`title`, `content`, `excerpt`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Services (formerly Features)
-- ============================================
CREATE TABLE IF NOT EXISTS `services` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `short_description` TEXT NULL,
    `full_description` LONGTEXT NULL,
    `icon_class` VARCHAR(100) NULL COMMENT 'FontAwesome icon class',
    `icon_svg` TEXT NULL COMMENT 'Custom SVG icon',
    `image` VARCHAR(255) NULL,
    `video_url` VARCHAR(500) NULL COMMENT 'YouTube/Vimeo embed URL',
    `category_id` INT UNSIGNED NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_category` (`category_id`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_featured` (`is_featured`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tools (formerly Integrations)
-- ============================================
CREATE TABLE IF NOT EXISTS `tools` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `subtitle` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `logo` VARCHAR(255) NULL,
    `platform_url` VARCHAR(500) NULL COMMENT 'Link to tool website',
    `video_url` VARCHAR(500) NULL COMMENT 'YouTube/Vimeo embed URL',
    `gallery` JSON NULL COMMENT 'Array of image objects with url and caption',
    `category_id` INT UNSIGNED NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_category` (`category_id`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_featured` (`is_featured`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- FAQs
-- ============================================
CREATE TABLE IF NOT EXISTS `faqs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `question` VARCHAR(500) NOT NULL,
    `answer` TEXT NOT NULL,
    `category_id` INT UNSIGNED NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `views` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_category` (`category_id`),
    INDEX `idx_active` (`is_active`),
    FULLTEXT INDEX `idx_search` (`question`, `answer`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Clients
-- ============================================
CREATE TABLE IF NOT EXISTS `clients` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `logo` VARCHAR(255) NULL,
    `website` VARCHAR(500) NULL,
    `industry` VARCHAR(100) NULL,
    `company_size` VARCHAR(50) NULL COMMENT 'e.g., 1-10, 11-50, 51-200, 201-500, 500+',
    `location` VARCHAR(255) NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_industry` (`industry`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_featured` (`is_featured`),
    FULLTEXT INDEX `idx_search` (`name`, `description`, `industry`, `location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Success Cases (Projects/Portfolio)
-- ============================================
CREATE TABLE IF NOT EXISTS `success_cases` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `client_id` INT UNSIGNED NOT NULL,
    `category_id` INT UNSIGNED NULL,
    `featured_image` VARCHAR(255) NULL,
    `video_url` VARCHAR(500) NULL COMMENT 'YouTube/Vimeo embed URL',
    `gallery` JSON NULL COMMENT 'Array of image objects: [{url, caption}]',
    `gallery_display` ENUM('carousel', 'grid') DEFAULT 'carousel',
    `challenge` TEXT NULL COMMENT 'The problem/challenge faced',
    `solution` TEXT NULL COMMENT 'The solution implemented',
    `results` TEXT NULL COMMENT 'The results achieved',
    `metrics` JSON NULL COMMENT 'Key metrics: [{value, label}]',
    `testimonial` TEXT NULL,
    `testimonial_author` VARCHAR(100) NULL,
    `testimonial_role` VARCHAR(100) NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `sort_order` INT DEFAULT 0,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `published_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_client` (`client_id`),
    INDEX `idx_category` (`category_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_featured` (`is_featured`),
    FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Service-Case Pivot Table (N:N)
-- ============================================
CREATE TABLE IF NOT EXISTS `service_cases` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `service_id` INT UNSIGNED NOT NULL,
    `case_id` INT UNSIGNED NOT NULL,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_service_case` (`service_id`, `case_id`),
    INDEX `idx_service` (`service_id`),
    INDEX `idx_case` (`case_id`),
    FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`case_id`) REFERENCES `success_cases`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Case-Tool Pivot Table (N:N)
-- ============================================
CREATE TABLE IF NOT EXISTS `case_tools` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `case_id` INT UNSIGNED NOT NULL,
    `tool_id` INT UNSIGNED NOT NULL,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_case_tool` (`case_id`, `tool_id`),
    INDEX `idx_case` (`case_id`),
    INDEX `idx_tool` (`tool_id`),
    FOREIGN KEY (`case_id`) REFERENCES `success_cases`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tool_id`) REFERENCES `tools`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Media Library
-- ============================================
CREATE TABLE IF NOT EXISTS `media` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `path` VARCHAR(500) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `file_size` INT UNSIGNED NOT NULL,
    `width` INT UNSIGNED NULL,
    `height` INT UNSIGNED NULL,
    `alt_text` VARCHAR(255) NULL,
    `title` VARCHAR(255) NULL,
    `caption` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_mime` (`mime_type`),
    INDEX `idx_filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Translations
-- ============================================
CREATE TABLE IF NOT EXISTS `translations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT UNSIGNED NOT NULL,
    `field` VARCHAR(100) NOT NULL,
    `language` CHAR(2) NOT NULL,
    `content` LONGTEXT NULL,
    `is_approved` TINYINT(1) DEFAULT 0,
    `translated_at` TIMESTAMP NULL,
    `approved_at` TIMESTAMP NULL,
    `approved_by` INT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_translation` (`entity_type`, `entity_id`, `field`, `language`),
    INDEX `idx_entity` (`entity_type`, `entity_id`),
    INDEX `idx_language` (`language`),
    INDEX `idx_approved` (`is_approved`),
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Settings
-- ============================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT NULL,
    `type` ENUM('string', 'text', 'number', 'boolean', 'json') DEFAULT 'string',
    `group` VARCHAR(50) DEFAULT 'general',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_key` (`key`),
    INDEX `idx_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Menus
-- ============================================
CREATE TABLE IF NOT EXISTS `menus` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `location` ENUM('header', 'footer', 'sidebar', 'other') DEFAULT 'header',
    `description` VARCHAR(255) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Menu Items
-- ============================================
CREATE TABLE IF NOT EXISTS `menu_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `menu_id` INT UNSIGNED NOT NULL,
    `parent_id` INT UNSIGNED NULL,
    `title` VARCHAR(100) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `target` ENUM('_self', '_blank') DEFAULT '_self',
    `icon` VARCHAR(100) NULL COMMENT 'FontAwesome icon class',
    `css_class` VARCHAR(100) NULL COMMENT 'Custom CSS classes',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_menu` (`menu_id`),
    INDEX `idx_parent` (`parent_id`),
    INDEX `idx_sort` (`sort_order`),
    FOREIGN KEY (`menu_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`parent_id`) REFERENCES `menu_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Team Members
-- ============================================
CREATE TABLE IF NOT EXISTS `team_members` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `role` VARCHAR(100) NULL,
    `bio` TEXT NULL,
    `photo` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `linkedin` VARCHAR(500) NULL,
    `twitter` VARCHAR(500) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Contact Submissions
-- ============================================
CREATE TABLE IF NOT EXISTS `contact_submissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) NULL,
    `company` VARCHAR(255) NULL,
    `message` TEXT NULL,
    `form_data` JSON NULL COMMENT 'Additional form fields',
    `source` VARCHAR(100) NULL COMMENT 'Form source: contact_page, floating_form, etc.',
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_source` (`source`),
    INDEX `idx_read` (`is_read`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
