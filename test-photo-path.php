<?php
// Test script to create sample photo files and verify paths

echo "Creating test photo files...\n";

// Create a small test image (1x1 pixel PNG)
$png = "\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x02\x00\x00\x00\x90wS\xde\x00\x00\x00\x0cIDATx\x9cc\xf8\x0f\x00\x00\x01\x01\x00\x05\x18\x0d\xd3U\x00\x00\x00\x00IEND\xaeB`\x82";

// Create sample 1: activity-photos/S2HF6.jpg (flat structure - your case)
$flatPath = 'storage/app/public/activity-photos/S2HF6.jpg';
if (!is_dir(dirname($flatPath))) {
    mkdir(dirname($flatPath), 0755, true);
}
file_put_contents($flatPath, $png);
echo "✓ Created: " . $flatPath . " (" . filesize($flatPath) . " bytes)\n";

// Create sample 2: activity-photos/1/12_1_2026-04-23.jpg (subdirectory - code structure)
$subdirPath = 'storage/app/public/activity-photos/1/12_1_2026-04-23.jpg';
if (!is_dir(dirname($subdirPath))) {
    mkdir(dirname($subdirPath), 0755, true);
}
file_put_contents($subdirPath, $png);
echo "✓ Created: " . $subdirPath . " (" . filesize($subdirPath) . " bytes)\n";

echo "\n=== Directory Structure ===\n";
system("find storage/app/public/activity-photos -type f -o -type d | sort");

echo "\n=== File Path Tests ===\n";

// Test flat structure
$fullPath1 = realpath($flatPath);
echo "Flat path exists: " . (file_exists($fullPath1) ? '✓ YES' : '✗ NO') . "\n";
echo "  → Absolute: $fullPath1\n";
echo "  → file:// URI: file://$fullPath1\n\n";

// Test subdirectory structure
$fullPath2 = realpath($subdirPath);
echo "Subdir path exists: " . (file_exists($fullPath2) ? '✓ YES' : '✗ NO') . "\n";
echo "  → Absolute: $fullPath2\n";
echo "  → file:// URI: file://$fullPath2\n\n";

// Test public_path logic
echo "=== Testing public_path() Logic (for Blade template) ===\n";
require 'vendor/autoload.php';

// Simulate what the blade template does
$testPhotoPaths = [
    'activity-photos/S2HF6.jpg',
    'activity-photos/1/12_1_2026-04-23.jpg'
];

foreach ($testPhotoPaths as $photosPath) {
    $photoPath = public_path('storage/' . $photosPath);
    $imageSrc = file_exists($photoPath) 
        ? 'file://' . $photoPath 
        : asset('storage/' . $photosPath);
    
    echo "\nDatabase path: $photosPath\n";
    echo "  → public_path result: $photoPath\n";
    echo "  → File exists: " . (file_exists($photoPath) ? 'YES' : 'NO') . "\n";
    echo "  → Image src (for <img>): $imageSrc\n";
}
