<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\Driver;
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

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully'
        ]);
    }
}
