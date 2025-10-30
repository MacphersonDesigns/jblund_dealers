# Quick Reference Card

## Installation
1. Upload to `/wp-content/plugins/`
2. Activate via WordPress admin
3. Find "Dealers" in admin menu

## Quick Start

### Add a Dealer (2 minutes)
1. Go to **Dealers > Add New**
2. Fill in company details
3. Check service boxes
4. (Optional) Add sub-locations
5. Click **Publish**

### Display Dealers (30 seconds)
1. Edit any page or post
2. Add shortcode: `[jblund_dealers]`
3. Publish/Update the page

## Essential Shortcode

```
[jblund_dealers]
```

## Common Variations

**Show 10 dealers:**
```
[jblund_dealers posts_per_page="10"]
```

**Newest first:**
```
[jblund_dealers orderby="date" order="DESC"]
```

## Field Guide

### Main Dealer Fields
| Field | Type | Required | Example |
|-------|------|----------|---------|
| Company Name | Text | Yes | "Waterfront Marine" |
| Address | Textarea | No | "123 Harbor Dr\nMarina Bay, CA" |
| Phone | Text | No | "(555) 123-4567" |
| Website | URL | No | "https://example.com" |
| Docks | Checkbox | No | ✓ |
| Lifts | Checkbox | No | ✓ |
| Trailers | Checkbox | No | ✗ |

### Sub-Location Fields
Same as main fields, plus:
- **Location Name** (e.g., "North Branch")

## Admin Locations

- **All Dealers**: Dealers menu
- **Add New**: Dealers > Add New
- **Edit**: Click dealer name in list
- **Delete**: Trash from list or edit screen

## Frontend Customization

**CSS Classes:**
- `.jblund-dealers-grid` - Main container
- `.dealer-card` - Individual card
- `.dealer-card-header` - Blue header
- `.dealer-card-body` - Card content
- `.dealer-sublocations` - Sub-locations section

## Support

- **Documentation**: See README.md
- **Usage Guide**: See USAGE_GUIDE.md
- **Examples**: See SAMPLE_DATA.md
- **Technical**: See VISUAL_OVERVIEW.md

## Version

Current Version: **1.0.0**

## Requirements

- WordPress: 5.0+
- PHP: 7.0+
- No external dependencies

## Key Features

✓ Unlimited dealers
✓ Unlimited sub-locations per dealer
✓ Responsive design
✓ Mobile-friendly
✓ Print-friendly
✓ No coding required

## Uninstall

To completely remove the plugin and all data:
1. Deactivate the plugin
2. Delete the plugin via WordPress admin
3. All dealer data will be removed automatically

## Tips

💡 Use meaningful company names
💡 Complete addresses help with maps
💡 Standardize phone format
💡 Always include https:// in URLs
💡 Name sub-locations clearly
💡 Only check offered services

## Troubleshooting

**Can't see dealers?**
→ Check if any are published

**Shortcode not working?**
→ Verify spelling: `[jblund_dealers]`

**Styling issues?**
→ Clear browser cache

**Sub-locations not saving?**
→ Enable JavaScript

---

For full documentation, see **README.md** and **USAGE_GUIDE.md**
