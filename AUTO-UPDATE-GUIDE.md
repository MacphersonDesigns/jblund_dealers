# Auto-Update System - Quick Reference

## 🚀 How It Works

1. **You make changes** in your dev branch
2. **Run version bump script**: `./bump-version.sh`
3. **Choose version type**: Patch (bug fixes), Minor (features), or Major (breaking)
4. **Script automatically**:
   - Updates version in `jblund-dealers.php`
   - Updates `CHANGELOG.md`
   - Creates git tag
   - Pushes to GitHub
5. **GitHub Action triggers**:
   - Builds clean production ZIP
   - Creates GitHub release
   - Attaches ZIP file
6. **WordPress sites auto-detect update**:
   - Shows update notification in admin
   - One-click update from GitHub

## 📝 Version Numbering (Semantic Versioning)

- **Major** (X.0.0): Breaking changes, major rewrites
  - Example: 1.0.0 → 2.0.0
  
- **Minor** (x.X.0): New features, non-breaking changes  
  - Example: 2.0.0 → 2.1.0
  
- **Patch** (x.x.X): Bug fixes, small tweaks
  - Example: 2.1.0 → 2.1.1

## 🎯 Quick Start

### First Release (v2.0.0)

```bash
# 1. Make sure all changes are committed
git status

# 2. Run version bump (it will guide you)
./bump-version.sh

# 3. Select option based on changes:
#    - Choose "1" (Patch) for bug fixes
#    - Choose "2" (Minor) for new features  
#    - Choose "3" (Major) for breaking changes

# 4. Follow prompts to create and push tag

# 5. Watch GitHub Action build
#    https://github.com/MacphersonDesigns/jblund_dealers/actions
```

### Subsequent Updates

```bash
# Quick version bump and release
./bump-version.sh

# Or manually:
git tag -a v2.0.1 -m "Release version 2.0.1"
git push origin v2.0.1
```

## 🔧 Manual Version Update

If you prefer to update manually:

1. **Update version in `jblund-dealers.php`**:
   ```php
   * Version: 2.0.1
   ```
   
2. **Update constant**:
   ```php
   define('JBLUND_DEALERS_VERSION', '2.0.1');
   ```

3. **Update CHANGELOG.md**

4. **Create and push tag**:
   ```bash
   git tag -a v2.0.1 -m "Release version 2.0.1"
   git push origin v2.0.1
   ```

## 📦 What Gets Included in Release

✅ **Included:**
- All PHP files
- Compiled CSS (not SCSS)
- JavaScript files
- Vendor dependencies (Composer)
- User documentation

❌ **Excluded:**
- Development files (.git, .github, node_modules)
- SCSS source files
- Build scripts
- Internal documentation

## 🔍 Monitoring Updates

### Check Release Status
```bash
# View tags
git tag -l

# View remote releases
gh release list  # (requires GitHub CLI)
```

### GitHub Actions
- View builds: https://github.com/MacphersonDesigns/jblund_dealers/actions
- View releases: https://github.com/MacphersonDesigns/jblund_dealers/releases

### WordPress Admin
- Updates appear in: **Dashboard → Updates**
- Or: **Plugins** page (update notification)

## 🛠️ Troubleshooting

### Update Not Showing in WordPress?

1. **Check GitHub release exists**:
   - Visit: https://github.com/MacphersonDesigns/jblund_dealers/releases
   - Ensure ZIP file is attached

2. **Force WordPress to check**:
   - Go to: Dashboard → Updates
   - Click "Check Again"

3. **Check plugin version**:
   - Should match latest release tag

4. **Clear update cache**:
   ```php
   // In WordPress admin, run in browser console:
   delete_site_transient('update_plugins');
   ```

### GitHub Action Failed?

1. **View action logs**: 
   - https://github.com/MacphersonDesigns/jblund_dealers/actions
   
2. **Common issues**:
   - Missing tag (must start with `v`)
   - Build script errors
   - Permission issues (check repo settings)

3. **Re-trigger action**:
   ```bash
   # Delete and recreate tag
   git tag -d v2.0.0
   git push origin :refs/tags/v2.0.0
   git tag -a v2.0.0 -m "Release version 2.0.0"
   git push origin v2.0.0
   ```

## 🔐 Private Repository Setup

If your repo is private, update the update checker:

```php
// In jblund-dealers.php, uncomment and set:
$updateChecker->setAuthentication('your-github-token-here');
```

Generate token: https://github.com/settings/tokens
- Scope: `repo` (Full control of private repositories)

## 📊 Current Setup

- **Version**: 2.0.0
- **Repository**: MacphersonDesigns/jblund_dealers
- **Update Check**: Automatic (every 12 hours)
- **Branch**: Releases from tags
- **Format**: ZIP with compiled assets

## 🎉 Benefits

✅ Professional update system  
✅ No WordPress.org submission needed  
✅ Full control over releases  
✅ Automatic changelog display  
✅ One-click updates for clients  
✅ Version history tracking  
✅ Rollback capability (via GitHub releases)

## 📞 Support

Questions? Check:
- GitHub Issues: https://github.com/MacphersonDesigns/jblund_dealers/issues
- Actions Docs: https://docs.github.com/en/actions
- Plugin Update Checker: https://github.com/YahnisElsts/plugin-update-checker
