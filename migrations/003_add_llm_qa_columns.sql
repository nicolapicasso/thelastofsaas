-- Migration: Add LLM Q&A columns to multiple tables
-- Run each statement separately

-- Features table
ALTER TABLE `features` ADD COLUMN `enable_llm_qa` TINYINT(1) DEFAULT 0;
ALTER TABLE `features` ADD COLUMN `llm_qa_content` TEXT NULL;

-- Integrations table
ALTER TABLE `integrations` ADD COLUMN `enable_llm_qa` TINYINT(1) DEFAULT 0;
ALTER TABLE `integrations` ADD COLUMN `llm_qa_content` TEXT NULL;

-- Success Cases table
ALTER TABLE `success_cases` ADD COLUMN `enable_llm_qa` TINYINT(1) DEFAULT 0;
ALTER TABLE `success_cases` ADD COLUMN `llm_qa_content` TEXT NULL;

-- Knowledge Articles table
ALTER TABLE `knowledge_articles` ADD COLUMN `enable_llm_qa` TINYINT(1) DEFAULT 0;
ALTER TABLE `knowledge_articles` ADD COLUMN `llm_qa_content` TEXT NULL;
