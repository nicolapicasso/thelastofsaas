-- Add invite_code_id column to tickets table
-- This links tickets to sponsor invite codes

ALTER TABLE `tickets`
ADD COLUMN `invite_code_id` INT DEFAULT NULL AFTER `sponsor_id`,
ADD INDEX `idx_invite_code_id` (`invite_code_id`);

-- Note: Foreign key to sponsor_invite_codes table
-- ALTER TABLE `tickets` ADD FOREIGN KEY (`invite_code_id`) REFERENCES `sponsor_invite_codes`(`id`) ON DELETE SET NULL;
