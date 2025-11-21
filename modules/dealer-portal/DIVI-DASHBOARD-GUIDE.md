# Dealer Dashboard - Divi Builder Integration Guide

## Overview

The dealer dashboard has been modularized into individual shortcode components that can be arranged and styled in Divi Builder. This gives you complete control over the layout, styling, and organization of the dealer portal without needing to modify plugin code.

## Available Shortcodes

### 1. Welcome Header

```
[dealer_welcome_header]
```

**Displays:** Personalized welcome message with dealer name/company name

**Use Case:** Top of dashboard page

---

### 2. Quick Links

```
[dealer_quick_links]
```

**Displays:** Navigation links (My Profile, View NDA, Logout)

**Use Case:** Sidebar or top navigation area

---

### 3. Account Status

```
[dealer_account_status]
```

**Displays:**

- Account type (Authorized Dealer)
- NDA acceptance status
- Company name

**Use Case:** Status widget/card area

---

### 4. Signed Documents

```
[dealer_signed_documents]
```

**Displays:**

- List of signed documents (currently NDA)
- View and Download PDF buttons
- Signing date

**Use Case:** Documents section
**Dynamic:** Automatically shows PDF download when available

---

### 5. Documents to Complete

```
[dealer_documents_to_complete]
```

**Displays:** Required documents from admin settings that need completion

**Use Case:** Task list area
**Admin Control:** Managed via Dealers > Settings > Documents tab

---

### 6. Resources

```
[dealer_resources resources="Product Catalog|/catalog/,Marketing Materials|/marketing/,Contact Support|/contact/"]
```

**Displays:** List of resource links

**Attributes:**

- `resources` - Comma-separated list of Title|URL pairs

**Use Case:** Resource links section
**Flexible:** Can be customized per page

---

### 7. Recent Updates

```
[dealer_recent_updates]
```

**Displays:** Scheduled announcements and news

**Use Case:** Announcements/news area
**Admin Control:** Managed via Dealers > Settings > Portal Updates tab
**Dynamic:** Shows/hides based on date ranges set in admin

---

### 8. Dealer Representative

```
[dealer_representative]
```

**Displays:**

- Representative name
- Phone number (clickable)
- Email address (clickable)

**Use Case:** Contact card
**Dynamic:** Only shows if dealer has assigned representative

---

## Divi Implementation Guide

### Step 1: Create New Page

1. Go to **Pages > Add New**
2. Title it "Dealer Dashboard" (or use existing page)
3. Click **Use Divi Builder**

### Step 2: Choose Layout Style

#### Option A: Grid Layout (Recommended)

Use Divi's **Row** with **3 Columns** for a card-style dashboard:

```
Row 1 (1 Column - Full Width)
└─ [dealer_welcome_header]

Row 2 (3 Columns)
├─ Column 1: [dealer_quick_links]
├─ Column 2: [dealer_account_status]
└─ Column 3: [dealer_signed_documents]

Row 3 (3 Columns)
├─ Column 1: [dealer_documents_to_complete]
├─ Column 2: [dealer_resources]
└─ Column 3: [dealer_recent_updates]

Row 4 (1 Column)
└─ [dealer_representative]
```

#### Option B: Sidebar Layout

```
Row 1 (2 Columns - 25% / 75%)
├─ Sidebar (25%):
│   ├─ [dealer_quick_links]
│   └─ [dealer_account_status]
└─ Main Content (75%):
    ├─ [dealer_welcome_header]
    ├─ [dealer_signed_documents]
    ├─ [dealer_documents_to_complete]
    ├─ [dealer_resources]
    └─ [dealer_recent_updates]
```

### Step 3: Add Shortcodes

1. Click **Add New Module**
2. Search for **Shortcode** module
3. Paste the shortcode
4. Style as desired using Divi's design options

### Step 4: Style with Divi

Each shortcode outputs semantic HTML with classes you can target:

**Module Classes:**

- `.dashboard-card` - Main card wrapper
- `.dashboard-header` - Welcome header
- `.link-list` - Quick links list
- `.status-info` - Account status items
- `.documents-list` - Documents list
- `.resources-list` - Resources list

**Styling Tips:**

- Add **Box Shadow** to cards for depth
- Use **Spacing** settings for padding/margins
- Apply **Background** colors to match your brand
- Add **Borders** for definition
- Use **Animation** for engaging effects

### Step 5: Assign to Dashboard

1. Go to **Dealers > Settings > Portal Pages tab**
2. Select your new Divi page for **Dashboard Page**
3. Save settings

---

## Advanced Customization

### Custom Resource Links Per Page

You can create multiple dashboard pages with different resources:

**Dealer Pricing Page:**

```
[dealer_resources resources="Price List 2025|/pricing/,Volume Discounts|/volume/,Terms|/terms/"]
```

**Dealer Marketing Page:**

```
[dealer_resources resources="Brand Guidelines|/brand/,Product Photos|/photos/,Marketing Videos|/videos/"]
```

### Conditional Display

Use Divi's **Visibility** settings to show/hide sections based on:

- User login status
- User role
- Custom conditions

### Custom CSS

Target specific shortcode elements:

```css
/* Style signed documents card */
.dashboard-card.signed-documents {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
}

/* Style quick links */
.quick-links .dashboard-link {
	transition: transform 0.3s;
}

.quick-links .dashboard-link:hover {
	transform: translateX(10px);
}

/* Style document status badges */
.document-status.signed {
	background: #10b981;
	padding: 4px 12px;
	border-radius: 12px;
	color: white;
}
```

---

## Dynamic Content Updates

### Admin-Controlled Sections

These shortcodes pull content from admin settings and update automatically:

1. **Documents to Complete** - Edit in **Dealers > Settings > Documents**
2. **Recent Updates** - Manage in **Dealers > Settings > Portal Updates**
3. **Signed Documents** - Updates when dealers sign documents

No page edits needed when admin makes changes!

### Future Expansion Ideas

With this modular approach, you can easily add:

- `[dealer_order_history]` - Order tracking
- `[dealer_pricing]` - Custom pricing tables
- `[dealer_territory]` - Territory information
- `[dealer_support_tickets]` - Support system
- `[dealer_training_videos]` - Video library
- `[dealer_inventory]` - Available inventory

Each new feature can be a standalone shortcode!

---

## Migration from Template

If you're currently using the built-in dashboard template and want to migrate to Divi:

1. Create new Divi page with all shortcodes
2. Test layout as admin
3. Assign new page in **Portal Pages** settings
4. Old template becomes inactive automatically

The plugin template remains as a fallback if no Divi page is assigned.

---

## Troubleshooting

**Shortcode shows as text:**

- Make sure you're using the **Shortcode module** in Divi, not Text module
- Check that the shortcode is exactly as documented (no typos)

**Nothing displays:**

- Ensure you're logged in as a dealer user
- Some shortcodes (like representative) only show when data exists

**Styling doesn't match:**

- Dashboard.css is still loaded for styling
- Add custom CSS in Divi's Custom CSS area or theme stylesheet

**PDF download not showing:**

- PDF generation happens when dealer signs NDA
- Check **Dealers > Signed NDAs** to see if PDF exists

---

## Example: Complete Divi Dashboard

Here's a complete example layout you can copy:

### Section 1: Hero

- Background: Gradient (#FF0000 to #CC0000)
- Text Color: White
- Module: `[dealer_welcome_header]`

### Section 2: Cards Grid

- Row with 3 columns (33% each)
- Column 1: `[dealer_quick_links]`
- Column 2: `[dealer_account_status]`
- Column 3: `[dealer_signed_documents]`

### Section 3: Content Area

- Row with 2 columns (66% / 33%)
- Main Column:
  - `[dealer_documents_to_complete]`
  - `[dealer_resources]`
- Sidebar:
  - `[dealer_recent_updates]`
  - `[dealer_representative]`

This creates a modern, responsive dashboard fully controlled through Divi Builder!

---

## Support

For questions or feature requests regarding dashboard shortcodes, refer to the main plugin documentation or contact the development team.
