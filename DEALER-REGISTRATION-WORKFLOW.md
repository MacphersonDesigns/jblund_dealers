# Dealer Registration Workflow - Complete Implementation Guide

## 🎯 Overview

The JBLund Dealers plugin now features a **fully automated dealer registration and onboarding system**. This document covers the complete workflow from initial application through full dealer portal access.

---

## 📋 Complete User Journey

### Step 1: Potential Dealer Applies

**User Action:** Visits registration page and fills out application form

**What Happens:**

- Potential dealer finds the registration page (via "Become a Dealer" link/button)
- Fills out registration form with:
  - First Name & Last Name
  - Email Address
  - Phone Number
  - Company Name
  - Company Phone (optional)
  - Territory/Location
  - Business description (optional)
- Submits application

**Technical Details:**

- Form shortcode: `[jblund_dealer_registration]`
- Creates `dealer_registration` custom post type entry (status: pending)
- Sends admin notification email
- Shows success message: "Thank you for your interest in becoming a JBLund dealer. Your application has been received and is currently under review. One of our account representatives will contact you shortly..."

**Meta Data Stored:**

```
_registration_rep_first_name
_registration_rep_last_name
_registration_rep_name (Full name)
_registration_email
_registration_phone
_registration_company
_registration_company_phone
_registration_territory
_registration_notes
_registration_status (pending)
_registration_date
_registration_ip
_registration_user_agent
```

---

### Step 2: Admin Reviews Application

**User:** Account Representative (Admin)

**What Happens:**

- Admin logs into WordPress
- Navigates to **Dealers > Registrations**
- Sees list of all pending applications with:
  - Date Submitted
  - Representative Name
  - Email (clickable mailto link)
  - Company Name
  - Territory
  - Status Badge (Yellow: Pending, Green: Approved, Red: Rejected)
  - Actions (Approve / Decline buttons)

**Admin Actions:**

#### If Approving:

1. Clicks "Approve" button
2. System automatically:
   - Generates username from first initial + last name
     - Example: Alex Macpherson → `amacpherson`
     - Handles duplicates: `amacpherson1`, `amacpherson2`, etc.
   - Generates secure 16-character password
   - Creates WordPress user account
   - Assigns "Dealer" role
   - Updates user meta with company name
   - Sets `_dealer_force_password_change` flag
   - Sends approval email with login credentials
   - Updates registration status to "approved"
3. Admin sees success message

#### If Declining:

1. Clicks "Decline" button
2. JavaScript prompt asks for rejection reason
3. System automatically:
   - Updates registration status to "rejected"
   - Stores rejection reason
   - Sends professional rejection email to applicant
4. Admin sees success message

---

### Step 3: Dealer Receives Approval Email

**User:** Newly Approved Dealer

**Email Contains:**

- Congratulations message
- Login credentials:
  - Username (e.g., `amacpherson`)
  - Temporary password
- Security reminder to change password
- 4-step onboarding checklist
- Login button/link to dealer login page
- Support contact information

**Approval Email Template:** `modules/dealer-portal/templates/emails/registration-approval.php`

---

### Step 4: First Login - Force Password Change

**User:** Dealer logs in for the first time

**What Happens:**

1. Dealer goes to login page (shortcode: `[jblund_dealer_login]`)
2. Enters username and temporary password
3. Successfully authenticates
4. **Immediately redirected to Password Change page**
5. Cannot access any dealer portal content until password is changed

**Password Change Page:**

- Shortcode: `[jblund_force_password_change]`
- Form fields:
  - Current Password (temporary password)
  - New Password (minimum 8 characters, letters + numbers)
  - Confirm New Password
- Client-side validation for password match
- Server-side validation for password strength

**After Password Change:**

- System updates password
- Removes `_dealer_force_password_change` flag
- Sets `_dealer_password_changed` flag
- Re-authenticates user with new password
- **Redirects to NDA Acceptance page**

---

### Step 5: NDA Acceptance - Required Before Portal Access

**User:** Dealer with changed password

**What Happens:**

1. Dealer is redirected to NDA Acceptance page
2. **Cannot access ANY dealer portal content until NDA is signed**
3. Sees complete NDA document (customizable via Dealers > NDA Editor)
4. Must:
   - Review NDA content
   - Provide digital signature (or acknowledgment)
   - Check acceptance checkbox
   - Submit acceptance form

**Technical Details:**

- Shortcode: `[jblund_nda_acceptance]`
- NDA content editable in admin: **Dealers > Settings > NDA Editor**
- Upon acceptance:
  - Stores acceptance data in user meta
  - Generates PDF document (TCPDF)
  - Saves PDF to: `wp-content/uploads/dealers/signed-documents/`
  - Stores PDF URL in user meta and dealer post meta
  - Sends confirmation email to dealer
  - Sends notification email to admin
  - Fires action hook: `jblund_dealer_nda_accepted`

**PDF Storage:**

- Directory: `wp-content/uploads/dealers/signed-documents/`
- Filename: `nda-{company-slug}-{timestamp}.pdf`
- Protected by `.htaccess` (download-only, no directory listing)

---

### Step 6: Full Dealer Portal Access

**User:** Fully onboarded dealer

**What Happens:**

1. After NDA acceptance, dealer is redirected to Dashboard
2. Now has full access to all dealer portal features:
   - **Dealer Dashboard** (`[jblund_dealer_dashboard]`)
   - **Dealer Profile** (`[jblund_dealer_profile]`)
   - Required documents section
   - Company information
   - Account settings

**Access Control:**

- All public pages remain accessible (homepage, dealer directory, contact, etc.)
- Only dealer-specific portal pages require authentication + NDA acceptance
- Admin and staff users bypass all restrictions

---

## 🔐 Security & Access Control

### Registration Security

- Nonce verification on form submission
- Email validation and duplicate checking
- IP address and user agent logging
- Sanitized input fields

### Login Security

- WordPress standard authentication
- Force password change on first login
- Minimum password requirements enforced
- Session management via WordPress

### NDA Security

- Must be logged in to view NDA page
- Cannot access portal without NDA acceptance
- Signature and acceptance data stored with timestamp and IP
- PDF documents stored in protected directory

### Access Restrictions

**Before NDA Acceptance:**

- ❌ Dealer Dashboard
- ❌ Dealer Profile
- ❌ Documents
- ❌ Any portal-specific pages
- ✅ Public pages (homepage, dealer directory)
- ✅ Password change page
- ✅ NDA acceptance page
- ✅ Logout

**After NDA Acceptance:**

- ✅ Full dealer portal access

---

## 📄 Required Pages & Shortcodes

### Auto-Created Pages (via Page Manager)

The following pages are automatically created on plugin activation:

1. **Dealer Registration** (manually created by admin)

   - Slug: `dealer-registration` (or custom)
   - Shortcode: `[jblund_dealer_registration]`
   - Public page

2. **Dealer Login**

   - Slug: `dealer-login`
   - Shortcode: `[jblund_dealer_login]`
   - Public page

3. **Password Change**

   - Slug: `dealer-password-change`
   - Shortcode: `[jblund_force_password_change]`
   - Restricted to logged-in dealers

4. **NDA Acceptance**

   - Slug: `dealer-nda-acceptance`
   - Shortcode: `[jblund_nda_acceptance]`
   - Restricted to logged-in dealers (before acceptance)

5. **Dealer Dashboard**

   - Slug: `dealer-dashboard`
   - Shortcode: `[jblund_dealer_dashboard]`
   - Restricted to dealers with NDA acceptance

6. **Dealer Profile**
   - Slug: `dealer-profile`
   - Shortcode: `[jblund_dealer_profile]`
   - Restricted to dealers with NDA acceptance

---

## 🛠️ Admin Interface

### Dealers > Registrations

**Purpose:** View and manage all dealer registration submissions

**Features:**

- Sortable table with 7 columns
- Filter by status (All / Pending / Approved / Rejected)
- Approve/Decline actions with one click
- Email notifications on approval/rejection
- Shows submission date, rep info, company, territory

**Columns:**

1. **Date Submitted** - Sortable
2. **Rep Name** - Sortable
3. **Email** - Clickable mailto link
4. **Company** - Sortable
5. **Territory** - Geographic location
6. **Status** - Color-coded badges
7. **Actions** - Approve / Decline buttons

### Dealers > Settings > NDA Editor

**Purpose:** Customize NDA content

**Features:**

- 6 TinyMCE editors for each NDA section
- Live preview modal
- Revert to defaults option
- HTML content allowed (sanitized)

---

## 📧 Email Notifications

### Admin Notification (New Registration)

**Trigger:** When dealer submits registration form

**Recipients:** WordPress admin email

**Content:**

- Representative name and email
- Company name
- Territory
- Link to Registrations admin page

### Approval Email (To Dealer)

**Trigger:** When admin approves registration

**Recipients:** Dealer email address

**Content:**

- Congratulations message
- Login credentials (username + temporary password)
- Security reminder
- 4-step onboarding checklist
- Login button/link
- Support information

**Template:** `modules/dealer-portal/templates/emails/registration-approval.php`

### Rejection Email (To Applicant)

**Trigger:** When admin declines registration

**Recipients:** Applicant email address

**Content:**

- Professional rejection message
- Admin's rejection reason
- Invitation to contact support or reapply
- Next steps guidance

**Template:** `modules/dealer-portal/templates/emails/registration-rejection.php`

### NDA Acceptance Emails

**Trigger:** When dealer accepts NDA

**Recipients:**

- Dealer (confirmation email)
- Admin (notification email)

**Content:**

- Confirmation of NDA acceptance
- PDF attachment (or download link)
- Date and time of acceptance
- Next steps

---

## 🗂️ Database Structure

### Custom Post Types

#### dealer_registration

**Purpose:** Store dealer application submissions

**Registration:**

- Public: No
- Show in Menu: No (accessed via custom list table)
- Capabilities: `manage_options` required
- Supports: Title only

**Status Values:**

- `pending` - Awaiting admin review
- `approved` - Application approved, user created
- `rejected` - Application declined

### User Meta Keys

#### Registration-Related

- `_dealer_company_name` - Company name from registration

#### Password Management

- `_dealer_force_password_change` - Flag requiring password change
- `_dealer_password_changed` - Flag indicating password has been changed

#### NDA-Related

- `_dealer_nda_acceptance` - JSON with acceptance data
- `_dealer_nda_accepted` - Quick boolean flag
- `_dealer_nda_pdf_path` - Server path to signed PDF
- `_dealer_nda_pdf_url` - Public URL to signed PDF

---

## 🔌 Hooks & Filters

### Actions

#### jblund_dealer_nda_accepted

**Trigger:** After dealer accepts NDA

**Parameters:**

- `$user_id` (int) - Dealer user ID
- `$signature_data` (string) - Signature data (if provided)

**Usage:**

```php
add_action('jblund_dealer_nda_accepted', function($user_id, $signature_data) {
    // Your custom logic after NDA acceptance
}, 10, 2);
```

### Filters

#### jblund_registration_form_attributes

**Filter:** Modify registration form attributes

**Parameters:**

- `$atts` (array) - Form attributes

**Usage:**

```php
add_filter('jblund_registration_form_attributes', function($atts) {
    $atts['title'] = 'Join Our Dealer Network';
    return $atts;
});
```

---

## 🧪 Testing Checklist

### Registration Flow

- [ ] Create registration page with `[jblund_dealer_registration]` shortcode
- [ ] Submit test application
- [ ] Verify registration appears in Dealers > Registrations
- [ ] Check admin notification email received
- [ ] Verify duplicate email detection works

### Approval Flow

- [ ] Approve test registration
- [ ] Verify username generated correctly (first initial + last name)
- [ ] Verify user account created with Dealer role
- [ ] Check approval email received with credentials
- [ ] Verify registration status changed to "approved"

### First Login

- [ ] Log in with temporary credentials
- [ ] Verify redirect to password change page
- [ ] Verify cannot access dashboard without password change
- [ ] Change password successfully
- [ ] Verify redirect to NDA page

### NDA Acceptance

- [ ] Verify cannot access dashboard without NDA acceptance
- [ ] Accept NDA
- [ ] Verify PDF generated in `uploads/dealers/signed-documents/`
- [ ] Check NDA confirmation email received
- [ ] Verify redirect to dashboard

### Full Access

- [ ] Access dealer dashboard
- [ ] Access dealer profile
- [ ] Verify all portal pages accessible
- [ ] Verify public pages still accessible without login

### Rejection Flow

- [ ] Submit another test application
- [ ] Decline with rejection reason
- [ ] Verify rejection email received
- [ ] Verify registration status changed to "rejected"

---

## 🚀 Deployment Steps

### 1. Plugin Activation

```bash
# The plugin will automatically:
# - Register dealer and dealer_registration post types
# - Create dealer role
# - Create portal pages (dashboard, profile, login, password change, NDA)
# - Flush rewrite rules
```

### 2. Create Registration Page

1. Go to **Pages > Add New**
2. Title: "Become a Dealer" (or your preference)
3. Add shortcode: `[jblund_dealer_registration]`
4. Publish page
5. Add link to this page in your navigation menu

### 3. Customize NDA Content

1. Go to **Dealers > Settings > NDA Editor**
2. Edit each section with your company's NDA text
3. Use "Preview" to review changes
4. Save settings

### 4. Test Complete Workflow

- Follow testing checklist above
- Use a real email address you can access
- Test all paths (approve, decline, password change, NDA)

### 5. Set Up Email Sending (Optional)

- Install SMTP plugin (WP Mail SMTP recommended)
- Configure email settings for reliable delivery
- Test email notifications

---

## 📦 Files Added/Modified

### New Files Created

#### Core Modules

- `modules/core/class-registration-post-type.php` (79 lines)
  - Registers `dealer_registration` custom post type

#### Dealer Portal Classes

- `modules/dealer-portal/classes/class-registration-form.php` (480 lines)

  - Registration form shortcode
  - Form submission handler
  - Success/error messaging
  - Admin notification email

- `modules/dealer-portal/classes/class-password-change-handler.php` (403 lines)
  - Force password change on first login
  - Password change form shortcode
  - Form validation and processing
  - Auto-redirect after password change

### Modified Files

#### Registration Admin

- `modules/dealer-portal/classes/class-registration-admin.php`
  - Updated approval workflow to generate username from first/last name
  - Handles duplicate usernames with counter suffix
  - Stores first/last name separately

#### NDA Handler

- `modules/dealer-portal/classes/class-nda-handler.php`
  - Blocks ALL dealer portal pages until NDA is signed
  - Allows password change page access
  - Uses portal pages from settings for page ID checks

#### Page Manager

- `modules/dealer-portal/classes/class-page-manager.php`
  - Added password change page definition
  - Auto-creates on plugin activation

#### Main Plugin File

- `jblund-dealers.php`
  - Loads new classes (Registration_Post_Type, Registration_Form, Password_Change_Handler)
  - Registers `dealer_registration` post type on activation

---

## 🎨 Customization Options

### Form Styling

All forms use inline CSS that can be overridden in your theme:

```css
/* Registration Form */
.jblund-dealer-registration {
}
.registration-container {
}
.dealer-registration-form {
}

/* Password Change Form */
.jblund-password-change {
}
.password-change-container {
}
.password-change-form {
}
```

### Email Templates

Email templates can be customized in:

- `modules/dealer-portal/templates/emails/registration-approval.php`
- `modules/dealer-portal/templates/emails/registration-rejection.php`

### Success Messages

Filter success messages:

```php
add_filter('jblund_registration_success_message', function($message) {
    return 'Your custom success message here!';
});
```

---

## 🆘 Troubleshooting

### Registration Form Not Submitting

- Check nonce verification
- Verify all required fields filled
- Check for JavaScript errors in console
- Ensure page has shortcode: `[jblund_dealer_registration]`

### Email Not Sending

- Install WP Mail SMTP plugin
- Configure SMTP settings
- Test email functionality
- Check spam folder

### Password Change Not Working

- Verify password meets requirements (8+ characters)
- Check that passwords match
- Ensure user is logged in
- Verify shortcode: `[jblund_force_password_change]`

### Cannot Access Dashboard After NDA

- Clear browser cache
- Check that NDA was actually submitted (check user meta)
- Verify PDF was generated in uploads directory
- Check for JavaScript errors

### Username Already Exists

- System automatically appends numbers (amacpherson1, amacpherson2, etc.)
- Check Users table for existing accounts
- If needed, manually delete test users

---

## 📞 Support & Documentation

### Documentation Files

- `README.md` - Plugin overview and quick start
- `USAGE_GUIDE.md` - Detailed usage instructions
- `DEALER-REGISTRATION-WORKFLOW.md` - This file
- `QUICK_REFERENCE.md` - Quick reference guide
- `CHANGELOG.md` - Version history

### Getting Help

For issues or questions:

1. Check documentation files
2. Review CHANGELOG.md for recent changes
3. Check Dealers > Settings > Help & Guide tab
4. Contact plugin developer: Macpherson Designs

---

## ✅ Implementation Checklist

- [x] Custom post type `dealer_registration` registered
- [x] Registration form shortcode created
- [x] Form submission handler implemented
- [x] Admin list table for registrations
- [x] Approval workflow with auto-username generation
- [x] Rejection workflow with reason
- [x] Email notifications (admin, approval, rejection)
- [x] Force password change on first login
- [x] Password change page and shortcode
- [x] NDA access restriction (blocks ALL portal pages)
- [x] Auto-redirect flow (password → NDA → dashboard)
- [x] Page Manager updated for password change page
- [ ] **Complete end-to-end testing** ← NEXT STEP

---

**Last Updated:** November 19, 2025  
**Plugin Version:** 1.3.0  
**Author:** Macpherson Designs
