-- Migration: Fix team_members table schema
-- TLOS - The Last of SaaS
--
-- INSTRUCCIONES: Ejecuta cada sentencia por separado.
-- Si alguna da error de "Duplicate column", ignórala y pasa a la siguiente.

-- 1. Primero, asegurarse que los miembros existentes estén activos
-- (si la columna active existe)
UPDATE `team_members` SET `active` = 1 WHERE `active` IS NULL;

-- 2. Si no tienes la columna active, descomenta y ejecuta esta línea:
-- ALTER TABLE `team_members` ADD COLUMN `active` TINYINT(1) DEFAULT 1;

-- 3. Si no tienes la columna sort_order, descomenta y ejecuta esta línea:
-- ALTER TABLE `team_members` ADD COLUMN `sort_order` INT DEFAULT 0;

-- 4. Si tienes 'description' pero no 'bio', ejecuta esta línea:
-- ALTER TABLE `team_members` CHANGE `description` `bio` TEXT NULL;

-- 5. Si no tienes photo_animated (ya deberías haberla añadido):
-- ALTER TABLE `team_members` ADD COLUMN `photo_animated` VARCHAR(500) NULL AFTER `photo`;

-- SOLUCIÓN RÁPIDA: Si simplemente los speakers no aparecen, ejecuta esto:
UPDATE `team_members` SET `active` = 1;
