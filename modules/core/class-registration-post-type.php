<?php
/**
 * Dealer Registration Post Type
 *
 * Handles registration of the 'dealer_registration' custom post type
 * for managing dealer application submissions.
 *
 * @package JBLund_Dealers
 * @subpackage Core
 * @since 1.3.0
 */

namespace JBLund\Core;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registration Post Type Class
 *
 * Registers and manages the dealer_registration custom post type
 */
class Registration_Post_Type {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'register_dealer_registration_post_type'));
    }

    /**
     * Register the Dealer Registration custom post type
     *
     * This is used internally for the approval workflow.
     * Not visible in admin menu - accessed via custom list table.
     */
    public function register_dealer_registration_post_type() {
        $labels = array(
            'name'                  => _x('Dealer Registrations', 'Post type general name', 'jblund-dealers'),
            'singular_name'         => _x('Dealer Registration', 'Post type singular name', 'jblund-dealers'),
            'menu_name'             => _x('Registrations', 'Admin Menu text', 'jblund-dealers'),
            'name_admin_bar'        => _x('Dealer Registration', 'Add New on Toolbar', 'jblund-dealers'),
            'add_new'               => __('Add New', 'jblund-dealers'),
            'add_new_item'          => __('Add New Registration', 'jblund-dealers'),
            'new_item'              => __('New Registration', 'jblund-dealers'),
            'edit_item'             => __('Edit Registration', 'jblund-dealers'),
            'view_item'             => __('View Registration', 'jblund-dealers'),
            'all_items'             => __('All Registrations', 'jblund-dealers'),
            'search_items'          => __('Search Registrations', 'jblund-dealers'),
            'not_found'             => __('No registrations found.', 'jblund-dealers'),
            'not_found_in_trash'    => __('No registrations found in Trash.', 'jblund-dealers'),
        );

        $args = array(
            'labels'                => $labels,
            'description'           => __('Dealer registration submissions for approval workflow', 'jblund-dealers'),
            'public'                => false, // Not public-facing
            'publicly_queryable'    => false,
            'show_ui'               => false, // Hidden from admin menu (uses custom list table)
            'show_in_menu'          => false,
            'query_var'             => false,
            'rewrite'               => false,
            'capability_type'       => 'post',
            'has_archive'           => false,
            'hierarchical'          => false,
            'supports'              => array('title'), // Only need title (company name)
            'show_in_rest'          => false, // No REST API needed
        );

        register_post_type('dealer_registration', $args);
    }
}
