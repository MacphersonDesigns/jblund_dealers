<?php
/**
 * Handles email functions for dealer portal.
 *
 * Adapted from login-terms-acceptance plugin's Mail_Handler class.
 * Manages email delivery for NDA confirmations and admin notifications.
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 2.0.0
 */

namespace JBLund\DealerPortal;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Exception;

/**
 * Class Email_Handler
 *
 * Handles email-related functionalities for dealer portal NDA acceptance.
 *
 * @package JBLund\DealerPortal
 */
class Email_Handler {

	/**
	 * Send NDA confirmation email to dealer with PDF attachment.
	 *
	 * @param int    $user_id   User ID of the dealer.
	 * @param string $pdf_path  Full path to generated PDF file.
	 * @return bool True on success, false on failure.
	 */
	public function send_nda_confirmation( $user_id, $pdf_path ) {
		try {
			$user = \get_userdata( $user_id );
			if ( ! $user ) {
				return false;
			}

			$dealer_id   = \get_user_meta( $user_id, '_linked_dealer_id', true );
			$dealer      = \get_post( $dealer_id );
			$dealer_name = $dealer ? $dealer->post_title : 'Unknown Dealer';

			$headers = array(
				'Content-Type: text/html; charset=UTF-8',
			);
			$subject = \__( 'NDA Acceptance Confirmation - JB Lund Dock B2B', 'jblund-dealers' );
			$to      = $user->user_email;
			$message = $this->get_mail_message( 'dealer-nda-confirmation', array(
				'user_name'       => $user->display_name,
				'dealer_name'     => $dealer_name,
				'acceptance_date' => \date_i18n( 'F j, Y' ),
			) );

			$attachments = array();
			if ( $pdf_path && \file_exists( $pdf_path ) ) {
				$attachments[] = $pdf_path;
			}

			$result = \wp_mail( $to, $subject, $message, $headers, $attachments );

			if ( ! $result ) {
				\error_log( 'JBLund: Failed to send NDA confirmation email to ' . $to );
			}

			return $result;

		} catch ( Exception $e ) {
			if ( \defined( 'WP_DEBUG_LOG' ) && \WP_DEBUG_LOG ) {
				\error_log( 'JBLund: Error sending NDA confirmation: ' . $e->getMessage() );
			}
			return false;
		}
	}

	/**
	 * Send admin notification email when dealer accepts NDA.
	 *
	 * @param int    $user_id   User ID of the dealer.
	 * @param string $pdf_path  Full path to generated PDF file.
	 * @return bool True on success, false on failure.
	 */
	public function send_admin_notification( $user_id, $pdf_path ) {
		try {
			$user = \get_userdata( $user_id );
			if ( ! $user ) {
				return false;
			}

			$dealer_id   = \get_user_meta( $user_id, '_linked_dealer_id', true );
			$dealer      = \get_post( $dealer_id );
			$dealer_name = $dealer ? $dealer->post_title : 'Unknown Dealer';

			$headers = array(
				'Content-Type: text/html; charset=UTF-8',
			);
			$subject = \sprintf( \__( 'New Dealer NDA Accepted - %s', 'jblund-dealers' ), $dealer_name );
			$to      = \get_option( 'admin_email' );
			$message = $this->get_mail_message( 'admin-nda-notification', array(
				'user_name'       => $user->display_name,
				'user_email'      => $user->user_email,
				'dealer_name'     => $dealer_name,
				'acceptance_date' => \date_i18n( 'F j, Y' ),
			) );

			$attachments = array();
			if ( $pdf_path && \file_exists( $pdf_path ) ) {
				$attachments[] = $pdf_path;
			}

			$result = \wp_mail( $to, $subject, $message, $headers, $attachments );

			if ( ! $result ) {
				\error_log( 'JBLund: Failed to send admin NDA notification' );
			}

			return $result;

		} catch ( Exception $e ) {
			if ( \defined( 'WP_DEBUG_LOG' ) && \WP_DEBUG_LOG ) {
				\error_log( 'JBLund: Error sending admin notification: ' . $e->getMessage() );
			}
			return false;
		}
	}

	/**
	 * Get the content of the email message from template.
	 *
	 * Adapted from XLTA's get_mail_message() pattern using ob_start/ob_get_clean.
	 *
	 * @param string $template  Template filename (without .php extension).
	 * @param array  $vars      Variables to extract for use in template.
	 * @return string The rendered email HTML content.
	 */
	private function get_mail_message( $template, $vars = array() ) {
		// Extract variables for use in template
		extract( $vars );

		$template_path = dirname( __DIR__ ) . '/templates/emails/' . $template . '.php';

		if ( ! \file_exists( $template_path ) ) {
			\error_log( 'JBLund: Email template not found: ' . $template_path );
			return '';
		}

		ob_start();
			include $template_path;
		return ob_get_clean();
	}
}
