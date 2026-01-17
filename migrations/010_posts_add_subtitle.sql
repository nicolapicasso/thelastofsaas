-- Migration: Add missing columns to posts table
-- Date: 2025-12-21
-- Description: Adds subtitle, video_url, video_thumbnail and other missing columns

-- Run each ALTER separately to avoid errors if some columns already exist
-- If a column already exists, just skip that line

ALTER TABLE `posts` ADD COLUMN `subtitle` VARCHAR(255) NULL AFTER `title`;
ALTER TABLE `posts` ADD COLUMN `video_url` VARCHAR(500) NULL AFTER `gallery`;
ALTER TABLE `posts` ADD COLUMN `video_thumbnail` VARCHAR(500) NULL AFTER `video_url`;
ALTER TABLE `posts` ADD COLUMN `enable_llm_qa` TINYINT(1) DEFAULT 0 AFTER `meta_description`;
ALTER TABLE `posts` ADD COLUMN `llm_qa_content` TEXT NULL AFTER `enable_llm_qa`;
