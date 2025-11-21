<?php
/**
 * Dealer Profile Manager
 *
 * Handles dealer profile updates including their linked dealer post.
 * Provides secure access for dealers to edit their own company information
 * while restricting admin-only fields.
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 1.4.0
 */

namespace JBLund\DealerPortal;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Dealer_Profile_Manager {

    /**
     * Singleton instance
     *
     * @var Dealer_Profile_Manager
     */
    private static $instance = null;

    /**
     * Fields that dealers CAN edit
     */
    const DEALER_EDITABLE_FIELDS = array(
        // User account fields
        'user_email',
        'display_name',

        // Company info (basic)
        '_dealer_company_name',
        '_dealer_company_address',
        '_dealer_company_phone',
        '_dealer_website',

        // Services
        '_dealer_docks',
        '_dealer_lifts',
        '_dealer_trailers',

        // Sub-locations
        '_dealer_sublocations',

        // Custom map link
        '_dealer_custom_map_link',
    );

    /**
     * Admin-only fields (read-only for dealers)
     */
    const ADMIN_ONLY_FIELDS = array(
        '_dealer_linked_user_id',
        '_dealer_latitude',
        '_dealer_longitude',
        '_dealer_rep_name',
        '_dealer_rep_email',
        '_dealer_rep_phone',
        '_dealer_territory',
        '_dealer_nda_pdf',
    );

    /**
     * Document types allowed for upload
     */
    const ALLOWED_DOCUMENT_TYPES = array(
        'w9' => 'W-9 Tax Form',
        'insurance' => 'Insurance Certificate',
        'license' => 'Business License',
        'contract' => 'Dealer Agreement',
    );

    /**
     * Get singleton instance
     *
     * @return Dealer_Profile_Manager
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
        add_action('wp_ajax_jblund_upload_dealer_document', array($this, 'handle_document_upload'));
        add_action('wp_ajax_jblund_delete_dealer_document', array($this, 'handle_document_delete'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_profile_scripts'));
    }

    /**
     * Enqueue profile editing scripts
     */
    public function enqueue_profile_scripts() {
        // Only load on dealer profile page
        if (!is_page() || !has_shortcode(get_post()->post_content, 'jblund_dealer_profile')) {
            return;
        }

        wp_enqueue_script(
            'jblund-dealer-profile',
            plugins_url('assets/js/dealer-profile.js', dirname(dirname(dirname(__FILE__)))),
            array('jquery'),
            '2.0.0',
            true
        );

        wp_localize_script('jblund-dealer-profile', 'jblundDealerProfile', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'uploadNonce' => wp_create_nonce('jblund_upload_document'),
            'deleteNonce' => wp_create_nonce('jblund_delete_document'),
        ));
    }

    /**
     * Get dealer post for current user
     *
     * @param int $user_id User ID
     * @return WP_Post|false
     */
    public function get_dealer_post($user_id = null) {
        if (empty($user_id)) {
            $user_id = get_current_user_id();
        }

        return jblund_get_user_dealer_post($user_id);
    }

    /**
     * Check if user can edit dealer post
     *
     * @param int $user_id User ID
     * @param int $post_id Dealer post ID
     * @return bool
     */
    public function can_edit_dealer_post($user_id, $post_id) {
        // Admins can edit any dealer post
        if (current_user_can('manage_options')) {
            return true;
        }

        // Check if this dealer post is linked to the user
        $linked_user_id = get_post_meta($post_id, '_dealer_linked_user_id', true);

        return !empty($linked_user_id) && (int)$linked_user_id === (int)$user_id;
    }

    /**
     * Update dealer profile (user account + linked dealer post)
     *
     * @param int $post_id Dealer post ID
     * @param array $data Form data
     * @return array Array with 'success' boolean and 'message' string
     */
    public function update_dealer_profile($post_id, $data) {
        $user_id = get_current_user_id();
        $is_admin = current_user_can('manage_options');

        // Verify user can edit this post
        if (!$this->can_edit_dealer_post($user_id, $post_id)) {
            return array(
                'success' => false,
                'message' => __('You do not have permission to edit this profile.', 'jblund-dealers'),
            );
        }

        // Update post title (company name)
        if (isset($data['company_name'])) {
            $company_name = sanitize_text_field($data['company_name']);
            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => $company_name,
            ));
        }

        // Update company fields
        $fields_map = array(
            'company_address' => '_dealer_company_address',
            'company_phone' => '_dealer_company_phone',
            'website' => '_dealer_website',
        );

        foreach ($fields_map as $form_field => $meta_key) {
            if (isset($data[$form_field])) {
                $value = $form_field === 'website' ? esc_url_raw($data[$form_field]) : sanitize_text_field($data[$form_field]);
                update_post_meta($post_id, $meta_key, $value);
            }
        }

        // Update services (boolean checkboxes)
        update_post_meta($post_id, '_dealer_docks', isset($data['docks']) ? '1' : '');
        update_post_meta($post_id, '_dealer_lifts', isset($data['lifts']) ? '1' : '');
        update_post_meta($post_id, '_dealer_trailers', isset($data['trailers']) ? '1' : '');

        // Update sub-locations
        if (isset($data['sublocations']) && is_array($data['sublocations'])) {
            $sublocations = array();

            foreach ($data['sublocations'] as $sublocation) {
                // Skip empty sub-locations
                if (empty($sublocation['name']) && empty($sublocation['address'])) {
                    continue;
                }

                $sublocations[] = array(
                    'name' => sanitize_text_field($sublocation['name'] ?? ''),
                    'address' => sanitize_textarea_field($sublocation['address'] ?? ''),
                    'phone' => sanitize_text_field($sublocation['phone'] ?? ''),
                    'website' => esc_url_raw($sublocation['website'] ?? ''),
                    'docks' => isset($sublocation['docks']) ? '1' : '',
                    'lifts' => isset($sublocation['lifts']) ? '1' : '',
                    'trailers' => isset($sublocation['trailers']) ? '1' : '',
                );
            }

            update_post_meta($post_id, '_dealer_sublocations', $sublocations);
        }

        return array(
            'success' => true,
            'message' => __('Profile updated successfully!', 'jblund-dealers'),
        );
    }

    /**
     * Update dealer post meta fields
     *
     * @param int $post_id Dealer post ID
     * @param array $data Form data
     * @param bool $is_admin Whether user is admin
     * @return array Result array
     */
    private function update_dealer_post_fields($post_id, $data, $is_admin = false) {
        // Update post title (company name syncs with title)
        if (isset($data['_dealer_company_name'])) {
            $company_name = sanitize_text_field($data['_dealer_company_name']);

            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => $company_name,
            ));

            update_post_meta($post_id, '_dealer_company_name', $company_name);
        }

        // Update editable meta fields
        foreach (self::DEALER_EDITABLE_FIELDS as $field) {
            // Skip user fields (already handled)
            if (strpos($field, 'user_') === 0) {
                continue;
            }

            if (isset($data[$field])) {
                $value = $this->sanitize_field_value($field, $data[$field]);
                update_post_meta($post_id, $field, $value);
            }
        }

        // Admin-only fields (only if admin is editing)
        if ($is_admin) {
            foreach (self::ADMIN_ONLY_FIELDS as $field) {
                if (isset($data[$field])) {
                    $value = $this->sanitize_field_value($field, $data[$field]);
                    update_post_meta($post_id, $field, $value);
                }
            }
        }

        return array(
            'success' => true,
            'message' => __('Dealer information updated.', 'jblund-dealers'),
        );
    }

    /**
     * Sanitize field value based on field type
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @return mixed Sanitized value
     */
    private function sanitize_field_value($field, $value) {
        // Boolean fields
        if (in_array($field, array('_dealer_docks', '_dealer_lifts', '_dealer_trailers'))) {
            return $value === '1' || $value === 1 || $value === true ? '1' : '0';
        }

        // URL fields
        if (in_array($field, array('_dealer_website', '_dealer_custom_map_link'))) {
            return esc_url_raw($value);
        }

        // Email fields
        if (strpos($field, 'email') !== false) {
            return sanitize_email($value);
        }

        // Array fields (sub-locations)
        if ($field === '_dealer_sublocations' && is_array($value)) {
            return array_map(function($location) {
                return array(
                    'name' => sanitize_text_field($location['name'] ?? ''),
                    'address' => sanitize_textarea_field($location['address'] ?? ''),
                    'phone' => sanitize_text_field($location['phone'] ?? ''),
                    'website' => esc_url_raw($location['website'] ?? ''),
                    'docks' => isset($location['docks']) && $location['docks'] === '1' ? '1' : '0',
                    'lifts' => isset($location['lifts']) && $location['lifts'] === '1' ? '1' : '0',
                    'trailers' => isset($location['trailers']) && $location['trailers'] === '1' ? '1' : '0',
                );
            }, $value);
        }

        // Textarea fields
        if ($field === '_dealer_company_address') {
            return sanitize_textarea_field($value);
        }

        // Default: sanitize as text
        return sanitize_text_field($value);
    }

    /**
     * Get dealer documents
     *
     * @param int $post_id Dealer post ID
     * @return array Array of documents
     */
    public function get_dealer_documents($post_id) {
        $documents = get_post_meta($post_id, '_dealer_documents', true);
        return is_array($documents) ? $documents : array();
    }

    /**
     * Handle document upload via AJAX
     */
    public function handle_document_upload() {
        check_ajax_referer('jblund_upload_document', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'jblund-dealers')));
        }

        $user_id = get_current_user_id();
        $dealer_post_id = get_user_meta($user_id, '_dealer_post_id', true);

        if (!$dealer_post_id || !$this->can_edit_dealer_post($user_id, $dealer_post_id)) {
            wp_send_json_error(array('message' => __('You do not have permission to upload documents.', 'jblund-dealers')));
        }

        // Handle multiple file uploads
        if (empty($_FILES['files'])) {
            wp_send_json_error(array('message' => __('No files uploaded.', 'jblund-dealers')));
        }

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $uploaded_documents = array();
        $document_ids = get_post_meta($dealer_post_id, '_dealer_documents', true) ?: array();

        $files = $_FILES['files'];
        $file_count = count($files['name']);

        for ($i = 0; $i < $file_count; $i++) {
            // Prepare individual file array
            $file = array(
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            );

            // Upload file
            $attachment_id = media_handle_sideload($file, $dealer_post_id);

            if (is_wp_error($attachment_id)) {
                continue; // Skip failed uploads
            }

            // Add to document array
            $document_ids[] = $attachment_id;

            // Get document info for response
            $file_path = get_attached_file($attachment_id);
            $uploaded_documents[] = array(
                'id' => $attachment_id,
                'title' => get_the_title($attachment_id),
                'url' => wp_get_attachment_url($attachment_id),
                'filename' => basename($file_path),
                'size' => size_format(filesize($file_path)),
                'date' => get_the_date('Y-m-d', $attachment_id),
            );
        }

        if (empty($uploaded_documents)) {
            wp_send_json_error(array('message' => __('No files were successfully uploaded.', 'jblund-dealers')));
        }

        // Update post meta with document IDs
        update_post_meta($dealer_post_id, '_dealer_documents', $document_ids);

        wp_send_json_success(array(
            'message' => __('Documents uploaded successfully.', 'jblund-dealers'),
            'documents' => $uploaded_documents,
        ));
    }

    /**
     * Handle document deletion via AJAX
     */
    public function handle_document_delete() {
        check_ajax_referer('jblund_delete_document', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'jblund-dealers')));
        }

        $user_id = get_current_user_id();
        $dealer_post_id = get_user_meta($user_id, '_dealer_post_id', true);

        if (!$dealer_post_id || !$this->can_edit_dealer_post($user_id, $dealer_post_id)) {
            wp_send_json_error(array('message' => __('You do not have permission to delete documents.', 'jblund-dealers')));
        }

        $document_id = intval($_POST['document_id'] ?? 0);
        $document_ids = get_post_meta($dealer_post_id, '_dealer_documents', true) ?: array();

        // Check if document belongs to this dealer
        if (!in_array($document_id, $document_ids, true)) {
            wp_send_json_error(array('message' => __('Document not found.', 'jblund-dealers')));
        }

        // Delete attachment
        if (wp_delete_attachment($document_id, true)) {
            // Remove from document array
            $document_ids = array_diff($document_ids, array($document_id));
            update_post_meta($dealer_post_id, '_dealer_documents', array_values($document_ids));

            wp_send_json_success(array('message' => __('Document deleted successfully.', 'jblund-dealers')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete document.', 'jblund-dealers')));
        }
    }
}
