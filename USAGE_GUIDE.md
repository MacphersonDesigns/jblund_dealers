# JBLund Dealers Plugin - Usage Guide

## Installation Steps

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the WordPress admin panel
3. Look for "Dealers" in the admin menu

## Admin Interface

### Adding a New Dealer

1. Navigate to **Dealers > Add New**
2. You'll see two meta boxes:

#### Dealer Information Meta Box
Fill in the following fields:
- **Company Name**: The main business name
- **Company Address**: Full address (supports multiple lines)
- **Company Phone**: Contact phone number
- **Website**: Full URL (including https://)
- **Services Offered**: Check applicable boxes
  - [ ] Docks
  - [ ] Lifts
  - [ ] Trailers

#### Sub-Locations Meta Box
- Click "Add Sub-Location" to add additional locations
- Each sub-location has:
  - Location Name
  - Address
  - Phone
  - Website (optional)
  - Services (Docks, Lifts, Trailers)
- Click "Remove" button to delete a sub-location

### Managing Existing Dealers

1. Navigate to **Dealers > All Dealers**
2. Click on any dealer to edit
3. Update information as needed
4. Click "Update" to save changes

## Frontend Display

### Using the Shortcode

Add the shortcode to any page or post:

```
[jblund_dealers]
```

### Shortcode Parameters

Control the display with these optional parameters:

```
[jblund_dealers posts_per_page="-1" orderby="title" order="ASC"]
```

**Parameters:**
- `posts_per_page`: Number of dealers to show (-1 = all, default: -1)
- `orderby`: Sort by field (title, date, etc., default: title)
- `order`: Sort direction (ASC or DESC, default: ASC)

### Display Examples

**Show all dealers (default):**
```
[jblund_dealers]
```

**Show 10 most recent dealers:**
```
[jblund_dealers posts_per_page="10" orderby="date" order="DESC"]
```

**Show dealers alphabetically:**
```
[jblund_dealers orderby="title" order="ASC"]
```

## Frontend Appearance

The plugin displays dealers in a responsive card grid:

### Card Layout
```
┌─────────────────────────────────────┐
│ [Blue Header]                       │
│ Company Name                        │
├─────────────────────────────────────┤
│ [White Body]                        │
│ Address: 123 Main St                │
│ Phone: (555) 123-4567               │
│ Website: example.com                │
│                                     │
│ Services:                           │
│ • Docks                             │
│ • Lifts                             │
│ • Trailers                          │
│                                     │
│ Additional Locations:               │
│ ┌─[Sub-location 1]─────────────┐   │
│ │ Branch Name                   │   │
│ │ Address                       │   │
│ │ Phone                         │   │
│ │ Services: Docks, Lifts        │   │
│ └───────────────────────────────┘   │
└─────────────────────────────────────┘
```

### Responsive Behavior

- **Desktop (>1024px)**: Multiple columns grid
- **Tablet (769-1024px)**: 2-3 columns grid
- **Mobile (<768px)**: Single column

## Customization

### Styling

All styles are in `assets/css/dealers.css`. You can customize:
- Card colors and borders
- Grid layout
- Typography
- Hover effects
- Sub-location styling

### Template Override

The shortcode output can be customized by:
1. Copying the shortcode function from the main plugin file
2. Creating a child theme
3. Modifying the HTML structure and classes

## Tips & Best Practices

1. **Company Name**: Keep it concise and clear
2. **Addresses**: Use complete addresses for better maps integration
3. **Phone Numbers**: Use standard formatting for consistency
4. **Websites**: Always include the protocol (https://)
5. **Services**: Only check services actually offered
6. **Sub-locations**: Use meaningful location names (e.g., "North Branch", "Downtown Office")

## Security Features

- Nonce verification on save
- Data sanitization (text fields, URLs, textareas)
- Capability checks (only editors/admins can manage)
- Escaped output on frontend
- No direct file access

## Data Structure

Dealers are stored as:
- **Post Type**: `dealer`
- **Meta Fields**:
  - `_dealer_company_name`
  - `_dealer_company_address`
  - `_dealer_company_phone`
  - `_dealer_website`
  - `_dealer_docks` (1 or 0)
  - `_dealer_lifts` (1 or 0)
  - `_dealer_trailers` (1 or 0)
  - `_dealer_sublocations` (serialized array)

## Troubleshooting

**Dealers not displaying?**
- Check if any dealers are published
- Verify the shortcode is correct
- Check for JavaScript errors in browser console

**Sub-locations not saving?**
- Ensure JavaScript is enabled
- Check browser console for errors
- Verify user has edit permissions

**Styling issues?**
- Clear browser cache
- Check if CSS file is loading
- Verify no theme conflicts

**Permalink issues?**
- Go to Settings > Permalinks
- Click "Save Changes" (flushes rewrite rules)
