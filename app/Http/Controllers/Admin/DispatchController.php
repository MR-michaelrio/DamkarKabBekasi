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

        if (request()->ajax()) {
            return view('admin.dispatches._table', compact('dispatches'));
        }

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
        $validated = $request->validate([
            'patient_name' => 'required',
            'request_date' => 'required|date',
            'pickup_time' => 'required|date_format:H:i',
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
        ]);
        
        // Auto-generate report number if not provided
        if (empty($validated['nomor'])) {
            $reportNumberService = new \App\Services\ReportNumberService();
            $validated['nomor'] = $reportNumberService->generate($validated['patient_condition']);
        }
        
        $dispatch = Dispatch::create($validated + [
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
                $ttsGeneratedName = $ttsService->generate("Dispatch baru. Unit {$plateNumber}. Untuk {$serviceType}. Di {$address}.");
                
                // Robust URL generation
                $ttsUrl = '';
                if ($ttsGeneratedName) {
                    $ttsUrl = asset($ttsGeneratedName);
                    if (str_contains($ttsUrl, 'localhost') && request()->getHost() !== 'localhost') {
                        $ttsUrl = request()->getSchemeAndHttpHost() . $ttsGeneratedName;
                    }
                }
                \Log::info("FCM Dispatch TTS URL: " . $ttsUrl);

                $message = CloudMessage::new ()
                ->withNotification(\Kreait\Firebase\Messaging\Notification::create('Dispatch Baru', "{$plateNumber}\n{$pletonName}\n{$address}\n{$serviceType}"))
                ->withData([
                    'title' => 'Dispatch Baru',
                    'body' => "{$plateNumber}\n{$pletonName}\n{$address}\n{$serviceType}",
                    'tts_url' => $ttsUrl ?: '',
                    'type' => 'dispatch',
                ])
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'damkar-emergency',
                        'sound' => 'emergency'
                    ],
                    'ttl' => 3600,
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
        $dispatch->load(['driver', 'ambulance', 'driver.pleton']);

        // ── Kumpulkan semua dispatch terkait (satu event/patient_request) ──
        $dispatches = collect([$dispatch]);
        if ($dispatch->patient_request_id) {
            $dispatches = Dispatch::where('patient_request_id', $dispatch->patient_request_id)
                ->with(['driver', 'ambulance', 'driver.pleton'])
                ->get();
        }

        $otherPlates = $dispatches->reject(fn($d) => $d->id === $dispatch->id)
            ->map(fn($d) => $d->ambulance?->plate_number)
            ->filter()
            ->values();

        $plateNumbers = $dispatches
            ->map(fn($d) => $d->ambulance?->plate_number)
            ->filter()
            ->values();

        // ── Kumpulkan foto dari ActivityLog ──
        // Cari ActivityLog yang terkait dengan dispatch ini (dari semua driver yg terlibat)
        $driverIds = $dispatches->pluck('driver_id')->filter()->values()->toArray();

        $activityLogs = \App\Models\ActivityLog::where(function ($q) use ($dispatch, $driverIds) {
                $q->where(function ($q2) use ($dispatch) {
                    $q2->where('model', 'Dispatch')->where('model_id', $dispatch->id);
                })->orWhere(function ($q2) use ($dispatch) {
                    $q2->where('model', 'dispatch')->where('model_id', $dispatch->id);
                });
                if ($dispatch->patient_request_id) {
                    $q->orWhere(function ($q2) use ($dispatch) {
                        $q2->where('model', 'PatientRequest')->where('model_id', $dispatch->patient_request_id);
                    });
                }
            })
            ->with(['photos', 'user'])
            ->get();

        // Juga ambil ActivityLog by user_id driver yang terlibat pada tanggal yang sama
        if ($activityLogs->isEmpty() && count($driverIds) > 0) {
            $activityLogs = \App\Models\ActivityLog::whereIn('user_id', $driverIds)
                ->with(['photos', 'user'])
                ->whereHas('photos')
                ->orderByDesc('created_at')
                ->get();
        }

        $photos = collect();
        foreach ($activityLogs as $log) {
            foreach ($log->photos as $photo) {
                $driverName = $dispatches->first(fn($d) => $d->driver_id == $log->user_id)?->driver?->name
                    ?? $log->user?->name
                    ?? $dispatch->driver?->name
                    ?? 'Petugas';
                $photos->push((object)[
                    'photo'    => $photo,
                    'uploader' => $driverName,
                ]);
            }
        }

        // ── Format tanggal ──
        $requestDate = $dispatch->request_date
            ? ($dispatch->request_date instanceof \Carbon\Carbon ? $dispatch->request_date : Carbon::parse($dispatch->request_date))
            : null;
        $otwAt      = $dispatch->otw_scene_at;
        $arriveAt   = $dispatch->pickup_at;
        $handledAt  = $dispatch->hospital_at;
        $completedAt = $dispatch->completed_at;

        $hariId   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $bulanId  = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        $hariNama  = $requestDate ? $hariId[$requestDate->dayOfWeek] : '..........';
        $bulanNama = $requestDate ? $bulanId[(int)$requestDate->format('n')] : '..........';
        $tglAngka  = $requestDate ? $requestDate->format('d') : '...';
        $tahun     = $requestDate ? $requestDate->format('Y') : '2026';

        $dayDate   = $requestDate ? "{$hariNama}, {$tglAngka} {$bulanNama} {$tahun}" : '-';
        $placeDate = 'Bekasi, ' . ($requestDate ? "{$tglAngka} {$bulanNama} {$tahun}" : (function() {
            $now = now();
            $hariId = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            $bulanId = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            return $now->format('d') . ' ' . $bulanId[(int)$now->format('n')] . ' ' . $now->format('Y');
        })());

        $timeReport    = $dispatch->pickup_time   ? substr($dispatch->pickup_time, 0, 5) : '-';
        $timeDeparture = $otwAt    ? $otwAt->format('H:i')    : '-';
        $timeArrival   = $arriveAt ? $arriveAt->format('H:i') : '-';
        $timeFinished  = $handledAt ? $handledAt->format('H:i') : '-';

        $lokasiLengkap = collect([$dispatch->pickup_address, $dispatch->kelurahan, $dispatch->kecamatan])
            ->filter()->implode(', ');

        $pdf = Pdf::loadView('admin.reports.kebakaran_pdf', [
            // ── Halaman 1: Info Surat ──
            'nomor'       => $dispatch->nomor ?? '-',
            'sifat'       => 'Penting',
            'lampiran'    => '-',
            'place_date'  => $placeDate,

            // ── Halaman 1: Data Kejadian ──
            'day_date'        => $dayDate,
            'time_report'     => $timeReport,
            'time_departure'  => $timeDeparture,
            'time_arrival'    => $timeArrival,
            'time_finished'   => $timeFinished,
            'chronology'      => $dispatch->patient_condition === 'kebakaran' ? 'Kebakaran' : ucfirst($dispatch->patient_condition ?? '-'),
            'address'         => $dispatch->pickup_address ?? '-',
            'village'         => $dispatch->kelurahan ?? '-',
            'district'        => $dispatch->kecamatan ?? '-',
            'reporter_name'   => $dispatch->patient_name ?? '-',
            'reporter_phone'  => $dispatch->patient_phone ?? '-',
            'community_leader_name'  => '-',
            'community_leader_phone' => '-',
            'area_size'       => '-',
            'building_type'   => '-',
            'owner_name'      => '-',
            'owner_age'       => '-',
            'owner_phone'     => '-',
            'owner_occupation'=> '-',
            'fire_origin'     => '-',
            'unit_count'      => $dispatches->count() . ' Unit',
            'vehicle_number'  => $dispatches->map(fn($d) => trim(($d->ambulance?->plate_number ?? '') . ' (' . ($d->ambulance?->code ?? '-') . ')'))->filter()->implode(', '),
            'additional_units'=> $otherPlates->toArray(),
            'scba_usage'      => '0',
            'apar_usage'      => '0',
            'injured'         => '0',
            'fatalities'      => '0',
            'displaced'       => '0',

            // ── Tanda tangan Halaman 1 ──
            'approver_name' => 'MULYADI HADI SAPUTRA, SE',
            'approver_rank' => 'Pembina – IV/a',
            'approver_nip'  => '19740410 200311 1 001',
            'officer_name'  => 'AHMAD FAUZI, ST',
            'officer_rank'  => 'Penata Tk.I – III/d',
            'officer_nip'   => '19751104 200901 1 001',

            // ── Halaman 2: Berita Acara ──
            'ba_hari'            => $hariNama,
            'ba_tanggal'         => $tglAngka,
            'ba_bulan'           => $bulanNama,
            'ba_tahun'           => $tahun,
            'ba_pukul'           => $timeReport,
            'ba_hari_tanggal'    => $dayDate,
            'ba_waktu_laporan'   => $timeReport,
            'ba_waktu_berangkat' => $timeDeparture,
            'ba_waktu_tiba'      => $timeArrival,
            'ba_waktu_selesai'   => $timeFinished,
            'ba_kronologi'       => '-',
            'ba_lokasi_kebakaran'=> $lokasiLengkap ?: '-',
            'ba_jenis_bangunan'  => '-',
            'ba_penyebab'        => '-',
            'ba_luas_area'       => '-',
            'ba_nama_pemilik'    => '-',
            'ba_umur_pemilik'    => '-',
            'ba_pekerjaan_pemilik' => '-',
            'ba_alamat'          => $dispatch->pickup_address ?? '-',
            'ba_kelurahan'       => $dispatch->kelurahan ?? '-',
            'ba_kecamatan'       => $dispatch->kecamatan ?? '-',
            'ba_nama_pelapor'    => $dispatch->patient_name ?? '-',
            'ba_telp_pelapor'    => $dispatch->patient_phone ?? '-',
            'ba_nama_rt_rw'      => 'RT ' . ($dispatch->rt ?? '-') . ' / RW ' . ($dispatch->rw ?? '-'),
            'ba_telp_rt_rw'      => '-',
            'ba_jumlah_unit'     => $dispatches->count(),
            'ba_no_seri_kendaraan' => $dispatches->map(fn($d) => $d->ambulance?->plate_number)->filter()->implode(', '),
            'ba_bantuan_unit'    => $otherPlates->toArray(),
            'ba_scba_usage'      => '0',
            'ba_apar_usage'      => '0',
            'ba_korban_luka'     => '0',
            'ba_korban_jiwa'     => '0',
            'ba_korban_terdampak'=> '0',
            'ba_tanggal_laporan' => $placeDate,
            'ba_komandan_regu'   => strtoupper($dispatch->driver?->name ?? 'KOMANDAN REGU'),
            'ba_nip_danru'       => '-',
            'ba_komandan_peleton'=> strtoupper($dispatch->driver?->pleton?->name ?? 'KOMANDAN PELETON'),
            'ba_nip_danton'      => '-',

            // ── Halaman 3 & 4: Foto + Armada ──
            'dispatches' => $dispatches,
            'photos'     => $photos,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-kebakaran-' . $dispatch->id . '-' . date('Ymd') . '.pdf');
    }
}