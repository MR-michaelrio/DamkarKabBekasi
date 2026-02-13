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
        // TIMEFRAME DISPATCHES
        // =====================
        $todayDispatches = Dispatch::with(['driver', 'ambulance'])
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('created_at')
            ->get();

        $weekDispatches = Dispatch::with(['driver', 'ambulance'])
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->orderByDesc('created_at')
            ->get();

        $monthDispatches = Dispatch::with(['driver', 'ambulance'])
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->orderByDesc('created_at')
            ->get();

        // =====================
        // AMBULANCE ANALYTICS (Current Month)
        // =====================
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $ambulanceAnalytics = Ambulance::withCount(['dispatches' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        }])->get();

        // =====================
        // SUMMARY STATS (Keep existing for potential use)
        // =====================
        $totalDispatch = Dispatch::count();
        $dispatchActive = Dispatch::whereNotIn('status', ['completed', 'cancelled'])->count();
        $dispatchEmergency = Dispatch::where('patient_condition', 'emergency')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
        $ambulanceReady = Ambulance::where('status', 'ready')->count();
        $ambulanceOnDuty = Ambulance::where('status', 'on_duty')->count();

        return view('admin.dashboard', compact(
            'todayDispatches',
            'weekDispatches',
            'monthDispatches',
            'ambulanceAnalytics',
            'totalDispatch',
            'dispatchActive',
            'dispatchEmergency',
            'ambulanceReady',
            'ambulanceOnDuty'
        ));
    }
}
