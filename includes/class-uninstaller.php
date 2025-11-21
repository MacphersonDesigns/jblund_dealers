<?php
/**
 * Plugin Uninstaller Class
 *
 * Handles complete plugin uninstallation and data cleanup.
 * This runs when the plugin is DELETED (not just deactivated).
 * Removes all posts, post meta, options, user roles, and other plugin data.
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
 * Class Uninstaller
 *
 * Handles complete plugin data removal
 */
class Uninstaller {

    /**
     * Run uninstallation cleanup
     */
    public static function uninstall() {
        self::delete_dealer_posts();
        self::delete_registration_posts();
        self::delete_plugin_options();
        self::delete_user_meta();
        self::delete_dealer_role();
        self::delete_portal_pages();
        self::cleanup_orphaned_data();
    }

    /**
     * Delete all dealer posts and their meta data
     */
    private static function delete_dealer_posts() {
        $dealers = get_posts(array(
            'post_type' => 'dealer',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ));

        foreach ($dealers as $dealer) {
            // Delete all dealer-specific post meta
            delete_post_meta($dealer->ID, '_dealer_company_name');
            delete_post_meta($dealer->ID, '_dealer_company_address');
            delete_post_meta($dealer->ID, '_dealer_company_phone');
            delete_post_meta($dealer->ID, '_dealer_website');
            delete_post_meta($dealer->ID, '_dealer_latitude');
            delete_post_meta($dealer->ID, '_dealer_longitude');
            delete_post_meta($dealer->ID, '_dealer_custom_map_link');
            delete_post_meta($dealer->ID, '_dealer_docks');
            delete_post_meta($dealer->ID, '_dealer_lifts');
            delete_post_meta($dealer->ID, '_dealer_trailers');
            delete_post_meta($dealer->ID, '_dealer_sublocations');
            delete_post_meta($dealer->ID, '_dealer_linked_user_id');

            // Force delete the post (bypass trash)
            wp_delete_post($dealer->ID, true);
        }
    }

    /**
     * Delete all dealer registration posts and their meta data
     */
    private static function delete_registration_posts() {
        $registrations = get_posts(array(
            'post_type' => 'dealer_registration',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ));

        foreach ($registrations as $registration) {
            // Force delete the post (bypass trash)
            wp_delete_post($registration->ID, true);
        }
    }

    /**
     * Delete all plugin options
     */
    private static function delete_plugin_options() {
        delete_option('jblund_dealers_settings');
        delete_option('jblund_dealers_portal_pages');
        delete_option('jblund_dealers_nda_content');
        delete_option('jblund_registration_messages');
        delete_option('jblund_email_template_approval');
        delete_option('jblund_email_template_rejection');
        delete_option('jblund_email_template_admin_notification');
    }

    /**
     * Delete dealer-related user meta
     */
    private static function delete_user_meta() {
        global $wpdb;

        $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key IN (
            '_dealer_nda_acceptance',
            '_dealer_nda_accepted',
            '_dealer_nda_pdf_path',
            '_dealer_nda_accepted_date'
        )");
    }

    /**
     * Remove dealer user role
     */
    private static function delete_dealer_role() {
        if (class_exists('JBLund\DealerPortal\Dealer_Role')) {
            \JBLund\DealerPortal\Dealer_Role::remove_role();
        }
    }

    /**
     * Delete auto-created portal pages
     */
    private static function delete_portal_pages() {
        // Delete NDA acceptance page (if it exists)
        $nda_page = get_page_by_path('dealer-nda-acceptance');
        if ($nda_page) {
            wp_delete_post($nda_page->ID, true);
        }

        // Delete any other auto-created portal pages
        $portal_page_slugs = array(
            'dealer-login',
            'dealer-dashboard',
            'dealer-profile',
        );

        foreach ($portal_page_slugs as $slug) {
            $page = get_page_by_path($slug);
            if ($page) {
                // Only delete if it was auto-created by plugin (has specific meta)
                $auto_created = get_post_meta($page->ID, '_jblund_auto_created', true);
                if ($auto_created) {
                    wp_delete_post($page->ID, true);
                }
            }
        }
    }

    /**
     * Clean up any orphaned data
     */
    private static function cleanup_orphaned_data() {
        global $wpdb;

        // Clean up any orphaned post meta with dealer prefixes
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_dealer_%'");

        // Clean up any orphaned options with plugin prefix
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'jblund_%'");
    }
}
