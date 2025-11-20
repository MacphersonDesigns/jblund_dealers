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
		echo '<div class="dealer-already-logged-in" style="max-width: 600px; margin: 40px auto; padding: 30px; background: #d4edda; border: 2px solid #28a745; border-radius: 8px; text-align: center;">';
		echo '<h2 style="color: #155724; margin-top: 0;">Already Logged In</h2>';
		echo '<p style="color: #155724; margin-bottom: 20px;">You are already logged in as a dealer.</p>';
		echo '<a href="' . esc_url( home_url( '/dealer-dashboard/' ) ) . '" style="display: inline-block; background: #003366; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: 600;">Go to Dashboard</a>';
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
		</div>
	</div>
</div>
