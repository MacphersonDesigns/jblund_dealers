<?php
/**
 * NDA Acceptance Manager
 *
 * Handles NDA acceptance form processing and data storage
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
 * Class NDA_Acceptance_Manager
 *
 * Manages NDA acceptance data and form processing
 */
class NDA_Acceptance_Manager {

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
	 * Check if user has accepted the NDA
	 *
	 * @param int|null $user_id Optional. User ID. Defaults to current user.
	 * @return bool True if NDA is accepted, false otherwise.
	 */
	public function is_accepted( $user_id = null ) {
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
	 * Process NDA acceptance
	 *
	 * @param int    $user_id User ID
	 * @param string $signature_data Base64 signature data
	 * @return bool True on success
	 */
	public function accept_nda( $user_id, $signature_data = '' ) {
		// Get representative name and company from POST data
		$representative_name = isset( $_POST['representative_name'] ) ? \sanitize_text_field( \wp_unslash( $_POST['representative_name'] ) ) : '';
		$dealer_company = isset( $_POST['dealer_company'] ) ? \sanitize_text_field( \wp_unslash( $_POST['dealer_company'] ) ) : '';

		// Create acceptance data
		$acceptance_data = array(
			'accepted'            => true,
			'acceptance_date'     => \current_time( 'mysql' ),
			'ip_address'          => \sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ),
			'signature_data'      => $signature_data,
			'user_agent'          => \sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ),
			'representative_name' => $representative_name,
			'dealer_company'      => $dealer_company,
		);

		// Store acceptance data
		\update_user_meta( $user_id, $this->acceptance_meta_key, \wp_json_encode( $acceptance_data ) );
		\update_user_meta( $user_id, $this->accepted_meta_key, '1' );

		return $acceptance_data;
	}
}
