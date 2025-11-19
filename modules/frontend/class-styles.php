<?php
/**
 * Frontend Styles Generator
 *
 * Handles enqueueing and dynamic generation of frontend CSS
 *
 * @package JBLund_Dealers
 * @subpackage Frontend
 */

namespace JBLund\Frontend;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Styles Class
 *
 * Manages frontend CSS loading and customization
 */
class Styles {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_dashboard_styles'));
    }

    /**
     * Enqueue frontend styles
     */
    public function enqueue_frontend_styles() {
        wp_enqueue_style(
            'jblund-dealers-styles',
            JBLUND_DEALERS_PLUGIN_URL . 'assets/css/dealers.css',
            array(),
            JBLUND_DEALERS_VERSION . '.2'
        );

        // Add custom CSS from settings
        $this->add_custom_styles();
    }

    /**
     * Add custom styles from settings
     */
    private function add_custom_styles() {
        $options = get_option('jblund_dealers_settings');

        // Check if theme styles should be inherited
        $inherit_theme = isset($options['inherit_theme_styles']) && $options['inherit_theme_styles'] === '1';

        if ($inherit_theme) {
            // Add minimal CSS for theme integration
            $custom_css = "
            .dealer-card-header {
                background: var(--wp--preset--color--primary, currentColor) !important;
                color: var(--wp--preset--color--background, #fff) !important;
            }
            .dealer-website-button {
                background: var(--wp--preset--color--primary, currentColor) !important;
                color: var(--wp--preset--color--background, #fff) !important;
                font-family: inherit !important;
            }
            .dealer-website-button:hover {
                opacity: 0.85 !important;
            }
            .dealer-card {
                background: var(--wp--preset--color--background, #fff) !important;
                color: var(--wp--preset--color--foreground, #000) !important;
                font-family: inherit !important;
            }
            ";
        } else {
            // Only add CSS for settings that differ from defaults
            // This preserves the base CSS file styling
            $custom_css = "";

            // Colors - only add if different from default
            if (isset($options['header_color']) && $options['header_color'] !== '#0073aa') {
                $custom_css .= ".dealer-card-header { background: {$options['header_color']} !important; }\n";
            }
            if (isset($options['card_background']) && $options['card_background'] !== '#ffffff') {
                $custom_css .= ".dealer-card { background: {$options['card_background']} !important; }\n";
            }
            if (isset($options['button_color']) && $options['button_color'] !== '#0073aa') {
                $custom_css .= ".dealer-website-button { background: {$options['button_color']} !important; }\n";
                $custom_css .= ".dealer-website-button:hover { background: " . $this->darken_color($options['button_color'], 10) . " !important; }\n";
            }
            if (isset($options['text_color']) && $options['text_color'] !== '#333333') {
                $custom_css .= ".dealer-card h3, .dealer-card-header h3 { color: {$options['text_color']} !important; }\n";
            }
            if (isset($options['secondary_text_color']) && $options['secondary_text_color'] !== '#666666') {
                $custom_css .= ".dealer-card p, .dealer-card span, .dealer-card-address, .dealer-card-phone, .dealer-card-info { color: {$options['secondary_text_color']} !important; }\n";
            }
            if (isset($options['border_color']) && $options['border_color'] !== '#dddddd') {
                $custom_css .= ".dealer-card { border-color: {$options['border_color']} !important; }\n";
            }
            if (isset($options['button_text_color']) && $options['button_text_color'] !== '#ffffff') {
                $custom_css .= ".dealer-website-button { color: {$options['button_text_color']} !important; }\n";
            }
            if (isset($options['icon_color']) && $options['icon_color'] !== '#0073aa') {
                $custom_css .= ".dealer-services-icons, .dealer-services-icons span { color: {$options['icon_color']} !important; }\n";
            }
            if (isset($options['link_color']) && $options['link_color'] !== '#0073aa') {
                $custom_css .= ".dealer-card a, .dealer-card-phone a, .dealer-card-website a { color: {$options['link_color']} !important; }\n";
            }
            if (isset($options['hover_background']) && $options['hover_background'] !== '#f9f9f9') {
                $custom_css .= ".dealer-card:hover { background: {$options['hover_background']} !important; }\n";
            }

            // Typography - only add if different from default
            if (isset($options['heading_font_size']) && $options['heading_font_size'] !== '24') {
                $custom_css .= ".dealer-card h3, .dealer-card-header h3 { font-size: {$options['heading_font_size']}px !important; }\n";
            }
            if (isset($options['body_font_size']) && $options['body_font_size'] !== '14') {
                $custom_css .= ".dealer-card p, .dealer-card span, .dealer-card-address, .dealer-card-phone, .dealer-card-info { font-size: {$options['body_font_size']}px !important; }\n";
            }
            if (isset($options['heading_font_weight']) && $options['heading_font_weight'] !== 'bold') {
                $custom_css .= ".dealer-card h3, .dealer-card-header h3 { font-weight: {$options['heading_font_weight']} !important; }\n";
            }
            if (isset($options['line_height']) && $options['line_height'] !== '1.6') {
                $custom_css .= ".dealer-card p, .dealer-card span, .dealer-card h3 { line-height: {$options['line_height']} !important; }\n";
            }

            // Spacing - only add if different from default
            if (isset($options['card_padding']) && $options['card_padding'] !== '20') {
                $custom_css .= ".dealer-card { padding: {$options['card_padding']}px !important; }\n";
            }
            if (isset($options['card_margin']) && $options['card_margin'] !== '15') {
                $custom_css .= ".dealer-card { margin: {$options['card_margin']}px !important; }\n";
            }
            if (isset($options['grid_gap']) && $options['grid_gap'] !== '20') {
                $custom_css .= ".dealers-grid { gap: {$options['grid_gap']}px !important; }\n";
            }
            if (isset($options['border_radius']) && $options['border_radius'] !== '8') {
                $custom_css .= ".dealer-card { border-radius: {$options['border_radius']}px !important; }\n";
                $custom_css .= ".dealer-card-header { border-radius: {$options['border_radius']}px {$options['border_radius']}px 0 0 !important; }\n";
                $custom_css .= ".dealer-website-button { border-radius: {$options['border_radius']}px !important; }\n";
            }
            if (isset($options['border_width']) && $options['border_width'] !== '1') {
                $custom_css .= ".dealer-card { border-width: {$options['border_width']}px !important; }\n";
            }
            if (isset($options['border_style']) && $options['border_style'] !== 'solid') {
                $custom_css .= ".dealer-card { border-style: {$options['border_style']} !important; }\n";
            }

            // Effects - only add if different from default
            if (isset($options['box_shadow']) && $options['box_shadow'] !== 'light') {
                $box_shadows = array(
                    'none' => 'none',
                    'light' => '0 2px 4px rgba(0,0,0,0.1)',
                    'medium' => '0 4px 8px rgba(0,0,0,0.15)',
                    'heavy' => '0 8px 16px rgba(0,0,0,0.2)',
                );
                $custom_css .= ".dealer-card { box-shadow: {$box_shadows[$options['box_shadow']]} !important; }\n";
            }

            if (isset($options['hover_effect']) && $options['hover_effect'] !== 'lift') {
                $hover_transform = '';
                $hover_shadow = '';

                switch ($options['hover_effect']) {
                    case 'none':
                        // No transform needed
                        break;
                    case 'lift':
                        $hover_transform = 'translateY(-5px)';
                        break;
                    case 'scale':
                        $hover_transform = 'scale(1.02)';
                        break;
                    case 'shadow':
                        $hover_shadow = 'box-shadow: 0 12px 24px rgba(0,0,0,0.25) !important;';
                        break;
                }

                if ($hover_transform) {
                    $custom_css .= ".dealer-card:hover { transform: {$hover_transform} !important; }\n";
                }
                if ($hover_shadow) {
                    $custom_css .= ".dealer-card:hover { {$hover_shadow} }\n";
                }
            }

            if (isset($options['transition_speed']) && $options['transition_speed'] !== '0.3') {
                $custom_css .= ".dealer-card, .dealer-website-button { transition: all {$options['transition_speed']}s ease !important; }\n";
            }
            if (isset($options['icon_size']) && $options['icon_size'] !== '24') {
                $custom_css .= ".dealer-services-icons, .dealer-services-icons span { font-size: {$options['icon_size']}px !important; }\n";
            }

            // Add custom CSS from textarea if provided
            if (isset($options['custom_css']) && !empty($options['custom_css'])) {
                $custom_css .= "\n/* Custom CSS */\n" . $options['custom_css'] . "\n";
            }
        }

        // Only add inline styles if there's actually custom CSS to add
        if (!empty(trim($custom_css))) {
            wp_add_inline_style('jblund-dealers-styles', $custom_css);
        }
    }

    /**
     * Darken a hex color
     */
    private function darken_color($hex, $percent) {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r - ($r * $percent / 100)));
        $g = max(0, min(255, $g - ($g * $percent / 100)));
        $b = max(0, min(255, $b - ($b * $percent / 100)));

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    /**
     * Enqueue dashboard styles for dealer portal pages
     */
    public function enqueue_dashboard_styles() {
        // Check if we're on a dealer portal page
        if (!is_user_logged_in()) {
            return;
        }

        $current_user = wp_get_current_user();
        if (!in_array('dealer', (array) $current_user->roles)) {
            return;
        }

        // Enqueue dashboard CSS
        wp_enqueue_style(
            'jblund-dealers-dashboard',
            JBLUND_DEALERS_PLUGIN_URL . 'modules/dealer-portal/assets/css/dashboard.css',
            array(),
            JBLUND_DEALERS_VERSION
        );
    }
}
