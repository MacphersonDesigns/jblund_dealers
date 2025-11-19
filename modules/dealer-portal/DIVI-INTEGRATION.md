# Divi Builder Integration Guide

Complete guide for customizing JBLund Dealer Portal pages with Divi Builder.

## Overview

All dealer portal pages are **100% Divi Builder compatible** out of the box. The plugin automatically detects if Divi is your active theme and configures pages accordingly.

## Automatic Divi Setup

When the plugin is activated with Divi as your active theme, the following happens automatically:

**Meta Fields Set**:

- `_et_pb_use_builder` = `on` - Enables Divi Builder
- `_et_pb_page_layout` = `et_full_width_page` - Full width layout (no sidebar)
- `_et_pb_side_nav` = `off` - Disabled side navigation
- `_et_pb_post_hide_nav` = `default` - Default navigation settings
- `_et_pb_old_content` = `[shortcode]` - Preserves original shortcode

**Pages Auto-Configured**:

1. Dealer Dashboard (`/dealer-dashboard/`)
2. Dealer Profile (`/dealer-profile/`)
3. Dealer Login (`/dealer-login/`)
4. Dealer NDA Acceptance (`/dealer-nda-acceptance/`)

## How to Customize with Divi Builder

### Method 1: Edit Existing Shortcode Pages (Recommended)

The default pages use shortcodes that render functional templates. You can:

1. **Navigate to Pages** → Find the dealer portal page
2. **Click "Use The Divi Builder"** button
3. **Add sections/rows/modules** around the existing shortcode
4. **Keep the shortcode** for core functionality
5. **Add custom content** using Divi modules

**Example Dashboard Structure**:

```
┌─ Section (Header) ──────────────────┐
│ Row: Hero banner with welcome text  │
└─────────────────────────────────────┘
┌─ Section (Main Content) ────────────┐
│ Row: [jblund_dealer_dashboard]      │ ← Keep this shortcode
└─────────────────────────────────────┘
┌─ Section (Custom Widgets) ──────────┐
│ Row: Add your own Divi modules here │
│ - Call to Action                    │
│ - Contact Form                      │
│ - Recent Posts                      │
└─────────────────────────────────────┘
```

### Method 2: Build From Scratch

If you prefer complete control:

1. **Open page in Divi Builder**
2. **Clear existing content** (save shortcode for reference)
3. **Add Code Module** with the shortcode:
   - Dashboard: `[jblund_dealer_dashboard]`
   - Profile: `[jblund_dealer_profile]`
   - Login: `[jblund_dealer_login]`
   - NDA: `[jblund_nda_acceptance]`
4. **Design around it** using Divi sections, rows, and modules

### Method 3: Clone & Customize Templates

Create your own template variations:

1. **Duplicate the page** in WordPress
2. **Edit the duplicate** with Divi Builder
3. **Save as Divi Library Layout**
4. **Apply to original page** or use as template

## Available Shortcodes

All dealer portal functionality is available via shortcodes:

| Shortcode                   | Purpose                          | Required Role                    |
| --------------------------- | -------------------------------- | -------------------------------- |
| `[jblund_dealer_dashboard]` | Main dealer hub with quick links | Dealer (logged in)               |
| `[jblund_dealer_profile]`   | Profile editing form             | Dealer (logged in)               |
| `[jblund_dealer_login]`     | Login form                       | Public                           |
| `[jblund_nda_acceptance]`   | NDA acceptance form              | Dealer (logged in, not accepted) |

## Customization Ideas

### Dashboard Enhancements

**Add Above Shortcode**:

- Blurb module with dealer-specific announcements
- Image module with company branding
- Contact form for dealer support

**Add Below Shortcode**:

- Video module with product demos
- Gallery module with marketing materials
- Blog module with company news

### Profile Page Additions

**Wrap in Tabs**:

- Tab 1: Profile shortcode
- Tab 2: Company information
- Tab 3: Territory details
- Tab 4: Support resources

**Add Sidebar**:

- Quick stats module
- Recent activity feed
- Help documentation links

### Login Page Styling

**Custom Background**:

- Full-screen background image
- Gradient overlay
- Company branding

**Additional Modules**:

- Benefits of dealer account (above login)
- Contact information (below login)
- Testimonials from other dealers

## CSS Customization

The plugin templates include inline CSS that can be overridden with Divi's custom CSS:

**Page Settings** → **Advanced** → **Custom CSS**

```css
/* Override dashboard card colors */
.jblund-dealer-dashboard .dashboard-card {
	background: #f9f9f9;
	border-color: #003366;
}

/* Customize profile form styling */
.jblund-dealer-profile .dealer-profile-form {
	max-width: 100%;
	padding: 30px;
}

/* Adjust login form appearance */
.jblund-dealer-login .login-container {
	background: transparent;
	box-shadow: none;
}
```

## Layout Options

### Full Width (Default)

Best for dashboard and profile pages. Set automatically by plugin.

```
Page Settings → Layout Settings → Page Layout → Full Width
```

### Boxed Layout

For a more contained appearance:

```
Page Settings → Layout Settings → Page Layout → Default
```

### Custom Widths

Control content width with Divi sections:

```
Section Settings → Design → Sizing → Max Width → 1200px (or custom)
```

## Responsive Design

All shortcode templates are mobile-responsive. When adding Divi modules:

**Test on Mobile**:

1. Use Divi's responsive editing mode (tablet/phone icons)
2. Adjust padding/spacing for mobile
3. Hide/show modules based on device

**Best Practices**:

- Keep dealer portal shortcodes visible on all devices
- Stack columns vertically on mobile
- Increase touch target sizes for buttons

## Divi Theme Builder Integration

Create custom templates for dealer portal pages:

1. **Divi** → **Theme Builder**
2. **Add New Template** → **Page**
3. **Assign to**: Specific Pages → Select dealer portal pages
4. **Build custom header/footer** for dealer-specific branding

**Example**: Create a dealer-only header with:

- Company logo
- Dealer-specific navigation menu
- Account status indicator
- Logout button

## Troubleshooting

### Divi Builder Not Enabled

If Divi Builder button doesn't appear:

1. **Edit page** in WordPress admin
2. **Enable Divi Builder** via "Use The Divi Builder" button
3. Or manually set meta: `_et_pb_use_builder` = `on`

### Shortcode Not Rendering

If shortcode appears as text:

1. **Check page content** - shortcode should be in Code Module, not Text Module
2. **Verify brackets** - ensure `[jblund_dealer_dashboard]` not `{jblund_dealer_dashboard}`
3. **Clear cache** - Divi cache, WordPress cache, browser cache

### Styling Conflicts

If plugin CSS conflicts with Divi:

1. **Disable plugin inline styles** - remove `<style>` tags from templates
2. **Use Divi CSS** - rebuild styling in Divi Theme Options
3. **Check z-index** - ensure Divi modules don't overlap dealer content

### Layout Issues

If content appears broken:

1. **Check page template** - should be "Default Page Template"
2. **Verify full-width** - use full-width layout for best results
3. **Clear Divi cache** - Divi → Theme Options → Builder → Static CSS File Generation → Clear

## Migration from Non-Divi

If you installed the plugin before activating Divi:

**Manual Setup**:

1. Edit each dealer portal page
2. Click "Use The Divi Builder"
3. Keep existing shortcode content
4. Add Divi sections/modules as desired

**Or use Page_Manager** to recreate with Divi settings:

```php
// In WordPress admin or custom code
$page_manager = new \JBLund\DealerPortal\Page_Manager();
$page_manager->recreate_missing_pages(); // Updates Divi meta for existing pages
```

## Performance Tips

**Optimize for Speed**:

- Use Divi's Dynamic CSS
- Minify CSS/JS in Theme Options
- Enable Divi's performance features
- Limit number of modules on dealer pages
- Use Divi's built-in image optimization

**Caching**:

- Clear Divi cache after changes
- Exclude dealer pages from full-page caching (they're dynamic)
- Cache only static assets

## Best Practices

1. **Preserve Functionality**: Always keep dealer portal shortcodes intact
2. **Test Logged In/Out States**: Verify pages work for both logged-in dealers and public visitors
3. **Mobile First**: Design for mobile, enhance for desktop
4. **Branding Consistency**: Match dealer portal to main site branding
5. **Security**: Don't expose admin-only content on public-facing pages

## Support & Resources

**Divi Documentation**:

- [Divi Builder Guide](https://www.elegantthemes.com/documentation/divi/)
- [Theme Builder](https://www.elegantthemes.com/documentation/divi/theme-builder/)
- [Code Module](https://www.elegantthemes.com/documentation/divi/code/)

**Plugin Documentation**:

- `README.md` - Plugin overview
- `QUICK_REFERENCE.md` - Shortcode reference
- `USAGE_GUIDE.md` - Setup instructions

## Examples & Inspiration

**Dashboard Layouts**:

- Hero section with dealer name and account status
- Icon boxes for quick actions (Profile, NDA, Resources)
- Accordion with FAQ
- Pricing tables for dealer tiers

**Profile Layouts**:

- Two-column layout (form + info sidebar)
- Progress bar showing profile completion
- Avatar upload module
- Territory map integration

**Login Layouts**:

- Split-screen design (image + form)
- Animated entrance effects
- Social login buttons (if integrated)
- Benefit highlights for non-logged-in users
