# Dealer Shortcode Layouts - Modular Structure

## Overview

The dealer shortcode system has been refactored into individual, focused files for easier editing and maintenance. Each layout type now has its own dedicated class file.

## File Structure

```
modules/frontend/shortcodes/
├── class-shortcode-manager.php    # Main coordinator
├── class-layout-base.php          # Shared base functionality
├── class-grid-layout.php          # Grid layout renderer
├── class-list-layout.php          # List layout renderer
├── class-compact-layout.php       # Compact layout renderer
└── README.md                      # This file
```

## Editing Individual Layouts

### Grid Layout (`class-grid-layout.php`)

**Shortcode:** `[jblund_dealers]` or `[jblund_dealers layout="grid"]`

Edit this file to customize:

- Vertical card layout
- Auto-fill responsive grid
- Card header and body structure
- Contact info display
- Website button
- Services section
- Sublocations in grid format

**Key Methods:**

- `render()` - Main rendering loop
- `render_contact_info()` - Address and phone display
- `render_website_button()` - Website button display
- `render_services()` - Services section (icons or list)
- `render_sublocations()` - Additional locations display

### List Layout (`class-list-layout.php`)

**Shortcode:** `[jblund_dealers layout="list"]`

Edit this file to customize:

- Horizontal row layout
- 4-column structure (name | contact | website | services)
- Inline contact display
- Service icons in row format
- Sublocations in list format

**Key Methods:**

- `render()` - Main rendering loop with 4 columns
- `render_contact_info()` - Inline contact items
- `render_website_button()` - Website button in column 3
- `render_sublocations()` - Additional locations in list format

### Compact Layout (`class-compact-layout.php`)

**Shortcode:** `[jblund_dealers layout="compact"]`

Edit this file to customize:

- Smaller card size
- Tighter spacing
- Same structure as grid but more compact
- Reduced padding and margins

**Key Methods:**

- Same as Grid Layout but with compact class wrapper

## Shared Functionality (`class-layout-base.php`)

This abstract base class provides shared methods that all layouts use:

### Methods Available to All Layouts:

**`get_dealer_data($post_id)`**

- Retrieves all dealer meta data for a post
- Returns array with company_name, address, phone, website, services, sublocations, etc.

**`generate_map_link($address, $latitude, $longitude, $custom_map_link)`**

- Generates Google Maps links
- Priority: Custom link > Coordinates > Address search

**`render_service_icons($docks, $lifts, $trailers)`**

- Renders service icons (🚢 🚛 ⚓)
- Shows active/inactive states
- Can be called from any layout

**`render_service_list($docks, $lifts, $trailers)`**

- Renders traditional bullet list of services
- Fallback when icons are disabled
- Can be called from any layout

**`$this->use_icons`**

- Property available in all layouts
- Determines whether to show icons or list
- Based on plugin settings

## How It Works

1. **`class-shortcode.php`** (loader) - Loaded by main plugin, initializes everything
2. **`class-shortcode-manager.php`** - Registers `[jblund_dealers]` shortcode
3. **Manager routes to layout** - Based on `layout` attribute
4. **Layout class renders** - Calls its own `render()` method
5. **Shared methods available** - From `class-layout-base.php`

## Adding a New Layout

1. Create new file: `class-your-layout.php`
2. Extend `Layout_Base`
3. Implement `render($dealers, $atts)` method
4. Register in `class-shortcode-manager.php`:
   ```php
   $this->renderers['your-layout'] = new Your_Layout();
   ```
5. Add CSS class: `.jblund-dealers-your-layout { ... }`

## Common Editing Tasks

### Change Contact Info Display

**Edit:** Individual layout file (`class-grid-layout.php`, etc.)
**Method:** `render_contact_info()` or inline in `render()`

### Change Website Button Text/Style

**Edit:** Individual layout file
**Method:** `render_website_button()`

### Modify Services Display

**Edit:** Individual layout file
**Method:** `render_services()` or use shared `render_service_icons()` / `render_service_list()`

### Change Sublocation Structure

**Edit:** Individual layout file
**Method:** `render_sublocations()`

### Add New Shared Function

**Edit:** `class-layout-base.php`
**Add:** Protected method, then call from any layout using `$this->method_name()`

## Benefits of Modular Structure

✅ **Easier to Edit** - Each layout is in its own file  
✅ **Less Intimidating** - Files are 100-200 lines instead of 400+  
✅ **Shared Code** - Common functionality in base class (DRY)  
✅ **Add New Layouts** - Simply extend base class  
✅ **Independent Changes** - Edit one layout without affecting others  
✅ **Better Organization** - Clear separation of concerns

## CSS Structure (Unchanged)

CSS for all layouts is still in:

- `assets/scss/_layout-grid.scss`
- `assets/scss/_layout-list.scss`
- `assets/scss/_layout-compact.scss`

Compiled to: `assets/css/dealers.css`

The modular PHP structure **works with existing CSS** - no CSS changes needed!

## Troubleshooting

**Problem:** Layout not displaying  
**Check:** `class-shortcode-manager.php` - Is your layout registered?

**Problem:** Method not found  
**Check:** Is it in `class-layout-base.php` or the specific layout file?

**Problem:** Shortcode not working  
**Check:** `class-shortcode.php` - Are all files being loaded?

## Questions?

This structure follows the same modular pattern as the rest of the plugin:

- `/modules/admin/` - Admin functionality
- `/modules/core/` - Post types and meta
- `/modules/frontend/shortcodes/` - Now modular too!

Each piece is focused, documented, and easier to understand. 🎉
