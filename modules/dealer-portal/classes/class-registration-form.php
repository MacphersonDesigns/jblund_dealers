<?php
/**
 * Dealer Registration Form Handler
 *
 * Handles the dealer registration form shortcode, submission processing,
 * and creation of dealer_registration posts.
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
 * Registration Form Class
 *
 * Manages dealer registration form display and submission
 */
class Registration_Form {

    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode('jblund_dealer_registration', array($this, 'registration_form_shortcode'));
        add_action('init', array($this, 'process_registration_submission'));
    }

    /**
     * Registration form shortcode
     *
     * Usage: [jblund_dealer_registration]
     *
     * @param array $atts Shortcode attributes
     * @return string Form HTML
     */
    public function registration_form_shortcode($atts) {
        // Parse attributes
        $atts = shortcode_atts(array(
            'title' => __('Become a Dealer', 'jblund-dealers'),
            'subtitle' => __('Fill out the form below to apply for a dealer account', 'jblund-dealers'),
        ), $atts);

        // Start output buffering
        ob_start();

        // Load template
        $this->render_registration_form($atts);

        return ob_get_clean();
    }

    /**
     * Render the registration form
     *
     * @param array $atts Form attributes
     */
    private function render_registration_form($atts) {
        // Check for success message
        $success = isset($_GET['registration']) && $_GET['registration'] === 'success';
        $error = isset($_GET['registration']) && $_GET['registration'] === 'error';
        $error_message = isset($_GET['error_message']) ? sanitize_text_field($_GET['error_message']) : '';

        ?>
        <div class="jblund-dealer-registration">
            <div class="registration-container">

                <?php if ($success) : ?>
                    <!-- Success Message -->
                    <?php
                    // Get active message from scheduler (includes scheduled messages)
                    if (class_exists('JBLund\\Admin\\Message_Scheduler')) {
                        $scheduler = new \JBLund\Admin\Message_Scheduler();
                        $active_message = $scheduler->get_active_message();
                        $success_title = $active_message['title'];
                        $success_message = $active_message['message'];
                        $success_note = $active_message['note'];
                    } else {
                        // Fallback to old settings system
                        $reg_settings = get_option('jblund_dealers_registration_settings', array());
                        $success_title = isset($reg_settings['success_title']) ? $reg_settings['success_title'] : __('Application Submitted Successfully!', 'jblund-dealers');
                        $success_message = isset($reg_settings['success_message']) ? $reg_settings['success_message'] : __('Thank you for your interest in becoming a JBLund dealer. Your application has been received and is currently under review. One of our account representatives will contact you shortly to discuss your application and next steps.', 'jblund-dealers');
                        $success_note = isset($reg_settings['success_note']) ? $reg_settings['success_note'] : __('Please allow 2-3 business days for our team to review your submission.', 'jblund-dealers');
                    }
                    ?>
                    <div class="registration-success">
                        <div class="success-icon">
                            <span class="dashicons dashicons-yes-alt"></span>
                        </div>
                        <h2><?php echo esc_html($success_title); ?></h2>
                        <div class="success-content">
                            <?php echo wpautop(wp_kses_post($success_message)); ?>
                        </div>
                        <?php if (!empty($success_note)) : ?>
                            <p class="success-note"><?php echo esc_html($success_note); ?></p>
                        <?php endif; ?>
                        <div style="margin-top: 20px;">
                            <a href="<?php echo home_url('/'); ?>" class="button"><?php _e('Return to Home', 'jblund-dealers'); ?></a>
                        </div>
                    </div>
                <?php else : ?>
                    <!-- Registration Form -->
                    <div class="registration-header">
                        <h1><?php echo esc_html($atts['title']); ?></h1>
                        <p><?php echo esc_html($atts['subtitle']); ?></p>
                    </div>

                    <?php if ($error) : ?>
                        <div class="registration-error">
                            <strong><?php _e('Error:', 'jblund-dealers'); ?></strong>
                            <?php echo esc_html($error_message ?: __('There was a problem submitting your application. Please try again.', 'jblund-dealers')); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="" class="dealer-registration-form" id="dealer-registration-form">
                        <?php wp_nonce_field('jblund_dealer_registration', 'registration_nonce'); ?>
                        <input type="hidden" name="jblund_registration_action" value="submit" />

                        <div class="form-section">
                            <h3><?php _e('Representative Information', 'jblund-dealers'); ?></h3>

                            <div class="form-row">
                                <label for="rep_first_name">
                                    <?php _e('First Name', 'jblund-dealers'); ?>
                                    <span class="required">*</span>
                                </label>
                                <input type="text" id="rep_first_name" name="rep_first_name" required />
                            </div>

                            <div class="form-row">
                                <label for="rep_last_name">
                                    <?php _e('Last Name', 'jblund-dealers'); ?>
                                    <span class="required">*</span>
                                </label>
                                <input type="text" id="rep_last_name" name="rep_last_name" required />
                            </div>

                            <div class="form-row">
                                <label for="rep_email">
                                    <?php _e('Email Address', 'jblund-dealers'); ?>
                                    <span class="required">*</span>
                                </label>
                                <input type="email" id="rep_email" name="rep_email" required />
                            </div>

                            <div class="form-row">
                                <label for="rep_phone">
                                    <?php _e('Phone Number', 'jblund-dealers'); ?>
                                    <span class="required">*</span>
                                </label>
                                <input type="tel" id="rep_phone" name="rep_phone" required />
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><?php _e('Company Information', 'jblund-dealers'); ?></h3>

                            <div class="form-row">
                                <label for="company_name">
                                    <?php _e('Company Name', 'jblund-dealers'); ?>
                                    <span class="required">*</span>
                                </label>
                                <input type="text" id="company_name" name="company_name" required />
                            </div>

                            <div class="form-row">
                                <label for="company_phone">
                                    <?php _e('Company Phone', 'jblund-dealers'); ?>
                                </label>
                                <input type="tel" id="company_phone" name="company_phone" />
                            </div>

                            <div class="form-row">
                                <label for="territory">
                                    <?php _e('Territory/Location', 'jblund-dealers'); ?>
                                    <span class="required">*</span>
                                </label>
                                <input type="text" id="territory" name="territory" required placeholder="<?php esc_attr_e('e.g., Northern California, Pacific Northwest', 'jblund-dealers'); ?>" />
                            </div>

                            <div class="form-row">
                                <label for="company_address">
                                    <?php _e('Company Address', 'jblund-dealers'); ?>
                                </label>
                                <textarea id="company_address" name="company_address" rows="3" placeholder="<?php esc_attr_e('Street, City, State, ZIP', 'jblund-dealers'); ?>"></textarea>
                            </div>

                            <div class="form-row">
                                <label for="company_website">
                                    <?php _e('Company Website', 'jblund-dealers'); ?>
                                </label>
                                <input type="url" id="company_website" name="company_website" placeholder="<?php esc_attr_e('https://example.com', 'jblund-dealers'); ?>" />
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><?php _e('Products & Services', 'jblund-dealers'); ?></h3>
                            <p class="section-description"><?php _e('What products or services does your business offer?', 'jblund-dealers'); ?></p>

                            <div class="form-row checkbox-group">
                                <label>
                                    <input type="checkbox" id="docks" name="docks" value="1" />
                                    <?php _e('Docks', 'jblund-dealers'); ?>
                                </label>
                                <label>
                                    <input type="checkbox" id="lifts" name="lifts" value="1" />
                                    <?php _e('Lifts', 'jblund-dealers'); ?>
                                </label>
                                <label>
                                    <input type="checkbox" id="trailers" name="trailers" value="1" />
                                    <?php _e('Trailers', 'jblund-dealers'); ?>
                                </label>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><?php _e('Additional Information', 'jblund-dealers'); ?></h3>

                            <div class="form-row">
                                <label for="notes">
                                    <?php _e('Tell us about your business and why you\'d like to become a dealer', 'jblund-dealers'); ?>
                                </label>
                                <textarea id="notes" name="notes" rows="5"></textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="button-primary">
                                <?php _e('Submit Application', 'jblund-dealers'); ?>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <style>
        .jblund-dealer-registration {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }

        .registration-container {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .registration-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #003366;
        }

        .registration-header h1 {
            margin: 0 0 10px 0;
            color: #003366;
            font-size: 32px;
        }

        .registration-header p {
            margin: 0;
            color: #666;
            font-size: 16px;
        }

        .registration-success {
            text-align: center;
            padding: 40px 20px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon .dashicons {
            font-size: 48px;
            width: 48px;
            height: 48px;
            color: #fff;
        }

        .registration-success h2 {
            color: #28a745;
            margin: 0 0 20px 0;
            font-size: 28px;
        }

        .registration-success p {
            margin: 0 0 15px 0;
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }

        .success-note {
            margin-top: 30px !important;
            padding: 15px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            color: #155724;
        }

        .registration-error {
            margin: 0 0 30px 0;
            padding: 15px 20px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            color: #721c24;
        }

        .dealer-registration-form .form-section {
            margin-bottom: 35px;
        }

        .dealer-registration-form .form-section h3 {
            margin: 0 0 20px 0;
            color: #003366;
            font-size: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .dealer-registration-form .form-row {
            margin-bottom: 20px;
        }

        .dealer-registration-form label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .dealer-registration-form .required {
            color: #c00;
        }

        .dealer-registration-form input[type="text"],
        .dealer-registration-form input[type="email"],
        .dealer-registration-form input[type="tel"],
        .dealer-registration-form input[type="url"],
        .dealer-registration-form textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .dealer-registration-form input:focus,
        .dealer-registration-form textarea:focus {
            outline: none;
            border-color: #003366;
        }

        .dealer-registration-form textarea {
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            margin: 0;
            cursor: pointer;
            font-weight: normal;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
            cursor: pointer;
        }

        .section-description {
            margin: 0 0 15px 0;
            color: #666;
            font-size: 14px;
            font-style: italic;
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
            transition: background 0.3s ease;
        }

        .button-primary:hover {
            background: #002244;
        }

        @media (max-width: 768px) {
            .jblund-dealer-registration {
                padding: 10px;
            }

            .registration-container {
                padding: 20px;
            }

            .registration-header h1 {
                font-size: 24px;
            }
        }
        </style>
        <?php
    }

    /**
     * Process registration form submission
     */
    public function process_registration_submission() {
        // Check if form was submitted
        if (!isset($_POST['jblund_registration_action']) || $_POST['jblund_registration_action'] !== 'submit') {
            return;
        }

        // Verify nonce
        if (!isset($_POST['registration_nonce']) || !wp_verify_nonce($_POST['registration_nonce'], 'jblund_dealer_registration')) {
            $this->redirect_with_error(__('Security check failed. Please try again.', 'jblund-dealers'));
            return;
        }

        // Sanitize and validate required fields
        $rep_first_name = isset($_POST['rep_first_name']) ? sanitize_text_field($_POST['rep_first_name']) : '';
        $rep_last_name = isset($_POST['rep_last_name']) ? sanitize_text_field($_POST['rep_last_name']) : '';
        $rep_email = isset($_POST['rep_email']) ? sanitize_email($_POST['rep_email']) : '';
        $rep_phone = isset($_POST['rep_phone']) ? sanitize_text_field($_POST['rep_phone']) : '';
        $company_name = isset($_POST['company_name']) ? sanitize_text_field($_POST['company_name']) : '';
        $company_phone = isset($_POST['company_phone']) ? sanitize_text_field($_POST['company_phone']) : '';
        $territory = isset($_POST['territory']) ? sanitize_text_field($_POST['territory']) : '';
        $company_address = isset($_POST['company_address']) ? sanitize_textarea_field($_POST['company_address']) : '';
        $company_website = isset($_POST['company_website']) ? esc_url_raw($_POST['company_website']) : '';
        $docks = isset($_POST['docks']) ? '1' : '0';
        $lifts = isset($_POST['lifts']) ? '1' : '0';
        $trailers = isset($_POST['trailers']) ? '1' : '0';
        $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';

        // Validate required fields
        if (empty($rep_first_name) || empty($rep_last_name) || empty($rep_email) || empty($rep_phone) || empty($company_name) || empty($territory)) {
            $this->redirect_with_error(__('Please fill in all required fields.', 'jblund-dealers'));
            return;
        }

        // Validate email
        if (!is_email($rep_email)) {
            $this->redirect_with_error(__('Please provide a valid email address.', 'jblund-dealers'));
            return;
        }

        // Check for duplicate email
        $existing = get_posts(array(
            'post_type' => 'dealer_registration',
            'meta_query' => array(
                array(
                    'key' => '_registration_email',
                    'value' => $rep_email,
                    'compare' => '='
                )
            ),
            'post_status' => 'any',
            'posts_per_page' => 1
        ));

        if (!empty($existing)) {
            $this->redirect_with_error(__('An application with this email address has already been submitted.', 'jblund-dealers'));
            return;
        }

        // Create dealer_registration post
        $post_data = array(
            'post_type' => 'dealer_registration',
            'post_title' => $company_name,
            'post_status' => 'publish',
            'post_author' => 0, // System-created
        );

        $registration_id = wp_insert_post($post_data);

        if (is_wp_error($registration_id)) {
            $this->redirect_with_error(__('Failed to submit application. Please try again.', 'jblund-dealers'));
            return;
        }

        // Store registration meta data
        update_post_meta($registration_id, '_registration_rep_first_name', $rep_first_name);
        update_post_meta($registration_id, '_registration_rep_last_name', $rep_last_name);
        update_post_meta($registration_id, '_registration_rep_name', $rep_first_name . ' ' . $rep_last_name);
        update_post_meta($registration_id, '_registration_email', $rep_email);
        update_post_meta($registration_id, '_registration_phone', $rep_phone);
        update_post_meta($registration_id, '_registration_company', $company_name);
        update_post_meta($registration_id, '_registration_company_phone', $company_phone);
        update_post_meta($registration_id, '_registration_territory', $territory);
        update_post_meta($registration_id, '_registration_company_address', $company_address);
        update_post_meta($registration_id, '_registration_company_website', $company_website);
        update_post_meta($registration_id, '_registration_docks', $docks);
        update_post_meta($registration_id, '_registration_lifts', $lifts);
        update_post_meta($registration_id, '_registration_trailers', $trailers);
        update_post_meta($registration_id, '_registration_notes', $notes);
        update_post_meta($registration_id, '_registration_status', 'pending');
        update_post_meta($registration_id, '_registration_date', current_time('mysql'));
        update_post_meta($registration_id, '_registration_ip', $this->get_user_ip());
        update_post_meta($registration_id, '_registration_user_agent', sanitize_text_field($_SERVER['HTTP_USER_AGENT']));

        // Send notification email to admin
        $this->send_admin_notification($registration_id, $rep_first_name, $rep_last_name, $rep_email, $company_name, $territory);

        // Redirect to success page
        wp_redirect(add_query_arg('registration', 'success', wp_get_referer()));
        exit;
    }

    /**
     * Send admin notification email for new registration
     *
     * @param int $registration_id Registration post ID
     * @param string $first_name Rep first name
     * @param string $last_name Rep last name
     * @param string $email Rep email
     * @param string $company Company name
     * @param string $territory Territory
     */
    private function send_admin_notification($registration_id, $first_name, $last_name, $email, $company, $territory) {
        // Get custom HTML template or default
        $template = get_option('jblund_email_template_admin_notification');
        if (empty($template)) {
            // Load default HTML template from file
            $template_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'templates/emails/admin-notification-template.html';
            if (file_exists($template_path)) {
                $template = file_get_contents($template_path);
            } else {
                // Fallback to simple HTML if template file not found
                $template = '<!DOCTYPE html><html><body><p>A new dealer application has been submitted.</p><p>Representative: {{rep_name}}<br>Email: {{rep_email}}<br>Phone: {{rep_phone}}<br>Company: {{company}}<br>Phone: {{company_phone}}<br>Territory: {{territory}}<br>Address: {{company_address}}<br>Website: {{company_website}}<br>Services: Docks: {{docks}}, Lifts: {{lifts}}, Trailers: {{trailers}}<br>Notes: {{notes}}</p><p><a href="{{admin_url}}">Review Application</a></p></body></html>';
            }
        }

        // Get all registration data
        $rep_phone = get_post_meta($registration_id, '_registration_phone', true);
        $company_phone = get_post_meta($registration_id, '_company_phone', true);
        $company_address = get_post_meta($registration_id, '_company_address', true);
        $company_website = get_post_meta($registration_id, '_company_website', true);
        $docks = get_post_meta($registration_id, '_docks', true) === '1' ? __('Yes', 'jblund-dealers') : __('No', 'jblund-dealers');
        $lifts = get_post_meta($registration_id, '_lifts', true) === '1' ? __('Yes', 'jblund-dealers') : __('No', 'jblund-dealers');
        $trailers = get_post_meta($registration_id, '_trailers', true) === '1' ? __('Yes', 'jblund-dealers') : __('No', 'jblund-dealers');
        $notes = get_post_meta($registration_id, '_notes', true);
        $admin_url = admin_url('edit.php?post_type=dealer&page=dealer-registrations');

        // Build shortcode replacements array
        $replacements = array(
            '{{rep_name}}' => esc_html($first_name . ' ' . $last_name),
            '{{rep_email}}' => esc_html($email),
            '{{rep_phone}}' => esc_html($rep_phone),
            '{{company}}' => esc_html($company),
            '{{company_phone}}' => esc_html($company_phone),
            '{{territory}}' => esc_html($territory),
            '{{company_address}}' => esc_html($company_address),
            '{{company_website}}' => esc_url($company_website),
            '{{docks}}' => $docks,
            '{{lifts}}' => $lifts,
            '{{trailers}}' => $trailers,
            '{{notes}}' => esc_html($notes),
            '{{admin_url}}' => esc_url($admin_url)
        );

        // Replace all shortcodes in template
        $message = str_replace(array_keys($replacements), array_values($replacements), $template);

        // Apply brand color if set (default #ff0000)
        $brand_color = get_option('jblund_email_brand_color', '#FF0000');
        $message = str_replace('#ff0000', $brand_color, $message);

        // Send HTML email
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('[JBLund Dealers] New Dealer Application - %s', 'jblund-dealers'), $company);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($admin_email, $subject, $message, $headers);
    }

    /**
     * Redirect with error message
     *
     * @param string $message Error message
     */
    private function redirect_with_error($message) {
        wp_redirect(add_query_arg(array(
            'registration' => 'error',
            'error_message' => urlencode($message)
        ), wp_get_referer()));
        exit;
    }

    /**
     * Get user IP address
     *
     * @return string IP address
     */
    private function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
        } else {
            return sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }
    }
}
