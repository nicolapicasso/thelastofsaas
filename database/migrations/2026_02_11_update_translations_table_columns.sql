-- Migration: Update translations table columns to match Translation model
-- Date: 2026-02-11
-- Description: Rename columns and add missing columns to translations table

-- Rename 'field' to 'field_name'
ALTER TABLE `translations`
    CHANGE COLUMN `field` `field_name` VARCHAR(100) NOT NULL;

-- Rename 'content' to 'translated_content'
ALTER TABLE `translations`
    CHANGE COLUMN `content` `translated_content` LONGTEXT NULL;

-- Add 'original_content' column
ALTER TABLE `translations`
    ADD COLUMN `original_content` LONGTEXT NULL AFTER `field_name`;

-- Add 'is_auto_translated' column
ALTER TABLE `translations`
    ADD COLUMN `is_auto_translated` TINYINT(1) DEFAULT 0 AFTER `translated_content`;

-- Update the unique key to use new column name
ALTER TABLE `translations`
    DROP INDEX `unique_translation`,
    ADD UNIQUE KEY `unique_translation` (`entity_type`, `entity_id`, `field_name`, `language`);
