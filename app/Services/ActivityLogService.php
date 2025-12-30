<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public static function log(string $action, string $model = null, int $modelId = null, string $description = null): void
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'model'      => $model,
            'model_id'   => $modelId,
            'description'=> $description,
        ]);
    }
}
