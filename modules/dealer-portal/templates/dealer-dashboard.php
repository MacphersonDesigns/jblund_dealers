<?php
/**
 * Dealer Dashboard Template
 *
 * This template is loaded via the [jblund_dealer_dashboard] shortcode.
 * Displays the main dealer portal dashboard with welcome message,
 * quick links, and status information.
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
	echo '<div class="dealer-access-denied" style="max-width: 600px; margin: 40px auto; padding: 30px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; text-align: center;">';
	echo '<h2 style="color: #856404; margin-top: 0;">Access Denied</h2>';
	echo '<p style="color: #856404; margin-bottom: 20px;">You must be logged in to access the dealer dashboard.</p>';
	echo '<a href="' . esc_url( home_url( '/dealer-login/' ) ) . '" style="display: inline-block; background: #003366; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: 600;">Login</a>';
	echo '</div>';
	return;
}

$current_user = wp_get_current_user();
$is_dealer    = in_array( 'dealer', (array) $current_user->roles, true );
$can_bypass   = JBLund\DealerPortal\Dealer_Role::can_bypass_dealer_restrictions( $current_user );

// Security check - must be a dealer or elevated user (admin/staff)
if ( ! $is_dealer && ! $can_bypass ) {
	echo '<div class="dealer-access-denied" style="max-width: 600px; margin: 40px auto; padding: 30px; background: #f8d7da; border: 2px solid #dc3545; border-radius: 8px; text-align: center;">';
	echo '<h2 style="color: #721c24; margin-top: 0;">Access Denied</h2>';
	echo '<p style="color: #721c24; margin-bottom: 20px;">This page is only accessible to authorized dealers.</p>';
	echo '<a href="' . esc_url( home_url() ) . '" style="display: inline-block; background: #003366; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: 600;">Return Home</a>';
	echo '</div>';
	return;
}

// Get dealer information
$company_name = get_user_meta( $current_user->ID, '_dealer_company_name', true );
$display_name = $current_user->display_name;
?>

<div class="jblund-dealer-dashboard">
	<div class="dashboard-header">
		<h1><?php esc_html_e( 'Dealer Dashboard', 'jblund-dealers' ); ?></h1>
		<p class="welcome-message">
			<?php
			/* translators: %s: Dealer name or company name */
			printf( esc_html__( 'Welcome back, %s!', 'jblund-dealers' ), esc_html( $company_name ? $company_name : $display_name ) );
			?>
		</p>
	</div>

	<div class="dashboard-content">
		<div class="dashboard-grid">

			<!-- Quick Links Card -->
			<div class="dashboard-card quick-links">
				<h2><?php esc_html_e( 'Quick Links', 'jblund-dealers' ); ?></h2>
				<ul class="link-list">
					<li>
						<a href="<?php echo esc_url( home_url( '/dealer-profile/' ) ); ?>" class="dashboard-link">
							<span class="link-icon">👤</span>
							<span class="link-text"><?php esc_html_e( 'My Profile', 'jblund-dealers' ); ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( home_url( '/dealer-nda-acceptance/' ) ); ?>" class="dashboard-link">
							<span class="link-icon">📄</span>
							<span class="link-text"><?php esc_html_e( 'View NDA', 'jblund-dealers' ); ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="dashboard-link logout-link">
							<span class="link-icon">🚪</span>
							<span class="link-text"><?php esc_html_e( 'Logout', 'jblund-dealers' ); ?></span>
						</a>
					</li>
				</ul>
			</div>

			<!-- Account Status Card -->
			<div class="dashboard-card account-status">
				<h2><?php esc_html_e( 'Account Status', 'jblund-dealers' ); ?></h2>
				<div class="status-info">
					<div class="status-item">
						<span class="status-label"><?php esc_html_e( 'Account Type:', 'jblund-dealers' ); ?></span>
						<span class="status-value"><?php esc_html_e( 'Authorized Dealer', 'jblund-dealers' ); ?></span>
					</div>
					<div class="status-item">
						<span class="status-label"><?php esc_html_e( 'NDA Status:', 'jblund-dealers' ); ?></span>
						<span class="status-value status-accepted">✓ <?php esc_html_e( 'Accepted', 'jblund-dealers' ); ?></span>
					</div>
					<?php if ( $company_name ) : ?>
					<div class="status-item">
						<span class="status-label"><?php esc_html_e( 'Company:', 'jblund-dealers' ); ?></span>
						<span class="status-value"><?php echo esc_html( $company_name ); ?></span>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Resources Card (Placeholder for future features) -->
			<div class="dashboard-card resources">
				<h2><?php esc_html_e( 'Resources', 'jblund-dealers' ); ?></h2>
				<p class="card-description"><?php esc_html_e( 'Access dealer resources and materials here.', 'jblund-dealers' ); ?></p>
				<ul class="link-list">
					<li>
						<a href="#" class="dashboard-link">
							<span class="link-icon">📚</span>
							<span class="link-text"><?php esc_html_e( 'Product Catalog', 'jblund-dealers' ); ?></span>
						</a>
					</li>
					<li>
						<a href="#" class="dashboard-link">
							<span class="link-icon">💼</span>
							<span class="link-text"><?php esc_html_e( 'Marketing Materials', 'jblund-dealers' ); ?></span>
						</a>
					</li>
					<li>
						<a href="#" class="dashboard-link">
							<span class="link-icon">📞</span>
							<span class="link-text"><?php esc_html_e( 'Contact Support', 'jblund-dealers' ); ?></span>
						</a>
					</li>
				</ul>
			</div>

		<!-- Updates Card (Placeholder for future features) -->
		<div class="dashboard-card updates">
			<h2><?php esc_html_e( 'Recent Updates', 'jblund-dealers' ); ?></h2>
			<p class="card-description"><?php esc_html_e( 'Stay informed about the latest news and announcements.', 'jblund-dealers' ); ?></p>
			<div class="updates-list">
				<?php
				// Get updates from settings
				$settings = get_option( 'jblund_dealers_settings' );
				$updates = isset( $settings['portal_updates'] ) ? $settings['portal_updates'] : array();

				// Filter updates by date (only show active updates)
				$active_updates = array();
				if ( is_array( $updates ) ) {
					$today = date( 'Y-m-d' );
					foreach ( $updates as $update ) {
						// Skip if empty title and message
						if ( empty( $update['title'] ) && empty( $update['message'] ) ) {
							continue;
						}

						// Check start date
						if ( ! empty( $update['start_date'] ) && $update['start_date'] > $today ) {
							continue; // Not yet started
						}

						// Check end date
						if ( ! empty( $update['end_date'] ) && $update['end_date'] < $today ) {
							continue; // Already ended
						}

						$active_updates[] = $update;
					}
				}

				if ( ! empty( $active_updates ) ) :
					foreach ( $active_updates as $update ) :
						$title = isset( $update['title'] ) ? $update['title'] : '';
						$message = isset( $update['message'] ) ? $update['message'] : '';
						$start_date = isset( $update['start_date'] ) ? $update['start_date'] : '';
						?>
						<div class="update-item">
							<?php if ( $title ) : ?>
								<h4 class="update-title">
									<span class="update-icon">📢</span>
									<?php echo esc_html( $title ); ?>
								</h4>
							<?php endif; ?>
							<?php if ( $message ) : ?>
								<p class="update-message"><?php echo esc_html( $message ); ?></p>
							<?php endif; ?>
							<?php if ( $start_date ) : ?>
								<p class="update-date">
									<small><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $start_date ) ) ); ?></small>
								</p>
							<?php endif; ?>
						</div>
					<?php endforeach;
				else : ?>
					<p class="no-updates"><?php esc_html_e( 'No new updates at this time.', 'jblund-dealers' ); ?></p>
				<?php endif; ?>
			</div>
		</div>		</div>
	</div>
</div>

<style>
.jblund-dealer-dashboard {
	max-width: 1200px;
	margin: 0 auto;
	padding: 20px;
}

.dashboard-header {
	margin-bottom: 30px;
	padding-bottom: 20px;
	border-bottom: 2px solid #003366;
}

.dashboard-header h1 {
	margin: 0 0 10px 0;
	color: #003366;
	font-size: 32px;
}

.welcome-message {
	margin: 0;
	font-size: 18px;
	color: #666;
}

.dashboard-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	gap: 20px;
	margin-bottom: 30px;
}

.dashboard-card {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 8px;
	padding: 20px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dashboard-card h2 {
	margin: 0 0 15px 0;
	color: #003366;
	font-size: 20px;
	padding-bottom: 10px;
	border-bottom: 1px solid #eee;
}

.card-description {
	color: #666;
	font-size: 14px;
	margin-bottom: 15px;
}

.link-list {
	list-style: none;
	padding: 0;
	margin: 0;
}

.link-list li {
	margin-bottom: 10px;
}

.dashboard-link {
	display: flex;
	align-items: center;
	padding: 10px;
	background: #f5f5f5;
	border-radius: 4px;
	text-decoration: none;
	color: #333;
	transition: background 0.3s ease;
}

.dashboard-link:hover {
	background: #e0e0e0;
}

.dashboard-link.logout-link:hover {
	background: #ffe0e0;
	color: #c00;
}

.link-icon {
	font-size: 20px;
	margin-right: 10px;
}

.link-text {
	font-weight: 500;
}

/* Updates Styling */
.updates-list {
	max-height: 400px;
	overflow-y: auto;
}

.update-item {
	padding: 15px;
	margin-bottom: 15px;
	background: #f0f7ff;
	border-left: 4px solid #003366;
	border-radius: 4px;
}

.update-item:last-child {
	margin-bottom: 0;
}

.update-title {
	margin: 0 0 8px 0;
	color: #003366;
	font-size: 16px;
	display: flex;
	align-items: center;
	gap: 8px;
}

.update-icon {
	font-size: 18px;
}

.update-message {
	margin: 0 0 8px 0;
	color: #333;
	line-height: 1.5;
}

.update-date {
	margin: 0;
	color: #666;
	font-size: 13px;
}

.update-date small {
	font-style: italic;
}

.no-updates {
	color: #666;
	font-style: italic;
	text-align: center;
	padding: 20px;
}

.status-info {
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.status-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 8px 0;
	border-bottom: 1px solid #f0f0f0;
}

.status-item:last-child {
	border-bottom: none;
}

.status-label {
	font-weight: 600;
	color: #666;
}

.status-value {
	color: #333;
}

.status-value.status-accepted {
	color: #28a745;
	font-weight: 600;
}

.no-updates {
	color: #999;
	font-style: italic;
	text-align: center;
	padding: 20px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
	.dashboard-grid {
		grid-template-columns: 1fr;
	}

	.dashboard-header h1 {
		font-size: 24px;
	}

	.welcome-message {
		font-size: 16px;
	}
}
</style>
