<?php
/**
 * JBLund Dealers Uninstall Script
 *
 * This file is executed when the plugin is deleted from the WordPress admin.
 * It cleans up all plugin data from the database.
 */

// Exit if uninstall not called from WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Load dealer role class for cleanup
require_once plugin_dir_path(__FILE__) . 'modules/dealer-portal/classes/class-dealer-role.php';

/**
 * Delete all dealers and their meta data
 */
function jblund_dealers_uninstall_cleanup() {
    global $wpdb;

    // Get all dealer posts
    $dealers = get_posts(array(
        'post_type' => 'dealer',
        'posts_per_page' => -1,
        'post_status' => 'any',
    ));

    // Delete each dealer and its meta data
    foreach ($dealers as $dealer) {
        // Delete all post meta
        delete_post_meta($dealer->ID, '_dealer_company_name');
        delete_post_meta($dealer->ID, '_dealer_company_address');
        delete_post_meta($dealer->ID, '_dealer_company_phone');
        delete_post_meta($dealer->ID, '_dealer_website');
        delete_post_meta($dealer->ID, '_dealer_docks');
        delete_post_meta($dealer->ID, '_dealer_lifts');
        delete_post_meta($dealer->ID, '_dealer_trailers');
        delete_post_meta($dealer->ID, '_dealer_sublocations');

        // Force delete the post (bypass trash)
        wp_delete_post($dealer->ID, true);
    }

    // Clean up any orphaned meta data
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_dealer_%'");

    // Clean up dealer portal user meta
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key IN ('_dealer_nda_acceptance', '_dealer_nda_accepted', '_dealer_nda_pdf_path')");

    // Remove dealer role
    if (class_exists('JBLund\DealerPortal\Dealer_Role')) {
        JBLund\DealerPortal\Dealer_Role::remove_role();
    }

    // Delete NDA acceptance page (if it exists)
    $nda_page = get_page_by_path('dealer-nda-acceptance');
    if ($nda_page) {
        wp_delete_post($nda_page->ID, true);
    }

    // Note: We don't delete the post type registration as it's handled by WordPress
    // The rewrite rules will be flushed automatically when another plugin is activated
}

// Run the cleanup
jblund_dealers_uninstall_cleanup();
