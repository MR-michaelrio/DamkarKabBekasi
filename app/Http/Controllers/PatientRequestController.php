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
            'pickup_time' => 'required',
            'phone' => 'required|string|max:20',
            'pickup_address' => 'required|string',
            'destination' => 'required|string',
            'patient_condition' => 'nullable|in:emergency,kontrol,pasien_pulang',
            'trip_type' => 'nullable|in:one_way,round_trip',
            'return_address' => 'nullable|string',
        ]);

        PatientRequest::create($validated);

        // Send Push Notification
        try {
            $ambulanceTokens = \App\Models\Ambulance::whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')->toArray();
                
            $deviceTokens = \App\Models\DeviceToken::pluck('token')->toArray();
            
            $tokens = array_unique(array_merge($ambulanceTokens, $deviceTokens));

            if (!empty($tokens)) {
                $messaging = app('firebase.messaging');
                $message = \Kreait\Firebase\Messaging\CloudMessage::new()
                    ->withNotification(\Kreait\Firebase\Messaging\Notification::create(
                        'Permintaan Pasien Baru',
                        "Pasien: {$validated['patient_name']} ({$validated['pickup_address']})"
                    ));

                $messaging->sendMulticast($message, array_values($tokens));
            }
        } catch (\Exception $e) {
            \Log::error('FCM Send Error: ' . $e->getMessage());
        }

        return redirect()->route('patient-request.create')
            ->with('success', 'Permintaan Anda telah dikirim. Kami akan segera menghubungi Anda.');
    }
}
