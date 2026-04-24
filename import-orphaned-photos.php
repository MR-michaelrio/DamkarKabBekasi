#!/php
<?php
/**
 * Manual Photo Import Script
 * Script ini untuk import foto-foto yang sudah ada di storage ke database
 * Berguna untuk recovery jika database insert gagal saat upload
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ActivityLog;
use App\Models\ActivityPhoto;
use Illuminate\Support\Facades\Storage;

echo "========================================\n";
echo "Photo Import Recovery Script\n";
echo "========================================\n\n";

// Find all photo files in storage
$photoDir = 'storage/app/public/activity-photos';

if (!is_dir($photoDir)) {
    echo "❌ Photo directory not found: $photoDir\n";
    exit(1);
}

$files = [];
$iterator = new RecursiveDirectoryIterator($photoDir);
foreach ($iterator as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $relativePath = str_replace($photoDir . '/', '', $file->getPathname());
        $relativePath = str_replace('storage/app/public/', '', $relativePath);
        $files[] = [
            'full_path' => $file->getPathname(),
            'relative_path' => $relativePath,
            'filename' => $file->getFilename(),
            'size' => $file->getSize(),
        ];
    }
}

echo "Found " . count($files) . " photo files in storage\n\n";

if (count($files) === 0) {
    echo "✓ No orphaned photos found\n";
    exit(0);
}

echo "Files to import:\n";
echo "───────────────────────────────────────────\n";
foreach ($files as $i => $file) {
    echo ($i + 1) . ". " . $file['relative_path'] . " (" . round($file['size'] / 1024, 1) . "KB)\n";
}

echo "\n";

// Try to match photos to activity logs
$imported = 0;
$skipped = 0;
$failed = 0;

foreach ($files as $file) {
    try {
        // Check if already in database
        $existing = ActivityPhoto::where('photo_path', $file['relative_path'])->first();
        if ($existing) {
            echo "⊘ SKIP: Already in database - {$file['relative_path']}\n";
            $skipped++;
            continue;
        }

        // Try to extract activity_log_id from path (if subdirectory structure)
        $pathParts = explode('/', $file['relative_path']);
        $activityLogId = null;

        if (count($pathParts) >= 2 && is_numeric($pathParts[1])) {
            // Subdirectory structure: activity-photos/{activity_log_id}/filename.jpg
            $activityLogId = (int) $pathParts[1];
        } else {
            // Flat structure: activity-photos/filename.jpg
            // Try to find any activity log (take the first one)
            $firstLog = ActivityLog::first();
            if ($firstLog) {
                $activityLogId = $firstLog->id;
            }
        }

        if (!$activityLogId) {
            echo "✗ FAIL: Cannot determine activity_log_id - {$file['relative_path']}\n";
            $failed++;
            continue;
        }

        // Check if activity log exists
        $activityLog = ActivityLog::find($activityLogId);
        if (!$activityLog) {
            echo "✗ FAIL: Activity log not found (id=$activityLogId) - {$file['relative_path']}\n";
            $failed++;
            continue;
        }

        // Get sequence number (count existing photos + 1)
        $sequence = $activityLog->photos()->count() + 1;

        // Create photo record
        $photo = ActivityPhoto::create([
            'activity_log_id' => $activityLogId,
            'photo_path' => $file['relative_path'],
            'photo_name' => $file['filename'],
            'mime_type' => mime_content_type($file['full_path']),
            'file_size' => $file['size'],
            'description' => 'Imported from orphaned storage file',
            'sequence' => $sequence,
        ]);

        echo "✓ IMPORTED: {$file['relative_path']} → ActivityLog #{$activityLogId}\n";
        $imported++;

    } catch (\Exception $e) {
        echo "✗ ERROR: " . $e->getMessage() . " - {$file['relative_path']}\n";
        $failed++;
    }
}

echo "\n========================================\n";
echo "Import Summary:\n";
echo "  ✓ Imported: $imported\n";
echo "  ⊘ Skipped:  $skipped\n";
echo "  ✗ Failed:   $failed\n";
echo "========================================\n\n";

if ($imported > 0) {
    echo "✓ Photos have been imported to database!\n";
    echo "You can now export PDF and photos should appear.\n";
} else {
    echo "⚠ No photos were imported. Check errors above.\n";
}
