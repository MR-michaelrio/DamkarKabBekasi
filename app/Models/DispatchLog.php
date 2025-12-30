<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchLog extends Model
{
    use HasFactory;

    protected $table = 'dispatch_logs';

    protected $fillable = [
        'dispatch_id',
        'status',
        'note',
    ];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }
}
