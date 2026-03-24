<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmbulanceMaintenance extends Model
{
    protected $fillable = [
        'ambulance_id',
        'maintenance_date',
        'maintenance_type',
        'workshop',
        'cost',
        'spare_parts',
        'odometer',
    ];

    protected $casts = [
        'spare_parts' => 'array',
        'maintenance_date' => 'date',
    ];

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class);
    }
}
