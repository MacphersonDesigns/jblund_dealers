# JBLund Dealers WordPress Plugin

A custom WordPress plugin to store and display dealer information for JBLund Dock's B2B Website. This plugin provides a complete dealer management system with support for multiple locations and service offerings.

## Features

- Custom post type for managing dealers
- Comprehensive dealer information fields:
  - Company Name
  - Company Address
  - Company Phone
  - Website
  - Services: Docks, Lifts, Trailers (boolean checkboxes)
- Support for multiple sub-locations per dealer
- Each sub-location can have:
  - Location Name
  - Address
  - Phone
  - Website (optional)
  - Services: Docks, Lifts, Trailers
- Beautiful card-based display on the frontend
- Responsive design for mobile and desktop
- Shortcode for easy integration

## Installation

1. Download or clone this repository
2. Copy the entire `jblund_dealers` folder to your WordPress installation's `wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. You'll see a new "Dealers" menu item in the WordPress admin

## Usage

### Adding a Dealer

1. Go to **Dealers > Add New** in the WordPress admin
2. Enter the dealer information in the **Dealer Information** meta box:
   - Company Name
   - Company Address
   - Company Phone
   - Website URL
   - Check the services offered (Docks, Lifts, Trailers)
3. (Optional) Add sub-locations using the **Sub-Locations** meta box:
   - Click "Add Sub-Location" to add a new location
   - Fill in the location details
   - Click "Remove" to delete a sub-location
4. Click "Publish" to save the dealer

### Displaying Dealers

Use the shortcode `[jblund_dealers]` in any post, page, or widget to display all dealers.

#### Shortcode Parameters

- `posts_per_page` - Number of dealers to display (default: -1 for all)
- `orderby` - Sort dealers by field (default: 'title')
- `order` - Sort order ASC or DESC (default: 'ASC')

**Examples:**

```
[jblund_dealers]
[jblund_dealers posts_per_page="10"]
[jblund_dealers orderby="date" order="DESC"]
```

## File Structure

```
jblund_dealers/
├── jblund-dealers.php       # Main plugin file
├── assets/
│   └── css/
│       └── dealers.css       # Frontend styles
└── README.md                 # This file
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## Design

The plugin displays dealers in a responsive grid layout with card-based design:
- Cards show dealer name in a blue header
- All dealer information is neatly organized
- Sub-locations are displayed in a separate section
- Services are listed with bullet points
- Hover effects for better user experience
- Fully responsive for mobile devices

## Development

This plugin is designed to be lightweight and maintainable:
- Uses WordPress best practices
- Proper data sanitization and escaping
- Nonce verification for security
- Internationalization ready (text domain: 'jblund-dealers')
- Clean, semantic HTML output

## License

GPL v2 or later

## Author

Macpherson Designs

## Support

For issues, questions, or contributions, please visit the GitHub repository. 
