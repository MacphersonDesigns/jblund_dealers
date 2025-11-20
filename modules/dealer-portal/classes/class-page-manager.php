<?php
/**
 * Page Manager Class
 *
 * Handles automatic creation and management of dealer portal pages.
 * Creates essential pages on plugin activation:
 * - Dealer Dashboard
 * - Dealer Profile
 * - Dealer Login
 * - Dealer NDA Acceptance
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
 * Page Manager class for dealer portal
 */
class Page_Manager {

	/**
	 * Option name for storing created page IDs
	 *
	 * @var string
	 */
	private $pages_option = 'jblund_dealer_portal_pages';

	/**
	 * Page definitions
	 *
	 * @var array
	 */
	private $pages = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->define_pages();
	}

	/**
	 * Define all dealer portal pages
	 *
	 * @return void
	 */
	private function define_pages() {
		$this->pages = array(
			'dashboard' => array(
				'title'     => \__( 'Dealer Dashboard', 'jblund-dealers' ),
				'slug'      => 'dealer-dashboard',
				'shortcode' => '[jblund_dealer_dashboard]',
				'template'  => 'dealer-dashboard',
			),
			'profile'   => array(
				'title'     => \__( 'Dealer Profile', 'jblund-dealers' ),
				'slug'      => 'dealer-profile',
				'shortcode' => '[jblund_dealer_profile]',
				'template'  => 'dealer-profile',
			),
			'login'     => array(
				'title'     => \__( 'Dealer Login', 'jblund-dealers' ),
				'slug'      => 'dealer-login',
				'shortcode' => '[jblund_dealer_login]',
				'template'  => 'dealer-login',
			),
			'password_change' => array(
				'title'     => \__( 'Change Password', 'jblund-dealers' ),
				'slug'      => 'dealer-password-change',
				'shortcode' => '[jblund_force_password_change]',
				'template'  => 'password-change',
			),
			'nda'       => array(
				'title'     => \__( 'Dealer NDA Acceptance', 'jblund-dealers' ),
				'slug'      => 'dealer-nda-acceptance',
				'shortcode' => '[jblund_nda_acceptance]',
				'template'  => 'nda-acceptance-page',
			),
		);
	}

	/**
	 * Create all dealer portal pages
	 *
	 * Called on plugin activation
	 *
	 * @return void
	 */
	public function create_pages() {
		$created_pages = array();

		foreach ( $this->pages as $page_key => $page_data ) {
			$page_id = $this->create_page( $page_data );

			if ( $page_id ) {
				$created_pages[ $page_key ] = $page_id;
			}
		}

		// Store created page IDs
		\update_option( $this->pages_option, $created_pages );
	}

	/**
	 * Create a single page if it doesn't exist
	 *
	 * @param array $page_data Page configuration array.
	 * @return int|false Page ID on success, false on failure.
	 */
	private function create_page( $page_data ) {
		// Check if page already exists
		$existing_page = $this->get_page_by_slug( $page_data['slug'] );
		if ( $existing_page ) {
			// Update Divi settings if Divi is active and page exists
			if ( $this->is_divi_active() ) {
				$this->set_divi_meta( $existing_page->ID, $page_data['shortcode'] );
			}
			return $existing_page->ID;
		}

		// Prepare meta input
		$meta_input = array(
			'_jblund_dealer_portal_page' => true,
		);

		// Add Divi-specific meta if Divi is active
		if ( $this->is_divi_active() ) {
			$meta_input['_et_pb_use_builder']        = 'on';
			$meta_input['_et_pb_page_layout']        = 'et_full_width_page';
			$meta_input['_et_pb_side_nav']           = 'off';
			$meta_input['_et_pb_post_hide_nav']      = 'default';
			$meta_input['_et_pb_old_content']        = $page_data['shortcode'];
		}

		// Create new page
		$page_id = \wp_insert_post(
			array(
				'post_title'   => $page_data['title'],
				'post_name'    => $page_data['slug'],
				'post_content' => $page_data['shortcode'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_author'  => 1, // Admin user
				'meta_input'   => $meta_input,
			)
		);

		if ( \is_wp_error( $page_id ) ) {
			return false;
		}

		return $page_id;
	}

	/**
	 * Check if Divi theme is active
	 *
	 * @return bool True if Divi or Divi child theme is active.
	 */
	private function is_divi_active() {
		$theme = \wp_get_theme();
		return 'Divi' === $theme->name || 'Divi' === $theme->parent_theme;
	}

	/**
	 * Set Divi-specific meta fields for a page
	 *
	 * @param int    $page_id Page ID.
	 * @param string $shortcode Shortcode content.
	 * @return void
	 */
	private function set_divi_meta( $page_id, $shortcode ) {
		\update_post_meta( $page_id, '_et_pb_use_builder', 'on' );
		\update_post_meta( $page_id, '_et_pb_page_layout', 'et_full_width_page' );
		\update_post_meta( $page_id, '_et_pb_side_nav', 'off' );
		\update_post_meta( $page_id, '_et_pb_post_hide_nav', 'default' );
		\update_post_meta( $page_id, '_et_pb_old_content', $shortcode );
	}

	/**
	 * Get page by slug
	 *
	 * @param string $slug Page slug.
	 * @return \WP_Post|null Page object or null if not found.
	 */
	private function get_page_by_slug( $slug ) {
		$query = new \WP_Query(
			array(
				'post_type'      => 'page',
				'name'           => $slug,
				'post_status'    => array( 'publish', 'draft', 'pending' ),
				'posts_per_page' => 1,
			)
		);

		if ( $query->have_posts() ) {
			return $query->posts[0];
		}

		return null;
	}

	/**
	 * Get a created page ID by key
	 *
	 * @param string $page_key Page key (dashboard, profile, login, nda).
	 * @return int|null Page ID or null if not found.
	 */
	public function get_page_id( $page_key ) {
		$pages = \get_option( $this->pages_option, array() );
		return $pages[ $page_key ] ?? null;
	}

	/**
	 * Get a page URL by key
	 *
	 * @param string $page_key Page key (dashboard, profile, login, nda).
	 * @return string|null Page URL or null if not found.
	 */
	public function get_page_url( $page_key ) {
		$page_id = $this->get_page_id( $page_key );
		if ( ! $page_id ) {
			return null;
		}

		return \get_permalink( $page_id );
	}

	/**
	 * Delete all dealer portal pages
	 *
	 * Called on plugin uninstall (not deactivation)
	 *
	 * @param bool $force_delete Whether to bypass trash and permanently delete.
	 * @return void
	 */
	public function delete_pages( $force_delete = false ) {
		$pages = \get_option( $this->pages_option, array() );

		foreach ( $pages as $page_id ) {
			\wp_delete_post( $page_id, $force_delete );
		}

		\delete_option( $this->pages_option );
	}

	/**
	 * Get all page definitions
	 *
	 * @return array Page definitions.
	 */
	public function get_pages() {
		return $this->pages;
	}

	/**
	 * Check if all required pages exist
	 *
	 * @return bool True if all pages exist, false otherwise.
	 */
	public function pages_exist() {
		$pages = \get_option( $this->pages_option, array() );

		// Check if we have all required pages
		if ( count( $pages ) !== count( $this->pages ) ) {
			return false;
		}

		// Verify each page still exists
		foreach ( $pages as $page_id ) {
			if ( ! \get_post( $page_id ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Recreate missing pages
	 *
	 * Can be called from admin notice or manually
	 *
	 * @return int Number of pages created.
	 */
	public function recreate_missing_pages() {
		$created_pages = \get_option( $this->pages_option, array() );
		$count         = 0;

		foreach ( $this->pages as $page_key => $page_data ) {
			// Skip if page exists
			if ( isset( $created_pages[ $page_key ] ) && \get_post( $created_pages[ $page_key ] ) ) {
				continue;
			}

			// Create missing page
			$page_id = $this->create_page( $page_data );
			if ( $page_id ) {
				$created_pages[ $page_key ] = $page_id;
				$count++;
			}
		}

		// Update option
		\update_option( $this->pages_option, $created_pages );

		return $count;
	}
}
