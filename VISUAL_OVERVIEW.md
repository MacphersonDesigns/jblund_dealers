# Visual Overview

## Plugin Icon
The plugin uses the WordPress "dashicons-store" icon in the admin menu, representing a storefront/dealer location.

## Admin Interface

### Dealers Menu
When you activate the plugin, you'll see a new menu item in the WordPress admin:
- Menu Icon: Store/Shop icon
- Menu Position: Below "Posts" menu
- Submenu items:
  - All Dealers
  - Add New
  - (Standard WordPress post type menu items)

### Add/Edit Dealer Screen

#### Screen Layout
The edit screen contains:
1. **Title field** (at top): Not used for display, but required by WordPress
2. **Dealer Information meta box**: Main dealer details
3. **Sub-Locations meta box**: Additional location management
4. **Publish box** (sidebar): Standard WordPress publish controls

#### Dealer Information Meta Box
```
┌─ Dealer Information ────────────────────────────────────────┐
│                                                              │
│ Company Name:      [________________________]               │
│                                                              │
│ Company Address:   [________________________]               │
│                    [________________________]               │
│                    [________________________]               │
│                                                              │
│ Company Phone:     [________________________]               │
│                                                              │
│ Website:           [________________________]               │
│                                                              │
│ Services Offered:  [ ] Docks                                │
│                    [ ] Lifts                                │
│                    [ ] Trailers                             │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

#### Sub-Locations Meta Box
```
┌─ Sub-Locations ──────────────────────────────────────────────┐
│                                                              │
│ ┌─ Sub-Location ─────────────────────────────[ Remove ]──┐  │
│ │                                                         │  │
│ │ Location Name:    [________________________]           │  │
│ │ Address:          [________________________]           │  │
│ │                   [________________________]           │  │
│ │ Phone:            [________________________]           │  │
│ │ Website:          [________________________]           │  │
│ │ Services Offered: [ ] Docks                            │  │
│ │                   [ ] Lifts                            │  │
│ │                   [ ] Trailers                         │  │
│ └─────────────────────────────────────────────────────────┘  │
│                                                              │
│ [Add Sub-Location]                                           │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

## Frontend Display

### Grid Layout (Desktop)
```
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ Dealer Card  │  │ Dealer Card  │  │ Dealer Card  │
│              │  │              │  │              │
└──────────────┘  └──────────────┘  └──────────────┘

┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ Dealer Card  │  │ Dealer Card  │  │ Dealer Card  │
│              │  │              │  │              │
└──────────────┘  └──────────────┘  └──────────────┘
```

### Individual Dealer Card
```
┌────────────────────────────────────────────────────┐
│ ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ │ ← Blue Header
│ ░░  Waterfront Marine                      ░░░░░░ │
│ ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ │
├────────────────────────────────────────────────────┤
│                                                    │
│ Address: 123 Harbor Drive                         │
│          Marina Bay, CA 94577                     │
│                                                    │
│ Phone: (555) 123-4567                             │
│                                                    │
│ Website: https://waterfrontmarine.example.com     │
│                                                    │
│ Services:                                          │
│ • Docks                                           │
│ • Lifts                                           │
│ • Trailers                                        │
│                                                    │
│ ═══════════════════════════════════════════════    │
│                                                    │
│ Additional Locations:                              │
│                                                    │
│ │ North Shore Branch                              │
│ │ 456 Lakeview Road                               │
│ │ North Shore, CA 94580                           │
│ │ Phone: (555) 234-5678                           │
│ │ Services: Docks, Lifts                          │
│                                                    │
└────────────────────────────────────────────────────┘
```

### Mobile Layout
On mobile devices (< 768px), cards stack vertically in a single column for optimal readability.

## Color Scheme

### Default Colors
- **Header Background**: #0073aa (WordPress admin blue)
- **Header Text**: #ffffff (white)
- **Card Background**: #ffffff (white)
- **Card Border**: #e0e0e0 (light gray)
- **Text**: #333333 (dark gray)
- **Links**: #0073aa (WordPress blue)
- **Sub-location Background**: #f9f9f9 (very light gray)
- **Sub-location Border**: #0073aa (left border accent)

### Interactive States
- **Card Hover**: Slightly elevated shadow and 2px upward movement
- **Link Hover**: Underline appears
- **Button Hover**: Standard WordPress button hover state

## Typography

- **Card Title**: 1.4em, bold
- **Section Headings**: 1.1em, bold
- **Body Text**: Default (typically 16px)
- **Sub-location Title**: 1em, semi-bold
- **Meta Information**: 0.95em

## Accessibility Features

- Semantic HTML structure
- Proper heading hierarchy (h3, h4, h5)
- Color contrast meets WCAG AA standards
- Keyboard navigable
- Screen reader friendly labels
- Focus states on interactive elements

## Browser Support

The plugin's frontend is designed to work with:
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Internet Explorer 11 (with graceful degradation)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Responsive Breakpoints

- **Mobile**: 0-768px (single column)
- **Tablet**: 769-1024px (2-3 columns)
- **Desktop**: 1025px+ (auto-fill grid, min 300px per card)
