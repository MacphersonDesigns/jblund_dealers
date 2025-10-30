<?php
/**
 * Plugin Name: JBLund Dealers
 * Plugin URI: https://github.com/MacphersonDesigns/jblund_dealers
 * Description: A custom WordPress plugin to store and display dealer information for JBLund Dock's B2B Website
 * Version: 1.0.0
 * Author: Macpherson Designs
 * Author URI: https://github.com/MacphersonDesigns
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: jblund-dealers
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('JBLUND_DEALERS_VERSION', '1.0.0');
define('JBLUND_DEALERS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JBLUND_DEALERS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main JBLund Dealers Plugin Class
 */
class JBLund_Dealers_Plugin {
    
    /**
     * Initialize the plugin
     */
    public function __construct() {
        add_action('init', array($this, 'register_dealer_post_type'));
        add_action('add_meta_boxes', array($this, 'add_dealer_meta_boxes'));
        add_action('save_post_dealer', array($this, 'save_dealer_meta'), 10, 2);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_styles'));
        add_shortcode('jblund_dealers', array($this, 'dealers_shortcode'));
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
    
    /**
     * Add meta boxes for dealer information
     */
    public function add_dealer_meta_boxes() {
        add_meta_box(
            'dealer_info',
            __('Dealer Information', 'jblund-dealers'),
            array($this, 'render_dealer_info_meta_box'),
            'dealer',
            'normal',
            'high'
        );
        
        add_meta_box(
            'dealer_sublocations',
            __('Sub-Locations', 'jblund-dealers'),
            array($this, 'render_sublocations_meta_box'),
            'dealer',
            'normal',
            'default'
        );
    }
    
    /**
     * Render the dealer information meta box
     */
    public function render_dealer_info_meta_box($post) {
        wp_nonce_field('jblund_dealer_meta_box', 'jblund_dealer_meta_box_nonce');
        
        $company_name = get_post_meta($post->ID, '_dealer_company_name', true);
        $company_address = get_post_meta($post->ID, '_dealer_company_address', true);
        $company_phone = get_post_meta($post->ID, '_dealer_company_phone', true);
        $website = get_post_meta($post->ID, '_dealer_website', true);
        $docks = get_post_meta($post->ID, '_dealer_docks', true);
        $lifts = get_post_meta($post->ID, '_dealer_lifts', true);
        $trailers = get_post_meta($post->ID, '_dealer_trailers', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="dealer_company_name"><?php _e('Company Name', 'jblund-dealers'); ?></label></th>
                <td><input type="text" id="dealer_company_name" name="dealer_company_name" value="<?php echo esc_attr($company_name); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="dealer_company_address"><?php _e('Company Address', 'jblund-dealers'); ?></label></th>
                <td><textarea id="dealer_company_address" name="dealer_company_address" rows="3" class="large-text"><?php echo esc_textarea($company_address); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="dealer_company_phone"><?php _e('Company Phone', 'jblund-dealers'); ?></label></th>
                <td><input type="text" id="dealer_company_phone" name="dealer_company_phone" value="<?php echo esc_attr($company_phone); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="dealer_website"><?php _e('Website', 'jblund-dealers'); ?></label></th>
                <td><input type="url" id="dealer_website" name="dealer_website" value="<?php echo esc_url($website); ?>" class="regular-text" placeholder="https://" /></td>
            </tr>
            <tr>
                <th><?php _e('Services Offered', 'jblund-dealers'); ?></th>
                <td>
                    <label><input type="checkbox" name="dealer_docks" value="1" <?php checked($docks, '1'); ?> /> <?php _e('Docks', 'jblund-dealers'); ?></label><br />
                    <label><input type="checkbox" name="dealer_lifts" value="1" <?php checked($lifts, '1'); ?> /> <?php _e('Lifts', 'jblund-dealers'); ?></label><br />
                    <label><input type="checkbox" name="dealer_trailers" value="1" <?php checked($trailers, '1'); ?> /> <?php _e('Trailers', 'jblund-dealers'); ?></label>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Render the sub-locations meta box
     */
    public function render_sublocations_meta_box($post) {
        $sublocations = get_post_meta($post->ID, '_dealer_sublocations', true);
        if (!is_array($sublocations)) {
            $sublocations = array();
        }
        ?>
        <div id="dealer-sublocations-wrapper">
            <div id="dealer-sublocations-list">
                <?php
                if (!empty($sublocations)) {
                    foreach ($sublocations as $index => $location) {
                        $this->render_sublocation_row($index, $location);
                    }
                }
                ?>
            </div>
            <button type="button" class="button" id="add-sublocation"><?php _e('Add Sub-Location', 'jblund-dealers'); ?></button>
        </div>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var sublocationIndex = <?php echo count($sublocations); ?>;
            
            $('#add-sublocation').on('click', function() {
                var template = `
                    <div class="sublocation-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9;">
                        <h4><?php _e('Sub-Location', 'jblund-dealers'); ?> <button type="button" class="button remove-sublocation" style="float: right;"><?php _e('Remove', 'jblund-dealers'); ?></button></h4>
                        <table class="form-table">
                            <tr>
                                <th><label><?php _e('Location Name', 'jblund-dealers'); ?></label></th>
                                <td><input type="text" name="dealer_sublocations[${sublocationIndex}][name]" value="" class="regular-text" /></td>
                            </tr>
                            <tr>
                                <th><label><?php _e('Address', 'jblund-dealers'); ?></label></th>
                                <td><textarea name="dealer_sublocations[${sublocationIndex}][address]" rows="3" class="large-text"></textarea></td>
                            </tr>
                            <tr>
                                <th><label><?php _e('Phone', 'jblund-dealers'); ?></label></th>
                                <td><input type="text" name="dealer_sublocations[${sublocationIndex}][phone]" value="" class="regular-text" /></td>
                            </tr>
                            <tr>
                                <th><label><?php _e('Website (Optional)', 'jblund-dealers'); ?></label></th>
                                <td><input type="url" name="dealer_sublocations[${sublocationIndex}][website]" value="" class="regular-text" placeholder="https://" /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Services Offered', 'jblund-dealers'); ?></th>
                                <td>
                                    <label><input type="checkbox" name="dealer_sublocations[${sublocationIndex}][docks]" value="1" /> <?php _e('Docks', 'jblund-dealers'); ?></label><br />
                                    <label><input type="checkbox" name="dealer_sublocations[${sublocationIndex}][lifts]" value="1" /> <?php _e('Lifts', 'jblund-dealers'); ?></label><br />
                                    <label><input type="checkbox" name="dealer_sublocations[${sublocationIndex}][trailers]" value="1" /> <?php _e('Trailers', 'jblund-dealers'); ?></label>
                                </td>
                            </tr>
                        </table>
                    </div>
                `;
                $('#dealer-sublocations-list').append(template);
                sublocationIndex++;
            });
            
            $(document).on('click', '.remove-sublocation', function() {
                $(this).closest('.sublocation-row').remove();
            });
        });
        </script>
        <?php
    }
    
    /**
     * Render a single sub-location row
     */
    private function render_sublocation_row($index, $location) {
        ?>
        <div class="sublocation-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9;">
            <h4><?php _e('Sub-Location', 'jblund-dealers'); ?> <button type="button" class="button remove-sublocation" style="float: right;"><?php _e('Remove', 'jblund-dealers'); ?></button></h4>
            <table class="form-table">
                <tr>
                    <th><label><?php _e('Location Name', 'jblund-dealers'); ?></label></th>
                    <td><input type="text" name="dealer_sublocations[<?php echo $index; ?>][name]" value="<?php echo esc_attr(isset($location['name']) ? $location['name'] : ''); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label><?php _e('Address', 'jblund-dealers'); ?></label></th>
                    <td><textarea name="dealer_sublocations[<?php echo $index; ?>][address]" rows="3" class="large-text"><?php echo esc_textarea(isset($location['address']) ? $location['address'] : ''); ?></textarea></td>
                </tr>
                <tr>
                    <th><label><?php _e('Phone', 'jblund-dealers'); ?></label></th>
                    <td><input type="text" name="dealer_sublocations[<?php echo $index; ?>][phone]" value="<?php echo esc_attr(isset($location['phone']) ? $location['phone'] : ''); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label><?php _e('Website (Optional)', 'jblund-dealers'); ?></label></th>
                    <td><input type="url" name="dealer_sublocations[<?php echo $index; ?>][website]" value="<?php echo esc_url(isset($location['website']) ? $location['website'] : ''); ?>" class="regular-text" placeholder="https://" /></td>
                </tr>
                <tr>
                    <th><?php _e('Services Offered', 'jblund-dealers'); ?></th>
                    <td>
                        <label><input type="checkbox" name="dealer_sublocations[<?php echo $index; ?>][docks]" value="1" <?php checked(isset($location['docks']) ? $location['docks'] : '', '1'); ?> /> <?php _e('Docks', 'jblund-dealers'); ?></label><br />
                        <label><input type="checkbox" name="dealer_sublocations[<?php echo $index; ?>][lifts]" value="1" <?php checked(isset($location['lifts']) ? $location['lifts'] : '', '1'); ?> /> <?php _e('Lifts', 'jblund-dealers'); ?></label><br />
                        <label><input type="checkbox" name="dealer_sublocations[<?php echo $index; ?>][trailers]" value="1" <?php checked(isset($location['trailers']) ? $location['trailers'] : '', '1'); ?> /> <?php _e('Trailers', 'jblund-dealers'); ?></label>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }
    
    /**
     * Save dealer meta data
     */
    public function save_dealer_meta($post_id, $post) {
        // Check if nonce is set
        if (!isset($_POST['jblund_dealer_meta_box_nonce'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['jblund_dealer_meta_box_nonce'], 'jblund_dealer_meta_box')) {
            return;
        }
        
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save dealer information
        if (isset($_POST['dealer_company_name'])) {
            update_post_meta($post_id, '_dealer_company_name', sanitize_text_field($_POST['dealer_company_name']));
        }
        
        if (isset($_POST['dealer_company_address'])) {
            update_post_meta($post_id, '_dealer_company_address', sanitize_textarea_field($_POST['dealer_company_address']));
        }
        
        if (isset($_POST['dealer_company_phone'])) {
            update_post_meta($post_id, '_dealer_company_phone', sanitize_text_field($_POST['dealer_company_phone']));
        }
        
        if (isset($_POST['dealer_website'])) {
            update_post_meta($post_id, '_dealer_website', esc_url_raw($_POST['dealer_website']));
        }
        
        // Save boolean values
        update_post_meta($post_id, '_dealer_docks', isset($_POST['dealer_docks']) ? '1' : '0');
        update_post_meta($post_id, '_dealer_lifts', isset($_POST['dealer_lifts']) ? '1' : '0');
        update_post_meta($post_id, '_dealer_trailers', isset($_POST['dealer_trailers']) ? '1' : '0');
        
        // Save sub-locations
        if (isset($_POST['dealer_sublocations'])) {
            $sublocations = array();
            foreach ($_POST['dealer_sublocations'] as $location) {
                $sublocations[] = array(
                    'name' => sanitize_text_field($location['name']),
                    'address' => sanitize_textarea_field($location['address']),
                    'phone' => sanitize_text_field($location['phone']),
                    'website' => isset($location['website']) ? esc_url_raw($location['website']) : '',
                    'docks' => isset($location['docks']) ? '1' : '0',
                    'lifts' => isset($location['lifts']) ? '1' : '0',
                    'trailers' => isset($location['trailers']) ? '1' : '0',
                );
            }
            update_post_meta($post_id, '_dealer_sublocations', $sublocations);
        } else {
            delete_post_meta($post_id, '_dealer_sublocations');
        }
    }
    
    /**
     * Enqueue frontend styles
     */
    public function enqueue_frontend_styles() {
        wp_enqueue_style(
            'jblund-dealers-styles',
            JBLUND_DEALERS_PLUGIN_URL . 'assets/css/dealers.css',
            array(),
            JBLUND_DEALERS_VERSION
        );
    }
    
    /**
     * Shortcode to display dealers
     */
    public function dealers_shortcode($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ), $atts, 'jblund_dealers');
        
        $args = array(
            'post_type' => 'dealer',
            'posts_per_page' => intval($atts['posts_per_page']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
        );
        
        $dealers = new WP_Query($args);
        
        if (!$dealers->have_posts()) {
            return '<p>' . __('No dealers found.', 'jblund-dealers') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="jblund-dealers-grid">
            <?php while ($dealers->have_posts()) : $dealers->the_post(); ?>
                <?php
                $post_id = get_the_ID();
                $company_name = get_post_meta($post_id, '_dealer_company_name', true);
                $company_address = get_post_meta($post_id, '_dealer_company_address', true);
                $company_phone = get_post_meta($post_id, '_dealer_company_phone', true);
                $website = get_post_meta($post_id, '_dealer_website', true);
                $docks = get_post_meta($post_id, '_dealer_docks', true);
                $lifts = get_post_meta($post_id, '_dealer_lifts', true);
                $trailers = get_post_meta($post_id, '_dealer_trailers', true);
                $sublocations = get_post_meta($post_id, '_dealer_sublocations', true);
                ?>
                <div class="dealer-card">
                    <div class="dealer-card-header">
                        <h3 class="dealer-name"><?php echo esc_html($company_name ? $company_name : get_the_title()); ?></h3>
                    </div>
                    <div class="dealer-card-body">
                        <?php if ($company_address) : ?>
                            <p class="dealer-address"><strong><?php _e('Address:', 'jblund-dealers'); ?></strong><br><?php echo nl2br(esc_html($company_address)); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($company_phone) : ?>
                            <p class="dealer-phone"><strong><?php _e('Phone:', 'jblund-dealers'); ?></strong> <a href="tel:<?php echo esc_attr($company_phone); ?>"><?php echo esc_html($company_phone); ?></a></p>
                        <?php endif; ?>
                        
                        <?php if ($website) : ?>
                            <p class="dealer-website"><strong><?php _e('Website:', 'jblund-dealers'); ?></strong> <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($website); ?></a></p>
                        <?php endif; ?>
                        
                        <?php if ($docks || $lifts || $trailers) : ?>
                            <div class="dealer-services">
                                <strong><?php _e('Services:', 'jblund-dealers'); ?></strong>
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
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($sublocations) && is_array($sublocations)) : ?>
                            <div class="dealer-sublocations">
                                <h4><?php _e('Additional Locations:', 'jblund-dealers'); ?></h4>
                                <?php foreach ($sublocations as $location) : ?>
                                    <div class="dealer-sublocation">
                                        <?php if (!empty($location['name'])) : ?>
                                            <h5><?php echo esc_html($location['name']); ?></h5>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($location['address'])) : ?>
                                            <p class="sublocation-address"><?php echo nl2br(esc_html($location['address'])); ?></p>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($location['phone'])) : ?>
                                            <p class="sublocation-phone"><strong><?php _e('Phone:', 'jblund-dealers'); ?></strong> <a href="tel:<?php echo esc_attr($location['phone']); ?>"><?php echo esc_html($location['phone']); ?></a></p>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($location['website'])) : ?>
                                            <p class="sublocation-website"><strong><?php _e('Website:', 'jblund-dealers'); ?></strong> <a href="<?php echo esc_url($location['website']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($location['website']); ?></a></p>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($location['docks']) || !empty($location['lifts']) || !empty($location['trailers'])) : ?>
                                            <div class="sublocation-services">
                                                <strong><?php _e('Services:', 'jblund-dealers'); ?></strong>
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
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        
        return ob_get_clean();
    }
}

// Initialize the plugin
function jblund_dealers_init() {
    new JBLund_Dealers_Plugin();
}
add_action('plugins_loaded', 'jblund_dealers_init');

/**
 * Activation hook
 */
function jblund_dealers_activate() {
    // Trigger the post type registration
    $plugin = new JBLund_Dealers_Plugin();
    $plugin->register_dealer_post_type();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'jblund_dealers_activate');

/**
 * Deactivation hook
 */
function jblund_dealers_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'jblund_dealers_deactivate');
