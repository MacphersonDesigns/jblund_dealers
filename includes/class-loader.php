<?php
/**
 * Module Loader Class
 *
 * Responsible for loading all plugin module files in the correct order.
 * Handles conditional loading (e.g., skipping dealer portal in builder contexts).
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
 * Class Loader
 *
 * Loads all plugin module files
 */
class Loader {

    /**
     * Plugin directory path
     *
     * @var string
     */
    private $plugin_dir;

    /**
     * Whether to skip dealer portal loading
     *
     * @var bool
     */
    private $skip_dealer_portal;

    /**
     * Constructor
     *
     * @param string $plugin_dir Plugin directory path
     */
    public function __construct($plugin_dir) {
        $this->plugin_dir = $plugin_dir;
        $this->skip_dealer_portal = $this->should_skip_dealer_portal();
    }

    /**
     * Load all module files
     */
    public function load_modules() {
        $this->load_core_modules();
        $this->load_admin_modules();
        $this->load_frontend_modules();

        if (!$this->skip_dealer_portal) {
            $this->load_dealer_portal_modules();
        }
    }

    /**
     * Load core modules
     */
    private function load_core_modules() {
        require_once $this->plugin_dir . 'modules/core/class-post-type.php';
        require_once $this->plugin_dir . 'modules/core/class-registration-post-type.php';
        require_once $this->plugin_dir . 'modules/core/class-meta-boxes.php';
    }

    /**
     * Load admin modules
     */
    private function load_admin_modules() {
        require_once $this->plugin_dir . 'modules/admin/class-admin-columns.php';
        require_once $this->plugin_dir . 'modules/admin/class-settings.php';
        require_once $this->plugin_dir . 'modules/admin/class-settings-page.php';
        require_once $this->plugin_dir . 'modules/admin/class-dashboard-widget.php';
        require_once $this->plugin_dir . 'modules/admin/class-message-scheduler.php';
        require_once $this->plugin_dir . 'modules/admin/class-csv-handler.php';
        require_once $this->plugin_dir . 'modules/admin/class-portal-fields.php';
        require_once $this->plugin_dir . 'modules/admin/class-field-renderer.php';
        require_once $this->plugin_dir . 'modules/admin/class-signed-ndas-list.php';
    }

    /**
     * Load frontend modules
     */
    private function load_frontend_modules() {
        require_once $this->plugin_dir . 'modules/frontend/class-styles.php';
        require_once $this->plugin_dir . 'modules/frontend/class-shortcode.php';
    }

    /**
     * Load dealer portal modules
     */
    private function load_dealer_portal_modules() {
        $portal_files = array(
            'modules/dealer-portal/classes/class-dealer-role.php',
            'modules/dealer-portal/classes/class-email-handler.php',
            'modules/dealer-portal/classes/class-nda-handler.php',
            'modules/dealer-portal/classes/class-nda-pdf-generator.php',
            'modules/dealer-portal/classes/class-menu-visibility.php',
            'modules/dealer-portal/classes/class-registration-admin.php',
            'modules/dealer-portal/classes/class-registration-form.php',
            'modules/dealer-portal/classes/class-password-change-handler.php',
            'modules/dealer-portal/classes/class-nda-editor.php',
            'modules/dealer-portal/classes/class-page-manager.php',
            'modules/dealer-portal/classes/class-dealer-profile-manager.php',
            'modules/dealer-portal/class-shortcodes.php',
        );

        foreach ($portal_files as $file) {
            $file_path = $this->plugin_dir . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }

    /**
     * Determine if dealer portal should be skipped
     *
     * @return bool True if dealer portal should be skipped
     */
    private function should_skip_dealer_portal() {
        return (isset($_GET['et_fb']) || isset($_POST['et_fb']))  // Divi Builder
            || isset($_GET['et_bfb'])                              // Divi Backend Builder
            || isset($_GET['elementor-preview'])                   // Elementor
            || isset($_GET['fl_builder']);                         // Beaver Builder
    }
}
