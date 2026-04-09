<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ActivityPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ActivityPhotoController extends Controller
{
    /**
     * Upload a new photo to activity log
     */
    public function upload(Request $request, ActivityLog $activityLog)
    {
        $request->validate([
            'file' => 'required|image|max:10240', // 10MB max upload size (will be compressed to ~100KB)
            'sequence' => 'integer|min:0|max:4',
            'description' => 'string|nullable|max:500',
        ]);

        try {
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

            // Compress image to ~100KB using Intervention Image
            $file = $request->file('file');
            $shortName = $this->generateShortFilename($file) . '.jpg';
            $path = 'activity-photos/' . $shortName;

            $targetSize = 100 * 1024; // 100KB in bytes

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            // Downscale if larger than 800px on any dimension
            if ($image->width() > 800 || $image->height() > 800) {
                $image->scale(800, 800);
            }

            // Compression loop: reduce quality until under 100KB
            $quality = 60;
            $encoded = $image->toJpeg($quality);
            $currentSize = strlen($encoded->toBinary());

            $attempts = 0;
            while ($currentSize > $targetSize && $attempts < 25) {
                $attempts++;

                if ($quality > 10) {
                    $quality -= 10;
                } else {
                    // Shrink dimensions if quality is already at minimum
                    $currentWidth = $image->width();
                    $newWidth = (int) ($currentWidth * 0.7);
                    if ($newWidth < 50) break;
                    $image->scale(width: $newWidth);
                    $quality = 50;
                }

                $encoded = $image->toJpeg($quality);
                $currentSize = strlen($encoded->toBinary());
            }

            // Absolute fallback: force very small size
            if ($currentSize > $targetSize) {
                $image->scale(400, 400);
                $encoded = $image->toJpeg(10);
                $currentSize = strlen($encoded->toBinary());
            }

            // Save compressed image
            Storage::disk('public')->put($path, $encoded->toBinary());
            $fileSize = $currentSize;
            $photoUrl = Storage::disk('public')->url($path);

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

        } catch (\Exception $e) {
            Log::error('Upload failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah foto: ' . $e->getMessage(),
                'debug' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Generate a short 5-character filename
     */
    private function generateShortFilename($file)
    {
        // Generate random 5 character alphanumeric string
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $name = '';
        for ($i = 0; $i < 5; $i++) {
            $name .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $name;
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
