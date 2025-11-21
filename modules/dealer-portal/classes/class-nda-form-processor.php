<?php
/**
 * NDA Form Processor
 *
 * Handles NDA acceptance form submissions
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
 * Class NDA_Form_Processor
 *
 * Processes NDA acceptance and decline actions
 */
class NDA_Form_Processor {

	/**
	 * NDA acceptance manager instance
	 *
	 * @var NDA_Acceptance_Manager
	 */
	private $acceptance_manager;

	/**
	 * Constructor
	 *
	 * @param NDA_Acceptance_Manager $acceptance_manager NDA acceptance manager
	 */
	public function __construct( $acceptance_manager ) {
		$this->acceptance_manager = $acceptance_manager;

		\add_action( 'init', array( $this, 'process_form_submission' ) );
	}

	/**
	 * Process NDA acceptance form submission
	 *
	 * @return void
	 */
	public function process_form_submission() {
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
			$this->handle_acceptance( $user_id );
		} elseif ( isset( $_POST['jblund_decline_nda'] ) ) {
			$this->handle_decline();
		}
	}

	/**
	 * Handle NDA acceptance
	 *
	 * @param int $user_id User ID
	 * @return void
	 */
	private function handle_acceptance( $user_id ) {
		// Get signature data
		$signature_data = isset( $_POST['signature_data'] ) ? \sanitize_textarea_field( \wp_unslash( $_POST['signature_data'] ) ) : '';

		// Store acceptance
		$acceptance_data = $this->acceptance_manager->accept_nda( $user_id, $signature_data );

		// Publish dealer post
		$this->publish_dealer_post( $user_id );

		// Send admin notification email
		$this->send_admin_notification( $user_id, $acceptance_data );

		// Trigger action for PDF generation and email
		\do_action( 'jblund_dealer_nda_accepted', $user_id, $signature_data );

		// Redirect to NDA page with success parameter
		$nda_url = \jblund_get_portal_page_url( 'nda' );
		if ( ! $nda_url ) {
			$nda_url = \home_url( '/dealer-nda-acceptance/' );
		}
		
		\wp_safe_redirect( \add_query_arg( 'nda_accepted', '1', $nda_url ) );
		exit;
	}

	/**
	 * Handle NDA decline
	 *
	 * @return void
	 */
	private function handle_decline() {
		// Log user out
		\wp_logout();
		\wp_safe_redirect( \home_url() );
		exit;
	}

	/**
	 * Send admin notification email when NDA is accepted
	 *
	 * @param int $user_id User ID.
	 * @param array $acceptance_data Acceptance data.
	 * @return void
	 */
	private function send_admin_notification( $user_id, $acceptance_data ) {
		$user = \get_userdata( $user_id );
		if ( ! $user ) {
			return;
		}

		// Get admin email from settings or use default
		$settings = \get_option( 'jblund_dealers_settings', array() );
		$admin_email = isset( $settings['admin_notification_email'] ) ? $settings['admin_notification_email'] : \get_option( 'admin_email' );

		// Email subject
		$subject = \sprintf(
			\__( '[%s] Dealer NDA Accepted - %s', 'jblund-dealers' ),
			\get_bloginfo( 'name' ),
			$acceptance_data['representative_name']
		);

		// Format acceptance date
		$acceptance_date = \date_i18n(
			\get_option( 'date_format' ) . ' ' . \get_option( 'time_format' ),
			\strtotime( $acceptance_data['acceptance_date'] )
		);

		// NDA page URL
		$nda_url = \jblund_get_portal_page_url( 'nda' );
		if ( ! $nda_url ) {
			$nda_url = \home_url( '/dealer-nda-acceptance/' );
		}

		// Email body
		$message = \sprintf(
			\__( "A dealer has accepted the Non-Disclosure Agreement.\n\n" .
				"Representative Name: %s\n" .
				"Dealer Company: %s\n" .
				"User Email: %s\n" .
				"Username: %s\n" .
				"IP Address: %s\n" .
				"Acceptance Date: %s\n\n" .
				"The dealer's post has been automatically published and they now have full portal access.\n\n" .
				"View Dealer Profile:\n%s\n\n" .
				"View Signed NDA:\n%s",
				'jblund-dealers'
			),
			$acceptance_data['representative_name'],
			$acceptance_data['dealer_company'],
			$user->user_email,
			$user->user_login,
			$acceptance_data['ip_address'],
			$acceptance_date,
			\admin_url( 'user-edit.php?user_id=' . $user_id ),
			$nda_url
		);

		// Email headers
		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'From: ' . \get_bloginfo( 'name' ) . ' <' . \get_option( 'admin_email' ) . '>',
		);

		// Send email
		\wp_mail( $admin_email, $subject, $message, $headers );
	}

	/**
	 * Publish dealer post when NDA is signed
	 *
	 * @param int $user_id User ID who signed the NDA
	 * @return bool True if dealer post was published
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

		// Publish the post
		$result = \wp_update_post(
			array(
				'ID'          => $dealer_post->ID,
				'post_status' => 'publish',
			)
		);

		if ( \is_wp_error( $result ) ) {
			return false;
		}

		// Log activity
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

		$activity[] = $entry;

		\update_post_meta( $registration_id, '_registration_activity', $activity );
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
