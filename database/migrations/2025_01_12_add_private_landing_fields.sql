-- Add Private Landing Fields Migration
-- Omniwallet CMS
-- Adds ability to password-protect landings for client presentations
-- Also adds html_content_translations for multi-language support

SET NAMES utf8mb4;

-- Add html_content_translations column for multi-language HTML support
-- Run this first if the column doesn't exist
ALTER TABLE `landings`
    ADD COLUMN `html_content_translations` LONGTEXT NULL COMMENT 'JSON of HTML translations for other languages' AFTER `html_content`;

-- Add is_private field
ALTER TABLE `landings`
    ADD COLUMN `is_private` TINYINT(1) DEFAULT 0 COMMENT 'Private landing requires password to view' AFTER `is_featured`;

-- Add access_password field
ALTER TABLE `landings`
    ADD COLUMN `access_password` VARCHAR(255) NULL COMMENT 'Password hash for private landings' AFTER `is_private`;

-- Add index for private landings
CREATE INDEX `idx_private` ON `landings` (`is_private`);
