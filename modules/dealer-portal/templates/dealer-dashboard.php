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
	$login_url = jblund_get_portal_page_url('login') ?: home_url( '/dealer-login/' );
	echo '<div class="dealer-access-denied warning">';
	echo '<h2>Access Denied</h2>';
	echo '<p>You must be logged in to access the dealer dashboard.</p>';
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
$display_name = $current_user->display_name;

// Get dealer representative info
$dealer_rep = jblund_get_dealer_representative( $current_user->ID );

// Get dealer post (for additional info if needed)
$dealer_post = jblund_get_user_dealer_post( $current_user->ID );

// Get NDA acceptance info
$nda_data = get_user_meta( $current_user->ID, '_dealer_nda_acceptance', true );
$nda_accepted = ! empty( $nda_data['accepted'] );
$nda_date = $nda_accepted && ! empty( $nda_data['acceptance_date'] ) ? $nda_data['acceptance_date'] : '';

// Get forms/documents from settings
$settings = get_option( 'jblund_dealers_settings' );
$required_documents = isset( $settings['required_documents'] ) ? $settings['required_documents'] : array();
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
						<a href="<?php echo esc_url( jblund_get_portal_page_url('profile') ?: home_url( '/dealer-profile/' ) ); ?>" class="dashboard-link">
							<span class="link-icon">👤</span>
							<span class="link-text"><?php esc_html_e( 'My Profile', 'jblund-dealers' ); ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( jblund_get_portal_page_url('nda') ?: home_url( '/dealer-nda-acceptance/' ) ); ?>" class="dashboard-link">
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

			<!-- Dealer Representative Card -->
			<?php if ( $dealer_rep ) : ?>
			<div class="dashboard-card dealer-representative">
				<h2><?php esc_html_e( 'Your Dealer Representative', 'jblund-dealers' ); ?></h2>
				<div class="rep-info">
					<div class="rep-item">
						<span class="rep-icon">👤</span>
						<div class="rep-details">
							<strong><?php echo esc_html( $dealer_rep['name'] ); ?></strong>
						</div>
					</div>
					<?php if ( ! empty( $dealer_rep['phone'] ) ) : ?>
					<div class="rep-item">
						<span class="rep-icon">📞</span>
						<div class="rep-details">
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $dealer_rep['phone'] ) ); ?>">
								<?php echo esc_html( $dealer_rep['phone'] ); ?>
							</a>
						</div>
					</div>
					<?php endif; ?>
					<?php if ( ! empty( $dealer_rep['email'] ) ) : ?>
					<div class="rep-item">
						<span class="rep-icon">✉️</span>
						<div class="rep-details">
							<a href="mailto:<?php echo esc_attr( $dealer_rep['email'] ); ?>">
								<?php echo esc_html( $dealer_rep['email'] ); ?>
							</a>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Signed Documents Card -->
			<div class="dashboard-card signed-documents">
				<h2><?php esc_html_e( 'Signed Documents', 'jblund-dealers' ); ?></h2>
				<p class="card-description"><?php esc_html_e( 'View and download your signed documents.', 'jblund-dealers' ); ?></p>
				<ul class="documents-list">
					<?php
					$has_documents = false;

					// Check for signed NDA with PDF
					if ( $nda_accepted ) :
						$nda_pdf_url = get_user_meta( $current_user->ID, '_dealer_nda_pdf_url', true );
						$has_documents = true;
					?>
					<li class="document-item">
						<span class="document-icon">📄</span>
						<div class="document-info">
							<strong><?php esc_html_e( 'Non-Disclosure Agreement', 'jblund-dealers' ); ?></strong>
							<div class="document-meta">
								<span class="document-status signed">✓ <?php esc_html_e( 'Signed', 'jblund-dealers' ); ?></span>
								<?php if ( $nda_date ) : ?>
								<span class="document-date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $nda_date ) ) ); ?></span>
								<?php endif; ?>
							</div>
							<div class="document-actions">
								<a href="<?php echo esc_url( jblund_get_portal_page_url( 'nda' ) ?: home_url( '/dealer-nda-acceptance/' ) ); ?>" class="document-link">
									<?php esc_html_e( 'View Document', 'jblund-dealers' ); ?> →
								</a>
								<?php if ( $nda_pdf_url ) : ?>
								<a href="<?php echo esc_url( $nda_pdf_url ); ?>" class="document-link download-link" download>
									<?php esc_html_e( 'Download PDF', 'jblund-dealers' ); ?> ⬇
								</a>
								<?php endif; ?>
							</div>
						</div>
					</li>
					<?php endif; ?>

					<?php if ( ! $has_documents ) : ?>
					<li class="no-documents">
						<p><?php esc_html_e( 'No signed documents yet.', 'jblund-dealers' ); ?></p>
					</li>
					<?php endif; ?>
				</ul>
			</div>

			<!-- Documents to Complete Card -->
			<?php if ( ! empty( $required_documents ) ) : ?>
			<div class="dashboard-card documents-to-complete">
				<h2><?php esc_html_e( 'Documents to Complete', 'jblund-dealers' ); ?></h2>
				<p class="card-description"><?php esc_html_e( 'Please review and complete the following documents.', 'jblund-dealers' ); ?></p>
				<ul class="documents-list">
					<?php
					foreach ( $required_documents as $document ) :
						if ( empty( $document['title'] ) ) {
							continue;
						}
						$doc_title = $document['title'];
						$doc_url = ! empty( $document['url'] ) ? $document['url'] : '#';
						$doc_description = ! empty( $document['description'] ) ? $document['description'] : '';
						$is_required = ! empty( $document['required'] );
						?>
						<li class="document-item">
							<span class="document-icon">📋</span>
							<div class="document-info">
								<strong>
									<?php echo esc_html( $doc_title ); ?>
									<?php if ( $is_required ) : ?>
										<span class="required-badge"><?php esc_html_e( 'Required', 'jblund-dealers' ); ?></span>
									<?php endif; ?>
								</strong>
								<?php if ( $doc_description ) : ?>
									<p class="document-description"><?php echo esc_html( $doc_description ); ?></p>
								<?php endif; ?>
								<a href="<?php echo esc_url( $doc_url ); ?>" class="document-button" target="_blank">
									<?php esc_html_e( 'Complete Form', 'jblund-dealers' ); ?> →
								</a>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

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
