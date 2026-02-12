<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AmbulanceController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\DispatchController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\PatientRequestController as AdminPatientRequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PatientRequestController;
use App\Http\Controllers\Driver\DriverDashboardController;
use App\Http\Controllers\Api\DriverLocationController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/portal', function () {
    return view('auth.portal');
})->name('portal');

// Public Patient Request Form
Route::get('/request', [PatientRequestController::class, 'create'])
    ->name('patient-request.create');
Route::post('/request', [PatientRequestController::class, 'store'])
    ->name('patient-request.store');

// Public Monitoring (No Auth Required)
Route::get('/monitoring', [\App\Http\Controllers\MonitoringController::class, 'index'])
    ->name('monitoring');
Route::get('/monitoring/data', [\App\Http\Controllers\MonitoringController::class, 'getData'])
    ->name('monitoring.data');

// API Routes (for driver GPS tracking)
Route::post('/api/driver/location', [DriverLocationController::class, 'updateLocation'])
    ->middleware('auth:ambulance');  // specific guard here if we want, or just 'auth' if we configure defaults properly

// Ambulance Auth Routes (Moved to bottom for clarity)

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| AUTHENTICATED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        // Redirect based on role/guard
        if (auth()->guard('ambulance')->check()) {
            return redirect()->route('driver.dashboard');
        }

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            // Fallback (though drivers shouldn't use user auth anymore)
            return redirect('/');
        }
    })->name('dashboard');

    // Driver Dashboard (moved to separate group below)
    /* Route::get('/driver/dashboard', [DriverDashboardController::class, 'index'])
        ->name('driver.dashboard'); */

    /*
    |--------------------------------------------------------------------------
    | ADMIN AREA
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Resource Routes
        Route::resource('ambulances', AmbulanceController::class);
        Route::resource('drivers', DriverController::class);
        Route::resource('users', UserController::class);
        
        // Dispatch Management
        Route::get('dispatches/export/pdf', [DispatchController::class, 'exportPdf'])
            ->name('dispatches.export.pdf');
            
        Route::resource('dispatches', DispatchController::class);
        
        Route::post('dispatches/{dispatch}/next', [DispatchController::class, 'next'])
            ->name('dispatches.next');

        Route::post('dispatches/{dispatch}/status', [DispatchController::class, 'updateStatus'])
             ->name('dispatches.update-status');

        // ✅ MAPS (INI YANG SEBELUMNYA HILANG)
        Route::get('maps', [MapController::class, 'index'])
            ->name('maps');
        Route::get('maps/ambulances', [MapController::class, 'getAmbulances'])
            ->name('maps.ambulances');

        // Patient Requests Management
        Route::get('patient-requests', [AdminPatientRequestController::class, 'index'])
            ->name('patient-requests.index');
        Route::get('patient-requests/{patientRequest}', [AdminPatientRequestController::class, 'show'])
            ->name('patient-requests.show');
        Route::get('patient-requests/{patientRequest}/edit', [AdminPatientRequestController::class, 'edit'])
            ->name('patient-requests.edit');
        Route::put('patient-requests/{patientRequest}', [AdminPatientRequestController::class, 'update'])
            ->name('patient-requests.update');
        Route::delete('patient-requests/{patientRequest}', [AdminPatientRequestController::class, 'destroy'])
            ->name('patient-requests.destroy');
        Route::get('patient-requests/{patientRequest}/dispatch', [AdminPatientRequestController::class, 'createDispatch'])
            ->name('patient-requests.create-dispatch');
        Route::post('patient-requests/{patientRequest}/reject', [AdminPatientRequestController::class, 'reject'])
            ->name('patient-requests.reject');
    });
});

/*
|--------------------------------------------------------------------------
| AMBULANCE AUTHENTICATED
|--------------------------------------------------------------------------
*/
// Middleware group for ambulance auth

// Ambulance Auth Routes
Route::prefix('ambulance')->name('ambulance.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\AmbulanceAuthController::class, 'showLoginForm'])
        ->middleware('guest:ambulance')
        ->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\AmbulanceAuthController::class, 'login'])
        ->middleware('guest:ambulance');
});

Route::post('ambulance/logout', [\App\Http\Controllers\Auth\AmbulanceAuthController::class, 'logout'])
    ->name('ambulance.logout')
    ->middleware('auth:ambulance');

// Driver Dashboard (Now uses ambulance auth)
Route::middleware(['auth:ambulance'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('dashboard');
    Route::post('/dispatches/{dispatch}/status', [DriverDashboardController::class, 'updateStatus'])->name('dispatches.update-status');
    Route::post('/dispatches/{dispatch}/toggle-pause', [DriverDashboardController::class, 'togglePause'])->name('dispatches.toggle-pause');
});
