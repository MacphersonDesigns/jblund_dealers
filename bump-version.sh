#!/bin/bash
# Version Bump Script - Automatically updates version numbers and creates git tag

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  JBLund Dealers - Version Bumper  ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════╝${NC}\n"

# Check current branch
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
echo -e "${BLUE}Current branch:${NC} $CURRENT_BRANCH"

if [[ "$CURRENT_BRANCH" != "main" ]]; then
    echo -e "${YELLOW}⚠️  Warning: You're not on the main branch${NC}"
    echo -e "${YELLOW}   Releases should be created from 'main' branch${NC}"
    echo ""
    read -p "Do you want to continue anyway? [y/N]: " CONTINUE_CHOICE
    
    if [[ ! $CONTINUE_CHOICE =~ ^[Yy]$ ]]; then
        echo -e "${BLUE}Recommended workflow:${NC}"
        echo -e "  1. Merge your changes to main:"
        echo -e "     ${BLUE}git checkout main${NC}"
        echo -e "     ${BLUE}git merge $CURRENT_BRANCH${NC}"
        echo -e "     ${BLUE}git push origin main${NC}"
        echo -e "  2. Run this script again from main"
        echo ""
        echo -e "${RED}Cancelled${NC}"
        exit 0
    fi
    echo ""
fi

# Get current version from plugin file
CURRENT_VERSION=$(grep "Version:" jblund-dealers.php | awk '{print $3}')
echo -e "${BLUE}Current version:${NC} $CURRENT_VERSION\n"

# Parse current version
IFS='.' read -r -a VERSION_PARTS <<< "$CURRENT_VERSION"
MAJOR="${VERSION_PARTS[0]}"
MINOR="${VERSION_PARTS[1]}"
PATCH="${VERSION_PARTS[2]}"

# Calculate potential new versions
NEXT_MAJOR="$((MAJOR + 1)).0.0"
NEXT_MINOR="${MAJOR}.$((MINOR + 1)).0"
NEXT_PATCH="${MAJOR}.${MINOR}.$((PATCH + 1))"

echo -e "${YELLOW}Select version bump type:${NC}"
echo "1) Patch   (bug fixes)          → v${NEXT_PATCH}"
echo "2) Minor   (new features)       → v${NEXT_MINOR}"
echo "3) Major   (breaking changes)   → v${NEXT_MAJOR}"
echo "4) Custom  (enter manually)"
echo "5) Cancel"
echo ""
read -p "Choice [1-5]: " CHOICE

case $CHOICE in
    1)
        NEW_VERSION="$NEXT_PATCH"
        ;;
    2)
        NEW_VERSION="$NEXT_MINOR"
        ;;
    3)
        NEW_VERSION="$NEXT_MAJOR"
        ;;
    4)
        read -p "Enter version (e.g., 2.1.0): " NEW_VERSION
        ;;
    5)
        echo -e "${RED}Cancelled${NC}"
        exit 0
        ;;
    *)
        echo -e "${RED}Invalid choice${NC}"
        exit 1
        ;;
esac

echo ""
echo -e "${BLUE}Updating to version:${NC} ${GREEN}$NEW_VERSION${NC}"
echo ""

# Update version in jblund-dealers.php (plugin header)
sed -i.bak "s/Version: $CURRENT_VERSION/Version: $NEW_VERSION/" jblund-dealers.php

# Update version constant
sed -i.bak "s/JBLUND_DEALERS_VERSION', '$CURRENT_VERSION'/JBLUND_DEALERS_VERSION', '$NEW_VERSION'/" jblund-dealers.php

# Clean up backup files
rm jblund-dealers.php.bak 2>/dev/null

echo -e "${GREEN}✓${NC} Updated jblund-dealers.php"

# Update CHANGELOG.md
TODAY=$(date +%Y-%m-%d)
CHANGELOG_HEADER="## [$NEW_VERSION] - $TODAY"

# Check if CHANGELOG exists
if [ -f CHANGELOG.md ]; then
    # Add new version header after the main title
    sed -i.bak "/^# Changelog/a\\
\\
$CHANGELOG_HEADER\\
\\
### Added\\
- Version bump to $NEW_VERSION\\
\\
### Changed\\
- [Add your changes here]\\
\\
### Fixed\\
- [Add bug fixes here]\\
" CHANGELOG.md
    rm CHANGELOG.md.bak 2>/dev/null
    echo -e "${GREEN}✓${NC} Updated CHANGELOG.md"
else
    echo -e "${YELLOW}⚠${NC} CHANGELOG.md not found, skipping"
fi

echo ""
echo -e "${BLUE}Git operations:${NC}"

# Check if there are uncommitted changes
if [[ -n $(git status -s) ]]; then
    echo -e "${YELLOW}Uncommitted changes detected${NC}"
    read -p "Commit all changes with message 'Release v${NEW_VERSION}'? [y/N]: " COMMIT_CHOICE
    
    if [[ $COMMIT_CHOICE =~ ^[Yy]$ ]]; then
        git add -A
        git commit -m "Release v${NEW_VERSION}"
        echo -e "${GREEN}✓${NC} Changes committed"
    else
        echo -e "${YELLOW}⚠${NC} Skipping commit"
    fi
fi

# Create git tag
read -p "Create and push git tag v${NEW_VERSION}? [y/N]: " TAG_CHOICE

if [[ $TAG_CHOICE =~ ^[Yy]$ ]]; then
    git tag -a "v${NEW_VERSION}" -m "Release version ${NEW_VERSION}"
    echo -e "${GREEN}✓${NC} Tag v${NEW_VERSION} created"
    
    read -p "Push tag to GitHub (triggers release build)? [y/N]: " PUSH_CHOICE
    
    if [[ $PUSH_CHOICE =~ ^[Yy]$ ]]; then
        git push origin "v${NEW_VERSION}"
        echo -e "${GREEN}✓${NC} Tag pushed to GitHub"
        echo ""
        echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
        echo -e "${GREEN}🚀 Release v${NEW_VERSION} triggered!${NC}"
        echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
        echo ""
        echo -e "GitHub Action will:"
        echo -e "  1. Build the plugin ZIP"
        echo -e "  2. Create a GitHub release"
        echo -e "  3. Attach the ZIP file"
        echo -e "  4. WordPress sites will check for updates"
        echo ""
        echo -e "View progress at:"
        echo -e "${BLUE}https://github.com/MacphersonDesigns/jblund_dealers/actions${NC}"
    fi
else
    echo -e "${YELLOW}⚠${NC} Tag not created"
    echo ""
    echo -e "${YELLOW}To manually create and push tag later:${NC}"
    echo -e "  git tag -a v${NEW_VERSION} -m 'Release version ${NEW_VERSION}'"
    echo -e "  git push origin v${NEW_VERSION}"
fi

echo ""
echo -e "${GREEN}✓ Version bump complete!${NC}"
echo -e "${BLUE}New version:${NC} ${GREEN}v${NEW_VERSION}${NC}"
