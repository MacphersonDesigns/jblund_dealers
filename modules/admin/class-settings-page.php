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
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=representative"
                   class="nav-tab <?php echo $active_tab === 'representative' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Representative', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=documents"
                   class="nav-tab <?php echo $active_tab === 'documents' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Documents', 'jblund-dealers'); ?>
                </a>
                <a href="?post_type=dealer&page=jblund-dealers-settings&tab=nda"
                   class="nav-tab <?php echo $active_tab === 'nda' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('NDA Editor', 'jblund-dealers'); ?>
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
                    case 'representative':
                        $this->render_representative_tab();
                        break;
                    case 'documents':
                        $this->render_documents_tab();
                        break;
                    case 'nda':
                        $this->render_nda_tab();
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
     * Render Import/Export tab
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
            <h3><?php _e('Creating Dealer Portal Users', 'jblund-dealers'); ?></h3>
            <ol class="step-list">
                <li><strong><?php _e('Create Dealer User Account', 'jblund-dealers'); ?></strong><br><?php _e('Go to Users > Add New and create a user with the "Dealer" role', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Link to Dealer Post', 'jblund-dealers'); ?></strong><br><?php _e('When editing the dealer location, select the user in the "Linked User Account" dropdown', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Set Login Credentials', 'jblund-dealers'); ?></strong><br><?php _e('The dealer will use these credentials to log into the portal', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Send Welcome Email', 'jblund-dealers'); ?></strong><br><?php _e('WordPress will automatically send login details to the dealer\'s email', 'jblund-dealers'); ?></li>
                <li><strong><?php _e('Dealer Accepts NDA', 'jblund-dealers'); ?></strong><br><?php _e('On first login, dealers must accept the NDA before accessing the portal', 'jblund-dealers'); ?></li>
            </ol>
        </div>

        <div class="settings-section">
            <h3><?php _e('Displaying Dealers on Your Site', 'jblund-dealers'); ?></h3>
            <p><?php _e('Use the shortcode to display dealers anywhere on your site:', 'jblund-dealers'); ?></p>
            <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa; margin: 15px 0;">
                <code style="font-size: 14px;">[jblund_dealers]</code>
            </div>
            <p><strong><?php _e('Shortcode Parameters:', 'jblund-dealers'); ?></strong></p>
            <ul style="list-style: disc; padding-left: 20px;">
                <li><code>layout="grid"</code> - <?php _e('Grid layout (default)', 'jblund-dealers'); ?></li>
                <li><code>layout="list"</code> - <?php _e('Horizontal list layout', 'jblund-dealers'); ?></li>
                <li><code>layout="compact"</code> - <?php _e('Compact grid layout', 'jblund-dealers'); ?></li>
                <li><code>posts_per_page="12"</code> - <?php _e('Number of dealers to display', 'jblund-dealers'); ?></li>
                <li><code>orderby="title"</code> - <?php _e('Sort by title, date, etc.', 'jblund-dealers'); ?></li>
            </ul>
            <p><strong><?php _e('Example:', 'jblund-dealers'); ?></strong></p>
            <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa;">
                <code style="font-size: 14px;">[jblund_dealers layout="list" posts_per_page="6"]</code>
            </div>
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
                    <p><?php _e('Built-in NDA acceptance workflow with digital signatures', 'jblund-dealers'); ?></p>
                </div>
                <div class="feature-card">
                    <h4><?php _e('📢 Announcements', 'jblund-dealers'); ?></h4>
                    <p><?php _e('Publish updates and news visible to all dealers', 'jblund-dealers'); ?></p>
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
                <li><?php _e('<strong>Dealer Registration:</strong> Allow dealers to self-register and request access', 'jblund-dealers'); ?></li>
            </ul>
        </div>

        <div class="settings-section">
            <h3><?php _e('Need Help?', 'jblund-dealers'); ?></h3>
            <p><?php _e('For additional support or feature requests, please contact your site administrator.', 'jblund-dealers'); ?></p>
        </div>
        <?php
    }
}
