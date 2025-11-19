<?php
/**
 * Shortcode Handler Class
 *
 * Handles the rendering of the [jblund_dealers] shortcode for displaying dealer information
 * on the frontend. Supports multiple layout options (grid, list, compact) and displays
 * dealer contact information, services, and sub-locations.
 *
 * @package    JBLund_Dealers
 * @subpackage Frontend
 * @since      1.0.0
 */

namespace JBLund\Frontend;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Shortcode
 *
 * Registers and renders the jblund_dealers shortcode for displaying dealer listings
 * with customizable layouts and service information.
 */
class Shortcode {

    /**
     * Constructor
     *
     * Registers the shortcode with WordPress.
     */
    public function __construct() {
        add_shortcode('jblund_dealers', array($this, 'render'));
    }

    /**
     * Render the dealers shortcode
     *
     * Displays a list of dealers with their contact information, services, and sub-locations.
     * Supports multiple layout options and customizable display settings.
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output of the dealers listing
     */
    public function render($atts) {
        $options = get_option('jblund_dealers_settings');
        $default_layout = isset($options['default_layout']) ? $options['default_layout'] : 'grid';
        $use_icons = isset($options['use_icons']) ? $options['use_icons'] : '1';

        $atts = shortcode_atts(array(
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'layout' => $default_layout,
        ), $atts, 'jblund_dealers');

        $args = array(
            'post_type' => 'dealer',
            'posts_per_page' => intval($atts['posts_per_page']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
        );

        $dealers = new \WP_Query($args);

        if (!$dealers->have_posts()) {
            return '<p>' . __('No dealers found.', 'jblund-dealers') . '</p>';
        }

        $layout_class = 'jblund-dealers-' . esc_attr($atts['layout']);

        ob_start();
        ?>
        <div class="<?php echo $layout_class; ?>">
            <?php while ($dealers->have_posts()) : $dealers->the_post(); ?>
                <?php
                $post_id = get_the_ID();
                $company_name = get_the_title(); // Use post title as company name
                $company_address = get_post_meta($post_id, '_dealer_company_address', true);
                $company_phone = get_post_meta($post_id, '_dealer_company_phone', true);
                $website = get_post_meta($post_id, '_dealer_website', true);
                $latitude = get_post_meta($post_id, '_dealer_latitude', true);
                $longitude = get_post_meta($post_id, '_dealer_longitude', true);
                $custom_map_link = get_post_meta($post_id, '_dealer_custom_map_link', true);
                $docks = get_post_meta($post_id, '_dealer_docks', true);
                $lifts = get_post_meta($post_id, '_dealer_lifts', true);
                $trailers = get_post_meta($post_id, '_dealer_trailers', true);
                $sublocations = get_post_meta($post_id, '_dealer_sublocations', true);
                ?>
                <div class="dealer-card">
                    <?php if ($atts['layout'] == 'list') : ?>
                        <!-- List Layout Structure -->
                        <div class="dealer-name-column">
                            <h3><?php echo esc_html($company_name); ?></h3>
                        </div>
                        <div class="dealer-content-column">
                            <?php if ($company_address) : ?>
                                <div class="dealer-contact-item dealer-address-item">
                                    <span class="contact-icon">📍</span>
                                    <?php
                                    $map_link = $this->generate_map_link($company_address, $latitude, $longitude, $custom_map_link);
                                    if ($map_link) : ?>
                                        <a href="<?php echo esc_url($map_link); ?>" target="_blank" rel="noopener noreferrer" class="dealer-address-link">
                                            <?php echo esc_html($company_address); ?>
                                        </a>
                                    <?php else : ?>
                                        <span><?php echo esc_html($company_address); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($company_phone) : ?>
                                <div class="dealer-contact-item dealer-phone-item">
                                    <span class="contact-icon">📞</span>
                                    <a href="tel:<?php echo esc_attr($company_phone); ?>"><?php echo esc_html($company_phone); ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="dealer-website-column">
                            <?php if ($website) : ?>
                                <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer" class="dealer-website-button">
                                    <span class="website-icon">🌐</span> <?php _e('Visit Website', 'jblund-dealers'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="dealer-services-column">
                            <div class="dealer-services-icons service-icons">
                                <span class="service-icon service-docks <?php echo ($docks == '1') ? 'active' : ''; ?>" title="<?php _e('Docks', 'jblund-dealers'); ?>">
                                    <span class="icon">🚢</span>
                                    <span class="label"><?php _e('Docks', 'jblund-dealers'); ?></span>
                                </span>
                                <span class="service-icon service-lifts <?php echo ($lifts == '1') ? 'active' : ''; ?>" title="<?php _e('Lifts', 'jblund-dealers'); ?>">
                                    <span class="icon">⚓</span>
                                    <span class="label"><?php _e('Lifts', 'jblund-dealers'); ?></span>
                                </span>
                                <span class="service-icon service-trailers <?php echo ($trailers == '1') ? 'active' : ''; ?>" title="<?php _e('Trailers', 'jblund-dealers'); ?>">
                                    <span class="icon">🚛</span>
                                    <span class="label"><?php _e('Trailers', 'jblund-dealers'); ?></span>
                                </span>
                            </div>
                        </div>
                    <?php else : ?>
                        <!-- Grid/Compact Layout Structure -->
                        <div class="dealer-card-header">
                            <h3 class="dealer-name"><?php echo esc_html($company_name); ?></h3>
                        </div>
                        <div class="dealer-card-body">
                            <div class="dealer-contact-info">
                                <?php if ($company_address) : ?>
                                    <p class="dealer-address">
                                        <strong><?php _e('Address:', 'jblund-dealers'); ?></strong><br>
                                        <?php
                                        $map_link = $this->generate_map_link($company_address, $latitude, $longitude, $custom_map_link);
                                        if ($map_link) : ?>
                                            <a href="<?php echo esc_url($map_link); ?>" target="_blank" rel="noopener noreferrer" class="dealer-address-link">
                                                <?php echo nl2br(esc_html($company_address)); ?>
                                            </a>
                                        <?php else : ?>
                                            <?php echo nl2br(esc_html($company_address)); ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ($company_phone) : ?>
                                    <p class="dealer-phone"><strong><?php _e('Phone:', 'jblund-dealers'); ?></strong> <a href="tel:<?php echo esc_attr($company_phone); ?>"><?php echo esc_html($company_phone); ?></a></p>
                                <?php endif; ?>
                            </div>

                            <?php if ($website) : ?>
                                <div class="dealer-website">
                                    <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer" class="dealer-website-button">
                                        <?php _e('Visit Website', 'jblund-dealers'); ?>
                                        <span class="website-icon">↗</span>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="dealer-services">
                                <strong><?php _e('Services:', 'jblund-dealers'); ?></strong>
                                <?php if ($use_icons == '1') : ?>
                                    <div class="dealer-services-icons">
                                        <span class="service-icon service-docks <?php echo ($docks == '1') ? 'active' : ''; ?>" title="<?php _e('Docks', 'jblund-dealers'); ?>">
                                            <span class="icon">🚢</span>
                                            <span class="label"><?php _e('Docks', 'jblund-dealers'); ?></span>
                                        </span>
                                        <span class="service-icon service-lifts <?php echo ($lifts == '1') ? 'active' : ''; ?>" title="<?php _e('Lifts', 'jblund-dealers'); ?>">
                                            <span class="icon">⚓</span>
                                            <span class="label"><?php _e('Lifts', 'jblund-dealers'); ?></span>
                                        </span>
                                        <span class="service-icon service-trailers <?php echo ($trailers == '1') ? 'active' : ''; ?>" title="<?php _e('Trailers', 'jblund-dealers'); ?>">
                                            <span class="icon">🚛</span>
                                            <span class="label"><?php _e('Trailers', 'jblund-dealers'); ?></span>
                                        </span>
                                    </div>
                                <?php else : ?>
                                    <?php if ($docks || $lifts || $trailers) : ?>
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
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Sub-locations -->
                    <?php if (!empty($sublocations) && is_array($sublocations)) : ?>
                            <div class="dealer-sublocations">
                                <h4><?php _e('Additional Locations:', 'jblund-dealers'); ?></h4>
                                <?php foreach ($sublocations as $location) : ?>
                                    <div class="dealer-sublocation">
                                        <!-- Column 1: Name -->
                                        <div class="sublocation-name-col">
                                            <?php if (!empty($location['name'])) : ?>
                                                <h5><?php echo esc_html($location['name']); ?></h5>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Column 2: Address & Phone -->
                                        <div class="sublocation-contact-col">
                                            <?php if (!empty($location['address'])) : ?>
                                                <div class="sublocation-address">
                                                    <span class="contact-icon">📍</span>
                                                    <?php echo esc_html($location['address']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($location['phone'])) : ?>
                                                <div class="sublocation-phone">
                                                    <span class="contact-icon">📞</span>
                                                    <a href="tel:<?php echo esc_attr($location['phone']); ?>"><?php echo esc_html($location['phone']); ?></a>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Column 3: Website -->
                                        <div class="sublocation-website-col">
                                            <?php if (!empty($location['website'])) : ?>
                                                <a href="<?php echo esc_url($location['website']); ?>" target="_blank" rel="noopener noreferrer" class="dealer-website-button sublocation-website-btn">
                                                    <span class="website-icon">🌐</span> <?php _e('Visit Website', 'jblund-dealers'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Column 4: Services -->
                                        <div class="sublocation-services-col">
                                            <?php if ($use_icons == '1') : ?>
                                                <div class="dealer-services-icons sublocation-services-icons">
                                                    <span class="service-icon service-docks <?php echo (!empty($location['docks']) && $location['docks'] == '1') ? 'active' : ''; ?>" title="<?php _e('Docks', 'jblund-dealers'); ?>">
                                                        <span class="icon">🚢</span>
                                                        <span class="label"><?php _e('Docks', 'jblund-dealers'); ?></span>
                                                    </span>
                                                    <span class="service-icon service-lifts <?php echo (!empty($location['lifts']) && $location['lifts'] == '1') ? 'active' : ''; ?>" title="<?php _e('Lifts', 'jblund-dealers'); ?>">
                                                        <span class="icon">⚓</span>
                                                        <span class="label"><?php _e('Lifts', 'jblund-dealers'); ?></span>
                                                    </span>
                                                    <span class="service-icon service-trailers <?php echo (!empty($location['trailers']) && $location['trailers'] == '1') ? 'active' : ''; ?>" title="<?php _e('Trailers', 'jblund-dealers'); ?>">
                                                        <span class="icon">🚛</span>
                                                        <span class="label"><?php _e('Trailers', 'jblund-dealers'); ?></span>
                                                    </span>
                                                </div>
                                            <?php else : ?>
                                                <?php if (!empty($location['docks']) || !empty($location['lifts']) || !empty($location['trailers'])) : ?>
                                                    <ul>
                                                        <?php if (!empty($location['docks']) && $location['docks'] == '1') : ?>
                                                            <li><?php _e('Docks', 'jblund-dealers'); ?></li>
                                                        <?php endif; ?>
                                                        <?php if (!empty($location['lifts']) && $location['lifts'] == '1') : ?>
                                                            <li><?php _e('Lifts', 'jblund-dealers'); ?></li>
                                                        <?php endif; ?>
                                                        <?php if (!empty($location['trailers']) && $location['trailers'] == '1') : ?>
                                                            <li><?php _e('Trailers', 'jblund-dealers'); ?></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                <?php else : ?>
                                                    <p><em><?php _e('No services specified', 'jblund-dealers'); ?></em></p>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Generate a map link based on available location data
     *
     * Generates a Google Maps link with priority given to custom links, then coordinates,
     * then address-based search.
     *
     * @param string $address       The dealer's address
     * @param string $latitude      Optional latitude coordinate
     * @param string $longitude     Optional longitude coordinate
     * @param string $custom_map_link Optional custom map URL
     * @return string The generated map URL or empty string if no location data available
     */
    private function generate_map_link($address, $latitude = '', $longitude = '', $custom_map_link = '') {
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
}
