<?php
/**
 * Layout Base Class
 *
 * Abstract base class for all dealer layout renderers. Provides shared
 * functionality for retrieving dealer data and generating map links.
 *
 * @package    JBLund_Dealers
 * @subpackage Frontend\Shortcodes
 * @since      2.0.0
 */

namespace JBLund\Frontend\Shortcodes;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Abstract Class Layout_Base
 *
 * Base functionality for all layout renderers
 */
abstract class Layout_Base {

    /**
     * Use icons for services display
     *
     * @var string
     */
    protected $use_icons;

    /**
     * Constructor
     */
    public function __construct() {
        $options = get_option('jblund_dealers_settings');
        $this->use_icons = isset($options['use_icons']) ? $options['use_icons'] : '1';
    }

    /**
     * Render the layout
     *
     * @param \WP_Query $dealers The dealers query object
     * @param array $atts Shortcode attributes
     */
    abstract public function render($dealers, $atts);

    /**
     * Get dealer data for a post
     *
     * @param int $post_id The dealer post ID
     * @return array Dealer data
     */
    protected function get_dealer_data($post_id) {
        return array(
            'company_name' => get_the_title($post_id),
            'company_address' => get_post_meta($post_id, '_dealer_company_address', true),
            'company_phone' => get_post_meta($post_id, '_dealer_company_phone', true),
            'website' => get_post_meta($post_id, '_dealer_website', true),
            'latitude' => get_post_meta($post_id, '_dealer_latitude', true),
            'longitude' => get_post_meta($post_id, '_dealer_longitude', true),
            'custom_map_link' => get_post_meta($post_id, '_dealer_custom_map_link', true),
            'docks' => get_post_meta($post_id, '_dealer_docks', true),
            'lifts' => get_post_meta($post_id, '_dealer_lifts', true),
            'trailers' => get_post_meta($post_id, '_dealer_trailers', true),
            'sublocations' => get_post_meta($post_id, '_dealer_sublocations', true),
        );
    }

    /**
     * Generate a map link based on available location data
     *
     * @param string $address The dealer's address
     * @param string $latitude Optional latitude coordinate
     * @param string $longitude Optional longitude coordinate
     * @param string $custom_map_link Optional custom map URL
     * @return string The generated map URL or empty string if no location data available
     */
    protected function generate_map_link($address, $latitude = '', $longitude = '', $custom_map_link = '') {
        // Priority 1: Use custom map link if provided
        if (!empty($custom_map_link)) {
            return $custom_map_link;
        }

        // Priority 2: Use coordinates for more precise mapping
        if (!empty($latitude) && !empty($longitude)) {
            return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($latitude . ',' . $longitude);
        }

        // Priority 3: Use address for general mapping
        if (!empty($address)) {
            return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address);
        }

        return '';
    }

    /**
     * Render service icons
     *
     * @param string $docks Whether docks are available
     * @param string $lifts Whether lifts are available
     * @param string $trailers Whether trailers are available
     */
    protected function render_service_icons($docks, $lifts, $trailers) {
        $icons_url = JBLUND_DEALERS_PLUGIN_URL . 'assets/icons/';
        ?>
        <div class="dealer-services-icons">
            <span class="service-icon service-docks <?php echo ($docks == '1') ? 'active' : ''; ?>" title="<?php _e('Docks', 'jblund-dealers'); ?>">
                <img src="<?php echo esc_url($icons_url . 'dock.svg'); ?>" alt="" aria-hidden="true" class="icon" width="40" height="40" />
                <span class="label"><?php _e('Docks', 'jblund-dealers'); ?></span>
            </span>
            <span class="service-icon service-lifts <?php echo ($lifts == '1') ? 'active' : ''; ?>" title="<?php _e('Lifts', 'jblund-dealers'); ?>">
                <img src="<?php echo esc_url($icons_url . 'lift.svg'); ?>" alt="" aria-hidden="true" class="icon" width="40" height="40" />
                <span class="label"><?php _e('Lifts', 'jblund-dealers'); ?></span>
            </span>
            <span class="service-icon service-trailers <?php echo ($trailers == '1') ? 'active' : ''; ?>" title="<?php _e('Trailers', 'jblund-dealers'); ?>">
                <img src="<?php echo esc_url($icons_url . 'trailer.svg'); ?>" alt="" aria-hidden="true" class="icon" width="40" height="40" />
                <span class="label"><?php _e('Trailers', 'jblund-dealers'); ?></span>
            </span>
        </div>
        <?php
    }

    /**
     * Render service list
     *
     * @param string $docks Whether docks are available
     * @param string $lifts Whether lifts are available
     * @param string $trailers Whether trailers are available
     */
    protected function render_service_list($docks, $lifts, $trailers) {
        if ($docks || $lifts || $trailers) : ?>
            <ul>
                <?php if ($docks == '1') : ?>
                    <li><?php _e('Docks', 'jblund-dealers'); ?></li>
                <?php endif; ?>
                <?php if ($lifts == '1') : ?>
                    <li><?php _e('Lifts', 'jblund-dealers'); ?></li>
                <?php endif; ?>
                <?php if ($trailers == '1') : ?>
                    <li><?php _e('Trailers', 'jblund-dealers'); ?></li>
                <?php endif; ?>
            </ul>
        <?php else : ?>
            <p><em><?php _e('No services specified', 'jblund-dealers'); ?></em></p>
        <?php endif;
    }
}
