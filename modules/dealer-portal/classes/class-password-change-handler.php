<?php
/**
 * Force Password Change Handler
 *
 * Handles forcing dealers to change their password on first login
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 1.3.0
 */

namespace JBLund\DealerPortal;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Password Change Class
 *
 * Forces password change for new dealer accounts
 */
class Password_Change_Handler {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_login', array($this, 'check_force_password_change'), 10, 2);
        add_action('template_redirect', array($this, 'redirect_to_password_change'));
        add_shortcode('jblund_force_password_change', array($this, 'password_change_form_shortcode'));
    }

    /**
     * Check if user needs to change password on login
     *
     * @param string $user_login Username
     * @param \WP_User $user User object
     */
    public function check_force_password_change($user_login, $user) {
        // Only for dealers
        if (!in_array('dealer', (array) $user->roles, true)) {
            return;
        }

        // Check if this is a new account that needs password change
        $password_changed = get_user_meta($user->ID, '_dealer_password_changed', true);

        if (!$password_changed) {
            // Set flag that password change is required
            update_user_meta($user->ID, '_dealer_force_password_change', '1');
        }
    }

    /**
     * Redirect dealer to password change page if required
     */
    public function redirect_to_password_change() {
        // Only for logged-in dealers
        if (!is_user_logged_in()) {
            return;
        }

        $current_user = wp_get_current_user();

        // Only for dealers
        if (!in_array('dealer', (array) $current_user->roles, true)) {
            return;
        }

        // Check if password change is required
        $force_change = get_user_meta($current_user->ID, '_dealer_force_password_change', true);

        if (!$force_change) {
            return;
        }

        // Get password change page
        $portal_pages = get_option('jblund_dealers_portal_pages', array());
        $password_change_page_id = isset($portal_pages['password_change']) ? $portal_pages['password_change'] : 0;

        // If no password change page is set, allow through (fail gracefully)
        if (!$password_change_page_id) {
            return;
        }

        // Don't redirect if already on password change page
        if (is_page($password_change_page_id)) {
            return;
        }

        // Don't redirect if on logout or admin
        if (is_admin() || isset($_GET['action']) && $_GET['action'] === 'logout') {
            return;
        }

        // Redirect to password change page
        wp_redirect(get_permalink($password_change_page_id));
        exit;
    }

    /**
     * Password change form shortcode
     *
     * Usage: [jblund_force_password_change]
     *
     * @return string Form HTML
     */
    public function password_change_form_shortcode() {
        // Must be logged in
        if (!is_user_logged_in()) {
            return '<p>' . __('You must be logged in to change your password.', 'jblund-dealers') . '</p>';
        }

        $current_user = wp_get_current_user();

        // Only for dealers
        if (!in_array('dealer', (array) $current_user->roles, true)) {
            return '<p>' . __('This page is for dealer accounts only.', 'jblund-dealers') . '</p>';
        }

        // Check if password was just changed
        $success = isset($_GET['password_changed']) && $_GET['password_changed'] === 'success';
        $error = isset($_GET['password_error']) ? sanitize_text_field($_GET['password_error']) : '';

        ob_start();
        ?>
        <div class="jblund-password-change">
            <div class="password-change-container">
                <div class="password-change-header">
                    <h1><?php _e('Change Your Password', 'jblund-dealers'); ?></h1>
                    <p><?php _e('For security reasons, you must change your password before accessing your dealer portal.', 'jblund-dealers'); ?></p>
                </div>

                <?php if ($success) : ?>
                    <div class="password-success">
                        <p><?php _e('Your password has been changed successfully! Redirecting...', 'jblund-dealers'); ?></p>
                    </div>
                    <script>
                        setTimeout(function() {
                            <?php
                            // Redirect to NDA page if not accepted, otherwise to dashboard
                            $nda_data = get_user_meta($current_user->ID, '_dealer_nda_acceptance', true);
                            $nda_accepted = !empty($nda_data['accepted']);

                            if ($nda_accepted) {
                                $portal_pages = get_option('jblund_dealers_portal_pages', array());
                                $dashboard_page_id = isset($portal_pages['dashboard']) ? $portal_pages['dashboard'] : 0;
                                if ($dashboard_page_id) {
                                    echo 'window.location.href = "' . esc_js(get_permalink($dashboard_page_id)) . '";';
                                }
                            } else {
                                $portal_pages = get_option('jblund_dealers_portal_pages', array());
                                $nda_page_id = isset($portal_pages['nda']) ? $portal_pages['nda'] : 0;
                                if ($nda_page_id) {
                                    echo 'window.location.href = "' . esc_js(get_permalink($nda_page_id)) . '";';
                                }
                            }
                            ?>
                        }, 2000);
                    </script>
                <?php else : ?>
                    <?php if ($error) : ?>
                        <div class="password-error">
                            <strong><?php _e('Error:', 'jblund-dealers'); ?></strong> <?php echo esc_html($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="" class="password-change-form">
                        <?php wp_nonce_field('change_dealer_password', 'password_change_nonce'); ?>
                        <input type="hidden" name="jblund_password_action" value="change" />

                        <div class="form-row">
                            <label for="current_password">
                                <?php _e('Current Password', 'jblund-dealers'); ?>
                                <span class="required">*</span>
                            </label>
                            <input type="password" id="current_password" name="current_password" required autocomplete="current-password" />
                        </div>

                        <div class="form-row">
                            <label for="new_password">
                                <?php _e('New Password', 'jblund-dealers'); ?>
                                <span class="required">*</span>
                            </label>
                            <input type="password" id="new_password" name="new_password" required autocomplete="new-password" />
                            <p class="field-description"><?php _e('Password must be at least 8 characters long and include letters and numbers.', 'jblund-dealers'); ?></p>
                        </div>

                        <div class="form-row">
                            <label for="confirm_password">
                                <?php _e('Confirm New Password', 'jblund-dealers'); ?>
                                <span class="required">*</span>
                            </label>
                            <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password" />
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="button-primary">
                                <?php _e('Change Password', 'jblund-dealers'); ?>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <style>
        .jblund-password-change {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }

        .password-change-container {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .password-change-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #003366;
        }

        .password-change-header h1 {
            margin: 0 0 10px 0;
            color: #003366;
            font-size: 28px;
        }

        .password-change-header p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .password-success {
            text-align: center;
            padding: 20px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            color: #155724;
        }

        .password-success p {
            margin: 0;
            font-size: 16px;
        }

        .password-error {
            margin: 0 0 20px 0;
            padding: 15px 20px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            color: #721c24;
        }

        .password-change-form .form-row {
            margin-bottom: 20px;
        }

        .password-change-form label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .password-change-form .required {
            color: #c00;
        }

        .password-change-form input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .password-change-form input:focus {
            outline: none;
            border-color: #003366;
        }

        .field-description {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }

        .form-actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }

        .button-primary {
            background: #003366;
            color: #fff;
            padding: 12px 40px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .button-primary:hover {
            background: #002244;
        }
        </style>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.password-change-form');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('<?php esc_js(_e('Passwords do not match. Please try again.', 'jblund-dealers')); ?>');
                    return false;
                }

                if (newPassword.length < 8) {
                    e.preventDefault();
                    alert('<?php esc_js(_e('Password must be at least 8 characters long.', 'jblund-dealers')); ?>');
                    return false;
                }
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}

// Hook into form submission
add_action('init', function() {
    if (!isset($_POST['jblund_password_action']) || $_POST['jblund_password_action'] !== 'change') {
        return;
    }

    if (!is_user_logged_in()) {
        return;
    }

    // Verify nonce
    if (!isset($_POST['password_change_nonce']) || !wp_verify_nonce($_POST['password_change_nonce'], 'change_dealer_password')) {
        wp_redirect(add_query_arg('password_error', urlencode(__('Security check failed', 'jblund-dealers')), wp_get_referer()));
        exit;
    }

    $current_user = wp_get_current_user();

    // Verify dealer role
    if (!in_array('dealer', (array) $current_user->roles, true)) {
        return;
    }

    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validate current password
    if (!wp_check_password($current_password, $current_user->user_pass, $current_user->ID)) {
        wp_redirect(add_query_arg('password_error', urlencode(__('Current password is incorrect', 'jblund-dealers')), wp_get_referer()));
        exit;
    }

    // Validate new password
    if (strlen($new_password) < 8) {
        wp_redirect(add_query_arg('password_error', urlencode(__('Password must be at least 8 characters long', 'jblund-dealers')), wp_get_referer()));
        exit;
    }

    if ($new_password !== $confirm_password) {
        wp_redirect(add_query_arg('password_error', urlencode(__('Passwords do not match', 'jblund-dealers')), wp_get_referer()));
        exit;
    }

    // Change password
    wp_set_password($new_password, $current_user->ID);

    // Mark password as changed
    delete_user_meta($current_user->ID, '_dealer_force_password_change');
    update_user_meta($current_user->ID, '_dealer_password_changed', '1');

    // Re-authenticate user
    wp_set_auth_cookie($current_user->ID, true);

    // Redirect with success
    wp_redirect(add_query_arg('password_changed', 'success', wp_get_referer()));
    exit;
});
