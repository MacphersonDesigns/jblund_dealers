<?php
/**
 * Settings Page Renderer
 *
 * Handles rendering of all settings page tabs and UI
 *
 * @package JBLund_Dealers
 * @subpackage Admin
 */

namespace JBLund\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Settings_Page {

    /**
     * Render settings page with tabs
     */
    public function render() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        ?>
        <div class="wrap jblund-dealers-settings">
            <h1><?php _e('JBLund Dealers Settings', 'jblund-dealers'); ?></h1>

            <!-- Tab Navigation -->
            <h2 class="nav-tab-wrapper">
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=general"
                   class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('General', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=pages"
                   class="nav-tab <?php echo $active_tab === 'pages' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Portal Pages', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=appearance"
                   class="nav-tab <?php echo $active_tab === 'appearance' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Appearance', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=portal"
                   class="nav-tab <?php echo $active_tab === 'portal' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Portal Updates', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=documents"
                   class="nav-tab <?php echo $active_tab === 'documents' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Documents', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=nda"
                   class="nav-tab <?php echo $active_tab === 'nda' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('NDA Editor', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=registration"
                   class="nav-tab <?php echo $active_tab === 'registration' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Registration', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=emails"
                   class="nav-tab <?php echo $active_tab === 'emails' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Email Templates', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=import-export"
                   class="nav-tab <?php echo $active_tab === 'import-export' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Import/Export', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=help"
                   class="nav-tab <?php echo $active_tab === 'help' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Help & Guide', 'jblund-dealers'); ?>
                </a>
            </h2>

            <!-- Tab Content -->
            <div class="tab-content">
                <?php
                switch ($active_tab) {
                    case 'general':
                        $this->render_general_tab();
                        break;
                    case 'pages':
                        $this->render_pages_tab();
                        break;
                    case 'appearance':
                        $this->render_appearance_tab();
                        break;
                    case 'portal':
                        $this->render_portal_tab();
                        break;
                    case 'documents':
                        $this->render_documents_tab();
                        break;
                    case 'nda':
                        $this->render_nda_tab();
                        break;
                    case 'registration':
                        $this->render_registration_tab();
                        break;
                    case 'emails':
                        $this->render_emails_tab();
                        break;
                    case 'import-export':
                        $this->render_import_export_tab();
                        break;
                    case 'help':
                        $this->render_help_tab();
                        break;
                }
                ?>
            </div>
        </div>

        <?php $this->render_styles(); ?>
        <?php
    }

    /**
     * Render common styles
     */
    private function render_styles() {
        ?>
        <style>
            .jblund-dealers-settings .tab-content {
                background: #fff;
                padding: 20px;
                border: 1px solid #ccd0d4;
                border-top: none;
                margin-top: -1px;
            }
            .jblund-dealers-settings .settings-section {
                margin-bottom: 30px;
            }
            .jblund-dealers-settings .settings-section h3 {
                margin-top: 0;
                padding-bottom: 10px;
                border-bottom: 1px solid #ddd;
            }
            .jblund-dealers-settings .help-section {
                margin-bottom: 30px;
                padding: 20px;
                background: #f9f9f9;
                border-left: 4px solid #0073aa;
            }
            .jblund-dealers-settings .help-section h3 {
                margin-top: 0;
                color: #0073aa;
            }
            .jblund-dealers-settings .step-list {
                counter-reset: step-counter;
                list-style: none;
                padding-left: 0;
            }
            .jblund-dealers-settings .step-list li {
                counter-increment: step-counter;
                margin-bottom: 15px;
                padding-left: 35px;
                position: relative;
            }
            .jblund-dealers-settings .step-list li:before {
                content: counter(step-counter);
                position: absolute;
                left: 0;
                top: 0;
                background: #0073aa;
                color: #fff;
                width: 25px;
                height: 25px;
                border-radius: 50%;
                text-align: center;
                line-height: 25px;
                font-weight: bold;
                font-size: 14px;
            }
            .jblund-dealers-settings .feature-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }
            .jblund-dealers-settings .feature-card {
                padding: 15px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .jblund-dealers-settings .feature-card h4 {
                margin-top: 0;
                color: #0073aa;
            }
        </style>
        <?php
    }

    /**
     * Render General tab
     */
    private function render_general_tab() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('jblund_dealers_settings'); ?>
            <div class="settings-section">
                <h3><?php _e('Shortcode Settings', 'jblund-dealers'); ?></h3>
                <p><?php _e('Configure default shortcode behavior and layout options.', 'jblund-dealers'); ?></p>
                <table class="form-table">
                    <?php do_settings_fields('jblund_dealers_settings', 'jblund_dealers_shortcode'); ?>
                </table>
            </div>
            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * Render Pages tab
     */
    private function render_pages_tab() {
        $portal_pages = get_option('jblund_dealers_portal_pages', array());
        $login_page = isset($portal_pages['login']) ? $portal_pages['login'] : '';
        $dashboard_page = isset($portal_pages['dashboard']) ? $portal_pages['dashboard'] : '';
        $profile_page = isset($portal_pages['profile']) ? $portal_pages['profile'] : '';
        $nda_page = isset($portal_pages['nda']) ? $portal_pages['nda'] : '';

        $pages = get_pages(array('sort_column' => 'post_title'));
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('jblund_dealers_portal_pages'); ?>

            <div class="settings-section">
                <h3><?php _e('Dealer Portal Page Assignment', 'jblund-dealers'); ?></h3>
                <p><?php _e('Assign pages for your dealer portal. Create pages with Divi (or any page builder) and add the shortcodes below, then assign them here.', 'jblund-dealers'); ?></p>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="login_page"><?php _e('Login Page', 'jblund-dealers'); ?></label></th>
                        <td>
                            <select name="jblund_dealers_portal_pages[login]" id="login_page" class="regular-text">
                                <option value=""><?php _e('— Select Page —', 'jblund-dealers'); ?></option>
                                <?php foreach ($pages as $page): ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($login_page, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <?php _e('Add this shortcode to your login page:', 'jblund-dealers'); ?>
                                <code>[jblund_dealer_login]</code>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="dashboard_page"><?php _e('Dashboard Page', 'jblund-dealers'); ?></label></th>
                        <td>
                            <select name="jblund_dealers_portal_pages[dashboard]" id="dashboard_page" class="regular-text">
                                <option value=""><?php _e('— Select Page —', 'jblund-dealers'); ?></option>
                                <?php foreach ($pages as $page): ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($dashboard_page, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <?php _e('Add this shortcode to your dashboard page:', 'jblund-dealers'); ?>
                                <code>[jblund_dealer_dashboard]</code>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="profile_page"><?php _e('Profile Page', 'jblund-dealers'); ?></label></th>
                        <td>
                            <select name="jblund_dealers_portal_pages[profile]" id="profile_page" class="regular-text">
                                <option value=""><?php _e('— Select Page —', 'jblund-dealers'); ?></option>
                                <?php foreach ($pages as $page): ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($profile_page, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <?php _e('Add this shortcode to your profile page:', 'jblund-dealers'); ?>
                                <code>[jblund_dealer_profile]</code>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="nda_page"><?php _e('NDA Acceptance Page', 'jblund-dealers'); ?></label></th>
                        <td>
                            <select name="jblund_dealers_portal_pages[nda]" id="nda_page" class="regular-text">
                                <option value=""><?php _e('— Select Page —', 'jblund-dealers'); ?></option>
                                <?php foreach ($pages as $page): ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($nda_page, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                <?php _e('Add this shortcode to your NDA page:', 'jblund-dealers'); ?>
                                <code>[jblund_nda_acceptance]</code>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="settings-section" style="background: #f9f9f9; padding: 20px; border-left: 4px solid #0073aa; margin-top: 30px;">
                <h3><?php _e('📋 Setup Instructions', 'jblund-dealers'); ?></h3>
                <ol style="line-height: 2;">
                    <li><?php _e('Create new pages in WordPress (you can use Divi Builder or any page builder)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Add the appropriate shortcode to each page (shown above)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Design the page around the shortcode using your page builder', 'jblund-dealers'); ?></li>
                    <li><?php _e('Assign the pages using the dropdowns above', 'jblund-dealers'); ?></li>
                    <li><?php _e('Save settings - the plugin will use these pages for redirects and navigation', 'jblund-dealers'); ?></li>
                </ol>
            </div>

            <div class="settings-section" style="background: #fff3cd; padding: 20px; border-left: 4px solid #ffc107; margin-top: 20px;">
                <h3><?php _e('📝 Available Shortcodes', 'jblund-dealers'); ?></h3>
                <table class="widefat" style="margin-top: 10px;">
                    <thead>
                        <tr>
                            <th><?php _e('Shortcode', 'jblund-dealers'); ?></th>
                            <th><?php _e('Description', 'jblund-dealers'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>[jblund_dealer_login]</code></td>
                            <td><?php _e('Displays the dealer login form with registration link', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[jblund_dealer_dashboard]</code></td>
                            <td><?php _e('Displays the dealer dashboard with welcome message and quick links', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[jblund_dealer_profile]</code></td>
                            <td><?php _e('Displays the dealer profile editor where dealers can update their information', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[jblund_nda_acceptance]</code></td>
                            <td><?php _e('Displays the NDA acceptance form (automatically shown to new dealers)', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td><code>[jblund_dealers]</code></td>
                            <td><?php _e('Displays the public dealer directory (use on any public page)', 'jblund-dealers'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php submit_button(__('Save Page Assignments', 'jblund-dealers')); ?>
        </form>
        <?php
    }

    /**
     * Render Appearance tab
     * Uses field renderer from main plugin class for backward compatibility
     */
    private function render_appearance_tab() {
        // Get field renderer instance
        $field_renderer = \JBLund\Admin\Field_Renderer::get_instance();

        ?>
        <form method="post" action="options.php">
            <?php settings_fields('jblund_dealers_settings'); ?>

            <div class="appearance-customization-wrapper">
                <div class="settings-section">
                    <h3><?php _e('Theme Integration', 'jblund-dealers'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="inherit_theme_styles"><?php _e('Inherit Theme Styles', 'jblund-dealers'); ?></label></th>
                            <td>
                                <?php
                                $options = get_option('jblund_dealers_settings');
                                $inherit = isset($options['inherit_theme_styles']) ? $options['inherit_theme_styles'] : '0';
                                ?>
                                <label>
                                    <input type="checkbox"
                                           name="jblund_dealers_settings[inherit_theme_styles]"
                                           id="inherit_theme_styles"
                                           value="1"
                                           <?php checked($inherit, '1'); ?> />
                                    <?php _e('Use theme colors, fonts, and styling instead of custom styles', 'jblund-dealers'); ?>
                                </label>
                                <p class="description">
                                    <?php _e('When enabled, dealer cards will inherit your theme\'s primary colors, typography, and button styles. Custom settings below will be ignored.', 'jblund-dealers'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="jblund-appearance-sections">
                    <!-- Colors Section -->
                    <div class="jblund-section-wrapper">
                        <h3 class="jblund-section-title" data-section="colors">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Colors', 'jblund-dealers'); ?>
                            <span class="section-badge"><?php _e('10 Options', 'jblund-dealers'); ?></span>
                        </h3>
                        <div class="jblund-section-content" id="section-colors">
                            <table class="form-table">
                                <tr><th scope="row"><label><?php _e('Card Header Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->color_field(array('field' => 'header_color', 'default' => '#0073aa')); ?></td></tr>
                                <tr><th scope="row"><label><?php _e('Card Background Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->color_field(array('field' => 'card_background', 'default' => '#ffffff')); ?></td></tr>
                                <tr><th scope="row"><label><?php _e('Button Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->color_field(array('field' => 'button_color', 'default' => '#0073aa')); ?></td></tr>
                                <tr><th scope="row"><label><?php _e('Primary Text Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->color_field(array('field' => 'text_color', 'default' => '#333333')); ?></td></tr>
                                <tr><th scope="row"><label><?php _e('Secondary Text Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->color_field(array('field' => 'secondary_text_color', 'default' => '#666666')); ?>
                                    <p class="description"><?php _e('Used for addresses, phone numbers, and other secondary info', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Border Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->color_field(array('field' => 'border_color', 'default' => '#dddddd')); ?></td></tr>
                                <tr><th scope="row"><label><?php _e('Button Text Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->color_field(array('field' => 'button_text_color', 'default' => '#ffffff')); ?></td></tr>
                                <tr><th scope="row"><label><?php _e('Icon Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->color_field(array('field' => 'icon_color', 'default' => '#0073aa')); ?>
                                    <p class="description"><?php _e('Color for service icons (docks, lifts, trailers)', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Link Color', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->color_field(array('field' => 'link_color', 'default' => '#0073aa')); ?></td></tr>
                                <tr><th scope="row"><label><?php _e('Card Hover Background', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->color_field(array('field' => 'hover_background', 'default' => '#f9f9f9')); ?>
                                    <p class="description"><?php _e('Background color when hovering over cards', 'jblund-dealers'); ?></p></td></tr>
                            </table>
                        </div>
                    </div>

                    <!-- Typography Section -->
                    <div class="jblund-section-wrapper">
                        <h3 class="jblund-section-title" data-section="typography">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Typography', 'jblund-dealers'); ?>
                            <span class="section-badge"><?php _e('4 Options', 'jblund-dealers'); ?></span>
                        </h3>
                        <div class="jblund-section-content" id="section-typography">
                            <table class="form-table">
                                <tr><th scope="row"><label><?php _e('Heading Font Size', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->range_field(array('field' => 'heading_font_size', 'default' => '24', 'min' => '16', 'max' => '32', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Dealer name/company name size', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Body Font Size', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->range_field(array('field' => 'body_font_size', 'default' => '14', 'min' => '12', 'max' => '18', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Address, phone, and contact info size', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Heading Font Weight', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->select_field(array(
                                        'field' => 'heading_font_weight',
                                        'options' => array(
                                            'normal' => __('Normal', 'jblund-dealers'),
                                            '600' => __('Semi-Bold (600)', 'jblund-dealers'),
                                            'bold' => __('Bold (700)', 'jblund-dealers'),
                                            '800' => __('Extra Bold (800)', 'jblund-dealers')
                                        ),
                                        'default' => 'bold'
                                    )); ?></td></tr>
                                <tr><th scope="row"><label><?php _e('Line Height', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->range_field(array('field' => 'line_height', 'default' => '1.6', 'min' => '1.2', 'max' => '2.0', 'step' => '0.1', 'unit' => '')); ?>
                                    <p class="description"><?php _e('Text line spacing', 'jblund-dealers'); ?></p></td></tr>
                            </table>
                        </div>
                    </div>

                    <!-- Spacing Section -->
                    <div class="jblund-section-wrapper">
                        <h3 class="jblund-section-title" data-section="spacing">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Spacing & Layout', 'jblund-dealers'); ?>
                            <span class="section-badge"><?php _e('6 Options', 'jblund-dealers'); ?></span>
                        </h3>
                        <div class="jblund-section-content" id="section-spacing">
                            <table class="form-table">
                                <tr><th scope="row"><label><?php _e('Card Padding', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->range_field(array('field' => 'card_padding', 'default' => '20', 'min' => '10', 'max' => '40', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Internal spacing inside dealer cards', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Card Margin', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->range_field(array('field' => 'card_margin', 'default' => '15', 'min' => '10', 'max' => '30', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Spacing around each card', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Grid Gap', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->range_field(array('field' => 'grid_gap', 'default' => '20', 'min' => '15', 'max' => '50', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Space between cards in grid layout', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Border Radius', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->range_field(array('field' => 'border_radius', 'default' => '8', 'min' => '0', 'max' => '20', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Rounded corners (0 = square)', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Border Width', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->range_field(array('field' => 'border_width', 'default' => '1', 'min' => '0', 'max' => '5', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Card border thickness (0 = no border)', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Border Style', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->select_field(array(
                                        'field' => 'border_style',
                                        'options' => array(
                                            'solid' => __('Solid', 'jblund-dealers'),
                                            'dashed' => __('Dashed', 'jblund-dealers'),
                                            'dotted' => __('Dotted', 'jblund-dealers'),
                                            'none' => __('None', 'jblund-dealers')
                                        ),
                                        'default' => 'solid'
                                    )); ?></td></tr>
                            </table>
                        </div>
                    </div>

                    <!-- Effects Section -->
                    <div class="jblund-section-wrapper">
                        <h3 class="jblund-section-title" data-section="effects">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Visual Effects', 'jblund-dealers'); ?>
                            <span class="section-badge"><?php _e('4 Options', 'jblund-dealers'); ?></span>
                        </h3>
                        <div class="jblund-section-content" id="section-effects">
                            <table class="form-table">
                                <tr><th scope="row"><label><?php _e('Box Shadow', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->select_field(array(
                                        'field' => 'box_shadow',
                                        'options' => array(
                                            'none' => __('None', 'jblund-dealers'),
                                            'light' => __('Light', 'jblund-dealers'),
                                            'medium' => __('Medium', 'jblund-dealers'),
                                            'heavy' => __('Heavy', 'jblund-dealers')
                                        ),
                                        'default' => 'light'
                                    )); ?>
                                    <p class="description"><?php _e('Card elevation/depth effect', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Hover Effect', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->select_field(array(
                                        'field' => 'hover_effect',
                                        'options' => array(
                                            'none' => __('None', 'jblund-dealers'),
                                            'lift' => __('Lift Up', 'jblund-dealers'),
                                            'scale' => __('Scale', 'jblund-dealers'),
                                            'shadow' => __('Shadow Increase', 'jblund-dealers')
                                        ),
                                        'default' => 'lift'
                                    )); ?>
                                    <p class="description"><?php _e('Animation when hovering over cards', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Transition Speed', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->range_field(array('field' => 'transition_speed', 'default' => '0.3', 'min' => '0.1', 'max' => '0.5', 'step' => '0.1', 'unit' => 's')); ?>
                                    <p class="description"><?php _e('Animation speed for hover effects', 'jblund-dealers'); ?></p></td></tr>
                                <tr><th scope="row"><label><?php _e('Icon Size', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->range_field(array('field' => 'icon_size', 'default' => '24', 'min' => '16', 'max' => '32', 'unit' => 'px')); ?>
                                    <p class="description"><?php _e('Service icon size (docks, lifts, trailers)', 'jblund-dealers'); ?></p></td></tr>
                            </table>
                        </div>
                    </div>

                    <!-- Custom CSS Section -->
                    <div class="jblund-section-wrapper">
                        <h3 class="jblund-section-title" data-section="custom-css">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                            <?php _e('Custom CSS', 'jblund-dealers'); ?>
                            <span class="section-badge"><?php _e('Advanced', 'jblund-dealers'); ?></span>
                        </h3>
                        <div class="jblund-section-content" id="section-custom-css">
                            <table class="form-table">
                                <tr><th scope="row"><label><?php _e('Custom CSS', 'jblund-dealers'); ?></label></th>
                                    <td><?php $field_renderer->textarea_field(array('field' => 'custom_css', 'default' => '')); ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <?php submit_button(); ?>
        </form>

        <script>
        jQuery(document).ready(function($) {
            $('.jblund-section-title').on('click', function() {
                var $title = $(this);
                var $content = $title.next('.jblund-section-content');
                var $icon = $title.find('.dashicons');

                $content.slideToggle(300);
                $icon.toggleClass('dashicons-arrow-right-alt2 dashicons-arrow-down-alt2');
                $title.toggleClass('active');
            });

            $('.jblund-section-title:first').trigger('click');
        });
        </script>

        <style>
        .appearance-customization-wrapper {max-width: 1200px;}
        .jblund-appearance-sections {margin-top: 20px;}
        .jblund-section-wrapper {background: #fff; border: 1px solid #ccd0d4; margin-bottom: 10px; border-radius: 4px;}
        .jblund-section-title {
            margin: 0; padding: 15px 20px; cursor: pointer; user-select: none;
            background: #f6f7f7; border-bottom: 1px solid #ccd0d4; transition: background 0.2s;
            display: flex; align-items: center; gap: 10px;
        }
        .jblund-section-title:hover {background: #e8eaeb;}
        .jblund-section-title.active {background: #fff;}
        .jblund-section-title .dashicons {transition: transform 0.2s; font-size: 20px; width: 20px; height: 20px;}
        .jblund-section-title .section-badge {
            margin-left: auto; background: #2271b1; color: #fff; padding: 2px 10px;
            border-radius: 3px; font-size: 11px; font-weight: 600; text-transform: uppercase;
        }
        .jblund-section-content {display: none; padding: 20px;}
        .range-field-wrapper {display: flex; align-items: center; gap: 15px;}
        .jblund-range-slider {flex: 1; max-width: 300px;}
        .range-value {min-width: 60px; font-weight: 600; color: #2271b1;}
        </style>
        <?php
    }

    /**
     * Render Portal Updates tab
     */
    private function render_portal_tab() {
        $portal_fields = \JBLund\Admin\Portal_Fields::get_instance();
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('jblund_dealers_settings'); ?>
            <div class="settings-section">
                <?php $portal_fields->portal_updates_section(); ?>
                <?php $portal_fields->portal_updates_field(); ?>
            </div>
            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * Render Representative tab
     */
    private function render_representative_tab() {
        $portal_fields = \JBLund\Admin\Portal_Fields::get_instance();
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('jblund_dealers_settings'); ?>
            <div class="settings-section">
                <?php $portal_fields->representative_section(); ?>
                <table class="form-table" role="presentation">
                    <?php do_settings_fields('jblund_dealers_settings', 'jblund_dealers_representative'); ?>
                </table>
            </div>
            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * Render Documents tab
     */
    private function render_documents_tab() {
        // Enqueue WordPress media library for file uploads
        wp_enqueue_media();

        $portal_fields = \JBLund\Admin\Portal_Fields::get_instance();
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('jblund_dealers_settings'); ?>
            <div class="settings-section">
                <?php $portal_fields->required_documents_section(); ?>
                <?php $portal_fields->required_documents_field(); ?>
            </div>
            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * Render NDA Editor tab
     */
    private function render_nda_tab() {
        if (class_exists('JBLund\DealerPortal\NDA_Editor')) {
            $nda_editor = new \JBLund\DealerPortal\NDA_Editor();
            $nda_editor->render_settings_page();
        } else {
            ?>
            <div class="notice notice-warning">
                <p><?php _e('NDA Editor is not available. The dealer portal module may not be loaded.', 'jblund-dealers'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Render Registration tab
     */
    private function render_registration_tab() {
        // Use the Message Scheduler for advanced message management
        if (class_exists('JBLund\\Admin\\Message_Scheduler')) {
            $scheduler = new \JBLund\Admin\Message_Scheduler();
            $scheduler->render_scheduler_interface();
        } else {
            ?>
            <div class="notice notice-error">
                <p><?php _e('Message Scheduler is not available. Please check that all plugin files are loaded correctly.', 'jblund-dealers'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Render Import/Export tab (original)
     */
    private function render_import_export_tab() {
        $csv_handler = \JBLund\Admin\CSV_Handler::get_instance();

        // Check if we need to show column mapping interface
        if (isset($_POST['action']) && $_POST['action'] === 'upload_csv' &&
            isset($_POST['jblund_dealers_upload_nonce']) &&
            wp_verify_nonce($_POST['jblund_dealers_upload_nonce'], 'jblund_dealers_upload')) {
            $csv_handler->render_column_mapping_interface();
        } else {
            ?>
            <div class="settings-section">
                <h3><?php _e('CSV Import/Export', 'jblund-dealers'); ?></h3>
                <p><?php _e('Import or export all dealer data for bulk management and server migration.', 'jblund-dealers'); ?></p>
                <?php $csv_handler->render_operations(); ?>
            </div>
            <?php
        }
    }

    /**
     * Render Help & Guide tab
     */
    private function render_help_tab() {
        ?>
        <div class="help-section">
            <h3><?php _e('Getting Started', 'jblund-dealers'); ?></h3>
            <p><?php _e('Welcome to JBLund Dealers! This plugin provides a complete dealer management system with public listings and a private dealer portal.', 'jblund-dealers'); ?></p>
        </div>

        <!-- SHORTCODES SECTION -->
        <div class="settings-section" style="background: #f0f7ff; padding: 20px; border-left: 4px solid #0073aa; margin: 20px 0;">
            <h2 style="margin-top: 0; color: #0073aa;"><?php _e('📋 Available Shortcodes', 'jblund-dealers'); ?></h2>
            <p><?php _e('Use these shortcodes to add functionality to your pages:', 'jblund-dealers'); ?></p>

            <!-- Public Dealer Directory Shortcode -->
            <div style="background: white; padding: 15px; margin: 15px 0; border-radius: 5px;">
                <h3 style="margin-top: 0;"><?php _e('🏢 Public Dealer Directory', 'jblund-dealers'); ?></h3>
                <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa; margin: 10px 0;">
                    <code style="font-size: 14px; font-weight: bold;">[jblund_dealers]</code>
                </div>
                <p><strong><?php _e('Description:', 'jblund-dealers'); ?></strong> <?php _e('Displays a grid or list of all dealer locations with company info, contact details, and services offered.', 'jblund-dealers'); ?></p>

                <p><strong><?php _e('Parameters:', 'jblund-dealers'); ?></strong></p>
                <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                    <thead>
                        <tr style="background: #f0f0f0;">
                            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;"><?php _e('Parameter', 'jblund-dealers'); ?></th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;"><?php _e('Options', 'jblund-dealers'); ?></th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;"><?php _e('Default', 'jblund-dealers'); ?></th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;"><?php _e('Description', 'jblund-dealers'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;"><code>layout</code></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">grid, list, compact</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">grid</td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php _e('Display layout style', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;"><code>posts_per_page</code></td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php _e('Any number', 'jblund-dealers'); ?></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">-1 (all)</td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php _e('Number of dealers to show', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;"><code>orderby</code></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">title, date, menu_order</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">title</td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php _e('Sort order', 'jblund-dealers'); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;"><code>order</code></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">ASC, DESC</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">ASC</td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php _e('Sort direction', 'jblund-dealers'); ?></td>
                        </tr>
                    </tbody>
                </table>

                <p><strong><?php _e('Examples:', 'jblund-dealers'); ?></strong></p>
                <div style="background: #f9f9f9; padding: 10px; margin: 5px 0; font-family: monospace;">
                    <div style="margin: 5px 0;"><code>[jblund_dealers]</code> <span style="color: #666;">← <?php _e('Default grid view, all dealers', 'jblund-dealers'); ?></span></div>
                    <div style="margin: 5px 0;"><code>[jblund_dealers layout="list"]</code> <span style="color: #666;">← <?php _e('Horizontal list layout', 'jblund-dealers'); ?></span></div>
                    <div style="margin: 5px 0;"><code>[jblund_dealers layout="compact" posts_per_page="6"]</code> <span style="color: #666;">← <?php _e('Compact grid, 6 dealers', 'jblund-dealers'); ?></span></div>
                    <div style="margin: 5px 0;"><code>[jblund_dealers orderby="date" order="DESC"]</code> <span style="color: #666;">← <?php _e('Newest dealers first', 'jblund-dealers'); ?></span></div>
                </div>

                <p><strong><?php _e('Where to Use:', 'jblund-dealers'); ?></strong> <?php _e('Add this to any page, post, or widget to display your dealer directory.', 'jblund-dealers'); ?></p>
            </div>

            <!-- Dealer Registration Form -->
            <div style="background: white; padding: 15px; margin: 15px 0; border-radius: 5px;">
                <h3 style="margin-top: 0;"><?php _e('📝 Dealer Registration Form', 'jblund-dealers'); ?></h3>
                <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa; margin: 10px 0;">
                    <code style="font-size: 14px; font-weight: bold;">[jblund_dealer_registration]</code>
                </div>
                <p><strong><?php _e('Description:', 'jblund-dealers'); ?></strong> <?php _e('Displays a registration form for potential dealers to apply for portal access. Admin approval required.', 'jblund-dealers'); ?></p>

                <p><strong><?php _e('Form Fields:', 'jblund-dealers'); ?></strong></p>
                <ul style="list-style: disc; padding-left: 20px;">
                    <li><?php _e('Representative first & last name (required)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Email address (required)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Phone number (required)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Company name (required)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Company phone (optional)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Territory/location (required)', 'jblund-dealers'); ?></li>
                    <li><?php _e('Business description notes (optional)', 'jblund-dealers'); ?></li>
                </ul>

                <p><strong><?php _e('Workflow:', 'jblund-dealers'); ?></strong></p>
                <ol style="padding-left: 20px;">
                    <li><?php _e('Dealer submits registration form', 'jblund-dealers'); ?></li>
                    <li><?php _e('Admin receives email notification', 'jblund-dealers'); ?></li>
                    <li><?php _e('Admin reviews in Dealers > Registrations', 'jblund-dealers'); ?></li>
                    <li><?php _e('Admin approves → Username auto-generated → Welcome email sent', 'jblund-dealers'); ?></li>
                    <li><?php _e('Dealer receives login credentials', 'jblund-dealers'); ?></li>
                </ul>

                <p><strong><?php _e('Where to Use:', 'jblund-dealers'); ?></strong> <?php _e('Create a page titled "Become a Dealer" or "Apply for Dealer Access" and add this shortcode. Link to it from your main navigation.', 'jblund-dealers'); ?></p>
            </div>

            <!-- Dealer Portal Shortcodes -->
            <div style="background: white; padding: 15px; margin: 15px 0; border-radius: 5px;">
                <h3 style="margin-top: 0;"><?php _e('🔐 Dealer Portal Pages (Private)', 'jblund-dealers'); ?></h3>
                <p><?php _e('These shortcodes are for dealer-only pages. They require login and show different content based on user role.', 'jblund-dealers'); ?></p>

                <!-- Dashboard -->
                <div style="margin: 15px 0; padding-left: 15px; border-left: 3px solid #ccc;">
                    <h4><?php _e('Dealer Dashboard', 'jblund-dealers'); ?></h4>
                    <div style="background: #f9f9f9; padding: 10px; margin: 10px 0;">
                        <code style="font-size: 14px; font-weight: bold;">[dealer_dashboard]</code>
                    </div>
                    <p><?php _e('Displays personalized dashboard with company info, recent updates, and quick links. Only visible to logged-in dealers.', 'jblund-dealers'); ?></p>
                </div>

                <!-- Profile Editor -->
                <div style="margin: 15px 0; padding-left: 15px; border-left: 3px solid #ccc;">
                    <h4><?php _e('Dealer Profile Editor', 'jblund-dealers'); ?></h4>
                    <div style="background: #f9f9f9; padding: 10px; margin: 10px 0;">
                        <code style="font-size: 14px; font-weight: bold;">[dealer_profile]</code>
                    </div>
                    <p><?php _e('Allows dealers to edit their company information, contact details, and services offered.', 'jblund-dealers'); ?></p>
                </div>

                <!-- Login Form -->
                <div style="margin: 15px 0; padding-left: 15px; border-left: 3px solid #ccc;">
                    <h4><?php _e('Dealer Login Form', 'jblund-dealers'); ?></h4>
                    <div style="background: #f9f9f9; padding: 10px; margin: 10px 0;">
                        <code style="font-size: 14px; font-weight: bold;">[dealer_login]</code>
                    </div>
                    <p><?php _e('Custom login form for dealers. Redirects to dashboard after successful login.', 'jblund-dealers'); ?></p>
                </div>

                <!-- Password Change -->
                <div style="margin: 15px 0; padding-left: 15px; border-left: 3px solid #ccc;">
                    <h4><?php _e('Force Password Change', 'jblund-dealers'); ?></h4>
                    <div style="background: #f9f9f9; padding: 10px; margin: 10px 0;">
                        <code style="font-size: 14px; font-weight: bold;">[jblund_force_password_change]</code>
                    </div>
                    <p><?php _e('Forces new dealers to change their temporary password on first login. Required before portal access.', 'jblund-dealers'); ?></p>
                </div>

                <!-- NDA Acceptance -->
                <div style="margin: 15px 0; padding-left: 15px; border-left: 3px solid #ccc;">
                    <h4><?php _e('NDA Acceptance Page', 'jblund-dealers'); ?></h4>
                    <div style="background: #f9f9f9; padding: 10px; margin: 10px 0;">
                        <code style="font-size: 14px; font-weight: bold;">[jblund_nda_acceptance]</code>
                    </div>
                    <p><?php _e('Displays NDA with signature capture. Dealers must accept before accessing any portal content. Generates PDF copy.', 'jblund-dealers'); ?></p>
                </div>

                <p style="margin-top: 20px;"><strong><?php _e('Auto-Created Pages:', 'jblund-dealers'); ?></strong> <?php _e('These pages are automatically created on plugin activation and configured in the Portal Pages tab.', 'jblund-dealers'); ?></p>
            </div>

            <!-- Usage Tips -->
            <div style="background: #e7f3e7; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #46b450;">
                <h3 style="margin-top: 0; color: #1e4620;"><?php _e('💡 Shortcode Usage Tips', 'jblund-dealers'); ?></h3>
                <ul style="list-style: disc; padding-left: 20px;">
                    <li><?php _e('<strong>In Pages:</strong> Edit any page and add the shortcode in a text block or shortcode block', 'jblund-dealers'); ?></li>
                    <li><?php _e('<strong>In Posts:</strong> Works the same as pages - add to any post content', 'jblund-dealers'); ?></li>
                    <li><?php _e('<strong>In Widgets:</strong> Use a text widget and add the shortcode', 'jblund-dealers'); ?></li>
                    <li><?php _e('<strong>In Templates:</strong> Use <code>echo do_shortcode(\'[shortcode_name]\');</code> in PHP files', 'jblund-dealers'); ?></li>
                    <li><?php _e('<strong>With Divi:</strong> Use a Code Module and paste the shortcode', 'jblund-dealers'); ?></li>
                    <li><?php _e('<strong>With Elementor:</strong> Use a Shortcode Widget and paste the shortcode', 'jblund-dealers'); ?></li>
                </ul>
            </div>
        </div>

        <div class="settings-section">
            <h3><?php _e('Adding Dealer Locations (Public Listings)', 'jblund-dealers'); ?></h3>
            <ol class="step-list">
                <li><strong><?php _e('Navigate to Dealers', 'jblund-dealers'); ?></strong><br><?php _e('In your WordPress admin, go to Dealers > Add New', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Enter Basic Information', 'jblund-dealers'); ?></strong><br><?php _e('Add dealer company name, address, phone, website, and services offered', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Add Sub-Locations', 'jblund-dealers'); ?></strong><br><?php _e('Optionally add multiple sub-locations for dealers with multiple branches', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Add GPS Coordinates', 'jblund-dealers'); ?></strong><br><?php _e('Enter latitude/longitude for map integration (optional)', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Publish', 'jblund-dealers'); ?></strong><br><?php _e('Click Publish to make the dealer visible in your directory', 'jblund-dealers'); ?></li>
            </ol>
        </div>

        <div class="settings-section">
            <h3><?php _e('Automated Dealer Registration & Onboarding', 'jblund-dealers'); ?></h3>
            <p><?php _e('The plugin includes a complete automated registration workflow:', 'jblund-dealers'); ?></p>
            <ol class="step-list">
                <li><strong><?php _e('Dealer Applies', 'jblund-dealers'); ?></strong><br><?php _e('Potential dealer fills out registration form on your site', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Admin Reviews', 'jblund-dealers'); ?></strong><br><?php _e('Admin receives email and reviews application in Dealers > Registrations', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Auto-Approval', 'jblund-dealers'); ?></strong><br><?php _e('Username generated (e.g., "amacpherson"), user account created, welcome email sent', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('First Login', 'jblund-dealers'); ?></strong><br><?php _e('Dealer logs in and is forced to change temporary password', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('NDA Acceptance', 'jblund-dealers'); ?></strong><br><?php _e('Dealer must accept NDA with digital signature before portal access', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Full Access', 'jblund-dealers'); ?></strong><br><?php _e('After NDA, dealer can access dashboard, profile editor, and all portal features', 'jblund-dealers'); ?></li>
            </ol>
        </div>

        <div class="settings-section">
            <h3><?php _e('Dealer Portal Features', 'jblund-dealers'); ?></h3>
            <div class="feature-grid">
                <div class="feature-card">
                    <h4><?php _e('🎯 Personalized Dashboard', 'jblund-dealers'); ?></h4>
                    <p><?php _e('Each dealer sees their company info, recent updates, and quick links', 'jblund-dealers'); ?></p>
                </div>
                <div class="feature-card">
                    <h4><?php _e('✏️ Profile Editor', 'jblund-dealers'); ?></h4>
                    <p><?php _e('Dealers can update their contact info, address, and services', 'jblund-dealers'); ?></p>
                </div>
                <div class="feature-card">
                    <h4><?php _e('📄 NDA Management', 'jblund-dealers'); ?></h4>
                    <p><?php _e('Built-in NDA acceptance workflow with digital signatures and PDF generation', 'jblund-dealers'); ?></p>
                </div>
                <div class="feature-card">
                    <h4><?php _e('📢 Announcements', 'jblund-dealers'); ?></h4>
                    <p><?php _e('Publish updates and news visible to all dealers on their dashboard', 'jblund-dealers'); ?></p>
                </div>
                <div class="feature-card">
                    <h4><?php _e('🔒 Security', 'jblund-dealers'); ?></h4>
                    <p><?php _e('Force password change on first login, NDA enforcement, role-based access control', 'jblund-dealers'); ?></p>
                </div>
                <div class="feature-card">
                    <h4><?php _e('📧 Automated Emails', 'jblund-dealers'); ?></h4>
                    <p><?php _e('Registration confirmations, approval notifications, NDA confirmations, rejection notices', 'jblund-dealers'); ?></p>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><?php _e('Bulk Import/Export', 'jblund-dealers'); ?></h3>
            <p><?php _e('Use the <strong>Import/Export</strong> tab to:', 'jblund-dealers'); ?></p>
            <ul style="list-style: disc; padding-left: 20px;">
                <li><?php _e('Export all dealer data to CSV for backup or migration', 'jblund-dealers'); ?></li>
                <li><?php _e('Import multiple dealers at once from a CSV file', 'jblund-dealers'); ?></li>
                <li><?php _e('Download a sample CSV to see the correct format', 'jblund-dealers'); ?></li>
                <li><?php _e('Update existing dealers by re-importing with matching IDs', 'jblund-dealers'); ?></li>
            </ul>
        </div>

        <div class="help-section" style="background: #fff3cd; border-left-color: #ffc107;">
            <h3 style="color: #856404;"><?php _e('Future Features (Coming Soon)', 'jblund-dealers'); ?></h3>
            <ul style="list-style: disc; padding-left: 20px;">
                <li><?php _e('<strong>Territory Management:</strong> Assign dealers to specific geographic territories', 'jblund-dealers'); ?></li>
                <li><?php _e('<strong>Territory Mapping:</strong> Visual map showing dealer coverage areas', 'jblund-dealers'); ?></li>
                <li><?php _e('<strong>Advanced Filtering:</strong> Filter dealers by services, territory, or custom criteria', 'jblund-dealers'); ?></li>
                <li><?php _e('<strong>Dealer Documents:</strong> Upload and manage dealer-specific documents in portal', 'jblund-dealers'); ?></li>
            </ul>
        </div>

        <div class="settings-section">
            <h3><?php _e('Need Help?', 'jblund-dealers'); ?></h3>
            <p><?php _e('For additional support, feature requests, or detailed documentation, please check:', 'jblund-dealers'); ?></p>
            <ul style="list-style: disc; padding-left: 20px;">
                <li><strong>DEALER-REGISTRATION-WORKFLOW.md</strong> - <?php _e('Complete registration system documentation', 'jblund-dealers'); ?></li>
                <li><strong>README.md</strong> - <?php _e('Plugin overview and installation guide', 'jblund-dealers'); ?></li>
                <li><strong>USAGE_GUIDE.md</strong> - <?php _e('Detailed usage instructions', 'jblund-dealers'); ?></li>
            </ul>
        </div>
        <?php
    }

    /**
     * Render Email Templates tab
     */
    private function render_emails_tab() {
        // Enqueue color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        // Enqueue custom admin JS for editor toggle
        wp_enqueue_script('jblund-email-editor',
            JBLUND_DEALERS_PLUGIN_URL . 'assets/js/email-editor.js',
            array('jquery'),
            JBLUND_DEALERS_VERSION,
            true
        );

        // Show success message after redirect
        if (isset($_GET['updated']) && $_GET['updated'] === 'true') {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Email template saved successfully!', 'jblund-dealers') . '</p></div>';
        }

        // Handle reset to default
        if (isset($_GET['reset']) && $_GET['reset'] == '1') {
            $reset_type = isset($_GET['email_type']) ? sanitize_text_field($_GET['email_type']) : 'approval';
            delete_option('jblund_email_template_' . $reset_type);

            // Redirect to remove the reset parameter
            wp_redirect(admin_url('edit.php?post_type=dealer&page=jblund-dealers-settings&tab=emails&email_type=' . $reset_type . '&reset_success=1'));
            exit;
        }

        // Handle save if form was submitted
        if (isset($_POST['submit_email_template'])) {
            check_admin_referer('jblund_email_templates');

            $template_type = sanitize_text_field($_POST['template_type']);

            // Check if using basic editor
            if (isset($_POST['editor_mode']) && $_POST['editor_mode'] === 'basic') {
                // Get the current template
                $template = get_option('jblund_email_template_' . $template_type);
                if (empty($template)) {
                    $template = $template_type === 'approval' ?
                        $this->get_default_approval_template() :
                        $this->get_default_rejection_template();
                }

                // Get basic fields based on template type
                if ($template_type === 'approval') {
                    $fields = array(
                        'greeting' => isset($_POST['email_greeting']) ? sanitize_text_field($_POST['email_greeting']) : '',
                        'intro' => isset($_POST['email_intro']) ? sanitize_textarea_field($_POST['email_intro']) : '',
                        'credentials_header' => isset($_POST['credentials_header']) ? sanitize_text_field($_POST['credentials_header']) : '',
                        'credentials_note' => isset($_POST['credentials_note']) ? sanitize_text_field($_POST['credentials_note']) : '',
                        'next_steps' => isset($_POST['next_steps']) && is_array($_POST['next_steps']) ?
                            array_map('sanitize_text_field', $_POST['next_steps']) : array(),
                        'cta_text' => isset($_POST['email_cta_text']) ? sanitize_text_field($_POST['email_cta_text']) : '',
                        'support' => isset($_POST['email_support']) ? sanitize_textarea_field($_POST['email_support']) : '',
                        'footer' => isset($_POST['email_footer']) ? sanitize_textarea_field($_POST['email_footer']) : ''
                    );
                } elseif ($template_type === 'admin_notification') {
                    // Admin notification template (HTML with markers like approval/rejection)
                    $fields = array(
                        'intro' => isset($_POST['admin_intro']) ? sanitize_textarea_field($_POST['admin_intro']) : '',
                        'rep_info_header' => isset($_POST['rep_info_header']) ? sanitize_text_field($_POST['rep_info_header']) : '',
                        'company_info_header' => isset($_POST['company_info_header']) ? sanitize_text_field($_POST['company_info_header']) : '',
                        'services_header' => isset($_POST['services_header']) ? sanitize_text_field($_POST['services_header']) : '',
                        'notes_header' => isset($_POST['notes_header']) ? sanitize_text_field($_POST['notes_header']) : '',
                        'cta_text' => isset($_POST['admin_cta_text']) ? sanitize_text_field($_POST['admin_cta_text']) : '',
                        'footer_note' => isset($_POST['admin_footer_note']) ? sanitize_textarea_field($_POST['admin_footer_note']) : '',
                        'footer' => isset($_POST['admin_footer']) ? sanitize_textarea_field($_POST['admin_footer']) : ''
                    );
                } else {
                    $fields = array(
                        'greeting' => isset($_POST['email_greeting']) ? sanitize_text_field($_POST['email_greeting']) : '',
                        'intro' => isset($_POST['email_intro']) ? sanitize_textarea_field($_POST['email_intro']) : '',
                        'reason_header' => isset($_POST['reason_header']) ? sanitize_text_field($_POST['reason_header']) : '',
                        'closing' => isset($_POST['closing']) && is_array($_POST['closing']) ?
                            array_map('sanitize_textarea_field', $_POST['closing']) : array(),
                        'support' => isset($_POST['email_support']) ? sanitize_textarea_field($_POST['email_support']) : '',
                        'footer' => isset($_POST['email_footer']) ? sanitize_textarea_field($_POST['email_footer']) : ''
                    );
                }

                // Update the HTML template with text-only changes
                $template = $this->update_template_fields($template, $template_type, $fields);

                // Save the modified HTML
                update_option('jblund_email_template_' . $template_type, $template);
            } else {
                // Advanced editor - save HTML with all allowed tags
                $allowed_tags = wp_kses_allowed_html('post');
                $allowed_tags['style'] = array();
                $allowed_tags['table'] = array('style' => true, 'cellpadding' => true, 'cellspacing' => true, 'border' => true, 'width' => true, 'align' => true);
                $allowed_tags['tr'] = array('style' => true);
                $allowed_tags['td'] = array('style' => true, 'colspan' => true, 'rowspan' => true, 'align' => true, 'valign' => true);
                $allowed_tags['th'] = array('style' => true, 'colspan' => true, 'rowspan' => true, 'align' => true, 'valign' => true);
                $content = wp_kses($_POST['email_content'], $allowed_tags);
                update_option('jblund_email_template_' . $template_type, $content);
            }

            // Redirect to prevent form resubmission
            wp_redirect(add_query_arg(array(
                'post_type' => 'dealer',
                'page' => 'jblund-dealers-settings',
                'tab' => 'emails',
                'email_type' => $template_type,
                'updated' => 'true'
            ), admin_url('edit.php')));
            exit;
        }

        // Handle branding settings save
        if (isset($_POST['submit_email_branding'])) {
            check_admin_referer('jblund_email_branding');

            $brand_color = sanitize_hex_color($_POST['email_primary_color']);
            update_option('jblund_email_brand_color', $brand_color);

            echo '<div class="notice notice-success"><p>' . __('Email branding saved successfully!', 'jblund-dealers') . '</p></div>';
        }

        // Get saved templates or defaults
        $approval_template = get_option('jblund_email_template_approval');
        if (empty($approval_template)) {
            $approval_template = $this->get_default_approval_template();
        }

        $rejection_template = get_option('jblund_email_template_rejection');
        if (empty($rejection_template)) {
            $rejection_template = $this->get_default_rejection_template();
        }

        $admin_notification_template = get_option('jblund_email_template_admin_notification');
        if (empty($admin_notification_template)) {
            $admin_notification_template = $this->get_default_admin_notification_template();
        }

        $brand_color = get_option('jblund_email_brand_color', '#FF0000');

        // Determine which template to show - THIS IS THE SINGLE SOURCE OF TRUTH
        $active_template = isset($_GET['email_type']) ? sanitize_text_field($_GET['email_type']) : 'approval';

        if ($active_template === 'admin_notification') {
            $current_html = $admin_notification_template;
        } elseif ($active_template === 'rejection') {
            $current_html = $rejection_template;
        } else {
            $current_html = $approval_template;
        }

        // Extract fields from the SAME HTML for the simple editor
        $fields = $this->extract_template_fields($current_html, $active_template);

        ?>
        <div class="jblund-nda-editor-wrap" style="max-width: 1400px;">

            <!-- Brand Color Settings -->
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; margin-top: 20px;">
                <h2 style="margin: 0 0 10px 0;"><?php _e('📧 Email Branding', 'jblund-dealers'); ?></h2>
                <p style="margin: 0 0 20px 0; color: #646970;">
                    <?php _e('Set your brand color to customize email headers, buttons, and accents across all email templates.', 'jblund-dealers'); ?>
                </p>

                <form method="post" action="" style="display: flex; align-items: center; gap: 15px;">
                    <?php wp_nonce_field('jblund_email_branding'); ?>

                    <div>
                        <label for="email_primary_color" style="font-weight: 600; margin-right: 10px;">
                            <?php _e('Primary Brand Color:', 'jblund-dealers'); ?>
                        </label>
                        <input type="text"
                               id="email_primary_color"
                               name="email_primary_color"
                               value="<?php echo esc_attr($brand_color); ?>"
                               class="color-field"
                               data-default-color="#FF0000"
                               style="width: 100px;" />
                        <span style="color: #646970; font-size: 13px; margin-left: 10px;">
                            <?php _e('Default: JBLund Red (#FF0000)', 'jblund-dealers'); ?>
                        </span>
                    </div>

                    <?php submit_button(__('Save Branding', 'jblund-dealers'), 'secondary', 'submit_email_branding', false); ?>
                </form>
            </div>

            <!-- Sub-navigation for email types -->
            <div style="background: #fff; padding: 15px 20px; border: 1px solid #ccd0d4; border-bottom: none; margin-top: 20px;">
                <h2 style="margin: 0 0 15px 0;"><?php _e('Email Templates', 'jblund-dealers'); ?></h2>
                <p style="margin: 0 0 15px 0; color: #646970;">
                    <?php _e('Customize the email templates sent to dealers during the registration process. Use the available shortcodes to insert dynamic content.', 'jblund-dealers'); ?>
                </p>
                <div class="nav-tab-wrapper" style="border-bottom: none; margin: 0;">
                    <a href="?post_type=dealer&page=jblund-dealers-settings&tab=emails&email_type=approval"
                       class="nav-tab <?php echo $active_template === 'approval' ? 'nav-tab-active' : ''; ?>">
                        <?php _e('✅ Approval Email', 'jblund-dealers'); ?>
                    </a>
                    <a href="?post_type=dealer&page=jblund-dealers-settings&tab=emails&email_type=rejection"
                       class="nav-tab <?php echo $active_template === 'rejection' ? 'nav-tab-active' : ''; ?>">
                        <?php _e('❌ Rejection Email', 'jblund-dealers'); ?>
                    </a>
                    <a href="?post_type=dealer&page=jblund-dealers-settings&tab=emails&email_type=admin_notification"
                       class="nav-tab <?php echo $active_template === 'admin_notification' ? 'nav-tab-active' : ''; ?>">
                        <?php _e('🔔 Admin Notification', 'jblund-dealers'); ?>
                    </a>
                </div>
            </div>

            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4;">

                <!-- Available Shortcodes Info Box -->
                <div style="background: #e7f3ff; border-left: 4px solid #0073aa; padding: 15px; margin-bottom: 20px;">
                    <h3 style="margin: 0 0 10px 0; color: #0073aa;">
                        <?php _e('📝 Available Shortcodes', 'jblund-dealers'); ?>
                    </h3>
                    <?php if ($active_template === 'approval'): ?>
                        <p style="margin: 0 0 10px 0;">
                            <strong>{{rep_name}}</strong> - Dealer representative's name<br>
                            <strong>{{username}}</strong> - Generated username<br>
                            <strong>{{password}}</strong> - Temporary password<br>
                            <strong>{{login_url}}</strong> - Link to dealer portal login
                        </p>
                    <?php elseif ($active_template === 'rejection'): ?>
                        <p style="margin: 0 0 10px 0;">
                            <strong>{{rep_name}}</strong> - Dealer representative's name<br>
                            <strong>{{reason}}</strong> - Rejection reason entered by admin
                        </p>
                    <?php else: ?>
                        <p style="margin: 0 0 10px 0;">
                            <strong>{{rep_name}}</strong> - Dealer representative's name<br>
                            <strong>{{rep_email}}</strong> - Dealer representative's email<br>
                            <strong>{{company}}</strong> - Company name<br>
                            <strong>{{territory}}</strong> - Territory/region<br>
                            <strong>{{admin_url}}</strong> - Link to review application
                        </p>
                    <?php endif; ?>
                    <p style="margin: 0; font-size: 13px; color: #555;">
                        <?php _e('These will be automatically replaced with actual values when the email is sent.', 'jblund-dealers'); ?>
                    </p>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;"><?php _e('Edit Email Template', 'jblund-dealers'); ?></h3>
                    <?php if ($active_template !== 'admin_notification'): ?>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" id="advanced_editor_toggle" />
                        <span><?php _e('Advanced HTML Editor', 'jblund-dealers'); ?></span>
                    </label>
                    <?php endif; ?>
                </div>

                <form method="post" action="">
                    <?php wp_nonce_field('jblund_email_templates'); ?>
                    <input type="hidden" name="template_type" value="<?php echo esc_attr($active_template); ?>">
                    <input type="hidden" name="editor_mode" id="editor_mode" value="basic">

                    <!-- Basic Editor -->
                    <div id="basic_editor" class="editor-mode">
                        <table class="form-table">
                            <?php if ($active_template === 'approval'): ?>
                                <!-- Approval Template Fields -->
                                <tr>
                                    <th scope="row"><label for="email_greeting"><?php _e('Greeting', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="email_greeting" id="email_greeting"
                                            value="<?php echo esc_attr($fields['greeting']); ?>"
                                            class="large-text"
                                            placeholder="e.g., Hello {{rep_name}}," />
                                        <p class="description"><?php _e('Use {{rep_name}} for the dealer representative name', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="email_intro"><?php _e('Approval Message', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <textarea name="email_intro" id="email_intro" rows="3" class="large-text"><?php echo esc_textarea($fields['intro']); ?></textarea>
                                        <p class="description"><?php _e('Main congratulations or notification message', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="credentials_header"><?php _e('Credentials Box Header', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="credentials_header" id="credentials_header"
                                            value="<?php echo esc_attr($fields['credentials_header']); ?>"
                                            class="large-text"
                                            placeholder="Your Login Credentials" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="credentials_note"><?php _e('Security Note', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="credentials_note" id="credentials_note"
                                            value="<?php echo esc_attr($fields['credentials_note']); ?>"
                                            class="large-text"
                                            placeholder="For security, please change your password..." />
                                        <p class="description"><?php _e('Security reminder shown below credentials', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label><?php _e('Next Steps', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <?php
                                        $step_count = !empty($fields['next_steps']) ? count($fields['next_steps']) : 4;
                                        for ($i = 0; $i < $step_count; $i++):
                                            $step_value = isset($fields['next_steps'][$i]) ? $fields['next_steps'][$i] : '';
                                        ?>
                                            <div style="margin-bottom: 10px;">
                                                <label style="display: block; font-weight: 600; margin-bottom: 3px;">Step <?php echo ($i + 1); ?></label>
                                                <input type="text" name="next_steps[]"
                                                    value="<?php echo esc_attr($step_value); ?>"
                                                    class="large-text"
                                                    placeholder="e.g., Log in to the dealer portal using the credentials above" />
                                            </div>
                                        <?php endfor; ?>
                                        <p class="description"><?php _e('Instructions shown as numbered list', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="email_cta_text"><?php _e('Button Text', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="email_cta_text" id="email_cta_text"
                                            value="<?php echo esc_attr($fields['cta_text']); ?>"
                                            class="large-text"
                                            placeholder="Log In to Dealer Portal" />
                                        <p class="description"><?php _e('Text shown on the call-to-action button', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                            <?php elseif ($active_template === 'rejection'): ?>
                                <!-- Rejection Template Fields -->
                                <tr>
                                    <th scope="row"><label for="email_greeting"><?php _e('Greeting', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="email_greeting" id="email_greeting"
                                            value="<?php echo esc_attr($fields['greeting']); ?>"
                                            class="large-text"
                                            placeholder="e.g., Hello {{rep_name}}," />
                                        <p class="description"><?php _e('Use {{rep_name}} for the dealer representative name', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="email_intro"><?php _e('Rejection Message', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <textarea name="email_intro" id="email_intro" rows="3" class="large-text"><?php echo esc_textarea($fields['intro']); ?></textarea>
                                        <p class="description"><?php _e('Main rejection notification message', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="reason_header"><?php _e('Reason Box Header', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="reason_header" id="reason_header"
                                            value="<?php echo esc_attr($fields['reason_header']); ?>"
                                            class="large-text"
                                            placeholder="Reason for Decision:" />
                                        <p class="description"><?php _e('Shown above the {{reason}} shortcode', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label><?php _e('Closing Paragraphs', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <?php
                                        $closing_count = !empty($fields['closing']) ? count($fields['closing']) : 2;
                                        for ($i = 0; $i < $closing_count; $i++):
                                            $closing_value = isset($fields['closing'][$i]) ? $fields['closing'][$i] : '';
                                        ?>
                                            <div style="margin-bottom: 10px;">
                                                <label style="display: block; font-weight: 600; margin-bottom: 3px;">Paragraph <?php echo ($i + 1); ?></label>
                                                <textarea name="closing[]" rows="2" class="large-text"><?php echo esc_textarea($closing_value); ?></textarea>
                                            </div>
                                        <?php endfor; ?>
                                        <p class="description"><?php _e('Closing remarks after the reason box', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                            <?php elseif ($active_template === 'admin_notification'): ?>
                                <!-- Admin Notification Template Fields -->
                                <tr>
                                    <th scope="row"><label for="admin_intro"><?php _e('Introduction', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <textarea name="admin_intro" id="admin_intro" rows="2" class="large-text"><?php echo esc_textarea($fields['intro']); ?></textarea>
                                        <p class="description"><?php _e('Main notification message at the top of the email', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="rep_info_header"><?php _e('Representative Section Header', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="rep_info_header" id="rep_info_header"
                                            value="<?php echo esc_attr($fields['rep_info_header']); ?>"
                                            class="large-text"
                                            placeholder="Representative Information" />
                                        <p class="description"><?php _e('Header text for the representative information section', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="company_info_header"><?php _e('Company Section Header', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="company_info_header" id="company_info_header"
                                            value="<?php echo esc_attr($fields['company_info_header']); ?>"
                                            class="large-text"
                                            placeholder="Company Information" />
                                        <p class="description"><?php _e('Header text for the company information section', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="services_header"><?php _e('Services Section Header', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="services_header" id="services_header"
                                            value="<?php echo esc_attr($fields['services_header']); ?>"
                                            class="large-text"
                                            placeholder="Services Offered" />
                                        <p class="description"><?php _e('Header text for the services offered section', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="notes_header"><?php _e('Notes Section Header', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="notes_header" id="notes_header"
                                            value="<?php echo esc_attr($fields['notes_header']); ?>"
                                            class="large-text"
                                            placeholder="About Their Business" />
                                        <p class="description"><?php _e('Header text for the notes/business description section', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="admin_cta_text"><?php _e('CTA Button Text', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <input type="text" name="admin_cta_text" id="admin_cta_text"
                                            value="<?php echo esc_attr($fields['cta_text']); ?>"
                                            class="large-text"
                                            placeholder="Review Application" />
                                        <p class="description"><?php _e('Text shown on the "Review Application" button', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="admin_footer_note"><?php _e('Footer Note', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <textarea name="admin_footer_note" id="admin_footer_note" rows="2" class="large-text"><?php echo esc_textarea($fields['footer_note']); ?></textarea>
                                        <p class="description"><?php _e('Instructions or note shown at the bottom before the copyright', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="admin_footer"><?php _e('Footer Copyright', 'jblund-dealers'); ?></label></th>
                                    <td>
                                        <textarea name="admin_footer" id="admin_footer" rows="2" class="large-text"><?php echo esc_textarea($fields['footer']); ?></textarea>
                                        <p class="description"><?php _e('Copyright text at the very bottom of the email', 'jblund-dealers'); ?></p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($active_template !== 'admin_notification'): ?>
                            <tr>
                                <th scope="row"><label for="email_support"><?php _e('Support Information', 'jblund-dealers'); ?></label></th>
                                <td>
                                    <textarea name="email_support" id="email_support" rows="2" class="large-text"><?php echo esc_textarea($fields['support']); ?></textarea>
                                    <p class="description"><?php _e('Help text shown in gray box', 'jblund-dealers'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="email_footer"><?php _e('Footer Text', 'jblund-dealers'); ?></label></th>
                                <td>
                                    <textarea name="email_footer" id="email_footer" rows="2" class="large-text"><?php echo esc_textarea($fields['footer']); ?></textarea>
                                    <p class="description"><?php _e('Small print at bottom of email', 'jblund-dealers'); ?></p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>

                    <!-- Advanced Editor (not available for admin notifications) -->
                    <?php if ($active_template !== 'admin_notification'): ?>
                    <div id="advanced_editor" class="editor-mode" style="display: none;">
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">
                                <?php _e('Edit HTML Template:', 'jblund-dealers'); ?>
                            </label>
                            <textarea name="email_content" id="email_content" rows="25" style="width: 100%; font-family: monospace; font-size: 13px; padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($current_html, ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <p class="description">
                                <?php
                                printf(
                                    __('Edit the HTML directly. Template loaded: %d characters. Use the shortcodes shown above to insert dynamic content.', 'jblund-dealers'),
                                    strlen($current_html)
                                );
                                ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div style="margin-top: 20px;">
                        <?php submit_button(__('Save Email Template', 'jblund-dealers'), 'primary', 'submit_email_template', false); ?>

                        <a href="?post_type=dealer&page=jblund-dealers-settings&tab=emails&email_type=<?php echo esc_attr($active_template); ?>&reset=1"
                           class="button"
                           onclick="return confirm('<?php _e('Reset to default template? Any customizations will be lost.', 'jblund-dealers'); ?>');"
                           style="margin-left: 10px;">
                            <?php _e('Reset to Default', 'jblund-dealers'); ?>
                        </a>
                    </div>
                </form>                <!-- Preview Section -->
                <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #ddd;">
                    <h3><?php _e('📧 Email Preview', 'jblund-dealers'); ?></h3>
                    <p style="color: #646970;">
                        <?php _e('This is how the email will appear to recipients (shortcodes shown as examples):', 'jblund-dealers'); ?>
                    </p>
                    <div style="background: #f9f9f9; border: 1px solid #ddd; padding: 20px; margin-top: 15px;">
                        <?php
                        // Show preview with example data - using the SAME HTML as above
                        $preview_html = $current_html;
                        if ($active_template === 'approval') {
                            $preview_html = str_replace(
                                array('{{rep_name}}', '{{username}}', '{{password}}', '{{login_url}}'),
                                array('<strong>John Smith</strong>', '<code>jsmith</code>', '<code>Temp123!Pass</code>', 'https://yoursite.com/dealer-login'),
                                $preview_html
                            );
                        } else {
                            $preview_html = str_replace(
                                array('{{rep_name}}', '{{reason}}'),
                                array('<strong>John Smith</strong>', '<em>Territory requirements not met at this time</em>'),
                                $preview_html
                            );
                        }
                        // Output the HTML directly - SAME HTML as in editors
                        echo $preview_html;
                        ?>
                    </div>
                </div>

            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Initialize color picker
            $('.color-field').wpColorPicker();
        });
        </script>
        <?php
    }

    /**
     * Get default approval email template
     */
    private function get_default_approval_template() {
        // Use absolute path from __FILE__ instead of plugin_dir_path
        $template_path = dirname(dirname(__FILE__)) . '/dealer-portal/templates/emails/approval-template.html';
        if (file_exists($template_path)) {
            return file_get_contents($template_path);
        }
        // Fallback - return error message for debugging
        return '<!-- Template not found at: ' . $template_path . ' -->';
    }

    /**
     * Get default rejection email template
     */
    private function get_default_rejection_template() {
        // Use absolute path from __FILE__ instead of plugin_dir_path
        $template_path = dirname(dirname(__FILE__)) . '/dealer-portal/templates/emails/rejection-template.html';
        if (file_exists($template_path)) {
            return file_get_contents($template_path);
        }
        // Fallback - return error message for debugging
        return '<!-- Template not found at: ' . $template_path . ' -->';
    }

    /**
     * Get default admin notification email template
     */
    private function get_default_admin_notification_template() {
        // Use absolute path from __FILE__ instead of plugin_dir_path
        $template_path = dirname(dirname(__FILE__)) . '/dealer-portal/templates/emails/admin-notification-template.html';
        if (file_exists($template_path)) {
            return file_get_contents($template_path);
        }
        // Fallback - return simple HTML
        return "<!DOCTYPE html><html><body><p>A new dealer application has been submitted.</p><p>Representative: {{rep_name}}<br>Email: {{rep_email}}<br>Phone: {{rep_phone}}<br>Company: {{company}}<br>Territory: {{territory}}</p><p><a href='{{admin_url}}'>Review Application</a></p></body></html>";
    }

    /**
     * Extract editable fields from admin notification HTML template
     */
    private function extract_admin_notification_fields($template) {
        $fields = array(
            'intro' => '',
            'rep_info_header' => '',
            'company_info_header' => '',
            'services_header' => '',
            'notes_header' => '',
            'cta_text' => '',
            'footer_note' => '',
            'footer' => ''
        );

        // Extract INTRO
        if (preg_match('/<!-- \[EDITABLE:INTRO\] -->(.*?)<!-- \[\/EDITABLE:INTRO\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['intro'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract REP_INFO header
        if (preg_match('/<!-- \[EDITABLE:REP_INFO\] -->(.*?)<!-- \[\/EDITABLE:REP_INFO\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['rep_info_header'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract COMPANY_INFO header
        if (preg_match('/<!-- \[EDITABLE:COMPANY_INFO\] -->(.*?)<!-- \[\/EDITABLE:COMPANY_INFO\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['company_info_header'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract SERVICES header
        if (preg_match('/<!-- \[EDITABLE:SERVICES\] -->(.*?)<!-- \[\/EDITABLE:SERVICES\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['services_header'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract NOTES header
        if (preg_match('/<!-- \[EDITABLE:NOTES\] -->(.*?)<!-- \[\/EDITABLE:NOTES\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['notes_header'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract CTA button text
        if (preg_match('/<!-- \[EDITABLE:CTA\] -->(.*?)<!-- \[\/EDITABLE:CTA\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['cta_text'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract FOOTER_NOTE
        if (preg_match('/<!-- \[EDITABLE:FOOTER_NOTE\] -->(.*?)<!-- \[\/EDITABLE:FOOTER_NOTE\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['footer_note'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract FOOTER
        if (preg_match('/<!-- \[EDITABLE:FOOTER\] -->(.*?)<!-- \[\/EDITABLE:FOOTER\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['footer'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        return $fields;
    }

    /**
     * Extract editable text-only fields from template HTML using markers
     */
    private function extract_template_fields($template, $type) {
        // Admin notification template (HTML with markers)
        if ($type === 'admin_notification') {
            // Extract fields from HTML using markers similar to other templates
            return $this->extract_admin_notification_fields($template);
        }

        // Rejection template has simpler structure
        if ($type === 'rejection') {
            $fields = array(
                'greeting' => '',
                'intro' => '',
                'reason_header' => '',
                'closing' => array(),
                'support' => '',
                'footer' => ''
            );

            // Extract greeting
            if (preg_match('/<!-- \[EDITABLE:GREETING\] -->(.*?)<!-- \[\/EDITABLE:GREETING\] -->/s', $template, $matches)) {
                $text = strip_tags($matches[1]);
                // Remove excessive whitespace while preserving single spaces
                $text = preg_replace('/\s+/', ' ', $text);
                $fields['greeting'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }

            // Extract intro
            if (preg_match('/<!-- \[EDITABLE:INTRO\] -->(.*?)<!-- \[\/EDITABLE:INTRO\] -->/s', $template, $matches)) {
                $text = strip_tags($matches[1]);
                $text = preg_replace('/\s+/', ' ', $text);
                $fields['intro'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }

            // Extract reason box header
            if (preg_match('/<!-- \[EDITABLE:REASON_BOX\] -->(.*?)<!-- \[\/EDITABLE:REASON_BOX\] -->/s', $template, $matches)) {
                if (preg_match('/<h3[^>]*>(.*?)<\/h3>/s', $matches[1], $h3_match)) {
                    $text = strip_tags($h3_match[1]);
                    $text = preg_replace('/\s+/', ' ', $text);
                    $fields['reason_header'] = trim($text);
                }
            }

            // Extract closing paragraphs (if marker exists, otherwise fallback to direct extraction)
            if (preg_match('/<!-- \[EDITABLE:CLOSING\] -->(.*?)<!-- \[\/EDITABLE:CLOSING\] -->/s', $template, $matches)) {
                preg_match_all('/<p[^>]*>(.*?)<\/p>/s', $matches[1], $p_matches);
                foreach ($p_matches[1] as $p) {
                    $text = strip_tags($p);
                    $text = preg_replace('/\s+/', ' ', $text);
                    $fields['closing'][] = trim($text);
                }
            } else {
                // Fallback: find paragraphs after reason box
                $after_reason = preg_split('/<!-- \[\/EDITABLE:REASON_BOX\] -->/', $template);
                if (isset($after_reason[1])) {
                    preg_match_all('/<p[^>]*style="[^"]*margin:\s*20px[^"]*"[^>]*>(.*?)<\/p>/s', $after_reason[1], $p_matches);
                    foreach ($p_matches[1] as $p) {
                        $text = strip_tags($p);
                        $text = preg_replace('/\s+/', ' ', $text);
                        if (!empty(trim($text))) {
                            $fields['closing'][] = trim($text);
                            if (count($fields['closing']) >= 2) break;
                        }
                    }
                }
            }

            // Extract support text
            if (preg_match('/<!-- \[EDITABLE:SUPPORT\] -->(.*?)<!-- \[\/EDITABLE:SUPPORT\] -->/s', $template, $matches)) {
                $text = strip_tags($matches[1]);
                $text = preg_replace('/\s+/', ' ', $text);
                $fields['support'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            } else {
                // Fallback: support info paragraph
                if (preg_match('/<p[^>]*background-color:\s*#f8f9fa[^>]*>(.*?)<\/p>/s', $template, $matches)) {
                    $text = strip_tags($matches[1]);
                    $text = preg_replace('/\s+/', ' ', $text);
                    $fields['support'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                }
            }

            // Extract footer
            if (preg_match('/<!-- \[EDITABLE:FOOTER\] -->(.*?)<!-- \[\/EDITABLE:FOOTER\] -->/s', $template, $matches)) {
                $text = preg_replace('/<br\s*\/?>/i', "\n", $matches[1]);
                $text = strip_tags($text);
                $text = preg_replace('/[ \t]+/m', ' ', $text); // Replace multiple spaces/tabs with single space per line
                $fields['footer'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            } else {
                // Fallback: footer paragraph
                if (preg_match('/<p[^>]*font-size:\s*13px[^>]*>(.*?)<\/p>/s', $template, $matches)) {
                    $text = preg_replace('/<br\s*\/?>/i', "\n", $matches[1]);
                    $text = strip_tags($text);
                    $text = preg_replace('/[ \t]+/m', ' ', $text);
                    $fields['footer'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                }
            }

            return $fields;
        }

        // Approval template has more complex structure
        $fields = array(
            'greeting' => '',
            'intro' => '',
            'credentials_header' => '',
            'credentials_note' => '',
            'next_steps' => array(),
            'cta_text' => '',
            'support' => '',
            'footer' => ''
        );

        // Extract greeting (just the text, not HTML) - clean whitespace
        if (preg_match('/<!-- \[EDITABLE:GREETING\] -->(.*?)<!-- \[\/EDITABLE:GREETING\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['greeting'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract intro paragraph
        if (preg_match('/<!-- \[EDITABLE:INTRO\] -->(.*?)<!-- \[\/EDITABLE:INTRO\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['intro'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract credentials section (header and note)
        if (preg_match('/<!-- \[EDITABLE:CREDENTIALS\] -->(.*?)<!-- \[\/EDITABLE:CREDENTIALS\] -->/s', $template, $matches)) {
            $content = $matches[1];
            // Extract header
            if (preg_match('/<h3[^>]*>(.*?)<\/h3>/s', $content, $h3_match)) {
                $text = strip_tags($h3_match[1]);
                $text = preg_replace('/\s+/', ' ', $text);
                $fields['credentials_header'] = trim($text);
            }
            // Extract security note (the italic paragraph)
            if (preg_match('/<p[^>]*font-style:\s*italic[^>]*>(.*?)<\/p>/s', $content, $note_match)) {
                $text = strip_tags($note_match[1]);
                $text = preg_replace('/\s+/', ' ', $text);
                $fields['credentials_note'] = trim($text);
            }
        }

        // Extract next steps list items
        if (preg_match('/<!-- \[EDITABLE:NEXT_STEPS\] -->(.*?)<!-- \[\/EDITABLE:NEXT_STEPS\] -->/s', $template, $matches)) {
            preg_match_all('/<li>(.*?)<\/li>/s', $matches[1], $li_matches);
            foreach ($li_matches[1] as $li) {
                $text = strip_tags($li);
                $text = preg_replace('/\s+/', ' ', $text);
                $fields['next_steps'][] = trim($text);
            }
        }

        // Extract CTA button text
        if (preg_match('/<!-- \[EDITABLE:CTA\] -->(.*?)<!-- \[\/EDITABLE:CTA\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['cta_text'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract support text
        if (preg_match('/<!-- \[EDITABLE:SUPPORT\] -->(.*?)<!-- \[\/EDITABLE:SUPPORT\] -->/s', $template, $matches)) {
            $text = strip_tags($matches[1]);
            $text = preg_replace('/\s+/', ' ', $text);
            $fields['support'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Extract footer
        if (preg_match('/<!-- \[EDITABLE:FOOTER\] -->(.*?)<!-- \[\/EDITABLE:FOOTER\] -->/s', $template, $matches)) {
            $text = preg_replace('/<br\s*\/?>/i', "\n", $matches[1]);
            $text = strip_tags($text);
            $text = preg_replace('/[ \t]+/m', ' ', $text); // Replace multiple spaces/tabs with single space per line
            $fields['footer'] = trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        return $fields;
    }

    /**
     * Update template HTML with text-only field values (preserves HTML structure)
        if (empty($fields['intro']) && $type === 'approval') {
            // Extract greeting - look for first paragraph with larger font
            if (preg_match('/<p[^>]*style="[^"]*font-size:\s*18px[^"]*"[^>]*>(.*?)<\/p>/s', $template, $matches)) {
                $fields['greeting'] = trim(strip_tags($matches[1]));
            }

            // Extract intro - look for paragraph after greeting
            if (preg_match('/<p[^>]*style="[^"]*margin:\s*0[^"]*"[^>]*>(.*?)<\/p>/s', $template, $matches)) {
                $fields['intro'] = trim(strip_tags($matches[1]));
            } elseif (preg_match('/<p[^>]*>(?!<)((?:Great news|Congratulations).*?)<\/p>/si', $template, $matches)) {
                $fields['intro'] = trim(strip_tags($matches[1]));
            } elseif (preg_match('/font-size:\s*18px[^>]*>.*?<\/p>\s*<p[^>]*>(.*?)<\/p>/s', $template, $matches)) {
                $fields['intro'] = trim(strip_tags($matches[1]));
            }

            // Extract credentials box content - everything in the bordered div
            if (preg_match('/<div[^>]*border-left:[^>]*>(.*?)<\/div>/s', $template, $matches)) {
                $content = $matches[1];
                // Remove HTML tags but keep line breaks
                $content = preg_replace('/<br\s*\/?>/i', "\n", $content);
                $content = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n\n", $content);
                $content = preg_replace('/<\/(div|p)>\s*<(div|p)[^>]*>/i', "\n", $content);
                $content = strip_tags($content);
                // Decode HTML entities
                $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $fields['body'] = trim($content);
            }

            // Extract CTA button text
            if (preg_match('/<a[^>]*class="[^"]*email-button[^"]*"[^>]*>(.*?)<\/a>/s', $template, $matches)) {
                $fields['cta_text'] = trim(strip_tags($matches[1]));
            }

            // Extract footer - gray text at bottom (multiple possible patterns)
            $footer_patterns = array(
                '/<p[^>]*style="[^"]*color:\s*#666[^"]*"[^>]*>(.*?)<\/p>/si',
                '/<p[^>]*style="[^"]*font-size:\s*12px[^"]*"[^>]*>(.*?)<\/p>/si',
                '/<p[^>]*style="[^"]*text-align:\s*center[^"]*color:[^"]*666[^"]*"[^>]*>(.*?)<\/p>/si'
            );
            foreach ($footer_patterns as $pattern) {
                if (preg_match($pattern, $template, $matches)) {
                    $fields['footer'] = trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    break;
                }
            }
        } else {
            // Rejection template
            // Extract greeting
            if (preg_match('/<p[^>]*style="[^"]*font-size:\s*18px[^"]*"[^>]*>(.*?)<\/p>/s', $template, $matches)) {
                $fields['greeting'] = trim(strip_tags($matches[1]));
            }

            // Extract intro - "Thank you for..." paragraph
            if (preg_match('/<p[^>]*style="[^"]*margin:\s*0[^"]*"[^>]*>(.*?)<\/p>/s', $template, $matches)) {
                $fields['intro'] = trim(strip_tags($matches[1]));
            } elseif (preg_match('/<p[^>]*>(Thank you for.*?)<\/p>/si', $template, $matches)) {
                $fields['intro'] = trim(strip_tags($matches[1]));
            } elseif (preg_match('/font-size:\s*18px[^>]*>.*?<\/p>\s*<p[^>]*>(.*?)<\/p>/s', $template, $matches)) {
                $fields['intro'] = trim(strip_tags($matches[1]));
            }

            // Extract reason section content - try multiple patterns
            if (preg_match('/<div[^>]*background-color:\s*#fff3cd[^>]*>(.*?)<\/div>/s', $template, $matches)) {
                $content = $matches[1];
                $content = preg_replace('/<br\s*\/?>/i', "\n", $content);
                $content = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n\n", $content);
                $content = strip_tags($content);
                $fields['body'] = trim($content);
            } elseif (preg_match('/<div[^>]*border:[^>]*yellow[^>]*>(.*?)<\/div>/s', $template, $matches)) {
                $content = $matches[1];
                $content = preg_replace('/<br\s*\/?>/i', "\n", $content);
                $content = strip_tags($content);
                $fields['body'] = trim($content);
            }

            // Extract CTA button text
            if (preg_match('/<a[^>]*class="[^"]*email-button[^"]*"[^>]*>(.*?)<\/a>/s', $template, $matches)) {
                $fields['cta_text'] = trim(strip_tags($matches[1]));
            }

            // Extract footer - multiple possible patterns
            if (preg_match('/<p[^>]*style="[^"]*color:\s*#666[^"]*"[^>]*>(.*?)<\/p>/s', $template, $matches)) {
                $fields['footer'] = trim(strip_tags($matches[1]));
            } elseif (preg_match('/<p[^>]*style="[^"]*font-size:\s*12px[^"]*"[^>]*>(.*?)<\/p>/s', $template, $matches)) {
                $fields['footer'] = trim(strip_tags($matches[1]));
            }
        }

        return $fields;
    }

    /**
     * Update template with new field values using markers or fallback patterns
     */
    private function update_template_fields($template, $type, $fields) {
        // Admin notification template - update 8 editable sections
        if ($type === 'admin_notification') {
            // Update INTRO
            if (isset($fields['intro']) && preg_match('/(<!-- \[EDITABLE:INTRO\] -->)(.*?)(<p[^>]*>)(.*?)(<\/p>)(.*?)(<!-- \[\/EDITABLE:INTRO\] -->)/s', $template, $match)) {
                $new_intro = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['intro'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
                $template = str_replace($match[0], $new_intro, $template);
            }

            // Update REP_INFO header
            if (isset($fields['rep_info_header']) && preg_match('/(<!-- \[EDITABLE:REP_INFO\] -->)(.*?)(<h3[^>]*>)(.*?)(<\/h3>)(.*?)(<!-- \[\/EDITABLE:REP_INFO\] -->)/s', $template, $match)) {
                $new_header = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['rep_info_header'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
                $template = str_replace($match[0], $new_header, $template);
            }

            // Update COMPANY_INFO header
            if (isset($fields['company_info_header']) && preg_match('/(<!-- \[EDITABLE:COMPANY_INFO\] -->)(.*?)(<h3[^>]*>)(.*?)(<\/h3>)(.*?)(<!-- \[\/EDITABLE:COMPANY_INFO\] -->)/s', $template, $match)) {
                $new_header = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['company_info_header'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
                $template = str_replace($match[0], $new_header, $template);
            }

            // Update SERVICES header
            if (isset($fields['services_header']) && preg_match('/(<!-- \[EDITABLE:SERVICES\] -->)(.*?)(<h3[^>]*>)(.*?)(<\/h3>)(.*?)(<!-- \[\/EDITABLE:SERVICES\] -->)/s', $template, $match)) {
                $new_header = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['services_header'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
                $template = str_replace($match[0], $new_header, $template);
            }

            // Update NOTES header
            if (isset($fields['notes_header']) && preg_match('/(<!-- \[EDITABLE:NOTES\] -->)(.*?)(<h3[^>]*>)(.*?)(<\/h3>)(.*?)(<!-- \[\/EDITABLE:NOTES\] -->)/s', $template, $match)) {
                $new_header = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['notes_header'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
                $template = str_replace($match[0], $new_header, $template);
            }

            // Update CTA button text
            if (isset($fields['cta_text']) && preg_match('/(<!-- \[EDITABLE:CTA\] -->)(.*?)(<a[^>]*>)(.*?)(<\/a>)(.*?)(<!-- \[\/EDITABLE:CTA\] -->)/s', $template, $match)) {
                $new_cta = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['cta_text'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
                $template = str_replace($match[0], $new_cta, $template);
            }

            // Update FOOTER_NOTE
            if (isset($fields['footer_note']) && preg_match('/(<!-- \[EDITABLE:FOOTER_NOTE\] -->)(.*?)(<p[^>]*>)(.*?)(<\/p>)(.*?)(<!-- \[\/EDITABLE:FOOTER_NOTE\] -->)/s', $template, $match)) {
                $new_note = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['footer_note'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
                $template = str_replace($match[0], $new_note, $template);
            }

            // Update FOOTER
            if (isset($fields['footer']) && preg_match('/(<!-- \[EDITABLE:FOOTER\] -->)(.*?)(<p[^>]*>)(.*?)(<\/p>)(.*?)(<!-- \[\/EDITABLE:FOOTER\] -->)/s', $template, $match)) {
                $new_footer = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['footer'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
                $template = str_replace($match[0], $new_footer, $template);
            }

            return $template;
        }

        // Approval/Rejection templates - update greeting - preserve HTML tags, replace only text
        if (preg_match('/(<!-- \[EDITABLE:GREETING\] -->)(.*?)(<p[^>]*>)(.*?)(<\/p>)(.*?)(<!-- \[\/EDITABLE:GREETING\] -->)/s', $template, $match)) {
            $new_greeting = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['greeting'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
            $template = str_replace($match[0], $new_greeting, $template);
        }

        // Update intro - preserve HTML tags, replace only text
        if (preg_match('/(<!-- \[EDITABLE:INTRO\] -->)(.*?)(<p[^>]*>)(.*?)(<\/p>)(.*?)(<!-- \[\/EDITABLE:INTRO\] -->)/s', $template, $match)) {
            $new_intro = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['intro'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
            $template = str_replace($match[0], $new_intro, $template);
        }

        // Update credentials section - preserve structure, update header and note
        if (preg_match('/(<!-- \[EDITABLE:CREDENTIALS\] -->)(.*?)(<!-- \[\/EDITABLE:CREDENTIALS\] -->)/s', $template, $match)) {
            $credentials_html = $match[2];
            // Update header
            $credentials_html = preg_replace('/(<h3[^>]*>)(.*?)(<\/h3>)/s', '$1' . htmlspecialchars($fields['credentials_header'], ENT_QUOTES) . '$3', $credentials_html);
            // Update security note
            $credentials_html = preg_replace('/(<p[^>]*font-style:\s*italic[^>]*>)(.*?)(<\/p>)/s', '$1' . htmlspecialchars($fields['credentials_note'], ENT_QUOTES) . '$3', $credentials_html);
            $template = str_replace($match[0], $match[1] . $credentials_html . $match[3], $template);
        }

        // Update next steps list items
        if (preg_match('/(<!-- \[EDITABLE:NEXT_STEPS\] -->)(.*?)(<ol[^>]*>)(.*?)(<\/ol>)(.*?)(<!-- \[\/EDITABLE:NEXT_STEPS\] -->)/s', $template, $match)) {
            $new_list_items = '';
            foreach ($fields['next_steps'] as $step) {
                $new_list_items .= "\n\t\t\t\t\t\t\t\t<li>" . htmlspecialchars($step, ENT_QUOTES) . "</li>";
            }
            $new_list_items .= "\n\t\t\t\t\t\t\t";
            $new_section = $match[1] . $match[2] . $match[3] . $new_list_items . $match[5] . $match[6] . $match[7];
            $template = str_replace($match[0], $new_section, $template);
        }

        // Update CTA button text - preserve HTML, update text only
        if (preg_match('/(<!-- \[EDITABLE:CTA\] -->)(.*?)(<a[^>]*>)(.*?)(<\/a>)(.*?)(<!-- \[\/EDITABLE:CTA\] -->)/s', $template, $match)) {
            $new_cta = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['cta_text'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
            $template = str_replace($match[0], $new_cta, $template);
        }

        // Update support text
        if (preg_match('/(<!-- \[EDITABLE:SUPPORT\] -->)(.*?)(<p[^>]*>)(.*?)(<\/p>)(.*?)(<!-- \[\/EDITABLE:SUPPORT\] -->)/s', $template, $match)) {
            $new_support = $match[1] . $match[2] . $match[3] . htmlspecialchars($fields['support'], ENT_QUOTES) . $match[5] . $match[6] . $match[7];
            $template = str_replace($match[0], $new_support, $template);
        }

        // Update footer - handle <br> tags for multi-line
        if (preg_match('/(<!-- \[EDITABLE:FOOTER\] -->)(.*?)(<p[^>]*>)(.*?)(<\/p>)(.*?)(<!-- \[\/EDITABLE:FOOTER\] -->)/s', $template, $match)) {
            $footer_html = str_replace("\n", "<br />\n\t\t\t\t\t\t\t\t", htmlspecialchars($fields['footer'], ENT_QUOTES));
            $new_footer = $match[1] . $match[2] . $match[3] . $footer_html . $match[5] . $match[6] . $match[7];
            $template = str_replace($match[0], $new_footer, $template);
        }

        return $template;
    }

    /**
     * Legacy update method using pattern matching (fallback)
     */
    private function update_template_fields_legacy($template, $type, $fields) {
        if ($type === 'approval') {
            // Update greeting
            $template = preg_replace(
                '/(<p[^>]*style="[^"]*font-size:\s*18px[^"]*"[^>]*>)(.*?)(<\/p>)/s',
                '$1' . htmlspecialchars($fields['greeting'], ENT_QUOTES) . '$3',
                $template
            );

            // Update intro paragraph
            $template = preg_replace(
                '/(<p[^>]*>)Great news!(.*?)(<\/p>)/s',
                '$1' . htmlspecialchars($fields['intro'], ENT_QUOTES) . '$3',
                $template
            );

            // Update credentials box content
            $body_html = nl2br(htmlspecialchars($fields['body'], ENT_QUOTES));
            $template = preg_replace(
                '/(<div[^>]*border-left:[^>]*>)(.*?)(<\/div>)/s',
                '$1' . $body_html . '$3',
                $template
            );

            // Update CTA button text
            $template = preg_replace(
                '/(<a[^>]*class="[^"]*email-button[^"]*"[^>]*>)(.*?)(<\/a>)/s',
                '$1' . htmlspecialchars($fields['cta_text'], ENT_QUOTES) . '$3',
                $template
            );

            // Update footer
            $template = preg_replace(
                '/(<p[^>]*style="[^"]*color:\s*#666666[^"]*"[^>]*>)(.*?)(<\/p>)/s',
                '$1' . htmlspecialchars($fields['footer'], ENT_QUOTES) . '$3',
                $template
            );
        } else {
            // Update rejection template fields
            $template = preg_replace(
                '/(<p[^>]*style="[^"]*font-size:\s*18px[^"]*"[^>]*>)(.*?)(<\/p>)/s',
                '$1' . htmlspecialchars($fields['greeting'], ENT_QUOTES) . '$3',
                $template
            );

            $template = preg_replace(
                '/(<p[^>]*>)Thank you for(.*?)(<\/p>)/s',
                '$1' . htmlspecialchars($fields['intro'], ENT_QUOTES) . '$3',
                $template
            );

            // Update reason box content
            $body_html = nl2br(htmlspecialchars($fields['body'], ENT_QUOTES));
            $template = preg_replace(
                '/(<div[^>]*background-color:\s*#fff3cd[^>]*>)(.*?)(<\/div>)/s',
                '$1' . $body_html . '$3',
                $template
            );

            $template = preg_replace(
                '/(<a[^>]*class="[^"]*email-button[^"]*"[^>]*>)(.*?)(<\/a>)/s',
                '$1' . htmlspecialchars($fields['cta_text'], ENT_QUOTES) . '$3',
                $template
            );

            $template = preg_replace(
                '/(<p[^>]*style="[^"]*color:\s*#666666[^"]*"[^>]*>)(.*?)(<\/p>)/s',
                '$1' . htmlspecialchars($fields['footer'], ENT_QUOTES) . '$3',
                $template
            );
        }

        return $template;
    }
}
