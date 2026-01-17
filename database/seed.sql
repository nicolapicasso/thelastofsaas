-- We're Sinapsis CMS Seed Data
-- Sample data for development

SET NAMES utf8mb4;

-- ============================================
-- Admin User (password: admin123)
-- ============================================
INSERT INTO `users` (`email`, `password`, `name`, `role`, `is_active`) VALUES
('admin@weresinapsis.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin', 1);

-- ============================================
-- Settings
-- ============================================
INSERT INTO `settings` (`key`, `value`, `type`, `group`) VALUES
('site_name', "We're Sinapsis", 'string', 'general'),
('site_tagline', 'Agencia de diseño web y marketing digital', 'string', 'general'),
('site_email', 'info@weresinapsis.com', 'string', 'general'),
('site_phone', '+34 600 000 000', 'string', 'general'),
('primary_color', '#6366F1', 'string', 'branding'),
('secondary_color', '#1E1B4B', 'string', 'branding'),
('accent_color', '#A855F7', 'string', 'branding'),
('default_language', 'es', 'string', 'localization'),
('available_languages', '["es","en"]', 'json', 'localization'),
('google_analytics_id', '', 'string', 'analytics'),
('floating_form_enabled', '1', 'boolean', 'forms'),
('floating_form_title', 'Hablemos de tu proyecto', 'string', 'forms'),
('floating_form_fields', '[{"name":"name","label":"Nombre","type":"text","required":true},{"name":"email","label":"Email","type":"email","required":true},{"name":"phone","label":"Teléfono","type":"tel","required":false},{"name":"company","label":"Empresa","type":"text","required":false},{"name":"message","label":"Mensaje","type":"textarea","required":true}]', 'json', 'forms'),
('floating_form_button_text', 'Contactar', 'string', 'forms'),
('floating_form_success_message', '¡Gracias! Nos pondremos en contacto contigo pronto.', 'string', 'forms');

-- ============================================
-- Categories (Shared)
-- ============================================
INSERT INTO `categories` (`name`, `slug`, `description`, `color`, `icon`, `sort_order`) VALUES
('Tecnología', 'tecnologia', 'Proyectos y artículos relacionados con tecnología, desarrollo de software y soluciones digitales.', '#3B82F6', 'fa-microchip', 1),
('E-commerce', 'ecommerce', 'Tiendas online, plataformas de venta y soluciones de comercio electrónico.', '#10B981', 'fa-shopping-cart', 2),
('Marketing Digital', 'marketing-digital', 'Estrategias de marketing online, SEO, SEM, redes sociales y publicidad digital.', '#F59E0B', 'fa-bullhorn', 3),
('Diseño', 'diseno', 'Diseño gráfico, branding, identidad visual y experiencia de usuario.', '#EC4899', 'fa-palette', 4),
('Startups', 'startups', 'Proyectos para startups, MVPs y productos digitales innovadores.', '#8B5CF6', 'fa-rocket', 5),
('Corporativo', 'corporativo', 'Soluciones para empresas, intranets y herramientas corporativas.', '#6B7280', 'fa-building', 6);

-- ============================================
-- Services
-- ============================================
INSERT INTO `services` (`title`, `slug`, `short_description`, `full_description`, `icon_class`, `category_id`, `sort_order`, `is_active`, `is_featured`) VALUES
('Diseño Web', 'diseno-web', 'Creamos sitios web modernos, responsivos y optimizados para convertir visitantes en clientes.', '<p>Diseñamos sitios web que no solo se ven increíbles, sino que también funcionan perfectamente. Cada proyecto está pensado para ofrecer la mejor experiencia de usuario y maximizar las conversiones.</p><h3>Lo que incluye</h3><ul><li>Diseño UI/UX personalizado</li><li>Desarrollo responsive</li><li>Optimización de velocidad</li><li>SEO técnico básico</li></ul>', 'fa-laptop-code', 4, 1, 1, 1),
('Desarrollo a Medida', 'desarrollo-a-medida', 'Soluciones tecnológicas personalizadas para las necesidades específicas de tu negocio.', '<p>Desarrollamos aplicaciones web y soluciones a medida que se adaptan perfectamente a los procesos de tu empresa. Utilizamos las últimas tecnologías para crear productos robustos y escalables.</p>', 'fa-code', 1, 2, 1, 1),
('Marketing Digital', 'marketing-digital', 'Estrategias de marketing online para aumentar tu visibilidad y ventas.', '<p>Creamos estrategias de marketing digital integrales que incluyen SEO, SEM, redes sociales y email marketing. Nuestro objetivo es hacer crecer tu negocio de forma sostenible.</p>', 'fa-bullhorn', 3, 3, 1, 1),
('SEO y Posicionamiento', 'seo-posicionamiento', 'Mejora tu posición en buscadores y atrae tráfico cualificado a tu web.', '<p>Optimizamos tu sitio web para los motores de búsqueda, mejorando tu visibilidad orgánica y atrayendo visitantes cualificados que se convierten en clientes.</p>', 'fa-search', 3, 4, 1, 0),
('Branding', 'branding', 'Diseño de identidad visual que refleja los valores de tu marca.', '<p>Creamos identidades visuales memorables que conectan con tu audiencia. Desde el logo hasta el manual de marca completo.</p>', 'fa-palette', 4, 5, 1, 0),
('E-commerce', 'ecommerce', 'Tiendas online profesionales que impulsan tus ventas.', '<p>Desarrollamos tiendas online optimizadas para la conversión, con pasarelas de pago seguras y una experiencia de compra fluida.</p>', 'fa-shopping-cart', 2, 6, 1, 1);

-- ============================================
-- Tools
-- ============================================
INSERT INTO `tools` (`title`, `slug`, `subtitle`, `description`, `logo`, `platform_url`, `category_id`, `is_featured`, `is_active`, `sort_order`) VALUES
('WordPress', 'wordpress', 'CMS más popular del mundo', 'Sistema de gestión de contenidos versátil y potente, ideal para blogs, webs corporativas y tiendas online.', NULL, 'https://wordpress.org', 1, 1, 1, 1),
('WooCommerce', 'woocommerce', 'E-commerce para WordPress', 'Plugin de comercio electrónico que transforma WordPress en una tienda online completa.', NULL, 'https://woocommerce.com', 2, 1, 1, 2),
('Figma', 'figma', 'Diseño colaborativo', 'Herramienta de diseño de interfaces y prototipos colaborativa basada en la nube.', NULL, 'https://figma.com', 4, 1, 1, 3),
('Google Analytics', 'google-analytics', 'Analítica web', 'Plataforma de análisis web que permite medir el tráfico y comportamiento de usuarios.', NULL, 'https://analytics.google.com', 3, 0, 1, 4),
('Mailchimp', 'mailchimp', 'Email marketing', 'Plataforma de automatización de marketing y email marketing.', NULL, 'https://mailchimp.com', 3, 0, 1, 5),
('Shopify', 'shopify', 'Plataforma e-commerce', 'Plataforma de comercio electrónico todo en uno para crear tiendas online.', NULL, 'https://shopify.com', 2, 1, 1, 6),
('Laravel', 'laravel', 'Framework PHP', 'Framework PHP elegante para desarrollo web con arquitectura MVC.', NULL, 'https://laravel.com', 1, 0, 1, 7),
('React', 'react', 'Librería JavaScript', 'Librería JavaScript para construir interfaces de usuario interactivas.', NULL, 'https://react.dev', 1, 1, 1, 8);

-- ============================================
-- Clients
-- ============================================
INSERT INTO `clients` (`name`, `slug`, `description`, `website`, `industry`, `company_size`, `location`, `is_featured`, `is_active`, `sort_order`) VALUES
('TechStart Solutions', 'techstart-solutions', 'Startup tecnológica especializada en soluciones SaaS para pymes. Fundada en 2020, ha crecido rápidamente en el mercado español.', 'https://techstart.example.com', 'Tecnología', '11-50', 'Madrid, España', 1, 1, 1),
('Moda Bella', 'moda-bella', 'Tienda de moda online con más de 10 años de experiencia en el sector. Referente en moda sostenible.', 'https://modabella.example.com', 'Moda y Retail', '51-200', 'Barcelona, España', 1, 1, 2),
('Grupo Inmobiliario Costa', 'grupo-inmobiliario-costa', 'Promotora inmobiliaria con presencia en toda la costa mediterránea española.', 'https://grupocosta.example.com', 'Inmobiliaria', '51-200', 'Valencia, España', 0, 1, 3),
('FoodTech Delivery', 'foodtech-delivery', 'Plataforma de delivery de comida saludable con servicio en las principales ciudades españolas.', 'https://foodtech.example.com', 'Alimentación', '11-50', 'Madrid, España', 1, 1, 4);

-- ============================================
-- Success Cases
-- ============================================
INSERT INTO `success_cases` (`title`, `slug`, `client_id`, `category_id`, `challenge`, `solution`, `results`, `metrics`, `testimonial`, `testimonial_author`, `testimonial_role`, `gallery_display`, `is_featured`, `status`, `published_at`) VALUES
('Rediseño completo de plataforma SaaS', 'techstart-rediseno-plataforma', 1, 1,
'TechStart necesitaba renovar completamente su plataforma SaaS que había quedado obsoleta. La interfaz era confusa, el rendimiento era lento y estaban perdiendo clientes frente a la competencia.',
'Realizamos un rediseño completo de la plataforma utilizando React para el frontend y optimizando el backend existente. Implementamos un nuevo sistema de diseño, mejoramos la arquitectura de la información y optimizamos el rendimiento.',
'La nueva plataforma ha conseguido reducir el churn rate en un 40%, aumentar el tiempo de permanencia de usuarios en un 60% y mejorar significativamente las valoraciones en reviews.',
'[{"value": "-40%", "label": "Churn rate"}, {"value": "+60%", "label": "Tiempo en plataforma"}, {"value": "4.8/5", "label": "Valoración usuarios"}]',
'El equipo de Sinapsis entendió perfectamente nuestras necesidades y transformó nuestra plataforma en algo que nuestros usuarios adoran. El proceso fue transparente y el resultado superó nuestras expectativas.',
'Carlos Martínez', 'CEO & Co-founder',
'grid', 1, 'published', NOW()),

('Tienda online de moda sostenible', 'moda-bella-ecommerce', 2, 2,
'Moda Bella tenía una tienda online anticuada con un proceso de compra complicado que resultaba en un alto porcentaje de carritos abandonados. Necesitaban modernizar su presencia digital.',
'Desarrollamos una nueva tienda online con WooCommerce, diseño responsive optimizado para móvil, proceso de checkout simplificado, integración con sistemas de pago modernos y una experiencia de usuario enfocada en la conversión.',
'Las ventas online aumentaron un 150% en los primeros 6 meses. El porcentaje de carritos abandonados se redujo del 75% al 35% y el ticket medio aumentó un 25%.',
'[{"value": "+150%", "label": "Ventas online"}, {"value": "-40%", "label": "Carritos abandonados"}, {"value": "+25%", "label": "Ticket medio"}]',
'Nuestra nueva tienda online es exactamente lo que necesitábamos. El equipo de Sinapsis nos guió en todo el proceso y el resultado ha sido espectacular para nuestro negocio.',
'Ana García', 'Directora de E-commerce',
'carousel', 1, 'published', NOW()),

('Portal inmobiliario con búsqueda avanzada', 'grupo-costa-portal-inmobiliario', 3, 6,
'El Grupo Inmobiliario Costa necesitaba un portal web que permitiera a los usuarios buscar propiedades de forma intuitiva y que se integrara con su CRM interno.',
'Creamos un portal inmobiliario a medida con búsqueda avanzada por ubicación, características y precio. Integramos el sistema con su CRM existente y añadimos funcionalidades como comparador de propiedades y alertas personalizadas.',
'El portal generó un 200% más de leads cualificados y redujo el tiempo de gestión comercial en un 30% gracias a la integración con el CRM.',
'[{"value": "+200%", "label": "Leads cualificados"}, {"value": "-30%", "label": "Tiempo de gestión"}, {"value": "50K", "label": "Visitas mensuales"}]',
'El portal que desarrollaron ha transformado nuestra forma de captar clientes. La integración con nuestro CRM nos ahorra horas de trabajo cada semana.',
'Roberto Sánchez', 'Director Comercial',
'grid', 0, 'published', NOW()),

('App de delivery con sistema de suscripción', 'foodtech-app-delivery', 4, 5,
'FoodTech Delivery quería lanzar un modelo de suscripción para fidelizar clientes, pero su app actual no soportaba esta funcionalidad y la experiencia de usuario era deficiente.',
'Rediseñamos la app móvil completa, implementamos un sistema de suscripciones con diferentes planes, añadimos gamificación para aumentar el engagement y optimizamos todo el flujo de pedidos.',
'Los suscriptores aumentaron un 300% en 3 meses, el LTV de clientes subió un 180% y las valoraciones en app stores pasaron de 3.2 a 4.7 estrellas.',
'[{"value": "+300%", "label": "Suscriptores"}, {"value": "+180%", "label": "Customer LTV"}, {"value": "4.7★", "label": "Rating en stores"}]',
'Sinapsis transformó nuestra app y nuestro modelo de negocio. El sistema de suscripciones que implementaron ha sido clave para nuestro crecimiento.',
'Laura Fernández', 'CPO',
'carousel', 1, 'published', NOW());

-- ============================================
-- Service-Case Relations
-- ============================================
INSERT INTO `service_cases` (`service_id`, `case_id`, `sort_order`) VALUES
(1, 1, 1), -- Diseño Web -> TechStart
(2, 1, 2), -- Desarrollo a Medida -> TechStart
(1, 2, 1), -- Diseño Web -> Moda Bella
(6, 2, 2), -- E-commerce -> Moda Bella
(1, 3, 1), -- Diseño Web -> Grupo Costa
(2, 3, 2), -- Desarrollo a Medida -> Grupo Costa
(4, 3, 3), -- SEO -> Grupo Costa
(1, 4, 1), -- Diseño Web -> FoodTech
(2, 4, 2), -- Desarrollo a Medida -> FoodTech
(3, 4, 3); -- Marketing Digital -> FoodTech

-- ============================================
-- Case-Tool Relations
-- ============================================
INSERT INTO `case_tools` (`case_id`, `tool_id`, `sort_order`) VALUES
(1, 8, 1), -- TechStart -> React
(1, 3, 2), -- TechStart -> Figma
(1, 4, 3), -- TechStart -> Google Analytics
(2, 1, 1), -- Moda Bella -> WordPress
(2, 2, 2), -- Moda Bella -> WooCommerce
(2, 3, 3), -- Moda Bella -> Figma
(2, 4, 4), -- Moda Bella -> Google Analytics
(3, 7, 1), -- Grupo Costa -> Laravel
(3, 3, 2), -- Grupo Costa -> Figma
(4, 8, 1), -- FoodTech -> React
(4, 3, 2), -- FoodTech -> Figma
(4, 5, 3); -- FoodTech -> Mailchimp

-- ============================================
-- FAQs
-- ============================================
INSERT INTO `faqs` (`question`, `answer`, `category_id`, `sort_order`, `is_active`) VALUES
('¿Cuánto tiempo tarda en desarrollarse un sitio web?', 'El tiempo de desarrollo varía según la complejidad del proyecto. Un sitio web corporativo básico puede estar listo en 3-4 semanas, mientras que proyectos más complejos como e-commerce pueden llevar 6-8 semanas o más.', NULL, 1, 1),
('¿Qué incluyen vuestros servicios de diseño web?', 'Nuestros servicios incluyen: diseño personalizado, desarrollo responsive, optimización SEO básica, integración con redes sociales, formularios de contacto, y formación para la gestión del contenido.', 4, 2, 1),
('¿Ofrecéis mantenimiento web?', 'Sí, ofrecemos planes de mantenimiento que incluyen actualizaciones de seguridad, copias de seguridad, soporte técnico y pequeñas modificaciones mensuales.', NULL, 3, 1),
('¿Trabajáis con clientes internacionales?', 'Sí, trabajamos con clientes de todo el mundo. Nos comunicamos principalmente en español e inglés y nos adaptamos a diferentes husos horarios.', NULL, 4, 1),
('¿Cómo es el proceso de trabajo?', 'Nuestro proceso incluye: briefing inicial, propuesta y presupuesto, diseño de mockups, desarrollo, revisiones, pruebas, lanzamiento y soporte post-lanzamiento.', NULL, 5, 1);

-- ============================================
-- Team Members
-- ============================================
INSERT INTO `team_members` (`name`, `slug`, `role`, `bio`, `is_active`, `sort_order`) VALUES
('María González', 'maria-gonzalez', 'CEO & Founder', 'Fundadora de Sinapsis con más de 15 años de experiencia en el sector digital.', 1, 1),
('Carlos Ruiz', 'carlos-ruiz', 'Director Creativo', 'Diseñador con pasión por crear experiencias digitales memorables.', 1, 2),
('Laura Martín', 'laura-martin', 'Lead Developer', 'Desarrolladora full-stack especializada en React y PHP.', 1, 3),
('Pablo Sánchez', 'pablo-sanchez', 'Marketing Manager', 'Experto en estrategias de marketing digital y growth hacking.', 1, 4);

-- ============================================
-- Homepage
-- ============================================
INSERT INTO `pages` (`title`, `slug`, `status`, `is_homepage`, `meta_title`, `meta_description`, `author_id`, `published_at`) VALUES
('Inicio', 'home', 'published', 1, "We're Sinapsis - Agencia de Diseño Web y Marketing Digital", "Somos una agencia creativa especializada en diseño web, desarrollo a medida y marketing digital. Transformamos ideas en experiencias digitales.", 1, NOW());

-- Sample blocks for homepage
INSERT INTO `page_blocks` (`page_id`, `type`, `content`, `settings`, `sort_order`, `is_active`) VALUES
(1, 'hero', '{"title": "Creamos experiencias digitales que conectan", "subtitle": "Somos una agencia creativa especializada en diseño web, desarrollo y marketing digital. Transformamos tus ideas en resultados.", "cta_text": "Ver proyectos", "cta_url": "/casos-de-exito", "cta_secondary_text": "Contactar", "cta_secondary_url": "/contacto"}', '{"style": "gradient", "alignment": "center"}', 1, 1),
(1, 'services', '{"title": "Nuestros servicios", "subtitle": "Soluciones digitales integrales para hacer crecer tu negocio"}', '{"columns": 3, "style": "cards", "limit": 6, "featured_only": false}', 2, 1),
(1, 'success_cases', '{"title": "Proyectos destacados", "subtitle": "Algunos de nuestros trabajos más recientes"}', '{"limit": 3, "style": "grid", "featured_only": true}', 3, 1),
(1, 'clients', '{"title": "Clientes que confían en nosotros", "subtitle": ""}', '{"style": "logos", "limit": 8}', 4, 1),
(1, 'cta_banner', '{"title": "¿Tienes un proyecto en mente?", "subtitle": "Cuéntanos tu idea y la haremos realidad", "cta_text": "Solicitar presupuesto", "cta_url": "/contacto"}', '{"style": "primary"}', 5, 1);
