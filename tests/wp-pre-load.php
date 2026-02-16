<?php
/**
 * Pre-load stubs to prevent WordPress errors during testing
 *
 * @package JBLund_Dealers
 */

if ( ! function_exists( 'wp_die' ) ) {
	function wp_die( $message = '', $title = '', $args = array() ) {
		// Silently suppress wp_die output during tests
		return null;
	}
}

if ( ! function_exists( 'wp_die_handler' ) ) {
	function wp_die_handler( $message = '', $title = '', $args = array() ) {
		return null;
	}
}

if ( ! function_exists( 'wp_die_xml_handler' ) ) {
	function wp_die_xml_handler( $message = '', $title = '', $args = array() ) {
		return null;
	}
}

if ( ! function_exists( 'wp_die_json_handler' ) ) {
	function wp_die_json_handler( $message = '', $title = '', $args = array() ) {
		return null;
	}
}
