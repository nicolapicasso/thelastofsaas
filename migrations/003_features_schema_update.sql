-- Migration 003: Update features table schema
-- Adds new columns for enhanced feature management
-- Compatible with MySQL 5.7+

-- Add short_description column
ALTER TABLE `features` ADD COLUMN `short_description` TEXT NULL AFTER `title`;

-- Add full_description column
ALTER TABLE `features` ADD COLUMN `full_description` LONGTEXT NULL AFTER `short_description`;

-- Add icon_svg column
ALTER TABLE `features` ADD COLUMN `icon_svg` TEXT NULL AFTER `full_description`;

-- Add icon_class column
ALTER TABLE `features` ADD COLUMN `icon_class` VARCHAR(100) NULL AFTER `icon_svg`;

-- Add video_url column
ALTER TABLE `features` ADD COLUMN `video_url` VARCHAR(255) NULL AFTER `image`;

-- Add feature_group column
ALTER TABLE `features` ADD COLUMN `feature_group` VARCHAR(50) NULL AFTER `video_url`;

-- Migrate existing data from old columns to new columns
UPDATE `features` SET `short_description` = `description` WHERE `short_description` IS NULL AND `description` IS NOT NULL;
UPDATE `features` SET `icon_class` = `icon` WHERE `icon_class` IS NULL AND `icon` IS NOT NULL;
UPDATE `features` SET `feature_group` = `group_name` WHERE `feature_group` IS NULL AND `group_name` IS NOT NULL;

-- Add index for feature_group (ignore error if exists)
-- ALTER TABLE `features` ADD INDEX `idx_feature_group` (`feature_group`);
