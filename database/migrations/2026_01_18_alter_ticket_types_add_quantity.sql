-- Migration: Add quantity columns to ticket_types table
-- TLOS - The Last of SaaS
-- Note: Run each statement separately. Ignore errors if column already exists.

ALTER TABLE `ticket_types` ADD COLUMN `quantity_available` int(11) DEFAULT NULL;
ALTER TABLE `ticket_types` ADD COLUMN `quantity_sold` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `ticket_types` ADD COLUMN `sale_start_date` datetime DEFAULT NULL;
ALTER TABLE `ticket_types` ADD COLUMN `sale_end_date` datetime DEFAULT NULL;
ALTER TABLE `ticket_types` ADD COLUMN `requires_approval` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `ticket_types` ADD COLUMN `max_per_purchase` int(11) DEFAULT NULL;
