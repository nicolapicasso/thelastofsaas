-- ============================================
-- TLOS - The Last of SaaS
-- Core Schema Migration
-- ============================================
-- This migration creates the core tables for the TLOS event management platform
-- Version: 1.0
-- Date: January 2025
-- ============================================

-- ============================================
-- 1. EVENTS MODULE
-- ============================================

-- Main events table
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    venue_name VARCHAR(255),
    venue_address TEXT,
    venue_city VARCHAR(100),
    venue_coordinates VARCHAR(100),
    event_date DATE,
    event_end_date DATE,
    total_capacity INT NOT NULL DEFAULT 100,
    status ENUM('draft', 'published', 'active', 'finished', 'cancelled') DEFAULT 'draft',
    featured_image VARCHAR(500),

    -- Settings
    registration_open BOOLEAN DEFAULT TRUE,
    matching_enabled BOOLEAN DEFAULT TRUE,
    meetings_enabled BOOLEAN DEFAULT TRUE,

    -- Metadata
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_status (status),
    INDEX idx_event_date (event_date),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event features/characteristics
CREATE TABLE IF NOT EXISTS event_features (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event_order (event_id, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. SPONSORS MODULE
-- ============================================

-- Sponsors table
CREATE TABLE IF NOT EXISTS sponsors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    category VARCHAR(255),
    description TEXT,
    short_description VARCHAR(500),
    website VARCHAR(500),
    logo_url VARCHAR(500),
    contact_emails TEXT,
    contact_phone VARCHAR(50),
    unique_code VARCHAR(100) NOT NULL UNIQUE,
    active BOOLEAN DEFAULT TRUE,

    -- Meeting configuration
    max_simultaneous_meetings INT DEFAULT 1,
    can_send_messages BOOLEAN DEFAULT FALSE,

    -- Social links
    linkedin_url VARCHAR(500),
    twitter_url VARCHAR(500),

    -- Metadata
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_unique_code (unique_code),
    INDEX idx_active (active),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event-Sponsor relationship with priority level
CREATE TABLE IF NOT EXISTS event_sponsors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    priority_level ENUM('platinum', 'gold', 'silver', 'bronze') NOT NULL DEFAULT 'bronze',
    display_order INT DEFAULT 0,
    custom_landing_enabled BOOLEAN DEFAULT TRUE,
    max_free_tickets INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_sponsor (event_id, sponsor_id),
    INDEX idx_event_level (event_id, priority_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. COMPANIES MODULE
-- ============================================

-- Companies (potential clients) table
CREATE TABLE IF NOT EXISTS companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(500),
    website VARCHAR(500),
    logo_url VARCHAR(500),
    contact_emails TEXT,
    contact_phone VARCHAR(50),
    company_size ENUM('1-10', '11-50', '51-200', '201-500', '500+'),
    industry VARCHAR(255),
    notes TEXT,
    unique_code VARCHAR(100) NOT NULL UNIQUE,
    active BOOLEAN DEFAULT TRUE,

    -- Metadata
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_unique_code (unique_code),
    INDEX idx_active (active),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SaaS tools used by each company
CREATE TABLE IF NOT EXISTS company_saas_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usage (company_id, sponsor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event-Company relationship
CREATE TABLE IF NOT EXISTS event_companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    company_id INT NOT NULL,
    registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_company (event_id, company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. TICKETS MODULE
-- ============================================

-- Ticket types
CREATE TABLE IF NOT EXISTS ticket_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    is_free BOOLEAN DEFAULT FALSE,
    sponsor_id INT NULL,
    quantity_available INT NULL,
    quantity_sold INT DEFAULT 0,
    sale_start_date DATETIME,
    sale_end_date DATETIME,
    status ENUM('active', 'inactive', 'sold_out') DEFAULT 'active',

    -- Additional settings
    requires_approval BOOLEAN DEFAULT FALSE,
    max_per_purchase INT DEFAULT 1,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE SET NULL,
    INDEX idx_event_status (event_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tickets (sold/registered entries)
CREATE TABLE IF NOT EXISTS tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_type_id INT NOT NULL,
    event_id INT NOT NULL,

    -- Attendee data
    attendee_first_name VARCHAR(100) NOT NULL,
    attendee_last_name VARCHAR(100) NOT NULL,
    attendee_email VARCHAR(255) NOT NULL,
    attendee_phone VARCHAR(50),
    attendee_job_title VARCHAR(100),

    -- Attendee company data
    attendee_company_name VARCHAR(255),
    attendee_company_website VARCHAR(500),
    attendee_company_size ENUM('1-10', '11-50', '51-200', '201-500', '500+'),

    -- Who invited/subscribed the ticket
    invited_by_type ENUM('organization', 'sponsor') NOT NULL DEFAULT 'organization',
    invited_by_sponsor_id INT NULL,

    -- Status and payment
    status ENUM('pending', 'confirmed', 'cancelled', 'checked_in') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded', 'free') DEFAULT 'pending',
    stripe_payment_intent_id VARCHAR(255),
    stripe_charge_id VARCHAR(255),
    amount_paid DECIMAL(10,2) DEFAULT 0.00,

    -- Codes
    ticket_code VARCHAR(50) NOT NULL UNIQUE,
    confirmation_code VARCHAR(20) NOT NULL,

    -- Metadata
    registration_ip VARCHAR(45),
    user_agent TEXT,
    notes TEXT,

    -- Dates
    event_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    confirmed_at DATETIME,
    checked_in_at DATETIME,

    FOREIGN KEY (ticket_type_id) REFERENCES ticket_types(id),
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (invited_by_sponsor_id) REFERENCES sponsors(id) ON DELETE SET NULL,
    INDEX idx_ticket_code (ticket_code),
    INDEX idx_attendee_email (attendee_email),
    INDEX idx_event_date (event_id, event_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. MATCHING MODULE
-- ============================================

-- Sponsor selections (sponsors selecting companies)
CREATE TABLE IF NOT EXISTS sponsor_selections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    company_id INT NOT NULL,
    priority INT DEFAULT 0,
    notes TEXT,
    selected_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_selection (event_id, sponsor_id, company_id),
    INDEX idx_sponsor (event_id, sponsor_id),
    INDEX idx_company (event_id, company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Company selections (companies selecting sponsors)
CREATE TABLE IF NOT EXISTS company_selections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    company_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    priority INT DEFAULT 0,
    notes TEXT,
    selected_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_selection (event_id, company_id, sponsor_id),
    INDEX idx_company (event_id, company_id),
    INDEX idx_sponsor (event_id, sponsor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email notifications log (anti-duplicate)
CREATE TABLE IF NOT EXISTS email_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NULL,
    notification_type VARCHAR(50) NOT NULL,
    sender_type VARCHAR(20) NOT NULL,
    sender_id INT NOT NULL,
    recipient_type VARCHAR(20) NOT NULL,
    recipient_id INT NOT NULL,
    recipient_email VARCHAR(255),
    subject VARCHAR(500),
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    UNIQUE KEY unique_notification (notification_type, sender_id, recipient_id, event_id),
    INDEX idx_sender (sender_type, sender_id),
    INDEX idx_recipient (recipient_type, recipient_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sponsor messages to companies
CREATE TABLE IF NOT EXISTS sponsor_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    company_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_message (event_id, sponsor_id, company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. MEETINGS MODULE
-- ============================================

-- Meeting time blocks
CREATE TABLE IF NOT EXISTS meeting_blocks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    meeting_duration INT NOT NULL DEFAULT 15,
    simultaneous_meetings INT NOT NULL DEFAULT 10,
    location VARCHAR(255),
    active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event_date (event_id, event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Individual meeting slots
CREATE TABLE IF NOT EXISTS meeting_slots (
    id INT PRIMARY KEY AUTO_INCREMENT,
    block_id INT NOT NULL,
    slot_time TIME NOT NULL,
    room_number INT NOT NULL,
    room_name VARCHAR(100),
    is_available BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (block_id) REFERENCES meeting_blocks(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slot (block_id, slot_time, room_number),
    INDEX idx_block (block_id),
    INDEX idx_time (slot_time),
    INDEX idx_available (block_id, is_available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Meeting assignments
CREATE TABLE IF NOT EXISTS meeting_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slot_id INT NOT NULL,
    event_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    company_id INT NOT NULL,
    status ENUM('confirmed', 'pending', 'cancelled', 'completed', 'no_show') DEFAULT 'confirmed',
    notes TEXT,
    feedback_sponsor TEXT,
    feedback_company TEXT,
    rating_sponsor INT,
    rating_company INT,
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    assigned_by ENUM('admin', 'live_matching', 'auto') DEFAULT 'admin',
    completed_at DATETIME,

    FOREIGN KEY (slot_id) REFERENCES meeting_slots(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (slot_id),
    INDEX idx_sponsor (event_id, sponsor_id),
    INDEX idx_company (event_id, company_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. VOTINGS MODULE
-- ============================================

-- Votings
CREATE TABLE IF NOT EXISTS votings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    status ENUM('draft', 'active', 'inactive', 'finished') DEFAULT 'draft',
    show_vote_counts BOOLEAN DEFAULT TRUE,
    show_ranking BOOLEAN DEFAULT TRUE,
    allow_multiple_votes BOOLEAN DEFAULT FALSE,
    voting_start DATETIME,
    voting_end DATETIME,
    featured_image VARCHAR(500),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Voting candidates
CREATE TABLE IF NOT EXISTS voting_candidates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    voting_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    logo_url VARCHAR(500),
    website_url VARCHAR(500),
    votes INT DEFAULT 0,
    base_votes INT DEFAULT 0,
    display_order INT DEFAULT 0,
    active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (voting_id) REFERENCES votings(id) ON DELETE CASCADE,
    INDEX idx_voting (voting_id),
    INDEX idx_votes (voting_id, votes DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Votes log (anti-fraud control)
CREATE TABLE IF NOT EXISTS votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    voting_id INT NOT NULL,
    candidate_id INT NOT NULL,
    voter_ip VARCHAR(45),
    voter_fingerprint VARCHAR(255),
    voter_cookie VARCHAR(255),
    voter_email VARCHAR(255),
    voted_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (voting_id) REFERENCES votings(id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES voting_candidates(id) ON DELETE CASCADE,
    INDEX idx_voting (voting_id),
    INDEX idx_ip (voter_ip),
    INDEX idx_fingerprint (voter_fingerprint)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. LIVE MATCHING MODULE
-- ============================================

-- Live activity log
CREATE TABLE IF NOT EXISTS live_activity_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    user_type VARCHAR(20) NOT NULL,
    user_id BIGINT NOT NULL,
    related_user_type VARCHAR(20),
    related_user_id BIGINT,
    slot_id BIGINT,
    device_type VARCHAR(20),
    ip_address VARCHAR(45),
    user_agent TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    metadata JSON,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event_type (event_type),
    INDEX idx_user (user_type, user_id),
    INDEX idx_timestamp (timestamp),
    INDEX idx_event (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. TLOS SETTINGS
-- ============================================

-- TLOS-specific settings (extends existing settings table)
CREATE TABLE IF NOT EXISTS tlos_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'boolean', 'number', 'json', 'email') DEFAULT 'text',
    setting_group VARCHAR(50) DEFAULT 'general',
    description VARCHAR(500),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_group (setting_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default TLOS settings
INSERT INTO tlos_settings (setting_key, setting_value, setting_type, setting_group, description) VALUES
('site_name', 'The Last of SaaS', 'text', 'general', 'Site name'),
('admin_emails', '', 'textarea', 'general', 'Admin notification emails (comma separated)'),
('email_from', 'noreply@thelastofsaas.es', 'email', 'email', 'From email address'),
('email_from_name', 'The Last of SaaS', 'text', 'email', 'From name for emails'),
('notify_sponsors', '1', 'boolean', 'email', 'Send notifications to sponsors'),
('notify_companies', '1', 'boolean', 'email', 'Send notifications to companies'),
('notify_meetings', '1', 'boolean', 'email', 'Send meeting notifications'),
('sponsor_page_url', '/seleccion-sponsor', 'text', 'matching', 'Sponsor selection page URL'),
('company_page_url', '/seleccion-empresa', 'text', 'matching', 'Company selection page URL'),
('hide_inactive', '1', 'boolean', 'matching', 'Hide inactive sponsors/companies in selection'),
('allow_sponsor_messages', '1', 'boolean', 'matching', 'Allow sponsors to send messages to companies'),
('stripe_public_key', '', 'text', 'stripe', 'Stripe public key'),
('stripe_secret_key', '', 'text', 'stripe', 'Stripe secret key'),
('stripe_webhook_secret', '', 'text', 'stripe', 'Stripe webhook secret'),
('currency', 'eur', 'text', 'stripe', 'Payment currency'),
('default_meeting_duration', '15', 'number', 'meetings', 'Default meeting duration in minutes'),
('default_simultaneous_meetings', '10', 'number', 'meetings', 'Default number of simultaneous meetings');

-- ============================================
-- 10. EMAIL TEMPLATES
-- ============================================

CREATE TABLE IF NOT EXISTS email_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_key VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    body_html TEXT NOT NULL,
    body_text TEXT,
    variables TEXT,
    active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default email templates
INSERT INTO email_templates (template_key, name, subject, body_html, variables) VALUES
('selection_received', 'Selection Received', '{SELECTOR_NAME} ha mostrado interés en ti',
'<h2>¡Buenas noticias!</h2><p><strong>{SELECTOR_NAME}</strong> ha mostrado interés en conectar contigo en el evento.</p><p>Accede a tu panel para ver más detalles y gestionar tus selecciones:</p><p><a href="{PANEL_URL}">Acceder al panel</a></p>',
'SELECTOR_NAME,SELECTED_NAME,PANEL_URL'),

('meeting_assigned', 'Meeting Assigned', 'Reunión confirmada: {WITH_NAME}',
'<h2>Reunión Confirmada</h2><p>Tu reunión ha sido programada:</p><ul><li><strong>Con:</strong> {WITH_NAME}</li><li><strong>Fecha:</strong> {DATE}</li><li><strong>Hora:</strong> {TIME}</li><li><strong>Mesa/Sala:</strong> {ROOM}</li><li><strong>Duración:</strong> {DURATION} minutos</li></ul><p>Bloque: {BLOCK_NAME}</p>',
'WITH_NAME,DATE,TIME,ROOM,DURATION,BLOCK_NAME'),

('ticket_confirmation', 'Ticket Confirmation', 'Tu entrada para {EVENT_NAME}',
'<h2>¡Entrada Confirmada!</h2><p>Gracias por registrarte en <strong>{EVENT_NAME}</strong>.</p><p><strong>Código de entrada:</strong> {TICKET_CODE}</p><p><strong>Fecha:</strong> {EVENT_DATE}</p><p><strong>Lugar:</strong> {VENUE_NAME}</p><p>Presenta el código QR adjunto en la entrada del evento.</p>',
'EVENT_NAME,TICKET_CODE,EVENT_DATE,VENUE_NAME,ATTENDEE_NAME'),

('sponsor_message', 'Sponsor Message', 'Mensaje de {SPONSOR_NAME}',
'<h2>Has recibido un mensaje</h2><p><strong>{SPONSOR_NAME}</strong> te ha enviado un mensaje:</p><blockquote>{MESSAGE}</blockquote><p>Puedes ver más información sobre este sponsor en tu panel:</p><p><a href="{PANEL_URL}">Acceder al panel</a></p>',
'SPONSOR_NAME,MESSAGE,PANEL_URL');
