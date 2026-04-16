<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Route;

// ────────────────────────────────────────────────────────────
// DEBUG ROUTES - Untuk test PDF rendering issues
// ────────────────────────────────────────────────────────────

/**
 * TEST ROUTE - Uncomment di routes/web.php untuk debug:
 * Route::get('/debug/pdf-test/{id}', function($id) {
 *     $dispatch = \App\Models\Dispatch::find($id);
 *     if (!$dispatch) abort(404);
 *     
 *     return view('admin.reports.kebakaran_pdf_simple', [
 *         'nomor' => $dispatch->nomor ?? 'NO-001',
 *         'day_date' => now()->format('l, d F Y'),
 *         'time_report' => '10:30',
 *         'time_departure' => '10:35',
 *         'time_arrival' => '10:45',
 *         'address' => $dispatch->pickup_address ?? 'Alamat Uji',
 *         'village' => $dispatch->kelurahan ?? 'Kelurahan',
 *         'district' => $dispatch->kecamatan ?? 'Kecamatan',
 *         'reporter_name' => $dispatch->patient_name ?? 'Pelapor',
 *         'reporter_phone' => $dispatch->patient_phone ?? '08xx',
 *         'chronology' => 'Kebakaran',
 *         'photos' => \App\Models\ActivityPhoto::limit(3)->get()->map(function($p) {
 *             return (object)['photo' => $p, 'uploader' => 'Test User'];
 *         }),
 *     ]);
 * });
 */
