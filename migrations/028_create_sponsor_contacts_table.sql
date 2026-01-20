-- Migration: Create sponsor_contacts table for multiple contacts per sponsor
-- TLOS - The Last of SaaS

CREATE TABLE IF NOT EXISTS `sponsor_contacts` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `sponsor_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `position` VARCHAR(255) NULL COMMENT 'Job title/position',
    `email` VARCHAR(255) NULL,
    `phone` VARCHAR(50) NULL,
    `is_primary` TINYINT(1) DEFAULT 0 COMMENT 'Primary contact flag',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_sponsor_contacts_sponsor` (`sponsor_id`),
    INDEX `idx_sponsor_contacts_primary` (`sponsor_id`, `is_primary`),
    CONSTRAINT `fk_sponsor_contacts_sponsor` FOREIGN KEY (`sponsor_id`)
        REFERENCES `sponsors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrate existing contact data from sponsors table to sponsor_contacts
INSERT INTO `sponsor_contacts` (`sponsor_id`, `name`, `email`, `phone`, `is_primary`)
SELECT
    `id` as sponsor_id,
    COALESCE(`contact_name`, 'Contacto') as name,
    `contact_email` as email,
    `contact_phone` as phone,
    1 as is_primary
FROM `sponsors`
WHERE `contact_name` IS NOT NULL OR `contact_email` IS NOT NULL;
