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
use App\Http\Controllers\Admin\AmbulanceMaintenanceController;
use App\Http\Controllers\Admin\AmbulanceTypeController;
use App\Http\Controllers\Admin\PletonController;
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

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

// Public Patient Request Form
Route::get('/request', [PatientRequestController::class , 'create'])
    ->name('patient-request.create');
Route::post('/request', [PatientRequestController::class , 'store'])
    ->name('patient-request.store');

// Public Monitoring (No Auth Required)
Route::get('/monitoring', [\App\Http\Controllers\MonitoringController::class , 'index'])
    ->name('monitoring');
Route::get('/monitoring/data', [\App\Http\Controllers\MonitoringController::class , 'getData'])
    ->name('monitoring.data');

// Public Calendar & Events
Route::get('/portal/jadwal', [\App\Http\Controllers\Admin\ScheduleController::class , 'public'])
    ->name('portal.jadwal');
Route::get('/portal/event-request', [\App\Http\Controllers\Admin\EventRequestController::class , 'publicCreate'])
    ->name('portal.event-request.create');
Route::post('/portal/event-request', [\App\Http\Controllers\Admin\EventRequestController::class , 'publicStore'])
    ->name('portal.event-request.store');

// Public FCM Token Save
Route::post('/public-fcm-token', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required|string',
        'project' => 'nullable|string|in:damkar,pmi,gmci'
    ]);
    
    \App\Models\DeviceToken::updateOrCreate(
        ['token' => $request->token],
        ['firebase_project' => $request->project ?? 'damkar']
    );
    
    return response()->json(['success' => true]);
})->name('public-fcm-token.save');

// API Routes (for driver GPS tracking)
Route::post('/api/driver/location', [DriverLocationController::class , 'updateLocation'])
    ->middleware('auth:ambulance'); // specific guard here if we want, or just 'auth' if we configure defaults properly

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

            if (in_array(auth()->user()->role, ['admin', 'user', 'dispatcher'])) {
                return redirect()->route('admin.dashboard');
            }
            else {
                // Fallback (though drivers shouldn't use user auth anymore)
                return redirect('/');
            }
        }
        )->name('dashboard');

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
            Route::get('/dashboard', [AdminDashboardController::class , 'index'])->name('dashboard');

            // Resource Routes
            Route::resource('ambulances', AmbulanceController::class);
            Route::resource('armada-types', AmbulanceTypeController::class)->parameters([
                'armada-types' => 'ambulance_type'
            ])->names('ambulance-types');
            Route::get('ambulances/{ambulance}/maintenance', [AmbulanceMaintenanceController::class , 'index'])->name('ambulances.maintenance.index');
            Route::post('ambulances/{ambulance}/maintenance', [AmbulanceMaintenanceController::class , 'store'])->name('ambulances.maintenance.store');
            Route::put('maintenance/{maintenance}', [AmbulanceMaintenanceController::class , 'update'])->name('maintenance.update');
            Route::delete('maintenance/{maintenance}', [AmbulanceMaintenanceController::class , 'destroy'])->name('maintenance.destroy');

            Route::resource('drivers', DriverController::class);
            Route::resource('pletons', PletonController::class);
            Route::resource('users', UserController::class)->middleware('role:admin');

            // Dispatch Management
            Route::get('dispatches/export/pdf', [DispatchController::class , 'exportPdf'])
                ->name('dispatches.export.pdf');
            Route::get('dispatches/{dispatch}/export-pdf', [DispatchController::class , 'exportSinglePdf'])
                ->name('dispatches.export-single.pdf');

            Route::resource('dispatches', DispatchController::class);

            Route::post('dispatches/{dispatch}/next', [DispatchController::class , 'next'])
                ->name('dispatches.next');

            Route::post('dispatches/{dispatch}/status', [DispatchController::class , 'updateStatus'])
                ->name('dispatches.update-status');

            Route::get('dispatches/{dispatch}/location-history', [DispatchController::class , 'locationHistory'])
                ->name('dispatches.location-history');

            // ✅ MAPS (INI YANG SEBELUMNYA HILANG)
            Route::get('maps', [MapController::class , 'index'])
                ->name('maps');
            Route::get('maps/ambulances', [MapController::class , 'getAmbulances'])
                ->name('maps.ambulances');

            Route::get('schedules', [\App\Http\Controllers\Admin\ScheduleController::class , 'index'])
                ->name('schedules.index');

            // Event Requests Management
            Route::resource('event-requests', \App\Http\Controllers\Admin\EventRequestController::class);
            Route::post('event-requests/{eventRequest}/approve', [\App\Http\Controllers\Admin\EventRequestController::class , 'approve'])
                ->name('event-requests.approve');
            Route::post('event-requests/{eventRequest}/reject', [\App\Http\Controllers\Admin\EventRequestController::class , 'reject'])
                ->name('event-requests.reject');
            Route::post('event-requests/{eventRequest}/assign-unit', [\App\Http\Controllers\Admin\EventRequestController::class , 'assignUnit'])
                ->name('event-requests.assign-unit');
            Route::post('event-requests/{eventRequest}/finish', [\App\Http\Controllers\Admin\EventRequestController::class , 'finish'])
                ->name('event-requests.finish');
            Route::post('event-requests/{eventRequest}/replace-unit/{dispatch}', [\App\Http\Controllers\Admin\EventRequestController::class , 'replaceUnit'])
                ->name('event-requests.replace-unit');

            // Patient Requests Management
            Route::get('laporan-masyarakat', [AdminPatientRequestController::class , 'index'])
                ->name('patient-requests.index');
            Route::get('laporan-masyarakat/{patientRequest}', [AdminPatientRequestController::class , 'show'])
                ->name('patient-requests.show');
            Route::get('laporan-masyarakat/{patientRequest}/edit', [AdminPatientRequestController::class , 'edit'])
                ->name('patient-requests.edit');
            Route::put('laporan-masyarakat/{patientRequest}', [AdminPatientRequestController::class , 'update'])
                ->name('patient-requests.update');
            Route::delete('laporan-masyarakat/{patientRequest}', [AdminPatientRequestController::class , 'destroy'])
                ->name('patient-requests.destroy');
            Route::get('laporan-masyarakat/{patientRequest}/dispatch', [AdminPatientRequestController::class , 'createDispatch'])
                ->name('patient-requests.create-dispatch');
            Route::get('laporan-masyarakat/{patientRequest}/pdf', [AdminPatientRequestController::class , 'exportPdf'])
                ->name('patient-requests.pdf');
            Route::post('laporan-masyarakat/{patientRequest}/reject', [AdminPatientRequestController::class , 'reject'])
                ->name('patient-requests.reject');
        }
        );
    });

/*
 |--------------------------------------------------------------------------
 | AMBULANCE AUTHENTICATED
 |--------------------------------------------------------------------------
 */
// Middleware group for ambulance auth

// Ambulance Auth Routes
Route::prefix('ambulance')->name('ambulance.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\AmbulanceAuthController::class , 'showLoginForm'])
        ->middleware('guest:ambulance')
        ->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\AmbulanceAuthController::class , 'login'])
        ->middleware('guest:ambulance');
});

Route::post('ambulance/logout', [\App\Http\Controllers\Auth\AmbulanceAuthController::class , 'logout'])
    ->name('ambulance.logout')
    ->middleware('auth:ambulance');

// Driver Dashboard (Now uses ambulance auth)
Route::middleware(['auth:ambulance'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverDashboardController::class , 'index'])->name('dashboard');
    Route::post('/dispatches/{dispatch}/status', [DriverDashboardController::class , 'updateStatus'])->name('dispatches.update-status');
    Route::post('/dispatches/{dispatch}/toggle-pause', [DriverDashboardController::class , 'togglePause'])->name('dispatches.toggle-pause');

    // New Dispatching Routes for Drivers
    Route::get('/dispatching', [DriverDashboardController::class , 'dispatching'])->name('dispatching');
    Route::get('/dispatching/{patientRequest}', [DriverDashboardController::class , 'createSelfDispatch'])->name('patient-requests.create-dispatch');
    Route::post('/dispatching/{patientRequest}', [DriverDashboardController::class , 'storeSelfDispatch'])->name('patient-requests.store-dispatch');

    // Save FCM Token
    Route::post('/fcm-token', [DriverDashboardController::class , 'saveFcmToken'])->name('fcm-token.save');
});