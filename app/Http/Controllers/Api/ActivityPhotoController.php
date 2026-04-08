<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ActivityPhoto;
use App\Services\ActivityPhotoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ActivityPhotoController extends Controller
{
    private ActivityPhotoService $photoService;

    public function __construct(ActivityPhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    /**
     * Upload photo for an activity
     * POST /api/activities/{activity_id}/photos
     *
     * @param Request $request
     * @param int $activity_id
     * @return JsonResponse
     */
    public function store(Request $request, int $activity_id): JsonResponse
    {
        // Get authenticated user
        $user = Auth::user() ?? Auth::guard('ambulance')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak terautentikasi'
            ], 401);
        }

        // Validate request
        $validated = $request->validate([
            'photo' => 'required|image|max:5120', // 5MB
            'description' => 'nullable|string|max:255'
        ]);

        try {
            // Get activity log
            $activityLog = ActivityLog::findOrFail($activity_id);

            // Check ownership (optional - customize based on your auth needs)
            if ($activityLog->user_id !== $user->id && $user->id !== Auth::guard('ambulance')->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke aktivitas ini'
                ], 403);
            }

            // Upload photo
            $photo = $this->photoService->uploadPhoto(
                $activityLog,
                $request->file('photo'),
                $validated['description'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diunggah',
                'data' => [
                    'id' => $photo->id,
                    'photo_url' => $photo->photo_url,
                    'photo_name' => $photo->photo_name,
                    'description' => $photo->description,
                    'sequence' => $photo->sequence,
                    'file_size' => $photo->file_size,
                    'created_at' => $photo->created_at,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all photos for an activity
     * GET /api/activities/{activity_id}/photos
     *
     * @param int $activity_id
     * @return JsonResponse
     */
    public function index(int $activity_id): JsonResponse
    {
        try {
            $activityLog = ActivityLog::findOrFail($activity_id);
            
            $photos = $this->photoService->getActivityPhotos($activityLog);
            $maxPhotos = $this->photoService->getMaxPhotosLimit();
            $currentCount = count($photos);

            return response()->json([
                'success' => true,
                'data' => [
                    'photos' => $photos,
                    'total_photos' => $currentCount,
                    'max_photos' => $maxPhotos,
                    'can_add_more' => $currentCount < $maxPhotos,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Delete a photo
     * DELETE /api/photos/{photo_id}
     *
     * @param int $photo_id
     * @return JsonResponse
     */
    public function destroy(int $photo_id): JsonResponse
    {
        try {
            $photo = ActivityPhoto::findOrFail($photo_id);

            // Verify user owns the activity
            $user = Auth::user() ?? Auth::guard('ambulance')->user();
            if ($photo->activityLog->user_id !== $user->id && $user->id !== Auth::guard('ambulance')->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke foto ini'
                ], 403);
            }

            if ($this->photoService->deletePhoto($photo)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto berhasil dihapus'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Foto tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update photo description
     * PATCH /api/photos/{photo_id}
     *
     * @param Request $request
     * @param int $photo_id
     * @return JsonResponse
     */
    public function update(Request $request, int $photo_id): JsonResponse
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255'
        ]);

        try {
            $photo = ActivityPhoto::findOrFail($photo_id);

            // Verify user owns the activity
            $user = Auth::user() ?? Auth::guard('ambulance')->user();
            if ($photo->activityLog->user_id !== $user->id && $user->id !== Auth::guard('ambulance')->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke foto ini'
                ], 403);
            }

            $updatedPhoto = $this->photoService->updatePhotoDescription(
                $photo,
                $validated['description']
            );

            return response()->json([
                'success' => true,
                'message' => 'Deskripsi foto berhasil diperbarui',
                'data' => [
                    'id' => $updatedPhoto->id,
                    'description' => $updatedPhoto->description,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Foto tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Get photo upload status for activity
     * GET /api/activities/{activity_id}/photos/status
     *
     * @param int $activity_id
     * @return JsonResponse
     */
    public function getStatus(int $activity_id): JsonResponse
    {
        try {
            $activityLog = ActivityLog::findOrFail($activity_id);
            
            $photoCount = $activityLog->photos()->count();
            $maxPhotos = $this->photoService->getMaxPhotosLimit();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_photos' => $photoCount,
                    'max_photos' => $maxPhotos,
                    'can_add_more' => $photoCount < $maxPhotos,
                    'remaining_slots' => $maxPhotos - $photoCount,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivitas tidak ditemukan'
            ], 404);
        }
    }
}
