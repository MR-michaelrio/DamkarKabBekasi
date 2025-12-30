<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        string $action,
        string $description,
        string $model = null,
        int $modelId = null
    ) {
        ActivityLog::create([
            'user_id'   => Auth::id(),
            'action'    => $action,
            'model'     => $model,
            'model_id'  => $modelId,
            'description' => $description,
        ]);
    }
}
