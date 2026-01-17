-- SEO Metadata Table
-- Stores SEO information for all content types with multi-language support
-- Omniwallet CMS

CREATE TABLE IF NOT EXISTS seo_metadata (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entity_type VARCHAR(50) NOT NULL COMMENT 'page, post, case, feature, partner, landing, knowledge, integration',
    entity_id INT NOT NULL,
    language VARCHAR(5) NOT NULL DEFAULT 'es',

    -- Core SEO fields
    meta_title VARCHAR(70) NULL,
    meta_description VARCHAR(170) NULL,

    -- Open Graph
    og_title VARCHAR(100) NULL,
    og_description VARCHAR(200) NULL,
    og_image VARCHAR(500) NULL,

    -- Advanced SEO
    canonical_url VARCHAR(500) NULL,
    robots VARCHAR(50) DEFAULT 'index, follow',
    keywords VARCHAR(255) NULL,

    -- Schema.org
    schema_type VARCHAR(50) NULL COMMENT 'Article, Product, FAQPage, Organization, etc.',
    schema_data JSON NULL COMMENT 'Custom schema properties',

    -- Generation tracking
    is_auto_generated TINYINT(1) DEFAULT 0,
    generation_source VARCHAR(20) NULL COMMENT 'gpt, manual, import',
    generated_at TIMESTAMP NULL,

    -- Audit
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Unique constraint per entity/language combination
    UNIQUE KEY unique_seo (entity_type, entity_id, language),

    -- Indexes for performance
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_language (language),
    INDEX idx_auto_generated (is_auto_generated)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEO Audit Log - Track changes and generation history
CREATE TABLE IF NOT EXISTS seo_audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    seo_metadata_id INT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT NOT NULL,
    action VARCHAR(20) NOT NULL COMMENT 'generate, update, delete',
    field_changed VARCHAR(50) NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    performed_by INT NULL COMMENT 'user_id',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_seo_metadata (seo_metadata_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEO Settings for global configuration
INSERT INTO settings (`key`, `value`, `type`, `group`, `label`, `description`) VALUES
('seo_auto_generate_on_publish', '0', 'boolean', 'seo', 'Auto-generate SEO on publish', 'Automatically generate SEO metadata when content is published'),
('seo_default_robots', 'index, follow', 'string', 'seo', 'Default robots meta', 'Default robots meta tag value for new content'),
('seo_title_suffix', ' | Omniwallet', 'string', 'seo', 'Title suffix', 'Text appended to all page titles'),
('seo_default_og_image', '/assets/images/og-default.jpg', 'string', 'seo', 'Default OG Image', 'Default Open Graph image when none specified'),
('robots_txt_content', 'User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /api/\nSitemap: https://omniwallet.es/sitemap.xml', 'textarea', 'seo', 'Robots.txt Content', 'Content of the robots.txt file')
ON DUPLICATE KEY UPDATE `key` = `key`;
