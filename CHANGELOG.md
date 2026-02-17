# Changelog

## [2.3.0] - 2026-02-16

### Added

- **Service Icons settings** — upload custom icons for Docks, Lifts, and Trailers via the WP Media Library under Dealers → Settings → Service Icons; falls back to bundled SVGs if not set
- Icons are now respected everywhere: main dealer cards, sublocation rows, and map info-window popups
- **Map Pin Status meta box row** — each dealer edit screen now shows geocoding status (coordinates saved / failed / pending) with a "Clear & Re-geocode" or "Retry" button so anyone can refresh coordinates without touching code
- Editing a dealer's address automatically clears any cached geocoding failure so it retries on the next map load


## [2.2.2] - 2026-02-16

### Added

- Address geocoding fallback for the dealer map: dealers without saved lat/lng coordinates are now geocoded automatically via the Google Maps Geocoding API using their stored address
- Geocoded coordinates are cached back to post meta on first resolution — no repeated API calls on subsequent page loads
- Dealers that fail geocoding (bad address, API error) are flagged with `_dealer_geocode_failed` meta and silently skipped rather than retried on every load


## [2.2.1] - 2026-02-16

### Fixed

- Google Maps API key is now read from Dealers → Settings instead of Divi theme settings (Divi lookup was returning empty on live site)
- Updated map unavailable notice to direct admins to the correct settings location


## [2.2.0] - 2026-02-16

### Added

- New `[jblund_dealer_map]` shortcode — renders a Google Maps view with a marker for every dealer that has latitude/longitude coordinates
- Markers show an info popup on click with dealer name, address, phone, website link, and service icons
- Map auto-fits bounds to show all plotted dealers
- Accepts a `height` attribute to control map height (default: 500px)


## [2.1.1] - 2026-02-16

### Fixed
- Critical: regenerated composer autoloader without dev dependencies — myclabs/deep-copy was referenced in autoload_files but not shipped in the plugin ZIP, causing a fatal error on activation
- Fixed null-safety bug in dealer portal shortcode style enqueuing (operator precedence causing post_content warnings on non-post pages)


## [2.1.0] - 2026-02-16

### Added
- New SVG service icons (dock, lift, trailer) replacing emoji icons across all dealer layouts
- SVG icons render consistently at 40×40px on main dealer cards and 32×32px in sublocation rows

### Changed
- Replaced hardcoded emoji icons (🚢 ⚓ 🚛) with proper SVG img tags in list, grid, and compact layouts
- Updated SCSS to use explicit width/height for icon sizing (previously used font-size which had no effect on img elements)
- All icon filenames normalised to lowercase (dock.svg, lift.svg, trailer.svg)
- Documentation moved to /docs directory
- Updated .gitignore to exclude dev vendor packages, testing artifacts, and AI tool directories

### Fixed
- Sublocation service icons now match the main dealer card icons (were still showing old emojis)
- Inconsistent icon sizing across layouts now resolved


## [2.0.3] - 2025-12-04

### Added
- Version bump to 2.0.3

### Changed
- [Add your changes here]

### Fixed
- [Add bug fixes here]


## [2.0.2] - 2025-12-04

### Added
- Version bump to 2.0.2

### Changed
- [Add your changes here]

### Fixed
- [Add bug fixes here]


## [2.0.1] - 2025-11-25

### Added
- Version bump to 2.0.1

### Changed
- [Add your changes here]

### Fixed
- [Add bug fixes here]


All notable changes to the JBLund Dealers plugin will be documented in this file.

## [Unreleased]

### Added - NDA-Triggered Dealer Profile Publishing

**Smart Two-Stage Approval Workflow**:

- ✅ **Application Approved** → Dealer post created as **DRAFT** + user account created
- ✅ **NDA Signed** → Dealer post automatically **PUBLISHED** (goes live on website)
- ✅ **Complete automation** - no manual publishing required
- ✅ **Activity tracking** logs both stages: approval (draft created) and NDA acceptance (published)
- ✅ **Modal indicators** show draft status with yellow badge until NDA is signed
- ✅ **Bidirectional linking** maintained between registration, user, and dealer post throughout entire workflow

**Why This Workflow?**

- Dealers shouldn't appear publicly until they've agreed to terms (NDA)
- Separates approval decision from legal agreement requirement
- Admin can prepare dealer profile without exposing it prematurely
- Automatic publishing eliminates manual step once NDA is signed
- Full audit trail of approval → NDA → publication

**Technical Implementation**:

- Dealer post created with `post_status = 'draft'` on application approval
- `NDA_Handler::publish_dealer_post()` finds and publishes draft when NDA accepted
- Activity log tracks both "approved" and "dealer_published" actions
- Modal shows yellow "Draft" badge before NDA, green "Published" after
- `_dealer_user_id` meta link enables finding draft post by user

**User Experience**:

- **Admin**: Approves application → sees yellow "Draft" status in modal
- **Dealer**: Receives account credentials → logs in → must sign NDA
- **System**: Publishes dealer profile automatically when NDA signed
- **Admin**: Sees "PUBLISHED" activity log entry and green status
- **Public**: Dealer appears on website only after NDA acceptance

### Added - Automatic Dealer Profile Creation on Approval

**Streamlined Approval Workflow**:

- ✅ **Automatic dealer post creation** when application is approved
- ✅ **Bidirectional linking** between registration and dealer post
- ✅ **Complete data transfer** from registration to dealer profile:
  - Company name, address, phone, website
  - Territory/location information
  - Products & services (docks, lifts, trailers)
  - Business description and notes
- ✅ **Activity tracking** includes dealer_id for full audit trail
- ✅ **Modal enhancement** shows "Dealer Profile Created" section with direct link to edit dealer post
- ✅ **Maintains backward compatibility** with existing manual dealer creation workflow

**Technical Implementation**:

- New `create_dealer_post_from_registration()` method automatically creates dealer post during approval
- Stores `_registration_dealer_id` on registration post
- Stores `_dealer_registration_id` on dealer post (bidirectional link)
- All registration data intelligently mapped to dealer meta fields
- Dealer post published immediately and associated with approved user account

**Admin Benefits**:

- One-click approval creates both user account AND dealer profile
- No manual data entry required - everything transfers automatically
- Direct access to dealer profile from application modal
- Complete traceability between application and dealer
- Saves significant time on multi-step approval process

### Added - Application Detail Modal & Activity Tracking

**Application Detail Modal** for comprehensive review:

- AJAX-powered modal displaying complete application details
- "View Details" button on all applications (pending, approved, rejected)
- Organized sections: Representative Info, Company Info, Business Description, Submission Details, Activity Log
- Real-time data loading with loading spinner
- Proper field alignment with actual registration form:
  - Territory/Location field (replaces incorrect address/website fields)
  - Business Description section (displays applicant's business notes)
  - Removed non-existent services fields (docks/lifts/trailers)
- Responsive modal with clean styling
- Security: Nonce verification and capability checks

### Added - Application Detail Modal & Activity Tracking

**Application Detail Modal** for comprehensive review:

- AJAX-powered modal displaying complete application details
- "View Details" button on all applications (pending, approved, rejected)
- Organized sections: Representative Info, Company Info, Business Description, Submission Details, Activity Log
- Real-time data loading with loading spinner
- Proper field alignment with actual registration form:
  - Territory/Location field (replaces incorrect address/website fields)
  - Business Description section (displays applicant's business notes)
  - Removed non-existent services fields (docks/lifts/trailers)
- Responsive modal with clean styling
- Security: Nonce verification and capability checks

**Activity Tracking System** for accountability:

- Complete audit trail of all actions on applications
- Tracks who views, approves, and declines applications
- Timestamps for every action
- Detailed notes for each activity entry
- Color-coded activity badges (viewed=blue, approved=green, declined=red)
- Activity log displayed in detail modal as sortable table
- Shows: Date/Time, Action, User, Details columns
- Automatic logging on:
  - Application view (when modal opens)
  - Application approval (with user info)
  - Application decline (with rejection reason)
- Stored as serialized array in `_registration_activity` meta field
- `log_activity()` helper method for consistent logging

**Data Integrity Improvements**:

- Fixed field mismatch between registration form and modal display
- Modal now shows exactly what applicant submitted
- Business description properly displayed for review decisions
- All meta fields properly aligned with form structure
- Prepared for dealer post linking (dealer_id field added)

### Added - Advanced Message Scheduling System

**Message Scheduler** for dynamic registration success messages:

- Save unlimited message templates with custom titles, bodies, and timeline notes
- Set one message as "active" for normal use
- Schedule specific messages for time periods (start date/time to end date/time)
- Automatic message switching based on schedule (perfect for holidays, team outings, etc.)
- Scheduled messages automatically revert to default after time period ends
- Visual status indicators (Active Now, Upcoming, Past)
- Full AJAX-powered interface for smooth user experience
- Complete CRUD operations: Create, Read, Update, Delete messages
- Protected default message (cannot be deleted)
- Schedule management: View all scheduled messages, delete schedules
- Example use case: "2-3 day response time" normally, but "5-7 days during holiday break" scheduled for specific dates

**Technical Features:**

- New `Message_Scheduler` class handles all message and schedule logic
- Stores multiple message templates in `jblund_dealers_registration_messages` option
- Stores schedules in `jblund_dealers_registration_schedule` option
- Active message determined by: Current schedule > Manual active selection > Default
- Real-time evaluation of schedules on every page load
- WordPress nonce verification and capability checks for all AJAX operations
- Sanitized inputs and escaped outputs throughout

**User Experience:**

- Clean interface showing currently active message at top
- Table view of all saved messages with status indicators
- One-click actions: Edit, Set Active, Schedule, Delete
- Modal editors for creating/editing messages and schedules
- Schedule table shows all upcoming, active, and past schedules
- Color-coded status badges for easy scanning
- Integrated seamlessly into existing Registration settings tab

### Added - Admin Experience Enhancements

**Dashboard Widget** for dealer application visibility:

- Real-time pending and total application counts
- 5 most recent applications displayed with metadata
- NEW badges for applications within 24 hours
- Color-coded status badges (pending/approved/rejected)
- Human-readable time differences
- One-click "View All Applications" button
- Only visible to users with `manage_options` capability

**Menu Enhancements** for improved navigation:

- Renamed "Registrations" to "Applications" for clarity
- Pending count bubble on menu item (WordPress standard styling)
- Updates dynamically as applications are processed
- Only displays bubble when count > 0

**Customizable Registration Messages**:

- New Registration settings tab in plugin settings
- Three customizable fields: Title, Message (rich text), Timeline Note
- Rich text editor (wp_editor) for message body
- Graceful fallback to default messages if not configured
- Helpful tips section for writing effective messages
- Registration workflow overview for admins

**Settings Page Improvements**:

- Comprehensive shortcode documentation in Help & Guide tab
- All 6 shortcodes documented with parameters and examples
- Usage tips for Divi, Elementor, and standard WordPress
- Color-coded sections for better organization
- Professional formatting with tables and inline examples

### Added - Visual Customizer Module

**Live Visual Editor** with real-time preview and dual editing modes:

- **Visual Mode**: Intuitive controls for all 28 appearance settings
  - Color pickers with WordPress native color picker
  - Range sliders with real-time value display
  - Select dropdowns for predefined options
  - Organized into 5 collapsible sections (Colors, Typography, Spacing, Effects, Custom CSS)
- **CSS Mode**: Direct CSS editing for advanced customization
  - Syntax-highlighted textarea
  - Copy CSS to clipboard functionality
  - Combines with visual settings
- **Real-Time Preview**: See changes instantly without page refresh
  - Preview with sample dealer cards (realistic data)
  - Device toggle (Desktop, Tablet, Mobile viewports)
  - Responsive preview frame with accurate sizing
- **Action Buttons**:
  - Save Changes: Store settings to database via AJAX
  - Reset: Restore all settings to defaults
  - Copy CSS: Export generated CSS for manual use

**Module Architecture**:

- Location: `modules/visual-customizer/`
- Self-contained module with classes, assets, templates
- Integration: New admin menu under Dealers → Visual Customizer
- Settings stored in existing `jblund_dealers_settings` option

**Technical Features**:

- AJAX-powered real-time updates (no page reload)
- WordPress color picker integration
- Nonce verification and capability checks
- Sanitized input and escaped output
- Responsive admin interface

**User Experience**:

- Split-view interface: Controls (left) + Preview (right)
- Mode toggle between Visual and CSS editing
- Device size toggle for responsive testing
- Professional admin UI matching WordPress standards
- Instant visual feedback while customizing

## [1.2.0] - 2025-11-10

### Added - Advanced Appearance Customization System

**28 Comprehensive Appearance Settings** organized into 5 categories:

- **Colors (10 settings)**:
  - Card header color, background color, button color
  - Primary and secondary text colors
  - Border color, button text color, icon color, link color, hover background
- **Typography (4 settings)**:
  - Heading font size (16-32px)
  - Body font size (12-18px)
  - Heading font weight (Normal, Semi-Bold, Bold, Extra Bold)
  - Line height (1.2-2.0)
- **Spacing & Layout (6 settings)**:
  - Card padding, margin, grid gap
  - Border radius, width, and style (solid, dashed, dotted, none)
- **Visual Effects (4 settings)**:
  - Box shadow (None, Light, Medium, Heavy)
  - Hover effects (None, Lift Up, Scale, Shadow Increase)
  - Transition speed, icon size
- **Custom CSS (1 setting)**: Free-form CSS textarea for advanced customization

**Theme Integration**:

- "Inherit Theme Styles" option to use theme colors and fonts automatically
- Uses CSS variables: `--wp--preset--color--primary`, `--wp--preset--color--background`, etc.
- Selective CSS overrides - only applies changes that differ from defaults
- Maintains base CSS file integrity

**User Experience**:

- Collapsible settings sections with badges showing option counts
- Live preview with color pickers
- Range sliders with real-time value display
- Professional admin interface with organized tabs

### Added - CSV Import/Export with Column Mapping

**Intelligent Column Mapping Interface**:

- Two-step import process: Upload → Map Columns → Import
- Visual mapping interface shows CSV columns with preview data
- **Auto-Detection**: Automatically maps common column names to dealer fields
  - Detects: name, company name, street, city_state_zip, phone, website, etc.
  - Shows "✓ Auto-detected" badge for recognized columns
- **Preview Table**: Shows first 5 rows of CSV data before import
- **Flexible Field Mapping**: Dropdown for each CSV column to select dealer field
- **"Do Not Import" Option**: Skip unwanted columns

**Import Features**:

- Supports split address fields (street + city_state_zip) or full address
- Handles both "1/0" and "Yes/No" formats for services
- Creates new dealers with all mapped data
- Success/error feedback with detailed statistics
- Automatic redirect with admin notices after import
- Temp file handling for secure multi-step process

**Export Features**:

- Export all dealer data to CSV including:
  - All dealer information (ID, name, address, phone, website)
  - GPS coordinates and custom map links
  - Services (Docks, Lifts, Trailers as Yes/No)
  - Sub-locations data (as JSON)
- Excel-compatible with UTF-8 BOM
- Timestamped filenames

**CSV Format Support**:

- Simple format: `name,street,city_state_zip,phone,website,docks,lifts,trailers`
- Full export format: `ID,Company Name,Address,Phone,Website,Latitude,Longitude,Custom Map Link,Docks,Lifts,Trailers,Sub-Locations`
- Flexible header detection (case-insensitive)

### Changed - Enhanced Import/Export Tab

**New UI Design**:

- Color-coded sections with emoji icons (📥 Export, 📤 Import)
- Expanded format documentation with code examples
- Important notes callout box with warnings
- Better visual hierarchy and spacing
- Professional button styling with icons

**User Guidance**:

- Step-by-step instructions for both import and export
- Format examples showing exact CSV structure
- Preview capabilities before committing to import
- Clear error messages for common issues

### Fixed - CSV Import Feedback

- **Critical Fix**: Import now shows results via admin notices
  - Previously: Import ran silently with no user feedback
  - Now: Shows success messages with counts (imported/updated/errors)
  - Added proper `wp_redirect()` with query parameters
  - Created `display_import_notices()` method for admin notices
- Fixed upload error handling with specific error messages
- Fixed empty file detection
- Fixed invalid format detection with helpful guidance

### Technical - Version 1.2.0

- Updated plugin version from 1.0.0 to 1.2.0
- Updated version constant: `JBLUND_DEALERS_VERSION`
- **Code Statistics**:
  - Appearance system: ~400 lines (settings fields, callbacks, CSS generation)
  - CSV column mapping: ~300 lines (mapping interface, auto-detection, preview)
  - Import enhancements: ~200 lines (redirect handling, notices, validation)
  - Total new/modified: ~900 lines
- New admin hooks: `admin_notices` for import feedback
- New methods:
  - `render_column_mapping_interface()` - Visual column mapper
  - `update_dealer_meta_from_mapped_data()` - Import with custom mapping
  - `display_import_notices()` - Admin notice handler
- Enhanced settings tabs with collapsible sections
- JavaScript enhancements for interactive UI

### Database

- No schema changes
- Appearance settings stored in `jblund_dealers_settings` option (existing)
- CSV import uses temporary files (automatically cleaned up)

## [Unreleased]

### Added - Bug Fix: NDA Acceptance Redirect Loop (November 5, 2025)

**Critical Bug Fix**:

- Fixed infinite page refresh loop after NDA acceptance
- **Root Cause**: `process_nda_acceptance()` was using `wp_redirect()` without `exit`, causing redirect to fail
- **Solution**: Changed to `wp_safe_redirect()` with explicit `exit` statement
- **Impact**: Dealers can now successfully accept NDA and be redirected to dashboard
- **File Modified**: `modules/dealer-portal/classes/class-nda-handler.php` (line 200)

### Added - Automatic Page Creation System (November 5, 2025)

**Page Manager Class** (310 lines) - `modules/dealer-portal/classes/class-page-manager.php`:

- Automatically creates all dealer portal pages on plugin activation
- **Divi Builder Integration** ✨ NEW:
  - **Auto-detects Divi theme** - Checks if Divi or Divi child theme is active
  - **Sets Divi meta automatically** - Configures pages for immediate Divi Builder use
  - **Divi Meta Fields Set**:
    - `_et_pb_use_builder` = `on` - Enables Divi Builder on portal pages
    - `_et_pb_page_layout` = `et_full_width_page` - Full width layout (no sidebar)
    - `_et_pb_side_nav` = `off` - Disables side navigation
    - `_et_pb_post_hide_nav` = `default` - Default navigation settings
    - `_et_pb_old_content` = `[shortcode]` - Preserves original shortcode content
  - **Methods Added**:
    - `is_divi_active()` - Detects Divi theme (parent or child)
    - `set_divi_meta($page_id, $shortcode)` - Applies Divi configuration to existing pages
  - **Retroactive Support**: Updates existing pages with Divi meta if Divi is activated later
- **Pages Created**:
  1. **Dealer Dashboard** (`/dealer-dashboard/`) - Main portal landing page
  2. **Dealer Profile** (`/dealer-profile/`) - Profile management
  3. **Dealer Login** (`/dealer-login/`) - Dedicated login page
  4. **Dealer NDA Acceptance** (`/dealer-nda-acceptance/`) - NDA form (previously manual)
- **Features**:
  - Checks for existing pages before creating (prevents duplicates)
  - Stores page IDs in `jblund_dealer_portal_pages` option
  - `get_page_id($key)` - Retrieve page ID by key
  - `get_page_url($key)` - Get permalink for any portal page
  - `pages_exist()` - Verify all pages are present
  - `recreate_missing_pages()` - Auto-repair if pages deleted
  - `delete_pages()` - Cleanup on uninstall (not deactivation)
- **Meta Tag**: All created pages tagged with `_jblund_dealer_portal_page` meta

**Divi Builder Customization Guide** - `modules/dealer-portal/DIVI-INTEGRATION.md`:

- Complete documentation for using Divi Builder with dealer portal pages
- **Customization Methods**:
  1. Edit existing shortcode pages (recommended) - Add Divi sections around shortcodes
  2. Build from scratch - Use Code Module with shortcodes
  3. Clone & customize templates - Create variations and save to Divi Library
- **Layout Examples**:
  - Dashboard with hero banner, custom widgets, and call-to-action sections
  - Profile with two-column layout and progress indicators
  - Login with split-screen design and benefit highlights
- **CSS Override Examples**: Custom styling for cards, forms, and containers
- **Responsive Design Guidelines**: Mobile-first approach with Divi's responsive tools
- **Theme Builder Integration**: Custom headers/footers for dealer-specific branding
- **Troubleshooting Section**: Common Divi + shortcode issues and solutions
- **Performance Tips**: Caching strategies and optimization recommendations

**Dealer Dashboard Template** (296 lines) - `modules/dealer-portal/templates/dealer-dashboard.php`:

- **Security**: Login check, dealer role verification, auto-redirects
- **Dashboard Cards**:
  1. **Quick Links**: My Profile, View NDA, Logout (with emoji icons)
  2. **Account Status**: Account type, NDA status (✓ Accepted), Company name
  3. **Resources**: Product Catalog, Marketing Materials, Contact Support (placeholders)
  4. **Recent Updates**: News and announcements (placeholder)
- **Shortcode**: `[jblund_dealer_dashboard]`
- **Styling**: JBLund navy blue branding (#003366), responsive grid layout, card-based UI
- **UX**: Welcome message shows company name or display name, hover effects, logout warning color

**Dealer Profile Template** (331 lines) - `modules/dealer-portal/templates/dealer-profile.php`:

- **Security**: Login check, dealer role verification, nonce verification
- **Form Sections**:
  1. **Account Information**: Username (readonly), Email, Display Name
  2. **Company Information**: Company Name, Company Phone, Territory (readonly with support note)
- **Form Processing**:
  - Updates user data via `wp_update_user()`
  - Updates user meta: `_dealer_company_name`, `_dealer_company_phone`
  - Success/error message display
  - Form data refresh after update
- **Shortcode**: `[jblund_dealer_profile]`
- **Styling**: Matching dashboard theme, form field styling, readonly fields grayed out
- **UX**: Back to Dashboard link, required field indicators, field descriptions

**Dealer Login Template** (174 lines) - `modules/dealer-portal/templates/dealer-login.php`:

- **Redirect Logic**: Auto-redirects if already logged in
- **WordPress Integration**: Uses `wp_login_form()` with custom labels
- **Features**:
  - Redirect to dashboard after login (or custom `redirect_to` param)
  - Remember Me checkbox
  - Forgot Password link
- **Shortcode**: `[jblund_dealer_login]`
- **Styling**: Clean login card UI, JBLund branding, responsive design
- **UX**: Professional appearance, clear call-to-action button

**Main Plugin Integration**:

- Loaded Page_Manager class in main plugin file
- Registered 3 new shortcodes in `jblund_dealers_init()`:
  - `[jblund_dealer_dashboard]` → `dealer-dashboard.php`
  - `[jblund_dealer_profile]` → `dealer-profile.php`
  - `[jblund_dealer_login]` → `dealer-login.php`
- **Activation Hook Enhanced**:
  - Removed manual NDA page creation (now handled by Page_Manager)
  - Added `Page_Manager::create_pages()` call
  - Creates all 4 portal pages automatically on activation
- **Total New Code**: 1,073 lines (Page_Manager: 272, Dashboard: 296, Profile: 331, Login: 174)

**User Experience Improvements**:

- **No Manual Page Setup**: All pages created automatically on plugin activation
- **Complete Portal Navigation**: Dashboard → Profile → NDA → Logout flow fully functional
- **Consistent Branding**: All pages use JBLund navy blue (#003366) theme
- **Mobile Responsive**: All templates optimized for mobile devices
- **Security First**: All pages check login status and dealer role before display

**Next Steps** (Future Development):

- Registration form to populate Registration Admin (IND-42)
- Signature Pad JS integration for NDA signing (IND-43)
- PDF generation for signed NDAs (IND-44)
- Territory management system (IND-40)
- Dashboard widgets and analytics (IND-45)

### Added - Dealer Portal Module (Phase 1: Code Extraction - Complete ✅)

- Created modular dealer portal system in `modules/dealer-portal/`
- **Email System** (adapted from login-terms-acceptance plugin):
  - `class-email-handler.php` (155 lines) - Dual email delivery system
  - Sends NDA confirmation emails to dealers with PDF attachment
  - Sends admin notification emails with PDF attachment
  - Template-based email rendering with variable extraction
  - `dealer-nda-confirmation.php` - Professional HTML email template with JBLund branding
  - `admin-nda-notification.php` - Admin notification email with green success header
- **NDA Handler System** (adapted from login-terms-acceptance plugin):
  - `class-nda-handler.php` (280 lines) - Complete NDA workflow management
  - Login redirect to NDA page for dealers who haven't accepted
  - Access restriction to portal pages until NDA is accepted
  - Form processing with nonce verification and JSON user meta storage
  - Stores acceptance data: date, IP, user agent, signature data
  - Auto-creates NDA acceptance page with shortcode
  - Shortcode registration: `[jblund_nda_acceptance]`
- **Menu Visibility System** (adapted from hide-menu-items-by-role plugin):
  - `class-menu-visibility.php` (145 lines) - Role-based menu control
  - Filter menu items based on dealer role
  - Admin UI for setting menu item visibility (Everyone/Dealers Only/Hide from Dealers)
  - Simplified single-role checking vs original multi-role system
- **Dealer Role System** (built fresh):
  - `class-dealer-role.php` (258 lines) - Complete role management
  - Custom 'dealer' role with specific capabilities
  - Capabilities: `view_dealer_portal`, `download_dealer_resources`, `view_dealer_pricing`, etc.
  - Helper methods: `is_dealer()`, `has_capability()`, `assign_to_user()`
  - Auto-creates role on plugin activation
  - Removes role on plugin uninstall
- **NDA Acceptance Page Template** (built fresh):
  - `nda-acceptance-page.php` (335 lines) - Complete frontend form
  - Professional NDA legal text with sections: Purpose, Confidential Info, Obligations, Term
  - Representative information form fields
  - Signature canvas placeholder (ready for Signature Pad JS integration)
  - Responsive design with inline CSS
  - Notice about Phase 2 signature functionality
- Module directory structure with classes/, templates/emails/, templates/, assets/css/, assets/js/
- Comprehensive module documentation in `modules/dealer-portal/README.md`, `PHASE-1-SUMMARY.md`, `QUICK-START.md`

### Changed - Dealer Portal Module Integration

- **Main Plugin Integration**:
  - Loads all 4 dealer portal classes (Dealer_Role, Email_Handler, NDA_Handler, Menu_Visibility)
  - Initializes classes on `plugins_loaded` hook
  - Safe file existence checks prevent errors if module files missing
- **Activation Hook Enhanced**:
  - Auto-creates dealer role with proper capabilities
  - Auto-creates NDA acceptance page with shortcode
  - Flushes rewrite rules for clean URLs
- **Uninstall Script Enhanced**:
  - Removes dealer role completely
  - Deletes all dealer portal user meta (`_dealer_nda_*`)
  - Deletes NDA acceptance page
  - Complete cleanup on plugin deletion
- **NDA Handler Updated**:
  - Now uses `Dealer_Role::is_dealer()` for consistent role checking
  - Integrated with Dealer_Role class methods

### Technical

- Used PHP namespaces: `JBLund\DealerPortal`
- All WordPress functions properly escaped for namespace compatibility
- Followed WordPress coding standards and security best practices
- **Total code created**: ~1028 lines (258 role + 155 email handler + 157 email templates + 280 NDA handler + 145 menu visibility + 335 NDA template + updates)
- Time saved through code reuse: ~6 hours (Phase 1 extraction)
- Time invested: ~6 hours (Phase 1 extraction + integration)
- Maintained compatibility with WordPress 5.0+ and PHP 7.0+

### Notes

- **Phase 1 Status**: ✅ Complete - All backend systems integrated and functional
- **User Testing**: ✅ Validated - Menu visibility confirmed working perfectly
- **Ready for Phase 2**: Frontend features (Signature Pad JS, PDF generation, email delivery)
- Linter errors for WordPress functions expected (functions loaded at runtime)

### Linear Project Updates

Created comprehensive issues for newly identified admin features:

- **IND-47**: Admin: Registration Submissions List & Approval Workflow (High Priority)
  - WP_List_Table for viewing all registration submissions
  - Status filtering (Pending/Approved/Rejected)
  - Approve action: Creates dealer user + sends welcome email
  - Decline action: Sends rejection email with reason
  - Integrates with IND-42 registration form
- **IND-48**: Admin: NDA Content Editor & Customization (Medium Priority)
  - TinyMCE editors for 5 NDA sections
  - Live preview modal
  - Revert to defaults functionality
  - Dynamic frontend rendering from options table
- **IND-42**: Updated registration form issue with complete admin workflow integration
- **IND-43, IND-44, IND-41**: Added progress updates and integration status comments

**Divi Builder Compatibility**: ✅ Confirmed - NDA page uses `[jblund_nda_acceptance]` shortcode compatible with all Divi modules

### Added - Dealer Portal Module (Phase 2: Admin Features - Complete ✅)

**Complete admin interface for managing dealer registrations and customizing NDA content (1,137 lines created).**

- **Registration Admin System** (`class-registration-admin.php` - 560 lines):

  - Complete `WP_List_Table` implementation for viewing all dealer registration submissions
  - Admin menu: Dealers > Registrations (requires `manage_options` capability)
  - **7 Columns**: Submission Date, Rep Name, Email (mailto link), Company, Territory, Status (color badges), Actions
  - **Sortable**: Date (default DESC), Rep Name, Company, Status
  - **Status Filter**: Dropdown to filter by All/Pending/Approved/Rejected
  - **Color-Coded Status Badges**: Yellow (Pending), Green (Approved), Red (Rejected)
  - **Approval Workflow**:
    - Creates WordPress user with generated password
    - Assigns 'dealer' role via `Dealer_Role::assign_to_user()`
    - Updates user meta with company name
    - Marks registration as approved with admin ID and timestamp
    - Sends welcome email with login credentials
    - Displays success message and redirects
  - **Decline Workflow**:
    - JavaScript prompt for admin to enter rejection reason
    - Marks registration as rejected with admin ID, timestamp, and reason
    - Sends professional rejection email to applicant
    - Displays success message and redirects
  - **Email Integration**:
    - `send_approval_email()` - Welcome email with credentials and login link
    - `send_rejection_email()` - Professional rejection with admin's reason
    - Uses `Email_Handler::get_mail_message()` for template rendering
  - **Meta Fields Tracked** (13 fields):
    - Submission: `_registration_rep_name`, `_registration_email`, `_registration_company`, `_registration_phone`, `_registration_territory`, `_registration_notes`
    - Technical: `_registration_ip`, `_registration_date`, `_registration_user_agent`, `_registration_status`
    - Approval: `_registration_approved_by`, `_registration_approved_date`, `_registration_user_id`
    - Rejection: `_registration_rejected_by`, `_registration_rejected_date`, `_registration_rejection_reason`
  - **Security**: Nonce verification on all actions, capability checks, input sanitization
  - **Dependencies**: Requires `dealer_registration` CPT (to be built in IND-42)

- **Registration Email Templates** (162 lines total):

  - `registration-approval.php` (82 lines) - Welcome email with login credentials:
    - JBLund navy blue header (#003366)
    - Credentials display box with username and temporary password
    - Security reminder to change password
    - 4-step onboarding checklist
    - Login CTA button
    - Support information
    - Responsive HTML matching existing email styling
  - `registration-rejection.php` (80 lines) - Professional rejection notification:
    - Gray header for neutral tone
    - Admin's rejection reason displayed in highlighted box
    - Professional, empathetic messaging
    - Invitation to contact support or reapply
    - Next steps guidance
    - Responsive HTML template

- **NDA Content Editor** (`class-nda-editor.php` - 415 lines):

  - Admin settings page: Dealers > NDA Editor (requires `manage_options` capability)
  - **6 TinyMCE Editors** for customizing NDA sections:
    - Introduction - Opening statement identifying parties
    - 1. Purpose - Purpose of the agreement
    - 2. Confidential Information - Definition and examples
    - 3. Obligations - Receiving party obligations
    - 4. Term - Duration of the agreement
    - 5. Return of Materials - Requirements for returning materials
  - **Live Preview Modal**:
    - JavaScript-powered AJAX preview
    - Shows complete NDA with current editor content
    - Modal overlay with close button
    - No need to save to preview changes
  - **Revert to Defaults**:
    - Separate form with confirmation prompt
    - Deletes all custom content from options table
    - Restores original NDA text
    - Prevents accidental data loss
  - **Content Storage**:
    - Saved to `jblund_nda_custom_content` option (array)
    - HTML sanitization with `wp_kses()` and allowed tags (p, strong, em, ul, ol, li, br, h3, h4)
    - Falls back to defaults if no custom content exists
  - **Static Methods for Template Access**:
    - `get_default_content()` - Returns array of default NDA text
    - `get_content()` - Returns custom content merged with defaults
  - **Inline CSS & JavaScript**:
    - Professional admin interface styling
    - Preview modal styles
    - TinyMCE editor initialization
    - AJAX handlers for preview and revert
  - **AJAX Preview Handler**:
    - Endpoint: `wp_ajax_preview_nda_content`
    - Nonce verification and capability checks
    - Renders NDA preview HTML with sanitized content

- **NDA Template Updates** (nda-acceptance-page.php modifications):

  - Now reads content from `NDA_Editor::get_content()` instead of hardcoded text
  - Falls back to inline defaults if `NDA_Editor` class not loaded
  - Uses `wp_kses()` to safely output custom HTML content
  - Maintains all original structure and styling
  - Fully backwards compatible (works with or without custom content)

- **Main Plugin Integration**:
  - Loads `class-registration-admin.php` and `class-nda-editor.php`
  - Initializes both classes on `plugins_loaded` hook
  - Safe file existence checks
  - Total dealer portal classes now loaded: 6 (Dealer_Role, Email_Handler, NDA_Handler, Menu_Visibility, Registration_Admin, NDA_Editor)

### Technical - Phase 2

- **Total code created (Phase 2)**: 1,137 lines
  - Registration Admin class: 560 lines
  - Email templates: 162 lines
  - NDA Editor class: 415 lines
  - Main plugin integration: minimal updates
- **Phase 1 + Phase 2 Total**: 2,165 lines
- **Technologies Used**:
  - `WP_List_Table` for admin submissions interface
  - TinyMCE editors with `wp_editor()` for content customization
  - AJAX for live preview functionality
  - WordPress Options API for NDA content storage
  - WordPress Email API (`wp_mail()`) for notifications
- Time invested: ~6 hours (admin features build)
- Maintained compatibility with WordPress 5.0+ and PHP 7.0+

### Linear Issues Status

- **IND-47** (Registration Admin): ✅ Complete - 560 lines + 162 lines email templates
- **IND-48** (NDA Editor): ✅ Complete - 415 lines + NDA template updates
- **IND-42** (Registration Form): ⏳ Pending - Backend admin ready, form build needed
- **IND-43** (NDA Acceptance): ✅ Phase 1 Complete - Signature Pad JS integration pending (Phase 3)
- **IND-44** (Email System): ✅ Complete - All email handlers and templates built

### Notes

### Added

- Initial release of JBLund Dealers WordPress plugin
- Custom post type "Dealer" for managing dealer information
- Admin interface with meta boxes for dealer data entry
- Support for the following dealer fields:
  - Company Name
  - Company Address
  - Company Phone
  - Website URL
  - Services: Docks (boolean)
  - Services: Lifts (boolean)
  - Services: Trailers (boolean)
- Sub-locations functionality with unlimited locations per dealer
- Each sub-location supports:
  - Location Name
  - Address
  - Phone
  - Website (optional)
  - Services: Docks, Lifts, Trailers
- Dynamic add/remove functionality for sub-locations in admin
- Frontend shortcode `[jblund_dealers]` for displaying dealers
- Shortcode parameters for customization (posts_per_page, orderby, order)
- Responsive card-based grid layout for frontend display
- Professional CSS styling with:
  - Blue header cards
  - Hover effects
  - Mobile responsiveness
  - Print-friendly styles
- Security features:
  - Nonce verification
  - Data sanitization
  - Capability checks
  - Output escaping
- WordPress best practices implementation
- Internationalization support (text domain: jblund-dealers)
- Comprehensive documentation:
  - README.md with installation and usage
  - SAMPLE_DATA.md with example dealer data
  - USAGE_GUIDE.md with detailed instructions

### Security

- Implemented nonce verification for all form submissions
- Added proper data sanitization for all input fields
- Added capability checks to ensure only authorized users can manage dealers
- Escaped all output to prevent XSS vulnerabilities

### Technical Details

- Plugin version: 1.0.0
- WordPress compatibility: 5.0+
- PHP compatibility: 7.0+
- No external dependencies required
- Clean activation and deactivation hooks
- Proper rewrite rules flushing on activation/deactivation

---

## Future Enhancements (Planned)

### Version 1.1.0 (Future)

- [ ] Search and filter functionality on frontend
- [ ] Map integration for dealer locations
- [ ] Import/Export dealer data (CSV)
- [ ] Email contact form per dealer
- [ ] Dealer logo/image support
- [ ] Advanced shortcode filters (by service type, location)
- [ ] REST API endpoints for dealer data
- [ ] Gutenberg block for dealer display

### Version 1.2.0 (Future)

- [ ] Multi-language support (WPML/Polylang compatibility)
- [ ] Custom fields API for extending dealer data
- [ ] Analytics tracking for dealer views/clicks
- [ ] Social media links per dealer
- [ ] Business hours field
- [ ] Google Maps integration
- [ ] Distance calculator from user location

---

## Notes

This changelog follows the [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) format,
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
