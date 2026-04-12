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
        $patientRequest->load(['dispatches.driver', 'dispatches.ambulance']);

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

        $incident = [
            'request_date'   => $patientRequest->request_date,
            'pickup_time'    => $patientRequest->pickup_time,
            'otw_at'         => $primaryDispatch?->otw_scene_at,
            'arrive_at'      => $primaryDispatch?->pickup_at,
            'handled_at'     => $primaryDispatch?->hospital_at,
            'completed_at'   => $primaryDispatch?->completed_at,
            'address'        => $patientRequest->pickup_address,
            'kelurahan'      => $patientRequest->kelurahan,
            'kecamatan'      => $patientRequest->kecamatan,
            'reporter_name'  => $patientRequest->patient_name,
            'reporter_phone' => $patientRequest->phone,
            'condition'      => $patientRequest->patient_condition,
            'unit_count'     => $dispatches->count(),
            'plate_number'   => $primaryDispatch?->ambulance?->plate_number,
            'other_plates'   => $otherPlates,
            'nomor'          => $patientRequest->nomor,
            'rt'             => $patientRequest->rt,
            'rw'             => $patientRequest->rw,
            'blok'           => $patientRequest->blok,
        ];

        $pdf = Pdf::loadView('admin.reports.kebakaran_pdf', compact('incident', 'dispatches', 'photos'))
            ->setPaper('a4', 'portrait');

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
