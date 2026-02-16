<?php
/**
 * WordPress Pre-Load Stubs
 *
 * These stubs are loaded BEFORE WordPress to prevent fatal errors.
 *
 * @package JBLund_Dealers
 * @subpackage Tests
 */

if ( ! function_exists( 'wp_die' ) ) {
	function wp_die( $message = '', $title = '', $args = array() ) {
		throw new Exception( 'WordPress died: ' . $message );
	}
}

if ( ! function_exists( 'wp_remote_get' ) ) {
	function wp_remote_get( $url, $args = array() ) {
		return new WP_Error( 'test', 'Not available in test mode' );
	}
}

if ( ! function_exists( 'wp_remote_post' ) ) {
	function wp_remote_post( $url, $args = array() ) {
		return new WP_Error( 'test', 'Not available in test mode' );
	}
}

if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		public $errors = array();
		public $error_data = array();

		public function __construct( $code = '', $message = '', $data = '' ) {
			if ( empty( $code ) ) {
				return;
			}
			$this->errors[ $code ][] = $message;
			if ( ! empty( $data ) ) {
				$this->error_data[ $code ] = $data;
			}
		}

		public function get_error_code() {
			$codes = array_keys( $this->errors );
			return isset( $codes[0] ) ? $codes[0] : '';
		}

		public function is_wp_error() {
			return true;
		}
	}
}
