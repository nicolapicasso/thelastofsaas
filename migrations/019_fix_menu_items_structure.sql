-- Fix menu_items table structure
-- Migration 019: Ensure menu_items has all required columns

-- Add menu_id column if it doesn't exist (the model uses menu_id, not menu_location)
ALTER TABLE `menu_items`
ADD COLUMN IF NOT EXISTS `menu_id` INT UNSIGNED NULL AFTER `id`;

-- Add item_type column if missing
ALTER TABLE `menu_items`
ADD COLUMN IF NOT EXISTS `item_type` ENUM('link', 'button', 'social') DEFAULT 'link' AFTER `css_class`;

-- Add button_style column if missing
ALTER TABLE `menu_items`
ADD COLUMN IF NOT EXISTS `button_style` ENUM('primary', 'outline') DEFAULT 'primary' AFTER `item_type`;

-- Add translations column if missing
ALTER TABLE `menu_items`
ADD COLUMN IF NOT EXISTS `translations` JSON NULL AFTER `button_style`;

-- If menu_id is NULL but menu_location exists, try to populate menu_id from menus table
UPDATE `menu_items` mi
INNER JOIN `menus` m ON m.location = mi.menu_location
SET mi.menu_id = m.id
WHERE mi.menu_id IS NULL AND mi.menu_location IS NOT NULL;
