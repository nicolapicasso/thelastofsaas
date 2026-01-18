-- ============================================
-- TLOS - The Last of SaaS
-- Complete Database Schema (Consolidated) - FIXED
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- DROP ALL EXISTING TABLES (clean start)
-- This includes tables from previous CMS installations
-- ============================================

-- Drop old CMS tables (from Sinapsis or other)
DROP TABLE IF EXISTS `Nueva`;
DROP TABLE IF EXISTS `blocks`;
DROP TABLE IF EXISTS `email_templates`;
DROP TABLE IF EXISTS `features`;
DROP TABLE IF EXISTS `knowledge_articles`;
DROP TABLE IF EXISTS `knowledge_topics`;
DROP TABLE IF EXISTS `languages`;
DROP TABLE IF EXISTS `live_activity_log`;
DROP TABLE IF EXISTS `success_cases`;
DROP TABLE IF EXISTS `team_members`;
DROP TABLE IF EXISTS `translation_queue`;
DROP TABLE IF EXISTS `chatbot_logs`;
DROP TABLE IF EXISTS `chatbot_context`;
DROP TABLE IF EXISTS `admin_logs`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `migrations`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `failed_jobs`;

-- Drop TLOS tables
DROP TABLE IF EXISTS `votes`;
DROP TABLE IF EXISTS `voting_candidates`;
DROP TABLE IF EXISTS `votings`;
DROP TABLE IF EXISTS `meeting_assignments`;
DROP TABLE IF EXISTS `meeting_slots`;
DROP TABLE IF EXISTS `meeting_blocks`;
DROP TABLE IF EXISTS `sponsor_messages`;
DROP TABLE IF EXISTS `company_selections`;
DROP TABLE IF EXISTS `sponsor_selections`;
DROP TABLE IF EXISTS `tickets`;
DROP TABLE IF EXISTS `ticket_types`;
DROP TABLE IF EXISTS `event_companies`;
DROP TABLE IF EXISTS `company_saas_usage`;
DROP TABLE IF EXISTS `companies`;
DROP TABLE IF EXISTS `event_sponsors`;
DROP TABLE IF EXISTS `sponsors`;
DROP TABLE IF EXISTS `event_features`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `email_notifications`;
DROP TABLE IF EXISTS `tlos_settings`;
DROP TABLE IF EXISTS `contact_submissions`;
DROP TABLE IF EXISTS `seo_metadata`;
DROP TABLE IF EXISTS `translations`;
DROP TABLE IF EXISTS `faqs`;
DROP TABLE IF EXISTS `menu_items`;
DROP TABLE IF EXISTS `menus`;
DROP TABLE IF EXISTS `media`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `page_blocks`;
DROP TABLE IF EXISTS `pages`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `settings`;

-- ============================================
-- CORE CMS TABLES
-- ============================================

-- Settings
CREATE TABLE `settings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_type` VARCHAR(50) DEFAULT 'text',
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users (admin)
CREATE TABLE `users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'editor', 'user') DEFAULT 'user',
    `avatar` VARCHAR(500),
    `active` TINYINT(1) DEFAULT 1,
    `last_login` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories
CREATE TABLE `categories` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT,
    `parent_id` INT DEFAULT NULL,
    `icon` VARCHAR(100),
    `icon_image` VARCHAR(500),
    `color` VARCHAR(20),
    `featured_image` VARCHAR(500),
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `meta_title` VARCHAR(255),
    `meta_description` TEXT,
    `llm_qa_generated` TINYINT(1) DEFAULT 0,
    `llm_qa_content` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pages
CREATE TABLE `pages` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` LONGTEXT,
    `excerpt` TEXT,
    `featured_image` VARCHAR(500),
    `template` VARCHAR(100) DEFAULT 'default',
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `is_homepage` TINYINT(1) DEFAULT 0,
    `parent_id` INT DEFAULT NULL,
    `display_order` INT DEFAULT 0,
    `meta_title` VARCHAR(255),
    `meta_description` TEXT,
    `meta_keywords` VARCHAR(500),
    `og_title` VARCHAR(255),
    `og_description` TEXT,
    `og_image` VARCHAR(500),
    `llm_qa_generated` TINYINT(1) DEFAULT 0,
    `llm_qa_content` TEXT,
    `author_id` INT,
    `published_at` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Page Blocks
CREATE TABLE `page_blocks` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `page_id` INT NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255),
    `content` LONGTEXT,
    `settings` JSON,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Posts (Blog)
CREATE TABLE `posts` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `subtitle` VARCHAR(500),
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` LONGTEXT,
    `excerpt` TEXT,
    `featured_image` VARCHAR(500),
    `category_id` INT,
    `author_id` INT,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `is_featured` TINYINT(1) DEFAULT 0,
    `views` INT DEFAULT 0,
    `meta_title` VARCHAR(255),
    `meta_description` TEXT,
    `llm_qa_generated` TINYINT(1) DEFAULT 0,
    `llm_qa_content` TEXT,
    `published_at` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`),
    INDEX `idx_published` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Media
CREATE TABLE `media` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255),
    `filepath` VARCHAR(500) NOT NULL,
    `filetype` VARCHAR(100),
    `filesize` INT,
    `width` INT,
    `height` INT,
    `alt_text` VARCHAR(255),
    `title` VARCHAR(255),
    `description` TEXT,
    `folder` VARCHAR(255) DEFAULT 'uploads',
    `uploaded_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_filetype` (`filetype`),
    INDEX `idx_folder` (`folder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menus
CREATE TABLE `menus` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `location` VARCHAR(50),
    `description` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menu Items
CREATE TABLE `menu_items` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `menu_id` INT NOT NULL,
    `parent_id` INT DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `url` VARCHAR(500),
    `target` VARCHAR(20) DEFAULT '_self',
    `icon` VARCHAR(100),
    `css_class` VARCHAR(100),
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_menu_id` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FAQs
CREATE TABLE `faqs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `question` TEXT NOT NULL,
    `answer` TEXT NOT NULL,
    `category_id` INT,
    `faq_group` VARCHAR(100),
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Translations
CREATE TABLE `translations` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT NOT NULL,
    `language` VARCHAR(10) NOT NULL,
    `field_name` VARCHAR(100) NOT NULL,
    `field_value` LONGTEXT,
    `is_auto_translated` TINYINT(1) DEFAULT 0,
    `is_approved` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_translation` (`entity_type`, `entity_id`, `language`, `field_name`),
    INDEX `idx_entity` (`entity_type`, `entity_id`),
    INDEX `idx_language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEO Metadata
CREATE TABLE `seo_metadata` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT NOT NULL,
    `language` VARCHAR(10) DEFAULT 'es',
    `meta_title` VARCHAR(255),
    `meta_description` TEXT,
    `meta_keywords` VARCHAR(500),
    `og_title` VARCHAR(255),
    `og_description` TEXT,
    `og_image` VARCHAR(500),
    `canonical_url` VARCHAR(500),
    `robots` VARCHAR(100),
    `schema_markup` JSON,
    `is_auto_generated` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_seo` (`entity_type`, `entity_id`, `language`),
    INDEX `idx_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact Submissions
CREATE TABLE `contact_submissions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100),
    `email` VARCHAR(255),
    `phone` VARCHAR(50),
    `company` VARCHAR(255),
    `subject` VARCHAR(255),
    `message` TEXT,
    `source_page` VARCHAR(255),
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `status` ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TLOS SPECIFIC TABLES
-- ============================================

-- Events
CREATE TABLE `events` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `short_description` VARCHAR(500),
    `description` LONGTEXT,
    `featured_image` VARCHAR(500),
    `location` VARCHAR(255),
    `address` TEXT,
    `city` VARCHAR(100),
    `coordinates` VARCHAR(100),
    `start_date` DATE NOT NULL,
    `end_date` DATE,
    `start_time` TIME,
    `end_time` TIME,
    `max_attendees` INT DEFAULT 100,
    `status` ENUM('draft', 'published', 'active', 'finished', 'cancelled') DEFAULT 'draft',
    `registration_open` TINYINT(1) DEFAULT 1,
    `matching_enabled` TINYINT(1) DEFAULT 1,
    `meetings_enabled` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `meta_title` VARCHAR(255),
    `meta_description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_start_date` (`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event Features
CREATE TABLE `event_features` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `feature` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(100),
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sponsors
CREATE TABLE `sponsors` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `tagline` VARCHAR(255),
    `description` TEXT,
    `logo_url` VARCHAR(500),
    `website` VARCHAR(500),
    `contact_name` VARCHAR(100),
    `contact_email` VARCHAR(255),
    `contact_phone` VARCHAR(50),
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `active` TINYINT(1) DEFAULT 1,
    `max_simultaneous_meetings` INT DEFAULT 1,
    `linkedin_url` VARCHAR(500),
    `twitter_url` VARCHAR(500),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_code` (`code`),
    INDEX `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event Sponsors (relationship)
CREATE TABLE `event_sponsors` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `sponsor_id` INT NOT NULL,
    `level` ENUM('platinum', 'gold', 'silver', 'bronze') DEFAULT 'bronze',
    `display_order` INT DEFAULT 0,
    `max_free_tickets` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_event_sponsor` (`event_id`, `sponsor_id`),
    INDEX `idx_event_id` (`event_id`),
    INDEX `idx_sponsor_id` (`sponsor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Companies
CREATE TABLE `companies` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT,
    `logo_url` VARCHAR(500),
    `website` VARCHAR(500),
    `sector` VARCHAR(100),
    `employees` VARCHAR(50),
    `contact_name` VARCHAR(100),
    `contact_email` VARCHAR(255),
    `contact_phone` VARCHAR(50),
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `active` TINYINT(1) DEFAULT 1,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_code` (`code`),
    INDEX `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Company SaaS Usage
CREATE TABLE `company_saas_usage` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `company_id` INT NOT NULL,
    `sponsor_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_usage` (`company_id`, `sponsor_id`),
    INDEX `idx_company_id` (`company_id`),
    INDEX `idx_sponsor_id` (`sponsor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event Companies (relationship)
CREATE TABLE `event_companies` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `company_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_event_company` (`event_id`, `company_id`),
    INDEX `idx_event_id` (`event_id`),
    INDEX `idx_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ticket Types
CREATE TABLE `ticket_types` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) DEFAULT 0.00,
    `max_tickets` INT DEFAULT 100,
    `tickets_sold` INT DEFAULT 0,
    `sale_start` DATETIME,
    `sale_end` DATETIME,
    `requires_approval` TINYINT(1) DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tickets
CREATE TABLE `tickets` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `ticket_type_id` INT NOT NULL,
    `sponsor_id` INT DEFAULT NULL,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `attendee_name` VARCHAR(255) NOT NULL,
    `attendee_email` VARCHAR(255) NOT NULL,
    `attendee_phone` VARCHAR(50),
    `attendee_company` VARCHAR(255),
    `attendee_position` VARCHAR(100),
    `price` DECIMAL(10,2) DEFAULT 0.00,
    `status` ENUM('pending', 'pending_payment', 'confirmed', 'used', 'cancelled', 'refunded') DEFAULT 'pending',
    `stripe_payment_id` VARCHAR(255),
    `notes` TEXT,
    `used_at` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_code` (`code`),
    INDEX `idx_email` (`attendee_email`),
    INDEX `idx_status` (`status`),
    INDEX `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sponsor Selections
CREATE TABLE `sponsor_selections` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `sponsor_id` INT NOT NULL,
    `company_id` INT NOT NULL,
    `priority` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_selection` (`event_id`, `sponsor_id`, `company_id`),
    INDEX `idx_event_sponsor` (`event_id`, `sponsor_id`),
    INDEX `idx_event_company` (`event_id`, `company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Company Selections
CREATE TABLE `company_selections` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `company_id` INT NOT NULL,
    `sponsor_id` INT NOT NULL,
    `priority` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_selection` (`event_id`, `company_id`, `sponsor_id`),
    INDEX `idx_event_company` (`event_id`, `company_id`),
    INDEX `idx_event_sponsor` (`event_id`, `sponsor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sponsor Messages
CREATE TABLE `sponsor_messages` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `sponsor_id` INT NOT NULL,
    `company_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `read_at` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_message` (`event_id`, `sponsor_id`, `company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Meeting Blocks
CREATE TABLE `meeting_blocks` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `event_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `slot_duration` INT DEFAULT 15,
    `total_rooms` INT DEFAULT 10,
    `location` VARCHAR(255),
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Meeting Slots
CREATE TABLE `meeting_slots` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `block_id` INT NOT NULL,
    `event_date` DATE NOT NULL,
    `slot_time` TIME NOT NULL,
    `room_number` INT NOT NULL,
    `room_name` VARCHAR(100),
    `is_available` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_slot` (`block_id`, `slot_time`, `room_number`),
    INDEX `idx_block_id` (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Meeting Assignments
CREATE TABLE `meeting_assignments` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `slot_id` INT NOT NULL,
    `event_id` INT NOT NULL,
    `sponsor_id` INT NOT NULL,
    `company_id` INT NOT NULL,
    `status` ENUM('confirmed', 'pending', 'cancelled', 'completed', 'no_show') DEFAULT 'confirmed',
    `notes` TEXT,
    `assigned_by` ENUM('admin', 'live_matching', 'auto') DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_assignment` (`slot_id`),
    INDEX `idx_event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Votings
CREATE TABLE `votings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT,
    `featured_image` VARCHAR(500),
    `status` ENUM('draft', 'active', 'inactive', 'finished') DEFAULT 'draft',
    `voting_start` DATETIME,
    `voting_end` DATETIME,
    `show_vote_counts` TINYINT(1) DEFAULT 1,
    `show_ranking` TINYINT(1) DEFAULT 1,
    `allow_multiple_votes` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Voting Candidates
CREATE TABLE `voting_candidates` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `voting_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `logo_url` VARCHAR(500),
    `website_url` VARCHAR(500),
    `votes` INT DEFAULT 0,
    `base_votes` INT DEFAULT 0,
    `display_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_voting_id` (`voting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Votes (anti-fraud log)
CREATE TABLE `votes` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `voting_id` INT NOT NULL,
    `candidate_id` INT NOT NULL,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `fingerprint` VARCHAR(255),
    `session_id` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ip` (`ip_address`),
    INDEX `idx_voting` (`voting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email Notifications Log
CREATE TABLE `email_notifications` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT DEFAULT NULL,
    `type` VARCHAR(50) NOT NULL,
    `recipient_email` VARCHAR(255) NOT NULL,
    `status` ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    `extra_data` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TLOS Settings
CREATE TABLE `tlos_settings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_type` VARCHAR(50) DEFAULT 'text',
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEFAULT DATA
-- ============================================

-- Default admin user (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `active`) VALUES
('Admin', 'admin@thelastofsaas.es', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'The Last of SaaS', 'general'),
('site_description', 'Eventos de networking B2B y SaaS', 'general'),
('default_language', 'es', 'general'),
('admin_email', 'admin@thelastofsaas.es', 'general');

-- Default TLOS settings
INSERT INTO `tlos_settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`) VALUES
('site_name', 'The Last of SaaS', 'text', 'general'),
('max_sponsor_selections', '10', 'number', 'matching'),
('max_company_selections', '5', 'number', 'matching'),
('notify_sponsors', '1', 'boolean', 'email'),
('notify_companies', '1', 'boolean', 'email'),
('allow_sponsor_messages', '1', 'boolean', 'matching'),
('auto_match_notification', '1', 'boolean', 'email'),
('default_meeting_duration', '15', 'number', 'meetings'),
('default_rooms_per_block', '10', 'number', 'meetings'),
('currency', 'eur', 'text', 'stripe');

-- Default homepage
INSERT INTO `pages` (`title`, `slug`, `content`, `status`, `is_homepage`, `template`) VALUES
('Inicio', 'home', '<h1>Bienvenido a The Last of SaaS</h1><p>Próximamente...</p>', 'published', 1, 'home');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- MIGRATION COMPLETE
-- ============================================
