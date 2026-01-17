-- ============================================
-- Chatbot Conversation Logs
-- Migration: 008_chatbot_logs.sql
-- Description: Store chatbot conversations for analytics
-- ============================================

CREATE TABLE IF NOT EXISTS `chatbot_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(100) NULL,
    `user_message` TEXT NOT NULL,
    `bot_response` TEXT NOT NULL,
    `articles_used` JSON NULL COMMENT 'IDs of knowledge articles used for context',
    `response_time_ms` INT UNSIGNED NULL COMMENT 'API response time in milliseconds',
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_session` (`session_id`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add helpful rating to conversations (for future use)
ALTER TABLE `chatbot_logs`
ADD COLUMN `rating` ENUM('helpful', 'not_helpful') NULL AFTER `user_agent`;
