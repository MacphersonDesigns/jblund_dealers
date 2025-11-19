<?php
/**
 * Registration Admin - List & Approval Workflow
 *
 * Admin interface for viewing, filtering, and managing dealer registration submissions.
 * Implements WP_List_Table for display and provides approve/decline workflow.
 *
 * @package    JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since      1.1.0
 */

namespace JBLund\DealerPortal;

if (!defined('ABSPATH')) {
    exit;
}

// Load WP_List_Table if not already loaded
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Registration Admin List Table
 *
 * Displays dealer registration submissions with approve/decline actions.
 */
class Registration_Admin_List_Table extends \WP_List_Table {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct([
            'singular' => 'registration',
            'plural'   => 'registrations',
            'ajax'     => false,
        ]);
    }

    /**
     * Get table columns
     *
     * @return array
     */
    public function get_columns() {
        return [
            'date'          => __('Date Submitted', 'jblund-dealers'),
            'rep_name'      => __('Representative', 'jblund-dealers'),
            'email'         => __('Email', 'jblund-dealers'),
            'company'       => __('Company', 'jblund-dealers'),
            'territory'     => __('Territory', 'jblund-dealers'),
            'status'        => __('Status', 'jblund-dealers'),
            'actions'       => __('Actions', 'jblund-dealers'),
        ];
    }

    /**
     * Get sortable columns
     *
     * @return array
     */
    public function get_sortable_columns() {
        return [
            'date'      => ['date', true], // true = default descending
            'rep_name'  => ['rep_name', false],
            'company'   => ['company', false],
            'status'    => ['status', false],
        ];
    }

    /**
     * Prepare items for display
     */
    public function prepare_items() {
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';

        // Query args
        $args = [
            'post_type'      => 'dealer_registration',
            'posts_per_page' => $per_page,
            'paged'          => $current_page,
            'orderby'        => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date',
            'order'          => isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC',
        ];

        // Filter by status
        if ($status_filter !== 'all') {
            $args['meta_query'] = [
                [
                    'key'   => '_registration_status',
                    'value' => $status_filter,
                ],
            ];
        }

        $query = new \WP_Query($args);

        $this->items = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();

                $this->items[] = [
                    'ID'        => $post_id,
                    'date'      => get_the_date('Y-m-d H:i:s'),
                    'rep_name'  => get_post_meta($post_id, '_registration_rep_name', true),
                    'email'     => get_post_meta($post_id, '_registration_email', true),
                    'company'   => get_post_meta($post_id, '_registration_company', true),
                    'territory' => get_post_meta($post_id, '_registration_territory', true),
                    'status'    => get_post_meta($post_id, '_registration_status', true) ?: 'pending',
                ];
            }
            wp_reset_postdata();
        }

        // Set pagination
        $this->set_pagination_args([
            'total_items' => $query->found_posts,
            'per_page'    => $per_page,
            'total_pages' => $query->max_num_pages,
        ]);

        // Set column headers
        $this->_column_headers = [
            $this->get_columns(),
            [],
            $this->get_sortable_columns(),
        ];
    }

    /**
     * Default column rendering
     *
     * @param array  $item
     * @param string $column_name
     * @return string
     */
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'date':
                return esc_html(date_i18n('M j, Y g:i a', strtotime($item['date'])));

            case 'rep_name':
            case 'company':
            case 'territory':
                return esc_html($item[$column_name] ?: '—');

            default:
                return '';
        }
    }

    /**
     * Email column with mailto link
     *
     * @param array $item
     * @return string
     */
    protected function column_email($item) {
        $email = esc_html($item['email']);
        return sprintf('<a href="mailto:%s">%s</a>', $email, $email);
    }

    /**
     * Status column with color badge
     *
     * @param array $item
     * @return string
     */
    protected function column_status($item) {
        $status = $item['status'];
        $colors = [
            'pending'  => '#f0ad4e', // Yellow
            'approved' => '#5cb85c', // Green
            'rejected' => '#d9534f', // Red
        ];
        $labels = [
            'pending'  => __('Pending', 'jblund-dealers'),
            'approved' => __('Approved', 'jblund-dealers'),
            'rejected' => __('Rejected', 'jblund-dealers'),
        ];

        $color = isset($colors[$status]) ? $colors[$status] : '#999';
        $label = isset($labels[$status]) ? $labels[$status] : ucfirst($status);

        return sprintf(
            '<span style="display: inline-block; padding: 4px 8px; border-radius: 3px; background: %s; color: white; font-size: 11px; font-weight: 600;">%s</span>',
            esc_attr($color),
            esc_html($label)
        );
    }

    /**
     * Actions column with approve/decline buttons
     *
     * @param array $item
     * @return string
     */
    protected function column_actions($item) {
        $status = $item['status'];
        $post_id = $item['ID'];
        $nonce = wp_create_nonce('dealer_registration_action_' . $post_id);

        if ($status === 'pending') {
            $approve_url = wp_nonce_url(
                admin_url('admin-post.php?action=jblund_approve_registration&registration_id=' . $post_id),
                'approve_registration_' . $post_id
            );
            $decline_url = '#';

            return sprintf(
                '<a href="%s" class="button button-small button-primary" style="margin-right: 5px;">%s</a>
                <a href="%s" class="button button-small jblund-decline-registration" data-registration-id="%d" data-nonce="%s">%s</a>',
                esc_url($approve_url),
                __('Approve', 'jblund-dealers'),
                esc_url($decline_url),
                $post_id,
                $nonce,
                __('Decline', 'jblund-dealers')
            );
        }

        return '—';
    }

    /**
     * Display when no items found
     */
    public function no_items() {
        _e('No registration submissions found.', 'jblund-dealers');
    }

    /**
     * Extra table navigation (status filter)
     *
     * @param string $which
     */
    protected function extra_tablenav($which) {
        if ($which !== 'top') {
            return;
        }

        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
        ?>
        <div class="alignleft actions">
            <select name="status">
                <option value="all" <?php selected($status_filter, 'all'); ?>><?php _e('All Statuses', 'jblund-dealers'); ?></option>
                <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php _e('Pending', 'jblund-dealers'); ?></option>
                <option value="approved" <?php selected($status_filter, 'approved'); ?>><?php _e('Approved', 'jblund-dealers'); ?></option>
                <option value="rejected" <?php selected($status_filter, 'rejected'); ?>><?php _e('Rejected', 'jblund-dealers'); ?></option>
            </select>
            <?php submit_button(__('Filter', 'jblund-dealers'), 'action', 'filter_action', false); ?>
        </div>
        <?php
    }
}

/**
 * Registration Admin Manager
 *
 * Manages admin page, actions, and email notifications.
 */
class Registration_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_jblund_approve_registration', [$this, 'process_approval']);
        add_action('admin_post_jblund_decline_registration', [$this, 'process_decline']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    /**
     * Add admin menu item
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=dealer',
            __('Dealer Registrations', 'jblund-dealers'),
            __('Registrations', 'jblund-dealers'),
            'manage_options',
            'dealer-registrations',
            [$this, 'render_admin_page']
        );
    }

    /**
     * Enqueue admin scripts
     *
     * @param string $hook
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'dealer_page_dealer-registrations') {
            return;
        }

        wp_enqueue_script('jquery');
        wp_add_inline_script('jquery', "
            jQuery(document).ready(function($) {
                // Decline button click handler
                $('.jblund-decline-registration').on('click', function(e) {
                    e.preventDefault();
                    var registrationId = $(this).data('registration-id');
                    var nonce = $(this).data('nonce');
                    var reason = prompt('Enter reason for declining this registration (required):');

                    if (reason && reason.trim() !== '') {
                        var form = $('<form>', {
                            'method': 'POST',
                            'action': '" . admin_url('admin-post.php') . "'
                        }).append($('<input>', {
                            'type': 'hidden',
                            'name': 'action',
                            'value': 'jblund_decline_registration'
                        })).append($('<input>', {
                            'type': 'hidden',
                            'name': 'registration_id',
                            'value': registrationId
                        })).append($('<input>', {
                            'type': 'hidden',
                            'name': '_wpnonce',
                            'value': nonce
                        })).append($('<input>', {
                            'type': 'hidden',
                            'name': 'rejection_reason',
                            'value': reason
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        ");
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'jblund-dealers'));
        }

        echo '<div class="wrap">';
        echo '<h1>' . __('Dealer Registrations', 'jblund-dealers') . '</h1>';

        // Display admin notices
        if (isset($_GET['approved'])) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Registration approved successfully! Dealer account created and welcome email sent.', 'jblund-dealers') . '</p></div>';
        }
        if (isset($_GET['declined'])) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Registration declined. Rejection email sent to applicant.', 'jblund-dealers') . '</p></div>';
        }
        if (isset($_GET['error'])) {
            $error_msg = sanitize_text_field($_GET['error']);
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($error_msg) . '</p></div>';
        }

        // Render list table
        $list_table = new Registration_Admin_List_Table();
        $list_table->prepare_items();

        echo '<form method="get">';
        echo '<input type="hidden" name="post_type" value="dealer" />';
        echo '<input type="hidden" name="page" value="dealer-registrations" />';
        $list_table->display();
        echo '</form>';

        echo '</div>';
    }

    /**
     * Process approval action
     */
    public function process_approval() {
        $registration_id = isset($_GET['registration_id']) ? intval($_GET['registration_id']) : 0;

        // Verify nonce
        if (!wp_verify_nonce($_GET['_wpnonce'], 'approve_registration_' . $registration_id)) {
            wp_die(__('Security check failed', 'jblund-dealers'));
        }

        // Verify capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jblund-dealers'));
        }

        // Get registration data
        $rep_name = get_post_meta($registration_id, '_registration_rep_name', true);
        $email = get_post_meta($registration_id, '_registration_email', true);
        $company = get_post_meta($registration_id, '_registration_company', true);

        if (!$email) {
            wp_redirect(add_query_arg(['error' => 'Invalid registration data'], wp_get_referer()));
            exit;
        }

        // Create WordPress user
        $username = sanitize_user(strtolower(str_replace(' ', '', $rep_name)));
        $password = wp_generate_password(12, true, true);

        // Check if user already exists
        if (username_exists($username) || email_exists($email)) {
            wp_redirect(add_query_arg(['error' => 'User already exists with this email'], wp_get_referer()));
            exit;
        }

        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_redirect(add_query_arg(['error' => $user_id->get_error_message()], wp_get_referer()));
            exit;
        }

        // Assign dealer role
        if (class_exists('JBLund\DealerPortal\Dealer_Role')) {
            \JBLund\DealerPortal\Dealer_Role::assign_to_user($user_id);
        }

        // Update user meta
        update_user_meta($user_id, 'first_name', $rep_name);
        update_user_meta($user_id, '_dealer_company_name', $company);

        // Update registration status
        update_post_meta($registration_id, '_registration_status', 'approved');
        update_post_meta($registration_id, '_registration_approved_by', get_current_user_id());
        update_post_meta($registration_id, '_registration_approved_date', current_time('mysql'));
        update_post_meta($registration_id, '_registration_user_id', $user_id);

        // Send approval email
        $this->send_approval_email($user_id, $email, $rep_name, $username, $password);

        // Redirect with success message
        wp_redirect(add_query_arg(['approved' => '1'], wp_get_referer()));
        exit;
    }

    /**
     * Process decline action
     */
    public function process_decline() {
        $registration_id = isset($_POST['registration_id']) ? intval($_POST['registration_id']) : 0;
        $rejection_reason = isset($_POST['rejection_reason']) ? sanitize_textarea_field($_POST['rejection_reason']) : '';

        // Verify nonce
        if (!wp_verify_nonce($_POST['_wpnonce'], 'dealer_registration_action_' . $registration_id)) {
            wp_die(__('Security check failed', 'jblund-dealers'));
        }

        // Verify capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'jblund-dealers'));
        }

        if (!$rejection_reason) {
            wp_redirect(add_query_arg(['error' => 'Rejection reason is required'], wp_get_referer()));
            exit;
        }

        // Get registration data
        $email = get_post_meta($registration_id, '_registration_email', true);
        $rep_name = get_post_meta($registration_id, '_registration_rep_name', true);

        // Update registration status
        update_post_meta($registration_id, '_registration_status', 'rejected');
        update_post_meta($registration_id, '_registration_rejected_by', get_current_user_id());
        update_post_meta($registration_id, '_registration_rejected_date', current_time('mysql'));
        update_post_meta($registration_id, '_registration_rejection_reason', $rejection_reason);

        // Send rejection email
        $this->send_rejection_email($email, $rep_name, $rejection_reason);

        // Redirect with success message
        wp_redirect(add_query_arg(['declined' => '1'], wp_get_referer()));
        exit;
    }

    /**
     * Send approval email to new dealer
     *
     * @param int    $user_id
     * @param string $email
     * @param string $rep_name
     * @param string $username
     * @param string $password
     */
    private function send_approval_email($user_id, $email, $rep_name, $username, $password) {
        $subject = __('Your JBLund Dealer Portal Account Has Been Approved', 'jblund-dealers');

        $vars = [
            'rep_name' => $rep_name,
            'username' => $username,
            'password' => $password,
            'login_url' => wp_login_url(),
        ];

        $message = $this->get_mail_message('registration-approval', $vars);

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        wp_mail($email, $subject, $message, $headers);
    }

    /**
     * Send rejection email to applicant
     *
     * @param string $email
     * @param string $rep_name
     * @param string $reason
     */
    private function send_rejection_email($email, $rep_name, $reason) {
        $subject = __('Update on Your JBLund Dealer Portal Application', 'jblund-dealers');

        $vars = [
            'rep_name' => $rep_name,
            'reason'   => $reason,
        ];

        $message = $this->get_mail_message('registration-rejection', $vars);

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        wp_mail($email, $subject, $message, $headers);
    }

    /**
     * Get email message from template
     *
     * @param string $template
     * @param array  $vars
     * @return string
     */
    private function get_mail_message($template, $vars) {
        extract($vars);

        ob_start();
        $template_path = plugin_dir_path(__FILE__) . "../templates/emails/{$template}.php";
        if (file_exists($template_path)) {
            include $template_path;
        }
        return ob_get_clean();
    }
}
