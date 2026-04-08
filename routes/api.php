<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityPhotoController;

Route::middleware('auth:sanctum')->group(function () {
    // Activity Photo Upload API
    Route::post('/activity-photos/{activityLog}/upload', [ActivityPhotoController::class, 'upload'])
        ->name('activity-photos.upload');
    
    Route::get('/activity-photos/{activityLog}', [ActivityPhotoController::class, 'list'])
        ->name('activity-photos.list');
    
    Route::delete('/activity-photos/{activityPhoto}', [ActivityPhotoController::class, 'delete'])
        ->name('activity-photos.delete');
    
    Route::put('/activity-photos/{activityPhoto}/sequence', [ActivityPhotoController::class, 'updateSequence'])
        ->name('activity-photos.update-sequence');
});
