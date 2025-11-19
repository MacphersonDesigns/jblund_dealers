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

<style>
.jblund-dealer-login {
	max-width: 450px;
	margin: 40px auto;
	padding: 20px;
}

.login-container {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 8px;
	padding: 40px;
	box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.login-header {
	text-align: center;
	margin-bottom: 30px;
	padding-bottom: 20px;
	border-bottom: 2px solid #003366;
}

.login-header h1 {
	margin: 0 0 10px 0;
	color: #003366;
	font-size: 28px;
}

.login-header p {
	margin: 0;
	color: #666;
	font-size: 14px;
}

.login-form-wrapper form {
	margin: 0;
}

.login-form-wrapper label {
	display: block;
	margin-bottom: 8px;
	color: #333;
	font-weight: 600;
	font-size: 14px;
}

.login-form-wrapper input[type="text"],
.login-form-wrapper input[type="password"] {
	width: 100%;
	padding: 12px;
	border: 1px solid #ddd;
	border-radius: 4px;
	font-size: 14px;
	margin-bottom: 15px;
	box-sizing: border-box;
}

.login-form-wrapper input[type="text"]:focus,
.login-form-wrapper input[type="password"]:focus {
	outline: none;
	border-color: #003366;
}

.login-form-wrapper .login-remember {
	margin-bottom: 20px;
}

.login-form-wrapper .login-remember label {
	display: inline;
	font-weight: normal;
	margin-left: 5px;
}

.login-form-wrapper input[type="submit"] {
	width: 100%;
	background: #003366;
	color: #fff;
	padding: 12px;
	border: none;
	border-radius: 4px;
	font-size: 16px;
	font-weight: 600;
	cursor: pointer;
	transition: background 0.3s ease;
}

.login-form-wrapper input[type="submit"]:hover {
	background: #002244;
}

.login-links {
	text-align: center;
	margin-top: 20px;
	padding-top: 20px;
	border-top: 1px solid #eee;
}

.login-links a {
	color: #003366;
	text-decoration: none;
	font-size: 14px;
}

.login-links a:hover {
	text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 480px) {
	.login-container {
		padding: 30px 20px;
	}

	.login-header h1 {
		font-size: 24px;
	}
}
</style>
