<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EventRequest;
use App\Models\Dispatch;
use App\Models\Ambulance;
use App\Models\Driver;
use App\Models\DispatchLog;

class EventRequestController extends Controller
{
    public function index()
    {
        $events = EventRequest::orderByDesc('created_at')->get();
        return view('admin.event_requests.index', compact('events'));
    }

    public function create()
    {
        return view('admin.event_requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'needs'      => 'nullable|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'type'       => 'required|in:event,disaster',
        ]);

        EventRequest::create($validated + ['status' => 'approved']);

        return redirect()->route('admin.event-requests.index')->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(EventRequest $eventRequest)
    {
        $eventRequest->load(['dispatches.ambulance', 'dispatches.driver']);
        $availableAmbulances = Ambulance::where('status', 'ready')->get();
        $availableDrivers    = Driver::where('status', 'available')->get();

        return view('admin.event_requests.show', compact('eventRequest', 'availableAmbulances', 'availableDrivers'));
    }

    public function edit(EventRequest $eventRequest)
    {
        return view('admin.event_requests.edit', compact('eventRequest'));
    }

    public function update(Request $request, EventRequest $eventRequest)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'needs'      => 'nullable|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:pending,approved,rejected',
            'type'       => 'required|in:event,disaster',
        ]);

        $eventRequest->update($validated);

        return redirect()->route('admin.event-requests.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(EventRequest $eventRequest)
    {
        $eventRequest->delete();
        return back()->with('success', 'Event berhasil dihapus.');
    }

    public function approve(EventRequest $eventRequest)
    {
        $eventRequest->update(['status' => 'approved']);
        return back()->with('success', 'Event disetujui.');
    }

    public function reject(EventRequest $eventRequest)
    {
        $eventRequest->update(['status' => 'rejected']);
        return back()->with('success', 'Event ditolak.');
    }

    /**
     * Assign a new ambulance unit to an event.
     */
    public function assignUnit(Request $request, EventRequest $eventRequest)
    {
        $request->validate([
            'ambulance_id' => 'required|exists:ambulances,id',
            'driver_id'    => 'required|exists:drivers,id',
        ]);

        $ambulance = Ambulance::findOrFail($request->ambulance_id);

        $dispatch = Dispatch::create([
            'event_request_id' => $eventRequest->id,
            'patient_name'     => 'Event: ' . $eventRequest->event_name,
            'patient_condition'=> $eventRequest->type === 'disaster' ? 'emergency' : 'kontrol',
            'pickup_address'   => $eventRequest->needs ?? '-',
            'destination'      => '-',
            'ambulance_id'     => $request->ambulance_id,
            'driver_id'        => $request->driver_id,
            'status'           => 'assigned',
            'assigned_at'      => now(),
            'request_date'     => $eventRequest->start_date,
            'is_replacement'   => false,
        ]);

        // Mark ambulance/driver as on_duty
        $ambulance->update(['status' => 'on_duty']);
        Driver::where('id', $request->driver_id)->update(['status' => 'on_duty']);

        DispatchLog::create([
            'dispatch_id' => $dispatch->id,
            'status'      => 'assigned',
            'note'        => 'Unit ditugaskan ke Event: ' . $eventRequest->event_name,
        ]);

        return redirect()->route('admin.event-requests.show', $eventRequest)
                         ->with('success', 'Unit ' . $ambulance->code . ' berhasil ditugaskan ke event.');
    }

    /**
     * Replace an active unit in the event with a new one.
     */
    public function replaceUnit(Request $request, EventRequest $eventRequest, Dispatch $dispatch)
    {
        $request->validate([
            'ambulance_id'     => 'nullable|exists:ambulances,id',
            'driver_id'        => 'nullable|exists:drivers,id',
            'replacement_date' => 'required|date|after_or_equal:' . $eventRequest->start_date->format('Y-m-d') . '|before_or_equal:' . $eventRequest->end_date->format('Y-m-d'),
            'reason'           => 'nullable|string|max:500',
        ]);

        // Fallback to current values if not provided
        $newAmbulanceId = $request->ambulance_id ?: $dispatch->ambulance_id;
        $newDriverId    = $request->driver_id ?: $dispatch->driver_id;

        // Mark old dispatch as completed
        $dispatch->update(['status' => 'completed', 'completed_at' => now()]);

        // Free old ambulance ONLY if it's actually being changed
        if ($request->ambulance_id && $dispatch->ambulance_id && $request->ambulance_id != $dispatch->ambulance_id) {
            $dispatch->ambulance->update(['status' => 'ready']);
        }
        
        // Free old driver ONLY if it's actually being changed
        if ($request->driver_id && $dispatch->driver_id && $request->driver_id != $dispatch->driver_id) {
            $dispatch->driver->update(['status' => 'available']);
        }

        DispatchLog::create([
            'dispatch_id' => $dispatch->id,
            'status'      => 'completed',
            'note'        => 'Unit diganti pada tanggal ' . $request->replacement_date . '. Alasan: ' . ($request->reason ?? 'Tidak ada keterangan'),
        ]);

        // Create new replacement dispatch
        $newDispatch = Dispatch::create([
            'event_request_id'    => $eventRequest->id,
            'patient_name'        => 'Event: ' . $eventRequest->event_name,
            'patient_condition'   => $eventRequest->type === 'disaster' ? 'emergency' : 'kontrol',
            'pickup_address'      => $eventRequest->needs ?? '-',
            'destination'         => '-',
            'ambulance_id'        => $newAmbulanceId,
            'driver_id'           => $newDriverId,
            'status'              => 'assigned',
            'assigned_at'         => now(),
            'request_date'        => $request->replacement_date,
            'is_replacement'      => true,
            'replaced_dispatch_id'=> $dispatch->id,
        ]);

        // Update status only for the new/kept ones
        if ($newAmbulanceId) {
            Ambulance::where('id', $newAmbulanceId)->update(['status' => 'on_duty']);
        }
        if ($newDriverId) {
            Driver::where('id', $newDriverId)->update(['status' => 'on_duty']);
        }

        DispatchLog::create([
            'dispatch_id' => $newDispatch->id,
            'status'      => 'assigned',
            'note'        => 'Perubahan unit/driver untuk Event: ' . $eventRequest->event_name . ' (Efektif: ' . $request->replacement_date . ')',
        ]);

        return redirect()->route('admin.event-requests.show', $eventRequest)
                         ->with('success', 'Perubahan berhasil disimpan. Update akan aktif mulai ' . $request->replacement_date);
    }

    /**
     * Complete the event manually.
     */
    public function finish(EventRequest $eventRequest)
    {
        // Update Event status and end_date
        $eventRequest->update([
            'status'   => 'approved', // Or 'completed' if you want a new status, 
                                   // but 'approved' is used for active. 
                                   // Let's stick to updating the end_date to TODAY to stop it from appearing in future calendar days.
            'end_date' => now()->startOfDay(), 
        ]);

        // Find and free all active dispatches for this event
        $activeDispatches = $eventRequest->dispatches->whereNotIn('status', ['completed']);

        foreach ($activeDispatches as $dispatch) {
            $dispatch->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);

            if ($dispatch->ambulance) {
                $dispatch->ambulance->update(['status' => 'ready']);
            }
            if ($dispatch->driver) {
                $dispatch->driver->update(['status' => 'available']);
            }

            DispatchLog::create([
                'dispatch_id' => $dispatch->id,
                'status'      => 'completed',
                'note'        => 'Event selesai secara manual.',
            ]);
        }

        return redirect()->route('admin.event-requests.index')
                         ->with('success', 'Event ' . $eventRequest->event_name . ' telah diselesaikan secara manual.');
    }

    public function publicCreate()
    {
        return view('patient_request.event_create');
    }

    public function publicStore(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'needs'      => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'type'       => 'required|in:event,disaster',
        ]);

        EventRequest::create($validated + ['status' => 'pending']);

        return redirect()->route('portal')->with('success', 'Permintaan Anda telah terkirim dan akan segera kami tinjau.');
    }
}
