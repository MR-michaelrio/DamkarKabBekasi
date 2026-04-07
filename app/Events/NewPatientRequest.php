<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use App\Models\PatientRequest;

class NewPatientRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $patientRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(PatientRequest $patientRequest)
    {
        $this->patientRequest = $patientRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('patient-requests'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-request';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->patientRequest->id,
            'patient_name' => $this->patientRequest->patient_name,
            'service_type' => $this->patientRequest->service_type,
            'pickup_address' => $this->patientRequest->pickup_address,
            'patient_condition' => $this->patientRequest->patient_condition,
            'created_at' => $this->patientRequest->created_at->toISOString(),
        ];
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return true;
    }
}
