-- Migration: Create activities table
-- TLOS - The Last of SaaS

CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `speaker_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `activity_type` varchar(50) NOT NULL DEFAULT 'charla',
  `activity_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `max_attendees` int(11) DEFAULT NULL,
  `requires_registration` tinyint(1) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_activities_event` (`event_id`),
  KEY `idx_activities_room` (`room_id`),
  KEY `idx_activities_speaker` (`speaker_id`),
  KEY `idx_activities_category` (`category_id`),
  KEY `idx_activities_date` (`activity_date`),
  KEY `idx_activities_active` (`active`),
  KEY `idx_activities_event_date` (`event_id`, `activity_date`),
  CONSTRAINT `fk_activities_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_activities_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_activities_speaker` FOREIGN KEY (`speaker_id`) REFERENCES `team_members` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_activities_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity types reference:
-- 'charla' - Charla / Ponencia
-- 'mesa_redonda' - Mesa Redonda
-- 'taller' - Taller / Workshop
-- 'networking' - Networking
-- 'comida' - Comida / Almuerzo
-- 'cafe' - Cafe / Descanso
-- 'bienvenida' - Bienvenida
-- 'cierre' - Cierre / Clausura
-- 'registro' - Registro
-- 'otro' - Otro
