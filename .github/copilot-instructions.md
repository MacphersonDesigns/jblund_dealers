# JBLund Dealers WordPress Plugin - AI Coding Guide

## Architecture Overview

This is a single-file WordPress plugin (`jblund-dealers.php`) implementing a complete dealer management system. The plugin follows WordPress best practices with a single class `JBLund_Dealers_Plugin` containing all functionality.

**Core Components:**

- **Custom Post Type**: `dealer` with custom meta boxes for structured data
- **Meta Data**: Main dealer info + dynamic sub-locations array
- **Frontend Display**: Shortcode-based responsive grid layout
- **Admin Interface**: Native WordPress admin with JavaScript-enhanced sub-location management

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

- **`jblund-dealers.php`**: Single-file architecture - all PHP logic here
- **`assets/css/dealers.css`**: Complete frontend styling (grid, cards, responsive)
- **`uninstall.php`**: Complete data cleanup on plugin deletion
- **Documentation**: README.md, QUICK_REFERENCE.md, USAGE_GUIDE.md

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

Plugin includes admin settings page under Dealers > Settings with:

- **Appearance**: Card colors, button colors (color pickers)
- **Shortcode**: Default layout, service icon toggle
- Settings stored in `jblund_dealers_settings` option

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

## Testing & Debugging

- Enable `WP_DEBUG` for WordPress-specific issues
- Check browser console for JavaScript errors (sub-location management)
- Use WordPress query debugging for shortcode issues
- Verify nonce and capability checks are working
