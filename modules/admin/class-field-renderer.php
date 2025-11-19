<?php
/**
 * Field Renderer
 *
 * Provides reusable field rendering methods for settings forms
 *
 * @package JBLund_Dealers
 * @subpackage Admin
 */

namespace JBLund\Admin;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Field_Renderer {

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
     * Constructor (private for singleton)
     */
    private function __construct() {
        // This class is instantiated on-demand
    }

    /**
     * Render text field
     */
    public function text_field($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : (isset($args['default']) ? $args['default'] : '');
        echo '<input type="text" name="jblund_dealers_settings[' . esc_attr($args['field']) . ']" value="' . esc_attr($value) . '" class="regular-text" />';
        if (isset($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    /**
     * Render color field
     */
    public function color_field($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<input type="color" name="jblund_dealers_settings[' . esc_attr($args['field']) . ']" value="' . esc_attr($value) . '" />';
    }

    /**
     * Render select field
     */
    public function select_field($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];

        echo '<select name="jblund_dealers_settings[' . esc_attr($args['field']) . ']">';
        foreach ($args['options'] as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($value, $key, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    /**
     * Render checkbox field
     */
    public function checkbox_field($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<input type="checkbox" name="jblund_dealers_settings[' . esc_attr($args['field']) . ']" value="1" ' . checked($value, '1', false) . ' />';
    }

    /**
     * Render range field (slider)
     */
    public function range_field($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        $min = isset($args['min']) ? $args['min'] : 0;
        $max = isset($args['max']) ? $args['max'] : 100;
        $step = isset($args['step']) ? $args['step'] : 1;
        $unit = isset($args['unit']) ? $args['unit'] : '';

        echo '<div class="range-field-wrapper">';
        echo '<input type="range" name="jblund_dealers_settings[' . esc_attr($args['field']) . ']" ';
        echo 'id="' . esc_attr($args['field']) . '" ';
        echo 'value="' . esc_attr($value) . '" ';
        echo 'min="' . esc_attr($min) . '" ';
        echo 'max="' . esc_attr($max) . '" ';
        echo 'step="' . esc_attr($step) . '" ';
        echo 'class="jblund-range-slider" />';
        echo '<span class="range-value" id="' . esc_attr($args['field']) . '_value">' . esc_html($value . $unit) . '</span>';
        echo '</div>';
        echo '<script>
        document.getElementById("' . esc_js($args['field']) . '").addEventListener("input", function(e) {
            document.getElementById("' . esc_js($args['field']) . '_value").textContent = e.target.value + "' . esc_js($unit) . '";
        });
        </script>';
    }

    /**
     * Render textarea field
     */
    public function textarea_field($args) {
        $options = get_option('jblund_dealers_settings');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : $args['default'];
        echo '<textarea name="jblund_dealers_settings[' . esc_attr($args['field']) . ']" ';
        echo 'rows="10" cols="50" class="large-text code">';
        echo esc_textarea($value);
        echo '</textarea>';

        if ($args['field'] === 'custom_css') {
            echo '<p class="description">' . __('Add custom CSS to further customize the appearance. Use standard CSS syntax.', 'jblund-dealers') . '</p>';
            echo '<p class="description"><strong>' . __('Example:', 'jblund-dealers') . '</strong> <code>.dealer-card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }</code></p>';
        }
    }
}
