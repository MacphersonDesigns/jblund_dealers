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
        add_action('wp_enqueue_scripts', array($this, 'add_custom_styles'), 20); // Priority 20 to run after stylesheet is registered
    }

    /**
     * Enqueue frontend styles
     */
    public function enqueue_frontend_styles() {
        wp_enqueue_style(
            'jblund-dealers-styles',
            JBLUND_DEALERS_PLUGIN_URL . 'assets/css/dealers.css',
            array(),
            JBLUND_DEALERS_VERSION . '.5'
        );
    }

    /**
     * Add custom styles from settings
     */
    public function add_custom_styles() {
        $options = get_option('jblund_dealers_settings');

        // Start with CSS custom properties for easy theming
        $custom_css = ":root {\n";

        // Inject all customizable colors as CSS variables
        $custom_css .= "\t--jblund-header-color: " . ($options['header_color'] ?? '#0073aa') . ";\n";
        $custom_css .= "\t--jblund-card-background: " . ($options['card_background'] ?? '#ffffff') . ";\n";
        $custom_css .= "\t--jblund-button-color: " . ($options['button_color'] ?? '#0073aa') . ";\n";
        $custom_css .= "\t--jblund-button-hover-color: " . $this->darken_color($options['button_color'] ?? '#0073aa', 15) . ";\n";
        $custom_css .= "\t--jblund-primary-text-color: " . ($options['text_color'] ?? '#333333') . ";\n";
        $custom_css .= "\t--jblund-secondary-text-color: " . ($options['secondary_text_color'] ?? '#666666') . ";\n";
        $custom_css .= "\t--jblund-border-color: " . ($options['border_color'] ?? '#e0e0e0') . ";\n";
        $custom_css .= "\t--jblund-button-text-color: " . ($options['button_text_color'] ?? '#ffffff') . ";\n";
        $custom_css .= "\t--jblund-icon-color: " . ($options['icon_color'] ?? '#0073aa') . ";\n";
        $custom_css .= "\t--jblund-link-color: " . ($options['link_color'] ?? '#0073aa') . ";\n";
        $custom_css .= "\t--jblund-hover-background: " . ($options['hover_background'] ?? '#f9f9f9') . ";\n";
        $custom_css .= "}\n\n";

        // Add custom CSS from textarea if provided
        if (isset($options['custom_css']) && !empty($options['custom_css'])) {
            $custom_css .= "/* Custom CSS */\n" . $options['custom_css'] . "\n";
        }

        // Add to both dealer directory and dealer portal styles
        wp_add_inline_style('jblund-dealers-styles', $custom_css);
        
        // Also add to dashboard if it's enqueued
        if (wp_style_is('jblund-dealers-dashboard', 'enqueued')) {
            wp_add_inline_style('jblund-dealers-dashboard', $custom_css);
        }
        
        // Also add to dealer portal if it's enqueued (from shortcodes)
        if (wp_style_is('jblund-dealer-portal', 'enqueued')) {
            wp_add_inline_style('jblund-dealer-portal', $custom_css);
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
