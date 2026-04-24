#!/bin/bash
# Debug script to investigate photo upload issue

echo "=========================================="
echo "Photo Upload Debugging Script"
echo "=========================================="
echo ""

# 1. Check logs for upload attempts
echo "🔍 STEP 1: Checking Laravel Logs for Upload Attempts..."
echo "---"
if [ -f storage/logs/laravel.log ]; then
    echo "Last 30 lines of laravel.log:"
    tail -30 storage/logs/laravel.log
    echo ""
else
    echo "Log file not found!"
fi

# 2. Check database connections
echo ""
echo "🔍 STEP 2: Checking Database..."
echo "---"
php -r "
\$mysqli = new mysqli('127.0.0.1:3306', 'root', '', 'damkarkabbekasi', 3306, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');
if (\$mysqli->connect_error) {
    echo 'Database connection error: ' . \$mysqli->connect_error . PHP_EOL;
    exit(1);
}

echo \"✓ Database connected\\n\";
echo \"\\nActivity Photos Records: \" . \$mysqli->query('SELECT COUNT(*) as cnt FROM activity_photos')->fetch_assoc()['cnt'] . \"\\n\";
echo \"Activity Logs Records: \" . \$mysqli->query('SELECT COUNT(*) as cnt FROM activity_logs')->fetch_assoc()['cnt'] . \"\\n\";
echo \"Dispatches Records: \" . \$mysqli->query('SELECT COUNT(*) as cnt FROM dispatches')->fetch_assoc()['cnt'] . \"\\n\";

\$mysqli->close();
"

# 3. Check storage
echo ""
echo "🔍 STEP 3: Checking Storage..."
echo "---"
PHOTO_COUNT=$(find storage/app/public/activity-photos -type f 2>/dev/null | wc -l)
echo "Photo files in storage: $PHOTO_COUNT"
if [ $PHOTO_COUNT -gt 0 ]; then
    echo ""
    echo "Files found:"
    find storage/app/public/activity-photos -type f -exec ls -lh {} \;
fi

# 4. Check permissions
echo ""
echo "🔍 STEP 4: Checking Permissions..."
echo "---"
echo "Storage directory permissions:"
ls -ld storage/app/public/activity-photos

echo ""
echo "Storage disk config check:"
grep -A 10 "'public' =>" config/filesystems.php | head -15

# 5. Check Controller logic
echo ""
echo "🔍 STEP 5: Checking Controller Upload Logic..."
echo "---"
echo "ActivityPhotoController upload method location:"
grep -n "public function upload" app/Http/Controllers/ActivityPhotoController.php

echo ""
echo "=========================================="
echo "DIAGNOSIS COMPLETE"
echo "=========================================="
echo ""
echo "❓ Questions to check:"
echo "1. Did you see any errors when uploading photo?"
echo "2. Does the photo file appear in storage/app/public/activity-photos/?"
echo "3. Is there an activity_log_id associated with the dispatch?"
echo ""
