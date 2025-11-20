<?php
/**
 * Plugin Name: JBLund Dealers
 * Plugin URI: https://github.com/MacphersonDesigns/jblund_dealers
 * Description: A custom WordPress plugin to store and display dealer information for JBLund Dock's B2B Website
 * Version: 1.3.0
 * Author: Macpherson Designs
 * Author URI: https://github.com/MacphersonDesigns
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: jblund-dealers
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Start output buffering to prevent header warnings
ob_start();

// Define plugin constants
define('JBLUND_DEALERS_VERSION', '1.3.0');
define('JBLUND_DEALERS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JBLUND_DEALERS_PLUGIN_URL', plugin_dir_url(__FILE__));

// ==================================================
// LOAD CORE MODULES
// ==================================================
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/core/class-post-type.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/core/class-registration-post-type.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/core/class-meta-boxes.php';

// ==================================================
// LOAD ADMIN MODULES
// ==================================================
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/admin/class-admin-columns.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/admin/class-settings.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/admin/class-settings-page.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/admin/class-dashboard-widget.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/admin/class-message-scheduler.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/admin/class-csv-handler.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/admin/class-portal-fields.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/admin/class-field-renderer.php';

// ==================================================
// LOAD FRONTEND MODULES
// ==================================================
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/frontend/class-styles.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/frontend/class-shortcode.php';

// ==================================================
// LOAD DEALER PORTAL MODULE (conditionally)
// ==================================================
// Skip loading in builder contexts
$skip_dealer_portal = (isset($_GET['et_fb']) || isset($_POST['et_fb']))  // Divi Builder
    || isset($_GET['et_bfb'])                                             // Divi Backend Builder
    || isset($_GET['elementor-preview'])                                  // Elementor
    || isset($_GET['fl_builder']);                                        // Beaver Builder

if (!$skip_dealer_portal) {
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-dealer-role.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-dealer-role.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-email-handler.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-email-handler.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-nda-handler.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-nda-handler.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-nda-pdf-generator.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-nda-pdf-generator.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-menu-visibility.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-menu-visibility.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-registration-admin.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-registration-admin.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-registration-form.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-registration-form.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-password-change-handler.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-password-change-handler.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-nda-editor.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-nda-editor.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-page-manager.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/classes/class-page-manager.php';
    }
    if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/class-shortcodes.php')) {
        require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/dealer-portal/class-shortcodes.php';
    }
}

/**
 * Main JBLund Dealers Plugin Class
 *
 * Minimal bootstrap class - delegates to modules
 */
class JBLund_Dealers_Plugin {

    /**
     * Settings page renderer
     */
    private $settings_page;

    /**
     * Initialize the plugin
     */
    public function __construct() {
        // Initialize core components
        new \JBLund\Core\Post_Type();
        new \JBLund\Core\Registration_Post_Type();
        new \JBLund\Core\Meta_Boxes();
        new \JBLund\Admin\Admin_Columns();
        new \JBLund\Admin\Settings();
        new \JBLund\Admin\Dashboard_Widget();
        new \JBLund\Admin\Message_Scheduler();
        new \JBLund\Frontend\Styles();
        new \JBLund\Frontend\Shortcode();

        // Initialize admin-only components
        if (is_admin()) {
            // CSV Handler
            \JBLund\Admin\CSV_Handler::get_instance();

            // Settings page renderer
            $this->settings_page = new \JBLund\Admin\Settings_Page();
        }

        // Initialize dealer portal components (if loaded)
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

        // Hook settings page into WordPress
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

// ==================================================
// PLUGIN INITIALIZATION
// ==================================================

/**
 * Initialize the plugin
 */
function jblund_dealers_init() {
    new JBLund_Dealers_Plugin();
}
add_action('plugins_loaded', 'jblund_dealers_init');

// ==================================================
// HELPER FUNCTIONS
// ==================================================

/**
 * Helper function to get assigned portal page URL
 *
 * @param string $page_type One of: 'login', 'dashboard', 'profile', 'nda'
 * @return string|false Page URL or false if not assigned
 */
function jblund_get_portal_page_url($page_type) {
    $portal_pages = get_option('jblund_dealers_portal_pages', array());

    if (empty($portal_pages[$page_type])) {
        return false;
    }

    $page_id = intval($portal_pages[$page_type]);
    return get_permalink($page_id);
}

/**
 * Helper function to get assigned portal page ID
 *
 * @param string $page_type One of: 'login', 'dashboard', 'profile', 'nda'
 * @return int|false Page ID or false if not assigned
 */
function jblund_get_portal_page_id($page_type) {
    $portal_pages = get_option('jblund_dealers_portal_pages', array());

    if (empty($portal_pages[$page_type])) {
        return false;
    }

    return intval($portal_pages[$page_type]);
}

/**
 * Helper function to get dealer post data linked to user account
 *
 * @param int $user_id User ID (defaults to current user)
 * @return WP_Post|false Dealer post object or false if not found
 */
function jblund_get_user_dealer_post($user_id = null) {
    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }

    if (empty($user_id)) {
        return false;
    }

    // Query for dealer posts where this user is linked
    $args = array(
        'post_type' => 'dealer',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_dealer_linked_user_id',
                'value' => $user_id,
                'compare' => '='
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        return $query->posts[0];
    }

    return false;
}

/**
 * Helper function to get dealer representative info
 *
 * @param int $user_id User ID (defaults to current user)
 * @return array|false Array with rep info or false if not found
 */
function jblund_get_dealer_representative($user_id = null) {
    $dealer_post = jblund_get_user_dealer_post($user_id);

    if (!$dealer_post) {
        return false;
    }

    // Get representative info from settings
    $settings = get_option('jblund_dealers_settings', array());

    return array(
        'name' => isset($settings['representative_name']) ? $settings['representative_name'] : 'Jim Johnson',
        'email' => isset($settings['representative_email']) ? $settings['representative_email'] : 'jim@jblund.com',
        'phone' => isset($settings['representative_phone']) ? $settings['representative_phone'] : '(555) 123-4567',
    );
}

// ==================================================
// ACTIVATION & DEACTIVATION HOOKS
// ==================================================

/**
 * Plugin activation hook
 */
function jblund_dealers_activate() {
    // Trigger the post type registration from the modules
    $post_type = new \JBLund\Core\Post_Type();
    $post_type->register_dealer_post_type();

    $registration_post_type = new \JBLund\Core\Registration_Post_Type();
    $registration_post_type->register_dealer_registration_post_type();

    // Create dealer role
    if (class_exists('JBLund\DealerPortal\Dealer_Role')) {
        JBLund\DealerPortal\Dealer_Role::create_role();
    }

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'jblund_dealers_activate');

/**
 * Deactivation hook
 */
function jblund_dealers_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'jblund_dealers_deactivate');
