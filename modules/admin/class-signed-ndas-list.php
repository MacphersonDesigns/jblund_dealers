<?php
/**
 * Signed NDAs List Table
 *
 * Admin page displaying all dealers who have signed NDAs with their information
 *
 * @package JBLund_Dealers
 * @subpackage Admin
 */

namespace JBLund\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Signed_NDAs_List {

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
        add_action('admin_menu', array($this, 'add_menu_page'), 20);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Add menu page
     */
    public function add_menu_page() {
        add_submenu_page(
            'edit.php?post_type=dealer',
            __('Signed NDAs', 'jblund-dealers'),
            __('Signed NDAs', 'jblund-dealers'),
            'manage_options',
            'signed-ndas',
            array($this, 'render_page')
        );
    }

    /**
     * Enqueue assets
     */
    public function enqueue_assets($hook) {
        if ($hook !== 'dealer_page_signed-ndas') {
            return;
        }

        wp_enqueue_style(
            'jblund-signed-ndas',
            JBLUND_DEALERS_PLUGIN_URL . 'assets/css/admin-signed-ndas.css',
            array(),
            JBLUND_DEALERS_VERSION
        );
    }

    /**
     * Get all users who have signed NDAs
     */
    private function get_signed_nda_users() {
        $args = array(
            'meta_query' => array(
                array(
                    'key' => '_dealer_nda_acceptance',
                    'compare' => 'EXISTS',
                ),
            ),
            'orderby' => 'meta_value',
            'meta_key' => '_dealer_nda_signed_date',
            'order' => 'DESC',
        );

        return get_users($args);
    }

    /**
     * Get dealer post linked to user
     */
    private function get_linked_dealer_post($user_id) {
        $args = array(
            'post_type' => 'dealer',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => '_dealer_linked_user_id',
                    'value' => $user_id,
                    'compare' => '='
                )
            )
        );

        $query = new \WP_Query($args);
        return $query->have_posts() ? $query->posts[0] : null;
    }

    /**
     * Render the page
     */
    public function render_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'jblund-dealers'));
        }

        $users = $this->get_signed_nda_users();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e('Signed NDAs', 'jblund-dealers'); ?></h1>
            <p class="description">
                <?php esc_html_e('View all dealers who have signed the Non-Disclosure Agreement, including their company information and signed documents.', 'jblund-dealers'); ?>
            </p>

            <?php if (empty($users)): ?>
                <div class="notice notice-info">
                    <p><?php esc_html_e('No signed NDAs found. When dealers accept the NDA agreement, they will appear here.', 'jblund-dealers'); ?></p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped signed-ndas-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Representative', 'jblund-dealers'); ?></th>
                            <th><?php esc_html_e('Company', 'jblund-dealers'); ?></th>
                            <th><?php esc_html_e('Email', 'jblund-dealers'); ?></th>
                            <th><?php esc_html_e('Username', 'jblund-dealers'); ?></th>
                            <th><?php esc_html_e('Date Signed', 'jblund-dealers'); ?></th>
                            <th><?php esc_html_e('IP Address', 'jblund-dealers'); ?></th>
                            <th><?php esc_html_e('Dealer Post', 'jblund-dealers'); ?></th>
                            <th><?php esc_html_e('Actions', 'jblund-dealers'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user):
                            $acceptance_data = json_decode(get_user_meta($user->ID, '_dealer_nda_acceptance', true), true);
                            $pdf_url = get_user_meta($user->ID, '_dealer_nda_pdf_url', true);
                            $signed_date = get_user_meta($user->ID, '_dealer_nda_signed_date', true);
                            $ip_address = get_user_meta($user->ID, '_dealer_nda_ip', true);
                            $dealer_post = $this->get_linked_dealer_post($user->ID);

                            $rep_name = $acceptance_data['representative_name'] ?? $user->display_name;
                            $company = $acceptance_data['dealer_company'] ?? get_user_meta($user->ID, '_dealer_company_name', true);
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($rep_name); ?></strong>
                                    <?php if (!empty($acceptance_data['signature_data'])): ?>
                                        <br><span class="signature-indicator" title="<?php esc_attr_e('Digital signature on file', 'jblund-dealers'); ?>">✓ <?php esc_html_e('Signed', 'jblund-dealers'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($company); ?></td>
                                <td>
                                    <a href="mailto:<?php echo esc_attr($user->user_email); ?>">
                                        <?php echo esc_html($user->user_email); ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $user->ID)); ?>">
                                        <?php echo esc_html($user->user_login); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    if ($signed_date) {
                                        echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($signed_date)));
                                    } else {
                                        echo '—';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <code><?php echo esc_html($ip_address ?: '—'); ?></code>
                                </td>
                                <td>
                                    <?php if ($dealer_post): ?>
                                        <a href="<?php echo esc_url(get_edit_post_link($dealer_post->ID)); ?>">
                                            <?php echo esc_html(get_the_title($dealer_post->ID)); ?>
                                        </a>
                                        <br>
                                        <span class="post-state">
                                            <?php echo esc_html(ucfirst(get_post_status($dealer_post->ID))); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="no-dealer-post"><?php esc_html_e('No dealer post', 'jblund-dealers'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-column">
                                    <?php if ($pdf_url): ?>
                                        <a href="<?php echo esc_url($pdf_url); ?>" class="button button-small" target="_blank">
                                            <?php esc_html_e('View PDF', 'jblund-dealers'); ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($acceptance_data['signature_data'])): ?>
                                        <button type="button" class="button button-small view-signature-btn" data-signature="<?php echo esc_attr($acceptance_data['signature_data']); ?>" data-name="<?php echo esc_attr($rep_name); ?>">
                                            <?php esc_html_e('View Signature', 'jblund-dealers'); ?>
                                        </button>
                                    <?php endif; ?>

                                    <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $user->ID)); ?>" class="button button-small">
                                        <?php esc_html_e('Edit User', 'jblund-dealers'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="signed-ndas-stats">
                    <h3><?php esc_html_e('Statistics', 'jblund-dealers'); ?></h3>
                    <ul>
                        <li><?php printf(esc_html__('Total Signed NDAs: %d', 'jblund-dealers'), count($users)); ?></li>
                        <li><?php printf(esc_html__('Total Dealer Posts: %d', 'jblund-dealers'), count(array_filter($users, function($user) { return $this->get_linked_dealer_post($user->ID) !== null; }))); ?></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Signature Viewer Modal -->
        <div id="signature-viewer-modal" class="signature-modal" style="display:none;">
            <div class="signature-modal-content">
                <span class="signature-close">&times;</span>
                <h2 id="signature-modal-title"></h2>
                <div class="signature-display">
                    <img id="signature-modal-image" src="" alt="<?php esc_attr_e('Digital Signature', 'jblund-dealers'); ?>" />
                </div>
            </div>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // View signature modal
            $('.view-signature-btn').on('click', function() {
                var signatureData = $(this).data('signature');
                var repName = $(this).data('name');

                $('#signature-modal-title').text('<?php esc_js_e('Digital Signature for', 'jblund-dealers'); ?> ' + repName);
                $('#signature-modal-image').attr('src', signatureData);
                $('#signature-viewer-modal').fadeIn();
            });

            // Close modal
            $('.signature-close, #signature-viewer-modal').on('click', function(e) {
                if (e.target === this) {
                    $('#signature-viewer-modal').fadeOut();
                }
            });
        });
        </script>
        <?php
    }
}
