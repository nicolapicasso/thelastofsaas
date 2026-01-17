-- Migration: Add multi-language HTML content support for landings
-- This allows storing different HTML content per language

ALTER TABLE `landings`
ADD COLUMN `html_content_translations` JSON NULL AFTER `html_content`;

-- The field stores a JSON object like:
-- {
--   "en": "<html content for English>",
--   "it": "<html content for Italian>",
--   "fr": "<html content for French>",
--   "de": "<html content for German>"
-- }
-- The original html_content field remains as the Spanish/default version
