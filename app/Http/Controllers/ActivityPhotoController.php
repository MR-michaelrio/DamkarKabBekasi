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

            $file      = $request->file('file');
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $shortName = $this->generateShortFilename($file) . '.' . $extension;
            $path      = 'activity-photos/' . $shortName;

            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
            $fileSize = $file->getSize();
            $photoUrl = Storage::disk('public')->url($path);

            // Create ActivityPhoto record
            $photo = ActivityPhoto::create([
                'activity_log_id' => $activityLog->id,
                'photo_path'      => $path,
                'photo_name'      => $shortName,
                'mime_type'       => $file->getMimeType(),
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
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah foto: ' . $e->getMessage(),
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
