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
        'patient_condition',
        'patient_phone',
        'pickup_address',
        'destination',
        'driver_id',
        'ambulance_id',
        'status',
        'assigned_at',
        'pickup_at',
        'hospital_at',
        'completed_at',
    ];

    protected $dates = [
        'deleted_at',
        'assigned_at',
        'pickup_at',
        'hospital_at',
        'completed_at',
    ];

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
