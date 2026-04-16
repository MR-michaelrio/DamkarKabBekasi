<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientRequest;
use App\Models\Dispatch;
use App\Models\DispatchLog;
use App\Models\Driver;
use App\Models\Ambulance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PatientRequestController extends Controller
{
    public function exportPdf(PatientRequest $patientRequest)
    {
        $patientRequest->load(['dispatches.driver.pleton', 'dispatches.ambulance']);

        $primaryDispatch = $patientRequest->dispatches->first();
        $dispatchIds     = $patientRequest->dispatches->pluck('id');

        // Load activity photos attached to all dispatches on this request
        $activityLogs = \App\Models\ActivityLog::where('model', 'Dispatch')
            ->whereIn('model_id', $dispatchIds)
            ->with(['photos'])
            ->get();

        $dispatchMap = $patientRequest->dispatches->keyBy('id');
        $photos = collect();
        foreach ($activityLogs as $log) {
            foreach ($log->photos as $photo) {
                $driver = $dispatchMap->get($log->model_id)?->driver;
                $photos->push((object)[
                    'photo'    => $photo,
                    'uploader' => $driver?->name ?? 'Petugas',
                ]);
            }
        }

        $dispatches  = $patientRequest->dispatches;
        $otherPlates = $dispatches->skip(1)
            ->map(fn($d) => $d->ambulance?->plate_number)
            ->filter()
            ->values();

        // ── Format waktu ──
        $requestDate = \Carbon\Carbon::parse($patientRequest->request_date);
        $dayDate = $requestDate->isoFormat('dddd, DD MMMM YYYY');
        $placeDate = 'Bekasi, ' . $requestDate->format('d F Y');
        $hariNama = $requestDate->isoFormat('dddd');
        $tglAngka = $requestDate->format('d');
        $bulanNama = $requestDate->isoFormat('MMMM');
        $tahun = $requestDate->format('Y');
        
        $timeReport = $patientRequest->pickup_time ? substr($patientRequest->pickup_time, 0, 5) : '-';
        $otwAt = $primaryDispatch?->otw_scene_at ? \Carbon\Carbon::parse($primaryDispatch->otw_scene_at) : null;
        $arriveAt = $primaryDispatch?->pickup_at ? \Carbon\Carbon::parse($primaryDispatch->pickup_at) : null;
        $handledAt = $primaryDispatch?->hospital_at ? \Carbon\Carbon::parse($primaryDispatch->hospital_at) : null;
        
        $timeDeparture = $otwAt ? $otwAt->format('H:i') : '-';
        $timeArrival = $arriveAt ? $arriveAt->format('H:i') : '-';
        $timeFinished = $handledAt ? $handledAt->format('H:i') : '-';
        
        $lokasiLengkap = collect([$patientRequest->pickup_address, $patientRequest->kelurahan, $patientRequest->kecamatan])
            ->filter()->implode(', ');

        $pdf = Pdf::loadView('admin.reports.kebakaran_pdf', [
            // ── Halaman 1: Info Surat ──
            'nomor'       => $patientRequest->nomor ?? '-',
            'sifat'       => 'Penting',
            'lampiran'    => '-',
            'place_date'  => $placeDate,

            // ── Halaman 1: Data Kejadian ──
            'day_date'        => $dayDate,
            'time_report'     => $timeReport,
            'time_departure'  => $timeDeparture,
            'time_arrival'    => $timeArrival,
            'time_finished'   => $timeFinished,
            'chronology'      => $patientRequest->patient_condition === 'kebakaran' ? 'Kebakaran' : ucfirst($patientRequest->patient_condition ?? '-'),
            'address'         => $patientRequest->pickup_address ?? '-',
            'village'         => $patientRequest->kelurahan ?? '-',
            'district'        => $patientRequest->kecamatan ?? '-',
            'reporter_name'   => $patientRequest->patient_name ?? '-',
            'reporter_phone'  => $patientRequest->phone ?? '-',
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
            'ba_alamat'          => $patientRequest->pickup_address ?? '-',
            'ba_kelurahan'       => $patientRequest->kelurahan ?? '-',
            'ba_kecamatan'       => $patientRequest->kecamatan ?? '-',
            'ba_nama_pelapor'    => $patientRequest->patient_name ?? '-',
            'ba_telp_pelapor'    => $patientRequest->phone ?? '-',
            'ba_nama_rt_rw'      => 'RT ' . ($patientRequest->rt ?? '-') . ' / RW ' . ($patientRequest->rw ?? '-'),
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
            'ba_komandan_regu'   => strtoupper($primaryDispatch?->driver?->name ?? 'KOMANDAN REGU'),
            'ba_nip_danru'       => '-',
            'ba_komandan_peleton'=> strtoupper($primaryDispatch?->driver?->pleton?->name ?? 'KOMANDAN PELETON'),
            'ba_nip_danton'      => '-',

            // ── Halaman 3 & 4: Foto + Armada ──
            'dispatches' => $dispatches,
            'photos'     => $photos,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-kebakaran-' . $patientRequest->id . '-' . date('Ymd') . '.pdf');
    }

    public function index(Request $request)
    {
        $direction = $request->get('direction', 'desc');
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $requests = PatientRequest::with('dispatches')
            ->orderBy('request_date', $direction)
            ->orderBy('pickup_time', $direction)
            ->get();

        if ($request->ajax()) {
            return view('admin.patient_requests._table', compact('requests', 'direction'));
        }

        return view('admin.patient_requests.index', compact('requests', 'direction'));
    }

    public function show(Request $request, PatientRequest $patientRequest)
    {
        if ($request->ajax()) {
            // Generate TTS URL on-demand
            $serviceType = ucfirst($patientRequest->service_type);
            $address = $patientRequest->pickup_address;
            $ttsText = "Laporan baru. {$serviceType}. {$address}.";
            $filename = md5($ttsText) . '.mp3';
            $ttsUrl = asset('storage/tts/' . $filename);

            return response()->json([
                'id' => $patientRequest->id,
                'patient_name' => $patientRequest->patient_name,
                'service_type' => $patientRequest->service_type,
                'pickup_address' => $patientRequest->pickup_address,
                'patient_condition' => $patientRequest->patient_condition,
                'request_date' => $patientRequest->request_date,
                'pickup_time' => $patientRequest->pickup_time,
                'phone' => $patientRequest->phone,
                'status' => $patientRequest->status,
                'tts_url' => $ttsUrl,
            ]);
        }

        return view('admin.patient_requests.show', compact('patientRequest'));
    }

    public function createDispatch(PatientRequest $patientRequest)
    {
        $drivers = Driver::where('status', 'available')->with('pleton')->get();
        $ambulances = Ambulance::where('status', 'ready')->get();
        $pletons = \App\Models\Pleton::all();

        return view('admin.dispatches.create', [
            'drivers' => $drivers,
            'ambulances' => $ambulances,
            'pletons' => $pletons,
            'patientRequest' => $patientRequest,
        ]);
    }

    public function edit(PatientRequest $patientRequest)
    {
        return view('admin.patient_requests.edit', compact('patientRequest'));
    }

    public function update(Request $request, PatientRequest $patientRequest)
    {
        $validated = $request->validate([
            'patient_name' => 'required',
            'service_type' => 'required|in:ambulance,jenazah',
            'request_date' => 'required|date',
            'phone' => 'nullable',
            'pickup_address' => 'required',
            'destination' => 'nullable',
            'patient_condition' => 'nullable|in:emergency,kontrol,pasien_pulang',
        ]);

        $patientRequest->update($validated);

        return redirect()->route('admin.patient-requests.index')
            ->with('success', 'Permintaan berhasil diperbarui');
    }

    public function destroy(PatientRequest $patientRequest)
    {
        $patientRequest->delete();

        return redirect()->route('admin.patient-requests.index')
            ->with('success', 'Permintaan berhasil dihapus');
    }

    public function reject(PatientRequest $patientRequest)
    {
        $patientRequest->update(['status' => 'rejected']);

        return redirect()->route('admin.patient-requests.index')
            ->with('success', 'Permintaan ditolak');
    }
}
