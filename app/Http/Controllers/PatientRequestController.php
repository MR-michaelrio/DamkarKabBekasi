<?php

namespace App\Http\Controllers;

use App\Models\PatientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;

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
            'service_type' => 'required|in:kebakaran,rescue',
            'request_date' => 'required|date',
            'pickup_time' => 'required',
            'phone' => 'required|string|max:20',
            'pickup_address' => 'required|string',
            'destination' => 'nullable|string',
            'patient_condition' => 'nullable|in:kebakaran,rescue',
            'trip_type' => 'nullable|in:one_way,round_trip',
            'return_address' => 'nullable|string',
            'blok' => 'nullable|string',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
            'kelurahan' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'nomor' => 'nullable|string',
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
                $serviceType = ucfirst($validated['service_type']);
                $address = $validated['pickup_address'];
                $time = $validated['pickup_time'];
                
                $message = CloudMessage::new ()
                    ->withNotification(Notification::create(
                    'Permintaan Baru',
                    "{$serviceType}\n{$address}\n{$time}"
                ))
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'notification' => [
                        'channel_id' => 'emergency-channel',
                        'sound' => 'emergency',
                    ],
                ]));

                $messaging->sendMulticast($message, array_values($tokens));
            }
        }
        catch (\Exception $e) {
            \Log::error('FCM Send Error: ' . $e->getMessage());
        }

        return redirect()->route('patient-request.create')
            ->with('success', 'Permintaan Anda telah dikirim. Kami akan segera menghubungi Anda.');
    }
}