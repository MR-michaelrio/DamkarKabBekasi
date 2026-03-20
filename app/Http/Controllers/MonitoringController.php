<?php

namespace App\Http\Controllers;

use App\Models\Ambulance;
use App\Models\Dispatch;
use App\Models\PatientRequest;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        return view('monitoring.index');
    }

    public function getData()
    {
        $activeDispatchStatuses = ['assigned', 'enroute_pickup', 'on_scene', 'enroute_destination', 'arrived_destination'];

        // Get active ambulances with GPS data and an active dispatch
        $ambulances = Ambulance::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereHas('dispatches', function($query) use ($activeDispatchStatuses) {
                $query->whereIn('status', $activeDispatchStatuses);
            })
            ->with(['dispatches' => function($query) use ($activeDispatchStatuses) {
                $query->whereIn('status', $activeDispatchStatuses)
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
                    'status' => $ambulance->status,
                    'latitude' => $ambulance->latitude,
                    'longitude' => $ambulance->longitude,
                    'last_update' => $ambulance->last_location_update?->diffForHumans(),
                    'dispatch' => $ambulance->dispatches->first() ? [
                        'patient_name' => $ambulance->dispatches->first()->patient_name,
                        'status' => $ambulance->dispatches->first()->status,
                    ] : null,
                ];
            });

        // Get active dispatches
        $dispatches = Dispatch::whereIn('status', $activeDispatchStatuses)
            ->with(['ambulance', 'driver', 'eventRequest'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($dispatch) {
                $condition = $dispatch->patient_condition;
                if ($dispatch->event_request_id && $dispatch->eventRequest) {
                    $condition = $dispatch->eventRequest->type === 'disaster' 
                        ? 'Event Emergency' 
                        : 'Event Terjadwal';
                }

                return [
                    'id' => $dispatch->id,
                    'patient_name' => $dispatch->patient_name,
                    'patient_condition' => $condition,
                    'status' => $dispatch->status,
                    'ambulance' => $dispatch->ambulance ? $dispatch->ambulance->plate_number : '-',
                    'created_at' => $dispatch->created_at->format('H:i'),
                ];
            });

        // Get recent patient requests (ONLY active ones: pending or dispatched)
        $requests = PatientRequest::whereIn('status', ['pending', 'dispatched'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'patient_name' => $request->patient_name,
                    'service_type' => $request->service_type,
                    'patient_condition' => $request->patient_condition,
                    'status' => $request->status,
                    'request_date' => $request->request_date->format('d/m/Y'),
                ];
            });

        return response()->json([
            'ambulances' => $ambulances,
            'dispatches' => $dispatches,
            'requests' => $requests,
        ]);
    }
}
