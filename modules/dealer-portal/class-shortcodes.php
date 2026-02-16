<?php
/**
 * Dealer Portal Shortcodes
 *
 * Handles all dealer portal shortcodes (dashboard, profile, login)
 *
 * @package JBLund_Dealers
 * @subpackage DealerPortal
 */

namespace JBLund\DealerPortal;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Shortcodes {

    /**
     * Constructor - Register shortcodes
     */
    public function __construct() {
        add_shortcode('jblund_dealer_dashboard', array($this, 'dashboard'));
        add_shortcode('jblund_dealer_profile', array($this, 'profile'));
        add_shortcode('jblund_dealer_login', array($this, 'login'));

        // Enqueue styles for dealer portal pages
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    /**
     * Enqueue dealer portal styles
     */
    public function enqueue_styles() {
        // Only enqueue on pages with dealer portal shortcodes
        global $post;
        if (is_a($post, 'WP_Post') && (
            has_shortcode($post->post_content, 'jblund_dealer_dashboard') ||
            has_shortcode($post->post_content, 'jblund_dealer_profile') ||
            has_shortcode($post->post_content, 'jblund_dealer_login')
        )) {

            wp_enqueue_style(
                'jblund-dealer-portal',
                JBLUND_DEALERS_PLUGIN_URL . 'modules/dealer-portal/assets/css/dashboard.css',
                array(),
                '1.3.0'
            );
        }
    }

    /**
     * Dealer Dashboard Shortcode
     */
    public function dashboard() {
        // Check if dealer portal classes are loaded (they won't be in admin/builder context)
        if (!class_exists('JBLund\DealerPortal\Dealer_Role')) {
            // Show builder-friendly placeholder when classes aren't loaded
            return '<div style="padding: 40px; background: #f0f0f0; border: 2px dashed #999; border-radius: 8px; text-align: center;">'
                 . '<h3 style="margin-top: 0; color: #333;">📊 Dealer Dashboard</h3>'
                 . '<p style="color: #666; margin-bottom: 0;">This shortcode displays the dealer portal dashboard.<br>'
                 . 'The full interface will be visible on the frontend to logged-in dealers.</p>'
                 . '</div>';
        }

        ob_start();
        include JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/templates/dealer-dashboard.php';
        return ob_get_clean();
    }

    /**
     * Dealer Profile Shortcode
     */
    public function profile() {
        // Check if dealer portal classes are loaded (they won't be in admin/builder context)
        if (!class_exists('JBLund\DealerPortal\Dealer_Role')) {
            // Show builder-friendly placeholder when classes aren't loaded
            return '<div style="padding: 40px; background: #f0f0f0; border: 2px dashed #999; border-radius: 8px; text-align: center;">'
                 . '<h3 style="margin-top: 0; color: #333;">👤 Dealer Profile</h3>'
                 . '<p style="color: #666; margin-bottom: 0;">This shortcode displays the dealer profile editor.<br>'
                 . 'The full interface will be visible on the frontend to logged-in dealers.</p>'
                 . '</div>';
        }

        ob_start();
        include JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/templates/dealer-profile.php';
        return ob_get_clean();
    }

    /**
     * Dealer Login Shortcode
     */
    public function login() {
        // Check if dealer portal classes are loaded (they won't be in admin/builder context)
        if (!class_exists('JBLund\DealerPortal\Dealer_Role')) {
            // Show builder-friendly placeholder when classes aren't loaded
            return '<div style="padding: 40px; background: #f0f0f0; border: 2px dashed #999; border-radius: 8px; text-align: center;">'
                 . '<h3 style="margin-top: 0; color: #333;">🔐 Dealer Login</h3>'
                 . '<p style="color: #666; margin-bottom: 0;">This shortcode displays the dealer login form.<br>'
                 . 'The full login interface will be visible on the frontend.</p>'
                 . '</div>';
        }

        ob_start();
        include JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/templates/dealer-login.php';
        return ob_get_clean();
    }
}
