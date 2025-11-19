# JBLund Dealers WordPress Plugin

A comprehensive WordPress plugin for managing and displaying dealer information for JBLund Dock's B2B Website. Features include dealer management, multiple locations per dealer, service tracking, and an optional dealer portal module with NDA acceptance, role management, and menu visibility controls.

## Features

### Core Dealer Management

- **Custom Post Type** for managing dealers with dedicated admin interface
- **Comprehensive Dealer Fields**:
  - Company Name (synced with post title)
  - Company Address (multi-line)
  - Company Phone
  - Website URL
  - Services: Docks, Lifts, Trailers (checkboxes)
- **Unlimited Sub-Locations** per dealer with:
  - Location Name
  - Address
  - Phone
  - Website (optional)
  - Independent service offerings
- **Three Layout Options**:
  - Grid (default) - Responsive multi-column
  - List - Horizontal rows
  - Compact - Tighter spacing
- **Advanced Customization** - 28+ appearance settings for colors, typography, spacing, and effects
- **Responsive Design** - Mobile-friendly layouts
- **Shortcode Integration** - Easy embedding on any page/post

### Dealer Portal Module (Optional)

- **Custom Dealer Role** with restricted capabilities
- **NDA Acceptance System** with signature capture
- **Menu Visibility Controls** - Show/hide menu items by role
- **Email Notifications** - Dual delivery to dealers and admins
- **PDF Generation** - Signed NDA documents (Phase 2)
- **Access Restrictions** - Portal locked until NDA accepted

## Quick Start

### Installation

1. Upload the `jblund_dealers` folder to `/wp-content/plugins/`
2. Activate through **Plugins** menu in WordPress
3. New "Dealers" menu appears in admin sidebar

### Add Your First Dealer (2 minutes)

1. Navigate to **Dealers > Add New**
2. Fill in company details in the **Dealer Information** meta box
3. Check applicable service boxes
4. (Optional) Click **Add Sub-Location** for additional locations
5. Click **Publish**

### Display Dealers (30 seconds)

Add this shortcode to any page or post:

```
[jblund_dealers]
```

**Done!** Your dealers will display in a responsive grid.

## Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.0 or higher
- **No external dependencies** for core features
- **Optional**: TCPDF for dealer portal PDF generation

## Shortcode Options

### Basic Usage

```
[jblund_dealers]
```

### With Parameters

```
[jblund_dealers posts_per_page="10" orderby="title" order="ASC" layout="grid"]
```

**Available Parameters:**

| Parameter        | Options             | Default | Description                          |
| ---------------- | ------------------- | ------- | ------------------------------------ |
| `posts_per_page` | Number or -1        | -1      | Number of dealers to show (-1 = all) |
| `orderby`        | title, date         | title   | Sort field                           |
| `order`          | ASC, DESC           | ASC     | Sort direction                       |
| `layout`         | grid, list, compact | grid    | Display layout                       |

### Layout Examples

**Grid Layout (Default)**

```
[jblund_dealers layout="grid"]
```

Multi-column responsive grid with card design

**List Layout**

```
[jblund_dealers layout="list"]
```

Horizontal rows, perfect for directory-style pages

**Compact Layout**

```
[jblund_dealers layout="compact"]
```

Tighter spacing for smaller sections

## Customization

### Appearance Settings

Navigate to **Dealers > Settings > Appearance** to customize:

- **Colors** (10 options): Headers, cards, buttons, text, borders, links, icons
- **Typography** (4 options): Font sizes, weights, line height
- **Spacing** (6 options): Padding, margins, gaps, borders
- **Effects** (4 options): Shadows, hover effects, transitions, icon size
- **Custom CSS** - Add your own styles

All settings preserve base CSS by default and only override when changed.

### CSS Classes for Advanced Styling

Target these classes in your theme's CSS:

```css
.jblund-dealers-container    /* Main wrapper */
/* Main wrapper */
.jblund-dealers-grid         /* Grid layout */
.jblund-dealers-list         /* List layout */
.jblund-dealers-compact      /* Compact layout */
.dealer-card                 /* Individual dealer card */
.dealer-card-header          /* Card header */
.dealer-card-body            /* Card content */
.dealer-sublocations; /* Sub-locations section */
```

## Installation Verification

After activation, verify:

- [ ] "Dealers" menu appears in WordPress admin
- [ ] Menu shows store/shop icon
- [ ] **Dealers > Add New** page loads without errors
- [ ] Meta boxes appear: "Dealer Information" and "Sub-Locations"
- [ ] Shortcode `[jblund_dealers]` displays on frontend
- [ ] CSS loads correctly (check browser dev tools)
- [ ] Responsive design works on mobile

### Common Issues

**Dealers not displaying?**

- Ensure dealers are published (not draft)
- Check shortcode spelling
- Verify no JavaScript errors in console

**Styles not loading?**

- Clear browser cache
- Verify `assets/css/dealers.css` exists
- Check file permissions (644 recommended)

**Sub-locations not saving?**

- Enable JavaScript in browser
- Check browser console for errors
- Try different browser

**404 errors?**

- Go to **Settings > Permalinks**
- Click "Save Changes" (flushes rewrite rules)

## File Structure

```
jblund_dealers/
├── jblund-dealers.php              # Main plugin file
├── uninstall.php                   # Data cleanup on deletion
├── CHANGELOG.md                    # Version history
├── USAGE_GUIDE.md                  # Detailed usage documentation
├── assets/
│   └── css/
│       └── dealers.css             # Frontend styles
└── modules/
    └── dealer-portal/              # Optional dealer portal module
        ├── classes/
        │   ├── class-dealer-role.php
        │   ├── class-email-handler.php
        │   ├── class-nda-handler.php
        │   └── class-menu-visibility.php
        ├── templates/
        │   ├── dealer-dashboard.php
        │   ├── dealer-login.php
        │   ├── dealer-profile.php
        │   ├── nda-acceptance-page.php
        │   └── emails/
        ├── assets/
        ├── README.md                # Dealer portal documentation
        └── DIVI-INTEGRATION.md      # Divi Builder guide
```

## Documentation

- **USAGE_GUIDE.md** - Comprehensive usage instructions and examples
- **CHANGELOG.md** - Version history and update notes
- **modules/dealer-portal/README.md** - Dealer portal module documentation
- **modules/dealer-portal/DIVI-INTEGRATION.md** - Divi Builder customization guide

## Security Features

- **Nonce verification** on all form submissions
- **Data sanitization** for all input fields
- **Output escaping** for XSS protection
- **Capability checks** - Only editors/admins can manage dealers
- **Role-based access** for dealer portal
- **No direct file access** - All files protected

## Data Storage

Dealers are stored as custom posts with meta fields:

- `_dealer_company_name` - Text
- `_dealer_company_address` - Textarea
- `_dealer_company_phone` - Text
- `_dealer_website` - URL
- `_dealer_docks` - Boolean (1/0)
- `_dealer_lifts` - Boolean (1/0)
- `_dealer_trailers` - Boolean (1/0)
- `_dealer_sublocations` - Serialized array

### Complete Data Removal

Uninstalling the plugin removes ALL data:

- All dealer posts
- All dealer meta data
- Plugin settings
- Dealer portal pages and user meta

## Development

Built with WordPress best practices:

- **Clean Architecture** - Single-class main plugin, modular portal
- **Security First** - Sanitization, escaping, nonces, capability checks
- **Internationalization** - Text domain: `jblund-dealers`
- **Performance** - Efficient queries, minimal database calls
- **Extensibility** - Action hooks and filters for customization
- **Documentation** - Comprehensive inline comments

## Version

**Current Version**: 1.0.0  
**Last Updated**: November 5, 2025

## License

GPL v2 or later

## Author

Macpherson Designs

## Support

For issues, questions, or contributions:

- GitHub Repository: MacphersonDesigns/jblund_dealers
- Review USAGE_GUIDE.md for detailed instructions
- Check CHANGELOG.md for update information
