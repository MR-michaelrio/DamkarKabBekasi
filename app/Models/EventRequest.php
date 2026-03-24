<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'needs',
        'start_date',
        'end_date',
        'status',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    /**
     * All dispatches assigned to this event.
     */
    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }

    /**
     * Active dispatches (not replaced).
     */
    public function activeDispatches()
    {
        return $this->hasMany(Dispatch::class)
                    ->where('is_replacement', false)
                    ->orWhere(function($q) {
                        $q->where('event_request_id', $this->id)
                          ->where('is_replacement', true)
                          ->whereIn('status', ['pending', 'on_the_way_scene', 'on_scene', 'on_the_way_kantor_pos']);
                    });
    }

    public function isDisaster(): bool
    {
        return $this->type === 'disaster';
    }

    public function isOngoing(): bool
    {
        return now()->between($this->start_date, $this->end_date->endOfDay());
    }
}
