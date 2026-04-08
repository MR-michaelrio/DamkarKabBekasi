<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ActivityPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            // Check if we've reached the max photo limit (5 photos)
            $photoCount = $activityLog->photos()->count();
            if ($photoCount >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimum 5 foto per aktivitas'
                ], 422);
            }

            // Store the uploaded file
            $file = $request->file('file');
            $path = Storage::disk('public')->put('activity-photos', $file);
            $photoUrl = Storage::disk('public')->url($path);

            // Create ActivityPhoto record
            $photo = ActivityPhoto::create([
                'activity_log_id' => $activityLog->id,
                'photo_path' => $path,
                'photo_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'description' => $request->input('description'),
                'sequence' => $request->input('sequence', $photoCount),
            ]);

            return response()->json([
                'success' => true,
                'photo' => array_merge($photo->toArray(), ['photo_url' => $photoUrl]),
                'message' => 'Foto berhasil diunggah'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah foto: ' . $e->getMessage()
            ], 500);
        }
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
    public function delete(ActivityPhoto $photo)
    {
        try {
            // Delete from storage
            if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }

            // Delete database record
            $photo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update photo sequence
     */
    public function updateSequence(Request $request, ActivityPhoto $photo)
    {
        $request->validate([
            'sequence' => 'required|integer|min:0|max:4',
        ]);

        try {
            $photo->update(['sequence' => $request->input('sequence')]);

            return response()->json([
                'success' => true,
                'photo' => $photo,
                'message' => 'Urutan foto berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan: ' . $e->getMessage()
            ], 500);
        }
    }
}
