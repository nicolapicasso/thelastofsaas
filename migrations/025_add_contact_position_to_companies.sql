-- Migration: Add contact_position column to companies table
-- TLOS - The Last of SaaS

ALTER TABLE `companies`
ADD COLUMN `contact_position` VARCHAR(255) NULL COMMENT 'Contact person job title/position'
AFTER `contact_name`;
