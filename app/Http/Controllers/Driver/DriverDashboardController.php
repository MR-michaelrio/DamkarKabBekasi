<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use Illuminate\Http\Request;

class DriverDashboardController extends Controller
{
    public function index()
    {
        // Get authenticated ambulance
        $ambulance = auth('ambulance')->user();
        
        // Get active dispatch for this ambulance
        $activeDispatch = Dispatch::where('ambulance_id', $ambulance->id)
            ->whereIn('status', ['assigned', 'enroute_pickup', 'on_scene', 'enroute_destination', 'arrived_destination'])
            ->first();

        return view('driver.dashboard', compact('activeDispatch', 'ambulance'));
    }

    public function updateStatus(Request $request, Dispatch $dispatch)
    {
        // Security check: ensure this dispatch belongs to the authenticated ambulance
        if ($dispatch->ambulance_id !== auth('ambulance')->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $flow = [
            'assigned' => 'enroute_pickup',
            'enroute_pickup' => 'on_scene',
            'on_scene' => 'enroute_destination',
            'enroute_destination' => 'arrived_destination',
            'arrived_destination' => 'completed',
        ];

        if (!isset($flow[$dispatch->status])) {
            return response()->json(['success' => false, 'message' => 'Invalid status transition'], 400);
        }

        $newStatus = $flow[$dispatch->status];
        
        $updateData = ['status' => $newStatus];
        
        // Dynamic timestamps based on status
        if ($newStatus === 'enroute_pickup') {
            // Optional: record start time if needed
        } elseif ($newStatus === 'on_scene') {
            $updateData['pickup_at'] = now();
        } elseif ($newStatus === 'arrived_destination') {
            // Using existing hospital_at for generic destination arrival if needed, 
            // or just rely on logs. For now let's keep it simple.
            $updateData['hospital_at'] = now(); 
        } elseif ($newStatus === 'completed') {
            $updateData['completed_at'] = now();
            
            // Free up ambulance and driver, and clear location
            $dispatch->ambulance->update([
                'status' => 'ready',
                'latitude' => null,
                'longitude' => null,
                'last_location_update' => null
            ]);
            $dispatch->driver->update(['status' => 'available']);

            // Sync PatientRequest status if exists
            \App\Models\PatientRequest::where('dispatch_id', $dispatch->id)
                ->update(['status' => 'completed']);
        }

        $dispatch->update($updateData);

        // Log the change
        \App\Models\DispatchLog::create([
            'dispatch_id' => $dispatch->id,
            'status' => $newStatus,
            'note' => 'Status diupdate oleh driver'
        ]);

        return response()->json([
            'success' => true,
            'new_status' => $newStatus,
            'message' => 'Status updated successfully'
        ]);
    }

    public function togglePause(Request $request, Dispatch $dispatch)
    {
        // Security check
        if ($dispatch->ambulance_id !== auth('ambulance')->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $dispatch->is_paused = !$dispatch->is_paused;
        $dispatch->save();

        // Log the change
        \App\Models\DispatchLog::create([
            'dispatch_id' => $dispatch->id,
            'status' => $dispatch->status,
            'note' => $dispatch->is_paused ? 'Driver sedang istirahat (Pause)' : 'Driver melanjutkan perjalanan (Resume)'
        ]);

        return response()->json([
            'success' => true,
            'is_paused' => $dispatch->is_paused,
            'message' => $dispatch->is_paused ? 'Perjalanan diistirahatkan' : 'Perjalanan dilanjutkan'
        ]);
    }
}
