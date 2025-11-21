# JBLund Dealers WordPress Plugin - AI Coding Guide

## Architecture Overview

This WordPress plugin implements a complete dealer management system with a **modular architecture**. The plugin has evolved from a single-file structure to a **module-based system** for better maintainability and scalability.

### Modular Structure

```
jblund-dealers/
├── jblund-dealers.php (Main bootstrap file)
├── uninstall.php (Clean uninstall)
├── assets/ (CSS/JS)
├── modules/
│   ├── core/ (Post types, meta boxes)
│   ├── admin/ (Settings, dashboard, CSV)
│   ├── frontend/ (Shortcodes, public display)
│   └── dealer-portal/ (Private portal, registration, NDA)
└── .github/copilot-instructions.md (This file)
```

**Core Components:**

- **Custom Post Types**: `dealer` (public listings) and `dealer_registration` (applications)
- **Meta Data**: Main dealer info + dynamic sub-locations array + registration metadata
- **Frontend Display**: Shortcode-based responsive grid layout
- **Admin Interface**: Advanced settings with tabs, message scheduling, email templates
- **Dealer Portal**: Private dashboard, NDA management, profile editing

## Key Patterns & Conventions

### Meta Data Structure

```php
// Main dealer fields (simple meta)
_dealer_company_name, _dealer_company_address, _dealer_company_phone, _dealer_website
_dealer_docks, _dealer_lifts, _dealer_trailers (boolean as '1'/'0')

// Sub-locations (serialized array)
_dealer_sublocations = [
    ['name' => '', 'address' => '', 'phone' => '', 'website' => '', 'docks' => '1', ...]
]
```

### Security Pattern

All data handling follows WordPress security practices:

- Nonce verification: `jblund_dealer_meta_box_nonce`
- Sanitization: `sanitize_text_field()`, `sanitize_textarea_field()`, `esc_url_raw()`
- Output escaping: `esc_html()`, `esc_attr()`, `esc_url()`
- Capability checks: `current_user_can('edit_post', $post_id)`

### Dynamic Sub-Locations System

Sub-locations use JavaScript to add/remove form fields dynamically. The pattern:

1. Server renders existing sub-locations with indexed names: `dealer_sublocations[0][name]`
2. JavaScript increments index counter for new fields
3. PHP processes the indexed array on save

## Critical Files & Locations

### Bootstrap & Core

- **`jblund-dealers.php`**: Main plugin file - autoloads modules via namespaces
- **`uninstall.php`**: Complete data cleanup on plugin deletion

### Module Structure

- **`modules/core/`**: Post type registration, meta boxes
- **`modules/admin/`**: Settings page, CSV import/export, dashboard widget, column management
- **`modules/frontend/`**: Public shortcodes and styling
- **`modules/dealer-portal/`**: Registration forms, dealer dashboard, NDA handling, email templates

### Assets

- **`assets/css/dealers.css`**: Frontend public dealer directory styling
- **`assets/css/dashboard.css`**: Dealer portal dashboard styling
- **`assets/js/email-editor.js`**: Email template editor functionality

### Templates

- **`modules/dealer-portal/templates/`**: Email templates (approval, rejection, admin notification)
- **`modules/dealer-portal/templates/dealer-dashboard.php`**: Portal dashboard template

### Documentation

- **Root level**: README.md, CHANGELOG.md, USAGE_GUIDE.md, DEALER-REGISTRATION-WORKFLOW.md

## WordPress Integration Points

### Hooks & Actions

```php
add_action('init', 'register_dealer_post_type')
add_action('add_meta_boxes', 'add_dealer_meta_boxes')
add_action('save_post_dealer', 'save_dealer_meta')     // Post type specific hook
add_action('wp_enqueue_scripts', 'enqueue_frontend_styles')
add_action('admin_menu', 'add_settings_page')         // Settings under Dealers menu
add_action('admin_init', 'register_settings')         // Settings fields & sections
add_filter('manage_dealer_posts_columns', 'add_dealer_columns')        // Admin list columns
add_action('manage_dealer_posts_custom_column', 'populate_dealer_columns') // Column data
add_shortcode('jblund_dealers', 'dealers_shortcode')
```

### Admin Customizations

- **Custom Columns**: Shows address, phone, website, services, sub-location count
- **Settings Page**: Color customization, layout defaults, icon toggles
- **Meta Box Note**: Explains title/company name sync to users

### Plugin Lifecycle

- **Activation**: Registers post type + flushes rewrite rules
- **Deactivation**: Only flushes rewrite rules (preserves data)
- **Uninstall**: Complete data removal via `uninstall.php`

## Development Workflows

### Modular Development Best Practices

**CRITICAL: Always follow these patterns when making changes:**

1. **Namespace Everything**: Use `namespace JBLund\[Module]` (e.g., `JBLund\Admin`, `JBLund\DealerPortal`)
2. **Singleton Pattern**: Use `get_instance()` for classes that need single instance
3. **Security First**: Always sanitize inputs, escape outputs, verify nonces
4. **WordPress Standards**: Follow WordPress Coding Standards and use built-in functions
5. **No Direct Access**: Always check `if (!defined('ABSPATH')) exit;`

### Module Organization

- **Core Module**: Post types, taxonomies, core data structures
- **Admin Module**: Backend-only functionality (settings, import/export, columns)
- **Frontend Module**: Public-facing features (shortcodes, public styling)
- **Dealer Portal Module**: Private dealer-only features (dashboard, registration, NDA)

### Adding New Fields

1. Add to `render_dealer_info_meta_box()` for form field
2. Add to `save_dealer_meta()` for processing with proper sanitization
3. Add to shortcode output in `dealers_shortcode()` with proper escaping
4. Update CSS in `dealers.css` for styling
5. Add to admin columns in `add_dealer_columns()` and `populate_dealer_columns()`

### Extending Sub-Locations

Sub-locations use a repeatable field pattern. To add fields:

1. Update both `render_sublocation_row()` and JavaScript template
2. Update save logic in `save_dealer_meta()`
3. Update frontend display in shortcode with icon/list options

### Company Name = Post Title

Company name automatically syncs with post title on save. No separate company name field needed.

### Settings System

Plugin includes comprehensive admin settings under **Dealers > Settings** with multiple tabs:

- **General**: Shortcode defaults (layout, icons)
- **Portal Pages**: Page assignments for login, dashboard, profile, NDA
- **Appearance**: Color customization for public dealer cards
- **Portal Updates**: Scheduled announcements for dealer dashboard
- **Documents**: Required document management with file uploads
- **NDA Editor**: Full WYSIWYG NDA content editor
- **Registration**: Success message scheduler with date ranges
- **Email Templates**: Approval, rejection, and admin notification email customization
- **Import/Export**: CSV bulk operations
- **Help & Guide**: Documentation and shortcode reference

Settings stored in multiple options:

- `jblund_dealers_settings` (main settings)
- `jblund_dealers_portal_pages` (page assignments)
- `jblund_email_template_*` (email templates)
- `jblund_registration_messages` (scheduled messages)

### Layout Options

Three layout modes via shortcode `layout=""` parameter:

- `grid` (default): CSS Grid with auto-fill columns
- `list`: Horizontal card layout
- `compact`: Smaller grid with tighter spacing

### Service Display

Services can show as icons or traditional list based on settings:

- Icons: Emoji with labels (🚢 Docks, ⚓ Lifts, 🚛 Trailers)
- List: Traditional bullet points
- Responsive scaling for mobile

## Shortcode System

**Default**: `[jblund_dealers]` displays all dealers
**Parameters**: `posts_per_page`, `orderby`, `order`, `layout`
**Examples**:

- `[jblund_dealers layout="list"]`
- `[jblund_dealers layout="compact" posts_per_page="6"]`

Uses `WP_Query` with dynamic layout classes and settings integration.

## Internationalization

Text domain: `jblund-dealers`
All user-facing strings use `__()` or `_e()` functions.
Ready for translation file generation.

## Email Template System

### Architecture

**Three Email Types:**

1. **Approval Email**: HTML template with credentials, next steps, login button
2. **Rejection Email**: HTML template with reason box, closing paragraphs
3. **Admin Notification**: HTML template with complete registration details

**Template Editing:**

- **Basic Editor**: Text-only fields that preserve HTML/CSS structure
- **Advanced Editor**: Raw HTML editing with marker system
- **Preview**: Live preview with sample data
- **Brand Color**: Customizable primary color (default #FF0000)

**HTML Markers**: Templates use HTML comments for section marking:

```html
<!-- [EDITABLE:SECTION_NAME] -->
Content here
<!-- [/EDITABLE:SECTION_NAME] -->
```

**Shortcodes Available:**

- Approval: `{{rep_name}}`, `{{username}}`, `{{password}}`, `{{login_url}}`
- Rejection: `{{rep_name}}`, `{{reason}}`
- Admin: `{{rep_name}}`, `{{rep_email}}`, `{{rep_phone}}`, `{{company}}`, `{{company_phone}}`, `{{territory}}`, `{{company_address}}`, `{{company_website}}`, `{{docks}}`, `{{lifts}}`, `{{trailers}}`, `{{notes}}`, `{{admin_url}}`

### Template Files

- `modules/dealer-portal/templates/emails/approval-template.html`
- `modules/dealer-portal/templates/emails/rejection-template.html`
- `modules/dealer-portal/templates/emails/admin-notification-template.html`

## Registration Workflow

### Process Flow

1. **Application Submission**: User fills `[jblund_dealer_registration]` form
2. **Registration Post Created**: Status `pending`, not visible to public
3. **Admin Notification**: HTML email sent to admin with all details
4. **Admin Review**: Admin can approve/decline from dashboard
5. **Approval Actions**:
   - Creates dealer user account
   - Sends approval email with credentials
   - User must accept NDA before accessing portal
6. **NDA Acceptance**: Triggers dealer profile publication (becomes public dealer)
7. **Portal Access**: Dealer can login, view dashboard, edit profile

### Key Classes

- `JBLund\DealerPortal\Registration_Form`: Form display and submission
- `JBLund\DealerPortal\Registration_Admin`: Approval/rejection handling
- `JBLund\DealerPortal\Email_Handler`: Email template processing
- `JBLund\Admin\Message_Scheduler`: Success message scheduling

## Testing & Debugging

### WordPress Debug Mode

- Enable `WP_DEBUG` for WordPress-specific issues
- Check `debug.log` for PHP errors and warnings
- Use WordPress query debugging for shortcode issues

### Common Issues

**JavaScript Errors:**

- Check browser console for errors
- Verify `wp_enqueue_media()` called for media uploader
- Ensure jQuery is loaded before custom scripts

**Email Issues:**

- Test with SMTP plugin (WP Mail SMTP recommended)
- Check spam folders for test emails
- Verify shortcode replacement in email output
- Use plain text fallback for email clients

**Portal Access:**

- Verify dealer role has correct capabilities
- Check NDA acceptance status
- Confirm page assignments in settings

**Security Checks:**

- Always verify nonce values
- Check `current_user_can()` for capabilities
- Sanitize ALL inputs, escape ALL outputs
- Use `wp_kses()` for HTML content

### Developer Tools

- **Query Monitor Plugin**: Debug queries, hooks, HTTP requests
- **Debug Bar**: WordPress debug information in admin bar
- **WP Mail SMTP**: Test and debug email delivery
- **Browser DevTools**: Network tab for AJAX debugging

## Testing & Debugging

- Enable `WP_DEBUG` for WordPress-specific issues
- Check browser console for JavaScript errors (sub-location management)
- Use WordPress query debugging for shortcode issues
- Verify nonce and capability checks are working
