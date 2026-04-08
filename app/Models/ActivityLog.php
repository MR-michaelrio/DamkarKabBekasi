<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description',
    ];

    /**
     * Get the photos associated with this activity log
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ActivityPhoto::class, 'activity_log_id')->orderBy('sequence');
    }

    /**
     * Get the user associated with this activity log
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this activity has reached the maximum number of photos
     */
    public function hasMaxPhotos(): bool
    {
        return $this->photos()->count() >= 5;
    }

    /**
     * Get the count of photos
     */
    public function getPhotoCountAttribute(): int
    {
        return $this->photos()->count();
    }
}
