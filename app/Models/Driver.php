<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'pleton',
        'pleton_id',
        'license_number',
        'status',
        'latitude',
        'longitude',
        'last_seen',
    ];

    public function pleton()
    {
        return $this->belongsTo(Pleton::class);
    }
}

