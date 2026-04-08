<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchRequestHistory extends Model
{
    use HasFactory;

    protected $table = 'dispatch_request_history';

    protected $fillable = [
        'ambulance_id',
        'dispatch_id',
        'sequence',
        'completed_at',
        'returned_to_base',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'returned_to_base' => 'boolean',
    ];

    /**
     * Get the ambulance for this history entry
     */
    public function ambulance(): BelongsTo
    {
        return $this->belongsTo(Ambulance::class);
    }

    /**
     * Get the dispatch for this history entry
     */
    public function dispatch(): BelongsTo
    {
        return $this->belongsTo(Dispatch::class);
    }
}
