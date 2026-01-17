-- Migration: Add missing LLM Q&A columns
-- Run this script to fix "Unknown column 'enable_llm_qa'" errors
-- If the columns already exist, you'll get a harmless warning

-- Add columns to pages table
ALTER TABLE `pages` ADD COLUMN `enable_llm_qa` TINYINT(1) DEFAULT 0;
ALTER TABLE `pages` ADD COLUMN `llm_qa_content` TEXT NULL;

-- Add columns to success_cases table
ALTER TABLE `success_cases` ADD COLUMN `enable_llm_qa` TINYINT(1) DEFAULT 0;
ALTER TABLE `success_cases` ADD COLUMN `llm_qa_content` TEXT NULL;

-- Add columns to posts table
ALTER TABLE `posts` ADD COLUMN `enable_llm_qa` TINYINT(1) DEFAULT 0;
ALTER TABLE `posts` ADD COLUMN `llm_qa_content` TEXT NULL;

-- Add columns to services table
ALTER TABLE `services` ADD COLUMN `enable_llm_qa` TINYINT(1) DEFAULT 0;
ALTER TABLE `services` ADD COLUMN `llm_qa_content` TEXT NULL;

-- Add columns to tools table
ALTER TABLE `tools` ADD COLUMN `enable_llm_qa` TINYINT(1) DEFAULT 0;
ALTER TABLE `tools` ADD COLUMN `llm_qa_content` TEXT NULL;
