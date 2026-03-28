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
