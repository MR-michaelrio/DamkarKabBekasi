<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityPhotoService;
use Illuminate\Http\Request;

/**
 * Example: Integration with Driver Activities
 * 
 * This controller shows how to integrate activity photo reporting
 * with the existing driver workflow
 */
class DriverActivityController extends Controller
{
    private ActivityPhotoService $photoService;

    public function __construct(ActivityPhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    /**
     * Create activity log for a driver action
     * 
     * Example: When driver completes a dispatch
     * 
     * @param Request $request
     * @return array
     */
    public function logDispatchCompletion(Request $request)
    {
        $validated = $request->validate([
            'dispatch_id' => 'required|integer|exists:dispatches,id',
            'notes' => 'nullable|string|max:500'
        ]);

        // Get authenticated driver/ambulance
        $ambulance = auth('ambulance')->user();

        // Create activity log
        $activity = ActivityLog::create([
            'user_id' => $ambulance->id,
            'action' => 'dispatch_completed',
            'model' => 'Dispatch',
            'model_id' => $validated['dispatch_id'],
            'description' => $validated['notes'] ?? 'Dispatch completed'
        ]);

        // Return activity ID so frontend can upload photos
        return [
            'activity_id' => $activity->id,
            'message' => 'Aktivitas berhasil dicatat. Silakan upload foto laporan.',
            'can_add_photos' => !$this->photoService->hasReachedMaxPhotos($activity),
            'max_photos' => $this->photoService->getMaxPhotosLimit()
        ];
    }

    /**
     * Get activity details with photos
     * 
     * @param int $activityId
     * @return array
     */
    public function getActivityWithPhotos(int $activityId)
    {
        $activity = ActivityLog::with('photos')->findOrFail($activityId);

        return [
            'id' => $activity->id,
            'action' => $activity->action,
            'model' => $activity->model,
            'model_id' => $activity->model_id,
            'description' => $activity->description,
            'photos' => $this->photoService->getActivityPhotos($activity),
            'photo_count' => $activity->photos()->count(),
            'max_photos' => $this->photoService->getMaxPhotosLimit(),
            'created_at' => $activity->created_at
        ];
    }

    /**
     * Generate report from activity with photos
     * 
     * Example: Generate PDF report including all photos
     * 
     * @param int $activityId
     * @return array
     */
    public function generateActivityReport(int $activityId)
    {
        $activity = ActivityLog::with('photos')
            ->with('user')
            ->findOrFail($activityId);

        // Collect data for report
        $reportData = [
            'activity_id' => $activity->id,
            'timestamp' => $activity->created_at,
            'driver_id' => $activity->user_id,
            'driver_name' => $activity->user?->name ?? 'Unknown',
            'action' => $activity->action,
            'description' => $activity->description,
            'photo_count' => $activity->photos()->count(),
            'photos' => $activity->photos->map(fn ($photo) => [
                'url' => $photo->photo_url,
                'name' => $photo->photo_name,
                'description' => $photo->description,
                'taken_at' => $photo->created_at,
                'size' => $this->formatBytes($photo->file_size)
            ])->toArray()
        ];

        return $reportData;
    }

    /**
     * List all activities for current driver
     * 
     * @param Request $request
     * @return array
     */
    public function listDriverActivities(Request $request)
    {
        $ambulance = auth('ambulance')->user();

        $activities = ActivityLog::where('user_id', $ambulance->id)
            ->with('photos')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return [
            'data' => $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'photo_count' => $activity->photos()->count(),
                    'created_at' => $activity->created_at,
                    'has_photos' => $activity->photos()->count() > 0
                ];
            })->toArray(),
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'total_pages' => $activities->lastPage(),
                'total_items' => $activities->total(),
                'per_page' => $activities->perPage()
            ]
        ];
    }

    /**
     * Helper function to format file size
     */
    private function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[(int)$pow];
    }
}
