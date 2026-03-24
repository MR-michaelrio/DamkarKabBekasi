<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use App\Models\Driver;
use App\Models\PatientRequest;
use App\Models\DispatchLog;
use Illuminate\Http\Request;

class DriverDashboardController extends Controller
{
    public function index()
    {
        // Get authenticated ambulance
        $ambulance = auth('ambulance')->user();
        
        // Get active dispatch for this ambulance
        $activeDispatch = Dispatch::where('ambulance_id', $ambulance->id)
            ->whereIn('status', ['assigned', 'enroute_pickup', 'on_scene', 'enroute_destination', 'arrived_destination', 'enroute_return', 'arrived_return'])
            ->first();

        return view('driver.dashboard', compact('activeDispatch', 'ambulance'));
    }

    public function updateStatus(Request $request, Dispatch $dispatch)
    {
        // Security check: ensure this dispatch belongs to the authenticated ambulance
        if ($dispatch->ambulance_id !== auth('ambulance')->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Event dispatch uses a simplified 4-step flow (no hospital leg)
        if ($dispatch->event_request_id) {
            $flow = [
                'assigned'       => 'enroute_pickup',
                'enroute_pickup' => 'on_scene',
                'on_scene'       => 'enroute_return',
                'enroute_return' => 'completed',
            ];
        } else {
            // Regular patient / jenazah dispatch
            $flow = [
                'assigned'            => 'enroute_pickup',
                'enroute_pickup'      => 'on_scene',
                'on_scene'            => 'enroute_destination',
                'enroute_destination' => 'arrived_destination',
                'arrived_destination' => 'completed',
                'enroute_return'      => 'arrived_return',
                'arrived_return'      => 'completed',
            ];
        }

        if (!isset($flow[$dispatch->status])) {
            return response()->json(['success' => false, 'message' => 'Invalid status transition'], 400);
        }

        $newStatus = $flow[$dispatch->status];

        // Round trip logic only for regular dispatches
        if (!$dispatch->event_request_id
            && $dispatch->status === 'arrived_destination'
            && $dispatch->trip_type === 'round_trip') {
            $newStatus = 'enroute_return';
        }

        $updateData = ['status' => $newStatus];

        // Dynamic timestamps
        if ($newStatus === 'on_scene') {
            $updateData['pickup_at'] = now();
        } elseif ($newStatus === 'arrived_destination') {
            $updateData['hospital_at'] = now();
        } elseif ($newStatus === 'enroute_return') {
            $updateData['hospital_at'] = now();
        } elseif ($newStatus === 'completed') {
            $updateData['completed_at'] = now();

            // Free up ambulance and driver, and clear location
            $dispatch->ambulance->update([
                'status'               => 'ready',
                'latitude'             => null,
                'longitude'            => null,
                'last_location_update' => null,
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
            'status'      => $newStatus,
            'note'        => 'Status diupdate oleh driver',
        ]);

        return response()->json([
            'success'    => true,
            'new_status' => $newStatus,
            'message'    => 'Status updated successfully',
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

    public function dispatching(Request $request)
    {
        $direction = $request->get('direction', 'asc');
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'asc';

        $requests = PatientRequest::where('status', 'pending')
            ->orderBy('request_date', $direction)
            ->orderBy('pickup_time', $direction)
            ->get();
            
        return view('driver.dispatching.index', compact('requests', 'direction'));
    }

    public function createSelfDispatch(PatientRequest $patientRequest)
    {
        // Check if ambulance is already on duty
        $ambulance = auth('ambulance')->user();
        $activeDispatch = Dispatch::where('ambulance_id', $ambulance->id)
            ->whereIn('status', ['assigned', 'enroute_pickup', 'on_scene', 'enroute_destination', 'arrived_destination', 'enroute_return', 'arrived_return'])
            ->first();

        if ($activeDispatch) {
            return redirect()->route('driver.dashboard')->with('error', 'Unit ambulans ini masih dalam penugasan aktif.');
        }

        $drivers = Driver::where('status', 'available')->get();
        
        return view('driver.dispatching.create', compact('patientRequest', 'drivers', 'ambulance'));
    }

    public function storeSelfDispatch(Request $request, PatientRequest $patientRequest)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
        ]);

        $ambulance = auth('ambulance')->user();

        // Double check penugasan aktif
        $activeDispatch = Dispatch::where('ambulance_id', $ambulance->id)
            ->whereIn('status', ['assigned', 'enroute_pickup', 'on_scene', 'enroute_destination', 'arrived_destination', 'enroute_return', 'arrived_return'])
            ->first();

        if ($activeDispatch) {
            return redirect()->route('driver.dashboard')->with('error', 'Unit ambulans ini masih dalam penugasan aktif.');
        }

        $dispatch = Dispatch::create([
            'patient_name' => $patientRequest->patient_name,
            'patient_phone' => $patientRequest->phone,
            'patient_condition' => $patientRequest->patient_condition ?? ($patientRequest->service_type === 'jenazah' ? 'jenazah' : 'emergency'),
            'pickup_address' => $patientRequest->pickup_address,
            'destination' => $patientRequest->destination,
            'driver_id' => $request->driver_id,
            'ambulance_id' => $ambulance->id,
            'status' => 'assigned',
            'assigned_at' => now(),
            'trip_type' => $patientRequest->trip_type ?? 'one_way',
            'return_address' => $patientRequest->return_address,
            'request_date' => $patientRequest->request_date,
            'pickup_time' => $patientRequest->pickup_time,
            'blok' => $patientRequest->blok,
            'rt' => $patientRequest->rt,
            'rw' => $patientRequest->rw,
            'kelurahan' => $patientRequest->kelurahan,
            'kecamatan' => $patientRequest->kecamatan,
            'nomor' => $patientRequest->nomor,
        ]);

        // Update statuses
        Driver::where('id', $request->driver_id)->update(['status' => 'on_duty']);
        $ambulance->update(['status' => 'on_duty']);

        // Log
        DispatchLog::create([
            'dispatch_id' => $dispatch->id,
            'status' => 'assigned',
            'note' => 'Dispatch dibuat sendiri oleh unit ambulans'
        ]);

        // Link to patient request
        $patientRequest->update([
            'status' => 'dispatched',
            'dispatch_id' => $dispatch->id,
        ]);

        return redirect()->route('driver.dashboard')->with('success', 'Penugasan berhasil dibuat!');
    }

    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $ambulance = auth('ambulance')->user();
        if ($ambulance) {
            $ambulance->update(['fcm_token' => $request->token]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 401);
    }
}
