-- Migration 027: Bidirectional Messaging System
-- Replaces the unidirectional sponsor_messages table with a flexible messaging system
-- that supports:
-- - Bidirectional messages (sponsor <-> company)
-- - Conversations/replies
-- - Per-event messaging
-- - Sender contact details

-- Create new messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,

    -- Sender info (can be sponsor or company)
    sender_type ENUM('sponsor', 'company') NOT NULL,
    sender_id INT NOT NULL,

    -- Recipient info (can be sponsor or company)
    recipient_type ENUM('sponsor', 'company') NOT NULL,
    recipient_id INT NOT NULL,

    -- Reply chain (NULL for initial message, parent_id for replies)
    parent_message_id INT NULL,

    -- Message content
    message TEXT NOT NULL,

    -- Sender contact details (personal contact info)
    sender_name VARCHAR(255) NULL,
    sender_email VARCHAR(255) NULL,
    sender_phone VARCHAR(50) NULL,

    -- Timestamps
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME NULL,

    -- Foreign keys
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_message_id) REFERENCES messages(id) ON DELETE SET NULL,

    -- Indexes for common queries
    INDEX idx_event_sender (event_id, sender_type, sender_id),
    INDEX idx_event_recipient (event_id, recipient_type, recipient_id),
    INDEX idx_parent_message (parent_message_id),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrate existing data from sponsor_messages to messages
-- Note: sponsor_messages uses 'created_at' column for timestamp
INSERT INTO messages (event_id, sender_type, sender_id, recipient_type, recipient_id, message, sent_at, read_at)
SELECT
    event_id,
    'sponsor' as sender_type,
    sponsor_id as sender_id,
    'company' as recipient_type,
    company_id as recipient_id,
    message,
    COALESCE(created_at, NOW()) as sent_at,
    read_at
FROM sponsor_messages;

-- Drop old table (optional - commented out for safety)
-- DROP TABLE IF EXISTS sponsor_messages;
