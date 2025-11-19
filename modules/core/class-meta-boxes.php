<?php
/**
 * Dealer Meta Boxes
 *
 * Handles all meta boxes for the dealer post type:
 * - Dealer Information
 * - Linked User Account
 * - Sub-Locations
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
 * Meta Boxes Class
 *
 * Manages all custom meta boxes for dealers
 */
class Meta_Boxes {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_dealer_meta_boxes'));
        add_action('save_post_dealer', array($this, 'save_dealer_meta'), 10, 2);
    }

    /**
     * Add dealer meta boxes
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
}
