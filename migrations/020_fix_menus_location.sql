-- Fix menus location ENUM to include all required values
-- Migration 020: Update menus location ENUM

ALTER TABLE `menus`
MODIFY COLUMN `location` VARCHAR(50) NOT NULL DEFAULT 'header';
