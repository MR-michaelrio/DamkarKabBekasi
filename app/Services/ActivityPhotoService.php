<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\ActivityPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
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
            // Generate unique filename with .jpg extension
            $filename    = now()->timestamp . '_' . uniqid() . '.jpg';
            $relativeDir = self::STORAGE_PATH . '/' . $activityLog->id;
            $fullPath    = $relativeDir . '/' . $filename;

            // Compress to ~100KB using native PHP GD (no Intervention dependency)
            $compressed  = $this->compressImageToTarget($file->getRealPath(), self::TARGET_FILE_SIZE);
            $currentSize = strlen($compressed);

            // Ensure directory exists
            if (!Storage::disk(self::STORAGE_DISK)->exists($relativeDir)) {
                Storage::disk(self::STORAGE_DISK)->makeDirectory($relativeDir);
            }

            $saved = Storage::disk(self::STORAGE_DISK)->put($fullPath, $compressed);

            if (!$saved) {
                throw new Exception('Gagal menyimpan file ke storage.');
            }

            // Get next sequence number
            $nextSequence = $activityLog->photos()->max('sequence') + 1;

            // Create photo record
            $photo = $activityLog->photos()->create([
                'photo_path' => $fullPath,
                'photo_name' => $file->getClientOriginalName(),
                'mime_type' => 'image/jpeg',
                'file_size' => $currentSize,
                'description' => $description,
                'sequence' => $nextSequence,
            ]);

            return $photo;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Compress image to target byte size using native PHP GD.
     * Works with any PHP version that has GD enabled — no Intervention dependency.
     */
    private function compressImageToTarget(string $filePath, int $targetBytes = 102400): string
    {
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            throw new Exception('Gagal membaca informasi gambar.');
        }

        $srcWidth  = $imageInfo[0];
        $srcHeight = $imageInfo[1];
        $mimeType  = $imageInfo['mime'];

        if ($mimeType === 'image/jpeg' || $mimeType === 'image/jpg') {
            $source = imagecreatefromjpeg($filePath);
        } elseif ($mimeType === 'image/png') {
            $source = imagecreatefrompng($filePath);
        } elseif ($mimeType === 'image/webp') {
            $source = imagecreatefromwebp($filePath);
        } elseif ($mimeType === 'image/gif') {
            $source = imagecreatefromgif($filePath);
        } else {
            $source = imagecreatefromjpeg($filePath);
        }

        if (!$source) {
            throw new Exception('Gagal memuat gambar.');
        }

        // Downscale jika lebih besar dari 800px
        if ($srcWidth > 800 || $srcHeight > 800) {
            $ratio     = min(800 / $srcWidth, 800 / $srcHeight);
            $newWidth  = (int) ($srcWidth * $ratio);
            $newHeight = (int) ($srcHeight * $ratio);
            $resized   = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);
            imagedestroy($source);
            $source    = $resized;
            $srcWidth  = $newWidth;
            $srcHeight = $newHeight;
        }

        // Loop kompresi: kurangi quality sampai di bawah targetBytes
        $quality = 60;
        ob_start();
        imagejpeg($source, null, $quality);
        $compressed  = ob_get_clean();
        $currentSize = strlen($compressed);

        $attempts = 0;
        while ($currentSize > $targetBytes && $attempts < 25) {
            $attempts++;
            if ($quality > 10) {
                $quality -= 10;
            } else {
                $newWidth  = (int) ($srcWidth * 0.7);
                $newHeight = (int) ($srcHeight * 0.7);
                if ($newWidth < 50) break;
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);
                imagedestroy($source);
                $source    = $resized;
                $srcWidth  = $newWidth;
                $srcHeight = $newHeight;
                $quality   = 50;
            }
            ob_start();
            imagejpeg($source, null, $quality);
            $compressed  = ob_get_clean();
            $currentSize = strlen($compressed);
        }

        // Fallback absolut: paksa 400x400 @q10
        if ($currentSize > $targetBytes) {
            $fallback = imagecreatetruecolor(400, 400);
            imagecopyresampled($fallback, $source, 0, 0, 0, 0, 400, 400, $srcWidth, $srcHeight);
            imagedestroy($source);
            $source = $fallback;
            ob_start();
            imagejpeg($source, null, 10);
            $compressed = ob_get_clean();
        }

        imagedestroy($source);
        return $compressed;
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
