<?php
/**
 * List Layout Renderer
 *
 * Renders dealers in a horizontal 4-column row layout.
 * Each dealer displays as a row with name, contact, website, and services.
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
 * Class List_Layout
 *
 * List layout renderer - horizontal rows with 4 columns
 */
class List_Layout extends Layout_Base {

    /**
     * Render the list layout
     *
     * @param \WP_Query $dealers The dealers query object
     * @param array $atts Shortcode attributes
     */
    public function render($dealers, $atts) {
        ?>
        <div class="jblund-dealers-list">
            <?php while ($dealers->have_posts()) : $dealers->the_post(); ?>
                <?php
                $dealer = $this->get_dealer_data(get_the_ID());
                ?>
                <div class="dealer-card">
                    <!-- Column 1: Dealer Name -->
                    <div class="dealer-name-column">
                        <h3><?php echo esc_html($dealer['company_name']); ?></h3>
                    </div>

                    <!-- Column 2: Contact Info -->
                    <div class="dealer-content-column">
                        <?php $this->render_contact_info($dealer); ?>
                    </div>

                    <!-- Column 3: Website -->
                    <div class="dealer-website-column">
                        <?php $this->render_website_button($dealer['website']); ?>
                    </div>

                    <!-- Column 4: Services -->
                    <div class="dealer-services-column">
                        <?php $this->render_service_icons($dealer['docks'], $dealer['lifts'], $dealer['trailers']); ?>
                    </div>

                    <!-- Sublocations (full width inside card) -->
                    <?php $this->render_sublocations_list($dealer['sublocations']); ?>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
    }

    /**
     * Render contact information
     *
     * @param array $dealer Dealer data
     */
    private function render_contact_info($dealer) {
        ?>
        <?php if ($dealer['company_address']) : ?>
            <div class="dealer-contact-item dealer-address-item">
                <span class="contact-icon">📍</span>
                <?php
                $map_link = $this->generate_map_link(
                    $dealer['company_address'],
                    $dealer['latitude'],
                    $dealer['longitude'],
                    $dealer['custom_map_link']
                );
                if ($map_link) : ?>
                    <a href="<?php echo esc_url($map_link); ?>" target="_blank" rel="noopener noreferrer" class="dealer-address-link">
                        <?php echo esc_html($dealer['company_address']); ?>
                    </a>
                <?php else : ?>
                    <span><?php echo esc_html($dealer['company_address']); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($dealer['company_phone']) : ?>
            <div class="dealer-contact-item dealer-phone-item">
                <span class="contact-icon">📞</span>
                <a href="tel:<?php echo esc_attr($dealer['company_phone']); ?>"><?php echo esc_html($dealer['company_phone']); ?></a>
            </div>
        <?php endif; ?>
        <?php
    }

    /**
     * Render website button
     *
     * @param string $website Website URL
     */
    private function render_website_button($website) {
        if ($website) : ?>
            <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer" class="dealer-website-button">
                <?php _e('Visit Website', 'jblund-dealers'); ?>
            </a>
        <?php endif;
    }

    /**
     * Render sublocations for list layout
     *
     * @param array $sublocations Array of sublocation data
     */
    private function render_sublocations_list($sublocations) {
        if (!empty($sublocations) && is_array($sublocations)) : ?>
            <div class="dealer-sublocations">
                <h4><?php _e('Additional Locations:', 'jblund-dealers'); ?></h4>
                <?php foreach ($sublocations as $location) : ?>
                    <div class="dealer-sublocation">
                        <!-- Column 1: Sublocation Name -->
                        <div class="sublocation-name-col">
                            <?php if (!empty($location['name'])) : ?>
                                <h5><?php echo esc_html($location['name']); ?></h5>
                            <?php endif; ?>
                        </div>

                        <!-- Column 2: Contact Info -->
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
                                    <?php _e('Visit Website', 'jblund-dealers'); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Column 4: Services -->
                        <div class="sublocation-services-col">
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
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif;
    }
}
