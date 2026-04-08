<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Ambulance extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'code',
        'type',
        'plate_number',
        'status',
        'latitude',
        'longitude',
        'last_location_update',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_location_update' => 'datetime',
    ];

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class, 'ambulance_id');
    }

    public function maintenances()
    {
        return $this->hasMany(AmbulanceMaintenance::class);
    }

    public function requestHistory()
    {
        return $this->hasMany(DispatchRequestHistory::class);
    }

    // Override auth identifier
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
