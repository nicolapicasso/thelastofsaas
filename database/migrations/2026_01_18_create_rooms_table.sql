-- Migration: Create rooms table
-- TLOS - The Last of SaaS

CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `floor` varchar(100) DEFAULT NULL,
  `equipment` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `color` varchar(20) DEFAULT '#3B82F6',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_rooms_active` (`active`),
  KEY `idx_rooms_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some default rooms
INSERT INTO `rooms` (`name`, `slug`, `description`, `capacity`, `color`, `sort_order`) VALUES
('Sala Principal', 'sala-principal', 'Sala principal para charlas y presentaciones', 200, '#3B82F6', 1),
('Sala A', 'sala-a', 'Sala de reuniones A', 20, '#10B981', 2),
('Sala B', 'sala-b', 'Sala de reuniones B', 20, '#F59E0B', 3),
('Networking', 'networking', 'Zona de networking y cafe', 100, '#8B5CF6', 4);
