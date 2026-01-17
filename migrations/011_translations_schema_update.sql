-- Migration: Update translations table schema
-- Omniwallet CMS
--
-- Ajusta los nombres de columnas para que coincidan con el modelo Translation

-- Renombrar columnas
ALTER TABLE `translations`
    CHANGE COLUMN `field` `field_name` VARCHAR(100) NOT NULL,
    CHANGE COLUMN `content` `translated_content` LONGTEXT NULL;

-- Añadir columna faltante para guardar el contenido original
ALTER TABLE `translations`
    ADD COLUMN `original_content` LONGTEXT NULL AFTER `language`;
