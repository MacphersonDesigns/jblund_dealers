<?php
/**
 * Plugin Name: JBLund Dealers
 * Plugin URI: https://github.com/MacphersonDesigns/jblund_dealers
 * Description: A custom WordPress plugin to store and display dealer information for JBLund Dock's B2B Website
 * Version: 2.2.2
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
define('JBLUND_DEALERS_VERSION', '2.2.2');
define('JBLUND_DEALERS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JBLUND_DEALERS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('JBLUND_DEALERS_GITHUB_REPO', 'MacphersonDesigns/jblund_dealers');

// Load Composer autoloader for TCPDF and other dependencies
if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once JBLUND_DEALERS_PLUGIN_DIR . 'vendor/autoload.php';
}

// Load core includes
require_once JBLUND_DEALERS_PLUGIN_DIR . 'includes/class-loader.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'includes/class-plugin.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'includes/class-activator.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'includes/class-deactivator.php';
require_once JBLUND_DEALERS_PLUGIN_DIR . 'includes/helper-functions.php';

/**
 * Initialize the plugin
 */
function jblund_dealers_init() {
    // Load all module files
    $loader = new \JBLund\Includes\Loader(JBLUND_DEALERS_PLUGIN_DIR);
    $loader->load_modules();

    // Initialize plugin
    new \JBLund\Includes\Plugin();

    // Initialize auto-updates from GitHub
    jblund_dealers_check_for_updates();
}
add_action('plugins_loaded', 'jblund_dealers_init');

/**
 * Check for plugin updates from GitHub
 */
function jblund_dealers_check_for_updates() {
    if (!class_exists('YahnisElsts\PluginUpdateChecker\v5\PucFactory')) {
        return;
    }

    $updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/' . JBLUND_DEALERS_GITHUB_REPO . '/',
        __FILE__,
        'jblund-dealers'
    );

    // For private repositories, use a GitHub token
    // Create a token at: https://github.com/settings/tokens (classic)
    // Required scopes: repo (full access to private repositories)
    // Store securely - consider using wp-config.php constant
    if (defined('JBLUND_GITHUB_ACCESS_TOKEN')) {
        $updateChecker->setAuthentication(JBLUND_GITHUB_ACCESS_TOKEN);
    }

    // Use release assets
    $updateChecker->getVcsApi()->enableReleaseAssets();
}

/**
 * Plugin activation hook
 */
function jblund_dealers_activate() {
    // Load necessary files for activation
    require_once JBLUND_DEALERS_PLUGIN_DIR . 'includes/class-loader.php';
    require_once JBLUND_DEALERS_PLUGIN_DIR . 'includes/class-activator.php';

    $loader = new \JBLund\Includes\Loader(JBLUND_DEALERS_PLUGIN_DIR);
    $loader->load_modules();

    \JBLund\Includes\Activator::activate();
}
register_activation_hook(__FILE__, 'jblund_dealers_activate');

/**
 * Plugin deactivation hook
 */
function jblund_dealers_deactivate() {
    \JBLund\Includes\Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'jblund_dealers_deactivate');
