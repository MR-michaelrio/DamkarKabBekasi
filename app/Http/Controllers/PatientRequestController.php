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
            'pickup_time' => 'required|date_format:H:i',
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
            // C. DETAIL KEJADIAN
            'event_description' => 'nullable|string',
            'building_type' => 'nullable|string',
            'fire_cause' => 'nullable|string',
            'affected_area' => 'nullable|string',
            // D. DATA PEMILIK
            'owner_name' => 'nullable|string|max:255',
            'owner_age' => 'nullable|string|max:10',
            'owner_phone' => 'nullable|string|max:20',
            'owner_profession' => 'nullable|string|max:255',
            // F. DATA KETUA RT/RW
            'community_leader_name' => 'nullable|string|max:255',
            'community_leader_phone' => 'nullable|string|max:20',
            // G. OPERASIONAL PEMADAM
            'unit_assistance' => 'nullable|string|max:255',
            // H. PENGGUNAAN PERALATAN (dispatcher)
            'time_finished' => 'nullable|date_format:H:i',
            'scba_usage' => 'nullable|integer|min:0',
            'apar_usage' => 'nullable|integer|min:0',
            // I. DATA KORBAN (dispatcher)
            'injured_count' => 'nullable|integer|min:0',
            'fatalities_count' => 'nullable|integer|min:0',
            'displaced_count' => 'nullable|integer|min:0',
        ]);

        $patientRequest = PatientRequest::create($validated);

        // Generate TTS first
        $serviceType = ucfirst($validated['service_type']);
        $address = $validated['pickup_address'];
        $time = $validated['pickup_time'];
        
        $ttsService = new \App\Services\TTSService();
        $ttsGeneratedName = $ttsService->generate("Laporan baru. {$serviceType}. {$address}.");
        
        // Robust URL generation
        $ttsUrl = '';
        if ($ttsGeneratedName) {
            $ttsUrl = asset($ttsGeneratedName);
            // Save TTS URL to database
            $patientRequest->update(['tts_url' => $ttsUrl]);
        }
        \Log::info("FCM PatientRequest TTS URL: " . $ttsUrl);

        // Save to Firebase Realtime Database for real-time notifications
        try {
            $firebaseUrl = 'https://damkarkabbekasi-default-rtdb.firebaseio.com/patient_requests/' . $patientRequest->id . '.json';
            
            $data = [
                'id' => $patientRequest->id,
                'patient_name' => $patientRequest->patient_name,
                'service_type' => $patientRequest->service_type,
                'pickup_address' => $patientRequest->pickup_address,
                'patient_condition' => $patientRequest->patient_condition,
                'created_at' => $patientRequest->created_at->toISOString(),
                'tts_url' => $ttsUrl,
            ];

            $ch = curl_init($firebaseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info('Patient request saved to Firebase: ' . $patientRequest->id);
            } else {
                Log::error('Failed to save to Firebase. HTTP Code: ' . $httpCode . ', Response: ' . $response);
            }
        } catch (\Exception $e) {
            Log::error('Failed to save to Firebase: ' . $e->getMessage());
        }

        // Broadcast event for real-time notifications
        broadcast(new \App\Events\NewPatientRequest($patientRequest));

        // Send Push Notification
        try {
            $ambulanceTokens = \App\Models\Ambulance::whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')->toArray();

            $deviceTokensByProject = \App\Models\DeviceToken::select('token', 'firebase_project')
                ->get()
                ->groupBy('firebase_project');

            if ($ambulanceTokens || $deviceTokensByProject->isNotEmpty()) {

                $message = CloudMessage::new ()
                ->withNotification(\Kreait\Firebase\Messaging\Notification::create('Permintaan Baru', "{$serviceType}\n{$address}\n{$time}"))
                ->withData([
                    'title' => 'Permintaan Baru',
                    'body' => "{$serviceType}\n{$address}\n{$time}",
                    'tts_url' => $ttsUrl ?: '',
                    'type' => 'patient_request',
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
                if ($validated['service_type'] === 'kebakaran') {
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