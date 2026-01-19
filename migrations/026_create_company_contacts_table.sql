-- Migration: Create company_contacts table for multiple contacts per company
-- TLOS - The Last of SaaS

CREATE TABLE IF NOT EXISTS `company_contacts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `position` VARCHAR(255) NULL COMMENT 'Job title/position',
    `email` VARCHAR(255) NULL,
    `phone` VARCHAR(50) NULL,
    `is_primary` TINYINT(1) DEFAULT 0 COMMENT 'Primary contact flag',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_company_contacts_company` (`company_id`),
    INDEX `idx_company_contacts_primary` (`company_id`, `is_primary`),
    CONSTRAINT `fk_company_contacts_company` FOREIGN KEY (`company_id`)
        REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrate existing contact data from companies table to company_contacts
INSERT INTO `company_contacts` (`company_id`, `name`, `position`, `email`, `phone`, `is_primary`)
SELECT
    `id` as company_id,
    COALESCE(`contact_name`, 'Contacto') as name,
    `contact_position` as position,
    `contact_email` as email,
    `contact_phone` as phone,
    1 as is_primary
FROM `companies`
WHERE `contact_name` IS NOT NULL OR `contact_email` IS NOT NULL;
