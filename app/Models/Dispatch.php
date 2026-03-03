<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dispatches';

    protected $fillable = [
        'patient_name',
        'request_date',
        'pickup_time',
        'patient_condition',
        'patient_phone',
        'pickup_address',
        'destination',
        'driver_id',
        'ambulance_id',
        'status',
        'is_paused',
        'assigned_at',
        'pickup_at',
        'hospital_at',
        'completed_at',
        'trip_type',
        'return_address',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'assigned_at' => 'datetime',
            'pickup_at' => 'datetime',
            'hospital_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /* =====================
     | RELATIONS
     ===================== */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class);
    }

    public function logs()
    {
        return $this->hasMany(DispatchLog::class);
    }
}
