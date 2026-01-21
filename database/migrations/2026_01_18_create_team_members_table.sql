-- Create team_members table
-- TLOS - The Last of SaaS

CREATE TABLE IF NOT EXISTS `team_members` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NULL,
    `position` VARCHAR(255) NULL COMMENT 'Job title',
    `company` VARCHAR(255) NULL COMMENT 'Company name',
    `bio` TEXT NULL COMMENT 'Biography',
    `photo` VARCHAR(500) NULL COMMENT 'Photo URL',
    `email` VARCHAR(255) NULL,
    `phone` VARCHAR(50) NULL,
    `linkedin_url` VARCHAR(500) NULL,
    `twitter_url` VARCHAR(500) NULL,
    `website_url` VARCHAR(500) NULL,
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_team_members_active` (`active`),
    INDEX `idx_team_members_sort` (`sort_order`),
    UNIQUE INDEX `idx_team_members_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
