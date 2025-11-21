# JBLund Dealers - SCSS Development

## Structure

The CSS has been reorganized into modular SCSS files:

```
assets/
├── scss/
│   ├── dealers.scss          # Main entry point
│   ├── _variables.scss        # CSS custom properties & SCSS variables
│   ├── _common.scss          # Shared styles (cards, buttons, services)
│   ├── _layout-grid.scss     # Grid layout specific styles
│   ├── _layout-list.scss     # List layout specific styles
│   └── _layout-compact.scss  # Compact layout specific styles
└── css/
    └── dealers.css           # Compiled output (auto-generated)
```

## Development Workflow

### Install Dependencies

```bash
npm install
```

### Development Mode (Watch for changes)

```bash
npm run watch
```

This will watch for changes in `assets/scss/` and automatically compile to `assets/css/dealers.css` with source maps for debugging.

### One-time Build

```bash
npm run dev      # Expanded CSS with source maps
npm run build    # Compressed CSS for production
```

## Editing Guidelines

### When to edit which file:

- **`_variables.scss`**: Change colors, spacing, breakpoints, transitions
- **`_common.scss`**: Edit shared card styles, buttons, services, sub-locations
- **`_layout-grid.scss`**: Modify grid-specific layout and responsive behavior
- **`_layout-list.scss`**: Modify horizontal list layout and columns
- **`_layout-compact.scss`**: Modify compact grid layout and sizing

### Never edit `dealers.css` directly!

The `assets/css/dealers.css` file is auto-generated. All edits should be made in the SCSS files.

## CSS Custom Properties

The plugin uses CSS custom properties (variables) that can be overridden by WordPress settings:

- `--jblund-header-color`
- `--jblund-card-background`
- `--jblund-button-color`
- `--jblund-link-color`
- And more...

These are defined in `_variables.scss` with defaults, but the WordPress settings will override them at runtime.

## Adding New Styles

1. Determine which module the style belongs to
2. Edit the appropriate SCSS file
3. The watcher will auto-compile (if running `npm run watch`)
4. Refresh your browser to see changes

## Production Deployment

Before committing or deploying, run:

```bash
npm run build
```

This creates a compressed, production-ready CSS file without source maps.
