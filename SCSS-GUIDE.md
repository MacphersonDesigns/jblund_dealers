# SCSS Quick Reference

## File Structure

```
assets/scss/
├── dealers.scss           → Main entry (public dealer directory)
├── dashboard.scss         → Dashboard entry (dealer portal)
├── _variables.scss        → Colors, spacing, breakpoints (SHARED)
├── _common.scss          → Shared card/button/service styles
├── _layout-grid.scss     → Grid layout (default)
├── _layout-list.scss     → Horizontal list layout
├── _layout-compact.scss  → Compact grid layout
└── _dashboard-styles.scss → Dealer portal dashboard styles
```

**Compiled Output:**

- `dealers.scss` → `assets/css/dealers.css` (Public dealer directory)
- `dashboard.scss` → `modules/dealer-portal/assets/css/dashboard.css` (Dealer portal)

**What's Included:**

- **dealers.css**: Public dealer directory (all 3 layouts: grid, list, compact)
- **dashboard.css**: All dealer portal pages (dashboard, login, profile, NDA)

## Development Commands

```bash
# Watch for changes (auto-compile on save)
npm run watch

# One-time compile (development mode - expanded CSS)
npm run dev

# Production build (compressed CSS)
npm run build
```

## Quick Edit Guide

### Change Colors/Spacing

→ Edit `_variables.scss`

### Edit Card Styles, Buttons, Services, Sub-locations

→ Edit `_common.scss`

### Modify Grid Layout

→ Edit `_layout-grid.scss`

### Modify List (Horizontal) Layout

→ Edit `_layout-list.scss`

### Modify Compact Grid Layout

→ Edit `_layout-compact.scss`

### Edit Dashboard/Portal Styles

→ Edit `_dashboard-styles.scss`

**Includes:**

- Main dashboard page styling
- Login form styling
- Profile edit form styling
- NDA acceptance page styling
- All portal-specific components

## Important Notes

✅ **DO** edit SCSS files in `assets/scss/`

❌ **DON'T** edit compiled CSS files:

- `assets/css/dealers.css` (auto-generated)
- `modules/dealer-portal/assets/css/dashboard.css` (auto-generated)

After editing SCSS:

1. The watcher auto-compiles (if running `npm run watch`)
2. OR manually run `npm run build`
3. Hard refresh browser (Cmd+Shift+R)

## CSS Custom Properties (WordPress Settings)

These variables are defined in SCSS but **overridden by WordPress** at runtime:

- `--jblund-header-color`
- `--jblund-card-background`
- `--jblund-button-color`
- `--jblund-link-color`
- `--jblund-icon-color`
- And more...

You'll see defaults in `_variables.scss`, but WordPress injects the actual values from **Dealers > Settings > Appearance**.
