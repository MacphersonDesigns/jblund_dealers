<?php
/**
 * Dashboard Widget for Dealer Applications
 *
 * Displays pending dealer registrations on the WordPress dashboard
 *
 * @package JBLund_Dealers
 * @subpackage Admin
 */

namespace JBLund\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Dashboard_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
    }

    /**
     * Add dashboard widget
     */
    public function add_dashboard_widget() {
        // Only show to users who can manage options
        if (!current_user_can('manage_options')) {
            return;
        }

        wp_add_dashboard_widget(
            'jblund_dealer_applications',
            __('Dealer Applications', 'jblund-dealers'),
            array($this, 'render_widget')
        );
    }

    /**
     * Render the dashboard widget
     */
    public function render_widget() {
        // Get pending registrations count
        $pending_count = $this->get_pending_count();
        $total_count = $this->get_total_count();
        $recent = $this->get_recent_registrations(5);

        ?>
        <div class="jblund-dashboard-widget">
            <div class="widget-stats">
                <div class="stat-box pending">
                    <span class="stat-number"><?php echo esc_html($pending_count); ?></span>
                    <span class="stat-label"><?php _e('Pending', 'jblund-dealers'); ?></span>
                </div>
                <div class="stat-box total">
                    <span class="stat-number"><?php echo esc_html($total_count); ?></span>
                    <span class="stat-label"><?php _e('Total Applications', 'jblund-dealers'); ?></span>
                </div>
            </div>

            <?php if (!empty($recent)) : ?>
                <div class="widget-recent">
                    <h4><?php _e('Recent Applications', 'jblund-dealers'); ?></h4>
                    <ul class="recent-applications-list">
                        <?php foreach ($recent as $registration) :
                            $rep_name = get_post_meta($registration->ID, '_registration_rep_name', true);
                            $company = get_post_meta($registration->ID, '_registration_company', true);
                            $email = get_post_meta($registration->ID, '_registration_email', true);
                            $status = get_post_meta($registration->ID, '_registration_status', true);
                            $date = get_post_meta($registration->ID, '_registration_date', true);

                            $status_class = 'status-' . esc_attr($status);
                            $status_label = ucfirst($status);

                            $is_new = (time() - strtotime($date)) < (24 * 60 * 60); // Within 24 hours
                        ?>
                            <li class="application-item <?php echo $is_new ? 'is-new' : ''; ?>">
                                <div class="app-info">
                                    <strong><?php echo esc_html($rep_name); ?></strong>
                                    <?php if ($is_new) : ?>
                                        <span class="new-badge"><?php _e('NEW', 'jblund-dealers'); ?></span>
                                    <?php endif; ?>
                                    <br>
                                    <span class="app-company"><?php echo esc_html($company); ?></span>
                                    <br>
                                    <span class="app-email"><?php echo esc_html($email); ?></span>
                                </div>
                                <div class="app-meta">
                                    <span class="app-status <?php echo $status_class; ?>"><?php echo esc_html($status_label); ?></span>
                                    <span class="app-date"><?php echo esc_html(human_time_diff(strtotime($date), current_time('timestamp'))); ?> <?php _e('ago', 'jblund-dealers'); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else : ?>
                <p class="no-applications"><?php _e('No recent applications.', 'jblund-dealers'); ?></p>
            <?php endif; ?>

            <div class="widget-actions">
                <a href="<?php echo admin_url('edit.php?post_type=dealer&page=jblund-dealer-registrations'); ?>" class="button button-primary">
                    <?php _e('View All Applications', 'jblund-dealers'); ?>
                </a>
            </div>

            <style>
                .jblund-dashboard-widget .widget-stats {
                    display: flex;
                    gap: 15px;
                    margin-bottom: 20px;
                }
                .jblund-dashboard-widget .stat-box {
                    flex: 1;
                    text-align: center;
                    padding: 15px;
                    border-radius: 4px;
                    background: #f0f0f1;
                }
                .jblund-dashboard-widget .stat-box.pending {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                }
                .jblund-dashboard-widget .stat-box.total {
                    background: #e7f3e7;
                    border-left: 4px solid #46b450;
                }
                .jblund-dashboard-widget .stat-number {
                    display: block;
                    font-size: 32px;
                    font-weight: bold;
                    line-height: 1;
                    margin-bottom: 5px;
                }
                .jblund-dashboard-widget .stat-label {
                    display: block;
                    font-size: 12px;
                    color: #666;
                    text-transform: uppercase;
                }
                .jblund-dashboard-widget h4 {
                    margin: 15px 0 10px;
                    font-size: 14px;
                }
                .jblund-dashboard-widget .recent-applications-list {
                    margin: 0;
                    padding: 0;
                    list-style: none;
                }
                .jblund-dashboard-widget .application-item {
                    padding: 10px;
                    margin: 5px 0;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                }
                .jblund-dashboard-widget .application-item.is-new {
                    background: #fff3cd;
                    border-left-width: 4px;
                    border-left-color: #ffc107;
                }
                .jblund-dashboard-widget .app-info {
                    flex: 1;
                }
                .jblund-dashboard-widget .app-info strong {
                    font-size: 14px;
                }
                .jblund-dashboard-widget .new-badge {
                    display: inline-block;
                    background: #ffc107;
                    color: #000;
                    padding: 2px 6px;
                    border-radius: 3px;
                    font-size: 10px;
                    font-weight: bold;
                    margin-left: 8px;
                }
                .jblund-dashboard-widget .app-company {
                    color: #666;
                    font-size: 13px;
                }
                .jblund-dashboard-widget .app-email {
                    color: #999;
                    font-size: 12px;
                }
                .jblund-dashboard-widget .app-meta {
                    text-align: right;
                    margin-left: 15px;
                }
                .jblund-dashboard-widget .app-status {
                    display: block;
                    padding: 4px 8px;
                    border-radius: 3px;
                    font-size: 11px;
                    font-weight: bold;
                    text-transform: uppercase;
                    margin-bottom: 5px;
                }
                .jblund-dashboard-widget .app-status.status-pending {
                    background: #fff3cd;
                    color: #856404;
                }
                .jblund-dashboard-widget .app-status.status-approved {
                    background: #d4edda;
                    color: #155724;
                }
                .jblund-dashboard-widget .app-status.status-rejected {
                    background: #f8d7da;
                    color: #721c24;
                }
                .jblund-dashboard-widget .app-date {
                    display: block;
                    font-size: 11px;
                    color: #999;
                }
                .jblund-dashboard-widget .widget-actions {
                    margin-top: 15px;
                    padding-top: 15px;
                    border-top: 1px solid #ddd;
                }
                .jblund-dashboard-widget .no-applications {
                    text-align: center;
                    color: #666;
                    padding: 20px 0;
                }
            </style>
        </div>
        <?php
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
     * Get total registrations count
     */
    private function get_total_count() {
        $query = new \WP_Query(array(
            'post_type' => 'dealer_registration',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ));

        return $query->found_posts;
    }

    /**
     * Get recent registrations
     */
    private function get_recent_registrations($limit = 5) {
        $query = new \WP_Query(array(
            'post_type' => 'dealer_registration',
            'post_status' => 'any',
            'posts_per_page' => $limit,
            'orderby' => 'meta_value',
            'meta_key' => '_registration_date',
            'order' => 'DESC',
        ));

        return $query->posts;
    }
}
