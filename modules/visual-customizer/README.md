# Visual Customizer Module

Live visual editor for JBLund Dealers plugin with real-time preview and dual editing modes.

## Features

### 🎨 Visual Mode

- **28 Customizable Settings** organized into 5 categories:
  - **Colors** (10 settings): Header, background, button, text, borders, icons, links, hover
  - **Typography** (4 settings): Font sizes, weights, line height
  - **Spacing** (6 settings): Padding, margins, grid gap, borders
  - **Effects** (4 settings): Shadows, hover effects, transitions, icon size
  - **Custom CSS** (1 setting): Free-form CSS editor

### 💻 CSS Mode

- Direct CSS editing with syntax highlighting
- Real-time preview of custom CSS
- Copy CSS to clipboard functionality
- Combines with visual settings

### ⚡ Real-Time Preview

- **Live Updates**: See changes instantly without page refresh
- **Sample Data**: Preview with realistic dealer cards
- **Responsive Toggle**: Test desktop, tablet, and mobile views
- **Device Simulation**: Accurate viewport sizing

## Usage

### Accessing the Customizer

1. Navigate to **Dealers → Visual Customizer** in WordPress admin
2. Choose between **Visual** or **CSS** mode
3. Make changes and see them update live
4. Click **Save Changes** to apply

### Visual Mode Controls

#### Color Pickers

- Click any color swatch to open WordPress color picker
- Choose from preset colors or enter hex values
- Clear button to reset to default

#### Range Sliders

- Drag slider or click track to adjust values
- Real-time value display with units
- Instant preview updates

#### Select Dropdowns

- Pre-defined options for consistency
- Border styles, shadow levels, hover effects
- Font weights and other style choices

### CSS Mode

Switch to CSS mode to:

- Write custom CSS rules directly
- Override visual settings with precision
- Copy generated CSS for external use
- Combine with visual controls

### Device Preview

Toggle between device sizes:

- **Desktop**: Full width (default)
- **Tablet**: 768px viewport
- **Mobile**: 375px viewport

Perfect for testing responsive designs!

## Technical Details

### File Structure

```
modules/visual-customizer/
├── classes/
│   └── class-visual-customizer.php    # Main class
├── assets/
│   ├── css/
│   │   └── customizer.css             # Interface styles
│   └── js/
│       └── customizer.js              # Real-time updates
└── README.md                          # This file
```

### Integration

The module loads automatically via the main plugin file:

```php
// Load visual customizer module
if (file_exists(JBLUND_DEALERS_PLUGIN_DIR . 'modules/visual-customizer/classes/class-visual-customizer.php')) {
    require_once JBLUND_DEALERS_PLUGIN_DIR . 'modules/visual-customizer/classes/class-visual-customizer.php';
}

// Initialize visual customizer module (if loaded)
if (class_exists('JBLund\VisualCustomizer\Visual_Customizer')) {
    new JBLund\VisualCustomizer\Visual_Customizer();
}
```

### Settings Storage

All settings are stored in the `jblund_dealers_settings` WordPress option:

```php
$settings = get_option('jblund_dealers_settings', array());
```

Settings are shared with the main plugin's appearance customization system.

### AJAX Endpoints

Three AJAX actions handle real-time operations:

1. **save_customizer_settings**: Save all settings to database
2. **reset_customizer_settings**: Reset to default values
3. **get_preview_html**: Regenerate preview content (future use)

### Security

- **Nonce Verification**: All AJAX requests use `jblund_customizer_nonce`
- **Capability Check**: Only `manage_options` users can access
- **Sanitization**: All input sanitized before saving
- **Escaping**: All output escaped for security

### CSS Generation

Settings dynamically generate CSS with `!important` rules to ensure they override theme styles:

```css
.dealer-card {
	background: #ffffff !important;
	padding: 20px !important;
}
```

## Customization Examples

### Example 1: Dark Theme

**Visual Mode Settings:**

- Header Color: `#1a1a1a`
- Card Background: `#2c2c2c`
- Text Color: `#ffffff`
- Secondary Text: `#cccccc`
- Button Color: `#0073aa`

### Example 2: Minimal Design

**Visual Mode Settings:**

- Border Radius: `0px`
- Border Style: `none`
- Box Shadow: `none`
- Hover Effect: `none`
- Card Padding: `15px`

### Example 3: Bold & Colorful

**Visual Mode Settings:**

- Header Color: `#ff6b6b`
- Button Color: `#4ecdc4`
- Border Radius: `15px`
- Box Shadow: `heavy`
- Hover Effect: `scale`

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Modern browsers with CSS Grid support

## Performance

- **Instant Updates**: JavaScript-based real-time preview
- **No Page Reload**: Changes apply without refresh
- **Optimized CSS**: Minimal CSS output
- **Efficient AJAX**: Only saves on demand

## Troubleshooting

### Changes Not Saving

1. Check browser console for JavaScript errors
2. Verify you have `manage_options` capability
3. Check server error logs for PHP errors
4. Clear browser cache and try again

### Preview Not Updating

1. Check browser console for errors
2. Ensure jQuery and WordPress color picker are loaded
3. Try refreshing the page
4. Check that JavaScript is enabled

### Styles Not Applying on Frontend

1. Click **Save Changes** in customizer
2. Clear site cache (if using caching plugin)
3. Check that frontend CSS file is loading
4. Verify `!important` rules in browser DevTools

## Version History

### 1.2.0 (Current)

- Initial release of Visual Customizer module
- Visual mode with 28 settings
- CSS mode with syntax highlighting
- Real-time preview functionality
- Responsive device toggle
- Copy CSS feature
- Reset to defaults

## Future Enhancements

Planned features for future versions:

- **Preset Library**: Save and load style presets
- **Import/Export**: Share customizations between sites
- **Live Frontend Preview**: Preview on actual site pages
- **Undo/Redo**: Step through changes
- **Color Schemes**: Quick theme selection
- **Font Library**: Google Fonts integration
- **Animation Builder**: Custom hover animations
- **CSS Variables**: Better theme integration

## Support

For issues or questions:

1. Check this documentation
2. Review browser console for errors
3. Check WordPress debug log
4. Contact plugin support

## License

This module is part of the JBLund Dealers plugin and uses the same GPL v2 or later license.
