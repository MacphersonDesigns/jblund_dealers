<?php
/**
 * NDA Handler Class
 *
 * Main coordinator for NDA acceptance functionality.
 * Delegates to specialized classes for acceptance management,
 * access restriction, and form processing.
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
 * NDA Handler class - Main coordinator
 */
class NDA_Handler {

	/**
	 * NDA acceptance manager
	 *
	 * @var NDA_Acceptance_Manager
	 */
	private $acceptance_manager;

	/**
	 * NDA access restrictor
	 *
	 * @var NDA_Access_Restrictor
	 */
	private $access_restrictor;

	/**
	 * NDA form processor
	 *
	 * @var NDA_Form_Processor
	 */
	private $form_processor;

	/**
	 * NDA page slug
	 *
	 * @var string
	 */
	private $nda_page_slug = 'dealer-nda-acceptance';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Load sub-components
		require_once __DIR__ . '/class-nda-acceptance-manager.php';
		require_once __DIR__ . '/class-nda-access-restrictor.php';
		require_once __DIR__ . '/class-nda-form-processor.php';

		// Initialize managers
		$this->acceptance_manager = new NDA_Acceptance_Manager();
		$this->access_restrictor  = new NDA_Access_Restrictor( $this->acceptance_manager );
		$this->form_processor     = new NDA_Form_Processor( $this->acceptance_manager );

		// Register shortcode
		\add_shortcode( 'jblund_nda_acceptance', array( $this, 'nda_acceptance_shortcode' ) );

		// Enqueue scripts for NDA page
		\add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts for NDA acceptance page
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		global $post;

		// Only enqueue on pages with the NDA shortcode
		if ( ! \is_a( $post, 'WP_Post' ) || ! \has_shortcode( $post->post_content, 'jblund_nda_acceptance' ) ) {
			return;
		}

		// Enqueue Signature Pad library from CDN
		\wp_enqueue_script(
			'signature-pad-lib',
			'https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js',
			array(),
			'4.1.7',
			true
		);

		// Enqueue our custom signature pad integration
		\wp_enqueue_script(
			'jblund-signature-pad',
			JBLUND_DEALERS_PLUGIN_URL . 'modules/dealer-portal/assets/js/signature-pad.js',
			array( 'jquery', 'signature-pad-lib' ),
			JBLUND_DEALERS_VERSION,
			true
		);
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
	 * Get NDA acceptance data for a user
	 *
	 * Public API method for external access
	 *
	 * @param int|null $user_id Optional. User ID. Defaults to current user.
	 * @return array|null Acceptance data array or null if not found.
	 */
	public function get_acceptance_data( $user_id = null ) {
		return $this->acceptance_manager->get_acceptance_data( $user_id );
	}

	/**
	 * Create the NDA acceptance page if it doesn't exist
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
}
