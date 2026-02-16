<?php
/**
 * PHPUnit Bootstrap File for JBLund Dealers Plugin Tests
 *
 * Sets up the WordPress test environment using stub functions.
 * This allows tests to run without requiring a live WordPress database.
 *
 * @package JBLund_Dealers
 * @subpackage Tests
 */

$plugin_dir = dirname( dirname( __FILE__ ) );

// Load Composer autoloader first (so PHPUnit classes are available)
if ( file_exists( $plugin_dir . '/vendor/autoload.php' ) ) {
	require_once $plugin_dir . '/vendor/autoload.php';
}

// Load WordPress function stubs for testing
require_once dirname( __FILE__ ) . '/wordpress-stubs.php';

// Load core plugin files
require_once $plugin_dir . '/includes/helper-functions.php';

// Load all PHP class files from modules and includes
$directories = array(
	$plugin_dir . '/includes',
	$plugin_dir . '/modules/admin',
	$plugin_dir . '/modules/dealer-portal',
	$plugin_dir . '/modules/frontend',
);

foreach ( $directories as $dir ) {
	if ( is_dir( $dir ) ) {
		$files = glob( $dir . '/class-*.php' );
		foreach ( (array) $files as $file ) {
			require_once $file;
		}
	}
}

// Load the main plugin file
require $plugin_dir . '/jblund-dealers.php';

// Verify PHP version requirement
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	echo "Fatal Error: JBLund Dealers requires PHP 7.4 or higher\n";
	exit( 1 );
}
