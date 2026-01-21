-- ============================================
-- TLOS - The Last of SaaS
-- Migration: Create sponsor_invite_codes table
-- Date: 2026-01-19
-- ============================================

-- Sponsor Invite Codes table
-- Allows sponsors to generate unique codes that attendees can use during registration
-- Tracks usage and links tickets to the sponsor who invited them

CREATE TABLE IF NOT EXISTS `sponsor_invite_codes` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `sponsor_id` INT NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255),
    `max_uses` INT DEFAULT NULL COMMENT 'NULL = unlimited uses',
    `times_used` INT DEFAULT 0,
    `ticket_type_id` INT DEFAULT NULL COMMENT 'Specific ticket type or NULL for any',
    `discount_type` ENUM('none', 'percentage', 'fixed') DEFAULT 'none',
    `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
    `valid_from` DATETIME DEFAULT NULL,
    `valid_until` DATETIME DEFAULT NULL,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_code_event` (`event_id`, `code`),
    INDEX `idx_sponsor_id` (`sponsor_id`),
    INDEX `idx_event_id` (`event_id`),
    INDEX `idx_code` (`code`),
    INDEX `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add invite_code_id column to tickets table for tracking
ALTER TABLE `tickets`
ADD COLUMN `invite_code_id` INT DEFAULT NULL AFTER `sponsor_id`,
ADD INDEX `idx_invite_code_id` (`invite_code_id`);

-- ============================================
-- END MIGRATION
-- ============================================
