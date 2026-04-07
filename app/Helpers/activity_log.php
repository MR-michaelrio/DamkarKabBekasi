<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (! function_exists('activity_log')) {
    function activity_log(
        string $action,
        ?string $description = null,
        ?string $model = null,
        ?int $model_id = null
    ) {
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'model'       => $model,
            'model_id'    => $model_id,
            'description' => $description,
        ]);
    }
}
