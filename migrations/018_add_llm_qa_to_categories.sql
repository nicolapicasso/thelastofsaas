-- Add LLM Q&A columns to categories table
-- Migration 018: Add LLM Q&A fields for AI-generated content

ALTER TABLE categories ADD COLUMN IF NOT EXISTS enable_llm_qa TINYINT(1) DEFAULT 0 AFTER meta_description;
ALTER TABLE categories ADD COLUMN IF NOT EXISTS llm_qa_content JSON NULL AFTER enable_llm_qa;
