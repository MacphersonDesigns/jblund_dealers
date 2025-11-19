<?php
/**
 * Settings Manager
 *
 * Handles all plugin settings registration and rendering
 *
 * @package JBLund_Dealers
 * @subpackage Admin
 */

namespace JBLund\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings Class
 *
 * Manages plugin settings and options pages
 */
class Settings {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Register all plugin settings
     */
    public function register_settings() {
        register_setting('jblund_dealers_settings', 'jblund_dealers_settings', array($this, 'sanitize_settings'));
        register_setting('jblund_dealers_portal_pages', 'jblund_dealers_portal_pages');

        $this->register_appearance_settings();
        $this->register_shortcode_settings();
        $this->register_portal_settings();
        $this->register_representative_settings();
        $this->register_documents_settings();
    }

    /**
     * Register appearance settings
     */
    private function register_appearance_settings() {
        // Appearance Section
        add_settings_section(
            'jblund_dealers_appearance',
            __('Appearance Settings', 'jblund-dealers'),
            array($this, 'appearance_section_callback'),
            'jblund_dealers_settings'
        );

        // Color Fields
        $color_fields = array(
            'header_color' => array('label' => __('Card Header Color', 'jblund-dealers'), 'default' => '#0073aa'),
            'card_background' => array('label' => __('Card Background Color', 'jblund-dealers'), 'default' => '#ffffff'),
            'button_color' => array('label' => __('Button Color', 'jblund-dealers'), 'default' => '#0073aa'),
            'text_color' => array('label' => __('Primary Text Color', 'jblund-dealers'), 'default' => '#333333'),
            'secondary_text_color' => array('label' => __('Secondary Text Color', 'jblund-dealers'), 'default' => '#666666'),
            'border_color' => array('label' => __('Border Color', 'jblund-dealers'), 'default' => '#dddddd'),
            'button_text_color' => array('label' => __('Button Text Color', 'jblund-dealers'), 'default' => '#ffffff'),
            'icon_color' => array('label' => __('Icon Color', 'jblund-dealers'), 'default' => '#0073aa'),
            'link_color' => array('label' => __('Link Color', 'jblund-dealers'), 'default' => '#0073aa'),
            'hover_background' => array('label' => __('Card Hover Background', 'jblund-dealers'), 'default' => '#f9f9f9'),
        );

        foreach ($color_fields as $field => $args) {
            add_settings_field(
                $field,
                $args['label'],
                array($this, 'color_field_callback'),
                'jblund_dealers_settings',
                'jblund_dealers_appearance',
                array('field' => $field, 'default' => $args['default'])
            );
        }

        // Typography Fields
        add_settings_field(
            'heading_font_size',
            __('Heading Font Size', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'heading_font_size', 'default' => '24', 'min' => '16', 'max' => '32', 'unit' => 'px')
        );

        add_settings_field(
            'body_font_size',
            __('Body Font Size', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'body_font_size', 'default' => '14', 'min' => '12', 'max' => '18', 'unit' => 'px')
        );

        add_settings_field(
            'heading_font_weight',
            __('Heading Font Weight', 'jblund-dealers'),
            array($this, 'select_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array(
                'field' => 'heading_font_weight',
                'options' => array(
                    'normal' => __('Normal', 'jblund-dealers'),
                    '600' => __('Semi-Bold (600)', 'jblund-dealers'),
                    'bold' => __('Bold (700)', 'jblund-dealers'),
                    '800' => __('Extra Bold (800)', 'jblund-dealers')
                ),
                'default' => 'bold'
            )
        );

        add_settings_field(
            'line_height',
            __('Line Height', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'line_height', 'default' => '1.6', 'min' => '1.2', 'max' => '2.0', 'step' => '0.1', 'unit' => '')
        );

        // Spacing Fields
        $spacing_fields = array(
            'card_padding' => array('label' => __('Card Padding', 'jblund-dealers'), 'default' => '20', 'min' => '10', 'max' => '40'),
            'card_margin' => array('label' => __('Card Margin', 'jblund-dealers'), 'default' => '15', 'min' => '10', 'max' => '30'),
            'grid_gap' => array('label' => __('Grid Gap', 'jblund-dealers'), 'default' => '20', 'min' => '15', 'max' => '50'),
            'border_radius' => array('label' => __('Border Radius', 'jblund-dealers'), 'default' => '8', 'min' => '0', 'max' => '20'),
            'border_width' => array('label' => __('Border Width', 'jblund-dealers'), 'default' => '1', 'min' => '0', 'max' => '5'),
        );

        foreach ($spacing_fields as $field => $args) {
            add_settings_field(
                $field,
                $args['label'],
                array($this, 'range_field_callback'),
                'jblund_dealers_settings',
                'jblund_dealers_appearance',
                array('field' => $field, 'default' => $args['default'], 'min' => $args['min'], 'max' => $args['max'], 'unit' => 'px')
            );
        }

        add_settings_field(
            'border_style',
            __('Border Style', 'jblund-dealers'),
            array($this, 'select_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array(
                'field' => 'border_style',
                'options' => array(
                    'solid' => __('Solid', 'jblund-dealers'),
                    'dashed' => __('Dashed', 'jblund-dealers'),
                    'dotted' => __('Dotted', 'jblund-dealers'),
                    'none' => __('None', 'jblund-dealers')
                ),
                'default' => 'solid'
            )
        );

        // Effects Fields
        add_settings_field(
            'box_shadow',
            __('Box Shadow', 'jblund-dealers'),
            array($this, 'select_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array(
                'field' => 'box_shadow',
                'options' => array(
                    'none' => __('None', 'jblund-dealers'),
                    'light' => __('Light', 'jblund-dealers'),
                    'medium' => __('Medium', 'jblund-dealers'),
                    'heavy' => __('Heavy', 'jblund-dealers')
                ),
                'default' => 'light'
            )
        );

        add_settings_field(
            'hover_effect',
            __('Hover Effect', 'jblund-dealers'),
            array($this, 'select_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array(
                'field' => 'hover_effect',
                'options' => array(
                    'none' => __('None', 'jblund-dealers'),
                    'lift' => __('Lift Up', 'jblund-dealers'),
                    'scale' => __('Scale', 'jblund-dealers'),
                    'shadow' => __('Shadow Increase', 'jblund-dealers')
                ),
                'default' => 'lift'
            )
        );

        add_settings_field(
            'transition_speed',
            __('Transition Speed', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'transition_speed', 'default' => '0.3', 'min' => '0.1', 'max' => '0.5', 'step' => '0.1', 'unit' => 's')
        );

        add_settings_field(
            'icon_size',
            __('Icon Size', 'jblund-dealers'),
            array($this, 'range_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'icon_size', 'default' => '24', 'min' => '16', 'max' => '32', 'unit' => 'px')
        );

        // Custom CSS Field
        add_settings_field(
            'custom_css',
            __('Custom CSS', 'jblund-dealers'),
            array($this, 'textarea_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_appearance',
            array('field' => 'custom_css', 'default' => '')
        );
    }

    /**
     * Register shortcode settings
     */
    private function register_shortcode_settings() {
        add_settings_section(
            'jblund_dealers_shortcode',
            __('Shortcode Settings', 'jblund-dealers'),
            array($this, 'shortcode_section_callback'),
            'jblund_dealers_settings'
        );

        add_settings_field(
            'default_layout',
            __('Default Layout', 'jblund-dealers'),
            array($this, 'select_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_shortcode',
            array(
                'field' => 'default_layout',
                'options' => array(
                    'grid' => __('Grid Layout', 'jblund-dealers'),
                    'list' => __('List Layout', 'jblund-dealers'),
                    'compact' => __('Compact Grid', 'jblund-dealers')
                ),
                'default' => 'grid'
            )
        );

        add_settings_field(
            'use_icons',
            __('Use Icons for Services', 'jblund-dealers'),
            array($this, 'checkbox_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_shortcode',
            array('field' => 'use_icons', 'default' => '1')
        );
    }

    /**
     * Register portal updates settings
     */
    private function register_portal_settings() {
        add_settings_section(
            'jblund_dealers_portal_updates',
            __('Dealer Portal Updates', 'jblund-dealers'),
            array($this, 'portal_updates_section_callback'),
            'jblund_dealers_settings'
        );

        add_settings_field(
            'portal_updates',
            __('Recent Updates', 'jblund-dealers'),
            array($this, 'portal_updates_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_portal_updates'
        );
    }

    /**
     * Register dealer representative settings
     */
    private function register_representative_settings() {
        add_settings_section(
            'jblund_dealers_representative',
            __('Dealer Representative', 'jblund-dealers'),
            array($this, 'representative_section_callback'),
            'jblund_dealers_settings'
        );

        add_settings_field(
            'representative_name',
            __('Representative Name', 'jblund-dealers'),
            array($this, 'text_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_representative',
            array('field' => 'representative_name', 'default' => 'Jim Johnson')
        );

        add_settings_field(
            'representative_email',
            __('Representative Email', 'jblund-dealers'),
            array($this, 'text_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_representative',
            array('field' => 'representative_email', 'default' => 'jim@jblund.com')
        );

        add_settings_field(
            'representative_phone',
            __('Representative Phone', 'jblund-dealers'),
            array($this, 'text_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_representative',
            array('field' => 'representative_phone', 'default' => '(555) 123-4567')
        );
    }

    /**
     * Register required documents settings
     */
    private function register_documents_settings() {
        add_settings_section(
            'jblund_dealers_required_documents',
            __('Required Documents', 'jblund-dealers'),
            array($this, 'required_documents_section_callback'),
            'jblund_dealers_settings'
        );

        add_settings_field(
            'required_documents',
            __('Documents to Complete', 'jblund-dealers'),
            array($this, 'required_documents_field_callback'),
            'jblund_dealers_settings',
            'jblund_dealers_required_documents'
        );
    }

    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $existing = get_option('jblund_dealers_settings', array());

        $checkbox_fields = array('inherit_theme_styles', 'use_icons');

        foreach ($checkbox_fields as $field) {
            if (array_key_exists($field, $existing) && !array_key_exists($field, $input)) {
                if ($field === 'inherit_theme_styles' && (
                    array_key_exists('header_color', $input) ||
                    array_key_exists('card_background', $input) ||
                    array_key_exists('custom_css', $input)
                )) {
                    $input[$field] = '0';
                } elseif ($field === 'use_icons' && array_key_exists('default_layout', $input)) {
                    $input[$field] = '0';
                }
            }
        }

        return array_merge($existing, $input);
    }

    /**
     * Section callbacks
     */
    public function appearance_section_callback() {
        echo '<p>' . __('Customize the appearance of dealer cards on your website.', 'jblund-dealers') . '</p>';
    }

    public function shortcode_section_callback() {
        echo '<p>' . __('Configure default shortcode behavior and layout options.', 'jblund-dealers') . '</p>';
    }

    public function portal_updates_section_callback() {
        echo '<p>' . __('Manage updates that appear on the dealer dashboard "Recent Updates" card. Add announcements, news, or important information for dealers.', 'jblund-dealers') . '</p>';
    }

    public function representative_section_callback() {
        echo '<p>' . __('Configure the dealer representative contact information shown on the dealer dashboard.', 'jblund-dealers') . '</p>';
    }

    public function required_documents_section_callback() {
        echo '<p>' . __('Manage documents that dealers need to complete. These appear in the "Documents to Complete" section on the dealer dashboard.', 'jblund-dealers') . '</p>';
    }

    /**
     * Field callbacks
     */
    public function color_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<input type="color" name="jblund_dealers_settings[' . $args['field'] . ']" value="' . esc_attr($value) . '" />';
    }

    public function text_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : (isset($args['default']) ? $args['default'] : '');
        echo '<input type="text" name="jblund_dealers_settings[' . $args['field'] . ']" value="' . esc_attr($value) . '" class="regular-text" />';
        if (isset($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    public function select_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];

        echo '<select name="jblund_dealers_settings[' . $args['field'] . ']">';
        foreach ($args['options'] as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($value, $key, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    public function checkbox_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<input type="checkbox" name="jblund_dealers_settings[' . $args['field'] . ']" value="1" ' . checked($value, '1', false) . ' />';
    }

    public function range_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        $step = isset($args['step']) ? $args['step'] : '1';
        ?>
        <input type="range"
               name="jblund_dealers_settings[<?php echo $args['field']; ?>]"
               min="<?php echo $args['min']; ?>"
               max="<?php echo $args['max']; ?>"
               step="<?php echo $step; ?>"
               value="<?php echo esc_attr($value); ?>"
               oninput="this.nextElementSibling.value = this.value" />
        <output><?php echo esc_attr($value); ?></output>
        <?php if (isset($args['unit']) && !empty($args['unit'])) : ?>
            <span class="unit"><?php echo esc_html($args['unit']); ?></span>
        <?php endif; ?>
        <?php
    }

    public function textarea_field_callback($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<textarea name="jblund_dealers_settings[' . $args['field'] . ']" rows="10" class="large-text code">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . __('Add custom CSS to override default styles.', 'jblund-dealers') . '</p>';
    }

    /**
     * Portal updates field callback
     */
    public function portal_updates_field_callback() {
        $options = get_option('jblund_dealers_settings');
        $updates = isset($options['portal_updates']) ? $options['portal_updates'] : array();

        if (!is_array($updates)) {
            $updates = array();
        }
        ?>
        <div id="portal-updates-manager">
            <div id="updates-list" style="margin-bottom: 20px;">
                <?php if (!empty($updates)) : ?>
                    <?php foreach ($updates as $index => $update) : ?>
                        <?php $this->render_update_row($index, $update); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" class="button button-secondary" id="add-update">
                <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
                <?php _e('Add Update', 'jblund-dealers'); ?>
            </button>

            <p class="description" style="margin-top: 15px;">
                <?php _e('Updates appear on the dealer dashboard in reverse chronological order. They can be scheduled with start and end dates.', 'jblund-dealers'); ?>
            </p>
        </div>

        <?php $this->render_updates_javascript(count($updates)); ?>
        <?php $this->render_updates_styles(); ?>
        <?php
    }

    /**
     * Render update row
     */
    private function render_update_row($index, $update) {
        $title = isset($update['title']) ? $update['title'] : '';
        $message = isset($update['message']) ? $update['message'] : '';
        $start_date = isset($update['start_date']) ? $update['start_date'] : '';
        $end_date = isset($update['end_date']) ? $update['end_date'] : '';
        ?>
        <div class="update-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
            <button type="button" class="button button-link-delete remove-update" style="position: absolute; top: 10px; right: 10px; color: #dc3232;">
                <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'jblund-dealers'); ?>
            </button>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Update Title:', 'jblund-dealers'); ?></strong>
                </label>
                <input type="text"
                       name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][title]"
                       value="<?php echo esc_attr($title); ?>"
                       class="large-text"
                       placeholder="<?php _e('e.g., New Product Launch, Price Update, etc.', 'jblund-dealers'); ?>" />
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Message:', 'jblund-dealers'); ?></strong>
                </label>
                <textarea name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][message]"
                          rows="3"
                          class="large-text"
                          placeholder="<?php _e('Enter your update message here...', 'jblund-dealers'); ?>"><?php echo esc_textarea($message); ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px;">
                        <strong><?php _e('Start Date (Optional):', 'jblund-dealers'); ?></strong>
                    </label>
                    <input type="date"
                           name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][start_date]"
                           value="<?php echo esc_attr($start_date); ?>"
                           class="regular-text" />
                    <p class="description"><?php _e('Update will appear starting from this date', 'jblund-dealers'); ?></p>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px;">
                        <strong><?php _e('End Date (Optional):', 'jblund-dealers'); ?></strong>
                    </label>
                    <input type="date"
                           name="jblund_dealers_settings[portal_updates][<?php echo $index; ?>][end_date]"
                           value="<?php echo esc_attr($end_date); ?>"
                           class="regular-text" />
                    <p class="description"><?php _e('Update will be hidden after this date', 'jblund-dealers'); ?></p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Required documents field callback
     */
    public function required_documents_field_callback() {
        $options = get_option('jblund_dealers_settings');
        $documents = isset($options['required_documents']) ? $options['required_documents'] : array();

        if (!is_array($documents)) {
            $documents = array();
        }
        ?>
        <div id="required-documents-manager">
            <div id="documents-list" style="margin-bottom: 20px;">
                <?php if (!empty($documents)) : ?>
                    <?php foreach ($documents as $index => $document) : ?>
                        <?php $this->render_document_row($index, $document); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" class="button button-secondary" id="add-document">
                <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
                <?php _e('Add Document', 'jblund-dealers'); ?>
            </button>

            <p class="description" style="margin-top: 15px;">
                <?php _e('Add forms, documents, or links that dealers need to complete.', 'jblund-dealers'); ?>
            </p>
        </div>

        <?php $this->render_documents_javascript(count($documents)); ?>
        <?php $this->render_documents_styles(); ?>
        <?php
    }

    /**
     * Render document row
     */
    private function render_document_row($index, $document) {
        $title = isset($document['title']) ? $document['title'] : '';
        $description = isset($document['description']) ? $document['description'] : '';
        $url = isset($document['url']) ? $document['url'] : '';
        $required = isset($document['required']) && $document['required'] === '1';
        ?>
        <div class="document-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
            <button type="button" class="button button-link-delete remove-document" style="position: absolute; top: 10px; right: 10px; color: #dc3232;">
                <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'jblund-dealers'); ?>
            </button>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Document Title:', 'jblund-dealers'); ?></strong>
                </label>
                <input type="text"
                       name="jblund_dealers_settings[required_documents][<?php echo $index; ?>][title]"
                       value="<?php echo esc_attr($title); ?>"
                       class="large-text"
                       placeholder="<?php _e('e.g., W-9 Form, Insurance Certificate, etc.', 'jblund-dealers'); ?>" />
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Description:', 'jblund-dealers'); ?></strong>
                </label>
                <textarea name="jblund_dealers_settings[required_documents][<?php echo $index; ?>][description]"
                          rows="2"
                          class="large-text"
                          placeholder="<?php _e('Brief description of what this document is for...', 'jblund-dealers'); ?>"><?php echo esc_textarea($description); ?></textarea>
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 5px;">
                    <strong><?php _e('Document URL:', 'jblund-dealers'); ?></strong>
                </label>
                <input type="url"
                       name="jblund_dealers_settings[required_documents][<?php echo $index; ?>][url]"
                       value="<?php echo esc_attr($url); ?>"
                       class="large-text"
                       placeholder="<?php _e('https://example.com/form', 'jblund-dealers'); ?>" />
                <p class="description"><?php _e('Link to the form or document page', 'jblund-dealers'); ?></p>
            </div>

            <div style="margin-bottom: 10px;">
                <label>
                    <input type="checkbox"
                           name="jblund_dealers_settings[required_documents][<?php echo $index; ?>][required]"
                           value="1"
                           <?php checked($required, true); ?> />
                    <strong><?php _e('Mark as Required', 'jblund-dealers'); ?></strong>
                </label>
                <p class="description"><?php _e('Required documents show a red badge', 'jblund-dealers'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Render updates JavaScript
     */
    private function render_updates_javascript($count) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var updateIndex = <?php echo $count; ?>;

            $('#add-update').on('click', function() {
                var template = `
                    <div class="update-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
                        <button type="button" class="button button-link-delete remove-update" style="position: absolute; top: 10px; right: 10px; color: #dc3232;">
                            <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'jblund-dealers'); ?>
                        </button>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Update Title:', 'jblund-dealers'); ?></strong>
                            </label>
                            <input type="text"
                                   name="jblund_dealers_settings[portal_updates][\${updateIndex}][title]"
                                   class="large-text"
                                   placeholder="<?php _e('e.g., New Product Launch, Price Update, etc.', 'jblund-dealers'); ?>" />
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Message:', 'jblund-dealers'); ?></strong>
                            </label>
                            <textarea name="jblund_dealers_settings[portal_updates][\${updateIndex}][message]"
                                      rows="3"
                                      class="large-text"
                                      placeholder="<?php _e('Enter your update message here...', 'jblund-dealers'); ?>"></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px;">
                                    <strong><?php _e('Start Date (Optional):', 'jblund-dealers'); ?></strong>
                                </label>
                                <input type="date"
                                       name="jblund_dealers_settings[portal_updates][\${updateIndex}][start_date]"
                                       class="regular-text" />
                                <p class="description"><?php _e('Update will appear starting from this date', 'jblund-dealers'); ?></p>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px;">
                                    <strong><?php _e('End Date (Optional):', 'jblund-dealers'); ?></strong>
                                </label>
                                <input type="date"
                                       name="jblund_dealers_settings[portal_updates][\${updateIndex}][end_date]"
                                       class="regular-text" />
                                <p class="description"><?php _e('Update will be hidden after this date', 'jblund-dealers'); ?></p>
                            </div>
                        </div>
                    </div>
                `;

                $('#updates-list').append(template);
                updateIndex++;
            });

            $(document).on('click', '.remove-update', function() {
                if (confirm('<?php _e('Are you sure you want to remove this update?', 'jblund-dealers'); ?>')) {
                    $(this).closest('.update-row').remove();
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Render documents JavaScript
     */
    private function render_documents_javascript($count) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var documentIndex = <?php echo $count; ?>;

            $('#add-document').on('click', function() {
                var template = `
                    <div class="document-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #f9f9f9; position: relative;">
                        <button type="button" class="button button-link-delete remove-document" style="position: absolute; top: 10px; right: 10px; color: #dc3232;">
                            <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'jblund-dealers'); ?>
                        </button>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Document Title:', 'jblund-dealers'); ?></strong>
                            </label>
                            <input type="text"
                                   name="jblund_dealers_settings[required_documents][\${documentIndex}][title]"
                                   class="large-text"
                                   placeholder="<?php _e('e.g., W-9 Form, Insurance Certificate, etc.', 'jblund-dealers'); ?>" />
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Description:', 'jblund-dealers'); ?></strong>
                            </label>
                            <textarea name="jblund_dealers_settings[required_documents][\${documentIndex}][description]"
                                      rows="2"
                                      class="large-text"
                                      placeholder="<?php _e('Brief description of what this document is for...', 'jblund-dealers'); ?>"></textarea>
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px;">
                                <strong><?php _e('Document URL:', 'jblund-dealers'); ?></strong>
                            </label>
                            <input type="url"
                                   name="jblund_dealers_settings[required_documents][\${documentIndex}][url]"
                                   class="large-text"
                                   placeholder="<?php _e('https://example.com/form', 'jblund-dealers'); ?>" />
                            <p class="description"><?php _e('Link to the form or document page', 'jblund-dealers'); ?></p>
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label>
                                <input type="checkbox"
                                       name="jblund_dealers_settings[required_documents][\${documentIndex}][required]"
                                       value="1" />
                                <strong><?php _e('Mark as Required', 'jblund-dealers'); ?></strong>
                            </label>
                            <p class="description"><?php _e('Required documents show a red badge', 'jblund-dealers'); ?></p>
                        </div>
                    </div>
                `;

                $('#documents-list').append(template);
                documentIndex++;
            });

            $(document).on('click', '.remove-document', function() {
                if (confirm('<?php _e('Are you sure you want to remove this document?', 'jblund-dealers'); ?>')) {
                    $(this).closest('.document-row').remove();
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Render updates styles
     */
    private function render_updates_styles() {
        ?>
        <style>
        .update-row .dashicons {
            margin-top: 3px;
        }
        .update-row label strong {
            color: #23282d;
        }
        </style>
        <?php
    }

    /**
     * Render documents styles
     */
    private function render_documents_styles() {
        ?>
        <style>
        .document-row .dashicons {
            margin-top: 3px;
        }
        .document-row label strong {
            color: #23282d;
        }
        </style>
        <?php
    }
}
