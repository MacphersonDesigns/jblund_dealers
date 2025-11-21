<?php
/**
 * Shortcode Manager Class
 *
 * Coordinates the rendering of the [jblund_dealers] shortcode by delegating
 * to specific layout renderers based on the requested layout type.
 *
 * @package    JBLund_Dealers
 * @subpackage Frontend\Shortcodes
 * @since      2.0.0
 */

namespace JBLund\Frontend\Shortcodes;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Shortcode_Manager
 *
 * Main shortcode handler that routes to specific layout renderers
 */
class Shortcode_Manager {

    /**
     * Layout renderers
     *
     * @var array
     */
    private $renderers = array();

    /**
     * Constructor
     *
     * Registers the shortcode and initializes layout renderers.
     */
    public function __construct() {
        add_shortcode('jblund_dealers', array($this, 'render'));
        $this->init_renderers();
    }

    /**
     * Initialize layout renderers
     */
    private function init_renderers() {
        $this->renderers['grid'] = new Grid_Layout();
        $this->renderers['list'] = new List_Layout();
        $this->renderers['compact'] = new Compact_Layout();
    }

    /**
     * Render the dealers shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output of the dealers listing
     */
    public function render($atts) {
        // Get default settings
        $options = get_option('jblund_dealers_settings');
        $default_layout = isset($options['default_layout']) ? $options['default_layout'] : 'grid';

        // Parse shortcode attributes
        $atts = shortcode_atts(array(
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'layout' => $default_layout,
        ), $atts, 'jblund_dealers');

        // Query dealers
        $args = array(
            'post_type' => 'dealer',
            'posts_per_page' => intval($atts['posts_per_page']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
        );

        $dealers = new \WP_Query($args);

        if (!$dealers->have_posts()) {
            return '<p>' . __('No dealers found.', 'jblund-dealers') . '</p>';
        }

        // Get the appropriate renderer
        $layout = $atts['layout'];
        if (!isset($this->renderers[$layout])) {
            $layout = 'grid'; // Fallback to grid
        }

        $renderer = $this->renderers[$layout];

        // Render the layout
        ob_start();
        $renderer->render($dealers, $atts);
        wp_reset_postdata();

        return ob_get_clean();
    }
}
