<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispatch;
use App\Models\Driver;
use App\Models\Ambulance;
use App\Models\DispatchLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DispatchController extends Controller
{
    public function index()
    {
        $dispatches = Dispatch::with(['driver', 'ambulance', 'logs'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.dispatches.index', compact('dispatches'));
    }

    public function create()
    {
        return view('admin.dispatches.create', [
            'drivers' => Driver::where('status', 'available')->get(),
            'ambulances' => Ambulance::where('status', 'ready')->get(),
            'patientRequest' => null, // Will be populated when coming from patient request
        ]);
    }

    public function store(Request $request)
    {
        $dispatch = Dispatch::create($request->validate([
            'patient_name' => 'required',
            'request_date' => 'required|date',
            'pickup_time' => 'required',
            'patient_condition' => 'required|in:kebakaran,rescue',
            'pickup_address' => 'required',
            'destination' => 'nullable',
            'driver_id' => 'required',
            'ambulance_id' => 'required',
            'trip_type' => 'nullable|in:one_way,round_trip',
            'return_address' => 'nullable',
            'blok' => 'nullable|string',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
            'kelurahan' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'nomor' => 'nullable|string',
        ]) + [
            'status' => 'pending',
            'assigned_at' => now(),
        ]);

        Driver::where('id', $dispatch->driver_id)->update(['status' => 'on_duty']);
        Ambulance::where('id', $dispatch->ambulance_id)->update(['status' => 'on_duty']);

        DispatchLog::create([
            'dispatch_id' => $dispatch->id,
            'status' => 'pending',
            'note' => 'Dispatch dibuat'
        ]);

        // Update patient request if dispatch was created from one
        if ($request->has('patient_request_id')) {
            \App\Models\PatientRequest::where('id', $request->patient_request_id)
                ->update([
                'status' => 'dispatched',
                'dispatch_id' => $dispatch->id,
            ]);
        }

        return redirect()->route('admin.dispatches.index');
    }

    public function show(Dispatch $dispatch)
    {
        $dispatch->load(['driver', 'ambulance', 'logs']);
        return view('admin.dispatches.show', compact('dispatch'));
    }

    public function next(Dispatch $dispatch)
    {
        $flow = [
            'pending' => 'on_the_way_scene',
            'on_the_way_scene' => 'on_scene',
            'on_scene' => 'on_the_way_kantor_pos',
            'on_the_way_kantor_pos' => 'completed',
        ];

        if (!isset($flow[$dispatch->status])) {
            return back();
        }

        // Simplified flow, no special round trip logic needed anymore per requirements
        $nextStatus = $flow[$dispatch->status];

        $dispatch->update(['status' => $nextStatus]);

        DispatchLog::create([
            'dispatch_id' => $dispatch->id,
            'status' => $dispatch->status,
        ]);

        if ($dispatch->status === 'completed') {
            $dispatch->ambulance->update(['status' => 'ready']);
            $dispatch->driver->update(['status' => 'available']);

            // Sync PatientRequest status if exists
            \App\Models\PatientRequest::where('dispatch_id', $dispatch->id)
                ->update(['status' => 'completed']);
        }

        return back();
    }

    public function destroy(Dispatch $dispatch)
    {
        $dispatch->logs()->delete();
        $dispatch->delete();

        return back();
    }

    // ✅ EXPORT PDF
    public function exportPdf(Request $request)
    {
        $range = $request->get('range', 'all');
        $query = Dispatch::with(['driver', 'ambulance']);

        $title = "Laporan Dispatch Armada";
        $startDate = null;
        $endDate = null;

        if ($range === 'today') {
            $query->whereDate('created_at', Carbon::today());
            $title .= " Hari Ini";
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        }
        elseif ($range === 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $title .= " Minggu Ini";
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }
        elseif ($range === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
            $title .= " Bulan Ini";
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        $dispatches = $query->orderByDesc('created_at')->get();

        // Ambulance Analytics for the period
        $analytics = Ambulance::with(['dispatches' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
            else {
                $q->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
            }
        }])->get()->map(function ($ambulance) {
            $ambulance->dispatches_count = $ambulance->dispatches->count();
            $ambulance->condition_breakdown = $ambulance->dispatches
                ->groupBy('patient_condition')
                ->map(function ($items) {
                return $items->count();
            }
            );
            return $ambulance;
        });

        $pdf = Pdf::loadView('admin.dispatches.dashboard_pdf', compact('dispatches', 'analytics', 'title', 'range'));

        return $pdf->download('dispatch-report-' . $range . '-' . date('Y-m-d') . '.pdf');
    }

    public function locationHistory(Dispatch $dispatch)
    {
        $history = \App\Models\DispatchLocationHistory::where('dispatch_id', $dispatch->id)
            ->orderBy('created_at', 'asc')
            ->get(['latitude', 'longitude', 'created_at']);

        return response()->json($history);
    }

    public function exportSinglePdf(Dispatch $dispatch)
    {
        $dispatch->load(['driver', 'ambulance']);

        $pdf = Pdf::loadView('admin.dispatches.single_pdf', compact('dispatch'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-kejadian-' . $dispatch->id . '-' . date('Ymd') . '.pdf');
    }
}