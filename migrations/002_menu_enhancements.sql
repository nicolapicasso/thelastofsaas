-- Migration: Menu Enhancements and Site Settings
-- Adds support for button types, social links, and translations in menus
-- Also creates site_settings table for footer tagline and other global settings

-- ============================================
-- Enhance menu_items table
-- ============================================

-- Add item_type column
ALTER TABLE `menu_items`
ADD COLUMN `item_type` ENUM('link', 'button', 'social') DEFAULT 'link' AFTER `css_class`;

-- Add button_style column (for button type items)
ALTER TABLE `menu_items`
ADD COLUMN `button_style` ENUM('primary', 'outline') DEFAULT 'primary' AFTER `item_type`;

-- Add translations JSON column
ALTER TABLE `menu_items`
ADD COLUMN `translations` JSON NULL AFTER `button_style`;

-- ============================================
-- Enhance menus table location options
-- ============================================

-- Update location ENUM to include new locations
ALTER TABLE `menus`
MODIFY COLUMN `location` ENUM('header', 'header_buttons', 'footer', 'footer_social', 'sidebar', 'other') DEFAULT 'header';

-- ============================================
-- Create site_settings table
-- ============================================

CREATE TABLE IF NOT EXISTS `site_settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT NULL,
    `translations` JSON NULL COMMENT 'Translations for all languages: {"en": "...", "it": "...", etc}',
    `type` ENUM('text', 'textarea', 'html', 'json', 'boolean', 'number') DEFAULT 'text',
    `group` VARCHAR(50) DEFAULT 'general',
    `label` VARCHAR(255) NULL COMMENT 'Human-readable label',
    `description` VARCHAR(500) NULL COMMENT 'Help text for admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_key` (`key`),
    INDEX `idx_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert default site settings
-- ============================================

INSERT INTO `site_settings` (`key`, `value`, `translations`, `type`, `group`, `label`, `description`) VALUES
('footer_tagline', 'La plataforma de pagos y wallet digital que impulsa tu negocio.', '{"en": "The payment platform and digital wallet that powers your business.", "it": "La piattaforma di pagamento e wallet digitale che potenzia il tuo business.", "fr": "La plateforme de paiement et portefeuille numérique qui propulse votre entreprise.", "de": "Die Zahlungsplattform und digitale Wallet, die Ihr Unternehmen antreibt."}', 'textarea', 'footer', 'Tagline del Footer', 'Texto que aparece debajo del logo en el footer'),
('footer_copyright', '© {year} Omniwallet. Todos los derechos reservados.', '{"en": "© {year} Omniwallet. All rights reserved.", "it": "© {year} Omniwallet. Tutti i diritti riservati.", "fr": "© {year} Omniwallet. Tous droits réservés.", "de": "© {year} Omniwallet. Alle Rechte vorbehalten."}', 'text', 'footer', 'Copyright del Footer', 'Texto de copyright. Usa {year} para el año actual');

-- ============================================
-- Create default menus for header buttons and social links
-- ============================================

-- Header Buttons menu
INSERT INTO `menus` (`name`, `slug`, `location`, `description`, `is_active`) VALUES
('Botones Cabecera', 'header-buttons', 'header_buttons', 'Botones de acción en la cabecera (Login, Registro)', 1);

-- Get the ID of the just inserted menu
SET @header_buttons_menu_id = LAST_INSERT_ID();

-- Insert header button items
INSERT INTO `menu_items` (`menu_id`, `title`, `url`, `target`, `icon`, `item_type`, `button_style`, `sort_order`, `is_active`, `translations`) VALUES
(@header_buttons_menu_id, 'Acceder', '/login', '_self', NULL, 'button', 'outline', 0, 1, '{"en": "Login", "it": "Accedi", "fr": "Connexion", "de": "Anmelden"}'),
(@header_buttons_menu_id, 'Empezar Gratis', '/registro', '_self', NULL, 'button', 'primary', 1, 1, '{"en": "Start Free", "it": "Inizia Gratis", "fr": "Commencer Gratuit", "de": "Kostenlos Starten"}');

-- Footer Social Links menu
INSERT INTO `menus` (`name`, `slug`, `location`, `description`, `is_active`) VALUES
('Redes Sociales Footer', 'footer-social', 'footer_social', 'Enlaces a redes sociales en el footer', 1);

SET @footer_social_menu_id = LAST_INSERT_ID();

-- Insert social link items
INSERT INTO `menu_items` (`menu_id`, `title`, `url`, `target`, `icon`, `item_type`, `sort_order`, `is_active`) VALUES
(@footer_social_menu_id, 'Twitter', 'https://twitter.com/omniwallet', '_blank', 'fab fa-twitter', 'social', 0, 1),
(@footer_social_menu_id, 'LinkedIn', 'https://linkedin.com/company/omniwallet', '_blank', 'fab fa-linkedin-in', 'social', 1, 1),
(@footer_social_menu_id, 'Instagram', 'https://instagram.com/omniwallet', '_blank', 'fab fa-instagram', 'social', 2, 1);

-- ============================================
-- Add translations to existing main menu items
-- ============================================

-- Update existing menu items to have translations (if main menu exists)
-- This assumes the main header menu has typical navigation items
-- You may need to manually update translations for custom menu items

