#!/bin/bash
# Test script for Photo PDF Export Functionality
# Usage: ./test-photo-pdf.sh [dispatch_id]

set -e

DISPATCH_ID=${1:-1}
LOG_FILE="storage/logs/laravel.log"
PHOTO_DIR="storage/app/public/activity-photos"

echo "=========================================="
echo "Photo PDF Export - Test Script"
echo "=========================================="
echo ""

# Test 1: Check Database Status
echo "📊 TEST 1: Checking Database Status..."
echo "---"

PHOTO_COUNT=$(php -r "
\$mysqli = new mysqli('127.0.0.1:3306', 'root', '', 'damkarkabbekasi', 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
\$result = \$mysqli->query('SELECT COUNT(*) as cnt FROM activity_photos');
\$row = \$result->fetch_assoc();
echo \$row['cnt'];
\$mysqli->close();
")

ACTIVITY_LOG_COUNT=$(php -r "
\$mysqli = new mysqli('127.0.0.1:3306', 'root', '', 'damkarkabbekasi', 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
\$result = \$mysqli->query('SELECT COUNT(*) as cnt FROM activity_logs WHERE model = \"Dispatch\"');
\$row = \$result->fetch_assoc();
echo \$row['cnt'];
\$mysqli->close();
")

DISPATCH_EXISTS=$(php -r "
\$mysqli = new mysqli('127.0.0.1:3306', 'root', '', 'damkarkabbekasi', 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
\$result = \$mysqli->query('SELECT COUNT(*) as cnt FROM dispatches WHERE id = $DISPATCH_ID');
\$row = \$result->fetch_assoc();
echo \$row['cnt'];
\$mysqli->close();
")

echo "  Total activity photos: $PHOTO_COUNT"
echo "  Activity logs (Dispatch): $ACTIVITY_LOG_COUNT"
echo "  Dispatch #$DISPATCH_ID exists: $([ $DISPATCH_EXISTS -eq 1 ] && echo 'YES ✓' || echo 'NO ✗')"
echo ""

# Test 2: Check Storage
echo "📁 TEST 2: Checking Storage..."
echo "---"

if [ -L public/storage ]; then
    echo "  Storage symlink: EXISTS ✓"
else
    echo "  Storage symlink: MISSING ✗"
fi

if [ -d "$PHOTO_DIR" ]; then
    echo "  Activity photos directory: EXISTS ✓"
    PHOTO_FILE_COUNT=$(find "$PHOTO_DIR" -type f 2>/dev/null | wc -l)
    echo "  Photo files stored: $PHOTO_FILE_COUNT"
    if [ $PHOTO_FILE_COUNT -gt 0 ]; then
        echo "  First 3 files:"
        find "$PHOTO_DIR" -type f | head -3 | sed 's/^/    - /'
    fi
else
    echo "  Activity photos directory: MISSING (will be created on first upload)"
fi
echo ""

# Test 3: Check Code Changes
echo "🔧 TEST 3: Checking Code Fixes..."
echo "---"

# Check if file:// protocol is used in blade template
if grep -q 'file://' "resources/views/admin/reports/kebakaran_pdf.blade.php"; then
    echo "  Image file:// protocol fix: APPLIED ✓"
else
    echo "  Image file:// protocol fix: NOT FOUND ✗"
fi

# Check if debug logging is added
if grep -q "PDF Export - Kebakaran Report" "app/Http/Controllers/Admin/DispatchController.php"; then
    echo "  Debug logging addition: APPLIED ✓"
else
    echo "  Debug logging addition: NOT FOUND ✗"
fi
echo ""

# Test 4: Check Recent Logs
echo "📋 TEST 4: Recent PDF Export Logs..."
echo "---"

if [ -f "$LOG_FILE" ]; then
    PDF_EXPORTS=$(grep -c "PDF Export" "$LOG_FILE" || true)
    if [ $PDF_EXPORTS -gt 0 ]; then
        echo "  PDF export attempts: $PDF_EXPORTS"
        echo "  Last 5 export logs:"
        grep "PDF Export" "$LOG_FILE" | tail -5 | sed 's/^/    /'
    else
        echo "  No PDF export logs found yet (will appear after first export)"
    fi
else
    echo "  Log file not found: $LOG_FILE"
fi
echo ""

# Test 5: Show Next Steps
echo "✅ TEST SUMMARY"
echo "=========================================="
if [ $DISPATCH_EXISTS -eq 1 ]; then
    if [ $PHOTO_COUNT -eq 0 ]; then
        echo "Status: READY FOR TESTING"
        echo ""
        echo "Next Steps:"
        echo "1. Upload photos to Dispatch #$DISPATCH_ID"
        echo "2. Run: php artisan log-export-pdf $DISPATCH_ID"
        echo "3. Check logs: tail -50 storage/logs/laravel.log"
        echo "4. Check if photo pages (3a/3b) appear in PDF"
    else
        echo "Status: PHOTOS FOUND! Ready to test export"
        echo ""
        echo "Next Steps:"
        echo "1. Access admin dashboard and login"
        echo "2. Navigate to Dispatches menu"
        echo "3. Click 'Export PDF' for Dispatch #1"
        echo "4. Verify photo pages appear in downloaded PDF"
        echo "5. Check logs: tail -f storage/logs/laravel.log"
    fi
else
    echo "Status: ERROR - Dispatch #$DISPATCH_ID not found"
    echo ""
    echo "Next Steps:"
    echo "1. Create a new dispatch in admin dashboard"
    echo "2. Or re-run script with valid dispatch ID: ./test-photo-pdf.sh 12"
fi
echo "=========================================="
