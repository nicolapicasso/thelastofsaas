-- Migration: Create Partners Table
-- Date: 2024-12-27
-- Description: Creates the partners directory table for Omniwallet CMS

CREATE TABLE IF NOT EXISTS `partners` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `website` VARCHAR(500) NULL,
    `email` VARCHAR(255) NULL,
    `phone` VARCHAR(50) NULL,
    `linkedin` VARCHAR(500) NULL,
    `logo` VARCHAR(255) NULL,
    `featured_image` VARCHAR(255) NULL,
    `country` VARCHAR(100) NULL,
    `city` VARCHAR(100) NULL,
    `description` TEXT NULL,
    `is_certified` TINYINT(1) DEFAULT 0 COMMENT 'Has Omniwallet certification',
    `partner_type` ENUM('agency', 'tech_partner') DEFAULT 'agency',
    `testimonial` TEXT NULL,
    `testimonial_author` VARCHAR(100) NULL,
    `testimonial_role` VARCHAR(100) NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_country` (`country`),
    INDEX `idx_city` (`city`),
    INDEX `idx_partner_type` (`partner_type`),
    INDEX `idx_certified` (`is_certified`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_featured` (`is_featured`),
    FULLTEXT INDEX `idx_search` (`name`, `description`, `city`, `country`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
