<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Block Model
 * Omniwallet CMS
 */
class Block extends Model
{
    protected string $table = 'page_blocks';

    protected array $fillable = [
        'page_id',
        'type',
        'sort_order',
        'content',
        'settings',
        'is_active',
    ];

    /**
     * Available block types
     */
    public const TYPES = [
        'hero' => 'Hero',
        'text_image_left' => 'Texto + Imagen (Izquierda)',
        'text_image_right' => 'Texto + Imagen (Derecha)',
        'text_full_width' => 'Texto Ancho Completo',
        'areas' => 'Áreas (Columnas Interactivas)',
        'services' => 'Servicios',
        'success_cases' => 'Casos de Éxito',
        'faq' => 'Preguntas Frecuentes',
        'posts' => 'Posts/Blog',
        'business_types' => 'Características Resumidas',
        'tools' => 'Herramientas',
        'cta_banner' => 'Banner CTA',
        'benefits' => 'Características Detalladas',
        'video' => 'Video',
        'image_gallery' => 'Galería de Imágenes',
        'video_gallery' => 'Galería de Videos',
        'clients' => 'Clientes (Logos)',
        'team' => 'Equipo',
        'landings' => 'Landing Pages',
        'contact_form' => 'Formulario de Contacto',
        'custom_html' => 'HTML Personalizado',
        // TLOS Blocks
        'sponsors' => 'Sponsors (TLOS)',
        'companies' => 'Empresas (TLOS)',
        'agenda' => 'Agenda de Evento (TLOS)',
        'tickets' => 'Tickets de Evento (TLOS)',
        'pricing' => 'Planes y Precios',
    ];

    /**
     * Get blocks for page
     */
    public function getForPage(int $pageId): array
    {
        return $this->where(
            ['page_id' => $pageId, 'is_active' => 1],
            ['sort_order' => 'ASC']
        );
    }

    /**
     * Get active blocks for page
     */
    public function getActiveForPage(int $pageId): array
    {
        $blocks = $this->getForPage($pageId);

        return array_map(function ($block) {
            $block['content'] = json_decode($block['content'], true) ?? [];
            $block['settings'] = json_decode($block['settings'], true) ?? [];
            return $block;
        }, $blocks);
    }

    /**
     * Create block with JSON encoding
     */
    public function createBlock(array $data): int
    {
        // Convert block_type to type if present
        if (isset($data['block_type'])) {
            $data['type'] = $data['block_type'];
            unset($data['block_type']);
        }

        if (isset($data['content']) && is_array($data['content'])) {
            $data['content'] = json_encode($data['content'], JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['settings']) && is_array($data['settings'])) {
            $data['settings'] = json_encode($data['settings'], JSON_UNESCAPED_UNICODE);
        }

        // Get next sort_order
        if (!isset($data['sort_order'])) {
            $maxOrder = $this->db->fetchColumn(
                "SELECT MAX(sort_order) FROM `{$this->table}` WHERE page_id = ?",
                [$data['page_id']]
            );
            $data['sort_order'] = ($maxOrder ?? -1) + 1;
        }

        return $this->create($data);
    }

    /**
     * Update block with JSON encoding
     */
    public function updateBlock(int $id, array $data): bool
    {
        if (isset($data['content']) && is_array($data['content'])) {
            $data['content'] = json_encode($data['content'], JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['settings']) && is_array($data['settings'])) {
            $data['settings'] = json_encode($data['settings'], JSON_UNESCAPED_UNICODE);
        }

        return $this->update($id, $data);
    }

    /**
     * Reorder blocks
     */
    public function reorder(int $pageId, array $blockIds): void
    {
        foreach ($blockIds as $sortOrder => $blockId) {
            $this->db->update(
                $this->table,
                ['sort_order' => $sortOrder],
                'id = ? AND page_id = ?',
                [$blockId, $pageId]
            );
        }
    }

    /**
     * Get default content for block type
     */
    public static function getDefaultContent(string $type): array
    {
        $defaults = [
            'hero' => [
                'slides' => [
                    [
                        'title' => '',
                        'subtitle' => '',
                        'background_image' => '',
                        'cta_text' => 'Reservar demo',
                        'cta_url' => '/reservar-demo',
                    ],
                ],
            ],
            'text_image_left' => [
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'link_text' => '',
                'link_url' => '',
                'image' => '',
                'image_alt' => '',
            ],
            'text_image_right' => [
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'link_text' => '',
                'link_url' => '',
                'image' => '',
                'image_alt' => '',
            ],
            'text_full_width' => [
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'link_text' => '',
                'link_url' => '',
            ],
            'success_cases' => [
                'title' => 'Quiénes confían en Omniwallet',
                'subtitle' => '',
            ],
            'faq' => [
                'title' => '¿Tienes preguntas?',
                'subtitle' => '',
            ],
            'posts' => [
                'title' => 'Últimas novedades',
                'subtitle' => '',
            ],
            'cta_banner' => [
                'title' => '',
                'subtitle' => '',
                'cta_text' => 'Contactar',
                'cta_url' => '/contacto',
            ],
            'video' => [
                'title' => '',
                'subtitle' => '',
                'video_url' => '',
                'video_thumbnail' => '',
                'description' => '',
            ],
            'image_gallery' => [
                'title' => '',
                'subtitle' => '',
                'images' => [],
            ],
            'video_gallery' => [
                'title' => '',
                'subtitle' => '',
                'videos' => [],
            ],
            'business_types' => [
                'title' => 'Características',
                'subtitle' => '',
                'items' => [],
            ],
            'tools' => [
                'title' => 'Herramientas que utilizamos',
                'subtitle' => '',
            ],
            'benefits' => [
                'title' => 'Características',
                'subtitle' => '',
                'items' => [],
            ],
            'areas' => [
                'title' => '',
                'subtitle' => '',
                'items' => [
                    [
                        'title' => 'Área 1',
                        'subtitle' => '',
                        'description' => '',
                        'url' => '#',
                        'background_color' => '#1A1A1A',
                        'image' => '',
                    ],
                    [
                        'title' => 'Área 2',
                        'subtitle' => '',
                        'description' => '',
                        'url' => '#',
                        'background_color' => '#A8B5A0',
                        'image' => '',
                    ],
                    [
                        'title' => 'Área 3',
                        'subtitle' => '',
                        'description' => '',
                        'url' => '#',
                        'background_color' => '#F9AF00',
                        'image' => '',
                    ],
                    [
                        'title' => 'Área 4',
                        'subtitle' => '',
                        'description' => '',
                        'url' => '#',
                        'background_color' => '#E5E5E5',
                        'image' => '',
                    ],
                ],
            ],
            'services' => [
                'title' => 'Nuestros Servicios',
                'subtitle' => '',
                'cta_text' => '',
                'cta_url' => '',
            ],
            'clients' => [
                'title' => 'Empresas que confían en nosotros',
                'subtitle' => '',
            ],
            'team' => [
                'title' => 'Nuestro Equipo',
                'subtitle' => '',
            ],
            'landings' => [
                'title' => 'Descubre nuestras soluciones',
                'subtitle' => '',
                'show_more' => false,
                'more_text' => 'Ver todas',
                'more_url' => '/lp',
            ],
            'contact_form' => [
                'title' => 'Contacta con nosotros',
                'subtitle' => 'Completa el formulario y nos pondremos en contacto contigo lo antes posible',
                'submit_text' => 'Enviar mensaje',
                'success_title' => '¡Mensaje enviado!',
                'success_message' => 'Gracias por contactar con nosotros. Hemos recibido tu mensaje y nos pondremos en contacto contigo lo antes posible.',
                'fields' => [
                    [
                        'name' => 'name',
                        'label' => 'Nombre completo',
                        'type' => 'text',
                        'placeholder' => 'Tu nombre',
                        'required' => true,
                        'enabled' => true,
                        'width' => 'half',
                    ],
                    [
                        'name' => 'email',
                        'label' => 'Email',
                        'type' => 'email',
                        'placeholder' => 'tu@email.com',
                        'required' => true,
                        'enabled' => true,
                        'width' => 'half',
                    ],
                    [
                        'name' => 'phone',
                        'label' => 'Teléfono',
                        'type' => 'tel',
                        'placeholder' => '+34 600 000 000',
                        'required' => false,
                        'enabled' => true,
                        'width' => 'half',
                    ],
                    [
                        'name' => 'company',
                        'label' => 'Empresa',
                        'type' => 'text',
                        'placeholder' => 'Nombre de tu empresa',
                        'required' => false,
                        'enabled' => true,
                        'width' => 'half',
                    ],
                    [
                        'name' => 'subject',
                        'label' => 'Asunto',
                        'type' => 'text',
                        'placeholder' => '¿En qué podemos ayudarte?',
                        'required' => false,
                        'enabled' => false,
                        'width' => 'full',
                    ],
                    [
                        'name' => 'message',
                        'label' => 'Mensaje',
                        'type' => 'textarea',
                        'placeholder' => 'Escribe tu mensaje aquí...',
                        'required' => true,
                        'enabled' => true,
                        'width' => 'full',
                    ],
                ],
            ],
            // TLOS Blocks
            'sponsors' => [
                'title' => 'Nuestros Sponsors',
                'subtitle' => '',
            ],
            'companies' => [
                'title' => 'Empresas Participantes',
                'subtitle' => '',
            ],
            'agenda' => [
                'title' => 'Agenda del Evento',
                'subtitle' => '',
            ],
            'tickets' => [
                'title' => 'Consigue tu entrada',
                'subtitle' => '',
                'cta_text' => 'Comprar entrada',
            ],
            'pricing' => [
                'title' => 'Planes y Precios',
                'subtitle' => '',
                'plans' => [],
            ],
        ];

        return $defaults[$type] ?? [];
    }

    /**
     * Get default settings for block type
     */
    public static function getDefaultSettings(string $type): array
    {
        $defaults = [
            'hero' => [
                'height' => '600px',
                'overlay_opacity' => 0.6,
                'text_alignment' => 'left',
                'parallax_enabled' => true,
            ],
            'text_image_left' => [
                'background_color' => '#ffffff',
                'padding_top' => '80px',
                'padding_bottom' => '80px',
                'image_animation' => 'fade-left',
            ],
            'text_image_right' => [
                'background_color' => '#f8f9fa',
                'padding_top' => '80px',
                'padding_bottom' => '80px',
                'image_animation' => 'fade-right',
            ],
            'success_cases' => [
                'limit' => 6,
                'columns' => 3,
                'show_logo_only' => true,
                'background_color' => '#f8f9fa',
            ],
            'posts' => [
                'limit' => 3,
                'columns' => 3,
                'background_color' => '#ffffff',
            ],
            'video' => [
                'layout' => 'full',
                'background' => '',
                'padding_top' => '80px',
                'padding_bottom' => '80px',
            ],
            'image_gallery' => [
                'layout_mode' => 'grid',
                'columns' => 4,
                'visible_items' => 4,
                'aspect_ratio' => '1:1',
                'gap' => 'md',
                'autoplay' => true,
                'autoplay_speed' => 'normal',
                'background_color' => '',
                'padding_top' => '80px',
                'padding_bottom' => '80px',
            ],
            'video_gallery' => [
                'layout_mode' => 'grid',
                'columns' => 3,
                'visible_items' => 3,
                'gap' => 'md',
                'autoplay' => true,
                'autoplay_speed' => 'normal',
                'background_color' => '',
                'padding_top' => '80px',
                'padding_bottom' => '80px',
            ],
            'business_types' => [
                'columns' => 4,                    // 3, 4, 5, 6
                'display_mode' => 'cards',         // cards, numbered, lettered, icons_only
                'card_style' => 'minimal',         // bordered, shadow, minimal, filled
                'number_style' => 'large',         // large, small, circle
                'show_icon' => true,
                'show_description' => true,
                'hover_effect' => 'lift',          // none, lift, glow, border
                'background_color' => '#f8f9fa',
            ],
            'tools' => [
                'display_mode' => 'grid',          // grid or carousel
                'columns' => 4,                    // for grid mode: 3, 4, 5, 6
                'visible_items' => 5,              // for carousel mode
                'selection_mode' => 'all',         // all, category, manual
                'category_filter' => '',           // category ID for filtering
                'selected_tools' => [],            // array of tool IDs for manual selection
                'limit' => 12,
                'logo_height' => 60,               // fixed height in pixels
                'grayscale' => true,               // show logos in grayscale
                'show_name' => false,              // show tool name below logo
                'autoplay' => true,                // for carousel mode
                'autoplay_speed' => 'normal',      // slow, normal, fast
                'background_color' => '#ffffff',
            ],
            'benefits' => [
                'columns' => 2,                    // 1, 2, 3
                'display_mode' => 'cards',         // cards, list, accordion
                'card_style' => 'bordered',        // bordered, shadow, minimal, filled
                'hover_effect' => 'lift',          // none, lift, glow, border
                'icon_position' => 'top',          // top, left, inline
                'show_icon' => true,
                'show_description' => true,
                'background_color' => '#ffffff',
            ],
            'areas' => [
                'effect' => 'reveal-up',        // reveal-up, reveal-down, reveal-left, reveal-right, zoom, flip, tilt, gradient
                'columns' => 4,                 // 2, 3, 4, 5, 6
                'height' => '500px',            // 400px, 500px, 600px, 700px, 100vh
                'gap' => '0',                   // 0, 4px, 8px, 16px
                'text_color' => '#ffffff',
                'text_position' => 'center',    // top, center, bottom
                'show_subtitle' => true,
                'show_description' => false,
                'overlay_opacity' => 0,         // 0, 0.2, 0.4, 0.6
                'border_radius' => '0',         // 0, 8px, 16px, 24px
                'full_width' => true,
            ],
            'services' => [
                'display_mode' => 'cards',         // cards, list, compact, icons_only
                'layout_mode' => 'grid',           // grid or carousel
                'columns' => 3,                    // 2, 3, 4
                'visible_items' => 3,              // for carousel mode
                'selection_mode' => 'all',         // all, category, manual
                'category_filter' => '',           // category ID for filtering
                'selected_services' => [],         // array of service IDs for manual selection
                'limit' => 6,
                'card_style' => 'shadow',          // bordered, shadow, minimal, filled
                'hover_effect' => 'lift',          // none, lift, glow, border
                'show_icon' => true,
                'show_description' => true,
                'show_category' => true,
                'show_link' => true,
                'autoplay' => true,                // for carousel mode
                'autoplay_speed' => 'normal',      // slow, normal, fast
                'background_color' => '#f8f9fa',
            ],
            'clients' => [
                'display_mode' => 'grid',     // grid or carousel
                'columns' => 6,               // for grid mode
                'visible_items' => 5,         // for carousel mode
                'selection_mode' => 'all',    // all, industry, manual
                'industry_filter' => '',      // industry slug for filtering
                'selected_cases' => [],       // array of case IDs for manual selection
                'limit' => 12,
                'logo_height' => 60,          // fixed height in pixels
                'show_link' => true,          // link to case page
                'background_color' => '#ffffff',
            ],
            'team' => [
                'display_mode' => 'basica',   // minimalista, basica, sinapsis, detallada
                'layout_mode' => 'grid',      // grid or carousel
                'columns' => 4,               // 2, 3, 4, 5, 6
                'selection_mode' => 'all',    // all or manual
                'selected_members' => [],     // array of member IDs for manual selection
                'limit' => 12,
                'visible_items' => 4,         // for carousel mode
                'autoplay' => true,           // for carousel mode
                'autoplay_speed' => 'normal', // slow, normal, fast
                'background_color' => '#ffffff',
            ],
            'landings' => [
                'display_mode' => 'extended',   // simple or extended
                'columns' => 3,                 // 2, 3, or 4
                'selection_mode' => 'manual',   // manual, theme, or themes
                'selected_landings' => [],      // array of landing IDs for manual selection
                'selected_theme' => '',         // theme ID for theme mode
                'selected_themes' => [],        // array of theme IDs for themes mode
                'limit' => 6,
                'open_new_window' => false,
                'animation' => 'fade-up',
                'animation_duration' => 'normal',
                'animation_delay' => '0',
                'animation_stagger' => false,
                'background_color' => '#ffffff',
            ],
            'contact_form' => [
                'recipient_email' => '',           // Email to receive submissions
                'recaptcha_enabled' => false,      // Enable reCAPTCHA v3
                'recaptcha_site_key' => '',        // reCAPTCHA site key
                'recaptcha_secret_key' => '',      // reCAPTCHA secret key
                'save_submissions' => true,        // Save to database
                'background_color' => '#ffffff',
                'form_style' => 'card',            // card or inline
                'animation' => 'fade-up',
                'animation_duration' => 'normal',
                'animation_delay' => '0',
            ],
            // TLOS Blocks
            'sponsors' => [
                'display_mode' => 'grid',          // grid or carousel
                'columns' => 4,                    // 3, 4, 5, 6
                'visible_items' => 5,              // for carousel mode
                'selection_mode' => 'all',         // all, event, level, manual
                'event_id' => '',                  // event ID for event filtering
                'level_filter' => '',              // sponsor level for filtering
                'selected_sponsors' => [],         // array of sponsor IDs for manual selection
                'limit' => 12,
                'logo_height' => 80,
                'show_name' => true,
                'show_level' => true,
                'grayscale' => false,
                'autoplay' => true,
                'autoplay_speed' => 'normal',
                'background_color' => '#ffffff',
            ],
            'companies' => [
                'display_mode' => 'grid',          // grid or carousel
                'columns' => 4,                    // 3, 4, 5, 6
                'visible_items' => 5,              // for carousel mode
                'selection_mode' => 'all',         // all, event, sector, manual
                'event_id' => '',                  // event ID for event filtering
                'sector_filter' => '',             // sector for filtering
                'selected_companies' => [],        // array of company IDs for manual selection
                'limit' => 12,
                'logo_height' => 80,
                'show_name' => true,
                'show_sector' => true,
                'grayscale' => false,
                'autoplay' => true,
                'autoplay_speed' => 'normal',
                'background_color' => '#f8f9fa',
            ],
            'agenda' => [
                'event_id' => '',                  // required: event ID
                'display_mode' => 'timeline',      // timeline, cards, compact
                'group_by' => 'date',              // date, room, type
                'show_room' => true,
                'show_speaker' => true,
                'show_description' => true,
                'show_time' => true,
                'filter_by_date' => '',            // optional: specific date
                'filter_by_room' => '',            // optional: room ID
                'filter_by_type' => '',            // optional: activity type
                'background_color' => '#ffffff',
            ],
            'tickets' => [
                'event_id' => '',                  // required: event ID
                'sponsor_id' => '',                // optional: sponsor offering this ticket
                'ticket_type_id' => '',            // optional: specific ticket type
                'display_mode' => 'card',          // card, inline, minimal
                'show_price' => true,
                'show_remaining' => true,
                'show_description' => true,
                'custom_price' => '',              // override ticket price display
                'custom_limit' => '',              // override availability display
                'background_color' => '#f8f9fa',
            ],
            'pricing' => [
                'columns' => 3,                    // 2, 3, 4
                'card_style' => 'bordered',        // bordered, shadow, minimal
                'show_price_suffix' => true,
                'show_description' => true,
                'show_icon' => false,
                'equal_height' => true,
                'background_color' => '#ffffff',
            ],
        ];

        return $defaults[$type] ?? [];
    }
}
