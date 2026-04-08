<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ActivityPhotoService;
use App\Models\ActivityLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ActivityPhotoCompressionTest extends TestCase
{
    /**
     * Set up tests
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Since ActivityLog factory uses Faker, ensure we have dummy DB or use RefreshDatabase
        // For a simple Unit test, maybe mocking is better, but since it's using model create, we use DB.
    }

    public function test_image_is_compressed_towards_100kb()
    {
        Storage::fake('public');

        // Create a large dummy image manually with GD
        $width = 2000;
        $height = 2000;
        $resource = imagecreatetruecolor($width, $height);
        $tempPath = tempnam(sys_get_temp_dir(), 'test_img') . '.jpg';
        imagejpeg($resource, $tempPath, 100);
        imagedestroy($resource);
        
        $file = new UploadedFile(
            $tempPath,
            'large_image.jpg',
            'image/jpeg',
            null,
            true
        );

        // Mock ActivityLog
        $activityLog = \Mockery::mock(ActivityLog::class);
        $activityLog->shouldReceive('getAttribute')->with('id')->andReturn(1);
        
        $photosRelation = \Mockery::mock('photos');
        $photosRelation->shouldReceive('count')->andReturn(0);
        $photosRelation->shouldReceive('max')->with('sequence')->andReturn(0);
        
        // Mock the creation of the photo record
        $photoMock = new ActivityPhoto([
            'photo_path' => 'activity-photos/1/test.jpg',
            'photo_name' => 'large_image.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 50000,
        ]);
        $photosRelation->shouldReceive('create')->andReturn($photoMock);
        
        $activityLog->shouldReceive('photos')->andReturn($photosRelation);
        
        $service = new ActivityPhotoService();
        $photo = $service->uploadPhoto($activityLog, $file, 'Compressed Test');

        // Verify photo record exists
        $this->assertNotNull($photo);
        
        // Assert storage
        Storage::disk('public')->assertExists($photo->photo_path);
        
        $actualSize = Storage::disk('public')->size($photo->photo_path);
        // We expect it to be less than TARGET_FILE_SIZE (100 * 1024)
        $this->assertLessThanOrEqual(100 * 1024, $actualSize, "Image size should be under 100KB");

        @unlink($tempPath);
    }
}
