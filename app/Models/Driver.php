<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'pleton',
        'license_number',
        'status',
        'latitude',
        'longitude',
        'last_seen',
    ];
}

