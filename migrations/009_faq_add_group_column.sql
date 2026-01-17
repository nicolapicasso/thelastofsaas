-- Migration: Add faq_group column to faqs table
-- Date: 2025-12-21
-- Description: Adds faq_group column for grouping FAQs

-- Check if column exists before adding (MySQL doesn't support IF NOT EXISTS for columns)
-- Run this manually or handle the error if column already exists

ALTER TABLE `faqs` ADD COLUMN `faq_group` VARCHAR(50) NULL AFTER `category_id`;
ALTER TABLE `faqs` ADD INDEX `idx_faq_group` (`faq_group`);
