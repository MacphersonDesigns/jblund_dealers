<?php
/**
 * NDA Access Restrictor
 *
 * Handles access restrictions for dealers who haven't accepted NDA
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 2.0.0
 */

namespace JBLund\DealerPortal;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NDA_Access_Restrictor
 *
 * Restricts portal access until NDA is accepted
 */
class NDA_Access_Restrictor {

	/**
	 * NDA acceptance manager instance
	 *
	 * @var NDA_Acceptance_Manager
	 */
	private $acceptance_manager;

	/**
	 * NDA page slug
	 *
	 * @var string
	 */
	private $nda_page_slug = 'dealer-nda-acceptance';

	/**
	 * Constructor
	 *
	 * @param NDA_Acceptance_Manager $acceptance_manager NDA acceptance manager
	 */
	public function __construct( $acceptance_manager ) {
		$this->acceptance_manager = $acceptance_manager;

		// Login redirect
		\add_action( 'wp_login', array( $this, 'redirect_to_nda_after_login' ), 10, 2 );

		// Access restriction (skip in builder context)
		if ( ! $this->is_builder_context() ) {
			\add_action( 'template_redirect', array( $this, 'restrict_portal_access' ) );
		}

		// Hide admin bar for dealers
		\add_action( 'after_setup_theme', array( $this, 'hide_admin_bar_for_dealers' ) );
	}

	/**
	 * Redirect dealer users to NDA page after login if they haven't accepted
	 *
	 * @param string   $user_login Username.
	 * @param \WP_User $user WP_User object of the logged-in user.
	 * @return void
	 */
	public function redirect_to_nda_after_login( $user_login, $user ) {
		// Allow administrators and staff to bypass
		if ( Dealer_Role::can_bypass_dealer_restrictions( $user ) ) {
			return;
		}

		// Check if user is a dealer
		if ( ! Dealer_Role::is_dealer( $user ) ) {
			return;
		}

		// Check if NDA already accepted
		if ( $this->acceptance_manager->is_accepted( $user->ID ) ) {
			return;
		}

		// Redirect to NDA acceptance page
		\wp_safe_redirect( $this->get_nda_page_url() );
		exit;
	}

	/**
	 * Restrict dealer portal pages until NDA is accepted
	 *
	 * @return void
	 */
	public function restrict_portal_access() {
		// Skip during AJAX
		if ( \defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( \wp_doing_ajax() ) {
			return;
		}

		// Skip in admin
		if ( \is_admin() ) {
			return;
		}

		// Skip for Divi Builder
		if ( \function_exists( 'et_core_is_fb_enabled' ) && \et_core_is_fb_enabled() ) {
			return;
		}

		// Skip for REST API
		if ( \defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		// If not logged in
		if ( ! \is_user_logged_in() ) {
			// Prevent non-logged-in users from accessing NDA page
			if ( \is_page( $this->nda_page_slug ) ) {
				\wp_safe_redirect( \home_url() );
				exit;
			}
			return;
		}

		$user = \wp_get_current_user();
		$is_nda_page = \is_page( $this->nda_page_slug );

		// Allow administrators and staff to bypass
		if ( Dealer_Role::can_bypass_dealer_restrictions( $user ) ) {
			if ( $is_nda_page ) {
				\wp_safe_redirect( \admin_url() );
				exit;
			}
			return;
		}

		// Only restrict dealer role
		if ( ! Dealer_Role::is_dealer( $user ) ) {
			if ( $is_nda_page ) {
				\wp_safe_redirect( \admin_url() );
				exit;
			}
			return;
		}

		// Dealer hasn't accepted NDA
		if ( ! $this->acceptance_manager->is_accepted( $user->ID ) ) {
			// Get portal pages
			$portal_pages = \get_option( 'jblund_dealers_portal_pages', array() );
			$password_change_page_id = isset( $portal_pages['password_change'] ) ? $portal_pages['password_change'] : 0;

			// Allow password change page
			if ( $password_change_page_id && \is_page( $password_change_page_id ) ) {
				return;
			}

			// Allow NDA page itself
			if ( $is_nda_page ) {
				return;
			}

			// Allow logout
			if ( isset( $_GET['action'] ) && $_GET['action'] === 'logout' ) {
				return;
			}

			// Check if current page is a portal page
			$current_page_id = \get_the_ID();
			$is_portal_page = false;

			foreach ( $portal_pages as $page_type => $page_id ) {
				if ( $page_id && $current_page_id === $page_id ) {
					$is_portal_page = true;
					break;
				}
			}

			// Redirect to NDA if accessing portal page
			if ( $is_portal_page ) {
				\wp_safe_redirect( $this->get_nda_page_url() );
				exit;
			}
		}
		// Don't redirect away from NDA page if already accepted - allow viewing
	}

	/**
	 * Hide WordPress admin bar for dealers only
	 *
	 * @return void
	 */
	public function hide_admin_bar_for_dealers() {
		if ( ! \is_user_logged_in() ) {
			return;
		}

		$current_user = \wp_get_current_user();

		// Don't hide for elevated users
		if ( Dealer_Role::can_bypass_dealer_restrictions( $current_user ) ) {
			return;
		}

		// Hide for dealers
		if ( Dealer_Role::is_dealer( $current_user ) ) {
			\show_admin_bar( false );
		}
	}

	/**
	 * Check if we're in a page builder context
	 *
	 * @return bool True if in builder context
	 */
	private function is_builder_context() {
		// Admin area
		if ( \is_admin() ) {
			return true;
		}

		// Post editor
		global $pagenow;
		if ( \in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return true;
		}

		// AJAX
		if ( \defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}

		if ( \function_exists( 'wp_doing_ajax' ) && \wp_doing_ajax() ) {
			return true;
		}

		// REST API
		if ( \defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		// Divi Builder
		if ( isset( $_GET['et_fb'] ) || isset( $_POST['et_fb'] ) ||
		     isset( $_GET['et_bfb'] ) || isset( $_POST['et_bfb'] ) ||
		     isset( $_GET['et_pb_preview'] ) ) {
			return true;
		}

		// Elementor
		if ( isset( $_GET['elementor-preview'] ) ) {
			return true;
		}

		// Beaver Builder
		if ( isset( $_GET['fl_builder'] ) ) {
			return true;
		}

		// Divi theme check
		if ( \function_exists( 'et_fb_is_enabled' ) && \et_fb_is_enabled() ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the URL to the NDA acceptance page
	 *
	 * @return string NDA page URL.
	 */
	private function get_nda_page_url() {
		if ( \function_exists( 'jblund_get_portal_page_url' ) ) {
			$url = \jblund_get_portal_page_url( 'nda' );
			if ( $url ) {
				return $url;
			}
		}

		return \home_url( '/' . $this->nda_page_slug . '/' );
	}

	/**
	 * Get the URL to the dealer dashboard page
	 *
	 * @return string Dashboard page URL.
	 */
	private function get_dashboard_page_url() {
		if ( \function_exists( 'jblund_get_portal_page_url' ) ) {
			$url = \jblund_get_portal_page_url( 'dashboard' );
			if ( $url ) {
				return $url;
			}
		}

		return \home_url( '/dealer-dashboard/' );
	}
}
