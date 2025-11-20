<?php
/**
 * Plugin Deactivation Class
 *
 * Handles plugin deactivation tasks such as flushing rewrite rules.
 * Note: This does NOT delete data - see uninstall.php for that.
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
 * Class Deactivator
 *
 * Handles plugin deactivation
 */
class Deactivator {

    /**
     * Run deactivation tasks
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
