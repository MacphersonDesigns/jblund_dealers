<?php
/**
 * CSV Import/Export Handler
 *
 * Handles all CSV import and export operations for dealer data
 *
 * @package JBLund_Dealers
 * @subpackage Admin
 */

namespace JBLund\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class CSV_Handler {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Handle CSV operations via admin hooks
        add_action('admin_init', array($this, 'handle_csv_operations'));
    }

    /**
     * Handle CSV operations (export/import)
     */
    public function handle_csv_operations() {
        // Handle export
        if (isset($_GET['action']) && $_GET['action'] === 'export_csv' &&
            isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'jblund_dealers_export')) {

            if (!current_user_can('export')) {
                wp_die(__('You do not have permission to export data.', 'jblund-dealers'));
            }

            $this->export_dealers_csv();
        }

        // Handle import (after column mapping)
        if (isset($_POST['action']) && $_POST['action'] === 'import_csv' &&
            isset($_POST['jblund_dealers_import_nonce']) &&
            wp_verify_nonce($_POST['jblund_dealers_import_nonce'], 'jblund_dealers_import')) {

            if (!current_user_can('import')) {
                wp_die(__('You do not have permission to import data.', 'jblund-dealers'));
            }

            $this->import_dealers_csv();
        }
    }

    /**
     * Export all dealers to CSV
     */
    public function export_dealers_csv() {
        // Get all dealers
        $dealers = get_posts(array(
            'post_type' => 'dealer',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        if (empty($dealers)) {
            wp_die(__('No dealers found to export.', 'jblund-dealers'));
        }

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=dealers-export-' . date('Y-m-d') . '.csv');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Create output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write header row
        fputcsv($output, array(
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
        ));

        // Write dealer data
        foreach ($dealers as $dealer) {
            $docks = get_post_meta($dealer->ID, '_dealer_docks', true) === '1' ? 'Yes' : 'No';
            $lifts = get_post_meta($dealer->ID, '_dealer_lifts', true) === '1' ? 'Yes' : 'No';
            $trailers = get_post_meta($dealer->ID, '_dealer_trailers', true) === '1' ? 'Yes' : 'No';

            $sublocations = get_post_meta($dealer->ID, '_dealer_sublocations', true);
            $sublocations_json = !empty($sublocations) ? json_encode($sublocations) : '';

            fputcsv($output, array(
                $dealer->ID,
                $dealer->post_title,
                get_post_meta($dealer->ID, '_dealer_company_address', true),
                get_post_meta($dealer->ID, '_dealer_company_phone', true),
                get_post_meta($dealer->ID, '_dealer_website', true),
                get_post_meta($dealer->ID, '_dealer_latitude', true),
                get_post_meta($dealer->ID, '_dealer_longitude', true),
                get_post_meta($dealer->ID, '_dealer_custom_map_link', true),
                $docks,
                $lifts,
                $trailers,
                $sublocations_json
            ));
        }

        fclose($output);
        exit;
    }

    /**
     * Import dealers from CSV
     */
    public function import_dealers_csv() {
        // Verify column mapping data exists
        if (!isset($_POST['column_mapping']) || !isset($_POST['csv_data'])) {
            wp_die(__('Missing import data. Please try again.', 'jblund-dealers'));
        }

        $column_mapping = array_map('sanitize_text_field', $_POST['column_mapping']);
        $csv_data = json_decode(stripslashes($_POST['csv_data']), true);

        if (empty($csv_data)) {
            wp_die(__('No data to import. Please try again.', 'jblund-dealers'));
        }

        $imported = 0;
        $updated = 0;
        $errors = 0;

        foreach ($csv_data as $row) {
            // Map CSV columns to dealer fields
            $dealer_data = array();
            foreach ($column_mapping as $csv_col => $dealer_field) {
                if ($dealer_field !== 'ignore' && isset($row[$csv_col])) {
                    $dealer_data[$dealer_field] = $row[$csv_col];
                }
            }

            // Check if we're updating existing dealer
            $dealer_id = isset($dealer_data['ID']) ? intval($dealer_data['ID']) : 0;
            $existing_dealer = $dealer_id > 0 ? get_post($dealer_id) : null;

            // Prepare post data
            $post_data = array(
                'post_type' => 'dealer',
                'post_status' => 'publish',
                'post_title' => isset($dealer_data['Company Name']) ? $dealer_data['Company Name'] : 'Imported Dealer'
            );

            if ($existing_dealer && $existing_dealer->post_type === 'dealer') {
                // Update existing dealer
                $post_data['ID'] = $dealer_id;
                $result = wp_update_post($post_data);
                if (!is_wp_error($result)) {
                    $updated++;
                } else {
                    $errors++;
                    continue;
                }
            } else {
                // Create new dealer
                $dealer_id = wp_insert_post($post_data);
                if (!is_wp_error($dealer_id)) {
                    $imported++;
                } else {
                    $errors++;
                    continue;
                }
            }

            // Update meta fields
            if (isset($dealer_data['Address'])) {
                update_post_meta($dealer_id, '_dealer_company_address', sanitize_textarea_field($dealer_data['Address']));
            }
            if (isset($dealer_data['Phone'])) {
                update_post_meta($dealer_id, '_dealer_company_phone', sanitize_text_field($dealer_data['Phone']));
            }
            if (isset($dealer_data['Website'])) {
                update_post_meta($dealer_id, '_dealer_website', esc_url_raw($dealer_data['Website']));
            }
            if (isset($dealer_data['Latitude'])) {
                update_post_meta($dealer_id, '_dealer_latitude', sanitize_text_field($dealer_data['Latitude']));
            }
            if (isset($dealer_data['Longitude'])) {
                update_post_meta($dealer_id, '_dealer_longitude', sanitize_text_field($dealer_data['Longitude']));
            }
            if (isset($dealer_data['Custom Map Link'])) {
                update_post_meta($dealer_id, '_dealer_custom_map_link', esc_url_raw($dealer_data['Custom Map Link']));
            }

            // Handle services (normalize Yes/No and 1/0)
            if (isset($dealer_data['Docks'])) {
                $docks_value = in_array(strtolower($dealer_data['Docks']), array('yes', '1', 'true')) ? '1' : '0';
                update_post_meta($dealer_id, '_dealer_docks', $docks_value);
            }
            if (isset($dealer_data['Lifts'])) {
                $lifts_value = in_array(strtolower($dealer_data['Lifts']), array('yes', '1', 'true')) ? '1' : '0';
                update_post_meta($dealer_id, '_dealer_lifts', $lifts_value);
            }
            if (isset($dealer_data['Trailers'])) {
                $trailers_value = in_array(strtolower($dealer_data['Trailers']), array('yes', '1', 'true')) ? '1' : '0';
                update_post_meta($dealer_id, '_dealer_trailers', $trailers_value);
            }

            // Handle sub-locations (JSON encoded)
            if (isset($dealer_data['Sub-Locations']) && !empty($dealer_data['Sub-Locations'])) {
                $sublocations = json_decode($dealer_data['Sub-Locations'], true);
                if (is_array($sublocations)) {
                    update_post_meta($dealer_id, '_dealer_sublocations', $sublocations);
                }
            }
        }

        // Redirect with success message
        $redirect_url = add_query_arg(array(
            'post_type' => 'dealer',
            'page' => 'jblund-dealers-settings',
            'tab' => 'import-export',
            'import_success' => '1',
            'imported' => $imported,
            'updated' => $updated,
            'errors' => $errors
        ), admin_url('edit.php'));

        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Render column mapping interface
     */
    public function render_column_mapping_interface() {
        // Verify upload
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            ?>
            <div class="notice notice-error">
                <p><?php _e('Error uploading CSV file. Please try again.', 'jblund-dealers'); ?></p>
            </div>
            <?php
            return;
        }

        // Read CSV file
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');

        if ($handle === false) {
            ?>
            <div class="notice notice-error">
                <p><?php _e('Error reading CSV file. Please ensure it\'s a valid CSV.', 'jblund-dealers'); ?></p>
            </div>
            <?php
            return;
        }

        // Get headers and first few rows for preview
        $headers = fgetcsv($handle);
        $preview_rows = array();
        $all_rows = array();

        $row_count = 0;
        while (($data = fgetcsv($handle)) !== false && $row_count < 3) {
            $preview_rows[] = $data;
            $row_count++;
        }

        // Reset to read all rows for import
        rewind($handle);
        fgetcsv($handle); // Skip header
        while (($data = fgetcsv($handle)) !== false) {
            $row = array();
            foreach ($headers as $index => $header) {
                $row[$header] = isset($data[$index]) ? $data[$index] : '';
            }
            $all_rows[] = $row;
        }
        fclose($handle);

        // Store CSV data in form
        $csv_data_json = json_encode($all_rows);

        // Available dealer fields
        $dealer_fields = array(
            'ignore' => __('— Ignore This Column —', 'jblund-dealers'),
            'ID' => __('Dealer ID (for updates)', 'jblund-dealers'),
            'Company Name' => __('Company Name', 'jblund-dealers'),
            'Address' => __('Address', 'jblund-dealers'),
            'Phone' => __('Phone', 'jblund-dealers'),
            'Website' => __('Website', 'jblund-dealers'),
            'Latitude' => __('Latitude', 'jblund-dealers'),
            'Longitude' => __('Longitude', 'jblund-dealers'),
            'Custom Map Link' => __('Custom Map Link', 'jblund-dealers'),
            'Docks' => __('Docks (Yes/No or 1/0)', 'jblund-dealers'),
            'Lifts' => __('Lifts (Yes/No or 1/0)', 'jblund-dealers'),
            'Trailers' => __('Trailers (Yes/No or 1/0)', 'jblund-dealers'),
            'Sub-Locations' => __('Sub-Locations (JSON)', 'jblund-dealers')
        );

        ?>
        <div class="wrap jblund-dealers-settings">
            <h1><?php _e('Map CSV Columns to Dealer Fields', 'jblund-dealers'); ?></h1>

            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; margin-top: 20px;">
                <p><?php _e('Match each column in your CSV file to the corresponding dealer field. The system will attempt to auto-match based on column names.', 'jblund-dealers'); ?></p>

                <form method="post" action="<?php echo admin_url('edit.php?post_type=dealer&page=jblund-dealers-settings&tab=import-export'); ?>">
                    <?php wp_nonce_field('jblund_dealers_import', 'jblund_dealers_import_nonce'); ?>
                    <input type="hidden" name="action" value="import_csv" />
                    <input type="hidden" name="csv_data" value="<?php echo esc_attr($csv_data_json); ?>" />

                    <table class="widefat" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th><?php _e('CSV Column', 'jblund-dealers'); ?></th>
                                <th><?php _e('Sample Data', 'jblund-dealers'); ?></th>
                                <th><?php _e('Maps To Dealer Field', 'jblund-dealers'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($headers as $index => $header): ?>
                                <?php
                                // Auto-match common column names
                                $auto_match = 'ignore';
                                $header_lower = strtolower(trim($header));

                                if (in_array($header_lower, array('id', 'dealer id', 'dealer_id'))) $auto_match = 'ID';
                                elseif (in_array($header_lower, array('name', 'company name', 'company_name', 'dealer name'))) $auto_match = 'Company Name';
                                elseif (in_array($header_lower, array('address', 'street', 'location'))) $auto_match = 'Address';
                                elseif (in_array($header_lower, array('phone', 'telephone', 'phone number'))) $auto_match = 'Phone';
                                elseif (in_array($header_lower, array('website', 'url', 'web'))) $auto_match = 'Website';
                                elseif (in_array($header_lower, array('latitude', 'lat'))) $auto_match = 'Latitude';
                                elseif (in_array($header_lower, array('longitude', 'lng', 'lon', 'long'))) $auto_match = 'Longitude';
                                elseif (in_array($header_lower, array('custom map link', 'map link', 'map_link'))) $auto_match = 'Custom Map Link';
                                elseif (in_array($header_lower, array('docks', 'dock'))) $auto_match = 'Docks';
                                elseif (in_array($header_lower, array('lifts', 'lift'))) $auto_match = 'Lifts';
                                elseif (in_array($header_lower, array('trailers', 'trailer'))) $auto_match = 'Trailers';
                                elseif (in_array($header_lower, array('sub-locations', 'sublocations', 'sub_locations'))) $auto_match = 'Sub-Locations';

                                // Get sample data
                                $sample = isset($preview_rows[0][$index]) ? $preview_rows[0][$index] : '';
                                if (empty($sample) && isset($preview_rows[1][$index])) {
                                    $sample = $preview_rows[1][$index];
                                }
                                if (strlen($sample) > 50) {
                                    $sample = substr($sample, 0, 50) . '...';
                                }
                                ?>
                                <tr>
                                    <td><strong><?php echo esc_html($header); ?></strong></td>
                                    <td><code><?php echo esc_html($sample); ?></code></td>
                                    <td>
                                        <select name="column_mapping[<?php echo esc_attr($header); ?>]" class="regular-text">
                                            <?php foreach ($dealer_fields as $field_key => $field_label): ?>
                                                <option value="<?php echo esc_attr($field_key); ?>" <?php selected($auto_match, $field_key); ?>>
                                                    <?php echo esc_html($field_label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 20px; padding: 15px; background: #f0f6fc; border-left: 4px solid #0073aa;">
                        <p style="margin: 0;">
                            <strong><?php _e('Ready to import:', 'jblund-dealers'); ?></strong>
                            <?php echo sprintf(_n('%d dealer found in CSV', '%d dealers found in CSV', count($all_rows), 'jblund-dealers'), count($all_rows)); ?>
                        </p>
                    </div>

                    <p style="margin-top: 20px;">
                        <button type="submit" class="button button-primary button-large">
                            <span class="dashicons dashicons-upload" style="margin-top: 3px;"></span>
                            <?php _e('Import Dealers', 'jblund-dealers'); ?>
                        </button>
                        <a href="<?php echo admin_url('edit.php?post_type=dealer&page=jblund-dealers-settings&tab=import-export'); ?>" class="button button-secondary button-large" style="margin-left: 10px;">
                            <?php _e('Cancel', 'jblund-dealers'); ?>
                        </a>
                    </p>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Render CSV operations UI (for settings page)
     */
    public function render_operations() {
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

        // Display import success message
        if (isset($_GET['import_success']) && $_GET['import_success'] === '1') {
            $imported = isset($_GET['imported']) ? intval($_GET['imported']) : 0;
            $updated = isset($_GET['updated']) ? intval($_GET['updated']) : 0;
            $errors = isset($_GET['errors']) ? intval($_GET['errors']) : 0;
            ?>
            <div class="notice notice-success" style="margin-top: 20px;">
                <p>
                    <strong><?php _e('Import completed successfully!', 'jblund-dealers'); ?></strong><br>
                    <?php echo sprintf(__('Imported: %d | Updated: %d | Errors: %d', 'jblund-dealers'), $imported, $updated, $errors); ?>
                </p>
            </div>
            <?php
        }
    }
}
