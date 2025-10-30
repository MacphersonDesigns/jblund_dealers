# Installation Verification

This document helps verify that the JBLund Dealers plugin is correctly installed and functioning.

## Pre-Installation Checklist

Before installing, verify:

- [ ] WordPress version is 5.0 or higher
  - Check: Dashboard > Updates
- [ ] PHP version is 7.0 or higher
  - Check: Dashboard > Tools > Site Health > Info > Server
- [ ] User has Administrator privileges
- [ ] Plugin folder is named `jblund_dealers` or `jblund-dealers`

## Installation Steps Verification

### Step 1: File Upload
Verify these files exist in `/wp-content/plugins/jblund_dealers/`:

- [ ] `jblund-dealers.php` (main plugin file)
- [ ] `uninstall.php` (cleanup script)
- [ ] `assets/css/dealers.css` (styles)
- [ ] `README.md` (documentation)
- [ ] Other documentation files (.md files)

### Step 2: Plugin Activation
After activation, verify:

- [ ] No error messages appear
- [ ] "Dealers" menu appears in WordPress admin
- [ ] Menu icon shows a store/shop icon
- [ ] Menu position is below "Posts"

### Step 3: Post Type Registration
Check if the custom post type is registered:

- [ ] Navigate to **Dealers > All Dealers**
- [ ] Page loads without errors
- [ ] "Add New" button is visible

## Functionality Verification

### Test 1: Create a Dealer

1. Go to **Dealers > Add New**
2. Verify these elements are present:
   - [ ] Title field (standard WordPress)
   - [ ] "Dealer Information" meta box
   - [ ] "Sub-Locations" meta box
   - [ ] Publish box (sidebar)

3. Fill in dealer information:
   - [ ] Company Name field accepts text
   - [ ] Address field accepts multiple lines
   - [ ] Phone field accepts text
   - [ ] Website field accepts URLs
   - [ ] Service checkboxes are clickable

4. Test sub-locations:
   - [ ] "Add Sub-Location" button works
   - [ ] New sub-location form appears
   - [ ] "Remove" button appears for each sub-location
   - [ ] Multiple sub-locations can be added

5. Save the dealer:
   - [ ] Click "Publish"
   - [ ] No errors occur
   - [ ] Success message appears
   - [ ] Dealer appears in "All Dealers" list

### Test 2: Edit a Dealer

1. Go to **Dealers > All Dealers**
2. Click on a dealer to edit
3. Verify:
   - [ ] All previously entered data is displayed
   - [ ] Fields are editable
   - [ ] Sub-locations are displayed correctly
   - [ ] Changes can be saved

### Test 3: Frontend Display

1. Create or edit a test page
2. Add the shortcode: `[jblund_dealers]`
3. Publish/Update the page
4. View the page on frontend
5. Verify:
   - [ ] Dealer cards are displayed
   - [ ] Cards show in a grid layout
   - [ ] Company name appears in blue header
   - [ ] All dealer information is visible
   - [ ] Sub-locations are displayed (if added)
   - [ ] Services are listed with bullets
   - [ ] Links are clickable

### Test 4: Styling Verification

On the frontend page with dealers:

- [ ] CSS file loads (check browser dev tools)
- [ ] Cards have borders and shadows
- [ ] Header is blue with white text
- [ ] Hover effect works (card elevates slightly)
- [ ] Links are blue and underline on hover
- [ ] Layout is responsive (test mobile view)

### Test 5: Shortcode Parameters

Test shortcode variations:

1. `[jblund_dealers posts_per_page="5"]`
   - [ ] Only 5 dealers display

2. `[jblund_dealers orderby="date" order="DESC"]`
   - [ ] Dealers sorted by date, newest first

### Test 6: Security Verification

1. Create a dealer and save
2. Check browser developer tools > Network
3. Verify:
   - [ ] POST request includes nonce field
   - [ ] No sensitive data in URL parameters

4. Try to edit a dealer as a non-admin user
   - [ ] Editor role can edit dealers
   - [ ] Subscriber role cannot edit dealers

## Common Issues & Solutions

### Issue: "Dealers" menu not appearing
**Solution:** 
- Deactivate and reactivate the plugin
- Check for conflicting plugins
- Verify file permissions

### Issue: Shortcode displays as text
**Solution:**
- Ensure plugin is activated
- Check shortcode spelling: `[jblund_dealers]` (no typos)
- Verify you're not in a code block

### Issue: Styles not loading
**Solution:**
- Clear browser cache
- Check if `assets/css/dealers.css` exists
- Verify file permissions (644 recommended)

### Issue: Sub-locations not saving
**Solution:**
- Check browser console for JavaScript errors
- Ensure JavaScript is enabled
- Try a different browser

### Issue: 404 errors on dealer pages
**Solution:**
- Go to Settings > Permalinks
- Click "Save Changes" (flushes rewrite rules)

## Performance Verification

For sites with many dealers:

- [ ] Admin list page loads in under 2 seconds
- [ ] Frontend page loads in under 3 seconds
- [ ] No console errors in browser
- [ ] No PHP errors in server logs

## Final Checklist

Before going live:

- [ ] All required dealers are added
- [ ] All dealer information is accurate
- [ ] Contact information is verified
- [ ] Services are correctly marked
- [ ] Sub-locations are tested
- [ ] Frontend display looks professional
- [ ] Mobile view is tested
- [ ] Links are tested and working
- [ ] No error messages anywhere

## Post-Installation Tasks

- [ ] Add shortcode to appropriate pages
- [ ] Test all links and phone numbers
- [ ] Create backup of database
- [ ] Document custom modifications (if any)
- [ ] Train content editors on usage

## Success Criteria

The installation is successful when:

✅ Plugin activates without errors
✅ "Dealers" menu is accessible
✅ Dealers can be created and edited
✅ Shortcode displays dealers correctly
✅ Frontend styling is applied
✅ Mobile view is responsive
✅ No JavaScript or PHP errors

## Getting Help

If verification fails:

1. Check README.md for troubleshooting
2. Review USAGE_GUIDE.md for detailed instructions
3. Verify server meets requirements
4. Check WordPress and plugin logs
5. Contact plugin support with error details

---

**Version:** 1.0.0  
**Last Updated:** 2025-10-30  
**Status:** Initial Release
