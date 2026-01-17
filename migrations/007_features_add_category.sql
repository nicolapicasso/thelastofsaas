-- Migration 007: Add category column to features table
-- Run this migration to enable category functionality

-- Add category column if it doesn't exist
ALTER TABLE `features` ADD COLUMN IF NOT EXISTS `category` VARCHAR(100) NULL AFTER `feature_group`;

-- Add index for category
ALTER TABLE `features` ADD INDEX IF NOT EXISTS `idx_category` (`category`);
