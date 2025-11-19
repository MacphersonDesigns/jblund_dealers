<?php
/**
 * Plugin Name: JBLund Dealers
 * Plugin URI: https://github.com/MacphersonDesigns/jblund_dealers
 * Description: A custom WordPress plugin to store and display dealer information for JBLund Dock's B2B Website
 * Version: 1.2.0
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
define('JBLUND_DEALERS_VERSION', '1.2.0');
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
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'handle_csv_operations'));
        add_action('admin_notices', array($this, 'display_import_notices'));
        add_filter('manage_dealer_posts_columns', array($this, 'add_dealer_columns'));
        add_action('manage_dealer_posts_custom_column', array($this, 'populate_dealer_columns'), 10, 2);
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
     * Handle CSV operations (export/import)
     */
    public function handle_csv_operations() {
        // Handle CSV export
        if (isset($_GET['action']) && $_GET['action'] === 'export_csv' &&
            isset($_GET['post_type']) && $_GET['post_type'] === 'dealer' &&
            isset($_GET['page']) && $_GET['page'] === 'jblund-dealers-settings') {
            $this->export_dealers_csv();
        }

        // Handle CSV upload for column mapping
        if (isset($_POST['action']) && $_POST['action'] === 'upload_csv' &&
            isset($_POST['jblund_dealers_upload_nonce'])) {
            // This is handled in render_import_export_tab to show mapping UI
            return;
        }

        // Handle CSV import with mapping
        if (isset($_POST['action']) && $_POST['action'] === 'import_csv' &&
            isset($_POST['jblund_dealers_import_nonce'])) {
            $this->import_dealers_csv();
        }
    }

    /**
     * Display admin notices for CSV import results
     */
    public function display_import_notices() {
        // Only show on our settings page
        if (!isset($_GET['page']) || $_GET['page'] !== 'jblund-dealers-settings') {
            return;
        }

        // Import success messages
        if (isset($_GET['import_success']) && $_GET['import_success'] == '1') {
            $imported = isset($_GET['imported']) ? intval($_GET['imported']) : 0;
            $updated = isset($_GET['updated']) ? intval($_GET['updated']) : 0;
            $errors = isset($_GET['errors']) ? intval($_GET['errors']) : 0;

            $messages = array();

            if ($imported > 0) {
                $messages[] = sprintf(
                    _n('Successfully imported %d new dealer.', 'Successfully imported %d new dealers.', $imported, 'jblund-dealers'),
                    $imported
                );
            }

            if ($updated > 0) {
                $messages[] = sprintf(
                    _n('Successfully updated %d existing dealer.', 'Successfully updated %d existing dealers.', $updated, 'jblund-dealers'),
                    $updated
                );
            }

            if (!empty($messages)) {
                echo '<div class="notice notice-success is-dismissible"><p>';
                echo implode(' ', $messages);
                echo '</p></div>';
            }

            if ($errors > 0) {
                echo '<div class="notice notice-warning is-dismissible"><p>';
                printf(
                    _n('Encountered %d error during import.', 'Encountered %d errors during import.', $errors, 'jblund-dealers'),
                    $errors
                );
                echo '</p></div>';
            }
        }

        // Import error messages
        if (isset($_GET['import_error'])) {
            $error_type = sanitize_text_field($_GET['import_error']);
            $error_messages = array(
                'upload_failed' => __('Error uploading CSV file. Please try again.', 'jblund-dealers'),
                'empty_file' => __('CSV file is empty. Please check your file and try again.', 'jblund-dealers'),
                'invalid_format' => __('Invalid CSV format. Please ensure your CSV has a "name" or "Company Name" column as the header.', 'jblund-dealers')
            );

            if (isset($error_messages[$error_type])) {
                echo '<div class="notice notice-error is-dismissible"><p>';
                echo esc_html($error_messages[$error_type]);
                echo '</p></div>';
            }
        }
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
            'dealer_user_account',
            __('Linked User Account', 'jblund-dealers'),
            array($this, 'render_dealer_user_account_meta_box'),
            'dealer',
            'side',
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
        $latitude = get_post_meta($post->ID, '_dealer_latitude', true);
        $longitude = get_post_meta($post->ID, '_dealer_longitude', true);
        $docks = get_post_meta($post->ID, '_dealer_docks', true);
        $lifts = get_post_meta($post->ID, '_dealer_lifts', true);
        $trailers = get_post_meta($post->ID, '_dealer_trailers', true);
        ?>
        <p><em><?php _e('Note: The dealer name is set using the title field above. The company name will automatically sync with the title.', 'jblund-dealers'); ?></em></p>
        <table class="form-table">
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
                <th><label for="dealer_latitude"><?php _e('Latitude', 'jblund-dealers'); ?></label></th>
                <td><input type="text" id="dealer_latitude" name="dealer_latitude" value="<?php echo esc_attr($latitude); ?>" class="regular-text" placeholder="e.g., 45.123456" />
                <p class="description"><?php _e('Optional: Decimal degrees format (e.g., 45.123456)', 'jblund-dealers'); ?></p></td>
            </tr>
            <tr>
                <th><label for="dealer_longitude"><?php _e('Longitude', 'jblund-dealers'); ?></label></th>
                <td><input type="text" id="dealer_longitude" name="dealer_longitude" value="<?php echo esc_attr($longitude); ?>" class="regular-text" placeholder="e.g., -93.123456" />
                <p class="description"><?php _e('Optional: Decimal degrees format (e.g., -93.123456)', 'jblund-dealers'); ?></p></td>
            </tr>
            <tr>
                <th><label for="dealer_custom_map_link"><?php _e('Custom Map Link', 'jblund-dealers'); ?></label></th>
                <td><input type="url" id="dealer_custom_map_link" name="dealer_custom_map_link" value="<?php echo esc_url(get_post_meta($post->ID, '_dealer_custom_map_link', true)); ?>" class="regular-text" placeholder="https://maps.google.com/..." />
                <p class="description"><?php _e('Optional: Custom map link to override auto-generated address link', 'jblund-dealers'); ?></p></td>
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
     * Render the dealer user account meta box
     */
    public function render_dealer_user_account_meta_box($post) {
        $linked_user_id = get_post_meta($post->ID, '_dealer_linked_user_id', true);

        // Get all users with 'dealer' role
        $dealer_users = get_users(array(
            'role' => 'dealer',
            'orderby' => 'display_name',
            'order' => 'ASC'
        ));
        ?>
        <p><em><?php _e('Link this dealer listing to a WordPress user account for portal access.', 'jblund-dealers'); ?></em></p>

        <p>
            <label for="dealer_linked_user_id"><strong><?php _e('Dealer User Account:', 'jblund-dealers'); ?></strong></label><br/>
            <select id="dealer_linked_user_id" name="dealer_linked_user_id" style="width: 100%;">
                <option value=""><?php _e('-- No Account Linked --', 'jblund-dealers'); ?></option>
                <?php foreach ($dealer_users as $user) : ?>
                    <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($linked_user_id, $user->ID); ?>>
                        <?php
                        echo esc_html($user->display_name);
                        $company = get_user_meta($user->ID, '_dealer_company_name', true);
                        if ($company) {
                            echo ' (' . esc_html($company) . ')';
                        }
                        echo ' - ' . esc_html($user->user_email);
                        ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <?php if ($linked_user_id) :
            $user = get_userdata($linked_user_id);
            if ($user) : ?>
                <div style="padding: 10px; background: #f0f0f1; border-left: 3px solid #003366; margin-top: 10px;">
                    <p style="margin: 0;"><strong><?php _e('Account Details:', 'jblund-dealers'); ?></strong></p>
                    <p style="margin: 5px 0 0 0; font-size: 12px;">
                        <?php echo esc_html($user->user_email); ?><br/>
                        <a href="<?php echo esc_url(get_edit_user_link($user->ID)); ?>" target="_blank"><?php _e('Edit User', 'jblund-dealers'); ?></a>
                    </p>
                </div>
            <?php endif;
        endif; ?>
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
                                <th><label><?php _e('Latitude (Optional)', 'jblund-dealers'); ?></label></th>
                                <td><input type="text" name="dealer_sublocations[${sublocationIndex}][latitude]" value="" class="regular-text" placeholder="e.g., 45.123456" />
                                <p class="description"><?php _e('Decimal degrees format', 'jblund-dealers'); ?></p></td>
                            </tr>
                            <tr>
                                <th><label><?php _e('Longitude (Optional)', 'jblund-dealers'); ?></label></th>
                                <td><input type="text" name="dealer_sublocations[${sublocationIndex}][longitude]" value="" class="regular-text" placeholder="e.g., -93.123456" />
                                <p class="description"><?php _e('Decimal degrees format', 'jblund-dealers'); ?></p></td>
                            </tr>
                            <tr>
                                <th><label><?php _e('Custom Map Link (Optional)', 'jblund-dealers'); ?></label></th>
                                <td><input type="url" name="dealer_sublocations[${sublocationIndex}][custom_map_link]" value="" class="regular-text" placeholder="https://maps.google.com/..." />
                                <p class="description"><?php _e('Custom map link to override auto-generated address link', 'jblund-dealers'); ?></p></td>
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
                    <th><label><?php _e('Latitude (Optional)', 'jblund-dealers'); ?></label></th>
                    <td><input type="text" name="dealer_sublocations[<?php echo $index; ?>][latitude]" value="<?php echo esc_attr(isset($location['latitude']) ? $location['latitude'] : ''); ?>" class="regular-text" placeholder="e.g., 45.123456" />
                    <p class="description"><?php _e('Decimal degrees format', 'jblund-dealers'); ?></p></td>
                </tr>
                <tr>
                    <th><label><?php _e('Longitude (Optional)', 'jblund-dealers'); ?></label></th>
                    <td><input type="text" name="dealer_sublocations[<?php echo $index; ?>][longitude]" value="<?php echo esc_attr(isset($location['longitude']) ? $location['longitude'] : ''); ?>" class="regular-text" placeholder="e.g., -93.123456" />
                    <p class="description"><?php _e('Decimal degrees format', 'jblund-dealers'); ?></p></td>
                </tr>
                <tr>
                    <th><label><?php _e('Custom Map Link (Optional)', 'jblund-dealers'); ?></label></th>
                    <td><input type="url" name="dealer_sublocations[<?php echo $index; ?>][custom_map_link]" value="<?php echo esc_url(isset($location['custom_map_link']) ? $location['custom_map_link'] : ''); ?>" class="regular-text" placeholder="https://maps.google.com/..." />
                    <p class="description"><?php _e('Custom map link to override auto-generated address link', 'jblund-dealers'); ?></p></td>
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

        // Sync company name with post title
        $company_name = get_the_title($post_id);
        update_post_meta($post_id, '_dealer_company_name', $company_name);

        // Save dealer information
        if (isset($_POST['dealer_company_address'])) {
            update_post_meta($post_id, '_dealer_company_address', sanitize_textarea_field($_POST['dealer_company_address']));
        }

        if (isset($_POST['dealer_company_phone'])) {
            update_post_meta($post_id, '_dealer_company_phone', sanitize_text_field($_POST['dealer_company_phone']));
        }

        if (isset($_POST['dealer_website'])) {
            update_post_meta($post_id, '_dealer_website', esc_url_raw($_POST['dealer_website']));
        }

        if (isset($_POST['dealer_latitude'])) {
            update_post_meta($post_id, '_dealer_latitude', sanitize_text_field($_POST['dealer_latitude']));
        }

        if (isset($_POST['dealer_longitude'])) {
            update_post_meta($post_id, '_dealer_longitude', sanitize_text_field($_POST['dealer_longitude']));
        }

        // Custom map link
        if (isset($_POST['dealer_custom_map_link'])) {
            update_post_meta($post_id, '_dealer_custom_map_link', esc_url_raw($_POST['dealer_custom_map_link']));
        }

        // Save boolean values
        update_post_meta($post_id, '_dealer_docks', isset($_POST['dealer_docks']) ? '1' : '0');
        update_post_meta($post_id, '_dealer_lifts', isset($_POST['dealer_lifts']) ? '1' : '0');
        update_post_meta($post_id, '_dealer_trailers', isset($_POST['dealer_trailers']) ? '1' : '0');

        // Save linked user account
        if (isset($_POST['dealer_linked_user_id'])) {
            $user_id = absint($_POST['dealer_linked_user_id']);
            if ($user_id > 0) {
                update_post_meta($post_id, '_dealer_linked_user_id', $user_id);
            } else {
                delete_post_meta($post_id, '_dealer_linked_user_id');
            }
        }

        // Save sub-locations
        if (isset($_POST['dealer_sublocations'])) {
            $sublocations = array();
            foreach ($_POST['dealer_sublocations'] as $location) {
                $sublocations[] = array(
                    'name' => sanitize_text_field($location['name']),
                    'address' => sanitize_textarea_field($location['address']),
                    'phone' => sanitize_text_field($location['phone']),
                    'website' => isset($location['website']) ? esc_url_raw($location['website']) : '',
                    'latitude' => isset($location['latitude']) ? sanitize_text_field($location['latitude']) : '',
                    'longitude' => isset($location['longitude']) ? sanitize_text_field($location['longitude']) : '',
                    'custom_map_link' => isset($location['custom_map_link']) ? esc_url_raw($location['custom_map_link']) : '',
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
            JBLUND_DEALERS_VERSION . '.2'
        );

        // Add custom CSS from settings
        $this->add_custom_styles();
    }

    /**
     * Add custom styles from settings
     */
    private function add_custom_styles() {
        $options = get_option('jblund_dealers_settings');

        // Check if theme styles should be inherited
        $inherit_theme = isset($options['inherit_theme_styles']) && $options['inherit_theme_styles'] === '1';

        if ($inherit_theme) {
            // Add minimal CSS for theme integration
            $custom_css = "
            .dealer-card-header {
                background: var(--wp--preset--color--primary, currentColor) !important;
                color: var(--wp--preset--color--background, #fff) !important;
            }
            .dealer-website-button {
                background: var(--wp--preset--color--primary, currentColor) !important;
                color: var(--wp--preset--color--background, #fff) !important;
                font-family: inherit !important;
            }
            .dealer-website-button:hover {
                opacity: 0.85 !important;
            }
            .dealer-card {
                background: var(--wp--preset--color--background, #fff) !important;
                color: var(--wp--preset--color--foreground, #000) !important;
                font-family: inherit !important;
            }
            ";
        } else {
            // Only add CSS for settings that differ from defaults
            // This preserves the base CSS file styling
            $custom_css = "";

            // Colors - only add if different from default
            if (isset($options['header_color']) && $options['header_color'] !== '#0073aa') {
                $custom_css .= ".dealer-card-header { background: {$options['header_color']} !important; }\n";
            }
            if (isset($options['card_background']) && $options['card_background'] !== '#ffffff') {
                $custom_css .= ".dealer-card { background: {$options['card_background']} !important; }\n";
            }
            if (isset($options['button_color']) && $options['button_color'] !== '#0073aa') {
                $custom_css .= ".dealer-website-button { background: {$options['button_color']} !important; }\n";
                $custom_css .= ".dealer-website-button:hover { background: " . $this->darken_color($options['button_color'], 10) . " !important; }\n";
            }
            if (isset($options['text_color']) && $options['text_color'] !== '#333333') {
                $custom_css .= ".dealer-card h3, .dealer-card-header h3 { color: {$options['text_color']} !important; }\n";
            }
            if (isset($options['secondary_text_color']) && $options['secondary_text_color'] !== '#666666') {
                $custom_css .= ".dealer-card p, .dealer-card span, .dealer-card-address, .dealer-card-phone, .dealer-card-info { color: {$options['secondary_text_color']} !important; }\n";
            }
            if (isset($options['border_color']) && $options['border_color'] !== '#dddddd') {
                $custom_css .= ".dealer-card { border-color: {$options['border_color']} !important; }\n";
            }
            if (isset($options['button_text_color']) && $options['button_text_color'] !== '#ffffff') {
                $custom_css .= ".dealer-website-button { color: {$options['button_text_color']} !important; }\n";
            }
            if (isset($options['icon_color']) && $options['icon_color'] !== '#0073aa') {
                $custom_css .= ".dealer-services-icons, .dealer-services-icons span { color: {$options['icon_color']} !important; }\n";
            }
            if (isset($options['link_color']) && $options['link_color'] !== '#0073aa') {
                $custom_css .= ".dealer-card a, .dealer-card-phone a, .dealer-card-website a { color: {$options['link_color']} !important; }\n";
            }
            if (isset($options['hover_background']) && $options['hover_background'] !== '#f9f9f9') {
                $custom_css .= ".dealer-card:hover { background: {$options['hover_background']} !important; }\n";
            }

            // Typography - only add if different from default
            if (isset($options['heading_font_size']) && $options['heading_font_size'] !== '24') {
                $custom_css .= ".dealer-card h3, .dealer-card-header h3 { font-size: {$options['heading_font_size']}px !important; }\n";
            }
            if (isset($options['body_font_size']) && $options['body_font_size'] !== '14') {
                $custom_css .= ".dealer-card p, .dealer-card span, .dealer-card-address, .dealer-card-phone, .dealer-card-info { font-size: {$options['body_font_size']}px !important; }\n";
            }
            if (isset($options['heading_font_weight']) && $options['heading_font_weight'] !== 'bold') {
                $custom_css .= ".dealer-card h3, .dealer-card-header h3 { font-weight: {$options['heading_font_weight']} !important; }\n";
            }
            if (isset($options['line_height']) && $options['line_height'] !== '1.6') {
                $custom_css .= ".dealer-card p, .dealer-card span, .dealer-card h3 { line-height: {$options['line_height']} !important; }\n";
            }

            // Spacing - only add if different from default
            if (isset($options['card_padding']) && $options['card_padding'] !== '20') {
                $custom_css .= ".dealer-card { padding: {$options['card_padding']}px !important; }\n";
            }
            if (isset($options['card_margin']) && $options['card_margin'] !== '15') {
                $custom_css .= ".dealer-card { margin: {$options['card_margin']}px !important; }\n";
            }
            if (isset($options['grid_gap']) && $options['grid_gap'] !== '20') {
                $custom_css .= ".dealers-grid { gap: {$options['grid_gap']}px !important; }\n";
            }
            if (isset($options['border_radius']) && $options['border_radius'] !== '8') {
                $custom_css .= ".dealer-card { border-radius: {$options['border_radius']}px !important; }\n";
                $custom_css .= ".dealer-card-header { border-radius: {$options['border_radius']}px {$options['border_radius']}px 0 0 !important; }\n";
                $custom_css .= ".dealer-website-button { border-radius: {$options['border_radius']}px !important; }\n";
            }
            if (isset($options['border_width']) && $options['border_width'] !== '1') {
                $custom_css .= ".dealer-card { border-width: {$options['border_width']}px !important; }\n";
            }
            if (isset($options['border_style']) && $options['border_style'] !== 'solid') {
                $custom_css .= ".dealer-card { border-style: {$options['border_style']} !important; }\n";
            }

            // Effects - only add if different from default
            if (isset($options['box_shadow']) && $options['box_shadow'] !== 'light') {
                $box_shadows = array(
                    'none' => 'none',
                    'light' => '0 2px 4px rgba(0,0,0,0.1)',
                    'medium' => '0 4px 8px rgba(0,0,0,0.15)',
                    'heavy' => '0 8px 16px rgba(0,0,0,0.2)',
                );
                $custom_css .= ".dealer-card { box-shadow: {$box_shadows[$options['box_shadow']]} !important; }\n";
            }

            if (isset($options['hover_effect']) && $options['hover_effect'] !== 'lift') {
                $hover_transform = '';
                $hover_shadow = '';

                switch ($options['hover_effect']) {
                    case 'none':
                        // No transform needed
                        break;
                    case 'lift':
                        $hover_transform = 'translateY(-5px)';
                        break;
                    case 'scale':
                        $hover_transform = 'scale(1.02)';
                        break;
                    case 'shadow':
                        $hover_shadow = 'box-shadow: 0 12px 24px rgba(0,0,0,0.25) !important;';
                        break;
                }

                if ($hover_transform) {
                    $custom_css .= ".dealer-card:hover { transform: {$hover_transform} !important; }\n";
                }
                if ($hover_shadow) {
                    $custom_css .= ".dealer-card:hover { {$hover_shadow} }\n";
                }
            }

            if (isset($options['transition_speed']) && $options['transition_speed'] !== '0.3') {
                $custom_css .= ".dealer-card, .dealer-website-button { transition: all {$options['transition_speed']}s ease !important; }\n";
            }
            if (isset($options['icon_size']) && $options['icon_size'] !== '24') {
                $custom_css .= ".dealer-services-icons, .dealer-services-icons span { font-size: {$options['icon_size']}px !important; }\n";
            }

            // Add custom CSS from textarea if provided
            if (isset($options['custom_css']) && !empty($options['custom_css'])) {
                $custom_css .= "\n/* Custom CSS */\n" . $options['custom_css'] . "\n";
            }
        }

        // Only add inline styles if there's actually custom CSS to add
        if (!empty(trim($custom_css))) {
            wp_add_inline_style('jblund-dealers-styles', $custom_css);
        }
    }

    /**
     * Darken a hex color
     */
    private function darken_color($hex, $percent) {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r - ($r * $percent / 100)));
        $g = max(0, min(255, $g - ($g * $percent / 100)));
        $b = max(0, min(255, $b - ($b * $percent / 100)));

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    /**
     * Add settings page to admin menu
     */
    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=dealer',
            __('Dealer Settings', 'jblund-dealers'),
            __('Settings', 'jblund-dealers'),
            'manage_options',
            'jblund-dealers-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register settings and fields
     */
    public function register_settings() {
        register_setting('jblund_dealers_settings', 'jblund_dealers_settings');
        register_setting('jblund_dealers_portal_pages', 'jblund_dealers_portal_pages');

        // Appearance Section
        add_settings_section(
            'jblund_dealers_appearance',
            __('Appearance Settings', 'jblund-dealers'),
            array($this, 'appearance_section_callback'),
            'jblund_dealers_settings'
        );

        add_settings_field(
            'header_color',
            __('Card Header Color', 'jblund-dealers'),
            array($this, 'color_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'header_color', 'default' => '#0073aa')
        );

        add_settings_field(
            'card_background',
            __('Card Background Color', 'jblund-dealers'),
            array($this, 'color_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'card_background', 'default' => '#ffffff')
        );

        add_settings_field(
            'button_color',
            __('Button Color', 'jblund-dealers'),
            array($this, 'color_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'button_color', 'default' => '#0073aa')
        );

        // Additional Color Fields
        add_settings_field(
            'text_color',
            __('Primary Text Color', 'jblund-dealers'),
            array($this, 'color_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'text_color', 'default' => '#333333')
        );

        add_settings_field(
            'secondary_text_color',
            __('Secondary Text Color', 'jblund-dealers'),
            array($this, 'color_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'secondary_text_color', 'default' => '#666666')
        );

        add_settings_field(
            'border_color',
            __('Border Color', 'jblund-dealers'),
            array($this, 'color_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'border_color', 'default' => '#dddddd')
        );

        add_settings_field(
            'button_text_color',
            __('Button Text Color', 'jblund-dealers'),
            array($this, 'color_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'button_text_color', 'default' => '#ffffff')
        );

        add_settings_field(
            'icon_color',
            __('Icon Color', 'jblund-dealers'),
            array($this, 'color_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'icon_color', 'default' => '#0073aa')
        );

        add_settings_field(
            'link_color',
            __('Link Color', 'jblund-dealers'),
            array($this, 'color_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'link_color', 'default' => '#0073aa')
        );

        add_settings_field(
            'hover_background',
            __('Card Hover Background', 'jblund-dealers'),
            array($this, 'color_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'hover_background', 'default' => '#f9f9f9')
        );

        // Typography Fields
        add_settings_field(
            'heading_font_size',
            __('Heading Font Size', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'heading_font_size', 'default' => '24', 'min' => '16', 'max' => '32', 'unit' => 'px')
        );

        add_settings_field(
            'body_font_size',
            __('Body Font Size', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'body_font_size', 'default' => '14', 'min' => '12', 'max' => '18', 'unit' => 'px')
        );

        add_settings_field(
            'heading_font_weight',
            __('Heading Font Weight', 'jblund-dealers'),
            array($this, 'select_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array(
                'field' => 'heading_font_weight',
                'options' => array(
                    'normal' => __('Normal', 'jblund-dealers'),
                    '600' => __('Semi-Bold (600)', 'jblund-dealers'),
                    'bold' => __('Bold (700)', 'jblund-dealers'),
                    '800' => __('Extra Bold (800)', 'jblund-dealers')
                ),
                'default' => 'bold'
            )
        );

        add_settings_field(
            'line_height',
            __('Line Height', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'line_height', 'default' => '1.6', 'min' => '1.2', 'max' => '2.0', 'step' => '0.1', 'unit' => '')
        );

        // Spacing Fields
        add_settings_field(
            'card_padding',
            __('Card Padding', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'card_padding', 'default' => '20', 'min' => '10', 'max' => '40', 'unit' => 'px')
        );

        add_settings_field(
            'card_margin',
            __('Card Margin', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'card_margin', 'default' => '15', 'min' => '10', 'max' => '30', 'unit' => 'px')
        );

        add_settings_field(
            'grid_gap',
            __('Grid Gap', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'grid_gap', 'default' => '20', 'min' => '15', 'max' => '50', 'unit' => 'px')
        );

        add_settings_field(
            'border_radius',
            __('Border Radius', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'border_radius', 'default' => '8', 'min' => '0', 'max' => '20', 'unit' => 'px')
        );

        add_settings_field(
            'border_width',
            __('Border Width', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'border_width', 'default' => '1', 'min' => '0', 'max' => '5', 'unit' => 'px')
        );

        add_settings_field(
            'border_style',
            __('Border Style', 'jblund-dealers'),
            array($this, 'select_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array(
                'field' => 'border_style',
                'options' => array(
                    'solid' => __('Solid', 'jblund-dealers'),
                    'dashed' => __('Dashed', 'jblund-dealers'),
                    'dotted' => __('Dotted', 'jblund-dealers'),
                    'none' => __('None', 'jblund-dealers')
                ),
                'default' => 'solid'
            )
        );

        // Effects Fields
        add_settings_field(
            'box_shadow',
            __('Box Shadow', 'jblund-dealers'),
            array($this, 'select_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array(
                'field' => 'box_shadow',
                'options' => array(
                    'none' => __('None', 'jblund-dealers'),
                    'light' => __('Light', 'jblund-dealers'),
                    'medium' => __('Medium', 'jblund-dealers'),
                    'heavy' => __('Heavy', 'jblund-dealers')
                ),
                'default' => 'light'
            )
        );

        add_settings_field(
            'hover_effect',
            __('Hover Effect', 'jblund-dealers'),
            array($this, 'select_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array(
                'field' => 'hover_effect',
                'options' => array(
                    'none' => __('None', 'jblund-dealers'),
                    'lift' => __('Lift Up', 'jblund-dealers'),
                    'scale' => __('Scale', 'jblund-dealers'),
                    'shadow' => __('Shadow Increase', 'jblund-dealers')
                ),
                'default' => 'lift'
            )
        );

        add_settings_field(
            'transition_speed',
            __('Transition Speed', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'transition_speed', 'default' => '0.3', 'min' => '0.1', 'max' => '0.5', 'step' => '0.1', 'unit' => 's')
        );

        add_settings_field(
            'icon_size',
            __('Icon Size', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'icon_size', 'default' => '24', 'min' => '16', 'max' => '32', 'unit' => 'px')
        );

        // Custom CSS Field
        add_settings_field(
            'custom_css',
            __('Custom CSS', 'jblund-dealers'),
            array($this, 'textarea_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'custom_css', 'default' => '')
        );

        // Shortcode Section
        add_settings_section(
            'jblund_dealers_shortcode',
            __('Shortcode Settings', 'jblund-dealers'),
            array($this, 'shortcode_section_callback'),
            'jblund_dealers_settings'
        );

        add_settings_field(
            'default_layout',
            __('Default Layout', 'jblund-dealers'),
            array($this, 'select_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_shortcode',
            array(
                'field' => 'default_layout',
                'options' => array(
                    'grid' => __('Grid Layout', 'jblund-dealers'),
                    'list' => __('List Layout', 'jblund-dealers'),
                    'compact' => __('Compact Grid', 'jblund-dealers')
                ),
                'default' => 'grid'
            )
        );

        add_settings_field(
            'use_icons',
            __('Use Icons for Services', 'jblund-dealers'),
            array($this, 'checkbox_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_shortcode',
            array('field' => 'use_icons', 'default' => '1')
        );

        // Dealer Portal Updates Section
        add_settings_section(
            'jblund_dealers_portal_updates',
            __('Dealer Portal Updates', 'jblund-dealers'),
            array($this, 'portal_updates_section_callback'),
            'jblund_dealers_settings'
        );

        add_settings_field(
            'portal_updates',
            __('Recent Updates', 'jblund-dealers'),
            array($this, 'portal_updates_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_portal_updates'
        );
    }

    /**
     * Render settings page with tabs
     */
    public function render_settings_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        ?>
        <div class="wrap jblund-dealers-settings">
            <h1><?php _e('JBLund Dealers Settings', 'jblund-dealers'); ?></h1>

            <!-- Tab Navigation -->
            <h2 class="nav-tab-wrapper">
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=general"
                   class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('General', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=pages"
                   class="nav-tab <?php echo $active_tab === 'pages' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Portal Pages', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=appearance"
                   class="nav-tab <?php echo $active_tab === 'appearance' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Appearance', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=portal"
                   class="nav-tab <?php echo $active_tab === 'portal' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Portal Updates', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=nda"
                   class="nav-tab <?php echo $active_tab === 'nda' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('NDA Editor', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=import-export"
                   class="nav-tab <?php echo $active_tab === 'import-export' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Import/Export', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=help"
                   class="nav-tab <?php echo $active_tab === 'help' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Help & Guide', 'jblund-dealers'); ?>
                </a>
            </h2>

            <!-- Tab Content -->
            <div class="tab-content">
                <?php
                switch ($active_tab) {
                    case 'general':
                        $this->render_general_tab();
                        break;
                    case 'pages':
                        $this->render_pages_tab();
                        break;
                    case 'appearance':
                        $this->render_appearance_tab();
                        break;
                    case 'portal':
                        $this->render_portal_tab();
                        break;
                    case 'nda':
                        $this->render_nda_tab();
                        break;
                    case 'import-export':
                        $this->render_import_export_tab();
                        break;
                    case 'help':
                        $this->render_help_tab();
                        break;
                }
                ?>
            </div>
        </div>

        <style>
            .jblund-dealers-settings .tab-content {
                background: #fff;
                padding: 20px;
                border: 1px solid #ccd0d4;
                border-top: none;
                margin-top: -1px;
            }
            .jblund-dealers-settings .settings-section {
                margin-bottom: 30px;
            }
            .jblund-dealers-settings .settings-section h3 {
                margin-top: 0;
                padding-bottom: 10px;
                border-bottom: 1px solid #ddd;
            }
            .jblund-dealers-settings .help-section {
                margin-bottom: 30px;
                padding: 20px;
                background: #f9f9f9;
                border-left: 4px solid #0073aa;
            }
            .jblund-dealers-settings .help-section h3 {
                margin-top: 0;
                color: #0073aa;
            }
            .jblund-dealers-settings .step-list {
                counter-reset: step-counter;
                list-style: none;
                padding-left: 0;
            }
            .jblund-dealers-settings .step-list li {
                counter-increment: step-counter;
                margin-bottom: 15px;
                padding-left: 35px;
                position: relative;
            }
            .jblund-dealers-settings .step-list li:before {
                content: counter(step-counter);
                position: absolute;
                left: 0;
                top: 0;
                background: #0073aa;
                color: #fff;
                width: 25px;
                height: 25px;
                border-radius: 50%;
                text-align: center;
                line-height: 25px;
                font-weight: bold;
                font-size: 14px;
            }
            .jblund-dealers-settings .feature-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }
            .jblund-dealers-settings .feature-card {
                padding: 15px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .jblund-dealers-settings .feature-card h4 {
                margin-top: 0;
                color: #0073aa;
            }
        </style>
        <?php
    }

    /**
     * Render General tab
     */
    private function render_general_tab() {
        ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('jblund_dealers_settings');
            ?>
            <div class="settings-section">
                <h3><?php _e('Shortcode Settings', 'jblund-dealers'); ?></h3>
                <p><?php _e('Configure default shortcode behavior and layout options.', 'jblund-dealers'); ?></p>
                <table class="form-table">
                    <?php
                    do_settings_fields('jblund_dealers_settings', 'jblund_dealers_shortcode');
                    ?>
                </table>
            </div>
            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * Render Pages tab - Page assignment and shortcode documentation
     */
    private function render_pages_tab() {
        $portal_pages = get_option('jblund_dealers_portal_pages', array());
        $login_page = isset($portal_pages['login']) ? $portal_pages['login'] : '';
        $dashboard_page = isset($portal_pages['dashboard']) ? $portal_pages['dashboard'] : '';
        $profile_page = isset($portal_pages['profile']) ? $portal_pages['profile'] : '';
        $nda_page = isset($portal_pages['nda']) ? $portal_pages['nda'] : '';

        // Get all pages for dropdown
        $pages = get_pages(array('sort_column' => 'post_title'));
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('jblund_dealers_portal_pages'); ?>

            <div class="settings-section">
                <h3><?php _e('Dealer Portal Page Assignment', 'jblund-dealers'); ?></h3>
                <p><?php _e('Assign pages for your dealer portal. Create pages with Divi (or any page builder) and add the shortcodes below, then assign them here.', 'jblund-dealers'); ?></p>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="login_page"><?php _e('Login Page', 'jblund-dealers'); ?></label>
                        </th>
                        <td>
                            <select name="jblund_dealers_portal_pages[login]" id="login_page" class="regular-text">
                                <option value=""><?php _e('— Select Page —', 'jblund-dealers'); ?></option>
                                <?php foreach ($pages as $page): ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($login_page, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <?php _e('Add this shortcode to your login page:', 'jblund-dealers'); ?>
                                <code>[jblund_dealer_login]</code>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="dashboard_page"><?php _e('Dashboard Page', 'jblund-dealers'); ?></label>
                        </th>
                        <td>
                            <select name="jblund_dealers_portal_pages[dashboard]" id="dashboard_page" class="regular-text">
                                <option value=""><?php _e('— Select Page —', 'jblund-dealers'); ?></option>
                                <?php foreach ($pages as $page): ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($dashboard_page, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <?php _e('Add this shortcode to your dashboard page:', 'jblund-dealers'); ?>
                                <code>[jblund_dealer_dashboard]</code>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="profile_page"><?php _e('Profile Page', 'jblund-dealers'); ?></label>
                        </th>
                        <td>
                            <select name="jblund_dealers_portal_pages[profile]" id="profile_page" class="regular-text">
                                <option value=""><?php _e('— Select Page —', 'jblund-dealers'); ?></option>
                                <?php foreach ($pages as $page): ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($profile_page, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <?php _e('Add this shortcode to your profile page:', 'jblund-dealers'); ?>
                                <code>[jblund_dealer_profile]</code>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nda_page"><?php _e('NDA Acceptance Page', 'jblund-dealers'); ?></label>
                        </th>
                        <td>
                            <select name="jblund_dealers_portal_pages[nda]" id="nda_page" class="regular-text">
                                <option value=""><?php _e('— Select Page —', 'jblund-dealers'); ?></option>
                                <?php foreach ($pages as $page): ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($nda_page, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <?php _e('Add this shortcode to your NDA page:', 'jblund-dealers'); ?>
                                <code>[jblund_nda_acceptance]</code>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="settings-section" style="background: #f9f9f9; padding: 20px; border-left: 4px solid #0073aa; margin-top: 30px;">
                <h3><?php _e('📋 Setup Instructions', 'jblund-dealers'); ?></h3>
                <ol style="line-height: 2;">
                    <li><?php _e('Create new pages in WordPress (you can use Divi Builder or any page builder)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Add the appropriate shortcode to each page (shown above)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Design the page around the shortcode using your page builder', 'jblund-dealers'); ?></li>
                    <li><?php _e('Assign the pages using the dropdowns above', 'jblund-dealers'); ?></li>
                    <li><?php _e('Save settings - the plugin will use these pages for redirects and navigation', 'jblund-dealers'); ?></li>
                </ol>
            </div>

            <div class="settings-section" style="background: #fff3cd; padding: 20px; border-left: 4px solid #ffc107; margin-top: 20px;">
                <h3><?php _e('📝 Available Shortcodes', 'jblund-dealers'); ?></h3>
                <table class="widefat" style="margin-top: 10px;">
                    <thead>
                        <tr>
                            <th><?php _e('Shortcode', 'jblund-dealers'); ?></th>
                            <th><?php _e('Description', 'jblund-dealers'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>[jblund_dealer_login]</code></td>
                            <td><?php _e('Displays the dealer login form with registration link', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[jblund_dealer_dashboard]</code></td>
                            <td><?php _e('Displays the dealer dashboard with welcome message and quick links', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[jblund_dealer_profile]</code></td>
                            <td><?php _e('Displays the dealer profile editor where dealers can update their information', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[jblund_nda_acceptance]</code></td>
                            <td><?php _e('Displays the NDA acceptance form (automatically shown to new dealers)', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[jblund_dealers]</code></td>
                            <td><?php _e('Displays the public dealer directory (use on any public page)', 'jblund-dealers'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php submit_button(__('Save Page Assignments', 'jblund-dealers')); ?>
        </form>
        <?php
    }

    /**
     * Render Appearance tab
     */
    private function render_appearance_tab() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('jblund_dealers_settings'); ?>

            <div class="appearance-customization-wrapper">

                <!-- Theme Integration -->
                <div class="settings-section">
                    <h3><?php _e('Theme Integration', 'jblund-dealers'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="inherit_theme_styles"><?php _e('Inherit Theme Styles', 'jblund-dealers'); ?></label>
                            </th>
                            <td>
                                <?php
                                $options = get_option('jblund_dealers_settings');
                                $inherit = isset($options['inherit_theme_styles']) ? $options['inherit_theme_styles'] : '0';
                                ?>
                                <label>
                                    <input type="checkbox"
                                           name="jblund_dealers_settings[inherit_theme_styles]"
                                           id="inherit_theme_styles"
                                           value="1"
                                           <?php checked($inherit, '1'); ?> />
                                    <?php _e('Use theme colors, fonts, and styling instead of custom styles', 'jblund-dealers'); ?>
                                </label>
                                <p class="description">
                                    <?php _e('When enabled, dealer cards will inherit your theme\'s primary colors, typography, and button styles. Custom settings below will be ignored.', 'jblund-dealers'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Collapsible Sections -->
                <div class="jblund-appearance-sections">

                    <!-- Colors Section -->
                    <div class="jblund-section-wrapper">
                        <h3 class="jblund-section-title" data-section="colors">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Colors', 'jblund-dealers'); ?>
                            <span class="section-badge"><?php _e('10 Options', 'jblund-dealers'); ?></span>
                        </h3>
                        <div class="jblund-section-content" id="section-colors">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="header_color"><?php _e('Card Header Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->color_field_callback(array('field' => 'header_color', 'default' => '#0073aa')); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="card_background"><?php _e('Card Background Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->color_field_callback(array('field' => 'card_background', 'default' => '#ffffff')); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="button_color"><?php _e('Button Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->color_field_callback(array('field' => 'button_color', 'default' => '#0073aa')); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="text_color"><?php _e('Primary Text Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->color_field_callback(array('field' => 'text_color', 'default' => '#333333')); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="secondary_text_color"><?php _e('Secondary Text Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->color_field_callback(array('field' => 'secondary_text_color', 'default' => '#666666')); ?>
                                    <p class="description"><?php _e('Used for addresses, phone numbers, and other secondary info', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="border_color"><?php _e('Border Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->color_field_callback(array('field' => 'border_color', 'default' => '#dddddd')); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="button_text_color"><?php _e('Button Text Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->color_field_callback(array('field' => 'button_text_color', 'default' => '#ffffff')); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="icon_color"><?php _e('Icon Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->color_field_callback(array('field' => 'icon_color', 'default' => '#0073aa')); ?>
                                    <p class="description"><?php _e('Color for service icons (docks, lifts, trailers)', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="link_color"><?php _e('Link Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->color_field_callback(array('field' => 'link_color', 'default' => '#0073aa')); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="hover_background"><?php _e('Card Hover Background', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->color_field_callback(array('field' => 'hover_background', 'default' => '#f9f9f9')); ?>
                                    <p class="description"><?php _e('Background color when hovering over cards', 'jblund-dealers'); ?></p></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Typography Section -->
                    <div class="jblund-section-wrapper">
                        <h3 class="jblund-section-title" data-section="typography">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Typography', 'jblund-dealers'); ?>
                            <span class="section-badge"><?php _e('4 Options', 'jblund-dealers'); ?></span>
                        </h3>
                        <div class="jblund-section-content" id="section-typography">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="heading_font_size"><?php _e('Heading Font Size', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->range_field_callback(array('field' => 'heading_font_size', 'default' => '24', 'min' => '16', 'max' => '32', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Dealer name/company name size', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="body_font_size"><?php _e('Body Font Size', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->range_field_callback(array('field' => 'body_font_size', 'default' => '14', 'min' => '12', 'max' => '18', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Address, phone, and contact info size', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="heading_font_weight"><?php _e('Heading Font Weight', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->select_field_callback(array(
                                        'field' => 'heading_font_weight',
                                        'options' => array(
                                            'normal' => __('Normal', 'jblund-dealers'),
                                            '600' => __('Semi-Bold (600)', 'jblund-dealers'),
                                            'bold' => __('Bold (700)', 'jblund-dealers'),
                                            '800' => __('Extra Bold (800)', 'jblund-dealers')
                                        ),
                                        'default' => 'bold'
                                    )); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="line_height"><?php _e('Line Height', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->range_field_callback(array('field' => 'line_height', 'default' => '1.6', 'min' => '1.2', 'max' => '2.0', 'step' => '0.1', 'unit' => '')); ?>
                                    <p class="description"><?php _e('Text line spacing', 'jblund-dealers'); ?></p></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Spacing Section -->
                    <div class="jblund-section-wrapper">
                        <h3 class="jblund-section-title" data-section="spacing">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Spacing & Layout', 'jblund-dealers'); ?>
                            <span class="section-badge"><?php _e('6 Options', 'jblund-dealers'); ?></span>
                        </h3>
                        <div class="jblund-section-content" id="section-spacing">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="card_padding"><?php _e('Card Padding', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->range_field_callback(array('field' => 'card_padding', 'default' => '20', 'min' => '10', 'max' => '40', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Internal spacing inside dealer cards', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="card_margin"><?php _e('Card Margin', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->range_field_callback(array('field' => 'card_margin', 'default' => '15', 'min' => '10', 'max' => '30', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Spacing around each card', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="grid_gap"><?php _e('Grid Gap', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->range_field_callback(array('field' => 'grid_gap', 'default' => '20', 'min' => '15', 'max' => '50', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Space between cards in grid layout', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="border_radius"><?php _e('Border Radius', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->range_field_callback(array('field' => 'border_radius', 'default' => '8', 'min' => '0', 'max' => '20', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Rounded corners (0 = square)', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="border_width"><?php _e('Border Width', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->range_field_callback(array('field' => 'border_width', 'default' => '1', 'min' => '0', 'max' => '5', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Card border thickness (0 = no border)', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="border_style"><?php _e('Border Style', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->select_field_callback(array(
                                        'field' => 'border_style',
                                        'options' => array(
                                            'solid' => __('Solid', 'jblund-dealers'),
                                            'dashed' => __('Dashed', 'jblund-dealers'),
                                            'dotted' => __('Dotted', 'jblund-dealers'),
                                            'none' => __('None', 'jblund-dealers')
                                        ),
                                        'default' => 'solid'
                                    )); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Effects Section -->
                    <div class="jblund-section-wrapper">
                        <h3 class="jblund-section-title" data-section="effects">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Visual Effects', 'jblund-dealers'); ?>
                            <span class="section-badge"><?php _e('4 Options', 'jblund-dealers'); ?></span>
                        </h3>
                        <div class="jblund-section-content" id="section-effects">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="box_shadow"><?php _e('Box Shadow', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->select_field_callback(array(
                                        'field' => 'box_shadow',
                                        'options' => array(
                                            'none' => __('None', 'jblund-dealers'),
                                            'light' => __('Light', 'jblund-dealers'),
                                            'medium' => __('Medium', 'jblund-dealers'),
                                            'heavy' => __('Heavy', 'jblund-dealers')
                                        ),
                                        'default' => 'light'
                                    )); ?>
                                    <p class="description"><?php _e('Card elevation/depth effect', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="hover_effect"><?php _e('Hover Effect', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->select_field_callback(array(
                                        'field' => 'hover_effect',
                                        'options' => array(
                                            'none' => __('None', 'jblund-dealers'),
                                            'lift' => __('Lift Up', 'jblund-dealers'),
                                            'scale' => __('Scale', 'jblund-dealers'),
                                            'shadow' => __('Shadow Increase', 'jblund-dealers')
                                        ),
                                        'default' => 'lift'
                                    )); ?>
                                    <p class="description"><?php _e('Animation when hovering over cards', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="transition_speed"><?php _e('Transition Speed', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->range_field_callback(array('field' => 'transition_speed', 'default' => '0.3', 'min' => '0.1', 'max' => '0.5', 'step' => '0.1', 'unit' => 's')); ?>
                                    <p class="description"><?php _e('Animation speed for hover effects', 'jblund-dealers'); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="icon_size"><?php _e('Icon Size', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->range_field_callback(array('field' => 'icon_size', 'default' => '24', 'min' => '16', 'max' => '32', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Service icon size (docks, lifts, trailers)', 'jblund-dealers'); ?></p></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Custom CSS Section -->
                    <div class="jblund-section-wrapper">
                        <h3 class="jblund-section-title" data-section="custom-css">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Custom CSS', 'jblund-dealers'); ?>
                            <span class="section-badge"><?php _e('Advanced', 'jblund-dealers'); ?></span>
                        </h3>
                        <div class="jblund-section-content" id="section-custom-css">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="custom_css"><?php _e('Custom CSS', 'jblund-dealers'); ?></label></th>
                                    <td><?php $this->textarea_field_callback(array('field' => 'custom_css', 'default' => '')); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div><!-- .jblund-appearance-sections -->

            </div><!-- .appearance-customization-wrapper -->

            <?php submit_button(); ?>
        </form>

        <!-- JavaScript for collapsible sections -->
        <script>
        jQuery(document).ready(function($) {
            // Toggle sections
            $('.jblund-section-title').on('click', function() {
                var $title = $(this);
                var $content = $title.next('.jblund-section-content');
                var $icon = $title.find('.dashicons');

                $content.slideToggle(300);
                $icon.toggleClass('dashicons-arrow-right-alt2 dashicons-arrow-down-alt2');
                $title.toggleClass('active');
            });

            // Open first section by default
            $('.jblund-section-title:first').trigger('click');
        });
        </script>

        <!-- CSS for appearance customization -->
        <style>
        .appearance-customization-wrapper {
            max-width: 1200px;
        }
        .jblund-appearance-sections {
            margin-top: 20px;
        }
        .jblund-section-wrapper {
            background: #fff;
            border: 1px solid #ccd0d4;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .jblund-section-title {
            margin: 0;
            padding: 15px 20px;
            cursor: pointer;
            user-select: none;
            background: #f6f7f7;
            border-bottom: 1px solid #ccd0d4;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .jblund-section-title:hover {
            background: #e8eaeb;
        }
        .jblund-section-title.active {
            background: #fff;
        }
        .jblund-section-title .dashicons {
            transition: transform 0.2s;
            font-size: 20px;
            width: 20px;
            height: 20px;
        }
        .jblund-section-title .section-badge {
            margin-left: auto;
            background: #2271b1;
            color: #fff;
            padding: 2px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .jblund-section-content {
            display: none;
            padding: 20px;
        }
        .range-field-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .jblund-range-slider {
            flex: 1;
            max-width: 300px;
        }
        .range-value {
            min-width: 60px;
            font-weight: 600;
            color: #2271b1;
        }
        </style>
        <?php
    }

    /**
     * Render Portal Updates tab
     */
    /**
     * Render Portal Updates tab
     */
    private function render_portal_tab() {
        ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('jblund_dealers_settings');
            ?>
            <div class="settings-section">
                <?php $this->portal_updates_section_callback(); ?>
                <?php $this->portal_updates_field_callback(); ?>
            </div>
            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * Render NDA Editor tab
     */
    private function render_nda_tab() {
        // Check if NDA_Editor class exists
        if (class_exists('JBLund\DealerPortal\NDA_Editor')) {
            // Get NDA Editor instance and render its page
            $nda_editor = new JBLund\DealerPortal\NDA_Editor();
            $nda_editor->render_settings_page();
        } else {
            ?>
            <div class="notice notice-warning">
                <p><?php _e('NDA Editor is not available. The dealer portal module may not be loaded.', 'jblund-dealers'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Render Import/Export tab
     */
    private function render_import_export_tab() {
        // Check if we need to show column mapping interface
        if (isset($_POST['action']) && $_POST['action'] === 'upload_csv' &&
            isset($_POST['jblund_dealers_upload_nonce']) &&
            wp_verify_nonce($_POST['jblund_dealers_upload_nonce'], 'jblund_dealers_upload')) {

            // Show column mapping interface
            $this->render_column_mapping_interface();
        } else {
            // Show regular import/export interface
            ?>
            <div class="settings-section">
                <h3><?php _e('CSV Import/Export', 'jblund-dealers'); ?></h3>
                <p><?php _e('Import or export all dealer data for bulk management and server migration.', 'jblund-dealers'); ?></p>
                <?php $this->csv_operations_callback(); ?>
            </div>
            <?php
        }
    }

    /**
     * Render Help & Guide tab
     */
    private function render_help_tab() {
        ?>
        <div class="help-section">
            <h3><?php _e('Getting Started', 'jblund-dealers'); ?></h3>
            <p><?php _e('Welcome to JBLund Dealers! This plugin provides a complete dealer management system with public listings and a private dealer portal.', 'jblund-dealers'); ?></p>
        </div>

        <div class="settings-section">
            <h3><?php _e('Adding Dealer Locations (Public Listings)', 'jblund-dealers'); ?></h3>
            <ol class="step-list">
                <li>
                    <strong><?php _e('Navigate to Dealers', 'jblund-dealers'); ?></strong><br>
                    <?php _e('In your WordPress admin, go to <strong>Dealers → Add New</strong>', 'jblund-dealers'); ?>
                </li>
                <li>
                    <strong><?php _e('Enter Company Information', 'jblund-dealers'); ?></strong><br>
                    <?php _e('The title field will be used as the company name. Fill in address, phone, website, and services offered.', 'jblund-dealers'); ?>
                </li>
                <li>
                    <strong><?php _e('Add Sub-Locations (Optional)', 'jblund-dealers'); ?></strong><br>
                    <?php _e('If the dealer has multiple locations, use the "Add Sub-Location" button to add them. Each can have its own contact info and services.', 'jblund-dealers'); ?>
                </li>
                <li>
                    <strong><?php _e('Link to User Account (Optional)', 'jblund-dealers'); ?></strong><br>
                    <?php _e('In the sidebar, you can link this dealer listing to a WordPress user account for the dealer portal.', 'jblund-dealers'); ?>
                </li>
                <li>
                    <strong><?php _e('Publish', 'jblund-dealers'); ?></strong><br>
                    <?php _e('Click Publish to make the dealer visible on your public dealer directory.', 'jblund-dealers'); ?>
                </li>
            </ol>
        </div>

        <div class="settings-section">
            <h3><?php _e('Creating Dealer Portal Users', 'jblund-dealers'); ?></h3>
            <ol class="step-list">
                <li>
                    <strong><?php _e('Go to Users Section', 'jblund-dealers'); ?></strong><br>
                    <?php _e('Navigate to <strong>Users → Add New</strong> in WordPress admin.', 'jblund-dealers'); ?>
                </li>
                <li>
                    <strong><?php _e('Create User Account', 'jblund-dealers'); ?></strong><br>
                    <?php _e('Enter username, email, and password for the dealer representative.', 'jblund-dealers'); ?>
                </li>
                <li>
                    <strong><?php _e('Select Dealer Role', 'jblund-dealers'); ?></strong><br>
                    <?php _e('Set the role to <strong>"Dealer"</strong> from the dropdown menu.', 'jblund-dealers'); ?>
                </li>
                <li>
                    <strong><?php _e('Send Login Credentials', 'jblund-dealers'); ?></strong><br>
                    <?php _e('The user will receive login credentials via email. They can access the dealer portal by logging in.', 'jblund-dealers'); ?>
                </li>
                <li>
                    <strong><?php _e('Link to Dealer Listing', 'jblund-dealers'); ?></strong><br>
                    <?php _e('Go back to the dealer listing and link this user account in the "User Account" meta box.', 'jblund-dealers'); ?>
                </li>
            </ol>
        </div>

        <div class="settings-section">
            <h3><?php _e('Displaying Dealers on Your Site', 'jblund-dealers'); ?></h3>
            <p><?php _e('Use the shortcode to display dealers anywhere on your site:', 'jblund-dealers'); ?></p>
            <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa; margin: 15px 0;">
                <code style="font-size: 14px;">[jblund_dealers]</code>
            </div>
            <p><strong><?php _e('Shortcode Parameters:', 'jblund-dealers'); ?></strong></p>
            <ul style="list-style: disc; padding-left: 20px;">
                <li><code>layout="grid"</code> - <?php _e('Grid layout (default)', 'jblund-dealers'); ?></li>
                <li><code>layout="list"</code> - <?php _e('Horizontal list layout', 'jblund-dealers'); ?></li>
                <li><code>layout="compact"</code> - <?php _e('Compact grid layout', 'jblund-dealers'); ?></li>
                <li><code>posts_per_page="12"</code> - <?php _e('Number of dealers to display', 'jblund-dealers'); ?></li>
                <li><code>orderby="title"</code> - <?php _e('Sort by title, date, etc.', 'jblund-dealers'); ?></li>
            </ul>
            <p><strong><?php _e('Example:', 'jblund-dealers'); ?></strong></p>
            <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa;">
                <code style="font-size: 14px;">[jblund_dealers layout="list" posts_per_page="6"]</code>
            </div>
        </div>

        <div class="settings-section">
            <h3><?php _e('Dealer Portal Features', 'jblund-dealers'); ?></h3>
            <div class="feature-grid">
                <div class="feature-card">
                    <h4><?php _e('Dashboard', 'jblund-dealers'); ?></h4>
                    <p><?php _e('Dealers can view announcements, quick links, and recent updates.', 'jblund-dealers'); ?></p>
                </div>
                <div class="feature-card">
                    <h4><?php _e('Profile Management', 'jblund-dealers'); ?></h4>
                    <p><?php _e('Dealers can update their contact information and preferences.', 'jblund-dealers'); ?></p>
                </div>
                <div class="feature-card">
                    <h4><?php _e('NDA Acceptance', 'jblund-dealers'); ?></h4>
                    <p><?php _e('New dealers must accept the NDA before accessing portal features.', 'jblund-dealers'); ?></p>
                </div>
                <div class="feature-card">
                    <h4><?php _e('Updates & News', 'jblund-dealers'); ?></h4>
                    <p><?php _e('Admins can post scheduled updates that appear on dealer dashboards.', 'jblund-dealers'); ?></p>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><?php _e('Bulk Import/Export', 'jblund-dealers'); ?></h3>
            <p><?php _e('Use the <strong>Import/Export</strong> tab to:', 'jblund-dealers'); ?></p>
            <ul style="list-style: disc; padding-left: 20px;">
                <li><?php _e('Export all dealer data to CSV for backup or migration', 'jblund-dealers'); ?></li>
                <li><?php _e('Import multiple dealers at once from a CSV file', 'jblund-dealers'); ?></li>
                <li><?php _e('Download a sample CSV to see the correct format', 'jblund-dealers'); ?></li>
                <li><?php _e('Update existing dealers by re-importing with matching IDs', 'jblund-dealers'); ?></li>
            </ul>
        </div>

        <div class="help-section" style="background: #fff3cd; border-left-color: #ffc107;">
            <h3 style="color: #856404;"><?php _e('Future Features (Coming Soon)', 'jblund-dealers'); ?></h3>
            <ul style="list-style: disc; padding-left: 20px;">
                <li><?php _e('<strong>Territory Management:</strong> Assign dealers to specific geographic territories', 'jblund-dealers'); ?></li>
                <li><?php _e('<strong>Territory Mapping:</strong> Visual map showing dealer coverage areas', 'jblund-dealers'); ?></li>
                <li><?php _e('<strong>Advanced Filtering:</strong> Filter dealers by services, territory, or custom criteria', 'jblund-dealers'); ?></li>
                <li><?php _e('<strong>Dealer Registration:</strong> Allow dealers to self-register and request access', 'jblund-dealers'); ?></li>
            </ul>
        </div>

        <div class="settings-section">
            <h3><?php _e('Need Help?', 'jblund-dealers'); ?></h3>
            <p><?php _e('For additional support or feature requests, please contact your site administrator.', 'jblund-dealers'); ?></p>
        </div>
        <?php
    }

    /**
     * Appearance section callback
     */
    public function appearance_section_callback() {
        echo '<p>' . __('Customize the appearance of dealer cards on the frontend.', 'jblund-dealers') . '</p>';
    }

    /**
     * Shortcode section callback
     */
    public function shortcode_section_callback() {
        echo '<p>' . __('Configure default shortcode behavior and layout options.', 'jblund-dealers') . '</p>';
    }

    /**
     * CSV operations callback
     */
    public function csv_operations_callback() {
        ?>
        <div class="csv-operations-container">
            <!-- Export Section -->
            <div class="csv-export-section" style="margin-bottom: 30px; padding: 20px; border: 1px solid #ccd0d4; background: #fff; border-left: 4px solid #72aee6;">
                <h3 style="margin-top: 0;"><?php _e('📥 Export Dealers', 'jblund-dealers'); ?></h3>
                <p><?php _e('Download all dealer data (including sub-locations) as a CSV file for backup, migration, or editing in Excel/Google Sheets.', 'jblund-dealers'); ?></p>

                <p><strong><?php _e('Export includes:', 'jblund-dealers'); ?></strong></p>
                <ul style="list-style: disc; padding-left: 20px; margin-bottom: 20px;">
                    <li><?php _e('All dealer information (name, address, phone, website)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Services offered (docks, lifts, trailers)', 'jblund-dealers'); ?></li>
                    <li><?php _e('GPS coordinates and custom map links', 'jblund-dealers'); ?></li>
                    <li><?php _e('Sub-locations data (if any)', 'jblund-dealers'); ?></li>
                </ul>

                <a href="<?php echo admin_url('edit.php?post_type=dealer&page=jblund-dealers-settings&action=export_csv&_wpnonce=' . wp_create_nonce('jblund_dealers_export')); ?>"
                   class="button button-secondary button-large">
                    <span class="dashicons dashicons-download" style="margin-top: 3px;"></span> <?php _e('Export All Dealers to CSV', 'jblund-dealers'); ?>
                </a>
            </div>

            <!-- Import Section -->
            <div class="csv-import-section" style="padding: 20px; border: 1px solid #ccd0d4; background: #fff; border-left: 4px solid #00a32a;">
                <h3 style="margin-top: 0;"><?php _e('📤 Import Dealers', 'jblund-dealers'); ?></h3>
                <p><?php _e('Upload a CSV file to bulk import or update dealer data. You can use a simple format or the full export format.', 'jblund-dealers'); ?></p>

                <!-- Format Information -->
                <div style="background: #f0f6fc; padding: 15px; margin-bottom: 20px; border-left: 4px solid #0073aa;">
                    <h4 style="margin-top: 0;"><?php _e('📋 Supported CSV Formats:', 'jblund-dealers'); ?></h4>

                    <p><strong><?php _e('Simple Format (recommended for basic imports):', 'jblund-dealers'); ?></strong></p>
                    <code style="display: block; background: #fff; padding: 10px; margin-bottom: 10px; overflow-x: auto;">
                        name,street,city_state_zip,phone,website,docks,lifts,trailers
                    </code>
                    <p class="description"><?php _e('Services should be 1 (yes) or 0 (no)', 'jblund-dealers'); ?></p>

                    <p style="margin-top: 15px;"><strong><?php _e('Full Format (from export):', 'jblund-dealers'); ?></strong></p>
                    <code style="display: block; background: #fff; padding: 10px; overflow-x: auto;">
                        ID,Company Name,Address,Phone,Website,Latitude,Longitude,Custom Map Link,Docks,Lifts,Trailers,Sub-Locations
                    </code>
                    <p class="description"><?php _e('Services should be "Yes" or "No". Export first to see this format.', 'jblund-dealers'); ?></p>
                </div>

                <!-- Import Form -->
                <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('edit.php?post_type=dealer&page=jblund-dealers-settings&tab=import-export'); ?>" style="margin-top: 20px;">
                    <?php wp_nonce_field('jblund_dealers_upload', 'jblund_dealers_upload_nonce'); ?>
                    <input type="hidden" name="action" value="upload_csv" />

                    <p>
                        <label for="csv_file" style="font-weight: 600; display: block; margin-bottom: 5px;">
                            <?php _e('Choose CSV File:', 'jblund-dealers'); ?>
                        </label>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv" required style="margin-top: 5px;" />
                    </p>

                    <p style="margin-top: 20px;">
                        <button type="submit" class="button button-primary button-large">
                            <span class="dashicons dashicons-arrow-right-alt2" style="margin-top: 3px;"></span>
                            <?php _e('Next: Map Columns', 'jblund-dealers'); ?>
                        </button>
                    </p>
                </form>

                <!-- Import Notes -->
                <div style="background: #fff3cd; padding: 15px; margin-top: 20px; border-left: 4px solid #ffc107;">
                    <h4 style="margin-top: 0; color: #856404;">⚠️ <?php _e('Important Notes:', 'jblund-dealers'); ?></h4>
                    <ul style="list-style: disc; padding-left: 20px; margin: 0; color: #856404;">
                        <li><?php _e('CSV must have a header row with column names', 'jblund-dealers'); ?></li>
                        <li><?php _e('If ID column is present and matches an existing dealer, it will be updated', 'jblund-dealers'); ?></li>
                        <li><?php _e('If no ID or ID doesn\'t exist, a new dealer will be created', 'jblund-dealers'); ?></li>
                        <li><?php _e('Address can be in one field or split into "street" and "city_state_zip"', 'jblund-dealers'); ?></li>
                        <li><?php _e('Test with 1-2 dealers first to ensure your format is correct', 'jblund-dealers'); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <style>
            .csv-operations-container h3 {
                color: #1d2327;
                font-size: 18px;
            }
            .csv-operations-container .button-large {
                height: auto;
                padding: 10px 20px;
                font-size: 14px;
            }
            .csv-operations-container code {
                font-size: 12px;
                font-family: Consolas, Monaco, monospace;
            }
        </style>
        <?php
    }

    /**
     * Portal updates section callback
     */
    public function portal_updates_section_callback() {
        echo '<p>' . __('Manage updates that appear on the dealer dashboard "Recent Updates" card. Add announcements, news, or important information for dealers.', 'jblund-dealers') . '</p>';
    }

    /**
     * Portal updates field callback
     */
    public function portal_updates_field_callback() {
        $options = get_option('jblund_dealers_settings');
        $updates = isset($options['portal_updates']) ? $options['portal_updates'] : array();

        // Ensure updates is an array
        if (!is_array($updates)) {
            $updates = array();
        }
        ?>
        <div id="portal-updates-manager">
            <div id="updates-list" style="margin-bottom: 20px;">
                <?php if (!empty($updates)) : ?>
                    <?php foreach ($updates as $index => $update) : ?>
                        <?php $this->render_update_row($index, $update); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" class="button button-secondary" id="add-update">
                <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
                <?php _e('Add Update', 'jblund-dealers'); ?>
            </button>

            <p class="description" style="margin-top: 15px;">
                <?php _e('Updates appear on the dealer dashboard in reverse chronological order. They can be scheduled with start and end dates.', 'jblund-dealers'); ?>
            </p>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var updateIndex = <?php echo count($updates); ?>;

            // Add new update
            $('#add-update').on('click', function() {
                var template = `
                    <div class="update-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
                        <button type="button" class="button button-link-delete remove-update" style="position: absolute; top: 10px; right: 10px; color: #dc3232;">
                            <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'jblund-dealers'); ?>
                        </button>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Update Title:', 'jblund-dealers'); ?></strong>
                            </label>
                            <input type="text"
                                   name="jblund_dealers_settings[portal_updates][${updateIndex}][title]"
                                   class="large-text"
                                   placeholder="<?php _e('e.g., New Product Launch, Price Update, etc.', 'jblund-dealers'); ?>" />
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Message:', 'jblund-dealers'); ?></strong>
                            </label>
                            <textarea name="jblund_dealers_settings[portal_updates][${updateIndex}][message]"
                                      rows="3"
                                      class="large-text"
                                      placeholder="<?php _e('Enter your update message here...', 'jblund-dealers'); ?>"></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px;">
                                    <strong><?php _e('Start Date (Optional):', 'jblund-dealers'); ?></strong>
                                </label>
                                <input type="date"
                                       name="jblund_dealers_settings[portal_updates][${updateIndex}][start_date]"
                                       class="regular-text" />
                                <p class="description"><?php _e('Update will appear starting from this date', 'jblund-dealers'); ?></p>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px;">
                                    <strong><?php _e('End Date (Optional):', 'jblund-dealers'); ?></strong>
                                </label>
                                <input type="date"
                                       name="jblund_dealers_settings[portal_updates][${updateIndex}][end_date]"
                                       class="regular-text" />
                                <p class="description"><?php _e('Update will be hidden after this date', 'jblund-dealers'); ?></p>
                            </div>
                        </div>
                    </div>
                `;

                $('#updates-list').append(template);
                updateIndex++;
            });

            // Remove update
            $(document).on('click', '.remove-update', function() {
                if (confirm('<?php _e('Are you sure you want to remove this update?', 'jblund-dealers'); ?>')) {
                    $(this).closest('.update-row').remove();
                }
            });
        });
        </script>

        <style>
        .update-row .dashicons {
            margin-top: 3px;
        }
        .update-row label strong {
            color: #23282d;
        }
        </style>
        <?php
    }

    /**
     * Render a single update row
     */
    private function render_update_row($index, $update) {
        $title = isset($update['title']) ? $update['title'] : '';
        $message = isset($update['message']) ? $update['message'] : '';
        $start_date = isset($update['start_date']) ? $update['start_date'] : '';
        $end_date = isset($update['end_date']) ? $update['end_date'] : '';
        ?>
        <div class="update-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
            <button type="button" class="button button-link-delete remove-update" style="position: absolute; top: 10px; right: 10px; color: #dc3232;">
                <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'jblund-dealers'); ?>
            </button>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Update Title:', 'jblund-dealers'); ?></strong>
                </label>
                <input type="text"
                       name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][title]"
                       value="<?php echo esc_attr($title); ?>"
                       class="large-text"
                       placeholder="<?php _e('e.g., New Product Launch, Price Update, etc.', 'jblund-dealers'); ?>" />
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Message:', 'jblund-dealers'); ?></strong>
                </label>
                <textarea name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][message]"
                          rows="3"
                          class="large-text"
                          placeholder="<?php _e('Enter your update message here...', 'jblund-dealers'); ?>"><?php echo esc_textarea($message); ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px;">
                        <strong><?php _e('Start Date (Optional):', 'jblund-dealers'); ?></strong>
                    </label>
                    <input type="date"
                           name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][start_date]"
                           value="<?php echo esc_attr($start_date); ?>"
                           class="regular-text" />
                    <p class="description"><?php _e('Update will appear starting from this date', 'jblund-dealers'); ?></p>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px;">
                        <strong><?php _e('End Date (Optional):', 'jblund-dealers'); ?></strong>
                    </label>
                    <input type="date"
                           name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][end_date]"
                           value="<?php echo esc_attr($end_date); ?>"
                           class="regular-text" />
                    <p class="description"><?php _e('Update will be hidden after this date', 'jblund-dealers'); ?></p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Color field callback
     */
    public function color_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<input type="color" name="jblund_dealers_settings[' . $args['field'] . ']" value="' . esc_attr($value) . '" />';
    }

    /**
     * Select field callback
     */
    public function select_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];

        echo '<select name="jblund_dealers_settings[' . $args['field'] . ']">';
        foreach ($args['options'] as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($value, $key, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    /**
     * Checkbox field callback
     */
    public function checkbox_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<input type="checkbox" name="jblund_dealers_settings[' . $args['field'] . ']" value="1" ' . checked($value, '1', false) . ' />';
    }

    /**
     * Range field callback (slider)
     */
    public function range_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        $min = isset($args['min']) ? $args['min'] : 0;
        $max = isset($args['max']) ? $args['max'] : 100;
        $step = isset($args['step']) ? $args['step'] : 1;
        $unit = isset($args['unit']) ? $args['unit'] : '';

        echo '<div class="range-field-wrapper">';
        echo '<input type="range" name="jblund_dealers_settings[' . esc_attr($args['field']) . ']" ';
        echo 'id="' . esc_attr($args['field']) . '" ';
        echo 'value="' . esc_attr($value) . '" ';
        echo 'min="' . esc_attr($min) . '" ';
        echo 'max="' . esc_attr($max) . '" ';
        echo 'step="' . esc_attr($step) . '" ';
        echo 'class="jblund-range-slider" />';
        echo '<span class="range-value" id="' . esc_attr($args['field']) . '_value">' . esc_html($value . $unit) . '</span>';
        echo '</div>';
        echo '<script>
        document.getElementById("' . esc_js($args['field']) . '").addEventListener("input", function(e) {
            document.getElementById("' . esc_js($args['field']) . '_value").textContent = e.target.value + "' . esc_js($unit) . '";
        });
        </script>';
    }

    /**
     * Textarea field callback
     */
    public function textarea_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<textarea name="jblund_dealers_settings[' . esc_attr($args['field']) . ']" ';
        echo 'rows="10" cols="50" class="large-text code">';
        echo esc_textarea($value);
        echo '</textarea>';

        if ($args['field'] === 'custom_css') {
            echo '<p class="description">' . __('Add custom CSS to further customize the appearance. Use standard CSS syntax.', 'jblund-dealers') . '</p>';
            echo '<p class="description"><strong>' . __('Example:', 'jblund-dealers') . '</strong> <code>.dealer-card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }</code></p>';
        }
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

    /**
     * Generate map link for address
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

    /**
     * Export dealers to CSV
     */
    public function export_dealers_csv() {
        // Security checks
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'jblund-dealers'));
        }

        if (!wp_verify_nonce($_GET['_wpnonce'], 'jblund_dealers_export')) {
            wp_die(__('Security check failed.', 'jblund-dealers'));
        }

        // Clean any existing output
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Prevent any other output
        ob_start();

        // Get all dealers
        $dealers = get_posts(array(
            'post_type' => 'dealer',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));

        // Debug mode - check if there are dealers
        if (empty($dealers)) {
            ob_end_clean();
            wp_die(__('No dealers found to export.', 'jblund-dealers'));
        }

        // Prepare CSV data
        $csv_data = array();

        // Headers
        $headers = array(
            'ID',
            'Company Name',
            'Address',
            'Phone',
            'Website',
            'Latitude',
            'Longitude',
            'Custom Map Link',
            'Docks',
            'Lifts',
            'Trailers',
            'Sub-Locations'
        );
        $csv_data[] = $headers;

        // Process each dealer
        foreach ($dealers as $dealer) {
            $post_id = $dealer->ID;
            $company_name = $dealer->post_title;
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

            // Format sub-locations as JSON for CSV
            $sublocations_json = !empty($sublocations) && is_array($sublocations) ? json_encode($sublocations) : '';

            $csv_data[] = array(
                $post_id,
                $company_name,
                $company_address,
                $company_phone,
                $website,
                $latitude,
                $longitude,
                $custom_map_link,
                $docks == '1' ? 'Yes' : 'No',
                $lifts == '1' ? 'Yes' : 'No',
                $trailers == '1' ? 'Yes' : 'No',
                $sublocations_json
            );
        }

        // Generate CSV content
        $csv_content = $this->array_to_csv($csv_data);

        // Add BOM for Excel compatibility
        $csv_content = "\xEF\xBB\xBF" . $csv_content;

        // Generate filename
        $filename = 'jblund-dealers-export-' . date('Y-m-d-H-i-s') . '.csv';

        // Clear any buffered output
        ob_end_clean();

        // Send headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');

        // Output CSV
        echo $csv_content;
        exit;
    }

    /**
     * Convert array to CSV string
     */
    private function array_to_csv($data) {
        $output = fopen('php://temp', 'r+');
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $csv;
    }

    /**
     * Render column mapping interface
     */
    private function render_column_mapping_interface() {
        // Security check
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'jblund-dealers'));
        }

        // Check if file was uploaded
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            echo '<div class="notice notice-error"><p>' . __('Error uploading CSV file.', 'jblund-dealers') . '</p></div>';
            $this->csv_operations_callback();
            return;
        }

        // Read CSV file
        $csv_file = $_FILES['csv_file']['tmp_name'];
        $csv_data = array_map('str_getcsv', file($csv_file));

        if (empty($csv_data)) {
            echo '<div class="notice notice-error"><p>' . __('CSV file is empty.', 'jblund-dealers') . '</p></div>';
            $this->csv_operations_callback();
            return;
        }

        // Get headers and preview rows
        $headers = array_shift($csv_data);
        $preview_rows = array_slice($csv_data, 0, 5); // First 5 rows for preview

        // Store CSV in temp file for later processing
        $temp_file = wp_tempnam();
        file_put_contents($temp_file, file_get_contents($_FILES['csv_file']['tmp_name']));

        // Available dealer fields
        $dealer_fields = array(
            '' => __('-- Do Not Import --', 'jblund-dealers'),
            'name' => __('Company Name', 'jblund-dealers'),
            'address' => __('Address (Full)', 'jblund-dealers'),
            'street' => __('Street Address', 'jblund-dealers'),
            'city_state_zip' => __('City, State, ZIP', 'jblund-dealers'),
            'phone' => __('Phone Number', 'jblund-dealers'),
            'website' => __('Website URL', 'jblund-dealers'),
            'latitude' => __('Latitude', 'jblund-dealers'),
            'longitude' => __('Longitude', 'jblund-dealers'),
            'custom_map_link' => __('Custom Map Link', 'jblund-dealers'),
            'docks' => __('Docks (1/0 or Yes/No)', 'jblund-dealers'),
            'lifts' => __('Lifts (1/0 or Yes/No)', 'jblund-dealers'),
            'trailers' => __('Trailers (1/0 or Yes/No)', 'jblund-dealers'),
        );

        ?>
        <div class="wrap">
            <h2><?php _e('Map CSV Columns to Dealer Fields', 'jblund-dealers'); ?></h2>

            <div style="background: #f0f6fc; padding: 15px; margin: 20px 0; border-left: 4px solid #0073aa;">
                <p style="margin: 0;">
                    <strong><?php _e('📋 Instructions:', 'jblund-dealers'); ?></strong><br>
                    <?php _e('Review the CSV preview below and map each column to the appropriate dealer field. Columns you don\'t need can be set to "Do Not Import".', 'jblund-dealers'); ?>
                </p>
            </div>

            <form method="post" action="<?php echo admin_url('edit.php?post_type=dealer&page=jblund-dealers-settings&tab=import-export'); ?>">
                <?php wp_nonce_field('jblund_dealers_import', 'jblund_dealers_import_nonce'); ?>
                <input type="hidden" name="action" value="import_csv" />
                <input type="hidden" name="temp_file" value="<?php echo esc_attr(basename($temp_file)); ?>" />

                <div class="column-mapping-container" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; margin: 20px 0;">
                    <table class="widefat" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th style="width: 200px;"><?php _e('CSV Column', 'jblund-dealers'); ?></th>
                                <th style="width: 250px;"><?php _e('Map To Dealer Field', 'jblund-dealers'); ?></th>
                                <th><?php _e('Preview Data', 'jblund-dealers'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($headers as $index => $header):
                                // Auto-detect mapping based on column name
                                $header_lower = strtolower(trim($header));
                                $auto_map = '';

                                if (in_array($header_lower, ['name', 'company name', 'company_name', 'dealer name'])) {
                                    $auto_map = 'name';
                                } elseif (in_array($header_lower, ['address', 'full address', 'company address'])) {
                                    $auto_map = 'address';
                                } elseif (in_array($header_lower, ['street', 'street address'])) {
                                    $auto_map = 'street';
                                } elseif (in_array($header_lower, ['city_state_zip', 'city state zip', 'city/state/zip'])) {
                                    $auto_map = 'city_state_zip';
                                } elseif (in_array($header_lower, ['phone', 'telephone', 'phone number'])) {
                                    $auto_map = 'phone';
                                } elseif (in_array($header_lower, ['website', 'url', 'web'])) {
                                    $auto_map = 'website';
                                } elseif ($header_lower === 'latitude' || $header_lower === 'lat') {
                                    $auto_map = 'latitude';
                                } elseif ($header_lower === 'longitude' || $header_lower === 'lon' || $header_lower === 'lng') {
                                    $auto_map = 'longitude';
                                } elseif (in_array($header_lower, ['custom map link', 'map link'])) {
                                    $auto_map = 'custom_map_link';
                                } elseif ($header_lower === 'docks') {
                                    $auto_map = 'docks';
                                } elseif ($header_lower === 'lifts') {
                                    $auto_map = 'lifts';
                                } elseif ($header_lower === 'trailers') {
                                    $auto_map = 'trailers';
                                }
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($header); ?></strong>
                                        <?php if ($auto_map): ?>
                                            <br><small style="color: #008a00;">✓ <?php _e('Auto-detected', 'jblund-dealers'); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select name="column_mapping[<?php echo $index; ?>]" class="regular-text" style="width: 100%;">
                                            <?php foreach ($dealer_fields as $value => $label): ?>
                                                <option value="<?php echo esc_attr($value); ?>" <?php selected($auto_map, $value); ?>>
                                                    <?php echo esc_html($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <div style="max-height: 100px; overflow-y: auto; font-size: 12px; color: #666;">
                                            <?php
                                            $samples = array();
                                            foreach ($preview_rows as $row) {
                                                if (isset($row[$index]) && !empty(trim($row[$index]))) {
                                                    $samples[] = esc_html($row[$index]);
                                                }
                                            }
                                            echo implode('<br>', array_slice($samples, 0, 3));
                                            if (count($samples) > 3) {
                                                echo '<br>...';
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #ccd0d4;">
                    <h3 style="margin-top: 0;"><?php _e('📊 CSV Preview', 'jblund-dealers'); ?></h3>
                    <p><?php printf(__('Showing first %d rows from your CSV file:', 'jblund-dealers'), count($preview_rows)); ?></p>

                    <div style="overflow-x: auto;">
                        <table class="widefat striped" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">#</th>
                                    <?php foreach ($headers as $header): ?>
                                        <th><?php echo esc_html($header); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($preview_rows as $row_num => $row): ?>
                                    <tr>
                                        <td><strong><?php echo ($row_num + 1); ?></strong></td>
                                        <?php foreach ($row as $cell): ?>
                                            <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?php echo esc_html($cell); ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="margin: 20px 0; padding: 15px; background: #f0f6fc; border-left: 4px solid #0073aa;">
                    <p style="margin: 0;">
                        <strong><?php _e('Ready to import?', 'jblund-dealers'); ?></strong><br>
                        <?php printf(__('Your CSV has %d total rows. Review the column mapping above and click Import when ready.', 'jblund-dealers'), count($csv_data)); ?>
                    </p>
                </div>

                <p>
                    <button type="submit" class="button button-primary button-large">
                        <span class="dashicons dashicons-upload" style="margin-top: 3px;"></span>
                        <?php _e('Import Dealers', 'jblund-dealers'); ?>
                    </button>
                    <a href="<?php echo admin_url('edit.php?post_type=dealer&page=jblund-dealers-settings&tab=import-export'); ?>" class="button button-secondary button-large">
                        <?php _e('Cancel', 'jblund-dealers'); ?>
                    </a>
                </p>
            </form>
        </div>

        <style>
            .column-mapping-container select {
                font-size: 13px;
            }
            .column-mapping-container td {
                vertical-align: top;
                padding: 12px !important;
            }
        </style>
        <?php
    }

    /**
     * Import dealers from CSV
     */
    public function import_dealers_csv() {
        // Security checks
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'jblund-dealers'));
        }

        if (!wp_verify_nonce($_POST['jblund_dealers_import_nonce'], 'jblund_dealers_import')) {
            wp_die(__('Security check failed.', 'jblund-dealers'));
        }

        // Get column mapping
        $column_mapping = isset($_POST['column_mapping']) ? $_POST['column_mapping'] : array();

        // Get temp file path
        $temp_filename = isset($_POST['temp_file']) ? sanitize_file_name($_POST['temp_file']) : '';
        $temp_file = get_temp_dir() . $temp_filename;

        if (!file_exists($temp_file)) {
            $redirect_url = add_query_arg(
                array(
                    'post_type' => 'dealer',
                    'page' => 'jblund-dealers-settings',
                    'tab' => 'import-export',
                    'import_error' => 'upload_failed'
                ),
                admin_url('edit.php')
            );
            wp_redirect($redirect_url);
            exit;
        }

        // Read CSV file
        $csv_data = array_map('str_getcsv', file($temp_file));

        if (empty($csv_data)) {
            unlink($temp_file); // Clean up
            $redirect_url = add_query_arg(
                array(
                    'post_type' => 'dealer',
                    'page' => 'jblund-dealers-settings',
                    'tab' => 'import-export',
                    'import_error' => 'empty_file'
                ),
                admin_url('edit.php')
            );
            wp_redirect($redirect_url);
            exit;
        }

        // Remove headers
        array_shift($csv_data);

        // Reverse the mapping (column index => field name)
        $field_mapping = array();
        foreach ($column_mapping as $col_index => $field_name) {
            if (!empty($field_name)) {
                $field_mapping[intval($col_index)] = $field_name;
            }
        }

        $imported = 0;
        $updated = 0;
        $errors = 0;

        // Process each row
        foreach ($csv_data as $row_index => $row) {
            if (empty($row)) continue;

            try {
                // Build dealer data from mapped columns
                $dealer_data = array();
                foreach ($field_mapping as $col_index => $field_name) {
                    if (isset($row[$col_index])) {
                        $dealer_data[$field_name] = $row[$col_index];
                    }
                }

                // Get company name
                $company_name = '';
                if (isset($dealer_data['name'])) {
                    $company_name = sanitize_text_field($dealer_data['name']);
                }

                if (empty($company_name)) {
                    $errors++;
                    continue;
                }

                // Create new dealer
                $post_data = array(
                    'post_title' => $company_name,
                    'post_type' => 'dealer',
                    'post_status' => 'publish'
                );
                $post_id = wp_insert_post($post_data);

                if (is_wp_error($post_id) || !$post_id) {
                    $errors++;
                    continue;
                }

                $imported++;

                // Update meta data using mapped columns
                $this->update_dealer_meta_from_mapped_data($post_id, $dealer_data);

            } catch (Exception $e) {
                $errors++;
                continue;
            }
        }

        // Clean up temp file
        unlink($temp_file);

        // Redirect with success message
        $redirect_url = add_query_arg(
            array(
                'post_type' => 'dealer',
                'page' => 'jblund-dealers-settings',
                'tab' => 'import-export',
                'import_success' => 1,
                'imported' => $imported,
                'updated' => $updated,
                'errors' => $errors
            ),
            admin_url('edit.php')
        );

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Update dealer meta from mapped data array
     */
    private function update_dealer_meta_from_mapped_data($post_id, $data) {
        // Update company name meta (synced with post title)
        update_post_meta($post_id, '_dealer_company_name', get_the_title($post_id));

        // Handle Address field (full address in one field)
        if (isset($data['address']) && !empty($data['address'])) {
            update_post_meta($post_id, '_dealer_company_address', sanitize_textarea_field($data['address']));
        }

        // Handle split address fields (street + city_state_zip)
        if (isset($data['street']) || isset($data['city_state_zip'])) {
            $address = '';
            if (isset($data['street'])) {
                $address = trim($data['street']);
            }
            if (isset($data['city_state_zip']) && !empty($data['city_state_zip'])) {
                $address .= ($address ? "\n" : '') . trim($data['city_state_zip']);
            }
            if (!empty($address)) {
                update_post_meta($post_id, '_dealer_company_address', sanitize_textarea_field($address));
            }
        }

        // Update phone
        if (isset($data['phone']) && !empty($data['phone'])) {
            update_post_meta($post_id, '_dealer_company_phone', sanitize_text_field($data['phone']));
        }

        // Update website
        if (isset($data['website']) && !empty($data['website'])) {
            update_post_meta($post_id, '_dealer_website', esc_url_raw($data['website']));
        }

        // Update coordinates
        if (isset($data['latitude']) && !empty($data['latitude'])) {
            update_post_meta($post_id, '_dealer_latitude', sanitize_text_field($data['latitude']));
        }

        if (isset($data['longitude']) && !empty($data['longitude'])) {
            update_post_meta($post_id, '_dealer_longitude', sanitize_text_field($data['longitude']));
        }

        if (isset($data['custom_map_link']) && !empty($data['custom_map_link'])) {
            update_post_meta($post_id, '_dealer_custom_map_link', esc_url_raw($data['custom_map_link']));
        }

        // Update services - handle both "Yes/No" and "1/0" formats
        if (isset($data['docks'])) {
            $docks_value = trim($data['docks']);
            update_post_meta($post_id, '_dealer_docks', ($docks_value === 'Yes' || $docks_value === '1') ? '1' : '0');
        }

        if (isset($data['lifts'])) {
            $lifts_value = trim($data['lifts']);
            update_post_meta($post_id, '_dealer_lifts', ($lifts_value === 'Yes' || $lifts_value === '1') ? '1' : '0');
        }

        if (isset($data['trailers'])) {
            $trailers_value = trim($data['trailers']);
            update_post_meta($post_id, '_dealer_trailers', ($trailers_value === 'Yes' || $trailers_value === '1') ? '1' : '0');
        }
    }

    /**
     * Update dealer meta from CSV row
     */
    private function update_dealer_meta_from_csv($post_id, $row, $header_map) {
        // Update company name meta (synced with post title)
        update_post_meta($post_id, '_dealer_company_name', get_the_title($post_id));

        // Handle Address field (full export format)
        if (isset($header_map['address']) && !empty($row[$header_map['address']])) {
            update_post_meta($post_id, '_dealer_company_address', sanitize_textarea_field($row[$header_map['address']]));
        }

        // Handle split address fields (simple format: street, city_state_zip)
        if (isset($header_map['street']) && isset($header_map['city_state_zip'])) {
            $address = trim($row[$header_map['street']]);
            if (!empty($row[$header_map['city_state_zip']])) {
                $address .= "\n" . trim($row[$header_map['city_state_zip']]);
            }
            if (!empty($address)) {
                update_post_meta($post_id, '_dealer_company_address', sanitize_textarea_field($address));
            }
        }

        // Update phone
        if (isset($header_map['phone']) && !empty($row[$header_map['phone']])) {
            update_post_meta($post_id, '_dealer_company_phone', sanitize_text_field($row[$header_map['phone']]));
        }

        // Update website
        if (isset($header_map['website']) && !empty($row[$header_map['website']])) {
            update_post_meta($post_id, '_dealer_website', esc_url_raw($row[$header_map['website']]));
        }

        // Update coordinates
        if (isset($header_map['latitude']) && !empty($row[$header_map['latitude']])) {
            update_post_meta($post_id, '_dealer_latitude', sanitize_text_field($row[$header_map['latitude']]));
        }

        if (isset($header_map['longitude']) && !empty($row[$header_map['longitude']])) {
            update_post_meta($post_id, '_dealer_longitude', sanitize_text_field($row[$header_map['longitude']]));
        }

        if (isset($header_map['custom map link']) && !empty($row[$header_map['custom map link']])) {
            update_post_meta($post_id, '_dealer_custom_map_link', esc_url_raw($row[$header_map['custom map link']]));
        }

        // Update services - handle both "Yes/No" and "1/0" formats
        if (isset($header_map['docks'])) {
            $docks_value = trim($row[$header_map['docks']]);
            update_post_meta($post_id, '_dealer_docks', ($docks_value === 'Yes' || $docks_value === '1') ? '1' : '0');
        }

        if (isset($header_map['lifts'])) {
            $lifts_value = trim($row[$header_map['lifts']]);
            update_post_meta($post_id, '_dealer_lifts', ($lifts_value === 'Yes' || $lifts_value === '1') ? '1' : '0');
        }

        if (isset($header_map['trailers'])) {
            $trailers_value = trim($row[$header_map['trailers']]);
            update_post_meta($post_id, '_dealer_trailers', ($trailers_value === 'Yes' || $trailers_value === '1') ? '1' : '0');
        }

        // Update sub-locations if present
        if (isset($header_map['sub-locations']) && !empty($row[$header_map['sub-locations']])) {
            $sublocations_data = json_decode($row[$header_map['sub-locations']], true);
            if (is_array($sublocations_data)) {
                update_post_meta($post_id, '_dealer_sublocations', $sublocations_data);
            }
        }
    }

    /**
     * Shortcode to display dealers
     */
    public function dealers_shortcode($atts) {
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

        $dealers = new WP_Query($args);

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
}

// Load dealer portal module classes
// Skip loading ONLY in Divi Builder contexts (not regular admin)
$skip_dealer_portal = (isset($_GET['et_fb']) || isset($_POST['et_fb']))  // Divi Builder
    || isset($_GET['et_bfb'])                                             // Divi Backend Builder
    || isset($_GET['elementor-preview'])                                  // Elementor
    || isset($_GET['fl_builder']);                                        // Beaver Builder

if (!$skip_dealer_portal) {
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-dealer-role.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-dealer-role.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-email-handler.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-email-handler.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-nda-handler.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-nda-handler.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-menu-visibility.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-menu-visibility.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-registration-admin.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-registration-admin.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-nda-editor.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-nda-editor.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-page-manager.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-page-manager.php';
    }
}

// Load visual customizer module
if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/visual-customizer/classes/class-visual-customizer.php')) {
    require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/visual-customizer/classes/class-visual-customizer.php';
}

// Initialize the plugin
function jblund_dealers_init() {
    new JBLund_Dealers_Plugin();

    // Only initialize dealer portal classes if they were loaded (not in admin/builder)
    // Initialize dealer portal module (if loaded)
    if (class_exists('JBLund\DealerPortal\Email_Handler')) {
        new JBLund\DealerPortal\Email_Handler();
    }

    if (class_exists('JBLund\DealerPortal\NDA_Handler')) {
        new JBLund\DealerPortal\NDA_Handler();
    }

    if (class_exists('JBLund\DealerPortal\Menu_Visibility')) {
        new JBLund\DealerPortal\Menu_Visibility();
    }
    if (class_exists('JBLund\DealerPortal\Registration_Admin')) {
        new JBLund\DealerPortal\Registration_Admin();
    }
    if (class_exists('JBLund\DealerPortal\NDA_Editor')) {
        new JBLund\DealerPortal\NDA_Editor();
    }

    // Initialize visual customizer module (if loaded)
    if (class_exists('JBLund\VisualCustomizer\Visual_Customizer')) {
        new JBLund\VisualCustomizer\Visual_Customizer();
    }

    // Register dealer portal shortcodes
    add_shortcode('jblund_dealer_dashboard', 'jblund_dealer_dashboard_shortcode');
    add_shortcode('jblund_dealer_profile', 'jblund_dealer_profile_shortcode');
    add_shortcode('jblund_dealer_login', 'jblund_dealer_login_shortcode');
}
add_action('plugins_loaded', 'jblund_dealers_init');

/**
 * Dealer Dashboard Shortcode
 */
function jblund_dealer_dashboard_shortcode() {
    // Check if dealer portal classes are loaded (they won't be in admin/builder context)
    if (!class_exists('JBLund\DealerPortal\Dealer_Role')) {
        // Show builder-friendly placeholder when classes aren't loaded
        return '<div style="padding: 40px; background: #f0f0f0; border: 2px dashed #999; border-radius: 8px; text-align: center;">'
             . '<h3 style="margin-top: 0; color: #333;">📊 Dealer Dashboard</h3>'
             . '<p style="color: #666; margin-bottom: 0;">This shortcode displays the dealer portal dashboard.<br>'
             . 'The full interface will be visible on the frontend to logged-in dealers.</p>'
             . '</div>';
    }

    ob_start();
    include JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/templates/dealer-dashboard.php';
    return ob_get_clean();
}

/**
 * Dealer Profile Shortcode
 */
function jblund_dealer_profile_shortcode() {
    // Check if dealer portal classes are loaded (they won't be in admin/builder context)
    if (!class_exists('JBLund\DealerPortal\Dealer_Role')) {
        // Show builder-friendly placeholder when classes aren't loaded
        return '<div style="padding: 40px; background: #f0f0f0; border: 2px dashed #999; border-radius: 8px; text-align: center;">'
             . '<h3 style="margin-top: 0; color: #333;">👤 Dealer Profile</h3>'
             . '<p style="color: #666; margin-bottom: 0;">This shortcode displays the dealer profile editor.<br>'
             . 'The full interface will be visible on the frontend to logged-in dealers.</p>'
             . '</div>';
    }

    ob_start();
    include JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/templates/dealer-profile.php';
    return ob_get_clean();
}

/**
 * Dealer Login Shortcode
 */
function jblund_dealer_login_shortcode() {
    // Check if dealer portal classes are loaded (they won't be in admin/builder context)
    if (!class_exists('JBLund\DealerPortal\Dealer_Role')) {
        // Show builder-friendly placeholder when classes aren't loaded
        return '<div style="padding: 40px; background: #f0f0f0; border: 2px dashed #999; border-radius: 8px; text-align: center;">'
             . '<h3 style="margin-top: 0; color: #333;">🔐 Dealer Login</h3>'
             . '<p style="color: #666; margin-bottom: 0;">This shortcode displays the dealer login form.<br>'
             . 'The full login interface will be visible on the frontend.</p>'
             . '</div>';
    }

    ob_start();
    include JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/templates/dealer-login.php';
    return ob_get_clean();
}

/**
 * Activation hook
 */

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
 * Plugin activation hook
 */
function jblund_dealers_activate() {
    // Trigger the post type registration
    $plugin = new JBLund_Dealers_Plugin();
    $plugin->register_dealer_post_type();

    // Create dealer role
    if (class_exists('JBLund\DealerPortal\Dealer_Role')) {
        JBLund\DealerPortal\Dealer_Role::create_role();
    }

    // Note: Pages are no longer auto-created
    // Admin must create pages and assign them in Settings > Portal Pages

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
