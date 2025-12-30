<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use App\Models\Ambulance;
use App\Models\Driver;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // =====================
        // DISPATCH
        // =====================
        $totalDispatch = Dispatch::count();

        $dispatchActive = Dispatch::whereNotIn('status', [
            'completed',
            'cancelled'
        ])->count();

        $dispatchEmergency = Dispatch::where('patient_condition', 'emergency')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        // =====================
        // AMBULANCE
        // =====================
        $ambulanceReady = Ambulance::where('status', 'ready')->count();
        $ambulanceOnDuty = Ambulance::where('status', 'on_duty')->count();

        // =====================
        // DRIVER
        // =====================
        $driverAvailable = Driver::where('status', 'available')->count();
        $driverOnDuty = Driver::where('status', 'on_duty')->count();

        return view('admin.dashboard', compact(
            'totalDispatch',
            'dispatchActive',
            'dispatchEmergency',
            'ambulanceReady',
            'ambulanceOnDuty',
            'driverAvailable',
            'driverOnDuty'
        ));
    }
}
