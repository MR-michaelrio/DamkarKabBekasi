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
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Laravel\Firebase\Facades\Firebase;

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
            'drivers' => Driver::where('status', 'available')->with('pleton')->get(),
            'ambulances' => Ambulance::where('status', 'ready')->get(),
            'pletons' => \App\Models\Pleton::all(),
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
            'patient_phone' => 'nullable|string',
            'patient_request_id' => 'nullable|exists:patient_requests,id',
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
            ]);
        }

        // Send Push Notification
        try {
            $dispatch->load(['ambulance', 'driver.pleton']);
            
            $ambulanceTokens = \App\Models\Ambulance::whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')->toArray();

            $deviceTokensByProject = \App\Models\DeviceToken::select('token', 'firebase_project')
                ->get()
                ->groupBy('firebase_project');

            if ($ambulanceTokens || $deviceTokensByProject->isNotEmpty()) {
                $plateNumber = $dispatch->ambulance->plate_number ?? '-';
                $pletonName = $dispatch->driver->pleton->name ?? '-';
                $address = $dispatch->pickup_address;
                $serviceType = ucfirst($dispatch->patient_condition);
                
                $ttsService = new \App\Services\TTSService();
                $ttsUrl = $ttsService->generate("Dispatch baru. Unit {$plateNumber}. Untuk {$serviceType}. Di {$address}.");

                $message = CloudMessage::new ()
                ->withData([
                    'title' => 'Dispatch Baru',
                    'body' => "{$plateNumber}\n{$pletonName}\n{$address}\n{$serviceType}",
                    'tts_url' => $ttsUrl ? url($ttsUrl) : '',
                ])
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'priority' => 'high',
                ]));

                $projects = ['damkar'];
                if ($dispatch->patient_condition === 'kebakaran') {
                    $projects = ['damkar', 'pmi', 'gmci'];
                }
                foreach ($projects as $projectName) {
                    $tokens = $deviceTokensByProject->get($projectName, collect())->pluck('token')->toArray();
                    
                    if ($projectName === 'damkar') {
                        $tokens = array_unique(array_merge($tokens, $ambulanceTokens));
                    }

                    if (!empty($tokens)) {
                        try {
                            $messaging = Firebase::project($projectName)->messaging();
                            $messaging->sendMulticast($message, array_values($tokens));
                        } catch (\Exception $e) {
                            \Log::error("FCM Send Error for Project {$projectName}: " . $e->getMessage());
                        }
                    }
                }
            }
        }
        catch (\Exception $e) {
            \Log::error('FCM Dispatch Send Error: ' . $e->getMessage());
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
        $updateData = ['status' => $nextStatus];

        if ($nextStatus === 'on_the_way_scene') {
            $updateData['otw_scene_at'] = now();
        } elseif ($nextStatus === 'on_scene') {
            $updateData['pickup_at'] = now();
        } elseif ($nextStatus === 'on_the_way_kantor_pos') {
            $updateData['hospital_at'] = now();
        } elseif ($nextStatus === 'completed') {
            $updateData['completed_at'] = now();
        }

        $dispatch->update($updateData);

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
        // Free up the assigned unit and driver
        if ($dispatch->ambulance_id) {
            $dispatch->ambulance->update(['status' => 'ready']);
        }
        if ($dispatch->driver_id) {
            $dispatch->driver->update(['status' => 'available']);
        }

        $patientRequestId = $dispatch->patient_request_id;
        $eventRequestId = $dispatch->event_request_id;

        $dispatch->logs()->delete();
        $dispatch->delete();

        // Revert parent status to pending if this was the last active dispatch for PatientRequest
        if ($patientRequestId) {
            $pr = \App\Models\PatientRequest::find($patientRequestId);
            if ($pr && $pr->dispatches()->count() === 0) {
                $pr->update(['status' => 'pending']);
            }
        }
        
        // Dispatches on Events don't strictly need automatic event status rollback, but could be handled if needed.

        return back()->with('success', 'Armada berhasil dihapus dari lokasi kejadian.');
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
            $month = $request->get('month', Carbon::now()->month);
            $year = $request->get('year', Carbon::now()->year);
            try {
                $filterDate = Carbon::createFromDate($year, $month, 1);
            } catch (\Exception $e) {
                $filterDate = Carbon::now();
                $month = $filterDate->month;
                $year = $filterDate->year;
            }

            $query->whereMonth('created_at', $month)
                ->whereYear('created_at', $year);
            $title .= " Bulan " . $filterDate->translatedFormat('F Y');
            $startDate = $filterDate->copy()->startOfMonth();
            $endDate = $filterDate->copy()->endOfMonth();
        }

        $dispatches = $query->orderByDesc('created_at')->get();

        // Ambulance Analytics for the period
        $analytics = Ambulance::with(['dispatches' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
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