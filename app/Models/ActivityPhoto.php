<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityPhoto extends Model
{
    use HasFactory;

    protected $table = 'activity_photos';

    protected $fillable = [
        'activity_log_id',
        'photo_path',
        'photo_name',
        'mime_type',
        'file_size',
        'description',
        'sequence',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sequence' => 'integer',
    ];

    /**
     * Get the activity log that owns the photo.
     */
    public function activityLog(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class, 'activity_log_id');
    }

    /**
     * Get the full URL of the photo
     */
    public function getPhotoUrlAttribute(): string
    {
        return asset('storage/' . $this->photo_path);
    }
}
