#!/bin/bash

# ACTIVITY PHOTO SYSTEM - DEPLOYMENT GUIDE
# Copy sebagai referensi untuk deployment ke production

echo "═══════════════════════════════════════════════════════════"
echo "  Activity Photo System - Deployment Checklist"
echo "═══════════════════════════════════════════════════════════"
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Counters
TOTAL=0
COMPLETED=0

# Function to check item
check_item() {
    local item=$1
    echo -e "${YELLOW}❓ $item${NC}"
    read -p "   ✓ Done? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${GREEN}   ✅ Completed${NC}"
        COMPLETED=$((COMPLETED + 1))
    fi
    TOTAL=$((TOTAL + 1))
}

# Pre-Deployment Checklist
echo -e "${BLUE}📋 PRE-DEPLOYMENT CHECKLIST${NC}"
echo "════════════════════════════════════════"
echo ""

check_item "Git repository up-to-date with all changes"
check_item "Run 'php artisan migrate' on development"
check_item "Test functionality in development environment"
check_item "Backup existing database"
check_item "Backup existing storage folder"

echo ""
echo -e "${BLUE}📁 FILE VERIFICATION${NC}"
echo "════════════════════════════════════════"
echo ""

# Check all created files
files_to_check=(
    "app/Models/ActivityPhoto.php"
    "app/Models/ActivityLog.php"
    "app/Services/ActivityPhotoService.php"
    "app/Http/Controllers/Api/ActivityPhotoController.php"
    "app/Http/Controllers/Driver/DriverActivityController.php"
    "database/migrations/2026_04_08_create_activity_photos_table.php"
    "database/factories/ActivityPhotoFactory.php"
    "resources/js/components/ActivityPhotoUploader.vue"
    "resources/js/components/ActivityPhotoUploaderWeb.vue"
)

echo -e "${BLUE}Checking required files:${NC}"
for file in "${files_to_check[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}  ✅ $file${NC}"
    else
        echo -e "${RED}  ❌ $file${NC}"
    fi
done

echo ""
echo -e "${BLUE}🚀 DEPLOYMENT STEPS${NC}"
echo "════════════════════════════════════════"
echo ""

echo "1. ${BLUE}Push code to repository${NC}"
echo "   git add ."
echo "   git commit -m 'Add activity photo reporting system'"
echo "   git push origin main"
echo ""

echo "2. ${BLUE}SSH into production server${NC}"
echo "   ssh user@production-server"
echo ""

echo "3. ${BLUE}Pull latest code${NC}"
echo "   cd /var/www/damkar-dispatch"
echo "   git pull origin main"
echo ""

echo "4. ${BLUE}Install dependencies (if needed)${NC}"
echo "   composer install --no-dev"
echo "   npm install"
echo ""

echo "5. ${BLUE}Run migrations${NC}"
echo "   php artisan migrate --force"
echo ""

echo "6. ${BLUE}Create storage symlink${NC}"
echo "   php artisan storage:link"
echo ""

echo "7. ${BLUE}Set correct permissions${NC}"
echo "   sudo chown -R www-data:www-data storage/"
echo "   sudo chmod -R 775 storage/"
echo ""

echo "8. ${BLUE}Clear cache${NC}"
echo "   php artisan cache:clear"
echo "   php artisan config:clear"
echo "   php artisan view:clear"
echo ""

echo "9. ${BLUE}Verify deployment${NC}"
echo "   php artisan route:list | grep activity-photos"
echo "   curl http://production-url/api/activities/1/photos"
echo ""

echo "10. ${BLUE}Monitor logs${NC}"
echo "    tail -f storage/logs/laravel.log"
echo ""

echo -e "${BLUE}📊 POST-DEPLOYMENT VERIFICATION${NC}"
echo "════════════════════════════════════════"
echo ""

check_item "API endpoints accessible"
check_item "Storage folder writable"
check_item "Photos can be uploaded"
check_item "Photos can be retrieved"
check_item "Photos can be deleted"

echo ""
echo -e "${BLUE}🔄 ROLLBACK PLAN (If needed)${NC}"
echo "════════════════════════════════════════"
echo ""

echo "If something goes wrong:"
echo ""
echo "1. ${YELLOW}Revert code${NC}"
echo "   git revert <commit-hash>"
echo "   git push origin main"
echo ""
echo "2. ${YELLOW}Rollback database (if migration failed)${NC}"
echo "   php artisan migrate:rollback"
echo ""
echo "3. ${YELLOW}Restore from backup${NC}"
echo "   # Restore database backup"
echo "   # Restore storage backup"
echo ""

echo ""
echo "════════════════════════════════════════"
echo -e "${GREEN}✅ Deployment Checklist Complete${NC}"
echo "Progress: $COMPLETED/$TOTAL items completed"
echo "════════════════════════════════════════"

# Calculate percentage
if [ $TOTAL -gt 0 ]; then
    percentage=$((COMPLETED * 100 / TOTAL))
    echo ""
    echo -e "Overall Progress: ${BLUE}${percentage}%${NC}"
    echo ""
    
    if [ $percentage -eq 100 ]; then
        echo -e "${GREEN}🎉 Ready for production deployment!${NC}"
    else
        echo -e "${YELLOW}⚠️  Complete remaining items before deploying${NC}"
    fi
fi
