<?php

namespace App\Http\Controllers;

use App\Models\PatientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Laravel\Firebase\Facades\Firebase;

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

            $deviceTokensByProject = \App\Models\DeviceToken::select('token', 'firebase_project')
                ->get()
                ->groupBy('firebase_project');

            if ($ambulanceTokens || $deviceTokensByProject->isNotEmpty()) {
                $serviceType = ucfirst($validated['service_type']);
                $address = $validated['pickup_address'];
                $time = $validated['pickup_time'];
                
                $ttsService = new \App\Services\TTSService();
                $ttsUrl = $ttsService->generate("Laporan baru. {$serviceType}. {$address}.");

                $message = CloudMessage::new ()
                ->withData([
                    'title' => 'Permintaan Baru',
                    'body' => "{$serviceType}\n{$address}\n{$time}",
                    'tts_url' => $ttsUrl ? url($ttsUrl) : '',
                ])
                ->withAndroidConfig(AndroidConfig::fromArray([
                    'priority' => 'high',
                ]));

                $projects = ['damkar', 'pmi', 'gmci'];
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
                            Log::error("FCM Send Error for Project {$projectName} (PatientRequest): " . $e->getMessage());
                        }
                    }
                }
            }
        }
        catch (\Exception $e) {
            \Log::error('FCM Send Error: ' . $e->getMessage());
        }

        return redirect()->route('patient-request.create')
            ->with('success', 'Permintaan Anda telah dikirim. Kami akan segera menghubungi Anda.');
    }
}