#!/bin/bash
# JBLund Dealers Plugin - Production Build Script
# Creates a clean ZIP file ready for WordPress installation

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}==================================${NC}"
echo -e "${BLUE}JBLund Dealers - Production Build${NC}"
echo -e "${BLUE}==================================${NC}\n"

# Get plugin directory and version
PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_NAME="jblund_dealers"
VERSION=$(grep "Version:" jblund-dealers.php | awk '{print $3}')
BUILD_DIR="$PLUGIN_DIR/../build"
DIST_DIR="$BUILD_DIR/$PLUGIN_NAME"
ZIP_NAME="${PLUGIN_NAME}-v${VERSION}.zip"

echo -e "${BLUE}Plugin Version:${NC} $VERSION"
echo -e "${BLUE}Build Directory:${NC} $BUILD_DIR\n"

# Clean previous build
echo -e "${BLUE}Cleaning previous build...${NC}"
rm -rf "$BUILD_DIR"
mkdir -p "$DIST_DIR"

# Copy plugin files
echo -e "${BLUE}Copying plugin files...${NC}"
rsync -av --progress "$PLUGIN_DIR/" "$DIST_DIR/" \
    --exclude='.git' \
    --exclude='.github' \
    --exclude='.gitignore' \
    --exclude='.DS_Store' \
    --exclude='.claude' \
    --exclude='node_modules' \
    --exclude='package.json' \
    --exclude='package-lock.json' \
    --exclude='composer.lock' \
    --exclude='build.sh' \
    --exclude='bump-version.sh' \
    --exclude='assets/scss' \
    --exclude='assets/css/old-reference.css' \
    --exclude='assets/css/*.map' \
    --exclude='SCSS-GUIDE.md' \
    --exclude='DEALER-REGISTRATION-WORKFLOW.md' \
    --exclude='**/MODULAR-REFACTOR.md' \
    --exclude='**/DEALER-PROFILE-ENHANCEMENT.md' \
    --exclude='**/DIVI-DASHBOARD-GUIDE.md' \
    --exclude='**/DIVI-INTEGRATION.md' \
    --exclude='modules/dealer-portal/README.md' \
    --exclude='modules/dealer-portal/assets/scss' \
    --exclude='*.log'

# Create ZIP file
echo -e "\n${BLUE}Creating ZIP archive...${NC}"
cd "$BUILD_DIR"
zip -r "$ZIP_NAME" "$PLUGIN_NAME" -q

# Move ZIP to plugin root
mv "$ZIP_NAME" "$PLUGIN_DIR/"

# Cleanup build directory
echo -e "${BLUE}Cleaning up...${NC}"
rm -rf "$DIST_DIR"

# Success message
echo -e "\n${GREEN}✓ Build Complete!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Package:${NC} $PLUGIN_DIR/$ZIP_NAME"
echo -e "${GREEN}Version:${NC} $VERSION"

# Get file size
FILE_SIZE=$(du -h "$PLUGIN_DIR/$ZIP_NAME" | cut -f1)
echo -e "${GREEN}Size:${NC} $FILE_SIZE"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"

# Show contents summary
echo -e "${BLUE}Package Contents:${NC}"
unzip -l "$PLUGIN_DIR/$ZIP_NAME" | head -20
TOTAL_FILES=$(unzip -l "$PLUGIN_DIR/$ZIP_NAME" | tail -1 | awk '{print $2}')
echo -e "\n${BLUE}Total Files:${NC} $TOTAL_FILES\n"

echo -e "${GREEN}Ready to upload to WordPress!${NC}\n"
