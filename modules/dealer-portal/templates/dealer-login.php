<?php
/**
 * Dealer Login Template
 *
 * This template is loaded via the [jblund_dealer_login] shortcode.
 * Simple login form for dealers with redirect to dashboard after login.
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Show message if already logged in (no redirect to avoid header errors)
if ( is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	if ( in_array( 'dealer', (array) $current_user->roles, true ) ) {
		echo '<div class="dealer-already-logged-in success">';
		echo '<h2>Already Logged In</h2>';
		echo '<p>You are already logged in as a dealer.</p>';
		echo '<a href="' . esc_url( home_url( '/dealer-dashboard/' ) ) . '">Go to Dashboard</a>';
		echo '</div>';
		return;
	}
}

// Get the redirect URL (dashboard by default)
$redirect_to = isset( $_GET['redirect_to'] ) ? esc_url_raw( $_GET['redirect_to'] ) : home_url( '/dealer-dashboard/' );
?>

<div class="jblund-dealer-login">
	<div class="login-container">
		<div class="login-header">
			<h1><?php esc_html_e( 'Dealer Login', 'jblund-dealers' ); ?></h1>
			<p><?php esc_html_e( 'Sign in to access your dealer portal', 'jblund-dealers' ); ?></p>
		</div>

		<div class="login-form-wrapper">
			<?php
			// WordPress login form
			wp_login_form(
				array(
					'redirect'       => $redirect_to,
					'label_username' => __( 'Username or Email', 'jblund-dealers' ),
					'label_password' => __( 'Password', 'jblund-dealers' ),
					'label_remember' => __( 'Remember Me', 'jblund-dealers' ),
					'label_log_in'   => __( 'Sign In', 'jblund-dealers' ),
					'value_username' => '',
					'value_remember' => true,
				)
			);
			?>
		</div>

		<div class="login-links">
			<p>
				<a href="<?php echo esc_url( wp_lostpassword_url( $redirect_to ) ); ?>">
					<?php esc_html_e( 'Forgot your password?', 'jblund-dealers' ); ?>
				</a>
			</p>
			<?php
			// Get registration page URL from settings
			$portal_pages = get_option( 'jblund_dealers_portal_pages', array() );
			$registration_page_id = isset( $portal_pages['registration'] ) ? absint( $portal_pages['registration'] ) : 0;

			if ( $registration_page_id && get_post_status( $registration_page_id ) === 'publish' ) {
				// Use assigned page from settings
				$registration_url = get_permalink( $registration_page_id );
			} else {
				// Fallback: search for page with registration shortcode
				$pages_with_shortcode = get_posts( array(
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					's'              => '[jblund_dealer_registration]',
				) );

				if ( ! empty( $pages_with_shortcode ) ) {
					$registration_url = get_permalink( $pages_with_shortcode[0]->ID );
				} else {
					// Final fallback to home
					$registration_url = home_url( '/' );
				}
			}
			?>
			<p class="registration-link">
				<?php esc_html_e( "Don't have an account?", 'jblund-dealers' ); ?>
				<a href="<?php echo esc_url( $registration_url ); ?>">
					<?php esc_html_e( 'Apply to become a dealer', 'jblund-dealers' ); ?>
				</a>
			</p>
		</div>
	</div>
</div>
