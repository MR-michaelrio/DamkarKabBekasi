<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ActivityPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class ActivityPhotoController extends Controller
{
    /**
     * Upload a new photo to activity log
     */
    public function upload(Request $request, ActivityLog $activityLog)
    {
        // Pastikan GD extension tersedia
        if (!function_exists('imagejpeg')) {
            return response()->json([
                'success' => false,
                'message' => 'PHP GD extension tidak tersedia di server ini.'
            ], 500);
        }

        try {
            $request->validate([
                'file' => 'required|image|max:10240',
                'sequence' => 'integer|min:0|max:4',
                'description' => 'string|nullable|max:500',
            ]);

            // Ensure directory exists
            if (!Storage::disk('public')->exists('activity-photos')) {
                Storage::disk('public')->makeDirectory('activity-photos');
            }

            // Check if we've reached the max photo limit (5 photos)
            $photoCount = $activityLog->photos()->count();
            if ($photoCount >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimum 5 foto per aktivitas'
                ], 422);
            }

            // Compress image to ~100KB using native PHP GD
            $file = $request->file('file');
            $shortName = $this->generateShortFilename($file) . '.jpg';
            $path = 'activity-photos/' . $shortName;

            $compressed = $this->compressImageToTarget($file->getRealPath());
            $fileSize   = strlen($compressed);
            Storage::disk('public')->put($path, $compressed);
            $photoUrl   = Storage::disk('public')->url($path);

            // Create ActivityPhoto record
            $photo = ActivityPhoto::create([
                'activity_log_id' => $activityLog->id,
                'photo_path' => $path,
                'photo_name' => $shortName,
                'mime_type' => 'image/jpeg',
                'file_size' => $fileSize,
                'description' => $request->input('description'),
                'sequence' => $request->input('sequence', $photoCount),
            ]);

            return response()->json([
                'success' => true,
                'photo' => array_merge($photo->toArray(), ['photo_url' => $photoUrl]),
                'message' => 'Foto berhasil diunggah'
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Upload failed: ' . $e->getMessage(), [
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 1000),
                'php_version' => PHP_VERSION,
                'gd_loaded' => extension_loaded('gd'),
                'gd_info' => function_exists('gd_info') ? gd_info() : 'N/A',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah foto: ' . $e->getMessage(),
                'debug' => [
                    'class' => get_class($e),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    'php' => PHP_VERSION,
                    'gd' => extension_loaded('gd'),
                ]
            ], 500);
        }
    }

    /**
     * Generate a short 5-character filename
     */
    private function generateShortFilename($file): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $name = '';
        for ($i = 0; $i < 5; $i++) {
            $name .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $name;
    }

    /**
     * Compress image to ~100KB using native PHP GD.
     * Does not depend on any Intervention Image version.
     */
    private function compressImageToTarget(string $filePath, int $targetBytes = 102400): string
    {
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            throw new \Exception('Gagal membaca informasi gambar.');
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
            throw new \Exception('Gagal memuat gambar.');
        }

        // Downscale if larger than 800px on any dimension
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

        // Compression loop: reduce quality until under targetBytes
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

        // Absolute fallback: force 400x400 @q10
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
     * Get all photos for an activity log
     */
    public function list(ActivityLog $activityLog)
    {
        $photos = $activityLog->photos()
            ->orderBy('sequence')
            ->get();

        return response()->json([
            'success' => true,
            'photos' => $photos,
            'count' => $photos->count(),
            'max' => 5
        ]);
    }

    /**
     * Delete a photo
     */
    public function delete(ActivityPhoto $activityPhoto)
    {
        try {
            $path = $activityPhoto->photo_path;
            
            // Delete from storage
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            // Delete database record
            $deleted = $activityPhoto->delete();
            
            Log::info('Photo deleted', [
                'id' => $activityPhoto->id,
                'deleted' => $deleted,
                'path' => $path
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Photo delete error: ' . $e->getMessage(), [
                'id' => $activityPhoto->id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage(),
                'debug' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Update photo sequence
     */
    public function updateSequence(Request $request, ActivityPhoto $activityPhoto)
    {
        $request->validate([
            'sequence' => 'required|integer|min:0|max:4',
        ]);

        try {
            $activityPhoto->update(['sequence' => $request->input('sequence')]);

            return response()->json([
                'success' => true,
                'photo' => $activityPhoto,
                'message' => 'Urutan foto berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            Log::error('Update sequence error: ' . $e->getMessage(), [
                'id' => $activityPhoto->id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan: ' . $e->getMessage(),
                'debug' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
}
