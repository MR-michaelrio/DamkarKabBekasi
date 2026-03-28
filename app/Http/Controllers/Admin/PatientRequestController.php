<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientRequest;
use App\Models\Dispatch;
use App\Models\DispatchLog;
use App\Models\Driver;
use App\Models\Ambulance;
use Illuminate\Http\Request;

class PatientRequestController extends Controller
{
    public function index(Request $request)
    {
        $direction = $request->get('direction', 'desc');
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $requests = PatientRequest::with('dispatches')
            ->orderBy('request_date', $direction)
            ->orderBy('pickup_time', $direction)
            ->get();

        return view('admin.patient_requests.index', compact('requests', 'direction'));
    }

    public function show(PatientRequest $patientRequest)
    {
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
