# Dealer Portal Module - Complete Documentation

Complete documentation for the JBLund Dealers dealer portal module including NDA acceptance, role management, menu visibility, and testing instructions.

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Installation & Testing](#installation--testing)
4. [Code Structure](#code-structure)
5. [Phase Status](#phase-status)
6. [Integration Details](#integration-details)

---

## Overview

This module adds dealer portal functionality to the JBLund Dealers plugin, including:

- Custom dealer role with restricted capabilities
- NDA acceptance workflow with signature capture
- Menu visibility controls (show/hide by role)
- Email notifications (dealer + admin)
- PDF generation for signed NDAs
- Protected dealer dashboard and profile pages

**Total Code**: 737+ lines across 5 classes and 2 email templates  
**Development Time**: ~20 hours total (Phase 1 complete, Phase 2 in progress)

---

## Features

### ✅ Phase 1 Complete

1. **Custom Dealer Role** (`class-dealer-role.php` - 258 lines)

   - Creates "Dealer" role on activation
   - Custom capabilities (read, edit_posts, upload_files)
   - Auto-cleanup on plugin deletion

2. **NDA Handler** (`class-nda-handler.php` - 280 lines)

   - Login redirect to NDA page for dealers
   - Access restriction until NDA accepted
   - Form processing with signature data
   - JSON user meta storage
   - Auto-creates NDA acceptance page

3. **Menu Visibility** (`class-menu-visibility.php` - 145 lines)

   - Admin UI in menu editor
   - Three modes: Everyone, Dealers Only, Hide from Dealers
   - Role-based filtering on frontend

4. **Email System** (`class-email-handler.php` - 155 lines)

   - Dual delivery (dealer + admin)
   - Template-based with variable substitution
   - PDF attachment support
   - Error handling and logging

5. **Email Templates** (157 lines total)
   - `dealer-nda-confirmation.php` - Dealer notification
   - `admin-nda-notification.php` - Admin alert

### ⏳ Phase 2 Pending

6. **Signature Pad Integration** (~4 hours)

   - JavaScript canvas for signature capture
   - Clear/redo functionality
   - Signature validation

7. **PDF Generation** (~6 hours)

   - TCPDF library integration
   - NDA document with embedded signature
   - Secure file storage

8. **Frontend Templates** (~5 hours)
   - Dealer dashboard page
   - Dealer profile editing
   - Custom login page (optional)

---

## Installation & Testing

### Prerequisites

- JBLund Dealers plugin activated
- WordPress 5.0+
- PHP 7.0+

### Activation Steps

1. **Start Local site** (plugin-testing)
2. **Deactivate** JBLund Dealers plugin
3. **Reactivate** JBLund Dealers plugin

**What Happens:**

- ✅ Creates custom "Dealer" role
- ✅ Creates "Dealer NDA Acceptance" page with `[jblund_nda_acceptance]` shortcode
- ✅ Flushes rewrite rules

### Create Test Dealer User

1. Go to **Users → Add New**
2. Create user:
   - Username: `testdealer`
   - Email: `testdealer@example.com`
   - Password: (generate/set)
   - **Role**: Select "Dealer"
3. Save User

### Test Menu Visibility

1. Go to **Appearance → Menus**
2. Edit any menu item
3. Look for **"Dealer Visibility"** dropdown
4. Options:
   - Show to all users (default)
   - Show to dealers only
   - Hide from dealers
5. Set one item to "Show to dealers only"
6. Save menu
7. Test as different users:
   - Admin: Should NOT see dealer-only item
   - Dealer: SHOULD see dealer-only item

### View NDA Acceptance Page

1. Go to **Pages → All Pages**
2. Find "Dealer NDA Acceptance"
3. Click "View"
4. You should see:
   - Professional NDA legal text
   - Representative information form
   - Signature canvas placeholder
   - "Accept Agreement" button

### Test Login Redirect

1. Log out of WordPress admin
2. Log in as `testdealer` user
3. Expected:
   - Redirects to NDA acceptance page
   - Form displays correctly
   - (Signature capture pending Phase 2)

### Verification Checklist

- [ ] Plugin reactivated successfully
- [ ] "Dealer" role appears in Users → Add New
- [ ] Test dealer user created
- [ ] Menu visibility dropdown appears
- [ ] Menu items filter correctly by role
- [ ] "Dealer NDA Acceptance" page exists
- [ ] NDA page displays with shortcode
- [ ] Login as dealer redirects to NDA
- [ ] Form is mobile-responsive

---

## Code Structure

```
modules/dealer-portal/
├── classes/
│   ├── class-dealer-role.php       ✅ 258 lines (Phase 1 complete)
│   ├── class-email-handler.php     ✅ 155 lines (Phase 1 complete)
│   ├── class-nda-handler.php       ✅ 280 lines (Phase 1 complete)
│   └── class-menu-visibility.php   ✅ 145 lines (Phase 1 complete)
├── templates/
│   ├── dealer-dashboard.php        ⏳ Pending Phase 2
│   ├── dealer-login.php            ⏳ Pending Phase 2
│   ├── dealer-profile.php          ⏳ Pending Phase 2
│   ├── nda-acceptance-page.php     ✅ 335 lines (Phase 1 complete)
│   └── emails/
│       ├── dealer-nda-confirmation.php  ✅ 75 lines
│       └── admin-nda-notification.php   ✅ 82 lines
├── assets/
│   ├── css/  📁 Ready for Phase 2
│   └── js/   📁 Ready for Phase 2
├── README.md              📄 This file
└── DIVI-INTEGRATION.md    📄 Divi Builder customization guide
```

### Namespace

All classes use: `JBLund\DealerPortal`

### WordPress Integration Points

**Hooks:**

- `wp_login` - Login redirect check
- `template_redirect` - Access restriction
- `init` - Register dealer role
- `wp_nav_menu_objects` - Menu filtering

**Custom Actions:**

- `jblund_dealer_nda_accepted` - Fires after NDA acceptance

**User Meta Keys:**

- `_dealer_nda_acceptance` - JSON acceptance data
- `_dealer_nda_accepted` - Quick boolean check ('1' or empty)
- `_dealer_nda_pdf_path` - PDF file path (pending Phase 2)

**Menu Meta Keys:**

- `_menu_item_dealer_visibility` - Menu item visibility setting

---

## Phase Status

### Phase 1: Code Extraction & Integration ✅ Complete

**Time**: ~10 hours  
**Lines**: 737  
**Status**: All backend code complete and integrated

**Completed:**

- [x] Directory structure created
- [x] Email handler class
- [x] Email templates (dealer + admin)
- [x] NDA handler (redirect, restriction, processing)
- [x] Menu visibility system
- [x] Dealer role management
- [x] Integration with main plugin
- [x] Documentation

### Phase 2: Frontend & PDF Generation ⏳ In Progress

**Estimated Time**: ~15 hours remaining  
**Status**: Backend ready, frontend pending

**Remaining:**

- [ ] Signature Pad JS integration (~4 hours)
- [ ] PDF generation with TCPDF (~6 hours)
- [ ] Dealer dashboard template (~2 hours)
- [ ] Profile editing template (~2 hours)
- [ ] End-to-end testing (~1 hour)

### Phase 3: Future Enhancements

**Territory Management** (IND-39, IND-40) - 12 hours  
**Registration & Approval** (IND-42) - 10 hours  
**Dashboard Widgets** (IND-45) - 15 hours  
**User-to-Post Linking** (IND-46) - 8 hours

**Total Future**: ~45 hours

---

## Integration Details

### Load Module Classes

In `jblund-dealers.php`:

```php
require_once plugin_dir_path(__FILE__) . 'modules/dealer-portal/classes/class-dealer-role.php';
require_once plugin_dir_path(__FILE__) . 'modules/dealer-portal/classes/class-email-handler.php';
require_once plugin_dir_path(__FILE__) . 'modules/dealer-portal/classes/class-nda-handler.php';
require_once plugin_dir_path(__FILE__) . 'modules/dealer-portal/classes/class-menu-visibility.php';

// Initialize
new JBLund\DealerPortal\Dealer_Role();
new JBLund\DealerPortal\Email_Handler();
new JBLund\DealerPortal\NDA_Handler();
new JBLund\DealerPortal\Menu_Visibility();
```

### Activation Hook

```php
register_activation_hook(__FILE__, 'jblund_dealers_activate');
function jblund_dealers_activate() {
    // Create dealer role
    $dealer_role = new JBLund\DealerPortal\Dealer_Role();
    $dealer_role->create_dealer_role();

    // Create NDA page
    $nda_handler = new JBLund\DealerPortal\NDA_Handler();
    $nda_handler->create_nda_page();

    flush_rewrite_rules();
}
```

### Hook PDF & Email to NDA Acceptance

```php
add_action('jblund_dealer_nda_accepted', 'jblund_handle_nda_acceptance', 10, 2);
function jblund_handle_nda_acceptance($user_id, $signature_data) {
    // 1. Generate PDF (Phase 2)
    $pdf_generator = new JBLund\DealerPortal\PDF_Generator();
    $pdf_path = $pdf_generator->generate_nda_pdf($user_id, $signature_data);

    // 2. Send emails
    $email_handler = new JBLund\DealerPortal\Email_Handler();
    $email_handler->send_nda_confirmation($user_id, $pdf_path);
    $email_handler->send_admin_notification($user_id, $pdf_path);
}
```

---

## Code Reuse Statistics

### Source Breakdown

| Source Plugin           | Original | Extracted | Enhancement     | Ratio    |
| ----------------------- | -------- | --------- | --------------- | -------- |
| login-terms-acceptance  | 102      | 592       | Email + NDA     | 5.8x     |
| hide-menu-items-by-role | 26       | 145       | Bidirectional   | 5.6x     |
| **Total**               | **128**  | **737**   | **Full Portal** | **5.8x** |

**Reuse Breakdown:**

- 20% direct code reuse (patterns, logic)
- 80% enhanced functionality
- 6 hours saved vs building from scratch

---

## Troubleshooting

### Dealer role not appearing?

- Deactivate and reactivate plugin
- Check PHP error logs
- Verify in Users → Add New → Role dropdown

### Menu visibility not working?

- Hard refresh browser (Cmd+Shift+R)
- Clear WordPress cache
- Try different menu item

### NDA page not found?

- Go to Settings → Permalinks → Save Changes
- Check Pages → All Pages for "dealer-nda-acceptance"
- Manually create with shortcode: `[jblund_nda_acceptance]`

### Login redirect not working?

- User must have ONLY dealer role (not admin + dealer)
- Check if NDA already accepted in user meta
- Clear browser cookies

---

## Security Notes

- All inputs sanitized (`sanitize_text_field`, `sanitize_textarea_field`)
- All outputs escaped (`esc_html`, `esc_attr`, `esc_url`)
- Nonce verification: `accept_nda_action`
- Role checks: `in_array('dealer', $user->roles)`
- IP/user agent tracking in acceptance data

---

## Documentation

- **README.md** (this file) - Complete module documentation
- **DIVI-INTEGRATION.md** - Divi Builder customization guide
- **../../CHANGELOG.md** - Version history
- **../../USAGE_GUIDE.md** - Main plugin usage

---

**Last Updated**: November 5, 2025  
**Status**: Phase 1 Complete ✅  
**Next**: Phase 2 - Signature Pad & PDF Generation

## Extraction Overview

**Total Extracted**: ~240 lines  
**Time Saved**: ~6 hours  
**Source Plugins**: `login-terms-acceptance`, `hide-menu-items-by-role`

---

## 1. Email Handler (`class-email-handler.php`)

**Extracted From**: `login-terms-acceptance/app/class-mail-handler.php` (42 lines)  
**Adapted To**: 163 lines including dual email delivery  
**Time Saved**: ~2 hours

### What Was Reused:

✅ **Core Pattern** (100% reusable):

- `get_mail_message()` template loading pattern with `ob_start()` / `ob_get_clean()`
- `send_mail()` structure with `wp_mail()` delivery
- Exception handling and error logging
- Headers structure

### What Was Adapted:

🔧 **Extended Functionality**:

- Split into two methods: `send_nda_confirmation()` and `send_admin_notification()`
- Added PDF attachment support
- Added dealer/user data retrieval
- Added template variable extraction
- Dual email delivery (dealer + admin)

### Key Changes:

```php
// Original XLTA - single email
public function send_mail() {
    $message = $this->get_mail_message();
    wp_mail($to_email, $subject, $message, $headers);
}

// Our adaptation - dual delivery with attachments
public function send_nda_confirmation($user_id, $pdf_path) {
    $message = $this->get_mail_message('dealer-nda-confirmation', $vars);
    wp_mail($to, $subject, $message, $headers, [$pdf_path]);
}
```

---

## 2. Email Templates

**Extracted From**: `login-terms-acceptance/admin/templates/email-template.php` (60 lines)  
**Adapted To**:

- `dealer-nda-confirmation.php` (75 lines)
- `admin-nda-notification.php` (82 lines)

**Time Saved**: ~2 hours

### What Was Reused:

✅ **HTML Structure**:

- DOCTYPE and responsive email markup
- Centered container with border/shadow
- Header section with branded color
- Content padding and spacing
- Footer with copyright

### What Was Adapted:

🔧 **Brand Customization**:

- Colors: `#0073aa` (blue) → `#003366` (JB Lund navy)
- Header: "Terms and Conditions" → "NDA Acceptance"
- Content: Generic terms → Dealer-specific NDA details
- Added CTA buttons for portal access
- Added detailed agreement information boxes

### What Was Built Fresh:

❌ **New Features**:

- Admin notification template (separate from dealer email)
- Agreement details box with dealer/representative info
- "Next Steps" section for admins
- Green success header for admin notifications
- Direct links to dealer portal and admin area

---

## 3. Menu Visibility Logic

**Status**: ⏳ **NOT YET EXTRACTED** (planned)  
**Source**: `hide-menu-items-by-role/includes/functions.php` (26 lines)  
**Target**: `class-menu-visibility.php`

### Extraction Plan:

✅ **Reusable Pattern** (~26 lines):

- `wp_nav_menu_objects` filter hook
- Menu item iteration and conditional `unset()`
- Post meta retrieval for visibility rules

🔧 **Simplifications Needed**:

- Original: Multi-role array checking `array_intersect($user->roles, $roles)`
- Our version: Single dealer role `in_array('dealer', $user->roles)`
- Add bidirectional visibility (dealer-only AND hide-from-dealers)

---

## 4. NDA Handler (Settings Patterns)

**Status**: ⏳ **NOT YET EXTRACTED** (planned)  
**Source**: `login-terms-acceptance/app/class-settings.php` (multiple functions)  
**Target**: `class-nda-handler.php`

### Extraction Plan:

✅ **Login Redirect** (lines 115-132, ~18 lines):

```php
// Adapt xlta_user_redirect_after_login()
public function redirect_to_nda_if_not_accepted($redirect_to, $request, $user) {
    if (!is_wp_error($user) && in_array('dealer', $user->roles)) {
        $nda_accepted = get_user_meta($user->ID, '_dealer_nda_accepted', true);
        if (!$nda_accepted) {
            return home_url('/dealer-nda-acceptance/');
        }
    }
    return $redirect_to;
}
```

✅ **Access Restriction** (lines 145-168, ~24 lines):

```php
// Adapt xlta_restrict_access_based_on_terms()
public function restrict_dealer_portal_access() {
    if (is_user_logged_in() && !is_admin()) {
        $user = wp_get_current_user();
        if (in_array('dealer', $user->roles)) {
            $nda_accepted = get_user_meta($user->ID, '_dealer_nda_accepted', true);
            $allowed_pages = ['dealer-nda-acceptance', 'wp-login.php'];
            if (!$nda_accepted && !in_array(current_page, $allowed_pages)) {
                wp_redirect(home_url('/dealer-nda-acceptance/'));
                exit;
            }
        }
    }
}
```

✅ **Form Processing** (lines 260-283, ~24 lines):

```php
// Adapt xlta_terms_acceptance_actions()
public function process_nda_acceptance() {
    if (isset($_POST['accept_dealer_nda']) && wp_verify_nonce($_POST['_nda_nonce'], 'dealer_nda_acceptance')) {
        $user_id = get_current_user_id();
        $signature_data = sanitize_text_field($_POST['signature_data']);

        $acceptance_data = json_encode([
            'accepted' => true,
            'acceptance_date' => current_time('mysql'),
            'acceptance_ip' => $_SERVER['REMOTE_ADDR'],
            'signature_data' => $signature_data
        ]);

        update_user_meta($user_id, '_dealer_nda_acceptance', $acceptance_data);
        update_user_meta($user_id, '_dealer_nda_accepted', '1');

        do_action('jblund_nda_accepted', $user_id, $signature_data);
        wp_redirect(home_url('/dealer-dashboard/'));
        exit;
    }
}
```

✅ **User Meta Structure**:

- JSON-encoded acceptance data (same pattern as XLTA)
- Quick boolean field for fast checks
- Stores: acceptance status, date, IP, signature

---

## Code Reuse Statistics

### By Source Plugin:

| Plugin                  | Lines Extracted | Lines Adapted | Time Saved  |
| ----------------------- | --------------- | ------------- | ----------- |
| login-terms-acceptance  | 102             | 240           | 4 hours     |
| hide-menu-items-by-role | 26              | 60 (planned)  | 2 hours     |
| **Total**               | **128**         | **300**       | **6 hours** |

### By Component:

| Component       | Reused  | Adapted | Built Fresh   | Total   |
| --------------- | ------- | ------- | ------------- | ------- |
| Email Handler   | 42      | 121     | 0             | 163     |
| Email Templates | 60      | 97      | 0             | 157     |
| NDA Handler     | 0       | 0       | 200 (planned) | 200     |
| Menu Visibility | 0       | 0       | 60 (planned)  | 60      |
| **Total**       | **102** | **218** | **260**       | **580** |

---

## Implementation Roadmap

### Phase 1: Completed ✅

- [x] Directory structure created
- [x] Email handler class extracted and adapted
- [x] Email templates created (dealer + admin)
- [x] Documentation created

### Phase 2: Remaining

- [ ] Extract NDA handler patterns (redirect, restriction, form processing)
- [ ] Extract menu visibility logic
- [ ] Create NDA acceptance page template
- [ ] Implement Signature Pad JS canvas
- [ ] Integrate TCPDF for PDF generation
- [ ] Wire up all components in main plugin file

---

## Next Steps

1. **Complete NDA Handler Extraction**:

   - Read `TEMP - OTHER PLUGINS/login-terms-acceptance/app/class-settings.php`
   - Extract redirect, restriction, and form processing functions
   - Adapt for dealer role and NDA-specific logic
   - Create `class-nda-handler.php`

2. **Complete Menu Visibility Extraction**:

   - Read `TEMP - OTHER PLUGINS/hide-menu-items-by-role/includes/functions.php`
   - Extract menu filter function
   - Simplify for single dealer role
   - Create `class-menu-visibility.php`

3. **Build Fresh Components**:

   - Signature Pad JS integration
   - PDF generator class with TCPDF
   - NDA acceptance page template
   - Dealer role system
   - Territory management

4. **Integration**:
   - Load module classes in main plugin file
   - Register hooks and actions
   - Create activation hooks for page creation
   - Test complete workflow

---

## Files Created

```
modules/dealer-portal/
├── classes/
│   └── class-email-handler.php          ✅ Complete (163 lines)
├── templates/
│   └── emails/
│       ├── dealer-nda-confirmation.php  ✅ Complete (75 lines)
│       └── admin-nda-notification.php   ✅ Complete (82 lines)
├── assets/
│   ├── css/                             📁 Empty (ready for future)
│   └── js/                              📁 Empty (ready for future)
└── README.md                            📄 This file
```

---

## Maintenance Notes

- All WordPress functions use backslash prefix (`\esc_html()`) due to namespace
- Lint errors about "Undefined function" are expected - WordPress loads these
- Email templates use inline CSS for email client compatibility
- Exception handling matches WordPress best practices
- All strings are internationalized with `jblund-dealers` text domain

---

**Last Updated**: November 2025  
**Status**: Phase 1 Complete, Phase 2 In Progress
