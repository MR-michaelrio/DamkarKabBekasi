<?php
/**
 * Quick Photo Recovery Command
 * Run this to import orphaned photos from storage to database
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ActivityLog;
use App\Models\ActivityPhoto;

echo "\n";
echo "==========================================\n";
echo "Photo Recovery Tool\n";
echo "==========================================\n\n";

// Get first activity log
if (!ActivityLog::exists()) {
    echo "❌ ERROR: No activity logs found in database!\n";
    echo "   Cannot import photos without an activity log.\n";
    echo "   Please create a dispatch first.\n\n";
    exit(1);
}

$activityLog = ActivityLog::first();
echo "Using ActivityLog #" . $activityLog->id;
echo " (Model: " . $activityLog->model . ")\n\n";

// Check orphaned files
$photoDir = 'storage/app/public/activity-photos';
$files = [];

if (is_dir($photoDir)) {
    $iterator = new RecursiveDirectoryIterator($photoDir);
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $relative = str_replace($photoDir . '/', '', $file->getPathname());
            $files[] = [
                'path' => $relative,
                'size' => $file->getSize(),
                'full' => $file->getPathname(),
            ];
        }
    }
}

echo "Files in storage: " . count($files) . "\n";

if (count($files) === 0) {
    echo "✓ No photos to import.\n\n";
    exit(0);
}

echo "\n✓ Attempting to import orphaned photos...\n\n";

$imported = 0;
$skipped = 0;
$failed = 0;

foreach ($files as $file) {
    // Skip if already imported
    if (ActivityPhoto::where('photo_path', $file['path'])->exists()) {
        echo "⊘ SKIP: Already in DB - {$file['path']}\n";
        $skipped++;
        continue;
    }
    
    try {
        $mimeType = 'image/jpeg';
        if (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($file['full']);
        } elseif (function_exists('finfo_file')) {
            $mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file['full']);
        }
        
        $sequence = ActivityPhoto::where('activity_log_id', $activityLog->id)->count() + 1;
        
        $photo = ActivityPhoto::create([
            'activity_log_id' => $activityLog->id,
            'photo_path' => $file['path'],
            'photo_name' => basename($file['path']),
            'mime_type' => $mimeType,
            'file_size' => $file['size'],
            'description' => 'Imported from storage',
            'sequence' => $sequence,
        ]);
        
        $sizeKb = round($file['size'] / 1024, 1);
        echo "✓ IMPORTED: {$file['path']} ({$sizeKb}KB) → Photo ID #{$photo->id}\n";
        $imported++;
    } catch (\Exception $e) {
        echo "✗ FAILED: {$file['path']}\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n==========================================\n";
echo "Recovery Summary:\n";
echo "  ✓ Imported: $imported\n";
echo "  ⊘ Skipped:  $skipped\n";
echo "  ✗ Failed:   $failed\n";
echo "==========================================\n\n";

if ($imported > 0) {
    echo "✓ SUCCESS! Photos have been imported to database.\n\n";
    echo "Next steps:\n";
    echo "  1. Go to Admin Dashboard → Dispatches\n";
    echo "  2. Select a dispatch\n";
    echo "  3. Click 'Export PDF' or 'Unduh Laporan'\n";
    echo "  4. Check halaman 3a/3b - photos should appear!\n\n";
} else {
    echo "⚠ WARNING: No photos were imported.\n";
    echo "Please check:\n";
    echo "  1. Are there files in storage/app/public/activity-photos/?\n";
    echo "  2. Are they valid image files (.jpg, .png, etc)?\n";
    echo "  3. Check logs: tail -50 storage/logs/laravel.log\n\n";
}
