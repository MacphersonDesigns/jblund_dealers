<?php
/**
 * Main Plugin Class
 *
 * Coordinates the initialization of all plugin modules and components.
 * This class acts as the central orchestrator, ensuring all modules are
 * loaded and initialized in the correct order.
 *
 * @package    JBLund_Dealers
 * @subpackage Includes
 * @since      2.0.0
 */

namespace JBLund\Includes;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Plugin
 *
 * Main plugin initialization and coordination
 */
class Plugin {

    /**
     * Settings page renderer
     *
     * @var \JBLund\Admin\Settings_Page
     */
    private $settings_page;

    /**
     * Initialize the plugin
     */
    public function __construct() {
        $this->init_core_modules();
        $this->init_admin_modules();
        $this->init_frontend_modules();
        $this->init_dealer_portal_modules();
        $this->register_hooks();
    }

    /**
     * Initialize core modules
     */
    private function init_core_modules() {
        new \JBLund\Core\Post_Type();
        new \JBLund\Core\Registration_Post_Type();
        new \JBLund\Core\Meta_Boxes();
    }

    /**
     * Initialize admin modules
     */
    private function init_admin_modules() {
        new \JBLund\Admin\Admin_Columns();
        new \JBLund\Admin\Settings();
        new \JBLund\Admin\Dashboard_Widget();
        new \JBLund\Admin\Message_Scheduler();

        // Initialize admin-only components
        if (is_admin()) {
            // CSV Handler
            \JBLund\Admin\CSV_Handler::get_instance();

            // Settings page renderer
            $this->settings_page = new \JBLund\Admin\Settings_Page();

            // Signed NDAs List
            \JBLund\Admin\Signed_NDAs_List::get_instance();
        }
    }

    /**
     * Initialize frontend modules
     */
    private function init_frontend_modules() {
        new \JBLund\Frontend\Styles();
        new \JBLund\Frontend\Shortcode();
        new \JBLund\Frontend\Dealer_Map();
    }

    /**
     * Initialize dealer portal modules (if loaded)
     */
    private function init_dealer_portal_modules() {
        if (class_exists('JBLund\DealerPortal\Email_Handler')) {
            new \JBLund\DealerPortal\Email_Handler();
        }
        if (class_exists('JBLund\DealerPortal\NDA_Handler')) {
            new \JBLund\DealerPortal\NDA_Handler();
        }
        if (class_exists('JBLund\DealerPortal\NDA_PDF_Generator')) {
            new \JBLund\DealerPortal\NDA_PDF_Generator();
        }
        if (class_exists('JBLund\DealerPortal\Menu_Visibility')) {
            new \JBLund\DealerPortal\Menu_Visibility();
        }
        if (class_exists('JBLund\DealerPortal\Registration_Admin')) {
            new \JBLund\DealerPortal\Registration_Admin();
        }
        if (class_exists('JBLund\DealerPortal\Registration_Form')) {
            new \JBLund\DealerPortal\Registration_Form();
        }
        if (class_exists('JBLund\DealerPortal\Password_Change_Handler')) {
            new \JBLund\DealerPortal\Password_Change_Handler();
        }
        if (class_exists('JBLund\DealerPortal\NDA_Editor')) {
            new \JBLund\DealerPortal\NDA_Editor();
        }
        if (class_exists('JBLund\DealerPortal\Shortcodes')) {
            new \JBLund\DealerPortal\Shortcodes();
        }
        if (class_exists('JBLund\DealerPortal\Dealer_Profile_Manager')) {
            \JBLund\DealerPortal\Dealer_Profile_Manager::get_instance();
        }
    }

    /**
     * Register WordPress hooks
     */
    private function register_hooks() {
        add_action('admin_menu', array($this, 'add_settings_page'));
    }

    /**
     * Add settings page to WordPress admin menu
     */
    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=dealer',
            __('Dealer Settings', 'jblund-dealers'),
            __('Settings', 'jblund-dealers'),
            'manage_options',
            'jblund-dealers-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Render settings page (delegates to Settings_Page module)
     */
    public function render_settings_page() {
        if ($this->settings_page) {
            $this->settings_page->render();
        }
    }

    /**
     * Helper function to darken colors (used by styles module)
     *
     * @param string $hex The hex color code
     * @param int $percent The percentage to darken
     * @return string The darkened hex color
     */
    public static function darken_color($hex, $percent) {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, $r - ($r * $percent / 100));
        $g = max(0, $g - ($g * $percent / 100));
        $b = max(0, $b - ($b * $percent / 100));

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}
