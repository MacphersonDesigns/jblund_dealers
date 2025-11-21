<?php
/**
 * Shortcode Loader Class
 *
 * Loads and initializes the modular shortcode system for displaying dealer information.
 * The actual rendering logic is split into individual layout classes for easier editing.
 *
 * @package    JBLund_Dealers
 * @subpackage Frontend
 * @since      2.0.0
 */

namespace JBLund\Frontend;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load shortcode classes
require_once __DIR__ . '/shortcodes/class-layout-base.php';
require_once __DIR__ . '/shortcodes/class-grid-layout.php';
require_once __DIR__ . '/shortcodes/class-list-layout.php';
require_once __DIR__ . '/shortcodes/class-compact-layout.php';
require_once __DIR__ . '/shortcodes/class-shortcode-manager.php';

/**
 * Class Shortcode
 *
 * Loads the modular shortcode system
 */
class Shortcode {

    /**
     * Shortcode manager instance
     *
     * @var \JBLund\Frontend\Shortcodes\Shortcode_Manager
     */
    private $manager;

    /**
     * Constructor
     *
     * Initializes the shortcode manager.
     */
    public function __construct() {
        $this->manager = new \JBLund\Frontend\Shortcodes\Shortcode_Manager();
    }

}
