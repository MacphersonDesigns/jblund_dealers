<?php
/**
 * Visual Customizer Class
 *
 * Provides a live visual editor for dealer card styling with real-time preview
 *
 * @package JBLund_Dealers
 * @subpackage Visual_Customizer
 * @since 1.2.0
 */

namespace JBLund\VisualCustomizer;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Visual Customizer Class
 *
 * Manages the live visual editor interface with real-time preview
 */
class Visual_Customizer {

    /**
     * Constructor
     */
    public function __construct() {
        // Add customizer menu under Dealers
        \add_action('admin_menu', array($this, 'add_customizer_menu'), 20);

        // Enqueue admin assets
        \add_action('admin_enqueue_scripts', array($this, 'enqueue_customizer_assets'));

        // AJAX handlers
        \add_action('wp_ajax_save_customizer_settings', array($this, 'save_customizer_settings'));
        \add_action('wp_ajax_reset_customizer_settings', array($this, 'reset_customizer_settings'));
        \add_action('wp_ajax_get_preview_html', array($this, 'get_preview_html'));
    }

    /**
     * Add customizer menu
     */
    public function add_customizer_menu() {
        \add_submenu_page(
            'edit.php?post_type=dealer',
            \__('Visual Customizer', 'jblund-dealers'),
            \__('Visual Customizer', 'jblund-dealers'),
            'manage_options',
            'jblund-visual-customizer',
            array($this, 'render_customizer_page')
        );
    }

    /**
     * Enqueue customizer assets
     */
    public function enqueue_customizer_assets($hook) {
        // Only load on customizer page
        if ($hook !== 'dealer_page_jblund-visual-customizer') {
            return;
        }

        // Enqueue color picker
        \wp_enqueue_style('wp-color-picker');
        \wp_enqueue_script('wp-color-picker');

        // Enqueue customizer CSS
        \wp_enqueue_style(
            'jblund-customizer-css',
            JBLUND_DEALERS_PLUGIN_URL . 'modules/visual-customizer/assets/css/customizer.css',
            array(),
            JBLUND_DEALERS_VERSION . '.1'
        );

        // Enqueue customizer JS
        \wp_enqueue_script(
            'jblund-customizer-js',
            JBLUND_DEALERS_PLUGIN_URL . 'modules/visual-customizer/assets/js/customizer.js',
            array('jquery', 'wp-color-picker'),
            JBLUND_DEALERS_VERSION,
            true
        );

        // Localize script with AJAX URL and nonce
        \wp_localize_script('jblund-customizer-js', 'jblundCustomizer', array(
            'ajaxUrl' => \admin_url('admin-ajax.php'),
            'nonce' => \wp_create_nonce('jblund_customizer_nonce'),
            'strings' => array(
                'saved' => \__('Settings saved successfully!', 'jblund-dealers'),
                'reset' => \__('Settings reset to defaults!', 'jblund-dealers'),
                'resetConfirm' => \__('Are you sure you want to reset all settings to defaults? This cannot be undone.', 'jblund-dealers'),
                'error' => \__('An error occurred. Please try again.', 'jblund-dealers'),
            )
        ));

        // Enqueue dealer styles for preview
        \wp_enqueue_style(
            'jblund-dealers-preview',
            JBLUND_DEALERS_PLUGIN_URL . 'assets/css/dealers.css',
            array(),
            JBLUND_DEALERS_VERSION
        );
    }

    /**
     * Render customizer page
     */
    public function render_customizer_page() {
        // Get current settings
        $options = \get_option('jblund_dealers_settings', array());

        // Default values
        $defaults = $this->get_default_settings();
        $settings = \wp_parse_args($options, $defaults);

        ?>
        <div class="wrap jblund-visual-customizer">
            <h1><?php \_e('Visual Customizer', 'jblund-dealers'); ?></h1>
            <p class="description">
                <?php \_e('Design your dealer cards in real-time with live preview. Toggle between Visual and CSS modes.', 'jblund-dealers'); ?>
            </p>

            <div class="customizer-container">
                <!-- Left Panel: Controls -->
                <div class="customizer-controls">
                    <div class="controls-header">
                        <div class="mode-toggle">
                            <button type="button" class="mode-btn active" data-mode="visual">
                                <span class="dashicons dashicons-admin-customizer"></span>
                                <?php \_e('Visual', 'jblund-dealers'); ?>
                            </button>
                            <button type="button" class="mode-btn" data-mode="css">
                                <span class="dashicons dashicons-editor-code"></span>
                                <?php \_e('CSS', 'jblund-dealers'); ?>
                            </button>
                        </div>

                        <div class="control-actions">
                            <button type="button" class="button button-secondary" id="reset-settings">
                                <span class="dashicons dashicons-image-rotate"></span>
                                <?php \_e('Reset', 'jblund-dealers'); ?>
                            </button>
                            <button type="button" class="button button-primary" id="save-settings">
                                <span class="dashicons dashicons-yes"></span>
                                <?php \_e('Save Changes', 'jblund-dealers'); ?>
                            </button>
                        </div>
                    </div>

                    <!-- Visual Mode Controls -->
                    <div class="controls-body mode-content" data-mode="visual">
                        <?php $this->render_visual_controls($settings); ?>
                    </div>

                    <!-- CSS Mode Controls -->
                    <div class="controls-body mode-content" data-mode="css" style="display: none;">
                        <?php $this->render_css_editor($settings); ?>
                    </div>
                </div>

                <!-- Right Panel: Live Preview -->
                <div class="customizer-preview">
                    <div class="preview-header">
                        <h3><?php \_e('Live Preview', 'jblund-dealers'); ?></h3>
                        <div class="preview-actions">
                            <div class="device-toggle">
                                <button type="button" class="device-btn active" data-device="desktop" title="Desktop">
                                    <span class="dashicons dashicons-desktop"></span>
                                </button>
                                <button type="button" class="device-btn" data-device="tablet" title="Tablet">
                                    <span class="dashicons dashicons-tablet"></span>
                                </button>
                                <button type="button" class="device-btn" data-device="mobile" title="Mobile">
                                    <span class="dashicons dashicons-smartphone"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="preview-body">
                        <div class="preview-frame desktop">
                            <?php $this->render_preview_content($settings); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden input to store all settings as JSON -->
            <input type="hidden" id="customizer-settings" value="<?php echo \esc_attr(\wp_json_encode($settings)); ?>" />
        </div>
        <?php
    }

    /**
     * Render visual mode controls
     */
    private function render_visual_controls($settings) {
        ?>
        <div class="visual-controls">
            <!-- Colors Section -->
            <div class="control-section">
                <h3 class="section-title">
                    <span class="dashicons dashicons-art"></span>
                    <?php \_e('Colors', 'jblund-dealers'); ?>
                </h3>
                <div class="section-controls">
                    <?php $this->render_color_control('header_color', __('Card Header', 'jblund-dealers'), $settings); ?>
                    <?php $this->render_color_control('card_background', __('Card Background', 'jblund-dealers'), $settings); ?>
                    <?php $this->render_color_control('button_color', __('Button Color', 'jblund-dealers'), $settings); ?>
                    <?php $this->render_color_control('text_color', __('Primary Text', 'jblund-dealers'), $settings); ?>
                    <?php $this->render_color_control('secondary_text_color', __('Secondary Text', 'jblund-dealers'), $settings); ?>
                    <?php $this->render_color_control('border_color', __('Border', 'jblund-dealers'), $settings); ?>
                    <?php $this->render_color_control('button_text_color', __('Button Text', 'jblund-dealers'), $settings); ?>
                    <?php $this->render_color_control('icon_color', __('Icons', 'jblund-dealers'), $settings); ?>
                    <?php $this->render_color_control('link_color', __('Links', 'jblund-dealers'), $settings); ?>
                    <?php $this->render_color_control('hover_background', __('Hover Background', 'jblund-dealers'), $settings); ?>
                </div>
            </div>

            <!-- Typography Section -->
            <div class="control-section">
                <h3 class="section-title">
                    <span class="dashicons dashicons-editor-textcolor"></span>
                    <?php \_e('Typography', 'jblund-dealers'); ?>
                </h3>
                <div class="section-controls">
                    <?php $this->render_range_control('heading_font_size', __('Heading Size', 'jblund-dealers'), $settings, 16, 32, 1, 'px'); ?>
                    <?php $this->render_range_control('body_font_size', __('Body Size', 'jblund-dealers'), $settings, 12, 18, 1, 'px'); ?>
                    <?php $this->render_select_control('heading_font_weight', __('Heading Weight', 'jblund-dealers'), $settings, array(
                        'normal' => 'Normal',
                        '600' => 'Semi-Bold',
                        'bold' => 'Bold',
                        '800' => 'Extra Bold'
                    )); ?>
                    <?php $this->render_range_control('line_height', __('Line Height', 'jblund-dealers'), $settings, 1.2, 2.0, 0.1, ''); ?>
                </div>
            </div>

            <!-- Spacing Section -->
            <div class="control-section">
                <h3 class="section-title">
                    <span class="dashicons dashicons-align-center"></span>
                    <?php \_e('Spacing & Layout', 'jblund-dealers'); ?>
                </h3>
                <div class="section-controls">
                    <?php $this->render_select_control('preview_layout', __('Preview Layout', 'jblund-dealers'), $settings, array(
                        'grid' => 'Grid (Default)',
                        'list' => 'Horizontal List',
                        'compact' => 'Compact Grid'
                    )); ?>
                    <?php $this->render_range_control('card_padding', __('Card Padding', 'jblund-dealers'), $settings, 10, 40, 1, 'px'); ?>
                    <?php $this->render_range_control('card_margin', __('Card Margin', 'jblund-dealers'), $settings, 10, 30, 1, 'px'); ?>
                    <?php $this->render_range_control('grid_gap', __('Grid Gap', 'jblund-dealers'), $settings, 15, 50, 1, 'px'); ?>
                    <?php $this->render_range_control('border_radius', __('Border Radius', 'jblund-dealers'), $settings, 0, 20, 1, 'px'); ?>
                    <?php $this->render_range_control('border_width', __('Border Width', 'jblund-dealers'), $settings, 0, 5, 1, 'px'); ?>
                    <?php $this->render_select_control('border_style', __('Border Style', 'jblund-dealers'), $settings, array(
                        'solid' => 'Solid',
                        'dashed' => 'Dashed',
                        'dotted' => 'Dotted',
                        'none' => 'None'
                    )); ?>
                </div>
            </div>

            <!-- Effects Section -->
            <div class="control-section">
                <h3 class="section-title">
                    <span class="dashicons dashicons-admin-appearance"></span>
                    <?php \_e('Effects', 'jblund-dealers'); ?>
                </h3>
                <div class="section-controls">
                    <?php $this->render_select_control('box_shadow', __('Box Shadow', 'jblund-dealers'), $settings, array(
                        'none' => 'None',
                        'light' => 'Light',
                        'medium' => 'Medium',
                        'heavy' => 'Heavy'
                    )); ?>
                    <?php $this->render_select_control('hover_effect', __('Hover Effect', 'jblund-dealers'), $settings, array(
                        'none' => 'None',
                        'lift' => 'Lift Up',
                        'scale' => 'Scale',
                        'shadow' => 'Shadow'
                    )); ?>
                    <?php $this->render_range_control('transition_speed', __('Transition Speed', 'jblund-dealers'), $settings, 0.1, 0.5, 0.1, 's'); ?>
                    <?php $this->render_range_control('icon_size', __('Icon Size', 'jblund-dealers'), $settings, 16, 32, 1, 'px'); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render CSS editor mode
     */
    private function render_css_editor($settings) {
        $custom_css = isset($settings['custom_css']) ? $settings['custom_css'] : '';
        ?>
        <div class="css-editor-container">
            <!-- HTML Structure Reference -->
            <div class="html-structure-section">
                <div class="editor-header">
                    <h3><?php \_e('HTML Structure Reference', 'jblund-dealers'); ?></h3>
                    <button type="button" class="button button-small" id="copy-html">
                        <span class="dashicons dashicons-clipboard"></span>
                        <?php \_e('Copy HTML', 'jblund-dealers'); ?>
                    </button>
                </div>
                <div class="html-code-display">
                    <pre><code class="language-html"><?php echo \esc_html($this->get_dealer_card_html_structure()); ?></code></pre>
                </div>
                <p class="description">
                    <?php \_e('This shows the actual HTML structure of dealer cards as they appear on the frontend. Use these class names in your CSS below.', 'jblund-dealers'); ?>
                </p>
            </div>

            <!-- CSS Editor -->
            <div class="css-editor-section">
                <div class="editor-header">
                    <h3><?php \_e('Custom CSS', 'jblund-dealers'); ?></h3>
                    <button type="button" class="button button-small" id="copy-css">
                        <span class="dashicons dashicons-clipboard"></span>
                        <?php \_e('Copy CSS', 'jblund-dealers'); ?>
                    </button>
                </div>
                <textarea id="custom-css-editor" name="custom_css" rows="15"><?php echo \esc_textarea($custom_css); ?></textarea>
                <p class="description">
                    <?php \_e('Add custom CSS to further customize dealer cards. Changes will apply immediately in the preview. Use the HTML structure above as reference.', 'jblund-dealers'); ?>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Render color picker control
     */
    private function render_color_control($name, $label, $settings) {
        $value = isset($settings[$name]) ? $settings[$name] : '';
        ?>
        <div class="control-item control-color">
            <label><?php echo \esc_html($label); ?></label>
            <input type="text" class="color-picker" name="<?php echo \esc_attr($name); ?>" value="<?php echo \esc_attr($value); ?>" data-default-color="<?php echo \esc_attr($value); ?>" />
        </div>
        <?php
    }

    /**
     * Render range slider control
     */
    private function render_range_control($name, $label, $settings, $min, $max, $step, $unit) {
        $value = isset($settings[$name]) ? $settings[$name] : $min;
        ?>
        <div class="control-item control-range">
            <label>
                <?php echo \esc_html($label); ?>
                <span class="range-value"><?php echo \esc_html($value . $unit); ?></span>
            </label>
            <input type="range" class="range-slider" name="<?php echo \esc_attr($name); ?>" value="<?php echo \esc_attr($value); ?>" min="<?php echo \esc_attr($min); ?>" max="<?php echo \esc_attr($max); ?>" step="<?php echo \esc_attr($step); ?>" data-unit="<?php echo \esc_attr($unit); ?>" />
        </div>
        <?php
    }

    /**
     * Render select dropdown control
     */
    private function render_select_control($name, $label, $settings, $options) {
        $value = isset($settings[$name]) ? $settings[$name] : '';
        ?>
        <div class="control-item control-select">
            <label><?php echo \esc_html($label); ?></label>
            <select name="<?php echo \esc_attr($name); ?>" class="select-control">
                <?php foreach ($options as $opt_value => $opt_label): ?>
                    <option value="<?php echo \esc_attr($opt_value); ?>" <?php \selected($value, $opt_value); ?>>
                        <?php echo \esc_html($opt_label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
    }

    /**
     * Render preview content with sample dealers
     */
    private function render_preview_content($settings) {
        $layout = isset($settings['preview_layout']) ? $settings['preview_layout'] : 'grid';
        $layout_class = 'layout-' . $layout;
        ?>
        <div class="dealers-grid <?php echo \esc_attr($layout_class); ?>">
            <?php
            // Sample dealer data
            $sample_dealers = $this->get_sample_dealers();

            foreach ($sample_dealers as $dealer) {
                $this->render_dealer_card($dealer);
            }
            ?>
        </div>

        <style id="preview-custom-css">
            <?php echo $this->generate_preview_css($settings); ?>
        </style>
        <?php
    }

    /**
     * Render a single dealer card for preview
     */
    private function render_dealer_card($dealer) {
        ?>
        <div class="dealer-card">
            <div class="dealer-card-header">
                <h3><?php echo \esc_html($dealer['name']); ?></h3>
            </div>
            <div class="dealer-card-body">
                <div class="dealer-card-address">
                    <span class="dashicons dashicons-location"></span>
                    <?php echo \esc_html($dealer['address']); ?>
                </div>
                <div class="dealer-card-phone">
                    <span class="dashicons dashicons-phone"></span>
                    <a href="tel:<?php echo \esc_attr($dealer['phone']); ?>"><?php echo \esc_html($dealer['phone']); ?></a>
                </div>
                <?php if (!empty($dealer['website'])): ?>
                    <div class="dealer-card-website">
                        <a href="<?php echo \esc_url($dealer['website']); ?>" class="dealer-website-button" target="_blank">
                            <?php \_e('Visit Website', 'jblund-dealers'); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (!empty($dealer['services'])): ?>
                    <div class="dealer-services-icons">
                        <?php foreach ($dealer['services'] as $service): ?>
                            <span title="<?php echo \esc_attr($service); ?>">
                                <?php echo $this->get_service_icon($service); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($dealer['sublocations'])): ?>
                    <div class="dealer-sublocations">
                        <h4><?php \_e('Additional Locations', 'jblund-dealers'); ?></h4>
                        <?php foreach ($dealer['sublocations'] as $sublocation): ?>
                            <div class="dealer-sublocation">
                                <?php if (!empty($sublocation['name'])): ?>
                                    <div class="sublocation-name"><?php echo \esc_html($sublocation['name']); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($sublocation['address'])): ?>
                                    <div class="dealer-card-address">
                                        <span class="dashicons dashicons-location"></span>
                                        <?php echo \esc_html($sublocation['address']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($sublocation['phone'])): ?>
                                    <div class="dealer-card-phone">
                                        <span class="dashicons dashicons-phone"></span>
                                        <a href="tel:<?php echo \esc_attr($sublocation['phone']); ?>"><?php echo \esc_html($sublocation['phone']); ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($sublocation['website'])): ?>
                                    <div class="dealer-card-website">
                                        <a href="<?php echo \esc_url($sublocation['website']); ?>" class="dealer-website-button" target="_blank">
                                            <?php \_e('Visit Website', 'jblund-dealers'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($sublocation['services'])): ?>
                                    <div class="dealer-services-icons">
                                        <?php foreach ($sublocation['services'] as $service): ?>
                                            <span title="<?php echo \esc_attr($service); ?>">
                                                <?php echo $this->get_service_icon($service); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Get sample dealer data
     */
    private function get_sample_dealers() {
        return array(
            array(
                'name' => 'Lakeshore Dock & Lift',
                'address' => '123 Marina Drive, Minneapolis, MN 55401',
                'phone' => '(612) 555-0123',
                'website' => 'https://example.com',
                'services' => array('Docks', 'Lifts', 'Trailers'),
                'sublocations' => array(
                    array(
                        'name' => 'Outlet Dock and Trailer',
                        'address' => '12034 CTY RD #4, Lake Park, MN 56554',
                        'phone' => '(701) 793-7900',
                        'website' => 'https://example.com',
                        'services' => array('Docks', 'Lifts', 'Trailers')
                    )
                )
            ),
            array(
                'name' => 'North Bay Marine',
                'address' => '456 Harbor Way, Duluth, MN 55802',
                'phone' => '(218) 555-0456',
                'website' => 'https://example.com',
                'services' => array('Docks', 'Lifts'),
                'sublocations' => array(
                    array(
                        'name' => 'North Bay - Warehouse',
                        'address' => '789 Industrial Pkwy, Duluth, MN 55802',
                        'phone' => '(218) 555-0457',
                        'website' => '',
                        'services' => array('Trailers')
                    ),
                    array(
                        'name' => 'North Bay - Downtown',
                        'address' => '321 Lake Ave, Duluth, MN 55802',
                        'phone' => '(218) 555-0458',
                        'website' => '',
                        'services' => array('Docks')
                    )
                )
            ),
            array(
                'name' => 'Superior Docks',
                'address' => '789 Waterfront Rd, St. Paul, MN 55101',
                'phone' => '(651) 555-0789',
                'website' => '',
                'services' => array('Docks')
            ),
        );
    }

    /**
     * Get service icon
     */
    private function get_service_icon($service) {
        $icons = array(
            'Docks' => '🚢',
            'Lifts' => '⚓',
            'Trailers' => '🚛'
        );

        return isset($icons[$service]) ? $icons[$service] . ' ' . $service : $service;
    }

    /**
     * Get dealer card HTML structure for CSS mode reference
     */
    private function get_dealer_card_html_structure() {
        $html = <<<'HTML'
<!-- Single Dealer Card Structure -->
<div class="dealer-card">

    <!-- Card Header -->
    <div class="dealer-card-header">
        <h3>Company Name Here</h3>
    </div>

    <!-- Card Body -->
    <div class="dealer-card-body">

        <!-- Address -->
        <div class="dealer-card-address">
            <span class="dashicons dashicons-location"></span>
            123 Main Street, City, State 12345
        </div>

        <!-- Phone -->
        <div class="dealer-card-phone">
            <span class="dashicons dashicons-phone"></span>
            <a href="tel:1234567890">(123) 456-7890</a>
        </div>

        <!-- Website Button -->
        <div class="dealer-card-website">
            <a href="https://example.com" class="dealer-website-button" target="_blank">
                Visit Website
            </a>
        </div>

        <!-- Services Icons -->
        <div class="dealer-services-icons">
            <span title="Docks">🚢 Docks</span>
            <span title="Lifts">⚓ Lifts</span>
            <span title="Trailers">🚛 Trailers</span>
        </div>

        <!-- Sub-Locations (if any) -->
        <div class="dealer-sublocations">
            <h4>Additional Locations</h4>

            <div class="dealer-sublocation">
                <div class="sublocation-name">Branch Location Name</div>
                <div class="dealer-card-address">
                    <span class="dashicons dashicons-location"></span>
                    456 Branch St, City, State 12345
                </div>
                <div class="dealer-card-phone">
                    <span class="dashicons dashicons-phone"></span>
                    <a href="tel:1234567890">(123) 456-7890</a>
                </div>
                <div class="dealer-card-website">
                    <a href="https://example.com" class="dealer-website-button" target="_blank">
                        Visit Website
                    </a>
                </div>
                <div class="dealer-services-icons">
                    <span title="Docks">🚢 Docks</span>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Grid Container (wraps all cards) -->
<div class="dealers-grid">
    <!-- Multiple .dealer-card elements go here -->
</div>

<!-- Available CSS Classes -->
/*
Main Container:
  .dealers-grid              - Grid container for all cards

Card Structure:
  .dealer-card               - Main card wrapper
  .dealer-card-header        - Header section (colored bar)
  .dealer-card-body          - Main content area

Content Elements:
  .dealer-card-address       - Address line with icon
  .dealer-card-phone         - Phone line with icon
  .dealer-card-phone a       - Phone link
  .dealer-card-website       - Website button container
  .dealer-website-button     - Actual button/link
  .dealer-services-icons     - Services container
  .dealer-services-icons span - Individual service item

Sub-Locations:
  .dealer-sublocations       - Container for all sub-locations
  .dealer-sublocations h4    - "Additional Locations" heading
  .dealer-sublocation        - Individual sub-location wrapper
  .sublocation-name          - Sub-location name/title

Icons:
  .dashicons                 - WordPress icon font
  .dashicons-location        - Location pin icon
  .dashicons-phone          - Phone icon

State Classes:
  .dealer-card:hover         - Card hover state
  .dealer-website-button:hover - Button hover state
*/
HTML;

        return $html;
    }

    /**
     * Generate preview CSS from settings
     */
    private function generate_preview_css($settings) {
        $css = '';

        // Colors
        if (!empty($settings['header_color'])) {
            $css .= ".dealer-card-header { background: {$settings['header_color']} !important; }\n";
        }
        if (!empty($settings['card_background'])) {
            $css .= ".dealer-card { background: {$settings['card_background']} !important; }\n";
        }
        if (!empty($settings['button_color'])) {
            $css .= ".dealer-website-button { background: {$settings['button_color']} !important; }\n";
        }
        if (!empty($settings['text_color'])) {
            $css .= ".dealer-card h3 { color: {$settings['text_color']} !important; }\n";
        }
        if (!empty($settings['secondary_text_color'])) {
            $css .= ".dealer-card-address, .dealer-card-phone { color: {$settings['secondary_text_color']} !important; }\n";
        }
        if (!empty($settings['border_color'])) {
            $css .= ".dealer-card { border-color: {$settings['border_color']} !important; }\n";
        }
        if (!empty($settings['button_text_color'])) {
            $css .= ".dealer-website-button { color: {$settings['button_text_color']} !important; }\n";
        }
        if (!empty($settings['icon_color'])) {
            $css .= ".dealer-services-icons { color: {$settings['icon_color']} !important; }\n";
        }
        if (!empty($settings['link_color'])) {
            $css .= ".dealer-card a { color: {$settings['link_color']} !important; }\n";
        }
        if (!empty($settings['hover_background'])) {
            $css .= ".dealer-card:hover { background: {$settings['hover_background']} !important; }\n";
        }

        // Typography
        if (!empty($settings['heading_font_size'])) {
            $css .= ".dealer-card h3 { font-size: {$settings['heading_font_size']}px !important; }\n";
        }
        if (!empty($settings['body_font_size'])) {
            $css .= ".dealer-card-address, .dealer-card-phone { font-size: {$settings['body_font_size']}px !important; }\n";
        }
        if (!empty($settings['heading_font_weight'])) {
            $css .= ".dealer-card h3 { font-weight: {$settings['heading_font_weight']} !important; }\n";
        }
        if (!empty($settings['line_height'])) {
            $css .= ".dealer-card p, .dealer-card span { line-height: {$settings['line_height']} !important; }\n";
        }

        // Spacing
        if (isset($settings['card_padding'])) {
            $css .= ".dealer-card { padding: {$settings['card_padding']}px !important; }\n";
        }
        if (isset($settings['card_margin'])) {
            $css .= ".dealer-card { margin: {$settings['card_margin']}px !important; }\n";
        }
        if (!empty($settings['grid_gap'])) {
            $css .= ".dealers-grid { gap: {$settings['grid_gap']}px !important; }\n";
        }
        if (isset($settings['border_radius'])) {
            $css .= ".dealer-card { border-radius: {$settings['border_radius']}px !important; }\n";
        }
        if (isset($settings['border_width'])) {
            $css .= ".dealer-card { border-width: {$settings['border_width']}px !important; }\n";
        }
        if (!empty($settings['border_style'])) {
            $css .= ".dealer-card { border-style: {$settings['border_style']} !important; }\n";
        }

        // Effects
        if (!empty($settings['box_shadow'])) {
            $shadows = array(
                'none' => 'none',
                'light' => '0 2px 4px rgba(0,0,0,0.1)',
                'medium' => '0 4px 8px rgba(0,0,0,0.15)',
                'heavy' => '0 8px 16px rgba(0,0,0,0.2)'
            );
            if (isset($shadows[$settings['box_shadow']])) {
                $css .= ".dealer-card { box-shadow: {$shadows[$settings['box_shadow']]} !important; }\n";
            }
        }

        if (!empty($settings['hover_effect'])) {
            switch ($settings['hover_effect']) {
                case 'lift':
                    $css .= ".dealer-card:hover { transform: translateY(-5px) !important; }\n";
                    break;
                case 'scale':
                    $css .= ".dealer-card:hover { transform: scale(1.02) !important; }\n";
                    break;
                case 'shadow':
                    $css .= ".dealer-card:hover { box-shadow: 0 12px 24px rgba(0,0,0,0.25) !important; }\n";
                    break;
            }
        }

        if (!empty($settings['transition_speed'])) {
            $css .= ".dealer-card { transition: all {$settings['transition_speed']}s ease !important; }\n";
        }

        if (!empty($settings['icon_size'])) {
            $css .= ".dealer-services-icons { font-size: {$settings['icon_size']}px !important; }\n";
        }

        // Custom CSS
        if (!empty($settings['custom_css'])) {
            $css .= "\n/* Custom CSS */\n" . $settings['custom_css'] . "\n";
        }

        return $css;
    }

    /**
     * Get default settings
     */
    private function get_default_settings() {
        return array(
            'header_color' => '#0073aa',
            'card_background' => '#ffffff',
            'button_color' => '#0073aa',
            'text_color' => '#333333',
            'secondary_text_color' => '#666666',
            'border_color' => '#dddddd',
            'button_text_color' => '#ffffff',
            'icon_color' => '#0073aa',
            'link_color' => '#0073aa',
            'hover_background' => '#f9f9f9',
            'heading_font_size' => '24',
            'body_font_size' => '14',
            'heading_font_weight' => 'bold',
            'line_height' => '1.6',
            'preview_layout' => 'grid',
            'card_padding' => '20',
            'card_margin' => '15',
            'grid_gap' => '20',
            'border_radius' => '8',
            'border_width' => '1',
            'border_style' => 'solid',
            'box_shadow' => 'light',
            'hover_effect' => 'lift',
            'transition_speed' => '0.3',
            'icon_size' => '24',
            'custom_css' => ''
        );
    }

    /**
     * AJAX: Save customizer settings
     */
    public function save_customizer_settings() {
        // Security check
        \check_ajax_referer('jblund_customizer_nonce', 'nonce');

        if (!\current_user_can('manage_options')) {
            \wp_send_json_error(array('message' => \__('Insufficient permissions', 'jblund-dealers')));
        }

        // Get settings from POST
        $settings = isset($_POST['settings']) ? $_POST['settings'] : array();

        // Sanitize settings
        $sanitized = array();
        foreach ($settings as $key => $value) {
            if ($key === 'custom_css') {
                $sanitized[$key] = \wp_kses_post($value);
            } else {
                $sanitized[$key] = \sanitize_text_field($value);
            }
        }

        // Save to database
        \update_option('jblund_dealers_settings', $sanitized);

        \wp_send_json_success(array(
            'message' => \__('Settings saved successfully!', 'jblund-dealers')
        ));
    }

    /**
     * AJAX: Reset customizer settings
     */
    public function reset_customizer_settings() {
        // Security check
        \check_ajax_referer('jblund_customizer_nonce', 'nonce');

        if (!\current_user_can('manage_options')) {
            \wp_send_json_error(array('message' => \__('Insufficient permissions', 'jblund-dealers')));
        }

        // Get defaults
        $defaults = $this->get_default_settings();

        // Save defaults
        \update_option('jblund_dealers_settings', $defaults);

        \wp_send_json_success(array(
            'message' => \__('Settings reset to defaults!', 'jblund-dealers'),
            'settings' => $defaults
        ));
    }

    /**
     * AJAX: Get preview HTML
     */
    public function get_preview_html() {
        // Security check
        \check_ajax_referer('jblund_customizer_nonce', 'nonce');

        // Get settings from POST
        $settings = isset($_POST['settings']) ? $_POST['settings'] : array();

        // Start output buffering
        \ob_start();
        $this->render_preview_content($settings);
        $html = \ob_get_clean();

        \wp_send_json_success(array('html' => $html));
    }
}
