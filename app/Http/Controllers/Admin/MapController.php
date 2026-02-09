<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;

class MapController extends Controller
{
    public function index()
    {
        // Get all ambulances with location data
        $ambulances = Ambulance::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['dispatches' => function($query) {
                $query->whereIn('status', ['assigned', 'enroute_pickup', 'on_scene', 'enroute_destination', 'arrived_destination'])
                      ->latest()
                      ->limit(1);
            }])
            ->get();

        return view('admin.maps.index', compact('ambulances'));
    }

    public function getAmbulances()
    {
        // API endpoint for real-time updates
        $ambulances = Ambulance::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['dispatches' => function($query) {
                $query->whereIn('status', ['assigned', 'enroute_pickup', 'on_scene', 'enroute_destination', 'arrived_destination'])
                      ->latest()
                      ->limit(1);
            }])
            ->get()
            ->map(function($ambulance) {
                return [
                    'id' => $ambulance->id,
                    'plate_number' => $ambulance->plate_number,
                    'code' => $ambulance->code,
                    'type' => $ambulance->type,
                    'latitude' => $ambulance->latitude,
                    'longitude' => $ambulance->longitude,
                    'status' => $ambulance->status,
                    'last_update' => $ambulance->last_location_update?->diffForHumans(),
                    'dispatch' => $ambulance->dispatches->first() ? [
                        'patient_name' => $ambulance->dispatches->first()->patient_name,
                        'status' => $ambulance->dispatches->first()->status,
                        'pickup_address' => $ambulance->dispatches->first()->pickup_address,
                        'destination' => $ambulance->dispatches->first()->destination,
                    ] : null,
                ];
            });

        return response()->json($ambulances);
    }
}
