<?php

namespace App\Http\Controllers;

use App\Models\PatientRequest;
use Illuminate\Http\Request;

class PatientRequestController extends Controller
{
    public function create()
    {
        return view('patient_request.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'service_type' => 'required|in:ambulance,jenazah',
            'request_date' => 'required|date',
            'phone' => 'required|string|max:20',
            'pickup_address' => 'required|string',
            'destination' => 'required|string',
            'patient_condition' => 'nullable|in:emergency,kontrol,pasien_pulang',
            'trip_type' => 'nullable|in:one_way,round_trip',
            'return_address' => 'nullable|string',
        ]);

        PatientRequest::create($validated);

        return redirect()->route('patient-request.create')
            ->with('success', 'Permintaan Anda telah dikirim. Kami akan segera menghubungi Anda.');
    }
}
