# Dealer Profile Enhancement - Implementation Guide

## Overview

The dealer profile system has been enhanced to allow dealers to edit their linked `dealer` custom post type directly from the frontend, without needing WordPress admin access.

## Architecture

### New Class: `Dealer_Profile_Manager`

**Location:** `modules/dealer-portal/classes/class-dealer-profile-manager.php`

**Key Features:**

- Manages dealer profile updates (user account + dealer post)
- Enforces field-level permissions (dealer-editable vs admin-only)
- Handles secure document uploads (W9, insurance, licenses)
- Validates user can only edit their own linked dealer post

**Security Model:**

```php
DEALER_EDITABLE_FIELDS = [
    // User account
    'user_email', 'display_name',

    // Company basics
    '_dealer_company_name',
    '_dealer_company_address',
    '_dealer_company_phone',
    '_dealer_website',

    // Services
    '_dealer_docks', '_dealer_lifts', '_dealer_trailers',

    // Sub-locations
    '_dealer_sublocations',

    // Custom map
    '_dealer_custom_map_link',
];

ADMIN_ONLY_FIELDS = [
    '_dealer_linked_user_id',      // Can't change which user they're linked to
    '_dealer_latitude',            // Geocoding is admin-managed
    '_dealer_longitude',
    '_dealer_rep_name',            // Rep assignment is admin-only
    '_dealer_rep_email',
    '_dealer_rep_phone',
    '_dealer_territory',           // Territory assignment
    '_dealer_nda_pdf',            // NDA management
];
```

## Implementation Steps

### Step 1: Initialize the Manager Class

**File:** `includes/class-plugin.php`

```php
private function init_dealer_portal_modules() {
    // ... existing code ...

    if (class_exists('JBLund\DealerPortal\Dealer_Profile_Manager')) {
        new \JBLund\DealerPortal\Dealer_Profile_Manager();
    }
}
```

### Step 2: Enhanced Profile Template

**File:** `modules/dealer-portal/templates/dealer-profile.php`

The new template should include these sections:

#### A. Account Information (Current)

- Username (read-only)
- Email address (editable)
- Display name (editable)

#### B. Company Information (Enhanced)

- Company name (editable - syncs with post title)
- Company address (editable)
- Company phone (editable)
- Website URL (editable)
- Custom map link (editable)

#### C. Services Offered (New)

- Docks checkbox
- Lifts checkbox
- Trailers checkbox

#### D. Sub-Locations (New)

- Dynamic repeater for multiple locations
- Each with: name, address, phone, website, services
- JavaScript for add/remove locations

#### E. Admin-Only Info (Read-Only for Dealers)

- Territory
- Assigned representative
- Coordinates (if geocoded)

#### F. Business Documents (New)

- W-9 Tax Form (upload/view/delete)
- Insurance Certificate
- Business License
- Dealer Agreement
- Each shows: filename, upload date, download/delete buttons

### Step 3: Document Upload Security

**AJAX Handlers:**

- `wp_ajax_upload_dealer_document` - File upload
- `wp_ajax_delete_dealer_document` - File deletion

**Security Checks:**

1. Nonce verification
2. User is logged in
3. User has linked dealer post
4. User can edit that specific post
5. File type validation (PDF, JPG, PNG only)
6. File size limits

**Storage:**

- Files uploaded to WordPress media library
- Metadata stored in `_dealer_documents` post meta
- Structure:
  ```php
  [
      'w9' => [
          'url' => 'https://...',
          'file' => '/path/to/file',
          'type' => 'application/pdf',
          'uploaded_date' => '2025-11-20 10:30:00',
          'uploaded_by' => 123,
      ],
      'insurance' => [...],
  ]
  ```

### Step 4: Form Processing

**Profile Update Flow:**

```php
if ($_POST['update_dealer_profile']) {
    // Verify nonce
    // Initialize manager
    $manager = new Dealer_Profile_Manager();

    // Update profile (user + dealer post)
    $result = $manager->update_dealer_profile($_POST);

    if ($result['success']) {
        // Show success message
        // Refresh data
    } else {
        // Show error
    }
}
```

## Frontend Template Structure

```html
<div class="jblund-dealer-profile">
	<div class="profile-header">
		<h1>My Profile</h1>
		<p><a href="dashboard">← Back to Dashboard</a></p>
	</div>

	<!-- Success/Error Messages -->
	<?php if ($message): ?>
	<div class="profile-message <?php echo $message_type; ?>">
		<?php echo $message; ?>
	</div>
	<?php endif; ?>

	<form method="post" class="dealer-profile-form">
		<?php wp_nonce_field('update_dealer_profile', 'profile_nonce'); ?>

		<!-- SECTION 1: Account Information -->
		<div class="form-section">
			<h2>Account Information</h2>
			<!-- Username (read-only) -->
			<!-- Email (editable) -->
			<!-- Display Name (editable) -->
		</div>

		<!-- SECTION 2: Company Information -->
		<div class="form-section">
			<h2>Company Information</h2>
			<?php if ($dealer_post): ?>
			<!-- Company Name -->
			<!-- Address (textarea) -->
			<!-- Phone -->
			<!-- Website -->
			<!-- Custom Map Link -->
			<?php else: ?>
			<p class="no-dealer-post">
				No dealer listing linked to your account. Contact support for
				assistance.
			</p>
			<?php endif; ?>
		</div>

		<!-- SECTION 3: Services Offered -->
		<div class="form-section">
			<h2>Services Offered</h2>
			<!-- Checkboxes for Docks, Lifts, Trailers -->
		</div>

		<!-- SECTION 4: Additional Locations -->
		<div class="form-section">
			<h2>Additional Locations</h2>
			<div id="sublocations-container">
				<!-- Dynamic repeater -->
			</div>
			<button type="button" id="add-sublocation">Add Location</button>
		</div>

		<!-- SECTION 5: Admin Info (Read-Only) -->
		<?php if ($territory || $rep_info): ?>
		<div class="form-section admin-info">
			<h2>Account Details</h2>
			<!-- Territory (read-only) -->
			<!-- Representative (read-only) -->
		</div>
		<?php endif; ?>

		<!-- SECTION 6: Business Documents -->
		<div class="form-section documents-section">
			<h2>Business Documents</h2>
			<p class="section-description">
				Upload and manage your business documents. Accepted formats: PDF, JPG,
				PNG
			</p>

			<?php foreach (Dealer_Profile_Manager::ALLOWED_DOCUMENT_TYPES as $type =>
			$label): ?>
			<div class="document-upload-row">
				<strong><?php echo esc_html($label); ?></strong>

				<?php if (isset($documents[$type])): ?>
				<!-- Show existing document -->
				<div class="document-info">
					<a
						href="<?php echo esc_url($documents[$type]['url']); ?>"
						target="_blank">
						View Document
					</a>
					<span class="upload-date">
						Uploaded:
						<?php echo date_i18n(get_option('date_format'), strtotime($documents[$type]['uploaded_date'])); ?>
					</span>
					<button
						type="button"
						class="delete-document"
						data-type="<?php echo esc_attr($type); ?>">
						Delete
					</button>
				</div>
				<?php else: ?>
				<!-- Show upload form -->
				<div class="document-upload-form">
					<input
						type="file"
						name="document_<?php echo esc_attr($type); ?>"
						class="document-file-input"
						data-type="<?php echo esc_attr($type); ?>"
						accept=".pdf,.jpg,.jpeg,.png" />
					<button
						type="button"
						class="upload-document-btn"
						data-type="<?php echo esc_attr($type); ?>">
						Upload
					</button>
				</div>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>

		<div class="form-actions">
			<button type="submit" name="update_dealer_profile" class="submit-button">
				Update Profile
			</button>
		</div>
	</form>
</div>
```

## JavaScript for Document Uploads

```javascript
jQuery(document).ready(function ($) {
	// Handle document upload
	$(".upload-document-btn").on("click", function () {
		var $btn = $(this);
		var type = $btn.data("type");
		var fileInput = $('input[data-type="' + type + '"]')[0];

		if (!fileInput.files.length) {
			alert("Please select a file first");
			return;
		}

		var formData = new FormData();
		formData.append("action", "upload_dealer_document");
		formData.append("nonce", dealerProfileData.uploadNonce);
		formData.append("document_type", type);
		formData.append("document_file", fileInput.files[0]);

		$.ajax({
			url: dealerProfileData.ajaxUrl,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			beforeSend: function () {
				$btn.prop("disabled", true).text("Uploading...");
			},
			success: function (response) {
				if (response.success) {
					location.reload(); // Refresh to show uploaded document
				} else {
					alert("Upload failed: " + response.data);
				}
			},
			error: function () {
				alert("Upload failed. Please try again.");
			},
			complete: function () {
				$btn.prop("disabled", false).text("Upload");
			},
		});
	});

	// Handle document delete
	$(".delete-document").on("click", function () {
		if (!confirm("Are you sure you want to delete this document?")) {
			return;
		}

		var $btn = $(this);
		var type = $btn.data("type");

		$.ajax({
			url: dealerProfileData.ajaxUrl,
			type: "POST",
			data: {
				action: "delete_dealer_document",
				nonce: dealerProfileData.deleteNonce,
				document_type: type,
			},
			beforeSend: function () {
				$btn.prop("disabled", true);
			},
			success: function (response) {
				if (response.success) {
					location.reload(); // Refresh to update UI
				} else {
					alert("Delete failed: " + response.data);
				}
			},
			error: function () {
				alert("Delete failed. Please try again.");
			},
		});
	});
});
```

## Benefits

### For Dealers:

- ✅ Edit company info without WordPress admin access
- ✅ Manage multiple locations
- ✅ Upload business documents (W9, insurance, etc.)
- ✅ Update services offered
- ✅ Self-service profile management

### For Admins:

- ✅ Dealers can't edit sensitive fields (territory, rep assignment)
- ✅ Dealers can't unlink themselves from posts
- ✅ Full audit trail (who uploaded what, when)
- ✅ Maintain control over dealer accounts
- ✅ Less support requests for profile updates

### Security:

- ✅ Field-level permissions
- ✅ User can only edit their own linked dealer post
- ✅ File type validation
- ✅ Nonce verification on all actions
- ✅ Capability checks

## Future Enhancements

1. **Document Encryption:** Use sodium_crypto_secretbox for sensitive docs
2. **Document Expiration:** Track expiration dates (insurance, licenses)
3. **Admin Notifications:** Email admin when dealer uploads new documents
4. **Approval Workflow:** Require admin approval for certain changes
5. **Change Log:** Track all profile changes for audit
6. **Geocoding:** Auto-geocode address changes for map display

## Migration Note

Existing dealer profiles will work as-is. The new features are additive:

- Existing user meta fields still work
- Dealer posts link through `_dealer_linked_user_id` (unchanged)
- New document upload system is optional (won't break existing profiles)

## Testing Checklist

- [ ] Dealer can edit their company name (syncs with post title)
- [ ] Dealer can edit address, phone, website
- [ ] Dealer can toggle services (docks, lifts, trailers)
- [ ] Dealer can add/edit/delete sub-locations
- [ ] Dealer can upload W9, insurance, licenses
- [ ] Dealer can delete uploaded documents
- [ ] Dealer CANNOT edit territory
- [ ] Dealer CANNOT edit rep assignment
- [ ] Dealer CANNOT edit another dealer's profile
- [ ] Admin CAN edit all fields
- [ ] Changes reflect on public dealer directory
- [ ] File uploads only accept PDF, JPG, PNG
