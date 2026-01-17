-- Contact Submissions Table
-- Stores form submissions from contact form blocks
-- Omniwallet CMS

CREATE TABLE IF NOT EXISTS `contact_submissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `block_id` INT UNSIGNED NOT NULL,
    `data` JSON NOT NULL COMMENT 'Form field values as JSON',
    `ip_address` VARCHAR(45) NULL COMMENT 'IPv4 or IPv6 address',
    `user_agent` VARCHAR(500) NULL,
    `page_url` VARCHAR(500) NULL COMMENT 'Page where form was submitted',
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_block_id` (`block_id`),
    INDEX `idx_is_read` (`is_read`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
