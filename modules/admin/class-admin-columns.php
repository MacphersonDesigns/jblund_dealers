<?php
/**
 * Admin Columns for Dealer List
 *
 * Customizes the columns displayed in the WordPress admin dealer list
 *
 * @package JBLund_Dealers
 * @subpackage Admin
 */

namespace JBLund\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Columns Class
 *
 * Manages custom columns in the dealer admin list table
 */
class Admin_Columns {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter('manage_dealer_posts_columns', array($this, 'add_dealer_columns'));
        add_action('manage_dealer_posts_custom_column', array($this, 'populate_dealer_columns'), 10, 2);
    }

    /**
     * Add custom columns to dealer admin list
     */
    public function add_dealer_columns($columns) {
        // Remove date and add our custom columns
        unset($columns['date']);

        $columns['company_address'] = __('Address', 'jblund-dealers');
        $columns['company_phone'] = __('Phone', 'jblund-dealers');
        $columns['company_website'] = __('Website', 'jblund-dealers');
        $columns['linked_user'] = __('User Account', 'jblund-dealers');
        $columns['coordinates'] = __('Coordinates', 'jblund-dealers');
        $columns['services'] = __('Services', 'jblund-dealers');
        $columns['sublocations'] = __('Sub-Locations', 'jblund-dealers');
        $columns['date'] = __('Date', 'jblund-dealers');

        return $columns;
    }

    /**
     * Populate custom columns in dealer admin list
     */
    public function populate_dealer_columns($column, $post_id) {
        switch ($column) {
            case 'company_address':
                $address = get_post_meta($post_id, '_dealer_company_address', true);
                echo esc_html($address ? wp_trim_words($address, 6) : '—');
                break;

            case 'company_phone':
                $phone = get_post_meta($post_id, '_dealer_company_phone', true);
                echo esc_html($phone ?: '—');
                break;

            case 'company_website':
                $website = get_post_meta($post_id, '_dealer_website', true);
                if ($website) {
                    echo '<a href="' . esc_url($website) . '" target="_blank">' . esc_html(wp_trim_words($website, 3)) . '</a>';
                } else {
                    echo '—';
                }
                break;

            case 'linked_user':
                $user_id = get_post_meta($post_id, '_dealer_linked_user_id', true);
                if ($user_id) {
                    $user = get_userdata($user_id);
                    if ($user) {
                        echo '<div style="line-height: 1.5;">';
                        echo '<strong><a href="' . esc_url(get_edit_user_link($user->ID)) . '" target="_blank">' . esc_html($user->display_name) . '</a></strong><br/>';
                        echo '<small style="color: #666;">' . esc_html($user->user_email) . '</small>';
                        echo '</div>';
                    } else {
                        echo '<span style="color: #999;">⚠️ User not found</span>';
                    }
                } else {
                    echo '<span style="color: #999;">—</span>';
                }
                break;

            case 'coordinates':
                $latitude = get_post_meta($post_id, '_dealer_latitude', true);
                $longitude = get_post_meta($post_id, '_dealer_longitude', true);
                if ($latitude && $longitude) {
                    echo '<span title="' . esc_attr($latitude . ', ' . $longitude) . '">📍</span>';
                } else {
                    echo '—';
                }
                break;

            case 'services':
                $services = array();
                if (get_post_meta($post_id, '_dealer_docks', true) == '1') $services[] = 'Docks';
                if (get_post_meta($post_id, '_dealer_lifts', true) == '1') $services[] = 'Lifts';
                if (get_post_meta($post_id, '_dealer_trailers', true) == '1') $services[] = 'Trailers';
                echo esc_html($services ? implode(', ', $services) : '—');
                break;

            case 'sublocations':
                $sublocations = get_post_meta($post_id, '_dealer_sublocations', true);
                if (is_array($sublocations) && !empty($sublocations)) {
                    echo esc_html(count($sublocations));
                } else {
                    echo '0';
                }
                break;
        }
    }
}
