<?php
/**
 * JBLund Dealers Uninstall Script
 *
 * This file is executed when the plugin is deleted from the WordPress admin.
 * It delegates to the Uninstaller class to clean up all plugin data.
 *
 * @package JBLund_Dealers
 * @since   1.0.0
 */

// Exit if uninstall not called from WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Load necessary files for uninstallation
require_once plugin_dir_path(__FILE__) . 'modules/dealer-portal/classes/class-dealer-role.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-uninstaller.php';

// Run the uninstallation
\JBLund\Includes\Uninstaller::uninstall();
