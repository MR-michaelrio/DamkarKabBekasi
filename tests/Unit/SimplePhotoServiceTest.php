<?php

use Tests\TestCase;
use App\Services\ActivityPhotoService;
use App\Models\ActivityLog;
use App\Models\ActivityPhoto;
use Illuminate\Support\Facades\Storage;

uses(TestCase::class);

test('compression_logic', function () {
    Storage::fake('public');
    
    // Create 2000x2000 noise image
    $width = 2000;
    $height = 2000;
    $img = imagecreatetruecolor($width, $height);
    for ($i = 0; $i < 1000; $i++) {
        imagesetpixel($img, rand(0, $width-1), rand(0, $height-1), rand(0, 0xFFFFFF));
    }
    $tmpFile = tempnam(sys_get_temp_dir(), 'test_') . '.jpg';
    imagejpeg($img, $tmpFile, 100);
    imagedestroy($img);

    $file = new \Illuminate\Http\UploadedFile($tmpFile, 'test.jpg', 'image/jpeg', null, true);

    // Mock ActivityLog
    $activityLog = \Mockery::mock(ActivityLog::class);
    $activityLog->shouldReceive('getAttribute')->with('id')->andReturn(1);
    $photosRelation = \Mockery::mock('photos');
    $photosRelation->shouldReceive('count')->andReturn(0);
    $photosRelation->shouldReceive('max')->with('sequence')->andReturn(0);
    
    $photoMock = new ActivityPhoto([
        'photo_path' => 'activity-photos/1/test.jpg',
        'file_size' => 50000,
    ]);
    $photosRelation->shouldReceive('create')->andReturn($photoMock);
    $activityLog->shouldReceive('photos')->andReturn($photosRelation);

    $service = new ActivityPhotoService();
    try {
        $photo = $service->uploadPhoto($activityLog, $file, 'Test description');
        expect($photo)->not()->toBeNull();
        
        $savedPath = $photo->photo_path;
        expect(Storage::disk('public')->exists($savedPath))->toBeTrue();
        $actualSize = Storage::disk('public')->size($savedPath);
        echo "\nActual Size: " . ($actualSize / 1024) . " KB\n";
        expect($actualSize)->toBeLessThanOrEqual(100 * 1024);
    } catch (\Exception $e) {
        echo "\nError: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
        throw $e;
    }

    @unlink($tmpFile);
});
