-- Migration: Add event_id to rooms table
-- TLOS - The Last of SaaS
-- Allows rooms to be associated with specific events

-- Add event_id column to rooms table
ALTER TABLE `rooms` ADD COLUMN `event_id` INT NULL AFTER `id`;

-- Add foreign key constraint
ALTER TABLE `rooms` ADD CONSTRAINT `fk_rooms_event`
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE SET NULL;

-- Add index for better query performance
ALTER TABLE `rooms` ADD INDEX `idx_rooms_event` (`event_id`);
