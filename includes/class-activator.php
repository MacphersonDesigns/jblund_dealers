<?php
/**
 * Plugin Activation Class
 *
 * Handles plugin activation tasks such as creating custom post types,
 * flushing rewrite rules, and creating dealer role.
 *
 * @package    JBLund_Dealers
 * @subpackage Includes
 * @since      2.0.0
 */

namespace JBLund\Includes;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Activator
 *
 * Handles plugin activation
 */
class Activator {

    /**
     * Run activation tasks
     */
    public static function activate() {
        self::register_post_types();
        self::create_dealer_role();
        self::flush_rewrite_rules();
    }

    /**
     * Register custom post types
     */
    private static function register_post_types() {
        // Trigger the post type registration from the modules
        $post_type = new \JBLund\Core\Post_Type();
        $post_type->register_dealer_post_type();

        $registration_post_type = new \JBLund\Core\Registration_Post_Type();
        $registration_post_type->register_dealer_registration_post_type();
    }

    /**
     * Create dealer role
     */
    private static function create_dealer_role() {
        if (class_exists('JBLund\DealerPortal\Dealer_Role')) {
            \JBLund\DealerPortal\Dealer_Role::create_role();
        }
    }

    /**
     * Flush WordPress rewrite rules
     */
    private static function flush_rewrite_rules() {
        flush_rewrite_rules();
    }
}
