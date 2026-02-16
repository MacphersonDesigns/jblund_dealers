# JBLund Dealers Plugin - Complete Usage Guide

## Table of Contents

1. [Installation](#installation)
2. [Quick Start](#quick-start)
3. [Admin Interface](#admin-interface)
4. [Frontend Display](#frontend-display)
5. [Customization](#customization)
6. [Example Data](#example-data)
7. [Troubleshooting](#troubleshooting)

---

## Installation

### Step 1: Upload Plugin

1. Upload the `jblund_dealers` folder to `/wp-content/plugins/`
2. Verify these files exist:
   - `jblund-dealers.php` (main plugin file)
   - `uninstall.php` (cleanup script)
   - `assets/css/dealers.css` (styles)

### Step 2: Activate Plugin

1. Navigate to **Plugins** menu in WordPress admin
2. Find "JBLund Dealers"
3. Click **Activate**
4. Verify "Dealers" menu appears in admin sidebar

### Requirements Checklist

- [ ] WordPress 5.0 or higher
- [ ] PHP 7.0 or higher
- [ ] Administrator privileges
- [ ] JavaScript enabled in browser

---

## Quick Start

### Add Your First Dealer (2 minutes)

1. Go to **Dealers > Add New**
2. Fill in company details in the **Dealer Information** meta box:
   - **Company Name**: "Waterfront Marine"
   - **Address**: "123 Harbor Drive, Marina Bay, CA 94577"
   - **Phone**: "(555) 123-4567"
   - **Website**: "https://waterfrontmarine.example.com"
3. Check applicable service boxes:
   - ☑ Docks
   - ☑ Lifts
   - ☑ Trailers
4. Click **Publish**

### Display Dealers (30 seconds)

1. Edit any page or post
2. Add shortcode: `[jblund_dealers]`
3. Click **Publish/Update**
4. View the page - your dealers will display in a responsive grid!

---

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

### List Layout

Displays dealers in horizontal rows on desktop, switches to cards on mobile:

- **Desktop**: 4-column horizontal layout with vertically centered content
- **Mobile**: Transforms to card view with blue headers and white content areas
- **Advantages**: Shows all info at a glance, easy to scan, responsive design
- **Best for**: Directory-style pages, professional listings

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

---

## Example Data

### Example Dealer 1: Waterfront Marine

**Main Location:**

- Company Name: Waterfront Marine
- Company Address: 123 Harbor Drive, Marina Bay, CA 94577
- Company Phone: (555) 123-4567
- Website: <https://waterfrontmarine.example.com>
- Services: ✓ Docks, ✓ Lifts, ✓ Trailers

**Sub-Location: North Shore Branch**

- Location Name: North Shore Branch
- Address: 456 Lakeview Road, North Shore, CA 94580
- Phone: (555) 234-5678
- Website: <https://waterfrontmarine.example.com/northshore>
- Services: ✓ Docks, ✓ Lifts, ✗ Trailers

### Example Dealer 2: Lake View Docks & More

**Main Location:**

- Company Name: Lake View Docks & More
- Company Address: 789 Shoreline Boulevard, Lakeside, CA 94589
- Company Phone: (555) 345-6789
- Website: <https://lakeviewdocks.example.com>
- Services: ✓ Docks, ✗ Lifts, ✓ Trailers

**Sub-Location 1: Eastside Location**

- Location Name: Eastside Location
- Address: 321 East Lake Drive, Lakeside, CA 94590
- Phone: (555) 456-7890
- Website: (none)
- Services: ✓ Docks, ✗ Lifts, ✗ Trailers

**Sub-Location 2: Mountain View Store**

- Location Name: Mountain View Store
- Address: 654 Mountain Road, Mountain View, CA 94591
- Phone: (555) 567-8901
- Website: <https://lakeviewdocks.example.com/mountainview>
- Services: ✗ Docks, ✗ Lifts, ✓ Trailers

### Example Dealer 3: Coastal Marine Supply

**Main Location:**

- Company Name: Coastal Marine Supply
- Company Address: 987 Pacific Highway, Coastal City, CA 94595
- Company Phone: (555) 678-9012
- Website: <https://coastalmarine.example.com>
- Services: ✓ Docks, ✓ Lifts, ✓ Trailers

_(No sub-locations)_

**Notes:**

- Dealers can have zero or more sub-locations
- Sub-location websites are optional
- Each location (main or sub) can independently offer different services
- All fields except services (checkboxes) are text-based

---

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

---

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
