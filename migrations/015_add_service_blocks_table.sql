-- Migration: Add service_blocks table for block editor in services
-- We're Sinapsis CMS

CREATE TABLE IF NOT EXISTS `service_blocks` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `service_id` INT UNSIGNED NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `sort_order` INT DEFAULT 0,
    `content` LONGTEXT,
    `settings` LONGTEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX `idx_service` (`service_id`),
    INDEX `idx_type` (`type`),
    INDEX `idx_sort` (`sort_order`),

    CONSTRAINT `fk_service_blocks_service`
        FOREIGN KEY (`service_id`)
        REFERENCES `services`(`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
