<?php
/**
 * WordPress Stubs for Testing
 *
 * Minimal WordPress function stubs to allow tests to run with Local by Flywheel.
 *
 * @package JBLund_Dealers
 * @subpackage Tests
 */

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

if ( ! function_exists( 'register_activation_hook' ) ) {
	function register_activation_hook( $file, $callback ) {
		return true;
	}
}

if ( ! function_exists( 'register_deactivation_hook' ) ) {
	function register_deactivation_hook( $file, $callback ) {
		return true;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

if ( ! function_exists( 'remove_action' ) ) {
	function remove_action( $hook, $callback, $priority = 10 ) {
		return true;
	}
}

if ( ! function_exists( 'do_action' ) ) {
	function do_action( $hook, ...$args ) {
		return true;
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook, $value, ...$args ) {
		return $value;
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $url ) {
		return esc_attr( $url );
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	function wp_kses_post( $text ) {
		return $text;
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) {
		return trim( stripslashes( $str ) );
	}
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
	function wp_verify_nonce( $nonce, $action = -1 ) {
		return 1;
	}
}

if ( ! function_exists( 'current_user_can' ) ) {
	function current_user_can( $capability ) {
		return true;
	}
}

if ( ! function_exists( 'get_user_meta' ) ) {
	function get_user_meta( $user_id, $key = '', $single = false ) {
		return $single ? '' : array();
	}
}

if ( ! function_exists( 'update_user_meta' ) ) {
	function update_user_meta( $user_id, $meta_key, $meta_value ) {
		return true;
	}
}

if ( ! function_exists( 'delete_user_meta' ) ) {
	function delete_user_meta( $user_id, $meta_key, $meta_value = '' ) {
		return true;
	}
}

if ( ! function_exists( 'delete_user_meta_by_key' ) ) {
	function delete_user_meta_by_key( $key ) {
		return true;
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( $post_id, $key = '', $single = false ) {
		return $single ? '' : array();
	}
}

if ( ! function_exists( 'update_post_meta' ) ) {
	function update_post_meta( $post_id, $meta_key, $meta_value ) {
		return true;
	}
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
	function plugin_dir_path( $file ) {
		return dirname( $file ) . '/';
	}
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
	function plugin_dir_url( $file ) {
		return 'http://example.com/wp-content/plugins/jblund_dealers/';
	}
}

if ( ! function_exists( 'plugin_basename' ) ) {
	function plugin_basename( $file ) {
		return 'jblund_dealers/jblund-dealers.php';
	}
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'wp_die' ) ) {
	function wp_die( $message = '', $title = '', $args = array() ) {
		error_log( 'WordPress die called: ' . $message );
		return;
	}
}

if ( ! function_exists( 'wp_die_handler' ) ) {
	function wp_die_handler( $message, $title = '', $args = array() ) {
		error_log( 'WordPress die handler called: ' . $message );
		return;
	}
}

if ( ! function_exists( '_e' ) ) {
	function _e( $text, $domain = 'default' ) {
		echo esc_html( $text );
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
		return true;
	}
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		return true;
	}
}

if ( ! function_exists( 'wp_localize_script' ) ) {
	function wp_localize_script( $handle, $object_name, $l10n ) {
		return true;
	}
}

if ( ! function_exists( 'register_post_type' ) ) {
	function register_post_type( $post_type, $args = array() ) {
		return (object) array( 'name' => $post_type );
	}
}

if ( ! function_exists( 'post_type_exists' ) ) {
	function post_type_exists( $post_type ) {
		return true;
	}
}

if ( ! function_exists( 'get_post_type_object' ) ) {
	function get_post_type_object( $post_type ) {
		return (object) array( 'name' => $post_type );
	}
}

if ( ! function_exists( 'wp_create_nonce' ) ) {
	function wp_create_nonce( $action = -1 ) {
		return 'test-nonce-' . time();
	}
}

if ( ! function_exists( 'get_post' ) ) {
	function get_post( $post = null, $output = OBJECT, $filter = 'raw' ) {
		$post = new WP_Post();
		$post->ID = is_numeric( $post ) ? $post : 0;
		return $post;
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

if ( ! function_exists( 'wp_set_current_user' ) ) {
	function wp_set_current_user( $user_id, $user_login = '' ) {
		return true;
	}
}

if ( ! function_exists( 'add_shortcode' ) ) {
	function add_shortcode( $tag, $callback ) {
		return true;
	}
}

if ( ! function_exists( 'do_shortcode' ) ) {
	function do_shortcode( $content ) {
		return $content;
	}
}

if ( ! class_exists( 'WP_UnitTestCase' ) ) {
	class WP_UnitTestCase extends \PHPUnit\Framework\TestCase {
		
		protected $factory;
		
		public function setUp(): void {
			parent::setUp();
			$this->factory = new class {
				public $post;
				public $user;
				
				public function __construct() {
					$this->post = new class {
						public function create( $args = array() ) {
							$post = new WP_Post();
							$post->ID = rand( 1, 10000 );
							$post->post_title = isset( $args['post_title'] ) ? $args['post_title'] : 'Test Post';
							$post->post_type = isset( $args['post_type'] ) ? $args['post_type'] : 'post';
							$post->post_status = isset( $args['post_status'] ) ? $args['post_status'] : 'publish';
							return $post->ID;
						}
						
						public function create_batch( $count, $args = array() ) {
							$ids = array();
							for ( $i = 0; $i < $count; $i++ ) {
								$ids[] = $this->create( $args );
							}
							return $ids;
						}
					};
					
					$this->user = new class {
						public function create( $args = array() ) {
							return rand( 1, 10000 );
						}
					};
				}
				
				public function __call( $method, $args ) {
					if ( $method === 'post' ) {
						return $this->post;
					}
					if ( $method === 'user' ) {
						return $this->user;
					}
					return $this->post;
				}
			};
		}
		
		public function tearDown(): void {
			parent::tearDown();
		}
		
		public function factory() {
			return $this->factory;
		}
	}
}

if ( ! class_exists( 'WP_Query' ) ) {
	class WP_Query {
		public function __construct( $args = array() ) {
		}
	}
}

if ( ! class_exists( 'WP_Post' ) ) {
	class WP_Post {
		public $ID = 0;
		public $post_title = '';
		public $post_content = '';
		public $post_type = '';
		public $post_status = 'publish';
	}
}

// Global WordPress object stubs
if ( ! isset( $wpdb ) ) {
	global $wpdb;
	$wpdb = new class {
		public $prefix = 'wp_';
		
		public function prepare( $query, ...$args ) {
			return $query;
		}
		
		public function get_results( $query ) {
			return array();
		}
		
		public function get_row( $query ) {
			return null;
		}
		
		public function query( $query ) {
			return true;
		}
	};
}
