<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientRequest extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'patient_name',
        'service_type',
        'request_date',
        'pickup_time',
        'phone',
        'pickup_address',
        'destination',
        'patient_condition',
        'status',
        'dispatch_id',
        'trip_type',
        'return_address',
        'blok',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'nomor',
        'tts_url',
        'event_description',
        'building_type',
        'fire_cause',
        'affected_area',
        'owner_name',
        'owner_age',
        'owner_phone',
        'owner_profession',
        'community_leader_name',
        'community_leader_phone',
        'unit_assistance',
        'time_finished',
        'scba_usage',
        'apar_usage',
        'injured_count',
        'fatalities_count',
        'displaced_count',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
        ];
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }
}
