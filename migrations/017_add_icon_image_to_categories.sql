-- Migration: Add icon_image column to categories table
-- We're Sinapsis CMS
-- Date: 2026-01-08

-- Add icon_image column for animated GIF icons in category hero
ALTER TABLE categories ADD COLUMN IF NOT EXISTS icon_image VARCHAR(500) NULL AFTER featured_image;
