-- Migration: Add photo_animated column to team_members table
-- TLOS - The Last of SaaS
-- This column stores the animated GIF URL for hover effect

ALTER TABLE `team_members`
ADD COLUMN `photo_animated` VARCHAR(500) NULL COMMENT 'Animated photo (GIF) for hover effect'
AFTER `photo`;
