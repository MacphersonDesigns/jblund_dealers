<?php
/**
 * PHPUnit Bootstrap File - Local by Flywheel Integration
 *
 * Sets up WordPress environment using the actual Local site.
 *
 * @package JBLund_Dealers
 * @subpackage Tests
 */

// Load pre-stubs before WordPress to prevent fatal errors
require_once dirname( __FILE__ ) . '/wp-pre-stubs.php';
require_once dirname( __FILE__ ) . '/wordpress-stubs.php';

// Get the WordPress path
$_root_dir = dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) );

// Try to load WordPress, capturing any errors
define( 'WP_USE_THEMES', false );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', false );

$error_reporting = error_reporting();
error_reporting( 0 );

$wp_load_error = null;
ob_start();
try {
	if ( file_exists( $_root_dir . '/wp-load.php' ) ) {
		require $_root_dir . '/wp-load.php';
	}
} catch ( Exception $e ) {
	$wp_load_error = $e->getMessage();
} catch ( Throwable $e ) {
	$wp_load_error = $e->getMessage();
}
$wp_output = ob_get_clean();

error_reporting( $error_reporting );

// Load the plugin file if WordPress didn't already load it
if ( ! class_exists( '\JBLund\Includes\Plugin' ) ) {
	require dirname( dirname( __FILE__ ) ) . '/jblund-dealers.php';
}
