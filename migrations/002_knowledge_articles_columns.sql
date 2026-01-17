-- Migration: Add missing columns to knowledge_articles table
-- This aligns the table with the admin interface expectations

-- Add category_id column (references categories table, shared with blog)
ALTER TABLE `knowledge_articles`
ADD COLUMN `category_id` INT UNSIGNED NULL AFTER `topic_id`,
ADD INDEX `idx_category` (`category_id`),
ADD CONSTRAINT `fk_knowledge_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL;

-- Add tags column for keyword tagging
ALTER TABLE `knowledge_articles`
ADD COLUMN `tags` VARCHAR(500) NULL AFTER `search_keywords`;

-- Add sort_order column for manual ordering
ALTER TABLE `knowledge_articles`
ADD COLUMN `sort_order` INT DEFAULT 0 AFTER `is_featured`,
ADD INDEX `idx_sort_order` (`sort_order`);

-- Add view_count column (the schema uses 'views' but code expects 'view_count')
ALTER TABLE `knowledge_articles`
ADD COLUMN `view_count` INT UNSIGNED DEFAULT 0 AFTER `views`;

-- Copy existing views data to view_count
UPDATE `knowledge_articles` SET `view_count` = `views` WHERE `views` > 0;
