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
        $request->validate([
            'file' => 'required|image|max:10240', // 10MB
            'sequence' => 'integer|min:0|max:4',
            'description' => 'string|nullable|max:500',
        ]);

        try {
            // Ensure directory exists
            if (!Storage::disk('public')->exists('activity-photos')) {
                Storage::disk('public')->makeDirectory('activity-photos');
            }

            // Check if we\'ve reached the max photo limit (5 photos)
            $photoCount = $activityLog->photos()->count();
            if ($photoCount >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimum 5 foto per aktivitas'
                ], 422);
            }

            // Compress and Resize the image using pure PHP (GD)
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $shortName = $this->generateShortFilename($file) . '.' . $extension;
            $path = 'activity-photos/' . $shortName;

            // Load original image
            $sourcePath = $file->getRealPath();
            $imageInfo = @getimagesize($sourcePath);
            if (!$imageInfo) {
                throw new \Exception("Gagal membaca informasi gambar dari file: " . $sourcePath);
            }
            list($width, $height, $type) = $imageInfo;

            // Resize if too large (Max 1200px)
            $maxDim = 1200;
            $newWidth = $width;
            $newHeight = $height;
            if ($width > $maxDim || $height > $maxDim) {
                $ratio = $width / $height;
                if ($ratio > 1) {
                    $newWidth = $maxDim;
                    $newHeight = $maxDim / $ratio;
                } else {
                    $newWidth = $maxDim * $ratio;
                    $newHeight = $maxDim;
                }
            }

            // Create canvas
            $imageResource = null;
            try {
                switch ($type) {
                    case IMAGETYPE_JPEG: $imageResource = @imagecreatefromjpeg($sourcePath); break;
                    case IMAGETYPE_PNG: $imageResource = @imagecreatefrompng($sourcePath); break;
                    case IMAGETYPE_GIF: $imageResource = @imagecreatefromgif($sourcePath); break;
                    case IMAGETYPE_WEBP: $imageResource = @imagecreatefromwebp($sourcePath); break;
                    default:
                        Log::warning("Unsupported image type: " . $type . " for file " . $sourcePath);
                }
            } catch (\Throwable $e) {
                Log::error("GD Error creating resource: " . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }

            $fileSize = 0;
            if ($imageResource) {
                try {
                    $newImage = imagecreatetruecolor($newWidth, $newHeight);
                    
                    // Keep transparency for PNG
                    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
                        imagealphablending($newImage, false);
                        imagesavealpha($newImage, true);
                    }

                    imagecopyresampled($newImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    // Save to buffer with compression (Quality 60 for ~100kb target)
                    ob_start();
                    $success = @imagejpeg($newImage, null, 60);
                    $compressedData = ob_get_clean();

                    if ($success && !empty($compressedData)) {
                        // Save to Storage
                        Storage::disk('public')->put($path, $compressedData);
                        $fileSize = strlen($compressedData);
                    } else {
                        Log::warning("imagejpeg failed, falling back to original file upload");
                        Storage::disk('public')->putFileAs('activity-photos', $file, $shortName);
                        $fileSize = $file->getSize();
                    }

                    // Free memory
                    imagedestroy($imageResource);
                    imagedestroy($newImage);
                } catch (\Throwable $e) {
                    Log::error("GD processing failed mid-way: " . $e->getMessage(), [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    Storage::disk('public')->putFileAs('activity-photos', $file, $shortName);
                    $fileSize = $file->getSize();
                }
            } else {
                // Fallback to original if GD fails
                Log::warning("GD failed to load image resource, using original file upload as fallback");
                Storage::disk('public')->putFileAs('activity-photos', $file, $shortName);
                $fileSize = $file->getSize();
            }
            
            $photoUrl = Storage::disk('public')->url($path);

            // Create ActivityPhoto record
            $photo = ActivityPhoto::create([
                'activity_log_id' => $activityLog->id,
                'photo_path' => $path,
                'photo_name' => $shortName,
                'mime_type' => 'image/jpeg', // We converted to jpeg for best compression
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
