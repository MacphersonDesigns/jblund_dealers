<?php
/**
 * Dealer Profile Template
 *
 * This template is loaded via the [jblund_dealer_profile] shortcode.
 * Displays and allows editing of dealer profile information.
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Security check - must be logged in
if ( ! is_user_logged_in() ) {
	$login_url = jblund_get_portal_page_url('login') ?: home_url( '/dealer-login/' );
	echo '<div class="dealer-access-denied warning">';
	echo '<h2>Access Denied</h2>';
	echo '<p>You must be logged in to access the dealer profile.</p>';
	echo '<a href="' . esc_url( $login_url ) . '">Login</a>';
	echo '</div>';
	return;
}

$current_user = wp_get_current_user();
$is_dealer    = in_array( 'dealer', (array) $current_user->roles, true );
$can_bypass   = JBLund\DealerPortal\Dealer_Role::can_bypass_dealer_restrictions( $current_user );

// Security check - must be a dealer or elevated user (admin/staff)
if ( ! $is_dealer && ! $can_bypass ) {
	echo '<div class="dealer-access-denied error">';
	echo '<h2>Access Denied</h2>';
	echo '<p>This page is only accessible to authorized dealers.</p>';
	echo '<a href="' . esc_url( home_url() ) . '">Return Home</a>';
	echo '</div>';
	return;
}

// Get dealer information
$company_name = get_user_meta( $current_user->ID, '_dealer_company_name', true );
$company_phone = get_user_meta( $current_user->ID, '_dealer_company_phone', true );
$territory = get_user_meta( $current_user->ID, '_dealer_territory', true );

// Handle profile update
$success_message = '';
$error_message = '';

if ( isset( $_POST['update_dealer_profile'] ) && check_admin_referer( 'update_dealer_profile', 'profile_nonce' ) ) {
	// Update user fields
	$user_data = array(
		'ID' => $current_user->ID,
		'user_email' => sanitize_email( $_POST['user_email'] ?? $current_user->user_email ),
		'display_name' => sanitize_text_field( $_POST['display_name'] ?? $current_user->display_name ),
	);

	$result = wp_update_user( $user_data );

	if ( ! is_wp_error( $result ) ) {
		// Update meta fields
		update_user_meta( $current_user->ID, '_dealer_company_name', sanitize_text_field( $_POST['company_name'] ?? '' ) );
		update_user_meta( $current_user->ID, '_dealer_company_phone', sanitize_text_field( $_POST['company_phone'] ?? '' ) );

		$success_message = __( 'Profile updated successfully!', 'jblund-dealers' );

		// Refresh data
		$company_name = get_user_meta( $current_user->ID, '_dealer_company_name', true );
		$company_phone = get_user_meta( $current_user->ID, '_dealer_company_phone', true );
	} else {
		$error_message = __( 'Error updating profile. Please try again.', 'jblund-dealers' );
	}
}
?>

<div class="jblund-dealer-profile">
	<div class="profile-header">
		<h1><?php esc_html_e( 'My Profile', 'jblund-dealers' ); ?></h1>
		<p class="back-link">
			<a href="<?php echo esc_url( jblund_get_portal_page_url('dashboard') ?: home_url( '/dealer-dashboard/' ) ); ?>">
				← <?php esc_html_e( 'Back to Dashboard', 'jblund-dealers' ); ?>
			</a>
		</p>
	</div>

	<?php if ( $success_message ) : ?>
		<div class="profile-message success">
			<?php echo esc_html( $success_message ); ?>
		</div>
	<?php endif; ?>

	<?php if ( $error_message ) : ?>
		<div class="profile-message error">
			<?php echo esc_html( $error_message ); ?>
		</div>
	<?php endif; ?>

	<div class="profile-content">
		<form method="post" action="" class="dealer-profile-form">
			<?php wp_nonce_field( 'update_dealer_profile', 'profile_nonce' ); ?>

			<div class="form-section">
				<h2><?php esc_html_e( 'Account Information', 'jblund-dealers' ); ?></h2>

				<div class="form-field">
					<label for="user_login"><?php esc_html_e( 'Username', 'jblund-dealers' ); ?></label>
					<input
						type="text"
						id="user_login"
						name="user_login"
						value="<?php echo esc_attr( $current_user->user_login ); ?>"
						disabled
						class="readonly-field"
					/>
					<p class="field-description"><?php esc_html_e( 'Username cannot be changed.', 'jblund-dealers' ); ?></p>
				</div>

				<div class="form-field">
					<label for="user_email"><?php esc_html_e( 'Email Address', 'jblund-dealers' ); ?> <span class="required">*</span></label>
					<input
						type="email"
						id="user_email"
						name="user_email"
						value="<?php echo esc_attr( $current_user->user_email ); ?>"
						required
					/>
				</div>

				<div class="form-field">
					<label for="display_name"><?php esc_html_e( 'Display Name', 'jblund-dealers' ); ?></label>
					<input
						type="text"
						id="display_name"
						name="display_name"
						value="<?php echo esc_attr( $current_user->display_name ); ?>"
					/>
				</div>
			</div>

			<div class="form-section">
				<h2><?php esc_html_e( 'Company Information', 'jblund-dealers' ); ?></h2>

				<div class="form-field">
					<label for="company_name"><?php esc_html_e( 'Company Name', 'jblund-dealers' ); ?></label>
					<input
						type="text"
						id="company_name"
						name="company_name"
						value="<?php echo esc_attr( $company_name ); ?>"
					/>
				</div>

				<div class="form-field">
					<label for="company_phone"><?php esc_html_e( 'Company Phone', 'jblund-dealers' ); ?></label>
					<input
						type="tel"
						id="company_phone"
						name="company_phone"
						value="<?php echo esc_attr( $company_phone ); ?>"
					/>
				</div>

				<?php if ( $territory ) : ?>
				<div class="form-field">
					<label for="territory"><?php esc_html_e( 'Territory', 'jblund-dealers' ); ?></label>
					<input
						type="text"
						id="territory"
						name="territory"
						value="<?php echo esc_attr( $territory ); ?>"
						disabled
						class="readonly-field"
					/>
					<p class="field-description"><?php esc_html_e( 'Contact support to change your territory.', 'jblund-dealers' ); ?></p>
				</div>
				<?php endif; ?>
			</div>

			<div class="form-actions">
				<button type="submit" name="update_dealer_profile" class="submit-button">
					<?php esc_html_e( 'Update Profile', 'jblund-dealers' ); ?>
				</button>
			</div>
		</form>
	</div>
</div>

