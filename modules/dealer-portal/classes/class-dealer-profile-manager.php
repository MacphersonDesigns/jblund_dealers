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
     * Constructor
     */
    public function __construct() {
        add_action('wp_ajax_upload_dealer_document', array($this, 'handle_document_upload'));
        add_action('wp_ajax_delete_dealer_document', array($this, 'handle_document_delete'));
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
     * @param array $data Form data
     * @param int $user_id User ID
     * @return array Array with 'success' boolean and 'message' string
     */
    public function update_dealer_profile($data, $user_id = null) {
        if (empty($user_id)) {
            $user_id = get_current_user_id();
        }

        $is_admin = current_user_can('manage_options');
        $dealer_post = $this->get_dealer_post($user_id);

        // Start transaction-like updates
        $errors = array();
        $updated = array();

        // 1. Update user account fields
        if (isset($data['user_email']) || isset($data['display_name'])) {
            $user_data = array('ID' => $user_id);

            if (isset($data['user_email'])) {
                $user_data['user_email'] = sanitize_email($data['user_email']);
            }

            if (isset($data['display_name'])) {
                $user_data['display_name'] = sanitize_text_field($data['display_name']);
            }

            $result = wp_update_user($user_data);

            if (is_wp_error($result)) {
                $errors[] = __('Error updating user account: ', 'jblund-dealers') . $result->get_error_message();
            } else {
                $updated[] = 'user_account';
            }
        }

        // 2. Update dealer post if linked
        if ($dealer_post && $this->can_edit_dealer_post($user_id, $dealer_post->ID)) {
            $post_updated = $this->update_dealer_post_fields($dealer_post->ID, $data, $is_admin);

            if ($post_updated['success']) {
                $updated[] = 'dealer_post';
            } else {
                $errors[] = $post_updated['message'];
            }
        }

        // Return result
        if (empty($errors)) {
            return array(
                'success' => true,
                'message' => __('Profile updated successfully!', 'jblund-dealers'),
                'updated' => $updated,
            );
        } else {
            return array(
                'success' => false,
                'message' => implode(' ', $errors),
            );
        }
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
        check_ajax_referer('dealer_document_upload', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(__('You must be logged in.', 'jblund-dealers'));
        }

        $user_id = get_current_user_id();
        $dealer_post = $this->get_dealer_post($user_id);

        if (!$dealer_post || !$this->can_edit_dealer_post($user_id, $dealer_post->ID)) {
            wp_send_json_error(__('You do not have permission to upload documents.', 'jblund-dealers'));
        }

        $document_type = sanitize_text_field($_POST['document_type'] ?? '');

        if (!array_key_exists($document_type, self::ALLOWED_DOCUMENT_TYPES)) {
            wp_send_json_error(__('Invalid document type.', 'jblund-dealers'));
        }

        // Handle file upload
        if (empty($_FILES['document_file'])) {
            wp_send_json_error(__('No file uploaded.', 'jblund-dealers'));
        }

        require_once(ABSPATH . 'wp-admin/includes/file.php');

        $upload_overrides = array(
            'test_form' => false,
            'mimes' => array(
                'pdf' => 'application/pdf',
                'jpg|jpeg' => 'image/jpeg',
                'png' => 'image/png',
            ),
        );

        $uploaded_file = $_FILES['document_file'];
        $upload = wp_handle_upload($uploaded_file, $upload_overrides);

        if (isset($upload['error'])) {
            wp_send_json_error($upload['error']);
        }

        // Store document info
        $documents = $this->get_dealer_documents($dealer_post->ID);

        $documents[$document_type] = array(
            'url' => $upload['url'],
            'file' => $upload['file'],
            'type' => $uploaded_file['type'],
            'uploaded_date' => current_time('mysql'),
            'uploaded_by' => $user_id,
        );

        update_post_meta($dealer_post->ID, '_dealer_documents', $documents);

        wp_send_json_success(array(
            'message' => __('Document uploaded successfully.', 'jblund-dealers'),
            'document' => $documents[$document_type],
        ));
    }

    /**
     * Handle document deletion via AJAX
     */
    public function handle_document_delete() {
        check_ajax_referer('dealer_document_delete', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(__('You must be logged in.', 'jblund-dealers'));
        }

        $user_id = get_current_user_id();
        $dealer_post = $this->get_dealer_post($user_id);

        if (!$dealer_post || !$this->can_edit_dealer_post($user_id, $dealer_post->ID)) {
            wp_send_json_error(__('You do not have permission to delete documents.', 'jblund-dealers'));
        }

        $document_type = sanitize_text_field($_POST['document_type'] ?? '');
        $documents = $this->get_dealer_documents($dealer_post->ID);

        if (!isset($documents[$document_type])) {
            wp_send_json_error(__('Document not found.', 'jblund-dealers'));
        }

        // Delete physical file
        if (isset($documents[$document_type]['file']) && file_exists($documents[$document_type]['file'])) {
            @unlink($documents[$document_type]['file']);
        }

        // Remove from meta
        unset($documents[$document_type]);
        update_post_meta($dealer_post->ID, '_dealer_documents', $documents);

        wp_send_json_success(__('Document deleted successfully.', 'jblund-dealers'));
    }
}
