<?php
/**
 * Helper Functions
 *
 * Global helper functions for use throughout the plugin and themes.
 * These functions provide convenient access to common plugin functionality.
 *
 * @package    JBLund_Dealers
 * @subpackage Includes
 * @since      1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper function to get assigned portal page URL
 *
 * @param string $page_type One of: 'login', 'dashboard', 'profile', 'nda'
 * @return string|false Page URL or false if not assigned
 */
function jblund_get_portal_page_url($page_type) {
    $portal_pages = get_option('jblund_dealers_portal_pages', array());

    if (empty($portal_pages[$page_type])) {
        return false;
    }

    $page_id = intval($portal_pages[$page_type]);
    return get_permalink($page_id);
}

/**
 * Helper function to get assigned portal page ID
 *
 * @param string $page_type One of: 'login', 'dashboard', 'profile', 'nda'
 * @return int|false Page ID or false if not assigned
 */
function jblund_get_portal_page_id($page_type) {
    $portal_pages = get_option('jblund_dealers_portal_pages', array());

    if (empty($portal_pages[$page_type])) {
        return false;
    }

    return intval($portal_pages[$page_type]);
}

/**
 * Helper function to get dealer post data linked to user account
 *
 * @param int $user_id User ID (defaults to current user)
 * @return WP_Post|false Dealer post object or false if not found
 */
function jblund_get_user_dealer_post($user_id = null) {
    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }

    if (empty($user_id)) {
        return false;
    }

    // Query for dealer posts where this user is linked
    $args = array(
        'post_type' => 'dealer',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_dealer_linked_user_id',
                'value' => $user_id,
                'compare' => '='
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        return $query->posts[0];
    }

    return false;
}

/**
 * Helper function to get dealer representative info
 *
 * @param int $user_id User ID (defaults to current user)
 * @return array|false Array with rep info or false if not found
 */
function jblund_get_dealer_representative($user_id = null) {
    $dealer_post = jblund_get_user_dealer_post($user_id);

    if (!$dealer_post) {
        return false;
    }

    // Get representative info from settings
    $settings = get_option('jblund_dealers_settings', array());

    return array(
        'name' => isset($settings['representative_name']) ? $settings['representative_name'] : 'Jim Johnson',
        'email' => isset($settings['representative_email']) ? $settings['representative_email'] : 'jim@jblund.com',
        'phone' => isset($settings['representative_phone']) ? $settings['representative_phone'] : '(555) 123-4567',
    );
}
