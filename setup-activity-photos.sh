#!/bin/bash

# Activity Photo System - Setup Script
# Jalankan script ini untuk setup otomatis sistem laporan foto

set -e

echo "🔧 Setting up Activity Photo System..."
echo "======================================"
echo ""

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if Laravel is installed
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: Laravel not found. Run from project root.${NC}"
    exit 1
fi

# 1. Run migrations
echo -e "${BLUE}📦 Running database migrations...${NC}"
php artisan migrate
echo -e "${GREEN}✅ Migrations completed${NC}"
echo ""

# 2. Create storage symlink
echo -e "${BLUE}🔗 Creating storage symlink...${NC}"
if [ -f "public/storage" ]; then
    echo "Storage symlink already exists"
else
    php artisan storage:link
fi
echo -e "${GREEN}✅ Storage symlink ready${NC}"
echo ""

# 3. Check permissions
echo -e "${BLUE}🔐 Checking storage permissions...${NC}"
if [ -w "storage/" ]; then
    echo -e "${GREEN}✅ Storage directory is writable${NC}"
else
    echo -e "${RED}⚠️  Warning: Storage directory may not be writable${NC}"
    echo "   Run: chmod -R 775 storage/"
fi
echo ""

# 4. Check if Capacitor is installed (optional)
echo -e "${BLUE}📱 Checking Capacitor setup...${NC}"
if [ -f "package.json" ]; then
    if grep -q "@capacitor/camera" package.json; then
        echo -e "${GREEN}✅ Capacitor Camera already installed${NC}"
    else
        echo -e "${BLUE}Install Capacitor Camera for mobile support?${NC}"
        read -p "Install? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            npm install @capacitor/camera
            npx cap sync
            echo -e "${GREEN}✅ Capacitor Camera installed${NC}"
        fi
    fi
else
    echo "package.json not found. Skipping npm setup."
fi
echo ""

# 5. Show config info
echo -e "${BLUE}📋 Configuration:${NC}"
echo "   Storage Path: storage/app/public/activity-photos/"
echo "   Max Photos: 5"
echo "   Max File Size: 5MB"
echo "   Supported Types: JPEG, PNG, GIF, WebP"
echo ""

# 6. Show next steps
echo -e "${GREEN}✅ Setup Complete!${NC}"
echo ""
echo -e "${BLUE}📚 Next Steps:${NC}"
echo "   1. Read documentation:"
echo "      - IMPLEMENTATION_SUMMARY.md"
echo "      - ACTIVITY_PHOTO_DOCUMENTATION.md"
echo "      - ACTIVITY_PHOTO_QUICKSTART.md"
echo ""
echo "   2. Add component to your view:"
echo "      <ActivityPhotoUploader :activity-id=\"activityId\" />"
echo ""
echo "   3. Run tests (optional):"
echo "      php artisan test tests/Feature/ActivityPhotoTest.php"
echo ""
echo "   4. Check API routes:"
echo "      php artisan route:list | grep activity-photos"
echo ""
echo -e "${BLUE}📸 Happy photo uploading!${NC}"
