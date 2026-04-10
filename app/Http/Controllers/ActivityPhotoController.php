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
            $file      = $request->file('file');
            $shortName = $this->generateShortFilename($file) . '.jpg';
            $path      = 'activity-photos/' . $shortName;

            $compressed = $this->compressImageToTarget($file->getRealPath());
            $fileSize   = strlen($compressed);
            Storage::disk('public')->put($path, $compressed);
            $photoUrl   = Storage::disk('public')->url($path);

            // Create ActivityPhoto record
            $photo = ActivityPhoto::create([
                'activity_log_id' => $activityLog->id,
                'photo_path'      => $path,
                'photo_name'      => $shortName,
                'mime_type'       => 'image/jpeg',
                'file_size'       => $fileSize,
                'description'     => $request->input('description'),
                'sequence'        => $request->input('sequence', $photoCount),
            ]);

            return response()->json([
                'success' => true,
                'photo'   => array_merge($photo->toArray(), ['photo_url' => $photoUrl]),
                'message' => 'Foto berhasil diunggah'
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Upload failed: ' . $e->getMessage(), [
                'exception_class' => get_class($e),
                'file'            => $e->getFile(),
                'line'            => $e->getLine(),
                'trace'           => substr($e->getTraceAsString(), 0, 1000),
                'php_version'     => PHP_VERSION,
                'gd_loaded'       => extension_loaded('gd'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah foto: ' . $e->getMessage(),
                'debug'   => [
                    'class' => get_class($e),
                    'file'  => basename($e->getFile()),
                    'line'  => $e->getLine(),
                    'php'   => PHP_VERSION,
                    'gd'    => extension_loaded('gd'),
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
     * Uses temp files (NOT ob_start) to avoid conflicting with Laravel's output buffer.
     */
    private function compressImageToTarget(string $filePath, int $targetBytes = 102400): string
    {
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            throw new \Exception('Gagal membaca informasi gambar: ' . $filePath);
        }

        $srcWidth  = $imageInfo[0];
        $srcHeight = $imageInfo[1];
        $mimeType  = $imageInfo['mime'];

        if ($mimeType === 'image/jpeg' || $mimeType === 'image/jpg') {
            $source = imagecreatefromjpeg($filePath);
        } elseif ($mimeType === 'image/png') {
            $source = imagecreatefrompng($filePath);
        } elseif ($mimeType === 'image/webp') {
            $source = function_exists('imagecreatefromwebp')
                ? imagecreatefromwebp($filePath)
                : imagecreatefromjpeg($filePath);
        } elseif ($mimeType === 'image/gif') {
            $source = imagecreatefromgif($filePath);
        } else {
            $source = @imagecreatefromjpeg($filePath);
        }

        if (!$source) {
            throw new \Exception('Gagal memuat gambar (mime: ' . $mimeType . ')');
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

        // Write to temp file — avoids ob_start conflict with Laravel output buffer
        $tmpFile = tempnam(sys_get_temp_dir(), 'gd_');

        $quality = 60;
        imagejpeg($source, $tmpFile, $quality);
        $compressed  = file_get_contents($tmpFile);
        $currentSize = strlen($compressed);

        $attempts = 0;
        while ($currentSize > $targetBytes && $attempts < 25) {
            $attempts++;
            if ($quality > 10) {
                $quality -= 10;
            } else {
                $newWidth  = (int) ($srcWidth * 0.7);
                $newHeight = (int) ($srcHeight * 0.7);
                if ($newWidth < 50) {
                    break;
                }
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);
                imagedestroy($source);
                $source    = $resized;
                $srcWidth  = $newWidth;
                $srcHeight = $newHeight;
                $quality   = 50;
            }
            imagejpeg($source, $tmpFile, $quality);
            $compressed  = file_get_contents($tmpFile);
            $currentSize = strlen($compressed);
        }

        // Absolute fallback: force 400x400 @q10
        if ($currentSize > $targetBytes) {
            $fallback = imagecreatetruecolor(400, 400);
            imagecopyresampled($fallback, $source, 0, 0, 0, 0, 400, 400, $srcWidth, $srcHeight);
            imagedestroy($source);
            $source = $fallback;
            imagejpeg($source, $tmpFile, 10);
            $compressed = file_get_contents($tmpFile);
        }

        imagedestroy($source);
        @unlink($tmpFile);

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
            'photos'  => $photos,
            'count'   => $photos->count(),
            'max'     => 5
        ]);
    }

    /**
     * Delete a photo
     */
    public function delete(ActivityPhoto $activityPhoto)
    {
        try {
            $path = $activityPhoto->photo_path;

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $activityPhoto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus'
            ]);

        } catch (\Throwable $e) {
            Log::error('Photo delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update photo sequence
     */
    public function updateSequence(Request $request, ActivityPhoto $activityPhoto)
    {
        try {
            $request->validate([
                'sequence' => 'required|integer|min:0|max:4',
            ]);

            $activityPhoto->update(['sequence' => $request->input('sequence')]);

            return response()->json([
                'success' => true,
                'photo'   => $activityPhoto,
                'message' => 'Urutan foto berhasil diperbarui'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
