<?php
/**
 * Dealer Profile Template
 *
 * This template is loaded via the [jblund_dealer_profile] shortcode.
 * Displays and allows editing of dealer profile information including
 * company details, services, sub-locations, and document uploads.
 *
 * @package JBLund_Dealers
 * @subpackage Dealer_Portal
 * @since 2.0.0
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

// Get dealer post ID
$dealer_post_id = get_user_meta( $current_user->ID, '_dealer_post_id', true );

if ( ! $dealer_post_id ) {
	echo '<div class="dealer-access-denied error">';
	echo '<h2>No Dealer Profile Found</h2>';
	echo '<p>Your dealer profile has not been set up yet. Please contact support.</p>';
	echo '</div>';
	return;
}

// Get profile manager instance
$profile_manager = JBLund\DealerPortal\Dealer_Profile_Manager::get_instance();

// Handle profile update
$success_message = '';
$error_message = '';

if ( isset( $_POST['update_dealer_profile'] ) && check_admin_referer( 'update_dealer_profile', 'profile_nonce' ) ) {
	$result = $profile_manager->update_dealer_profile( $dealer_post_id, $_POST );
	
	if ( $result['success'] ) {
		$success_message = $result['message'];
	} else {
		$error_message = $result['message'];
	}
}

// Get current dealer data
$dealer_post = get_post( $dealer_post_id );
$company_name = $dealer_post->post_title;
$company_address = get_post_meta( $dealer_post_id, '_dealer_company_address', true );
$company_phone = get_post_meta( $dealer_post_id, '_dealer_company_phone', true );
$website = get_post_meta( $dealer_post_id, '_dealer_website', true );
$docks = get_post_meta( $dealer_post_id, '_dealer_docks', true );
$lifts = get_post_meta( $dealer_post_id, '_dealer_lifts', true );
$trailers = get_post_meta( $dealer_post_id, '_dealer_trailers', true );
$sublocations = get_post_meta( $dealer_post_id, '_dealer_sublocations', true ) ?: array();

// Get dealer documents
$document_ids = get_post_meta( $dealer_post_id, '_dealer_documents', true ) ?: array();
$documents = array();
if ( is_array( $document_ids ) ) {
	foreach ( $document_ids as $doc_id ) {
		$file = get_attached_file( $doc_id );
		if ( $file && file_exists( $file ) ) {
			$documents[] = array(
				'id' => $doc_id,
				'title' => get_the_title( $doc_id ),
				'url' => wp_get_attachment_url( $doc_id ),
				'filename' => basename( $file ),
				'size' => size_format( filesize( $file ) ),
				'date' => get_the_date( 'Y-m-d', $doc_id ),
			);
		}
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
		<form method="post" action="" class="dealer-profile-form" enctype="multipart/form-data">
			<?php wp_nonce_field( 'update_dealer_profile', 'profile_nonce' ); ?>

			<!-- Company Information -->
			<div class="form-section">
				<h2><?php esc_html_e( 'Company Information', 'jblund-dealers' ); ?></h2>

				<div class="form-field">
					<label for="company_name"><?php esc_html_e( 'Company Name', 'jblund-dealers' ); ?> <span class="required">*</span></label>
					<input
						type="text"
						id="company_name"
						name="company_name"
						value="<?php echo esc_attr( $company_name ); ?>"
						required
					/>
				</div>

				<div class="form-field">
					<label for="company_address"><?php esc_html_e( 'Company Address', 'jblund-dealers' ); ?></label>
					<textarea
						id="company_address"
						name="company_address"
						rows="3"
					><?php echo esc_textarea( $company_address ); ?></textarea>
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

				<div class="form-field">
					<label for="website"><?php esc_html_e( 'Website', 'jblund-dealers' ); ?></label>
					<input
						type="url"
						id="website"
						name="website"
						value="<?php echo esc_attr( $website ); ?>"
						placeholder="https://"
					/>
				</div>
			</div>

			<!-- Services Offered -->
			<div class="form-section">
				<h2><?php esc_html_e( 'Services Offered', 'jblund-dealers' ); ?></h2>
				
				<div class="form-field-group">
					<label class="checkbox-label">
						<input
							type="checkbox"
							name="docks"
							value="1"
							<?php checked( $docks, '1' ); ?>
						/>
						<?php esc_html_e( 'Docks', 'jblund-dealers' ); ?>
					</label>

					<label class="checkbox-label">
						<input
							type="checkbox"
							name="lifts"
							value="1"
							<?php checked( $lifts, '1' ); ?>
						/>
						<?php esc_html_e( 'Lifts', 'jblund-dealers' ); ?>
					</label>

					<label class="checkbox-label">
						<input
							type="checkbox"
							name="trailers"
							value="1"
							<?php checked( $trailers, '1' ); ?>
						/>
						<?php esc_html_e( 'Trailers', 'jblund-dealers' ); ?>
					</label>
				</div>
			</div>

			<!-- Sub-Locations -->
			<div class="form-section">
				<h2><?php esc_html_e( 'Additional Locations', 'jblund-dealers' ); ?></h2>
				
				<div id="sublocations-container">
					<?php if ( ! empty( $sublocations ) ) : ?>
						<?php foreach ( $sublocations as $index => $sublocation ) : ?>
							<div class="sublocation-row" data-index="<?php echo esc_attr( $index ); ?>">
								<h3><?php printf( esc_html__( 'Location %d', 'jblund-dealers' ), $index + 1 ); ?></h3>
								
								<div class="form-field">
									<label><?php esc_html_e( 'Location Name', 'jblund-dealers' ); ?></label>
									<input type="text" name="sublocations[<?php echo $index; ?>][name]" value="<?php echo esc_attr( $sublocation['name'] ?? '' ); ?>" />
								</div>

								<div class="form-field">
									<label><?php esc_html_e( 'Address', 'jblund-dealers' ); ?></label>
									<textarea name="sublocations[<?php echo $index; ?>][address]" rows="2"><?php echo esc_textarea( $sublocation['address'] ?? '' ); ?></textarea>
								</div>

								<div class="form-field">
									<label><?php esc_html_e( 'Phone', 'jblund-dealers' ); ?></label>
									<input type="tel" name="sublocations[<?php echo $index; ?>][phone]" value="<?php echo esc_attr( $sublocation['phone'] ?? '' ); ?>" />
								</div>

								<div class="form-field">
									<label><?php esc_html_e( 'Website', 'jblund-dealers' ); ?></label>
									<input type="url" name="sublocations[<?php echo $index; ?>][website]" value="<?php echo esc_attr( $sublocation['website'] ?? '' ); ?>" />
								</div>

								<div class="form-field-group">
									<label class="checkbox-label">
										<input type="checkbox" name="sublocations[<?php echo $index; ?>][docks]" value="1" <?php checked( $sublocation['docks'] ?? '', '1' ); ?> />
										<?php esc_html_e( 'Docks', 'jblund-dealers' ); ?>
									</label>
									<label class="checkbox-label">
										<input type="checkbox" name="sublocations[<?php echo $index; ?>][lifts]" value="1" <?php checked( $sublocation['lifts'] ?? '', '1' ); ?> />
										<?php esc_html_e( 'Lifts', 'jblund-dealers' ); ?>
									</label>
									<label class="checkbox-label">
										<input type="checkbox" name="sublocations[<?php echo $index; ?>][trailers]" value="1" <?php checked( $sublocation['trailers'] ?? '', '1' ); ?> />
										<?php esc_html_e( 'Trailers', 'jblund-dealers' ); ?>
									</label>
								</div>

								<button type="button" class="remove-sublocation-btn"><?php esc_html_e( 'Remove Location', 'jblund-dealers' ); ?></button>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>

				<button type="button" id="add-sublocation-btn" class="secondary-button">
					+ <?php esc_html_e( 'Add Location', 'jblund-dealers' ); ?>
				</button>
			</div>

			<!-- Document Upload -->
			<div class="form-section">
				<h2><?php esc_html_e( 'Business Documents', 'jblund-dealers' ); ?></h2>
				<p class="field-description">
					<?php esc_html_e( 'Upload important business documents (W9, insurance certificates, licenses, etc.)', 'jblund-dealers' ); ?>
				</p>

				<div id="document-upload-area">
					<div class="upload-dropzone">
						<input type="file" id="document-upload-input" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none;" />
						<label for="document-upload-input" class="upload-label">
							<span class="upload-icon">📎</span>
							<span class="upload-text"><?php esc_html_e( 'Click to upload or drag files here', 'jblund-dealers' ); ?></span>
							<span class="upload-subtext"><?php esc_html_e( 'PDF, DOC, DOCX, JPG, PNG (Max 10MB per file)', 'jblund-dealers' ); ?></span>
						</label>
					</div>

					<?php if ( ! empty( $documents ) ) : ?>
						<div class="document-list">
							<h3><?php esc_html_e( 'Uploaded Documents', 'jblund-dealers' ); ?></h3>
							<?php foreach ( $documents as $doc ) : ?>
								<div class="document-item" data-doc-id="<?php echo esc_attr( $doc['id'] ); ?>">
									<div class="document-info">
										<span class="document-icon">📄</span>
										<div class="document-details">
											<strong><?php echo esc_html( $doc['title'] ); ?></strong>
											<span class="document-meta"><?php echo esc_html( $doc['size'] ); ?> • <?php echo esc_html( $doc['date'] ); ?></span>
										</div>
									</div>
									<div class="document-actions">
										<a href="<?php echo esc_url( $doc['url'] ); ?>" target="_blank" class="document-view-btn"><?php esc_html_e( 'View', 'jblund-dealers' ); ?></a>
										<button type="button" class="document-delete-btn" data-doc-id="<?php echo esc_attr( $doc['id'] ); ?>"><?php esc_html_e( 'Delete', 'jblund-dealers' ); ?></button>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" name="update_dealer_profile" class="submit-button">
					<?php esc_html_e( 'Update Profile', 'jblund-dealers' ); ?>
				</button>
			</div>
		</form>
	</div>
</div>

<script type="text/template" id="sublocation-template">
	<div class="sublocation-row" data-index="{{INDEX}}">
		<h3><?php esc_html_e( 'Location', 'jblund-dealers' ); ?> {{NUMBER}}</h3>
		
		<div class="form-field">
			<label><?php esc_html_e( 'Location Name', 'jblund-dealers' ); ?></label>
			<input type="text" name="sublocations[{{INDEX}}][name]" value="" />
		</div>

		<div class="form-field">
			<label><?php esc_html_e( 'Address', 'jblund-dealers' ); ?></label>
			<textarea name="sublocations[{{INDEX}}][address]" rows="2"></textarea>
		</div>

		<div class="form-field">
			<label><?php esc_html_e( 'Phone', 'jblund-dealers' ); ?></label>
			<input type="tel" name="sublocations[{{INDEX}}][phone]" value="" />
		</div>

		<div class="form-field">
			<label><?php esc_html_e( 'Website', 'jblund-dealers' ); ?></label>
			<input type="url" name="sublocations[{{INDEX}}][website]" value="" />
		</div>

		<div class="form-field-group">
			<label class="checkbox-label">
				<input type="checkbox" name="sublocations[{{INDEX}}][docks]" value="1" />
				<?php esc_html_e( 'Docks', 'jblund-dealers' ); ?>
			</label>
			<label class="checkbox-label">
				<input type="checkbox" name="sublocations[{{INDEX}}][lifts]" value="1" />
				<?php esc_html_e( 'Lifts', 'jblund-dealers' ); ?>
			</label>
			<label class="checkbox-label">
				<input type="checkbox" name="sublocations[{{INDEX}}][trailers]" value="1" />
				<?php esc_html_e( 'Trailers', 'jblund-dealers' ); ?>
			</label>
		</div>

		<button type="button" class="remove-sublocation-btn"><?php esc_html_e( 'Remove Location', 'jblund-dealers' ); ?></button>
	</div>
</script>
