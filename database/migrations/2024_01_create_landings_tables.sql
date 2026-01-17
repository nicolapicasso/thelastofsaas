-- Landing Pages System Migration
-- Omniwallet CMS

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- Landing Themes (Temáticas)
-- ============================================
CREATE TABLE IF NOT EXISTS `landing_themes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `subtitle` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `icon` VARCHAR(100) NULL COMMENT 'FontAwesome icon class',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Landings (Páginas Landing individuales)
-- ============================================
CREATE TABLE IF NOT EXISTS `landings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `theme_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `subtitle` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `icon` VARCHAR(100) NULL COMMENT 'FontAwesome icon class',
    `html_content` LONGTEXT NULL COMMENT 'Full HTML content of the landing page',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `views` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_theme` (`theme_id`),
    INDEX `idx_slug` (`slug`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_sort` (`sort_order`),
    UNIQUE KEY `unique_theme_slug` (`theme_id`, `slug`),
    FOREIGN KEY (`theme_id`) REFERENCES `landing_themes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
