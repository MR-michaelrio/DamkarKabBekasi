<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\DispatchController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Driver\DriverDashboardController;
use App\Http\Controllers\Admin\PletonController;

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



// Public Monitoring (No Auth Required)
Route::get('/monitoring', [\App\Http\Controllers\MonitoringController::class, 'index'])
    ->name('monitoring');
Route::get('/monitoring/data', [\App\Http\Controllers\MonitoringController::class, 'getData'])
    ->name('monitoring.data');

// Public Calendar & Events
Route::get('/portal/jadwal', [\App\Http\Controllers\Admin\ScheduleController::class, 'public'])
    ->name('portal.jadwal');
Route::get('/portal/event-request', [\App\Http\Controllers\Admin\EventRequestController::class, 'publicCreate'])
    ->name('portal.event-request.create');
Route::post('/portal/event-request', [\App\Http\Controllers\Admin\EventRequestController::class, 'publicStore'])
    ->name('portal.event-request.store');

// Public FCM Token Save
// Route::post('/public-fcm-token', function (\Illuminate\Http\Request $request) {
//     $request->validate([
//         'token' => 'required|string',
//         'project' => 'nullable|string|in:damkar,pmi,gmci'
//     ]);

//     \App\Models\DeviceToken::updateOrCreate(
//         ['token' => $request->token],
//         ['firebase_project' => $request->project ?? 'damkar']
//     );

//     return response()->json(['success' => true]);
// })->name('public-fcm-token.save');

// API Routes for real-time notifications
Route::prefix('api')->group(function () {
    // Placeholder for future API routes
});

Route::match(['post', 'options'], '/public-fcm-token', function (\Illuminate\Http\Request $request) {
    if ($request->isMethod('options')) {
        return response()->json(['success' => true])
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, X-Requested-With, X-CSRF-TOKEN');
    }

    $request->validate([
        'token' => 'required|string',
        'project' => 'nullable|string'
    ]);

    $project = strtolower(trim($request->project ?? 'damkar'));
    if (empty($project))
        $project = 'damkar';

    \App\Models\DeviceToken::updateOrCreate(
        ['token' => $request->token],
        ['firebase_project' => $project]
    );

    return response()->json(['success' => true], 201)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, X-Requested-With, X-CSRF-TOKEN');
})->name('public-fcm-token.save');



// Temporary Preview Route
Route::get('/preview-report', function () {
    return view('report.template1', [
        'nomor' => '123/Laporan/Damkar/2026',
        'sifat' => 'Penting',
        'attachment' => '-',
        'place_date' => 'Bekasi, Sabtu 10 Januari 2026',
        'day_date' => 'Sabtu, 10 Januari 2026',
        'time_report' => '10 : 35',
        'time_departure' => '10 : 37',
        'time_arrival' => '10 : 47',
        'time_finished' => '11 : 25',
        'chronology' => 'Laporan dari warga; kebakaran berasal dari kendaraan bus yang mengalami kebakaran mesin',
        'address' => 'Jl pantrua toyogiri indoporlen',
        'village' => 'Setia Mekar',
        'district' => 'Tambun Selatan',
        'reporter_name' => 'vanesa',
        'reporter_phone' => '081314451876',
        'community_leader_name' => '-',
        'community_leader_phone' => '-',
        'area_size' => '100m²',
        'building_type' => 'Kendaraan roda empat/Bus',
        'owner_name' => '-',
        'owner_age' => '-',
        'owner_phone' => '-',
        'owner_occupation' => '-',
        'fire_origin' => 'Listrik',
        'unit_count' => '1 Unit',
        'vehicle_number' => 'B 9001 FHA',
        'additional_units' => [],
        'scba_usage' => '0',
        'apar_usage' => '0',
        'injured' => '0',
        'fatalities' => '0',
        'displaced' => '0',
    ]);
});

// Activity Photo Routes (API)
Route::prefix('api')->middleware(['auth:web'])->group(function () {
    // Photo Management Routes
    Route::post('/activities/{activity_id}/photos', [\App\Http\Controllers\Api\ActivityPhotoController::class, 'store'])
        ->name('activity-photos.store');
    Route::get('/activities/{activity_id}/photos', [\App\Http\Controllers\Api\ActivityPhotoController::class, 'index'])
        ->name('activity-photos.index');
    Route::get('/activities/{activity_id}/photos/status', [\App\Http\Controllers\Api\ActivityPhotoController::class, 'getStatus'])
        ->name('activity-photos.status');
    Route::patch('/photos/{photo_id}', [\App\Http\Controllers\Api\ActivityPhotoController::class, 'update'])
        ->name('activity-photos.update');
    Route::delete('/photos/{photo_id}', [\App\Http\Controllers\Api\ActivityPhotoController::class, 'destroy'])
        ->name('activity-photos.destroy');
});

// Ambulance Auth Routes (Moved to bottom for clarity)

require __DIR__ . '/auth.php';

/*
 |--------------------------------------------------------------------------
 | AUTHENTICATED
 |--------------------------------------------------------------------------
 */
Route::middleware(['auth'])->group(function () {

    Route::get(
        '/dashboard',
        function () {
            // Redirect based on role
            if (in_array(auth()->user()->role, ['admin', 'user', 'dispatcher'])) {
                return redirect()->route('admin.dashboard');
            } else {
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
    Route::prefix('admin')->name('admin.')->group(
        function () {

            // Dashboard
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Resource Routes
            Route::resource('drivers', DriverController::class);
            Route::resource('pletons', PletonController::class);
            Route::resource('users', UserController::class)->middleware('role:admin');

            // Dispatch Management
            Route::get('dispatches/export/pdf', [DispatchController::class, 'exportPdf'])
                ->name('dispatches.export.pdf');
            Route::get('dispatches/{dispatch}/export-pdf', [DispatchController::class, 'exportSinglePdf'])
                ->name('dispatches.export-single.pdf');

            Route::resource('dispatches', DispatchController::class);

            Route::post('dispatches/{dispatch}/next', [DispatchController::class, 'next'])
                ->name('dispatches.next');

            Route::post('dispatches/{dispatch}/status', [DispatchController::class, 'updateStatus'])
                ->name('dispatches.update-status');

            Route::get('dispatches/{dispatch}/location-history', [DispatchController::class, 'locationHistory'])
                ->name('dispatches.location-history');

            // Maps
            Route::get('maps', [MapController::class, 'index'])
                ->name('maps');

            Route::get('schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])
                ->name('schedules.index');

            // Event Requests Management
            Route::resource('event-requests', \App\Http\Controllers\Admin\EventRequestController::class);
            Route::post('event-requests/{eventRequest}/approve', [\App\Http\Controllers\Admin\EventRequestController::class, 'approve'])
                ->name('event-requests.approve');
            Route::post('event-requests/{eventRequest}/reject', [\App\Http\Controllers\Admin\EventRequestController::class, 'reject'])
                ->name('event-requests.reject');
            Route::post('event-requests/{eventRequest}/assign-unit', [\App\Http\Controllers\Admin\EventRequestController::class, 'assignUnit'])
                ->name('event-requests.assign-unit');
            Route::post('event-requests/{eventRequest}/finish', [\App\Http\Controllers\Admin\EventRequestController::class, 'finish'])
                ->name('event-requests.finish');
            Route::post('event-requests/{eventRequest}/replace-unit/{dispatch}', [\App\Http\Controllers\Admin\EventRequestController::class, 'replaceUnit'])
                ->name('event-requests.replace-unit');
        }
    );
});

// ──── DEBUG ROUTE (remove after testing) ────
Route::get('/debug/pdf-test', function() {
    return view('admin.reports.kebakaran_pdf_simple', [
        'nomor' => 'TEST-001/2026',
        'sifat' => 'Penting',
        'place_date' => 'Bekasi, 16 April 2026',
        'day_date' => 'Rabu, 16 April 2026',
        'time_report' => '10:30',
        'time_departure' => '10:35',
        'time_arrival' => '10:45',
        'time_finished' => '11:15',
        'chronology' => 'Kebakaran',
        'address' => 'Jalan Raya Bekasi No. 123',
        'village' => 'Kelurahan Test',
        'district' => 'Kecamatan Test',
        'reporter_name' => 'Budi Santoso',
        'reporter_phone' => '08123456789',
        'photos' => collect(),
    ]);
});

// Test Dompdf with minimal template
Route::get('/debug/pdf-minimal', function() {
    $html = view('admin.reports.kebakaran_pdf_test_minimal')->render();
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');
    return $pdf->stream('test-minimal.pdf');
});
