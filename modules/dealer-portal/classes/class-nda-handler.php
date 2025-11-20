<?php
/**
 * NDA Handler Class
 *
 * Handles dealer NDA acceptance workflow including:
 * - Login redirects to NDA page
 * - Access restriction until NDA is accepted
 * - Form processing for NDA acceptance
 *
 * Adapted from login-terms-acceptance plugin patterns
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 1.0.0
 */

namespace JBLund\DealerPortal;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NDA Handler class for dealer portal
 */
class NDA_Handler {

	/**
	 * The page slug for NDA acceptance
	 *
	 * @var string
	 */
	private $nda_page_slug = 'dealer-nda-acceptance';

	/**
	 * User meta key for NDA acceptance data
	 *
	 * @var string
	 */
	private $acceptance_meta_key = '_dealer_nda_acceptance';

	/**
	 * User meta key for quick NDA status check
	 *
	 * @var string
	 */
	private $accepted_meta_key = '_dealer_nda_accepted';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Hook into WordPress
		\add_action( 'wp_login', array( $this, 'redirect_to_nda_after_login' ), 10, 2 );

		// Only add template_redirect hook if not in builder/admin context
		// This prevents conflicts with Divi Builder and other page builders
		if ( ! $this->is_builder_context() ) {
			\add_action( 'template_redirect', array( $this, 'restrict_dealer_portal_access' ) );
		}

		\add_action( 'init', array( $this, 'process_nda_acceptance' ) );
		\add_shortcode( 'jblund_nda_acceptance', array( $this, 'nda_acceptance_shortcode' ) );

		// Hide admin bar for dealers only (keep it visible for admin/staff)
		\add_action( 'after_setup_theme', array( $this, 'hide_admin_bar_for_dealers' ) );
	}

	/**
	 * Check if we're in a page builder context
	 *
	 * @return bool True if in builder context
	 */
	private function is_builder_context() {
		// Check for admin area
		if ( \is_admin() ) {
			return true;
		}

		// Check if we're in the post editor
		global $pagenow;
		if ( \in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return true;
		}

		// Check for AJAX
		if ( \defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}

		if ( \function_exists( 'wp_doing_ajax' ) && \wp_doing_ajax() ) {
			return true;
		}

		// Check for REST API
		if ( \defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		// Check for Divi Builder (multiple detection methods)
		if ( isset( $_GET['et_fb'] ) || isset( $_POST['et_fb'] ) ) {
			return true;
		}

		if ( isset( $_GET['et_bfb'] ) || isset( $_POST['et_bfb'] ) ) {
			return true;
		}

		if ( isset( $_GET['et_pb_preview'] ) ) {
			return true;
		}

		// Check for Divi page builder action
		if ( isset( $_GET['action'] ) && 'et_fb_ajax_save' === $_GET['action'] ) {
			return true;
		}

		// Check for Elementor
		if ( isset( $_GET['elementor-preview'] ) ) {
			return true;
		}

		// Check for Beaver Builder
		if ( isset( $_GET['fl_builder'] ) ) {
			return true;
		}

		// Check if Divi theme functions indicate builder is active
		if ( \function_exists( 'et_fb_is_enabled' ) && \et_fb_is_enabled() ) {
			return true;
		}

		return false;
	}

	/**
	 * Hide WordPress admin bar for dealers only
	 *
	 * Administrators and staff will still see the admin bar.
	 *
	 * @return void
	 */
	public function hide_admin_bar_for_dealers() {
		// Only hide for logged-in users
		if ( ! \is_user_logged_in() ) {
			return;
		}

		$current_user = \wp_get_current_user();

		// Don't hide admin bar for elevated users (admin/staff)
		if ( Dealer_Role::can_bypass_dealer_restrictions( $current_user ) ) {
			return;
		}

		// Hide admin bar for dealers
		if ( Dealer_Role::is_dealer( $current_user ) ) {
			\show_admin_bar( false );
		}
	}

	/**
	 * Shortcode callback for NDA acceptance form
	 *
	 * @return string HTML output of the NDA acceptance page
	 */
	public function nda_acceptance_shortcode() {
		\ob_start();
		include \plugin_dir_path( __FILE__ ) . '../templates/nda-acceptance-page.php';
		return \ob_get_clean();
	}

	/**
	 * Redirect dealer users to NDA page after login if they haven't accepted
	 *
	 * Adapted from: xlta_user_redirect_after_login()
	 *
	 * @param string  $user_login Username.
	 * @param \WP_User $user WP_User object of the logged-in user.
	 * @return void
	 */
	public function redirect_to_nda_after_login( $user_login, $user ) {
		// Allow administrators and staff to bypass NDA requirements
		if ( Dealer_Role::can_bypass_dealer_restrictions( $user ) ) {
			return;
		}

		// Check if user is a dealer
		if ( ! Dealer_Role::is_dealer( $user ) ) {
			return;
		}

		// Check if NDA already accepted
		if ( $this->is_nda_accepted( $user->ID ) ) {
			return;
		}

		// Redirect to NDA acceptance page
		\wp_safe_redirect( $this->get_nda_page_url() );
		exit;
	}

	/**
	 * Restrict dealer portal pages until NDA is accepted
	 *
	 * Runs on template_redirect to check if dealer has accepted NDA.
	 * If not, redirect to NDA acceptance page.
	 *
	 * @return void
	 */
	public function restrict_dealer_portal_access() {
		// Don't run during AJAX requests
		if ( \defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( \wp_doing_ajax() ) {
			return;
		}

		// Don't run in admin area
		if ( \is_admin() ) {
			return;
		}

		// Don't interfere with Divi Builder (multiple checks)
		if ( \function_exists( 'et_core_is_fb_enabled' ) && \et_core_is_fb_enabled() ) {
			return;
		}

		// Check for any Divi-related query parameters
		if ( isset( $_GET['et_fb'] ) || isset( $_POST['et_fb'] ) ) {
			return;
		}

		if ( isset( $_GET['et_bfb'] ) || isset( $_POST['et_bfb'] ) ) {
			return;
		}

		if ( isset( $_GET['et_pb_preview'] ) ) {
			return;
		}

		// Don't interfere with REST API requests
		if ( \defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		// Check for WordPress JSON API
		if ( \defined( 'JSON_REQUEST' ) && JSON_REQUEST ) {
			return;
		}

		// If not logged in, don't process
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

		// Allow administrators and staff to bypass all restrictions
		if ( Dealer_Role::can_bypass_dealer_restrictions( $user ) ) {
			// High-level users shouldn't see NDA page
			if ( $is_nda_page ) {
				\wp_safe_redirect( \admin_url() );
				exit;
			}
			return;
		}

		// Only restrict dealer role
		if ( ! Dealer_Role::is_dealer( $user ) ) {
			// Non-dealers shouldn't access NDA page
			if ( $is_nda_page ) {
				\wp_safe_redirect( \admin_url() );
				exit;
			}
			return;
		}

		// Dealer hasn't accepted NDA
		if ( ! $this->is_nda_accepted( $user->ID ) ) {
			// Get all portal pages from settings
			$portal_pages = \get_option( 'jblund_dealers_portal_pages', array() );
			$password_change_page_id = isset( $portal_pages['password_change'] ) ? $portal_pages['password_change'] : 0;

			// Allow access to password change page (if it exists)
			if ( $password_change_page_id && \is_page( $password_change_page_id ) ) {
				return;
			}

			// Allow access to NDA page itself
			if ( $is_nda_page ) {
				return;
			}

			// Allow logout action
			if ( isset( $_GET['action'] ) && $_GET['action'] === 'logout' ) {
				return;
			}

			// Block ALL dealer-only pages until NDA is signed
			// Check if current page is any portal page
			$current_page_id = \get_the_ID();
			$is_portal_page = false;

			foreach ( $portal_pages as $page_type => $page_id ) {
				if ( $page_id && $current_page_id === $page_id ) {
					$is_portal_page = true;
					break;
				}
			}

			// Redirect to NDA if accessing any dealer portal page
			if ( $is_portal_page ) {
				\wp_safe_redirect( $this->get_nda_page_url() );
				exit;
			}
		} elseif ( $is_nda_page ) {
			// Already accepted, redirect away from NDA page
			\wp_safe_redirect( $this->get_dashboard_page_url() );
			exit;
		}
	}

	/**
	 * Process NDA acceptance form submission
	 *
	 * Adapted from: xlta_terms_acceptance_actions()
	 *
	 * @return void
	 */
	public function process_nda_acceptance() {
		// Check if form was submitted
		if ( ! isset( $_POST['jblund_nda_action'] ) ) {
			return;
		}

		// Verify nonce
		if ( ! isset( $_POST['accept_nda_nonce'] ) ||
			! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['accept_nda_nonce'] ) ), 'accept_nda_action' )
		) {
			return;
		}

		// User must be logged in
		if ( ! \is_user_logged_in() ) {
			return;
		}

		$user_id = \get_current_user_id();
		$user    = \wp_get_current_user();

		// User must be a dealer
		if ( ! \in_array( 'dealer', (array) $user->roles, true ) ) {
			return;
		}

		// Handle acceptance
		if ( isset( $_POST['jblund_accept_nda'] ) ) {
			// Get signature data (will be base64 from canvas)
			$signature_data = isset( $_POST['signature_data'] ) ? \sanitize_textarea_field( \wp_unslash( $_POST['signature_data'] ) ) : '';

			// Create acceptance data
			$acceptance_data = array(
				'accepted'        => true,
				'acceptance_date' => \current_time( 'mysql' ),
				'acceptance_ip'   => \sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ),
				'signature_data'  => $signature_data,
				'user_agent'      => \sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ),
			);

			// Store acceptance data
			\update_user_meta( $user_id, $this->acceptance_meta_key, \wp_json_encode( $acceptance_data ) );
			\update_user_meta( $user_id, $this->accepted_meta_key, '1' );

            // Publish dealer post now that NDA is signed
            $this->publish_dealer_post( $user_id );

            // Trigger action for PDF generation and email
            \do_action( 'jblund_dealer_nda_accepted', $user_id, $signature_data );

            // Redirect to dealer dashboard
            \wp_safe_redirect( $this->get_dashboard_page_url() );
            exit;		} elseif ( isset( $_POST['jblund_decline_nda'] ) ) {
			// User declined - log them out
			\wp_logout();
			\wp_safe_redirect( \home_url() );
			exit;
		}
	}

	/**
	 * Check if user has accepted the NDA
	 *
	 * Adapted from: xlta_is_terms_accepted()
	 *
	 * @param int|null $user_id Optional. User ID. Defaults to current user.
	 * @return bool True if NDA is accepted, false otherwise.
	 */
	private function is_nda_accepted( $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = \get_current_user_id();
		}

		// Quick check first
		$accepted = \get_user_meta( $user_id, $this->accepted_meta_key, true );
		if ( $accepted === '1' ) {
			return true;
		}

		// Fallback to detailed check
		$acceptance_data = \get_user_meta( $user_id, $this->acceptance_meta_key, true );
		if ( empty( $acceptance_data ) ) {
			return false;
		}

		$data = \json_decode( $acceptance_data, true );
		if ( null !== $data && isset( $data['accepted'] ) ) {
			return (bool) $data['accepted'];
		}

		return false;
	}

	/**
	 * Get the URL to the NDA acceptance page
	 *
	 * Adapted from: xlta_get_terms_page_redirect_url()
	 *
	 * @return string NDA page URL.
	 */
	private function get_nda_page_url() {
		// Try to get assigned page URL first
		if ( \function_exists( 'jblund_get_portal_page_url' ) ) {
			$url = \jblund_get_portal_page_url( 'nda' );
			if ( $url ) {
				return $url;
			}
		}

		// Fallback to hardcoded slug
		return \home_url( '/' . $this->nda_page_slug . '/' );
	}

	/**
	 * Get the URL to the dealer dashboard page
	 *
	 * @return string Dashboard page URL.
	 */
	private function get_dashboard_page_url() {
		// Try to get assigned page URL first
		if ( \function_exists( 'jblund_get_portal_page_url' ) ) {
			$url = \jblund_get_portal_page_url( 'dashboard' );
			if ( $url ) {
				return $url;
			}
		}

		// Fallback to hardcoded slug
		return \home_url( '/dealer-dashboard/' );
	}

	/**
	 * Get NDA acceptance data for a user
	 *
	 * @param int|null $user_id Optional. User ID. Defaults to current user.
	 * @return array|null Acceptance data array or null if not found.
	 */
	public function get_acceptance_data( $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = \get_current_user_id();
		}

		$acceptance_data = \get_user_meta( $user_id, $this->acceptance_meta_key, true );
		if ( empty( $acceptance_data ) ) {
			return null;
		}

		return \json_decode( $acceptance_data, true );
	}

	/**
	 * Create the NDA acceptance page if it doesn't exist
	 *
	 * Adapted from: xlta_create_terms_acceptance_page()
	 *
	 * @return int|false Page ID on success, false on failure.
	 */
	public function create_nda_page() {
		// Check if page already exists
		$query = new \WP_Query(
			array(
				'post_type'      => 'page',
				'name'           => $this->nda_page_slug,
				'post_status'    => array( 'publish', 'draft', 'pending' ),
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( $query->have_posts() ) {
			return $query->posts[0];
		}

		// Create new page
		$page_id = \wp_insert_post(
			array(
				'post_title'   => \__( 'Dealer NDA Acceptance', 'jblund-dealers' ),
				'post_name'    => $this->nda_page_slug,
				'post_content' => '[jblund_nda_acceptance]',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_author'  => \get_current_user_id() ? \get_current_user_id() : 1,
			)
		);

		if ( \is_wp_error( $page_id ) ) {
			return false;
		}

		return $page_id;
	}

	/**
	 * Publish dealer post when NDA is signed
	 *
	 * Finds the dealer post associated with this user and publishes it
	 *
	 * @param int $user_id User ID who signed the NDA
	 * @return bool True if dealer post was published, false otherwise
	 */
	private function publish_dealer_post( $user_id ) {
		// Find dealer post by user_id
		$dealer_posts = \get_posts(
			array(
				'post_type'      => 'dealer',
				'post_status'    => 'draft',
				'posts_per_page' => 1,
				'meta_query'     => array(
					array(
						'key'     => '_dealer_user_id',
						'value'   => $user_id,
						'compare' => '=',
					),
				),
			)
		);

		if ( empty( $dealer_posts ) ) {
			return false;
		}

		$dealer_post = $dealer_posts[0];

		// Publish the dealer post
		$result = \wp_update_post(
			array(
				'ID'          => $dealer_post->ID,
				'post_status' => 'publish',
			)
		);

		if ( \is_wp_error( $result ) ) {
			return false;
		}

		// Log activity on the associated registration if it exists
		$registration_id = \get_post_meta( $dealer_post->ID, '_dealer_registration_id', true );
		if ( $registration_id ) {
			$this->log_dealer_publish_activity( $registration_id, $dealer_post->ID, $user_id );
		}

		return true;
	}

	/**
	 * Log activity when dealer post is published via NDA acceptance
	 *
	 * @param int $registration_id Registration post ID
	 * @param int $dealer_id Dealer post ID
	 * @param int $user_id User ID
	 * @return void
	 */
	private function log_dealer_publish_activity( $registration_id, $dealer_id, $user_id ) {
		// Get current user info
		$user      = \get_userdata( $user_id );
		$user_name = $user ? $user->display_name : $user->user_login;

		// Get existing activity log
		$activity = \get_post_meta( $registration_id, '_registration_activity', true );
		if ( ! is_array( $activity ) ) {
			$activity = array();
		}

		// Create new activity entry
		$entry = array(
			'action'    => 'dealer_published',
			'user'      => \sanitize_text_field( $user_name ),
			'user_id'   => $user_id,
			'timestamp' => \current_time( 'mysql' ),
			'note'      => 'NDA signed - Dealer profile published and now visible on website',
			'dealer_id' => $dealer_id,
		);

		// Add to activity log
		$activity[] = $entry;

		// Save updated activity
		\update_post_meta( $registration_id, '_registration_activity', $activity );
	}
}
