<?php
/**
 * Block Renderer Service
 * Renders blocks for frontend display
 * We're Sinapsis CMS
 */

namespace App\Services;

use App\Helpers\TranslationHelper;

class BlockRenderer
{
    private string $templatePath;
    private TranslationHelper $translator;

    public function __construct()
    {
        $this->templatePath = __DIR__ . '/../../templates/frontend/blocks';
        $this->translator = TranslationHelper::getInstance();
    }

    /**
     * Render all blocks with grid layout support
     */
    public function renderBlocks(array $blocks): string
    {
        $html = '';
        $currentRow = [];
        $currentRowWidth = 0;

        foreach ($blocks as $block) {
            $settings = $this->ensureArray($block['settings'] ?? []);
            $width = $settings['block_width'] ?? 'full';
            $widthValue = $this->getWidthValue($width);

            // If full width or would exceed row, flush current row first
            if ($width === 'full' || ($currentRowWidth + $widthValue > 1)) {
                if (!empty($currentRow)) {
                    $html .= $this->renderBlockRow($currentRow);
                    $currentRow = [];
                    $currentRowWidth = 0;
                }
            }

            if ($width === 'full') {
                // Full width blocks render directly
                $html .= $this->renderBlock($block);
            } else {
                // Partial width blocks go into current row
                $currentRow[] = $block;
                $currentRowWidth += $widthValue;

                // If row is full, render it
                if ($currentRowWidth >= 1) {
                    $html .= $this->renderBlockRow($currentRow);
                    $currentRow = [];
                    $currentRowWidth = 0;
                }
            }
        }

        // Render any remaining blocks in the current row
        if (!empty($currentRow)) {
            $html .= $this->renderBlockRow($currentRow);
        }

        return $html;
    }

    /**
     * Render a row of blocks (for multi-column layouts)
     */
    private function renderBlockRow(array $blocks): string
    {
        if (count($blocks) === 1) {
            // Single block in row, just render it
            return $this->renderBlock($blocks[0]);
        }

        $html = '<div class="block-row">';
        foreach ($blocks as $block) {
            $settings = $this->ensureArray($block['settings'] ?? []);
            $width = $settings['block_width'] ?? 'half';
            $valign = $settings['vertical_align'] ?? 'stretch';

            $attrs = 'class="block-column block-column-' . htmlspecialchars($width) . '"';
            if ($valign !== 'stretch') {
                $attrs .= ' data-valign="' . htmlspecialchars($valign) . '"';
            }

            $html .= '<div ' . $attrs . '>';
            $html .= $this->renderBlock($block);
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Get numeric width value for calculations
     */
    private function getWidthValue(string $width): float
    {
        return match ($width) {
            'half' => 0.5,
            'third' => 0.333,
            'two-thirds' => 0.667,
            'quarter' => 0.25,
            'three-quarters' => 0.75,
            default => 1.0
        };
    }

    /**
     * Render a single block
     */
    public function renderBlock(array $block): string
    {
        $type = $block['type'] ?? $block['block_type'] ?? 'default';
        $content = $this->ensureArray($block['content'] ?? []);
        $settings = $this->ensureArray($block['settings'] ?? []);

        // Translate block content if block has an ID
        if (!empty($block['id'])) {
            // Determine entity type based on block source (service_block vs page_block)
            $entityType = $block['block_source'] ?? 'block';
            $content = $this->translator->translateBlockContent((int)$block['id'], $content, $entityType);
        }

        // Look for template
        $templateFile = "{$this->templatePath}/{$type}.php";

        if (!file_exists($templateFile)) {
            $templateFile = "{$this->templatePath}/default.php";
        }

        if (!file_exists($templateFile)) {
            return '<!-- Block template not found: ' . htmlspecialchars($type) . ' -->';
        }

        // Render
        ob_start();
        extract([
            'block' => $block,
            'content' => $content,
            'settings' => $settings,
            'renderer' => $this
        ]);
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * Ensure value is an array (handles both JSON strings and arrays)
     */
    private function ensureArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    /**
     * Get CSS classes for block
     */
    public function getBlockClasses(array $block, array $settings): string
    {
        $classes = ['block', 'block-' . ($block['type'] ?? $block['block_type'] ?? 'default')];

        if (!empty($settings['background'])) {
            $classes[] = 'bg-' . $settings['background'];
        }

        if (!empty($settings['padding'])) {
            $classes[] = 'padding-' . $settings['padding'];
        }

        if (!empty($settings['custom_class'])) {
            $classes[] = $settings['custom_class'];
        }

        // Visibility classes
        $showDesktop = $settings['show_desktop'] ?? true;
        $showMobile = $settings['show_mobile'] ?? true;

        if (!$showDesktop && $showMobile) {
            $classes[] = 'mobile-only';
        } elseif ($showDesktop && !$showMobile) {
            $classes[] = 'desktop-only';
        } elseif (!$showDesktop && !$showMobile) {
            $classes[] = 'hidden-all';
        }

        return implode(' ', $classes);
    }

    /**
     * Get inline styles for block
     */
    public function getBlockStyles(array $settings): string
    {
        $styles = [];

        if (!empty($settings['background_color'])) {
            $styles[] = "background-color: {$settings['background_color']}";
        }

        if (!empty($settings['background_image'])) {
            $styles[] = "background-image: url('{$settings['background_image']}')";
            $styles[] = "background-size: cover";
            $styles[] = "background-position: center";
        }

        if (!empty($settings['text_color'])) {
            $styles[] = "color: {$settings['text_color']}";
        }

        // Handle padding_top - use isset to properly handle "0" value
        if (isset($settings['padding_top']) && $settings['padding_top'] !== '') {
            $paddingTop = $settings['padding_top'];
            // Handle custom padding option
            if ($paddingTop === 'custom' && isset($settings['padding_top_custom']) && $settings['padding_top_custom'] !== '') {
                $paddingTop = $settings['padding_top_custom'];
            }
            // Handle "0" as "0px"
            if ($paddingTop === '0' || $paddingTop === 0) {
                $paddingTop = '0px';
            }
            if ($paddingTop !== 'custom') {
                $styles[] = "padding-top: {$paddingTop}";
            }
        }

        // Handle padding_bottom - use isset to properly handle "0" value
        if (isset($settings['padding_bottom']) && $settings['padding_bottom'] !== '') {
            $paddingBottom = $settings['padding_bottom'];
            // Handle custom padding option
            if ($paddingBottom === 'custom' && isset($settings['padding_bottom_custom']) && $settings['padding_bottom_custom'] !== '') {
                $paddingBottom = $settings['padding_bottom_custom'];
            }
            // Handle "0" as "0px"
            if ($paddingBottom === '0' || $paddingBottom === 0) {
                $paddingBottom = '0px';
            }
            if ($paddingBottom !== 'custom') {
                $styles[] = "padding-bottom: {$paddingBottom}";
            }
        }

        return !empty($styles) ? implode('; ', $styles) : '';
    }

    /**
     * Get animation attributes for a block
     */
    public function getAnimationAttributes(array $settings): string
    {
        $attrs = [];

        if (!empty($settings['animation'])) {
            $attrs[] = 'data-animate="' . htmlspecialchars($settings['animation']) . '"';

            if (!empty($settings['animation_duration']) && $settings['animation_duration'] !== 'normal') {
                $attrs[] = 'data-animate-duration="' . htmlspecialchars($settings['animation_duration']) . '"';
            }

            if (!empty($settings['animation_delay']) && $settings['animation_delay'] !== '0') {
                $attrs[] = 'data-animate-delay="' . htmlspecialchars($settings['animation_delay']) . '"';
            }
        }

        return implode(' ', $attrs);
    }

    /**
     * Get stagger animation attribute for container
     */
    public function getStaggerAttribute(array $settings): string
    {
        if (!empty($settings['animation_stagger']) && !empty($settings['animation'])) {
            return 'data-animate-stagger="' . htmlspecialchars($settings['animation']) . '"';
        }
        return '';
    }

    /**
     * Render CTA button
     */
    public function renderButton(array $button): string
    {
        if (empty($button['text']) || empty($button['url'])) {
            return '';
        }

        $class = 'btn ' . ($button['style'] ?? 'btn-primary');
        $target = !empty($button['new_tab']) ? ' target="_blank" rel="noopener"' : '';

        return sprintf(
            '<a href="%s" class="%s"%s>%s</a>',
            htmlspecialchars($button['url']),
            $class,
            $target,
            htmlspecialchars($button['text'])
        );
    }

    /**
     * Process content with shortcodes
     */
    public function processContent(string $content): string
    {
        // Process common shortcodes
        $content = preg_replace_callback('/\[button\s+(.+?)\](.+?)\[\/button\]/s', function($matches) {
            $attrs = $this->parseAttributes($matches[1]);
            $text = $matches[2];
            $url = $attrs['url'] ?? '#';
            $class = 'btn ' . ($attrs['style'] ?? 'btn-primary');
            return "<a href=\"{$url}\" class=\"{$class}\">{$text}</a>";
        }, $content);

        return $content;
    }

    /**
     * Parse shortcode attributes
     */
    private function parseAttributes(string $str): array
    {
        $attrs = [];
        preg_match_all('/(\w+)=["\']([^"\']+)["\']/', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $attrs[$match[1]] = $match[2];
        }
        return $attrs;
    }
}
