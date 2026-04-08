<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\ActivityPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Exception;

class ActivityPhotoService
{
    /**
     * Target file size in bytes (100KB)
     */
    private const TARGET_FILE_SIZE = 100 * 1024;
    /**
     * Maximum number of photos allowed per activity
     */
    private const MAX_PHOTOS = 5;

    /**
     * Maximum file size in bytes (5MB)
     */
    private const MAX_FILE_SIZE = 5 * 1024 * 1024;

    /**
     * Allowed MIME types
     */
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'image/gif',
        'image/webp',
    ];

    /**
     * Storage disk
     */
    private const STORAGE_DISK = 'public';

    /**
     * Storage path
     */
    private const STORAGE_PATH = 'activity-photos';

    /**
     * Upload a photo for an activity
     *
     * @param ActivityLog $activityLog
     * @param UploadedFile $file
     * @param string|null $description
     * @return ActivityPhoto
     * @throws Exception
     */
    public function uploadPhoto(
        ActivityLog $activityLog,
        UploadedFile $file,
        ?string $description = null
    ): ActivityPhoto {
        // Validate photo count
        $photoCount = $activityLog->photos()->count();
        if ($photoCount >= self::MAX_PHOTOS) {
            throw new Exception("Maksimal {$photoCount} foto sudah tercapai. Tidak dapat menambah foto lebih banyak.");
        }

        // Validate file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
        }

        // Validate MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new Exception('Tipe file tidak didukung. Gunakan: JPG, PNG, GIF, WebP.');
        }

        try {
            // Generate unique filename with .jpg extension for standardized compression
            $filename = now()->timestamp . '_' . uniqid() . '.jpg';
            $relativeDir = self::STORAGE_PATH . '/' . $activityLog->id;
            $fullPath = $relativeDir . '/' . $filename;
            
            // Create image manager
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            // Image resizing if it's too large (optional but helps compression)
            if ($image->width() > 1600 || $image->height() > 1600) {
                $image->scale(1600, 1600);
            }

            // Start compression loop
            $quality = 60;
            $encoded = $image->toJpeg($quality);
            $size = strlen($encoded->toBinary());
            
            // Loop to reduce quality/resolution until size is under TARGET_FILE_SIZE or limit reached
            while ($size > self::TARGET_FILE_SIZE && $quality > 10) {
                $quality -= 10;
                $encoded = $image->toJpeg($quality);
                $size = strlen($encoded->toBinary());

                // If quality is already low and still too big, scale down further
                if ($quality <= 30 && $size > self::TARGET_FILE_SIZE) {
                    $image->scale(width: $image->width() * 0.8);
                    $quality = 50; // reset quality slightly to try again at smaller size
                }
            }

            // Ensure directory exists
            if (!Storage::disk(self::STORAGE_DISK)->exists($relativeDir)) {
                Storage::disk(self::STORAGE_DISK)->makeDirectory($relativeDir);
            }

            // Store encoded image
            Storage::disk(self::STORAGE_DISK)->put($fullPath, $encoded->toBinary());

            // Get next sequence number
            $nextSequence = $activityLog->photos()->max('sequence') + 1;

            // Create photo record
            $photo = $activityLog->photos()->create([
                'photo_path' => $fullPath,
                'photo_name' => $file->getClientOriginalName(),
                'mime_type' => 'image/jpeg',
                'file_size' => $size,
                'description' => $description,
                'sequence' => $nextSequence,
            ]);

            return $photo;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete a photo
     *
     * @param ActivityPhoto $photo
     * @return bool
     */
    public function deletePhoto(ActivityPhoto $photo): bool
    {
        try {
            // Delete file from storage
            if (Storage::disk(self::STORAGE_DISK)->exists($photo->photo_path)) {
                Storage::disk(self::STORAGE_DISK)->delete($photo->photo_path);
            }

            // Delete database record
            $photo->delete();

            // Reorder remaining photos
            $this->reorderPhotos($photo->activityLog);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Update photo description
     *
     * @param ActivityPhoto $photo
     * @param string|null $description
     * @return ActivityPhoto
     */
    public function updatePhotoDescription(ActivityPhoto $photo, ?string $description): ActivityPhoto
    {
        $photo->update(['description' => $description]);
        return $photo;
    }

    /**
     * Reorder photos after deletion
     *
     * @param ActivityLog $activityLog
     * @return void
     */
    private function reorderPhotos(ActivityLog $activityLog): void
    {
        $photos = $activityLog->photos()->orderBy('created_at')->get();
        
        foreach ($photos as $index => $photo) {
            $photo->update(['sequence' => $index + 1]);
        }
    }

    /**
     * Get photos for activity with URLs
     *
     * @param ActivityLog $activityLog
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivityPhotos(ActivityLog $activityLog)
    {
        return $activityLog->photos()
            ->orderBy('sequence')
            ->get()
            ->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'photo_url' => $photo->photo_url,
                    'photo_name' => $photo->photo_name,
                    'description' => $photo->description,
                    'sequence' => $photo->sequence,
                    'file_size' => $photo->file_size,
                    'mime_type' => $photo->mime_type,
                    'created_at' => $photo->created_at,
                ];
            });
    }

    /**
     * Get max photos limit
     *
     * @return int
     */
    public function getMaxPhotosLimit(): int
    {
        return self::MAX_PHOTOS;
    }

    /**
     * Check if activity has reached max photos
     *
     * @param ActivityLog $activityLog
     * @return bool
     */
    public function hasReachedMaxPhotos(ActivityLog $activityLog): bool
    {
        return $activityLog->photos()->count() >= self::MAX_PHOTOS;
    }
}
