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

        $actions = sprintf(
            '<a href="#" class="button button-small view-registration-details" data-registration-id="%d">%s</a> ',
            $post_id,
            __('View Details', 'jblund-dealers')
        );

        if ($status === 'pending') {
            $approve_url = wp_nonce_url(
                admin_url('admin-post.php?action=jblund_approve_registration&registration_id=' . $post_id),
                'approve_registration_' . $post_id
            );
            $decline_url = '#';

            $actions .= sprintf(
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

        return $actions;
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
        add_action('wp_ajax_jblund_get_registration_details', [$this, 'ajax_get_registration_details']);
    }

    /**
     * Add admin menu item
     */
    public function add_admin_menu() {
        // Get pending count for menu bubble
        $pending_count = $this->get_pending_count();
        $menu_title = __('Applications', 'jblund-dealers');

        if ($pending_count > 0) {
            $menu_title .= sprintf(
                ' <span class="awaiting-mod">%d</span>',
                $pending_count
            );
        }

        add_submenu_page(
            'edit.php?post_type=dealer',
            __('Dealer Applications', 'jblund-dealers'),
            $menu_title,
            'manage_options',
            'jblund-dealer-registrations',
            [$this, 'render_admin_page']
        );
    }

    /**
     * Get pending registrations count
     */
    private function get_pending_count() {
        $query = new \WP_Query(array(
            'post_type' => 'dealer_registration',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_registration_status',
                    'value' => 'pending',
                )
            ),
            'fields' => 'ids',
        ));

        return $query->found_posts;
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

        // Add modal for viewing registration details
        $this->render_details_modal();

        echo '</div>';
    }

    /**
     * Render the registration details modal
     */
    private function render_details_modal() {
        $nonce = wp_create_nonce('jblund_registration_details');
        ?>
        <!-- Registration Details Modal -->
        <div id="registration-details-modal" style="display: none; position: fixed; z-index: 100000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
            <div style="background-color: #fefefe; margin: 50px auto; padding: 0; border: 1px solid #888; width: 80%; max-width: 800px; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);">
                <!-- Modal Header -->
                <div style="padding: 20px; background-color: #0073aa; color: white;">
                    <span class="close-modal" style="color: white; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                    <h2 style="margin: 0; color: white;"><?php _e('Registration Application Details', 'jblund-dealers'); ?></h2>
                </div>

                <!-- Modal Body -->
                <div id="registration-details-content" style="padding: 20px;">
                    <div class="loading-spinner" style="text-align: center; padding: 40px;">
                        <span class="spinner is-active" style="float: none; margin: 0 auto;"></span>
                        <p><?php _e('Loading application details...', 'jblund-dealers'); ?></p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div style="padding: 20px; background-color: #f1f1f1; text-align: right;">
                    <button type="button" class="button close-modal"><?php _e('Close', 'jblund-dealers'); ?></button>
                </div>
            </div>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var modal = $('#registration-details-modal');
            var detailsContent = $('#registration-details-content');

            // View Details button click
            $(document).on('click', '.view-registration-details', function(e) {
                e.preventDefault();
                var registrationId = $(this).data('registration-id');

                // Show modal and loading state
                modal.show();
                detailsContent.html('<div class="loading-spinner" style="text-align: center; padding: 40px;"><span class="spinner is-active" style="float: none;"></span><p><?php _e('Loading application details...', 'jblund-dealers'); ?></p></div>');

                // Fetch registration details
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jblund_get_registration_details',
                        registration_id: registrationId,
                        nonce: '<?php echo $nonce; ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            var html = '<table class="form-table" style="width: 100%;">';

                            html += '<tr><th colspan="2" style="background: #f9f9f9; padding: 12px;"><h3 style="margin: 0;"><?php _e('Representative Information', 'jblund-dealers'); ?></h3></th></tr>';
                            html += '<tr><th style="width: 30%; padding: 12px;"><?php _e('Name', 'jblund-dealers'); ?></th><td style="padding: 12px;">' + (data.rep_first_name + ' ' + data.rep_last_name) + '</td></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('Email', 'jblund-dealers'); ?></th><td style="padding: 12px;"><a href="mailto:' + data.email + '">' + data.email + '</a></td></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('Phone', 'jblund-dealers'); ?></th><td style="padding: 12px;">' + (data.phone || '—') + '</td></tr>';

                            html += '<tr><th colspan="2" style="background: #f9f9f9; padding: 12px; border-top: 2px solid #ddd;"><h3 style="margin: 0;"><?php _e('Company Information', 'jblund-dealers'); ?></h3></th></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('Company Name', 'jblund-dealers'); ?></th><td style="padding: 12px;"><strong>' + data.company + '</strong></td></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('Phone', 'jblund-dealers'); ?></th><td style="padding: 12px;">' + (data.company_phone || '—') + '</td></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('Territory/Location', 'jblund-dealers'); ?></th><td style="padding: 12px;">' + (data.territory || '—') + '</td></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('Address', 'jblund-dealers'); ?></th><td style="padding: 12px; white-space: pre-wrap;">' + (data.company_address || '—') + '</td></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('Website', 'jblund-dealers'); ?></th><td style="padding: 12px;">' + (data.company_website ? '<a href="' + data.company_website + '" target="_blank">' + data.company_website + '</a>' : '—') + '</td></tr>';

                            html += '<tr><th colspan="2" style="background: #f9f9f9; padding: 12px; border-top: 2px solid #ddd;"><h3 style="margin: 0;"><?php _e('Products & Services', 'jblund-dealers'); ?></h3></th></tr>';
                            var services = [];
                            if (data.docks == '1') services.push('<?php _e('Docks', 'jblund-dealers'); ?>');
                            if (data.lifts == '1') services.push('<?php _e('Lifts', 'jblund-dealers'); ?>');
                            if (data.trailers == '1') services.push('<?php _e('Trailers', 'jblund-dealers'); ?>');
                            html += '<tr><td colspan="2" style="padding: 12px;">' + (services.length > 0 ? services.join(', ') : '<?php _e('None selected', 'jblund-dealers'); ?>') + '</td></tr>';

                            html += '<tr><th colspan="2" style="background: #f9f9f9; padding: 12px; border-top: 2px solid #ddd;"><h3 style="margin: 0;"><?php _e('Business Description', 'jblund-dealers'); ?></h3></th></tr>';
                            html += '<tr><td colspan="2" style="padding: 12px; white-space: pre-wrap;">' + (data.notes || '<?php _e('No description provided.', 'jblund-dealers'); ?>') + '</td></tr>';

                            html += '<tr><th colspan="2" style="background: #f9f9f9; padding: 12px; border-top: 2px solid #ddd;"><h3 style="margin: 0;"><?php _e('Submission Details', 'jblund-dealers'); ?></h3></th></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('Status', 'jblund-dealers'); ?></th><td style="padding: 12px;"><span class="status-badge status-' + data.status + '" style="padding: 4px 12px; border-radius: 3px; font-weight: bold;">' + data.status.charAt(0).toUpperCase() + data.status.slice(1) + '</span></td></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('Date Submitted', 'jblund-dealers'); ?></th><td style="padding: 12px;">' + data.date + '</td></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('IP Address', 'jblund-dealers'); ?></th><td style="padding: 12px;">' + (data.ip_address || '—') + '</td></tr>';
                            html += '<tr><th style="padding: 12px;"><?php _e('User Agent', 'jblund-dealers'); ?></th><td style="padding: 12px;"><small>' + (data.user_agent || '—') + '</small></td></tr>';

                            // Dealer Profile Link (if approved)
                            if (data.dealer_id && data.status === 'approved') {
                                html += '<tr><th colspan="2" style="background: #fff3cd; padding: 12px; border-top: 2px solid #ffc107;"><h3 style="margin: 0; color: #856404;"><?php _e('⏳ Dealer Profile Created (Draft)', 'jblund-dealers'); ?></h3></th></tr>';
                                html += '<tr><td colspan="2" style="padding: 12px; background: #fffbf0;">';
                                html += '<p style="margin: 0 0 10px 0;"><?php _e('This application has been approved and a dealer profile has been created as a <strong>draft</strong>. The profile will be automatically published when the dealer signs the NDA.', 'jblund-dealers'); ?></p>';
                                html += '<a href="<?php echo admin_url('post.php?action=edit&post='); ?>' + data.dealer_id + '" class="button button-secondary" target="_blank"><?php _e('View Draft Profile →', 'jblund-dealers'); ?></a>';
                                html += '</td></tr>';
                            }

                            // Activity Log
                            if (data.activity && data.activity.length > 0) {
                                html += '<tr><th colspan="2" style="background: #f9f9f9; padding: 12px; border-top: 2px solid #ddd;"><h3 style="margin: 0;"><?php _e('Activity Log', 'jblund-dealers'); ?></h3></th></tr>';
                                html += '<tr><td colspan="2" style="padding: 0;"><table style="width: 100%; border-collapse: collapse;">';
                                html += '<thead><tr style="background: #f0f0f0;"><th style="padding: 8px; text-align: left; width: 20%;"><?php _e('Date/Time', 'jblund-dealers'); ?></th><th style="padding: 8px; text-align: left; width: 15%;"><?php _e('Action', 'jblund-dealers'); ?></th><th style="padding: 8px; text-align: left; width: 20%;"><?php _e('User', 'jblund-dealers'); ?></th><th style="padding: 8px; text-align: left;"><?php _e('Details', 'jblund-dealers'); ?></th></tr></thead><tbody>';

                                data.activity.forEach(function(entry) {
                                    var actionBadge = '';
                                    if (entry.action === 'viewed') {
                                        actionBadge = '<span style="background: #e3f2fd; color: #1976d2; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold;">' + entry.action.toUpperCase() + '</span>';
                                    } else if (entry.action === 'approved') {
                                        actionBadge = '<span style="background: #d4edda; color: #155724; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold;">' + entry.action.toUpperCase() + '</span>';
                                    } else if (entry.action === 'declined') {
                                        actionBadge = '<span style="background: #f8d7da; color: #721c24; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold;">' + entry.action.toUpperCase() + '</span>';
                                    } else if (entry.action === 'dealer_published') {
                                        actionBadge = '<span style="background: #d1ecf1; color: #0c5460; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold;">PUBLISHED</span>';
                                    } else {
                                        actionBadge = '<span style="background: #f0f0f0; color: #333; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold;">' + entry.action.toUpperCase() + '</span>';
                                    }

                                    html += '<tr style="border-bottom: 1px solid #e0e0e0;">';
                                    html += '<td style="padding: 8px;"><small>' + entry.timestamp + '</small></td>';
                                    html += '<td style="padding: 8px;">' + actionBadge + '</td>';
                                    html += '<td style="padding: 8px;">' + entry.user + '</td>';
                                    html += '<td style="padding: 8px;"><small>' + (entry.note || '—') + '</small></td>';
                                    html += '</tr>';
                                });

                                html += '</tbody></table></td></tr>';
                            }

                            html += '</table>';

                            detailsContent.html(html);
                        } else {
                            detailsContent.html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                        }
                    },
                    error: function() {
                        detailsContent.html('<div class="notice notice-error"><p><?php _e('Failed to load application details.', 'jblund-dealers'); ?></p></div>');
                    }
                });
            });

            // Close modal
            $('.close-modal').on('click', function() {
                modal.hide();
            });

            // Close on outside click
            $(window).on('click', function(e) {
                if (e.target.id === 'registration-details-modal') {
                    modal.hide();
                }
            });
        });
        </script>

        <style>
            .status-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 3px;
                font-size: 12px;
                font-weight: bold;
                text-transform: uppercase;
            }
            .status-pending {
                background-color: #fff8e5;
                color: #f0b849;
                border: 1px solid #f0b849;
            }
            .status-approved {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #28a745;
            }
            .status-rejected {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #dc3545;
            }
        </style>
        <?php
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
        // Get all registration data
        $rep_first_name = get_post_meta($registration_id, '_registration_rep_first_name', true);
        $rep_last_name = get_post_meta($registration_id, '_registration_rep_last_name', true);
        $rep_name = get_post_meta($registration_id, '_registration_rep_name', true);
        $email = get_post_meta($registration_id, '_registration_email', true);
        $rep_phone = get_post_meta($registration_id, '_registration_phone', true);
        $company = get_post_meta($registration_id, '_registration_company', true);
        $company_website = get_post_meta($registration_id, '_registration_company_website', true);

        if (!$email || !$rep_first_name || !$rep_last_name) {
            wp_redirect(add_query_arg(['error' => 'Invalid registration data'], wp_get_referer()));
            exit;
        }

        // Generate username from first initial + last name (e.g., Alex Macpherson -> amacpherson)
        $username_base = strtolower(substr($rep_first_name, 0, 1) . $rep_last_name);
        $username = sanitize_user(str_replace(' ', '', $username_base));

        // Handle duplicate usernames by appending numbers
        $username_original = $username;
        $counter = 1;
        while (username_exists($username)) {
            $username = $username_original . $counter;
            $counter++;
        }

        $password = wp_generate_password(16, true, true);

        // Check if user already exists
        if (username_exists($username) || email_exists($email)) {
            wp_redirect(add_query_arg(['error' => 'User already exists with this email'], wp_get_referer()));
            exit;
        }

        // Create user with complete profile data from registration
        $user_data = [
            'user_login'    => $username,
            'user_pass'     => $password,
            'user_email'    => $email,
            'first_name'    => $rep_first_name,
            'last_name'     => $rep_last_name,
            'nickname'      => $company,              // Company name as nickname
            'display_name'  => $rep_name,             // Full name as display name
            'user_url'      => $company_website,      // Company website
            'role'          => '',                     // Don't assign default role yet
        ];

        $user_id = wp_insert_user($user_data);

        if (is_wp_error($user_id)) {
            wp_redirect(add_query_arg(['error' => $user_id->get_error_message()], wp_get_referer()));
            exit;
        }

        // Assign dealer role
        if (class_exists('JBLund\DealerPortal\Dealer_Role')) {
            \JBLund\DealerPortal\Dealer_Role::assign_to_user($user_id);
        }

        // Update additional user meta
        update_user_meta($user_id, '_dealer_company_name', $company);
        update_user_meta($user_id, '_dealer_rep_phone', $rep_phone);
        update_user_meta($user_id, '_force_password_change', true);  // Require password change on first login

        // Create dealer post automatically
        $dealer_id = $this->create_dealer_post_from_registration($registration_id, $user_id);

        // Update registration status
        update_post_meta($registration_id, '_registration_status', 'approved');
        update_post_meta($registration_id, '_registration_approved_by', get_current_user_id());
        update_post_meta($registration_id, '_registration_approved_date', current_time('mysql'));
        update_post_meta($registration_id, '_registration_user_id', $user_id);
        update_post_meta($registration_id, '_registration_dealer_id', $dealer_id);

        // Log approval activity
        $this->log_activity($registration_id, 'approved', 'Application approved - user account created and dealer profile created as draft (pending NDA)', [
            'user_id' => $user_id,
            'username' => $username,
            'dealer_id' => $dealer_id
        ]);

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

        // Log decline activity
        $this->log_activity($registration_id, 'declined', 'Application declined', [
            'reason' => $rejection_reason
        ]);

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

        // Get custom template or default
        $template_content = get_option('jblund_email_template_approval');

        if ($template_content) {
            // Use custom template with shortcode replacement
            $message = str_replace(
                array('{{rep_name}}', '{{username}}', '{{password}}', '{{login_url}}'),
                array($rep_name, $username, $password, wp_login_url()),
                $template_content
            );
        } else {
            // Use file-based template (default)
            $vars = [
                'rep_name' => $rep_name,
                'username' => $username,
                'password' => $password,
                'login_url' => wp_login_url(),
            ];
            $message = $this->get_mail_message('registration-approval', $vars);
        }

        // Apply brand color
        $brand_color = get_option('jblund_email_brand_color', '#FF0000');
        $message = str_replace('#003366', $brand_color, $message);

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

        // Get custom template or default
        $template_content = get_option('jblund_email_template_rejection');

        if ($template_content) {
            // Use custom template with shortcode replacement
            $message = str_replace(
                array('{{rep_name}}', '{{reason}}'),
                array($rep_name, $reason),
                $template_content
            );
        } else {
            // Use file-based template (default)
            $vars = [
                'rep_name' => $rep_name,
                'reason'   => $reason,
            ];
            $message = $this->get_mail_message('registration-rejection', $vars);
        }

        // Apply brand color
        $brand_color = get_option('jblund_email_brand_color', '#FF0000');
        $message = str_replace('#003366', $brand_color, $message);
        $message = str_replace('#6c757d', $brand_color, $message);

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

    /**
     * AJAX handler to get registration details
     */
    public function ajax_get_registration_details() {
        check_ajax_referer('jblund_registration_details', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'jblund-dealers')]);
        }

        $registration_id = isset($_POST['registration_id']) ? intval($_POST['registration_id']) : 0;

        if (!$registration_id) {
            wp_send_json_error(['message' => __('Invalid registration ID', 'jblund-dealers')]);
        }

        // Get all registration meta data
        $data = [
            'rep_first_name' => get_post_meta($registration_id, '_registration_rep_first_name', true),
            'rep_last_name' => get_post_meta($registration_id, '_registration_rep_last_name', true),
            'rep_name' => get_post_meta($registration_id, '_registration_rep_name', true),
            'email' => get_post_meta($registration_id, '_registration_email', true),
            'phone' => get_post_meta($registration_id, '_registration_phone', true),
            'company' => get_post_meta($registration_id, '_registration_company', true),
            'company_phone' => get_post_meta($registration_id, '_registration_company_phone', true),
            'territory' => get_post_meta($registration_id, '_registration_territory', true),
            'company_address' => get_post_meta($registration_id, '_registration_company_address', true),
            'company_website' => get_post_meta($registration_id, '_registration_company_website', true),
            'docks' => get_post_meta($registration_id, '_registration_docks', true),
            'lifts' => get_post_meta($registration_id, '_registration_lifts', true),
            'trailers' => get_post_meta($registration_id, '_registration_trailers', true),
            'notes' => get_post_meta($registration_id, '_registration_notes', true),
            'status' => get_post_meta($registration_id, '_registration_status', true),
            'date' => get_post_meta($registration_id, '_registration_date', true),
            'ip_address' => get_post_meta($registration_id, '_registration_ip', true),
            'user_agent' => get_post_meta($registration_id, '_registration_user_agent', true),
            'activity' => get_post_meta($registration_id, '_registration_activity', true),
            'dealer_id' => get_post_meta($registration_id, '_registration_dealer_id', true),
        ];

        // Log the view activity - but only once per day per user to avoid spam
        $this->log_activity_throttled($registration_id, 'viewed', 'Application viewed by admin');

        wp_send_json_success($data);
    }

    /**
     * Log activity with throttling to prevent spam
     *
     * Only logs if the same action by same user hasn't been logged in the last 24 hours
     *
     * @param int $registration_id Application post ID
     * @param string $action Action type (viewed, approved, declined)
     * @param string $note Optional note about the action
     * @param array $extra_data Optional additional data to log
     * @return void
     */
    private function log_activity_throttled($registration_id, $action, $note = '', $extra_data = []) {
        // Get current user
        $current_user = wp_get_current_user();

        // Get existing activity log
        $activity = get_post_meta($registration_id, '_registration_activity', true);
        if (!is_array($activity)) {
            $activity = [];
        }

        // Check if same user performed same action recently (within 24 hours)
        $now = current_time('timestamp');
        $throttle_period = 24 * HOUR_IN_SECONDS; // 24 hours

        foreach (array_reverse($activity) as $entry) {
            if ($entry['action'] === $action && $entry['user_id'] === $current_user->ID) {
                $entry_time = strtotime($entry['timestamp']);
                if (($now - $entry_time) < $throttle_period) {
                    // Same action by same user within throttle period - skip logging
                    return;
                }
                // Found an older entry, break and log new one
                break;
            }
        }

        // Log the activity (not throttled)
        $this->log_activity($registration_id, $action, $note, $extra_data);
    }

    /**
     * Log activity for an application
     *
     * @param int $registration_id Application post ID
     * @param string $action Action type (viewed, approved, declined)
     * @param string $note Optional note about the action
     * @param array $extra_data Optional additional data to log
     * @return void
     */
    private function log_activity($registration_id, $action, $note = '', $extra_data = []) {
        // Get current user info
        $current_user = wp_get_current_user();
        $user_name = $current_user->display_name ?: $current_user->user_login;

        // Get existing activity log
        $activity = get_post_meta($registration_id, '_registration_activity', true);
        if (!is_array($activity)) {
            $activity = [];
        }

        // Create new activity entry
        $entry = [
            'action' => sanitize_text_field($action),
            'user' => sanitize_text_field($user_name),
            'user_id' => $current_user->ID,
            'timestamp' => current_time('mysql'),
            'note' => sanitize_text_field($note),
        ];

        // Add any extra data
        if (!empty($extra_data)) {
            $entry = array_merge($entry, $extra_data);
        }

        // Add to activity log
        $activity[] = $entry;

        // Save updated activity
        update_post_meta($registration_id, '_registration_activity', $activity);
    }

    /**
     * Create a dealer post from approved registration
     *
     * @param int $registration_id Registration post ID
     * @param int $user_id Associated WordPress user ID
     * @return int|false Dealer post ID on success, false on failure
     */
    private function create_dealer_post_from_registration($registration_id, $user_id) {
        // Get all registration data
        $company = get_post_meta($registration_id, '_registration_company', true);
        $company_address = get_post_meta($registration_id, '_registration_company_address', true);
        $company_phone = get_post_meta($registration_id, '_registration_company_phone', true);
        $company_website = get_post_meta($registration_id, '_registration_company_website', true);
        $territory = get_post_meta($registration_id, '_registration_territory', true);
        $docks = get_post_meta($registration_id, '_registration_docks', true);
        $lifts = get_post_meta($registration_id, '_registration_lifts', true);
        $trailers = get_post_meta($registration_id, '_registration_trailers', true);
        $notes = get_post_meta($registration_id, '_registration_notes', true);

        // Create dealer post as DRAFT (will be published when NDA is signed)
        $dealer_post = [
            'post_type' => 'dealer',
            'post_title' => $company,
            'post_status' => 'draft',
            'post_author' => $user_id,
        ];

        $dealer_id = wp_insert_post($dealer_post);

        if (is_wp_error($dealer_id)) {
            return false;
        }

        // Transfer registration data to dealer meta
        // Main dealer info
        update_post_meta($dealer_id, '_dealer_company_name', $company);
        update_post_meta($dealer_id, '_dealer_company_address', $company_address);
        update_post_meta($dealer_id, '_dealer_company_phone', $company_phone);
        update_post_meta($dealer_id, '_dealer_website', $company_website);

        // Services
        update_post_meta($dealer_id, '_dealer_docks', $docks);
        update_post_meta($dealer_id, '_dealer_lifts', $lifts);
        update_post_meta($dealer_id, '_dealer_trailers', $trailers);

        // Store territory in dealer notes for now (or create custom field if needed)
        if ($territory) {
            $dealer_notes = "Territory: $territory\n\n";
            if ($notes) {
                $dealer_notes .= "Application Notes:\n$notes";
            }
            update_post_meta($dealer_id, '_dealer_notes', $dealer_notes);
        }

        // Link back to registration
        update_post_meta($dealer_id, '_dealer_registration_id', $registration_id);
        update_post_meta($dealer_id, '_dealer_user_id', $user_id);

        return $dealer_id;
    }
}
