<?php
/**
 * Dealer Post Type Registration
 *
 * Handles registration of the custom 'dealer' post type
 *
 * @package JBLund_Dealers
 * @subpackage Core
 */

namespace JBLund\Core;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Post Type Class
 *
 * Registers and manages the dealer custom post type
 */
class Post_Type {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'register_dealer_post_type'));
    }

    /**
     * Register the Dealer custom post type
     */
    public function register_dealer_post_type() {
        $labels = array(
            'name'                  => _x('Dealers', 'Post type general name', 'jblund-dealers'),
            'singular_name'         => _x('Dealer', 'Post type singular name', 'jblund-dealers'),
            'menu_name'             => _x('Dealers', 'Admin Menu text', 'jblund-dealers'),
            'name_admin_bar'        => _x('Dealer', 'Add New on Toolbar', 'jblund-dealers'),
            'add_new'               => __('Add New', 'jblund-dealers'),
            'add_new_item'          => __('Add New Dealer', 'jblund-dealers'),
            'new_item'              => __('New Dealer', 'jblund-dealers'),
            'edit_item'             => __('Edit Dealer', 'jblund-dealers'),
            'view_item'             => __('View Dealer', 'jblund-dealers'),
            'all_items'             => __('All Dealers', 'jblund-dealers'),
            'search_items'          => __('Search Dealers', 'jblund-dealers'),
            'parent_item_colon'     => __('Parent Dealers:', 'jblund-dealers'),
            'not_found'             => __('No dealers found.', 'jblund-dealers'),
            'not_found_in_trash'    => __('No dealers found in Trash.', 'jblund-dealers'),
        );

        $args = array(
            'labels'                => $labels,
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => array('slug' => 'dealer'),
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => false,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-store',
            'supports'              => array('title'),
            'show_in_rest'          => true,
        );

        register_post_type('dealer', $args);
    }
}
