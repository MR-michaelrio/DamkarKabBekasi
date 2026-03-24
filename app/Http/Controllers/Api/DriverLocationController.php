<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\Driver;
use App\Models\Dispatch;
use App\Models\DispatchLocationHistory;
use Illuminate\Http\Request;

class DriverLocationController extends Controller
{
    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // Get authenticated ambulance
        $ambulance = auth('ambulance')->user();

        if (!$ambulance) {
            return response()->json([
                'success' => false, 
                'message' => 'Unauthenticated'
            ], 401);
        }
        
        // Update ambulance location
        $ambulance->update([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'last_location_update' => now(),
        ]);

        // Save to location history if on active dispatch
        $activeDispatch = Dispatch::where('ambulance_id', $ambulance->id)
            ->whereIn('status', ['pending', 'on_the_way_scene', 'on_scene', 'on_the_way_kantor_pos'])
            ->first();

        if ($activeDispatch) {
            DispatchLocationHistory::create([
                'dispatch_id' => $activeDispatch->id,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully'
        ]);
    }
}
