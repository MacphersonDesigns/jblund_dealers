<?php
/**
 * Grid Layout Renderer
 *
 * Renders dealers in a responsive card-based grid layout.
 * Default layout with vertical cards.
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
 * Class Grid_Layout
 *
 * Grid layout renderer - vertical cards in auto-fill grid
 */
class Grid_Layout extends Layout_Base {

    /**
     * Render the grid layout
     *
     * @param \WP_Query $dealers The dealers query object
     * @param array $atts Shortcode attributes
     */
    public function render($dealers, $atts) {
        ?>
        <div class="jblund-dealers-grid">
            <?php while ($dealers->have_posts()) : $dealers->the_post(); ?>
                <?php
                $dealer = $this->get_dealer_data(get_the_ID());
                ?>
                <div class="dealer-card">
                    <div class="dealer-card-header">
                        <h3 class="dealer-name"><?php echo esc_html($dealer['company_name']); ?></h3>
                    </div>
                    <div class="dealer-card-body">
                        <?php $this->render_contact_info($dealer); ?>
                        <?php $this->render_website_button($dealer['website']); ?>
                        <?php $this->render_services($dealer['docks'], $dealer['lifts'], $dealer['trailers']); ?>
                        <?php $this->render_sublocations($dealer['sublocations']); ?>
                    </div>
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
        <div class="dealer-contact-info">
            <?php if ($dealer['company_address']) : ?>
                <p class="dealer-address">
                    <strong><?php _e('Address:', 'jblund-dealers'); ?></strong><br>
                    <?php
                    $map_link = $this->generate_map_link(
                        $dealer['company_address'],
                        $dealer['latitude'],
                        $dealer['longitude'],
                        $dealer['custom_map_link']
                    );
                    if ($map_link) : ?>
                        <a href="<?php echo esc_url($map_link); ?>" target="_blank" rel="noopener noreferrer" class="dealer-address-link">
                            <?php echo nl2br(esc_html($dealer['company_address'])); ?>
                        </a>
                    <?php else : ?>
                        <?php echo nl2br(esc_html($dealer['company_address'])); ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <?php if ($dealer['company_phone']) : ?>
                <p class="dealer-phone">
                    <strong><?php _e('Phone:', 'jblund-dealers'); ?></strong>
                    <a href="tel:<?php echo esc_attr($dealer['company_phone']); ?>"><?php echo esc_html($dealer['company_phone']); ?></a>
                </p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render website button
     *
     * @param string $website Website URL
     */
    private function render_website_button($website) {
        if ($website) : ?>
            <div class="dealer-website">
                <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer" class="dealer-website-button">
                    <?php _e('Visit Website', 'jblund-dealers'); ?>
                    <span class="website-icon">↗</span>
                </a>
            </div>
        <?php endif;
    }

    /**
     * Render services section
     *
     * @param string $docks Docks availability
     * @param string $lifts Lifts availability
     * @param string $trailers Trailers availability
     */
    private function render_services($docks, $lifts, $trailers) {
        ?>
        <div class="dealer-services">
            <strong><?php _e('Services:', 'jblund-dealers'); ?></strong>
            <?php if ($this->use_icons == '1') : ?>
                <?php $this->render_service_icons($docks, $lifts, $trailers); ?>
            <?php else : ?>
                <?php $this->render_service_list($docks, $lifts, $trailers); ?>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render sublocations
     *
     * @param array $sublocations Array of sublocation data
     */
    private function render_sublocations($sublocations) {
        if (!empty($sublocations) && is_array($sublocations)) : ?>
            <hr class="sublocation-divider">
            <div class="dealer-sublocations">
                <h4><?php _e('Additional Locations:', 'jblund-dealers'); ?></h4>
                <?php foreach ($sublocations as $location) : ?>
                    <div class="dealer-sublocation">
                        <div class="sublocation-name-col">
                            <?php if (!empty($location['name'])) : ?>
                                <h5><?php echo esc_html($location['name']); ?></h5>
                            <?php endif; ?>
                        </div>
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
                        <div class="sublocation-website-col">
                            <?php if (!empty($location['website'])) : ?>
                                <a href="<?php echo esc_url($location['website']); ?>" target="_blank" rel="noopener noreferrer" class="dealer-website-button sublocation-website-btn">
                                    <span class="website-icon">🌐</span> <?php _e('Visit Website', 'jblund-dealers'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="sublocation-services-col">
                            <?php if ($this->use_icons == '1') : ?>
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
        <?php endif;
    }
}
