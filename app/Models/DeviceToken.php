<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'firebase_project', 'name', 'phone', 'is_whitelisted'];
}
