-- Migration: Create team_members table
-- Run this SQL in phpMyAdmin

CREATE TABLE IF NOT EXISTS `team_members` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `position` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `photo` VARCHAR(500) NULL,
    `photo_hover` VARCHAR(500) NULL COMMENT 'Can be animated GIF',
    `email` VARCHAR(255) NULL,
    `linkedin_url` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
